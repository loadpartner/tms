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
        Schema::table('customers', function (Blueprint $table) {
            $table->integer('net_pay_days')->nullable();
            $table->unsignedBigInteger('billing_location_id')->nullable();
            $table->string('dba_name')->nullable();
            $table->string('invoice_number_schema')->nullable();
            $table->integer('billing_contact_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {
            $table->dropColumn([
                'net_pay_days',
                'billing_location_id',
                'dba_name',
                'invoice_number_schema',
                'billing_contact_id'
            ]);
        });
        
    }
};
