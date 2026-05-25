<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPoll extends Model
{
    protected $fillable = ['community_post_id', 'question', 'allows_multiple', 'ends_at'];

    protected function casts(): array
    {
        return ['allows_multiple' => 'boolean', 'ends_at' => 'datetime'];
    }

    public function post()    { return $this->belongsTo(CommunityPost::class, 'community_post_id'); }
    public function options() { return $this->hasMany(CommunityPollOption::class)->orderBy('order'); }

    public function getIsExpiredAttribute(): bool
    {
        return $this->ends_at && $this->ends_at->isPast();
    }
}
