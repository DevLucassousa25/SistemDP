<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreinamentoDuvidaResposta extends Model
{
    protected $table = 'treinamento_duvida_respostas';

    protected $fillable = [
        'duvida_id', 'user_id', 'resposta',
    ];

    public function duvida(): BelongsTo
    {
        return $this->belongsTo(TreinamentoDuvida::class, 'duvida_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
