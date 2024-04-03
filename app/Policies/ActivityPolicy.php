<?php

namespace App\Policies;

use App\Models\Activity;
use App\Models\User;

class ActivityPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Activity $activity): bool
    {
        return $activity->user_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(): bool
    {
        return true;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Activity $activity): bool
    {
        return $activity->user_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Activity $activity): bool
    {
        return $activity->user_id === $user->id;
    }
}
