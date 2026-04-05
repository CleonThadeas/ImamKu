<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrayerTime;
use App\Models\RamadanSeason;
use App\Services\PrayerTimeService;
use Illuminate\Http\Request;

class PrayerTimeController extends Controller
{
    protected PrayerTimeService $prayerTimeService;

    public function __construct(PrayerTimeService $prayerTimeService)
    {
        $this->prayerTimeService = $prayerTimeService;
    }

    public function index(Request $request)
    {
        $season = RamadanSeason::where('is_active', true)->first();
        $selectedDate = $request->input('date', now()->toDateString());

        $prayerTimes = collect();
        if ($season) {
            $prayerTimes = $this->prayerTimeService->getTimesForDate($season->id, $selectedDate);
        }

        return view('admin.prayer-times.index', compact('season', 'selectedDate', 'prayerTimes'));
    }

    public function syncFromApi(Request $request)
    {
        $request->validate(['season_id' => 'required|exists:ramadan_seasons,id']);

        try {
            $count = $this->prayerTimeService->syncSeasonTimes($request->season_id);
            return redirect()->route('admin.prayer-times.index')
                ->with('success', "{$count} waktu sholat berhasil disinkronkan dari API.");
        } catch (\Exception $e) {
            return redirect()->route('admin.prayer-times.index')
                ->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    public function override(Request $request, PrayerTime $prayerTime)
    {
        $request->validate([
            'override_time' => 'nullable|date_format:H:i',
        ]);

        $this->prayerTimeService->overrideTime($prayerTime->id, $request->override_time);

        return redirect()->back()
            ->with('success', 'Waktu sholat berhasil diperbarui.');
    }
}
