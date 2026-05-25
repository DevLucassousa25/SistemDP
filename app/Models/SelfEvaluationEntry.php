<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SelfEvaluationEntry extends Model
{
    protected $table = 'self_evaluation_entries';

    protected $fillable = [
        'self_evaluation_id',
        'criterion_id',
        'score',
        'comment',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function selfEvaluation(): BelongsTo
    {
        return $this->belongsTo(SelfEvaluation::class, 'self_evaluation_id');
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(EvaluationCriterion::class, 'criterion_id');
    }

    // ── Acessórios ─────────────────────────────────────────────────────

    /**
     * Retorna a nota normalizada para escala 1-5, independente do tipo de resposta.
     *  - scale:           retorna o score diretamente (1–5)
     *  - boolean:         0 → 1.0 (Não), 1 → 5.0 (Sim)
     *  - multiple_choice: converte índice para escala 1-5 pelo número de opções
     */
    public function getNormalizedScoreAttribute(): ?float
    {
        $criterion = $this->criterion;
        if (! $criterion) {
            return null;
        }

        return match ($criterion->response_type) {
            'scale'           => (float) $this->score,
            'boolean'         => $this->score ? 5.0 : 1.0,
            'multiple_choice' => $this->normalizeMultipleChoice(),
            default           => (float) $this->score,
        };
    }

    /**
     * Label legível do valor selecionado.
     */
    public function getDisplayValueAttribute(): string
    {
        $criterion = $this->criterion;
        if (! $criterion) {
            return (string) $this->score;
        }

        return match ($criterion->response_type) {
            'scale'           => (string) $this->score . '/5',
            'boolean'         => $this->score ? 'Sim' : 'Não',
            'multiple_choice' => $this->getOptionLabel(),
            default           => (string) $this->score,
        };
    }

    private function normalizeMultipleChoice(): float
    {
        $options = $this->criterion->options ?? [];
        $n       = count($options);

        if ($n <= 1) {
            return 3.0;
        }

        // Índice 0 = pior opção → 1; último índice = melhor → 5
        $index = (int) $this->score;
        return round(1 + ($index / ($n - 1)) * 4, 2);
    }

    private function getOptionLabel(): string
    {
        $options = $this->criterion->options ?? [];
        $index   = (int) $this->score;

        return $options[$index] ?? (string) $this->score;
    }
}
