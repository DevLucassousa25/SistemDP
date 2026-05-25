<?php

namespace App\Events;

use App\Models\FeedPost;
use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;

class FeedInteracaoEvent
{
    use Dispatchable;

    public function __construct(
        public readonly FeedPost $post,
        public readonly User     $destinatario,
        public readonly string   $atorNome,
        public readonly string   $tipo,  // 'curtiu' | 'comentou' | 'mencionou'
    ) {}
}
