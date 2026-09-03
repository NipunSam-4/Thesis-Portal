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
     * Determine if the user can review and endorse the form at the current stage.
     */
     public function review(User $user, Pts2Form $pts2): bool
     {
         return $pts2->canUserReview($user);
     }

     /**
      * Determine if the user is the Main Supervisor allowed to edit and update the student's submission.
      */
     public function mainSupervisorEdit(User $user, Pts2Form $pts2): bool
     {
         return $pts2->canMainSupervisorEdit($user);
     }

     /**
      * Determine if the user can revert the form.
      */
     public function revert(User $user, Pts2Form $pts2): bool
     {
         return $pts2->canUserRevert($user);
     }
 }
