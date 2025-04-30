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
        Schema::create('shipment_flags', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shipment_id');
            $table->foreignId('organization_id');
            $table->string('type')->index();
            $table->foreignId('created_by')->constrained('users');
            $table->foreignId('updated_by')->constrained('users');
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        Schema::create('shipment_flag_custom_types', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id');
            $table->string('name')->index();
            $table->string('color');
            $table->string('icon');
            $table->string('label');
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_flags');
        Schema::dropIfExists('shipment_flag_custom_types');
    }
};
