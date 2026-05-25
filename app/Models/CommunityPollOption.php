<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPollOption extends Model
{
    protected $fillable = ['community_poll_id', 'label', 'order'];

    public function poll()  { return $this->belongsTo(CommunityPoll::class, 'community_poll_id'); }
    public function votes() { return $this->hasMany(CommunityPollVote::class); }
}
