<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedPollOption extends Model
{
    protected $fillable = ['feed_poll_id', 'label', 'order'];

    public function poll(): BelongsTo
    {
        return $this->belongsTo(FeedPoll::class, 'feed_poll_id');
    }

    public function votes(): HasMany
    {
        return $this->hasMany(FeedPollVote::class);
    }
}
