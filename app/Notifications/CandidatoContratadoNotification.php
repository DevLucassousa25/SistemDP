<?php

namespace App\Notifications;

use App\Models\RhOnboarding;

class CandidatoContratadoNotification extends AppNotification
{
    public function __construct(private readonly RhOnboarding $onboarding) {}

    public function toArray(object $notifiable): array
    {
        $nome  = $this->onboarding->user->name ?? 'Novo funcionário';
        $cargo = $this->onboarding->cargo ?? '—';

        return [
            'title'   => 'Novo funcionário contratado!',
            'message' => "{$nome} foi contratado(a) como {$cargo}. O onboarding foi iniciado automaticamente.",
            'icon'    => 'user-check',
            'color'   => 'green',
            'url'     => route('rh.onboarding'),
            'module'  => 'onboarding',
        ];
    }
}
