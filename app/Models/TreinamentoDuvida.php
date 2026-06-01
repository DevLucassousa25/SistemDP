<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TreinamentoDuvida extends Model
{
    protected $table = 'treinamento_duvidas';

    protected $fillable = [
        'treinamento_id', 'aula_id', 'user_id', 'pergunta', 'status',
    ];

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
    }

    public function aula(): BelongsTo
    {
        return $this->belongsTo(TreinamentoAula::class, 'aula_id');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function respostas(): HasMany
    {
        return $this->hasMany(TreinamentoDuvidaResposta::class, 'duvida_id')->oldest();
    }
}
