<div class="p-4 sm:p-6 lg:p-8">

    {{-- ───── Cabeçalho ─────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-1">
                <a href="{{ route('pesquisas') }}"
                   class="flex items-center gap-1 text-xs lato-regular text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" />
                    Pesquisas
                </a>
                <x-lucide-chevron-right class="w-3 h-3 text-slate-300" />
                <span class="text-xs text-slate-500 lato-regular">Ranking de Gerentes</span>
            </div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white lato-black tracking-tight flex items-center gap-2">
                <x-lucide-trophy class="w-6 h-6 sm:w-7 sm:h-7 text-amber-400" />
                Ranking de Gerentes
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1 lato-regular">
                Baseado nas respostas da Pesquisa de Clima — perguntas marcadas como "Avaliação do Gestor"
            </p>
        </div>
    </div>

    {{-- ───── Cards de Estatísticas ─────────────────────────────────── --}}
    <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">

        <div class="bg-[#F1F5F9] dark:bg-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-slate-400 lato-regular">Gerentes</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">
                    {{ $this->stats['total_gerentes'] }}
                </p>
                @if ($this->stats['sem_nota'] > 0)
                    <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular mt-0.5 flex items-center gap-1">
                        <x-lucide-circle-dashed class="w-3 h-3" />
                        {{ $this->stats['sem_nota'] }} sem nota
                    </p>
                @endif
            </div>
            <x-lucide-user-check class="w-8 h-8 sm:w-10 sm:h-10 text-slate-400" />
        </div>

        <div class="bg-[#D1FAE5] dark:bg-emerald-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-emerald-200 lato-regular">Média Geral</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">
                    @if ($this->stats['media_geral'] !== null)
                        {{ number_format($this->stats['media_geral'], 2, ',', '') }}
                    @else
                        —
                    @endif
                </p>
                @if ($this->stats['media_geral'] !== null)
                    @php
                        $nivelMedia = $this->stats['media_geral'] >= 8 ? 'Excelente' : ($this->stats['media_geral'] >= 6 ? 'Regular' : 'Atenção');
                        $nivelCor   = $this->stats['media_geral'] >= 8 ? 'text-emerald-600 dark:text-emerald-400' : ($this->stats['media_geral'] >= 6 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
                    @endphp
                    <p class="text-xs lato-regular mt-0.5 flex items-center gap-1 {{ $nivelCor }}">
                        @if ($this->stats['media_geral'] >= 8)
                            <x-lucide-star class="w-3 h-3" />
                        @elseif ($this->stats['media_geral'] >= 6)
                            <x-lucide-minus-circle class="w-3 h-3" />
                        @else
                            <x-lucide-alert-triangle class="w-3 h-3" />
                        @endif
                        {{ $nivelMedia }}
                    </p>
                @endif
            </div>
            <x-lucide-bar-chart-2 class="w-8 h-8 sm:w-10 sm:h-10 text-emerald-500" />
        </div>

        <div class="bg-[#EDE9FE] dark:bg-violet-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-violet-200 lato-regular">Com Nota</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">
                    {{ $this->stats['com_nota'] }}
                </p>
                @if ($this->stats['total_gerentes'] > 0)
                    <p class="text-xs text-indigo-500 dark:text-indigo-400 lato-regular mt-0.5 flex items-center gap-1">
                        <x-lucide-percent class="w-3 h-3" />
                        {{ round(($this->stats['com_nota'] / $this->stats['total_gerentes']) * 100) }}% do total
                    </p>
                @endif
            </div>
            <x-lucide-star class="w-8 h-8 sm:w-10 sm:h-10 text-indigo-500" />
        </div>

        <div class="bg-[#FEF3C7] dark:bg-amber-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-amber-200 lato-regular">Respostas</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">
                    {{ $this->stats['total_respostas'] }}
                </p>
                @if ($this->stats['com_nota'] > 0)
                    <p class="text-xs text-amber-600 dark:text-amber-400 lato-regular mt-0.5 flex items-center gap-1">
                        <x-lucide-users class="w-3 h-3" />
                        ~{{ round($this->stats['total_respostas'] / max(1, $this->stats['com_nota']), 1) }} por gerente
                    </p>
                @endif
            </div>
            <x-lucide-message-square class="w-8 h-8 sm:w-10 sm:h-10 text-amber-500" />
        </div>

    </div>

    {{-- ───── Barra de Distribuição por Nível ─────────────────────────── --}}
    @if ($this->stats['com_nota'] > 0)
        @php
            $total   = max(1, $this->stats['com_nota']);
            $pctExc  = round(($this->stats['excelente'] / $total) * 100);
            $pctReg  = round(($this->stats['regular']   / $total) * 100);
            $pctAtt  = round(($this->stats['atencao']   / $total) * 100);
        @endphp
        <div class="mt-4 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-4 sm:p-5">
            <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3 flex items-center gap-1.5">
                <x-lucide-layout-list class="w-3.5 h-3.5" />
                Distribuição por Nível
            </p>
            <div class="flex h-3 rounded-full overflow-hidden gap-0.5">
                @if ($pctExc > 0)
                    <div class="bg-emerald-500 transition-all rounded-l-full {{ $pctReg == 0 && $pctAtt == 0 ? 'rounded-r-full' : '' }}"
                         style="width: {{ $pctExc }}%"
                         title="Excelente: {{ $this->stats['excelente'] }}"></div>
                @endif
                @if ($pctReg > 0)
                    <div class="bg-amber-500 transition-all {{ $pctExc == 0 ? 'rounded-l-full' : '' }} {{ $pctAtt == 0 ? 'rounded-r-full' : '' }}"
                         style="width: {{ $pctReg }}%"
                         title="Regular: {{ $this->stats['regular'] }}"></div>
                @endif
                @if ($pctAtt > 0)
                    <div class="bg-red-500 transition-all rounded-r-full {{ $pctExc == 0 && $pctReg == 0 ? 'rounded-l-full' : '' }}"
                         style="width: {{ $pctAtt }}%"
                         title="Atenção: {{ $this->stats['atencao'] }}"></div>
                @endif
            </div>
            <div class="mt-2.5 flex flex-wrap gap-x-5 gap-y-1.5">
                <span class="flex items-center gap-1.5 text-xs lato-regular text-slate-600 dark:text-slate-300">
                    <x-lucide-star class="w-3 h-3 text-emerald-500" />
                    Excelente — <strong class="lato-bold">{{ $this->stats['excelente'] }}</strong>
                    <span class="text-slate-400">({{ $pctExc }}%)</span>
                </span>
                <span class="flex items-center gap-1.5 text-xs lato-regular text-slate-600 dark:text-slate-300">
                    <x-lucide-minus-circle class="w-3 h-3 text-amber-500" />
                    Regular — <strong class="lato-bold">{{ $this->stats['regular'] }}</strong>
                    <span class="text-slate-400">({{ $pctReg }}%)</span>
                </span>
                <span class="flex items-center gap-1.5 text-xs lato-regular text-slate-600 dark:text-slate-300">
                    <x-lucide-alert-triangle class="w-3 h-3 text-red-500" />
                    Atenção — <strong class="lato-bold">{{ $this->stats['atencao'] }}</strong>
                    <span class="text-slate-400">({{ $pctAtt }}%)</span>
                </span>
            </div>
        </div>
    @endif

    {{-- ───── Filtros ───────────────────────────────────────────────── --}}
    <div class="mt-6 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row gap-3">

        {{-- Busca por nome --}}
        <div class="relative flex-1">
            <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar gerente pelo nome..."
                class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-slate-50 dark:bg-slate-700
                       text-slate-800 dark:text-white placeholder-slate-400
                       border border-slate-200 dark:border-slate-600
                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition" />
        </div>

        {{-- Filtro por pesquisa --}}
        <div class="relative sm:w-72">
            <x-lucide-clipboard-list class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <select wire:model.live="surveyFilter"
                class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-slate-50 dark:bg-slate-700
                       text-slate-800 dark:text-white border border-slate-200 dark:border-slate-600
                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                       appearance-none cursor-pointer transition">
                <option value="">Todas as pesquisas</option>
                @foreach ($this->pesquisasDisponiveis as $survey)
                    <option value="{{ $survey->id }}">{{ $survey->title }}</option>
                @endforeach
            </select>
        </div>

        @if (trim($search) !== '' || $surveyFilter !== '' || $sort !== 'score' || $sortDir !== 'desc')
            <button type="button" wire:click="limparFiltros"
                class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg
                       bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600
                       text-slate-600 dark:text-slate-300 transition cursor-pointer whitespace-nowrap">
                <x-lucide-x class="w-3 h-3" />
                Limpar
            </button>
        @endif
    </div>

    {{-- ───── Tabela de Ranking ────────────────────────────────────── --}}
    <div class="mt-6">
        @if ($this->ranking->isEmpty())
            <div class="bg-white dark:bg-slate-800 border border-dashed border-slate-100 dark:border-slate-700
                        rounded-2xl p-10 flex flex-col items-center text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                    <x-lucide-trophy class="w-7 h-7 text-slate-400" />
                </div>
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Nenhum dado disponível</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-1 max-w-sm">
                    O ranking será exibido assim que colaboradores responderem pesquisas com perguntas de avaliação do gestor.
                </p>
            </div>
        @else
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">

                {{-- ── Macro para ícone de ordenação ───────────────────── --}}
                {{-- Uso: @include com $sortCol --}}

                {{-- Cabeçalho da tabela (com ordenação) --}}
                @php
                    $thBase = 'flex items-center gap-1 cursor-pointer select-none group/th transition hover:text-slate-700 dark:hover:text-slate-200';
                @endphp

                <div class="hidden sm:grid grid-cols-12 gap-4 px-5 py-3 bg-slate-50 dark:bg-slate-700/50
                            border-b border-slate-100 dark:border-slate-700
                            text-[11px] lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">

                    <div class="col-span-1 text-center">
                        <x-lucide-hash class="w-3.5 h-3.5 mx-auto" />
                    </div>

                    {{-- Gerente (ordenável) --}}
                    <div class="col-span-3">
                        <button wire:click="sortBy('name')" class="{{ $thBase }}">
                            <x-lucide-user class="w-3.5 h-3.5" />
                            Gerente
                            @if ($sort === 'name' && $sortDir === 'asc')
                                <x-lucide-chevron-up class="w-3.5 h-3.5 text-emerald-500" />
                            @elseif ($sort === 'name' && $sortDir === 'desc')
                                <x-lucide-chevron-down class="w-3.5 h-3.5 text-emerald-500" />
                            @else
                                <x-lucide-chevrons-up-down class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover/th:text-slate-400" />
                            @endif
                        </button>
                    </div>

                    {{-- Departamento --}}
                    <div class="col-span-3 flex items-center gap-1">
                        <x-lucide-building-2 class="w-3.5 h-3.5" />
                        Departamento
                    </div>

                    {{-- Pesquisa --}}
                    <div class="col-span-2 flex items-center gap-1">
                        <x-lucide-clipboard-list class="w-3.5 h-3.5" />
                        Pesquisa
                    </div>

                    {{-- Respostas (ordenável) --}}
                    <div class="col-span-2 flex justify-center">
                        <button wire:click="sortBy('responses')" class="{{ $thBase }}">
                            <x-lucide-message-square class="w-3.5 h-3.5" />
                            Respostas
                            @if ($sort === 'responses' && $sortDir === 'asc')
                                <x-lucide-chevron-up class="w-3.5 h-3.5 text-emerald-500" />
                            @elseif ($sort === 'responses' && $sortDir === 'desc')
                                <x-lucide-chevron-down class="w-3.5 h-3.5 text-emerald-500" />
                            @else
                                <x-lucide-chevrons-up-down class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover/th:text-slate-400" />
                            @endif
                        </button>
                    </div>

                    {{-- Nota (ordenável) --}}
                    <div class="col-span-2 flex justify-center">
                        <button wire:click="sortBy('score')" class="{{ $thBase }}">
                            <x-lucide-star class="w-3.5 h-3.5" />
                            Nota
                            @if ($sort === 'score' && $sortDir === 'asc')
                                <x-lucide-chevron-up class="w-3.5 h-3.5 text-emerald-500" />
                            @elseif ($sort === 'score' && $sortDir === 'desc')
                                <x-lucide-chevron-down class="w-3.5 h-3.5 text-emerald-500" />
                            @else
                                <x-lucide-chevrons-up-down class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 group-hover/th:text-slate-400" />
                            @endif
                        </button>
                    </div>
                </div>

                {{-- Linhas --}}
                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                    @foreach ($this->ranking as $i => $score)
                        @php
                            $posicao = $i + 1;

                            $notaColor = match (true) {
                                $score->average_score === null => 'text-slate-400',
                                $score->average_score >= 8    => 'text-emerald-600 dark:text-emerald-400',
                                $score->average_score >= 6    => 'text-amber-600 dark:text-amber-400',
                                default                       => 'text-red-600 dark:text-red-400',
                            };

                            $badgeCls = match (true) {
                                $score->average_score === null => null,
                                $score->average_score >= 8    => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                                $score->average_score >= 6    => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                                default                       => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                            };

                            $badgeLabel = match (true) {
                                $score->average_score === null => null,
                                $score->average_score >= 8    => 'Excelente',
                                $score->average_score >= 6    => 'Regular',
                                default                       => 'Atenção',
                            };

                            $barWidth = $score->average_score !== null
                                ? min(100, round(($score->average_score / 10) * 100))
                                : 0;

                            $barColor = match (true) {
                                $score->average_score === null => 'bg-slate-200',
                                $score->average_score >= 8    => 'bg-emerald-500',
                                $score->average_score >= 6    => 'bg-amber-500',
                                default                       => 'bg-red-500',
                            };
                        @endphp

                        <div class="grid grid-cols-2 sm:grid-cols-12 gap-3 sm:gap-4 px-4 sm:px-5 py-3.5
                                    hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">

                            {{-- Posição / Medalha --}}
                            <div class="col-span-1 sm:col-span-1 flex items-center justify-center">
                                @if ($posicao === 1)
                                    <span title="1º lugar">
                                        <x-lucide-trophy class="w-5 h-5 text-amber-400" />
                                    </span>
                                @elseif ($posicao === 2)
                                    <span title="2º lugar">
                                        <x-lucide-award class="w-5 h-5 text-slate-400" />
                                    </span>
                                @elseif ($posicao === 3)
                                    <span title="3º lugar">
                                        <x-lucide-award class="w-5 h-5 text-amber-600" />
                                    </span>
                                @else
                                    <span class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700
                                                 flex items-center justify-center text-xs lato-bold
                                                 text-slate-500 dark:text-slate-400">
                                        {{ $posicao }}
                                    </span>
                                @endif
                            </div>

                            {{-- Gerente --}}
                            <div class="col-span-1 sm:col-span-3 flex items-center gap-2.5 min-w-0">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-blue-500 to-indigo-500
                                            flex items-center justify-center shrink-0 text-white text-xs lato-black">
                                    {{ mb_strtoupper(mb_substr($score->manager?->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="min-w-0">
                                    <span class="text-sm lato-bold text-slate-800 dark:text-white truncate block">
                                        {{ $score->manager?->name ?? '—' }}
                                    </span>
                                    {{-- Departamento visível só no mobile --}}
                                    <span class="sm:hidden text-xs lato-regular text-slate-400 truncate flex items-center gap-1">
                                        <x-lucide-building-2 class="w-3 h-3 shrink-0" />
                                        {{ $score->manager?->department?->name ?? '—' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Departamento (desktop) --}}
                            <div class="hidden sm:flex col-span-3 items-center gap-1.5">
                                <x-lucide-building-2 class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0" />
                                <span class="text-sm lato-regular text-slate-500 dark:text-slate-400 truncate">
                                    {{ $score->manager?->department?->name ?? '—' }}
                                </span>
                            </div>

                            {{-- Pesquisa --}}
                            <div class="hidden sm:flex col-span-2 items-center gap-1.5 min-w-0">
                                <x-lucide-clipboard-list class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0" />
                                <span class="text-xs lato-regular text-slate-500 dark:text-slate-400 truncate"
                                      title="{{ $score->survey?->title }}">
                                    {{ $score->survey?->title ?? '—' }}
                                </span>
                            </div>

                            {{-- Respostas --}}
                            <div class="hidden sm:flex col-span-2 items-center justify-center">
                                <span class="inline-flex items-center gap-1 text-xs lato-regular
                                             text-slate-500 dark:text-slate-400">
                                    <x-lucide-message-square class="w-3.5 h-3.5" />
                                    {{ $score->total_responses }}
                                </span>
                            </div>

                            {{-- Nota + Badge + Barra --}}
                            <div class="col-span-1 sm:col-span-2 flex flex-col items-center justify-center gap-1">
                                <span class="text-base lato-black {{ $notaColor }}">
                                    {{ $score->score_label }}
                                </span>

                                {{-- Badge de classificação com ícone --}}
                                @if ($badgeCls && $badgeLabel)
                                    <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[10px] lato-bold {{ $badgeCls }}">
                                        @if ($score->average_score >= 8)
                                            <x-lucide-star class="w-2.5 h-2.5" />
                                        @elseif ($score->average_score >= 6)
                                            <x-lucide-minus-circle class="w-2.5 h-2.5" />
                                        @else
                                            <x-lucide-alert-triangle class="w-2.5 h-2.5" />
                                        @endif
                                        {{ $badgeLabel }}
                                    </span>
                                @endif

                                {{-- Mini barra de progresso (desktop) --}}
                                <div class="hidden sm:block w-full h-1 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $barColor }} transition-all"
                                         style="width: {{ $barWidth }}%"></div>
                                </div>
                            </div>

                        </div>
                    @endforeach
                </div>

                {{-- Rodapé --}}
                <div class="px-5 py-3 bg-slate-50 dark:bg-slate-700/30 border-t border-slate-100 dark:border-slate-700
                            flex items-center justify-between text-xs lato-regular text-slate-400">
                    <span class="flex items-center gap-1.5">
                        <x-lucide-users class="w-3.5 h-3.5" />
                        {{ $this->ranking->count() }} {{ $this->ranking->count() === 1 ? 'gerente' : 'gerentes' }} no ranking
                    </span>
                    <span class="hidden sm:flex items-center gap-1.5">
                        <x-lucide-refresh-cw class="w-3 h-3" />
                        Nota: escala 0 – 10 · atualizado a cada resposta
                    </span>
                </div>

            </div>

            {{-- Legenda de cores --}}
            <div class="mt-3 flex flex-wrap items-center gap-4 text-xs lato-regular text-slate-400">
                <span class="flex items-center gap-1.5">
                    <x-lucide-star class="w-3.5 h-3.5 text-emerald-500" />
                    ≥ 8,0 — Excelente
                </span>
                <span class="flex items-center gap-1.5">
                    <x-lucide-minus-circle class="w-3.5 h-3.5 text-amber-500" />
                    6,0 – 7,9 — Regular
                </span>
                <span class="flex items-center gap-1.5">
                    <x-lucide-alert-triangle class="w-3.5 h-3.5 text-red-500" />
                    &lt; 6,0 — Atenção
                </span>
                <span class="flex items-center gap-1.5">
                    <x-lucide-circle-dashed class="w-3.5 h-3.5 text-slate-300" />
                    — Sem respostas
                </span>
            </div>
        @endif
    </div>

</div>
