<?php

namespace App\Http\Controllers;

use App\Models\Pts2Form;
use App\Models\Thesis;
use App\Models\User;
use App\Models\VestedDoaa;
use App\Services\PtsDocumentService;
use App\Http\Requests\Pts2\StorePts2Request;
use App\Http\Requests\Pts2\UpdatePts2SupervisorRequest;
use App\Http\Requests\Pts2\EndorsePts2Request;
use App\Http\Requests\Pts2\RevertPts2Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Pts2Controller extends Controller
{
    use AuthorizesRequests;
    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }

    // Display the PTS-2 creation form for students.
    public function create()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->where('status', 'in_progress')->with(['pts1Form', 'pts2Form'])->first();

        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'Please register your thesis title first.');
        }

        // Must have an APPROVED PTS-1 Form (status === 'approved')
        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('warning', 'PTS-2 Synopsis Form is locked until your PTS-1 Form is fully approved.');
        }

        // Must be within the Open Seminar deadline (or approved extension deadline)
        if (!$thesis->isPts2SubmissionActive()) {
            $deadline = $thesis->getPts2Deadline();
            return redirect()->route('student.dashboard')->with('warning', 'The PTS-2 submission deadline passed on ' . ($deadline ? $deadline->format('d-M-Y') : 'the deadline') . '. Please apply for a PTS-2 extension if eligible.');
        }

        $pts2Form = $thesis->pts2Form;
        if ($pts2Form) {
            if ($pts2Form->status === 'in_progress') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-2 form is currently under review.');
            }
            if ($pts2Form->status === 'approved') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-2 form has already been approved.');
            }
            if ($pts2Form->status === 'reverted') {
                return redirect()->route('student.pts2.edit')->with('warning', 'You have a reverted PTS-2 form. Please edit and resubmit your reverted form.');
            }
            if ($pts2Form->status === 'rejected') {
                $pts2Form = null;
            }
        }

        return view('student.pts2.create', compact('user', 'student', 'thesis', 'pts2Form'));
    }

    // Display the PTS-2 edit form for reverted student submissions.
    public function edit()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->where('status', 'in_progress')->with(['pts1Form', 'pts2Form'])->first();

        if (!$thesis) {
            return redirect()->route('student.dashboard')->with('warning', 'No active registered thesis found.');
        }

        $pts2Form = $thesis->pts2Form;
        if (!$pts2Form || $pts2Form->status !== 'reverted') {
            return redirect()->route('student.dashboard')->with('warning', 'You do not have a reverted PTS-2 form to edit.');
        }

        return view('student.pts2.create', compact('user', 'student', 'thesis', 'pts2Form'));
    }

    // Store a newly created / resubmitted PTS-2 submission.
    public function store(StorePts2Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = Thesis::where('student_id', $student->id)->where('status', 'in_progress')->with(['pts1Form', 'pts2Form', 'pts2Extension'])->firstOrFail();

        if (!$thesis->pts1Form || $thesis->pts1Form->status !== 'approved') {
            return redirect()->route('student.dashboard')->with('error', 'Unauthorized: PTS-1 is not approved.');
        }

        if (!$thesis->isPts2SubmissionActive()) {
            $deadline = $thesis->getPts2Deadline();
            return redirect()->route('student.dashboard')->with('error', 'Cannot submit PTS-2: the submission deadline passed on ' . ($deadline ? $deadline->format('d-M-Y') : 'N/A') . '.');
        }

        $pts2Form = $thesis->pts2Form;
        $hasExisting = $pts2Form && in_array($pts2Form->status, ['reverted', 'rejected']);
        $validated = $request->validated();

        // Update active thesis title
        $thesis->update(['title' => $validated['thesis_title']]);

        $filePath = $this->ptsDocService->handleInProgressFile(
            $request->file('synopsis_report_doc'),
            $hasExisting ? $pts2Form?->synopsis_report_doc_path : null,
            $student->roll_number,
            $thesis->id,
            'pts2',
            'Synopsis_Report',
            'Student'
        );

        $activeMainSup = $student->active_main_supervisor;
        if (!$activeMainSup) {
            return back()->withInput()->with('error', 'Unable to submit PTS-2: No active Main Supervisor is assigned to your profile. Please contact the Academic Office.');
        }

        $coSupervisors = $student->activeAllCoSupervisors()->pluck('id')->all();

        // Initialize co-supervisor slot mapping with active supervisors
        $coSupData = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            $coSupData[$col] = $coSupervisors[$i - 1] ?? null;
        }

        $formData = [
            'thesis_id' => $thesis->id,
            'thesis_title' => $validated['thesis_title'],
            'synopsis_report_doc_path' => $filePath,
            'main_supervisor_id' => $activeMainSup->id,
            'vested_doaa_email' => VestedDoaa::getActiveVestedEmail(),
            'current_stage' => 'main_supervisor',
            'status' => 'in_progress',
            'date_of_submission' => now()->toDateString(),
            'course_credits_student' => (float)$validated['course_credits_student'],
            'current_address' => $validated['current_address'],
            'alternate_email' => $validated['alternate_email'] ?? null,
            'recent_phone_number' => $validated['recent_phone_number'],
            'recent_phone_country_code' => $validated['recent_phone_country_code'],
            'recent_phone_iso2' => $validated['recent_phone_iso2'],
            'alternate_phone_number' => $validated['alternate_phone_number'] ?? null,
            'alternate_phone_country_code' => $validated['alternate_phone_country_code'] ?? null,
            'alternate_phone_iso2' => $validated['alternate_phone_iso2'] ?? null,
            'cert_prima_facie_case' => true,
            'cert_no_prior_degree_submission' => true,
            'collaborative_work_status' => $request->boolean('collaborative_work_status'),
            'collaborative_work_details' => $request->boolean('collaborative_work_status') ? $validated['collaborative_work_details'] : null,
        ];

        // Dynamically assign up to 10 active Co-Supervisors
        for ($i = 1; $i <= 10; $i++) {
            $formData["co_supervisor_{$i}_id"] = $coSupervisors[$i - 1] ?? null;
        }

        // Synchronize contact info with student profile
        $student->update([
            'phone_number' => $validated['recent_phone_number'],
            'phone_country_code' => $validated['recent_phone_country_code'],
            'phone_iso2' => $validated['recent_phone_iso2'],
            'alternate_phone_number' => $validated['alternate_phone_number'] ?? null,
            'alternate_phone_country_code' => $validated['alternate_phone_country_code'] ?? '+91',
            'alternate_phone_iso2' => $validated['alternate_phone_iso2'] ?? 'in',
            'alternate_email' => $validated['alternate_email'] ?? null,
            'current_address' => $validated['current_address'],
        ]);

        // Always create a new PTS-2 form row, preserving historical reverted/rejected submissions
        Pts2Form::create($formData);

        $actionVerb = $hasExisting ? 'resubmitted' : 'submitted';

        return redirect()->route('student.dashboard')->with('success', "PTS-2 Synopsis Form {$actionVerb} successfully and forwarded to your Main Supervisor for review.");
    }

    // Show the Main Supervisor edit form for a PTS-2 submission.
    public function mainSupervisorEdit(Pts2Form $pts2)
    {
        $this->authorize('mainSupervisorEdit', $pts2);

        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;

        return view('faculty.pts2.edit', compact('pts2', 'thesis', 'student', 'studentUser'));
    }

    // Process Main Supervisor review submission (Edits, Certifications, Evaluation & Endorsement).
    public function update(UpdatePts2SupervisorRequest $request, Pts2Form $pts2)
    {
        $thesis = $pts2->thesis;
        $validated = $request->validated();

        // Update active thesis title in database
        $thesis->update(['title' => $validated['thesis_title']]);

        // Optional Synopsis Replacement by Main Supervisor
        $msSynopsisPath = $pts2->main_supervisor_synopsis_report_doc_path;
        if ($request->hasFile('synopsis_report_doc')) {
            $msSynopsisPath = $this->ptsDocService->handleInProgressFile($request->file('synopsis_report_doc'), null, $thesis->student->roll_number, $thesis->id, 'pts2', 'Synopsis_Report', 'Supervisor_Modified');
        }

        // Check if any co-supervisors exist
        $hasCoSup = false;
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts2->$col) {
                $hasCoSup = true;
                break;
            }
        }
        $nextStage = $hasCoSup ? 'co_supervisors' : 'academic_office';

        $pts2->update([
            'main_supervisor_thesis_title' => $validated['thesis_title'],
            'main_supervisor_synopsis_report_doc_path' => $msSynopsisPath,
            'main_supervisor_cert_prima_facie_case' => $request->boolean('cert_prima_facie_case'),
            'main_supervisor_cert_no_prior_degree_submission' => $request->boolean('cert_no_prior_degree_submission'),
            'main_supervisor_collaborative_work_status' => $request->boolean('collaborative_work_status'),
            'main_supervisor_collaborative_work_details' => $request->boolean('collaborative_work_status') ? $validated['collaborative_work_details'] : null,
            'main_supervisor_recommendation' => $request->boolean('recommendation'),
            'main_supervisor_student_comment' => $validated['main_supervisor_student_comment'] ?? null,
            'main_supervisor_confidential_remark' => $validated['main_supervisor_confidential_remark'] ?? null,
            'main_supervisor_submitted_at' => now(),
            'current_stage' => $nextStage,
            'status' => 'in_progress',
        ]);

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-2 Form endorsed and forwarded successfully.');
    }

    // Display dedicated full-page review & endorsement view for PTS-2 with complete audit trail.
    public function review(Pts2Form $pts2)
    {
        $this->authorize('review', $pts2);

        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;

        if ($student->isMainSupervisor($user) && $pts2->current_stage === 'main_supervisor') {
            return redirect()->route('faculty.pts2.edit', $pts2->id);
        }

        $student->loadMissing(['mainSupervisors', 'coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;

        $mainSupervisor = $pts2->mainSupervisor ?? $student->mainSupervisors->first();
        $coSupervisors = $pts2->getCoSupervisors();

        $academicOffice = $user->isAcademicOffice();
        $actingDoaaUsers = \App\Models\ActingDoaa::where('is_acting_doaa', true)->with('user')->get()->pluck('user')->filter();

        return view('pts2.review', compact(
            'user',
            'pts2',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'academicOffice',
            'actingDoaaUsers'
        ));
    }

    // Handle Endorsement of PTS-2 by evaluating authorities.
    public function endorse(EndorsePts2Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $stage = $pts2->current_stage;
        $validated = $request->validated();

        $isRecommended = $request->boolean('recommendation');
        $comment = $validated['student_comment'] ?? null;
        $remark = $validated['confidential_remark'] ?? null;

        switch ($stage) {
            case 'co_supervisors':
                $roleKey = null;
                for ($i = 1; $i <= 10; $i++) {
                    $col = "co_supervisor_{$i}_id";
                    if ($pts2->$col === $user->id) {
                        $roleKey = $i;
                        break;
                    }
                }

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                }

                $pts2->update([
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
                    if ($pts2->$idCol && is_null($pts2->$recCol)) {
                        $allCoDone = false;
                        break;
                    }
                }

                if ($allCoDone) {
                    $pts2->update([
                        'current_stage' => 'academic_office',
                        'co_supervisors_submitted_at' => now(),
                    ]);
                }
                break;

            case 'academic_office':
                if (!$user->isAcademicOffice() && !$user->isGlobalAuthority()) {
                    return back()->with('error', 'Unauthorized access.');
                }

                $academicOfficeCourseCredits = (float)$validated['academic_office_course_credits'];
                $actingDoaaEmail = $request->filled('acting_doaa_email') ? $request->input('acting_doaa_email') : null;

                $pts2->update([
                    'academic_office_is_verified' => true,
                    'academic_office_verification_remark' => $validated['verification_remark'] ?? $remark,
                    'academic_office_course_credits' => $academicOfficeCourseCredits,
                    'academic_office_submitted_at' => now(),
                    'academic_office_user_id' => $user->id,
                    'acting_doaa_email' => $actingDoaaEmail,
                    'current_stage' => 'doaa',
                ]);

                // If Academic Office provides verified/adjusted course credits, override student's course credits earned
                if ($academicOfficeCourseCredits !== null) {
                    $thesis->student->update([
                        'course_credits_earned' => $academicOfficeCourseCredits,
                    ]);
                }
                break;

            case 'doaa':
                if (!($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts2->acting_doaa_email === $user->email || $pts2->vested_doaa_email === $user->email)))) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts2->update([
                    'doaa_student_comment' => $comment,
                    'doaa_approval' => $isRecommended,
                    'doaa_confidential_remark' => $remark,
                    'doaa_submitted_at' => now(),
                    'doaa_user_id' => $user->id,
                    'approved_by_id' => $user->id,
                    'current_stage' => 'completed',
                    'status' => $isRecommended ? 'approved' : 'rejected',
                ]);

                if ($isRecommended) {
                    $this->ptsDocService->moveToApproved($pts2, 'pts2');
                } else {
                    $this->ptsDocService->moveToRejected($pts2, 'pts2');
                }
                break;

            default:
                return back()->with('error', 'Invalid stage for endorsement.');
        }

        return redirect()->route('dashboard')->with('success', 'PTS-2 form evaluated and submitted successfully!');
    }

    // Universal Pop-up Reversion action for all evaluating authorities.
    public function revert(RevertPts2Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis?->student;
        $stage = $pts2->current_stage;

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
                if ($pts2->$col === $user->id) {
                    $revertedRole = "co_supervisor_{$i}";
                    break;
                }
            }
            if (!$revertedRole) {
                return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
            }
        } elseif ($stage === 'doaa') {
            if (!($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts2->acting_doaa_email === $user->email || $pts2->vested_doaa_email === $user->email)))) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'doaa';
        } else {
            return back()->with('error', 'Invalid stage for reversion.');
        }

        $pts2->update([
            'status' => 'reverted',
            'current_stage' => 'reverted',
            'reverted_by_role' => $revertedRole,
            'reverted_by_id' => $user->id,
            'reversion_comment' => $validated['reversion_comment'],
        ]);

        $this->ptsDocService->moveToReverted($pts2, 'pts2');

        return redirect()->route('dashboard')->with('warning', 'PTS-2 Synopsis Form has been reverted back to the student.');
    }

    // Show read-only details of an approved/rejected PTS-2 form.
    public function show(Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $student->loadMissing(['coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;

        // Authorization check: User must be authorized to view this submission
        if (!$pts2->canUserView($user)) {
            abort(403, 'Unauthorized access to view this submission.');
        }

        if ($pts2->status === 'in_progress') {
            return redirect()->route('pts2.submitted', $pts2->id);
        }

        if ($pts2->status === 'reverted') {
            return redirect()->route('pts2.reverted', $pts2->id);
        }

        $mainSupervisor = $pts2->mainSupervisor ?? $student->mainSupervisors->first();
        $coSupervisors = $pts2->getCoSupervisors();

        return view('pts2.show', compact(
            'pts2',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors'
        ));
    }


    // Display view of submitted PTS-2 form strictly scoped to viewing user's submission state.
    public function submitted(Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $student->loadMissing(['coSupervisors', 'externalSupervisors.externalSupervisorProfile']);

        // Status Guardrails
        if (in_array($pts2->status, ['approved', 'rejected'])) {
            return redirect()->route('pts2.show', $pts2->id);
        }

        if ($pts2->status === 'reverted') {
            return redirect()->route('pts2.reverted', $pts2->id);
        }

        // Check workflow stage progression & access
        $accessStatus = $pts2->getUserSubmissionAccessStatus($user);
        if ($accessStatus === 'pending_endorsement') {
            if ($student->isMainSupervisor($user) && $pts2->current_stage === 'main_supervisor') {
                return redirect()->route('faculty.pts2.edit', $pts2->id);
            }
            return redirect()->route('pts2.review', $pts2->id);
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
            if ($pts2->$col == $user->id) {
                $myCoSupSlot = $i;
                break;
            }
        }
        $isCoSupervisor = ($myCoSupSlot !== null);
        $isAcademicOffice = $user->isAcademicOffice();
        $isDpgc = $user->isDpgc();
        $isHod = $user->isHod();
        $isDoaa = ($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic() || ($user->isActingApprovalAuthority() && ($pts2->acting_doaa_email === $user->email || $pts2->vested_doaa_email === $user->email)));

        $studentUser = $student->user;
        $mainSupervisor = $pts2->mainSupervisor ?? $student->mainSupervisors->first();
        $coSupervisors = $pts2->getCoSupervisors();

        // Determine viewPerspective: 'student', 'main_supervisor', 'co_supervisor', 'dpgc', 'hod', 'academic_office', 'doaa'
        $viewPerspective = 'student';
        if ($isOwnerStudent) {
            $viewPerspective = 'student';
        } elseif ($isMainSupervisor) {
            $viewPerspective = 'main_supervisor';
        } elseif ($isCoSupervisor) {
            $viewPerspective = 'co_supervisor';
        } elseif ($isDpgc) {
            $viewPerspective = 'dpgc';
        } elseif ($isHod) {
            $viewPerspective = 'hod';
        } elseif ($isAcademicOffice) {
            $viewPerspective = 'academic_office';
        } elseif ($isDoaa) {
            $viewPerspective = 'doaa';
        }

        return view('pts2.submitted', compact(
            'pts2',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'viewPerspective',
            'myCoSupSlot'
        ));
    }

    // Show dedicated view for reverted PTS-2 form.
    public function reverted(Pts2Form $pts2)
    {
        $user = auth()->user();

       // Guardrail: Reverted view is only for reverted status
        if ($pts2->status !== 'reverted') {
            if (in_array($pts2->status, ['approved', 'rejected'])) {
                return redirect()->route('pts2.show', $pts2->id);
            }
            return redirect()->route('pts2.submitted', $pts2->id);
        }

        // Student owner redirects to student form creation/edit; other students blocked
        if ($user->isStudent()) {
            if ($pts2->thesis?->student?->user_id === $user->id) {
                return redirect()->route('student.pts2.create');
            }
            abort(403, 'Unauthorized access to reverted PTS-2 view.');
        }

        if (!$pts2->canUserViewRevertedForm($user)) {
            abort(403, 'Unauthorized access to reverted PTS-2 view.');
        }

        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $student->loadMissing(['coSupervisors', 'externalSupervisors.externalSupervisorProfile']);
        $studentUser = $student->user;
        $mainSupervisor = $pts2->mainSupervisor ?? $student->mainSupervisors->first();
        $coSupervisors = $pts2->getCoSupervisors();

        return view('pts2.reverted', compact(
            'pts2',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors'
        ));
    }
}
