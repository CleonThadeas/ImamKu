<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\RamadanSeason;
use Illuminate\Http\Request;

class SeasonController extends Controller
{
    public function index()
    {
        $seasons = RamadanSeason::orderByDesc('hijri_year')->get();
        return view('admin.seasons.index', compact('seasons'));
    }

    public function create()
    {
        return view('admin.seasons.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hijri_year' => 'required|integer|min:1400|max:1500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($request->boolean('is_active')) {
            RamadanSeason::where('is_active', true)->update(['is_active' => false]);
        }

        RamadanSeason::create([
            'name' => $validated['name'],
            'hijri_year' => $validated['hijri_year'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.seasons.index')
            ->with('success', 'Season Ramadan berhasil ditambahkan.');
    }

    public function edit(RamadanSeason $season)
    {
        return view('admin.seasons.edit', compact('season'));
    }

    public function update(Request $request, RamadanSeason $season)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'hijri_year' => 'required|integer|min:1400|max:1500',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'is_active' => 'boolean',
        ]);

        if ($request->boolean('is_active') && !$season->is_active) {
            RamadanSeason::where('is_active', true)->update(['is_active' => false]);
        }

        $season->update([
            'name' => $validated['name'],
            'hijri_year' => $validated['hijri_year'],
            'start_date' => $validated['start_date'],
            'end_date' => $validated['end_date'],
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.seasons.index')
            ->with('success', 'Season Ramadan berhasil diperbarui.');
    }

    public function destroy(RamadanSeason $season)
    {
        $season->delete();
        return redirect()->route('admin.seasons.index')
            ->with('success', 'Season Ramadan berhasil dihapus.');
    }
}
