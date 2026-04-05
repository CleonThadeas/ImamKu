<?php

namespace App\Http\Controllers\Imam;

use App\Http\Controllers\Controller;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Models\SwapRequest;
use App\Services\FeeService;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $season = RamadanSeason::where('is_active', true)->first();

        $mySchedules = collect();
        $pendingSwaps = collect();
        $totalFee = 0;

        if ($season) {
            $mySchedules = Schedule::with(['prayerType', 'attendance'])
                ->where('season_id', $season->id)
                ->where('user_id', $user->id)
                ->where('date', '>=', now()->toDateString())
                ->join('prayer_types', 'schedules.prayer_type_id', '=', 'prayer_types.id')
                ->orderBy('schedules.created_at', 'desc')
                ->select('schedules.*')
                ->limit(10)
                ->get();

            $pendingSwaps = SwapRequest::with(['schedule.prayerType', 'targetSchedule.prayerType', 'requester'])
                ->whereHas('targetSchedule', fn ($qs) => $qs->where('user_id', $user->id))
                ->where('status', 'pending')
                ->latest()
                ->get();

            $feeReport = app(FeeService::class)->getImamFeeReport($season->id, $user->id);
            if (isset($feeReport['mode']) && $feeReport['mode'] === 'per_day') {
                $totalFee = $feeReport['total'];
            } else {
                $totalFee = collect($feeReport['details'] ?? [])->sum('subtotal') ?: $feeReport['total'];
            }
        }

        return view('imam.dashboard', compact('season', 'mySchedules', 'pendingSwaps', 'totalFee'));
    }
}
