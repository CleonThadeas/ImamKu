<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Kompatibilitas PostgreSQL: Menghapus check constraint lama bawaan enum
        // dan menggantinya dengan check baru yang mengizinkan channel tambahan.
        
        if (DB::connection()->getDriverName() === 'pgsql') {
            // 1. Hapus constraint lama
            DB::statement('ALTER TABLE notification_logs DROP CONSTRAINT IF EXISTS notification_logs_channel_check');
            
            // 2. Tambahkan constraint baru
            DB::statement("ALTER TABLE notification_logs ADD CONSTRAINT notification_logs_channel_check CHECK (channel::text IN ('email', 'whatsapp', 'database', 'broadcast'))");
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Kembalikan ke constraint lama (hanya email & whatsapp)
        if (DB::connection()->getDriverName() === 'pgsql') {
            DB::statement('ALTER TABLE notification_logs DROP CONSTRAINT IF EXISTS notification_logs_channel_check');
            DB::statement("ALTER TABLE notification_logs ADD CONSTRAINT notification_logs_channel_check CHECK (channel::text IN ('email', 'whatsapp'))");
        }
    }
};
