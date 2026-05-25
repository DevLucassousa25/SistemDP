<?php

namespace App\Notifications;

use App\Models\OkrKeyResult;

class OkrCheckinRealizadoNotification extends AppNotification
{
    public function __construct(
        private readonly OkrKeyResult $kr,
        private readonly string       $realizadoPor,
        private readonly float        $novoValor,
        private readonly float        $progresso
    ) {}

    public function toArray(object $notifiable): array
    {
        $progressoFmt = number_format($this->progresso, 0) . '%';

        return [
            'title'   => 'Check-in registrado',
            'message' => "{$this->realizadoPor} fez um check-in em \"{$this->kr->title}\" — progresso agora em {$progressoFmt}.",
            'icon'    => 'trending-up',
            'color'   => 'emerald',
            'url'     => route('okrs'),
            'module'  => 'okrs',
        ];
    }
}
