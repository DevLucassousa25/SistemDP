<?php

namespace App\Livewire\Components\Ouvidoria;

use App\Livewire\SecureComponent;
use App\Models\Manifestacao;
use Illuminate\Support\Facades\Auth;

class OuvidoriaStats extends SecureComponent
{
    public int $total       = 0;
    public int $emAnalise   = 0;
    public int $emAndamento = 0;
    public int $concluidos  = 0;

    protected $listeners = [
        'manifestacaoCriada'     => 'recarregar',
        'manifestacaoAtualizada' => 'recarregar',
        'manifestacaoExcluida'   => 'recarregar',
        'refresh'                => 'recarregar',
    ];

    public function mount(): void
    {
        $this->recarregar();
    }

    public function recarregar(): void
    {
        $user        = Auth::user();
        $podeVerTudo = $user->isRhOuDp();

        // Usuário comum vê apenas as próprias manifestações
        $base = Manifestacao::when(! $podeVerTudo, fn ($q) => $q->where('user_id', $user->id));

        $this->total       = (clone $base)->count();
        $this->emAnalise   = (clone $base)->emAnalise()->count();
        $this->emAndamento = (clone $base)->emAndamento()->count();
        $this->concluidos  = (clone $base)->concluidas()->count();
    }

    public function render()
    {
        return view('livewire.components.ouvidoria.ouvidoria-stats');
    }
}
