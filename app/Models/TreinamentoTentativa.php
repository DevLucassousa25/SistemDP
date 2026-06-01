<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreinamentoTentativa extends Model
{
    protected $table = 'treinamento_tentativas';

    protected $fillable = [
        'inscricao_id', 'teste_id', 'numero', 'nota', 'aprovado', 'iniciada_em', 'finalizada_em',
    ];

    protected $casts = [
        'aprovado'      => 'boolean',
        'nota'          => 'decimal:2',
        'iniciada_em'   => 'datetime',
        'finalizada_em' => 'datetime',
    ];

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(TreinamentoInscricao::class, 'inscricao_id');
    }

    public function teste(): BelongsTo
    {
        return $this->belongsTo(TreinamentoTeste::class, 'teste_id');
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(TreinamentoResposta::class, 'tentativa_id');
    }
}
