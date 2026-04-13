<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fee_configs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('ramadan_seasons')->cascadeOnDelete();
            $table->enum('mode', ['per_schedule', 'per_day'])->default('per_schedule');
            $table->boolean('is_enabled')->default(false);
            $table->timestamps();
        });

        Schema::create('fee_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fee_config_id')->constrained('fee_configs')->cascadeOnDelete();
            $table->foreignId('prayer_type_id')->nullable()->constrained('prayer_types')->nullOnDelete();
            $table->decimal('amount', 12, 2)->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fee_details');
        Schema::dropIfExists('fee_configs');
    }
};
