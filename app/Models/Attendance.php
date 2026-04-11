<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'schedule_id',
        'proof_path',
        'latitude',
        'longitude',
        'distance_meters',
        'is_within_radius',
        'is_within_time_window',
        'checked_in_at',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'latitude' => 'decimal:8',
            'longitude' => 'decimal:8',
            'distance_meters' => 'integer',
            'is_within_radius' => 'boolean',
            'is_within_time_window' => 'boolean',
            'checked_in_at' => 'datetime',
        ];
    }

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
