<?php

namespace App\Notifications;

use App\Models\Task;

class TarefaAtribuidaNotification extends AppNotification
{
    public function __construct(
        private readonly Task   $tarefa,
        private readonly string $atribuidaPor
    ) {}

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Nova tarefa atribuída',
            'message' => "{$this->atribuidaPor} atribuiu a tarefa \"{$this->tarefa->title}\" para você.",
            'icon'    => 'check-square',
            'color'   => 'blue',
            'url'     => route('tarefas'),
            'module'  => 'tarefas',
        ];
    }
}
