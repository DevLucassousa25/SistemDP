<?php

namespace App\Events;

use App\Models\RhCandidatoTeste;
use Illuminate\Foundation\Events\Dispatchable;

class TesteOnlineConcluidoEvent
{
    use Dispatchable;

    public function __construct(public readonly RhCandidatoTeste $tentativa) {}
}
