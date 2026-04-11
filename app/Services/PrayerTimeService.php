<?php

namespace App\Services;

use App\Models\PrayerTime;
use App\Models\PrayerType;
use App\Models\RamadanSeason;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PrayerTimeService
{
    /**
     * Build dynamic API mapping from prayer_types table.
     * Maps local prayer name => Aladhan API key using the api_key column.
     */
    private function getApiMapping(): array
    {
        return PrayerType::whereNotNull('api_key')
            ->pluck('api_key', 'name')
            ->toArray();
    }

    /**
     * Fetch prayer times from Aladhan API for a specific date.
     */
    public function fetchFromApi(string $date, ?string $city = null, ?string $country = null): array
    {
        $city = $city ?? config('imamku.aladhan.city');
        $country = $country ?? config('imamku.aladhan.country');
        $method = config('imamku.aladhan.method');

        try {
            /** @var \Illuminate\Http\Client\Response $response */
            $response = Http::timeout(10)->get(config('imamku.aladhan.base_url') . '/timingsByCity/' . $date, [
                'city' => $city,
                'country' => $country,
                'method' => $method,
            ]);

            if ($response->successful()) {
                $timings = $response->json('data.timings');
                return $this->mapApiTimings($timings);
            }

            Log::error('Aladhan API error', ['status' => $response->status(), 'body' => $response->body()]);
            return [];
        } catch (\Exception $e) {
            Log::error('Aladhan API exception', ['message' => $e->getMessage()]);
            return [];
        }
    }

    /**
     * Map API timings to our prayer type names using the dynamic api_key mapping.
     * This now covers ALL types: Subuh(Fajr), Imsak, Sunrise, Sunset, Midnight, etc.
     */
    private function mapApiTimings(array $timings): array
    {
        $mapped = [];
        $apiMapping = $this->getApiMapping();

        foreach ($apiMapping as $localName => $apiKey) {
            if (isset($timings[$apiKey])) {
                // Aladhan returns "HH:MM (TZ)" — strip timezone info
                $time = preg_replace('/\s*\(.*\)/', '', $timings[$apiKey]);
                $mapped[$localName] = $time;
            }
        }

        // Tarawih is typically 15-20 min after Isya (calculated, not from API)
        if (isset($mapped['Isya'])) {
            $isyaTime = \Carbon\Carbon::createFromFormat('H:i', $mapped['Isya']);
            $mapped['Tarawih'] = $isyaTime->addMinutes(20)->format('H:i');
        }

        return $mapped;
    }

    /**
     * Sync prayer times from API for all dates in a season.
     * IMPORTANT: Skips records where is_manual = true to preserve admin overrides.
     */
    public function syncSeasonTimes(int $seasonId): int
    {
        set_time_limit(300); // Allow up to 5 minutes for full season sync

        $season = RamadanSeason::findOrFail($seasonId);
        // Only sync default prayer types (5 fardhu + Tarawih)
        $prayerTypes = PrayerType::where('is_default', true)->get()->keyBy('name');
        $count = 0;

        // Pre-load all manual records to skip them during sync
        $manualRecords = PrayerTime::where('season_id', $seasonId)
            ->where('is_manual', true)
            ->get()
            ->keyBy(fn ($pt) => $pt->date->toDateString() . '_' . $pt->prayer_type_id);

        $currentDate = $season->start_date->copy();
        $endDate = $season->end_date->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('d-m-Y');
            $timings = $this->fetchFromApi($dateStr);

            foreach ($timings as $prayerName => $time) {
                if (!isset($prayerTypes[$prayerName])) continue;

                $prayerTypeId = $prayerTypes[$prayerName]->id;
                $key = $currentDate->toDateString() . '_' . $prayerTypeId;

                // Skip manual records — never overwrite them
                if ($manualRecords->has($key)) {
                    continue;
                }

                PrayerTime::updateOrCreate(
                    [
                        'season_id' => $seasonId,
                        'date' => $currentDate->toDateString(),
                        'prayer_type_id' => $prayerTypeId,
                    ],
                    [
                        'api_time' => $time,
                    ]
                );
                $count++;
            }

            $currentDate->addDay();

            // Rate limiting: wait 250ms between API calls
            usleep(250000);
        }

        return $count;
    }

    /**
     * Override a prayer time manually (admin).
     */
    public function overrideTime(int $prayerTimeId, ?string $time): PrayerTime
    {
        $prayerTime = PrayerTime::findOrFail($prayerTimeId);
        $prayerTime->update(['override_time' => $time]);
        return $prayerTime->fresh();
    }

    /**
     * Create a fully manual prayer time entry (not from API).
     */
    public function createManualTime(int $seasonId, string $date, int $prayerTypeId, string $time): PrayerTime
    {
        // Check if record already exists for this date + prayer type
        $existing = PrayerTime::where('season_id', $seasonId)
            ->where('date', $date)
            ->where('prayer_type_id', $prayerTypeId)
            ->first();

        if ($existing) {
            $typeName = $existing->prayerType->name ?? 'Sholat';
            throw new \Exception("Jenis sholat \"{$typeName}\" sudah terdaftar untuk tanggal ini. Gunakan tombol Edit untuk mengubah waktunya.");
        }

        return PrayerTime::create([
            'season_id' => $seasonId,
            'date' => $date,
            'prayer_type_id' => $prayerTypeId,
            'api_time' => null,
            'override_time' => $time,
            'is_manual' => true,
        ]);
    }

    /**
     * Update an existing prayer time (manual edit).
     */
    public function updateTime(int $prayerTimeId, string $time): PrayerTime
    {
        $prayerTime = PrayerTime::findOrFail($prayerTimeId);
        $prayerTime->update(['override_time' => $time]);
        return $prayerTime->fresh();
    }

    /**
     * Reset override — clear override_time and is_manual, revert to API time.
     */
    public function resetToApi(int $prayerTimeId): PrayerTime
    {
        $prayerTime = PrayerTime::findOrFail($prayerTimeId);

        if (!$prayerTime->api_time) {
            throw new \Exception('Tidak ada waktu API untuk record ini. Hapus record jika tidak diperlukan.');
        }

        $prayerTime->update([
            'override_time' => null,
            'is_manual' => false,
        ]);

        return $prayerTime->fresh();
    }

    /**
     * Delete a manual prayer time entry entirely.
     */
    public function deleteManualTime(int $prayerTimeId): void
    {
        $prayerTime = PrayerTime::findOrFail($prayerTimeId);

        if (!$prayerTime->is_manual && $prayerTime->api_time) {
            throw new \Exception('Tidak bisa menghapus record API. Gunakan Reset ke API untuk mengembalikan waktu asli.');
        }

        $prayerTime->delete();
    }

    /**
     * Get all prayer times for a specific date in a season.
     */
    public function getTimesForDate(int $seasonId, string $date): \Illuminate\Support\Collection
    {
        return PrayerTime::with('prayerType')
            ->where('season_id', $seasonId)
            ->where('date', $date)
            ->join('prayer_types', 'prayer_times.prayer_type_id', '=', 'prayer_types.id')
            ->orderBy('prayer_types.sort_order')
            ->select('prayer_times.*')
            ->get();
    }
}
