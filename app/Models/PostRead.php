<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostRead extends Model
{
    protected $table = 'post_reads';

    protected $fillable = ['post_id', 'user_id', 'read_at', 'confirmed_at'];

    protected function casts(): array
    {
        return [
            'read_at'      => 'datetime',
            'confirmed_at' => 'datetime',
        ];
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getIsConfirmedAttribute(): bool
    {
        return ! is_null($this->confirmed_at);
    }
}
