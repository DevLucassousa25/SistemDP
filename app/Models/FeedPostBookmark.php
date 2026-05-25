<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPostBookmark extends Model
{
    protected $fillable = ['user_id', 'feed_post_id'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function feedPost(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class);
    }
}
