<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DpiTemplateAction extends Model
{
    protected $table = 'dpi_template_actions';

    protected $fillable = [
        'dpi_template_goal_id', 'title', 'description', 'type', 'order',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────

    public function goal(): BelongsTo
    {
        return $this->belongsTo(DpiTemplateGoal::class, 'dpi_template_goal_id');
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

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'curso'        => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/40 dark:text-indigo-300',
            'certificacao' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-300',
            'leitura'      => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
            'mentoria'     => 'bg-violet-100 text-violet-700 dark:bg-violet-900/40 dark:text-violet-300',
            'projeto'      => 'bg-orange-100 text-orange-700 dark:bg-orange-900/40 dark:text-orange-300',
            'workshop'     => 'bg-pink-100 text-pink-700 dark:bg-pink-900/40 dark:text-pink-300',
            default        => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        };
    }
}
