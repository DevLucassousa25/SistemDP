<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreinamentoTeste extends Model
{
    protected $table = 'treinamento_testes';

    protected $fillable = [
        'treinamento_id', 'titulo', 'descricao', 'nota_minima',
        'tentativas_maximas', 'tempo_limite', 'embaralhar_questoes',
    ];

    protected $casts = [
        'embaralhar_questoes' => 'boolean',
    ];

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
    }

    public function questoes(): HasMany
    {
        return $this->hasMany(TreinamentoQuestao::class, 'teste_id')->orderBy('ordem');
    }

    public function tentativas(): HasMany
    {
        return $this->hasMany(TreinamentoTentativa::class, 'teste_id');
    }
}
