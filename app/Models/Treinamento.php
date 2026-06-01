<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Treinamento extends Model
{
    protected $fillable = [
        'titulo', 'descricao', 'capa', 'tipo', 'categoria', 'instrutor',
        'carga_horaria', 'nivel', 'status', 'certificado_habilitado',
        'nota_minima_aprovacao', 'inscricao_aberta', 'data_inicio', 'data_fim', 'criado_por',
    ];

    protected $casts = [
        'data_inicio'             => 'date',
        'data_fim'                => 'date',
        'certificado_habilitado'  => 'boolean',
        'inscricao_aberta'        => 'boolean',
    ];

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function aulas(): HasMany
    {
        return $this->hasMany(TreinamentoAula::class)->orderBy('ordem');
    }

    public function teste(): HasOne
    {
        return $this->hasOne(TreinamentoTeste::class);
    }

    public function inscricoes(): HasMany
    {
        return $this->hasMany(TreinamentoInscricao::class);
    }

    public function duvidas(): HasMany
    {
        return $this->hasMany(TreinamentoDuvida::class);
    }

    public function inscricaoDoUsuario(int $userId): ?TreinamentoInscricao
    {
        return $this->inscricoes()->where('user_id', $userId)->first();
    }

    public function getCargaHorariaFormatadaAttribute(): string
    {
        $h = intdiv($this->carga_horaria, 60);
        $m = $this->carga_horaria % 60;
        if ($h > 0 && $m > 0) return "{$h}h {$m}min";
        if ($h > 0) return "{$h}h";
        return "{$m}min";
    }

    public function getNivelCorAttribute(): string
    {
        return match($this->nivel) {
            'basico'         => 'emerald',
            'intermediario'  => 'amber',
            'avancado'       => 'rose',
            default          => 'slate',
        };
    }

    public function getTipoLabelAttribute(): string
    {
        return $this->tipo === 'interno' ? 'Interno' : 'Externo';
    }
}
