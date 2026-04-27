<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ManagerEvaluationEntry extends Model
{
    protected $table = 'manager_evaluation_entries';

    protected $fillable = [
        'manager_evaluation_id',
        'employee_id',
        'criterion_id',
        'score',
        'comment',
    ];

    protected $casts = [
        'score' => 'integer',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function evaluation(): BelongsTo
    {
        return $this->belongsTo(ManagerEvaluation::class, 'manager_evaluation_id');
    }

    public function employee(): BelongsTo
    {
        return $this->belongsTo(User::class, 'employee_id');
    }

    public function criterion(): BelongsTo
    {
        return $this->belongsTo(EvaluationCriterion::class, 'criterion_id');
    }
}
