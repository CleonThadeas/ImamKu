<?php

namespace App\Console\Commands;

use App\Models\PrayerTime;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Services\PenaltyService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessNoShowPenalties extends Command
{
    protected $signature = 'schedule:process-no-show-penalties';
    protected $description = 'Apply no-show penalties for schedules that are past prayer time with no attendance.';

    public function handle(PenaltyService $penaltyService): int
    {
        $season = RamadanSeason::where('is_active', true)->first();
        if (!$season) return self::SUCCESS;

        $processed = 0;

        // Get all assigned schedules for today and yesterday that have no attendance
        $schedules = Schedule::with(['prayerType', 'attendance'])
            ->where('season_id', $season->id)
            ->whereNotNull('user_id')
            ->whereDoesntHave('attendance')
            ->where('date', '<=', now()->toDateString())
            ->get();

        foreach ($schedules as $schedule) {
            /** @var \App\Models\Schedule $schedule */
            $prayerTime = PrayerTime::where('season_id', $schedule->season_id)
                ->where('date', $schedule->date->toDateString())
                ->where('prayer_type_id', $schedule->prayer_type_id)
                ->first();

            if (!$prayerTime || !$prayerTime->effective_time) continue;

            $prayerDateTime = Carbon::parse($schedule->date->toDateString() . ' ' . $prayerTime->effective_time);

            // Only penalize if prayer time was more than 30 minutes ago
            if (now()->diffInMinutes($prayerDateTime, false) < -30) {
                $penaltyService->recordNoShow($schedule);
                $processed++;
            }
        }

        if ($processed > 0) {
            $this->info("Applied {$processed} no-show penalties.");
        }

        return self::SUCCESS;
    }
}
