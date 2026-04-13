<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement("ALTER TABLE penalty_logs MODIFY COLUMN event_type ENUM('attendance_ontime', 'attendance_late', 'no_show', 'swap_expired', 'admin_reset') NOT NULL");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE penalty_logs MODIFY COLUMN event_type ENUM('attendance_ontime', 'attendance_late', 'no_show', 'swap_expired') NOT NULL");
    }
};
