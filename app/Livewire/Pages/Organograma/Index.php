<?php

namespace App\Livewire\Pages\Organograma;

use App\Livewire\SecureComponent;
use App\Models\Department;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class Index extends SecureComponent
{
    /**
     * ID do colaborador selecionado para abrir no drawer.
     */
    #[Locked]
    public ?int $perfilUserId = null;

    /**
     * Controla se o drawer de perfil está aberto.
     */
    public bool $drawerAberto = false;

    public function mount(): void
    {
        $this->requireAuth();
    }

    /**
     * Abre o drawer com os detalhes de um colaborador.
     */
    public function abrirPerfil(int $userId): void
    {
        $this->requireAuth();

        $existe = User::where('id', $userId)->where('is_active', true)->exists();

        if (! $existe) {
            return;
        }

        $this->perfilUserId = $userId;
        $this->drawerAberto = true;
    }

    /**
     * Fecha o drawer de perfil.
     */
    public function fecharDrawer(): void
    {
        $this->drawerAberto = false;
        $this->perfilUserId = null;
    }

    /**
     * Colaborador atualmente selecionado (para o drawer).
     */
    #[Computed]
    public function colaboradorSelecionado(): ?User
    {
        if (! $this->perfilUserId) {
            return null;
        }

        return User::with(['accessProfile', 'department'])
            ->where('id', $this->perfilUserId)
            ->first();
    }

    /**
     * Dados hierárquicos da empresa para o organograma.
     *
     * Estrutura retornada:
     * [
     *   'lideranca' => [ ...users com slug administrator ou hr ],
     *   'departamentos' => [
     *     [
     *       'id'        => int,
     *       'nome'      => string,
     *       'gerentes'  => [ ...users manager ],
     *       'membros'   => [ ...users employee / outros ],
     *     ],
     *     ...
     *   ],
     *   'sem_departamento' => [ ...users ativos sem dept ],
     * ]
     */
    #[Computed]
    public function hierarquia(): array
    {
        // Nível de liderança visível no topo do organograma: somente CEO
        $slugsLideranca = ['ceo'];

        // Perfis completamente ocultos do organograma (Administrador é um perfil interno)
        $slugsOcultos   = ['administrator'];

        // Gerentes dentro de departamentos: manager e hr_manager
        $slugsGerente   = ['manager', 'hr_manager'];

        // Excluídos de aparecer como membros/semDept: liderança + ocultos + gerentes
        $slugsExcluidos = array_merge($slugsLideranca, $slugsOcultos, $slugsGerente);

        // 1. Liderança topo: apenas CEO
        $lideranca = User::with(['accessProfile', 'department'])
            ->where('is_active', true)
            ->whereHas('accessProfile', fn ($q) => $q->whereIn('slug', $slugsLideranca))
            ->orderByRaw("CASE access_profile_id
                WHEN (SELECT id FROM access_profiles WHERE slug='ceo' LIMIT 1)           THEN 1
                WHEN (SELECT id FROM access_profiles WHERE slug='administrator' LIMIT 1)  THEN 2
                ELSE 3 END")
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => $this->formatarUsuario($u))
            ->values()
            ->toArray();

        // 2. Departamentos: gerentes (manager / hr_manager) + membros (employee / hr / outros)
        $departamentos = Department::with([
                'users' => fn ($q) => $q->with('accessProfile')->where('is_active', true)->orderBy('name'),
            ])
            ->orderBy('name')
            ->get()
            ->filter(fn ($d) => $d->users->isNotEmpty())
            ->map(function ($dept) use ($slugsGerente, $slugsExcluidos) {
                $gerentes = $dept->users
                    ->filter(fn ($u) => in_array($u->accessProfile?->slug, $slugsGerente))
                    ->map(fn ($u) => $this->formatarUsuario($u))
                    ->values()
                    ->toArray();

                // Membros = todos que não estão no topo (liderança) e não são gerentes
                $membros = $dept->users
                    ->filter(fn ($u) => ! in_array($u->accessProfile?->slug, $slugsExcluidos))
                    ->map(fn ($u) => $this->formatarUsuario($u))
                    ->values()
                    ->toArray();

                return [
                    'id'        => $dept->id,
                    'nome'      => $dept->nome ?? $dept->name,
                    'descricao' => $dept->description,
                    'gerentes'  => $gerentes,
                    'membros'   => $membros,
                    'total'     => count($gerentes) + count($membros),
                ];
            })
            ->values()
            ->toArray();

        // 3. Usuários sem departamento e fora da liderança/gerência
        $semDept = User::with(['accessProfile', 'department'])
            ->where('is_active', true)
            ->whereNull('department_id')
            ->whereDoesntHave('accessProfile', fn ($q) => $q->whereIn('slug', $slugsExcluidos))
            ->orderBy('name')
            ->get()
            ->map(fn ($u) => $this->formatarUsuario($u))
            ->values()
            ->toArray();

        return compact('lideranca', 'departamentos', 'semDept');
    }

    /**
     * Estatísticas gerais da empresa.
     */
    #[Computed]
    public function stats(): array
    {
        $total        = User::where('is_active', true)->count();
        $departamentos = Department::has('users')->count();
        $gerentes     = User::where('is_active', true)
                            ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
                            ->count();

        return compact('total', 'departamentos', 'gerentes');
    }

    /**
     * Formata um usuário para uso no organograma (JSON-safe).
     */
    private function formatarUsuario(User $user): array
    {
        return [
            'id'         => $user->id,
            'nome'       => $user->name,
            'cargo'      => $user->position ?? 'Sem cargo',
            'email'      => $user->email,
            'perfil'     => $user->accessProfile?->slug ?? 'employee',
            'perfilNome' => $user->accessProfile?->name ?? 'Colaborador',
            'dept'       => $user->department?->name ?? null,
            'iniciais'   => $user->initials(),
            'cor'        => $user->avatarColor(),
            'avatarUrl'  => $user->avatarUrl(),
            'is_active'  => $user->is_active,
        ];
    }

    public function render()
    {
        return view('livewire.pages.organograma.index');
    }
}
