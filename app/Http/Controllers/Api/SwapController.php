<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreSwapRequest;
use App\Http\Requests\Api\RespondSwapRequest;
use App\Http\Resources\Api\ScheduleResource;
use App\Http\Resources\Api\SwapResource;
use App\Http\Traits\ApiResponse;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Models\SwapRequest;
use App\Services\SwapService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;

class SwapController extends Controller
{
    use ApiResponse;

    public function __construct(private SwapService $swapService) {}

    /**
     * GET /api/swaps
     * List: broadcast swap tersedia + riwayat swap saya.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $season = RamadanSeason::where('is_active', true)->first();

        // Broadcast swap tersedia untuk diambil
        $availableSwaps = SwapRequest::with(['schedule.prayerType', 'schedule.user', 'requester'])
            ->where('status', 'pending')
            ->where('requester_id', '!=', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'available_page');

        // Riwayat swap saya sendiri
        $mySwaps = SwapRequest::with(['schedule.prayerType', 'targetSchedule.prayerType', 'targetSchedule.user'])
            ->where('requester_id', $user->id)
            ->latest()
            ->paginate(10, ['*'], 'my_page');

        // Jadwal saya yang eligible untuk ditukar (≥2 jam sebelum sholat)
        $mySwappableSchedules = collect();
        if ($season) {
            $rawSchedules = Schedule::with(['prayerType', 'attendance'])
                ->where('season_id', $season->id)
                ->where('user_id', $user->id)
                ->where('date', '>=', now()->toDateString())
                ->orderBy('date')
                ->get();

            $mySwappableSchedules = $rawSchedules->filter(function ($s) {
                if ($s->attendance) return false;
                $pt = $s->prayerTime;
                if (! $pt || ! $pt->effective_time) return false;
                $prayerDt = Carbon::parse($s->date->toDateString() . ' ' . $pt->effective_time);
                return now()->diffInMinutes($prayerDt, false) >= 120;
            })->values();
        }

        return $this->success([
            'available_swaps'     => SwapResource::collection($availableSwaps),
            'my_swaps'            => SwapResource::collection($mySwaps),
            'my_swappable_schedules' => ScheduleResource::collection($mySwappableSchedules),
        ], 'Data swap berhasil diambil');
    }

    /**
     * POST /api/swaps
     * Buat permintaan swap baru (broadcast).
     */
    public function store(StoreSwapRequest $request): JsonResponse
    {
        try {
            $swap = $this->swapService->requestSwap(
                $request->schedule_id,
                $request->user()->id
            );

            // Kirim notifikasi ke semua imam lain
            $imams = \App\Models\User::where('role', 'imam')
                ->where('is_active', true)
                ->where('id', '!=', $request->user()->id)
                ->get();
            Notification::send($imams, new \App\Notifications\SwapRequested($swap));

            return $this->success(
                new SwapResource($swap->load(['schedule.prayerType', 'requester'])),
                'Permintaan swap berhasil disiarkan',
                201
            );
        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    /**
     * POST /api/swaps/{swap}/respond
     * Accept atau cancel swap.
     */
    public function respond(RespondSwapRequest $request, SwapRequest $swap): JsonResponse
    {
        try {
            if ($request->action === 'accept') {
                $updatedSwap = $this->swapService->acceptSwap(
                    $swap->id,
                    $request->target_schedule_id,
                    $request->user()->id
                );

                // Notifikasi ke kedua pihak
                $updatedSwap->requester->notify(new \App\Notifications\SwapAccepted($updatedSwap, false));
                $request->user()->notify(new \App\Notifications\SwapAccepted($updatedSwap, true));

                return $this->success(
                    new SwapResource($updatedSwap->load(['schedule.prayerType', 'targetSchedule.prayerType'])),
                    'Swap berhasil diterima, jadwal telah ditukar'
                );
            }

            // Cancel — hanya pembuat yang bisa cancel
            if ($swap->requester_id !== $request->user()->id) {
                return $this->error('Hanya pembuat request yang bisa membatalkan.', 403);
            }

            $this->swapService->rejectSwap($swap->id);

            return $this->success(null, 'Swap berhasil dibatalkan');

        } catch (\Exception $e) {
            return $this->error($e->getMessage(), 422);
        }
    }
}
