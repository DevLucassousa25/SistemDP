<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class Survey extends Model
{
    protected $table = 'surveys';

    protected $fillable = [
        'title',
        'description',
        'status',
        'target_audience',
        'target_department_ids',
        'is_anonymous',
        'start_date',
        'end_date',
        'created_by',
        'evaluation_cycle_id',
    ];

    protected $casts = [
        'target_department_ids' => 'array',
        'is_anonymous'          => 'boolean',
        'start_date'            => 'date',
        'end_date'              => 'date',
    ];

    // ─────────────────────────────────────────────────────────────────
    // Relacionamentos
    // ─────────────────────────────────────────────────────────────────
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function evaluationCycle(): BelongsTo
    {
        return $this->belongsTo(\App\Models\EvaluationCycle::class, 'evaluation_cycle_id');
    }

    public function questions(): HasMany
    {
        return $this->hasMany(SurveyQuestion::class)->orderBy('order')->orderBy('id');
    }

    public function responses(): HasMany
    {
        return $this->hasMany(SurveyResponse::class);
    }

    public function managerScores(): HasMany
    {
        return $this->hasMany(SurveyManagerScore::class);
    }

    /**
     * Indica se esta pesquisa possui ao menos uma pergunta de avaliação de gestor.
     */
    public function temPerguntasDeGestor(): bool
    {
        return $this->questions()->where('is_manager_evaluation', true)->exists();
    }

    // ─────────────────────────────────────────────────────────────────
    // Helpers de resposta
    // ─────────────────────────────────────────────────────────────────

    /**
     * Verifica se o usuário autenticado já respondeu esta pesquisa.
     */
    public function jaRespondeu(?int $userId = null): bool
    {
        $uid = $userId ?? Auth::id();

        if (! $uid) {
            return false;
        }

        return $this->responses()
            ->where('user_id', $uid)
            ->whereNotNull('completed_at')
            ->exists();
    }

    /**
     * Verifica se o usuário pode responder à pesquisa
     * (ativa hoje + no público-alvo).
     */
    public function podeResponder(\App\Models\User $user): bool
    {
        if (! $this->isAtivaHoje()) {
            return false;
        }

        if ($this->target_audience === 'todos') {
            return true;
        }

        if ($this->target_audience === 'departamentos' && $user->department_id) {
            return in_array(
                (int) $user->department_id,
                array_map('intval', $this->target_department_ids ?? [])
            );
        }

        return false;
    }

    // ─────────────────────────────────────────────────────────────────
    // Acessórios
    // ─────────────────────────────────────────────────────────────────
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'rascunho'  => 'Rascunho',
            'ativa'     => 'Ativa',
            'encerrada' => 'Encerrada',
            default     => ucfirst((string) $this->status),
        };
    }

    public function getAudienceLabelAttribute(): string
    {
        return match ($this->target_audience) {
            'todos'         => 'Todos os colaboradores',
            'departamentos' => 'Departamentos selecionados',
            default         => '—',
        };
    }

    /**
     * Verifica se a pesquisa está ativa hoje (dentro do intervalo).
     */
    public function isAtivaHoje(): bool
    {
        if ($this->status !== 'ativa') {
            return false;
        }

        $hoje = Carbon::today();

        if ($this->start_date && $hoje->lt($this->start_date)) {
            return false;
        }

        if ($this->end_date && $hoje->gt($this->end_date)) {
            return false;
        }

        return true;
    }
}
