<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChecklistExecucao extends Model
{
    protected $table = 'checklist_execucoes';

    protected $fillable = [
        'checklist_item_id',
        'user_id',
        'executado_por',
        'tipo',
        'concluido',
        'data_conclusao',
        'observacoes',
    ];

    protected $casts = [
        'concluido'      => 'boolean',
        'data_conclusao' => 'datetime',
    ];

    // ── Relações ──────────────────────────────────────────────────────────────

    public function checklistItem(): BelongsTo
    {
        return $this->belongsTo(ChecklistItem::class, 'checklist_item_id');
    }

    public function funcionario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function executadoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'executado_por');
    }
}
