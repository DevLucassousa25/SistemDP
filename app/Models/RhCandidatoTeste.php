<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class RhCandidatoTeste extends Model
{
    protected $table = 'rh_candidato_testes';

    protected $fillable = ['curriculo_id', 'teste_id', 'vaga_id', 'token', 'status', 'iniciado_at', 'concluido_at', 'expira_at', 'nota', 'aprovado'];

    protected $hidden = ['token'];

    protected function casts(): array
    {
        return [
            'iniciado_at'  => 'datetime',
            'concluido_at' => 'datetime',
            'expira_at'    => 'datetime',
            'aprovado'     => 'boolean',
            'nota'         => 'decimal:2',
        ];
    }

    protected static function boot(): void
    {
        parent::boot();
        static::creating(fn($m) => $m->token = $m->token ?? Str::random(64));
    }

    public function curriculo()
    {
        return $this->belongsTo(RhCurriculo::class, 'curriculo_id');
    }

    public function teste()
    {
        return $this->belongsTo(RhTeste::class, 'teste_id');
    }

    public function vaga()
    {
        return $this->belongsTo(RhVaga::class, 'vaga_id');
    }

    public function respostas()
    {
        return $this->hasMany(RhCandidatoResposta::class, 'candidato_teste_id');
    }

    public function getTempoDecorridoAttribute(): int
    {
        if (!$this->iniciado_at) return 0;
        return $this->iniciado_at->diffInSeconds($this->concluido_at ?? now());
    }
}
