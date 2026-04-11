<?php

namespace App\Console\Commands;

use App\Models\PrayerTime;
use App\Models\SwapRequest;
use App\Models\User;
use App\Services\NotificationService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class ProcessExpiredSwaps extends Command
{
    protected $signature = 'schedule:process-expired-swaps';
    protected $description = 'Auto-expire pending swap requests when within cutoff time before prayer, vacate the slot, and send emergency broadcasts.';

    public function handle(): int
    {
        $cutoffMinutes = config('imamku.swap_expiry_minutes_before', 60);
        $processed = 0;

        $pendingSwaps = SwapRequest::with(['schedule.prayerType', 'schedule.season', 'requester'])
            ->where('status', 'pending')
            ->get();

        foreach ($pendingSwaps as $swap) {
            $schedule = $swap->schedule;
            if (!$schedule) continue;

            // Get prayer time for this schedule
            $prayerTime = PrayerTime::where('season_id', $schedule->season_id)
                ->where('date', $schedule->date->toDateString())
                ->where('prayer_type_id', $schedule->prayer_type_id)
                ->first();

            if (!$prayerTime || !$prayerTime->effective_time) continue;

            $prayerDateTime = Carbon::parse($schedule->date->toDateString() . ' ' . $prayerTime->effective_time);
            $minutesUntilPrayer = now()->diffInMinutes($prayerDateTime, false);

            // If within cutoff window (e.g., <= 60 minutes before prayer)
            if ($minutesUntilPrayer <= $cutoffMinutes && $minutesUntilPrayer > -30) {
                DB::transaction(function () use ($swap, $schedule) {
                    // Lock the swap to prevent race condition with accept
                    $swap = SwapRequest::lockForUpdate()->find($swap->id);
                    if ($swap->status !== 'pending') return;

                    // 1. Expire the swap
                    $swap->update([
                        'status' => 'expired',
                        'processed_at' => now(),
                    ]);

                    // 2. Vacate the schedule slot
                    $schedule->update([
                        'user_id' => null,
                        'notes' => 'Dikosongkan otomatis: swap expired',
                    ]);

                    // 3. Mark for emergency broadcast (stored in notes for fallback command)
                    $schedule->update([
                        'notes' => 'EMERGENCY_BROADCAST|' . now()->toIso8601String(),
                    ]);
                });

                // 3.5 Record swap expired penalty
                try {
                    app(\App\Services\PenaltyService::class)->recordSwapExpired($swap);
                } catch (\Exception $e) {
                    Log::error('Failed to record swap expired penalty', ['error' => $e->getMessage()]);
                }

                // 4. Notify the requester
                try {
                    $notifService = app(NotificationService::class);
                    $prayerName = $schedule->prayerType->name ?? 'Sholat';
                    $dateStr = $schedule->date->translatedFormat('l, d F Y');

                    $requesterMessage = "Assalamu'alaikum {$swap->requester->name},\n\n"
                        . "Permintaan swap Anda untuk {$prayerName} pada {$dateStr} telah EXPIRED karena tidak ada imam yang menerima.\n"
                        . "Jadwal Anda telah dikosongkan.\n\n"
                        . "— ImamKu System";

                    $swap->requester->notify(new \App\Notifications\BroadcastMessage($requesterMessage, ['database']));
                } catch (\Exception $e) {
                    Log::error('Failed to notify swap requester', ['error' => $e->getMessage()]);
                }

                // 5. Notify all admins
                try {
                    $admins = User::where('role', 'admin')->get();
                    $prayerName = $schedule->prayerType->name ?? 'Sholat';
                    $dateStr = $schedule->date->translatedFormat('l, d F Y');

                    $adminMessage = "⚠️ SLOT KOSONG DARURAT\n\n"
                        . "Jadwal {$prayerName} pada {$dateStr} telah dikosongkan karena swap expired.\n"
                        . "Imam: {$swap->requester->name}\n"
                        . "Segera lakukan penugasan ulang.\n\n"
                        . "— ImamKu System";

                    foreach ($admins as $admin) {
                        \App\Models\NotificationLog::create([
                            'user_id' => $admin->id,
                            'channel' => 'system_log',
                            'type' => 'emergency_empty_slot',
                            'payload' => ['message' => $adminMessage],
                            'status' => 'logged',
                            'sent_at' => now(),
                        ]);
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to notify admins about expired swap', ['error' => $e->getMessage()]);
                }

                // 6. Emergency broadcast to all active imams
                try {
                    $prayerName = $schedule->prayerType->name ?? 'Sholat';
                    $dateStr = $schedule->date->translatedFormat('l, d F Y');

                    $imams = User::where('role', 'imam')
                        ->where('is_active', true)
                        ->where('id', '!=', $swap->requester_id)
                        ->get();

                    $emergencyMessage = "🚨 SLOT DARURAT TERSEDIA\n\n"
                        . "Jadwal {$prayerName} pada {$dateStr} membutuhkan imam pengganti.\n"
                        . "Segera hubungi admin atau login ke ImamKu untuk mengambil slot ini.\n\n"
                        . "— ImamKu System";

                    Notification::send($imams, new \App\Notifications\BroadcastMessage($emergencyMessage, ['database']));
                } catch (\Exception $e) {
                    Log::error('Failed to send emergency broadcast', ['error' => $e->getMessage()]);
                }

                $processed++;
                $this->info("Swap #{$swap->id} expired — slot vacated, emergency broadcast sent.");
            }
        }

        if ($processed > 0) {
            $this->info("Processed {$processed} expired swaps.");
        }

        return self::SUCCESS;
    }
}
