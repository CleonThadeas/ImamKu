<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

$season = App\Models\RamadanSeason::where('is_active', true)->first();
$today = now()->toDateString();
$tomorrow = now()->addDay()->toDateString();

$schedules = App\Models\Schedule::with(['prayerType', 'user'])
    ->where('season_id', $season->id)
    ->whereIn('date', [$today, $tomorrow])
    ->whereNotNull('user_id')
    ->get();

echo "Now: " . now()->toDateTimeString() . "\n";
echo "Found {$schedules->count()} schedules for $today and $tomorrow.\n\n";

foreach ($schedules as $schedule) {
    if(!$schedule->date) continue;
    $prayerTime = App\Models\PrayerTime::where('season_id', $season->id)
        ->where('date', $schedule->date->toDateString())
        ->where('prayer_type_id', $schedule->prayer_type_id)
        ->first();
        
    if (!$prayerTime || !$prayerTime->effective_time) continue;

    $prayerDateTime = \Carbon\Carbon::parse($schedule->date->toDateString() . ' ' . $prayerTime->effective_time);
    $reminderTime = $prayerDateTime->copy()->subMinutes(5);
    $now = now();
    
    $diffMinutes = $now->diffInMinutes($reminderTime, false);
    
    echo "Schedule ID {$schedule->id} ({$schedule->prayerType->name})\n";
    echo "- Scheduled Date: {$schedule->date->toDateString()}\n";
    echo "- PrayerTime: {$prayerDateTime->toDateTimeString()}\n";
    echo "- Reminder(5m): {$reminderTime->toDateTimeString()}\n";
    echo "- Is Within Window: " . ($now->between($reminderTime->copy()->subMinutes(2), $reminderTime->copy()->addMinutes(2)) ? 'YES' : 'NO') . "\n\n";
}
