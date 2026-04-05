<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ScheduleResource;
use App\Http\Traits\ApiResponse;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Models\SwapRequest;
use App\Services\FeeService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/dashboard
     * Data dashboard imam: season, jadwal mendatang, pending swaps, total fee.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $season = RamadanSeason::where('is_active', true)->first();

        if (! $season) {
            return $this->success([
                'season'           => null,
                'upcoming_schedules' => [],
                'pending_swaps_count' => 0,
                'total_fee'        => 0,
            ], 'Belum ada season Ramadan aktif');
        }

        // Jadwal mendatang (hari ini ke depan), sorted by newest assignment
        $schedules = Schedule::with(['prayerType', 'attendance', 'user'])
            ->where('season_id', $season->id)
            ->where('user_id', $user->id)
            ->where('date', '>=', now()->toDateString())
            ->orderBy('created_at', 'desc')
            ->limit(10)
            ->get();

        // Hitung kemampuan check-in dan swap per jadwal
        $schedules->each(function ($schedule) {
            $schedule->can_check_in = false;
            $schedule->can_swap = false;

            if (! $schedule->attendance && $schedule->prayerTime?->effective_time) {
                $prayerDt = Carbon::parse($schedule->date->toDateString() . ' ' . $schedule->prayerTime->effective_time);
                $diffMins = now()->diffInMinutes($prayerDt, false);
                if ($diffMins <= 30 && $diffMins >= -30) $schedule->can_check_in = true;
                if ($diffMins >= 120) $schedule->can_swap = true;
            }
        });

        // Pending swap count
        $pendingSwaps = SwapRequest::where('status', 'pending')
            ->where('requester_id', '!=', $user->id)
            ->count();

        // Total Fee
        $feeService = app(FeeService::class);
        $report = $feeService->getImamFeeReport($season->id, $user->id);

        return $this->success([
            'season' => [
                'id'   => $season->id,
                'name' => $season->name,
            ],
            'upcoming_schedules'  => ScheduleResource::collection($schedules),
            'pending_swaps_count' => $pendingSwaps,
            'total_fee'           => $report['total'] ?? 0,
        ], 'Data dashboard berhasil diambil');
    }
}
