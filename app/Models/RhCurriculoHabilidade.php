<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhCurriculoHabilidade extends Model
{
    protected $table = 'rh_curriculo_habilidades';

    protected $fillable = ['curriculo_id', 'habilidade', 'nivel', 'tipo'];

    public function curriculo()
    {
        return $this->belongsTo(RhCurriculo::class, 'curriculo_id');
    }
}
