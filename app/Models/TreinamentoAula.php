<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreinamentoAula extends Model
{
    protected $table = 'treinamento_aulas';

    protected $fillable = [
        'treinamento_id', 'titulo', 'descricao', 'video_url', 'video_arquivo',
        'material_pdf', 'duracao', 'ordem', 'obrigatoria', 'exigir_video',
    ];

    protected $casts = [
        'obrigatoria'  => 'boolean',
        'exigir_video' => 'boolean',
    ];

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
    }

    public function progressos(): HasMany
    {
        return $this->hasMany(TreinamentoAulaProgresso::class, 'aula_id');
    }

    public function duvidas(): HasMany
    {
        return $this->hasMany(TreinamentoDuvida::class, 'aula_id');
    }

    public function getDuracaoFormatadaAttribute(): string
    {
        $h = intdiv($this->duracao, 60);
        $m = $this->duracao % 60;
        if ($h > 0 && $m > 0) return "{$h}h {$m}min";
        if ($h > 0) return "{$h}h";
        return "{$m}min";
    }

    /**
     * Retorna o tipo de vídeo: 'youtube' | 'vimeo' | 'html5' | null
     */
    public function getVideoTipoAttribute(): ?string
    {
        if ($this->video_arquivo) return 'html5';
        if (!$this->video_url) return null;
        if (str_contains($this->video_url, 'youtube') || str_contains($this->video_url, 'youtu.be')) return 'youtube';
        if (str_contains($this->video_url, 'vimeo')) return 'vimeo';
        return 'embed';
    }

    /**
     * ID do vídeo no YouTube (se aplicável)
     */
    public function getVideoYoutubeIdAttribute(): ?string
    {
        if (preg_match(
            '/(?:youtube\.com\/(?:watch\?v=|shorts\/|live\/|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/',
            $this->video_url ?? '',
            $m
        )) {
            return $m[1];
        }
        return null;
    }

    /**
     * ID do vídeo no Vimeo (se aplicável)
     */
    public function getVideoVimeoIdAttribute(): ?string
    {
        if (preg_match('/vimeo\.com\/(\d+)/', $this->video_url ?? '', $m)) {
            return $m[1];
        }
        return null;
    }

    /**
     * Duração em segundos (duracao está em minutos)
     */
    public function getDuracaoSegundosAttribute(): int
    {
        return ($this->duracao ?? 0) * 60;
    }

    public function getVideoEmbedUrlAttribute(): ?string
    {
        $url = $this->video_url;
        if (!$url) return null;

        // YouTube
        if (preg_match('/(?:youtube\.com\/(?:watch\?v=|shorts\/|live\/|embed\/)|youtu\.be\/)([a-zA-Z0-9_-]+)/', $url, $m)) {
            return "https://www.youtube.com/embed/{$m[1]}";
        }
        // Vimeo
        if (preg_match('/vimeo\.com\/(\d+)/', $url, $m)) {
            return "https://player.vimeo.com/video/{$m[1]}";
        }

        return $url;
    }
}
