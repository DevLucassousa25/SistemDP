<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Manifestacao extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Laravel pluraliza "Manifestacao" como "manifestacaos" (errado).
     * Definimos manualmente o nome correto da tabela no PostgreSQL.
     */
    protected $table = 'manifestacoes';

    protected $fillable = [
        'protocolo',
        'categoria',
        'assunto',
        'descricao',
        'status',
        'anonimo',
        'user_id',
        'respondido_em',
        'concluido_em',
    ];

    protected $casts = [
        'anonimo'       => 'boolean',
        'respondido_em' => 'datetime',
        'concluido_em'  => 'datetime',
    ];

    // ─── Constantes de status ────────────────────────────────────────────────

    const STATUS_EM_ANALISE   = 'em_analise';
    const STATUS_EM_ANDAMENTO = 'em_andamento';
    const STATUS_RESPONDIDO   = 'respondido';
    const STATUS_CONCLUIDO    = 'concluido';

    // ─── Scopes ──────────────────────────────────────────────────────────────

    public function scopeEmAnalise($query)
    {
        return $query->where('status', self::STATUS_EM_ANALISE);
    }

    public function scopeEmAndamento($query)
    {
        return $query->where('status', self::STATUS_EM_ANDAMENTO);
    }

    public function scopeConcluidas($query)
    {
        return $query->where('status', self::STATUS_CONCLUIDO);
    }

    // ─── Relacionamentos ─────────────────────────────────────────────────────

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
