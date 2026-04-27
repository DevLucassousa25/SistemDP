<?php

namespace App\Livewire\Pages\Users;

use App\Livewire\SecureComponent;

class Index extends SecureComponent
{
    public function mount(): void
    {
        // Segunda camada de defesa: bloqueia requisições diretas ao componente
        // que porventura burlem o middleware de rota (ex: chamadas Livewire).
        $this->requireRhOrAdmin();
    }

    public function openModal(): void
    {
        $this->requireRhOrAdmin(); // re-verifica antes de qualquer ação
        $this->dispatch('openUserModal');
    }

    public function render()
    {
        return view('livewire.pages.users.index');
    }
}
