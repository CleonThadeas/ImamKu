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

// Auto-expire pending swap requests when within cutoff time
Schedule::command('schedule:process-expired-swaps')->everyMinute();

// Auto-assign fallback for emergency empty slots (DISABLED PER USER REQUEST)
// Schedule::command('schedule:auto-assign-fallback')->everyMinute();

// Process no-show penalties for past schedules without attendance
Schedule::command('schedule:process-no-show-penalties')->everyMinute();
