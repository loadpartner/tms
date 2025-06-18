<?php

use App\Enums\Permission;
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
        Schema::create('integration_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('organization_id')->constrained()->cascadeOnDelete();
            $table->string('key');
            $table->text('value')->nullable();
            $table->string('provider')->nullable()->comment('Integration provider name (e.g., stripe, twilio)');
            $table->boolean('expose_to_frontend')->default(false);
            $table->timestampsTz();
            
            $table->unique(['organization_id', 'key']);
        });

        Permission::syncToDatabase();
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('integration_settings');

        Permission::syncToDatabase();
    }
}; 