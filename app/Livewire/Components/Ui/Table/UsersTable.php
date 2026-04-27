<?php

namespace App\Livewire\Components\Ui\Table;

use App\Livewire\SecureComponent;
use App\Models\User;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class UsersTable extends SecureComponent
{
    use WithPagination;

    // #[Url] garante que cada filtro apareça na URL (com except evitando
    // parâmetros vazios), sem conflitar com o rastreamento de "page" interno
    // do Livewire 3 — que é a causa do bug "não deixa voltar".
    #[Url(except: '')]
    public string $search = '';

    #[Url(except: '')]
    public string $status = '';

    #[Url(except: '')]
    public string $department = '';

    #[Url(except: '')]
    public string $accessProfile = '';

    protected $listeners = [
        'userCreated'     => 'refreshList',
        'userUpdated'     => 'refreshList',
        'userDeactivated' => 'refreshList',
        'activateUser'    => 'refreshList',
    ];

    public function refreshList(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatingStatus(): void
    {
        $this->resetPage();
    }

    public function updatingDepartment(): void
    {
        $this->resetPage();
    }

    // Hook ausente: trocar o filtro de Perfil de Acesso também deve
    // voltar para a primeira página.
    public function updatingAccessProfile(): void
    {
        $this->resetPage();
    }

    public function limparFiltros(): void
    {
        $this->search        = '';
        $this->department    = '';
        $this->status        = '';
        $this->accessProfile = '';
        $this->resetPage(); // corrigido: estava faltando
    }

    public function render()
    {
        $users = User::with(['department', 'accessProfile'])
            ->when(
                $this->search,
                fn ($q) => $q->where(function ($query) {
                    $query->where('name',  'like', "%{$this->search}%")
                          ->orWhere('email', 'like', "%{$this->search}%");
                })
            )
            ->when(
                $this->department,
                fn ($q) => $q->whereHas(
                    'department',
                    fn ($q2) => $q2->where('name', $this->department)
                )
            )
            ->when(
                $this->status !== '',
                fn ($q) => $q->where('is_active', $this->status)
            )
            ->when(
                $this->accessProfile,
                fn ($q) => $q->whereHas(
                    'accessProfile',
                    fn ($q2) => $q2->where('id', $this->accessProfile)
                )
            )
            ->paginate(10);

        return view('livewire.components.ui.table.users-table', compact('users'));
    }
}
