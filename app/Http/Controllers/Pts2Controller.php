<?php

namespace App\Http\Controllers;

use App\Models\Pts2Form;
use App\Models\User;
use Illuminate\Http\Request;

class Pts2Controller extends Controller
{
    /**
     * Display dedicated full-page review & endorsement view for PTS-2 with complete audit trail.
     */
    public function showReview(Pts2Form $pts2)
    {
        $user = auth()->user();
        $thesis = $pts2->thesis;
        $student = $thesis->student;
        $studentUser = $student->user;

        $mainSupervisor = $student->mainSupervisors->first();
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
            'studentUser',
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
        $comment = $request->input('comment') ?: '';

        switch ($pts2->current_stage) {
            case 'main_supervisor':
                if ($user->id !== $pts2->thesis->student->main_supervisor_id && $user->id !== $pts2->thesis->main_supervisor_id) {
                    return back()->with('error', 'Unauthorized access.');
                }

                $nextStage = ($pts2->co_supervisor_1_id ? 'co_supervisors' : 'dpgc');
                $pts2->update([
                    'main_supervisor_recommendation' => true,
                    'main_supervisor_confidential_remark' => $comment,
                    'main_supervisor_submitted_at' => now(),
                    'current_stage' => $nextStage,
                ]);
                break;

            case 'co_supervisors':
                $roleKey = null;
                if ($pts2->co_supervisor_1_id === $user->id) $roleKey = 1;
                elseif ($pts2->co_supervisor_2_id === $user->id) $roleKey = 2;
                elseif ($pts2->co_supervisor_3_id === $user->id) $roleKey = 3;

                if (!$roleKey) {
                    return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
                }

                $pts2->update([
                    "co_supervisor_{$roleKey}_recommendation" => true,
                    "co_supervisor_{$roleKey}_confidential_remark" => $comment,
                    "co_supervisor_{$roleKey}_submitted_at" => now(),
                ]);

                $co1Done = !$pts2->co_supervisor_1_id || $pts2->co_supervisor_1_recommendation;
                $co2Done = !$pts2->co_supervisor_2_id || $pts2->co_supervisor_2_recommendation;
                $co3Done = !$pts2->co_supervisor_3_id || $pts2->co_supervisor_3_recommendation;

                if ($co1Done && $co2Done && $co3Done) {
                    $pts2->update([
                        'co_supervisors_submitted_at' => now(),
                        'current_stage' => 'dpgc'
                    ]);
                }
                break;

            case 'dpgc':
                if (!$user->isDpgc() && $user->role !== 'dpgc') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts2->update([
                    'dpgc_recommendation' => true,
                    'dpgc_confidential_remark' => $comment,
                    'dpgc_submitted_at' => now(),
                    'current_stage' => 'hod',
                ]);
                break;

            case 'hod':
                if (!$user->isHod() && $user->role !== 'hod') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts2->update([
                    'hod_recommendation' => true,
                    'hod_confidential_remark' => $comment,
                    'hod_submitted_at' => now(),
                    'current_stage' => 'section_officer',
                ]);
                break;

            case 'section_officer':
                if (!$user->isSectionOfficer() && $user->role !== 'section_officer') {
                    return back()->with('error', 'Unauthorized access.');
                }
                $pts2->update([
                    'academic_office_recommendation' => true,
                    'academic_office_confidential_remark' => $comment,
                    'academic_office_submitted_at' => now(),
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
                    'doaa_submitted_at' => now(),
                    'pts2_submitted_at' => now(),
                    'current_stage' => 'completed',
                    'status' => 'accepted',
                ]);
                $pts2->thesis->update(['status' => 'completed']);
                break;
        }

        return redirect()->route('dashboard')->with('success', 'PTS-2 Synopsis Form endorsed successfully.');
    }

    public function revert(Request $request, Pts2Form $pts2)
    {
        $user = auth()->user();
        $request->validate(['reversion_comment' => 'required|string']);
        $comment = $request->input('reversion_comment');

        $student = $pts2->thesis?->student;
        $isMainSup = $student && ($student->isMainSupervisor($user) || $student->mainSupervisors->pluck('id')->contains($user->id));

        $stage = $pts2->current_stage;
        $revertedRole = null;

        if ($stage === 'main_supervisor' || $isMainSup) {
            if (!$isMainSup && !$user->isFaculty()) {
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
            if (!$revertedRole && $isMainSup) {
                $revertedRole = 'main_supervisor';
            }
            if (!$revertedRole) {
                return back()->with('error', 'You are not an assigned Co-Supervisor for this thesis.');
            }
        } elseif ($stage === 'pspc_members') {
            for ($i = 1; $i <= 10; $i++) {
                $col = "pspc_member_{$i}_id";
                if ($pts2->$col === $user->id) {
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
        } elseif ($stage === 'academic_office' || $stage === 'section_officer') {
            if (!$user->isSectionOfficer() && !$isMainSup) {
                return back()->with('error', 'Unauthorized access.');
            }
            $revertedRole = $user->isSectionOfficer() ? 'section_officer' : 'main_supervisor';
        } elseif ($stage === 'doaa') {
            if (!($user->isDoaa() || $user->isAdoaa() || $user->isSenateChairperson() || $user->isArAcademic()) && !$isMainSup) {
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

        $pts2->update([
            'reversion_comment' => $comment,
            'reverted_by_role' => $revertedRole,
            'status' => 'reverted',
            'current_stage' => 'reverted',
        ]);

        $redirectRoute = $user->isFaculty() ? 'faculty.dashboard' : 'dashboard';
        return redirect()->route($redirectRoute)->with('warning', 'PTS-2 form has been reverted to the student for resubmission.');
    }
}
