<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasManyThrough;

class DpiPlan extends Model
{
    protected $table = 'dpi_plans';

    protected $fillable = [
        'user_id', 'created_by', 'year', 'status',
        'manager_feedback', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'reviewed_at' => 'datetime',
        'year'        => 'integer',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(DpiGoal::class)->orderBy('order')->orderBy('id');
    }

    public function actions(): HasManyThrough
    {
        return $this->hasManyThrough(DpiAction::class, DpiGoal::class);
    }

    // ── Helpers de origem ─────────────────────────────────────────────

    /**
     * O plano foi criado pelo próprio gerente do departamento
     * (não pelo RH/DP). Isso determina se precisa de aprovação extra.
     */
    public function wasCreatedByManager(): bool
    {
        if (! $this->created_by) return false;
        $creator = $this->creator;
        return $creator && $creator->isGerente();
    }

    public function wasCreatedByRh(): bool
    {
        if (! $this->created_by) return false;
        $creator = $this->creator;
        return $creator && ($creator->isRhOuDp() || $creator->isAdmin());
    }

    // ── Helpers de papel do dono ──────────────────────────────────────

    /**
     * O plano pertence a um Gestor (Gerente de setor).
     * Determina que a aprovação final é responsabilidade do RH.
     */
    public function isPlanForGerente(): bool
    {
        return $this->user && $this->user->isGerente();
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getStatusLabelAttribute(): string
    {
        if ($this->isPlanForGerente()) {
            return match ($this->status) {
                'rascunho'  => 'Em preparação',
                'aprovado'  => 'Em execução',
                'enviado'   => 'Aguardando RH',
                'reprovado' => 'Devolvido pelo RH',
                'concluido' => 'Concluído',
                default     => $this->status,
            };
        }

        return match ($this->status) {
            'rascunho'  => 'Em preparação',
            'enviado'   => 'Aguardando Gerente',
            'aprovado'  => 'Ativo',
            'reprovado' => 'Devolvido para revisão',
            'concluido' => 'Concluído',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'rascunho'  => 'slate',
            'enviado'   => 'amber',
            'aprovado'  => 'emerald',
            'reprovado' => 'red',
            'concluido' => 'teal',
            default     => 'slate',
        };
    }

    public function getProgressPercentAttribute(): int
    {
        $goals = $this->goals;
        if ($goals->isEmpty()) return 0;

        $total = $goals->sum(fn ($g) => $g->nivel_meta > 0
            ? min(100, round(($g->nivel_atual / $g->nivel_meta) * 100))
            : 0
        );

        return (int) round($total / $goals->count());
    }
}
