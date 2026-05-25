<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhOnboardingTarefa extends Model
{
    protected $table = 'rh_onboarding_tarefas';

    protected $fillable = [
        'onboarding_id', 'titulo', 'descricao', 'responsavel',
        'status', 'concluido_by', 'concluido_at', 'ordem',
    ];

    protected function casts(): array
    {
        return [
            'concluido_at' => 'datetime',
        ];
    }

    public function onboarding()
    {
        return $this->belongsTo(RhOnboarding::class, 'onboarding_id');
    }

    public function concluidoPor()
    {
        return $this->belongsTo(User::class, 'concluido_by');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function corResponsavel(): string
    {
        return match ($this->responsavel) {
            'rh'         => 'bg-blue-100 text-blue-700',
            'ti'         => 'bg-violet-100 text-violet-700',
            'gestao'     => 'bg-emerald-100 text-emerald-700',
            'financeiro' => 'bg-amber-100 text-amber-700',
            default      => 'bg-gray-100 text-gray-600',
        };
    }

    public function labelResponsavel(): string
    {
        return match ($this->responsavel) {
            'rh'         => 'RH',
            'ti'         => 'TI',
            'gestao'     => 'Gestão',
            'financeiro' => 'Financeiro',
            default      => ucfirst($this->responsavel),
        };
    }
}
