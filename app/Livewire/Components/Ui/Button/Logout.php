<?php

namespace App\Livewire\Components\Ui\Button;
use App\Livewire\SecureComponent;

use Illuminate\Support\Facades\Auth;

class Logout extends SecureComponent
{
    public function logout(): void
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return <<<'HTML'
            <button wire:click="logout" wire:confirm="Deseja realmente sair?">
                {{ $slot ?? 'Sair' }}
            </button>
        HTML;
    }
}
