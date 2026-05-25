<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class FeedPost extends Model
{
    protected $fillable = [
        'user_id', 'content', 'image', 'repost_of_id',
        'views_count', 'is_pinned', 'edited_at',
    ];

    protected $casts = [
        'is_pinned' => 'boolean',
        'edited_at' => 'datetime',
    ];

    // ── Relacionamentos ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(FeedPostReaction::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(FeedPostComment::class)->whereNull('parent_id');
    }

    public function allComments(): HasMany
    {
        return $this->hasMany(FeedPostComment::class);
    }

    public function bookmarks(): HasMany
    {
        return $this->hasMany(FeedPostBookmark::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(FeedPostImage::class)->orderBy('order');
    }

    public function poll(): HasOne
    {
        return $this->hasOne(FeedPoll::class);
    }

    public function reads(): HasMany
    {
        return $this->hasMany(FeedPostRead::class, 'feed_post_id');
    }

    public function getReadByMeAttribute(): bool
    {
        return $this->reads->contains('user_id', Auth::id());
    }

    /** Post original que este post está repostando */
    public function repostedFrom(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class, 'repost_of_id')->with('user');
    }

    /** Todos os reposts deste post */
    public function reposts(): HasMany
    {
        return $this->hasMany(FeedPost::class, 'repost_of_id');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? Storage::url($this->image) : null;
    }

    public function getExcerptAttribute(): string
    {
        return \Str::limit($this->content, 200);
    }

    public function getIsRepostAttribute(): bool
    {
        return ! is_null($this->repost_of_id);
    }
}
