<?php

namespace App\Notifications;

use App\Models\OkrObjective;

class OkrObjetivoConcluidoNotification extends AppNotification
{
    public function __construct(
        private readonly OkrObjective $objetivo
    ) {}

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Objetivo concluído! 🎯',
            'message' => "O objetivo \"{$this->objetivo->title}\" atingiu 100% e foi marcado como concluído.",
            'icon'    => 'award',
            'color'   => 'violet',
            'url'     => route('okrs'),
            'module'  => 'okrs',
        ];
    }
}
