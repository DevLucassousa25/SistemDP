<?php

namespace App\Livewire\Components\Room;
use App\Livewire\SecureComponent;

use App\Models\room;

class StatsCards extends SecureComponent
{
    public int $total        = 0;
    public int $disponiveis  = 0;
    public int $ocupadas     = 0;
    public int $reservadas   = 0;
    public int $manutencao   = 0;

    public function mount(): void
    {
        $this->carregarEstatisticas();
    }


    public function carregarEstatisticas(): void
    {
        $this->total       = room::count();
        $this->disponiveis = room::where('status', 'disponivel')->count();
        $this->ocupadas    = room::where('status', 'ocupada')->count();
        $this->reservadas  = room::where('status', 'reservada')->count();
        $this->manutencao  = room::where('status', 'manutencao')->count();
    }


    //On('sala-atualizada')
    public function atualizarEstatisticas(): void
    {
        $this->carregarEstatisticas();
    }

    public function render()
    {
        return view('livewire.components.room.stats-cards');
    }
}
