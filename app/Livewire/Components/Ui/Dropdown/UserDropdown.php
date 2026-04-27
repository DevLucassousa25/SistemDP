<?php

namespace App\Livewire\Components\Ui\Dropdown;
use App\Livewire\SecureComponent;

use Illuminate\Support\Facades\Auth;

class UserDropdown extends SecureComponent
{
    public bool $open = false;

    public function toggle(): void
    {
        $this->open = !$this->open;
    }

    public function logout(): void
    {
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        $this->redirect(route('login'), navigate: true);
    }

    public function render()
    {
        return view('livewire.components.ui.dropdown.user-dropdown');
    }
}
