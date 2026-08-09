<?php

namespace App\Http\Controllers;

use App\Models\Pts1Form;
use App\Models\User;
use Illuminate\Http\Request;

class Pts1EndorsementController extends Controller
{
    /**
     * Display dedicated full-page review & endorsement view for PTS-1 with complete audit trail.
     */
    public function showReview(Pts1Form $pts1)
    {
        $user = auth()->user();
        $thesis = $pts1->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;
        $mainSupervisor = $thesis->mainSupervisor;

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

        $sectionofficer = ($user->role === 'section_officer');

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

        $validated = $request->validate([
            'work_status' => 'required|in:adequate,inadequate',
            'comment' => 'nullable|string',
            'confidential_remark' => $request->input('work_status') === 'inadequate' ? 'required|string' : 'nullable|string',
        ]);

        $isRecommended = ($validated['work_status'] === 'adequate');
        $comment = $validated['comment'] ?? 'N/A';
        $remark = $validated['confidential_remark'] ?: ($isRecommended ? 'Recommended' : 'Not Recommended');

        $stage = $pts1->current_stage;

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
                    $remCol = "co_supervisor_{$i}_confidential_remark";
                    if ($pts1->$idCol && is_null($pts1->$remCol)) {
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
                    $remCol = "pspc_member_{$i}_confidential_remark";
                    if ($pts1->$idCol && is_null($pts1->$remCol)) {
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
                if ($user->role !== 'dpgc') {
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
                if ($user->role !== 'hod') {
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
                if ($user->role !== 'section_officer') {
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
                if (!in_array($user->role, ['doaa', 'adoaa', 'senate_chairperson', 'ar'])) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'doaa_student_comment' => $comment,
                    'doaa_approval' => $isRecommended,
                    'doaa_confidential_remark' => $remark,
                    'current_stage' => 'completed',
                    'status' => 'accepted',
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
        $request->validate(['comment' => 'required|string']);
        $comment = $request->input('comment');

        $stage = $pts1->current_stage;

        switch ($stage) {
            case 'main_supervisor':
                $isMainSupervisor = $pts1->thesis->supervisors()
                    ->where('users.id', $user->id)
                    ->wherePivot('supervisor_type', 'main')
                    ->exists();

                if (!$isMainSupervisor) {
                    return back()->with('error', 'Unauthorized access. Only Main Supervisor can revert at this stage.');
                }

                $pts1->update([
                    'main_supervisor_reversion_comment' => $comment,
                    'reverted_by_role' => 'main_supervisor',
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
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
                    "{$roleKey}_reversion_comment" => $comment,
                    'reverted_by_role' => $roleKey,
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
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
                    "{$roleKey}_reversion_comment" => $comment,
                    'reverted_by_role' => $roleKey,
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
                ]);
                break;

            case 'dpgc':
                if ($user->role !== 'dpgc') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'dpgc_reversion_comment' => $comment,
                    'reverted_by_role' => 'dpgc',
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
                ]);
                break;

            case 'hod':
                if ($user->role !== 'hod') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'hod_reversion_comment' => $comment,
                    'reverted_by_role' => 'hod',
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
                ]);
                break;

            case 'section_officer':
                if ($user->role !== 'section_officer') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'section_officer_reversion_comment' => $comment,
                    'reverted_by_role' => 'section_officer',
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
                ]);
                break;

            case 'doaa':
                if (!in_array($user->role, ['doaa', 'adoaa', 'senate_chairperson', 'ar'])) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'doaa_reversion_comment' => $comment,
                    'reverted_by_role' => 'doaa',
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
                ]);
                break;

            default:
                return back()->with('error', 'Invalid stage for reversion.');
        }

        return redirect()->route('dashboard')->with('warning', 'PTS-1 form has been reverted to the student for resubmission.');
    }
}
