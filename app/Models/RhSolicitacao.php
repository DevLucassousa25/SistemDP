<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class RhSolicitacao extends Model
{
    protected $table = 'rh_solicitacoes';

    protected $fillable = [
        'user_id',
        'tipo',
        'descricao',
        'status',
        'prazo',
        'rh_user_id',
        'observacao_rh',
        'arquivo_url',
        'concluida_em',
    ];

    protected function casts(): array
    {
        return [
            'prazo'        => 'date',
            'concluida_em' => 'datetime',
        ];
    }

    // ── Labels ────────────────────────────────────────────────────────

    public static array $tipoLabels = [
        'declaracao_emprego'    => 'Declaração de Vínculo Empregatício',
        'declaracao_salario'    => 'Declaração de Salário',
        'declaracao_ferias'     => 'Declaração de Férias',
        'declaracao_quitacao'   => 'Declaração de Quitação',
        'comprovante_pagamento' => 'Comprovante de Pagamento',
        'comprovante_fgts'      => 'Comprovante FGTS',
        'informe_rendimentos'   => 'Informe de Rendimentos',
        'segunda_via_cracha'    => '2ª Via de Crachá',
        'outros'                => 'Outros',
    ];

    public static array $statusLabels = [
        'pendente'    => 'Pendente',
        'em_andamento' => 'Em Andamento',
        'concluida'   => 'Concluída',
        'cancelada'   => 'Cancelada',
    ];

    public function getTipoLabelAttribute(): string
    {
        return self::$tipoLabels[$this->tipo] ?? $this->tipo;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::$statusLabels[$this->status] ?? $this->status;
    }

    public function getVencidaAttribute(): bool
    {
        return $this->prazo
            && $this->prazo->isPast()
            && ! in_array($this->status, ['concluida', 'cancelada']);
    }

    // ── Relations ─────────────────────────────────────────────────────

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rhUser()
    {
        return $this->belongsTo(User::class, 'rh_user_id');
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopePendente(Builder $q): Builder
    {
        return $q->where('status', 'pendente');
    }

    public function scopeEmAndamento(Builder $q): Builder
    {
        return $q->where('status', 'em_andamento');
    }

    public function scopeAbertas(Builder $q): Builder
    {
        return $q->whereIn('status', ['pendente', 'em_andamento']);
    }

    public function scopeVencidas(Builder $q): Builder
    {
        return $q->whereNotNull('prazo')
                 ->where('prazo', '<', now()->toDateString())
                 ->whereNotIn('status', ['concluida', 'cancelada']);
    }
}
