<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DpiAction extends Model
{
    protected $table = 'dpi_actions';

    protected $fillable = [
        'dpi_goal_id', 'title', 'description', 'type',
        'target_date', 'status', 'order',
        'attachment_path', 'attachment_name',
        'validated_by', 'validated_at',
        'task_id',
    ];

    protected $casts = [
        'target_date'  => 'date',
        'validated_at' => 'datetime',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────

    public function goal(): BelongsTo
    {
        return $this->belongsTo(DpiGoal::class, 'dpi_goal_id');
    }

    public function validator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'validated_by');
    }

    public function task(): BelongsTo
    {
        return $this->belongsTo(Task::class, 'task_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'curso'        => 'Curso',
            'certificacao' => 'Certificação',
            'leitura'      => 'Leitura',
            'mentoria'     => 'Mentoria',
            'projeto'      => 'Projeto',
            'workshop'     => 'Workshop',
            'outro'        => 'Outro',
            default        => $this->type,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pendente'     => 'Pendente',
            'em_andamento' => 'Em andamento',
            'concluido'    => 'Concluído',
            default        => $this->status,
        };
    }
}
