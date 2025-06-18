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
        Schema::table('carrier_bounces', function (Blueprint $table) {
            $table->renameColumn('bounce_type', 'bounce_cause');
            $table->string('reason')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('carrier_bounces', function (Blueprint $table) {
            $table->renameColumn('bounce_cause', 'bounce_type');
            $table->string('reason')->nullable(false)->change();
        });
    }
};
