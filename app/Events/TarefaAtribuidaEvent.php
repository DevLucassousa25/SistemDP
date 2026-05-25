<?php

namespace App\Events;

use App\Models\Task;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class TarefaAtribuidaEvent
{
    use Dispatchable;

    public function __construct(
        public readonly Task   $tarefa,
        public readonly User   $destinatario,
        public readonly string $atribuidaPor,
    ) {}
}
