<?php

namespace App\Livewire\Components\Ui\Modal;
use App\Livewire\SecureComponent;


class AlertModal extends SecureComponent
{
    public bool $show = false;
    public string $title = '';
    public string $description = '';
    public string $type = 'info';

    protected $listeners = [
        'openAlert' => 'open',
        'closeAlert' => 'close',
    ];


    public function open(string $title, string $description, string $type = 'info'): void
    {
        $this->title       = $title;
        $this->description = $description;
        $this->type        = in_array($type, ['info', 'success', 'warning', 'error']) ? $type : 'info';
        $this->show        = true;
    }

    public function close(): void
    {
        $this->show = false;
    }

    public function render()
    {
        return view('livewire.components.ui.modal.alert-modal');
    }
}
