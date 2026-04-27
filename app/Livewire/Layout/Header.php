<?php

namespace App\Livewire\Layout;
use App\Livewire\SecureComponent;


class Header extends SecureComponent
{
    public function abrirMenu()
    {
        $this->dispatch('call-sidebar');
    }

    public function render()
    {
        return view('livewire.layout.header');
    }
}
