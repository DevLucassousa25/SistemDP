<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EquipamentoInventario extends Model
{
    protected $table = 'equipamento_inventarios';

    protected $fillable = [
        'criado_por',
        'titulo',
        'status',
        'data_inicio',
        'data_conclusao',
        'observacoes',
    ];

    protected $casts = [
        'data_inicio'     => 'date',
        'data_conclusao'  => 'date',
    ];

    public function criadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function itens(): HasMany
    {
        return $this->hasMany(EquipamentoInventarioItem::class, 'inventario_id');
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'em_andamento' => 'Em andamento',
            'concluido'    => 'Concluído',
            'cancelado'    => 'Cancelado',
            default        => $this->status,
        };
    }

    public function getProgressoAttribute(): array
    {
        $total      = $this->itens->count();
        $conferidos = $this->itens->whereNotIn('status', ['pendente'])->count();
        $encontrados  = $this->itens->where('status', 'encontrado')->count();
        $naoEncontrados = $this->itens->where('status', 'nao_encontrado')->count();
        $divergencias = $this->itens->where('status', 'divergencia')->count();

        return compact('total', 'conferidos', 'encontrados', 'naoEncontrados', 'divergencias');
    }
}
