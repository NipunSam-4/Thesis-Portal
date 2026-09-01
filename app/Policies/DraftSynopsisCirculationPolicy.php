<?php

namespace App\Policies;

use App\Models\DraftSynopsisCirculation;
use App\Models\User;

class DraftSynopsisCirculationPolicy
{
    /**
     * Determine whether the user can view the draft synopsis circulation and feedback.
     */
    public function view(User $user, DraftSynopsisCirculation $circulation): bool
    {
        $student = $circulation->thesis?->student;
        if (!$student) {
            return false;
        }

        if ($user->isStudent()) {
            return (int)$student->user_id === (int)$user->id;
        }

        return $student->isSupervisor($user) || $student->isPspcMember($user);
    }

    /**
     * Determine whether the user can submit comments on the draft synopsis.
     */
    public function comment(User $user, DraftSynopsisCirculation $circulation): bool
    {
        $pts1Form = $circulation->thesis?->pts1Form;
        if ($pts1Form && $pts1Form->status === 'approved') {
            return false;
        }

        $student = $circulation->thesis?->student;
        if (!$student) {
            return false;
        }

        return $student->isSupervisor($user) || $student->isPspcMember($user);
    }
}
