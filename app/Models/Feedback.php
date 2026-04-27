<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Feedback extends Model
{
    protected $table = 'feedbacks';

    protected $fillable = [
        'employee_id',
        'evaluator_id',
        'type',
        'category',
        'severity',
        'rating',
        'occurred_at',
        'message',
        'template',
        'template_data',
        'private_note',
        'action_plan',
        'action_plan_deadline',
        'action_plan_responsible_id',
        'status',
        'is_anonymous',
        'attachments',
    ];

    protected $casts = [
        'occurred_at'          => 'date',
        'action_plan_deadline' => 'date',
        'is_anonymous'         => 'boolean',
        'attachments'          => 'array',
        'template_data'        => 'array',
        'rating'               => 'integer',
    ];

    // ─────────────────────────────────────────────────────────────────
    // Relacionamentos
    // ─────────────────────────────────────────────────────────────────

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function evaluator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'evaluator_id');
    }

    public function actionPlanResponsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'action_plan_responsible_id');
    }

    public function statusHistory(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeedbackStatusHistory::class)->orderBy('created_at');
    }

    public function comments(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeedbackComment::class)->orderBy('created_at');
    }

    public function actionTasks(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(FeedbackActionTask::class)->orderBy('created_at');
    }

    // ─────────────────────────────────────────────────────────────────
    // Acessórios
    // ─────────────────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'reconhecimento'   => 'Reconhecimento',
            'sugestao'         => 'Sugestão',
            'alerta'           => 'Alerta',
            'avaliacao_gestor' => 'Avaliação do Gestor',
            default            => ucfirst((string) $this->type),
        };
    }

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'comportamento'      => 'Comportamento',
            'desempenho'         => 'Desempenho',
            'pontualidade'       => 'Pontualidade',
            'trabalho_em_equipe' => 'Trabalho em equipe',
            'comunicacao'        => 'Comunicação',
            'lideranca'          => 'Liderança',
            'outros'             => 'Outros',
            default              => ucfirst((string) $this->category),
        };
    }

    public function getSeverityLabelAttribute(): ?string
    {
        return match ($this->severity) {
            'baixo'  => 'Baixo',
            'medio'  => 'Médio',
            'alto'   => 'Alto',
            'critico'=> 'Crítico',
            default  => null,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aberto'             => 'Aberto',
            'em_analise'         => 'Em análise',
            'aguardando_plano'   => 'Aguard. plano',
            'plano_em_andamento' => 'Plano em andamento',
            'resolvido'          => 'Resolvido',
            'arquivado'          => 'Arquivado',
            default              => ucfirst((string) $this->status),
        };
    }

    public function getEvaluatorNameAttribute(): string
    {
        if ($this->is_anonymous) {
            return 'Anônimo';
        }

        return $this->evaluator?->name ?? '—';
    }

    // Cor do tipo para badges inline (hex)
    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'reconhecimento'   => '#10b981', // emerald
            'sugestao'         => '#3b82f6', // blue
            'alerta'           => '#ef4444', // red
            'avaliacao_gestor' => '#8b5cf6', // violet
            default            => '#64748b',
        };
    }

    // Cor da gravidade para badges inline (hex)
    public function getSeverityColorAttribute(): string
    {
        return match ($this->severity) {
            'baixo'  => '#10b981',
            'medio'  => '#f59e0b',
            'alto'   => '#f97316',
            'critico'=> '#dc2626',
            default  => '#64748b',
        };
    }
}
