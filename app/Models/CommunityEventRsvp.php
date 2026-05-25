<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityEventRsvp extends Model
{
    protected $fillable = ['community_post_id', 'user_id', 'status'];

    public function post(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(CommunityPost::class, 'community_post_id');
    }

    public function user(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
