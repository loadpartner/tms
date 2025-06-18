<?php

use App\Actions\Defaults\CreateOrUpdateOrganizationDefaults;
use App\Models\Organizations\Organization;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $organizations = Organization::all();

        foreach ($organizations as $organization) {
            run_with_organization($organization->id, function() use ($organization) {
                CreateOrUpdateOrganizationDefaults::run($organization->id);
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
