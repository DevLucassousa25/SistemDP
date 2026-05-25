<?php

namespace App\Notifications;

use App\Models\Survey;

class PesquisaRespondidaNotification extends AppNotification
{
    public function __construct(private readonly Survey $pesquisa) {}

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Pesquisa respondida',
            'message' => "Uma nova resposta foi registrada para \"{$this->pesquisa->title}\".",
            'icon'    => 'bar-chart-2',
            'color'   => 'teal',
            'url'     => route('pesquisas.resultados', $this->pesquisa->id),
            'module'  => 'pesquisas',
        ];
    }
}
