<?php

namespace App\Notifications;

use App\Models\Post;

class PublicacaoObrigatoriaNotification extends AppNotification
{
    public function __construct(private readonly Post $post) {}

    public function toArray(object $notifiable): array
    {
        return [
            'title'   => 'Nova publicação obrigatória',
            'message' => "Você tem uma leitura obrigatória: \"{$this->post->titulo}\".",
            'icon'    => 'book-open',
            'color'   => 'rose',
            'url'     => route('publicacoes'),
            'module'  => 'publicacoes',
        ];
    }
}
