<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrilhaInscricao extends Model
{
    protected $table = 'trilha_inscricoes';

    protected $fillable = [
        'trilha_id', 'user_id', 'status', 'progresso', 'concluida_em',
    ];

    protected $casts = [
        'concluida_em' => 'datetime',
    ];

    public function trilha(): BelongsTo
    {
        return $this->belongsTo(Trilha::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Recalcula progresso com base nas inscrições dos cursos da trilha.
     */
    public function recalcularProgresso(): void
    {
        $trilhaCursos = $this->trilha->trilhaCursos()->with('treinamento')->get();
        $total = $trilhaCursos->count();
        if ($total === 0) { $this->progresso = 0; $this->save(); return; }

        $concluidos = TreinamentoInscricao::where('user_id', $this->user_id)
            ->whereIn('treinamento_id', $trilhaCursos->pluck('treinamento_id'))
            ->where('status', 'concluido')
            ->count();

        $this->progresso = (int) round(($concluidos / $total) * 100);

        if ($this->progresso >= 100) {
            $this->status      = 'concluida';
            $this->concluida_em = now();
        }
        $this->save();
    }
}
