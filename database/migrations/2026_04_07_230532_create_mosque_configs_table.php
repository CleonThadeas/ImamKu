<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mosque_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->unique()->constrained('ramadan_seasons')->cascadeOnDelete();
            $table->string('name');
            $table->decimal('latitude', 10, 8);
            $table->decimal('longitude', 11, 8);
            $table->unsignedInteger('radius_meters')->default(100);
            $table->unsignedInteger('attendance_window_minutes')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('mosque_configs');
    }
};
