<?php

namespace App\Policies;

use App\Models\Pts1Form;
use App\Models\User;

class Pts1Policy
{
    /**
     * Determine if the user can view the form (in-progress, submitted, approved, or rejected).
     */
    public function view(User $user, Pts1Form $pts1): bool
    {
        return $pts1->canUserView($user);
    }

    /**
     * Determine if the user can view the reverted form audit trail.
     */
    public function viewReverted(User $user, Pts1Form $pts1): bool
    {
        return $pts1->canUserViewRevertedForm($user);
    }

    /**
     * Determine if the user can evaluate/endorse the form at the current stage.
     */
    public function evaluate(User $user, Pts1Form $pts1): bool
    {
        return $pts1->canUserEvaluate($user);
    }

    /**
     * Determine if the user is the Main Supervisor allowed to edit and review the student's submission.
     */
    public function supervisorEdit(User $user, Pts1Form $pts1): bool
    {
        $thesis = $pts1->thesis;
        return $pts1->status === 'in_progress'
            && $pts1->current_stage === 'main_supervisor'
            && $thesis
            && $thesis->student
            && $thesis->student->isMainSupervisor($user);
    }

    /**
     * Determine if the user can revert the form.
     */
    public function revert(User $user, Pts1Form $pts1): bool
    {
        return $pts1->canUserRevert($user);
    }
}
