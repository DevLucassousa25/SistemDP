<?php

namespace App\Livewire\Components\Ui\Modal;
use App\Livewire\SecureComponent;


class UserCreatedModal extends SecureComponent
{
    public $open = false;
    public $type = 'create'; // create | update

    protected $listeners = [
        'userCreated' => 'showCreated',
        'userUpdated' => 'showUpdated',
    ];

    public function showCreated()
    {
        $this->type = 'create';
        $this->open = true;
    }

    public function showUpdated()
    {
        $this->type = 'update';
        $this->open = true;
    }

    public function close()
    {
        $this->open = false;
    }

    public function render()
    {
        return view('livewire.components.ui.modal.user-created-modal');
    }
}
