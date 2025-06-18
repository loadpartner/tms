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

        Schema::create('check_calls', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id');
            $table->foreignId('carrier_id')->nullable();
            $table->foreignId('shipment_id');
            $table->foreignId('user_id');
            $table->string('contact_name');
            $table->string('contact_method')->nullable();
            $table->string('contact_method_detail')->nullable();
            $table->boolean('is_late')->nullable();
            $table->dateTimeTz('arrived_at')->nullable();
            $table->dateTimeTz('left_at')->nullable();
            $table->dateTimeTz('eta')->nullable();
            $table->dateTimeTz('is_truck_empty')->nullable();
            $table->dateTimeTz('reported_trailer_temp')->nullable();
            $table->dateTimeTz('loaded_unloaded_at')->nullable();
            $table->foreignId('note_id')->nullable();
            $table->foreignId('next_stop_id')->nullable();
            $table->foreignId('current_stop_id')->nullable();
            $table->timestampsTz();
            $table->softDeletesTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('check_calls');
    }
}; 