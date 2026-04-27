<?php

namespace App\Livewire\Pages\Team;

use App\Livewire\SecureComponent;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;

class Index extends SecureComponent
{
    /**
     * Filtro de busca por nome, e-mail ou cargo.
     */
    #[Url(as: 'q', except: '')]
    public string $search = '';

    /**
     * Filtro por status: 'todos' | 'ativos' | 'inativos'.
     */
    #[Url(except: 'todos')]
    public string $statusFilter = 'todos';

    /**
     * ID do membro selecionado para abrir no drawer lateral.
     */
    #[Locked]
    public ?int $memberIdDrawer = null;

    /**
     * Controla se o drawer está aberto.
     */
    public bool $drawerOpen = false;

    public function mount(): void
    {
        $this->requireAuth();
    }

    /**
     * Abre o drawer lateral exibindo os detalhes de um membro do time.
     * Só funciona para usuários do mesmo departamento (IDOR guard).
     */
    public function abrirDetalhesMembro(int $id): void
    {
        $this->requireAuth();

        $meuDept = Auth::user()?->department_id;

        if (! $meuDept) {
            return;
        }

        // Garante que o membro pertence ao mesmo departamento
        $existe = User::where('id', $id)
            ->where('department_id', $meuDept)
            ->exists();

        if (! $existe) {
            abort(403, 'Este usuário não pertence ao seu time.');
        }

        $this->memberIdDrawer = $id;
        $this->drawerOpen     = true;
    }

    /**
     * Fecha o drawer lateral.
     */
    public function fecharDrawer(): void
    {
        $this->drawerOpen     = false;
        $this->memberIdDrawer = null;
    }

    /**
     * Limpa todos os filtros.
     */
    public function limparFiltros(): void
    {
        $this->search       = '';
        $this->statusFilter = 'todos';
    }

    /**
     * Departamento do usuário autenticado (pode ser null).
     */
    #[Computed]
    public function meuDepartamento()
    {
        return Auth::user()?->department;
    }

    /**
     * Gerente do departamento do usuário autenticado.
     */
    #[Computed]
    public function gerente(): ?User
    {
        $dept = $this->meuDepartamento;

        if (! $dept) {
            return null;
        }

        return User::with('accessProfile')
            ->where('department_id', $dept->id)
            ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
            ->where('is_active', true)
            ->first();
    }

    /**
     * Lista dos membros do time (mesmo departamento), já ordenados.
     */
    #[Computed]
    public function membros()
    {
        $user = Auth::user();
        $dept = $this->meuDepartamento;

        if (! $dept) {
            return collect();
        }

        $query = User::with(['accessProfile', 'department'])
            ->where('department_id', $dept->id);

        if ($this->statusFilter === 'ativos') {
            $query->where('is_active', true);
        } elseif ($this->statusFilter === 'inativos') {
            $query->where('is_active', false);
        }

        if (trim($this->search) !== '') {
            $termo = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($termo) {
                $q->where('name', 'ilike', $termo)
                    ->orWhere('email', 'ilike', $termo)
                    ->orWhere('position', 'ilike', $termo);
            });
        }

        return $query
            ->orderByRaw("CASE WHEN id = ? THEN 0 ELSE 1 END", [$user->id])
            ->orderBy('is_active', 'desc')
            ->orderBy('name')
            ->get();
    }

    /**
     * Membro selecionado para exibição no drawer lateral.
     */
    #[Computed]
    public function membroSelecionado(): ?User
    {
        if (! $this->memberIdDrawer) {
            return null;
        }

        $meuDept = Auth::user()?->department_id;

        if (! $meuDept) {
            return null;
        }

        return User::with(['accessProfile', 'department'])
            ->where('id', $this->memberIdDrawer)
            ->where('department_id', $meuDept)
            ->first();
    }

    /**
     * Estatísticas gerais do time.
     */
    #[Computed]
    public function stats(): array
    {
        $dept = $this->meuDepartamento;

        if (! $dept) {
            return ['total' => 0, 'ativos' => 0, 'inativos' => 0, 'gerentes' => 0];
        }

        $total    = User::where('department_id', $dept->id)->count();
        $ativos   = User::where('department_id', $dept->id)->where('is_active', true)->count();
        $inativos = $total - $ativos;

        $gerentes = User::where('department_id', $dept->id)
            ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
            ->count();

        return compact('total', 'ativos', 'inativos', 'gerentes');
    }

    public function render()
    {
        return view('livewire.pages.team.index');
    }
}
