<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // PostgreSQL compatible: This migration is redundant as Enum was converted to string in creation.
        // DB::statement("ALTER TABLE notification_logs MODIFY COLUMN channel VARCHAR(255)");
        // DB::statement("ALTER TABLE notification_logs MODIFY COLUMN status VARCHAR(50)");
    }

    public function down(): void
    {
        // DB::statement("ALTER TABLE notification_logs MODIFY COLUMN channel ENUM('email', 'whatsapp')");
        // DB::statement("ALTER TABLE notification_logs MODIFY COLUMN status ENUM('sent', 'failed', 'pending') DEFAULT 'pending'");
    }
};
