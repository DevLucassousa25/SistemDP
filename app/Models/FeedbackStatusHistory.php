<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedbackStatusHistory extends Model
{
    protected $table = 'feedback_status_history';

    protected $fillable = [
        'feedback_id',
        'changed_by',
        'old_status',
        'new_status',
    ];

    public function feedback(): BelongsTo
    {
        return $this->belongsTo(Feedback::class);
    }

    public function changedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'changed_by');
    }

    private function statusLabel(?string $status): string
    {
        return match ($status) {
            'aberto'             => 'Aberto',
            'em_analise'         => 'Em análise',
            'aguardando_plano'   => 'Aguard. plano',
            'plano_em_andamento' => 'Plano em andamento',
            'resolvido'          => 'Resolvido',
            'arquivado'          => 'Arquivado',
            null                 => 'Criação',
            default              => ucfirst((string) $status),
        };
    }

    public function getOldStatusLabelAttribute(): string
    {
        return $this->statusLabel($this->old_status);
    }

    public function getNewStatusLabelAttribute(): string
    {
        return $this->statusLabel($this->new_status);
    }
}
