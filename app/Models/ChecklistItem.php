<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ChecklistItem extends Model
{
    protected $table = 'checklist_itens';

    protected $fillable = [
        'titulo',
        'descricao',
        'tipo',
        'categoria',
        'responsavel_padrao',
        'ordem',
        'obrigatorio',
        'ativo',
    ];

    protected $casts = [
        'obrigatorio' => 'boolean',
        'ativo'       => 'boolean',
    ];

    // ── Relações ──────────────────────────────────────────────────────────────

    public function execucoes(): HasMany
    {
        return $this->hasMany(ChecklistExecucao::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeAtivo($query)
    {
        return $query->where('ativo', true);
    }

    public function scopeOnboarding($query)
    {
        return $query->where('tipo', 'onboarding');
    }

    public function scopeOffboarding($query)
    {
        return $query->where('tipo', 'offboarding');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getCategoriaLabelAttribute(): string
    {
        return match ($this->categoria) {
            'documentos'     => 'Documentos',
            'equipamentos'   => 'Equipamentos',
            'acesso_sistemas'=> 'Acesso a Sistemas',
            'treinamentos'   => 'Treinamentos',
            default          => 'Outros',
        };
    }

    public function getResponsavelLabelAttribute(): string
    {
        return match ($this->responsavel_padrao) {
            'rh'         => 'RH/DP',
            'ti'         => 'TI',
            'gestor'     => 'Gestor',
            'funcionario'=> 'Funcionário',
            default      => '—',
        };
    }
}
