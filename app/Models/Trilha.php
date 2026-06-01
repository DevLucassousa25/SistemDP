<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Trilha extends Model
{
    protected $fillable = [
        'titulo', 'descricao', 'capa', 'status', 'categoria', 'criado_por',
    ];

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }

    public function trilhaCursos(): HasMany
    {
        return $this->hasMany(TrilhaCurso::class)->orderBy('ordem');
    }

    public function inscricoes(): HasMany
    {
        return $this->hasMany(TrilhaInscricao::class);
    }

    public function inscricaoDoUsuario(int $userId): ?TrilhaInscricao
    {
        return $this->inscricoes()->where('user_id', $userId)->first();
    }

    /** Carga horária total somando os cursos da trilha */
    public function getCargaHorariaTotalAttribute(): int
    {
        return $this->trilhaCursos()->with('treinamento')->get()
            ->sum(fn($tc) => $tc->treinamento->carga_horaria ?? 0);
    }

    public function getCargaHorariaFormatadaAttribute(): string
    {
        $min = $this->carga_horaria_total;
        $h   = intdiv($min, 60);
        $m   = $min % 60;
        if ($h > 0 && $m > 0) return "{$h}h {$m}min";
        if ($h > 0) return "{$h}h";
        return "{$m}min";
    }
}
