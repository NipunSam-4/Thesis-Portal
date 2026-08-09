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
        $coSupervisor1 = $pts1->co_supervisor_1_id ? User::find($pts1->co_supervisor_1_id) : null;
        $coSupervisor2 = $pts1->co_supervisor_2_id ? User::find($pts1->co_supervisor_2_id) : null;
        $coSupervisor3 = $pts1->co_supervisor_3_id ? User::find($pts1->co_supervisor_3_id) : null;

        $pspc1 = $pts1->pspc_member_1_id ? User::find($pts1->pspc_member_1_id) : null;
        $pspc2 = $pts1->pspc_member_2_id ? User::find($pts1->pspc_member_2_id) : null;
        $pspc3 = $pts1->pspc_member_3_id ? User::find($pts1->pspc_member_3_id) : null;

        $sectionofficer = $user->role=='section_officer';

        return view('pts1.review_endorse', compact(
            'pts1',
            'thesis',
            'student',
            'studentUser',
            'mainSupervisor',
            'coSupervisor1',
            'coSupervisor2',
            'coSupervisor3',
            'pspc1',
            'pspc2',
            'pspc3',
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
        $comment = $request->input('comment') ?: 'N/A';

        $stage = $pts1->current_stage;

        switch ($stage) {
            case 'co_supervisors':
                $roleKey = null;
                if ($pts1->co_supervisor_1_id === $user->id) $roleKey = 1;
                elseif ($pts1->co_supervisor_2_id === $user->id) $roleKey = 2;
                elseif ($pts1->co_supervisor_3_id === $user->id) $roleKey = 3;

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                }

                $pts1->update([
                    "co_supervisor_{$roleKey}_endorsement" => true,
                    "co_supervisor_{$roleKey}_comment" => $comment,
                ]);

                // Check if all assigned co-supervisors have endorsed
                $allCoDone = true;
                for ($i = 1; $i <= 3; $i++) {
                    $idCol = "co_supervisor_{$i}_id";
                    $endCol = "co_supervisor_{$i}_endorsement";
                    if ($pts1->$idCol && !$pts1->$endCol) {
                        $allCoDone = false;
                        break;
                    }
                }

                if ($allCoDone) {
                    $pspcCount = $thesis->student ? $thesis->student->pspcMembers()->count() : 0;
                    $nextStage = $pspcCount > 0 ? 'pspc_members' : 'dpgc';
                    $pts1->update(['current_stage' => $nextStage]);
                }
                break;

            case 'pspc_members':
                $roleKey = null;
                if ($pts1->pspc_member_1_id === $user->id) $roleKey = 1;
                elseif ($pts1->pspc_member_2_id === $user->id) $roleKey = 2;
                elseif ($pts1->pspc_member_3_id === $user->id) $roleKey = 3;

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned PSPC member for this thesis.');
                }

                $pts1->update([
                    "pspc_member_{$roleKey}_endorsement" => true,
                    "pspc_member_{$roleKey}_comment" => $comment,
                ]);

                // Check if all assigned PSPC members have endorsed
                $allPspcDone = true;
                for ($i = 1; $i <= 3; $i++) {
                    $idCol = "pspc_member_{$i}_id";
                    $endCol = "pspc_member_{$i}_endorsement";
                    if ($pts1->$idCol && !$pts1->$endCol) {
                        $allPspcDone = false;
                        break;
                    }
                }

                if ($allPspcDone) {
                    $pts1->update(['current_stage' => 'dpgc']);
                }
                break;

            case 'dpgc':
                if ($user->role !== 'dpgc') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'dpgc_recommendation' => true,
                    'dpgc_confidential_remark' => $comment,
                    'current_stage' => 'hod',
                ]);
                break;

            case 'hod':
                if ($user->role !== 'hod') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'hod_recommendation' => true,
                    'hod_confidential_remark' => $comment,
                    'current_stage' => 'section_officer',
                ]);
                break;

            case 'section_officer':
                if ($user->role !== 'section_officer') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'section_officer_recommendation' => true,
                    'section_officer_confidential_remark' => $comment,
                    'current_stage' => 'doaa',
                ]);
                break;

            case 'doaa':
                if (!in_array($user->role, ['doaa', 'adoaa', 'senate_chairperson', 'ar'])) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts1->update([
                    'doaa_approval' => true,
                    'doaa_confidential_remark' => $comment,
                    'current_stage' => 'completed',
                    'status' => 'accepted',
                ]);
                break;

            default:
                return back()->with('error', 'Invalid stage for endorsement.');
        }

        return redirect()->route('dashboard')->with('success', 'PTS-1 form endorsed successfully!');
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

        // Check if user is Main Supervisor for this thesis
        $isMainSupervisor = $pts1->thesis->supervisors()
            ->where('users.id', $user->id)
            ->wherePivot('supervisor_type', 'main')
            ->exists();

        if ($stage === 'main_supervisor_review' || $isMainSupervisor) {
            $pts1->update([
                'main_supervisor_student_comment' => $comment,
                'main_supervisor_confidential_remark' => $comment,
                'reverted_by_role' => 'main_supervisor',
                'status' => 'reverted',
                'current_stage' => 'rejected',
            ]);
            return redirect()->route('faculty.dashboard')->with('warning', 'PTS-1 form has been reverted to the student for resubmission.');
        }

        switch ($stage) {
            case 'co_supervisors':
                $roleKey = null;
                if ($pts1->co_supervisor_1_id === $user->id) $roleKey = 'co_supervisor_1';
                elseif ($pts1->co_supervisor_2_id === $user->id) $roleKey = 'co_supervisor_2';
                elseif ($pts1->co_supervisor_3_id === $user->id) $roleKey = 'co_supervisor_3';

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                }

                $pts1->update([
                    "{$roleKey}_comment" => $comment,
                    'reverted_by_role' => $roleKey,
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
                ]);
                break;

            case 'pspc_members':
                $roleKey = null;
                if ($pts1->pspc_member_1_id === $user->id) $roleKey = 'pspc_member_1';
                elseif ($pts1->pspc_member_2_id === $user->id) $roleKey = 'pspc_member_2';
                elseif ($pts1->pspc_member_3_id === $user->id) $roleKey = 'pspc_member_3';

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned PSPC member for this thesis.');
                }

                $pts1->update([
                    "{$roleKey}_comment" => $comment,
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
                    'dpgc_confidential_remark' => $comment,
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
                    'hod_confidential_remark' => $comment,
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
                    'section_officer_confidential_remark' => $comment,
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
                    'doaa_confidential_remark' => $comment,
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
