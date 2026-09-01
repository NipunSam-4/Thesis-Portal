<?php

namespace App\Policies;

use App\Models\Pts2Extension;
use App\Models\User;

class Pts2ExtensionPolicy
{
    /**
     * Determine if the user can view the PTS-2 Extension form.
     */
    public function view(User $user, Pts2Extension $pts2Extension): bool
    {
        return $pts2Extension->canUserView($user);
    }

    /**
     * Determine if the user can evaluate/endorse the PTS-2 Extension form.
     */
    public function evaluate(User $user, Pts2Extension $pts2Extension): bool
    {
        return $pts2Extension->canUserEvaluate($user);
    }

    /**
     * Determine if the user can revert the PTS-2 Extension form.
     */
    public function revert(User $user, Pts2Extension $pts2Extension): bool
    {
        return $pts2Extension->canUserEvaluate($user);
    }
}
