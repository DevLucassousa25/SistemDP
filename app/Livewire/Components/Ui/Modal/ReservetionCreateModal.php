<?php

namespace App\Livewire\Components\Ui\Modal;
use App\Livewire\SecureComponent;


class ReservetionCreateModal extends SecureComponent
{
    public function render()
    {
        return view('livewire.components.ui.modal.reservetion-create-modal');
    }
}
