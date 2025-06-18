<?php

use App\Models\Accounting\Currency;
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
        Schema::create('currencies', function(Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('symbol');
        });
        Schema::create('customer_rate_types', function(Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name');
            $table->timestampsTz();
            $table->softDeletesTz();
        }); 

        Schema::create('carrier_rate_types', function(Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name');
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        Schema::create('accessorial_types', function(Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->string('name');
            $table->timestampsTz();
            $table->softDeletesTz();
        });

        Schema::create('accessorials', function(Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained('organizations');
            $table->foreignId('shipment_id')->constrained('shipments');

            $table->foreignId('customer_id')->nullable(true)->constrained('customers');
            $table->foreignId('carrier_id')->nullable(true)->constrained('carriers');

            $table->boolean('invoice_customer')->default(false);
            $table->boolean('pay_carrier')->default(false);

            $table->decimal('rate', 10, 2);
            $table->decimal('quantity', 10, 2);
            $table->decimal('total', 10, 2);

            $table->foreignId('accessorial_type_id')->constrained('accessorial_types');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->timestampsTz();

        });

        Schema::create('shipment_customer_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('customers');
            $table->foreignId('shipment_id')->constrained('shipments');
            $table->foreignId('organization_id')->constrained('organizations');

            $table->decimal('rate', 10, 2);
            $table->decimal('quantity', 10, 2);
            $table->decimal('total', 10, 2);

            $table->foreignId('customer_rate_type_id')->constrained('customer_rate_types');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->timestampsTz();
        });

        Schema::create('shipment_carrier_rates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('carrier_id')->constrained('carriers');
            $table->foreignId('shipment_id')->constrained('shipments');
            $table->foreignId('organization_id')->constrained('organizations');

            $table->decimal('rate', 10, 2);
            $table->decimal('quantity', 10, 2);
            $table->decimal('total', 10, 2);

            $table->foreignId('carrier_rate_type_id')->constrained('carrier_rate_types');
            $table->foreignId('currency_id')->constrained('currencies');
            $table->timestampsTz();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shipment_carrier_rates');
        Schema::dropIfExists('shipment_customer_rates');
        Schema::dropIfExists('accessorials');
        Schema::dropIfExists('accessorial_types');
        Schema::dropIfExists('carrier_rate_types');
        Schema::dropIfExists('customer_rate_types');
        Schema::dropIfExists('currencies');
    }
};
