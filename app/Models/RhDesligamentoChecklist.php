<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RhDesligamentoChecklist extends Model
{
    protected $table = 'rh_desligamento_checklist';

    protected $fillable = [
        'desligamento_id', 'titulo', 'responsavel',
        'status', 'concluido_by', 'concluido_at', 'ordem',
    ];

    protected function casts(): array
    {
        return ['concluido_at' => 'datetime'];
    }

    public static array $responsavelLabels = [
        'rh'        => 'RH',
        'ti'        => 'TI',
        'gestao'    => 'Gestão',
        'financeiro' => 'Financeiro',
    ];

    public static array $responsavelCores = [
        'rh'        => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'ti'        => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
        'gestao'    => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'financeiro' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
    ];

    public function desligamento(): BelongsTo
    {
        return $this->belongsTo(RhDesligamento::class, 'desligamento_id');
    }

    public function concluidoPor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'concluido_by');
    }
}
