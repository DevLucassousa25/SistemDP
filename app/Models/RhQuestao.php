<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhQuestao extends Model
{
    protected $table = 'rh_questoes';

    protected $fillable = ['teste_id', 'enunciado', 'tipo', 'peso', 'ordem'];

    public function teste()
    {
        return $this->belongsTo(RhTeste::class, 'teste_id');
    }

    public function opcoes()
    {
        return $this->hasMany(RhOpcao::class, 'questao_id')->orderBy('ordem');
    }

    public function respostas()
    {
        return $this->hasMany(RhCandidatoResposta::class, 'questao_id');
    }
}
