<?php

namespace App\Http\Controllers;

use App\Http\Requests\Pts1\StorePts1Request;
use App\Models\Pts1Form;
use App\Models\Thesis;
use App\Models\User;
use App\Models\VestedDoaa;
use App\Services\PtsDocumentService;
use App\Http\Requests\Pts1\UpdatePts1SupervisorRequest;
use App\Http\Requests\Pts1\EndorsePts1Request;
use App\Http\Requests\Pts1\RevertPts1Request;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Request;

class Pts1Controller extends Controller
{
    use AuthorizesRequests;
    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }

    // Display the PTS-1 creation form for students.
    public function create()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        // Check if student has an active thesis with status in_progress
        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('info', 'This thesis has already been completed.');
            }
            return redirect()->route('student.dashboard')->with('warning', 'Please register your thesis title first.');
        }

        $pts1Form = $thesis->pts1Form;
        if ($pts1Form) {
            if ($pts1Form->status === 'in_progress') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-1 form is currently under review.');
            }
            if ($pts1Form->status === 'approved') {
                return redirect()->route('student.dashboard')->with('info', 'Your PTS-1 form has already been approved.');
            }
            if ($pts1Form->status === 'reverted') {
                return redirect()->route('student.pts1.edit')->with('warning', 'You have a reverted PTS-1 form. Please edit and resubmit your reverted form.');
            }
            if ($pts1Form->status === 'rejected') {
                $pts1Form = null;
            }
        }

        return view('student.pts1.create', compact('user', 'student', 'thesis', 'pts1Form'));
    }

    // Download the sample Excel publication list template.
    public function downloadTemplate()
    {
        $customTemplatePath = public_path('templates/publication_list_template.xlsx');
        
        if (file_exists($customTemplatePath)) {
            return response()->download($customTemplatePath);
        }

        $xlsTemplatePath = public_path('templates/publication_list_template.xls');
        if (file_exists($xlsTemplatePath)) {
            return response()->download($xlsTemplatePath);
        }

        abort(404, 'Publication list template file not found.');
    }

    // Display the PTS-1 edit form for reverted student submissions.
    public function edit()
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        // Check if student has an active thesis with status in_progress
        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('info', 'This thesis has already been completed.');
            }
            return redirect()->route('student.dashboard')->with('warning', 'Please register your thesis title first.');
        }

        $pts1Form = $thesis->pts1Form;
        if (!$pts1Form || $pts1Form->status !== 'reverted') {
            return redirect()->route('student.dashboard')->with('warning', 'You do not have a reverted PTS-1 form to edit.');
        }

        return view('student.pts1.create', compact('user', 'student', 'thesis', 'pts1Form'));
    }

    // Store a newly created / resubmitted PTS-1 submission.
    public function store(StorePts1Request $request)
    {
        $user = auth()->user();
        $student = $user->student;

        if (!$student) {
            return redirect()->route('student.dashboard')->with('error', 'Student profile not found.');
        }

        $thesis = $student->theses()->where('status', 'in_progress')->latest()->first();
        if (!$thesis) {
            if ($student->hasCompletedThesis()) {
                return redirect()->route('student.dashboard')->with('error', 'This thesis has already been completed.');
            }
            return redirect()->route('student.dashboard')->with('error', 'Please register your thesis title on your dashboard first before submitting PTS-1.');
        }

        $pts1Form = $thesis->pts1Form;
        $hasExisting = $pts1Form && in_array($pts1Form->status, ['reverted', 'rejected']);
        $validated = $request->validated();

        $pubNormFulfilled = $request->boolean('publication_norm_fulfillment');
        $pubSpecialApproval = $pubNormFulfilled ? null : ($request->has('special_approval_publication') ? $request->boolean('special_approval_publication') : null);

        $minTimeFulfilled = $request->boolean('min_time_req_fulfilled');
        $minTimeSpecialApproval = $minTimeFulfilled ? null : ($request->has('special_approval_min_time') ? $request->boolean('special_approval_min_time') : null);

        // Guard validation: If norm/min-time is false and special approval is not true, reject
        if (!$pubNormFulfilled && !$pubSpecialApproval) {
            return back()->withInput()->withErrors(['special_approval_publication' => 'Special approval is required when publication norm criteria is not fulfilled.']);
        }

        if (!$minTimeFulfilled && !$minTimeSpecialApproval) {
            return back()->withInput()->withErrors(['special_approval_min_time' => 'Special approval is required when minimum time requirement criteria is not fulfilled.']);
        }

        // Update student confirmation date
        $student->update(['date_confirmation' => $validated['date_confirmation']]);

        // Update thesis title in database
        $thesis->update(['title' => $validated['thesis_title']]);

        // Handle private local file uploads
        $pubAppDocPath = null;
        if (!$pubNormFulfilled && $pubSpecialApproval) {
            $pubAppDocPath = $this->ptsDocService->handleInProgressFile(
                $request->file('publication_approval_doc'),
                $hasExisting ? $pts1Form?->publication_approval_doc_path : null,
                $student->roll_number,
                $thesis->id,
                'pts1',
                'Publication_Approval',
                'Student'
            );
        }

        $minTimeAppDocPath = null;
        if (!$minTimeFulfilled && $minTimeSpecialApproval) {
            $minTimeAppDocPath = $this->ptsDocService->handleInProgressFile(
                $request->file('min_time_approval_doc'),
                $hasExisting ? $pts1Form?->min_time_approval_doc_path : null,
                $student->roll_number,
                $thesis->id,
                'pts1',
                'Min_Time_Approval',
                'Student'
            );
        }

        $synopsisPath = $this->ptsDocService->handleInProgressFile(
            $request->file('draft_synopsis_report'),
            $hasExisting ? $pts1Form?->draft_synopsis_report_doc_path : null,
            $student->roll_number,
            $thesis->id,
            'pts1',
            'Draft_Synopsis',
            'Student'
        );

        $pubListPath = $this->ptsDocService->handleInProgressFile(
            $request->file('publication_list'),
            $hasExisting ? $pts1Form?->publication_list_doc_path : null,
            $student->roll_number,
            $thesis->id,
            'pts1',
            'Publication_List',
            'Student'
        );

        // Committee Co-Supervisors & PSPC IDs (Active Only)
        $activeMainSup = $student->active_main_supervisor;
        if (!$activeMainSup) {
            return back()->withInput()->with('error', 'Unable to submit PTS-1: No active Main Supervisor is assigned to your profile. Please contact the Academic Office.');
        }

        $coSupervisors = $student->activeAllCoSupervisors()->pluck('id')->all();
        $pspcMembers = $student->activePspcMembers()->pluck('id')->all();

        $formData = [
            'thesis_id' => $thesis->id,
            'thesis_title' => $validated['thesis_title'],
            'seminar_date' => $validated['seminar_date'],
            'seminar_time' => $validated['seminar_time'],
            'seminar_venue' => $validated['seminar_venue'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'publication_norm_fulfillment' => $pubNormFulfilled,
            'special_approval_publication' => $pubSpecialApproval,
            'publication_approval_doc_path' => $pubAppDocPath,
            'min_time_req_fulfilled' => $minTimeFulfilled,
            'special_approval_min_time' => $minTimeSpecialApproval,
            'min_time_approval_doc_path' => $minTimeAppDocPath,
            'draft_synopsis_report_doc_path' => $synopsisPath,
            'publication_list_doc_path' => $pubListPath,
            'main_supervisor_draft_synopsis_report_doc_path' => $synopsisPath,
            'main_supervisor_publication_list_doc_path' => $pubListPath,
            'main_supervisor_publication_approval_doc_path' => $pubAppDocPath,
            'main_supervisor_min_time_approval_doc_path' => $minTimeAppDocPath,
            'main_supervisor_id' => $activeMainSup->id,
            'vested_doaa_email' => VestedDoaa::getActiveVestedEmail(),
            'current_stage' => 'main_supervisor',
            'status' => 'in_progress',
        ];

        // Dynamically assign up to 10 active Co-Supervisors and 10 active PSPC Members
        for ($i = 1; $i <= 10; $i++) {
            $formData["co_supervisor_{$i}_id"] = $coSupervisors[$i - 1] ?? null;
            $formData["pspc_member_{$i}_id"] = $pspcMembers[$i - 1] ?? null;
        }

        // Create new active PTS-1 Form
        Pts1Form::create($formData);

        $actionVerb = $hasExisting ? 'resubmitted' : 'submitted';

        return redirect()->route('student.dashboard')->with('success', "PTS-1 form {$actionVerb} successfully and forwarded to your Main Supervisor for review!");
    }

    // Show the Main Supervisor edit form for a PTS-1 submission.
    public function mainSupervisorEdit(Pts1Form $pts1)
    {
        $this->authorize('mainSupervisorEdit', $pts1);

        $thesis = $pts1->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;

        return view('faculty.pts1.edit', compact('pts1', 'thesis', 'student', 'studentUser'));
    }

    // Process Main Supervisor review submission (Edits, Evaluation & Endorsement/Reversion).
    public function update(UpdatePts1SupervisorRequest $request, Pts1Form $pts1)
    {
        $thesis = $pts1->thesis;
        $validated = $request->validated();
        $pubNormFulfilled = $request->boolean('publication_norm_fulfillment');
        $pubSpecialApproval = $pubNormFulfilled ? null : ($request->has('special_approval_publication') ? $request->boolean('special_approval_publication') : null);

        $minTimeFulfilled = $request->boolean('min_time_req_fulfilled');
        $minTimeSpecialApproval = $minTimeFulfilled ? null : ($request->has('special_approval_min_time') ? $request->boolean('special_approval_min_time') : null);

        // Update active thesis title in database
        $thesis->update(['title' => $validated['thesis_title']]);

        $studentRoll = $thesis->student->roll_number;

        // Optional File Replacements by Main Supervisor
        $msPubAppPath = $pts1->main_supervisor_publication_approval_doc_path;
        if (!$pubNormFulfilled && $pubSpecialApproval && $request->hasFile('publication_approval_doc')) {
            $msPubAppPath = $this->ptsDocService->handleInProgressFile($request->file('publication_approval_doc'), null, $studentRoll, $thesis->id, 'pts1', 'Publication_Approval', 'Supervisor_Modified');
        }

        $msMinTimeAppPath = $pts1->main_supervisor_min_time_approval_doc_path;
        if (!$minTimeFulfilled && $minTimeSpecialApproval && $request->hasFile('min_time_approval_doc')) {
            $msMinTimeAppPath = $this->ptsDocService->handleInProgressFile($request->file('min_time_approval_doc'), null, $studentRoll, $thesis->id, 'pts1', 'Min_Time_Approval', 'Supervisor_Modified');
        }

        $msSynopsisPath = $pts1->main_supervisor_draft_synopsis_report_doc_path;
        if ($request->hasFile('draft_synopsis_report')) {
            $msSynopsisPath = $this->ptsDocService->handleInProgressFile($request->file('draft_synopsis_report'), null, $studentRoll, $thesis->id, 'pts1', 'Draft_Synopsis', 'Supervisor_Modified');
        }

        $msPubListPath = $pts1->main_supervisor_publication_list_doc_path;
        if ($request->hasFile('publication_list')) {
            $msPubListPath = $this->ptsDocService->handleInProgressFile($request->file('publication_list'), null, $studentRoll, $thesis->id, 'pts1', 'Publication_List', 'Supervisor_Modified');
        }

        // Update Student confirmation date
        $thesis->student->update(['date_confirmation' => $validated['date_confirmation']]);

        // Determine Next Stage for Forwarding
        $coSupervisorsCount = $thesis->student ? $thesis->student->allCoSupervisors()->count() : 0;
        $pspcMembersCount = $thesis->student ? $thesis->student->pspcMembers()->count() : 0;

        $nextStage = 'dpgc';
        if ($coSupervisorsCount > 0) {
            $nextStage = 'co_supervisors';
        } elseif ($pspcMembersCount > 0) {
            $nextStage = 'pspc_members';
        }

        $pts1->update([
            'main_supervisor_thesis_title' => $validated['thesis_title'],
            'main_supervisor_seminar_date' => $validated['seminar_date'],
            'main_supervisor_seminar_time' => $validated['seminar_time'],
            'main_supervisor_seminar_venue' => $validated['seminar_venue'],
            'main_supervisor_meeting_link' => $validated['meeting_link'] ?? null,
            'main_supervisor_publication_norm_fulfillment' => $pubNormFulfilled,
            'main_supervisor_special_approval_publication' => $pubSpecialApproval,
            'main_supervisor_min_time_req_fulfilled' => $minTimeFulfilled,
            'main_supervisor_special_approval_min_time' => $minTimeSpecialApproval,
            'main_supervisor_date_confirmation' => $validated['date_confirmation'],

            'main_supervisor_draft_synopsis_report_doc_path' => $msSynopsisPath,
            'main_supervisor_publication_list_doc_path' => $msPubListPath,
            'main_supervisor_publication_approval_doc_path' => $pubNormFulfilled ? null : $msPubAppPath,
            'main_supervisor_min_time_approval_doc_path' => $minTimeFulfilled ? null : $msMinTimeAppPath,
            'work_status' => $validated['work_status'],
            'main_supervisor_student_comment' => $validated['main_supervisor_student_comment'],
            'main_supervisor_confidential_remark' => $validated['main_supervisor_confidential_remark'] ?? null,
            'main_supervisor_recommendation' => ($validated['work_status'] === 'adequate'),
            'current_stage' => $nextStage,
            'status' => 'in_progress',
            'main_supervisor_submitted_at' => now(),
        ]);

        return redirect()->route('faculty.dashboard')->with('success', 'PTS-1 form submitted successfully and forwarded to next stage.');
    }

    // Display dedicated full-page review & endorsement view for PTS-1 with complete audit trail.
    public function review(Pts1Form $pts1)
    {
        $this->authorize('review', $pts1);

        $user = auth()->user();
        $thesis = $pts1->thesis;
        $student = $thesis->student;
        $student->loadMissing(['coSupervisors', 'externalSupervisors.externalSupervisorProfile', 'pspcMembers']);
        $studentUser = $student->user;
        $mainSupervisor = $pts1->mainSupervisor ?? $student->mainSupervisors->first();

        $coSupervisors = $pts1->getCoSupervisors();
        $pspcMembers = $pts1->getPspcMembers();

        $academicoffice = $user->isAcademicOffice();
        $actingDoaaUsers = \App\Models\ActingDoaa::where('is_acting_doaa', true)->with('user')->get()->pluck('user')->filter();

        return view('pts1.review', compact(
            'user',
            'pts1',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'pspcMembers',
            'academicoffice',
            'actingDoaaUsers'
        ));
    }

    // Handle Endorsement of PTS-1 by an authority (Co-Supervisor, PSPC, DPGC, HOD, Academic Office, DOAA).
    public function endorse(EndorsePts1Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;
        $stage = $pts1->current_stage;
        $validated = $request->validated();

        if ($stage === 'academic_office') {
            $isRecommended = true;
            $comment = null;
            $remark = $validated['confidential_remark'];
        } else {
            $isRecommended = $request->boolean('recommendation');
            $comment = $validated['student_comment'] ?? null;
            $remark = $validated['confidential_remark'] ?? null;
        }

        switch ($stage) {
            case 'co_supervisors':
                $roleKey = null;
                for ($i = 1; $i <= 10; $i++) {
                    $col = "co_supervisor_{$i}_id";
                    if ($pts1->$col && (int)$pts1->$col === (int)$user->id) {
                        $roleKey = $i;
                        break;
                    }
                }

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                }

                $pts1->update([
                    "co_supervisor_{$roleKey}_recommendation" => $isRecommended,
                    "co_supervisor_{$roleKey}_confidential_remark" => $remark,
                    "co_supervisor_{$roleKey}_submitted_at" => now(),
                ]);

                // Check if all assigned co-supervisors have submitted recommendations
                $allCoDone = true;
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    $recCol = "co_supervisor_{$i}_recommendation";
                    if ($pts1->$idCol && is_null($pts1->$recCol)) {
                        $allCoDone = false;
                        break;
                    }
                }

                if ($allCoDone) {
                    $hasPspc = false;
                    for ($i = 1; $i <= 10; $i++) {
                        $col = "pspc_member_{$i}_id";
                        if ($pts1->$col) {
                            $hasPspc = true;
                            break;
                        }
                    }
                    $nextStage = $hasPspc ? 'pspc_members' : 'dpgc';
                    $pts1->update([
                        'current_stage' => $nextStage,
                        'co_supervisors_submitted_at' => now(),
                    ]);
                }
                break;

            case 'pspc_members':
                $roleKey = null;
                for ($i = 1; $i <= 10; $i++) {
                    $col = "pspc_member_{$i}_id";
                    if ($pts1->$col && (int)$pts1->$col === (int)$user->id) {
                        $roleKey = $i;
                        break;
                    }
                }

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned PSPC member for this thesis.');
                }

                $pts1->update([
                    "pspc_member_{$roleKey}_recommendation" => $isRecommended,
                    "pspc_member_{$roleKey}_confidential_remark" => $remark,
                    "pspc_member_{$roleKey}_submitted_at" => now(),
                ]);

                // Check if all assigned PSPC members have submitted recommendations
                $allPspcDone = true;
                for ($i = 1; $i <= 10; $i++) {
                    $idCol = "pspc_member_{$i}_id";
                    $recCol = "pspc_member_{$i}_recommendation";
                    if ($pts1->$idCol && is_null($pts1->$recCol)) {
                        $allPspcDone = false;
                        break;
                    }
                }

                if ($allPspcDone) {
                    $pts1->update([
                        'current_stage' => 'dpgc',
                        'pspc_members_submitted_at' => now(),
                    ]);
                }
                break;

            case 'dpgc':
                $isDpgc = $thesis->student->department->isDpgcConvener($user);
                if (!$isDpgc) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'dpgc_student_comment' => $comment,
                    'dpgc_recommendation' => $isRecommended,
                    'dpgc_confidential_remark' => $remark,
                    'dpgc_submitted_at' => now(),
                    'dpgc_user_id' => $user->id,
                    'current_stage' => 'hod',
                ]);
                break;

            case 'hod':
                $isHod = $thesis->student->department->isHod($user);
                if (!$isHod) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'hod_student_comment' => $comment,
                    'hod_recommendation' => $isRecommended,
                    'hod_confidential_remark' => $remark,
                    'hod_submitted_at' => now(),
                    'hod_user_id' => $user->id,
                    'current_stage' => 'academic_office',
                ]);
                break;

            case 'academic_office':
                if (!$user->isAcademicOffice()) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $actingDoaaEmail = $request->filled('acting_doaa_email') ? $request->input('acting_doaa_email') : null;
                $pts1->update([
                    'academic_office_is_verified' => $isRecommended,
                    'academic_office_confidential_remark' => $remark,
                    'academic_office_submitted_at' => now(),
                    'academic_office_user_id' => $user->id,
                    'acting_doaa_email' => $actingDoaaEmail,
                    'current_stage' => 'doaa',
                ]);
                break;

            case 'doaa':
                if (!($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts1->acting_doaa_email === $user->email || $pts1->vested_doaa_email === $user->email)))) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'doaa_student_comment' => $comment,
                    'doaa_approval' => $isRecommended,
                    'doaa_confidential_remark' => $remark,
                    'doaa_submitted_at' => now(),
                    'doaa_user_id' => $user->id,
                    'approved_by_id' => $user->id,
                    'current_stage' => 'completed',
                    'status' => $isRecommended ? 'approved' : 'rejected',
                    'pts1_submitted_at' => now(),
                ]);

                if ($isRecommended) {
                    $this->ptsDocService->moveToApproved($pts1, 'pts1');
                } else {
                    $this->ptsDocService->moveToRejected($pts1, 'pts1');
                }
                break;

            default:
                return back()->with('error', 'Invalid stage for endorsement.');
        }

        return redirect()->route('dashboard')->with('success', 'PTS-1 form evaluated and submitted successfully!');
    }

    // Handle Reversion of PTS-1 form by an authority back to student.
    public function revert(RevertPts1Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();
        $validated = $request->validated();
        $comment = $validated['reversion_comment'];

        $student = $pts1->thesis?->student;
        $isMainSup = $student && ($student->isMainSupervisor($user) || $student->mainSupervisors->pluck('id')->contains($user->id));

        $stage = $pts1->current_stage;
        $revertedRole = null;

        if ($stage === 'main_supervisor') {
            if (!$isMainSup) {
                return back()->with('error', 'Unauthorized access. Only Main Supervisor can revert at this stage.');
            }
            $revertedRole = 'main_supervisor';
        } elseif ($stage === 'co_supervisors') {
            for ($i = 1; $i <= 10; $i++) {
                $col = "co_supervisor_{$i}_id";
                if ($pts1->$col && (int)$pts1->$col === (int)$user->id) {
                    $revertedRole = "co_supervisor_{$i}";
                    break;
                }
            }
            if (!$revertedRole) {
                return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
            }
        } elseif ($stage === 'pspc_members') {
            for ($i = 1; $i <= 10; $i++) {
                $col = "pspc_member_{$i}_id";
                if ($pts1->$col && (int)$pts1->$col === (int)$user->id) {
                    $revertedRole = "pspc_member_{$i}";
                    break;
                }
            }
            if (!$revertedRole) {
                return back()->with('error', 'You are not an assigned PSPC member for this thesis.');
            }
        } elseif ($stage === 'dpgc') {
            if (!$user->isDpgc()) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'dpgc';
        } elseif ($stage === 'hod') {
            if (!$user->isHod()) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'hod';
        } elseif ($stage === 'doaa') {
            if (!($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts1->acting_doaa_email === $user->email || $pts1->vested_doaa_email === $user->email)))) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = 'doaa';
        } else {
            return back()->with('error', 'Invalid stage for reversion.');
        }

        $pts1->update([
            'reversion_comment' => $comment,
            'reverted_by_role' => $revertedRole,
            'reverted_by_id' => $user->id,
            'status' => 'reverted',
            'current_stage' => 'reverted',
        ]);

        $this->ptsDocService->moveToReverted($pts1, 'pts1');

        return redirect()->route('dashboard')->with('warning', 'PTS-1 form has been reverted to the student for resubmission.');
    }

        // Display view-only submitted PTS-1 form for students and authorities.
    public function show(Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;
        $student = $thesis->student;

        // Authorization check: User must be authorized to view this submission
        if (!$pts1->canUserView($user)) {
            abort(403, 'Unauthorized access to view this submission.');
        }

        if ($pts1->status === 'in_progress') {
            return redirect()->route('pts1.submitted', $pts1->id);
        }

        $student->loadMissing(['coSupervisors', 'externalSupervisors.externalSupervisorProfile', 'pspcMembers']);

        if ($pts1->status === 'reverted') {
            return redirect()->route('pts1.reverted', $pts1->id);
        }

        $studentUser = $student->user;
        $mainSupervisor = $pts1->mainSupervisor ?? $student->mainSupervisors->first();

        $coSupervisors = $pts1->getCoSupervisors();
        $pspcMembers = $pts1->getPspcMembers();

        return view('pts1.show', compact(
            'pts1',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'pspcMembers'
        ));
    }

    // Display view of submitted PTS-1 form strictly scoped to viewing user's submission state.
    public function submitted(Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;
        $student = $thesis->student;
        $student->loadMissing(['coSupervisors', 'externalSupervisors.externalSupervisorProfile', 'pspcMembers']);

        // Status Guardrails
        if (in_array($pts1->status, ['approved', 'rejected'])) {
            return redirect()->route('pts1.show', $pts1->id);
        }

        if ($pts1->status === 'reverted') {
            return redirect()->route('pts1.reverted', $pts1->id);
        }

        // Check workflow stage progression & access
        $accessStatus = $pts1->getUserSubmissionAccessStatus($user);
        if ($accessStatus === 'pending_endorsement') {
            if ($student->isMainSupervisor($user) && $pts1->current_stage === 'main_supervisor') {
                return redirect()->route('faculty.pts1.edit', $pts1->id);
            }
            return redirect()->route('pts1.review', $pts1->id);
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
            if ($pts1->$col == $user->id) {
                $myCoSupSlot = $i;
                break;
            }
        }

        // Find if user is a specific PSPC member
        $myPspcSlot = null;
        for ($i = 1; $i <= 10; $i++) {
            $col = "pspc_member_{$i}_id";
            if ($pts1->$col == $user->id) {
                $myPspcSlot = $i;
                break;
            }
        }

        $studentUser = $student->user;
        $mainSupervisor = $pts1->mainSupervisor ?? $student->mainSupervisors->first();

        // Load co-supervisors and PSPC members maps
        $coSupervisors = $pts1->getCoSupervisors();
        $pspcMembers = $pts1->getPspcMembers();

        // Determine viewPerspective: 'student', 'main_supervisor', 'co_supervisor', 'pspc_member', 'dpgc', 'hod', 'doaa'
        $viewPerspective = 'student';
        if ($isOwnerStudent) {
            $viewPerspective = 'student';
        } elseif ($isMainSupervisor) {
            $viewPerspective = 'main_supervisor';
        } elseif ($myCoSupSlot !== null) {
            $viewPerspective = 'co_supervisor';
        } elseif ($myPspcSlot !== null) {
            $viewPerspective = 'pspc_member';
        } elseif ($user->isDpgc()) {
            $viewPerspective = 'dpgc';
        } elseif ($user->isHod()) {
            $viewPerspective = 'hod';
        } elseif ($user->isAcademicOffice()) {
            $viewPerspective = 'academic_office';
        } elseif ($user->isDoaa() || $user->isActingApprovalAuthority()) {
            $viewPerspective = 'doaa';
        }

        return view('pts1.submitted', compact(
            'pts1',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'pspcMembers',
            'viewPerspective',
            'myCoSupSlot',
            'myPspcSlot'
        ));
    }

    // Display form for reverted PTS-1 (read-only with complete audit trail and reversion details).
    public function reverted(Pts1Form $pts1)
    {
        $user = auth()->user();

        // Guardrail: Reverted view is only for reverted status
        if ($pts1->status !== 'reverted') {
            if (in_array($pts1->status, ['approved', 'rejected'])) {
                return redirect()->route('pts1.show', $pts1->id);
            }
            return redirect()->route('pts1.submitted', $pts1->id);
        }

        // Student owner redirects to student form creation/edit; other students blocked
        if ($user->isStudent()) {
            if ($pts1->thesis?->student?->user_id === $user->id) {
                return redirect()->route('student.pts1.create');
            }
            abort(403, 'Unauthorized access to reverted PTS-1 view.');
        }

        // Verify if user is authorized to view reverted form
        if (!$pts1->canUserViewRevertedForm($user)) {
            abort(403, 'Unauthorized access to reverted PTS-1 view.');
        }

        $thesis = $pts1->thesis;
        $student = $thesis->student;
        $student->loadMissing(['coSupervisors', 'externalSupervisors.externalSupervisorProfile', 'pspcMembers']);
        $studentUser = $student->user;
        $mainSupervisor = $pts1->mainSupervisor ?? $student->mainSupervisors->first();

        $coSupervisors = $pts1->getCoSupervisors();
        $pspcMembers = $pts1->getPspcMembers();

        return view('pts1.reverted', compact(
            'pts1',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'pspcMembers'
        ));
    }
}
