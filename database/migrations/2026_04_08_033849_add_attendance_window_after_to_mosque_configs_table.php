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
        Schema::table('mosque_configs', function (Blueprint $table) {
            $table->integer('attendance_window_after_minutes')->default(30)->after('attendance_window_minutes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('mosque_configs', function (Blueprint $table) {
            $table->dropColumn('attendance_window_after_minutes');
        });
    }
};
