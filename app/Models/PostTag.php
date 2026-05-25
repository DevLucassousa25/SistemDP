<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Str;

class PostTag extends Model
{
    protected $table = 'post_tags';

    protected $fillable = ['name', 'slug', 'color'];

    public static function boot(): void
    {
        parent::boot();

        static::creating(function (self $tag) {
            $tag->slug = Str::slug($tag->name);
        });

        static::updating(function (self $tag) {
            $tag->slug = Str::slug($tag->name);
        });
    }

    public function posts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_post_tag', 'post_tag_id', 'post_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getBadgeClassAttribute(): string
    {
        return match ($this->color) {
            'indigo'  => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
            'violet'  => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
            'blue'    => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'emerald' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'teal'    => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300',
            'amber'   => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'orange'  => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
            'red'     => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
            'pink'    => 'bg-pink-100 text-pink-700 dark:bg-pink-900/30 dark:text-pink-300',
            'slate'   => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
            default   => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
        };
    }

    public function getDotClassAttribute(): string
    {
        return match ($this->color) {
            'indigo'  => 'bg-indigo-500',
            'violet'  => 'bg-violet-500',
            'blue'    => 'bg-blue-500',
            'emerald' => 'bg-emerald-500',
            'teal'    => 'bg-teal-500',
            'amber'   => 'bg-amber-500',
            'orange'  => 'bg-orange-500',
            'red'     => 'bg-red-500',
            'pink'    => 'bg-pink-500',
            'slate'   => 'bg-slate-400',
            default   => 'bg-indigo-500',
        };
    }
}
