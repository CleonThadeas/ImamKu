<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\StoreAttendanceRequest;
use App\Http\Traits\ApiResponse;
use App\Models\Attendance;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;

class AttendanceController extends Controller
{
    use ApiResponse;

    /**
     * POST /api/schedules/{schedule}/attendance
     * Check-in absensi imam dengan foto.
     */
    public function store(StoreAttendanceRequest $request, Schedule $schedule): JsonResponse
    {
        $user = $request->user();

        // 1. Pastikan jadwal milik imam yang login
        if ($schedule->user_id !== $user->id) {
            return $this->error('Jadwal ini bukan milik Anda.', 403);
        }

        // 2. Cegah double absensi
        if ($schedule->attendance) {
            return $this->error('Anda sudah melakukan absensi untuk jadwal ini.', 409);
        }

        // 3. Validasi waktu check-in (±30 menit dari waktu sholat)
        $pt = $schedule->prayerTime;
        if (! $pt || ! $pt->effective_time) {
            return $this->error('Waktu sholat belum tersedia untuk jadwal ini.', 422);
        }

        $prayerDt = Carbon::parse($schedule->date->toDateString() . ' ' . $pt->effective_time);
        $diffMins = now()->diffInMinutes($prayerDt, false);

        if ($diffMins > 30 || $diffMins < -30) {
            return $this->error('Absensi hanya bisa dilakukan 30 menit sebelum hingga 30 menit setelah waktu sholat.', 422);
        }

        // 4. Upload foto
        $path = $request->file('proof_photo')->store('attendances', 'public');

        // 5. Simpan absensi melalui service sentral (termasuk validasi radius & penalti)
        try {
            $attendance = app(\App\Services\AttendanceService::class)->processCheckIn(
                $schedule,
                (float) $request->latitude,
                (float) $request->longitude,
                $path
            );
        } catch (\Exception $e) {
            // Tangkap exception jika gagal validasi GPS / larangan lainnya
            return $this->error($e->getMessage(), 422);
        }

        // 6. Auto-approve jika setting aktif
        $feeConfig = \App\Models\FeeConfig::where('season_id', $schedule->season_id)->first();
        if ($feeConfig && $feeConfig->is_auto_approve_attendance) {
            $attendance->update(['status' => 'approved']);
        }

        return $this->success([
            'id'         => $attendance->id,
            'status'     => $attendance->status,
            'proof_url'  => asset('storage/' . $attendance->proof_path),
            'created_at' => $attendance->created_at->toIso8601String(),
        ], 'Absensi berhasil dicatat', 201);
    }
}
