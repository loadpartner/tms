<?php

use App\Enums\Permission;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Role;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Permission::syncToDatabase();

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Permissions are handled by the syncToDatabase method, no need to delete individually
    }
}; 