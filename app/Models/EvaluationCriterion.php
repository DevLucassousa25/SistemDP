<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationCriterion extends Model
{
    protected $table = 'evaluation_criteria';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'is_default',
        'order',
        'weight',
        'response_type',
        'options',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'is_default'    => 'boolean',
        'weight'        => 'float',
        'options'       => 'array',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function entries(): HasMany
    {
        return $this->hasMany(ManagerEvaluationEntry::class, 'criterion_id');
    }

    public function selfEntries(): HasMany
    {
        return $this->hasMany(SelfEvaluationEntry::class, 'criterion_id');
    }

    // ── Acessórios ─────────────────────────────────────────────────────

    public function getResponseTypeLabelAttribute(): string
    {
        return match ($this->response_type ?? 'scale') {
            'scale'           => 'Escala 1–5',
            'boolean'         => 'Sim / Não',
            'multiple_choice' => 'Múltipla escolha',
            default           => 'Escala 1–5',
        };
    }

    public function getWeightLabelAttribute(): string
    {
        $w = (float) ($this->weight ?? 1.0);
        return number_format($w, 1, ',', '');
    }
}
