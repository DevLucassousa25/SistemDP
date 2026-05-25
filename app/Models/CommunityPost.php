<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CommunityPost extends Model
{
    protected $fillable = [
        'community_id', 'user_id', 'content', 'is_pinned', 'edited_at', 'views_count',
        'type', 'event_location', 'event_date', 'event_ends_at',
    ];

    protected function casts(): array
    {
        return [
            'is_pinned'   => 'boolean',
            'edited_at'   => 'datetime',
            'event_date'  => 'datetime',
            'event_ends_at' => 'datetime',
        ];
    }

    public function community()    { return $this->belongsTo(Community::class); }
    public function user()         { return $this->belongsTo(User::class); }
    public function images()       { return $this->hasMany(CommunityPostImage::class)->orderBy('order'); }
    public function reactions()    { return $this->hasMany(CommunityPostReaction::class); }
    public function comments()     { return $this->hasMany(CommunityPostComment::class)->whereNull('parent_id')->latest(); }
    public function allComments()  { return $this->hasMany(CommunityPostComment::class); }
    public function poll()         { return $this->hasOne(CommunityPoll::class); }
    public function rsvps()        { return $this->hasMany(CommunityEventRsvp::class); }
}
