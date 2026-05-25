<?php

namespace App\Notifications;

use App\Models\RhSolicitacao;

class RhSolicitacaoAtualizadaNotification extends AppNotification
{
    private static array $mensagens = [
        'em_andamento' => 'Sua solicitação está sendo analisada pelo RH.',
        'concluida'    => 'Sua solicitação foi concluída! Verifique se há arquivo anexado.',
        'cancelada'    => 'Sua solicitação foi cancelada pelo RH.',
    ];

    private static array $cores = [
        'em_andamento' => 'blue',
        'concluida'    => 'green',
        'cancelada'    => 'red',
    ];

    private static array $icones = [
        'em_andamento' => 'loader',
        'concluida'    => 'check-circle',
        'cancelada'    => 'x-circle',
    ];

    public function __construct(
        private readonly RhSolicitacao $solicitacao,
        private readonly string        $nomeRh,
    ) {}

    public function toArray(object $notifiable): array
    {
        $status  = $this->solicitacao->status;
        $tipo    = \App\Models\RhSolicitacao::$tipoLabels[$this->solicitacao->tipo] ?? $this->solicitacao->tipo;
        $label   = \App\Models\RhSolicitacao::$statusLabels[$status] ?? $status;

        return [
            'title'   => "Solicitação {$label}",
            'message' => self::$mensagens[$status] ?? "Sua solicitação de {$tipo} foi atualizada pelo RH.",
            'icon'    => self::$icones[$status]    ?? 'bell',
            'color'   => self::$cores[$status]     ?? 'blue',
            'url'     => route('solicitacoes'),
            'module'  => 'solicitacoes',
        ];
    }
}
