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
        Schema::table('organizations', function (Blueprint $table) {
            $table->string('company_name')->nullable();
            $table->unsignedBigInteger('company_location_id')->nullable();
            $table->string('company_phone')->nullable();
            $table->string('company_email')->nullable();
            $table->string('accounting_contact_email')->nullable();
            $table->string('accounting_contact_phone')->nullable();
            
            // Add foreign key constraint
            $table->foreign('company_location_id')->references('id')->on('locations')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('organizations', function (Blueprint $table) {
            $table->dropForeign(['company_location_id']);
            $table->dropColumn([
                'company_name',
                'company_location_id',
                'company_phone',
                'company_email',
                'accounting_contact_email',
                'accounting_contact_phone',
            ]);
        });
    }
};
