<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Meeting extends Model
{
    protected $fillable = [
        'title',
        'description',
        'start_time',
        'end_time',
        'location_type',
        'online_platform',
        'online_link',
        'room_id',
        'organizer_id',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'start_time' => 'datetime',
            'end_time'   => 'datetime',
        ];
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RELATIONSHIPS
    // ──────────────────────────────────────────────────────────────────────────

    public function organizer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'organizer_id');
    }

    public function room(): BelongsTo
    {
        return $this->belongsTo(Room::class);
    }

    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'meeting_participants')
                    ->withPivot('rsvp')
                    ->withTimestamps();
    }

    public function agendaItems(): HasMany
    {
        return $this->hasMany(MeetingAgendaItem::class)
                    ->orderBy('sort_order')
                    ->orderBy('id');
    }

    public function meetingNotes(): HasMany
    {
        return $this->hasMany(MeetingNote::class)->latest();
    }

    /** Reserva vinculada (gerada automaticamente quando a reunião é presencial). */
    public function reservation(): HasOne
    {
        return $this->hasOne(Reservations::class, 'meeting_id');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SCOPES
    // ──────────────────────────────────────────────────────────────────────────

    /** Reuniões em determinado dia (date string YYYY-MM-DD). */
    public function scopeOnDay(Builder $q, string $date): Builder
    {
        return $q->whereDate('start_time', $date);
    }

    /** Dias (YYYY-MM-DD) que têm pelo menos uma reunião no mês/ano. */
    public static function daysWithMeetingsInMonth(int $year, int $month): array
    {
        return static::query()
            ->selectRaw('DISTINCT DATE(start_time) as day')
            ->whereYear('start_time', $year)
            ->whereMonth('start_time', $month)
            ->pluck('day')
            ->map(fn ($d) => (string) $d)
            ->toArray();
    }

    /** Reuniões onde o usuário é organizador OU participante. */
    public function scopeVisibleTo(Builder $q, int $userId): Builder
    {
        return $q->where(function ($sub) use ($userId) {
            $sub->where('organizer_id', $userId)
                ->orWhereHas('participants', fn ($p) => $p->where('users.id', $userId));
        });
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS
    // ──────────────────────────────────────────────────────────────────────────

    /** Cor do ponto no calendário baseada na plataforma / tipo. */
    public function colorClass(): string
    {
        return match (true) {
            $this->online_platform === 'google_meet' => 'bg-blue-400',
            $this->online_platform === 'zoom'        => 'bg-indigo-400',
            $this->online_platform === 'teams'       => 'bg-purple-400',
            $this->location_type   === 'presencial'  => 'bg-emerald-400',
            default                                  => 'bg-amber-400',
        };
    }

    /** Duração da reunião no formato "1h 30min" / "45min". */
    public function durationLabel(): string
    {
        if (! $this->start_time || ! $this->end_time) {
            return '—';
        }

        $minutes = (int) $this->start_time->diffInMinutes($this->end_time);

        if ($minutes <= 0) return '—';

        $h = intdiv($minutes, 60);
        $m = $minutes % 60;

        return match (true) {
            $h > 0 && $m > 0 => "{$h}h {$m}min",
            $h > 0           => "{$h}h",
            default          => "{$m}min",
        };
    }

    /** Rótulo amigável para o tipo (plataforma ou presencial). */
    public function typeLabel(): string
    {
        if ($this->location_type === 'presencial') {
            return 'Reunião Presencial';
        }

        return match ($this->online_platform) {
            'google_meet' => 'Google Meet',
            'zoom'        => 'Zoom',
            'teams'       => 'Microsoft Teams',
            default       => 'Reunião Online',
        };
    }

    /** Label amigável da localização. */
    public function locationLabel(): string
    {
        if ($this->location_type === 'presencial') {
            return $this->room?->name ?? 'Sala presencial';
        }

        return match ($this->online_platform) {
            'google_meet' => 'Google Meet',
            'zoom'        => 'Zoom',
            'teams'       => 'Microsoft Teams',
            default       => $this->online_link ?? 'Online',
        };
    }
}
