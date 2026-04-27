<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackActionTask extends Model
{
    protected $table = 'feedback_action_tasks';

    protected $fillable = [
        'feedback_id',
        'description',
        'responsible_id',
        'due_date',
        'phase',
        'resources',
        'completed',
        'completed_at',
        'completed_by',
        'validation_note',
        'created_by',
    ];

    protected $casts = [
        'due_date'     => 'date',
        'completed'    => 'boolean',
        'completed_at' => 'datetime',
    ];

    // ─────────────────────────────────────────────────────────────────
    // Relacionamentos
    // ─────────────────────────────────────────────────────────────────

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    public function responsible(): BelongsTo
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }

    public function completedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'completed_by');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─────────────────────────────────────────────────────────────────
    // Acessórios
    // ─────────────────────────────────────────────────────────────────

    public function getIsOverdueAttribute(): bool
    {
        return ! $this->completed
            && $this->due_date !== null
            && $this->due_date->isPast();
    }

    public function getPhaseLabelAttribute(): ?string
    {
        return match($this->phase) {
            '30_dias' => '30 dias',
            '60_dias' => '60 dias',
            '90_dias' => '90 dias',
            default   => null,
        };
    }
}
