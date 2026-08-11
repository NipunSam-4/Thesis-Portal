<?php

namespace App\Http\Controllers;

use App\Models\Pts1Form;
use App\Models\User;
use Illuminate\Http\Request;

class Pts1Controller extends Controller
{
    /**
     * Show the Main Supervisor review & edit form for a PTS-1 submission.
     */
    public function edit(Pts1Form $pts1)
    {
        $user = auth()->user();

        // Check if logged-in user is Main Supervisor for this thesis
        $thesis = $pts1->thesis;
        $isMainSupervisor = $thesis->student->isMainSupervisor($user);

        if (!$isMainSupervisor) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-1 review.');
        }

        $student = $thesis->student;
        $studentUser = $student->user;

        return view('faculty.pts1.review', compact('pts1', 'thesis', 'student', 'studentUser'));
    }

    /**
     * Process Main Supervisor review submission (Edits, Evaluation & Endorsement/Reversion).
     */
    public function update(Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;

        $isMainSupervisor = $thesis->student->isMainSupervisor($user);

        if (!$isMainSupervisor) {
            return redirect()->route('faculty.dashboard')->with('error', 'Unauthorized access to PTS-1 review.');
        }

        $pubNormFulfilled = $request->boolean('publication_norm_fulfillment');
        $pubSpecialApproval = $pubNormFulfilled ? false : $request->boolean('special_approval_publication');

        $minTimeFulfilled = $request->boolean('min_time_req_fulfilled');
        $minTimeSpecialApproval = $minTimeFulfilled ? false : $request->boolean('special_approval_min_time');

        $requirePubDoc = !$pubNormFulfilled && $pubSpecialApproval && !$pts1->publication_approval_doc_path;
        $requireMinTimeDoc = !$minTimeFulfilled && $minTimeSpecialApproval && !$pts1->min_time_approval_doc_path;

        $validated = $request->validate([
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
            'main_supervisor_student_comment' => 'required|string',
            'main_supervisor_confidential_remark' => $request->input('work_status') === 'inadequate' ? 'required|string' : 'nullable|string',
        ]);

        // Optional File Replacements by Main Supervisor
        $pubAppPath = $pts1->publication_approval_doc_path;
        if (!$pubNormFulfilled && $pubSpecialApproval && $request->hasFile('publication_approval_doc')) {
            $pubAppPath = $request->file('publication_approval_doc')->store('private/pts1_documents', 'local');
        }

        $minTimeAppPath = $pts1->min_time_approval_doc_path;
        if (!$minTimeFulfilled && $minTimeSpecialApproval && $request->hasFile('min_time_approval_doc')) {
            $minTimeAppPath = $request->file('min_time_approval_doc')->store('private/pts1_documents', 'local');
        }

        $synopsisPath = $pts1->draft_synopsis_report_doc_path;
        if ($request->hasFile('draft_synopsis_report')) {
            $synopsisPath = $request->file('draft_synopsis_report')->store('private/pts1_documents', 'local');
        }

        $pubListPath = $pts1->publication_list_doc_path;
        if ($request->hasFile('publication_list')) {
            $pubListPath = $request->file('publication_list')->store('private/pts1_documents', 'local');
        }

        // Update Student confirmation date
        $thesis->student->update(['date_confirmation' => $validated['date_confirmation']]);

        // Determine Next Stage for Forwarding
        $coSupervisorsCount = $thesis->student ? $thesis->student->coSupervisors()->count() : 0;
        $pspcMembersCount = $thesis->student ? $thesis->student->pspcMembers()->count() : 0;

        $nextStage = 'dpgc';
        if ($coSupervisorsCount > 0) {
            $nextStage = 'co_supervisors';
        } elseif ($pspcMembersCount > 0) {
            $nextStage = 'pspc_members';
        }

        $pts1->update([
            'seminar_date' => $validated['seminar_date'],
            'seminar_time' => $validated['seminar_time'],
            'seminar_venue' => $validated['seminar_venue'],
            'meeting_link' => $validated['meeting_link'] ?? null,
            'publication_norm_fulfillment' => $pubNormFulfilled,
            'special_approval_publication' => $pubSpecialApproval,
            'publication_approval_doc_path' => $pubNormFulfilled ? null : $pubAppPath,
            'min_time_req_fulfilled' => $minTimeFulfilled,
            'special_approval_min_time' => $minTimeSpecialApproval,
            'min_time_approval_doc_path' => $minTimeFulfilled ? null : $minTimeAppPath,
            'draft_synopsis_report_doc_path' => $synopsisPath,
            'publication_list_doc_path' => $pubListPath,
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

    /**
     * Display view-only submitted PTS-1 form for students and authorities.
     */
    public function show(Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;
        $student = $thesis->student;

        // Authorization check: User must be the student, or an assigned faculty/supervisor, or an authority.
        $isOwnerStudent = ($user->isStudent() && $student->user_id === $user->id);
        $isSupervisorOrFaculty = $user->isFaculty();
        $isAuthority = ($user->isHod() || $user->isDpgc() || $user->isSectionOfficer() || $user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic());

        if (!$isOwnerStudent && !$isSupervisorOrFaculty && !$isAuthority) {
            abort(403, 'Unauthorized access to view this submission.');
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

    /**
     * Display dedicated full-page review & endorsement view for PTS-1 with complete audit trail.
     */
    public function showReview(Pts1Form $pts1)
    {
        $user = auth()->user();
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

        $sectionofficer = $user->isSectionOfficer();

        return view('pts1.review_endorse', compact(
            'pts1',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisors',
            'pspcMembers',
            'sectionofficer'
        ));
    }

    /**
     * Handle Endorsement of PTS-1 by an authority (Co-Supervisor, PSPC, DPGC, HOD, Section Officer, DOAA).
     */
    public function endorse(Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;
        $stage = $pts1->current_stage;

        if ($stage === 'section_officer') {
            $validated = $request->validate([
                'verified_details' => 'required|accepted',
                'confidential_remark' => 'required|string',
            ]);
            $isRecommended = true;
            $comment = null;
            $remark = $validated['confidential_remark'];
        } else {
            $validated = $request->validate([
                'recommendation' => 'required|boolean',
                'student_comment' => 'nullable|string',
                'confidential_remark' => $request->boolean('recommendation') ? 'nullable|string' : 'required|string',
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
                if (!$user->isDpgc()) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'dpgc_student_comment' => $comment,
                    'dpgc_recommendation' => $isRecommended,
                    'dpgc_confidential_remark' => $remark,
                    'current_stage' => 'hod',
                ]);
                break;

            case 'hod':
                if (!$user->isHod()) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'hod_student_comment' => $comment,
                    'hod_recommendation' => $isRecommended,
                    'hod_confidential_remark' => $remark,
                    'current_stage' => 'section_officer',
                ]);
                break;

            case 'section_officer':
                if (!$user->isSectionOfficer()) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'section_officer_student_comment' => $comment,
                    'section_officer_recommendation' => $isRecommended,
                    'section_officer_confidential_remark' => $remark,
                    'current_stage' => 'doaa',
                ]);
                break;

            case 'doaa':
                if (!($user->isDoaa())) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'doaa_student_comment' => $comment,
                    'doaa_approval' => $isRecommended,
                    'doaa_confidential_remark' => $remark,
                    'current_stage' => $isRecommended ? 'completed' : 'rejected',
                    'status' => $isRecommended ? 'accepted' : 'rejected',
                    'pts1_submitted_at' => now(),
                ]);
                break;

            default:
                return back()->with('error', 'Invalid stage for endorsement.');
        }

        return redirect()->route('dashboard')->with('success', 'PTS-1 form evaluated and submitted successfully!');
    }

    /**
     * Handle Reversion of PTS-1 form by an authority back to student.
     */
    public function revert(Request $request, Pts1Form $pts1)
    {
        $user = auth()->user();
        $request->validate(['reversion_comment' => 'required|string']);
        $comment = $request->input('reversion_comment');

        $stage = $pts1->current_stage;

        switch ($stage) {
            case 'main_supervisor':
                $isMainSupervisor = $pts1->thesis->student->isMainSupervisor($user);

                if (!$isMainSupervisor) {
                    return back()->with('error', 'Unauthorized access. Only Main Supervisor can revert at this stage.');
                }

                $pts1->update([
                    'reversion_comment' => $comment,
                    'reverted_by_role' => 'main_supervisor',
                    'status' => 'reverted',
                    'current_stage' => 'reverted',
                ]);
                return redirect()->route('faculty.dashboard')->with('warning', 'PTS-1 form has been reverted to the student for resubmission.');
            case 'co_supervisors':
                $roleKey = null;
                for ($i = 1; $i <= 10; $i++) {
                    $col = "co_supervisor_{$i}_id";
                    if ($pts1->$col === $user->id) {
                        $roleKey = "co_supervisor_{$i}";
                        break;
                    }
                }

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                }

                $pts1->update([
                    'reversion_comment' => $comment,
                    'reverted_by_role' => $roleKey,
                    'status' => 'reverted',
                    'current_stage' => 'reverted',
                ]);
                break;

            case 'pspc_members':
                $roleKey = null;
                for ($i = 1; $i <= 10; $i++) {
                    $col = "pspc_member_{$i}_id";
                    if ($pts1->$col === $user->id) {
                        $roleKey = "pspc_member_{$i}";
                        break;
                    }
                }

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned PSPC member for this thesis.');
                }

                $pts1->update([
                    'reversion_comment' => $comment,
                    'reverted_by_role' => $roleKey,
                    'status' => 'reverted',
                    'current_stage' => 'reverted',
                ]);
                break;

            case 'dpgc':
                if (!$user->isDpgc()) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'reversion_comment' => $comment,
                    'reverted_by_role' => 'dpgc',
                    'status' => 'reverted',
                    'current_stage' => 'reverted',
                ]);
                break;

            case 'hod':
                if (!$user->isHod()) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'reversion_comment' => $comment,
                    'reverted_by_role' => 'hod',
                    'status' => 'reverted',
                    'current_stage' => 'reverted',
                ]);
                break;

            case 'doaa':
                if (!($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic())) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'reversion_comment' => $comment,
                    'reverted_by_role' => 'doaa',
                    'status' => 'reverted',
                    'current_stage' => 'reverted',
                ]);
                break;

            default:
                return back()->with('error', 'Invalid stage for reversion.');
        }

        return redirect()->route('dashboard')->with('warning', 'PTS-1 form has been reverted to the student for resubmission.');
    }
}
