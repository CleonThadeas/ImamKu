<?php

namespace App\Services;

use App\Models\PrayerTime;
use App\Models\Schedule;
use App\Models\SwapRequest;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class SwapService
{
    protected ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    /**
     * Create a broadcast swap request for a schedule.
     */
    public function requestSwap(int $scheduleId, int $requesterId): SwapRequest
    {
        $schedule = Schedule::with(['prayerType', 'user'])->findOrFail($scheduleId);

        // Requester must be the imam of the schedule
        if ($schedule->user_id !== $requesterId) {
            throw new \Exception("Anda hanya bisa meminta swap untuk jadwal Anda sendiri.");
        }

        if ($schedule->user->is_restricted) {
            throw new \Exception("Akun Anda dibatasi (restricted) karena poin penalti. Anda tidak dapat melakukan swap jadwal.");
        }

        $this->validateMinimumTime($schedule);

        // Check for existing pending swap request for same schedule
        $existing = SwapRequest::where('schedule_id', $scheduleId)
            ->where('status', 'pending')
            ->exists();

        if ($existing) {
            throw new \Exception("Sudah ada permintaan swap yang pending untuk jadwal ini.");
        }

        return SwapRequest::create([
            'schedule_id' => $scheduleId,
            'target_schedule_id' => null, // Not known yet, will be filled when someone accepts
            'requester_id' => $requesterId,
            'status' => 'pending',
        ]);
    }

    /**
     * Accept a broadcast swap request by offering own schedule.
     */
    public function acceptSwap(int $swapRequestId, int $targetScheduleId, int $accepterId): SwapRequest
    {
        return DB::transaction(function () use ($swapRequestId, $targetScheduleId, $accepterId) {
            $swap = SwapRequest::with(['schedule.prayerType'])
                ->lockForUpdate()
                ->findOrFail($swapRequestId);

            if ($swap->status !== 'pending') {
                throw new \Exception("Swap request sudah diproses atau dibatalkan.");
            }

            if ($swap->requester_id === $accepterId) {
                throw new \Exception("Anda tidak bisa menerima request swap Anda sendiri.");
            }

            $targetSchedule = Schedule::with(['prayerType', 'user'])->findOrFail($targetScheduleId);

            if ($targetSchedule->user_id !== $accepterId) {
                throw new \Exception("Jadwal tawaran harus milik Anda sendiri.");
            }

            if ($targetSchedule->user->is_restricted) {
                throw new \Exception("Akun Anda dibatasi (restricted). Anda tidak dapat menerima swap jadwal.");
            }

            $this->validateMinimumTime($targetSchedule);

            $schedule = $swap->schedule;

            // Validate post-swap consecutive rules for both imams
            $this->validateSwapPostConditions($schedule, $targetSchedule);

            // Execute swap: exchange user_ids
            $tempUserId = $schedule->user_id;
            $schedule->update(['user_id' => $targetSchedule->user_id]);
            $targetSchedule->update(['user_id' => $tempUserId]);

            $swap->update([
                'target_schedule_id' => $targetScheduleId,
                'status' => 'accepted',
                'processed_at' => now(),
            ]);

            return $swap->fresh(['schedule.user', 'targetSchedule.user']);
        });
    }

    /**
     * Reject/Cancel a swap request. (Usually by the requester themselves)
     */
    public function rejectSwap(int $swapRequestId): SwapRequest
    {
        $swap = SwapRequest::findOrFail($swapRequestId);

        if ($swap->status !== 'pending') {
            throw new \Exception("Swap request sudah diproses.");
        }

        $swap->update([
            'status' => 'rejected',
            'processed_at' => now(),
        ]);

        return $swap->fresh();
    }

    /**
     * Validate that the swap doesn't violate consecutive rules for both imams after swapping.
     */
    private function validateSwapPostConditions(Schedule $schedule, Schedule $targetSchedule): void
    {
        $scheduleImam = $schedule->user_id;
        $targetImam = $targetSchedule->user_id;

        // Check if targetImam can be in schedule's slot
        if (!$this->scheduleService->validateConsecutiveRule(
            $schedule->date,
            $schedule->prayer_type_id,
            $targetImam,
            $targetSchedule->id // Exclude target schedule from rules since they won't have it anymore
        )) {
            throw new \Exception("Swap akan melanggar aturan jadwal berurutan bagi Anda (tidak berlaku untuk group yang sama).");
        }

        // Check if scheduleImam can be in targetSchedule's slot
        if (!$this->scheduleService->validateConsecutiveRule(
            $targetSchedule->date,
            $targetSchedule->prayer_type_id,
            $scheduleImam,
            $schedule->id // Exclude their original schedule
        )) {
            throw new \Exception("Swap ini akan mengakibatkan imam peminta melanggar aturan jadwal berurutan.");
        }
    }

    /**
     * Validate minimum time before prayer for swap.
     */
    private function validateMinimumTime(Schedule $schedule): void
    {
        $prayerTime = PrayerTime::where('season_id', $schedule->season_id)
            ->where('date', $schedule->date)
            ->where('prayer_type_id', $schedule->prayer_type_id)
            ->first();

        if ($prayerTime && $prayerTime->effective_time) {
            $prayerDateTime = Carbon::parse($schedule->date->toDateString() . ' ' . $prayerTime->effective_time);
            $minHours = config('imamku.swap_min_hours', 2);

            if (now()->diffInHours($prayerDateTime, false) < $minHours) {
                throw new \Exception("Swap harus dilakukan minimal {$minHours} jam sebelum waktu sholat.");
            }
        }
    }
}
