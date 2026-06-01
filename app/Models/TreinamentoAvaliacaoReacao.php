<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreinamentoAvaliacaoReacao extends Model
{
    protected $table = 'treinamento_avaliacoes_reacao';

    protected $fillable = [
        'inscricao_id', 'nota', 'comentario',
    ];

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(TreinamentoInscricao::class, 'inscricao_id');
    }
}
