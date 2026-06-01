<?php

namespace App\Livewire\Layout;

use App\Livewire\SecureComponent;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class Sidebar extends SecureComponent
{
    public bool  $open      = false;
    public bool  $collapsed = false;
    public bool  $editMode  = false;

    public array $menus = [
        'principal'    => true,
        'gestao'       => true,
        'pessoas'      => true,
        'colaboracao'  => true,
        'treinamentos' => false,
    ];

    /** Chaves dos itens ocultos pelo usuário */
    public array $hiddenItems = [];

    // ── Definição de todos os itens navegáveis ────────────────────────

    public function allNavItems(): array
    {
        $user = Auth::user();

        return [
            'principal' => [
                'section' => 'Principal',
                'color'   => 'blue',
                'items'   => array_filter([
                    'dashboard'   => ['label' => 'Dashboard',     'icon' => 'layout-dashboard', 'visible' => true],
                    'feed'        => ['label' => 'Feed Social',   'icon' => 'globe',             'visible' => true],
                    'communities' => ['label' => 'Comunidades',   'icon' => 'users',             'visible' => true],
                    'humor'       => ['label' => 'Meu Humor',     'icon' => 'activity',          'visible' => !$user?->isAdmin()],
                    'publicacoes' => ['label' => 'Publicações',   'icon' => 'send',              'visible' => (bool) $user?->isRhOuDp()],
                ], fn ($i) => $i['visible']),
            ],
            'gestao' => [
                'section' => 'Gestão',
                'color'   => 'violet',
                'items'   => array_filter([
                    'dpi'          => ['label' => 'DPI',            'icon' => 'bar-chart-3',  'visible' => true],
                    'okrs'         => ['label' => 'OKRs',           'icon' => 'target',       'visible' => true],
                    'tarefas'      => ['label' => 'Tarefas',        'icon' => 'check-square', 'visible' => true],
                    'solicitacoes' => ['label' => 'Solicitações RH','icon' => 'inbox',        'visible' => !(bool) $user?->isRhOuDp()],
                    'avaliacoes'   => ['label' => 'Avaliação',      'icon' => 'trending-up',  'visible' => (bool) $user?->podeAvaliar()],
                ], fn ($i) => $i['visible']),
            ],
            'pessoas' => [
                'section' => 'Pessoas',
                'color'   => 'emerald',
                'items'   => array_filter([
                    'time'         => ['label' => 'Meu Time',     'icon' => 'users',          'visible' => true],
                    'organograma'  => ['label' => 'Organograma',  'icon' => 'network',        'visible' => true],
                    'portal_rs'    => ['label' => 'Portal R&S',   'icon' => 'file-text',      'visible' => (bool) $user?->isRhOuDp()],
                    'equipamentos' => ['label' => 'Equipamentos', 'icon' => 'monitor',        'visible' => (bool) $user?->isRhOuDp() || $this->isTiDept()],
                    'treinamentos' => ['label' => 'Treinamentos', 'icon' => 'graduation-cap', 'visible' => true],
                    'pesquisas'    => ['label' => 'Pesquisas',    'icon' => 'clipboard-list', 'visible' => true],
                    'usuarios'     => ['label' => 'Usuários',     'icon' => 'user-plus',      'visible' => (bool) $user?->isRhOuDp()],
                ], fn ($i) => $i['visible']),
            ],
            'colaboracao' => [
                'section' => 'Colaboração',
                'color'   => 'amber',
                'items'   => array_filter([
                    'feedbacks'  => ['label' => 'Feedbacks',  'icon' => 'message-square', 'visible' => (bool) $user?->podeGerenciarPesquisas()],
                    'calendario' => ['label' => 'Calendário', 'icon' => 'calendar-days',  'visible' => true],
                    'reunioes'   => ['label' => 'Reuniões',   'icon' => 'calendar',        'visible' => true],
                    'salas'      => ['label' => 'Salas',      'icon' => 'building',        'visible' => true],
                    'ouvidoria'  => ['label' => 'Ouvidoria',  'icon' => 'alert-circle',    'visible' => true],
                ], fn ($i) => $i['visible']),
            ],
        ];
    }

    // ── Lifecycle ─────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->loadPreferences();
    }

    // ── Persistência ──────────────────────────────────────────────────

    private function loadPreferences(): void
    {
        $prefs = Auth::user()?->sidebar_preferences ?? [];

        $this->hiddenItems = $prefs['hidden']      ?? [];
        $this->collapsed   = $prefs['collapsed']   ?? false;

        if (!empty($prefs['menu_states'])) {
            $this->menus = array_merge($this->menus, $prefs['menu_states']);
        }
    }

    private function savePreferences(): void
    {
        $user = Auth::user();
        if (! $user) return;

        $user->update([
            'sidebar_preferences' => [
                'hidden'      => array_values($this->hiddenItems),
                'collapsed'   => $this->collapsed,
                'menu_states' => $this->menus,
            ],
        ]);
    }

    // ── Ações de navegação ────────────────────────────────────────────

    public function close(): void
    {
        $this->open = false;
    }

    #[On('call-sidebar')]
    public function toggle(): void
    {
        $this->open = !$this->open;
    }

    public function toggleCollapse(): void
    {
        $this->collapsed = !$this->collapsed;
        if ($this->collapsed) $this->editMode = false;
        $this->savePreferences();
    }

    public function toggleMenu(string $menu): void
    {
        $this->menus[$menu] = !($this->menus[$menu] ?? true);
        $this->savePreferences();
    }

    // ── Editor de preferências ────────────────────────────────────────

    public function openEditor(): void
    {
        if ($this->collapsed) $this->collapsed = false;
        $this->editMode = true;
    }

    public function closeEditor(): void
    {
        $this->editMode = false;
    }

    public function toggleNavItem(string $key): void
    {
        if (in_array($key, $this->hiddenItems, true)) {
            $this->hiddenItems = array_values(
                array_filter($this->hiddenItems, fn ($k) => $k !== $key)
            );
        } else {
            $this->hiddenItems[] = $key;
        }
        $this->savePreferences();
    }

    public function resetPreferences(): void
    {
        $this->hiddenItems = [];
        $this->menus = [
            'principal'    => true,
            'gestao'       => true,
            'pessoas'      => true,
            'colaboracao'  => true,
            'treinamentos' => false,
        ];
        $this->collapsed = false;
        $this->editMode  = false;
        $this->savePreferences();
    }

    public function isHidden(string $key): bool
    {
        return in_array($key, $this->hiddenItems, true);
    }

    private function isTiDept(): bool
    {
        $name = strtolower(Auth::user()?->department?->name ?? '');
        return str_contains($name, 'ti')
            || str_contains($name, 'tecnologia')
            || str_contains($name, 'informação');
    }

    public function render()
    {
        return view('livewire.layout.sidebar');
    }
}
