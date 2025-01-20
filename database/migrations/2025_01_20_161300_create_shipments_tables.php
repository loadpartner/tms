<?php

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

        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name');
            $table->string('address_line_1');
            $table->string('address_line_2')->nullable();
            $table->string('address_city');
            $table->string('address_state');
            $table->string('address_zipcode');
            $table->timestampsTz();
        });
        

        Schema::create('facilities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name');
            $table->foreignId('location_id')->constrained('locations');
            $table->timestampsTz();
        });

        Schema::create('carriers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name');
            $table->timestampsTz();
        });

        Schema::create('shippers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name');
            $table->timestampsTz();
        });

        
        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('carrier_id')->constrained('carriers');
            $table->timestampsTz();
        });

        Schema::create('shipment_shippers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('shipment_id')->constrained('shipments');
            $table->foreignId('shipper_id')->constrained('shippers');
            $table->timestampsTz();
        });

        Schema::create('shipment_stops', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('shipment_id')->constrained('shipments');
            $table->foreignId('facility_id')->constrained('facilities');
            $table->string('stop_type');
            $table->timestampsTz();
        });

        Schema::create('shipment_stop_appointments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('shipment_stop_id')->constrained('shipment_stops');
            $table->dateTimeTz('appointment_datetime');
            $table->dateTimeTz('appointment_end_datetime')->nullable();
            $table->string('appointment_type')->nullable();
            $table->softDeletesTz();
            $table->timestampsTz();
        });

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_stop_appointments');
        Schema::dropIfExists('shipment_stops');
        Schema::dropIfExists('shipment_shippers');
        Schema::dropIfExists('shipments');
        Schema::dropIfExists('shippers');
        Schema::dropIfExists('facilities');
        Schema::dropIfExists('carriers');
        Schema::dropIfExists('locations');
    }
};
