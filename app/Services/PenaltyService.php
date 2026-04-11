<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\PenaltyLog;
use App\Models\Schedule;
use App\Models\SwapRequest;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PenaltyService
{
    /**
     * Record penalty/reward for attendance event.
     */
    public function recordAttendance(Schedule $schedule, Attendance $attendance): void
    {
        $userId = $schedule->user_id;
        if (!$userId) return;

        $eventType = $attendance->is_within_time_window ? 'attendance_ontime' : 'attendance_late';
        $points = config("imamku.penalty.{$eventType}", $eventType === 'attendance_ontime' ? 10 : -5);

        $description = $eventType === 'attendance_ontime'
            ? 'Hadir tepat waktu untuk ' . ($schedule->prayerType->name ?? 'sholat')
            : 'Terlambat untuk ' . ($schedule->prayerType->name ?? 'sholat');

        $this->createLog($userId, $schedule->id, null, $eventType, $points, $description);
    }

    /**
     * Record no-show penalty for a schedule that was missed.
     */
    public function recordNoShow(Schedule $schedule): void
    {
        $userId = $schedule->user_id;
        if (!$userId) return;

        $points = config('imamku.penalty.no_show', -20);

        $this->createLog(
            $userId,
            $schedule->id,
            null,
            'no_show',
            $points,
            'Tidak hadir untuk ' . ($schedule->prayerType->name ?? 'sholat') . ' pada ' . $schedule->date->format('d/m/Y')
        );
    }

    /**
     * Record penalty for an expired swap request.
     */
    public function recordSwapExpired(SwapRequest $swapRequest): void
    {
        $userId = $swapRequest->requester_id;
        $points = config('imamku.penalty.swap_expired', -10);

        $this->createLog(
            $userId,
            $swapRequest->schedule_id,
            $swapRequest->id,
            'swap_expired',
            $points,
            'Swap request expired tanpa penerima'
        );
    }

    /**
     * Create a penalty log entry (with duplicate prevention).
     */
    private function createLog(int $userId, ?int $scheduleId, ?int $swapRequestId, string $eventType, int $points, string $description): void
    {
        try {
            $log = PenaltyLog::firstOrCreate(
                [
                    'user_id' => $userId,
                    'schedule_id' => $scheduleId,
                    'event_type' => $eventType,
                ],
                [
                    'swap_request_id' => $swapRequestId,
                    'points' => $points,
                    'description' => $description,
                ]
            );

            if ($log->wasRecentlyCreated) {
                // Send Notification to Imam
                $user = User::find($userId);
                if ($user) {
                    $ptSign = $points > 0 ? '+' : '';
                    $title = $points > 0 ? "Poin Bertambah" : "Teguran Poin";
                    $message = "{$title}\n\nPerubahan skor Anda: {$ptSign}{$points} Poin\nKet: {$description}\n\nCek layar Poin untuk detail harian Anda.\n— ImamKu System";
                    $user->notify(new \App\Notifications\BroadcastMessage($message, ['database']));
                }
            }

            $this->recalculateUserPoints($userId);
        } catch (\Exception $e) {
            Log::error('Failed to create penalty log', [
                'user_id' => $userId,
                'event_type' => $eventType,
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Recalculate total penalty points for a user from all logs.
     */
    public function recalculateUserPoints(int $userId): void
    {
        $total = PenaltyLog::where('user_id', $userId)->sum('points');

        $threshold = config('imamku.penalty.restriction_threshold', -30);
        $isRestricted = $total <= $threshold;

        User::where('id', $userId)->update([
            'penalty_points' => $total,
            'is_restricted' => $isRestricted,
        ]);
    }

    /**
     * Admin manually lifts restriction on an imam.
     */
    public function liftRestriction(int $userId): void
    {
        User::where('id', $userId)->update(['is_restricted' => false]);
    }

    /**
     * Get penalty summary for all imams (ranking).
     */
    public function getImamRanking(): \Illuminate\Support\Collection
    {
        return User::where('role', 'imam')
            ->orderByDesc('penalty_points')
            ->get()
            ->map(function ($imam) {
                $imam->penalty_breakdown = PenaltyLog::where('user_id', $imam->id)
                    ->selectRaw('event_type, COUNT(*) as count, SUM(points) as total_points')
                    ->groupBy('event_type')
                    ->get()
                    ->keyBy('event_type');
                return $imam;
            });
    }

    /**
     * Get penalty history for a specific imam.
     */
    public function getImamHistory(int $userId): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        return PenaltyLog::with(['schedule.prayerType', 'swapRequest'])
            ->where('user_id', $userId)
            ->latest()
            ->paginate(20);
    }
}
