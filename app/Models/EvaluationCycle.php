<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationCycle extends Model
{
    protected $fillable = [
        'name',
        'description',
        'start_date',
        'end_date',
        'status',
        'created_by',
        'results_published_at',
    ];

    protected $casts = [
        'start_date'           => 'date',
        'end_date'             => 'date',
        'results_published_at' => 'datetime',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function managerEvaluations(): HasMany
    {
        return $this->hasMany(ManagerEvaluation::class, 'evaluation_cycle_id');
    }

    public function surveys(): HasMany
    {
        return $this->hasMany(\App\Models\Survey::class, 'evaluation_cycle_id');
    }

    public function selfEvaluations(): HasMany
    {
        return $this->hasMany(SelfEvaluation::class, 'evaluation_cycle_id');
    }

    // ── Acessórios ─────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'draft'  => 'Rascunho',
            'active' => 'Ativo',
            'closed' => 'Encerrado',
            default  => ucfirst((string) $this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'draft'  => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
            'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'closed' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
            default  => 'bg-slate-100 text-slate-600',
        };
    }

    public function getPeriodLabelAttribute(): string
    {
        return $this->start_date->format('d/m/Y') . ' – ' . $this->end_date->format('d/m/Y');
    }
}
