<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notification_configs', function (Blueprint $table) {
            $table->id();
            $table->string('channels')->default('database,whatsapp'); // comma separated
            $table->integer('reminder_1_minutes')->default(90);
            $table->boolean('enable_reminder_2')->default(false);
            $table->integer('reminder_2_minutes')->default(30);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notification_configs');
    }
};
