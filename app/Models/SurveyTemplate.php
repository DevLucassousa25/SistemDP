<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SurveyTemplate extends Model
{
    protected $table = 'survey_templates';

    protected $fillable = [
        'name',
        'description',
        'category',
        'questions',
        'created_by',
        'is_system',
    ];

    protected $casts = [
        'questions'  => 'array',
        'is_system'  => 'boolean',
    ];

    // ─────────────────────────────────────────────────────────────────
    // Relacionamentos
    // ─────────────────────────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ─────────────────────────────────────────────────────────────────
    // Acessórios
    // ─────────────────────────────────────────────────────────────────

    public function getCategoryLabelAttribute(): string
    {
        return match ($this->category) {
            'nps'      => 'NPS',
            'clima'    => 'Clima',
            'feedback' => 'Feedback',
            'rh'       => 'RH',
            'custom'   => 'Personalizado',
            default    => ucfirst((string) $this->category),
        };
    }

    public function getCategoryColorAttribute(): string
    {
        return match ($this->category) {
            'nps'      => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-400 dark:border-blue-700/40',
            'clima'    => 'bg-emerald-50 text-emerald-700 border-emerald-200 dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-700/40',
            'feedback' => 'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-900/20 dark:text-violet-400 dark:border-violet-700/40',
            'rh'       => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-400 dark:border-amber-700/40',
            default    => 'bg-slate-100 text-slate-600 border-slate-200 dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600',
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match ($this->category) {
            'nps'      => 'lucide-star',
            'clima'    => 'lucide-sun',
            'feedback' => 'lucide-message-square',
            'rh'       => 'lucide-briefcase',
            default    => 'lucide-layout-template',
        };
    }

    public function getQuestionsCountAttribute(): int
    {
        return count($this->questions ?? []);
    }
}
