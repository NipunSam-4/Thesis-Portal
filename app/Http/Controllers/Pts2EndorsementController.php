<?php

namespace App\Http\Controllers;

use App\Models\Pts2Form;
use App\Models\User;
use Illuminate\Http\Request;

class Pts2EndorsementController extends Controller
{
    /**
     * Display dedicated full-page review & endorsement view for PTS-2 with complete audit trail.
     */
    public function showReview(Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $scholarUser = $student->user;

        $mainSupervisor = $thesis->mainSupervisor;
        $coSupervisor1 = $pts2->co_supervisor_1_id ? User::find($pts2->co_supervisor_1_id) : null;
        $coSupervisor2 = $pts2->co_supervisor_2_id ? User::find($pts2->co_supervisor_2_id) : null;
        $coSupervisor3 = $pts2->co_supervisor_3_id ? User::find($pts2->co_supervisor_3_id) : null;

        $pspc1 = $pts2->pspc_member_1_id ? User::find($pts2->pspc_member_1_id) : null;
        $pspc2 = $pts2->pspc_member_2_id ? User::find($pts2->pspc_member_2_id) : null;
        $pspc3 = $pts2->pspc_member_3_id ? User::find($pts2->pspc_member_3_id) : null;

        return view('pts2.review_endorse', compact(
            'pts2',
            'thesis',
            'student',
            'scholarUser',
            'mainSupervisor',
            'coSupervisor1',
            'coSupervisor2',
            'coSupervisor3',
            'pspc1',
            'pspc2',
            'pspc3'
        ));
    }

    public function endorse(Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $comment = $request->input('comment') ?: 'N/A';

        switch ($pts2->current_stage) {
            case 'co_supervisors':
                $roleKey = null;
                if ($pts2->co_supervisor_1_id === $user->id) $roleKey = 1;
                elseif ($pts2->co_supervisor_2_id === $user->id) $roleKey = 2;
                elseif ($pts2->co_supervisor_3_id === $user->id) $roleKey = 3;

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                }

                $pts2->update([
                    "co_supervisor_{$roleKey}_endorsement" => true,
                    "co_supervisor_{$roleKey}_comment" => $comment,
                ]);

                $co1Done = !$pts2->co_supervisor_1_id || $pts2->co_supervisor_1_recommendation;
                $co2Done = !$pts2->co_supervisor_2_id || $pts2->co_supervisor_2_recommendation;
                $co3Done = !$pts2->co_supervisor_3_id || $pts2->co_supervisor_3_recommendation;

                if ($co1Done && $co2Done && $co3Done) {
                    $nextStage = ($pts2->pspc_member_1_id || $pts2->pspc_member_2_id || $pts2->pspc_member_3_id) ? 'pspc_members' : 'dpgc';
                    $pts2->update(['current_stage' => $nextStage]);
                }
                break;

            case 'pspc_members':
                $roleKey = null;
                if ($pts2->pspc_member_1_id === $user->id) $roleKey = 1;
                elseif ($pts2->pspc_member_2_id === $user->id) $roleKey = 2;
                elseif ($pts2->pspc_member_3_id === $user->id) $roleKey = 3;

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned PSPC member for this thesis.');
                }

                $pts2->update([
                    "pspc_member_{$roleKey}_endorsement" => true,
                    "pspc_member_{$roleKey}_comment" => $comment,
                ]);

                $pspc1Done = !$pts2->pspc_member_1_id || $pts2->pspc_member_1_recommendation;
                $pspc2Done = !$pts2->pspc_member_2_id || $pts2->pspc_member_2_recommendation;
                $pspc3Done = !$pts2->pspc_member_3_id || $pts2->pspc_member_3_recommendation;

                if ($pspc1Done && $pspc2Done && $pspc3Done) {
                    $pts2->update(['current_stage' => 'dpgc']);
                }
                break;

            case 'dpgc':
                if ($user->role !== 'dpgc') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts2->update([
                    'dpgc_recommendation' => true,
                    'dpgc_confidential_remark' => $comment,
                    'current_stage' => 'hod',
                ]);
                break;

            case 'hod':
                if ($user->role !== 'hod') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts2->update([
                    'hod_recommendation' => true,
                    'hod_confidential_remark' => $comment,
                    'current_stage' => 'section_officer',
                ]);
                break;

            case 'section_officer':
                if ($user->role !== 'section_officer') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts2->update([
                    'section_officer_recommendation' => true,
                    'section_officer_confidential_remark' => $comment,
                    'current_stage' => 'doaa',
                ]);
                break;

            case 'doaa':
                if (!$user->isDoaa() && !$user->isAdoaa() && !$user->isSenateChairperson() && !$user->isArAcademic()) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts2->update([
                    'doaa_approval' => true,
                    'doaa_confidential_remark' => $comment,
                    'current_stage' => 'completed',
                    'status' => 'accepted',
                ]);
                $pts2->thesis->update(['current_status' => 'PTS-2 Approved / Synopsis Approved']);
                break;
        }

        return redirect()->route('dashboard')->with('success', 'PTS-2 Synopsis Form endorsed successfully.');
    }

    public function revert(Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $request->validate(['comment' => 'required|string']);
        $comment = $request->input('comment');

        switch ($pts2->current_stage) {
            case 'co_supervisors':
                $roleKey = null;
                if ($pts2->co_supervisor_1_id === $user->id) $roleKey = 'co_supervisor_1';
                elseif ($pts2->co_supervisor_2_id === $user->id) $roleKey = 'co_supervisor_2';
                elseif ($pts2->co_supervisor_3_id === $user->id) $roleKey = 'co_supervisor_3';

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                }

                $pts2->update([
                    "{$roleKey}_comment" => $comment,
                    'reverted_by_role' => $roleKey,
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
                ]);
                break;

            case 'pspc_members':
                $roleKey = null;
                if ($pts2->pspc_member_1_id === $user->id) $roleKey = 'pspc_member_1';
                elseif ($pts2->pspc_member_2_id === $user->id) $roleKey = 'pspc_member_2';
                elseif ($pts2->pspc_member_3_id === $user->id) $roleKey = 'pspc_member_3';

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned PSPC member for this thesis.');
                }

                $pts2->update([
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
                $pts2->update([
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
                $pts2->update([
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
                $pts2->update([
                    'section_officer_confidential_remark' => $comment,
                    'reverted_by_role' => 'section_officer',
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
                ]);
                break;

            case 'doaa':
                if (!$user->isDoaa() && !$user->isAdoaa() && !$user->isSenateChairperson() && !$user->isArAcademic()) {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts2->update([
                    'doaa_confidential_remark' => $comment,
                    'reverted_by_role' => 'doaa',
                    'status' => 'reverted',
                    'current_stage' => 'rejected',
                ]);
                break;
        }

        return redirect()->route('dashboard')->with('warning', 'PTS-2 form has been reverted to the scholar for resubmission.');
    }
}
