<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrayerType;
use App\Models\RamadanSeason;
use App\Models\Schedule;
use App\Models\User;
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
        $season = RamadanSeason::where('is_active', true)->first();
        $seasons = RamadanSeason::orderByDesc('hijri_year')->get();
        $selectedSeasonId = $request->input('season_id', $season?->id);

        $selectedSeason = $selectedSeasonId ? RamadanSeason::find($selectedSeasonId) : null;
        $schedules = collect();
        $prayerTypes = collect();
        $imams = User::where('role', 'imam')->where('is_active', true)->get();

        if ($selectedSeason) {
            $schedules = $this->scheduleService->getSeasonSchedules($selectedSeason->id);

            // Get unique prayer types that actually have schedule slots in this season
            $activeTypeIds = [];
            foreach ($schedules as $dateSchedules) {
                foreach ($dateSchedules as $schedule) {
                    $activeTypeIds[$schedule->prayer_type_id] = true;
                }
            }

            if (!empty($activeTypeIds)) {
                $prayerTypes = PrayerType::whereIn('id', array_keys($activeTypeIds))
                    ->orderBy('sort_order')
                    ->get();
            }
        }

        return view('admin.schedules.index', compact('seasons', 'selectedSeason', 'schedules', 'prayerTypes', 'imams'));
    }

    public function generate(Request $request)
    {
        $request->validate(['season_id' => 'required|exists:ramadan_seasons,id']);
        $count = $this->scheduleService->bulkGenerate($request->season_id);

        return redirect()->route('admin.schedules.index', ['season_id' => $request->season_id])
            ->with('success', "{$count} slot jadwal berhasil digenerate.");
    }

    public function assign(Request $request)
    {
        $request->validate([
            'schedule_id' => 'required|exists:schedules,id',
            'user_id' => 'required|exists:users,id',
        ]);

        try {
            $this->scheduleService->assignImam($request->schedule_id, $request->user_id);
            return redirect()->back()->with('success', 'Imam berhasil ditugaskan.');
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function removeAssignment(Schedule $schedule)
    {
        $this->scheduleService->removeAssignment($schedule->id);
        return redirect()->back()->with('success', 'Penugasan imam berhasil dihapus.');
    }

    public function getAvailableImams(Request $request)
    {
        $date = $request->input('date');
        $prayerTypeId = $request->input('prayer_type_id');

        if (!$date || !$prayerTypeId) {
            return response()->json([]);
        }

        $imams = $this->scheduleService->getAvailableImams($date, $prayerTypeId);
        return response()->json($imams->values());
    }
}
