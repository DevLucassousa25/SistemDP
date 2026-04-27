<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class room extends Model
{
    protected $table = 'rooms';

    protected $fillable = [
        'name',
        'capacity',
        'location',
        'status',
        'maintenance_start',
        'maintenance_end',
        'has_tv',
        'has_wifi',
        'has_video_conference',
        'has_projector',
        'has_coffee',
        'has_whiteboard',
    ];

    protected $casts = [
        'capacity'             => 'integer',
        'maintenance_start'    => 'datetime',
        'maintenance_end'      => 'datetime',
        'has_tv'               => 'boolean',
        'has_wifi'             => 'boolean',
        'has_video_conference' => 'boolean',
        'has_projector'        => 'boolean',
        'has_coffee'           => 'boolean',
        'has_whiteboard'       => 'boolean',
    ];


    public function images()
    {
        return $this->hasMany(room_images::class, 'room_id')->orderBy('order');
    }


    public function reservations(): HasMany
    {
        return $this->hasMany(Reservations::class);
    }

    // Reservas de hoje confirmadas
    public function reservasHoje()
    {
        return $this->reservations()
            ->whereDate('start_time', today())
            ->where('status', 'confirmada')
            ->orderBy('start_time');
    }

    // Próximo horário livre hoje

    public function proximoHorarioLivre(): string
    {
        $agora = Carbon::now('America/Sao_Paulo');

        // Verifica se existe reserva acontecendo AGORA (já começou e ainda não terminou)
        $reservaAtual = $this->reservations()
            ->where('status', 'confirmada')
            ->where('start_time', '<=', $agora) // ← estava faltando isso
            ->where('end_time', '>=', $agora)
            ->first();

        if ($reservaAtual) {
            return 'Ocupada até ' . Carbon::parse($reservaAtual->end_time)
                ->timezone('America/Sao_Paulo')
                ->format('H:i');
        }

        // Próxima reserva futura
        $proxima = $this->reservations()
            ->where('status', 'confirmada')
            ->where('start_time', '>', $agora)
            ->orderBy('start_time')
            ->first();

        if ($proxima) {
            return 'Livre — próxima reserva às ' . Carbon::parse($proxima->start_time)
                ->timezone('America/Sao_Paulo')
                ->format('H:i'); // ← melhorei a mensagem também
        }

        return 'Livre agora';
    }

    // Primeira imagem como capa
    public function getCoverAttribute(): ?string
    {
        return $this->images->first()?->url;
    }
}
