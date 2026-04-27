<?php

namespace App\Livewire\Components\User;
use App\Livewire\SecureComponent;

use App\Models\User;

class UserStats extends SecureComponent
{
    public $totalUsuarios;
    public $administradores;
    public $gestores;
    public $colaboradores;

    public function mount()
    {
        $this->totalUsuarios = User::count();

        $this->administradores = User::whereHas('accessProfile', function ($q) {
            $q->where('slug', 'administrator');
        })->count();

        $this->gestores = User::whereHas('accessProfile', function ($q) {
            $q->where('slug', 'manager');
        })->count();

        $this->colaboradores = User::whereHas('accessProfile', function ($q) {
            $q->where('slug', 'employee');
        })->count();
    }


    public function render()
    {
        return view('livewire.components.user.user-stats');
    }
}
