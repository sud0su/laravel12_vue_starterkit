<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): bool|null
    {
        // Grant all permissions to superadmin and admin
        if ($user->hasRole('superadmin') || $user->hasRole('admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view users');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        // A user can view their own profile, or if they have the general permission.
        return $user->id === $model->id || $user->hasPermissionTo('view users');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create users');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // A user can always update their own profile.
        // The permission 'edit users' is for updating other users.
        return $user->id === $model->id || $user->hasPermissionTo('edit users');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent a user from deleting their own account.
        if ($user->id === $model->id) {
            return false;
        }

        // Check if the user has the general permission to delete users.
        return $user->hasPermissionTo('delete users');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        return $user->hasPermissionTo('restore users');
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        // Prevent a user from force-deleting their own account.
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasPermissionTo('delete users');
    }
}
