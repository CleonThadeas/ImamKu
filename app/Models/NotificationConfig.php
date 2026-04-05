<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class NotificationConfig extends Model
{
    protected $fillable = [
        'channels',
        'reminder_1_minutes',
        'enable_reminder_2',
        'reminder_2_minutes'
    ];

    protected $casts = [
        'enable_reminder_2' => 'boolean',
    ];
}
