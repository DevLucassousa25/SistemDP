{{-- ═══════════════════════════════════════════════════════════════════
     ABA: PLANO DE SUCESSÃO
═══════════════════════════════════════════════════════════════════ --}}
@if($aba === 'sucessao')
@php
    $stats     = $this->sucStats;
    $positions = $this->successionPositions;

    $riskConfig = [
        'critico' => ['label'=>'Crítico','color'=>'rose',  'bg'=>'bg-rose-500',  'light'=>'bg-rose-50 dark:bg-rose-900/20',  'text'=>'text-rose-700 dark:text-rose-300',  'ring'=>'ring-rose-200 dark:ring-rose-700/40'],
        'alto'    => ['label'=>'Alto',   'color'=>'amber', 'bg'=>'bg-amber-500', 'light'=>'bg-amber-50 dark:bg-amber-900/20', 'text'=>'text-amber-700 dark:text-amber-300', 'ring'=>'ring-amber-200 dark:ring-amber-700/40'],
        'medio'   => ['label'=>'Médio',  'color'=>'blue',  'bg'=>'bg-blue-500',  'light'=>'bg-blue-50 dark:bg-blue-900/20',   'text'=>'text-blue-700 dark:text-blue-300',   'ring'=>'ring-blue-200 dark:ring-blue-700/40'],
    ];

    $readinessConfig = [
        'pronto_agora' => ['label'=>'Pronto agora','color'=>'emerald','bg'=>'bg-emerald-500'],
        '6_a_12_meses' => ['label'=>'6–12 meses',  'color'=>'amber',  'bg'=>'bg-amber-500'],
        '1_a_3_anos'   => ['label'=>'1–3 anos',    'color'=>'blue',   'bg'=>'bg-blue-500'],
    ];

    $btnPrimary = 'cursor-pointer inline-flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm font-semibold px-4 py-2.5 rounded-xl shadow-sm shadow-indigo-200 dark:shadow-indigo-900/30 transition lato-bold';
@endphp

<div class="space-y-6 p-6">

    {{-- ── Header ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm shadow-indigo-200 dark:shadow-indigo-900/30 shrink-0">
                <x-lucide-git-branch class="w-5 h-5 text-white" />
            </div>
            <div>
                <h2 class="text-xl font-bold text-slate-800 dark:text-white lato-black leading-tight">
                    Plano de Sucessão
                </h2>
                <p class="text-sm text-slate-400 lato-regular mt-0.5">
                    Mapeamento de posições críticas e potenciais sucessores — vinculado ao PDI e à avaliação de desempenho.
                </p>
            </div>
        </div>
        <button wire:click="sucAbrirModalPos('criar')" class="{{ $btnPrimary }} shrink-0">
            <x-lucide-plus class="w-4 h-4" />
            Nova Posição Crítica
        </button>
    </div>

    {{-- ── KPIs ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3">
        @foreach([
            ['label'=>'Posições mapeadas', 'value'=>$stats['total'],       'icon'=>'map',           'from'=>'from-blue-400',    'to'=>'to-indigo-500'],
            ['label'=>'Risco crítico',     'value'=>$stats['criticas'],    'icon'=>'alert-octagon', 'from'=>'from-rose-400',    'to'=>'to-rose-600'],
            ['label'=>'Com cobertura',     'value'=>$stats['cobertas'],    'icon'=>'shield-check',  'from'=>'from-emerald-400', 'to'=>'to-teal-500'],
            ['label'=>'Descobertas',       'value'=>$stats['descobertas'], 'icon'=>'shield-alert',  'from'=>'from-amber-400',   'to'=>'to-orange-500'],
            ['label'=>'Prontos agora',     'value'=>$stats['prontos'],     'icon'=>'user-check',    'from'=>'from-violet-400',  'to'=>'to-purple-600'],
        ] as $kpi)
        <div class="bg-white dark:bg-slate-800 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 p-4 flex items-center gap-3 hover:shadow-sm transition">
            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $kpi['from'] }} {{ $kpi['to'] }} flex items-center justify-center shadow-sm shrink-0">
                <x-dynamic-component :component="'lucide-' . $kpi['icon']" class="w-4 h-4 text-white" />
            </div>
            <div>
                <p class="text-2xl font-black lato-black text-slate-800 dark:text-white leading-none">{{ $kpi['value'] }}</p>
                <p class="text-[11px] text-slate-400 lato-regular mt-0.5 leading-tight">{{ $kpi['label'] }}</p>
            </div>
        </div>
        @endforeach
    </div>

    {{-- ── Filtros ──────────────────────────────────────────────────── --}}
    <div class="flex flex-wrap items-center gap-2.5 bg-white dark:bg-slate-800 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 px-4 py-3">
        <div class="relative flex-1 min-w-48">
            <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
            <input wire:model.live.debounce.300ms="sucBusca" type="text" placeholder="Buscar posição…"
                class="w-full pl-8 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                       bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-200 placeholder-slate-400
                       focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400 lato-regular transition" />
        </div>

        {{-- Filtro: Risco --}}
        <div x-data="{
            open: false,
            val: $wire.entangle('sucFiltroRisco'),
            opts: [
                { value: '', label: 'Todos os riscos', dot: 'bg-slate-300', text: 'text-slate-500' },
                { value: 'critico', label: 'Crítico', dot: 'bg-rose-500',  text: 'text-rose-600' },
                { value: 'alto',    label: 'Alto',    dot: 'bg-amber-400', text: 'text-amber-600' },
                { value: 'medio',   label: 'Médio',   dot: 'bg-blue-400',  text: 'text-blue-600' },
            ],
            get selected() { return this.opts.find(o => o.value === this.val) ?? this.opts[0] }
        }" @click.outside="open = false" class="relative shrink-0">
            <button type="button" @click="open = !open"
                class="cursor-pointer flex items-center gap-2 px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                       bg-slate-50 dark:bg-slate-700/50 hover:bg-white dark:hover:bg-slate-700 hover:border-blue-300 transition lato-regular"
                :class="val ? 'border-blue-300 bg-white dark:bg-slate-700 ring-1 ring-blue-200 dark:ring-blue-700/40' : ''">
                <span class="w-2 h-2 rounded-full shrink-0 transition" :class="selected.dot"></span>
                <span class="text-slate-700 dark:text-slate-200" x-text="selected.label"></span>
                <svg class="w-3.5 h-3.5 text-slate-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"   x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 class="absolute z-30 top-full mt-1.5 left-0 w-44 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl py-1.5 overflow-hidden"
                 style="display:none">
                <template x-for="opt in opts" :key="opt.value">
                    <button type="button" @click="val = opt.value; open = false"
                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm cursor-pointer transition"
                        :class="val === opt.value ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700'">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" :class="opt.dot"></span>
                        <span class="flex-1 text-left lato-regular"
                              :class="val === opt.value ? 'text-blue-700 dark:text-blue-300 font-semibold' : 'text-slate-600 dark:text-slate-300'"
                              x-text="opt.label"></span>
                        <svg x-show="val === opt.value" class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </template>
            </div>
        </div>

        {{-- Filtro: Departamento --}}
        <div x-data="{
            open: false,
            val: $wire.entangle('sucFiltroDept'),
            opts: [
                { value: '', label: 'Todos os departamentos' },
                @foreach($this->departamentos as $dept)
                { value: {{ $dept->id }}, label: '{{ addslashes($dept->name) }}' },
                @endforeach
            ],
            get selected() { return this.opts.find(o => String(o.value) === String(this.val)) ?? this.opts[0] }
        }" @click.outside="open = false" class="relative flex-1 min-w-40">
            <button type="button" @click="open = !open"
                class="cursor-pointer w-full flex items-center gap-2 px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                       bg-slate-50 dark:bg-slate-700/50 hover:bg-white dark:hover:bg-slate-700 hover:border-blue-300 transition lato-regular text-left"
                :class="val ? 'border-blue-300 bg-white dark:bg-slate-700 ring-1 ring-blue-200 dark:ring-blue-700/40' : ''">
                <div class="w-5 h-5 rounded-md flex items-center justify-center shrink-0 transition"
                     :class="val ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-200 dark:bg-slate-600'">
                    <svg class="w-3 h-3 transition" :class="val ? 'text-indigo-500' : 'text-slate-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h14a2 2 0 012 2v14M9 21V12h6v9"/></svg>
                </div>
                <span class="flex-1 truncate" :class="val ? 'text-slate-800 dark:text-white font-medium' : 'text-slate-500 dark:text-slate-400'" x-text="selected.label"></span>
                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95 -translate-y-1" x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-75"   x-transition:leave-start="opacity-100 scale-100 translate-y-0" x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                 class="absolute z-30 top-full mt-1.5 left-0 right-0 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl py-1.5 overflow-hidden max-h-56 overflow-y-auto"
                 style="display:none">
                <template x-for="opt in opts" :key="opt.value">
                    <button type="button" @click="val = opt.value; open = false"
                        class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm cursor-pointer transition"
                        :class="String(val) === String(opt.value) ? 'bg-blue-50 dark:bg-blue-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-700'">
                        <div class="w-5 h-5 rounded-md flex items-center justify-center shrink-0"
                             :class="String(val) === String(opt.value) ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                            <svg class="w-3 h-3" :class="String(val) === String(opt.value) ? 'text-blue-500' : 'text-slate-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <template x-if="opt.value !== ''"><path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h14a2 2 0 012 2v14M9 21V12h6v9"/></template>
                                <template x-if="opt.value === ''"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></template>
                            </svg>
                        </div>
                        <span class="flex-1 text-left lato-regular truncate"
                              :class="String(val) === String(opt.value) ? 'text-blue-700 dark:text-blue-300 font-semibold' : 'text-slate-600 dark:text-slate-300'"
                              x-text="opt.label"></span>
                        <svg x-show="String(val) === String(opt.value)" class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                    </button>
                </template>
            </div>
        </div>

        @if($sucBusca || $sucFiltroRisco || $sucFiltroDept)
        <button wire:click="sucLimparFiltros"
            class="cursor-pointer flex items-center gap-1.5 text-xs text-slate-500 hover:text-slate-700 dark:text-slate-400 dark:hover:text-slate-200
                   border border-slate-200 dark:border-slate-700 px-3 py-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition lato-regular shrink-0">
            <x-lucide-x class="w-3.5 h-3.5" /> Limpar
        </button>
        @endif
    </div>

    {{-- ── Posições ─────────────────────────────────────────────────── --}}
    @if($positions->isEmpty())
        <div class="bg-white dark:bg-slate-800 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 py-20 text-center">
            <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100 dark:from-blue-900/30 dark:to-indigo-900/30 flex items-center justify-center mx-auto mb-4">
                <x-lucide-git-branch class="w-7 h-7 text-indigo-400" />
            </div>
            <p class="text-sm font-semibold text-slate-600 dark:text-slate-300 lato-bold">Nenhuma posição crítica mapeada</p>
            <p class="text-xs text-slate-400 lato-regular mt-1 mb-6">Comece identificando os cargos estratégicos da empresa.</p>
            <button wire:click="sucAbrirModalPos('criar')" class="{{ $btnPrimary }}">
                <x-lucide-plus class="w-4 h-4" /> Mapear primeira posição
            </button>
        </div>
    @else
        <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-4">
            @foreach($positions as $pos)
            @php
                $rCfg       = $riskConfig[$pos->risk_level] ?? $riskConfig['medio'];
                $candidates = $pos->candidates;
                $isOpen     = $sucPosDetalheId === $pos->id;
            @endphp
            <div class="bg-white dark:bg-slate-800 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden
                        transition hover:ring-blue-300 dark:hover:ring-blue-600/50 hover:shadow-md hover:shadow-blue-100/60 dark:hover:shadow-blue-900/20 flex flex-col">

                {{-- Faixa de risco --}}
                <div class="h-1 w-full {{ $rCfg['bg'] }}"></div>

                {{-- Header do card --}}
                <div class="px-5 pt-4 pb-3 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="flex flex-wrap items-center gap-1.5 mb-1.5">
                                <span class="inline-flex items-center gap-1 text-[10px] font-bold px-2 py-0.5 rounded-full ring-1
                                             {{ $rCfg['light'] }} {{ $rCfg['text'] }} {{ $rCfg['ring'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $rCfg['bg'] }}"></span>
                                    {{ $rCfg['label'] }}
                                </span>
                                @if($pos->department)
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full lato-regular">
                                    <x-lucide-building-2 class="w-2.5 h-2.5" />
                                    {{ $pos->department->name }}
                                </span>
                                @endif
                                <span class="inline-flex items-center gap-1 text-[10px] text-slate-400 lato-regular ml-auto">
                                    <x-lucide-clock class="w-2.5 h-2.5" />
                                    {{ $pos->time_to_fill_months }}m
                                </span>
                            </div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white lato-bold leading-snug">
                                {{ $pos->title }}
                            </h3>
                            @if($pos->currentHolder)
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5 flex items-center gap-1">
                                <x-lucide-user class="w-3 h-3 shrink-0" />
                                Titular: <span class="text-slate-600 dark:text-slate-300">{{ $pos->currentHolder->name }}</span>
                            </p>
                            @endif
                        </div>

                        <div class="flex items-center gap-0.5 shrink-0">
                            <button wire:click="sucAbrirModalPos('editar', {{ $pos->id }})" title="Editar"
                                class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 hover:text-blue-600 transition">
                                <x-lucide-pencil class="w-3.5 h-3.5" />
                            </button>
                            <button x-data @click="if(confirm('Remover esta posição e todos os candidatos vinculados?')) $wire.sucExcluirPos({{ $pos->id }})" title="Remover"
                                class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 hover:text-rose-500 transition">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    </div>
                </div>

                {{-- Corpo: candidatos --}}
                <div class="px-5 py-3 flex-1 flex flex-col gap-3">

                    {{-- Estado de cobertura --}}
                    @if($candidates->isEmpty())
                    <div class="flex items-center gap-2 bg-rose-50 dark:bg-rose-900/10 border border-rose-100 dark:border-rose-800/20 rounded-xl px-3 py-2">
                        <x-lucide-alert-triangle class="w-3.5 h-3.5 text-rose-400 shrink-0" />
                        <span class="text-[11px] text-rose-600 dark:text-rose-400 lato-regular">Posição sem sucessores mapeados</span>
                    </div>
                    @else
                    <div class="space-y-1.5">
                        @foreach($candidates->take(3) as $cand)
                        @php
                            $rCandCfg   = $readinessConfig[$cand->readiness] ?? $readinessConfig['1_a_3_anos'];
                            $score      = $cand->readinessScore;
                            $scoreColor = $score >= 75 ? 'emerald' : ($score >= 50 ? 'amber' : 'rose');
                        @endphp
                        <div class="flex items-center gap-2.5 p-2 rounded-xl bg-slate-50 dark:bg-slate-700/40 group hover:bg-blue-50/60 dark:hover:bg-blue-900/10 transition">
                            <div class="w-7 h-7 rounded-full flex items-center justify-center text-[10px] font-bold text-white shrink-0
                                        bg-gradient-to-br from-blue-400 to-indigo-500">
                                {{ $cand->user->initials() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-1">
                                    <p class="text-[11px] font-semibold text-slate-700 dark:text-slate-200 lato-bold truncate">
                                        {{ $cand->user->name }}
                                    </p>
                                    <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full shrink-0
                                                 bg-{{ $rCandCfg['color'] }}-100 dark:bg-{{ $rCandCfg['color'] }}-900/30
                                                 text-{{ $rCandCfg['color'] }}-700 dark:text-{{ $rCandCfg['color'] }}-300">
                                        {{ $rCandCfg['label'] }}
                                    </span>
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <div class="flex-1 h-1 bg-slate-200 dark:bg-slate-600 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full bg-gradient-to-r
                                            {{ $scoreColor === 'emerald' ? 'from-emerald-400 to-teal-400' : ($scoreColor === 'amber' ? 'from-amber-400 to-orange-400' : 'from-rose-400 to-pink-400') }}"
                                             style="width: {{ $score }}%"></div>
                                    </div>
                                    <span class="text-[10px] font-bold text-{{ $scoreColor }}-500 shrink-0 w-7 text-right">{{ $score }}%</span>
                                </div>
                            </div>
                            <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition shrink-0">
                                <button wire:click="sucAbrirModalCand({{ $pos->id }}, 'editar', {{ $cand->id }})"
                                    class="cursor-pointer p-1 rounded-md text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                    <x-lucide-pencil class="w-3 h-3" />
                                </button>
                                <button x-data @click="if(confirm('Remover candidato?')) $wire.sucExcluirCand({{ $cand->id }})"
                                    class="cursor-pointer p-1 rounded-md text-slate-400 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
                                    <x-lucide-x class="w-3 h-3" />
                                </button>
                            </div>
                        </div>
                        @endforeach
                        @if($candidates->count() > 3)
                        <p class="text-[10px] text-slate-400 text-center lato-regular pt-0.5">
                            +{{ $candidates->count() - 3 }} mais —
                            <button wire:click="sucVerDetalhe({{ $pos->id }})" class="cursor-pointer text-blue-500 hover:underline">ver todos</button>
                        </p>
                        @endif
                    </div>
                    @endif

                    {{-- Ações do card --}}
                    <div class="flex items-center gap-2 mt-auto pt-1">
                        <button wire:click="sucAbrirModalCand({{ $pos->id }}, 'criar')"
                            class="cursor-pointer flex-1 flex items-center justify-center gap-1.5 text-xs font-semibold
                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                                   text-white py-2 rounded-xl shadow-sm shadow-indigo-200/50 transition lato-bold">
                            <x-lucide-user-plus class="w-3.5 h-3.5" /> Candidato
                        </button>
                        <button wire:click="sucVerDetalhe({{ $pos->id }})"
                            class="cursor-pointer flex items-center justify-center gap-1 text-xs px-3 py-2 rounded-xl transition lato-regular border shrink-0
                                   {{ $isOpen
                                       ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white border-transparent shadow-sm shadow-indigo-200/50'
                                       : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-blue-300 hover:text-blue-600 dark:hover:text-blue-400' }}">
                            @if($isOpen)
                                <x-lucide-chevron-up class="w-3.5 h-3.5" /> Fechar
                            @else
                                <x-lucide-eye class="w-3.5 h-3.5" /> Detalhe
                            @endif
                        </button>
                    </div>
                </div>

                {{-- Painel expansível de detalhe --}}
                @if($isOpen && $this->sucPosDetalhe?->id === $pos->id)
                @php $det = $this->sucPosDetalhe; @endphp
                <div class="border-t border-slate-100 dark:border-slate-700 bg-gradient-to-b from-slate-50/80 to-white dark:from-slate-700/20 dark:to-slate-800 px-5 py-4 space-y-4">

                    @if($det->description)
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular leading-relaxed italic border-l-2 border-indigo-300 pl-3">
                        {{ $det->description }}
                    </p>
                    @endif

                    <div class="space-y-3">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider lato-bold flex items-center gap-1.5">
                            <x-lucide-users class="w-3 h-3" /> Análise de Prontidão dos Candidatos
                        </p>

                        @forelse($det->candidates as $cand)
                        @php
                            $rCfgC      = $readinessConfig[$cand->readiness] ?? $readinessConfig['1_a_3_anos'];
                            $score      = $cand->readinessScore;
                            $sColor     = $score >= 75 ? 'emerald' : ($score >= 50 ? 'amber' : 'rose');
                            $dpiProgress = $cand->dpiPlan?->progress_percent ?? null;
                            $dpiStatus   = match($cand->dpiPlan?->status ?? '') {
                                'aprovado'  => 'Aprovado',
                                'concluido' => 'Concluído',
                                'enviado'   => 'Enviado',
                                default     => null,
                            };
                            $eval       = \App\Models\ManagerEvaluation::where('manager_id', $cand->user_id)
                                ->whereNotNull('final_score')->latest('completed_at')->first();
                            $evalScore  = $eval?->final_score;
                        @endphp
                        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700/60 p-3.5 shadow-sm">
                            {{-- Cabeçalho do candidato --}}
                            <div class="flex items-center justify-between gap-2 mb-3">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-xs font-bold text-white shrink-0">
                                        {{ $cand->user->initials() }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-700 dark:text-slate-200 lato-bold leading-tight">{{ $cand->user->name }}</p>
                                        <p class="text-[10px] text-slate-400 lato-regular">
                                            {{ $cand->user->position ?? $cand->user->department?->name ?? 'Colaborador' }}
                                            · <span class="text-indigo-500">{{ $cand->priorityLabel }} prioridade</span>
                                        </p>
                                    </div>
                                </div>
                                <div class="text-right shrink-0">
                                    <p class="text-xl font-black lato-black
                                               {{ $sColor === 'emerald' ? 'text-emerald-500' : ($sColor === 'amber' ? 'text-amber-500' : 'text-rose-500') }}">
                                        {{ $score }}%
                                    </p>
                                    <p class="text-[9px] text-slate-400 lato-regular">prontidão</p>
                                </div>
                            </div>

                            {{-- Barra principal --}}
                            <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mb-3">
                                <div class="h-full rounded-full bg-gradient-to-r
                                    {{ $sColor === 'emerald' ? 'from-emerald-400 to-teal-400' : ($sColor === 'amber' ? 'from-amber-400 to-orange-400' : 'from-rose-400 to-pink-400') }}"
                                     style="width:{{ $score }}%"></div>
                            </div>

                            {{-- Três métricas --}}
                            <div class="grid grid-cols-3 gap-2 text-center">
                                <div class="rounded-lg p-2 bg-{{ $rCfgC['color'] }}-50 dark:bg-{{ $rCfgC['color'] }}-900/20 ring-1 ring-{{ $rCfgC['color'] }}-100 dark:ring-{{ $rCfgC['color'] }}-800/20">
                                    <p class="text-[11px] font-bold text-{{ $rCfgC['color'] }}-600 dark:text-{{ $rCfgC['color'] }}-400 lato-bold">{{ $rCfgC['label'] }}</p>
                                    <p class="text-[9px] text-slate-400 lato-regular mt-0.5">Horizonte</p>
                                </div>
                                <div class="rounded-lg p-2 bg-violet-50 dark:bg-violet-900/20 ring-1 ring-violet-100 dark:ring-violet-800/20">
                                    @if($dpiProgress !== null)
                                        <p class="text-[11px] font-bold text-violet-600 dark:text-violet-400 lato-bold">{{ $dpiProgress }}%</p>
                                        <p class="text-[9px] text-slate-400 lato-regular mt-0.5">PDI{{ $dpiStatus ? " · {$dpiStatus}" : '' }}</p>
                                    @else
                                        <p class="text-[11px] text-slate-400 lato-regular">—</p>
                                        <p class="text-[9px] text-slate-400 lato-regular mt-0.5">Sem PDI</p>
                                    @endif
                                </div>
                                <div class="rounded-lg p-2 bg-blue-50 dark:bg-blue-900/20 ring-1 ring-blue-100 dark:ring-blue-800/20">
                                    @if($evalScore !== null)
                                        <p class="text-[11px] font-bold text-blue-600 dark:text-blue-400 lato-bold">{{ number_format($evalScore, 1) }}<span class="font-normal text-blue-400">/5</span></p>
                                        <p class="text-[9px] text-slate-400 lato-regular mt-0.5">Avaliação</p>
                                    @else
                                        <p class="text-[11px] text-slate-400 lato-regular">—</p>
                                        <p class="text-[9px] text-slate-400 lato-regular mt-0.5">Sem avaliação</p>
                                    @endif
                                </div>
                            </div>

                            @if($cand->notes)
                            <p class="text-[10px] text-slate-400 italic mt-2.5 lato-regular leading-relaxed border-l-2 border-slate-200 pl-2">"{{ $cand->notes }}"</p>
                            @endif
                        </div>
                        @empty
                        <p class="text-xs text-slate-400 lato-regular text-center py-3">Nenhum candidato vinculado.</p>
                        @endforelse
                    </div>

                    @if($det->notes)
                    <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-800/20 rounded-xl p-3">
                        <p class="text-[10px] font-bold text-amber-700 dark:text-amber-400 lato-bold mb-0.5 flex items-center gap-1">
                            <x-lucide-sticky-note class="w-3 h-3" /> Observações
                        </p>
                        <p class="text-xs text-amber-700 dark:text-amber-300 lato-regular">{{ $det->notes }}</p>
                    </div>
                    @endif
                </div>
                @endif

            </div>
            @endforeach
        </div>
    @endif

</div>

{{-- ══════════════════════════════════════════════════════════════════
     MODAL: Posição Crítica
══════════════════════════════════════════════════════════════════ --}}
@if($sucPosModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('sucPosModal', false)"></div>

    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-xl flex flex-col
                rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 max-h-[92vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm shadow-indigo-200/50">
                    <x-lucide-git-branch class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white lato-bold">
                        {{ $sucPosModo === 'criar' ? 'Nova Posição Crítica' : 'Editar Posição' }}
                    </h3>
                    <p class="text-[10px] text-slate-400 lato-regular">
                        {{ $sucPosModo === 'criar' ? 'Mapeie um cargo estratégico para sucessão' : 'Atualize os dados da posição' }}
                    </p>
                </div>
            </div>
            <button wire:click="$set('sucPosModal', false)"
                class="cursor-pointer w-8 h-8 flex items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Título da posição *</label>
                <input wire:model="sucPosTitle" type="text" placeholder="Ex: Diretor de Operações, Gerente de TI…"
                    class="w-full px-3.5 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                           bg-white dark:bg-slate-700/60 text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400 lato-regular transition" />
                @error('sucPosTitle') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Descrição <span class="font-normal text-slate-400">(opcional)</span></label>
                <textarea wire:model="sucPosDesc" rows="2" placeholder="Responsabilidades, contexto estratégico…"
                    class="w-full px-3.5 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                           bg-white dark:bg-slate-700/60 text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400 lato-regular resize-none transition"></textarea>
            </div>

            {{-- Risco --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Nível de risco *</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach([
                        ['critico','Crítico',  'rose',  'alert-octagon'],
                        ['alto',   'Alto',     'amber', 'alert-triangle'],
                        ['medio',  'Médio',    'blue',  'info'],
                    ] as [$val,$lbl,$clr,$ico])
                    <button type="button" wire:click="$set('sucPosRisk', '{{ $val }}')"
                        class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 text-xs font-semibold transition cursor-pointer
                               {{ $sucPosRisk === $val
                                   ? "border-{$clr}-400 bg-{$clr}-50 dark:bg-{$clr}-900/20 text-{$clr}-700 dark:text-{$clr}-300"
                                   : 'border-slate-200 dark:border-slate-600 text-slate-400 hover:border-slate-300 dark:hover:border-slate-500' }}">
                        <x-dynamic-component :component="'lucide-' . $ico" class="w-4 h-4" />
                        {{ $lbl }}
                    </button>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                {{-- Prazo --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Prazo de reposição (meses)</label>
                    <div class="relative">
                        <x-lucide-clock class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                        <input wire:model="sucPosTimeFill" type="number" min="1" max="120"
                            class="w-full pl-8 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                   bg-white dark:bg-slate-700/60 text-slate-800 dark:text-white
                                   focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400 lato-regular transition" />
                    </div>
                </div>

                {{-- Departamento — dropdown customizado --}}
                <div x-data="{
                    open: false,
                    val: $wire.entangle('sucPosDeptId'),
                    opts: [
                        { value: '', label: 'Nenhum', icon: 'slash' },
                        @foreach($this->departamentos as $dept)
                        { value: {{ $dept->id }}, label: '{{ addslashes($dept->name) }}', icon: 'building-2' },
                        @endforeach
                    ],
                    get selected() { return this.opts.find(o => String(o.value) === String(this.val)) ?? this.opts[0] }
                }" @click.outside="open = false" class="relative">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Departamento</label>
                    <button type="button" @click="open = !open"
                        class="cursor-pointer w-full flex items-center gap-2.5 px-3 py-2.5 text-sm
                               border border-slate-200 dark:border-slate-700 rounded-xl
                               bg-white dark:bg-slate-700/60 text-slate-700 dark:text-slate-200
                               hover:border-blue-300 focus:outline-none focus:ring-2 focus:ring-blue-400/30 transition text-left">
                        <div class="w-6 h-6 rounded-lg shrink-0 flex items-center justify-center"
                             :class="selected.value ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                            <svg class="w-3 h-3" :class="selected.value ? 'text-indigo-500' : 'text-slate-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h14a2 2 0 012 2v14M9 21V12h6v9"/>
                            </svg>
                        </div>
                        <span class="flex-1 truncate lato-regular text-sm" x-text="selected.label"
                              :class="selected.value ? 'text-slate-800 dark:text-white' : 'text-slate-400'"></span>
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
                         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
                         x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
                         class="absolute z-40 top-full mt-1.5 w-full bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden"
                         style="display:none">
                        <div class="max-h-48 overflow-y-auto py-1">
                            <template x-for="opt in opts" :key="opt.value">
                                <button type="button" @click="val = opt.value; open = false"
                                    class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm cursor-pointer transition"
                                    :class="String(val) === String(opt.value)
                                        ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                    <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition"
                                         :class="String(val) === String(opt.value) ? 'bg-blue-100 dark:bg-blue-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                                        <svg class="w-3 h-3 transition" :class="String(val) === String(opt.value) ? 'text-blue-500' : 'text-slate-400'" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <template x-if="opt.value !== ''">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3 21V7a2 2 0 012-2h14a2 2 0 012 2v14M9 21V12h6v9"/>
                                            </template>
                                            <template x-if="opt.value === ''">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M18 12H6"/>
                                            </template>
                                        </svg>
                                    <span x-text="opt.label" class="flex-1 text-left lato-regular text-sm truncate"></span>
                                    <svg x-show="String(val) === String(opt.value)" class="w-3.5 h-3.5 text-blue-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/></svg>
                                </button>
                            </template>
                            <div x-show="filtered.length === 0" class="px-4 py-5 text-center text-xs text-slate-400 lato-regular">
                                Nenhum departamento encontrado
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Titular atual --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Titular atual</label>
                    <select wire:model="sucPosHolderId"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                               bg-white dark:bg-slate-700/60 text-slate-700 dark:text-slate-200
                               focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular cursor-pointer">
                        <option value="">Vaga / Não definido</option>
                        @foreach($this->sucAllUsers as $u)
                        <option value="{{ $u->id }}">{{ $u->name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Observações --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Observações <span class="font-normal text-slate-400">(opcional)</span></label>
                    <textarea wire:model="sucPosNotes" rows="2" placeholder="Anotações, urgência, contexto…"
                        class="w-full px-3.5 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                               bg-white dark:bg-slate-700/60 text-slate-800 dark:text-white placeholder-slate-400
                               focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none transition"></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0 bg-slate-50/50 dark:bg-slate-800/50">
                <button wire:click="$set('sucPosModal', false)"
                    class="cursor-pointer px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400
                           rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition lato-regular">
                    Cancelar
                </button>
                <button wire:click="sucSalvarPos" wire:loading.attr="disabled"
                    class="cursor-pointer flex-1 flex items-center justify-center gap-2
                           bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                           text-white text-sm py-2.5 rounded-xl shadow-sm shadow-indigo-200/60 transition lato-bold disabled:opacity-60">
                    <span wire:loading.remove wire:target="sucSalvarPos" class="flex items-center gap-2">
                        <x-lucide-check class="w-4 h-4" />
                        {{ $sucPosModo === 'criar' ? 'Criar posição' : 'Salvar alterações' }}
                    </span>
                    <span wire:loading wire:target="sucSalvarPos">Salvando…</span>
                </button>
            </div>
        </div>
    </div>
@endif
{{-- /sucPosModal --}}

{{-- Modal: Candidato --}}
@if($sucCandModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" wire:click="$set('sucCandModal', false)"></div>
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg flex flex-col
                rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 max-h-[92vh] overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm">
                    <x-lucide-user-plus class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h3 class="text-sm font-bold text-slate-800 dark:text-white lato-bold">
                        {{ $sucCandModo === 'criar' ? 'Adicionar Candidato' : 'Editar Candidato' }}
                    </h3>
                    <p class="text-[10px] text-slate-400 lato-regular">Potencial sucessor vinculado ao PDI e avaliação</p>
                </div>
            </div>
            <button wire:click="$set('sucCandModal', false)"
                class="cursor-pointer w-8 h-8 flex items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Colaborador *</label>
                <select wire:model.live="sucCandUserId"
                    class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                           bg-white dark:bg-slate-700/60 text-slate-700 dark:text-slate-200
                           focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular cursor-pointer">
                    <option value="">Selecione…</option>
                    @foreach($this->sucAllUsers as $u)
                    <option value="{{ $u->id }}">{{ $u->name }}{{ $u->position ? ' — ' . $u->position : '' }}</option>
                    @endforeach
                </select>
                @error('sucCandUserId') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Horizonte de prontidão *</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach([['pronto_agora','Pronto agora','emerald','user-check'],['6_a_12_meses','6–12 meses','amber','clock'],['1_a_3_anos','1–3 anos','blue','calendar']] as [$val,$lbl,$clr,$ico])
                    <button type="button" wire:click="$set('sucCandReadiness', '{{ $val }}')"
                        class="flex flex-col items-center gap-1.5 py-3 px-2 rounded-xl border-2 transition cursor-pointer
                               {{ $sucCandReadiness === $val ? "border-{$clr}-400 bg-{$clr}-50 dark:bg-{$clr}-900/20 text-{$clr}-700 dark:text-{$clr}-300" : 'border-slate-200 dark:border-slate-600 text-slate-400 hover:border-slate-300' }}">
                        <x-dynamic-component :component="'lucide-' . $ico" class="w-4 h-4" />
                        <span class="text-[10px] font-bold lato-bold text-center leading-tight">{{ $lbl }}</span>
                    </button>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Prioridade</label>
                <div class="grid grid-cols-3 gap-2">
                    @foreach([1=>'1º — Principal', 2=>'2º — Secundário', 3=>'3º — Reserva'] as $p=>$pl)
                    <button type="button" wire:click="$set('sucCandPriority', {{ $p }})"
                        class="py-2 text-xs font-semibold rounded-xl border-2 transition cursor-pointer
                               {{ $sucCandPriority === $p ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300' : 'border-slate-200 dark:border-slate-600 text-slate-400 hover:border-slate-300' }}">
                        {{ $pl }}
                    </button>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Observações <span class="font-normal text-slate-400">(opcional)</span></label>
                <textarea wire:model="sucCandNotes" rows="2" placeholder="Pontos de desenvolvimento, gaps…"
                    class="w-full px-3.5 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                           bg-white dark:bg-slate-700/60 text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none transition"></textarea>
            </div>
        </div>
        <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0 bg-slate-50/50 dark:bg-slate-800/50">
            <button wire:click="$set('sucCandModal', false)"
                class="cursor-pointer px-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400
                       rounded-xl hover:bg-slate-100 dark:hover:bg-slate-700 transition lato-regular">
                Cancelar
            </button>
            <button wire:click="sucSalvarCand" wire:loading.attr="disabled"
                class="cursor-pointer flex-1 flex items-center justify-center gap-2
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       text-white text-sm py-2.5 rounded-xl shadow-sm transition lato-bold disabled:opacity-60">
                <span wire:loading.remove wire:target="sucSalvarCand" class="flex items-center gap-2">
                    <x-lucide-check class="w-4 h-4" />
                    {{ $sucCandModo === 'criar' ? 'Adicionar candidato' : 'Salvar' }}
                </span>
                <span wire:loading wire:target="sucSalvarCand">Salvando…</span>
            </button>
        </div>
    </div>
</div>
@endif
{{-- /sucCandModal --}}

@endif
{{-- /aba sucessao --}}
