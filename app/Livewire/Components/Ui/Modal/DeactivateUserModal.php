<?php

namespace App\Livewire\Components\Ui\Modal;
use App\Livewire\SecureComponent;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Locked;

class DeactivateUserModal extends SecureComponent
{
    public bool $show = false;
    public string $userName = '';
    public string $status = '';

    // #[Locked] impede que o cliente manipule o userId via JS/wire
    #[Locked]
    public ?int $userId = null;

    protected $listeners = ['deactivateUser' => 'open'];

    public function open(int $id, string $status): void
    {
        // Autorização: apenas RH/Admin pode ativar/desativar usuários
        $authUser = Auth::user();
        if (! $authUser->isRhOuDp()) {
            abort(403, 'Sem permissão para realizar esta ação.');
        }

        // Não pode agir sobre si mesmo
        if ($authUser->id === $id) {
            $this->dispatch('openAlert',
                title: 'Ação não permitida',
                description: 'Você não pode alterar o status da sua própria conta.',
                type: 'error'
            );
            return;
        }

        $user = User::findOrFail($id);
        $this->userId   = $id;
        $this->userName = $user->name;
        $this->status   = $status;
        $this->show     = true;
    }

    public function confirm(): void
    {
        // Re-valida autorização no servidor (defesa em profundidade)
        $authUser = Auth::user();
        if (! $authUser->isRhOuDp() || $authUser->id === $this->userId) {
            abort(403);
        }

        $isActive = $this->status !== 'deactivate';

        User::where('id', $this->userId)->update(['is_active' => $isActive]);

        $title       = $isActive ? 'Usuário ativado'    : 'Usuário desativado';
        $description = $isActive
            ? 'O usuário foi ativado com sucesso.'
            : 'O usuário foi desativado com sucesso.';

        $this->reset();

        $this->dispatch('userDeactivated');
        $this->dispatch('openAlert', title: $title, description: $description, type: $isActive ? 'success' : 'info');
    }

    public function cancel(): void
    {
        $this->reset();
    }

    public function render()
    {
        return view('livewire.components.ui.modal.deactivate-user-modal');
    }
}
