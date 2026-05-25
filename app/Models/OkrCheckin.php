<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OkrCheckin extends Model
{
    protected $fillable = [
        'key_result_id', 'user_id',
        'previous_value', 'new_value',
        'confidence', 'comment',
    ];

    protected $casts = [
        'previous_value' => 'decimal:2',
        'new_value'      => 'decimal:2',
    ];

    public function keyResult(): BelongsTo
    {
        return $this->belongsTo(OkrKeyResult::class, 'key_result_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getDeltaAttribute(): float
    {
        return $this->new_value - $this->previous_value;
    }

    public function getDeltaLabelAttribute(): string
    {
        $delta = $this->delta;
        return ($delta >= 0 ? '+' : '') . number_format($delta, 2, ',', '.');
    }
}
