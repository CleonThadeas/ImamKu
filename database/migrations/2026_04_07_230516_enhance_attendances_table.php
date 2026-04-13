<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->decimal('latitude', 10, 8)->nullable()->after('proof_path');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->unsignedInteger('distance_meters')->nullable()->after('longitude');
            $table->boolean('is_within_radius')->default(false)->after('distance_meters');
            $table->boolean('is_within_time_window')->default(false)->after('is_within_radius');
            $table->timestamp('checked_in_at')->nullable()->after('is_within_time_window');
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
