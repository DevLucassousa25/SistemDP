<?php

namespace App\Notifications;

use App\Models\OkrKeyResult;

class OkrKrAtribuidoNotification extends AppNotification
{
    public function __construct(
        private readonly OkrKeyResult $kr,
        private readonly string       $atribuidoPor
    ) {}

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Novo Key Result atribuído',
            'message' => "{$this->atribuidoPor} atribuiu o Key Result \"{$this->kr->title}\" a você.",
            'icon'    => 'target',
            'color'   => 'indigo',
            'url'     => route('okrs'),
            'module'  => 'okrs',
        ];
    }
}
