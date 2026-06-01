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
        'company'    => ['label' => 'Empresa',      'bg' => 'bg-violet-50 dark:bg-violet-900/30', 'text' => 'text-violet-700 dark:text-indigo-400', 'icon' => 'building-2'],
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
                    <x-lucide-circle-plus class="w-4 h-4" />
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
                            ['company', 'Empresa', 'building-2', 'text-indigo-600'],
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

@include('livewire.pages.okrs.modals._modal-ciclo')
@include('livewire.pages.okrs.modals._modal-objetivo')
@include('livewire.pages.okrs.modals._modal-key-result')
@include('livewire.pages.okrs.modals._modal-checkin')
@include('livewire.pages.okrs.modals._modal-historico')

{{-- ══════════════════════════════════════════════════════════════════ --}}
{{-- MODAL: Confirmação de exclusão (genérico)                          --}}
{{-- ══════════════════════════════════════════════════════════════════ --}}
<div x-show="confirmDialog.show"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     style="display:none"
     @click.self="confirmDialog.show = false">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.stop>
        <div class="flex items-start gap-4 mb-5">
            <div class="w-11 h-11 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                <x-lucide-trash-2 class="w-5 h-5 text-red-500" />
            </div>
            <div>
                <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100" x-text="confirmDialog.title"></h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 mt-1 leading-relaxed" x-text="confirmDialog.message"></p>
            </div>
        </div>
        <div class="flex justify-end gap-2">
            <button type="button"
                    @click="confirmDialog.show = false"
                    class="px-4 py-2 text-xs font-semibold rounded-xl border border-slate-200 dark:border-slate-700
                           text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                Cancelar
            </button>
            <button type="button"
                    @click="runConfirm()"
                    class="px-4 py-2 text-xs font-semibold rounded-xl bg-red-500 hover:bg-red-600 text-white transition cursor-pointer">
                Confirmar exclusão
            </button>
        </div>
    </div>
</div>
