<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class PrayerType extends Model
{
    protected $fillable = ['name', 'group_code', 'sort_order'];

    public function prayerTimes(): HasMany
    {
        return $this->hasMany(PrayerTime::class);
    }

    public function schedules(): HasMany
    {
        return $this->hasMany(Schedule::class);
    }

    public function feeDetails(): HasMany
    {
        return $this->hasMany(FeeDetail::class);
    }
}
