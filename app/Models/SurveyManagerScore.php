<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Score consolidado de um gerente em uma pesquisa de clima.
 *
 * Calculado a partir das respostas de perguntas com is_manager_evaluation = true.
 * Suporta dois tipos de pergunta:
 *   - escala (0–10):        usa value_scale diretamente.
 *   - multipla_escolha:     normaliza pela posição da opção na lista (1ª = 10, última = 0).
 *
 * Serve de base para o ranking de gerentes exibido ao DP/RH.
 */
class SurveyManagerScore extends Model
{
    protected $table = 'survey_manager_scores';

    protected $fillable = [
        'survey_id',
        'manager_id',
        'average_score',
        'total_responses',
        'calculated_at',
    ];

    protected $casts = [
        'average_score'   => 'float',
        'total_responses' => 'integer',
        'calculated_at'   => 'datetime',
    ];

    // ─────────────────────────────────────────────────────────────────
    // Relacionamentos
    // ─────────────────────────────────────────────────────────────────

    public function survey(): BelongsTo
    {
        return $this->belongsTo(Survey::class);
    }

    public function manager(): BelongsTo
    {
        return $this->belongsTo(User::class, 'manager_id');
    }

    // ─────────────────────────────────────────────────────────────────
    // Núcleo: conversão de resposta → nota (0–10)
    // ─────────────────────────────────────────────────────────────────

    /**
     * Converte uma resposta em nota normalizada de 0 a 10.
     *
     * - escala (0–10):       retorna value_scale direto.
     * - multipla_escolha:    normaliza pela posição da opção no array.
     *                        1ª opção (índice 0) = 10 (melhor).
     *                        Última opção        = 0  (pior).
     *                        Fórmula: (n - 1 - index) / (n - 1) × 10
     *
     * Retorna null se o tipo não for suportado ou os dados estiverem ausentes.
     */
    public static function resolverNota(SurveyAnswer $answer): ?float
    {
        $question = $answer->question;

        if (! $question) {
            return null;
        }

        if ($question->type === 'escala' && $answer->value_scale !== null) {
            return (float) $answer->value_scale;
        }

        if ($question->type === 'multipla_escolha' && $answer->value_option !== null) {
            $options = $question->options ?? [];
            $n       = count($options);

            if ($n === 0) {
                return null;
            }

            $index = array_search($answer->value_option, $options, strict: true);

            if ($index === false) {
                return null;
            }

            // Com apenas 1 opção não há gradação → nota máxima
            return $n === 1
                ? 10.0
                : round(($n - 1 - $index) / ($n - 1) * 10, 4);
        }

        return null;
    }

    // ─────────────────────────────────────────────────────────────────
    // Recálculo
    // ─────────────────────────────────────────────────────────────────

    /**
     * Recalcula e persiste o score do gerente para uma pesquisa específica.
     *
     * Processa escala e múltipla escolha; texto livre é ignorado pois
     * não possui valor numérico mensurável.
     */
    public static function recalcular(int $surveyId, int $managerId): self
    {
        $answers = SurveyAnswer::with('question:id,type,options')
            ->whereHas('question', fn ($q) => $q
                ->where('survey_id', $surveyId)
                ->where('is_manager_evaluation', true)
                ->whereIn('type', ['escala', 'multipla_escolha'])
            )
            ->where('manager_id', $managerId)
            ->get(['id', 'question_id', 'value_scale', 'value_option']);

        $notas = $answers
            ->map(fn ($a) => static::resolverNota($a))
            ->filter(fn ($n) => $n !== null)
            ->values();

        $count = $notas->count();
        $avg   = $count > 0 ? round($notas->avg(), 2) : null;

        return static::updateOrCreate(
            [
                'survey_id'  => $surveyId,
                'manager_id' => $managerId,
            ],
            [
                'average_score'   => $avg,
                'total_responses' => $count,
                'calculated_at'   => now(),
            ]
        );
    }

    // ─────────────────────────────────────────────────────────────────
    // Acessórios
    // ─────────────────────────────────────────────────────────────────

    /**
     * Nota formatada para exibição (ex: "7,80" ou "—").
     */
    public function getScoreLabelAttribute(): string
    {
        return $this->average_score !== null
            ? number_format($this->average_score, 2, ',', '')
            : '—';
    }
}
