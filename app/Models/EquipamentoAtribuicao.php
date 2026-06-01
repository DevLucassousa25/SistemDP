<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipamentoAtribuicao extends Model
{
    protected $table = 'equipamento_atribuicoes';

    protected $fillable = [
        'equipamento_id',
        'user_id',
        'responsavel_id',
        'tipo',
        'data_entrega',
        'data_devolucao',
        'condicao_entrega',
        'condicao_devolucao',
        'observacoes',
        'assinado_funcionario',
        'assinado_em',
    ];

    protected $casts = [
        'data_entrega'        => 'date',
        'data_devolucao'      => 'date',
        'assinado_funcionario'=> 'boolean',
        'assinado_em'         => 'datetime',
    ];

    // ── Relações ──────────────────────────────────────────────────────────────

    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function responsavel(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsavel_id');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getEstaAtivaAttribute(): bool
    {
        return $this->tipo === 'entrega' && is_null($this->data_devolucao);
    }
}
