<?php

namespace App\Notifications;

use App\Models\RhSolicitacao;

class RhSolicitacaoNovaNotification extends AppNotification
{
    public function __construct(
        private readonly RhSolicitacao $solicitacao,
        private readonly string        $nomeFunc,
    ) {}

    public function toArray(object $notifiable): array
    {
        $tipo = \App\Models\RhSolicitacao::$tipoLabels[$this->solicitacao->tipo] ?? $this->solicitacao->tipo;

        return [
            'title'   => 'Nova solicitação de RH',
            'message' => "{$this->nomeFunc} solicitou: {$tipo}.",
            'icon'    => 'inbox',
            'color'   => 'amber',
            'url'     => route('rh.curriculos', ['aba' => 'solicitacoes']),
            'module'  => 'solicitacoes',
        ];
    }
}
