<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyAnswer extends Model
{
    protected $table = 'survey_answers';

    protected $fillable = [
        'response_id',
        'question_id',
        'value_scale',
        'value_option',
        'value_text',
    ];

    protected $casts = [
        'value_scale' => 'integer',
    ];

    // ─────────────────────────────────────────────────────────────────
    // Relacionamentos
    // ─────────────────────────────────────────────────────────────────

    public function response(): BelongsTo
    {
        return $this->belongsTo(SurveyResponse::class, 'response_id');
    }

    public function question(): BelongsTo
    {
        return $this->belongsTo(SurveyQuestion::class, 'question_id');
    }

    // ─────────────────────────────────────────────────────────────────
    // Helper: retorna o valor legível conforme o tipo da pergunta
    // ─────────────────────────────────────────────────────────────────

    public function getValueAttribute(): string|int|null
    {
        return $this->value_scale ?? $this->value_option ?? $this->value_text;
    }
}
