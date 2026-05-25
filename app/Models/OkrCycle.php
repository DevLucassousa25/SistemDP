<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class OkrCycle extends Model
{
    protected $fillable = [
        'name', 'start_date', 'end_date',
        'status', 'description', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date'   => 'date',
    ];

    public function objectives(): HasMany
    {
        return $this->hasMany(OkrObjective::class, 'cycle_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getDurationLabelAttribute(): string
    {
        return $this->start_date->format('d/m/Y') . ' — ' . $this->end_date->format('d/m/Y');
    }

    public function getProgressDaysAttribute(): int
    {
        $total   = $this->start_date->diffInDays($this->end_date);
        $elapsed = $this->start_date->diffInDays(now());
        return $total > 0 ? (int) min(100, max(0, ($elapsed / $total) * 100)) : 0;
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'planning' => 'Planejamento',
            'active'   => 'Ativo',
            'closed'   => 'Encerrado',
            default    => $this->status,
        };
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
