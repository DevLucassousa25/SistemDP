<div class="min-h-screen bg-slate-50 dark:bg-slate-900 p-4 sm:p-6"
     x-data>

    @once
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@1.15.2/Sortable.min.js"></script>
    @endonce

    {{-- ───── Cabeçalho ──────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-xl font-semibold text-slate-800 dark:text-white lato-bold">Tarefas</h1>
            <p class="text-sm text-slate-400 lato-regular mt-0.5">
                @if (auth()->user()->isGerente())
                    Gerencie suas tarefas e as do seu departamento
                @else
                    Organize e acompanhe suas tarefas diárias
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            {{-- Toggle lista / kanban --}}
            <div class="flex items-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 gap-1">
                <button type="button" wire:click="setViewMode('list')"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg transition cursor-pointer
                               {{ $viewMode === 'list' ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-list class="w-3.5 h-3.5" /> Lista
                </button>
                <button type="button" wire:click="setViewMode('kanban')"
                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg transition cursor-pointer
                               {{ $viewMode === 'kanban' ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-columns-3 class="w-3.5 h-3.5" /> Kanban
                </button>
            </div>

            <button type="button" wire:click="abrirModal"
                    class="flex items-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm
                           font-semibold lato-bold px-4 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                <x-lucide-plus class="w-4 h-4" />
                Nova tarefa
            </button>
        </div>
    </div>

    {{-- ───── Cards de resumo ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-5 gap-3 mb-6">

        {{-- Total --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Total</p>
                <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                    <x-lucide-list-checks class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" />
                </span>
            </div>
            <p class="text-2xl font-semibold lato-bold text-slate-700 dark:text-slate-200">{{ $this->stats['total'] }}</p>
        </div>

        {{-- Pendentes --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Pendentes</p>
                <span class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center shrink-0">
                    <x-lucide-clock class="w-3.5 h-3.5 text-amber-500" />
                </span>
            </div>
            <p class="text-2xl font-semibold lato-bold text-amber-500">{{ $this->stats['pendentes'] }}</p>
        </div>

        {{-- Em andamento --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Em andamento</p>
                <span class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center shrink-0">
                    <x-lucide-loader-circle class="w-3.5 h-3.5 text-blue-500" />
                </span>
            </div>
            <p class="text-2xl font-semibold lato-bold text-blue-600 dark:text-blue-400">{{ $this->stats['em_andamento'] }}</p>
        </div>

        {{-- Concluídas --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Concluídas</p>
                <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center shrink-0">
                    <x-lucide-circle-check class="w-3.5 h-3.5 text-emerald-500" />
                </span>
            </div>
            <p class="text-2xl font-semibold lato-bold text-emerald-600 dark:text-emerald-400">{{ $this->stats['concluidas'] }}</p>
        </div>

        {{-- Vencidas --}}
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-3 sm:p-4">
            <div class="flex items-center justify-between mb-2">
                <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Vencidas</p>
                <span class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0">
                    <x-lucide-circle-alert class="w-3.5 h-3.5 text-red-500" />
                </span>
            </div>
            <p class="text-2xl font-semibold lato-bold text-red-500 dark:text-red-400">{{ $this->stats['vencidas'] }}</p>
        </div>

    </div>

    {{-- ───── Abas ─────────────────────────────────────────────────────── --}}
    <div class="flex gap-1 mb-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 w-fit flex-wrap">
        @foreach (array_filter([
            'minhas'       => 'Minhas tarefas',
            'departamento' => auth()->user()->isGerente() ? 'Departamento' : null,
            'plano_acao'   => 'Plano de ação',
            'relatorio'    => 'Relatório',
        ]) as $tab => $label)
            <button type="button" wire:click="$set('activeTab', '{{ $tab }}')"
                    class="px-4 py-2 text-sm lato-bold rounded-lg transition cursor-pointer
                           {{ $activeTab === $tab
                               ? 'bg-emerald-500 text-white shadow-sm'
                               : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                {{ $label }}
                @if ($tab === 'minhas' && $this->stats['pendentes'] > 0)
                    <span class="ml-1 text-[10px] {{ $activeTab === $tab ? 'bg-white/20' : 'bg-slate-100 dark:bg-slate-700' }} px-1.5 py-0.5 rounded-full">
                        {{ $this->stats['pendentes'] }}
                    </span>
                @endif
                @if ($tab === 'plano_acao' && $this->pendingActionCount > 0)
                    <span class="ml-1.5 min-w-[18px] h-[18px] inline-flex items-center justify-center text-[10px] lato-bold px-1 rounded-full
                                 {{ $activeTab === $tab ? 'bg-white/25 text-white' : 'bg-red-500 text-white' }}">
                        {{ $this->pendingActionCount > 99 ? '99+' : $this->pendingActionCount }}
                    </span>
                @endif
            </button>
        @endforeach
    </div>

    {{-- ───── Filtros ─────────────────────────────────────────────────── --}}
    @if ($activeTab !== 'plano_acao' && $activeTab !== 'relatorio')
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 flex flex-col gap-3 mb-5">

            {{-- Busca --}}
            <div class="relative w-full">
                <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Buscar tarefas..."
                       class="w-full pl-10 pr-9 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-600
                              rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400
                              focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent transition" />
                @if ($search)
                    <button wire:click="$set('search', '')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-pointer">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                @endif
            </div>

            {{-- Dropdowns de filtro --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">

                {{-- Filtro Status --}}
                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer
                                   {{ $statusFilter ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400' }}">
                        <div class="flex items-center gap-1.5 truncate">
                            @if ($statusFilter === 'pendente')
                                <x-lucide-clock class="w-3.5 h-3.5 flex-shrink-0 text-amber-500" />
                            @elseif ($statusFilter === 'em_andamento')
                                <x-lucide-loader-circle class="w-3.5 h-3.5 flex-shrink-0 text-blue-500" />
                            @elseif ($statusFilter === 'concluida')
                                <x-lucide-circle-check class="w-3.5 h-3.5 flex-shrink-0 text-emerald-500" />
                            @elseif ($statusFilter === 'cancelada')
                                <x-lucide-circle-x class="w-3.5 h-3.5 flex-shrink-0 text-slate-400" />
                            @else
                                <x-lucide-filter class="w-3.5 h-3.5 flex-shrink-0" />
                            @endif
                            <span class="truncate text-xs font-medium lato-bold">
                                {{ match($statusFilter) {
                                    'pendente'     => 'Pendente',
                                    'em_andamento' => 'Em andamento',
                                    'concluida'    => 'Concluída',
                                    'cancelada'    => 'Cancelada',
                                    default        => 'Status',
                                } }}
                            </span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                        <button type="button" @click="open=false; $wire.set('statusFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <x-lucide-filter class="w-3.5 h-3.5" /> Todos os status
                        </button>
                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                        <button type="button" @click="open=false; $wire.set('statusFilter', 'pendente')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-amber-50 dark:hover:bg-amber-900/10 flex items-center justify-between
                                       {{ $statusFilter === 'pendente' ? 'bg-amber-50 dark:bg-amber-900/10 font-medium text-amber-700 dark:text-amber-400' : 'text-slate-700 dark:text-slate-300' }}">
                            <span class="flex items-center gap-2"><x-lucide-clock class="w-3.5 h-3.5 text-amber-500" /> Pendente</span>
                            @if ($statusFilter === 'pendente') <x-lucide-check class="w-3.5 h-3.5 text-amber-500" /> @endif
                        </button>
                        <button type="button" @click="open=false; $wire.set('statusFilter', 'em_andamento')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-blue-50 dark:hover:bg-blue-900/10 flex items-center justify-between
                                       {{ $statusFilter === 'em_andamento' ? 'bg-blue-50 dark:bg-blue-900/10 font-medium text-blue-700 dark:text-blue-400' : 'text-slate-700 dark:text-slate-300' }}">
                            <span class="flex items-center gap-2"><x-lucide-loader-circle class="w-3.5 h-3.5 text-blue-500" /> Em andamento</span>
                            @if ($statusFilter === 'em_andamento') <x-lucide-check class="w-3.5 h-3.5 text-blue-500" /> @endif
                        </button>
                        <button type="button" @click="open=false; $wire.set('statusFilter', 'concluida')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-emerald-50 dark:hover:bg-emerald-900/10 flex items-center justify-between
                                       {{ $statusFilter === 'concluida' ? 'bg-emerald-50 dark:bg-emerald-900/10 font-medium text-emerald-700 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }}">
                            <span class="flex items-center gap-2"><x-lucide-circle-check class="w-3.5 h-3.5 text-emerald-500" /> Concluída</span>
                            @if ($statusFilter === 'concluida') <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" /> @endif
                        </button>
                        <button type="button" @click="open=false; $wire.set('statusFilter', 'cancelada')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700/50 flex items-center justify-between
                                       {{ $statusFilter === 'cancelada' ? 'bg-slate-100 dark:bg-slate-700 font-medium text-slate-600 dark:text-slate-300' : 'text-slate-700 dark:text-slate-300' }}">
                            <span class="flex items-center gap-2"><x-lucide-circle-x class="w-3.5 h-3.5 text-slate-400" /> Cancelada</span>
                            @if ($statusFilter === 'cancelada') <x-lucide-check class="w-3.5 h-3.5 text-slate-500" /> @endif
                        </button>
                    </div>
                </div>

                {{-- Filtro Prioridade --}}
                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer
                                   {{ $priorityFilter ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400' }}">
                        <div class="flex items-center gap-1.5 truncate">
                            <x-lucide-flag class="w-3.5 h-3.5 flex-shrink-0
                                {{ $priorityFilter === 'alta' ? 'text-red-500' : ($priorityFilter === 'media' ? 'text-amber-500' : ($priorityFilter === 'baixa' ? 'text-emerald-500' : '')) }}" />
                            <span class="truncate text-xs font-medium lato-bold">
                                {{ match($priorityFilter) {
                                    'alta'  => 'Alta',
                                    'media' => 'Média',
                                    'baixa' => 'Baixa',
                                    default => 'Prioridade',
                                } }}
                            </span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute right-0 mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                        <button type="button" @click="open=false; $wire.set('priorityFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <x-lucide-flag class="w-3.5 h-3.5" /> Todas
                        </button>
                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                        <button type="button" @click="open=false; $wire.set('priorityFilter', 'alta')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-red-50 dark:hover:bg-red-900/10 flex items-center justify-between
                                       {{ $priorityFilter === 'alta' ? 'bg-red-50 dark:bg-red-900/10 font-medium text-red-700 dark:text-red-400' : 'text-slate-700 dark:text-slate-300' }}">
                            <span class="flex items-center gap-2"><x-lucide-flag class="w-3.5 h-3.5 text-red-500" /> Alta</span>
                            @if ($priorityFilter === 'alta') <x-lucide-check class="w-3.5 h-3.5 text-red-500" /> @endif
                        </button>
                        <button type="button" @click="open=false; $wire.set('priorityFilter', 'media')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-amber-50 dark:hover:bg-amber-900/10 flex items-center justify-between
                                       {{ $priorityFilter === 'media' ? 'bg-amber-50 dark:bg-amber-900/10 font-medium text-amber-700 dark:text-amber-400' : 'text-slate-700 dark:text-slate-300' }}">
                            <span class="flex items-center gap-2"><x-lucide-flag class="w-3.5 h-3.5 text-amber-500" /> Média</span>
                            @if ($priorityFilter === 'media') <x-lucide-check class="w-3.5 h-3.5 text-amber-500" /> @endif
                        </button>
                        <button type="button" @click="open=false; $wire.set('priorityFilter', 'baixa')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-emerald-50 dark:hover:bg-emerald-900/10 flex items-center justify-between
                                       {{ $priorityFilter === 'baixa' ? 'bg-emerald-50 dark:bg-emerald-900/10 font-medium text-emerald-700 dark:text-emerald-400' : 'text-slate-700 dark:text-slate-300' }}">
                            <span class="flex items-center gap-2"><x-lucide-flag class="w-3.5 h-3.5 text-emerald-500" /> Baixa</span>
                            @if ($priorityFilter === 'baixa') <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" /> @endif
                        </button>
                    </div>
                </div>

                {{-- Filtro Prazo --}}
                @php
                    $dueDateLabels = [
                        ''           => 'Prazo',
                        'hoje'       => 'Hoje',
                        'esta_semana'=> 'Esta semana',
                        'proximos_7' => 'Próximos 7 dias',
                        'vencidas'   => 'Vencidas',
                        'sem_prazo'  => 'Sem prazo',
                    ];
                @endphp
                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer
                                   {{ $dueDateFilter ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400' }}">
                        <div class="flex items-center gap-1.5 truncate">
                            @if ($dueDateFilter === 'vencidas')
                                <x-lucide-calendar-x class="w-3.5 h-3.5 flex-shrink-0 text-red-500" />
                            @elseif ($dueDateFilter === 'sem_prazo')
                                <x-lucide-calendar-off class="w-3.5 h-3.5 flex-shrink-0 text-slate-400" />
                            @elseif ($dueDateFilter)
                                <x-lucide-calendar class="w-3.5 h-3.5 flex-shrink-0 text-indigo-500" />
                            @else
                                <x-lucide-calendar class="w-3.5 h-3.5 flex-shrink-0" />
                            @endif
                            <span class="truncate text-xs font-medium lato-bold">
                                {{ $dueDateLabels[$dueDateFilter] ?? 'Prazo' }}
                            </span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                        <button type="button" @click="open=false; $wire.set('dueDateFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <x-lucide-calendar class="w-3.5 h-3.5" /> Qualquer prazo
                        </button>
                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                        @foreach ([
                            'hoje'        => ['Hoje',             'lucide-calendar-check', 'text-emerald-500'],
                            'esta_semana' => ['Esta semana',      'lucide-calendar-range',  'text-indigo-500'],
                            'proximos_7'  => ['Próximos 7 dias',  'lucide-calendar-clock',  'text-blue-500'],
                            'vencidas'    => ['Vencidas',         'lucide-calendar-x',      'text-red-500'],
                            'sem_prazo'   => ['Sem prazo',        'lucide-calendar-off',    'text-slate-400'],
                        ] as $val => [$lbl, $icon, $iconColor])
                            <button type="button" @click="open=false; $wire.set('dueDateFilter', '{{ $val }}')"
                                    class="w-full text-left px-3 py-2 text-sm flex items-center justify-between transition
                                           {{ $dueDateFilter === $val
                                               ? 'bg-emerald-50 dark:bg-emerald-900/10 font-medium text-emerald-700 dark:text-emerald-400'
                                               : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <span class="flex items-center gap-2">
                                    <x-dynamic-component :component="$icon" class="w-3.5 h-3.5 {{ $iconColor }}" />
                                    {{ $lbl }}
                                </span>
                                @if ($dueDateFilter === $val) <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" /> @endif
                            </button>
                        @endforeach
                    </div>
                </div>

                {{-- Filtro Recorrência --}}
                <div x-data="{ open: false }" class="relative">
                    <button type="button" @click="open = !open"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer
                                   {{ $recurrenceFilter ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400' }}">
                        <div class="flex items-center gap-1.5 truncate">
                            <x-lucide-repeat class="w-3.5 h-3.5 flex-shrink-0
                                {{ $recurrenceFilter === 'sim' ? 'text-indigo-500' : ($recurrenceFilter === 'nao' ? 'text-slate-400' : '') }}" />
                            <span class="truncate text-xs font-medium lato-bold">
                                {{ match($recurrenceFilter) {
                                    'sim' => 'Recorrentes',
                                    'nao' => 'Sem recorrência',
                                    default => 'Recorrência',
                                } }}
                            </span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" @click.outside="open = false" x-transition
                         class="absolute right-0 mt-1 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                        <button type="button" @click="open=false; $wire.set('recurrenceFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                            <x-lucide-repeat class="w-3.5 h-3.5" /> Todas
                        </button>
                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                        <button type="button" @click="open=false; $wire.set('recurrenceFilter', 'sim')"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between transition
                                       {{ $recurrenceFilter === 'sim' ? 'bg-indigo-50 dark:bg-indigo-900/10 font-medium text-indigo-700 dark:text-indigo-400' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <span class="flex items-center gap-2"><x-lucide-repeat class="w-3.5 h-3.5 text-indigo-500" /> Recorrentes</span>
                            @if ($recurrenceFilter === 'sim') <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" /> @endif
                        </button>
                        <button type="button" @click="open=false; $wire.set('recurrenceFilter', 'nao')"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between transition
                                       {{ $recurrenceFilter === 'nao' ? 'bg-slate-100 dark:bg-slate-700 font-medium text-slate-700 dark:text-slate-300' : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <span class="flex items-center gap-2"><x-lucide-repeat class="w-3.5 h-3.5 text-slate-400" /> Sem recorrência</span>
                            @if ($recurrenceFilter === 'nao') <x-lucide-check class="w-3.5 h-3.5 text-slate-500" /> @endif
                        </button>
                    </div>
                </div>

            </div>

            {{-- Filtro Responsável (apenas gerentes na aba departamento) --}}
            @if ($activeTab === 'departamento' && auth()->user()->isGerente() && $this->assignableUsers->isNotEmpty())
                <div class="flex items-center gap-2 pt-1 border-t border-slate-100 dark:border-slate-700">
                    <span class="text-[11px] text-slate-400 lato-regular shrink-0 flex items-center gap-1">
                        <x-lucide-user class="w-3 h-3" /> Responsável:
                    </span>
                    <div class="flex flex-wrap gap-1.5">
                        <button type="button" wire:click="$set('assigneeFilter', null)"
                                class="text-[11px] lato-bold px-2.5 py-1 rounded-full border transition cursor-pointer
                                       {{ !$assigneeFilter ? 'bg-slate-700 dark:bg-slate-300 text-white dark:text-slate-800 border-transparent' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-slate-300' }}">
                            Todos
                        </button>
                        @foreach ($this->assignableUsers as $u)
                            <button type="button" wire:click="$set('assigneeFilter', {{ $u->id }})"
                                    class="text-[11px] lato-bold px-2.5 py-1 rounded-full border transition cursor-pointer
                                           {{ $assigneeFilter === $u->id
                                               ? 'bg-indigo-500 text-white border-indigo-500'
                                               : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-indigo-300 hover:text-indigo-600' }}">
                                {{ $u->name }}
                            </button>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Linha de Tags + GroupBy --}}
            @if ($this->allTags->isNotEmpty() || true)
            <div class="flex items-center gap-2 flex-wrap pt-1 border-t border-slate-100 dark:border-slate-700">
                <span class="text-[11px] text-slate-400 lato-regular shrink-0 flex items-center gap-1">
                    <x-lucide-tag class="w-3 h-3" /> Tags:
                </span>
                @foreach ($this->allTags as $tag)
                    <button type="button" wire:click="toggleTagFilter({{ $tag->id }})"
                            class="flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] lato-bold border transition cursor-pointer"
                            style="border-color: {{ $tag->color }}; color: {{ in_array($tag->id, $tagFilter) ? '#fff' : $tag->color }}; background: {{ in_array($tag->id, $tagFilter) ? $tag->color : 'transparent' }};">
                        {{ $tag->name }}
                        @if (in_array($tag->id, $tagFilter))
                            <x-lucide-x class="w-2.5 h-2.5" />
                        @endif
                    </button>
                @endforeach
                @if ($this->allTags->isEmpty())
                    <span class="text-[11px] text-slate-400 lato-regular italic">Nenhuma tag criada ainda</span>
                @endif

                {{-- Dropdown Agrupar --}}
                <div class="ml-auto" x-data="{ open: false }" @click.outside="open = false">
                    <button type="button" @click="open = !open"
                            class="flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg border transition cursor-pointer
                                   {{ $groupBy !== 'none'
                                       ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400'
                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-500' }}">
                        <x-lucide-layers class="w-3.5 h-3.5 flex-shrink-0" />
                        <span>
                            {{ match($groupBy) {
                                'priority' => 'Por prioridade',
                                'due_date' => 'Por prazo',
                                default    => 'Agrupar',
                            } }}
                        </span>
                        @if ($groupBy !== 'none')
                            <span wire:click.stop="$set('groupBy', 'none')"
                                  class="ml-0.5 hover:opacity-75 transition cursor-pointer">
                                <x-lucide-x class="w-3 h-3" />
                            </span>
                        @else
                            <x-lucide-chevron-down class="w-3 h-3 transition-transform flex-shrink-0"
                                                   x-bind:class="open ? 'rotate-180' : ''" />
                        @endif
                    </button>

                    <div x-show="open" x-transition
                         class="absolute right-0 mt-1.5 w-44 bg-white dark:bg-slate-800 border border-slate-200
                                dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                        <div class="px-3 py-1.5 border-b border-slate-100 dark:border-slate-700">
                            <span class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Agrupar por</span>
                        </div>
                        @foreach ([
                            'none'     => ['Sem agrupamento', 'lucide-x-circle',   'text-slate-500'],
                            'priority' => ['Por prioridade',  'lucide-flag',        'text-amber-500'],
                            'due_date' => ['Por prazo',       'lucide-calendar',    'text-blue-500'],
                        ] as $val => [$lbl, $icon, $iconColor])
                            <button type="button"
                                    @click="open = false; $wire.set('groupBy', '{{ $val }}')"
                                    class="w-full flex items-center gap-2 px-3 py-2 text-sm text-left transition cursor-pointer
                                           {{ $groupBy === $val
                                               ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 font-semibold'
                                               : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                                <x-dynamic-component :component="$icon" class="w-3.5 h-3.5 {{ $iconColor }} flex-shrink-0" />
                                <span class="lato-regular text-sm flex-1">{{ $lbl }}</span>
                                @if ($groupBy === $val)
                                    <x-lucide-check class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" />
                                @endif
                            </button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
        </div>

        {{-- Chips de filtros ativos --}}
        @if ($search || $statusFilter || $priorityFilter || count($tagFilter) || $dueDateFilter || $recurrenceFilter || $assigneeFilter)
            <div class="flex items-center gap-2 flex-wrap -mt-2 mb-4">
                <span class="text-xs text-slate-400 lato-regular">Filtros:</span>

                @if ($search)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs font-medium lato-bold">
                        <x-lucide-search class="w-3 h-3" />
                        "{{ Str::limit($search, 20) }}"
                        <button type="button" wire:click="$set('search', '')" class="ml-0.5 hover:text-slate-900 dark:hover:text-white transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($statusFilter)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-medium lato-bold">
                        <x-lucide-filter class="w-3 h-3" />
                        {{ match($statusFilter) { 'pendente' => 'Pendente', 'em_andamento' => 'Em andamento', 'concluida' => 'Concluída', 'cancelada' => 'Cancelada', default => $statusFilter } }}
                        <button type="button" wire:click="$set('statusFilter', '')" class="ml-0.5 hover:text-blue-900 dark:hover:text-white transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($priorityFilter)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-medium lato-bold">
                        <x-lucide-flag class="w-3 h-3" />
                        {{ match($priorityFilter) { 'alta' => 'Alta', 'media' => 'Média', 'baixa' => 'Baixa', default => $priorityFilter } }}
                        <button type="button" wire:click="$set('priorityFilter', '')" class="ml-0.5 hover:text-amber-900 dark:hover:text-white transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($dueDateFilter)
                    @php
                        $dueDateChipLabels = ['hoje'=>'Hoje','esta_semana'=>'Esta semana','proximos_7'=>'Próximos 7 dias','vencidas'=>'Vencidas','sem_prazo'=>'Sem prazo'];
                        $dueDateChipColor  = $dueDateFilter === 'vencidas' ? 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400' : 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-400';
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium lato-bold {{ $dueDateChipColor }}">
                        <x-lucide-calendar class="w-3 h-3" />
                        {{ $dueDateChipLabels[$dueDateFilter] ?? $dueDateFilter }}
                        <button type="button" wire:click="$set('dueDateFilter', '')" class="ml-0.5 hover:opacity-75 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($recurrenceFilter)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-400 text-xs font-medium lato-bold">
                        <x-lucide-repeat class="w-3 h-3" />
                        {{ $recurrenceFilter === 'sim' ? 'Recorrentes' : 'Sem recorrência' }}
                        <button type="button" wire:click="$set('recurrenceFilter', '')" class="ml-0.5 hover:opacity-75 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($assigneeFilter)
                    @php $au = $this->assignableUsers->firstWhere('id', $assigneeFilter); @endphp
                    @if ($au)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-teal-100 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 text-xs font-medium lato-bold">
                            <x-lucide-user class="w-3 h-3" />
                            {{ $au->name }}
                            <button type="button" wire:click="$set('assigneeFilter', null)" class="ml-0.5 hover:opacity-75 transition cursor-pointer">
                                <x-lucide-x class="w-3 h-3" />
                            </button>
                        </span>
                    @endif
                @endif

                @foreach ($tagFilter as $filteredTagId)
                    @php $ft = $this->allTags->firstWhere('id', $filteredTagId); @endphp
                    @if ($ft)
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-white text-xs font-medium lato-bold"
                              style="background: {{ $ft->color }}">
                            <x-lucide-tag class="w-3 h-3" />
                            {{ $ft->name }}
                            <button type="button" wire:click="toggleTagFilter({{ $ft->id }})" class="ml-0.5 hover:opacity-75 transition cursor-pointer">
                                <x-lucide-x class="w-3 h-3" />
                            </button>
                        </span>
                    @endif
                @endforeach

                <button type="button" wire:click="limparFiltros"
                        class="text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 lato-regular transition cursor-pointer underline underline-offset-2">
                    Limpar todos
                </button>
            </div>
        @endif
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- KANBAN                                                         --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if ($viewMode === 'kanban' && in_array($activeTab, ['minhas','departamento']))
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4"
             x-data="{
                 _sortables: [],
                 setupKanban() {
                     if (typeof Sortable === 'undefined') return;
                     this._sortables.forEach(s => s.destroy());
                     this._sortables = [];
                     const self = this;
                     ['pendente','em_andamento','concluida','cancelada'].forEach(status => {
                         const el = document.getElementById('kanban-col-' + status);
                         if (!el) return;
                         self._sortables.push(Sortable.create(el, {
                             group: 'kanban',
                             animation: 150,
                             ghostClass: 'opacity-30',
                             dragClass: 'shadow-lg scale-[1.02]',
                             onEnd(evt) {
                                 const taskId   = parseInt(evt.item.dataset.taskId);
                                 const fromStatus = evt.from.dataset.status;
                                 const toStatus   = evt.to.dataset.status;
                                 if (taskId && toStatus && fromStatus !== toStatus) {
                                     self.$wire.moverKanban(taskId, toStatus);
                                 }
                             }
                         }));
                     });
                 }
             }"
             x-init="
                 $nextTick(() => setupKanban());

                 const _lwHandler = () => $nextTick(() => setupKanban());
                 document.addEventListener('livewire:updated', _lwHandler);
                 $el._kanbanLwHandler = _lwHandler;

                 window.addEventListener('kanban-atualizado', _lwHandler);
                 $el._kanbanMvHandler = _lwHandler;
             "
             x-destroy="
                 document.removeEventListener('livewire:updated', $el._kanbanLwHandler);
                 window.removeEventListener('kanban-atualizado', $el._kanbanMvHandler);
             ">

            @php
                $colDefs = [
                    'pendente'     => ['Pendente',     'w-3 h-3 rounded-full bg-slate-400 shrink-0'],
                    'em_andamento' => ['Em andamento', 'w-3 h-3 rounded-full bg-blue-500 shrink-0'],
                    'concluida'    => ['Concluída',    'w-3 h-3 rounded-full bg-emerald-500 shrink-0'],
                    'cancelada'    => ['Cancelada',    'w-3 h-3 rounded-full bg-slate-300 shrink-0'],
                ];
                $priorityDot = [
                    'alta'  => ['bg-red-500',   'Alta'],
                    'media' => ['bg-amber-400', 'Média'],
                    'baixa' => ['bg-sky-400',   'Baixa'],
                ];
            @endphp

            @foreach ($colDefs as $colStatus => [$colLabel, $colDotClass])
                @php $colTasks = $this->kanbanTasks->get($colStatus, collect()); @endphp
                <div class="flex flex-col gap-3">

                    {{-- Header da coluna --}}
                    <div class="flex items-center gap-2 px-1">
                        <span class="{{ $colDotClass }}"></span>
                        <span class="text-sm lato-bold text-slate-600 dark:text-slate-300">{{ $colLabel }}</span>
                        <span class="text-xs text-slate-400 dark:text-slate-500 lato-regular ml-0.5">{{ $colTasks->count() }}</span>
                    </div>

                    {{-- Cards --}}
                    <div id="kanban-col-{{ $colStatus }}"
                         data-status="{{ $colStatus }}"
                         class="flex flex-col gap-1.5 min-h-[80px]">

                        @foreach ($colTasks as $task)
                            @php
                                $kParts    = explode(' ', trim($task->assignedTo?->name ?? ''));
                                $kInitials = strtoupper(substr($kParts[0], 0, 1) . (isset($kParts[1]) ? substr($kParts[1], 0, 1) : ''));
                                $kPalette  = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500','bg-blue-400','bg-rose-400'];
                                $kColor    = $task->assignedTo ? $kPalette[abs(crc32($task->assignedTo->name)) % count($kPalette)] : 'bg-slate-300';
                            @endphp
                            <div wire:key="kanban-{{ $task->id }}"
                                 data-task-id="{{ $task->id }}"
                                 class="group bg-white dark:bg-slate-800
                                        border border-slate-200 dark:border-slate-700
                                        rounded-lg p-3 cursor-grab active:cursor-grabbing select-none
                                        hover:bg-slate-50 dark:hover:bg-slate-750
                                        transition-colors duration-100">

                                {{-- Linha topo: título + avatar --}}
                                <div class="flex items-start justify-between gap-2 mb-2.5">
                                    <p class="text-[13px] lato-bold leading-snug flex-1
                                               {{ in_array($task->status, ['concluida','cancelada'])
                                                   ? 'line-through text-slate-400 dark:text-slate-500'
                                                   : 'text-slate-700 dark:text-slate-200' }}">
                                        {{ $task->title }}
                                    </p>
                                    @if ($task->assignedTo)
                                        <span title="{{ $task->assignedTo->name }}"
                                              class="w-5 h-5 rounded-full {{ $kColor }} text-white text-[8px] lato-bold
                                                     flex items-center justify-center shrink-0 mt-0.5">
                                            {{ $kInitials }}
                                        </span>
                                    @endif
                                </div>

                                {{-- Barra progresso subtarefas --}}
                                @if ($task->subtasks_total > 0)
                                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1 overflow-hidden mb-2.5">
                                        <div class="h-1 rounded-full transition-all duration-500
                                                    {{ $task->subtasks_progress === 100 ? 'bg-emerald-500' : 'bg-blue-400' }}"
                                             style="width: {{ $task->subtasks_progress }}%">
                                        </div>
                                    </div>
                                @endif

                                {{-- Footer: chips --}}
                                <div class="flex items-center gap-1.5 flex-wrap">

                                    {{-- Prioridade --}}
                                    <span class="inline-flex items-center gap-1 text-[11px] lato-regular
                                                 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400
                                                 px-1.5 py-0.5 rounded-md">
                                        <span class="w-1.5 h-1.5 rounded-full shrink-0 {{ $priorityDot[$task->priority][0] ?? 'bg-slate-400' }}"></span>
                                        {{ $priorityDot[$task->priority][1] ?? '' }}
                                    </span>

                                    {{-- Tags --}}
                                    @foreach ($task->tags as $tag)
                                        <span class="inline-flex items-center gap-1 text-[11px] lato-regular px-1.5 py-0.5 rounded-md"
                                              style="background: {{ $tag->color }}18; color: {{ $tag->color }}">
                                            <span class="w-1.5 h-1.5 rounded-full shrink-0" style="background: {{ $tag->color }}"></span>
                                            {{ $tag->name }}
                                        </span>
                                    @endforeach

                                    {{-- Subtarefas --}}
                                    @if ($task->subtasks_total > 0)
                                        <span class="inline-flex items-center gap-1 text-[11px] lato-regular
                                                     bg-slate-100 dark:bg-slate-700
                                                     {{ $task->subtasks_done === $task->subtasks_total ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-500 dark:text-slate-400' }}
                                                     px-1.5 py-0.5 rounded-md">
                                            <x-lucide-list-checks class="w-3 h-3" />
                                            {{ $task->subtasks_done }}/{{ $task->subtasks_total }}
                                        </span>
                                    @endif

                                    {{-- Prazo --}}
                                    @if ($task->due_date)
                                        <span class="inline-flex items-center gap-1 text-[11px] lato-regular px-1.5 py-0.5 rounded-md
                                                     {{ $task->is_overdue
                                                         ? 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400'
                                                         : ($task->due_date->isToday()
                                                             ? 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400'
                                                             : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400') }}">
                                            <x-lucide-calendar class="w-3 h-3 shrink-0" />
                                            {{ $task->due_date->format('d/m') }}
                                        </span>
                                    @endif

                                    {{-- Comentários --}}
                                    <button type="button" wire:click="abrirComentarios({{ $task->id }})"
                                            class="inline-flex items-center gap-1 text-[11px] lato-regular px-1.5 py-0.5 rounded-md cursor-pointer transition
                                                   {{ $task->comments_count > 0
                                                       ? 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 hover:text-indigo-500'
                                                       : 'text-slate-300 dark:text-slate-600 hover:text-slate-400' }}">
                                        <x-lucide-message-circle class="w-3 h-3" />
                                        @if ($task->comments_count > 0) {{ $task->comments_count }} @endif
                                    </button>

                                    {{-- Recorrência --}}
                                    @if ($task->recurrence)
                                        <span class="inline-flex items-center gap-1 text-[11px] lato-regular px-1.5 py-0.5 rounded-md
                                                     bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500"
                                              title="{{ $task->recurrence_label }}">
                                            <x-lucide-repeat class="w-3 h-3" />
                                        </span>
                                    @endif

                                    {{-- Botão editar (hover) --}}
                                    <button type="button" wire:click="editar({{ $task->id }})"
                                            class="opacity-0 group-hover:opacity-100 inline-flex items-center gap-1 text-[11px]
                                                   px-1.5 py-0.5 rounded-md ml-auto
                                                   text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20
                                                   transition cursor-pointer">
                                        <x-lucide-pencil class="w-3 h-3" />
                                    </button>
                                </div>
                            </div>
                        @endforeach

                        {{-- Estado vazio --}}
                        @if ($colTasks->isEmpty())
                            <div class="flex items-center justify-center py-6 rounded-lg
                                        border border-dashed border-slate-200 dark:border-slate-700
                                        text-slate-300 dark:text-slate-600 text-[11px] lato-regular">
                                Arraste aqui
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- ABA: Minhas tarefas (lista)                                    --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'minhas' && $viewMode === 'list')
        @if ($this->myTasks->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <x-lucide-check-square class="w-6 h-6 text-slate-400" />
                </div>
                <p class="text-slate-500 dark:text-slate-400 lato-regular text-sm">Nenhuma tarefa encontrada.</p>
                <button type="button" wire:click="abrirModal"
                        class="mt-4 text-sm text-emerald-600 hover:text-emerald-700 lato-bold transition cursor-pointer">
                    + Criar primeira tarefa
                </button>
            </div>
        @else
            @php
                $grouped = match($groupBy) {
                    'priority' => $this->myTasks->groupBy('priority'),
                    'due_date' => $this->myTasks->groupBy(fn($t) => $t->due_date?->format('d/m/Y') ?? 'Sem prazo'),
                    default    => null,
                };
                $groupLabels = ['alta' => '🔴 Alta prioridade', 'media' => '🟡 Média prioridade', 'baixa' => '🔵 Baixa prioridade'];
            @endphp
            @if ($grouped)
                @foreach ($grouped as $groupKey => $groupTasks)
                    <div class="mb-5">
                        <p class="text-xs font-bold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2 flex items-center gap-1.5">
                            <x-lucide-chevron-right class="w-3.5 h-3.5" />
                            {{ $groupLabels[$groupKey] ?? $groupKey }}
                            <span class="font-normal normal-case">({{ $groupTasks->count() }})</span>
                        </p>
                        <div class="space-y-2 pl-4 border-l-2 border-slate-200 dark:border-slate-700">
                            @foreach ($groupTasks as $task)
                                @include('livewire.pages.tasks._task-card', ['task' => $task, 'showAssignee' => false])
                            @endforeach
                        </div>
                    </div>
                @endforeach
            @else
                <div class="space-y-2">
                    @foreach ($this->myTasks as $task)
                        @include('livewire.pages.tasks._task-card', ['task' => $task, 'showAssignee' => false])
                    @endforeach
                </div>
            @endif
        @endif
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- ABA: Departamento (só gerente, lista)                          --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'departamento' && auth()->user()->isGerente() && $viewMode === 'list')
        @if ($this->deptTasks->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-4">
                    <x-lucide-users class="w-6 h-6 text-slate-400" />
                </div>
                <p class="text-slate-500 dark:text-slate-400 lato-regular text-sm">Nenhuma tarefa atribuída no departamento.</p>
            </div>
        @else
            @php
                $deptGrouped = match($groupBy) {
                    'priority' => $this->deptTasks->groupBy('priority'),
                    'due_date' => $this->deptTasks->groupBy(fn($t) => $t->due_date?->format('d/m/Y') ?? 'Sem prazo'),
                    default    => $this->deptTasks->groupBy(fn($t) => $t->assignedTo?->name ?? '—'),
                };
                $isDeptDefault = $groupBy === 'none';
            @endphp
            @foreach ($deptGrouped as $groupKey => $groupTasks)
                <div class="mb-6">
                    <div class="flex items-center gap-2 mb-3">
                        @if ($isDeptDefault)
                            <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center
                                        text-indigo-600 dark:text-indigo-300 text-xs font-bold lato-black shrink-0">
                                {{ mb_strtoupper(mb_substr($groupKey, 0, 1)) }}
                            </div>
                        @endif
                        <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold">
                            {{ $groupLabels[$groupKey] ?? $groupKey }}
                        </span>
                        <span class="text-[11px] text-slate-400 lato-regular">({{ $groupTasks->count() }})</span>
                    </div>
                    <div class="space-y-2 {{ $isDeptDefault ? 'pl-9' : 'pl-4 border-l-2 border-slate-200 dark:border-slate-700' }}">
                        @foreach ($groupTasks as $task)
                            @include('livewire.pages.tasks._task-card', ['task' => $task, 'showAssignee' => $isDeptDefault])
                        @endforeach
                    </div>
                </div>
            @endforeach
        @endif
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- ABA: Plano de ação                                             --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'plano_acao')
        @php
            $canValidate = auth()->user()->isGerente() || auth()->user()->isRhOuDp();
            $isRh        = auth()->user()->isRhOuDp();
        @endphp

        @if ($this->actionPlanTasks->isEmpty())
            <div class="flex flex-col items-center justify-center py-16 text-center">
                <div class="w-14 h-14 rounded-full bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center mb-4">
                    <x-lucide-clipboard-list class="w-6 h-6 text-indigo-400" />
                </div>
                <p class="text-slate-600 dark:text-slate-300 lato-bold text-sm">Nenhum plano de ação ativo</p>
                <p class="text-xs text-slate-400 lato-regular mt-1">Planos criados nos feedbacks aparecem aqui automaticamente.</p>
            </div>
        @else
            <div class="space-y-6">
            @foreach ($this->actionPlanTasks->groupBy('feedback_id') as $feedbackId => $planTasks)
                @php
                    $feedback   = $planTasks->first()->feedback;
                    $done       = $planTasks->where('completed', true)->count();
                    $total      = $planTasks->count();
                    $pct        = $total > 0 ? round($done / $total * 100) : 0;
                    $allDone    = $pct === 100;
                    $hasPhases  = $planTasks->whereNotNull('phase')->isNotEmpty();

                    // Datas do plano (menor e maior due_date das tarefas, ou action_plan_deadline)
                    $planEnd    = $feedback?->action_plan_deadline;
                    $planStart  = $feedback?->occurred_at;

                    // Responsável geral (gestor do plano)
                    $planResp   = $feedback?->actionPlanResponsible ?? $feedback?->evaluator;
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-xl border
                            {{ $allDone ? 'border-emerald-200 dark:border-emerald-800/40' : 'border-slate-200 dark:border-slate-700' }}
                            overflow-hidden">

                    {{-- ── Cabeçalho do plano ─────────────────────────── --}}
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700
                                {{ $allDone ? 'bg-emerald-50/60 dark:bg-emerald-900/10' : 'bg-indigo-50/50 dark:bg-indigo-900/10' }}">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-start gap-3">
                                <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0 mt-0.5
                                            {{ $allDone ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-indigo-100 dark:bg-indigo-900/30' }}">
                                    @if ($allDone)
                                        <x-lucide-circle-check-big class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                                    @else
                                        <x-lucide-clipboard-list class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" />
                                    @endif
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <p class="text-sm lato-bold text-slate-800 dark:text-slate-100">
                                            Plano de ação — {{ $feedback?->employee?->name ?? '—' }}
                                        </p>
                                        @if ($allDone)
                                            <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/40 text-emerald-700 dark:text-emerald-400">
                                                Concluído
                                            </span>
                                        @else
                                            <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-400">
                                                Em andamento
                                            </span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] text-slate-500 dark:text-slate-400 lato-regular mt-0.5">
                                        {{ $feedback?->category_label }}
                                        @if ($planStart) · Iniciado em {{ $planStart->format('d/m/Y') }} @endif
                                    </p>
                                </div>
                            </div>
                            {{-- <a href="{{ route('feedback') }}"
                               class="flex items-center gap-1 text-[11px] text-indigo-500 hover:text-indigo-700
                                      dark:text-indigo-400 dark:hover:text-indigo-300 lato-bold transition shrink-0">
                                <x-lucide-external-link class="w-3 h-3" />
                                Ver feedback
                            </a> --}}
                        </div>

                        {{-- Metadados do plano --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-3">

                            {{-- Objetivo --}}
                            @if ($feedback?->action_plan)
                                <div class="flex items-start gap-1.5 w-full">
                                    <x-lucide-target class="w-3.5 h-3.5 text-indigo-400 shrink-0 mt-0.5" />
                                    <p class="text-[12px] text-slate-600 dark:text-slate-300 lato-regular leading-relaxed">
                                        <span class="lato-bold text-slate-700 dark:text-slate-200">Objetivo: </span>{{ $feedback->action_plan }}
                                    </p>
                                </div>
                            @endif

                            {{-- Prazo geral --}}
                            @if ($planEnd)
                                <span class="flex items-center gap-1 text-[11px] lato-regular
                                             {{ $planEnd->isPast() && ! $allDone ? 'text-red-500 font-semibold' : 'text-slate-500 dark:text-slate-400' }}">
                                    <x-lucide-calendar-clock class="w-3.5 h-3.5" />
                                    Prazo: {{ $planEnd->format('d/m/Y') }}
                                    @if ($planEnd->isPast() && ! $allDone)
                                        <span class="text-[10px] bg-red-100 dark:bg-red-900/30 text-red-600 px-1.5 py-0.5 rounded-full lato-bold">Vencido</span>
                                    @endif
                                </span>
                            @endif

                            {{-- Gestor responsável --}}
                            @if ($planResp)
                                <span class="flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400 lato-regular">
                                    <x-lucide-user-check class="w-3.5 h-3.5" />
                                    Gestor: {{ $planResp->name }}
                                </span>
                            @endif

                            {{-- Funcionário --}}
                            @if ($feedback?->employee)
                                <span class="flex items-center gap-1 text-[11px] text-slate-500 dark:text-slate-400 lato-regular">
                                    <x-lucide-user class="w-3.5 h-3.5" />
                                    Funcionário: {{ $feedback->employee->name }}
                                </span>
                            @endif
                        </div>
                    </div>

                    {{-- ── Barra de progresso ──────────────────────────── --}}
                    <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50">
                        <div class="flex items-center justify-between mb-1.5">
                            <span class="text-[11px] text-slate-500 lato-bold dark:text-slate-400 uppercase tracking-wide">Progresso</span>
                            <span class="text-[11px] lato-bold {{ $allDone ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-600 dark:text-indigo-400' }}">
                                {{ $done }}/{{ $total }} · {{ $pct }}%
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                            <div class="h-2 rounded-full transition-all duration-700
                                        {{ $allDone ? 'bg-emerald-500' : 'bg-indigo-500' }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>
                    </div>

                    {{-- ── Tarefas (agrupadas por fase se houver) ──────── --}}
                    @php
                        $phaseOrder  = ['30_dias' => 1, '60_dias' => 2, '90_dias' => 3, '' => 4, null => 4];
                        $sortedTasks = $planTasks->sortBy([
                            fn ($t) => $phaseOrder[$t->phase ?? ''] ?? 4,
                            fn ($t) => (int) $t->completed,
                            fn ($t) => $t->due_date,
                        ]);

                        $currentPhase   = '__INIT__';
                        $phaseLabels    = ['30_dias' => '1ª fase — 30 dias', '60_dias' => '2ª fase — 60 dias', '90_dias' => '3ª fase — 90 dias'];
                        $phaseDoneCounts = [];
                        $phaseTotalCounts = [];
                        foreach ($planTasks as $t) {
                            $ph = $t->phase ?? 'sem_fase';
                            $phaseTotalCounts[$ph] = ($phaseTotalCounts[$ph] ?? 0) + 1;
                            if ($t->completed) $phaseDoneCounts[$ph] = ($phaseDoneCounts[$ph] ?? 0) + 1;
                        }
                    @endphp

                    <div class="divide-y divide-slate-100 dark:divide-slate-700/70">
                        @foreach ($sortedTasks as $planTask)
                            @php $thisPhase = $planTask->phase ?? null; @endphp

                            {{-- Cabeçalho de fase (só quando muda de fase) --}}
                            @if ($hasPhases && $thisPhase !== $currentPhase)
                                @php $currentPhase = $thisPhase; @endphp
                                @if ($thisPhase && isset($phaseLabels[$thisPhase]))
                                    @php
                                        $phDone  = $phaseDoneCounts[$thisPhase] ?? 0;
                                        $phTotal = $phaseTotalCounts[$thisPhase] ?? 0;
                                        $phDoneAll = $phDone === $phTotal && $phTotal > 0;
                                    @endphp
                                    <div class="flex items-center gap-2 px-5 py-2
                                                {{ $phDoneAll ? 'bg-emerald-50/60 dark:bg-emerald-900/10' : 'bg-slate-50 dark:bg-slate-700/30' }}">
                                        <span class="text-[10px] lato-bold uppercase tracking-wider
                                                     {{ $phDoneAll ? 'text-emerald-600 dark:text-emerald-400' : 'text-indigo-500 dark:text-indigo-400' }}">
                                            {{ $phaseLabels[$thisPhase] }}
                                        </span>
                                        @if ($phDoneAll)
                                            <x-lucide-check-circle-2 class="w-3 h-3 text-emerald-500" />
                                        @endif
                                        <span class="ml-auto text-[10px] text-slate-400 lato-regular">{{ $phDone }}/{{ $phTotal }}</span>
                                    </div>
                                @endif
                            @endif

                            {{-- Linha da tarefa --}}
                            <div class="flex items-start gap-3 px-5 py-3.5
                                        {{ $planTask->completed ? 'bg-emerald-50/30 dark:bg-emerald-900/5' : 'hover:bg-slate-50/70 dark:hover:bg-slate-700/20' }}
                                        transition-colors duration-150">

                                {{-- Botão de validação --}}
                                @if ($planTask->completed)
                                    <div class="mt-0.5 w-5 h-5 rounded-full bg-emerald-500 shrink-0 flex items-center justify-center">
                                        <x-lucide-check class="w-3 h-3 text-white" />
                                    </div>
                                @elseif ($canValidate)
                                    <button type="button"
                                            wire:click="abrirValidacao({{ $planTask->id }})"
                                            title="Validar conclusão"
                                            class="mt-0.5 w-5 h-5 rounded-full shrink-0 flex items-center justify-center cursor-pointer transition
                                                   border-2 border-slate-300 dark:border-slate-500 hover:border-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20">
                                    </button>
                                @else
                                    {{-- Funcionário: só visualiza, não pode validar --}}
                                    <div class="mt-0.5 w-5 h-5 rounded-full shrink-0
                                                border-2 border-slate-200 dark:border-slate-600"></div>
                                @endif

                                <div class="flex-1 min-w-0">
                                    {{-- Descrição --}}
                                    <p class="text-sm lato-regular leading-snug
                                               {{ $planTask->completed
                                                   ? 'line-through text-slate-400 dark:text-slate-500'
                                                   : 'text-slate-700 dark:text-slate-200' }}">
                                        {{ $planTask->description }}
                                    </p>

                                    {{-- Recursos da empresa --}}
                                    @if ($planTask->resources)
                                        <div class="flex items-start gap-1 mt-1.5">
                                            <x-lucide-package class="w-3 h-3 text-blue-400 shrink-0 mt-0.5" />
                                            <p class="text-[11px] text-blue-600 dark:text-blue-400 lato-regular leading-snug">
                                                <span class="lato-bold">Recursos: </span>{{ $planTask->resources }}
                                            </p>
                                        </div>
                                    @endif

                                    {{-- Nota de validação (evidência) --}}
                                    @if ($planTask->completed && $planTask->validation_note)
                                        <div class="flex items-start gap-1 mt-1.5 px-2.5 py-1.5 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/40">
                                            <x-lucide-file-check class="w-3 h-3 text-emerald-500 shrink-0 mt-0.5" />
                                            <p class="text-[11px] text-emerald-700 dark:text-emerald-400 lato-regular leading-snug">
                                                <span class="lato-bold">Evidência: </span>{{ $planTask->validation_note }}
                                            </p>
                                        </div>
                                    @endif

                                    {{-- Meta row --}}
                                    <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-1.5">

                                        {{-- Responsável (funcionário) --}}
                                        @if ($planTask->responsible)
                                            <span class="flex items-center gap-1 text-[10px] text-slate-400 lato-regular">
                                                <x-lucide-user class="w-3 h-3" />
                                                {{ $planTask->responsible->name }}
                                            </span>
                                        @endif

                                        {{-- Prazo da tarefa --}}
                                        @if ($planTask->due_date)
                                            <span class="flex items-center gap-1 text-[10px] lato-regular
                                                         {{ $planTask->is_overdue ? 'text-red-500 lato-bold' : 'text-slate-400 dark:text-slate-500' }}">
                                                <x-lucide-calendar class="w-3 h-3" />
                                                {{ $planTask->due_date->format('d/m/Y') }}
                                                @if ($planTask->is_overdue)
                                                    <span class="bg-red-100 dark:bg-red-900/30 text-red-600 px-1 py-0.5 rounded text-[9px]">vencida</span>
                                                @endif
                                            </span>
                                        @endif

                                        {{-- Validado por --}}
                                        @if ($planTask->completed && $planTask->completedBy)
                                            <span class="flex items-center gap-1 text-[10px] text-emerald-600 dark:text-emerald-400 lato-regular">
                                                <x-lucide-shield-check class="w-3 h-3" />
                                                Validado por {{ Str::before($planTask->completedBy->name, ' ') }}
                                                · {{ $planTask->completed_at?->format('d/m/Y') }}
                                            </span>
                                        @endif

                                        {{-- Desfazer (só RH) --}}
                                        @if ($planTask->completed && $isRh)
                                            <button type="button"
                                                    wire:click="desfazerValidacao({{ $planTask->id }})"
                                                    class="ml-auto text-[10px] text-slate-400 hover:text-red-500 lato-regular
                                                           flex items-center gap-0.5 transition cursor-pointer">
                                                <x-lucide-rotate-ccw class="w-3 h-3" />
                                                Desfazer
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
            </div>
        @endif

    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- ABA: Relatório                                                 --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'relatorio')
        @php
            $r          = $this->reportData;
            $isGerente  = auth()->user()->isGerente();
            $taxaConcl  = $r['total'] > 0 ? round($r['concluidas'] / $r['total'] * 100) : 0;
        @endphp

        {{-- ──────────────────────────────────────────────────────────── --}}
        {{-- SEÇÃO: Minhas tarefas                                        --}}
        {{-- ──────────────────────────────────────────────────────────── --}}
        <div class="flex items-center gap-2 mb-4">
            <x-lucide-user class="w-4 h-4 text-slate-400" />
            <h2 class="text-sm lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Minhas tarefas</h2>
        </div>

        {{-- KPIs — linha 1 --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Total</p>
                    <span class="w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <x-lucide-list-checks class="w-3.5 h-3.5 text-slate-500" />
                    </span>
                </div>
                <p class="text-2xl lato-bold text-slate-800 dark:text-white">{{ $r['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Concluídas</p>
                    <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                        <x-lucide-circle-check class="w-3.5 h-3.5 text-emerald-500" />
                    </span>
                </div>
                <p class="text-2xl lato-bold text-emerald-600 dark:text-emerald-400">{{ $r['concluidas'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Em andamento</p>
                    <span class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                        <x-lucide-loader-circle class="w-3.5 h-3.5 text-blue-500" />
                    </span>
                </div>
                <p class="text-2xl lato-bold text-blue-600 dark:text-blue-400">{{ $r['emAndamento'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Vencidas</p>
                    <span class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                        <x-lucide-circle-alert class="w-3.5 h-3.5 text-red-500" />
                    </span>
                </div>
                <p class="text-2xl lato-bold {{ $r['vencidas'] > 0 ? 'text-red-500' : 'text-slate-400 dark:text-slate-500' }}">{{ $r['vencidas'] }}</p>
            </div>
        </div>

        {{-- KPIs — linha 2 --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Taxa de conclusão</p>
                    <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                        <x-lucide-percent class="w-3.5 h-3.5 text-indigo-500" />
                    </span>
                </div>
                <p class="text-2xl lato-bold text-indigo-600 dark:text-indigo-400">{{ $taxaConcl }}%</p>
                <div class="mt-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1">
                    <div class="h-1 rounded-full bg-indigo-400 transition-all" style="width: {{ $taxaConcl }}%"></div>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Cumprimento de prazo</p>
                    <span class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center">
                        <x-lucide-calendar-check class="w-3.5 h-3.5 text-teal-500" />
                    </span>
                </div>
                <p class="text-2xl lato-bold {{ $r['taxaPrazo'] !== null ? 'text-teal-600 dark:text-teal-400' : 'text-slate-400' }}">
                    {{ $r['taxaPrazo'] !== null ? $r['taxaPrazo'].'%' : '—' }}
                </p>
                @if ($r['taxaPrazo'] !== null)
                    <div class="mt-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1">
                        <div class="h-1 rounded-full bg-teal-400 transition-all" style="width: {{ $r['taxaPrazo'] }}%"></div>
                    </div>
                @endif
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Tempo médio</p>
                    <span class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                        <x-lucide-timer class="w-3.5 h-3.5 text-amber-500" />
                    </span>
                </div>
                <p class="text-2xl lato-bold text-slate-700 dark:text-slate-200">
                    {{ $r['avgDays'] > 0 ? $r['avgDays'].'d' : '—' }}
                </p>
                <p class="text-[10px] text-slate-400 lato-regular mt-0.5">para concluir</p>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center justify-between mb-2">
                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Recorrentes</p>
                    <span class="w-7 h-7 rounded-lg bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center">
                        <x-lucide-repeat class="w-3.5 h-3.5 text-violet-500" />
                    </span>
                </div>
                <p class="text-2xl lato-bold text-violet-600 dark:text-violet-400">{{ $r['recorrentes'] }}</p>
                <p class="text-[10px] text-slate-400 lato-regular mt-0.5">de {{ $r['total'] }} tarefas</p>
            </div>
        </div>

        {{-- Gráfico 7 dias + Prioridade/Status --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">

            {{-- Gráfico 7 dias --}}
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <x-lucide-trending-up class="w-4 h-4 text-emerald-500" />
                    Atividade — últimos 7 dias
                </h3>
                @php
                    $maxVal7 = max(array_merge(array_column($r['days']->toArray(), 'criadas'), array_column($r['days']->toArray(), 'concluidas'), [1]));
                @endphp
                <div class="flex items-end gap-2 h-28">
                    @foreach ($r['days'] as $day)
                        <div class="flex-1 flex flex-col items-center gap-1">
                            <div class="w-full flex flex-col-reverse gap-0.5">
                                <div class="w-full rounded-t bg-emerald-400/70 dark:bg-emerald-500/60 transition-all min-h-[1px]"
                                     style="height: {{ $maxVal7 > 0 ? max(2, round($day['concluidas'] / $maxVal7 * 80)) : 2 }}px"
                                     title="Concluídas: {{ $day['concluidas'] }}"></div>
                                <div class="w-full rounded-t bg-blue-300/60 dark:bg-blue-400/50 transition-all min-h-[1px]"
                                     style="height: {{ $maxVal7 > 0 ? max(2, round($day['criadas'] / $maxVal7 * 80)) : 2 }}px"
                                     title="Criadas: {{ $day['criadas'] }}"></div>
                            </div>
                            <span class="text-[10px] text-slate-400 lato-regular">{{ $day['date'] }}</span>
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-4 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <span class="flex items-center gap-1.5 text-[11px] text-slate-500 lato-regular"><span class="w-3 h-2 rounded-sm bg-blue-300/70 inline-block"></span> Criadas</span>
                    <span class="flex items-center gap-1.5 text-[11px] text-slate-500 lato-regular"><span class="w-3 h-2 rounded-sm bg-emerald-400/70 inline-block"></span> Concluídas</span>
                </div>
            </div>

            {{-- Por prioridade + status --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-4">
                <div>
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-2">
                        <x-lucide-flag class="w-4 h-4 text-amber-500" />
                        Por prioridade
                    </h3>
                    <div class="space-y-2.5">
                        @foreach (['alta' => ['Alta','bg-red-500','text-red-600 dark:text-red-400'], 'media' => ['Média','bg-amber-400','text-amber-600 dark:text-amber-400'], 'baixa' => ['Baixa','bg-slate-400','text-slate-500 dark:text-slate-400']] as $pKey => [$pLabel, $pBar, $pText])
                            @php $pCount = $r['byPriority'][$pKey]; $pPct = $r['total'] > 0 ? round($pCount / $r['total'] * 100) : 0; @endphp
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs lato-bold {{ $pText }}">{{ $pLabel }}</span>
                                    <span class="text-xs text-slate-400 lato-regular">{{ $pCount }} · {{ $pPct }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $pBar }} transition-all" style="width: {{ $pPct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
                <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                    <h3 class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2.5">Por status</h3>
                    <div class="space-y-1.5">
                        @foreach (['pendente' => ['Pendente','text-slate-500','bg-slate-100 dark:bg-slate-700'], 'em_andamento' => ['Em andamento','text-blue-600 dark:text-blue-400','bg-blue-50 dark:bg-blue-900/20'], 'concluida' => ['Concluída','text-emerald-600 dark:text-emerald-400','bg-emerald-50 dark:bg-emerald-900/20'], 'cancelada' => ['Cancelada','text-rose-500','bg-rose-50 dark:bg-rose-900/20']] as $sKey => [$sLabel, $sColor, $sBg])
                            <div class="flex justify-between items-center px-2.5 py-1.5 rounded-lg {{ $sBg }}">
                                <span class="text-xs lato-regular {{ $sColor }}">{{ $sLabel }}</span>
                                <span class="text-xs lato-bold {{ $sColor }}">{{ $r['byStatus'][$sKey] }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>

        {{-- Heatmap 30 dias + Tempo médio por prioridade + Top Tags --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-5">

            {{-- Heatmap 30 dias --}}
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4">
                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-2">
                    <x-lucide-calendar-days class="w-4 h-4 text-indigo-500" />
                    Concluídas — últimos 30 dias
                </h3>
                @php $maxHeat = max($r['days30']->max('concluidas'), 1); @endphp
                <div class="flex flex-wrap gap-1">
                    @foreach ($r['days30'] as $hDay)
                        @php
                            $intensity = $hDay['concluidas'] > 0 ? min(100, round($hDay['concluidas'] / $maxHeat * 100)) : 0;
                            $heatClass = match(true) {
                                $intensity === 0  => 'bg-slate-100 dark:bg-slate-700',
                                $intensity <= 25  => 'bg-emerald-200 dark:bg-emerald-900/50',
                                $intensity <= 50  => 'bg-emerald-300 dark:bg-emerald-700/70',
                                $intensity <= 75  => 'bg-emerald-400 dark:bg-emerald-600',
                                default           => 'bg-emerald-500 dark:bg-emerald-500',
                            };
                        @endphp
                        <div class="w-7 h-7 rounded-md {{ $heatClass }} flex items-center justify-center cursor-default transition-all"
                             title="{{ $hDay['label'] }}: {{ $hDay['concluidas'] }} concluída(s)">
                            @if ($hDay['concluidas'] > 0)
                                <span class="text-[9px] lato-bold text-white">{{ $hDay['concluidas'] }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <span class="text-[10px] text-slate-400 lato-regular">Menos</span>
                    @foreach (['bg-slate-100 dark:bg-slate-700', 'bg-emerald-200', 'bg-emerald-300', 'bg-emerald-400', 'bg-emerald-500'] as $hc)
                        <span class="w-4 h-4 rounded {{ $hc }} inline-block"></span>
                    @endforeach
                    <span class="text-[10px] text-slate-400 lato-regular">Mais</span>
                </div>
            </div>

            {{-- Tempo médio por prioridade + Top Tags --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-4">
                <div>
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-2">
                        <x-lucide-timer class="w-4 h-4 text-amber-500" />
                        Tempo médio por prioridade
                    </h3>
                    <div class="space-y-2">
                        @foreach (['alta' => ['Alta','text-red-500','bg-red-400'], 'media' => ['Média','text-amber-500','bg-amber-400'], 'baixa' => ['Baixa','text-slate-500','bg-slate-400']] as $pk => [$pl, $pc, $pb])
                            <div class="flex items-center justify-between py-1.5 border-b border-slate-100 dark:border-slate-700/60 last:border-0">
                                <span class="flex items-center gap-1.5 text-xs lato-regular text-slate-600 dark:text-slate-300">
                                    <span class="w-2 h-2 rounded-full {{ $pb }} inline-block shrink-0"></span>
                                    {{ $pl }}
                                </span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">
                                    {{ $r['avgByPriority'][$pk] ? $r['avgByPriority'][$pk].'d' : '—' }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>

                @if ($r['topTags']->isNotEmpty())
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                        <h3 class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2.5">Tags mais usadas</h3>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($r['topTags'] as $tag)
                                <span class="inline-flex items-center gap-1.5 text-[11px] lato-bold px-2.5 py-1 rounded-full text-white"
                                      style="background: {{ $tag->color }}">
                                    {{ $tag->name }}
                                    <span class="bg-white/25 text-white text-[9px] px-1 rounded-full">{{ $tag->total }}</span>
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- ──────────────────────────────────────────────────────────── --}}
        {{-- SEÇÃO: Relatório do Departamento (apenas gestores)           --}}
        {{-- ──────────────────────────────────────────────────────────── --}}
        @if ($isGerente && $r['dept'])
            @php $d = $r['dept']; $dTaxa = $d['total'] > 0 ? round($d['concluidas'] / $d['total'] * 100) : 0; @endphp

            <div class="flex items-center gap-3 mb-4 mt-2 pt-6 border-t border-slate-200 dark:border-slate-700">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center">
                    <x-lucide-building-2 class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200">Relatório do Departamento</h2>
                    <p class="text-[11px] text-slate-400 lato-regular">{{ $d['totalUsers'] }} colaborador(es) com tarefas</p>
                </div>
            </div>

            {{-- KPIs departamento — linha 1 --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-3">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Total dept.</p>
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                            <x-lucide-list-checks class="w-3.5 h-3.5 text-indigo-500" />
                        </span>
                    </div>
                    <p class="text-2xl lato-bold text-slate-800 dark:text-white">{{ $d['total'] }}</p>
                    <p class="text-[10px] text-slate-400 lato-regular mt-0.5">{{ $d['totalUsers'] }} colaboradores</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Concluídas</p>
                        <span class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                            <x-lucide-circle-check class="w-3.5 h-3.5 text-emerald-500" />
                        </span>
                    </div>
                    <p class="text-2xl lato-bold text-emerald-600 dark:text-emerald-400">{{ $d['concluidas'] }}</p>
                    <p class="text-[10px] text-slate-400 lato-regular mt-0.5">{{ $dTaxa }}% do total</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Em andamento</p>
                        <span class="w-7 h-7 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center">
                            <x-lucide-loader-circle class="w-3.5 h-3.5 text-blue-500" />
                        </span>
                    </div>
                    <p class="text-2xl lato-bold text-blue-600 dark:text-blue-400">{{ $d['emAndamento'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Vencidas</p>
                        <span class="w-7 h-7 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center">
                            <x-lucide-circle-alert class="w-3.5 h-3.5 text-red-500" />
                        </span>
                    </div>
                    <p class="text-2xl lato-bold {{ $d['vencidas'] > 0 ? 'text-red-500' : 'text-slate-400' }}">{{ $d['vencidas'] }}</p>
                </div>
            </div>

            {{-- KPIs departamento — linha 2 --}}
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-5">
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Taxa dept.</p>
                        <span class="w-7 h-7 rounded-lg bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center">
                            <x-lucide-percent class="w-3.5 h-3.5 text-indigo-500" />
                        </span>
                    </div>
                    <p class="text-2xl lato-bold text-indigo-600 dark:text-indigo-400">{{ $dTaxa }}%</p>
                    <div class="mt-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1">
                        <div class="h-1 rounded-full bg-indigo-400" style="width: {{ $dTaxa }}%"></div>
                    </div>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Prazo dept.</p>
                        <span class="w-7 h-7 rounded-lg bg-teal-50 dark:bg-teal-900/20 flex items-center justify-center">
                            <x-lucide-calendar-check class="w-3.5 h-3.5 text-teal-500" />
                        </span>
                    </div>
                    <p class="text-2xl lato-bold {{ $d['taxaPrazo'] !== null ? 'text-teal-600 dark:text-teal-400' : 'text-slate-400' }}">
                        {{ $d['taxaPrazo'] !== null ? $d['taxaPrazo'].'%' : '—' }}
                    </p>
                    @if ($d['taxaPrazo'] !== null)
                        <div class="mt-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1">
                            <div class="h-1 rounded-full bg-teal-400" style="width: {{ $d['taxaPrazo'] }}%"></div>
                        </div>
                    @endif
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <div class="flex items-center justify-between mb-2">
                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Tempo médio dept.</p>
                        <span class="w-7 h-7 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center">
                            <x-lucide-timer class="w-3.5 h-3.5 text-amber-500" />
                        </span>
                    </div>
                    <p class="text-2xl lato-bold text-slate-700 dark:text-slate-200">
                        {{ $d['avgDays'] > 0 ? $d['avgDays'].'d' : '—' }}
                    </p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <div class="flex items-center justify-between mb-1">
                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Destaque</p>
                        <x-lucide-trophy class="w-4 h-4 text-amber-400" />
                    </div>
                    @if ($d['mvp'])
                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 leading-snug truncate">
                            {{ Str::before($d['mvp']->name, ' ') }}
                        </p>
                        <p class="text-[10px] text-emerald-500 lato-regular mt-0.5">{{ $d['mvp']->done_tasks }} concluídas</p>
                    @else
                        <p class="text-slate-400 text-sm">—</p>
                    @endif
                </div>
            </div>

            {{-- Gráfico 7 dias dept + Prioridade dept --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-4">
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-trending-up class="w-4 h-4 text-indigo-500" />
                        Atividade do departamento — últimos 7 dias
                    </h3>
                    @php $maxDept7 = max(array_merge(array_column($d['days']->toArray(), 'criadas'), array_column($d['days']->toArray(), 'concluidas'), [1])); @endphp
                    <div class="flex items-end gap-2 h-28">
                        @foreach ($d['days'] as $day)
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <div class="w-full flex flex-col-reverse gap-0.5">
                                    <div class="w-full rounded-t bg-indigo-400/60 dark:bg-indigo-500/50 transition-all"
                                         style="height: {{ $maxDept7 > 0 ? max(2, round($day['concluidas'] / $maxDept7 * 80)) : 2 }}px"
                                         title="Concluídas: {{ $day['concluidas'] }}"></div>
                                    <div class="w-full rounded-t bg-violet-300/60 dark:bg-violet-400/40 transition-all"
                                         style="height: {{ $maxDept7 > 0 ? max(2, round($day['criadas'] / $maxDept7 * 80)) : 2 }}px"
                                         title="Criadas: {{ $day['criadas'] }}"></div>
                                </div>
                                <span class="text-[10px] text-slate-400 lato-regular">{{ $day['date'] }}</span>
                            </div>
                        @endforeach
                    </div>
                    <div class="flex items-center gap-4 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <span class="flex items-center gap-1.5 text-[11px] text-slate-500 lato-regular"><span class="w-3 h-2 rounded-sm bg-violet-300/70 inline-block"></span> Criadas</span>
                        <span class="flex items-center gap-1.5 text-[11px] text-slate-500 lato-regular"><span class="w-3 h-2 rounded-sm bg-indigo-400/70 inline-block"></span> Concluídas</span>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-2">
                        <x-lucide-flag class="w-4 h-4 text-amber-500" />
                        Prioridade no departamento
                    </h3>
                    <div class="space-y-2.5">
                        @foreach (['alta' => ['Alta','bg-red-500','text-red-600 dark:text-red-400'], 'media' => ['Média','bg-amber-400','text-amber-600 dark:text-amber-400'], 'baixa' => ['Baixa','bg-slate-400','text-slate-500 dark:text-slate-400']] as $pKey => [$pLabel, $pBar, $pText])
                            @php $pCount = $d['byPriority'][$pKey]; $pPct = $d['total'] > 0 ? round($pCount / $d['total'] * 100) : 0; @endphp
                            <div>
                                <div class="flex justify-between mb-1">
                                    <span class="text-xs lato-bold {{ $pText }}">{{ $pLabel }}</span>
                                    <span class="text-xs text-slate-400 lato-regular">{{ $pCount }} · {{ $pPct }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full {{ $pBar }} transition-all" style="width: {{ $pPct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    @if ($d['topTags']->isNotEmpty())
                        <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                            <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wide mb-2">Tags do dept.</h4>
                            <div class="flex flex-wrap gap-1">
                                @foreach ($d['topTags'] as $tag)
                                    <span class="inline-flex items-center gap-1 text-[10px] lato-bold px-2 py-0.5 rounded-full text-white"
                                          style="background: {{ $tag->color }}">
                                        {{ $tag->name }}
                                        <span class="bg-white/25 text-[9px] px-1 rounded-full">{{ $tag->total }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Tabela expandida de colaboradores --}}
            @if ($d['byUser']->isNotEmpty())
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4 mb-4">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-users class="w-4 h-4 text-indigo-500" />
                        Desempenho por colaborador
                    </h3>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm min-w-[640px]">
                            <thead>
                                <tr class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide border-b border-slate-100 dark:border-slate-700">
                                    <th class="text-left pb-3 pr-4">Colaborador</th>
                                    <th class="text-center pb-3 px-2">Total</th>
                                    <th class="text-center pb-3 px-2">Pendente</th>
                                    <th class="text-center pb-3 px-2">Andamento</th>
                                    <th class="text-center pb-3 px-2">Concluída</th>
                                    <th class="text-center pb-3 px-2">Vencidas</th>
                                    <th class="text-center pb-3 px-2">No prazo</th>
                                    <th class="text-center pb-3 px-2">Tempo médio</th>
                                    <th class="text-left pb-3 pl-2 w-28">Progresso</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700/60">
                                @foreach ($d['byUser'] as $idx => $u)
                                    @php
                                        $uParts   = explode(' ', trim($u->name));
                                        $uInit    = strtoupper(substr($uParts[0],0,1).(isset($uParts[1])?substr($uParts[1],0,1):''));
                                        $uColors  = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-400','bg-amber-500','bg-blue-400'];
                                        $uColor   = $uColors[abs(crc32($u->name)) % count($uColors)];
                                        $isMvp    = $d['mvp'] && $d['mvp']->id === $u->id;
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                        <td class="py-3 pr-4">
                                            <div class="flex items-center gap-2.5">
                                                <span class="w-7 h-7 rounded-full {{ $uColor }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0">
                                                    {{ $uInit }}
                                                </span>
                                                <div>
                                                    <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 leading-tight">
                                                        {{ $u->name }}
                                                        @if ($isMvp)
                                                            <x-lucide-trophy class="w-3 h-3 text-amber-400 inline ml-1" />
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="py-3 text-center lato-bold text-slate-600 dark:text-slate-300 text-xs px-2">{{ $u->total_tasks }}</td>
                                        <td class="py-3 text-center text-xs px-2">
                                            <span class="{{ $u->pending_tasks > 0 ? 'text-slate-500' : 'text-slate-300 dark:text-slate-600' }} lato-regular">
                                                {{ $u->pending_tasks ?: '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center text-xs px-2">
                                            <span class="{{ $u->in_progress > 0 ? 'text-blue-500 lato-bold' : 'text-slate-300 dark:text-slate-600' }}">
                                                {{ $u->in_progress ?: '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center text-xs px-2">
                                            <span class="{{ $u->done_tasks > 0 ? 'text-emerald-500 lato-bold' : 'text-slate-300 dark:text-slate-600' }}">
                                                {{ $u->done_tasks ?: '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center text-xs px-2">
                                            <span class="{{ $u->overdue_tasks > 0 ? 'text-red-500 lato-bold' : 'text-slate-300 dark:text-slate-600' }}">
                                                {{ $u->overdue_tasks ?: '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3 text-center text-xs px-2">
                                            @if ($u->taxa_prazo !== null)
                                                <span class="lato-bold {{ $u->taxa_prazo >= 80 ? 'text-emerald-500' : ($u->taxa_prazo >= 50 ? 'text-amber-500' : 'text-red-400') }}">
                                                    {{ $u->taxa_prazo }}%
                                                </span>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600">—</span>
                                            @endif
                                        </td>
                                        <td class="py-3 text-center text-xs px-2">
                                            <span class="{{ $u->avg_days ? 'text-slate-600 dark:text-slate-300 lato-regular' : 'text-slate-300 dark:text-slate-600' }}">
                                                {{ $u->avg_days ? $u->avg_days.'d' : '—' }}
                                            </span>
                                        </td>
                                        <td class="py-3 pl-2">
                                            <div class="flex items-center gap-1.5">
                                                <div class="flex-1 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                                                    <div class="h-1.5 rounded-full transition-all {{ $u->taxa_conclusao === 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                                         style="width: {{ $u->taxa_conclusao }}%"></div>
                                                </div>
                                                <span class="text-[10px] lato-bold text-slate-500 dark:text-slate-400 w-8 text-right">{{ $u->taxa_conclusao }}%</span>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            {{-- Heatmap 30 dias — departamento --}}
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-indigo-100 dark:border-indigo-900/30 p-4">
                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-2">
                    <x-lucide-calendar-days class="w-4 h-4 text-indigo-500" />
                    Concluídas no departamento — últimos 30 dias
                </h3>
                @php $maxHeatD = max($d['days30']->max('concluidas'), 1); @endphp
                <div class="flex flex-wrap gap-1">
                    @foreach ($d['days30'] as $hDay)
                        @php
                            $intensity = $hDay['concluidas'] > 0 ? min(100, round($hDay['concluidas'] / $maxHeatD * 100)) : 0;
                            $heatClass = match(true) {
                                $intensity === 0  => 'bg-slate-100 dark:bg-slate-700',
                                $intensity <= 25  => 'bg-indigo-200 dark:bg-indigo-900/50',
                                $intensity <= 50  => 'bg-indigo-300 dark:bg-indigo-700/70',
                                $intensity <= 75  => 'bg-indigo-400 dark:bg-indigo-600',
                                default           => 'bg-indigo-500 dark:bg-indigo-500',
                            };
                        @endphp
                        <div class="w-7 h-7 rounded-md {{ $heatClass }} flex items-center justify-center cursor-default transition-all"
                             title="{{ $hDay['label'] }}: {{ $hDay['concluidas'] }} concluída(s)">
                            @if ($hDay['concluidas'] > 0)
                                <span class="text-[9px] lato-bold text-white">{{ $hDay['concluidas'] }}</span>
                            @endif
                        </div>
                    @endforeach
                </div>
                <div class="flex items-center gap-2 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                    <span class="text-[10px] text-slate-400 lato-regular">Menos</span>
                    @foreach (['bg-slate-100 dark:bg-slate-700','bg-indigo-200','bg-indigo-300','bg-indigo-400','bg-indigo-500'] as $hc)
                        <span class="w-4 h-4 rounded {{ $hc }} inline-block"></span>
                    @endforeach
                    <span class="text-[10px] text-slate-400 lato-regular">Mais</span>
                </div>
            </div>
        @endif
    @endif

    {{-- ═══════════════════════════════════════════════════════════════ --}}
    {{-- COMPONENTES DE MODAL                                           --}}
    {{-- ═══════════════════════════════════════════════════════════════ --}}
    <livewire:pages.tasks.modals.task-form />
    <livewire:pages.tasks.modals.confirm-delete />
    <livewire:pages.tasks.modals.validation-modal />
    <livewire:pages.tasks.modals.comment-drawer />

    <livewire:components.ui.modal.alert-modal />

</div>
