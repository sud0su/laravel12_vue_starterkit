<?php

namespace App\Policies;

use Illuminate\Auth\Access\Response;
use App\Models\User;

class RolePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        // Allow superadmin and admin full access
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }
        return $user->hasPermissionTo('view roles');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, $role): bool
    {
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }
        return $user->hasPermissionTo('view roles');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }
        return $user->hasPermissionTo('create roles') || $user->hasPermissionTo('manage roles');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, $role): bool
    {
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }
        return $user->hasPermissionTo('edit roles') || $user->hasPermissionTo('manage roles');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, $role): bool
    {
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }
        return $user->hasPermissionTo('delete roles') || $user->hasPermissionTo('manage roles');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, $role): bool
    {
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }
        return $user->hasPermissionTo('restore roles') || $user->hasPermissionTo('manage roles');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, $role): bool
    {
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }
        return $user->hasPermissionTo('delete roles') || $user->hasPermissionTo('manage roles');
    }
}
