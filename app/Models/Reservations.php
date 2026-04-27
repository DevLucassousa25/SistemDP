<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Reservations extends Model
{
     protected $fillable = [
        'room_id',
        'meeting_id',
        'user_id',
        'title',
        'description',
        'start_time',
        'end_time',
        'attendees_count',
        'status',
        'is_maintenance',
    ];

    protected $casts = [
        'start_time'     => 'datetime',
        'end_time'       => 'datetime',
        'is_maintenance' => 'boolean',
    ];

    // Relacionamentos
    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function meeting(): BelongsTo
    {
        return $this->belongsTo(Meeting::class);
    }

    // Scopes
    public function scopeHoje(Builder $query): Builder
    {
        return $query->whereDate('start_time', today());
    }

    public function scopeConfirmadas(Builder $query): Builder
    {
        return $query->where('status', 'confirmada');
    }

    public function scopeFuturas(Builder $query): Builder
    {
        return $query->where('end_time', '>', now());
    }

    public function scopePassadas(Builder $query): Builder
    {
        return $query->where('end_time', '<', now());
    }

    // Accessors
    public function getDuracaoAttribute(): string
    {
        return $this->start_time->diffForHumans($this->end_time, true);
    }

    public function getHorarioAttribute(): string
    {
        return $this->start_time->format('H:i') . ' - ' . $this->end_time->format('H:i');
    }

    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'confirmada' => 'emerald',
            'cancelada'  => 'red',
            'concluida'  => 'gray',
            default      => 'gray',
        };
    }

    // Verifica conflito de horário
    public static function temConflito(int $roomId, Carbon $start, Carbon $end, ?int $exceptId = null): bool
    {
        return static::where('room_id', $roomId)
            ->where('status', 'confirmada')
            ->when($exceptId, fn($q) => $q->where('id', '!=', $exceptId))
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_time', [$start, $end])
                  ->orWhereBetween('end_time', [$start, $end])
                  ->orWhere(function ($q) use ($start, $end) {
                      $q->where('start_time', '<=', $start)
                        ->where('end_time', '>=', $end);
                  });
            })->exists();
    }
}
