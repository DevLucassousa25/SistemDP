<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class RhDesligamento extends Model
{
    protected $table = 'rh_desligamentos';

    protected $fillable = [
        'user_id', 'rh_user_id', 'tipo', 'status',
        'data_aviso', 'data_ultimo_dia',
        'aviso_previo_tipo', 'aviso_previo_dias',
        'recontratavel', 'observacoes', 'concluido_at',
    ];

    protected function casts(): array
    {
        return [
            'data_aviso'       => 'date',
            'data_ultimo_dia'  => 'date',
            'recontratavel'    => 'boolean',
            'concluido_at'     => 'datetime',
        ];
    }

    // Labels legíveis
    public static array $tipoLabels = [
        'voluntario'       => 'Pedido de Demissão',
        'sem_justa_causa'  => 'Sem Justa Causa',
        'com_justa_causa'  => 'Com Justa Causa',
        'acordo_mutuo'     => 'Acordo Mútuo',
        'aposentadoria'    => 'Aposentadoria',
    ];

    public static array $tipoCores = [
        'voluntario'       => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'sem_justa_causa'  => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'com_justa_causa'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
        'acordo_mutuo'     => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        'aposentadoria'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function rhUser(): BelongsTo
    {
        return $this->belongsTo(User::class, 'rh_user_id');
    }

    public function checklist(): HasMany
    {
        return $this->hasMany(RhDesligamentoChecklist::class, 'desligamento_id')->orderBy('ordem');
    }

    public function entrevista(): HasOne
    {
        return $this->hasOne(RhEntrevistaDesligamento::class, 'desligamento_id');
    }

    public function verbas(): HasOne
    {
        return $this->hasOne(RhVerbaRescisoria::class, 'desligamento_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getTipoLabelAttribute(): string
    {
        return self::$tipoLabels[$this->tipo] ?? $this->tipo;
    }

    public function getTipoCorAttribute(): string
    {
        return self::$tipoCores[$this->tipo] ?? 'bg-slate-100 text-slate-700';
    }

    public function getChecklistProgressoAttribute(): array
    {
        $total    = $this->checklist->count();
        $concluidos = $this->checklist->where('status', 'concluido')->count();
        return [
            'total'     => $total,
            'concluidos' => $concluidos,
            'pct'        => $total > 0 ? round($concluidos / $total * 100) : 0,
        ];
    }

    // Checklist padrão para novos desligamentos
    public static function checklistPadrao(): array
    {
        return [
            ['titulo' => 'Comunicar o gestor direto',              'responsavel' => 'rh',        'ordem' => 1],
            ['titulo' => 'Comunicar o time/equipe',                'responsavel' => 'gestao',    'ordem' => 2],
            ['titulo' => 'Enviar entrevista de desligamento',      'responsavel' => 'rh',        'ordem' => 3],
            ['titulo' => 'Revogar acessos de sistemas',            'responsavel' => 'ti',        'ordem' => 4],
            ['titulo' => 'Revogar acesso físico / crachá',         'responsavel' => 'ti',        'ordem' => 5],
            ['titulo' => 'Coletar equipamentos (notebook, etc.)',  'responsavel' => 'ti',        'ordem' => 6],
            ['titulo' => 'Backup e transferência de arquivos',     'responsavel' => 'ti',        'ordem' => 7],
            ['titulo' => 'Transferência de responsabilidades',     'responsavel' => 'gestao',    'ordem' => 8],
            ['titulo' => 'Cancelar benefícios (VT, VR, plano)',    'responsavel' => 'rh',        'ordem' => 9],
            ['titulo' => 'Processar rescisão / TRCT',              'responsavel' => 'financeiro','ordem' => 10],
            ['titulo' => 'Homologação no sindicato (se aplicável)','responsavel' => 'rh',        'ordem' => 11],
            ['titulo' => 'Dar baixa na CTPS',                      'responsavel' => 'rh',        'ordem' => 12],
        ];
    }
}
