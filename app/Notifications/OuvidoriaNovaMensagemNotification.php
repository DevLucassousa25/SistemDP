<?php

namespace App\Notifications;

use App\Models\Manifestacao;

class OuvidoriaNovaMensagemNotification extends AppNotification
{
    public function __construct(private readonly Manifestacao $manifestacao) {}

    public function toArray(object $notifiable): array
    {
        $protocolo = $this->manifestacao->protocolo ?? "#{$this->manifestacao->id}";

        return [
            'title'   => 'Nova manifestação na Ouvidoria',
            'message' => "Protocolo {$protocolo} — {$this->manifestacao->tipo_label ?? 'Manifestação'} recebida e aguardando análise.",
            'icon'    => 'megaphone',
            'color'   => 'red',
            'url'     => route('ouvidoria'),
            'module'  => 'ouvidoria',
        ];
    }
}
