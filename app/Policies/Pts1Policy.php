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
     * Determine if the user can review and endorse the form at the current stage.
     */
    public function review(User $user, Pts1Form $pts1): bool
    {
        return $pts1->canUserReview($user);
    }

    /**
     * Determine if the user is the Main Supervisor allowed to edit and update the student's submission.
     */
    public function mainSupervisorEdit(User $user, Pts1Form $pts1): bool
    {
        return $pts1->canMainSupervisorEdit($user);
    }

    /**
     * Determine if the user can revert the form.
     */
    public function revert(User $user, Pts1Form $pts1): bool
    {
        return $pts1->canUserRevert($user);
    }
}
