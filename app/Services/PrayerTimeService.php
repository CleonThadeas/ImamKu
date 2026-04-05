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
     * Map Aladhan API response keys to our prayer type names.
     */
    private const API_MAPPING = [
        'Subuh'   => 'Fajr',
        'Dzuhur'  => 'Dhuhr',
        'Ashar'   => 'Asr',
        'Maghrib' => 'Maghrib',
        'Isya'    => 'Isha',
    ];

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
     * Map API timings to our prayer type names.
     */
    private function mapApiTimings(array $timings): array
    {
        $mapped = [];
        foreach (self::API_MAPPING as $localName => $apiKey) {
            if (isset($timings[$apiKey])) {
                // Aladhan returns "HH:MM (TZ)" — strip timezone info
                $time = preg_replace('/\s*\(.*\)/', '', $timings[$apiKey]);
                $mapped[$localName] = $time;
            }
        }
        // Tarawih is typically 15-20 min after Isya
        if (isset($mapped['Isya'])) {
            $isyaTime = \Carbon\Carbon::createFromFormat('H:i', $mapped['Isya']);
            $mapped['Tarawih'] = $isyaTime->addMinutes(20)->format('H:i');
        }
        return $mapped;
    }

    /**
     * Sync prayer times from API for all dates in a season.
     */
    public function syncSeasonTimes(int $seasonId): int
    {
        set_time_limit(300); // Allow up to 5 minutes for full season sync

        $season = RamadanSeason::findOrFail($seasonId);
        $prayerTypes = PrayerType::all()->keyBy('name');
        $count = 0;

        $currentDate = $season->start_date->copy();
        $endDate = $season->end_date->copy();

        while ($currentDate->lte($endDate)) {
            $dateStr = $currentDate->format('d-m-Y');
            $timings = $this->fetchFromApi($dateStr);

            foreach ($timings as $prayerName => $time) {
                if (!isset($prayerTypes[$prayerName])) continue;

                PrayerTime::updateOrCreate(
                    [
                        'season_id' => $seasonId,
                        'date' => $currentDate->toDateString(),
                        'prayer_type_id' => $prayerTypes[$prayerName]->id,
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
