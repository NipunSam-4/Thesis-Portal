<?php

namespace App\Http\Controllers;

use App\Models\Pts1Form;
use App\Models\User;
use App\Services\PtsDocumentService;
use Illuminate\Http\Request;

class Pts1Controller extends Controller
{
    protected PtsDocumentService $ptsDocService;

    public function __construct(PtsDocumentService $ptsDocService)
    {
        $this->ptsDocService = $ptsDocService;
    }
    // Show the Main Supervisor review & edit form for a PTS-1 submission.
    public function review(Pts1Form $pts1)
    {
        $user = auth()->user();

        // Check if logged-in user is Main Supervisor for this thesis
        $thesis = $pts1->thesis;
        $isMainSupervisor = $thesis->student->isMainSupervisor($user);

        if (!$isMainSupervisor || $pts1->status !== 'in_progress' || $pts1->current_stage !== 'main_supervisor') {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-1 review.');
        }

        $student = $thesis->student;
        $studentUser = $student->user;

        return view('faculty.pts1.review', compact('pts1', 'thesis', 'student', 'studentUser'));
    }

    // Process Main Supervisor review submission (Edits, Evaluation & Endorsement/Reversion).
    public function update(Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;

        $isMainSupervisor = $thesis->student->isMainSupervisor($user);

        if (!$isMainSupervisor) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-1 review.');
        }

        $pubNormFulfilled = $request->boolean('publication_norm_fulfillment');
        $pubSpecialApproval = $pubNormFulfilled ? null : ($request->has('special_approval_publication') ? $request->boolean('special_approval_publication') : null);

        $minTimeFulfilled = $request->boolean('min_time_req_fulfilled');
        $minTimeSpecialApproval = $minTimeFulfilled ? null : ($request->has('special_approval_min_time') ? $request->boolean('special_approval_min_time') : null);

        $requirePubDoc = !$pubNormFulfilled && $pubSpecialApproval && !$pts1->publication_approval_doc_path;
        $requireMinTimeDoc = !$minTimeFulfilled && $minTimeSpecialApproval && !$pts1->min_time_approval_doc_path;

        $validated = $request->validate([
            'thesis_title' => 'required|string|max:1000',
            'date_confirmation' => 'required|date',
            'seminar_date' => 'required|date',
            'seminar_time' => 'required|string|max:100',
            'seminar_venue' => 'required|string|max:255',
            'meeting_link' => 'nullable|url|max:255',
            
            'publication_norm_fulfillment' => 'required|boolean',
            'special_approval_publication' => 'nullable|boolean',
            'publication_approval_doc' => ($requirePubDoc ? 'required' : 'nullable') . '|file|mimes:pdf,png,jpg,jpeg|max:2048',

            'min_time_req_fulfilled' => 'required|boolean',
            'special_approval_min_time' => 'nullable|boolean',
            'min_time_approval_doc' => ($requireMinTimeDoc ? 'required' : 'nullable') . '|file|mimes:pdf,png,jpg,jpeg|max:2048',

            'draft_synopsis_report' => 'nullable|file|mimes:pdf,docx|max:10240',
            'publication_list' => 'nullable|file|mimes:xlsx,xls|max:2048',

            'work_status' => 'required|in:adequate,inadequate',
            'main_supervisor_student_comment' => 'required|string|max:2000',
            'main_supervisor_confidential_remark' => $request->input('work_status') === 'inadequate' ? 'required|string|max:2000' : 'nullable|string|max:2000',
            'pspc_undertaking' => 'required|accepted',
        ]);

        // Update active thesis title in database
        $thesis->update(['title' => $validated['thesis_title']]);

        $studentRoll = $thesis->student->roll_number;

        // Optional File Replacements by Main Supervisor
        $msPubAppPath = $pts1->main_supervisor_publication_approval_doc_path;
        if (!$pubNormFulfilled && $pubSpecialApproval && $request->hasFile('publication_approval_doc')) {
            $msPubAppPath = $this->ptsDocService->storeInProgressDocument($request->file('publication_approval_doc'), $studentRoll, $thesis->id, 'pts1', 'Publication_Approval', 'Supervisor_Modified');
        }

        $msMinTimeAppPath = $pts1->main_supervisor_min_time_approval_doc_path;
        if (!$minTimeFulfilled && $minTimeSpecialApproval && $request->hasFile('min_time_approval_doc')) {
            $msMinTimeAppPath = $this->ptsDocService->storeInProgressDocument($request->file('min_time_approval_doc'), $studentRoll, $thesis->id, 'pts1', 'Min_Time_Approval', 'Supervisor_Modified');
        }

        $msSynopsisPath = $pts1->main_supervisor_draft_synopsis_report_doc_path;
        if ($request->hasFile('draft_synopsis_report')) {
            $msSynopsisPath = $this->ptsDocService->storeInProgressDocument($request->file('draft_synopsis_report'), $studentRoll, $thesis->id, 'pts1', 'Draft_Synopsis', 'Supervisor_Modified');
        }

        $msPubListPath = $pts1->main_supervisor_publication_list_doc_path;
        if ($request->hasFile('publication_list')) {
            $msPubListPath = $this->ptsDocService->storeInProgressDocument($request->file('publication_list'), $studentRoll, $thesis->id, 'pts1', 'Publication_List', 'Supervisor_Modified');
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

        if ($pts1->status === 'reverted') {
            return redirect()->route('pts1.reverted', $pts1->id);
        }

        $studentUser = $student->user;
        $mainSupervisor = $student->mainSupervisors->first();

        $coSupervisors = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts1->$col) {
                $coSupervisors[$i] = User::find($pts1->$col);
            }
        }

        $pspcMembers = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "pspc_member_{$i}_id";
            if ($pts1->$col) {
                $pspcMembers[$i] = User::find($pts1->$col);
            }
        }

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
                return redirect()->route('faculty.pts1.review', $pts1->id);
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
        $mainSupervisor = $student->mainSupervisors->first();

        // Load co-supervisors and PSPC members maps
        $coSupervisors = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts1->$col) {
                $coSupervisors[$i] = User::find($pts1->$col);
            }
        }

        $pspcMembers = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "pspc_member_{$i}_id";
            if ($pts1->$col) {
                $pspcMembers[$i] = User::find($pts1->$col);
            }
        }

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
        $studentUser = $student->user;
        $mainSupervisor = $student->mainSupervisors->first();

        $coSupervisors = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts1->$col) {
                $coSupervisors[$i] = User::find($pts1->$col);
            }
        }

        $pspcMembers = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "pspc_member_{$i}_id";
            if ($pts1->$col) {
                $pspcMembers[$i] = User::find($pts1->$col);
            }
        }

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

    // Display dedicated full-page review & endorsement view for PTS-1 with complete audit trail.
    public function reviewEndorse(Pts1Form $pts1)
    {
        $user = auth()->user();

        // Must be currently authorized to evaluate at this stage
        if (!$pts1->canUserEvaluate($user)) {
            if ($pts1->canUserView($user)) {
                return redirect()->route('pts1.submitted', $pts1->id);
            }
            abort(403, 'Unauthorized access to review this submission.');
        }

        $thesis = $pts1->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;
        $mainSupervisor = $student->mainSupervisors->first();

        $coSupervisors = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "co_supervisor_{$i}_id";
            if ($pts1->$col) {
                $coSupervisors[$i] = User::find($pts1->$col);
            }
        }

        $pspcMembers = [];
        for ($i = 1; $i <= 10; $i++) {
            $col = "pspc_member_{$i}_id";
            if ($pts1->$col) {
                $pspcMembers[$i] = User::find($pts1->$col);
            }
        }

        $academicoffice = $user->isAcademicOffice();
        $actingDoaaUsers = \App\Models\ActingDoaa::where('is_active', true)->with('user')->get()->pluck('user')->filter();

        return view('pts1.review_endorse', compact(
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
    public function endorse(Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();

        if (!$pts1->canUserEvaluate($user)) {
            abort(403, 'Unauthorized action on this submission.');
        }

        $thesis = $pts1->thesis;
        $stage = $pts1->current_stage;

        if ($stage === 'academic_office') {
            $validated = $request->validate([
                'verified_details' => 'required|accepted',
                'confidential_remark' => 'required|string|max:2000',
                'acting_doaa_email' => 'nullable|email',
            ]);
            $isRecommended = true;
            $comment = null;
            $remark = $validated['confidential_remark'];
        } else {
            $validated = $request->validate([
                'recommendation' => 'required|boolean',
                'student_comment' => 'nullable|string|max:2000',
                'confidential_remark' => $request->boolean('recommendation') ? 'nullable|string|max:2000' : 'required|string|max:2000',
            ]);
            $isRecommended = $request->boolean('recommendation');
            $comment = $validated['student_comment'] ?? null;
            $remark = $validated['confidential_remark'] ?? null;
        }

        switch ($stage) {
            case 'co_supervisors':
                $roleKey = null;
                for ($i = 1; $i <= 10; $i++) {
                    $col = "co_supervisor_{$i}_id";
                    if ($pts1->$col === $user->id) {
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
                    if ($pts1->$col === $user->id) {
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
                    'current_stage' => 'academic_office',
                ]);
                break;

            case 'academic_office':
                if (!$user->isAcademicOffice()) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $actingDoaaEmail = $request->filled('acting_doaa_email') ? $request->input('acting_doaa_email') : null;
                $pts1->update([
                    'academic_office_student_comment' => $comment,
                    'academic_office_verified' => $isRecommended,
                    'academic_office_confidential_remark' => $remark,
                    'academic_office_submitted_at' => now(),
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
                    'approved_by_authority' => $user->email,
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
    public function revert(Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();

        if (!$pts1->canUserEvaluate($user)) {
            abort(403, 'Unauthorized action on this submission.');
        }

        $request->validate(['reversion_comment' => 'required|string|max:2000']);
        $comment = $request->input('reversion_comment');

        $student = $pts1->thesis?->student;
        $isMainSup = $student && ($student->isMainSupervisor($user) || $student->mainSupervisors->pluck('id')->contains($user->id));

        $stage = $pts1->current_stage;
        $revertedRole = null;

        if ($stage === 'main_supervisor' || $isMainSup) {
            if (!$isMainSup && !$user->isFaculty()) {
                return back()->with('error', 'Unauthorized access. Only Main Supervisor can revert at this stage.');
            }
            $revertedRole = 'main_supervisor';
        } elseif ($stage === 'co_supervisors') {
            for ($i = 1; $i <= 10; $i++) {
                $col = "co_supervisor_{$i}_id";
                if ($pts1->$col === $user->id) {
                    $revertedRole = "co_supervisor_{$i}";
                    break;
                }
            }
            if (!$revertedRole && $isMainSup) {
                $revertedRole = 'main_supervisor';
            }
            if (!$revertedRole) {
                return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
            }
        } elseif ($stage === 'pspc_members') {
            for ($i = 1; $i <= 10; $i++) {
                $col = "pspc_member_{$i}_id";
                if ($pts1->$col === $user->id) {
                    $revertedRole = "pspc_member_{$i}";
                    break;
                }
            }
            if (!$revertedRole && $isMainSup) {
                $revertedRole = 'main_supervisor';
            }
            if (!$revertedRole) {
                return back()->with('error', 'You are not an assigned PSPC member for this thesis.');
            }
        } elseif ($stage === 'dpgc') {
            if (!$user->isDpgc() && !$isMainSup) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = $user->isDpgc() ? 'dpgc' : 'main_supervisor';
        } elseif ($stage === 'hod') {
            if (!$user->isHod() && !$isMainSup) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = $user->isHod() ? 'hod' : 'main_supervisor';
        } elseif ($stage === 'academic_office') {
            if (!$user->isAcademicOffice() && !$isMainSup) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = $user->isAcademicOffice() ? 'academic_office' : 'main_supervisor';
        } elseif ($stage === 'doaa') {
            if (!($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic() || ($user->isActingApprovalAuthority() && ($pts1->acting_doaa_email === $user->email || $pts1->vested_doaa_email === $user->email))) && !$isMainSup) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = $isMainSup ? 'main_supervisor' : 'doaa';
        } else {
            if ($isMainSup) {
                $revertedRole = 'main_supervisor';
            } else {
                return back()->with('error', 'Invalid stage for reversion.');
            }
        }

        $pts1->update([
            'reversion_comment' => $comment,
            'reverted_by_role' => $revertedRole,
            'reverted_by_id' => $user->id,
            'status' => 'reverted',
            'current_stage' => 'reverted',
        ]);

        $this->ptsDocService->moveToReverted($pts1, 'pts1');

        $redirectRoute = $user->isExternalSupervisor() ? 'external_supervisor.dashboard' : ($user->isFaculty() ? 'faculty.dashboard' : 'dashboard');
        return redirect()->route($redirectRoute)->with('warning', 'PTS-1 form has been reverted to the student for resubmission.');
    }
}
