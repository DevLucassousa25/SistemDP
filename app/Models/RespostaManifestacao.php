<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RespostaManifestacao extends Model
{
    use HasUuids;

    protected $table = 'respostas_manifestacaos';

    protected $fillable = ['manifestacao_id', 'respondente_id', 'conteudo', 'is_interno', 'is_automatica'];

    protected $casts = [
        'is_interno'    => 'boolean',
        'is_automatica' => 'boolean',
    ];

    public function manifestacao(): BelongsTo
    {
        return $this->belongsTo(Manifestacao::class);
    }

    public function respondente(): BelongsTo
    {
        return $this->belongsTo(User::class, 'respondente_id');
    }

    public function anexos(): HasMany
    {
        return $this->hasMany(AnexoManifestacao::class, 'resposta_manifestacao_id')->orderBy('created_at');
    }
}
