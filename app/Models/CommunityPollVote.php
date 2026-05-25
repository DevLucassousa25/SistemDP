<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPollVote extends Model
{
    protected $fillable = ['community_poll_option_id', 'user_id'];

    public function option() { return $this->belongsTo(CommunityPollOption::class, 'community_poll_option_id'); }
    public function user()   { return $this->belongsTo(User::class); }
}
