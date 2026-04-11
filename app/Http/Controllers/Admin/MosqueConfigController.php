<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MosqueConfig;
use App\Models\RamadanSeason;
use Illuminate\Http\Request;

class MosqueConfigController extends Controller
{
    public function index()
    {
        $season = RamadanSeason::where('is_active', true)->first();
        $config = $season ? MosqueConfig::where('season_id', $season->id)->first() : null;
        $bounds = MosqueConfig::radiusBounds();

        return view('admin.mosque-config.index', compact('season', 'config', 'bounds'));
    }

    public function store(Request $request)
    {
        $bounds = MosqueConfig::radiusBounds();

        $request->validate([
            'season_id' => 'required|exists:ramadan_seasons,id',
            'name' => 'required|string|max:255',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'radius_meters' => "required|integer|min:{$bounds['min']}|max:{$bounds['max']}",
            'attendance_window_minutes' => 'required|integer|min:10|max:120',
            'attendance_window_after_minutes' => 'required|integer|min:10|max:120',
        ]);

        MosqueConfig::updateOrCreate(
            ['season_id' => $request->season_id],
            $request->only(['name', 'latitude', 'longitude', 'radius_meters', 'attendance_window_minutes', 'attendance_window_after_minutes'])
        );

        return redirect()->route('admin.mosque-config.index')
            ->with('success', 'Konfigurasi lokasi masjid berhasil disimpan.');
    }
}
