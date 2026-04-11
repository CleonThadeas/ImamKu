<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PrayerTime;
use App\Models\PrayerType;
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
        $prayerTypes = PrayerType::orderBy('sort_order')->get();
        $apiTimesForDate = [];
        $existingTypeIds = [];

        if ($season) {
            $prayerTimes = $this->prayerTimeService->getTimesForDate($season->id, $selectedDate);

            // Build a map of prayer_type_id => api_time for the selected date
            $existingTimes = PrayerTime::where('season_id', $season->id)
                ->where('date', $selectedDate)
                ->get();

            foreach ($existingTimes as $pt) {
                $existingTypeIds[] = $pt->prayer_type_id;
                if ($pt->api_time) {
                    $apiTimesForDate[$pt->prayer_type_id] = $pt->api_time;
                }
            }
        }

        // Group prayer types for the modal dropdown
        $defaultTypes = $prayerTypes->where('is_default', true);
        $specialTypes = $prayerTypes->where('is_default', false);

        return view('admin.prayer-times.index', compact(
            'season', 'selectedDate', 'prayerTimes', 'prayerTypes',
            'apiTimesForDate', 'existingTypeIds',
            'defaultTypes', 'specialTypes'
        ));
    }

    public function syncFromApi(Request $request)
    {
        $request->validate(['season_id' => 'required|exists:ramadan_seasons,id']);

        try {
            $count = $this->prayerTimeService->syncSeasonTimes($request->season_id);
            return redirect()->route('admin.prayer-times.index')
                ->with('success', "{$count} waktu sholat berhasil disinkronkan dari API (record manual dilewati).");
        } catch (\Exception $e) {
            return redirect()->route('admin.prayer-times.index')
                ->with('error', 'Gagal sinkronisasi: ' . $e->getMessage());
        }
    }

    /**
     * Store a new manual prayer time entry.
     * Supports two modes: 'api' (use existing API time) or 'custom' (user-provided time).
     */
    public function store(Request $request)
    {
        $request->validate([
            'season_id' => 'required|exists:ramadan_seasons,id',
            'date' => 'required|date',
            'prayer_type_id' => 'required|exists:prayer_types,id',
            'time_source' => 'required|in:api,custom',
            'time' => 'required_if:time_source,custom|nullable|date_format:H:i',
        ], [
            'time.required_if' => 'Waktu wajib diisi jika memilih mode kustom.',
        ]);

        try {
            $time = $request->time;

            if ($request->time_source === 'api') {
                // Look up the API time for this prayer type on this date
                $existing = PrayerTime::where('season_id', $request->season_id)
                    ->where('date', $request->date)
                    ->where('prayer_type_id', $request->prayer_type_id)
                    ->first();

                if ($existing && $existing->api_time) {
                    $time = $existing->api_time;
                } else {
                    // Fetch from API on the fly
                    $dateFormatted = \Carbon\Carbon::parse($request->date)->format('d-m-Y');
                    $apiTimes = $this->prayerTimeService->fetchFromApi($dateFormatted);
                    $prayerType = PrayerType::find($request->prayer_type_id);

                    if ($prayerType && isset($apiTimes[$prayerType->name])) {
                        $time = $apiTimes[$prayerType->name];
                    } else {
                        return redirect()->back()
                            ->with('error', 'Waktu API tidak ditemukan untuk jenis sholat ini. Silakan gunakan mode kustom.');
                    }
                }
            }

            $this->prayerTimeService->createManualTime(
                $request->season_id,
                $request->date,
                $request->prayer_type_id,
                $time
            );

            $sourceLabel = $request->time_source === 'api' ? 'dari API' : 'kustom';
            return redirect()->back()
                ->with('success', "Waktu sholat ({$sourceLabel}: {$time}) berhasil ditambahkan.");
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Update/Override a prayer time.
     */
    public function override(Request $request, PrayerTime $prayerTime)
    {
        $request->validate([
            'override_time' => 'required|date_format:H:i',
        ]);

        $this->prayerTimeService->updateTime($prayerTime->id, $request->override_time);

        return redirect()->back()
            ->with('success', 'Waktu sholat berhasil diperbarui.');
    }

    /**
     * Reset an overridden prayer time back to API time.
     */
    public function resetOverride(PrayerTime $prayerTime)
    {
        try {
            $this->prayerTimeService->resetToApi($prayerTime->id);
            return redirect()->back()
                ->with('success', 'Waktu sholat berhasil direset ke waktu API.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }

    /**
     * Delete a manual prayer time entry.
     */
    public function destroy(PrayerTime $prayerTime)
    {
        try {
            $this->prayerTimeService->deleteManualTime($prayerTime->id);
            return redirect()->back()
                ->with('success', 'Waktu sholat manual berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', $e->getMessage());
        }
    }
}
