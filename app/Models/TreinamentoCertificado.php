<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreinamentoCertificado extends Model
{
    protected $table = 'treinamento_certificados';

    protected $fillable = [
        'inscricao_id', 'codigo', 'arquivo', 'emitido_em',
    ];

    protected $casts = [
        'emitido_em' => 'datetime',
    ];

    public function inscricao(): BelongsTo
    {
        return $this->belongsTo(TreinamentoInscricao::class, 'inscricao_id');
    }
}
