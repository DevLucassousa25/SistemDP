<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhCurriculoExperiencia extends Model
{
    protected $table = 'rh_curriculo_experiencias';

    protected $fillable = ['curriculo_id', 'empresa', 'cargo', 'descricao', 'data_inicio', 'data_fim', 'atual'];

    protected function casts(): array
    {
        return ['data_inicio' => 'date', 'data_fim' => 'date', 'atual' => 'boolean'];
    }

    public function curriculo()
    {
        return $this->belongsTo(RhCurriculo::class, 'curriculo_id');
    }
}
