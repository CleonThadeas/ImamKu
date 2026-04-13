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
        Schema::table('swap_requests', function (Blueprint $table) {
            // PostgreSQL compatible: This migration is redundant and ->change() avoided. 
            // target_schedule_id was made nullable directly in 2026_04_02_000006.
            // $table->dropForeign(['target_schedule_id']);
            // $table->unsignedBigInteger('target_schedule_id')->nullable()->change();
            // $table->foreign('target_schedule_id')->references('id')->on('schedules')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('swap_requests', function (Blueprint $table) {
            // $table->dropForeign(['target_schedule_id']);
            // $table->unsignedBigInteger('target_schedule_id')->nullable(false)->change();
            // $table->foreign('target_schedule_id')->references('id')->on('schedules')->cascadeOnDelete();
        });
    }
};
