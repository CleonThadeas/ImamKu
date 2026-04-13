<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->integer('penalty_points')->default(0); // Removed after() for PostgreSQL compatibility
            $table->boolean('is_restricted')->default(false); // Removed after() for PostgreSQL compatibility
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['penalty_points', 'is_restricted']);
        });
    }
};
