<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DpiGoal extends Model
{
    protected $table = 'dpi_goals';

    protected $fillable = [
        'dpi_plan_id', 'name', 'priority', 'nivel_atual', 'nivel_meta', 'order',
    ];

    protected $casts = [
        'nivel_atual' => 'integer',
        'nivel_meta'  => 'integer',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────

    public function plan(): BelongsTo
    {
        return $this->belongsTo(DpiPlan::class, 'dpi_plan_id');
    }

    public function actions(): HasMany
    {
        return $this->hasMany(DpiAction::class)->orderBy('order')->orderBy('id');
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

    public static function nivelLabels(): array
    {
        return [
            1 => 'Básico',
            2 => 'Elementar',
            3 => 'Intermediário',
            4 => 'Avançado',
            5 => 'Expert',
        ];
    }

    public function getNivelAtualLabelAttribute(): string
    {
        return static::nivelLabels()[$this->nivel_atual] ?? "Nível {$this->nivel_atual}";
    }

    public function getNivelMetaLabelAttribute(): string
    {
        return static::nivelLabels()[$this->nivel_meta] ?? "Nível {$this->nivel_meta}";
    }

    /** Progresso percentual com base nos níveis (atual/meta). */
    public function getProgressPercentAttribute(): int
    {
        if ($this->nivel_meta <= 0) return 0;
        return (int) min(100, round(($this->nivel_atual / $this->nivel_meta) * 100));
    }
}
