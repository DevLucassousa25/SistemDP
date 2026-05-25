<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RhOnboarding extends Model
{
    protected $table = 'rh_onboardings';

    protected $fillable = [
        'candidatura_id', 'user_id', 'criado_by', 'department_id',
        'cargo', 'data_inicio', 'status', 'concluido_at', 'observacoes',
    ];

    protected function casts(): array
    {
        return [
            'data_inicio'  => 'date',
            'concluido_at' => 'datetime',
        ];
    }

    // ── Relacionamentos ───────────────────────────────────────────────

    public function candidatura()
    {
        return $this->belongsTo(RhCandidatura::class, 'candidatura_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function criadoPor()
    {
        return $this->belongsTo(User::class, 'criado_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function tarefas()
    {
        return $this->hasMany(RhOnboardingTarefa::class, 'onboarding_id')->orderBy('ordem');
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function progressoPercent(): int
    {
        $total = $this->tarefas()->count();
        if ($total === 0) return 0;
        $concluidas = $this->tarefas()->where('status', 'concluido')->count();
        return (int) round($concluidas / $total * 100);
    }

    /**
     * Tarefas padrão criadas automaticamente em todo onboarding.
     */
    public static function tarefasPadrao(): array
    {
        return [
            ['titulo' => 'Assinar contrato de trabalho',       'responsavel' => 'rh',        'ordem' => 1],
            ['titulo' => 'Coletar e verificar documentação',   'responsavel' => 'rh',        'ordem' => 2],
            ['titulo' => 'Cadastrar na folha de pagamento',    'responsavel' => 'financeiro', 'ordem' => 3],
            ['titulo' => 'Criar e-mail corporativo',           'responsavel' => 'ti',        'ordem' => 4],
            ['titulo' => 'Configurar acesso ao sistema',       'responsavel' => 'ti',        'ordem' => 5],
            ['titulo' => 'Entregar equipamentos / materiais',  'responsavel' => 'ti',        'ordem' => 6],
            ['titulo' => 'Apresentar equipe e ambiente',       'responsavel' => 'gestao',    'ordem' => 7],
            ['titulo' => 'Orientação sobre políticas internas','responsavel' => 'rh',        'ordem' => 8],
        ];
    }
}
