<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipamentoManutencao extends Model
{
    protected $table = 'equipamento_manutencoes';

    protected $fillable = [
        'equipamento_id',
        'registrado_por',
        'titulo',
        'descricao',
        'tipo',
        'status',
        'data_entrada',
        'data_previsao',
        'data_conclusao',
        'fornecedor',
        'custo_estimado',
        'custo_real',
        'resolucao',
        'observacoes',
    ];

    protected $casts = [
        'data_entrada'    => 'date',
        'data_previsao'   => 'date',
        'data_conclusao'  => 'date',
        'custo_estimado'  => 'decimal:2',
        'custo_real'      => 'decimal:2',
    ];

    // ── Relações ──────────────────────────────────────────────────────────────

    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function registradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registrado_por');
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getTipoLabelAttribute(): string
    {
        return match ($this->tipo) {
            'corretiva'   => 'Corretiva',
            'preventiva'  => 'Preventiva',
            'calibracao'  => 'Calibração',
            default       => $this->tipo,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'aberta'       => 'Aberta',
            'em_andamento' => 'Em andamento',
            'concluida'    => 'Concluída',
            'cancelada'    => 'Cancelada',
            default        => $this->status,
        };
    }
}
