<?php

namespace App\Notifications;

use App\Models\Meeting;

class ReuniaoAgendadaNotification extends AppNotification
{
    public function __construct(private readonly Meeting $reuniao) {}

    public function toArray(object $notifiable): array
    {
        $quando = $this->reuniao->scheduled_at
            ? \Carbon\Carbon::parse($this->reuniao->scheduled_at)->format('d/m/Y \à\s H:i')
            : 'em breve';

        return [
            'title'   => 'Reunião agendada',
            'message' => "Você foi convidado para \"{$this->reuniao->title}\" — {$quando}.",
            'icon'    => 'calendar',
            'color'   => 'indigo',
            'url'     => route('reunioes'),
            'module'  => 'reunioes',
        ];
    }
}
