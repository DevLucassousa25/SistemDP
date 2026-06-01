<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreinamentoQuestao extends Model
{
    protected $table = 'treinamento_questoes';

    protected $fillable = [
        'teste_id', 'enunciado', 'tipo', 'ordem', 'pontos',
    ];

    public function teste(): BelongsTo
    {
        return $this->belongsTo(TreinamentoTeste::class, 'teste_id');
    }

    public function opcoes(): HasMany
    {
        return $this->hasMany(TreinamentoQuestaoOpcao::class, 'questao_id')->orderBy('ordem');
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(TreinamentoResposta::class, 'questao_id');
    }
}
