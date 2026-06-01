<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SuccessionPosition extends Model
{
    protected $table = 'succession_positions';

    protected $fillable = [
        'title', 'description', 'department_id', 'current_holder_id',
        'risk_level', 'time_to_fill_months', 'notes', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function currentHolder(): BelongsTo
    {
        return $this->belongsTo(User::class, 'current_holder_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function candidates(): HasMany
    {
        return $this->hasMany(SuccessionCandidate::class, 'position_id')->orderBy('priority');
    }

    // ── Acessórios ─────────────────────────────────────────────────────

    public function getRiskLabelAttribute(): string
    {
        return match ($this->risk_level) {
            'critico' => 'Crítico',
            'alto'    => 'Alto',
            'medio'   => 'Médio',
            default   => ucfirst($this->risk_level),
        };
    }

    public function getRiskColorAttribute(): string
    {
        return match ($this->risk_level) {
            'critico' => 'rose',
            'alto'    => 'amber',
            'medio'   => 'blue',
            default   => 'slate',
        };
    }

    public function getCoverageAttribute(): string
    {
        $count = $this->candidates()->count();
        if ($count === 0) return 'Descoberta';
        if ($count === 1) return '1 candidato';
        return "{$count} candidatos";
    }

    public function getBestReadinessAttribute(): ?string
    {
        $best = $this->candidates()
            ->orderByRaw("CASE readiness WHEN 'pronto_agora' THEN 1 WHEN '6_a_12_meses' THEN 2 ELSE 3 END")
            ->value('readiness');
        return $best;
    }
}
