<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FeedPollVote extends Model
{
    protected $fillable = ['feed_poll_option_id', 'user_id'];

    public function option(): BelongsTo
    {
        return $this->belongsTo(FeedPollOption::class, 'feed_poll_option_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
