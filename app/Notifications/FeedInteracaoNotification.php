<?php

namespace App\Notifications;

use App\Models\FeedPost;

class FeedInteracaoNotification extends AppNotification
{
    public function __construct(
        private readonly FeedPost $post,
        private readonly string   $atorNome,
        private readonly string   $tipo  // 'curtiu' | 'comentou' | 'mencionou'
    ) {}

    public function toArray(object $notifiable): array
    {
        $msgs = [
            'curtiu'    => "{$this->atorNome} curtiu sua publicação.",
            'comentou'  => "{$this->atorNome} comentou na sua publicação.",
            'mencionou' => "{$this->atorNome} mencionou você em um comentário.",
        ];

        return [
            'title'   => 'Interação no Feed',
            'message' => $msgs[$this->tipo] ?? $msgs['curtiu'],
            'icon'    => 'heart',
            'color'   => 'violet',
            'url'     => route('feed'),
            'module'  => 'feed',
        ];
    }
}
