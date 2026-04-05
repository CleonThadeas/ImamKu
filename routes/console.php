<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Send prayer reminders every minute
Schedule::command('schedule:send-reminders')->everyMinute();

// Process auto-approve attendances and grant fees 30 mins after prayer ends
Schedule::command('schedule:process-attendances')->everyMinute();
