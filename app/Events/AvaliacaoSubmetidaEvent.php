<?php

namespace App\Events;

use Illuminate\Foundation\Events\Dispatchable;

class AvaliacaoSubmetidaEvent
{
    use Dispatchable;

    public function __construct(
        public readonly string $avaliado,
        public readonly string $ciclo,
    ) {}
}
