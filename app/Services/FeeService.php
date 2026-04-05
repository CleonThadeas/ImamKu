<?php

namespace App\Services;

use App\Models\FeeConfig;
use App\Models\FeeDetail;
use App\Models\PrayerType;
use App\Models\Schedule;
use Illuminate\Support\Collection;

class FeeService
{
    /**
     * Calculate fee for a single schedule entry.
     */
    public function calculateScheduleFee(Schedule $schedule): float
    {
        $feeConfig = FeeConfig::where('season_id', $schedule->season_id)->first();

        if (!$feeConfig || !$feeConfig->is_enabled) {
            return 0;
        }

        if ($feeConfig->mode === 'per_schedule') {
            // Find fee detail for this prayer type
            $detail = FeeDetail::where('fee_config_id', $feeConfig->id)
                ->where('prayer_type_id', $schedule->prayer_type_id)
                ->first();

            return $detail ? (float) $detail->amount : 0;
        }

        // Per day mode: get the flat daily rate
        $detail = FeeDetail::where('fee_config_id', $feeConfig->id)
            ->whereNull('prayer_type_id')
            ->first();

        return $detail ? (float) $detail->amount : 0;
    }

    /**
     * Get fee report for an imam in a season.
     */
    public function getImamFeeReport(int $seasonId, int $userId): array
    {
        $feeConfig = FeeConfig::where('season_id', $seasonId)->first();

        if (!$feeConfig || !$feeConfig->is_enabled) {
            return ['total' => 0, 'details' => [], 'mode' => null];
        }

        $schedules = Schedule::with(['prayerType', 'attendance'])
            ->where('season_id', $seasonId)
            ->where('user_id', $userId)
            ->whereHas('attendance', function ($q) {
                $q->where('status', 'approved');
            })
            ->get();

        if ($feeConfig->mode === 'per_schedule') {
            $details = [];
            $total = 0;

            foreach ($schedules as $schedule) {
                /** @var \App\Models\Schedule $schedule */
                $fee = $this->calculateScheduleFee($schedule);
                $total += $fee;

                $prayerName = $schedule->prayerType->name ?? 'Unknown';
                if (!isset($details[$prayerName])) {
                    $details[$prayerName] = ['count' => 0, 'fee_per' => $fee, 'subtotal' => 0];
                }
                $details[$prayerName]['count']++;
                $details[$prayerName]['subtotal'] += $fee;
            }

            return ['total' => $total, 'details' => $details, 'mode' => 'per_schedule'];
        }

        // Per day mode
        $uniqueDays = $schedules->pluck('date')->unique()->count();
        $dailyRate = FeeDetail::where('fee_config_id', $feeConfig->id)
            ->whereNull('prayer_type_id')
            ->value('amount') ?? 0;

        return [
            'total' => $uniqueDays * (float) $dailyRate,
            'details' => ['days' => $uniqueDays, 'daily_rate' => (float) $dailyRate],
            'mode' => 'per_day',
        ];
    }

    /**
     * Get fee summary for all imams in a season.
     */
    public function getSeasonFeeSummary(int $seasonId): Collection
    {
        $schedules = Schedule::with(['user', 'prayerType'])
            ->where('season_id', $seasonId)
            ->whereNotNull('user_id')
            ->get();

        $imamIds = $schedules->pluck('user_id')->unique();

        return $imamIds->map(fn ($userId) => [
            'user' => $schedules->firstWhere('user_id', $userId)->user,
            'report' => $this->getImamFeeReport($seasonId, $userId),
        ])->values();
    }
}
