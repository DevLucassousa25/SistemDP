        {{-- ═══════════════════════════════════════════════════════════
             ABA: DESLIGAMENTOS
        ═══════════════════════════════════════════════════════════ --}}
        @if ($aba === 'desligamentos')
        @php
            $ds = $this->demStats;
            $tipoLabels = \App\Models\RhDesligamento::$tipoLabels;
            $tipoCores  = \App\Models\RhDesligamento::$tipoCores;
            $respLabels = \App\Models\RhDesligamentoChecklist::$responsavelLabels;
            $respCores  = \App\Models\RhDesligamentoChecklist::$responsavelCores;
        @endphp
        <div class="p-3 md:p-6 space-y-4 md:space-y-5">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white">Desligamentos</h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">Gerencie processos de desligamento de colaboradores</p>
                </div>
                @if ($demSubAba === 'processos')
                <button wire:click="openDemModal" type="button"
                        class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 text-white text-sm lato-bold hover:from-rose-600 hover:to-red-700 transition shadow-sm shrink-0">
                    <x-lucide-user-minus class="w-4 h-4" /> Novo Desligamento
                </button>
                @endif
            </div>

            {{-- Sub-abas --}}
            <div class="flex gap-1 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 p-1 rounded-xl w-fit">
                <button wire:click="$set('demSubAba','processos')" type="button"
                        class="cursor-pointer flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm lato-bold transition
                        {{ $demSubAba === 'processos' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300' }}">
                    <x-lucide-list class="w-4 h-4" /> Processos
                </button>
                <button wire:click="$set('demSubAba','dashboard')" type="button"
                        class="cursor-pointer flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm lato-bold transition
                        {{ $demSubAba === 'dashboard' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300' }}">
                    <x-lucide-bar-chart-2 class="w-4 h-4" /> Dashboard de Turnover
                </button>
            </div>

            {{-- Stats cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <p class="text-xs text-slate-400 lato-regular mb-1">Em processo</p>
                    <p class="text-2xl lato-black text-rose-600 dark:text-rose-400">{{ $ds['emProcesso'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <p class="text-xs text-slate-400 lato-regular mb-1">Concluídos este mês</p>
                    <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $ds['esteMes'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <p class="text-xs text-slate-400 lato-regular mb-1">Turnover mensal</p>
                    <p class="text-2xl lato-black text-amber-600 dark:text-amber-400">{{ $ds['turnover'] }}%</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <p class="text-xs text-slate-400 lato-regular mb-1">Motivo mais frequente</p>
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">
                        {{ $ds['motivoTop'] ? (\App\Models\RhEntrevistaDesligamento::$motivosPrincipais[$ds['motivoTop']->motivo_principal] ?? $ds['motivoTop']->motivo_principal) : '—' }}
                    </p>
                </div>
            </div>

            @if ($demSubAba === 'processos')
            {{-- Filtros --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3">

                {{-- Busca --}}
                <div class="relative w-full">
                    <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    <input type="text" wire:model.live.debounce.300ms="demSearch"
                           placeholder="Buscar colaborador por nome..."
                           class="w-full pl-10 pr-9 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                  bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                  focus:outline-none focus:ring-2 focus:ring-rose-400/40 focus:border-rose-400
                                  placeholder-slate-400 transition" />
                    @if ($demSearch)
                    <button wire:click="$set('demSearch','')" class="cursor-pointer absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                    @endif
                </div>

                {{-- Dropdowns --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">

                    {{-- Status --}}
                    @php
                        $statusDemOpts = [
                            'em_processo' => ['Em Processo',  'bg-amber-400'],
                            'concluido'   => ['Concluído',    'bg-emerald-400'],
                            'cancelado'   => ['Cancelado',    'bg-slate-400'],
                        ];
                        $statusDemLbl = $demStatusFiltro ? ($statusDemOpts[$demStatusFiltro][0] ?? 'Status') : 'Status';
                        $statusDemDot = $demStatusFiltro ? ($statusDemOpts[$demStatusFiltro][1] ?? null) : null;
                    @endphp
                    <div wire:key="dropdown-5" x-data="{ isOpen: false }" class="relative">
                        <button @click="isOpen = !isOpen" type="button"
                                class="cursor-pointer w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 transition
                                {{ $demStatusFiltro ? 'border-rose-400 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate min-w-0">
                                @if ($statusDemDot)
                                    <span class="w-2 h-2 rounded-full {{ $statusDemDot }} shrink-0"></span>
                                @else
                                    <x-lucide-circle-dot class="w-3.5 h-3.5 shrink-0" />
                                @endif
                                <span class="truncate text-xs lato-bold">{{ $statusDemLbl }}</span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform ml-1" x-bind:class="isOpen ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="isOpen" @click.outside="isOpen = false" x-transition
                             class="absolute mt-1 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1">
                            <button @click="isOpen=false; $wire.set('demStatusFiltro','')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <x-lucide-circle-dot class="w-3.5 h-3.5" /> Todos os status
                            </button>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                            @foreach ($statusDemOpts as $val => [$lbl, $dot])
                            <button @click="isOpen=false; $wire.set('demStatusFiltro','{{ $val }}')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs flex items-center gap-2 transition
                                    {{ $demStatusFiltro === $val ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span> {{ $lbl }}
                                @if ($demStatusFiltro === $val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-rose-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tipo --}}
                    @php
                        $tipoDemLbl = $demTipoFiltro ? ($tipoLabels[$demTipoFiltro] ?? 'Tipo') : 'Tipo';
                    @endphp
                    <div wire:key="dropdown-6" x-data="{ isOpen: false }" class="relative">
                        <button @click="isOpen = !isOpen" type="button"
                                class="cursor-pointer w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 transition
                                {{ $demTipoFiltro ? 'border-rose-400 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate min-w-0">
                                <x-lucide-tag class="w-3.5 h-3.5 shrink-0" />
                                <span class="truncate text-xs lato-bold">{{ $tipoDemLbl }}</span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform ml-1" x-bind:class="isOpen ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="isOpen" @click.outside="isOpen = false" x-transition
                             class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1">
                            <button @click="isOpen=false; $wire.set('demTipoFiltro','')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <x-lucide-tag class="w-3.5 h-3.5" /> Todos os tipos
                            </button>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                            @foreach ($tipoLabels as $val => $lbl)
                            <button @click="isOpen=false; $wire.set('demTipoFiltro','{{ $val }}')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs flex items-center justify-between gap-2 transition
                                    {{ $demTipoFiltro === $val ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                {{ $lbl }}
                                @if ($demTipoFiltro === $val) <x-lucide-check class="w-3.5 h-3.5 text-rose-500 shrink-0" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Limpar filtros --}}
                    @if ($demSearch || $demStatusFiltro || $demTipoFiltro)
                    <button wire:click="$set('demSearch',''); $set('demStatusFiltro',''); $set('demTipoFiltro','')" type="button"
                            class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl border border-rose-200 dark:border-rose-700 text-xs lato-bold text-rose-500 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
                        <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar
                    </button>
                    @endif
                </div>
            </div>

            {{-- Lista --}}
            <div class="space-y-3">
                @forelse ($this->desligamentos as $des)
                @php
                    $prog = $des->checklist_progresso;
                @endphp
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 hover:border-rose-300 dark:hover:border-rose-600 transition cursor-pointer"
                     wire:click="openDemDrawer({{ $des->id }})">
                    <div class="flex items-start gap-4">
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-red-600 flex items-center justify-center text-white lato-bold text-sm shrink-0">
                            {{ strtoupper(substr($des->funcionario?->name ?? '?', 0, 1)) }}
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm lato-bold text-slate-800 dark:text-white">{{ $des->funcionario?->name ?? '—' }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $tipoCores[$des->tipo] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $tipoLabels[$des->tipo] ?? $des->tipo }}
                                </span>
                                @if ($des->status === 'concluido')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Concluído</span>
                                @elseif ($des->status === 'cancelado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">Cancelado</span>
                                @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Em processo</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $des->funcionario?->department?->name ?? '' }}
                                @if ($des->funcionario?->position) · {{ $des->funcionario->position }} @endif
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Último dia: <span class="lato-bold">{{ $des->data_ultimo_dia?->format('d/m/Y') ?? '—' }}</span>
                                @if ($des->data_aviso)
                                · Aviso: {{ $des->data_aviso->format('d/m/Y') }}
                                @endif
                            </p>
                            {{-- Checklist progress --}}
                            @if ($prog['total'] > 0)
                            <div class="mt-2">
                                <div class="flex items-center justify-between mb-0.5">
                                    <span class="text-xs text-slate-400">Checklist</span>
                                    <span class="text-xs text-slate-500">{{ $prog['concluidos'] }}/{{ $prog['total'] }}</span>
                                </div>
                                <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all
                                        {{ $prog['pct'] === 100 ? 'bg-emerald-500' : 'bg-rose-500' }}"
                                         style="width: {{ $prog['pct'] }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                        {{-- Arrow --}}
                        <x-lucide-chevron-right class="w-4 h-4 text-slate-400 shrink-0 mt-1" />
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <x-lucide-user-minus class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-3" />
                    <p class="text-sm text-slate-400 lato-regular">Nenhum desligamento encontrado</p>
                </div>
                @endforelse

                {{ $this->desligamentos->links() }}
            </div>
            @elseif ($demSubAba === 'dashboard')
            {{-- ═══ DASHBOARD DE TURNOVER ═══ --}}
            @php
                $td = $this->demTurnoverData;
                $maxMes = max(array_column($td['meses'], 'total') ?: [1]);
                $tipoLabelsD = \App\Models\RhDesligamento::$tipoLabels;
                $tipoCoresD  = \App\Models\RhDesligamento::$tipoCores;
                $totalPorTipo = array_sum($td['porTipo'] ?: [1]);
                $motivoLabels = \App\Models\RhEntrevistaDesligamento::$motivosPrincipais;
            @endphp

            <div class="space-y-4">
                {{-- KPIs --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <p class="text-xs text-slate-400 lato-regular mb-1">Total desligados</p>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $td['totalConc'] }}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <p class="text-xs text-slate-400 lato-regular mb-1">Em processo</p>
                        <p class="text-2xl lato-black text-amber-600 dark:text-amber-400">{{ $ds['emProcesso'] }}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <p class="text-xs text-slate-400 lato-regular mb-1">Recontratáveis</p>
                        <p class="text-2xl lato-black text-emerald-600 dark:text-emerald-400">{{ $td['recontratavel'] }}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <p class="text-xs text-slate-400 lato-regular mb-1">Não recontratáveis</p>
                        <p class="text-2xl lato-black text-rose-600 dark:text-rose-400">{{ $td['naoRecontr'] }}</p>
                    </div>
                </div>

                {{-- Gráfico: Desligamentos por mês (últimos 12 meses) --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                    <h3 class="text-sm lato-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                        <x-lucide-trending-up class="w-4 h-4 text-rose-500" />
                        Desligamentos por mês (últimos 12 meses)
                    </h3>
                    <div class="flex items-end gap-2 h-40">
                        @foreach ($td['meses'] as $m)
                        @php $pct = $maxMes > 0 ? ($m['total'] / $maxMes * 100) : 0; @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 min-w-0">
                            <span class="text-[10px] lato-bold text-slate-600 dark:text-slate-300">{{ $m['total'] > 0 ? $m['total'] : '' }}</span>
                            <div class="w-full rounded-t-md transition-all"
                                 style="height: {{ max($pct, $m['total'] > 0 ? 4 : 1) }}%; background: {{ $m['total'] > 0 ? 'linear-gradient(to top, #f43f5e, #fb7185)' : '#e2e8f0' }}; min-height: 4px;"></div>
                            <span class="text-[9px] text-slate-400 truncate w-full text-center">{{ $m['mes'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Por tipo --}}
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                        <h3 class="text-sm lato-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                            <x-lucide-pie-chart class="w-4 h-4 text-rose-500" />
                            Distribuição por tipo
                        </h3>
                        @if (empty($td['porTipo']))
                        <p class="text-xs text-slate-400 text-center py-8">Sem dados ainda</p>
                        @else
                        <div class="space-y-2.5">
                            @foreach ($td['porTipo'] as $tipo => $qtd)
                            @php $pctTipo = $totalPorTipo > 0 ? round($qtd / $totalPorTipo * 100) : 0; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $tipoLabelsD[$tipo] ?? $tipo }}</span>
                                    <span class="text-xs text-slate-400">{{ $qtd }} ({{ $pctTipo }}%)</span>
                                </div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full"
                                         style="width: {{ $pctTipo }}%; background: linear-gradient(to right, #f43f5e, #fb923c)"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Motivos mais citados --}}
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                        <h3 class="text-sm lato-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                            <x-lucide-message-square class="w-4 h-4 text-rose-500" />
                            Motivos mais citados nas entrevistas
                        </h3>
                        @if (empty($td['motivos']))
                        <p class="text-xs text-slate-400 text-center py-8">Nenhuma entrevista respondida ainda</p>
                        @else
                        @php $maxMotivo = max(array_column($td['motivos'], 'total') ?: [1]); @endphp
                        <div class="space-y-2.5">
                            @foreach ($td['motivos'] as $m)
                            @php $pctM = $maxMotivo > 0 ? round($m['total'] / $maxMotivo * 100) : 0; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate max-w-[70%]">{{ $m['motivo'] }}</span>
                                    <span class="text-xs text-slate-400">{{ $m['total'] }}</span>
                                </div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full bg-rose-400"
                                         style="width: {{ $pctM }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif {{-- fim sub-abas processos/dashboard --}}
        </div>
        @endif


