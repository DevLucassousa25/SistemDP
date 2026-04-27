<?php

namespace App\Livewire\Components\Ui\Modal;
use App\Livewire\SecureComponent;

use App\Models\room;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;

class DeleteRoomModal extends SecureComponent
{
    public bool $open = false;
    public string $roomName = '';

    // #[Locked] impede que o cliente substitua o roomId via JS/wire
    #[Locked]
    public ?int $roomId = null;

    protected $listeners = ['confirmarExclusao' => 'abrir'];

    public function abrir(int $id): void
    {
        // Autorização: apenas RH/Admin pode excluir salas
        if (! Auth::user()->isRhOuDp()) {
            abort(403, 'Sem permissão para excluir salas.');
        }

        $room = Room::with(['images', 'reservations'])->findOrFail($id);

        $this->roomId   = $room->id;
        $this->roomName = $room->name;
        $this->open     = true;
    }

    public function fechar(): void
    {
        $this->open     = false;
        $this->roomId   = null;
        $this->roomName = '';
    }

    public function excluir(): void
    {
        // Re-valida autorização (defesa em profundidade)
        if (! Auth::user()->isRhOuDp()) {
            abort(403);
        }

        $room = Room::findOrFail($this->roomId);

        foreach ($room->images as $image) {
            Storage::disk('public')->delete($image->path);
            $image->delete();
        }

        // Cancela reservas ativas antes de excluir
        $room->reservations()->delete();
        $room->delete();

        $this->fechar();
        $this->dispatch('roomDeleted');
        $this->redirect(route('rooms'), navigate: true);
    }

    public function render()
    {
        return view('livewire.components.ui.modal.delete-room-modal');
    }
}
