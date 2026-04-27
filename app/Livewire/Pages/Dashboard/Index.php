<?php

namespace App\Livewire\Pages\Dashboard;
use App\Livewire\SecureComponent;


class Index extends SecureComponent
{
    public function render()
    {
        return view('livewire.pages.dashboard.index');
    }
}
