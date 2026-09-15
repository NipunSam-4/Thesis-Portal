<?php

namespace App\Policies;

use App\Models\Pts3Form;
use App\Models\User;

class Pts3Policy
{
    /**
     * Determine if the user can view the PTS-3 form.
     */
    public function view(User $user, Pts3Form $pts3): bool
    {
        return $pts3->canUserView($user);
    }

    /**
     * Determine if the user can view the reverted PTS-3 form audit trail.
     */
    public function viewReverted(User $user, Pts3Form $pts3): bool
    {
        return $pts3->canUserViewRevertedForm($user);
    }

    /**
     * Determine if the user can review and endorse the PTS-3 form at the current stage.
     */
    public function review(User $user, Pts3Form $pts3): bool
    {
        if ($pts3->status !== 'in_progress') {
            return false;
        }

        $stage = $pts3->current_stage;
        $rank = $this->getUserRank($user, $pts3);

        return match ($stage) {
            'main_supervisor' => ($rank === 1),
            'co_supervisors' => ($rank === 2 && $this->isCoSupervisorPending($user, $pts3)),
            'dpgc' => ($rank === 3),
            'hod' => ($rank === 4),
            'academic_office' => ($rank === 5),
            'doaa' => ($rank === 6),
            'senate_chairperson' => ($rank === 7),
            default => false,
        };
    }

    /**
     * Determine if the user can revert the PTS-3 form.
     */
    public function revert(User $user, Pts3Form $pts3): bool
    {
        if ($pts3->status !== 'in_progress') {
            return false;
        }
        $rank = $this->getUserRank($user, $pts3);
        // Authorities from DPGC (rank 3) up to Senate Chairperson (rank 7) can revert
        return $rank >= 3;
    }

    protected function getUserRank(User $user, Pts3Form $pts3): int
    {
        $thesis = $pts3->thesis;
        $student = $thesis?->student;

        if ($student && $student->isMainSupervisor($user)) {
            return 1;
        }
        if ($student && $student->isCoSupervisor($user)) {
            return 2;
        }
        if ($user->isDpgc() && $student && $user->deptAuthorityProfile?->department_id === $student->department_id) {
            return 3;
        }
        if ($user->isHod() && $student && $user->deptAuthorityProfile?->department_id === $student->department_id) {
            return 4;
        }
        if ($user->isAcademicOffice()) {
            return 5;
        }
        if ($user->isDoaa() || $user->isAdoaa() || ($user->isActingApprovalAuthority() && ($pts3->acting_doaa_email === $user->email || $pts3->vested_doaa_email === $user->email)) || ($pts3->vested_doaa_email && $user->email === $pts3->vested_doaa_email) || ($pts3->acting_doaa_email && $user->email === $pts3->acting_doaa_email)) {
            return 6;
        }
        if ($user->role === 'senate_chairperson' || auth('admin')->check()) {
            return 7;
        }
        return 0;
    }

    protected function isCoSupervisorPending(User $user, Pts3Form $pts3): bool
    {
        for ($i = 1; $i <= 10; $i++) {
            if ($pts3->{"co_supervisor_{$i}_id"} === $user->id) {
                return $pts3->{"co_supervisor_{$i}_recommendation"} === null;
            }
        }
        return false;
    }
}
