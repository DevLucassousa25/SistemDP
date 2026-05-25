<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Post extends Model
{
    protected $table = 'posts';

    protected $fillable = [
        'user_id', 'title', 'content', 'cover_image',
        'type', 'status', 'target_audience',
        'pinned', 'is_mandatory_read',
        'views_count', 'published_at', 'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'pinned'            => 'boolean',
            'is_mandatory_read' => 'boolean',
            'published_at'      => 'datetime',
            'expires_at'        => 'datetime',
        ];
    }

    // ── Relacionamentos ───────────────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(PostTag::class, 'post_post_tag', 'post_id', 'post_tag_id');
    }

    public function reads(): HasMany
    {
        return $this->hasMany(PostRead::class);
    }

    public function confirmedReads(): HasMany
    {
        return $this->hasMany(PostRead::class)->whereNotNull('confirmed_at');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(PostReaction::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(PostComment::class);
    }

    // ── Scopes ────────────────────────────────────────────────────────

    public function scopePublished($query)
    {
        return $query->where('status', 'publicado');
    }

    public function scopePinned($query)
    {
        return $query->where('pinned', true);
    }

    public function scopeScheduled($query)
    {
        return $query->where('status', 'rascunho')
                     ->whereNotNull('published_at')
                     ->where('published_at', '<=', now());
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getIsPublishedAttribute(): bool
    {
        return $this->status === 'publicado';
    }

    public function getTypeLabelAttribute(): string
    {
        return match ($this->type) {
            'noticia'    => 'Notícia',
            'comunicado' => 'Comunicado',
            'evento'     => 'Evento',
            'aviso'      => 'Aviso',
            default      => $this->type,
        };
    }

    public function getTypeColorAttribute(): string
    {
        return match ($this->type) {
            'noticia'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'comunicado' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
            'evento'     => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'aviso'      => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            default      => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match ($this->type) {
            'noticia'    => 'newspaper',
            'comunicado' => 'megaphone',
            'evento'     => 'calendar-heart',
            'aviso'      => 'triangle-alert',
            default      => 'file-text',
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'rascunho'  => 'Rascunho',
            'publicado' => 'Publicado',
            'arquivado' => 'Arquivado',
            default     => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'rascunho'  => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
            'publicado' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'arquivado' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            default     => 'bg-slate-100 text-slate-500',
        };
    }

    public function getAudienceLabelAttribute(): string
    {
        return match ($this->target_audience) {
            'todos'        => 'Todos',
            'gerentes'     => 'Gerentes',
            'funcionarios' => 'Funcionários',
            'rh'           => 'RH / DP',
            default        => $this->target_audience,
        };
    }

    public function getExcerptAttribute(): string
    {
        return \Str::limit(strip_tags($this->content), 120);
    }

    public function getReadsCountAttribute(): int
    {
        return $this->reads()->count();
    }

    public function getConfirmedReadsCountAttribute(): int
    {
        return $this->confirmedReads()->count();
    }

    public function getReadingTimeMinutesAttribute(): int
    {
        $words = str_word_count(strip_tags($this->content ?? ''));
        return max(1, (int) ceil($words / 200));
    }
}
