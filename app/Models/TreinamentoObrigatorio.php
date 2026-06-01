<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TreinamentoObrigatorio extends Model
{
    protected $table = 'treinamento_obrigatorios';

    protected $fillable = [
        'treinamento_id', 'tipo_alvo', 'user_id', 'department_id', 'prazo', 'criado_por',
    ];

    protected $casts = [
        'prazo' => 'date',
    ];

    public function treinamento(): BelongsTo
    {
        return $this->belongsTo(Treinamento::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'criado_por');
    }
}
