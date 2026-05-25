<?php

namespace App\Events;

use App\Models\RhCurriculo;
use Illuminate\Foundation\Events\Dispatchable;

class NovoCurriculoEvent
{
    use Dispatchable;

    public function __construct(public readonly RhCurriculo $curriculo) {}
}
