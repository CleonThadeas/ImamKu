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
        $prayerTypes = collect();
        $schedules = collect();

        if ($season) {
            $schedules = $this->scheduleService->getSeasonSchedules($season->id);
            
            // Get unique prayer types that actually have schedule slots in this season
            $activeTypeIds = [];
            foreach ($schedules as $dateSchedules) {
                foreach ($dateSchedules as $schedule) {
                    $activeTypeIds[$schedule->prayer_type_id] = true;
                }
            }

            if (!empty($activeTypeIds)) {
                $prayerTypes = PrayerType::whereIn('id', array_keys($activeTypeIds))
                    ->orderBy('sort_order')->get();
            }
        }

        return view('imam.schedules.index', compact('season', 'schedules', 'prayerTypes', 'user'));
    }
}
