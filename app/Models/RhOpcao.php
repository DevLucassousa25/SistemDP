<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhOpcao extends Model
{
    protected $table = 'rh_opcoes';

    protected $fillable = ['questao_id', 'texto', 'correta', 'ordem'];

    protected function casts(): array
    {
        return ['correta' => 'boolean'];
    }

    public function questao()
    {
        return $this->belongsTo(RhQuestao::class, 'questao_id');
    }
}
