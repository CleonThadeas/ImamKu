<?php

namespace App\Http\Controllers\Imam;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Schedule;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function store(Request $request, Schedule $schedule)
    {
        $request->validate([
            'proof_photo' => 'required|image|max:5120',
        ]);

        if ($schedule->user_id !== auth()->id()) {
            abort(403);
        }

        if ($schedule->attendance) {
            return back()->with('error', 'Anda sudah melakukan absensi untuk jadwal ini.');
        }

        $path = $request->file('proof_photo')->store('attendances', 'public');

        Attendance::create([
            'schedule_id' => $schedule->id,
            'proof_path' => $path,
            'status' => 'pending',
            'notes' => 'Hadir'
        ]);

        return back()->with('success', 'Absensi berhasil! Bukti kehadiran Anda menunggu verifikasi admin.');
    }
}
