<?php

namespace App\Events;

use App\Models\Manifestacao;
use Illuminate\Foundation\Events\Dispatchable;

class OuvidoriaNovaMensagemEvent
{
    use Dispatchable;

    public function __construct(public readonly Manifestacao $manifestacao) {}
}
