<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EvaluationCriterion extends Model
{
    protected $table = 'evaluation_criteria';

    protected $fillable = [
        'name',
        'description',
        'is_active',
        'is_default',
        'order',
    ];

    protected $casts = [
        'is_active'  => 'boolean',
        'is_default' => 'boolean',
    ];

    // ── Relacionamentos ────────────────────────────────────────────────

    public function entries(): HasMany
    {
        return $this->hasMany(ManagerEvaluationEntry::class, 'criterion_id');
    }
}
