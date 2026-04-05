<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Traits\ApiResponse;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Services\FeeService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    use ApiResponse;

    public function __construct(private FeeService $feeService) {}

    /**
     * GET /api/fees
     * Laporan pendapatan imam (fee).
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $season = RamadanSeason::where('is_active', true)->first();

        if (! $season) {
            return $this->success([
                'season'    => null,
                'total_fee' => 0,
                'mode'      => null,
                'schedules' => [],
            ], 'Belum ada season Ramadan aktif');
        }

        // Summary dari FeeService
        $report = $this->feeService->getImamFeeReport($season->id, $user->id);

        // Detail paginated — jadwal yang sudah approved
        $schedules = Schedule::with(['prayerType', 'attendance'])
            ->where('season_id', $season->id)
            ->where('user_id', $user->id)
            ->whereHas('attendance', fn($q) => $q->where('status', 'approved'))
            ->orderBy('date', 'desc')
            ->paginate(15);

        // Tambahkan kalkulasi fee per item
        $items = $schedules->getCollection()->map(function ($schedule) {
            return [
                'id'           => $schedule->id,
                'date'         => $schedule->date->format('Y-m-d'),
                'date_formatted' => $schedule->date->translatedFormat('d M Y'),
                'prayer_type'  => $schedule->prayerType?->name,
                'status'       => $schedule->attendance?->status,
                'fee_amount'   => $this->feeService->calculateScheduleFee($schedule),
            ];
        });

        return $this->success([
            'season' => [
                'id'   => $season->id,
                'name' => $season->name,
            ],
            'total_fee' => $report['total'] ?? 0,
            'mode'      => $report['mode'] ?? null,
            'schedules' => $items,
            'pagination' => [
                'current_page' => $schedules->currentPage(),
                'last_page'    => $schedules->lastPage(),
                'per_page'     => $schedules->perPage(),
                'total'        => $schedules->total(),
            ],
        ], 'Laporan fee berhasil diambil');
    }
}
