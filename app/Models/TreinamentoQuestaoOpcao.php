<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreinamentoQuestaoOpcao extends Model
{
    protected $table = 'treinamento_questao_opcoes';

    protected $fillable = [
        'questao_id', 'texto', 'correta', 'ordem',
    ];

    protected $casts = [
        'correta' => 'boolean',
    ];

    public function questao(): BelongsTo
    {
        return $this->belongsTo(TreinamentoQuestao::class, 'questao_id');
    }
}
