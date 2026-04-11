<?php

namespace App\Console\Commands;

use App\Models\PrayerTime;
use App\Models\Schedule;
use App\Models\User;
use App\Services\ScheduleService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessAutoAssignFallback extends Command
{
    protected $signature = 'schedule:auto-assign-fallback';
    protected $description = 'Auto-assign imams to emergency empty slots after the broadcast delay period has passed.';

    public function handle(ScheduleService $scheduleService): int
    {
        if (!config('imamku.enable_auto_assignment', true)) {
            return self::SUCCESS;
        }

        $delayMinutes = config('imamku.auto_assign_delay_minutes', 15);

        // Find schedules that were emptied by swap expiry and broadcast has been sent
        $emptySlots = Schedule::whereNull('user_id')
            ->where('notes', 'LIKE', 'EMERGENCY_BROADCAST|%')
            ->where('date', '>=', now()->toDateString())
            ->get();

        foreach ($emptySlots as $schedule) {
            // Parse the broadcast timestamp from notes
            $parts = explode('|', $schedule->notes);
            if (count($parts) < 2) continue;

            $broadcastTime = Carbon::parse($parts[1]);
            $minutesSinceBroadcast = $broadcastTime->diffInMinutes(now());

            // Only auto-assign after the delay period
            if ($minutesSinceBroadcast < $delayMinutes) continue;

            // Find the best available imam
            $bestImam = $scheduleService->findBestAvailableImam($schedule);

            if (!$bestImam) {
                $this->warn("No available imam found for schedule #{$schedule->id}");
                continue;
            }

            try {
                DB::transaction(function () use ($schedule, $bestImam) {
                    $schedule = Schedule::lockForUpdate()->find($schedule->id);

                    // Double-check still empty (someone might have taken it manually)
                    if ($schedule->user_id !== null) return;

                    $schedule->update([
                        'user_id' => $bestImam->id,
                        'notes' => 'Auto-assigned setelah swap expired',
                    ]);
                });

                // Notify the auto-assigned imam
                $prayerName = $schedule->prayerType->name ?? 'Sholat';
                $dateStr = $schedule->date->translatedFormat('l, d F Y');

                $imamMessage = "Assalamu'alaikum {$bestImam->name},\n\n"
                    . "Anda telah ditugaskan otomatis sebagai imam {$prayerName} pada {$dateStr}.\n"
                    . "Slot ini kosong karena imam sebelumnya tidak dapat hadir.\n\n"
                    . "Jazakallahu khairan.\n"
                    . "— ImamKu System";

                $bestImam->notify(new \App\Notifications\BroadcastMessage($imamMessage, ['database']));

                // Notify admins
                $admins = User::where('role', 'admin')->get();
                $adminMessage = "✅ AUTO-ASSIGN BERHASIL\n\n"
                    . "Jadwal {$prayerName} pada {$dateStr} telah diisi otomatis oleh {$bestImam->name}.\n\n"
                    . "— ImamKu System";

                Notification::send($admins, new \App\Notifications\BroadcastMessage($adminMessage, ['database']));

                $this->info("Auto-assigned {$bestImam->name} to schedule #{$schedule->id}");

            } catch (\Exception $e) {
                Log::error('Auto-assign failed', [
                    'schedule_id' => $schedule->id,
                    'imam_id' => $bestImam->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }

        return self::SUCCESS;
    }
}
