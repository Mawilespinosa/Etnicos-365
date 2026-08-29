<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * A user cannot delete their own account.
     */
    public function delete(User $user, User $targetUser): bool
    {
        return $user->id !== $targetUser->id;
    }
}