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
        Schema::dropIfExists('shipment_carrier_rates');
        Schema::dropIfExists('shipment_customer_rates');
        Schema::dropIfExists('accessorials');
        Schema::dropIfExists('accessorial_types');
        Schema::dropIfExists('carrier_rate_types');
        Schema::dropIfExists('customer_rate_types');

        Schema::create('rate_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('organization_id')->constrained('organizations');
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        Schema::create('payables', function(Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('shipment_id')->constrained('shipments');

            $table->morphs('payee');

            $table->decimal('rate', 10, 2);
            $table->decimal('quantity', 10, 2);
            $table->decimal('total', 10, 2);

            $table->foreignId('rate_type_id')->constrained('rate_types');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->timestampsTz();
            $table->softDeletesTz();

        });

        Schema::create('receivables', function(Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('shipment_id')->constrained('shipments');

            $table->morphs('payer');

            $table->decimal('rate', 10, 2);
            $table->decimal('quantity', 10, 2);
            $table->decimal('total', 10, 2);

            $table->foreignId('rate_type_id')->constrained('rate_types');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->timestampsTz();
            $table->softDeletesTz();
        });


        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new \Exception('This migration is irreversible');
    }
};
