<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhCurriculoFormacao extends Model
{
    protected $table = 'rh_curriculo_formacoes';

    protected $fillable = ['curriculo_id', 'instituicao', 'curso', 'nivel', 'ano_inicio', 'ano_conclusao', 'em_andamento'];

    protected function casts(): array
    {
        return ['em_andamento' => 'boolean'];
    }

    public function curriculo()
    {
        return $this->belongsTo(RhCurriculo::class, 'curriculo_id');
    }
}
