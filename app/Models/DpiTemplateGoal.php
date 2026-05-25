<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DpiTemplateGoal extends Model
{
    protected $table = 'dpi_template_goals';

    protected $fillable = [
        'dpi_template_id', 'name', 'priority', 'nivel_atual', 'nivel_meta', 'order',
    ];

    protected $casts = [
        'nivel_atual' => 'integer',
        'nivel_meta'  => 'integer',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────

    public function template(): BelongsTo
    {
        return $this->belongsTo(DpiTemplate::class, 'dpi_template_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(DpiTemplateAction::class, 'dpi_template_goal_id')->orderBy('order')->orderBy('id');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            'alta'  => 'Alta',
            'media' => 'Média',
            'baixa' => 'Baixa',
            default => $this->priority,
        };
    }

    public function getPriorityColorAttribute(): string
    {
        return match ($this->priority) {
            'alta'  => 'text-red-500',
            'media' => 'text-amber-500',
            'baixa' => 'text-slate-400',
            default => 'text-slate-400',
        };
    }
}
