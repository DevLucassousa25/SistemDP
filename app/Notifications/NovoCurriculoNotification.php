<?php

namespace App\Notifications;

use App\Models\RhCurriculo;

class NovoCurriculoNotification extends AppNotification
{
    public function __construct(private readonly RhCurriculo $curriculo) {}

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Novo currículo recebido',
            'message' => "O currículo de {$this->curriculo->nome} foi enviado e está aguardando análise.",
            'icon'    => 'file-text',
            'color'   => 'blue',
            'url'     => route('rh.curriculos'),
            'module'  => 'rh',
        ];
    }
}
