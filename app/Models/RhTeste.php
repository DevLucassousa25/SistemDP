<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhTeste extends Model
{
    protected $table = 'rh_testes';

    protected $fillable = ['created_by', 'vaga_id', 'data_inicio', 'data_fim', 'titulo', 'descricao', 'instrucoes', 'tempo_limite_minutos', 'randomizar_questoes', 'randomizar_opcoes', 'nota_aprovacao', 'ativo'];

    protected function casts(): array
    {
        return [
            'randomizar_questoes' => 'boolean',
            'randomizar_opcoes'   => 'boolean',
            'ativo'               => 'boolean',
            'data_inicio'         => 'datetime',
            'data_fim'            => 'datetime',
        ];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function vaga()
    {
        return $this->belongsTo(RhVaga::class, 'vaga_id');
    }

    public function questoes()
    {
        return $this->hasMany(RhQuestao::class, 'teste_id')->orderBy('ordem');
    }

    public function tentativas()
    {
        return $this->hasMany(RhCandidatoTeste::class, 'teste_id');
    }
}
