<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FeedPoll extends Model
{
    protected $fillable = ['feed_post_id', 'question', 'allows_multiple', 'ends_at'];

    protected $casts = [
        'allows_multiple' => 'boolean',
        'ends_at'         => 'datetime',
    ];

    public function feedPost(): BelongsTo
    {
        return $this->belongsTo(FeedPost::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(FeedPollOption::class, 'feed_poll_id')->orderBy('order');
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }

    public function getTotalVotesAttribute(): int
    {
        return $this->options->sum(fn ($o) => $o->votes_count ?? 0);
    }
}
