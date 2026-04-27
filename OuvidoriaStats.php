<?php

namespace App\Livewire\Ouvidoria;

use App\Models\Manifestacao;
use Livewire\Component;

class OuvidoriaStats extends Component
{
    // ─── Propriedades reativas ────────────────────────────────────────────────

    public int $total       = 0;
    public int $emAnalise   = 0;
    public int $emAndamento = 0;
    public int $concluidos  = 0;

    // ─── Listeners de eventos Livewire ───────────────────────────────────────

    protected $listeners = [
        'manifestacaoCriada'     => 'recarregar',
        'manifestacaoAtualizada' => 'recarregar',
        'manifestacaoExcluida'   => 'recarregar',
        'refresh'                => 'recarregar',
    ];

    // ─── Lifecycle ────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->recarregar();
    }

    // ─── Ações ───────────────────────────────────────────────────────────────

    /**
     * Recalcula todos os contadores em uma única query.
     * Usa os scopes do model para garantir que SoftDeletes seja respeitado.
     */
    public function recarregar(): void
    {
        $this->total       = Manifestacao::count();
        $this->emAnalise   = Manifestacao::emAnalise()->count();
        $this->emAndamento = Manifestacao::emAndamento()->count();
        $this->concluidos  = Manifestacao::concluidas()->count();
    }

    // ─── Render ───────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.ouvidoria.ouvidoria-stats');
    }
}
