<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EquipamentoInventarioItem extends Model
{
    protected $table = 'equipamento_inventario_itens';

    protected $fillable = [
        'inventario_id',
        'equipamento_id',
        'conferido_por',
        'status',
        'conferido_at',
        'observacao',
    ];

    protected $casts = [
        'conferido_at' => 'datetime',
    ];

    public function inventario(): BelongsTo
    {
        return $this->belongsTo(EquipamentoInventario::class);
    }

    public function equipamento(): BelongsTo
    {
        return $this->belongsTo(Equipamento::class);
    }

    public function conferidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'conferido_por');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendente'       => 'Pendente',
            'encontrado'     => 'Encontrado',
            'nao_encontrado' => 'Não encontrado',
            'divergencia'    => 'Divergência',
            default          => $this->status,
        };
    }
}
