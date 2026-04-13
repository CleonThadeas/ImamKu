<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            // Removed after() for PostgreSQL compatibility
            $table->decimal('latitude', 10, 8)->nullable();
            $table->decimal('longitude', 11, 8)->nullable();
            $table->unsignedInteger('distance_meters')->nullable();
            $table->boolean('is_within_radius')->default(false);
            $table->boolean('is_within_time_window')->default(false);
            $table->timestamp('checked_in_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropColumn([
                'latitude', 'longitude', 'distance_meters',
                'is_within_radius', 'is_within_time_window', 'checked_in_at',
            ]);
        });
    }
};
