<?php

namespace App\Policies\Organizations;

use App\Enums\Permission;
use App\Models\Organizations\IntegrationSetting;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class IntegrationSettingPolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view integration settings.
     */
    public function view(User $user, IntegrationSetting $setting): bool
    {
        return $user->hasPermissionTo(Permission::INTEGRATION_SETTINGS_EDIT->value) && 
               $user->current_organization_id === $setting->organization_id;
    }

    /**
     * Determine whether the user can view any integration settings.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(Permission::INTEGRATION_SETTINGS_EDIT->value);
    }

    /**
     * Determine whether the user can create integration settings.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(Permission::INTEGRATION_SETTINGS_EDIT->value);
    }

    /**
     * Determine whether the user can update integration settings.
     */
    public function update(User $user, IntegrationSetting $setting): bool
    {
        return $user->hasPermissionTo(Permission::INTEGRATION_SETTINGS_EDIT->value) && 
               $user->current_organization_id === $setting->organization_id;
    }

    /**
     * Determine whether the user can delete integration settings.
     */
    public function delete(User $user, IntegrationSetting $setting): bool
    {
        return $user->hasPermissionTo(Permission::INTEGRATION_SETTINGS_EDIT->value) && 
               $user->current_organization_id === $setting->organization_id;
    }
} 