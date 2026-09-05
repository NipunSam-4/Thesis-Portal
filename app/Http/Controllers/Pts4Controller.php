<?php

namespace App\Http\Controllers;

use App\Models\Pts4Form;
use App\Models\Thesis;
use App\Models\User;
use App\Services\PtsDocumentService;
use App\Http\Requests\Pts4\StorePts4Request;
use App\Http\Requests\Pts4\UpdatePts4SupervisorRequest;
use App\Http\Requests\Pts4\EndorsePts4Request;
use App\Http\Requests\Pts4\RevertPts4Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class Pts4Controller extends Controller
{
    use AuthorizesRequests;
    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }

    // Display the student PTS-4 creation form.
    public function create()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->with(['pts1Form', 'pts2Form', 'pts4Form'])
            ->first();

        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('info', 'This thesis has already been completed.');
            }
            return redirect()->route('student.dashboard')->with('warning', 'Please register your thesis title first.');
        }

        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);

        // Must have an APPROVED PTS-2 Form
        if (!$thesis->pts2Form || $thesis->pts2Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('warning', 'PTS-4 Thesis Form is locked until your PTS-2 Synopsis Form is fully approved.');
        }

        // Must be within the Open Seminar deadline (or approved extension deadline)
        if (!$thesis->isPts4SubmissionActive()) {
            $deadline = $thesis->getPts4Deadline();
            return redirect()->route('student.dashboard')->with('warning', 'The PTS-4 submission deadline passed on ' . ($deadline ? $deadline->format('d-M-Y') : 'the deadline') . '. Please apply for a PTS-4 extension if eligible.');
        }

        $pts4Form = $thesis->pts4Form;
        if ($pts4Form) {
            if ($pts4Form->status === 'in_progress') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-4 form is currently under review.');
            }
            if ($pts4Form->status === 'approved') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-4 form has already been approved.');
            }
            if ($pts4Form->status === 'reverted') {
                return redirect()->route('student.pts4.edit')->with('warning', 'You have a reverted PTS-4 form. Please edit and resubmit your reverted form.');
            }
            if ($pts4Form->status === 'rejected') {
                $pts4Form = null;
            }
        }

        return view('student.pts4.create', compact('user', 'student', 'thesis', 'pts4Form'));
    }

    // Display the PTS-4 edit form for reverted student submissions.
    public function edit()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->with(['pts1Form', 'pts2Form', 'pts4Form'])
            ->first();

        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('info', 'This thesis has already been completed.');
            }
            return redirect()->route('student.dashboard')->with('warning', 'No active registered thesis found.');
        }

        $pts4Form = $thesis->pts4Form;
        if (!$pts4Form || $pts4Form->status !== 'reverted') {
            return redirect()->route('student.dashboard')->with('warning', 'You do not have a reverted PTS-4 form to edit.');
        }

        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);

        return view('student.pts4.create', compact('user', 'student', 'thesis', 'pts4Form'));
    }

    // Store a newly created / resubmitted PTS-4 submission.
    public function store(StorePts4Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)
            ->where('status', 'in_progress')
            ->with(['pts2Form', 'pts4Form', 'pts4Extension'])
            ->first();

        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('error', 'This thesis has already been completed.');
            }
            return redirect()->route('student.dashboard')->with('error', 'Active registered thesis not found.');
        }

        if (!$thesis->pts2Form || $thesis->pts2Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'Unauthorized: PTS-2 is not approved.');
        }

        if (!$thesis->isPts4SubmissionActive()) {
            $deadline = $thesis->getPts4Deadline();
            return redirect()->route('student.dashboard')->with('error', 'Cannot submit PTS-4: the submission deadline passed on ' . ($deadline ? $deadline->format('d-M-Y') : 'N/A') . '.');
        }

        $pts4Form = $thesis->pts4Form;
        $hasExisting = $pts4Form && in_array($pts4Form->status, ['reverted', 'rejected']);
        $validated = $request->validated();

        // Update active thesis title
        $thesis->update(['title' => $validated['thesis_title']]);


        // Document handling (max 100MB)
        $thesisDocPath = $this->ptsDocService->handleInProgressFile(
            $request->file('thesis_doc'),
            $hasExisting ? $pts4Form?->thesis_doc_path:null,
            $student->roll_number,
            $thesis->id,
            'pts4',
            'Thesis_Document',
            'Student_Original'
        );

        $activeMainSup = $student->active_main_supervisor;
        if (!$activeMainSup) {
            return back()->withInput()->with('error', 'Unable to submit PTS-4: No active Main Supervisor is assigned to your profile. Please contact the Academic Office.');
        }

        $coSupervisors = $student->activeAllCoSupervisors()->pluck('id')->all();

        // Initialize co-supervisor slot mapping with active supervisors
        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            $coSupData[$col] = $coSupervisors[$i - 1] ?? null;
        }

        // Synchronize contact info & Hindi name with student profile
        $student->update([
            'hindi_name' => $request->input('hindi_name'),
            'alternate_email' => $request->input('alternate_email'),
            'alternate_phone_number' => $request->input('alternate_phone_number'),
            'alternate_phone_country_code' => $request->input('alternate_phone_country_code', '+91'),
            'alternate_phone_iso2' => $request->input('alternate_phone_iso2', 'in'),
        ]);

        $formData = [
            'thesis_id' => $thesis->id,
            'thesis_title' => $request->input('thesis_title'),
            'thesis_doc_path' => $thesisDocPath,
            'main_supervisor_id' => $activeMainSup->id,
            'hindi_name' => $request->input('hindi_name'),
            'alternate_email' => $request->input('alternate_email'),
            'alternate_phone_number' => $request->input('alternate_phone_number'),
            'alternate_phone_country_code' => $request->input('alternate_phone_country_code', '+91'),
            'alternate_phone_iso2' => $request->input('alternate_phone_iso2', 'in'),
            'current_stage' => 'main_supervisor',
            'status' => 'in_progress',
            'reverted_by_role' => null,
            'reverted_by_id' => null,
            'reversion_comment' => null,
            'main_supervisor_recommendation' => null,
            'main_supervisor_student_comment' => null,
            'main_supervisor_confidential_remark' => null,
            'main_supervisor_submitted_at' => null,
            'academic_office_is_verified' => null,
            'academic_office_verification_remark' => null,
            'academic_office_confidential_remark' => null,
            'academic_office_submitted_at' => null,
            'dr_approval' => null,
            'dr_student_comment' => null,
            'dr_confidential_remark' => null,
            'dr_submitted_at' => null,
            'co_supervisors_submitted_at' => null,
        ];

        // Dynamically assign up to 10 active Co-Supervisors
        for ($i = 1; $i <= 10; $i++) {
            $formData["co_supervisor_{$i}_id"] = $coSupervisors[$i - 1] ?? null;
        }

        Pts4Form::create($formData);

        $actionVerb = $hasExisting ? 'resubmitted' : 'submitted';

        return redirect()->route('student.dashboard')->with('success', "PTS-4 Thesis Form {$actionVerb} successfully and forwarded to your Main Supervisor for review.");
    }

    // Show the Main Supervisor edit form for a PTS-4 submission.
    public function mainSupervisorEdit(Pts4Form $pts4)
    {
        $this->authorize('mainSupervisorEdit', $pts4);

        $thesis = $pts4->thesis;
        $student = $thesis->student;
        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;

        return view('faculty.pts4.edit', compact('pts4', 'thesis', 'student', 'studentUser'));
    }

    // Process Main Supervisor review submission (Edits, Document Replacement, Evaluation & Endorsement).
    public function update(UpdatePts4SupervisorRequest $request, Pts4Form $pts4)
    {
        $thesis = $pts4->thesis;
        $validated = $request->validated();

        // Update active thesis title
        $thesis->update(['title' => $validated['thesis_title']]);

        // Optional Thesis Document Replacement by Main Supervisor
        $msThesisDocPath = $pts4->main_supervisor_thesis_doc_path;
        if ($request->hasFile('thesis_doc')) {
            $msThesisDocPath = $this->ptsDocService->handleInProgressFile(
                $request->file('thesis_doc'),
                null,
                $thesis->student->roll_number,
                $thesis->id,
                'pts4',
                'Thesis_Document',
                'Supervisor_Modified'
            );
        }

        // Check if any co-supervisors exist
        $hasCoSup = false;
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts4->$col) {
                $hasCoSup = true;
                break;
            }
        }
        $nextStage = $hasCoSup ? 'co_supervisors' : 'academic_office';

        $pts4->update([
            'main_supervisor_thesis_title' => $validated['thesis_title'],
            'main_supervisor_thesis_doc_path' => $msThesisDocPath,
            'main_supervisor_recommendation' => $request->boolean('recommendation'),
            'main_supervisor_student_comment' => $validated['main_supervisor_student_comment'] ?? null,
            'main_supervisor_confidential_remark' => $validated['main_supervisor_confidential_remark'] ?? null,
            'main_supervisor_submitted_at' => now(),
            'main_supervisor_id' => auth()->id(),
            'current_stage' => $nextStage,
            'status' => 'in_progress',
        ]);

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-4 Form endorsed and forwarded successfully.');
    }

    // Display dedicated full-page review & endorsement view for PTS-4 with complete audit trail.
    public function review(Pts4Form $pts4)
    {
        $this->authorize('review', $pts4);

        $user = auth()->user();
        $thesis = $pts4->thesis;
        $student = $thesis->student;

        if ($student->isMainSupervisor($user) && $pts4->current_stage === 'main_supervisor') {
            return redirect()->route('faculty.pts4.edit', $pts4->id);
        }

        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;

        $mainSupervisor = $pts4->mainSupervisor ?? $student->mainSupervisors->first();
        $coSupervisors = $pts4->getCoSupervisors();
        $academicOffice = $user->isAcademicOffice();
        $isDr = $user->isDr();

        return view('pts4.review', compact(
            'user',
            'pts4',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'academicOffice',
            'isDr'
        ));
    }

    // Handle Endorsement of PTS-4 by evaluating authorities.
    public function endorse(EndorsePts4Request $request, Pts4Form $pts4)
    {
        $user = auth()->user();
        $thesis = $pts4->thesis;
        $stage = $pts4->current_stage;
        $validated = $request->validated();

        $isRecommended = $request->boolean('recommendation');
        $comment = $validated['student_comment'] ?? null;
        $remark = $validated['confidential_remark'] ?? null;

        switch ($stage) {
            case 'co_supervisors':
                $roleKey = null;
                for ($i = 1; $i <= 10; $i++) {
                    $col = "co_supervisor_{$i}_id";
                    if ($pts4->$col === $user->id) {
                        $roleKey = $i;
                        break;
                    }
                }

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                }

                $pts4->update([
                    "co_supervisor_{$roleKey}_recommendation" => $isRecommended,
                    "co_supervisor_{$roleKey}_student_comment" => $comment,
                    "co_supervisor_{$roleKey}_confidential_remark" => $remark,
                    "co_supervisor_{$roleKey}_submitted_at" => now(),
                ]);

                // Check if all assigned co-supervisors have submitted recommendations
                $allCoDone = true;
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    if ($pts4->$idCol && is_null($pts4->$recCol)) {
                        $allCoDone = false;
                        break;
                    }
                }

                if ($allCoDone) {
                    $pts4->update([
                        'current_stage' => 'academic_office',
                        'co_supervisors_submitted_at' => now(),
                    ]);
                }
                break;

            case 'academic_office':
                if (!$user->isAcademicOffice() && !$user->isGlobalAuthority()) {
                    return back()->with('error', 'Unauthorized access.');
                }

                $pts4->update([
                    'academic_office_is_verified' => true,
                    'academic_office_verification_remark' => $validated['verification_remark'],
                    'academic_office_submitted_at' => now(),
                    'academic_office_user_id' => $user->id,
                    'current_stage' => 'dr',
                ]);
                break;

            case 'dr':
                if (!$user->isDr()) {
                    return back()->with('error', 'Unauthorized access.');
                }

                $pts4->update([
                    'dr_student_comment' => $comment,
                    'dr_approval' => $isRecommended,
                    'dr_confidential_remark' => $remark,
                    'dr_submitted_at' => now(),
                    'dr_user_id' => $user->id,
                    'approved_by_id' => $user->id,
                    'current_stage' => 'completed',
                    'status' => $isRecommended ? 'approved' : 'rejected',
                ]);

                if ($isRecommended) {
                    $this->ptsDocService->moveToApproved($pts4, 'pts4');
                    $this->ptsDocService->generateThesisCertificate($pts4);
                } else {
                    $this->ptsDocService->moveToRejected($pts4, 'pts4');
                }
                break;

            default:
                return back()->with('error', 'Invalid stage for endorsement.');
        }

        return redirect()->route('dashboard')->with('success', 'PTS-4 form evaluated and submitted successfully!');
    }

    // Universal Pop-up Reversion action for all evaluating authorities.
    public function revert(RevertPts4Request $request, Pts4Form $pts4)
    {
        $user = auth()->user();
        $thesis = $pts4->thesis;
        $student = $thesis?->student;
        $stage = $pts4->current_stage;

        if ($user->isAcademicOffice() || $stage === 'academic_office') {
            return back()->with('error', 'Academic Office cannot revert forms.');
        }

        $validated = $request->validated();
        $revertedRole = null;

        if ($stage === 'main_supervisor') {
            if (!$student || !$student->isMainSupervisor($user)) {
                return back()->with('error', 'Unauthorized access. Only Main Supervisor can revert at this stage.');
            }
            $revertedRole = 'main_supervisor';
        } elseif ($stage === 'co_supervisors') {
            for ($i = 1; $i <= 10; $i++) {
                $col = "co_supervisor_{$i}_id";
                if ($pts4->$col === $user->id) {
                    $revertedRole = "co_supervisor_{$i}";
                    break;
                }
            }
            if (!$revertedRole) {
                return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
            }
        } elseif ($stage === 'dr') {
            if (!$user->isDr()) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'dr';
        } else {
            return back()->with('error', 'Invalid stage for reversion.');
        }

        $pts4->update([
            'status' => 'reverted',
            'current_stage' => 'reverted',
            'reverted_by_role' => $revertedRole,
            'reverted_by_id' => $user->id,
            'reversion_comment' => $validated['reversion_comment'],
        ]);

        $this->ptsDocService->moveToReverted($pts4, 'pts4');

        return redirect()->route('dashboard')->with('warning', 'PTS-4 Thesis Form has been reverted back to the student.');
    }

    // Show read-only details of an approved/rejected PTS-4 form.
    public function show(Pts4Form $pts4)
    {
        $user = auth()->user();
        $thesis = $pts4->thesis;
        $student = $thesis->student;
        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;

        // Authorization check: User must be authorized to view this submission
        if (!$pts4->canUserView($user)) {
            abort(403, 'Unauthorized access to view this submission.');
        }

        // Guardrail: Active forms should go to submitted view
        if ($pts4->status === 'in_progress') {
            return redirect()->route('pts4.submitted', $pts4->id);
        }

        if ($pts4->status === 'reverted') {
            return redirect()->route('pts4.reverted', $pts4->id);
        }

        $mainSupervisor = $pts4->mainSupervisor ?? $student->mainSupervisors->first();
        $coSupervisors = $pts4->getCoSupervisors();

        return view('pts4.show', compact(
            'pts4',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors'
        ));
    }

    // Display view of submitted PTS-4 form strictly scoped to viewing user's submission state.
    public function submitted(Pts4Form $pts4)
    {
        $user = auth()->user();
        $thesis = $pts4->thesis;
        $student = $thesis->student;
        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;

        // Status Guardrails
        if (in_array($pts4->status, ['approved', 'rejected'])) {
            return redirect()->route('pts4.show', $pts4->id);
        }

        if ($pts4->status === 'reverted') {
            return redirect()->route('pts4.reverted', $pts4->id);
        }

        // Check workflow stage progression & access
        $accessStatus = $pts4->getUserSubmissionAccessStatus($user);
        if ($accessStatus === 'pending_endorsement') {
            if ($student->isMainSupervisor($user) && $pts4->current_stage === 'main_supervisor') {
                return redirect()->route('faculty.pts4.edit', $pts4->id);
            }
            return redirect()->route('pts4.review', $pts4->id);
        }
        if ($accessStatus === 'not_reached' || $accessStatus === 'unauthorized') {
            abort(403, 'This submission has not reached your review stage yet.');
        }

        // Role checks
        $isOwnerStudent = ($user->isStudent() && $student->user_id === $user->id);
        $isMainSupervisor = $student->isMainSupervisor($user);

        // Find if user is a specific co-supervisor
        $myCoSupSlot = null;
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts4->$col == $user->id) {
                $myCoSupSlot = $i;
                break;
            }
        }
        $isCoSupervisor = ($myCoSupSlot !== null);

        $isAcademicOffice = $user->isAcademicOffice();
        $isDr = $user->isDr();

        $mainSupervisor = $pts4->mainSupervisor ?? $student->mainSupervisors->first();
        $coSupervisors = $pts4->getCoSupervisors();

        $viewPerspective = 'guest';
        if ($isOwnerStudent) $viewPerspective = 'student';
        elseif ($isMainSupervisor) $viewPerspective = 'main_supervisor';
        elseif ($isCoSupervisor) $viewPerspective = 'co_supervisor';
        elseif ($isAcademicOffice) $viewPerspective = 'academic_office';
        elseif ($isDr) $viewPerspective = 'dr';

        return view('pts4.submitted', compact(
            'pts4',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'viewPerspective',
            'myCoSupSlot'
        ));
    }

    // Show dedicated view for reverted PTS-4 form.
    public function reverted(Pts4Form $pts4)
    {
        $user = auth()->user();

        // Guardrail: Reverted view is only for reverted status
        if ($pts4->status !== 'reverted') {
            if (in_array($pts4->status, ['approved', 'rejected'])) {
                return redirect()->route('pts4.show', $pts4->id);
            }
            return redirect()->route('pts4.submitted', $pts4->id);
        }

        // Student owner redirects to student form edit
        if ($user->isStudent()) {
            if ($pts4->thesis?->student?->user_id === $user->id) {
                return redirect()->route('student.pts4.edit');
            }
            abort(403, 'Unauthorized access to reverted PTS-4 view.');
        }

        if (!$pts4->canUserViewRevertedForm($user)) {
            abort(403, 'Unauthorized access to reverted PTS-4 view.');
        }

        $thesis = $pts4->thesis;
        $student = $thesis->student;
        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;
        $mainSupervisor = $pts4->mainSupervisor ?? $student->mainSupervisors->first();
        $coSupervisors = $pts4->getCoSupervisors();

        return view('pts4.reverted', compact(
            'pts4',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors'
        ));
    }

    /**
     * Download the official Thesis Submission Certificate.
     */
    public function downloadCertificate(Pts4Form $pts4)
    {
        $user = auth()->user();
        $thesis = $pts4->thesis;
        $student = $thesis?->student;

        // Authorization: student owner, faculty, authority, or admin
        $isOwner = $student && $student->user_id === $user?->id;
        $isStaffOrAuth = $user && ($user->isFaculty() || $user->isHod() || $user->isDpgc() || $user->isGlobalAuthority() || $user->isActingApprovalAuthority()) || auth('admin')->check();

        if (!$isOwner && !$isStaffOrAuth) {
            abort(403, 'Unauthorized access to thesis certificate.');
        }

        if ($pts4->status !== 'approved') {
            abort(404, 'Thesis certificate is only available for approved PTS-4 submissions.');
        }

        // Generate certificate if not already created
        if (!$pts4->thesis_certificate_doc_path || !\Illuminate\Support\Facades\Storage::disk('local')->exists($pts4->thesis_certificate_doc_path)) {
            $this->ptsDocService->generateThesisCertificate($pts4);
            $pts4->refresh();
        }

        $filePath = $pts4->thesis_certificate_doc_path;
        if (!$filePath || !\Illuminate\Support\Facades\Storage::disk('local')->exists($filePath)) {
            abort(404, 'Thesis certificate file not found.');
        }

        $rollNumber = $student?->roll_number ?? 'Student';
        $downloadFilename = "{$rollNumber}_Thesiscerificate.pdf";

        return \Illuminate\Support\Facades\Storage::disk('local')->download($filePath, $downloadFilename);
    }
}
