<?php

namespace App\Http\Controllers\Imam;

use App\Http\Controllers\Controller;
use App\Models\Schedule;
use App\Services\AttendanceService;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request, Schedule $schedule, AttendanceService $attendanceService)
    {
        $request->validate([
            'proof_photo' => 'required|image|max:5120',
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
        ]);

        if ($schedule->user_id !== auth()->id()) {
            abort(403);
        }

        if ($schedule->attendance) {
            return back()->with('error', 'Anda sudah melakukan absensi untuk jadwal ini.');
        }

        $path = $request->file('proof_photo')->store('attendances', 'public');

        try {
            $attendance = $attendanceService->processCheckIn(
                $schedule,
                (float) $request->latitude,
                (float) $request->longitude,
                $path
            );

            $message = 'Absensi berhasil dicatat!';
            if (!$attendance->is_within_radius) {
                $message .= ' ⚠️ Lokasi Anda di luar radius masjid (' . $attendance->distance_meters . 'm). Menunggu verifikasi admin.';
            }
            if (!$attendance->is_within_time_window) {
                $message .= ' ⚠️ Absensi di luar jendela waktu yang ditentukan.';
            }

            return back()->with('success', $message);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
