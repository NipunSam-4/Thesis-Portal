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
        return $pts4Extension->canUserView($user);
    }

    /**
     * Determine if the user can review/endorse the PTS-4 Extension form.
     */
    public function review(User $user, Pts4Extension $pts4Extension): bool
    {
        return $pts4Extension->canUserReview($user);
    }

    /**
     * Determine if the user can revert the PTS-4 Extension form.
     */
    public function revert(User $user, Pts4Extension $pts4Extension): bool
    {
        return $this->review($user, $pts4Extension);
    }
}

