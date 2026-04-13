<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('penalty_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('schedule_id')->nullable()->constrained('schedules')->nullOnDelete();
            $table->foreignId('swap_request_id')->nullable()->constrained('swap_requests')->nullOnDelete();
            $table->string('event_type'); // PostgreSQL compatible: using string instead of enum
            $table->integer('points');
            $table->text('description')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'schedule_id', 'event_type'], 'penalty_unique_event');
            $table->index(['user_id', 'event_type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('penalty_logs');
    }
};
