<?php

namespace App\Livewire\Components\Ui\Tab;
use App\Livewire\SecureComponent;

use App\Models\User;

class ProfileTabs extends SecureComponent
{
    public string $activeTab = 'informacoes';

    public $user = null;

    /** @var \Illuminate\Support\Collection */
    public $managers;

    public function mount($user): void
    {
        $this->user = $user;
        $this->loadManagers();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    /**
     * ID do perfil de Gestor/Gerente na tabela access_profiles.
     * Altere aqui caso o ID mude no banco.
     */
    private const MANAGER_PROFILE_ID = 3;

    private function loadManagers(): void
    {
        // Sem departamento → sem gestores
        if (!$this->user?->department_id) {
            $this->managers = collect();
            return;
        }

        // Busca todos os usuários com access_profile_id = 3 (Gerente)
        // no mesmo departamento, excluindo o próprio usuário
        $this->managers = User::query()
            ->where('access_profile_id', self::MANAGER_PROFILE_ID)
            ->where('department_id', $this->user->department_id)
            ->where('id', '!=', $this->user->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'email', 'department_id', 'access_profile_id']);
    }

    public function render()
    {
        return view('livewire.components.ui.tab.profile-tabs', [
            'managers' => $this->managers,
        ]);
    }
}
