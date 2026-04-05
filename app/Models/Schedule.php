<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Schedule extends Model
{
    protected $fillable = ['season_id', 'date', 'prayer_type_id', 'user_id', 'notes'];

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Accessor for prayer time.
     */
    public function getPrayerTimeAttribute()
    {
        return PrayerTime::where('season_id', $this->season_id)
            ->where('date', $this->date->toDateString())
            ->where('prayer_type_id', $this->prayer_type_id)
            ->first();
    }

    public function swapRequests(): HasMany
    {
        return $this->hasMany(SwapRequest::class, 'schedule_id');
    }

    public function targetSwapRequests(): HasMany
    {
        return $this->hasMany(SwapRequest::class, 'target_schedule_id');
    }

    public function notificationLogs(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function attendance(): HasOne
    {
        return $this->hasOne(Attendance::class);
    }
}
