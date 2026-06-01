<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreinamentoAulaProgresso extends Model
{
    protected $table = 'treinamento_aula_progresso';

    protected $fillable = [
        'inscricao_id', 'aula_id', 'concluida', 'segundos_assistidos', 'concluida_em',
    ];

    protected $casts = [
        'concluida'    => 'boolean',
        'concluida_em' => 'datetime',
    ];

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(TreinamentoInscricao::class, 'inscricao_id');
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(TreinamentoAula::class, 'aula_id');
    }
}
