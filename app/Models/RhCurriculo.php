<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhCurriculo extends Model
{
    protected $table = 'rh_curriculos';

    protected $fillable = [
        'user_id', 'candidato_id', 'nome', 'cpf', 'email', 'telefone', 'data_nascimento',
        'endereco', 'cidade', 'estado', 'cep', 'escolaridade', 'resumo_profissional',
        'pretensao_salarial', 'area_interesse', 'arquivo_path', 'arquivo_original',
        'arquivo_mime', 'texto_ocr', 'ocr_processado_at', 'notas_internas', 'status', 'nota_media',
    ];

    protected function casts(): array
    {
        return [
            'data_nascimento'    => 'date',
            'pretensao_salarial' => 'decimal:2',
            'ocr_processado_at'  => 'datetime',
        ];
    }

    public function candidato()
    {
        return $this->belongsTo(RhCandidato::class, 'candidato_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function experiencias()
    {
        return $this->hasMany(RhCurriculoExperiencia::class, 'curriculo_id')->latest('data_inicio');
    }

    public function formacoes()
    {
        return $this->hasMany(RhCurriculoFormacao::class, 'curriculo_id')->latest('ano_conclusao');
    }

    public function habilidades()
    {
        return $this->hasMany(RhCurriculoHabilidade::class, 'curriculo_id');
    }

    public function tags()
    {
        return $this->hasMany(RhCurriculoTag::class, 'curriculo_id');
    }

    public function candidaturas()
    {
        return $this->hasMany(RhCandidatura::class, 'curriculo_id');
    }

    public function testes()
    {
        return $this->hasMany(RhCandidatoTeste::class, 'curriculo_id');
    }

    public function getIdadeAttribute(): ?int
    {
        return $this->data_nascimento ? $this->data_nascimento->age : null;
    }

    public function getTempoExperienciaAttribute(): int
    {
        return $this->experiencias->sum(function ($e) {
            $fim = $e->atual ? now() : ($e->data_fim ?? now());
            return $e->data_inicio->diffInMonths($fim);
        });
    }
}
