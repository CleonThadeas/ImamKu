<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'schedule_id',
        'proof_path',
        'status',
        'notes',
    ];

    public function schedule()
    {
        return $this->belongsTo(Schedule::class);
    }
}
