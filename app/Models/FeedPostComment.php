<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedPostComment extends Model
{
    protected $fillable = ['feed_post_id', 'user_id', 'parent_id', 'content'];

    public function feedPost(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(FeedPostComment::class, 'parent_id');
    }

    public function replies(): HasMany
    {
        return $this->hasMany(FeedPostComment::class, 'parent_id')->with('user')->latest();
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(FeedPostCommentReaction::class);
    }
}
