<?php

namespace App\Notifications;

use App\Models\DpiPlan;

class DpiPlanoAtualizacaoNotification extends AppNotification
{
    public function __construct(
        private readonly DpiPlan $plano,
        private readonly string  $acao  // 'submetido' | 'aprovado' | 'rejeitado'
    ) {}

    public function toArray(object $notifiable): array
    {
        $owner = $this->plano->user?->name ?? 'Colaborador';

        $msgs = [
            'submetido'  => "O DPI de {$owner} foi submetido para aprovação.",
            'aprovado'   => "Seu DPI foi aprovado!",
            'rejeitado'  => "Seu DPI foi rejeitado. Verifique o feedback.",
        ];

        $colors = ['submetido' => 'amber', 'aprovado' => 'green', 'rejeitado' => 'red'];

        return [
            'title'   => 'DPI — Plano de Desenvolvimento',
            'message' => $msgs[$this->acao] ?? $msgs['submetido'],
            'icon'    => 'target',
            'color'   => $colors[$this->acao] ?? 'amber',
            'url'     => route('dpi'),
            'module'  => 'dpi',
        ];
    }
}
