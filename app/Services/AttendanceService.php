<?php

namespace App\Services;

use App\Models\Attendance;
use App\Models\MosqueConfig;
use App\Models\PrayerTime;
use App\Models\Schedule;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class AttendanceService
{
    /**
     * Process a GPS-validated check-in for an imam.
     */
    public function processCheckIn(Schedule $schedule, float $latitude, float $longitude, string $proofPath): Attendance
    {
        return DB::transaction(function () use ($schedule, $latitude, $longitude, $proofPath) {
            // Prevent double attendance
            if ($schedule->attendance) {
                throw new \Exception('Anda sudah melakukan absensi untuk jadwal ini.');
            }

            $mosqueConfig = MosqueConfig::where('season_id', $schedule->season_id)->first();

            if (!$mosqueConfig || empty($mosqueConfig->latitude) || empty($mosqueConfig->longitude)) {
                $errorMsg = "PAGGIILAN SISTEM DARURAT: Sistem Koordinat/GPS Lokasi Masjid belum dikonfigurasi! Seorang Imam bernama {$schedule->user->name} gagal memproses absensinya.";
                $admins = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();
                \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\SystemAlertNotification($errorMsg));
                throw new \Exception('Absensi tidak dapat dilakukan karena admin belum mengatur target koordinat lokasi masjid.');
            }

            // Calculate distance
            $distance = $this->calculateHaversineDistance(
                $latitude, $longitude,
                (float) $mosqueConfig->latitude, (float) $mosqueConfig->longitude
            );
            $isWithinRadius = $distance <= $mosqueConfig->radius_meters;

            // Validate time window
            $isWithinTimeWindow = $this->validateTimeWindow($schedule, $mosqueConfig);

            if ($mosqueConfig && !$isWithinRadius) {
                $distInt = round($distance);
                throw new \Exception("Absensi gagal. Anda berada di luar jangkauan ({$distInt} meter dari masjid). Maksimal jarak yang diizinkan adalah {$mosqueConfig->radius_meters} meter.");
            }

            $status = 'pending';

            $attendance = Attendance::create([
                'schedule_id' => $schedule->id,
                'proof_path' => $proofPath,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'distance_meters' => $distance ? (int) round($distance) : null,
                'is_within_radius' => $isWithinRadius,
                'is_within_time_window' => $isWithinTimeWindow,
                'checked_in_at' => now(),
                'status' => $status,
                'notes' => $this->buildNotes($isWithinRadius, $isWithinTimeWindow, $distance),
            ]);

            // Assign penalty/reward points immediately after attendance
            try {
                app(\App\Services\PenaltyService::class)->recordAttendance($schedule, $attendance);
            } catch (\Exception $e) {
                // Log and swallow the error, so attendance still succeeds
                \Illuminate\Support\Facades\Log::error('Failed to log penalty for attendance: ' . $e->getMessage());
            }

            return $attendance;
        });
    }

    public function validateTimeWindow(Schedule $schedule, ?MosqueConfig $mosqueConfig = null): bool
    {
        $windowMinutesBefore = $mosqueConfig?->attendance_window_minutes ?? 30;
        $windowMinutesAfter = $mosqueConfig?->attendance_window_after_minutes ?? 30;

        $prayerTime = PrayerTime::where('season_id', $schedule->season_id)
            ->where('date', $schedule->date->toDateString())
            ->where('prayer_type_id', $schedule->prayer_type_id)
            ->first();

        if (!$prayerTime || !$prayerTime->effective_time) {
            $formattedDate = $schedule->date->format('d M Y');
            $prayerName = $schedule->prayerType->name ?? 'Sholat';
            $errorMsg = "PAGGIILAN SISTEM DARURAT: Data Waktu Sholat ({$prayerName}) untuk tanggal {$formattedDate} belum dikonfigurasi/sinkron! Sistem terhenti saat mendapat panggilan absensi.";
            $admins = \App\Models\User::where('role', 'admin')->where('is_active', true)->get();
            \Illuminate\Support\Facades\Notification::send($admins, new \App\Notifications\SystemAlertNotification($errorMsg));
            
            throw new \Exception("Absensi tidak dapat diproses: Waktu Sholat sistem belum disinkronisasi oleh Admin.");
        }

        $prayerDateTime = Carbon::parse($schedule->date->toDateString() . ' ' . $prayerTime->effective_time);
        $windowStart = $prayerDateTime->copy()->subMinutes($windowMinutesBefore);

        // Allow check-in from [windowStart] until [prayerTime + X min]
        $windowEnd = $prayerDateTime->copy()->addMinutes($windowMinutesAfter);

        return now()->between($windowStart, $windowEnd);
    }

    /**
     * Calculate distance between two GPS coordinates using Haversine formula.
     * Returns distance in meters.
     */
    public function calculateHaversineDistance(float $lat1, float $lng1, float $lat2, float $lng2): float
    {
        $earthRadius = 6371000; // meters

        $dLat = deg2rad($lat2 - $lat1);
        $dLng = deg2rad($lng2 - $lng1);

        $a = sin($dLat / 2) * sin($dLat / 2)
            + cos(deg2rad($lat1)) * cos(deg2rad($lat2))
            * sin($dLng / 2) * sin($dLng / 2);

        $c = 2 * atan2(sqrt($a), sqrt(1 - $a));

        return $earthRadius * $c;
    }

    /**
     * Build human-readable notes for attendance.
     */
    private function buildNotes(bool $isWithinRadius, bool $isWithinTimeWindow, ?float $distance): string
    {
        $parts = [];

        if ($isWithinRadius) {
            $parts[] = 'Lokasi valid';
        } else {
            $parts[] = 'Di luar radius masjid' . ($distance ? " ({$distance}m)" : '');
        }

        if ($isWithinTimeWindow) {
            $parts[] = 'Waktu valid';
        } else {
            $parts[] = 'Di luar jendela waktu';
        }

        return implode('. ', $parts) . '.';
    }
}
