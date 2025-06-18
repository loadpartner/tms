<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('check_calls', function (Blueprint $table) {
            // Drop the incorrectly typed columns
            $table->dropColumn(['reported_trailer_temp', 'is_truck_empty']);
        });
        
        Schema::table('check_calls', function (Blueprint $table) {
            // Re-add them with correct types
            $table->string('reported_trailer_temp')->nullable();
            $table->boolean('is_truck_empty')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('check_calls', function (Blueprint $table) {
            // Drop the corrected columns
            $table->dropColumn(['reported_trailer_temp', 'is_truck_empty']);
        });
        
        Schema::table('check_calls', function (Blueprint $table) {
            // Re-add them with original incorrect types
            $table->dateTimeTz('reported_trailer_temp')->nullable();
            $table->dateTimeTz('is_truck_empty')->nullable();
        });
    }
};
