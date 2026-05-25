<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhCandidaturaHistorico extends Model
{
    public $timestamps = false;

    protected $table = 'rh_candidatura_historicos';

    protected $fillable = ['candidatura_id', 'user_id', 'etapa_anterior', 'etapa_nova', 'acao', 'descricao', 'created_at'];

    protected function casts(): array
    {
        return ['created_at' => 'datetime'];
    }

    public function candidatura()
    {
        return $this->belongsTo(RhCandidatura::class, 'candidatura_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
