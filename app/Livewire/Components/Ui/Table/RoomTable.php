<?php

namespace App\Livewire\Components\Ui\Table;
use App\Livewire\SecureComponent;

use App\Models\Room;
use Livewire\WithPagination;

class RoomTable extends SecureComponent
{
    use WithPagination;

    public string $busca            = '';
    public string $filtroStatus     = '';
    public string $filtroAndar      = '';
    public string $filtroCapacidade = '';
    public int    $perPage          = 5;


    // ── Reset de página ao alterar qualquer filtro ──────────────


    protected $listeners = [
        'room-created' => 'refreshList',
        'room-updated' => 'refreshList',
    ];

    public function updatingBusca(): void
    {
        $this->resetPage();
    }
    public function updatingFiltroStatus(): void
    {
        $this->resetPage();
    }
    public function updatingFiltroAndar(): void
    {
        $this->resetPage();
    }
    public function updatingFiltroCapacidade(): void
    {
        $this->resetPage();
    }

    // ── Render ──────────────────────────────────────────────────

    public function render()
    {
        Room::with('reservations')
            ->get()
            ->each(function (Room $sala) {
                // Manutenção: só sai automaticamente se o prazo já expirou
                if ($sala->status === 'manutencao') {
                    if ($sala->maintenance_end && $sala->maintenance_end->isPast()) {
                        $novoStatus = static::computarStatusSala($sala);

                        $sala->update([
                            'status'            => $novoStatus,
                            'maintenance_start' => null,
                            'maintenance_end'   => null,
                        ]);
                    }
                    return; // não sobrescreve manutenção ainda ativa
                }

                $novoStatus = static::computarStatusSala($sala);

                if ($sala->status !== $novoStatus) {
                    $sala->update(['status' => $novoStatus]);
                }
            });

        $salas = Room::query()
            ->when(
                $this->busca,
                fn($q) => $q->where('name', 'like', "%{$this->busca}%")
                    ->orWhere('location', 'like', "%{$this->busca}%")
            )
            ->when(
                $this->filtroStatus,
                fn($q) => $q->where('status', $this->filtroStatus)
            )
            ->when(
                $this->filtroAndar,
                fn($q) => $q->where('location', $this->filtroAndar)
            )
            ->when(
                $this->filtroCapacidade,
                fn($q) => match ($this->filtroCapacidade) {
                    'pequena' => $q->where('capacity', '<=', 6),
                    'media'   => $q->whereBetween('capacity', [7, 15]),
                    'grande'  => $q->where('capacity', '>=', 16),
                    default   => $q,
                }
            )
            ->with('images')
            ->latest()
            ->paginate($this->perPage);

        // Lista de andares únicos para popular o dropdown
        $andares = Room::query()
            ->select('location')
            ->distinct()
            ->orderBy('location')
            ->pluck('location');

        return view('livewire.components.ui.table.room-table', compact('salas', 'andares'));
    }

    // ── Ações ───────────────────────────────────────────────────

    public function editar(int $id): void
    {
        $this->dispatch('abrir-modal-edicao', salaId: $id);
    }

    public function verDetalhes(int $id)
    {
        return redirect()->route('rooms.details', ['id' => $id]);
    }

    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function limparFiltros(): void
    {
        $this->busca = '';
        $this->filtroAndar = '';
        $this->filtroStatus = '';
        $this->filtroCapacidade = '';
        $this->resetPage();
    }

    /**
     * Calcula o status dinâmico de uma sala com base nas reservas:
     *  - "ocupada"    → tem reserva confirmada acontecendo agora (now ∈ [start, end]).
     *  - "reservada"  → tem reserva confirmada futura (start > now).
     *  - "disponivel" → nenhuma das anteriores.
     *
     * Obs.: "manutencao" é tratado fora deste método.
     */
    public static function computarStatusSala(Room $sala): string
    {
        $agora = now();

        $emUso = $sala->reservations()
            ->where('status', 'confirmada')
            ->where('start_time', '<=', $agora)
            ->where('end_time',   '>=', $agora)
            ->exists();

        if ($emUso) {
            return 'ocupada';
        }

        $temFutura = $sala->reservations()
            ->where('status', 'confirmada')
            ->where('start_time', '>', $agora)
            ->exists();

        return $temFutura ? 'reservada' : 'disponivel';
    }
}
