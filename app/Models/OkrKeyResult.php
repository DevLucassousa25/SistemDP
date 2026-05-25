<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OkrKeyResult extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'objective_id', 'title', 'description', 'type',
        'initial_value', 'target_value', 'current_value', 'unit',
        'owner_id', 'status', 'progress', 'due_date',
    ];

    protected $casts = [
        'initial_value' => 'decimal:2',
        'target_value'  => 'decimal:2',
        'current_value' => 'decimal:2',
        'progress'      => 'decimal:2',
        'due_date'      => 'date',
    ];

    // ── Relations ─────────────────────────────────────────────────────

    public function objective(): BelongsTo
    {
        return $this->belongsTo(OkrObjective::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function checkins(): HasMany
    {
        return $this->hasMany(OkrCheckin::class, 'key_result_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'numeric'    => 'Numérico',
            'percentage' => 'Percentual',
            'boolean'    => 'Booleano',
            'currency'   => 'Monetário',
            default      => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_started' => 'Não iniciado',
            'on_track'    => 'No prazo',
            'at_risk'     => 'Em risco',
            'behind'      => 'Atrasado',
            'completed'   => 'Concluído',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'on_track'  => 'emerald',
            'at_risk'   => 'amber',
            'behind'    => 'rose',
            'completed' => 'blue',
            default     => 'slate',
        };
    }

    public function getFormattedCurrentAttribute(): string
    {
        return $this->formatValue($this->current_value);
    }

    public function getFormattedTargetAttribute(): string
    {
        return $this->formatValue($this->target_value);
    }

    private function formatValue(float $value): string
    {
        return match ($this->type) {
            'percentage' => number_format($value, 1) . '%',
            'currency'   => 'R$ ' . number_format($value, 2, ',', '.'),
            'boolean'    => $value >= 1 ? 'Sim' : 'Não',
            default      => number_format($value, 0, ',', '.') . ($this->unit ? ' ' . $this->unit : ''),
        };
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Atualiza current_value, recalcula progress/status e dispara recalc no Objetivo pai.
     */
    public function aplicarCheckin(float $newValue): void
    {
        $this->current_value = $newValue;

        // Progresso baseado no tipo
        if ($this->type === 'boolean') {
            $progress = $newValue >= 1 ? 100 : 0;
        } else {
            $range = $this->target_value - $this->initial_value;
            $progress = $range != 0
                ? min(100, max(0, (($newValue - $this->initial_value) / $range) * 100))
                : ($newValue >= $this->target_value ? 100 : 0);
        }

        $status = match (true) {
            $progress >= 100 => 'completed',
            $progress >= 70  => 'on_track',
            $progress >= 40  => 'at_risk',
            $progress > 0    => 'behind',
            default          => 'not_started',
        };

        $this->progress = $progress;
        $this->status   = $status;
        $this->save();

        // Propaga recalculo para o objetivo pai
        $this->objective->recalcularProgresso();
    }
}
