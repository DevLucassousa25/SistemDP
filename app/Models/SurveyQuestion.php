<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SurveyQuestion extends Model
{
    protected $table = 'survey_questions';

    protected $fillable = [
        'survey_id',
        'question',
        'type',
        'options',
        'required',
        'order',
        'is_manager_evaluation',
    ];

    protected $casts = [
        'options'               => 'array',
        'required'              => 'boolean',
        'is_manager_evaluation' => 'boolean',
    ];

    // ─────────────────────────────────────────────────────────────────
    // Relacionamentos
    // ─────────────────────────────────────────────────────────────────

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function answers(): HasMany
    {
        return $this->hasMany(SurveyAnswer::class, 'question_id');
    }

    // ─────────────────────────────────────────────────────────────────
    // Acessórios
    // ─────────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'escala'           => 'Escala 0–10',
            'multipla_escolha' => 'Múltipla escolha',
            'texto_livre'      => 'Texto livre',
            default            => ucfirst((string) $this->type),
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'escala'           => 'lucide-bar-chart-2',
            'multipla_escolha' => 'lucide-list-checks',
            'texto_livre'      => 'lucide-align-left',
            default            => 'lucide-circle-help',
        };
    }
}
