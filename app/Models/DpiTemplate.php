<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class DpiTemplate extends Model
{
    protected $table = 'dpi_templates';

    protected $fillable = [
        'name', 'description', 'is_active', 'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function goals(): HasMany
    {
        return $this->hasMany(DpiTemplateGoal::class)->orderBy('order')->orderBy('id');
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /** Total de ações em todas as metas do template. */
    public function totalActionsCount(): int
    {
        return $this->goals->sum(fn ($g) => $g->actions->count());
    }
}
