<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhCandidatura extends Model
{
    protected $table = 'rh_candidaturas';

    protected $fillable = ['curriculo_id', 'vaga_id', 'etapa_id', 'status', 'nota_final', 'ranking_posicao', 'aprovado_at', 'pipeline_ordem'];

    protected function casts(): array
    {
        return ['aprovado_at' => 'datetime', 'nota_final' => 'decimal:2'];
    }

    public function curriculo()
    {
        return $this->belongsTo(RhCurriculo::class, 'curriculo_id');
    }

    public function vaga()
    {
        return $this->belongsTo(RhVaga::class, 'vaga_id');
    }

    public function etapa()
    {
        return $this->belongsTo(RhVagaEtapa::class, 'etapa_id');
    }

    public function historicos()
    {
        return $this->hasMany(RhCandidaturaHistorico::class, 'candidatura_id')->latest();
    }

    public function comentarios()
    {
        return $this->hasMany(RhCandidaturaComentario::class, 'candidatura_id')->latest();
    }
}
