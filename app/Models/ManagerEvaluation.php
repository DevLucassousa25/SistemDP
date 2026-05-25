<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ManagerEvaluation extends Model
{
    protected $fillable = [
        'evaluation_cycle_id',
        'manager_id',
        'status',
        'final_score',
        'completed_at',
        'results_published_at',
    ];

    protected $casts = [
        'completed_at'         => 'datetime',
        'results_published_at' => 'datetime',
        'final_score'          => 'float',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(EvaluationCycle::class, 'evaluation_cycle_id');
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    public function entries(): HasMany
    {
        return $this->hasMany(ManagerEvaluationEntry::class, 'manager_evaluation_id');
    }

    // ── Acessórios ─────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_started' => 'Não iniciada',
            'in_progress' => 'Em andamento',
            'completed'   => 'Finalizada',
            default       => ucfirst((string) $this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'not_started' => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
            'in_progress' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'completed'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            default       => 'bg-slate-100 text-slate-500',
        };
    }

    /**
     * Média ponderada das notas, considerando calibração e peso dos critérios.
     * Persiste o resultado em final_score ao ser chamado.
     */
    public function calculateFinalScore(): ?float
    {
        $entries = $this->entries()->with('criterion')->get();

        if ($entries->isEmpty()) {
            return null;
        }

        $totalWeight = 0;
        $weightedSum = 0;

        foreach ($entries as $entry) {
            $criterion    = $entry->criterion;
            $weight       = (float) ($criterion->weight ?? 1.0);
            $score        = $entry->normalizedScore;

            if ($score !== null) {
                $weightedSum += $score * $weight;
                $totalWeight += $weight;
            }
        }

        if ($totalWeight <= 0) {
            return null;
        }

        $final = round($weightedSum / $totalWeight, 2);
        $this->update(['final_score' => $final]);

        return $final;
    }

    /**
     * Média simples (sem ponderação) — fallback legado.
     */
    public function getAverageScoreAttribute(): ?float
    {
        if ($this->final_score !== null) {
            return $this->final_score;
        }

        $avg = $this->entries()->avg('score');
        return $avg ? round((float) $avg, 1) : null;
    }
}
