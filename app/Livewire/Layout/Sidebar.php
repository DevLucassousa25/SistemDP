<?php

namespace App\Livewire\Layout;
use App\Livewire\SecureComponent;

use Livewire\Attributes\On;

class Sidebar extends SecureComponent
{
     public $open = false;
    public $collapsed = false;

    public $menus = [
        'principal'   => true,
        'gestao'      => true,
        'pessoas'     => true,
        'colaboracao' => true,
    ];

    public function close()
    {
        $this->open = false;
    }

    #[On('call-sidebar')]
    public function toggle()
    {
        $this->open = !$this->open;
    }

    public function toggleCollapse()
    {
        $this->collapsed = !$this->collapsed;
    }

    public function toggleMenu($menu)
    {
        $this->menus[$menu] = !$this->menus[$menu];
    }

    public function render()
    {
        return view('livewire.layout.sidebar');
    }
}
