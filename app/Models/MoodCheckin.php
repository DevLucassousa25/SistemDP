<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MoodCheckin extends Model
{
    protected $fillable = ['user_id', 'mood', 'note', 'checkin_date'];

    protected $casts = [
        'checkin_date' => 'date',
    ];

    // ── Constantes ────────────────────────────────────────────────────

    const MOODS = [
        'otimo'   => ['label' => 'Ótimo',   'icon' => 'laugh', 'color' => 'from-green-400 to-emerald-500',  'text' => 'text-green-600 dark:text-green-400',  'bg' => 'bg-green-50 dark:bg-green-900/30',  'border' => 'border-green-300 dark:border-green-700',  'hex' => '#10b981'],
        'bem'     => ['label' => 'Bem',     'icon' => 'smile', 'color' => 'from-blue-400 to-indigo-500',    'text' => 'text-blue-600 dark:text-blue-400',    'bg' => 'bg-blue-50 dark:bg-blue-900/30',    'border' => 'border-blue-300 dark:border-blue-700',    'hex' => '#6366f1'],
        'normal'  => ['label' => 'Normal',  'icon' => 'meh',   'color' => 'from-amber-400 to-yellow-500',   'text' => 'text-amber-600 dark:text-amber-400',  'bg' => 'bg-amber-50 dark:bg-amber-900/30',  'border' => 'border-amber-300 dark:border-amber-700',  'hex' => '#f59e0b'],
        'pessimo' => ['label' => 'Péssimo', 'icon' => 'frown', 'color' => 'from-rose-400 to-red-500',       'text' => 'text-rose-600 dark:text-rose-400',    'bg' => 'bg-rose-50 dark:bg-rose-900/30',    'border' => 'border-rose-300 dark:border-rose-700',    'hex' => '#f43f5e'],
    ];

    // ── Relacionamentos ───────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    // ── Helpers estáticos ─────────────────────────────────────────────

    public static function respondeuHoje(int $userId): bool
    {
        return static::where('user_id', $userId)
            ->where('checkin_date', today())
            ->exists();
    }

    public static function checkinHoje(int $userId): ?static
    {
        return static::where('user_id', $userId)
            ->where('checkin_date', today())
            ->first();
    }
}
