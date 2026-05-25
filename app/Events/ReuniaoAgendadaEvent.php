<?php

namespace App\Events;

use App\Models\Meeting;
use Illuminate\Foundation\Events\Dispatchable;

/** Despachado quando uma nova reunião é agendada ou participantes são adicionados. */
class ReuniaoAgendadaEvent
{
    use Dispatchable;

    /**
     * @param  \Illuminate\Support\Collection<int, \App\Models\User>  $participantes
     */
    public function __construct(
        public readonly Meeting $reuniao,
        public readonly \Illuminate\Support\Collection $participantes,
    ) {}
}
