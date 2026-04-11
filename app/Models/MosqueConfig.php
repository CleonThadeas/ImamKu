<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MosqueConfig extends Model
{
    protected $fillable = [
        'season_id', 'name', 'latitude', 'longitude',
        'radius_meters', 'attendance_window_minutes', 'attendance_window_after_minutes'
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'radius_meters' => 'integer',
            'attendance_window_minutes' => 'integer',
        ];
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(RamadanSeason::class, 'season_id');
    }

    /**
     * Get boundaries for admin UI validation.
     */
    public static function radiusBounds(): array
    {
        return ['min' => 25, 'max' => 500, 'default' => 100];
    }
}
