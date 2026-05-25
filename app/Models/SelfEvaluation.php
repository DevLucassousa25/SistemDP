<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SelfEvaluation extends Model
{
    protected $fillable = [
        'evaluation_cycle_id',
        'employee_id',
        'status',
        'completed_at',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(EvaluationCycle::class, 'evaluation_cycle_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(SelfEvaluationEntry::class, 'self_evaluation_id');
    }

    // ── Acessórios ─────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'Pendente',
            'in_progress' => 'Em andamento',
            'completed'   => 'Finalizada',
            default       => ucfirst((string) $this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending'     => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
            'in_progress' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'completed'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            default       => 'bg-slate-100 text-slate-500',
        };
    }

    /**
     * Média ponderada das notas (normalizada para escala 1-5).
     */
    public function getWeightedScoreAttribute(): ?float
    {
        $entries = $this->entries()->with('criterion')->get();
        if ($entries->isEmpty()) {
            return null;
        }

        $totalWeight = 0;
        $weightedSum = 0;

        foreach ($entries as $entry) {
            $criterion = $entry->criterion;
            $weight    = (float) ($criterion->weight ?? 1.0);
            $score     = $entry->normalizedScore;

            if ($score !== null) {
                $weightedSum  += $score * $weight;
                $totalWeight  += $weight;
            }
        }

        return $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : null;
    }
}
