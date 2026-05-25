<?php

namespace App\Events;

use App\Models\Post;
use Illuminate\Foundation\Events\Dispatchable;

class PublicacaoObrigatoriaEvent
{
    use Dispatchable;

    public function __construct(public readonly Post $post) {}
}
