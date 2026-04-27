<?php

namespace App\Livewire\Pages\Users;
use App\Livewire\SecureComponent;

use App\Models\User;
use Livewire\Attributes\Locked;

class Details extends SecureComponent
{
    #[Locked]
    public int $userId;
    public ?User $user = null;

    protected $listeners = [
        'userUpdated'     => 'refreshUser',
        'userDeactivated' => 'refreshUser',
        'activateUser'    => 'refreshUser',
    ];

    public function mount(int $id): void
    {
        // Apenas Admin/RH-DP podem acessar qualquer perfil de usuário.
        // O middleware de rota já bloqueia na camada HTTP; esta verificação
        // protege contra chamadas diretas ao componente Livewire.
        $this->requireRhOrAdmin();

        $this->userId = $id;
        $this->loadUser();
    }

    public function loadUser(): void
    {
        $this->user = User::with(['department', 'accessProfile'])
            ->select([
                'id',
                'name',
                'email',
                'department_id',
                'access_profile_id',
                'position',
                'is_active',
            ])
            ->findOrFail($this->userId);
    }


    public function refreshUser(): void
    {
        $this->loadUser();
    }


    public function render()
    {
        return view('livewire.pages.users.details');
    }
}
