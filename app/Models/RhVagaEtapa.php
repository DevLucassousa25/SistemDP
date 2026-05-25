<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhVagaEtapa extends Model
{
    protected $table = 'rh_vaga_etapas';

    protected $fillable = ['vaga_id', 'nome', 'ordem', 'cor', 'is_aprovado', 'is_reprovado', 'is_banco_talentos'];

    protected function casts(): array
    {
        return ['is_aprovado' => 'boolean', 'is_reprovado' => 'boolean', 'is_banco_talentos' => 'boolean'];
    }

    public function vaga()
    {
        return $this->belongsTo(RhVaga::class, 'vaga_id');
    }

    public function candidaturas()
    {
        return $this->hasMany(RhCandidatura::class, 'etapa_id');
    }
}
