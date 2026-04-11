<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PenaltyLog extends Model
{
    protected $fillable = [
        'user_id', 'schedule_id', 'swap_request_id',
        'event_type', 'points', 'description',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function schedule(): BelongsTo
    {
        return $this->belongsTo(Schedule::class);
    }

    public function swapRequest(): BelongsTo
    {
        return $this->belongsTo(SwapRequest::class);
    }
}
