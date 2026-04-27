<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Query\Builder;

class Manifestacao extends Model
{
    use HasUuids, SoftDeletes;

    protected $table = 'manifestacoes';


    protected $fillable = [
        'protocolo',
        'categoria',
        'assunto',
        'descricao',
        'status',
        'is_anonimo',
        'user_id',
        'respondido_em',
        'concluido_em',
        'prazo_personalizado_horas',
        'auto_encerramento_desativado',
    ];

    protected $casts = [
        'is_anonimo'                   => 'boolean',
        'respondido_em'                => 'datetime',
        'concluido_em'                 => 'datetime',
        'auto_encerramento_desativado' => 'boolean',
    ];


    const STATUS_EM_ANALISE   = 'em_analise';
    const STATUS_EM_ANDAMENTO = 'em_andamento';
    const STATUS_RESPONDIDO   = 'respondido';
    const STATUS_CONCLUIDO    = 'concluido';


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


    // Gera protocolo automaticamente ao criar
    protected static function booted(): void
    {
        static::creating(function (Manifestacao $m) {
            $ano = now()->year;
            $seq = static::whereYear('created_at', $ano)->count() + 1;
            $m->protocolo = sprintf('OUV-%d-%03d', $ano, $seq);
        });
    }

    // Retorna null no autor se anônimo (proteção extra na camada de negócio)
    public function getAutorAttribute(): ?User
    {
        return $this->is_anonimo ? null : $this->user;
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(RespostaManifestacao::class)
                    ->where('is_interno', false) // somente respostas públicas para o autor
                    ->orderBy('created_at');
    }

    public function respostasInternas(): HasMany
    {
        return $this->hasMany(RespostaManifestacao::class)
                    ->where('is_interno', true);
    }

    public function todasRespostas(): HasMany
    {
        return $this->hasMany(RespostaManifestacao::class)->orderBy('created_at');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(AnexoManifestacao::class);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS DE AUTO-ENCERRAMENTO
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Retorna o timestamp da última atividade relevante na manifestação.
     * Considera updated_at + última resposta criada.
     */
    public function ultimaAtividadeEm(): \Carbon\Carbon
    {
        $ultimaResposta = $this->todasRespostas()->latest()->value('created_at');

        return $ultimaResposta
            ? max($this->updated_at, \Carbon\Carbon::parse($ultimaResposta))
            : $this->updated_at;
    }

    /**
     * Retorna o prazo efetivo em horas (personalizado ou global).
     */
    public function prazoEfetivoHoras(): int
    {
        return $this->prazo_personalizado_horas
            ?? ConfiguracaoOuvidoria::instancia()->prazo_horas;
    }

    /**
     * Indica se esta manifestação está elegível para auto-encerramento.
     */
    public function deveAutoEncerrar(): bool
    {
        if ($this->auto_encerramento_desativado) {
            return false;
        }

        if (! in_array($this->status, [self::STATUS_EM_ANALISE, self::STATUS_EM_ANDAMENTO])) {
            return false;
        }

        // floor() + max(0) garante valor inteiro não-negativo (Carbon 3 retorna float)
        $horasDecorridas = max(0, (int) floor($this->ultimaAtividadeEm()->diffInHours(now())));

        return $horasDecorridas >= $this->prazoEfetivoHoras();
    }

    // Scopes úteis
    public function scopeVisivelPara(Builder $q, User $user): Builder
    {
        // RH/DP vê tudo; usuário comum só vê as próprias (sem expor anônimas de outros)
        if ($user->isRhOuDp()) {
            return $q;
        }

        return $q->where('user_id', $user->id);
    }
}
