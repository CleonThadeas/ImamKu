<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RamadanSeason extends Model
{
    protected $fillable = ['name', 'hijri_year', 'start_date', 'end_date', 'is_active'];

    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'is_active' => 'boolean',
        ];
    }

    public function prayerTimes(): HasMany
    {
        return $this->hasMany(PrayerTime::class, 'season_id');
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class, 'season_id');
    }

    public function feeConfig(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(FeeConfig::class, 'season_id');
    }

    /**
     * Get the number of days in this Ramadan season.
     */
    public function getDaysCountAttribute(): int
    {
        return $this->start_date->diffInDays($this->end_date) + 1;
    }
}
