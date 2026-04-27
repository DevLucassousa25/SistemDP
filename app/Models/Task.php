<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Task extends Model
{
    protected $table = 'tasks';

    protected $fillable = [
        'title',
        'description',
        'assigned_to',
        'assigned_by',
        'due_date',
        'priority',
        'status',
        'completed_at',
        'recurrence',
        'recurrence_ends_at',
        'estimated_minutes',
    ];

    protected $casts = [
        'due_date'           => 'date',
        'completed_at'       => 'datetime',
        'recurrence_ends_at' => 'date',
    ];

    // ─────────────────────────────────────────────────────────────────
    // Relacionamentos
    // ─────────────────────────────────────────────────────────────────

    public function assignedTo(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function assignedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'assigned_by');
    }

    public function subtasks(): HasMany
    {
        return $this->hasMany(TaskSubtask::class)->orderBy('order')->orderBy('id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(TaskTag::class, 'task_tag', 'task_id', 'task_tag_id');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(TaskComment::class)->orderBy('created_at');
    }

    // ─────────────────────────────────────────────────────────────────
    // Acessórios
    // ─────────────────────────────────────────────────────────────────

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta'  => 'Alta',
            default => ucfirst((string) $this->priority),
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'baixa' => 'text-slate-500 bg-slate-100 dark:bg-slate-700 dark:text-slate-300',
            'media' => 'text-amber-700 bg-amber-50 dark:bg-amber-900/30 dark:text-amber-300',
            'alta'  => 'text-red-700 bg-red-50 dark:bg-red-900/30 dark:text-red-300',
            default => 'text-slate-500 bg-slate-100',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendente'     => 'Pendente',
            'em_andamento' => 'Em andamento',
            'concluida'    => 'Concluída',
            'cancelada'    => 'Cancelada',
            default        => ucfirst((string) $this->status),
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pendente'     => 'text-slate-600 bg-slate-100 dark:bg-slate-700 dark:text-slate-300',
            'em_andamento' => 'text-blue-700 bg-blue-50 dark:bg-blue-900/30 dark:text-blue-300',
            'concluida'    => 'text-emerald-700 bg-emerald-50 dark:bg-emerald-900/30 dark:text-emerald-300',
            'cancelada'    => 'text-rose-700 bg-rose-50 dark:bg-rose-900/30 dark:text-rose-300',
            default        => 'text-slate-500 bg-slate-100',
        };
    }

    public function getIsOverdueAttribute(): bool
    {
        return ! in_array($this->status, ['concluida', 'cancelada'])
            && $this->due_date !== null
            && $this->due_date->isPast();
    }

    public function getIsSelfCreatedAttribute(): bool
    {
        return $this->assigned_by === null || $this->assigned_by === $this->assigned_to;
    }

    public function getSubtasksTotalAttribute(): int
    {
        return $this->subtasks->count();
    }

    public function getSubtasksDoneAttribute(): int
    {
        return $this->subtasks->where('completed', true)->count();
    }

    public function getSubtasksProgressAttribute(): int
    {
        return $this->subtasks_total > 0
            ? (int) round($this->subtasks_done / $this->subtasks_total * 100)
            : 0;
    }

    public function getEstimatedTimeFormattedAttribute(): ?string
    {
        if (! $this->estimated_minutes) {
            return null;
        }
        $h = intdiv($this->estimated_minutes, 60);
        $m = $this->estimated_minutes % 60;
        if ($h > 0 && $m > 0) return "{$h}h {$m}min";
        if ($h > 0) return "{$h}h";
        return "{$m}min";
    }

    public function getRecurrenceLabelAttribute(): ?string
    {
        return match ($this->recurrence) {
            'daily'   => 'Diária',
            'weekly'  => 'Semanal',
            'monthly' => 'Mensal',
            'yearly'  => 'Anual',
            default   => null,
        };
    }

    public function getCommentsCountAttribute(): int
    {
        return $this->comments->count();
    }
}
