<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('prayer_times', function (Blueprint $table) {
            $table->id();
            $table->foreignId('season_id')->constrained('ramadan_seasons')->cascadeOnDelete();
            $table->date('date');
            $table->foreignId('prayer_type_id')->constrained('prayer_types')->cascadeOnDelete();
            $table->time('api_time')->nullable();
            $table->time('override_time')->nullable();
            $table->timestamps();

            $table->unique(['season_id', 'date', 'prayer_type_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prayer_times');
    }
};
