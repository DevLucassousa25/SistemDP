<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class room_images extends Model
{
    protected $fillable = [
        'room_id',
        'disk',
        'path',
        'original_name',
        'mime_type',
        'size',
        'order',
    ];

    protected $appends = ['url'];

    public function room()
    {
        return $this->belongsTo(room::class, 'room_id');
    }

    // Accessor para URL pública da imagem
    public function getUrlAttribute(): string
    {
        return Storage::disk($this->disk ?? 'public')->url($this->path);
    }
}
