<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeeDetail extends Model
{
    protected $fillable = ['fee_config_id', 'prayer_type_id', 'amount'];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
        ];
    }

    public function feeConfig(): BelongsTo
    {
        return $this->belongsTo(FeeConfig::class);
    }

    public function prayerType(): BelongsTo
    {
        return $this->belongsTo(PrayerType::class);
    }
}
