<?php

namespace App\Notifications;

class FeedbackRecebidoNotification extends AppNotification
{
    public function __construct(
        private readonly string $remetente,
        private readonly string $tipo = 'feedback'
    ) {}

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Novo feedback recebido',
            'message' => "{$this->remetente} enviou um {$this->tipo} para você.",
            'icon'    => 'message-circle',
            'color'   => 'violet',
            'url'     => route('feedback'),
            'module'  => 'feedback',
        ];
    }
}
