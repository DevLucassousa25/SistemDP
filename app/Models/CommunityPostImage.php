<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPostImage extends Model
{
    protected $fillable = ['community_post_id', 'path', 'order'];

    public function post() { return $this->belongsTo(CommunityPost::class, 'community_post_id'); }
}
