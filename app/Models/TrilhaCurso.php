<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TrilhaCurso extends Model
{
    protected $table = 'trilha_cursos';

    protected $fillable = [
        'trilha_id', 'treinamento_id', 'ordem', 'obrigatorio',
    ];

    protected $casts = [
        'obrigatorio' => 'boolean',
    ];

    public function trilha(): BelongsTo
    {
        return $this->belongsTo(Trilha::class);
    }

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
    }
}
