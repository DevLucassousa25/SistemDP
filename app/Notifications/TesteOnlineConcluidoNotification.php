<?php

namespace App\Notifications;

use App\Models\RhCandidatoTeste;

class TesteOnlineConcluidoNotification extends AppNotification
{
    public function __construct(private readonly RhCandidatoTeste $tentativa) {}

    public function toArray(object $notifiable): array
    {
        $nome  = $this->tentativa->curriculo?->nome ?? 'Candidato';
        $teste = $this->tentativa->teste?->titulo    ?? 'Teste online';
        $nota  = $this->tentativa->nota !== null ? " (Nota: {$this->tentativa->nota})" : '';

        return [
            'title'   => 'Teste online concluído',
            'message' => "{$nome} concluiu o teste \"{$teste}\"{$nota}.",
            'icon'    => 'clipboard-check',
            'color'   => 'green',
            'url'     => route('rh.curriculos'),
            'module'  => 'rh',
        ];
    }
}
