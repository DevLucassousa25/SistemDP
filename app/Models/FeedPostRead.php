<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPostRead extends Model
{
    public $timestamps = false;

    protected $fillable = ['feed_post_id', 'user_id', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function post(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class, 'feed_post_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
