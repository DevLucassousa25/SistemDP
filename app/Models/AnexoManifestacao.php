<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class AnexoManifestacao extends Model
{
    use HasUuids;

    protected $table = 'anexos_manifestacaos';

    protected $fillable = [
        'manifestacao_id',
        'resposta_manifestacao_id',
        'uploaded_by',
        'nome_arquivo',
        'caminho',
        'mime_type',
        'tamanho_bytes',
    ];

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /** URL pública do arquivo (disco public). */
    public function url(): string
    {
        return Storage::disk('public')->url($this->caminho);
    }

    /** Tamanho legível para humanos (ex.: "2,3 MB"). */
    public function tamanhoLegivel(): string
    {
        $bytes = $this->tamanho_bytes;

        if ($bytes >= 1_048_576) {
            return number_format($bytes / 1_048_576, 1, ',', '.') . ' MB';
        }

        if ($bytes >= 1_024) {
            return number_format($bytes / 1_024, 0, ',', '.') . ' KB';
        }

        return $bytes . ' B';
    }

    /**
     * Retorna a categoria do tipo do arquivo para exibição de ícone.
     * Valores possíveis: 'image' | 'pdf' | 'document' | 'spreadsheet' | 'file'
     */
    public function tipoCategoria(): string
    {
        return match (true) {
            str_starts_with($this->mime_type, 'image/')                                                        => 'image',
            $this->mime_type === 'application/pdf'                                                             => 'pdf',
            in_array($this->mime_type, [
                'application/msword',
                'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
            ])                                                                                                 => 'document',
            in_array($this->mime_type, [
                'application/vnd.ms-excel',
                'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            ])                                                                                                 => 'spreadsheet',
            default                                                                                            => 'file',
        };
    }

    /** Indica se o arquivo pode ser visualizado diretamente no navegador. */
    public function podeVisualizar(): bool
    {
        return in_array($this->tipoCategoria(), ['image', 'pdf']);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ──────────────────────────────────────────────────────────────────────────

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(Manifestacao::class);
    }

    public function resposta(): BelongsTo
    {
        return $this->belongsTo(RespostaManifestacao::class, 'resposta_manifestacao_id');
    }

    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }
}
