<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PrayerTime extends Model
{
    protected $fillable = ['season_id', 'date', 'prayer_type_id', 'api_time', 'override_time'];

    protected function casts(): array
    {
        return [
            'date' => 'date',
        ];
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(RamadanSeason::class, 'season_id');
    }

    public function prayerType(): BelongsTo
    {
        return $this->belongsTo(PrayerType::class);
    }

    /**
     * Get effective time: override takes priority over API time.
     */
    public function getEffectiveTimeAttribute(): ?string
    {
        return $this->override_time ?? $this->api_time;
    }
}
