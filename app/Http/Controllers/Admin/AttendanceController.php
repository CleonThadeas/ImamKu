<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\FeeConfig;
use App\Models\RamadanSeason;
use App\Services\FeeService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function index()
    {
        $season = RamadanSeason::where('is_active', true)->first();
        $feeConfig = null;
        $attendances = [];

        if ($season) {
            $feeConfig = FeeConfig::where('season_id', $season->id)->first() ?? new FeeConfig();
            
            $attendances = Attendance::with(['schedule.user', 'schedule.prayerType'])
                ->whereHas('schedule', function($q) use ($season) {
                    $q->where('season_id', $season->id);
                })
                ->latest()
                ->paginate(20);
        }

        return view('admin.attendances.index', compact('season', 'attendances', 'feeConfig'));
    }

    public function approve(Attendance $attendance, FeeService $feeService)
    {
        if (!in_array($attendance->status, ['pending', 'expired'])) {
            return back()->with('error', 'Absensi ini sudah diproses sebelumnya.');
        }

        $attendance->update(['status' => 'approved']);

        return back()->with('success', 'Absensi disetujui & fee telah dicairkan ke saldo Imam (Laporan Fee).');
    }

    public function reject(Attendance $attendance, Request $request)
    {
        if (!in_array($attendance->status, ['pending', 'expired'])) {
            return back()->with('error', 'Absensi ini sudah diproses sebelumnya.');
        }

        $attendance->update([
            'status' => 'rejected',
            'notes' => $request->notes ?? 'Bukti absen ditolak admin.'
        ]);

        return back()->with('success', 'Absensi ditolak.');
    }

    public function updateConfig(Request $request)
    {
        $season = RamadanSeason::where('is_active', true)->first();
        if (!$season) return back()->with('error', 'Tidak ada season aktif.');

        $config = FeeConfig::firstOrCreate(['season_id' => $season->id], ['mode' => 'per_schedule']);
        $config->update([
            'is_auto_approve_attendance' => $request->input('is_auto_approve_attendance') == '1'
        ]);
        
        return back()->with('success', 'Skema validasi absensi berhasil diperbarui!');
    }
}
