<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\ScheduleResource;
use App\Http\Traits\ApiResponse;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    use ApiResponse;

    /**
     * GET /api/schedules
     * Jadwal imam. Query params: ?filter=upcoming|past|all (default: all)
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $season = RamadanSeason::where('is_active', true)->first();

        if (! $season) {
            return $this->success([], 'Belum ada season Ramadan aktif');
        }

        $query = Schedule::with(['prayerType', 'attendance', 'user'])
            ->where('season_id', $season->id)
            ->where('user_id', $user->id);

        // Filter: upcoming / past / all
        $filter = $request->query('filter', 'all');
        if ($filter === 'upcoming') {
            $query->where('date', '>=', now()->toDateString());
        } elseif ($filter === 'past') {
            $query->where('date', '<', now()->toDateString());
        }

        $schedules = $query->orderBy('date', 'desc')->paginate(15);

        // Hitung flags check-in & swap
        $schedules->getCollection()->transform(function ($schedule) {
            $schedule->can_check_in = false;
            $schedule->can_swap = false;

            if (! $schedule->attendance && $schedule->prayerTime?->effective_time) {
                $prayerDt = Carbon::parse($schedule->date->toDateString() . ' ' . $schedule->prayerTime->effective_time);
                $diffMins = now()->diffInMinutes($prayerDt, false);
                if ($diffMins <= 30 && $diffMins >= -30) $schedule->can_check_in = true;
                if ($diffMins >= 120) $schedule->can_swap = true;
            }

            return $schedule;
        });

        return $this->success([
            'schedules' => ScheduleResource::collection($schedules),
            'pagination' => [
                'current_page' => $schedules->currentPage(),
                'last_page'    => $schedules->lastPage(),
                'per_page'     => $schedules->perPage(),
                'total'        => $schedules->total(),
            ],
        ], 'Jadwal berhasil diambil');
    }

    /**
     * GET /api/schedules/grid
     * Mengambil seluruh grid jadwal (semua imam)
     */
    public function grid(Request $request): JsonResponse
    {
        $season = RamadanSeason::where('is_active', true)->first();
        if (! $season) {
            return $this->success(['schedules' => [], 'prayer_types' => []], 'Belum ada season Ramadan aktif');
        }

        $schedules = app(\App\Services\ScheduleService::class)->getSeasonSchedules($season->id);
        $prayerTypes = \App\Models\PrayerType::orderBy('sort_order')->get();

        // Convert the Collection of grouped schedules to Array to keep JSON structure consistent
        $grid = [];
        foreach ($schedules as $date => $dailySchedules) {
            $grid[$date] = ScheduleResource::collection($dailySchedules);
        }

        return $this->success([
            'schedules' => $grid,
            'prayer_types' => $prayerTypes,
        ], 'Grid jadwal berhasil diambil');
    }

    /**
     * GET /api/schedules/{id}
     * Detail satu jadwal.
     */
    public function show(Request $request, Schedule $schedule): JsonResponse
    {
        // Pastikan jadwal milik imam yang login
        if ($schedule->user_id !== $request->user()->id) {
            return $this->error('Jadwal ini bukan milik Anda.', 403);
        }

        $schedule->load(['prayerType', 'attendance', 'user']);

        // Hitung flags
        $schedule->can_check_in = false;
        $schedule->can_swap = false;

        if (! $schedule->attendance && $schedule->prayerTime?->effective_time) {
            $prayerDt = Carbon::parse($schedule->date->toDateString() . ' ' . $schedule->prayerTime->effective_time);
            $diffMins = now()->diffInMinutes($prayerDt, false);
            if ($diffMins <= 30 && $diffMins >= -30) $schedule->can_check_in = true;
            if ($diffMins >= 120) $schedule->can_swap = true;
        }

        return $this->success(
            new ScheduleResource($schedule),
            'Detail jadwal berhasil diambil'
        );
    }
}
