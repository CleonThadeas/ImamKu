<?php

namespace Database\Seeders;

use App\Models\PrayerType;
use Illuminate\Database\Seeder;

class PrayerTypeSeeder extends Seeder
{
    public function run(): void
    {
        // ── Default 6: Sholat Fardhu (Wajib 5 Waktu) + Tarawih ──────
        $defaults = [
            ['name' => 'Subuh',    'group_code' => 'A', 'sort_order' => 1,  'is_default' => true, 'api_key' => 'Fajr'],
            ['name' => 'Dzuhur',   'group_code' => 'B', 'sort_order' => 2,  'is_default' => true, 'api_key' => 'Dhuhr'],
            ['name' => 'Ashar',    'group_code' => 'C', 'sort_order' => 3,  'is_default' => true, 'api_key' => 'Asr'],
            ['name' => 'Maghrib',  'group_code' => 'D', 'sort_order' => 4,  'is_default' => true, 'api_key' => 'Maghrib'],
            ['name' => 'Isya',     'group_code' => 'E', 'sort_order' => 5,  'is_default' => true, 'api_key' => 'Isha'],
            ['name' => 'Tarawih',  'group_code' => 'E', 'sort_order' => 6,  'is_default' => true, 'api_key' => null],
        ];

        // ── Sholat Sunnah & Khusus (ditambahkan manual oleh admin) ───
        $specials = [
            ['name' => 'Sholat Dhuha',       'group_code' => 'S', 'sort_order' => 10, 'is_default' => false, 'api_key' => 'Sunrise'],
            ['name' => 'Sholat Tahajjud',    'group_code' => 'S', 'sort_order' => 11, 'is_default' => false, 'api_key' => 'Midnight'],
            ['name' => 'Sholat Witir',       'group_code' => 'S', 'sort_order' => 12, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Jumat',       'group_code' => 'S', 'sort_order' => 13, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Idul Fitri',  'group_code' => 'S', 'sort_order' => 14, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Idul Adha',   'group_code' => 'S', 'sort_order' => 15, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Istikharah',  'group_code' => 'S', 'sort_order' => 16, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Hajat',       'group_code' => 'S', 'sort_order' => 17, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Gerhana',     'group_code' => 'S', 'sort_order' => 18, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Istisqa',     'group_code' => 'S', 'sort_order' => 19, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Jenazah',     'group_code' => 'S', 'sort_order' => 20, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Rawatib',     'group_code' => 'S', 'sort_order' => 21, 'is_default' => false, 'api_key' => null],
            ['name' => 'Sholat Taubat',      'group_code' => 'S', 'sort_order' => 22, 'is_default' => false, 'api_key' => null],
        ];

        $allTypes = array_merge($defaults, $specials);

        foreach ($allTypes as $type) {
            PrayerType::updateOrCreate(
                ['name' => $type['name']],
                $type
            );
        }

        // ── Remove non-sholat entries that were previously seeded ──
        $nonSholatNames = ['Imsak', 'Sunrise', 'Sunset', 'Midnight', 'Firstthird', 'Lastthird'];
        PrayerType::whereIn('name', $nonSholatNames)->delete();
    }
}
