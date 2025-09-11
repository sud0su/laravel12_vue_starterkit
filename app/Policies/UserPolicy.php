<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
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
        // Check if user has general view permission or can view own data
        if ($user->hasPermissionTo('view users')) {
            return true;
        }

        // Check if user can view own data
        if ($user->hasPermissionTo('view own users') && $user->id === $model->id) {
            return true;
        }

        return false;
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
        // Check if user has general edit permission
        if ($user->hasPermissionTo('edit users')) {
            return true;
        }

        // Check if user can edit own data
        if ($user->hasPermissionTo('edit own users') && $user->id === $model->id) {
            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        // Prevent deleting own account
        if ($user->id === $model->id) {
            return false;
        }

        // Check if user has general delete permission
        if ($user->hasPermissionTo('delete users')) {
            return true;
        }

        // Check if user can delete own data (though this is restricted above)
        if ($user->hasPermissionTo('delete own users') && $user->id === $model->id) {
            return false; // Still prevent self-deletion
        }

        return false;
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
        return $user->hasPermissionTo('delete users');
    }
}
