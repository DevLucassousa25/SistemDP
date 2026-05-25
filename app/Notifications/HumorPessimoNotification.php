<?php

namespace App\Notifications;

class HumorPessimoNotification extends AppNotification
{
    public function __construct(
        private readonly string $nomeColaborador,
        private readonly string $tipo  // 'gerente' | 'rh'
    ) {}

    public function toArray(object $notifiable): array
    {
        if ($this->tipo === 'gerente') {
            return [
                'title'   => 'Atenção: colaborador precisa de suporte',
                'message' => "{$this->nomeColaborador} relatou estar se sentindo péssimo hoje. Entre em contato para oferecer apoio.",
                'icon'    => 'heart',
                'color'   => 'rose',
                'url'     => route('time'),
                'module'  => 'mood',
            ];
        }

        return [
            'title'   => 'Alerta de bem-estar — DP/RH',
            'message' => "{$this->nomeColaborador} sinalizou humor péssimo hoje. Recomenda-se sondar a situação.",
            'icon'    => 'heart',
            'color'   => 'rose',
            'url'     => route('users'),
            'module'  => 'mood',
        ];
    }
}
