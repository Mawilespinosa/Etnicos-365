<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * The built-in admin role cannot be deleted.
     */
    public function delete(User $user, Role $role): bool
    {
        return $role->name !== 'admin';
    }
}