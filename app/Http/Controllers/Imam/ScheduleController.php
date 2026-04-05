<?php

namespace App\Http\Controllers\Imam;

use App\Http\Controllers\Controller;
use App\Models\PrayerType;
use App\Models\RamadanSeason;
use App\Services\ScheduleService;
use Illuminate\Http\Request;

class ScheduleController extends Controller
{
    protected ScheduleService $scheduleService;

    public function __construct(ScheduleService $scheduleService)
    {
        $this->scheduleService = $scheduleService;
    }

    public function index(Request $request)
    {
        $user = auth()->user();
        $season = RamadanSeason::where('is_active', true)->first();
        $prayerTypes = PrayerType::orderBy('sort_order')->get();
        $schedules = collect();

        if ($season) {
            $schedules = $this->scheduleService->getSeasonSchedules($season->id);
        }

        return view('imam.schedules.index', compact('season', 'schedules', 'prayerTypes', 'user'));
    }
}
