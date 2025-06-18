<?php
declare(strict_types=1);
namespace App\Enums;

use App\Models\User;

enum Permission: string
{
    // case NAMEINAPP = 'name-in-database';
    case ORGANIZATION_MANAGE_USERS = 'organization-manage-users';
    case ORGANIZATION_MANAGER = 'organization-manager';
    case ORGANIZATION_BILLING = 'organization-billing';

    case CUSTOMER_EDIT = 'customer-edit';
    case CUSTOMER_VIEW = 'customer-view';

    case FACILITY_EDIT = 'facility-edit';
    case FACILITY_VIEW = 'facility-view';

    case CARRIER_EDIT = 'carrier-edit';
    case CARRIER_VIEW = 'carrier-view';

    case SHIPMENT_EDIT = 'shipment-edit';

    case CHECK_CALL_EDIT = 'check-call-edit';

    case INTEGRATION_SETTINGS_EDIT = 'integration-settings-edit';

    // extra helper to allow for greater customization of displayed values, without disclosing the name/value data directly
    public function label(): string
    {
        return match ($this) {
            self::ORGANIZATION_MANAGE_USERS => 'Manage Organization Users & Permissions',
            self::ORGANIZATION_MANAGER => 'View & Edit Organization Details',
            self::ORGANIZATION_BILLING => 'Manage Organization Billing & Subscriptions',
            self::CUSTOMER_EDIT => 'Edit Customer Details',
            self::CUSTOMER_VIEW => 'View Customer Details',
            self::FACILITY_EDIT => 'Edit Facility Details',
            self::FACILITY_VIEW => 'View Facility Details',
            self::CARRIER_EDIT => 'Edit Carrier Details',
            self::CARRIER_VIEW => 'View Carrier Details',
            self::SHIPMENT_EDIT => 'Edit Shipment Details',
            self::CHECK_CALL_EDIT => 'Edit Check Calls',
            self::INTEGRATION_SETTINGS_EDIT => 'Edit Integration Settings',
        };
    }

    public static function syncToDatabase(): bool
    {
        foreach (static::cases() as $permission) {
            if (!\Spatie\Permission\Models\Permission::where('name', $permission->value)->exists()) {
                \Spatie\Permission\Models\Permission::create(['name' => $permission->value]);
            }
        }

        return true;
    }

    public static function getPermissionsForUser(?User $user): array
    {
        if (!$user) {
            return [];
        }

        $permissions = [];
        foreach (static::cases() as $permission) {
            $permissions[$permission->name] = $user->can($permission) ? true : false;
        }
        return $permissions;
    }
}
