<?php

namespace App\Console\Commands;

use App\Models\Attendance;
use App\Models\FeeConfig;
use App\Models\PrayerTime;
use App\Models\RamadanSeason;
use App\Services\FeeService;
use Carbon\Carbon;
use Illuminate\Console\Command;

class ProcessAutoAttendances extends Command
{
    protected $signature = 'schedule:process-attendances';
    protected $description = 'Auto-approve pending attendances and grant fee 30 minutes after prayer time.';

    public function handle(FeeService $feeService): int
    {
        $season = RamadanSeason::where('is_active', true)->first();
        if (!$season) return 0;

        $feeConfig = FeeConfig::where('season_id', $season->id)->first();
        // If config requires manual or fee auto approve is disabled, we do nothing
        if (!$feeConfig || !$feeConfig->is_auto_approve_attendance) {
            return 0;
        }

        $pendingAttendances = Attendance::with(['schedule.prayerType'])
            ->whereHas('schedule', function ($q) use ($season) {
                $q->where('season_id', $season->id);
            })
            ->where('status', 'pending')
            ->get();

        $processed = 0;
        $now = now();
        $today = $now->toDateString();
        $tomorrow = $now->copy()->addDay()->toDateString();

        foreach ($pendingAttendances as $attendance) {
            $schedule = $attendance->schedule;
            if (!$schedule->date) continue;
            
            $prayerTime = PrayerTime::where('season_id', $season->id)
                ->where('date', $schedule->date->toDateString())
                ->where('prayer_type_id', $schedule->prayer_type_id)
                ->first();

            if (!$prayerTime || !$prayerTime->effective_time) continue;

            $prayerDt = Carbon::parse($schedule->date->toDateString() . ' ' . $prayerTime->effective_time);
            
            // Require 30 minutes to pass since the prayer started
            $triggerTime = $prayerDt->copy()->addMinutes(30);

            if ($now->greaterThanOrEqualTo($triggerTime)) {
                $attendance->update([
                    'status' => 'approved',
                    'notes' => 'Di-approve Otomatis oleh Sistem setelah 30 Menit'
                ]);

                $processed++;
            }
        }

        // --- PART 2: EXPIRY SCRIPT ---
        // Find schedules where 30 mins have passed without checkin to mark explicitly as expired
        $pastSchedules = \App\Models\Schedule::whereHas('season', fn($q) => $q->where('is_active', true))
            ->whereNotNull('user_id')
            ->whereDoesntHave('attendance')
            ->whereIn('date', [$today, $tomorrow, now()->subDay()->toDateString()])
            ->get();

        foreach ($pastSchedules as $schedule) {
            $prayerTime = PrayerTime::where('season_id', $schedule->season_id)
                ->where('date', $schedule->date->toDateString())
                ->where('prayer_type_id', $schedule->prayer_type_id)
                ->first();

            if (!$prayerTime || !$prayerTime->effective_time) continue;
            
            $prayerDt = Carbon::parse($schedule->date->toDateString() . ' ' . $prayerTime->effective_time);
            
            if ($now->greaterThan($prayerDt->copy()->addMinutes(30))) {
                Attendance::create([
                    'schedule_id' => $schedule->id,
                    'status' => 'expired',
                    'notes' => 'Waktu habis (>30 menit) & tidak ada respons.'
                ]);
            }
        }

        // --- PART 3: SWAP EXPIRY SCRIPT ---
        $pendingSwaps = \App\Models\SwapRequest::with('schedule.prayerType')->where('status', 'pending')
            ->whereHas('schedule', fn($q) => $q->where('season_id', $season->id))
            ->get();

        foreach ($pendingSwaps as $swap) {
            /** @var \App\Models\SwapRequest $swap */
            $sched = $swap->schedule;
            if (!$sched || !$sched->date) continue;
            
            $pt = PrayerTime::where('season_id', $season->id)
                ->where('date', $sched->date->toDateString())
                ->where('prayer_type_id', $sched->prayer_type_id)
                ->first();

            if (!$pt || !$pt->effective_time) continue;

            $swapPrayerDt = Carbon::parse($sched->date->toDateString() . ' ' . $pt->effective_time);
            
            // If the time has passed, the schedule slot cannot be swapped anymore
            if ($now->greaterThanOrEqualTo($swapPrayerDt)) {
                $swap->update(['status' => 'expired']);
            }
        }

        if ($processed > 0) {
            $this->info("Auto-approved {$processed} attendances.");
        }
        return 0;
    }
}
