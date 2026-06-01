<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Equipamento extends Model
{
    protected $fillable = [
        'nome',
        'categoria',
        'descricao',
        'numero_serie',
        'codigo_patrimonio',
        'marca',
        'modelo',
        'status',
        'data_aquisicao',
        'data_garantia',
        'local',
        'valor',
        'taxa_depreciacao',
        'observacoes',
        'cadastrado_por',
    ];

    protected $casts = [
        'data_aquisicao'   => 'date',
        'data_garantia'    => 'date',
        'valor'            => 'decimal:2',
        'taxa_depreciacao' => 'decimal:2',
    ];

    // ── Relações ──────────────────────────────────────────────────────────────

    public function cadastradoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'cadastrado_por');
    }

    public function atribuicoes(): HasMany
    {
        return $this->hasMany(EquipamentoAtribuicao::class);
    }

    public function atribuicaoAtiva(): HasMany
    {
        return $this->hasMany(EquipamentoAtribuicao::class)
                    ->whereNull('data_devolucao')
                    ->where('tipo', 'entrega');
    }

    public function manutencoes(): HasMany
    {
        return $this->hasMany(EquipamentoManutencao::class);
    }

    public function inventarioItens(): HasMany
    {
        return $this->hasMany(EquipamentoInventarioItem::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────────────

    public function scopeDisponivel($query)
    {
        return $query->where('status', 'disponivel');
    }

    public function scopeEmUso($query)
    {
        return $query->where('status', 'em_uso');
    }

    public function scopeGarantiaVencendo($query, int $dias = 30)
    {
        return $query->whereNotNull('data_garantia')
                     ->whereDate('data_garantia', '>=', now())
                     ->whereDate('data_garantia', '<=', now()->addDays($dias));
    }

    public function scopeGarantiaVencida($query)
    {
        return $query->whereNotNull('data_garantia')
                     ->whereDate('data_garantia', '<', now());
    }

    // ── Helpers ───────────────────────────────────────────────────────────────

    public function getCategoriaLabelAttribute(): string
    {
        return match ($this->categoria) {
            'notebook'  => 'Notebook',
            'desktop'   => 'Desktop',
            'monitor'   => 'Monitor',
            'teclado'   => 'Teclado',
            'mouse'     => 'Mouse',
            'headset'   => 'Headset',
            'cracha'    => 'Crachá',
            'epi'       => 'EPI',
            'cadeira'   => 'Cadeira',
            'celular'   => 'Celular',
            default     => 'Outros',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'disponivel'  => 'Disponível',
            'em_uso'      => 'Em Uso',
            'manutencao'  => 'Manutenção',
            'descartado'  => 'Descartado',
            default       => $this->status,
        };
    }

    /**
     * Valor atual estimado após depreciação.
     * Usa taxa_depreciacao (% ao ano) ou fallback por categoria.
     */
    public function getValorAtualAttribute(): ?float
    {
        if (! $this->valor || ! $this->data_aquisicao) return null;

        $taxa = $this->taxa_depreciacao ?? $this->taxaDepreciacaoPadrao();
        if (! $taxa) return (float) $this->valor;

        $anos = $this->data_aquisicao->floatDiffInYears(now());
        $fator = pow(1 - ($taxa / 100), $anos);
        return max(0, round((float) $this->valor * $fator, 2));
    }

    public function getDepreciacaoPercentualAttribute(): ?float
    {
        if (! $this->valor || ! $this->data_aquisicao) return null;
        $atual = $this->valor_atual;
        if ($atual === null) return null;
        return round((1 - $atual / (float) $this->valor) * 100, 1);
    }

    public function getGarantiaStatusAttribute(): string
    {
        if (! $this->data_garantia) return 'sem_garantia';
        if ($this->data_garantia->isPast()) return 'vencida';
        if ($this->data_garantia->diffInDays(now()) <= 0 && $this->data_garantia->isFuture()) {
            $dias = now()->diffInDays($this->data_garantia);
            if ($dias <= 30) return 'vencendo';
        }
        $dias = now()->diffInDays($this->data_garantia);
        if ($dias <= 30) return 'vencendo';
        return 'valida';
    }

    private function taxaDepreciacaoPadrao(): float
    {
        return match ($this->categoria) {
            'notebook', 'desktop' => 20.0,
            'celular'             => 25.0,
            'monitor'             => 15.0,
            'teclado', 'mouse', 'headset' => 20.0,
            'cadeira'             => 10.0,
            'epi'                 => 20.0,
            default               => 10.0,
        };
    }
}
