<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class FeedPostImage extends Model
{
    protected $fillable = ['feed_post_id', 'path', 'order'];

    public function feedPost(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class);
    }

    public function getUrlAttribute(): string
    {
        return Storage::url($this->path);
    }
}
