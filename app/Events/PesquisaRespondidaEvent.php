<?php

namespace App\Events;

use App\Models\Survey;
use Illuminate\Foundation\Events\Dispatchable;

class PesquisaRespondidaEvent
{
    use Dispatchable;

    public function __construct(public readonly Survey $pesquisa) {}
}
