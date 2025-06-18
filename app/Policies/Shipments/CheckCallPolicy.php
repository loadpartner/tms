<?php

namespace App\Policies\Shipments;

use App\Enums\Permission;
use App\Models\CheckCalls\CheckCall;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class CheckCallPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, CheckCall $checkCall): bool
    {
        return true;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can(Permission::CHECK_CALL_EDIT);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, CheckCall $checkCall): bool
    {
        return $user->can(Permission::CHECK_CALL_EDIT);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, CheckCall $checkCall): bool
    {
        return (
            $user->can(Permission::CHECK_CALL_EDIT) 
            && $checkCall->user_id === $user->id
        );
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, CheckCall $checkCall): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, CheckCall $checkCall): bool
    {
        return false;
    }
} 