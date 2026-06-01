<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class TreinamentoInscricao extends Model
{
    protected $table = 'treinamento_inscricoes';

    protected $fillable = [
        'treinamento_id', 'user_id', 'status', 'progresso', 'concluido_em', 'prazo_conclusao',
    ];

    protected $casts = [
        'concluido_em'   => 'datetime',
        'prazo_conclusao' => 'date',
    ];

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function progressoAulas(): HasMany
    {
        return $this->hasMany(TreinamentoAulaProgresso::class, 'inscricao_id');
    }

    public function tentativas(): HasMany
    {
        return $this->hasMany(TreinamentoTentativa::class, 'inscricao_id');
    }

    public function certificado(): HasOne
    {
        return $this->hasOne(TreinamentoCertificado::class, 'inscricao_id');
    }

    public function avaliacaoReacao(): HasOne
    {
        return $this->hasOne(TreinamentoAvaliacaoReacao::class, 'inscricao_id');
    }

    public function tentativaAprovada(): ?TreinamentoTentativa
    {
        return $this->tentativas()->where('aprovado', true)->latest()->first();
    }

    public function recalcularProgresso(): void
    {
        $totalAulas = $this->treinamento->aulas()->count();
        if ($totalAulas === 0) {
            $this->progresso = 0;
            $this->save();
            return;
        }
        $concluidas = $this->progressoAulas()->where('concluida', true)->count();
        $this->progresso = (int) round(($concluidas / $totalAulas) * 100);
        if ($this->progresso >= 100 && $this->status === 'em_andamento') {
            $this->status = 'concluido';
            $this->concluido_em = now();
        }
        $this->save();
    }
}
