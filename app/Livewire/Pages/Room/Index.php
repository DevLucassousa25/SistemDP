<?php

namespace App\Livewire\Pages\Room;
use App\Livewire\SecureComponent;


class Index extends SecureComponent
{
    public function render()
    {
        return view('livewire.pages.room.index');
    }
}
