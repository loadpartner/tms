<?php

namespace App\Actions\Defaults;

use App\Models\Accounting\RateType;
use App\Models\Permissions\Role;
use App\Models\Shipments\TrailerSize;
use App\Models\Shipments\TrailerType;
use Lorisleiva\Actions\Concerns\AsAction;

class CreateOrUpdateOrganizationDefaults {
    use AsAction;

    
    public function handle(int $organizationId) {
        
        $this->createDefaultRoles($organizationId);

        $this->createDefaultRateTypes($organizationId);
        
        $this->createDefaultTrailers($organizationId);

        /** Contact types */
        // TODO - https://linear.app/loadpartner/issue/TMS-133/change-contact-types-to-the-new-default-format-and-remove-the-enums

        /** Default document folders */
        // TODO - change document folders to use a "default folders" approach
        // https://linear.app/loadpartner/issue/TMS-120/default-document-folders-table-instead-of-enum-merge-types-together
    }

    protected function createDefaultTrailers(int $organizationId) {

        /** Check if this needs to run */
        $trailers = TrailerType::where('organization_id', $organizationId)->get();
        if ($trailers->count() > 0) {
            return;
        }

        /** Trailer related */
        TrailerType::create(['name' => 'Stepdeck', 'organization_id' => $organizationId]);
        TrailerType::create(['name' => 'Dry Van', 'organization_id' => $organizationId]);
        TrailerType::create(['name' => 'Reefer', 'organization_id' => $organizationId]);
        TrailerType::create(['name' => 'Flatbed', 'organization_id' => $organizationId]);

        TrailerSize::create(['name'=> '53\'', 'organization_id' => $organizationId]);
        TrailerSize::create(['name'=> '48\'', 'organization_id' => $organizationId]);
    }

    protected function createDefaultRoles(int $organizationId) {

        /** Check if this needs to run */
        $roles = Role::where('organization_id', $organizationId)->get();
        if ($roles->count() > 0) {
            return;
        }

        Role::create([
            'name' => 'default',
            'organization_id' => $organizationId
        ]);
    }

    protected function createDefaultRateTypes(int $organizationId) {

        /** Check if this needs to run */
        $rateTypes = RateType::where('organization_id', $organizationId)->get();
        if ($rateTypes->count() > 0) {
            return;
        }

        /** rate types */
        RateType::create(['name' => 'Fee Type', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Pre-pull', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Per Diem', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Demurrage', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Detention', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Yard Storage', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Chassis Use', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Reefer Fee', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Drop and Hook', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Layover', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Congestion Fee', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Fuel Surcharge', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Overweight', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Lumper Fee', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Port Fees', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Chassis Split', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Flat', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Per Mile', 'organization_id' => $organizationId]);
        RateType::create(['name' => 'Linehaul', 'organization_id' => $organizationId]);
        
    }
}