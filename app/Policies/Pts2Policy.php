<?php

namespace App\Policies;

use App\Models\Pts2Form;
use App\Models\User;

class Pts2Policy
{
    /**
     * Determine if the user can view the form (in-progress, submitted, approved, or rejected).
     */
    public function view(User $user, Pts2Form $pts2): bool
    {
        return $pts2->canUserView($user);
    }

    /**
     * Determine if the user can view the reverted form audit trail.
     */
    public function viewReverted(User $user, Pts2Form $pts2): bool
    {
        return $pts2->canUserViewRevertedForm($user);
    }

    /**
     * Determine if the user can evaluate/endorse the form at the current stage.
     */
    public function evaluate(User $user, Pts2Form $pts2): bool
    {
        return $pts2->canUserEvaluate($user);
    }

    /**
     * Determine if the user is the Main Supervisor allowed to edit and review the student's submission.
     */
    public function supervisorEdit(User $user, Pts2Form $pts2): bool
    {
        $thesis = $pts2->thesis;
        return $pts2->status === 'in_progress'
            && $pts2->current_stage === 'main_supervisor'
            && $thesis
            && $thesis->student
            && $thesis->student->isMainSupervisor($user);
    }

    /**
     * Determine if the user can revert the form.
     */
    public function revert(User $user, Pts2Form $pts2): bool
    {
        return $pts2->canUserRevert($user);
    }
}
