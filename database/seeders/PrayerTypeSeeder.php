<?php

namespace Database\Seeders;

use App\Models\PrayerType;
use Illuminate\Database\Seeder;

class PrayerTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Subuh',    'group_code' => 'A', 'sort_order' => 1],
            ['name' => 'Dzuhur',   'group_code' => 'B', 'sort_order' => 2],
            ['name' => 'Ashar',    'group_code' => 'C', 'sort_order' => 3],
            ['name' => 'Maghrib',  'group_code' => 'D', 'sort_order' => 4],
            ['name' => 'Isya',     'group_code' => 'E', 'sort_order' => 5],
            ['name' => 'Tarawih',  'group_code' => 'E', 'sort_order' => 6],
        ];

        foreach ($types as $type) {
            PrayerType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }
    }
}
