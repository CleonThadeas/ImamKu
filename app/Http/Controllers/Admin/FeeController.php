<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FeeConfig;
use App\Models\FeeDetail;
use App\Models\PrayerType;
use App\Models\RamadanSeason;
use App\Services\FeeService;
use Illuminate\Http\Request;

class FeeController extends Controller
{
    protected FeeService $feeService;

    public function __construct(FeeService $feeService)
    {
        $this->feeService = $feeService;
    }

    public function index()
    {
        $season = RamadanSeason::where('is_active', true)->first();
        $feeConfig = $season ? FeeConfig::where('season_id', $season->id)->first() : null;
        $prayerTypes = PrayerType::orderBy('sort_order')->get();
        $feeDetails = $feeConfig ? FeeDetail::where('fee_config_id', $feeConfig->id)->get()->keyBy('prayer_type_id') : collect();

        return view('admin.fees.index', compact('season', 'feeConfig', 'prayerTypes', 'feeDetails'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'season_id' => 'required|exists:ramadan_seasons,id',
            'mode' => 'required|in:per_schedule,per_day',
            'is_enabled' => 'boolean',
        ]);

        $feeConfig = FeeConfig::updateOrCreate(
            ['season_id' => $request->season_id],
            [
                'mode' => $request->mode,
                'is_enabled' => $request->boolean('is_enabled'),
            ]
        );

        if ($request->mode === 'per_schedule') {
            $prayerTypes = PrayerType::all();
            foreach ($prayerTypes as $pt) {
                $amount = $request->input("fee_{$pt->id}", 0);
                FeeDetail::updateOrCreate(
                    ['fee_config_id' => $feeConfig->id, 'prayer_type_id' => $pt->id],
                    ['amount' => $amount]
                );
            }
        } else {
            // Per day: store flat daily rate
            $amount = $request->input('daily_rate', 0);
            FeeDetail::updateOrCreate(
                ['fee_config_id' => $feeConfig->id, 'prayer_type_id' => null],
                ['amount' => $amount]
            );
        }

        return redirect()->route('admin.fees.index')
            ->with('success', 'Konfigurasi fee berhasil disimpan.');
    }

    public function report()
    {
        $season = RamadanSeason::where('is_active', true)->first();
        $summary = $season ? $this->feeService->getSeasonFeeSummary($season->id) : collect();

        return view('admin.fees.report', compact('season', 'summary'));
    }
}
