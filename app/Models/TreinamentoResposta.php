<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreinamentoResposta extends Model
{
    protected $table = 'treinamento_respostas';

    protected $fillable = [
        'tentativa_id', 'questao_id', 'opcao_id', 'correta',
    ];

    protected $casts = [
        'correta' => 'boolean',
    ];

    public function tentativa(): BelongsTo
    {
        return $this->belongsTo(TreinamentoTentativa::class, 'tentativa_id');
    }

    public function questao(): BelongsTo
    {
        return $this->belongsTo(TreinamentoQuestao::class, 'questao_id');
    }

    public function opcao(): BelongsTo
    {
        return $this->belongsTo(TreinamentoQuestaoOpcao::class, 'opcao_id');
    }
}
