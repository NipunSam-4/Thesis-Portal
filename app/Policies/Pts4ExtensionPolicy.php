<?php

namespace App\Policies;

use App\Models\Pts4Extension;
use App\Models\User;

class Pts4ExtensionPolicy
{
    /**
     * Determine if the user can view the PTS-4 Extension form.
     */
    public function view(User $user, Pts4Extension $pts4Extension): bool
    {
        $thesis = $pts4Extension->thesis;
        $student = $thesis?->student;
        if (!$student) return false;

        if ($user->isStudent()) {
            return (int)$student->user_id === (int)$user->id;
        }

        if ($student->isSupervisor($user) || $student->isPspcMember($user)) {
            return true;
        }

        if ($user->isDeptAuthority()) {
            return (int)$user->deptAuthorityProfile?->department_id === (int)$student->department_id;
        }

        if ($user->isGlobalAuthority() || $user->isActingApprovalAuthority()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can review/endorse the PTS-4 Extension form.
     */
    public function review(User $user, Pts4Extension $pts4Extension): bool
    {
        if ($pts4Extension->status !== 'in_progress') {
            return false;
        }

        $thesis = $pts4Extension->thesis;
        $student = $thesis?->student;
        if (!$student) return false;

        $userRole = null;
        if ($student->isMainSupervisor($user)) {
            $userRole = 'main_supervisor';
        } elseif ($user->isDpgc() && (int)$user->deptAuthorityProfile?->department_id === (int)$student->department_id) {
            $userRole = 'dpgc';
        } elseif ($user->isHod() && (int)$user->deptAuthorityProfile?->department_id === (int)$student->department_id) {
            $userRole = 'hod';
        } elseif ($user->isAcademicOffice()) {
            $userRole = 'academic_office';
        } elseif ($user->isDoaa() || ($user->isActingApprovalAuthority() && ($pts4Extension->acting_doaa_email === $user->email || $pts4Extension->vested_doaa_email === $user->email))) {
            $userRole = 'doaa';
        }

        return $userRole !== null && $pts4Extension->current_stage === $userRole;
    }

    /**
     * Determine if the user can revert the PTS-4 Extension form.
     */
    public function revert(User $user, Pts4Extension $pts4Extension): bool
    {
        return $this->review($user, $pts4Extension);
    }
}
