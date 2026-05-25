<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhCandidatoResposta extends Model
{
    protected $table = 'rh_candidato_respostas';

    protected $fillable = ['candidato_teste_id', 'questao_id', 'opcao_id', 'resposta_discursiva', 'correta', 'nota_obtida'];

    protected function casts(): array
    {
        return ['correta' => 'boolean', 'nota_obtida' => 'decimal:2'];
    }

    public function candidatoTeste()
    {
        return $this->belongsTo(RhCandidatoTeste::class, 'candidato_teste_id');
    }

    public function questao()
    {
        return $this->belongsTo(RhQuestao::class, 'questao_id');
    }

    public function opcao()
    {
        return $this->belongsTo(RhOpcao::class, 'opcao_id');
    }
}
