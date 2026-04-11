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
     * Generate schedule slots only for prayer types that have active prayer_times on each date.
     * Also removes orphaned slots whose prayer_type no longer has a prayer_time entry.
     */
    public function bulkGenerate(int $seasonId): int
    {
        $season = RamadanSeason::findOrFail($seasonId);
        $count = 0;
        $validSlotKeys = []; // track date_prayerTypeId combos that should exist

        $startDate = $season->start_date->copy();
        $endDate = $season->end_date->copy();

        // Pre-load all prayer_times for this season grouped by date
        $allPrayerTimes = \App\Models\PrayerTime::where('season_id', $seasonId)
            ->get()
            ->groupBy(fn ($pt) => $pt->date->toDateString());

        while ($startDate->lte($endDate)) {
            $dateStr = $startDate->toDateString();
            $datePrayerTimes = $allPrayerTimes->get($dateStr, collect());

            // Only create schedule slots for prayer types that have a prayer_time entry
            foreach ($datePrayerTimes as $prayerTime) {
                $key = $dateStr . '_' . $prayerTime->prayer_type_id;
                $validSlotKeys[] = $key;

                Schedule::firstOrCreate([
                    'season_id' => $seasonId,
                    'date' => $dateStr,
                    'prayer_type_id' => $prayerTime->prayer_type_id,
                ], [
                    'user_id' => null,
                    'notes' => null,
                ]);
                $count++;
            }
            $startDate->addDay();
        }

        // Remove orphaned schedule slots that don't have matching prayer_times
        $existingSchedules = Schedule::where('season_id', $seasonId)->get();
        $orphanIds = [];
        foreach ($existingSchedules as $schedule) {
            $key = $schedule->date->toDateString() . '_' . $schedule->prayer_type_id;
            if (!in_array($key, $validSlotKeys)) {
                // Only remove if unassigned (don't delete assigned schedules)
                if (!$schedule->user_id) {
                    $orphanIds[] = $schedule->id;
                }
            }
        }

        if (!empty($orphanIds)) {
            Schedule::whereIn('id', $orphanIds)->delete();
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

            $user = User::find($userId);
            if ($user && $user->is_restricted) {
                throw new \Exception("Imam {$user->name} saat ini dibatasi (restricted) karena penalti, tidak bisa ditugaskan.");
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

    /**
     * Find the best available imam for a schedule slot.
     * Criteria: (1) active + not restricted, (2) passes consecutive rule,
     * (3) fewest schedules in current season, (4) highest penalty points.
     */
    public function findBestAvailableImam(Schedule $schedule): ?User
    {
        $imams = User::where('role', 'imam')
            ->where('is_active', true)
            ->get();

        // Filter by consecutive rule
        $eligible = $imams->filter(function ($imam) use ($schedule) {
            // Check restriction (penalty system — Phase 4, graceful fallback)
            if (method_exists($imam, 'getAttribute') && $imam->is_restricted) {
                return false;
            }

            return $this->validateConsecutiveRule(
                $schedule->date,
                $schedule->prayer_type_id,
                $imam->id
            );
        });

        if ($eligible->isEmpty()) {
            return null;
        }

        // Count schedules per imam in the active season
        $seasonId = $schedule->season_id;
        $scheduleCounts = Schedule::where('season_id', $seasonId)
            ->whereNotNull('user_id')
            ->selectRaw('user_id, COUNT(*) as total')
            ->groupBy('user_id')
            ->pluck('total', 'user_id');

        // Sort by: fewest schedules first, then highest penalty_points (for future Phase 4)
        return $eligible->sortBy(function ($imam) use ($scheduleCounts) {
            $count = $scheduleCounts->get($imam->id, 0);
            $points = $imam->penalty_points ?? 0;
            // Lower schedule count = better, higher points = better (negate for ascending sort)
            return [$count, -$points];
        })->first();
    }
}
