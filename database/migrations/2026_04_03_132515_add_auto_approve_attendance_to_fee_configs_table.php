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
        Schema::table('fee_configs', function (Blueprint $table) {
            $table->boolean('is_auto_approve_attendance')->default(false); // Removed after() for PostgreSQL compatibility
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fee_configs', function (Blueprint $table) {
            $table->dropColumn('is_auto_approve_attendance');
        });
    }
};
