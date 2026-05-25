<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Carbon\Carbon;

class CalendarioEvento extends Model
{
    protected $table = 'calendario_eventos';

    protected $fillable = [
        'data',
        'titulo',
        'tipo',
        'descricao',
        'cor',
        'recorrente_anual',
        'created_by',
    ];

    protected $casts = [
        'data'             => 'date',
        'recorrente_anual' => 'boolean',
    ];

    // ── Tipos ──────────────────────────────────────────────────────────

    public const TIPOS = [
        'feriado_nacional'   => 'Feriado Nacional',
        'data_comemorativa'  => 'Data Comemorativa',
        'evento_empresa'     => 'Evento da Empresa',
    ];

    public const CORES_PADRAO = [
        'feriado_nacional'   => '#EF4444', // red-500
        'data_comemorativa'  => '#F59E0B', // amber-500
        'evento_empresa'     => '#3B82F6', // blue-500
    ];

    // ── Accessors ─────────────────────────────────────────────────────

    public function getTipoLabelAttribute(): string
    {
        return self::TIPOS[$this->tipo] ?? $this->tipo;
    }

    // ── Relationships ─────────────────────────────────────────────────

    public function criador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // ── Scopes ────────────────────────────────────────────────────────

    /**
     * Eventos de um mês/ano específico, incluindo recorrentes anuais.
     */
    public function scopeDoMes(Builder $query, int $ano, int $mes): Builder
    {
        return $query->where(function ($q) use ($ano, $mes) {
            // Eventos cadastrados exatamente neste mês/ano
            $q->whereYear('data', $ano)->whereMonth('data', $mes);
        })->orWhere(function ($q) use ($mes) {
            // Recorrentes anuais cujo mês coincide
            $q->where('recorrente_anual', true)->whereMonth('data', $mes);
        });
    }

    /**
     * Eventos numa data específica (considera recorrência anual).
     */
    public function scopeNaData(Builder $query, string $data): Builder
    {
        $carbon = Carbon::parse($data);
        return $query->where(function ($q) use ($carbon) {
            $q->whereDate('data', $carbon->toDateString());
        })->orWhere(function ($q) use ($carbon) {
            $q->where('recorrente_anual', true)
              ->whereMonth('data', $carbon->month)
              ->whereDay('data', $carbon->day);
        });
    }

    // ── Helpers ───────────────────────────────────────────────────────

    /**
     * Retorna array de eventos agrupados por dia-do-mês para uso no calendário.
     * [ '2026-05-01' => [evento, ...], ... ]
     */
    public static function mapDoMes(int $ano, int $mes): array
    {
        $eventos = static::doMes($ano, $mes)
            ->orderBy('data')
            ->get();

        $mapa = [];
        foreach ($eventos as $evento) {
            // Para recorrentes, usamos o ano/mês atual
            $chave = Carbon::create($ano, $mes, $evento->data->day)->toDateString();
            $mapa[$chave][] = $evento;
        }

        return $mapa;
    }

    /**
     * Verifica se há eventos na data e retorna coleção.
     */
    public static function eventosNaData(string $data): \Illuminate\Database\Eloquent\Collection
    {
        return static::naData($data)->get();
    }
}
