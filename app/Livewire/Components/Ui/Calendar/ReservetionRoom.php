<?php

namespace App\Livewire\Components\Ui\Calendar;
use App\Livewire\SecureComponent;

use App\Models\Reservations;
use Carbon\Carbon;
use Livewire\Attributes\On;

class ReservetionRoom extends SecureComponent
{

    public int $roomId;
    public array $reservas = [];
    public int $mes;
    public int $ano;
    public array $stats = ['total' => 0, 'confirmadas' => 0, 'canceladas' => 0];

    public function mount($id): void
    {
        $this->roomId = $id;
        $this->mes = now()->month;
        $this->ano = now()->year;
        $this->carregarReservas();
    }

    #[On('reservaCriada')]
    public function atualizarCalendario(): void
    {
        $this->carregarReservas();
    }

    public function mesAnterior(): void
    {
        if ($this->mes === 1) {
            $this->mes = 12;
            $this->ano--;
        } else {
            $this->mes--;
        }
        $this->carregarReservas();
    }

    public function proximoMes(): void
    {
        if ($this->mes === 12) {
            $this->mes = 1;
            $this->ano++;
        } else {
            $this->mes++;
        }
        $this->carregarReservas();
    }

    private function carregarReservas(): void
    {
        $inicio = Carbon::create($this->ano, $this->mes, 1)->startOfMonth();
        $fim    = Carbon::create($this->ano, $this->mes, 1)->endOfMonth();

        $reservas = Reservations::where('room_id', $this->roomId)
            ->whereBetween('start_time', [$inicio, $fim])
            ->orderBy('start_time')
            ->get();

        $agrupadas = [];

        foreach ($reservas as $r) {
            // parse com timezone local para agrupar pelo dia correto
            $dia = Carbon::parse($r->start_time)->timezone(config('app.timezone'))->format('Y-m-d');

            $agrupadas[$dia][] = [
                'title'          => $r->is_maintenance ? 'Manutenção' : $r->title,
                'start'          => Carbon::parse($r->start_time)->timezone(config('app.timezone'))->format('H:i'),
                'end'            => Carbon::parse($r->end_time)->timezone(config('app.timezone'))->format('H:i'),
                'status'         => $r->status,
                'attendees'      => $r->attendees_count,
                'is_maintenance' => (bool) $r->is_maintenance,
            ];
        }

        $this->reservas = $agrupadas;

        $this->stats = [
            'total'       => $reservas->count(),
            'confirmadas' => $reservas->where('status', 'confirmada')->count(),
            'canceladas'  => $reservas->where('status', 'cancelada')->count(),
        ];
    }

    public function render()
    {

        return view('livewire.components.ui.calendar.reservetion-room', [
            'mes' => $this->mes,
            'ano' => $this->ano,
        ]);
    }
}
