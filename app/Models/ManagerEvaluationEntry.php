<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerEvaluationEntry extends Model
{
    protected $table = 'manager_evaluation_entries';

    protected $fillable = [
        'manager_evaluation_id',
        'employee_id',
        'criterion_id',
        'score',
        'comment',
        'calibrated_score',
        'calibration_note',
        'calibrated_by',
        'calibrated_at',
    ];

    protected $casts = [
        'score'           => 'integer',
        'calibrated_score' => 'integer',
        'calibrated_at'   => 'datetime',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(ManagerEvaluation::class, 'manager_evaluation_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(EvaluationCriterion::class, 'criterion_id');
    }

    public function calibratedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'calibrated_by');
    }

    // ── Acessórios ─────────────────────────────────────────────────────

    /**
     * Retorna a nota efetiva: calibrada (se existir) ou original.
     */
    public function getEffectiveScoreAttribute(): int
    {
        return $this->calibrated_score ?? $this->score;
    }

    /**
     * Normaliza o score efetivo para escala 1-5 considerando o tipo de resposta do critério.
     */
    public function getNormalizedScoreAttribute(): ?float
    {
        $criterion = $this->criterion;
        if (! $criterion) {
            return null;
        }

        $effective = $this->effectiveScore;

        return match ($criterion->response_type ?? 'scale') {
            'scale'           => (float) $effective,
            'boolean'         => $effective ? 5.0 : 1.0,
            'multiple_choice' => $this->normalizeMultipleChoice($effective, $criterion),
            default           => (float) $effective,
        };
    }

    /**
     * Label legível do valor selecionado (score original, sem calibração).
     */
    public function getDisplayValueAttribute(): string
    {
        $criterion = $this->criterion;
        if (! $criterion) {
            return (string) $this->score;
        }

        return match ($criterion->response_type ?? 'scale') {
            'scale'           => $this->score . '/5',
            'boolean'         => $this->score ? 'Sim' : 'Não',
            'multiple_choice' => $this->getOptionLabel($this->score, $criterion),
            default           => (string) $this->score,
        };
    }

    /**
     * Label legível do valor efetivo: usa a nota calibrada se existir, senão a original.
     */
    public function getEffectiveDisplayValueAttribute(): string
    {
        $criterion = $this->criterion;
        $effective = $this->effectiveScore;

        if (! $criterion) {
            return (string) $effective;
        }

        return match ($criterion->response_type ?? 'scale') {
            'scale'           => $effective . '/5',
            'boolean'         => $effective ? 'Sim' : 'Não',
            'multiple_choice' => $this->getOptionLabel($effective, $criterion),
            default           => (string) $effective,
        };
    }

    private function normalizeMultipleChoice(int $score, EvaluationCriterion $criterion): float
    {
        $options = $criterion->options ?? [];
        $n       = count($options);

        if ($n <= 1) {
            return 3.0;
        }

        return round(1 + ($score / ($n - 1)) * 4, 2);
    }

    private function getOptionLabel(int $score, EvaluationCriterion $criterion): string
    {
        $options = $criterion->options ?? [];
        return $options[$score] ?? (string) $score;
    }
}
