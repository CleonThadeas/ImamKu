<?php

namespace App\Http\Controllers\Imam;

use App\Http\Controllers\Controller;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Services\FeeService;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    private FeeService $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function index()
    {
        $userId = auth()->id();
        $season = RamadanSeason::where('is_active', true)->first();

        if (!$season) {
            return view('imam.fees.index', [
                'season' => null,
                'schedules' => collect(),
                'report' => ['total' => 0, 'details' => [], 'mode' => null]
            ]);
        }

        // Get Summary Report
        $report = $this->feeService->getImamFeeReport($season->id, $userId);

        // Get Paginated detailed trace of completed schedules
        $schedules = Schedule::with(['prayerType', 'attendance'])
            ->where('season_id', $season->id)
            ->where('user_id', $userId)
            ->whereHas('attendance', function ($q) {
                $q->where('status', 'approved');
            })
            ->orderBy('date', 'desc')
            ->paginate(10);

        return view('imam.fees.index', compact('season', 'schedules', 'report', 'userId'));
    }
}
