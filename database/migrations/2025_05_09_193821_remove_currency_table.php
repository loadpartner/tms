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
        // fix currency_id fields in tables to use code

        Schema::table('payables', function (Blueprint $table) {
            $table->string('currency_code')->default('usd')->after('rate_type_id');
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });

        Schema::table('receivables', function (Blueprint $table) {
            $table->string('currency_code')->default('usd')->after('rate_type_id');
            $table->dropForeign(['currency_id']);
            $table->dropColumn('currency_id');
        });

        // remove currency table
        Schema::dropIfExists('currencies');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        throw new Exception("Not reversible");
    }
};
