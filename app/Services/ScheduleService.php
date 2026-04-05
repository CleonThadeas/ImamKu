<?php

namespace App\Services;

use App\Models\PrayerType;
use App\Models\Schedule;
use App\Models\RamadanSeason;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class ScheduleService
{
    /**
     * Generate empty schedule slots for all dates × prayer types in a season.
     */
    public function bulkGenerate(int $seasonId): int
    {
        $season = RamadanSeason::findOrFail($seasonId);
        $prayerTypes = PrayerType::orderBy('sort_order')->get();
        $count = 0;

        $startDate = $season->start_date->copy();
        $endDate = $season->end_date->copy();

        while ($startDate->lte($endDate)) {
            foreach ($prayerTypes as $prayerType) {
                Schedule::firstOrCreate([
                    'season_id' => $seasonId,
                    'date' => $startDate->toDateString(),
                    'prayer_type_id' => $prayerType->id,
                ], [
                    'user_id' => null,
                    'notes' => null,
                ]);
                $count++;
            }
            $startDate->addDay();
        }

        return $count;
    }

    /**
     * Assign an imam to a schedule slot with validation.
     */
    public function assignImam(int $scheduleId, int $userId): Schedule
    {
        return DB::transaction(function () use ($scheduleId, $userId) {
            $schedule = Schedule::with('prayerType')->lockForUpdate()->findOrFail($scheduleId);

            // Check if slot already assigned to another imam
            if ($schedule->user_id && $schedule->user_id !== $userId) {
                throw new \Exception("Slot ini sudah diisi oleh imam lain.");
            }

            // Validate consecutive rule
            if (!$this->validateConsecutiveRule($schedule->date, $schedule->prayer_type_id, $userId, $scheduleId)) {
                throw new \Exception("Imam tidak boleh mengisi dua slot sholat berurutan dengan group berbeda.");
            }

            $schedule->update(['user_id' => $userId]);
            return $schedule->fresh(['prayerType', 'user']);
        });
    }

    /**
     * Remove imam from a schedule slot.
     */
    public function removeAssignment(int $scheduleId): Schedule
    {
        $schedule = Schedule::findOrFail($scheduleId);
        $schedule->update(['user_id' => null]);
        return $schedule->fresh();
    }

    /**
     * Validate that assigning this imam does not violate the consecutive-slot rule.
     *
     * Rule: Imam cannot fill two consecutive prayer slots on the same day IF they have different group_codes.
     * Same group (Isya+Tarawih, both group E) IS allowed consecutively.
     * After skipping one slot, the imam can be scheduled again.
     */
    public function validateConsecutiveRule(string|Carbon $date, int $prayerTypeId, int $userId, ?int $excludeScheduleId = null): bool
    {
        $dateStr = $date instanceof Carbon ? $date->toDateString() : $date;
        $currentPrayerType = PrayerType::findOrFail($prayerTypeId);

        // Get all prayer types ordered by sort_order
        $allPrayerTypes = PrayerType::orderBy('sort_order')->get();

        // Find adjacent prayer types (previous and next by sort_order)
        $adjacentTypes = [];
        foreach ($allPrayerTypes as $index => $pt) {
            if ($pt->id === $currentPrayerType->id) {
                if ($index > 0) {
                    $adjacentTypes[] = $allPrayerTypes[$index - 1]; // previous
                }
                if ($index < $allPrayerTypes->count() - 1) {
                    $adjacentTypes[] = $allPrayerTypes[$index + 1]; // next
                }
                break;
            }
        }

        // Check each adjacent slot
        foreach ($adjacentTypes as $adjType) {
            // Same group_code means they're allowed to be consecutive
            if ($adjType->group_code === $currentPrayerType->group_code) {
                continue;
            }

            // Check if this imam is assigned to the adjacent slot
            $query = Schedule::where('date', $dateStr)
                ->where('prayer_type_id', $adjType->id)
                ->where('user_id', $userId);

            if ($excludeScheduleId) {
                $query->where('id', '!=', $excludeScheduleId);
            }

            if ($query->exists()) {
                return false; // Violation: imam is in adjacent slot with different group
            }
        }

        return true;
    }

    /**
     * Get available imams for a given date and prayer type slot.
     */
    public function getAvailableImams(string $date, int $prayerTypeId): Collection
    {
        $imams = User::where('role', 'imam')
            ->where('is_active', true)
            ->get();

        return $imams->filter(function ($imam) use ($date, $prayerTypeId) {
            // Check if imam is already assigned to this slot
            $alreadyAssigned = Schedule::where('date', $date)
                ->where('prayer_type_id', $prayerTypeId)
                ->where('user_id', $imam->id)
                ->exists();

            if ($alreadyAssigned) {
                return true; // Already assigned, still available (for display)
            }

            // Check if the slot is already taken by another imam
            $slotTaken = Schedule::where('date', $date)
                ->where('prayer_type_id', $prayerTypeId)
                ->whereNotNull('user_id')
                ->exists();

            if ($slotTaken) {
                return false;
            }

            // Validate consecutive rule
            return $this->validateConsecutiveRule($date, $prayerTypeId, $imam->id);
        });
    }

    /**
     * Get schedules for a specific date.
     */
    public function getSchedulesByDate(string $date, int $seasonId): Collection
    {
        return Schedule::with(['prayerType', 'user'])
            ->where('season_id', $seasonId)
            ->where('date', $date)
            ->join('prayer_types', 'schedules.prayer_type_id', '=', 'prayer_types.id')
            ->orderBy('prayer_types.sort_order')
            ->select('schedules.*')
            ->get();
    }

    /**
     * Get all schedules for a season grouped by date.
     */
    public function getSeasonSchedules(int $seasonId): Collection
    {
        return Schedule::with(['prayerType', 'user', 'attendance'])
            ->where('season_id', $seasonId)
            ->join('prayer_types', 'schedules.prayer_type_id', '=', 'prayer_types.id')
            ->orderBy('schedules.date')
            ->orderBy('prayer_types.sort_order')
            ->select('schedules.*')
            ->get()
            ->groupBy(fn ($s) => $s->date->toDateString());
    }
}
