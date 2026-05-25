<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class FeedbackRecebidoEvent
{
    use Dispatchable;

    public function __construct(
        public readonly User   $destinatario,
        public readonly string $remetente,
        public readonly string $tipo = 'feedback',
    ) {}
}
