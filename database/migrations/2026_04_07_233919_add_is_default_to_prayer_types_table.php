<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('prayer_types', function (Blueprint $table) {
            $table->boolean('is_default')->default(false); // Removed after() for PostgreSQL compatibility
            $table->string('api_key')->nullable(); // Removed after() for PostgreSQL compatibility
        });
    }

    public function down(): void
    {
        Schema::table('prayer_types', function (Blueprint $table) {
            $table->dropColumn(['is_default', 'api_key']);
        });
    }
};
