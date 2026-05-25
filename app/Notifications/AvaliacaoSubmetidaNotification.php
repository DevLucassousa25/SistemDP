<?php

namespace App\Notifications;

class AvaliacaoSubmetidaNotification extends AppNotification
{
    public function __construct(
        private readonly string $avaliado,
        private readonly string $ciclo
    ) {}

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Avaliação de desempenho submetida',
            'message' => "A avaliação de {$this->avaliado} foi submetida no ciclo {$this->ciclo}.",
            'icon'    => 'trending-up',
            'color'   => 'amber',
            'url'     => route('avaliacoes'),
            'module'  => 'avaliacoes',
        ];
    }
}
