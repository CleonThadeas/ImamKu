<?php

namespace App\Console\Commands;

use App\Models\PrayerTime;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class SendPrayerReminders extends Command
{
    protected $signature = 'schedule:send-reminders';
    protected $description = 'Send prayer reminders to imams for upcoming schedules';

    public function handle(NotificationService $notificationService): int
    {
        $season = RamadanSeason::where('is_active', true)->first();
        if (!$season) {
            $this->info('No active Ramadan season found.');
            return 0;
        }

        $config = \App\Models\NotificationConfig::first();
        if (!$config) {
            $this->info('No notification active configuration found.');
            return 0;
        }

        $today = now()->toDateString();
        $tomorrow = now()->addDay()->toDateString();
        
        $reminders = [$config->reminder_1_minutes];
        if ($config->enable_reminder_2) {
            $reminders[] = $config->reminder_2_minutes;
        }

        $schedules = Schedule::with(['prayerType', 'user'])
            ->where('season_id', $season->id)
            ->whereIn('date', [$today, $tomorrow])
            ->whereNotNull('user_id')
            ->get();

        $sent = 0;
        foreach ($schedules as $schedule) {
            /** @var \App\Models\Schedule $schedule */
            $prayerTime = PrayerTime::where('season_id', $season->id)
                ->where('date', $schedule->date->toDateString())
                ->where('prayer_type_id', $schedule->prayer_type_id)
                ->first();

            if (!$prayerTime || !$prayerTime->effective_time) continue;

            $prayerDateTime = Carbon::parse($today . ' ' . $prayerTime->effective_time);

            foreach ($reminders as $minutesBefore) {
                $reminderTime = $prayerDateTime->copy()->subMinutes($minutesBefore);
                $now = now();

                // Check if we're within the reminder window (±2 minutes)
                if ($now->between($reminderTime->copy()->subMinutes(2), $reminderTime->copy()->addMinutes(2))) {
                    $notificationService->sendReminder($schedule, $minutesBefore);
                    $sent++;
                }
            }
        }

        $this->info("Sent {$sent} reminders.");
        return 0;
    }
}
