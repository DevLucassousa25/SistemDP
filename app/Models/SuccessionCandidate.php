<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuccessionCandidate extends Model
{
    protected $table = 'succession_candidates';

    protected $fillable = [
        'position_id', 'user_id', 'readiness', 'priority',
        'dpi_plan_id', 'notes', 'created_by',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function position(): BelongsTo
    {
        return $this->belongsTo(SuccessionPosition::class, 'position_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function dpiPlan(): BelongsTo
    {
        return $this->belongsTo(DpiPlan::class, 'dpi_plan_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Acessórios ─────────────────────────────────────────────────────

    public function getReadinessLabelAttribute(): string
    {
        return match ($this->readiness) {
            'pronto_agora'  => 'Pronto agora',
            '6_a_12_meses'  => '6 a 12 meses',
            '1_a_3_anos'    => '1 a 3 anos',
            default         => $this->readiness,
        };
    }

    public function getReadinessColorAttribute(): string
    {
        return match ($this->readiness) {
            'pronto_agora' => 'emerald',
            '6_a_12_meses' => 'amber',
            '1_a_3_anos'   => 'blue',
            default        => 'slate',
        };
    }

    public function getPriorityLabelAttribute(): string
    {
        return match ($this->priority) {
            1 => '1º',
            2 => '2º',
            3 => '3º',
            default => $this->priority . 'º',
        };
    }

    /**
     * Score de prontidão calculado (0–100):
     *  - 40% nota da avaliação do gestor (ManagerEvaluation.final_score, escala 1–5 → 0–100)
     *  - 40% progresso do DPI vinculado (DpiPlan.progress_percent)
     *  - 20% readiness declarada (pronto=100, 6-12=60, 1-3=30)
     */
    public function getReadinessScoreAttribute(): int
    {
        $evalScore = 0;
        $dpiScore  = 0;

        // Nota de avaliação mais recente do gestor
        $eval = ManagerEvaluation::where('manager_id', $this->user_id)
            ->whereNotNull('final_score')
            ->latest('completed_at')
            ->first();

        if ($eval && $eval->final_score !== null) {
            $evalScore = round(($eval->final_score / 5) * 100);
        }

        // Progresso do DPI vinculado (ou plano mais recente do usuário)
        $dpiPlan = $this->dpi_plan_id
            ? $this->dpiPlan
            : DpiPlan::where('user_id', $this->user_id)
                ->whereIn('status', ['aprovado', 'concluido', 'enviado'])
                ->latest()
                ->first();

        if ($dpiPlan) {
            $dpiScore = $dpiPlan->progress_percent ?? 0;
        }

        $readinessBase = match ($this->readiness) {
            'pronto_agora' => 100,
            '6_a_12_meses' => 60,
            '1_a_3_anos'   => 30,
            default        => 0,
        };

        return (int) round(($evalScore * 0.4) + ($dpiScore * 0.4) + ($readinessBase * 0.2));
    }

    public function getReadinessScoreColorAttribute(): string
    {
        $s = $this->readinessScore;
        if ($s >= 75) return 'emerald';
        if ($s >= 50) return 'amber';
        return 'rose';
    }
}
