<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPostCommentReaction extends Model
{
    protected $fillable = ['feed_post_comment_id', 'user_id', 'type'];

    public function comment(): BelongsTo
    {
        return $this->belongsTo(FeedPostComment::class, 'feed_post_comment_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
