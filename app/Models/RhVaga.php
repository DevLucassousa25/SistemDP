<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhVaga extends Model
{
    protected $table = 'rh_vagas';

    protected $fillable = [
        'created_by', 'titulo', 'cargo', 'descricao', 'requisitos', 'competencias', 'beneficios',
        'salario_min', 'salario_max', 'modalidade', 'cidade', 'estado', 'status',
        'nota_minima_aprovacao', 'sla_dias', 'vagas_disponiveis', 'data_encerramento',
    ];

    protected function casts(): array
    {
        return ['data_encerramento' => 'date', 'salario_min' => 'decimal:2', 'salario_max' => 'decimal:2'];
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function etapas()
    {
        return $this->hasMany(RhVagaEtapa::class, 'vaga_id')->orderBy('ordem');
    }

    public function candidaturas()
    {
        return $this->hasMany(RhCandidatura::class, 'vaga_id');
    }

    public function getCandidatosCountAttribute(): int
    {
        return $this->candidaturas()->count();
    }
}
