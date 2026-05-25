<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RhEntrevistaDesligamento extends Model
{
    protected $table = 'rh_entrevista_desligamentos';

    protected $fillable = [
        'desligamento_id', 'token', 'enviado_at', 'respondido_at',
        'satisfacao_gestao', 'satisfacao_cultura', 'satisfacao_remuneracao',
        'satisfacao_crescimento', 'satisfacao_equilibrio',
        'recomendaria_empresa', 'motivo_principal',
        'pontos_positivos', 'pontos_melhoria', 'outros_comentarios',
    ];

    protected function casts(): array
    {
        return [
            'enviado_at'        => 'datetime',
            'respondido_at'     => 'datetime',
            'recomendaria_empresa' => 'boolean',
        ];
    }

    public static array $motivosPrincipais = [
        'melhor_oportunidade'   => 'Melhor oportunidade de trabalho',
        'remuneracao'           => 'Remuneração insatisfatória',
        'clima_organizacional'  => 'Clima organizacional',
        'relacionamento_gestor' => 'Relacionamento com gestor',
        'falta_crescimento'     => 'Falta de crescimento/plano de carreira',
        'mudanca_area'          => 'Mudança de área/segmento',
        'pessoal'               => 'Motivo pessoal/familiar',
        'aposentadoria'         => 'Aposentadoria',
        'outro'                 => 'Outro',
    ];

    public function desligamento(): BelongsTo
    {
        return $this->belongsTo(RhDesligamento::class, 'desligamento_id');
    }

    public function getNpsMediaAttribute(): float
    {
        $campos = [
            $this->satisfacao_gestao,
            $this->satisfacao_cultura,
            $this->satisfacao_remuneracao,
            $this->satisfacao_crescimento,
            $this->satisfacao_equilibrio,
        ];
        $preenchidos = array_filter($campos, fn($v) => !is_null($v));
        return count($preenchidos) > 0
            ? round(array_sum($preenchidos) / count($preenchidos), 1)
            : 0;
    }
}
