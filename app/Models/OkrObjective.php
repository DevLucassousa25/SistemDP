<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class OkrObjective extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cycle_id', 'title', 'description', 'level',
        'owner_id', 'department_id', 'parent_id',
        'status', 'progress', 'created_by',
    ];

    protected $casts = [
        'progress' => 'decimal:2',
    ];

    // ── Relations ─────────────────────────────────────────────────────

    public function cycle(): BelongsTo
    {
        return $this->belongsTo(OkrCycle::class);
    }

    public function owner(): BelongsTo
    {
        return $this->belongsTo(User::class, 'owner_id');
    }

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function parent(): BelongsTo
    {
        return $this->belongsTo(OkrObjective::class, 'parent_id');
    }

    public function children(): HasMany
    {
        return $this->hasMany(OkrObjective::class, 'parent_id');
    }

    public function keyResults(): HasMany
    {
        return $this->hasMany(OkrKeyResult::class, 'objective_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Accessors ─────────────────────────────────────────────────────

    public function getLevelLabelAttribute(): string
    {
        return match ($this->level) {
            'company'    => 'Empresa',
            'department' => 'Departamento',
            'individual' => 'Individual',
            default      => $this->level,
        };
    }

    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'not_started' => 'Não iniciado',
            'on_track'    => 'No prazo',
            'at_risk'     => 'Em risco',
            'behind'      => 'Atrasado',
            'completed'   => 'Concluído',
            default       => $this->status,
        };
    }

    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'on_track'  => 'emerald',
            'at_risk'   => 'amber',
            'behind'    => 'rose',
            'completed' => 'blue',
            default     => 'slate',
        };
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Recalcula o progresso do objetivo com base na média dos KRs ativos.
     */
    public function recalcularProgresso(): void
    {
        $krs = $this->keyResults()->withoutTrashed()->get();

        if ($krs->isEmpty()) {
            $this->update(['progress' => 0]);
            return;
        }

        $media = $krs->avg('progress');
        $status = match (true) {
            $media >= 100              => 'completed',
            $media >= 70               => 'on_track',
            $media >= 40               => 'at_risk',
            default                    => 'behind',
        };

        // Mantém not_started se progresso ainda é zero
        if ($media == 0) $status = 'not_started';

        $this->update(['progress' => $media, 'status' => $status]);
    }
}
