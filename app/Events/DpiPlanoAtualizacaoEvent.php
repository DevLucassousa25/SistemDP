<?php

namespace App\Events;

use App\Models\DpiPlan;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class DpiPlanoAtualizacaoEvent
{
    use Dispatchable;

    /**
     * @param  string  $acao  'submetido' | 'aprovado' | 'rejeitado'
     * @param  User    $destinatario  Quem recebe a notificação
     */
    public function __construct(
        public readonly DpiPlan $plano,
        public readonly User    $destinatario,
        public readonly string  $acao,
    ) {}
}
