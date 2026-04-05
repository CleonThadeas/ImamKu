<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeeConfig extends Model
{
    protected $fillable = ['season_id', 'mode', 'is_enabled', 'is_auto_approve_attendance'];

    protected function casts(): array
    {
        return [
            'is_enabled' => 'boolean',
        ];
    }

    public function season(): BelongsTo
    {
        return $this->belongsTo(RamadanSeason::class, 'season_id');
    }

    public function details(): HasMany
    {
        return $this->hasMany(FeeDetail::class);
    }
}
