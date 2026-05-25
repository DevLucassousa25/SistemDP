@php
    $moods = \App\Models\MoodCheckin::MOODS;

    // Ícones Lucide por humor (substitui emojis nos badges)
    $moodIcon = [
        'otimo'   => 'smile',
        'bem'     => 'thumbs-up',
        'normal'  => 'meh',
        'pessimo' => 'frown',
    ];
@endphp

@once
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
@endonce

<div class="p-4 sm:p-6 space-y-6" x-data="{ tab: 'hoje' }">

    {{-- ══ CABEÇALHO ══════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2.5">
                <span class="w-8 h-8 rounded-xl bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center shrink-0">
                    <x-lucide-heart-pulse class="w-5 h-5 text-rose-500 dark:text-rose-400" />
                </span>
                Humor das Equipes
            </h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 ml-10.5">
                Acompanhamento do bem-estar dos colaboradores
            </p>
        </div>

        {{-- Abas + Exportar --}}
        <div class="flex flex-wrap items-center gap-2">

            {{-- Abas --}}
            <div class="flex bg-slate-100 dark:bg-slate-800 rounded-xl p-1 gap-1">
                <button
                    type="button"
                    x-on:click="tab = 'hoje'"
                    :class="tab === 'hoje'
                        ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition cursor-pointer"
                >
                    <x-lucide-calendar-days class="w-4 h-4" />
                    Hoje
                </button>
                <button
                    type="button"
                    x-on:click="tab = 'mensal'"
                    :class="tab === 'mensal'
                        ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm'
                        : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                    class="inline-flex items-center gap-2 px-4 py-2 rounded-lg text-sm font-semibold transition cursor-pointer"
                >
                    <x-lucide-bar-chart-3 class="w-4 h-4" />
                    Análise Mensal
                </button>
            </div>

            {{-- Botões de exportação --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open" @click.away="open = false" type="button"
                        class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-teal-600 hover:bg-teal-700 text-white text-sm font-semibold transition cursor-pointer shadow-sm">
                    <x-lucide-download class="w-4 h-4" />
                    Exportar
                    <x-lucide-chevron-down class="w-3.5 h-3.5 opacity-70" />
                </button>

                <div x-show="open" x-cloak x-transition
                     class="absolute right-0 top-full mt-2 w-64 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl z-50 overflow-hidden">

                    <div class="px-4 pt-3 pb-1">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Relatório do Dia</p>
                    </div>
                    <a href="{{ route('rh.humor-equipes.export.pdf.hoje') }}"
                       target="_blank"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        <span class="w-8 h-8 rounded-lg bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center">
                            <x-lucide-file-text class="w-4 h-4 text-rose-600 dark:text-rose-400" />
                        </span>
                        <div>
                            <p class="font-semibold">PDF — Hoje</p>
                            <p class="text-xs text-slate-400">{{ today()->format('d/m/Y') }}</p>
                        </div>
                    </a>

                    <div class="border-t border-slate-100 dark:border-slate-700 mx-4"></div>
                    <div class="px-4 pt-3 pb-1">
                        <p class="text-xs font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Análise Mensal</p>
                    </div>
                    <a href="{{ route('rh.humor-equipes.export.pdf.mensal', ['mes' => $mes]) }}"
                       target="_blank"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        <span class="w-8 h-8 rounded-lg bg-teal-50 dark:bg-teal-900/30 flex items-center justify-center">
                            <x-lucide-file-bar-chart class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                        </span>
                        <div>
                            <p class="font-semibold">PDF — Mensal</p>
                            <p class="text-xs text-slate-400">{{ $this->periodoMensal['label'] }}</p>
                        </div>
                    </a>
                    <a href="{{ route('rh.humor-equipes.export.excel', ['mes' => $mes]) }}"
                       target="_blank"
                       class="flex items-center gap-3 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        <span class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <x-lucide-table class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        </span>
                        <div>
                            <p class="font-semibold">Excel — Mensal</p>
                            <p class="text-xs text-slate-400">{{ $this->periodoMensal['label'] }}</p>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ══ FILTROS ═════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl p-4 flex flex-col gap-3">

        {{-- Busca por colaborador (só na aba Hoje) --}}
        <div class="relative w-full" x-show="tab === 'hoje'" wire:ignore.self>
            <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
            <input wire:model.live.debounce.300ms="filtroBusca"
                   type="text" placeholder="Buscar colaborador..."
                   class="w-full pl-10 pr-9 py-2.5 text-sm border border-gray-200 dark:border-slate-600
                          rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                          placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500
                          focus:border-transparent lato-regular transition" />
            @if($filtroBusca)
            <button wire:click="$set('filtroBusca', '')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer">
                <x-lucide-x class="w-3.5 h-3.5" />
            </button>
            @endif
        </div>

        {{-- Navegação de mês (só na aba Mensal) --}}
        <div class="flex items-center gap-2 w-full" x-show="tab === 'mensal'" style="display:none" wire:ignore.self>
            <button type="button"
                    wire:click="mesAnterior"
                    wire:loading.attr="disabled"
                    wire:target="mesAnterior"
                    class="p-2.5 rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700
                           text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 transition cursor-pointer
                           disabled:opacity-40 disabled:cursor-not-allowed">
                <x-lucide-chevron-left class="w-4 h-4" />
            </button>
            <span class="flex-1 text-center text-sm lato-bold text-slate-700 dark:text-slate-200">
                {{ $this->periodoMensal['label'] }}
            </span>
            <button type="button"
                    wire:click="mesSeguinte"
                    @if(!$this->periodoMensal['pode_avancar']) disabled @endif
                    wire:loading.attr="disabled"
                    wire:target="mesSeguinte"
                    class="p-2.5 rounded-xl border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700
                           text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-600 transition cursor-pointer
                           disabled:opacity-40 disabled:cursor-not-allowed">
                <x-lucide-chevron-right class="w-4 h-4" />
            </button>
        </div>

        {{-- Filtros dropdown --}}
        <div class="grid grid-cols-2 gap-2">

            {{-- Departamento --}}
            <div class="relative" x-data="{ deptOpen: false, deptSearch: '' }" x-on:click.outside="deptOpen = false">
                <button type="button" x-on:click="deptOpen = !deptOpen"
                        class="w-full flex items-center justify-between text-sm border border-gray-200 dark:border-slate-600 rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 text-gray-500 dark:text-slate-300 transition cursor-pointer">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <x-lucide-building-2 class="w-3.5 h-3.5 flex-shrink-0" />
                        <span class="truncate text-xs lato-bold">
                            @if($filtroDepartamento)
                                {{ $this->departamentos->firstWhere('id', $filtroDepartamento)?->name ?? 'Departamento' }}
                            @else
                                Departamento
                            @endif
                        </span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 ml-1 transition-transform duration-200"
                                           x-bind:class="deptOpen ? 'rotate-180' : ''" />
                </button>

                <div x-show="deptOpen"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     style="display:none"
                     class="absolute mt-1 w-full min-w-52 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg z-30 overflow-hidden">

                    {{-- Search --}}
                    <div class="p-2 border-b border-gray-100 dark:border-slate-700">
                        <div class="relative">
                            <x-lucide-search class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                            <input type="text" x-model="deptSearch" placeholder="Buscar..."
                                   x-on:click.stop
                                   class="w-full pl-8 pr-3 py-1.5 text-xs border border-gray-200 dark:border-slate-600
                                          rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                          placeholder-gray-400 focus:outline-none focus:ring-1 focus:ring-blue-400
                                          lato-regular transition" />
                        </div>
                    </div>

                    <div class="py-1 overflow-y-auto max-h-52">
                        <button type="button"
                                x-on:click="deptOpen = false; deptSearch = ''"
                                wire:click="$set('filtroDepartamento', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 flex items-center gap-2 lato-regular">
                            <x-lucide-building-2 class="w-3.5 h-3.5" /> Todos os departamentos
                        </button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>

                        @foreach($this->departamentos as $dept)
                        <button type="button"
                                x-show="!deptSearch || '{{ addslashes(strtolower($dept->name)) }}'.includes(deptSearch.toLowerCase())"
                                x-on:click="deptOpen = false; deptSearch = ''"
                                wire:click="$set('filtroDepartamento', {{ $dept->id }})"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular
                                       {{ $filtroDepartamento == $dept->id
                                            ? 'bg-blue-50 text-blue-700 font-semibold dark:bg-blue-900/30 dark:text-blue-300'
                                            : 'text-gray-700 hover:bg-gray-50 dark:text-slate-200 dark:hover:bg-slate-700' }}">
                            {{ $dept->name }}
                            @if($filtroDepartamento == $dept->id)
                                <x-lucide-check class="w-3.5 h-3.5 text-blue-500" />
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- Humor --}}
            <div class="relative" x-data="{ humorOpen: false }" x-on:click.outside="humorOpen = false">
                <button type="button" x-on:click="humorOpen = !humorOpen"
                        class="w-full flex items-center justify-between text-sm border border-gray-200 dark:border-slate-600 rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 text-gray-500 dark:text-slate-300 transition cursor-pointer">
                    <div class="flex items-center gap-1.5 min-w-0">
                        <x-lucide-smile class="w-3.5 h-3.5 flex-shrink-0" />
                        <span class="truncate text-xs lato-bold">
                            @if($filtroHumor && isset($moods[$filtroHumor]))
                                {{ $moods[$filtroHumor]['label'] }}
                            @else
                                Humor
                            @endif
                        </span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 ml-1 transition-transform duration-200"
                                           x-bind:class="humorOpen ? 'rotate-180' : ''" />
                </button>

                <div x-show="humorOpen"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     style="display:none"
                     class="absolute mt-1 w-full min-w-44 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1">

                    <button type="button"
                            x-on:click="humorOpen = false"
                            wire:click="$set('filtroHumor', '')"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 flex items-center gap-2 lato-regular">
                        <x-lucide-smile class="w-3.5 h-3.5" /> Todos os humores
                    </button>
                    <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>

                    @foreach($moods as $key => $mood)
                    <button type="button"
                            x-on:click="humorOpen = false"
                            wire:click="$set('filtroHumor', '{{ $key }}')"
                            class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular
                                   {{ $filtroHumor === $key
                                        ? 'bg-rose-50 text-rose-700 font-semibold dark:bg-rose-900/30 dark:text-rose-300'
                                        : 'text-gray-700 hover:bg-gray-50 dark:text-slate-200 dark:hover:bg-slate-700' }}">
                        <span>{{ $mood['label'] }}</span>
                        @if($filtroHumor === $key)
                            <x-lucide-check class="w-3.5 h-3.5 text-rose-500" />
                        @endif
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Limpar --}}
            @if($filtroDepartamento || $filtroHumor || $filtroBusca)
            <div class="col-span-2 flex justify-end">
                <button wire:click="limparFiltros" type="button"
                        class="flex items-center gap-1.5 px-3 py-2 text-sm text-slate-500 dark:text-slate-400
                               hover:text-rose-500 dark:hover:text-rose-400 transition cursor-pointer rounded-xl
                               hover:bg-rose-50 dark:hover:bg-rose-900/20 lato-regular">
                    <x-lucide-x class="w-4 h-4" /> Limpar filtros
                </button>
            </div>
            @endif

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ABA: HOJE                                                      --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'hoje'" wire:ignore.self>

    @php $stats = $this->statsHoje; @endphp

    {{-- Cards de stats --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">

        {{-- Participação --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <x-lucide-users class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Participação</span>
            </div>
            <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $stats['participacao'] }}%</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">{{ $stats['checkins'] }} de {{ $stats['total_funcionarios'] }} responderam</p>
        </div>

        {{-- Positivos --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <x-lucide-smile class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Positivos</span>
            </div>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">
                {{ ($stats['distribuicao']['otimo'] ?? 0) + ($stats['distribuicao']['bem'] ?? 0) }}
            </p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Ótimo + Bem</p>
        </div>

        {{-- Neutros --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                    <x-lucide-meh class="w-4.5 h-4.5 text-amber-600 dark:text-amber-400" />
                </div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Neutros</span>
            </div>
            <p class="text-3xl font-bold text-amber-600 dark:text-amber-400">
                {{ $stats['distribuicao']['normal'] ?? 0 }}
            </p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Normal</p>
        </div>

        {{-- Péssimos --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border {{ $stats['pessimos'] > 0 ? 'border-rose-300 dark:border-rose-700 bg-rose-50 dark:bg-rose-900/20' : 'border-slate-200 dark:border-slate-700' }} p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center">
                    <x-lucide-frown class="w-4.5 h-4.5 text-rose-600 dark:text-rose-400" />
                </div>
                <span class="text-xs font-semibold text-rose-500 dark:text-rose-400 uppercase tracking-wide">Péssimos</span>
            </div>
            <p class="text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $stats['pessimos'] }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Requer atenção</p>
        </div>
    </div>

    {{-- ── ALERTAS: PÉSSIMOS HOJE ──────────────────────────────────── --}}
    @if($this->pessimosHoje->isNotEmpty())
    <div class="bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800 rounded-2xl overflow-hidden my-5">
        <div class="flex items-center gap-3 px-5 py-4 border-b border-rose-200 dark:border-rose-800">
            <div class="w-8 h-8 rounded-lg bg-rose-500 flex items-center justify-center shrink-0">
                <x-lucide-triangle-alert class="w-4 h-4 text-white" />
            </div>
            <div>
                <p class="text-sm font-bold text-rose-700 dark:text-rose-300">
                    {{ $this->pessimosHoje->count() }} colaborador(es) com humor péssimo hoje
                </p>
                <p class="text-xs text-rose-500 dark:text-rose-400">Entre em contato com o gestor do setor</p>
            </div>
        </div>

        <div class="divide-y divide-rose-100 dark:divide-rose-900/40">
            @foreach($this->pessimosHoje as $alerta)
            <div class="px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
                {{-- Colaborador --}}
                <div class="flex items-center gap-3 flex-1 min-w-0">
                    <div class="w-9 h-9 rounded-full bg-rose-200 dark:bg-rose-800 flex items-center justify-center shrink-0">
                        <span class="text-sm font-bold text-rose-700 dark:text-rose-200">
                            {{ strtoupper(substr($alerta['user_nome'], 0, 1)) }}
                        </span>
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $alerta['user_nome'] }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 flex items-center gap-1.5">
                            <x-lucide-building-2 class="w-3 h-3 shrink-0" />
                            {{ $alerta['departamento'] }}
                            <span class="text-slate-300 dark:text-slate-600">·</span>
                            <x-lucide-clock class="w-3 h-3 shrink-0" />
                            {{ $alerta['horario'] }}
                        </p>
                    </div>
                </div>

                {{-- Nota --}}
                @if($alerta['nota'])
                <div class="flex-1 min-w-0 max-w-sm">
                    <p class="text-xs text-rose-700 dark:text-rose-300 bg-rose-100 dark:bg-rose-900/40 rounded-lg px-3 py-2 italic line-clamp-2 flex items-start gap-1.5">
                        <x-lucide-message-square class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                        "{{ $alerta['nota'] }}"
                    </p>
                </div>
                @endif

                {{-- Contato com gestor --}}
                <div class="shrink-0">
                    @if($alerta['gerente_email'])
                    <a href="mailto:{{ $alerta['gerente_email'] }}?subject=Atenção: colaborador {{ $alerta['user_nome'] }} com humor péssimo hoje"
                       class="inline-flex items-center gap-2 px-4 py-2 bg-rose-600 hover:bg-rose-700 text-white text-xs font-semibold rounded-xl transition">
                        <x-lucide-mail class="w-3.5 h-3.5" />
                        Contatar {{ $alerta['gerente_nome'] ?? 'Gestor' }}
                    </a>
                    @else
                    <span class="text-xs text-slate-400 dark:text-slate-500 italic flex items-center gap-1.5">
                        <x-lucide-user-x class="w-3.5 h-3.5" />
                        Sem gestor cadastrado
                    </span>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ── TABELA POR DEPARTAMENTO ─────────────────────────────────── --}}
    @if(!empty($this->porDepartamentoHoje))
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
            <x-lucide-building-2 class="w-4 h-4 text-slate-400" />
            <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200">Resumo por Departamento</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-700/50 text-xs uppercase tracking-wide text-slate-500 dark:text-slate-400">
                        <th class="text-left px-5 py-3 font-semibold">Departamento</th>
                        <th class="text-center px-3 py-3 font-semibold">
                            <span class="inline-flex items-center gap-1 justify-center">
                                <x-lucide-smile class="w-3.5 h-3.5 text-emerald-500" /> Ótimo
                            </span>
                        </th>
                        <th class="text-center px-3 py-3 font-semibold">
                            <span class="inline-flex items-center gap-1 justify-center">
                                <x-lucide-thumbs-up class="w-3.5 h-3.5 text-blue-500" /> Bem
                            </span>
                        </th>
                        <th class="text-center px-3 py-3 font-semibold">
                            <span class="inline-flex items-center gap-1 justify-center">
                                <x-lucide-meh class="w-3.5 h-3.5 text-amber-500" /> Normal
                            </span>
                        </th>
                        <th class="text-center px-3 py-3 font-semibold">
                            <span class="inline-flex items-center gap-1 justify-center">
                                <x-lucide-frown class="w-3.5 h-3.5 text-rose-500" /> Péssimo
                            </span>
                        </th>
                        <th class="text-center px-3 py-3 font-semibold">
                            <span class="inline-flex items-center gap-1 justify-center">
                                <x-lucide-activity class="w-3.5 h-3.5 text-violet-500" /> Score
                            </span>
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($this->porDepartamentoHoje as $row)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition {{ $row['pessimo'] > 0 ? 'bg-rose-50/40 dark:bg-rose-900/10' : '' }}">
                        <td class="px-5 py-3.5 font-semibold text-slate-700 dark:text-slate-200">
                            {{ $row['departamento'] }}
                            <span class="ml-1.5 text-xs text-slate-400 font-normal">({{ $row['total'] }})</span>
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            @if($row['otimo'] > 0)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 text-xs font-bold">{{ $row['otimo'] }}</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            @if($row['bem'] > 0)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 text-xs font-bold">{{ $row['bem'] }}</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            @if($row['normal'] > 0)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 text-xs font-bold">{{ $row['normal'] }}</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            @if($row['pessimo'] > 0)
                                <span class="inline-flex items-center justify-center w-7 h-7 rounded-full bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-400 text-xs font-bold">{{ $row['pessimo'] }}</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-3 py-3.5 text-center">
                            @if($row['score'] !== null)
                                @php
                                    $cor = $row['score'] >= 70 ? 'text-emerald-600 dark:text-emerald-400'
                                         : ($row['score'] >= 40 ? 'text-amber-600 dark:text-amber-400'
                                         : 'text-rose-600 dark:text-rose-400');
                                @endphp
                                <span class="text-sm font-bold {{ $cor }}">{{ $row['score'] }}</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── LISTA DE CHECK-INS DO DIA ──────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden my-6">
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
            <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                <x-lucide-list class="w-4 h-4 text-slate-400" />
                Check-ins de Hoje
            </h2>
            <span class="text-xs text-slate-400 dark:text-slate-500">{{ $this->checkinsHoje->count() }} registro(s)</span>
        </div>

        @if($this->checkinsHoje->isEmpty())
            <div class="flex flex-col items-center justify-center py-12 text-center">
                <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                    <x-lucide-search class="w-6 h-6 text-slate-400" />
                </div>
                <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Nenhum check-in encontrado</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Tente ajustar os filtros</p>
            </div>
        @else
            <div class="divide-y divide-slate-100 dark:divide-slate-700">
                @foreach($this->checkinsHoje as $checkin)
                @php $m = $moods[$checkin->mood] ?? null; @endphp
                <div class="flex items-center gap-4 px-5 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-700/20 transition">
                    {{-- Avatar --}}
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-300 to-slate-400 dark:from-slate-600 dark:to-slate-700 flex items-center justify-center shrink-0">
                        <span class="text-xs font-bold text-white">{{ strtoupper(substr($checkin->user->name, 0, 1)) }}</span>
                    </div>

                    {{-- Nome + Departamento --}}
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $checkin->user->name }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                            <x-lucide-building-2 class="w-3 h-3 shrink-0" />
                            {{ $checkin->user->department?->name ?? 'Sem departamento' }}
                        </p>
                    </div>

                    {{-- Nota --}}
                    @if($checkin->note)
                    <div class="hidden sm:flex flex-1 min-w-0 max-w-xs items-start gap-1.5">
                        <x-lucide-message-square class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0 mt-0.5" />
                        <p class="text-xs text-slate-500 dark:text-slate-400 italic truncate">"{{ $checkin->note }}"</p>
                    </div>
                    @endif

                    {{-- Humor badge com ícone Lucide --}}
                    @if($m)
                    <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold {{ $m['bg'] }} {{ $m['text'] }} {{ $m['border'] }} border">
                        <x-dynamic-component :component="'lucide-' . ($moodIcon[$checkin->mood] ?? 'smile')" class="w-3.5 h-3.5" />
                        {{ $m['label'] }}
                    </span>
                    @endif

                    {{-- Horário --}}
                    <span class="shrink-0 text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                        <x-lucide-clock class="w-3 h-3" />
                        {{ $checkin->created_at->format('H:i') }}
                    </span>
                </div>
                @endforeach
            </div>
        @endif
    </div>

    </div>{{-- /aba hoje --}}

    {{-- ══════════════════════════════════════════════════════════════ --}}
    {{-- ABA: MENSAL                                                    --}}
    {{-- ══════════════════════════════════════════════════════════════ --}}
    <div x-show="tab === 'mensal'" style="display:none" wire:ignore.self>

    @php $stats = $this->statsMensal; @endphp

    {{-- Stats mensais --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center">
                    <x-lucide-clipboard-list class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" />
                </div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Total Check-ins</span>
            </div>
            <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $stats['total'] }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">No mês</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                    <x-lucide-heart-pulse class="w-4.5 h-4.5 text-violet-600 dark:text-violet-400" />
                </div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Score Médio</span>
            </div>
            <p class="text-3xl font-bold {{ $stats['score'] !== null ? ($stats['score'] >= 70 ? 'text-emerald-600 dark:text-emerald-400' : ($stats['score'] >= 40 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400')) : 'text-slate-300' }}">
                {{ $stats['score'] !== null ? $stats['score'] : '—' }}
            </p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Bem-estar 0–100</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                    <x-lucide-smile class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                </div>
                <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Positivos</span>
            </div>
            <p class="text-3xl font-bold text-emerald-600 dark:text-emerald-400">
                {{ $stats['distribuicao']['otimo'] + $stats['distribuicao']['bem'] }}
            </p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Ótimo + Bem</p>
        </div>

        <div class="bg-white dark:bg-slate-800 rounded-2xl border {{ $stats['pessimos'] > 0 ? 'border-rose-300 dark:border-rose-700' : 'border-slate-200 dark:border-slate-700' }} p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-rose-50 dark:bg-rose-900/30 flex items-center justify-center">
                    <x-lucide-frown class="w-4.5 h-4.5 text-rose-600 dark:text-rose-400" />
                </div>
                <span class="text-xs font-semibold text-rose-500 dark:text-rose-400 uppercase tracking-wide">Péssimos</span>
            </div>
            <p class="text-3xl font-bold text-rose-600 dark:text-rose-400">{{ $stats['pessimos'] }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">No mês</p>
        </div>
    </div>

    {{-- ── GRÁFICO DE TENDÊNCIA ────────────────────────────────────── --}}
    @php $tendencia = $this->tendenciaMensal; @endphp
    @if(!empty($tendencia['labels']))
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 my-4">
        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
            <x-lucide-trending-up class="w-4 h-4 text-slate-400" />
            Tendência Diária
        </h2>

        {{-- wire:key força recriação do componente Alpine quando mês ou filtro muda --}}
        <div wire:key="chart-tendencia-{{ $mes }}-{{ $filtroDepartamento }}"
             x-data="{
                 chart: null,
                 init() {
                     this.$nextTick(() => {
                         const el = this.$refs.chartEl;
                         if (!el || typeof ApexCharts === 'undefined') return;
                         const isDark = document.documentElement.classList.contains('dark');
                         this.chart = new ApexCharts(el, {
                             chart: {
                                 type: 'bar', stacked: true, height: 260,
                                 toolbar: { show: false }, background: 'transparent',
                                 fontFamily: 'Inter, sans-serif',
                             },
                             series: @js(array_map(fn($s) => ['name' => $s['name'], 'data' => $s['data']], $tendencia['series'])),
                             colors: @js(array_column($tendencia['series'], 'color')),
                             xaxis: {
                                 categories: @js($tendencia['labels']),
                                 labels: { style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' } },
                                 axisBorder: { show: false },
                                 axisTicks: { show: false },
                             },
                             yaxis: {
                                 labels: { style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' } },
                             },
                             grid: { borderColor: isDark ? '#334155' : '#f1f5f9', strokeDashArray: 4 },
                             legend: { position: 'top', labels: { colors: isDark ? '#94a3b8' : '#64748b' } },
                             plotOptions: { bar: { borderRadius: 3, columnWidth: '60%' } },
                             dataLabels: { enabled: false },
                             tooltip: { theme: isDark ? 'dark' : 'light' },
                         });
                         this.chart.render();
                     });
                 },
                 destroy() {
                     if (this.chart) { this.chart.destroy(); this.chart = null; }
                 },
             }">
            <div x-ref="chartEl" style="min-height:260px;"></div>
        </div>
    </div>
    @endif

    {{-- ── DISTRIBUIÇÃO --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

        {{-- Ranking departamentos --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <x-lucide-trophy class="w-4 h-4 text-amber-500" />
                <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200">Ranking de Departamentos</h2>
            </div>
            @if(empty($this->rankingDepartamentosMensal))
                <div class="py-10 text-center">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-2">
                        <x-lucide-inbox class="w-5 h-5 text-slate-400" />
                    </div>
                    <p class="text-sm text-slate-400">Sem dados</p>
                </div>
            @else
            <div class="p-5 space-y-3">
                @foreach($this->rankingDepartamentosMensal as $i => $row)
                <div class="flex items-center gap-3">
                    <div class="w-6 flex items-center justify-center shrink-0">
                        @if($i === 0)
                            <x-lucide-trophy class="w-4 h-4 text-amber-400" />
                        @else
                            <span class="text-xs font-bold text-slate-400">{{ $i + 1 }}</span>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $row['departamento'] }}</p>
                            <span class="text-xs font-bold {{ $row['score'] >= 70 ? 'text-emerald-600 dark:text-emerald-400' : ($row['score'] >= 40 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400') }} ml-2 shrink-0">
                                {{ $row['score'] }}
                            </span>
                        </div>
                        <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                            <div class="h-1.5 rounded-full {{ $row['score'] >= 70 ? 'bg-emerald-500' : ($row['score'] >= 40 ? 'bg-amber-500' : 'bg-rose-500') }} transition-all"
                                 style="width: {{ $row['score'] }}%"></div>
                        </div>
                    </div>
                    @if($row['pessimos'] > 0)
                    <span class="shrink-0 inline-flex items-center gap-1 text-xs font-bold text-rose-500 bg-rose-50 dark:bg-rose-900/30 px-2 py-0.5 rounded-full">
                        <x-lucide-frown class="w-3 h-3" /> {{ $row['pessimos'] }}
                    </span>
                    @endif
                </div>
                @endforeach
            </div>
            @endif
        </div>

        {{-- Péssimos do mês --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <x-lucide-triangle-alert class="w-4 h-4 text-rose-500" />
                <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200">Registros Péssimo no Mês</h2>
                <span class="ml-auto text-xs font-bold text-rose-500 bg-rose-50 dark:bg-rose-900/30 px-2 py-0.5 rounded-full">
                    {{ $this->pessimosNoMes->count() }}
                </span>
            </div>
            @if($this->pessimosNoMes->isEmpty())
                <div class="py-10 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-3">
                        <x-lucide-check-circle class="w-6 h-6 text-emerald-500" />
                    </div>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Nenhum registro péssimo no período!</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Continue assim</p>
                </div>
            @else
                <div class="overflow-y-auto max-h-72 divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach($this->pessimosNoMes as $p)
                    <div class="flex items-center gap-3 px-5 py-3">
                        <div class="w-8 h-8 rounded-full bg-rose-100 dark:bg-rose-900/40 flex items-center justify-center shrink-0">
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400">{{ strtoupper(substr($p->user->name, 0, 1)) }}</span>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ $p->user->name }}</p>
                            <p class="text-xs text-slate-400 flex items-center gap-1">
                                <x-lucide-building-2 class="w-3 h-3 shrink-0" />
                                {{ $p->user->department?->name ?? 'Sem dept.' }}
                            </p>
                        </div>
                        <span class="shrink-0 text-xs text-slate-400 dark:text-slate-500 flex items-center gap-1">
                            <x-lucide-calendar class="w-3 h-3" />
                            {{ $p->checkin_date->format('d/m') }}
                        </span>
                    </div>
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    </div>{{-- /aba mensal --}}

</div>
