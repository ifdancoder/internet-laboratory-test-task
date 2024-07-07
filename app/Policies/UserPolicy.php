<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, User $user_updatable)
    {
        return $user->id === $user_updatable->id;
    }

    public function delete(User $user, User $user_updatable)
    {
        return $user->id === $user_updatable->id;
    }
}
