<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE notification_logs ALTER COLUMN channel TYPE VARCHAR(255)");
        DB::statement("ALTER TABLE notification_logs ALTER COLUMN status TYPE VARCHAR(50)");
    }

    public function down(): void
    {
        // PostgreSQL tidak mendukung ENUM seperti MySQL secara langsung
        // jadi kita fallback ke VARCHAR saja (safe rollback)

        DB::statement("ALTER TABLE notification_logs ALTER COLUMN channel TYPE VARCHAR(50)");
        DB::statement("ALTER TABLE notification_logs ALTER COLUMN status TYPE VARCHAR(50)");
    }
};
