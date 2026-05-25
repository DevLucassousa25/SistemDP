<div class="min-h-screen bg-slate-50 dark:bg-slate-950"
     x-data="{
         confirmDialog: { show: false, title: '', message: '', icon: 'trash-2', action: null },
         openConfirm(title, message, action, icon = 'trash-2') {
             this.confirmDialog = { show: true, title, message, icon, action };
         },
         runConfirm() {
             if (this.confirmDialog.action) this.confirmDialog.action();
             this.confirmDialog.show = false;
         }
     }">
@php
    $user     = Auth::user();
    $slug     = $user->accessProfile?->slug ?? '';
    $isRhAdmin = in_array($slug, ['administrator', 'hr']);
    $isManager = $slug === 'manager';

    $statusConfig = [
        'not_started' => ['label' => 'Não iniciado', 'color' => 'slate',   'bg' => 'bg-slate-100 dark:bg-slate-800',    'text' => 'text-slate-500 dark:text-slate-400',   'ring' => 'ring-slate-200 dark:ring-slate-700'],
        'on_track'    => ['label' => 'No prazo',     'color' => 'emerald', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/30','text' => 'text-emerald-700 dark:text-emerald-400','ring' => 'ring-emerald-200 dark:ring-emerald-700'],
        'at_risk'     => ['label' => 'Em risco',     'color' => 'amber',   'bg' => 'bg-amber-50 dark:bg-amber-900/30',   'text' => 'text-amber-700 dark:text-amber-400',   'ring' => 'ring-amber-200 dark:ring-amber-700'],
        'behind'      => ['label' => 'Atrasado',     'color' => 'rose',    'bg' => 'bg-rose-50 dark:bg-rose-900/30',     'text' => 'text-rose-700 dark:text-rose-400',     'ring' => 'ring-rose-200 dark:ring-rose-700'],
        'completed'   => ['label' => 'Concluído',    'color' => 'blue',    'bg' => 'bg-blue-50 dark:bg-blue-900/30',     'text' => 'text-blue-700 dark:text-blue-400',     'ring' => 'ring-blue-200 dark:ring-blue-700'],
    ];
    $levelConfig = [
        'company'    => ['label' => 'Empresa',      'bg' => 'bg-violet-50 dark:bg-violet-900/30', 'text' => 'text-violet-700 dark:text-violet-400', 'icon' => 'building-2'],
        'department' => ['label' => 'Departamento', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/30', 'text' => 'text-indigo-700 dark:text-indigo-400', 'icon' => 'users'],
        'individual' => ['label' => 'Individual',   'bg' => 'bg-teal-50 dark:bg-teal-900/30',     'text' => 'text-teal-700 dark:text-teal-400',    'icon' => 'user'],
    ];
@endphp

<div class="max-w-6xl mx-auto px-4 sm:px-6 py-6 space-y-5">

    {{-- ── Cabeçalho ────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white tracking-tight">OKRs</h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-0.5">Objetivos e Resultados-chave da organização</p>
        </div>
        <div class="flex items-center gap-2 flex-wrap">
            @if ($isRhAdmin)
                <button wire:click="openCreateCycle"
                    class="inline-flex items-center gap-1.5 text-sm font-medium text-slate-600 dark:text-slate-300
                           bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                           hover:bg-slate-50 dark:hover:bg-slate-700 px-3 py-2 rounded-xl transition cursor-pointer shadow-sm">
                    <x-lucide-refresh-cw class="w-4 h-4" />
                    Novo Ciclo
                </button>
            @endif
            @if ($this->activeCycleId)
                <button wire:click="openCreateObjective('individual')"
                    class="inline-flex items-center gap-1.5 text-sm font-semibold text-white
                           bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                           px-4 py-2 rounded-xl transition cursor-pointer shadow-sm shadow-indigo-200 dark:shadow-indigo-900">
                    <x-lucide-plus class="w-4 h-4" />
                    Novo Objetivo
                </button>
            @endif
        </div>
    </div>

    {{-- ── Seletor de Ciclos ─────────────────────────────────────────── --}}
    @if ($this->cycles->isNotEmpty())
        <div class="flex gap-2 overflow-x-auto pb-1">
            @foreach ($this->cycles as $cycle)
                <button wire:click="$set('activeCycleId', {{ $cycle->id }})"
                    class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm font-medium whitespace-nowrap
                           transition border cursor-pointer
                           {{ $activeCycleId === $cycle->id
                               ? 'bg-indigo-600 text-white border-indigo-600 shadow-md shadow-indigo-200 dark:shadow-indigo-900'
                               : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 border-slate-200 dark:border-slate-700 hover:border-indigo-300' }}">
                    <span class="w-2 h-2 rounded-full flex-shrink-0
                        {{ $cycle->status === 'active' ? 'bg-emerald-400' : ($cycle->status === 'closed' ? 'bg-slate-400' : 'bg-amber-400') }}"></span>
                    {{ $cycle->name }}
                    @if ($isRhAdmin)
                        <span wire:click.stop="openEditCycle({{ $cycle->id }})"
                              class="opacity-60 hover:opacity-100 transition cursor-pointer">
                            <x-lucide-pencil class="w-3 h-3" />
                        </span>
                    @endif
                </button>
            @endforeach
        </div>
    @else
        {{-- Estado vazio --}}
        <div class="flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mb-4">
                <x-lucide-target class="w-8 h-8 text-indigo-400" />
            </div>
            <h3 class="text-base font-semibold text-slate-700 dark:text-slate-300">Nenhum ciclo criado</h3>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1 mb-4">Crie um ciclo para começar a gerenciar seus OKRs.</p>
            @if ($isRhAdmin)
                <button wire:click="openCreateCycle"
                    class="inline-flex items-center gap-2 text-sm font-semibold text-white
                           bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                           px-4 py-2 rounded-xl transition cursor-pointer shadow-sm shadow-indigo-200 dark:shadow-indigo-900">
                    <x-lucide-plus class="w-4 h-4" />
                    Criar primeiro ciclo
                </button>
            @endif
        </div>
    @endif

    @if ($this->activeCycle)

        {{-- ── Dashboard Stats ───────────────────────────────────────── --}}
        @php $stats = $this->dashboardStats; @endphp
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

            {{-- Progresso geral --}}
            <div class="col-span-2 sm:col-span-3 lg:col-span-2 bg-white dark:bg-slate-900 rounded-2xl
                        ring-1 ring-slate-200 dark:ring-slate-700 p-5 flex items-center gap-4">
                {{-- Anel SVG --}}
                <div class="relative w-16 h-16 flex-shrink-0">
                    <svg class="w-16 h-16 -rotate-90" viewBox="0 0 64 64">
                        <circle cx="32" cy="32" r="26" fill="none" stroke="currentColor"
                                class="text-slate-100 dark:text-slate-800" stroke-width="6"/>
                        <circle cx="32" cy="32" r="26" fill="none"
                                stroke="{{ $stats['progress'] >= 70 ? '#10b981' : ($stats['progress'] >= 40 ? '#f59e0b' : '#f43f5e') }}"
                                stroke-width="6"
                                stroke-linecap="round"
                                stroke-dasharray="{{ round($stats['progress'] * 1.634, 1) }} 163.4"/>
                    </svg>
                    <span class="absolute inset-0 flex items-center justify-center text-sm font-bold text-slate-800 dark:text-white">
                        {{ $stats['progress'] }}%
                    </span>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-400 dark:text-slate-500 uppercase tracking-wide">Progresso Geral</p>
                    <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $stats['total'] }}</p>
                    <p class="text-xs text-slate-400">objetivo{{ $stats['total'] !== 1 ? 's' : '' }}</p>
                </div>
            </div>

            {{-- Cards de status --}}
            @foreach ([
                ['key' => 'on_track',  'label' => 'No prazo',    'color' => 'emerald', 'icon' => 'trending-up'],
                ['key' => 'at_risk',   'label' => 'Em risco',    'color' => 'amber',   'icon' => 'alert-triangle'],
                ['key' => 'behind',    'label' => 'Atrasado',    'color' => 'rose',    'icon' => 'alert-circle'],
                ['key' => 'completed', 'label' => 'Concluídos',  'color' => 'blue',    'icon' => 'check-circle'],
            ] as $card)
                <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 p-4 flex flex-col gap-2">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                bg-{{ $card['color'] }}-50 dark:bg-{{ $card['color'] }}-900/30">
                        <x-dynamic-component :component="'lucide-' . $card['icon']"
                            class="w-4 h-4 text-{{ $card['color'] }}-500" />
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $stats[$card['key']] }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500">{{ $card['label'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ── Filtros ─────────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 p-3 flex flex-col sm:flex-row gap-2">

            {{-- Busca --}}
            <div class="relative flex-1">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar objetivos..."
                    class="w-full pl-9 pr-9 py-2.5 text-sm bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                           rounded-xl text-slate-700 dark:text-slate-200 placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-300 transition" />
                @if ($search)
                    <button wire:click="$set('search', '')"
                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer transition">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                @endif
            </div>

            {{-- Separador vertical (desktop) --}}
            <div class="hidden sm:block w-px bg-slate-200 dark:bg-slate-700 self-stretch my-0.5"></div>

            {{-- Filtros em linha --}}
            <div class="flex items-center gap-2">

                {{-- Filtro: Nível --}}
                <div x-data="{
                    open: false,
                    opts: [
                        { value: '',           label: 'Nível',          icon: 'layers' },
                        { value: 'company',    label: 'Empresa',        icon: 'building-2' },
                        { value: 'department', label: 'Departamento',   icon: 'users' },
                        { value: 'individual', label: 'Individual',     icon: 'user' },
                    ],
                    get active() { return '{{ $filterLevel }}' !== '' },
                    get label() {
                        const v = '{{ $filterLevel }}';
                        return this.opts.find(o => o.value === v)?.label ?? 'Nível';
                    }
                }" class="relative">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium rounded-xl border transition cursor-pointer whitespace-nowrap
                               {{ $filterLevel
                                   ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-300 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300'
                                   : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600' }}">
                        <x-lucide-layers class="w-3.5 h-3.5 flex-shrink-0" />
                        <span class="text-xs font-semibold">
                            @if ($filterLevel === 'company') Empresa
                            @elseif ($filterLevel === 'department') Departamento
                            @elseif ($filterLevel === 'individual') Individual
                            @else Nível
                            @endif
                        </span>
                        @if ($filterLevel)
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 flex-shrink-0"></span>
                        @else
                            <x-lucide-chevron-down class="w-3 h-3 flex-shrink-0 opacity-50" />
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute left-0 top-full mt-1.5 z-20 min-w-[160px]
                                bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-1.5 overflow-hidden">
                        @foreach ([
                            ['', 'Todos os níveis', 'layers', ''],
                            ['company', 'Empresa', 'building-2', 'text-violet-600'],
                            ['department', 'Departamento', 'users', 'text-indigo-600'],
                            ['individual', 'Individual', 'user', 'text-teal-600'],
                        ] as [$val, $lbl, $ico, $clr])
                            <button wire:click="$set('filterLevel', '{{ $val }}')" @click="open = false" type="button"
                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm cursor-pointer transition
                                       {{ $filterLevel === $val
                                           ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                           : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <x-dynamic-component :component="'lucide-' . $ico" class="w-3.5 h-3.5 {{ $clr }} flex-shrink-0" />
                                {{ $lbl }}
                                @if ($filterLevel === $val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" /> @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Filtro: Status --}}
                <div x-data="{ open: false }" class="relative">
                    <button @click="open = !open" type="button"
                        class="flex items-center gap-1.5 px-3 py-2.5 text-sm font-medium rounded-xl border transition cursor-pointer whitespace-nowrap
                               {{ $filterStatus
                                   ? 'bg-indigo-50 dark:bg-indigo-900/30 border-indigo-300 dark:border-indigo-600 text-indigo-700 dark:text-indigo-300'
                                   : 'bg-slate-50 dark:bg-slate-800 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600' }}">
                        {{-- Dot colorido quando ativo --}}
                        @if ($filterStatus)
                            <span class="w-2 h-2 rounded-full flex-shrink-0
                                {{ $filterStatus === 'on_track' ? 'bg-emerald-400' : ($filterStatus === 'at_risk' ? 'bg-amber-400' : ($filterStatus === 'behind' ? 'bg-rose-400' : ($filterStatus === 'completed' ? 'bg-blue-400' : 'bg-slate-400'))) }}"></span>
                        @else
                            <x-lucide-bar-chart-2 class="w-3.5 h-3.5 flex-shrink-0" />
                        @endif
                        <span class="text-xs font-semibold">
                            @if ($filterStatus === 'not_started') Não iniciado
                            @elseif ($filterStatus === 'on_track') No prazo
                            @elseif ($filterStatus === 'at_risk') Em risco
                            @elseif ($filterStatus === 'behind') Atrasado
                            @elseif ($filterStatus === 'completed') Concluído
                            @else Status
                            @endif
                        </span>
                        @if ($filterStatus)
                            <span class="w-1.5 h-1.5 rounded-full bg-indigo-500 flex-shrink-0"></span>
                        @else
                            <x-lucide-chevron-down class="w-3 h-3 flex-shrink-0 opacity-50" />
                        @endif
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 top-full mt-1.5 z-20 min-w-[170px]
                                bg-white dark:bg-slate-800 rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-1.5 overflow-hidden">
                        @foreach ([
                            ['', 'Todos os status', 'bg-slate-300'],
                            ['not_started', 'Não iniciado', 'bg-slate-400'],
                            ['on_track',    'No prazo',     'bg-emerald-400'],
                            ['at_risk',     'Em risco',     'bg-amber-400'],
                            ['behind',      'Atrasado',     'bg-rose-400'],
                            ['completed',   'Concluído',    'bg-blue-400'],
                        ] as [$val, $lbl, $dot])
                            <button wire:click="$set('filterStatus', '{{ $val }}')" @click="open = false" type="button"
                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm cursor-pointer transition
                                       {{ $filterStatus === $val
                                           ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                           : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <span class="w-2 h-2 rounded-full flex-shrink-0 {{ $dot }}"></span>
                                {{ $lbl }}
                                @if ($filterStatus === $val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" /> @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Limpar filtros --}}
                @if ($search || $filterLevel || $filterStatus)
                    <button wire:click="clearFilters"
                        class="flex items-center gap-1 px-2.5 py-2.5 text-xs font-semibold text-rose-500 dark:text-rose-400
                               hover:bg-rose-50 dark:hover:bg-rose-900/20 rounded-xl transition cursor-pointer whitespace-nowrap">
                        <x-lucide-x class="w-3 h-3" />
                        Limpar
                    </button>
                @endif
            </div>
        </div>

        {{-- ── Lista de Objetivos ─────────────────────────────────────── --}}
        @if ($this->objectives->isEmpty())
            <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mx-auto mb-3">
                    <x-lucide-target class="w-7 h-7 text-slate-300 dark:text-slate-600" />
                </div>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Nenhum objetivo encontrado</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                    {{ $search || $filterLevel || $filterStatus ? 'Tente ajustar os filtros.' : 'Clique em "Novo Objetivo" para começar.' }}
                </p>
            </div>
        @else
            <div class="space-y-3">
                @foreach ($this->objectives as $obj)
                    @php
                        $sCfg = $statusConfig[$obj->status] ?? $statusConfig['not_started'];
                        $lCfg = $levelConfig[$obj->level]   ?? $levelConfig['individual'];
                        $isExpanded = in_array($obj->id, $expandedObjectives);
                        $krs = $obj->keyResults;
                    @endphp

                    <div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden">

                        {{-- ── Cabeçalho do Objetivo ──────────────────── --}}
                        <div class="p-4 sm:p-5">
                            <div class="flex items-start gap-3">

                                {{-- Expandir --}}
                                <button wire:click="toggleObjective({{ $obj->id }})"
                                    class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0 mt-0.5
                                           bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700
                                           text-slate-500 transition cursor-pointer">
                                    <x-lucide-chevron-right class="w-4 h-4 transition-transform {{ $isExpanded ? 'rotate-90' : '' }}" />
                                </button>

                                {{-- Info principal --}}
                                <div class="flex-1 min-w-0">
                                    <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                        {{-- Badge nível --}}
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-md {{ $lCfg['bg'] }} {{ $lCfg['text'] }}">
                                            <x-dynamic-component :component="'lucide-' . $lCfg['icon']" class="w-3 h-3" />
                                            {{ $lCfg['label'] }}
                                        </span>
                                        {{-- Badge status --}}
                                        <span class="inline-flex items-center gap-1 text-xs font-semibold px-2 py-0.5 rounded-md ring-1 {{ $sCfg['bg'] }} {{ $sCfg['text'] }} {{ $sCfg['ring'] }}">
                                            {{ $sCfg['label'] }}
                                        </span>
                                        {{-- KRs count --}}
                                        <span class="text-xs text-slate-400 dark:text-slate-500">
                                            {{ $krs->count() }} KR{{ $krs->count() !== 1 ? 's' : '' }}
                                        </span>
                                    </div>

                                    <h3 class="text-sm sm:text-base font-semibold text-slate-800 dark:text-white leading-snug">
                                        {{ $obj->title }}
                                    </h3>

                                    @if ($obj->description)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 line-clamp-1">{{ $obj->description }}</p>
                                    @endif

                                    {{-- Owner + Dept --}}
                                    <div class="flex flex-wrap items-center gap-3 mt-2">
                                        @if ($obj->owner)
                                            <span class="inline-flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                                                <x-lucide-user class="w-3 h-3" />
                                                {{ $obj->owner->name }}
                                            </span>
                                        @endif
                                        @if ($obj->department)
                                            <span class="inline-flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                                                <x-lucide-building-2 class="w-3 h-3" />
                                                {{ $obj->department->name }}
                                            </span>
                                        @endif
                                    </div>
                                </div>

                                {{-- Progresso + ações --}}
                                <div class="flex items-center gap-3 flex-shrink-0">
                                    {{-- % --}}
                                    <div class="text-right hidden sm:block">
                                        <p class="text-xl font-bold text-slate-800 dark:text-white">{{ round($obj->progress) }}%</p>
                                        <p class="text-xs text-slate-400">progresso</p>
                                    </div>

                                    @if ($this->canManageObjective($obj))
                                        <div class="flex items-center gap-1">
                                            <button wire:click="openCreateKr({{ $obj->id }})"
                                                class="p-1.5 rounded-lg text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition cursor-pointer"
                                                title="Adicionar Key Result">
                                                <x-lucide-plus class="w-4 h-4" />
                                            </button>
                                            <button wire:click="openEditObjective({{ $obj->id }})"
                                                class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                                title="Editar objetivo">
                                                <x-lucide-pencil class="w-4 h-4" />
                                            </button>
                                            <button
                                                @click="openConfirm('Remover Objetivo', 'Tem certeza que deseja remover este objetivo e todos os seus Key Results? Esta ação não pode ser desfeita.', () => $wire.deleteObjective({{ $obj->id }}))"
                                                class="p-1.5 rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-900/30 transition cursor-pointer"
                                                title="Remover">
                                                <x-lucide-trash-2 class="w-4 h-4" />
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            {{-- Barra de progresso --}}
                            <div class="mt-4">
                                <div class="flex items-center justify-between mb-1.5">
                                    <span class="text-xs text-slate-400">0%</span>
                                    <span class="text-xs font-semibold text-slate-600 dark:text-slate-300 sm:hidden">{{ round($obj->progress) }}%</span>
                                    <span class="text-xs text-slate-400">100%</span>
                                </div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all duration-500
                                        {{ $obj->status === 'completed' ? 'bg-blue-500' : ($obj->status === 'on_track' ? 'bg-emerald-500' : ($obj->status === 'at_risk' ? 'bg-amber-500' : ($obj->status === 'behind' ? 'bg-rose-500' : 'bg-slate-300'))) }}"
                                         style="width: {{ min(100, $obj->progress) }}%"></div>
                                </div>
                            </div>
                        </div>

                        {{-- ── Key Results (expansível) ────────────────── --}}
                        @if ($isExpanded)
                            <div class="border-t border-slate-100 dark:border-slate-800">
                                @if ($krs->isEmpty())
                                    <div class="p-6 text-center">
                                        <p class="text-sm text-slate-400">Nenhum Key Result ainda.</p>
                                        @if ($this->canManageObjective($obj))
                                            <button wire:click="openCreateKr({{ $obj->id }})"
                                                class="mt-2 text-sm text-indigo-500 hover:underline cursor-pointer">
                                                + Adicionar Key Result
                                            </button>
                                        @endif
                                    </div>
                                @else
                                    <div class="divide-y divide-slate-50 dark:divide-slate-800/60">
                                        @foreach ($krs as $kr)
                                            @php
                                                $krSCfg = $statusConfig[$kr->status] ?? $statusConfig['not_started'];
                                                $lastCheckin = $kr->checkins->first();
                                            @endphp
                                            <div class="px-5 py-4 flex items-start gap-3 group hover:bg-slate-50/50 dark:hover:bg-slate-800/30 transition">

                                                {{-- Ícone tipo --}}
                                                <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-800 flex items-center justify-center flex-shrink-0 mt-0.5">
                                                    @switch($kr->type)
                                                        @case('percentage') <x-lucide-percent class="w-3.5 h-3.5 text-slate-500" /> @break
                                                        @case('currency')   <x-lucide-dollar-sign class="w-3.5 h-3.5 text-slate-500" /> @break
                                                        @case('boolean')    <x-lucide-toggle-left class="w-3.5 h-3.5 text-slate-500" /> @break
                                                        @default            <x-lucide-hash class="w-3.5 h-3.5 text-slate-500" />
                                                    @endswitch
                                                </div>

                                                <div class="flex-1 min-w-0">
                                                    <div class="flex flex-wrap items-center gap-2 mb-1">
                                                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $kr->title }}</p>
                                                        <span class="text-xs px-1.5 py-0.5 rounded ring-1 {{ $krSCfg['bg'] }} {{ $krSCfg['text'] }} {{ $krSCfg['ring'] }}">
                                                            {{ $krSCfg['label'] }}
                                                        </span>
                                                    </div>

                                                    {{-- Progresso KR --}}
                                                    <div class="flex items-center gap-3 mb-2">
                                                        <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                                            <div class="h-full rounded-full
                                                                {{ $kr->status === 'completed' ? 'bg-blue-500' : ($kr->status === 'on_track' ? 'bg-emerald-500' : ($kr->status === 'at_risk' ? 'bg-amber-500' : ($kr->status === 'behind' ? 'bg-rose-500' : 'bg-slate-300'))) }}"
                                                                style="width: {{ min(100, $kr->progress) }}%"></div>
                                                        </div>
                                                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 w-10 text-right">{{ round($kr->progress) }}%</span>
                                                    </div>

                                                    {{-- Valores --}}
                                                    <div class="flex flex-wrap items-center gap-x-4 gap-y-1 text-xs text-slate-500 dark:text-slate-400">
                                                        <span>Atual: <strong class="text-slate-700 dark:text-slate-200">{{ $kr->formatted_current }}</strong></span>
                                                        <span>Meta: <strong class="text-slate-700 dark:text-slate-200">{{ $kr->formatted_target }}</strong></span>
                                                        @if ($kr->owner)
                                                            <span class="flex items-center gap-1"><x-lucide-user class="w-3 h-3" />{{ $kr->owner->name }}</span>
                                                        @endif
                                                        @if ($kr->due_date)
                                                            <span class="flex items-center gap-1"><x-lucide-calendar class="w-3 h-3" />{{ $kr->due_date->format('d/m/Y') }}</span>
                                                        @endif
                                                        @if ($lastCheckin)
                                                            <span class="flex items-center gap-1 text-slate-400">
                                                                <x-lucide-clock class="w-3 h-3" />
                                                                Check-in {{ $lastCheckin->created_at->diffForHumans() }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Ações KR --}}
                                                <div class="flex items-center gap-1 opacity-0 group-hover:opacity-100 transition flex-shrink-0">
                                                    @if ($this->canCheckin($kr))
                                                        <button wire:click="openCheckin({{ $kr->id }})"
                                                            class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-1.5 rounded-lg
                                                                   text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/30
                                                                   hover:bg-indigo-100 transition cursor-pointer">
                                                            <x-lucide-zap class="w-3 h-3" />
                                                            Check-in
                                                        </button>
                                                    @endif
                                                    <button wire:click="openHistory({{ $kr->id }})"
                                                        class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer"
                                                        title="Histórico">
                                                        <x-lucide-clock class="w-3.5 h-3.5" />
                                                    </button>
                                                    @if ($this->canManageObjective($obj))
                                                        <button wire:click="openEditKr({{ $kr->id }})"
                                                            class="p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition cursor-pointer">
                                                            <x-lucide-pencil class="w-3.5 h-3.5" />
                                                        </button>
                                                        <button
                                                            @click="openConfirm('Remover Key Result', 'Tem certeza que deseja remover este Key Result? Esta ação não pode ser desfeita.', () => $wire.deleteKr({{ $kr->id }}))"
                                                            class="p-1.5 rounded-lg text-slate-400 hover:bg-rose-50 hover:text-rose-500 dark:hover:bg-rose-900/30 transition cursor-pointer">
                                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                        </button>
                                                    @endif
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif

    @endif
</div>

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- MODAIS                                                             --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}

{{-- ── Modal: Ciclo ─────────────────────────────────────────────────── --}}
@if ($cycleModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('cycleModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-md
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[92vh] sm:max-h-[88vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                {{ $cycleMode === 'create' ? 'bg-gradient-to-br from-violet-500 to-indigo-600' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }}">
                        <x-lucide-refresh-cw class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
                            {{ $cycleMode === 'create' ? 'Novo Ciclo' : 'Editar Ciclo' }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $cycleMode === 'create' ? 'Defina o período e configurações' : 'Atualize os dados do ciclo' }}
                        </p>
                    </div>
                </div>
                <button wire:click="$set('cycleModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Nome do ciclo *</label>
                <input wire:model="cycleName" type="text" placeholder="Ex: Q2 2025, Semestral 2025..."
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                @error('cycleName') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Início *</label>
                    <input wire:model="cycleStart" type="date"
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Fim *</label>
                    <input wire:model="cycleEnd" type="date"
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                </div>
            </div>
            {{-- Status com dot colorido --}}
            <div x-data="{
                open: false,
                val: $wire.entangle('cycleStatus'),
                opts: [
                    { value: 'planning', label: 'Planejamento', dot: 'bg-amber-400',   bg: 'bg-amber-50 dark:bg-amber-900/30',   text: 'text-amber-700 dark:text-amber-400' },
                    { value: 'active',   label: 'Ativo',         dot: 'bg-emerald-400', bg: 'bg-emerald-50 dark:bg-emerald-900/30', text: 'text-emerald-700 dark:text-emerald-400' },
                    { value: 'closed',   label: 'Encerrado',     dot: 'bg-slate-400',   bg: 'bg-slate-100 dark:bg-slate-800',     text: 'text-slate-500 dark:text-slate-400' },
                ],
                get selected() { return this.opts.find(o => o.value === this.val) ?? this.opts[0] }
            }" class="relative">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                <button @click="open = !open" type="button"
                    class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm
                           bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white cursor-pointer
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left">
                    <span class="w-2 h-2 rounded-full shrink-0 transition-colors" :class="selected.dot"></span>
                    <span class="flex-1 font-medium" x-text="selected.label"></span>
                    <x-lucide-chevron-down class="w-4 h-4 text-slate-400 shrink-0 transition-transform" ::class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800
                            rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-1.5 overflow-hidden">
                    <template x-for="opt in opts" :key="opt.value">
                        <button @click="val = opt.value; open = false" type="button"
                            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm cursor-pointer transition"
                            :class="val === opt.value
                                ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                            <span class="w-2 h-2 rounded-full shrink-0" :class="opt.dot"></span>
                            <span x-text="opt.label" class="flex-1 text-left"></span>
                            <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" x-show="val === opt.value" />
                        </button>
                    </template>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Descrição</label>
                <textarea wire:model="cycleDescription" rows="3" placeholder="Opcional..."
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 resize-none transition"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-between items-center gap-3 px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('cycleModal', false)"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Cancelar
            </button>
            <button wire:click="saveCycle" wire:loading.attr="disabled" wire:target="saveCycle"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition cursor-pointer
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       shadow-md shadow-blue-500/20 disabled:opacity-60">
                <span wire:loading.remove wire:target="saveCycle">
                    {{ $cycleMode === 'create' ? 'Criar Ciclo' : 'Salvar Alterações' }}
                </span>
                <span wire:loading wire:target="saveCycle">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Modal: Objetivo ───────────────────────────────────────────────── --}}
@if ($objectiveModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('objectiveModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[92vh] sm:max-h-[88vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                {{ $objectiveMode === 'create' ? 'bg-gradient-to-br from-violet-500 to-indigo-600' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }}">
                        <x-lucide-target class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
                            {{ $objectiveMode === 'create' ? 'Novo Objetivo' : 'Editar Objetivo' }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $objectiveMode === 'create' ? 'Defina o que você quer alcançar' : 'Atualize os dados do objetivo' }}
                        </p>
                    </div>
                </div>
                <button wire:click="$set('objectiveModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-5">

            {{-- Título --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Título do Objetivo *</label>
                <input wire:model="objTitle" type="text" placeholder="O que você quer alcançar?"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                @error('objTitle') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Descrição</label>
                <textarea wire:model="objDescription" rows="2" placeholder="Contexto adicional (opcional)..."
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 resize-none transition"></textarea>
            </div>

            {{-- Nível: pills visuais --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Nível *</label>
                <div class="grid gap-2
                    {{ ($isRhAdmin) ? 'grid-cols-3' : ($isManager ? 'grid-cols-2' : 'grid-cols-1') }}">
                    @if ($isRhAdmin)
                        <button wire:click="$set('objLevel', 'company')" type="button"
                            class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition cursor-pointer
                                   {{ $objLevel === 'company'
                                       ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300'
                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-500 dark:text-slate-400 hover:border-violet-300 hover:bg-violet-50/50' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                        {{ $objLevel === 'company' ? 'bg-violet-100 dark:bg-violet-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                                <x-lucide-building-2 class="w-4 h-4 {{ $objLevel === 'company' ? 'text-violet-600 dark:text-violet-400' : 'text-slate-400' }}" />
                            </div>
                            <span class="text-xs font-semibold">Empresa</span>
                        </button>
                    @endif
                    @if ($isRhAdmin || $isManager)
                        <button wire:click="$set('objLevel', 'department')" type="button"
                            class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition cursor-pointer
                                   {{ $objLevel === 'department'
                                       ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300'
                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-500 dark:text-slate-400 hover:border-indigo-300 hover:bg-indigo-50/50' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                        {{ $objLevel === 'department' ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                                <x-lucide-users class="w-4 h-4 {{ $objLevel === 'department' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}" />
                            </div>
                            <span class="text-xs font-semibold">Departamento</span>
                        </button>
                    @endif
                    <button wire:click="$set('objLevel', 'individual')" type="button"
                        class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition cursor-pointer
                               {{ $objLevel === 'individual'
                                   ? 'border-teal-400 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-500 dark:text-slate-400 hover:border-teal-300 hover:bg-teal-50/50' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                    {{ $objLevel === 'individual' ? 'bg-teal-100 dark:bg-teal-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-user class="w-4 h-4 {{ $objLevel === 'individual' ? 'text-teal-600 dark:text-teal-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-xs font-semibold">Individual</span>
                    </button>
                </div>
            </div>

            {{-- Status + Responsável lado a lado --}}
            <div class="grid grid-cols-2 gap-3">

                {{-- Status com dot --}}
                <div x-data="{
                    open: false,
                    val: $wire.entangle('objStatus'),
                    opts: [
                        { value: 'not_started', label: 'Não iniciado', dot: 'bg-slate-400' },
                        { value: 'on_track',    label: 'No prazo',     dot: 'bg-emerald-400' },
                        { value: 'at_risk',     label: 'Em risco',     dot: 'bg-amber-400' },
                        { value: 'behind',      label: 'Atrasado',     dot: 'bg-rose-400' },
                        { value: 'completed',   label: 'Concluído',    dot: 'bg-blue-400' },
                    ],
                    get selected() { return this.opts.find(o => o.value === this.val) ?? this.opts[0] }
                }" class="relative">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                    <button @click="open = !open" type="button"
                        class="w-full flex items-center gap-2 px-3.5 py-2.5 text-sm
                               bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl text-slate-800 dark:text-white cursor-pointer
                               focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left">
                        <span class="w-2 h-2 rounded-full shrink-0" :class="selected.dot"></span>
                        <span class="flex-1 font-medium truncate text-sm" x-text="selected.label"></span>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" ::class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" @click.outside="open = false"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95"
                         x-transition:enter-end="opacity-100 scale-100"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800
                                rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-1.5 overflow-hidden">
                        <template x-for="opt in opts" :key="opt.value">
                            <button @click="val = opt.value; open = false" type="button"
                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm cursor-pointer transition"
                                :class="val === opt.value
                                    ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                    : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                <span class="w-2 h-2 rounded-full shrink-0" :class="opt.dot"></span>
                                <span x-text="opt.label" class="flex-1 text-left"></span>
                                <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" x-show="val === opt.value" />
                            </button>
                        </template>
                    </div>
                </div>

                {{-- Responsável com avatar + busca --}}
                <div x-data="{
                    open: false,
                    query: '',
                    val: $wire.entangle('objOwnerId'),
                    opts: [
                        { value: '', name: 'Nenhum', initials: '' },
                        @foreach ($this->users as $u)
                        { value: {{ $u->id }}, name: '{{ addslashes($u->name) }}', initials: '{{ strtoupper(mb_substr($u->name, 0, 1)) }}{{ strtoupper(mb_substr(explode(" ", trim($u->name))[1] ?? "", 0, 1)) }}' },
                        @endforeach
                    ],
                    get filtered() {
                        if (!this.query) return this.opts;
                        return this.opts.filter(o => o.name.toLowerCase().includes(this.query.toLowerCase()));
                    },
                    get selected() { return this.opts.find(o => String(o.value) === String(this.val)) ?? this.opts[0] },
                    openAndFocus() { this.open = true; this.$nextTick(() => this.$refs.ownerSearch?.focus()) }
                }" class="relative" @keydown.escape="open = false">

                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Responsável</label>

                    {{-- Trigger --}}
                    <button @click="openAndFocus()" type="button"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm
                               bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left">
                        <template x-if="selected.value !== ''">
                            <span class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600
                                         flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                  x-text="selected.initials"></span>
                        </template>
                        <template x-if="selected.value === ''">
                            <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                <x-lucide-user class="w-3.5 h-3.5 text-slate-400" />
                            </div>
                        </template>
                        <span class="flex-1 truncate text-sm"
                              :class="selected.value === '' ? 'text-slate-400' : 'font-medium text-slate-800 dark:text-white'"
                              x-text="selected.name"></span>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform"
                                               ::class="open ? 'rotate-180' : ''" />
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" @click.outside="open = false; query = ''"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute z-30 right-0 w-full mt-1.5 bg-white dark:bg-slate-800
                                rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                        {{-- Busca --}}
                        <div class="px-3 pt-3 pb-2 border-b border-slate-100 dark:border-slate-700">
                            <div class="relative">
                                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                <input x-ref="ownerSearch"
                                       x-model="query"
                                       type="text"
                                       placeholder="Buscar responsável..."
                                       class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700/60
                                              border border-slate-200 dark:border-slate-600 rounded-lg
                                              text-slate-800 dark:text-white placeholder-slate-400
                                              focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                                <button x-show="query" @click="query = ''; $refs.ownerSearch.focus()" type="button"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                    <x-lucide-x class="w-3 h-3" />
                                </button>
                            </div>
                        </div>

                        {{-- Lista --}}
                        <div class="max-h-48 overflow-y-auto py-1.5">
                            <template x-for="opt in filtered" :key="opt.value">
                                <button @click="val = opt.value; open = false; query = ''" type="button"
                                    class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm cursor-pointer transition"
                                    :class="String(val) === String(opt.value)
                                        ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                    <template x-if="opt.value !== ''">
                                        <span class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600
                                                     flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                              x-text="opt.initials"></span>
                                    </template>
                                    <template x-if="opt.value === ''">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                            <x-lucide-user class="w-3.5 h-3.5 text-slate-400" />
                                        </div>
                                    </template>
                                    <span x-text="opt.name" class="flex-1 text-left truncate"></span>
                                    <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0"
                                                    x-show="String(val) === String(opt.value)" />
                                </button>
                            </template>

                            {{-- Sem resultados --}}
                            <div x-show="filtered.length === 0"
                                 class="px-4 py-5 text-center text-xs text-slate-400">
                                <x-lucide-user-x class="w-5 h-5 mx-auto mb-1.5 opacity-40" />
                                Nenhum usuário encontrado
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Departamento com busca (condicional) --}}
            @if ($objLevel === 'department')
                <div x-data="{
                    open: false,
                    query: '',
                    val: $wire.entangle('objDepartmentId'),
                    opts: [
                        { value: '', name: 'Nenhum' },
                        @foreach ($this->departments as $dept)
                        { value: {{ $dept->id }}, name: '{{ addslashes($dept->name) }}' },
                        @endforeach
                    ],
                    get filtered() {
                        if (!this.query) return this.opts;
                        return this.opts.filter(o => o.name.toLowerCase().includes(this.query.toLowerCase()));
                    },
                    get selected() { return this.opts.find(o => String(o.value) === String(this.val)) ?? this.opts[0] },
                    openAndFocus() { this.open = true; this.$nextTick(() => this.$refs.search?.focus()) }
                }" class="relative" @keydown.escape="open = false">

                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                        Departamento *
                    </label>

                    {{-- Trigger --}}
                    <button @click="openAndFocus()" type="button"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm
                               bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left"
                        :class="selected.value !== '' ? 'text-slate-800 dark:text-white' : 'text-slate-400'">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                             :class="selected.value !== '' ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                            <x-lucide-building-2 class="w-3.5 h-3.5 transition-colors"
                                                 ::class="selected.value !== '' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400'" />
                        </div>
                        <span class="flex-1 truncate font-medium" x-text="selected.name"></span>
                        <x-lucide-chevron-down class="w-4 h-4 text-slate-400 shrink-0 transition-transform"
                                               ::class="open ? 'rotate-180' : ''" />
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" @click.outside="open = false; query = ''"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800
                                rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                        {{-- Campo de busca --}}
                        <div class="px-3 pt-3 pb-2 border-b border-slate-100 dark:border-slate-700">
                            <div class="relative">
                                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                <input x-ref="search"
                                       x-model="query"
                                       type="text"
                                       placeholder="Buscar departamento..."
                                       class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700/60
                                              border border-slate-200 dark:border-slate-600 rounded-lg
                                              text-slate-800 dark:text-white placeholder-slate-400
                                              focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                                <button x-show="query" @click="query = ''; $refs.search.focus()" type="button"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                    <x-lucide-x class="w-3 h-3" />
                                </button>
                            </div>
                        </div>

                        {{-- Lista --}}
                        <div class="max-h-48 overflow-y-auto py-1.5">
                            <template x-for="opt in filtered" :key="opt.value">
                                <button @click="val = opt.value; open = false; query = ''" type="button"
                                    class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm cursor-pointer transition"
                                    :class="String(val) === String(opt.value)
                                        ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                    <div class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 transition-colors"
                                         :class="String(val) === String(opt.value)
                                             ? 'bg-indigo-100 dark:bg-indigo-900/40'
                                             : 'bg-slate-100 dark:bg-slate-700'">
                                        <x-lucide-building-2 class="w-3 h-3"
                                            ::class="String(val) === String(opt.value) ? 'text-indigo-500' : 'text-slate-400'" />
                                    </div>
                                    <span x-text="opt.name" class="flex-1 text-left truncate"></span>
                                    <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0"
                                                    x-show="String(val) === String(opt.value)" />
                                </button>
                            </template>

                            {{-- Sem resultados --}}
                            <div x-show="filtered.length === 0"
                                 class="px-4 py-5 text-center text-xs text-slate-400">
                                <x-lucide-search class="w-5 h-5 mx-auto mb-1.5 opacity-40" />
                                Nenhum departamento encontrado
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            {{-- Alinhar a objetivo pai --}}
            @if ($this->parentObjectives->isNotEmpty())
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                        Objetivo pai
                        <span class="font-normal text-slate-400">(opcional)</span>
                    </label>
                    <select wire:model="objParentId"
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 cursor-pointer">
                        <option value="">Nenhum</option>
                        @foreach ($this->parentObjectives as $parent)
                            <option value="{{ $parent->id }}">[{{ ucfirst($parent->level) }}] {{ $parent->title }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-between items-center gap-3 px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('objectiveModal', false)"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Cancelar
            </button>
            <button wire:click="saveObjective" wire:loading.attr="disabled" wire:target="saveObjective"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition cursor-pointer
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       shadow-md shadow-blue-500/20 disabled:opacity-60">
                <span wire:loading.remove wire:target="saveObjective">
                    {{ $objectiveMode === 'create' ? 'Criar Objetivo' : 'Salvar Alterações' }}
                </span>
                <span wire:loading wire:target="saveObjective">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Modal: Key Result ─────────────────────────────────────────────── --}}
@if ($krModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('krModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[92vh] sm:max-h-[88vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                {{ $krMode === 'create' ? 'bg-gradient-to-br from-teal-500 to-emerald-600' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }}">
                        <x-lucide-bar-chart-2 class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
                            {{ $krMode === 'create' ? 'Novo Key Result' : 'Editar Key Result' }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $krMode === 'create' ? 'Como você vai medir o sucesso?' : 'Atualize os dados do resultado-chave' }}
                        </p>
                    </div>
                </div>
                <button wire:click="$set('krModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-5">

            {{-- Título --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Título do Key Result *</label>
                <input wire:model="krTitle" type="text" placeholder="Ex: Aumentar NPS para 70 pontos"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                @error('krTitle') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tipo de medição: pills --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Tipo de medição</label>
                <div class="grid grid-cols-4 gap-2">

                    {{-- Numérico --}}
                    <button wire:click="$set('krType', 'numeric')" type="button"
                        class="flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 transition cursor-pointer
                               {{ $krType === 'numeric'
                                   ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-indigo-200 hover:bg-indigo-50/40' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                    {{ $krType === 'numeric' ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-hash class="w-3.5 h-3.5 {{ $krType === 'numeric' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-[10px] font-semibold leading-tight">Numérico</span>
                    </button>

                    {{-- Percentual --}}
                    <button wire:click="$set('krType', 'percentage')" type="button"
                        class="flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 transition cursor-pointer
                               {{ $krType === 'percentage'
                                   ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-violet-200 hover:bg-violet-50/40' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                    {{ $krType === 'percentage' ? 'bg-violet-100 dark:bg-violet-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-percent class="w-3.5 h-3.5 {{ $krType === 'percentage' ? 'text-violet-600 dark:text-violet-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-[10px] font-semibold leading-tight">Percentual</span>
                    </button>

                    {{-- Monetário --}}
                    <button wire:click="$set('krType', 'currency')" type="button"
                        class="flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 transition cursor-pointer
                               {{ $krType === 'currency'
                                   ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-emerald-200 hover:bg-emerald-50/40' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                    {{ $krType === 'currency' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-dollar-sign class="w-3.5 h-3.5 {{ $krType === 'currency' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-[10px] font-semibold leading-tight">Monetário</span>
                    </button>

                    {{-- Booleano --}}
                    <button wire:click="$set('krType', 'boolean')" type="button"
                        class="flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 transition cursor-pointer
                               {{ $krType === 'boolean'
                                   ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-amber-200 hover:bg-amber-50/40' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                    {{ $krType === 'boolean' ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-toggle-left class="w-3.5 h-3.5 {{ $krType === 'boolean' ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-[10px] font-semibold leading-tight">Sim / Não</span>
                    </button>

                </div>
            </div>

            {{-- Unidade (apenas para numérico) --}}
            @if ($krType === 'numeric')
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                        Unidade <span class="font-normal text-slate-400">(opcional)</span>
                    </label>
                    <div class="relative">
                        <x-lucide-tag class="absolute left-3.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                        <input wire:model="krUnit" type="text" placeholder="usuários, vendas, tickets..."
                            class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                                   rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                    </div>
                </div>
            @endif

            {{-- Valores numéricos --}}
            @if ($krType !== 'boolean')
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-3">Valores</p>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach ([
                            ['krInitial', 'Inicial',  '0',   'from-slate-400 to-slate-500'],
                            ['krTarget',  'Meta',     '100', 'from-indigo-500 to-violet-600'],
                            ['krCurrent', 'Atual',    '0',   'from-teal-400 to-emerald-500'],
                        ] as [$field, $label, $ph, $grad])
                            <div>
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="w-2 h-2 rounded-full bg-gradient-to-br {{ $grad }} shrink-0"></span>
                                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $label }}</label>
                                </div>
                                <input wire:model="{{ $field }}" type="number" step="any" placeholder="{{ $ph }}"
                                    class="w-full px-3 py-2.5 text-sm font-semibold text-center
                                           bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                                           rounded-xl text-slate-800 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                            </div>
                        @endforeach
                    </div>

                    {{-- Sufixo da unidade --}}
                    @if ($krType === 'percentage')
                        <p class="text-[10px] text-slate-400 mt-2 text-center">Valores em percentual (%)</p>
                    @elseif ($krType === 'currency')
                        <p class="text-[10px] text-slate-400 mt-2 text-center">Valores em reais (R$)</p>
                    @endif
                </div>
            @else
                {{-- Booleano --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Situação atual</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="$set('krCurrent', 0)" type="button"
                            class="flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 transition cursor-pointer text-sm font-semibold
                                   {{ (int)$krCurrent === 0
                                       ? 'border-rose-400 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300'
                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-slate-300' }}">
                            <x-lucide-x class="w-4 h-4" /> Não concluído
                        </button>
                        <button wire:click="$set('krCurrent', 1)" type="button"
                            class="flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 transition cursor-pointer text-sm font-semibold
                                   {{ (int)$krCurrent === 1
                                       ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300'
                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-slate-300' }}">
                            <x-lucide-check class="w-4 h-4" /> Concluído
                        </button>
                    </div>
                </div>
            @endif

            {{-- Responsável com busca + Data limite --}}
            <div class="grid grid-cols-2 gap-3">

                {{-- Responsável --}}
                <div x-data="{
                    open: false,
                    query: '',
                    val: $wire.entangle('krOwnerId'),
                    opts: [
                        { value: '', name: 'Nenhum', initials: '' },
                        @foreach ($this->users as $u)
                        { value: {{ $u->id }}, name: '{{ addslashes($u->name) }}', initials: '{{ strtoupper(mb_substr($u->name, 0, 1)) }}{{ strtoupper(mb_substr(explode(" ", trim($u->name))[1] ?? "", 0, 1)) }}' },
                        @endforeach
                    ],
                    get filtered() { return !this.query ? this.opts : this.opts.filter(o => o.name.toLowerCase().includes(this.query.toLowerCase())) },
                    get selected() { return this.opts.find(o => String(o.value) === String(this.val)) ?? this.opts[0] },
                    openAndFocus() { this.open = true; this.$nextTick(() => this.$refs.krOwnerSearch?.focus()) }
                }" class="relative" @keydown.escape="open = false">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Responsável</label>
                    <button @click="openAndFocus()" type="button"
                        class="w-full flex items-center gap-2 px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60
                               border border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer
                               focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left">
                        <template x-if="selected.value !== ''">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600
                                         flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                  x-text="selected.initials"></span>
                        </template>
                        <template x-if="selected.value === ''">
                            <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                <x-lucide-user class="w-3 h-3 text-slate-400" />
                            </div>
                        </template>
                        <span class="flex-1 truncate text-sm"
                              :class="selected.value === '' ? 'text-slate-400' : 'font-medium text-slate-800 dark:text-white'"
                              x-text="selected.name"></span>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" ::class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" @click.outside="open = false; query = ''"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute z-30 left-0 w-full mt-1.5 bg-white dark:bg-slate-800
                                rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-3 pt-3 pb-2 border-b border-slate-100 dark:border-slate-700">
                            <div class="relative">
                                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                <input x-ref="krOwnerSearch" x-model="query" type="text" placeholder="Buscar..."
                                    class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700/60
                                           border border-slate-200 dark:border-slate-600 rounded-lg
                                           text-slate-800 dark:text-white placeholder-slate-400
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                            </div>
                        </div>
                        <div class="max-h-44 overflow-y-auto py-1.5">
                            <template x-for="opt in filtered" :key="opt.value">
                                <button @click="val = opt.value; open = false; query = ''" type="button"
                                    class="w-full flex items-center gap-2 px-3.5 py-2 text-sm cursor-pointer transition"
                                    :class="String(val) === String(opt.value)
                                        ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                    <template x-if="opt.value !== ''">
                                        <span class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600
                                                     flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                              x-text="opt.initials"></span>
                                    </template>
                                    <template x-if="opt.value === ''">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                            <x-lucide-user class="w-3 h-3 text-slate-400" />
                                        </div>
                                    </template>
                                    <span x-text="opt.name" class="flex-1 text-left truncate"></span>
                                    <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0" x-show="String(val) === String(opt.value)" />
                                </button>
                            </template>
                            <div x-show="filtered.length === 0" class="px-4 py-4 text-center text-xs text-slate-400">
                                Nenhum usuário encontrado
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data limite --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Data limite</label>
                    <div class="relative">
                        <x-lucide-calendar class="absolute left-3.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                        <input wire:model="krDueDate" type="date"
                            class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60
                                   border border-slate-200 dark:border-slate-600 rounded-xl
                                   text-slate-800 dark:text-white
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition cursor-pointer" />
                    </div>
                </div>
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Descrição <span class="font-normal text-slate-400">(opcional)</span></label>
                <textarea wire:model="krDescription" rows="2" placeholder="Contexto adicional..."
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 resize-none transition"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-between items-center gap-3 px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('krModal', false)"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Cancelar
            </button>
            <button wire:click="saveKr" wire:loading.attr="disabled" wire:target="saveKr"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition cursor-pointer
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       shadow-md shadow-blue-500/20 disabled:opacity-60">
                <span wire:loading.remove wire:target="saveKr">
                    {{ $krMode === 'create' ? 'Criar Key Result' : 'Salvar Alterações' }}
                </span>
                <span wire:loading wire:target="saveKr">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Modal: Check-in ───────────────────────────────────────────────── --}}
@if ($checkinModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('checkinModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-md
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[92vh] sm:max-h-[88vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                bg-gradient-to-br from-amber-400 to-orange-500">
                        <x-lucide-zap class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">Check-in de Progresso</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Atualize o valor atual deste resultado-chave</p>
                    </div>
                </div>
                <button wire:click="$set('checkinModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Valor atual *</label>
                <input wire:model="checkinValue" type="number" step="any" placeholder="0"
                    class="w-full px-3.5 py-3 text-lg font-bold bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-300
                           focus:outline-none focus:ring-2 focus:ring-amber-400/40 transition" />
                @error('checkinValue') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Confiança --}}
            <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nível de confiança</label>
                    <span class="text-sm font-bold px-2.5 py-0.5 rounded-lg
                        {{ $checkinConfidence >= 8 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400'
                         : ($checkinConfidence >= 5 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400'
                         : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400') }}">
                        {{ $checkinConfidence }}/10
                    </span>
                </div>
                <input wire:model.live="checkinConfidence" type="range" min="1" max="10"
                    class="w-full accent-indigo-600 cursor-pointer" />
                <div class="flex justify-between text-[10px] text-slate-400 mt-1.5 font-medium">
                    <span>😟 Baixa</span>
                    <span>😐 Média</span>
                    <span>😊 Alta</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Comentário</label>
                <textarea wire:model="checkinComment" rows="3"
                    placeholder="O que impulsionou ou bloqueou o progresso?"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 resize-none transition"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-between items-center gap-3 px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('checkinModal', false)"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Cancelar
            </button>
            <button wire:click="saveCheckin" wire:loading.attr="disabled" wire:target="saveCheckin"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition cursor-pointer
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       shadow-md shadow-blue-500/20 disabled:opacity-60">
                <x-lucide-zap class="w-3.5 h-3.5" wire:loading.remove wire:target="saveCheckin" />
                <span wire:loading.remove wire:target="saveCheckin">Registrar Check-in</span>
                <span wire:loading wire:target="saveCheckin">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Modal: Histórico de Check-ins ─────────────────────────────────── --}}
@if ($historyModal && $this->historyKr)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('historyModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[85vh] sm:max-h-[80vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                bg-gradient-to-br from-slate-500 to-slate-700">
                        <x-lucide-clock class="w-4 h-4 text-white" />
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">Histórico de Check-ins</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate max-w-[220px] sm:max-w-xs">{{ $this->historyKr->title }}</p>
                    </div>
                </div>
                <button wire:click="$set('historyModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body (scrollável) --}}
        <div class="flex-1 overflow-y-auto px-5 py-4">
            @if ($this->historyKr->checkins->isEmpty())
                <div class="text-center py-12">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                        <x-lucide-clock class="w-6 h-6 text-slate-400 dark:text-slate-500" />
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Nenhum check-in registrado</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Os check-ins aparecerão aqui conforme forem registrados.</p>
                </div>
            @else
                {{-- Timeline --}}
                <div class="relative">
                    {{-- Linha vertical --}}
                    <div class="absolute left-[19px] top-0 bottom-0 w-px bg-slate-100 dark:bg-slate-700"></div>

                    <div class="space-y-4">
                        @foreach ($this->historyKr->checkins->sortByDesc('created_at') as $ci)
                            <div class="flex items-start gap-4">
                                {{-- Avatar com inicial --}}
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600
                                            flex items-center justify-center shrink-0 z-10 shadow-sm ring-2 ring-white dark:ring-slate-800">
                                    <span class="text-xs font-bold text-white">
                                        {{ strtoupper(substr($ci->user?->name ?? '?', 0, 1)) }}
                                    </span>
                                </div>

                                {{-- Card do check-in --}}
                                <div class="flex-1 min-w-0 bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3.5 border border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                                            {{ $ci->user?->name ?? '—' }}
                                        </p>
                                        <time class="text-[10px] text-slate-400 shrink-0">{{ $ci->created_at->format('d/m/y H:i') }}</time>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-base font-bold text-slate-800 dark:text-white">
                                            {{ number_format($ci->new_value, 2, ',', '.') }}
                                        </span>
                                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold px-2 py-0.5 rounded-lg
                                            {{ $ci->delta >= 0
                                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                                : 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                            {{ $ci->delta >= 0 ? '↑' : '↓' }} {{ $ci->delta_label }}
                                        </span>
                                        @if ($ci->confidence)
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                confiança {{ $ci->confidence }}/10
                                            </span>
                                        @endif
                                    </div>

                                    @if ($ci->comment)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed border-t border-slate-200 dark:border-slate-600 pt-2">
                                            {{ $ci->comment }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-end px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('historyModal', false)"
                class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Fechar
            </button>
        </div>
    </div>
</div>
@endif

<livewire:components.ui.modal.alert-modal />

{{-- ════════════════════════════════════════════════════════════════
     CONFIRM DIALOG
     ════════════════════════════════════════════════════════════════ --}}
<template x-if="confirmDialog.show">
    <div class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center sm:p-4"
         x-data
         x-effect="document.body.style.overflow = 'hidden'"
         @keydown.escape.window="confirmDialog.show = false">

        {{-- Backdrop --}}
        <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"
             @click="confirmDialog.show = false"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"></div>

        {{-- Card --}}
        <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-sm
                    rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
             x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
             x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

            {{-- Top accent bar --}}
            <div class="h-1 w-full bg-gradient-to-r from-rose-400 to-rose-600"></div>

            {{-- Body --}}
            <div class="px-6 pt-6 pb-5">
                {{-- Icon --}}
                <div class="mx-auto mb-4 w-14 h-14 rounded-2xl bg-rose-50 dark:bg-rose-900/30
                            flex items-center justify-center">
                    <x-lucide-trash-2 class="w-7 h-7 text-rose-500 dark:text-rose-400" />
                </div>

                {{-- Title --}}
                <h3 class="text-center text-base font-bold text-slate-800 dark:text-slate-100 mb-1"
                    x-text="confirmDialog.title"></h3>

                {{-- Message --}}
                <p class="text-center text-sm text-slate-500 dark:text-slate-400 leading-relaxed"
                   x-text="confirmDialog.message"></p>
            </div>

            {{-- Footer --}}
            <div class="px-6 pb-6 flex flex-col-reverse sm:flex-row gap-2.5">
                <button
                    @click="confirmDialog.show = false"
                    type="button"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-xl
                           text-slate-600 dark:text-slate-300
                           border border-slate-200 dark:border-slate-600
                           hover:bg-slate-50 dark:hover:bg-slate-700
                           transition cursor-pointer">
                    Cancelar
                </button>
                <button
                    @click="runConfirm()"
                    type="button"
                    class="flex-1 px-4 py-2.5 text-sm font-semibold rounded-xl
                           text-white bg-gradient-to-r from-rose-500 to-rose-600
                           hover:from-rose-600 hover:to-rose-700
                           shadow-sm shadow-rose-200 dark:shadow-rose-900
                           transition cursor-pointer">
                    Confirmar
                </button>
            </div>

        </div>
    </div>
</template>
</div>
