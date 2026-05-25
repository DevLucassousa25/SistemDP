<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Community extends Model
{
    protected $fillable = [
        'name', 'slug', 'description', 'rules', 'cover_image',
        'avatar_color', 'created_by', 'department_id', 'is_private',
    ];

    protected function casts(): array
    {
        return ['is_private' => 'boolean'];
    }

    // ── Boot ──────────────────────────────────────────────────────────

    protected static function boot(): void
    {
        parent::boot();
        static::creating(function (self $community) {
            if (empty($community->slug)) {
                $community->slug = Str::slug($community->name) . '-' . Str::random(5);
            }
            // Cria cor de avatar com base no nome
            $colors = [
                'bg-indigo-500', 'bg-violet-500', 'bg-pink-500',
                'bg-teal-500',   'bg-amber-500',  'bg-orange-500',
                'bg-cyan-500',   'bg-rose-500',   'bg-emerald-500',
                'bg-blue-500',   'bg-lime-500',   'bg-fuchsia-500',
            ];
            $community->avatar_color = $colors[abs(crc32($community->name)) % count($colors)];
        });
    }

    // ── Relations ─────────────────────────────────────────────────────

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function members()
    {
        return $this->hasMany(CommunityMember::class);
    }

    public function acceptedMembers()
    {
        return $this->hasMany(CommunityMember::class)->where('status', 'accepted');
    }

    public function pendingMembers()
    {
        return $this->hasMany(CommunityMember::class)->where('status', 'pending');
    }

    public function posts()
    {
        return $this->hasMany(CommunityPost::class)->latest();
    }

    public function pinnedPosts()
    {
        return $this->hasMany(CommunityPost::class)->where('is_pinned', true)->latest();
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getInitialsAttribute(): string
    {
        $words = explode(' ', $this->name);
        $init  = strtoupper(substr($words[0], 0, 1));
        if (isset($words[1])) $init .= strtoupper(substr($words[1], 0, 1));
        return $init;
    }

    // ── Helpers ───────────────────────────────────────────────────────

    public function membershipOf(int $userId): ?CommunityMember
    {
        return $this->members()->where('user_id', $userId)->first();
    }

    public function isAdmin(int $userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('role', 'admin')
            ->where('status', 'accepted')
            ->exists();
    }

    public function isMember(int $userId): bool
    {
        return $this->members()
            ->where('user_id', $userId)
            ->where('status', 'accepted')
            ->exists();
    }
}
