<div class="p-4 sm:p-6 lg:p-8">
{{-- v2 --}}

    {{-- ── ApexCharts CDN (gráfico de evolução) ── --}}
    @once
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endonce

    {{-- ═══════════════════════════════════════════════════════════════
         CABEÇALHO
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white lato-black tracking-tight">
                Avaliação de Desempenho
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5 lato-regular">
                @if(auth()->user()->isEmployee())
                    Realize sua autoavaliação e acompanhe seu resultado após a publicação
                @elseif(auth()->user()->isGerente())
                    Avalie seus colaboradores e acompanhe o histórico de desempenho
                @else
                    Gerencie ciclos, critérios, calibre notas e acompanhe o ranking dos gestores
                @endif
            </p>
        </div>

        @if(auth()->user()->isRhOuDp() && $activeTab === 'ciclos')
            <button type="button" wire:click="abrirCicloModal"
                    class="flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                           text-sm font-medium px-4 py-2.5 rounded-lg shadow-md shadow-blue-500/20 transition cursor-pointer lato-bold">
                <x-lucide-circle-plus class="w-4 h-4" />
                Novo Ciclo
            </button>
        @endif

        @if(auth()->user()->isRhOuDp() && $activeTab === 'criterios')
            <button type="button" wire:click="abrirCriterioModal"
                    class="inline-flex items-center gap-2 px-5 py-2 rounded-xl cursor-pointer text-sm lato-bold bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20 text-white transition disabled:opacity-60">
                <x-lucide-circle-plus class="w-4 h-4" />
                Novo Critério
            </button>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         CARDS RÁPIDOS — GERENTE
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isGerente())
    <div class="mt-6 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">

        {{-- Nota de satisfação --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">Satisfação do dept.</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mt-1 lato-black">
                    {{ $this->satisfactionScore !== null ? number_format($this->satisfactionScore, 1) : '—' }}
                    @if($this->satisfactionScore !== null)<span class="text-base font-normal text-slate-400">/10</span>@endif
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                <x-lucide-star class="w-5 h-5 text-amber-500" />
            </div>
        </div>

        {{-- Status ciclo atual --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">Ciclo atual</p>
                @if($this->activeCycle)
                    <p class="text-sm font-bold text-slate-800 dark:text-white mt-1 lato-black leading-tight">{{ Str::limit($this->activeCycle->name, 20) }}</p>
                    <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full mt-1 inline-block {{ $this->myEvaluation?->status_color ?? 'bg-slate-100 text-slate-500' }}">
                        {{ $this->myEvaluation?->status_label ?? 'Não iniciada' }}
                    </span>
                @else
                    <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mt-1">Nenhum ativo</p>
                @endif
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                <x-lucide-clipboard-list class="w-5 h-5 text-indigo-500" />
            </div>
        </div>

        {{-- Colaboradores avaliados --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">Avaliados</p>
                <p class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white mt-1 lato-black">
                    {{ $this->evaluationProgress['evaluated'] }}<span class="text-base font-normal text-slate-400">/{{ $this->evaluationProgress['total'] }}</span>
                </p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                <x-lucide-users class="w-5 h-5 text-emerald-500" />
            </div>
        </div>

        {{-- Progresso % --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border border-slate-200 dark:border-slate-700">
            @php $prog = min(100, $this->evaluationProgress['pct']); @endphp
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">Progresso</p>
                <span class="text-xs lato-bold text-indigo-600">{{ $prog }}%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5 overflow-hidden">
                <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-500"
                     style="width: {{ $prog }}%"></div>
            </div>
            <p class="text-[10px] text-slate-400 lato-regular mt-2">
                @if($prog >= 100)
                    <span class="text-emerald-500">Todos avaliados!</span>
                @elseif($this->activeCycle)
                    {{ max(0, $this->evaluationProgress['total'] - $this->evaluationProgress['evaluated']) }} restante(s)
                @else
                    Sem ciclo ativo
                @endif
            </p>
        </div>

    </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         TABS
    ═══════════════════════════════════════════════════════════════════ --}}
    @php
        $tabClass = fn($tab) => 'flex items-center gap-1.5 px-3.5 py-2 text-xs lato-bold rounded-lg transition cursor-pointer ' .
            ($activeTab === $tab
                ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900'
                : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200');
    @endphp
    <div class="mt-6 flex items-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 w-fit flex-wrap">
        @if(auth()->user()->isEmployee())
            <button type="button" wire:click="$set('activeTab', 'autoavaliacao')" class="{{ $tabClass('autoavaliacao') }}">
                <x-lucide-pencil-line class="w-3.5 h-3.5" /> Minha Autoavaliação
            </button>
            <button type="button" wire:click="$set('activeTab', 'resultado')" class="{{ $tabClass('resultado') }}">
                <x-lucide-bar-chart-2 class="w-3.5 h-3.5" /> Meu Resultado
            </button>
        @elseif(auth()->user()->isGerente())
            <button type="button" wire:click="$set('activeTab', 'dashboard')" class="{{ $tabClass('dashboard') }}">
                <x-lucide-layout-dashboard class="w-3.5 h-3.5" /> Dashboard
            </button>
            <button type="button" wire:click="$set('activeTab', 'avaliacao')" class="{{ $tabClass('avaliacao') }}">
                <x-lucide-clipboard-check class="w-3.5 h-3.5" /> Avaliação
            </button>
            <button type="button" wire:click="$set('activeTab', 'historico')" class="{{ $tabClass('historico') }}">
                <x-lucide-clock class="w-3.5 h-3.5" /> Histórico
            </button>
        @else
            <button type="button" wire:click="$set('activeTab', 'ciclos')" class="{{ $tabClass('ciclos') }}">
                <x-lucide-calendar class="w-3.5 h-3.5" /> Ciclos
            </button>
            <button type="button" wire:click="$set('activeTab', 'criterios')" class="{{ $tabClass('criterios') }}">
                <x-lucide-award class="w-3.5 h-3.5" /> Critérios
            </button>
            <button type="button" wire:click="$set('activeTab', 'ranking')" class="{{ $tabClass('ranking') }}">
                <x-lucide-trophy class="w-3.5 h-3.5" /> Ranking
            </button>
            <button type="button" wire:click="$set('activeTab', 'avaliacoes')" class="{{ $tabClass('avaliacoes') }}">
                <x-lucide-clipboard-list class="w-3.5 h-3.5" /> Avaliações
            </button>
            <button type="button" wire:click="$set('activeTab', 'calibracao')" class="{{ $tabClass('calibracao') }}">
                <x-lucide-sliders-horizontal class="w-3.5 h-3.5" /> Calibração
            </button>
        @endif
    </div>


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: DASHBOARD (GERENTE)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isGerente() && $activeTab === 'dashboard')
    <div class="mt-6 grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Ciclo ativo / CTA --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6">
            @if($this->activeCycle)
                <div class="flex items-start justify-between gap-4">
                    <div>
                        <span class="text-[10px] lato-bold px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">
                            Ciclo ativo
                        </span>
                        <h2 class="text-lg font-bold text-slate-800 dark:text-white lato-black mt-2">{{ $this->activeCycle->name }}</h2>
                        <p class="text-sm text-slate-400 lato-regular mt-1">
                            {{ $this->activeCycle->period_label }}
                        </p>
                        @if($this->activeCycle->description)
                            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-2 leading-relaxed">{{ $this->activeCycle->description }}</p>
                        @endif
                    </div>
                    <span class="text-[11px] lato-bold px-3 py-1.5 rounded-full shrink-0 {{ $this->myEvaluation?->status_color ?? 'bg-slate-100 text-slate-500' }}">
                        {{ $this->myEvaluation?->status_label ?? 'Não iniciada' }}
                    </span>
                </div>

                <div class="mt-6">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-slate-500 lato-regular">Progresso da avaliação</span>
                        <span class="text-xs lato-bold text-indigo-600">{{ $this->evaluationProgress['evaluated'] }}/{{ $this->evaluationProgress['total'] }} colaboradores</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-3">
                        <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-3 rounded-full transition-all duration-700"
                             style="width: {{ min(100, $this->evaluationProgress['pct']) }}%; max-width: 100%"></div>
                    </div>
                </div>

                <div class="mt-5 flex gap-3">
                    @if($this->myEvaluation?->status !== 'completed')
                        <button type="button" wire:click="$set('activeTab', 'avaliacao')"
                                class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm lato-bold px-5 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                            <x-lucide-clipboard-check class="w-4 h-4" />
                            {{ $this->myEvaluation?->status === 'in_progress' ? 'Continuar avaliação' : 'Iniciar avaliação' }}
                        </button>
                    @else
                        <button type="button" wire:click="$set('activeTab', 'historico')"
                                class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm lato-bold px-5 py-2.5 rounded-xl shadow-sm transition cursor-pointer">
                            <x-lucide-check-circle class="w-4 h-4" />
                            Ver resultados
                        </button>
                    @endif
                </div>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                        <x-lucide-calendar-off class="w-6 h-6 text-slate-400" />
                    </div>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300 lato-bold">Nenhum ciclo ativo</p>
                    <p class="text-xs text-slate-400 lato-regular mt-1">Aguarde o RH/DP ativar um novo ciclo de avaliação.</p>
                </div>
            @endif
        </div>

        {{-- Nota de satisfação --}}
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 flex flex-col">
            <div class="flex items-center gap-2 mb-4">
                <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                    <x-lucide-star class="w-4 h-4 text-amber-500" />
                </div>
                <span class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold">Nota de satisfação</span>
            </div>

            @if($this->satisfactionScore !== null)
                <div class="flex-1 flex flex-col items-center justify-center">
                    <p class="text-6xl font-black text-slate-800 dark:text-white lato-black">
                        {{ number_format($this->satisfactionScore, 1) }}
                    </p>
                    <p class="text-sm text-slate-400 lato-regular mt-1">de 10 pontos</p>

                    {{-- Barra visual --}}
                    <div class="w-full mt-4 bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                        <div class="h-2 rounded-full bg-gradient-to-r from-amber-400 to-amber-500 transition-all"
                             style="width: {{ min(100, ($this->satisfactionScore / 10) * 100) }}%"></div>
                    </div>
                    <p class="text-[10px] text-slate-400 lato-regular mt-2 text-center">
                        Média das pesquisas de satisfação do seu departamento
                    </p>
                </div>
            @else
                <div class="flex-1 flex flex-col items-center justify-center text-center">
                    <x-lucide-bar-chart-2 class="w-10 h-10 text-slate-200 dark:text-slate-600 mb-2" />
                    <p class="text-sm text-slate-400 lato-regular">Nenhuma pesquisa respondida ainda.</p>
                </div>
            @endif
        </div>

    </div>

    {{-- ═══ Como meus funcionários me avaliam (via Pesquisas de Clima) ════════ --}}
    @php
        $survStats   = $this->managerSurveyStats;
        $survResults = $this->managerSurveyResults;
    @endphp
    <div class="mt-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6">
        <div class="flex items-center gap-3 mb-5">
            <div class="w-9 h-9 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center">
                <x-lucide-user-check class="w-5 h-5 text-indigo-600" />
            </div>
            <div>
                <p class="text-sm font-bold text-slate-800 dark:text-white lato-black">Como meus funcionários me avaliam</p>
                <p class="text-xs text-slate-400 lato-regular">
                    Perguntas de Avaliação do Gestor nas pesquisas de clima
                    @if($survStats['count'] > 0)
                        &nbsp;·&nbsp;{{ $survStats['count'] }} {{ $survStats['count'] === 1 ? 'resposta coletada' : 'respostas coletadas' }}
                    @endif
                </p>
            </div>
            @if($survStats['avg'] !== null)
                <div class="ml-auto text-right shrink-0">
                    <p class="text-3xl font-black text-indigo-600 dark:text-indigo-400 lato-black">{{ number_format($survStats['avg'], 1) }}</p>
                    <p class="text-[10px] text-slate-400 lato-regular">de 10 pontos</p>
                </div>
            @endif
        </div>

        @if($survResults->isEmpty())
            <div class="flex flex-col items-center justify-center py-8 text-center">
                <x-lucide-inbox class="w-10 h-10 text-slate-200 dark:text-slate-600 mb-2" />
                <p class="text-sm text-slate-400 lato-regular">Nenhuma avaliação de gestor disponível ainda.</p>
                <p class="text-xs text-slate-400 lato-regular mt-1 max-w-xs">
                    O RH/DP precisa marcar perguntas como
                    <span class="text-indigo-500 lato-bold">Avaliação do Gestor</span>
                    nas pesquisas de clima para que os resultados apareçam aqui.
                </p>
            </div>
        @else
            @if($survStats['avg'] !== null)
                {{-- Barra de média geral --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-slate-500 lato-regular">Média geral (escala 0–10)</span>
                        <span class="text-xs font-bold text-indigo-600 lato-bold">{{ number_format($survStats['avg'], 1) }}/10</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-2 rounded-full transition-all"
                             style="width: {{ min(100, ($survStats['avg'] / 10) * 100) }}%"></div>
                    </div>
                </div>
            @endif

            {{-- Por pergunta --}}
            <div class="space-y-3">
                <p class="text-xs font-semibold text-slate-600 dark:text-slate-400 lato-bold uppercase tracking-wide">Por pergunta</p>

                @foreach($survResults as $result)
                    <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-4 space-y-2.5">
                        <div class="flex items-start justify-between gap-3">
                            <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 flex-1 leading-relaxed">
                                {{ $result->question }}
                            </p>
                            <div class="flex items-center gap-1.5 shrink-0">
                                @if($result->type === 'escala')
                                    <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full bg-blue-50 text-blue-700 border border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800">Escala</span>
                                @elseif($result->type === 'multipla_escolha')
                                    <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full bg-violet-50 text-violet-700 border border-violet-200 dark:bg-violet-900/20 dark:text-violet-300 dark:border-violet-800">M. escolha</span>
                                @else
                                    <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full bg-amber-50 text-amber-700 border border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800">Texto livre</span>
                                @endif
                                <span class="text-[10px] text-slate-400 lato-regular">{{ $result->response_count }} resp.</span>
                            </div>
                        </div>

                        @if($result->avg_score !== null)
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-slate-200 dark:bg-slate-600 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all
                                                {{ $result->avg_score >= 7 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : ($result->avg_score >= 5 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-red-400 to-red-500') }}"
                                         style="width: {{ min(100, ($result->avg_score / 10) * 100) }}%"></div>
                                </div>
                                <span class="text-sm lato-black font-bold shrink-0
                                             {{ $result->avg_score >= 7 ? 'text-emerald-600' : ($result->avg_score >= 5 ? 'text-amber-600' : 'text-red-500') }}">
                                    {{ number_format($result->avg_score, 1) }}<span class="text-[10px] text-slate-400 font-normal lato-regular">/10</span>
                                </span>
                            </div>
                        @endif

                        @if($result->type === 'multipla_escolha' && ! empty($result->distribution))
                            <div class="space-y-1 mt-1">
                                @php $maxCount = max(array_values($result->distribution) ?: [1]); @endphp
                                @foreach($result->distribution as $opcao => $count)
                                    @if($count > 0)
                                    <div class="flex items-center gap-2">
                                        <span class="text-[10px] lato-regular text-slate-500 dark:text-slate-400 truncate w-28 shrink-0">{{ $opcao }}</span>
                                        <div class="flex-1 bg-slate-100 dark:bg-slate-600 rounded-full h-1">
                                            <div class="h-1 rounded-full bg-violet-400 transition-all"
                                                 style="width: {{ $maxCount > 0 ? round(($count / $maxCount) * 100) : 0 }}%"></div>
                                        </div>
                                        <span class="text-[10px] lato-bold text-slate-500 dark:text-slate-400 shrink-0 w-4 text-right">{{ $count }}</span>
                                    </div>
                                    @endif
                                @endforeach
                            </div>
                        @endif

                        @if($result->comments->isNotEmpty())
                            <div class="space-y-1.5 pt-0.5">
                                @foreach($result->comments->take(3) as $comment)
                                    <div class="bg-white dark:bg-slate-700 rounded-lg px-3 py-2 border border-slate-100 dark:border-slate-600">
                                        <p class="text-[11px] text-slate-600 dark:text-slate-300 lato-regular italic">"{{ $comment }}"</p>
                                    </div>
                                @endforeach
                                @if($result->comments->count() > 3)
                                    <p class="text-[10px] text-slate-400 lato-regular pl-1">
                                        + {{ $result->comments->count() - 3 }} comentário(s) não exibido(s)
                                    </p>
                                @endif
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: AVALIAÇÃO (GERENTE)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isGerente() && $activeTab === 'avaliacao')
    <div class="mt-6">

        @if(!$this->activeCycle)
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-12 text-center">
                <x-lucide-calendar-off class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
                <p class="text-slate-500 dark:text-slate-400 lato-regular">Nenhum ciclo de avaliação ativo no momento.</p>
            </div>
        @elseif($this->myEvaluation?->status === 'completed')
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-2xl p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                    <x-lucide-check-circle-2 class="w-6 h-6 text-emerald-600" />
                </div>
                <div>
                    <p class="text-sm font-bold text-emerald-800 dark:text-emerald-300 lato-bold">Avaliação finalizada!</p>
                    <p class="text-xs text-emerald-600 dark:text-emerald-400 lato-regular mt-0.5">
                        Concluída em {{ $this->myEvaluation->completed_at?->format('d/m/Y \à\s H:i') }}. Acesse o histórico para ver os resultados.
                    </p>
                </div>
                <button type="button" wire:click="$set('activeTab', 'historico')"
                        class="ml-auto text-xs lato-bold text-emerald-700 hover:text-emerald-900 underline cursor-pointer shrink-0">
                    Ver histórico
                </button>
            </div>
        @else

            {{-- Barra de progresso + busca --}}
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4 flex flex-col sm:flex-row sm:items-center gap-3">
                <div class="flex-1">
                    <div class="flex items-center justify-between mb-1">
                        <span class="text-xs text-slate-500 lato-regular">
                            {{ $this->evaluationProgress['evaluated'] }} de {{ $this->evaluationProgress['total'] }} colaboradores avaliados
                        </span>
                        <span class="text-xs lato-bold text-indigo-600">{{ $this->evaluationProgress['pct'] }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-indigo-500 h-2 rounded-full transition-all"
                             style="width: {{ $this->evaluationProgress['pct'] }}%"></div>
                    </div>
                </div>

                <div class="relative sm:w-64 shrink-0">
                    <x-lucide-search class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    <input type="text" wire:model.live.debounce.300ms="employeeSearch"
                           placeholder="Buscar colaborador..."
                           class="w-full pl-9 pr-3 py-2 text-xs border border-slate-200 dark:border-slate-600 rounded-xl
                                  bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                </div>
            </div>

            {{-- Lista de colaboradores --}}
            <div class="mt-3 space-y-2">
                @forelse($this->employees as $emp)
                    @php $isCompleted = in_array($emp->id, $this->completedEmployeeIds); @endphp

                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                        {{-- Linha do colaborador --}}
                        <button type="button" wire:click="openEmployee({{ $emp->id }})"
                                class="w-full flex items-center gap-3 px-4 py-3.5 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition cursor-pointer text-left">

                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-400 to-slate-600
                                        flex items-center justify-center text-white text-xs lato-black shrink-0">
                                {{ mb_strtoupper(mb_substr($emp->name, 0, 1)) }}
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $emp->name }}</p>
                                <p class="text-[11px] text-slate-400 lato-regular truncate">{{ $emp->position ?? '—' }}</p>
                            </div>

                            @if($isCompleted)
                                <span class="shrink-0 flex items-center gap-1 text-[10px] lato-bold text-emerald-600 bg-emerald-50 dark:bg-emerald-900/30 px-2 py-0.5 rounded-full">
                                    <x-lucide-check class="w-3 h-3" /> Avaliado
                                </span>
                            @else
                                <span class="shrink-0 text-[10px] lato-bold text-amber-600 bg-amber-50 dark:bg-amber-900/20 px-2 py-0.5 rounded-full">
                                    Pendente
                                </span>
                            @endif

                            <x-lucide-chevron-down class="w-4 h-4 text-slate-400 shrink-0 transition-transform {{ $selectedEmployeeId === $emp->id ? 'rotate-180' : '' }}" />
                        </button>

                        {{-- Painel de avaliação (critérios) --}}
                        @if($selectedEmployeeId === $emp->id)
                        <div class="border-t border-slate-100 dark:border-slate-700 px-4 py-4 bg-slate-50/50 dark:bg-slate-800/50">
                            <p class="text-xs text-slate-400 lato-regular mb-4">
                                <x-lucide-info class="w-3 h-3 inline mr-1" />
                                Notas salvas automaticamente. Clique nas estrelas para avaliar cada critério.
                            </p>

                            <div class="space-y-5">
                                @foreach($this->criteria as $criterion)
                                <div class="flex flex-col sm:flex-row sm:items-start gap-3"
                                     wire:key="criterion-{{ $criterion->id }}">

                                    {{-- Critério info --}}
                                    <div class="sm:w-52 shrink-0">
                                        <div class="flex items-center gap-1.5 flex-wrap">
                                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $criterion->name }}</p>
                                            @if(($criterion->weight ?? 1.0) != 1.0)
                                            <span class="text-[9px] px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 border border-amber-200 lato-bold">×{{ number_format($criterion->weight, 1) }}</span>
                                            @endif
                                        </div>
                                        @if($criterion->description)
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5 leading-relaxed">{{ $criterion->description }}</p>
                                        @endif
                                    </div>

                                    {{-- Input por tipo de resposta --}}
                                    <div class="flex flex-col gap-2 flex-1">

                                        {{-- Escala 1-5 com estrelas --}}
                                        @if(($criterion->response_type ?? 'scale') === 'scale')
                                        <div class="flex gap-2" x-data="{ hover: 0 }">
                                            @for($s = 1; $s <= 5; $s++)
                                            <button type="button"
                                                    wire:click="saveScore({{ $criterion->id }}, {{ $s }})"
                                                    @mouseenter="hover = {{ $s }}"
                                                    @mouseleave="hover = 0"
                                                    :class="(hover > 0 ? hover >= {{ $s }} : (($wire.scores[{{ $criterion->id }}] ?? 0) >= {{ $s }})) ? 'text-amber-400 scale-110' : 'text-slate-200 dark:text-slate-600'"
                                                    class="transition-all duration-150 cursor-pointer hover:scale-125 focus:outline-none"
                                                    title="Nota {{ $s }}"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="saveScore" class="flex items-center gap-1.5">
                                                    <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                                </span>
                                                <span wire:loading wire:target="saveScore" class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                </span>
                                            </button>
                                            @endfor
                                            @if(isset($scores[$criterion->id]))
                                                <span class="ml-1 self-center text-xs lato-bold text-slate-500 dark:text-slate-400">
                                                    {{ $scores[$criterion->id] }}/5
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Sim / Não --}}
                                        @elseif(($criterion->response_type ?? 'scale') === 'boolean')
                                        <div class="flex gap-3">
                                            <button type="button" wire:click="saveScore({{ $criterion->id }}, 0)"
                                                    class="flex-1 py-2 rounded-xl text-xs lato-bold border transition cursor-pointer
                                                           {{ ($scores[$criterion->id] ?? null) === 0
                                                              ? 'bg-red-500 border-red-500 text-white'
                                                              : 'border-slate-200 dark:border-slate-600 text-slate-500 hover:border-red-400 hover:text-red-500' }}"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="saveScore" class="flex items-center gap-1.5">
                                                    Não
                                                </span>
                                                <span wire:loading wire:target="saveScore" class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                </span>
                                            </button>
                                            <button type="button" wire:click="saveScore({{ $criterion->id }}, 1)"
                                                    class="flex-1 py-2 rounded-xl text-xs lato-bold border transition cursor-pointer
                                                           {{ ($scores[$criterion->id] ?? null) === 1
                                                              ? 'bg-emerald-500 border-emerald-500 text-white'
                                                              : 'border-slate-200 dark:border-slate-600 text-slate-500 hover:border-emerald-400 hover:text-emerald-500' }}"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="saveScore" class="flex items-center gap-1.5">
                                                    Sim
                                                </span>
                                                <span wire:loading wire:target="saveScore" class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                </span>
                                            </button>
                                        </div>

                                        {{-- Múltipla escolha --}}
                                        @elseif(($criterion->response_type ?? 'scale') === 'multiple_choice')
                                        <div class="flex flex-wrap gap-2">
                                            @foreach(($criterion->options ?? []) as $idx => $option)
                                            <button type="button" wire:click="saveScore({{ $criterion->id }}, {{ $idx }})"
                                                    class="px-3 py-1.5 rounded-xl text-xs lato-bold border transition cursor-pointer
                                                           {{ ($scores[$criterion->id] ?? null) == $idx
                                                              ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm'
                                                              : 'border-slate-200 dark:border-slate-600 text-slate-500 hover:border-indigo-400 hover:text-indigo-600' }}"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="saveScore" class="flex items-center gap-1.5">
                                                    {{ $option }}
                                                </span>
                                                <span wire:loading wire:target="saveScore" class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                </span>
                                            </button>
                                            @endforeach
                                        </div>
                                        @endif

                                        {{-- Comentário --}}
                                        <textarea wire:model.lazy="entryComments.{{ $criterion->id }}"
                                                  @change.stop="$wire.saveComment({{ $criterion->id }})"
                                                  rows="2"
                                                  placeholder="Comentário opcional..."
                                                  class="w-full px-3 py-1.5 text-xs lato-regular rounded-lg border border-slate-200 dark:border-slate-600
                                                         bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200
                                                         placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 resize-none transition"
                                        ></textarea>
                                    </div>

                                </div>
                                @endforeach
                            </div>

                        </div>
                        @endif
                    </div>
                @empty
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 text-center">
                        <x-lucide-users class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                        <p class="text-sm text-slate-400 lato-regular">
                            {{ $employeeSearch ? 'Nenhum colaborador encontrado.' : 'Nenhum colaborador no seu departamento.' }}
                        </p>
                    </div>
                @endforelse
            </div>

            {{-- Botão finalizar --}}
            @if($this->allDeptEmployees->count() > 0)
            <div class="mt-5 flex items-center justify-between bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-4">
                <div>
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">Finalizar avaliação</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">
                        Após finalizar, os dados não poderão ser editados.
                        @if($this->evaluationProgress['evaluated'] < $this->evaluationProgress['total'])
                            <span class="text-amber-500">{{ $this->evaluationProgress['total'] - $this->evaluationProgress['evaluated'] }} colaborador(es) sem avaliação completa.</span>
                        @endif
                    </p>
                </div>
                @php $todosAvaliados = $this->evaluationProgress['evaluated'] >= $this->evaluationProgress['total'] && $this->evaluationProgress['total'] > 0; @endphp
                <button type="button" wire:click="requestComplete"
                        @if(!$todosAvaliados) disabled title="Avalie todos os colaboradores antes de finalizar" @endif
                        class="flex items-center gap-2 text-sm lato-bold px-5 py-2.5 rounded-xl shadow-sm transition shrink-0
                               {{ $todosAvaliados
                                   ? 'bg-emerald-600 hover:bg-emerald-700 text-white cursor-pointer'
                                   : 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 cursor-not-allowed' }}">
                    <x-lucide-check-circle class="w-4 h-4" />
                    Finalizar
                </button>
            </div>
            @endif

        @endif
    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: HISTÓRICO (GERENTE)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isGerente() && $activeTab === 'historico')
    <div class="mt-6">

        {{-- Filtros --}}
        <div class="mb-5 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700
                    rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row items-stretch sm:items-center gap-3">

            {{-- Select: Ano --}}
            <div class="relative flex-1 min-w-[140px]">
                <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <select wire:model.live="historyYearFilter"
                        class="w-full appearance-none pl-9 pr-8 py-2.5 text-sm lato-bold rounded-xl
                               bg-slate-50 dark:bg-slate-700
                               text-slate-700 dark:text-slate-200
                               border border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                               cursor-pointer transition">
                    <option value="">Todos os anos</option>
                    @foreach($this->availableYears as $y)
                        <option value="{{ $y }}">{{ $y }}</option>
                    @endforeach
                </select>
                <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            </div>

            {{-- Divisor vertical (visível apenas sm+) --}}
            <div class="hidden sm:block w-px h-6 bg-slate-200 dark:bg-slate-700 shrink-0"></div>

            {{-- Select: Ciclo --}}
            <div class="relative flex-1 min-w-[180px]">
                <x-lucide-refresh-cw class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <select wire:model.live="historyCycleFilter"
                        class="w-full appearance-none pl-9 pr-8 py-2.5 text-sm lato-bold rounded-xl
                               bg-slate-50 dark:bg-slate-700
                               text-slate-700 dark:text-slate-200
                               border border-slate-200 dark:border-slate-600
                               focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                               cursor-pointer transition">
                    <option value="">Todos os ciclos</option>
                    @foreach($this->allCycles as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
                <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            </div>

            {{-- Botão limpar filtros (só aparece quando há filtro ativo) --}}
            @if($historyYearFilter || $historyCycleFilter)
                <button type="button"
                        wire:click="$set('historyYearFilter', null); $set('historyCycleFilter', null)"
                        class="flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs lato-bold rounded-xl
                               bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600
                               text-slate-500 dark:text-slate-300 transition cursor-pointer whitespace-nowrap shrink-0">
                    <x-lucide-x class="w-3.5 h-3.5" />
                    Limpar
                </button>
            @endif

        </div>

        @forelse($this->historyEvaluations as $eval)
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl mb-3 overflow-hidden">

            {{-- Header --}}
            <button type="button" wire:click="$set('expandedHistoryId', {{ $expandedHistoryId === $eval->id ? 'null' : $eval->id }})"
                    class="w-full flex items-center gap-3 px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-700/40 transition cursor-pointer text-left">
                <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                    <x-lucide-clipboard-check class="w-4 h-4 text-indigo-600" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $eval->cycle->name }}</p>
                    <p class="text-[11px] text-slate-400 lato-regular">{{ $eval->cycle->period_label }}</p>
                </div>
                <div class="text-right shrink-0">
                    @php $avg = $eval->average_score; @endphp
                    @if($avg !== null)
                        <p class="text-lg lato-black font-bold text-indigo-600">{{ $avg }}</p>
                        <p class="text-[10px] text-slate-400 lato-regular">média geral</p>
                    @endif
                </div>
                <span class="text-[10px] lato-bold px-2.5 py-1 rounded-full {{ $eval->status_color }} shrink-0">{{ $eval->status_label }}</span>
                <x-lucide-chevron-down class="w-4 h-4 text-slate-400 shrink-0 transition-transform {{ $expandedHistoryId === $eval->id ? 'rotate-180' : '' }}" />
            </button>

            {{-- Detalhe --}}
            @if($expandedHistoryId === $eval->id)
            <div class="border-t border-slate-100 dark:border-slate-700 px-5 py-4">

                @php
                    $grouped = $eval->entries->groupBy('employee_id');
                    $avgByCriterion = $eval->entries->groupBy('criterion_id')
                        ->map(fn($g) => round($g->avg(fn($e) => $e->effectiveScore), 1));
                @endphp

                {{-- Média por critério --}}
                <div class="mb-5">
                    <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wide">Média por critério</p>
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                        @foreach($eval->entries->pluck('criterion')->unique('id') as $crit)
                            @php $avg = $avgByCriterion[$crit->id] ?? null; @endphp
                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                                <p class="text-xs lato-bold text-slate-600 dark:text-slate-300 truncate">{{ $crit->name }}</p>
                                <div class="flex items-center gap-1.5 mt-1">
                                    <span class="text-lg lato-black font-bold {{ $avg >= 4 ? 'text-emerald-600' : ($avg >= 3 ? 'text-amber-600' : 'text-red-500') }}">
                                        {{ $avg ?? '—' }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">/5</span>
                                </div>
                                {{-- mini star bar --}}
                                <div class="flex gap-0.5 mt-1">
                                    @for($s = 1; $s <= 5; $s++)
                                        <div class="h-1 flex-1 rounded-full {{ $s <= round($avg ?? 0) ? 'bg-amber-400' : 'bg-slate-200 dark:bg-slate-600' }}"></div>
                                    @endfor
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Por colaborador --}}
                <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wide">Por colaborador</p>
                <div class="space-y-3">
                    @foreach($grouped as $empId => $entries)
                        @php
                            $emp = $entries->first()->employee;
                            $empAvg = round($entries->avg(fn($e) => $e->effectiveScore), 1);
                        @endphp
                        <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-[10px] lato-black shrink-0">
                                        {{ mb_strtoupper(mb_substr($emp->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $emp->name }}</span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs lato-black font-bold {{ $empAvg >= 4 ? 'text-emerald-600' : ($empAvg >= 3 ? 'text-amber-600' : 'text-red-500') }}">
                                        {{ $empAvg }}/5
                                    </span>
                                    <button type="button" wire:click="openEvolution({{ $empId }})"
                                            title="Ver histórico de evolução"
                                            class="w-6 h-6 flex items-center justify-center rounded-md
                                                   {{ $evolutionEmployeeId === $empId ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-slate-600 text-slate-400 hover:text-indigo-600' }}
                                                   border border-slate-200 dark:border-slate-600 transition cursor-pointer">
                                        <x-lucide-trending-up class="w-3 h-3" />
                                    </button>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                                @foreach($entries as $entry)
                                    @php $effectiveScore = $entry->effectiveScore; $isCalibrated = $entry->calibrated_score !== null; @endphp
                                    <div class="flex items-center gap-1.5 text-[10px] lato-regular text-slate-500 dark:text-slate-400">
                                        <div class="flex gap-0.5">
                                            @for($s = 1; $s <= 5; $s++)
                                                <div class="w-2 h-2 rounded-full {{ $s <= $effectiveScore ? ($isCalibrated ? 'bg-indigo-600' : 'bg-amber-400') : 'bg-slate-200 dark:bg-slate-600' }}"></div>
                                            @endfor
                                        </div>
                                        <span class="truncate">{{ $entry->criterion->name }}</span>
                                        @if($isCalibrated)
                                            <span title="Nota calibrada pelo RH" class="text-indigo-500">✦</span>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
            @endif
        </div>
        @empty
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-10 text-center">
                <x-lucide-clock class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
                <p class="text-sm text-slate-400 lato-regular">Nenhuma avaliação finalizada encontrada.</p>
            </div>
        @endforelse

        {{-- ── Gráfico de evolução do colaborador ──────────────────── --}}
        @if($evolutionEmployeeId && count($this->employeeEvolutionData) > 0)
        @php $evoData = $this->employeeEvolutionData; @endphp
        <div class="mt-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5"
             x-data x-init="
            (function init() {
                if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }

                const data       = {{ json_encode($evoData) }};
                const labels     = data.map(d => d.cycle_name);
                const scores     = data.map(d => d.score);
                const selfScores = data.map(d => d.self_score);
                const hasSelf    = selfScores.some(s => s !== null);

                const series = [{ name: 'Nota do Gerente', data: scores }];
                if (hasSelf) series.push({ name: 'Autoavaliação', data: selfScores });

                const isDark = document.documentElement.classList.contains('dark');

                new ApexCharts($refs.evoChart, {
                    series,
                    chart: { type: 'line', height: 200, toolbar: { show: false }, background: 'transparent', animations: { enabled: true, speed: 600 } },
                    stroke: { curve: 'smooth', width: [3, 2], dashArray: [0, 5] },
                    colors: ['#6366f1', '#0891b2'],
                    markers: { size: 5, strokeWidth: 0 },
                    xaxis: { categories: labels, labels: { style: { fontSize: '10px', colors: isDark ? '#94a3b8' : '#64748b' } } },
                    yaxis: { min: 1, max: 5, tickAmount: 4, labels: { style: { fontSize: '10px', colors: isDark ? '#94a3b8' : '#64748b' }, formatter: v => v ? v.toFixed(1) : '' } },
                    grid: { borderColor: isDark ? '#334155' : '#f1f5f9', strokeDashArray: 4 },
                    tooltip: { x: { show: true }, theme: isDark ? 'dark' : 'light' },
                    legend: { show: true, position: 'bottom', fontSize: '11px', fontFamily: 'inherit' },
                    theme: { mode: isDark ? 'dark' : 'light' },
                }).render();
            })();
        ">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <p class="text-sm lato-bold text-slate-800 dark:text-white">
                        Evolução — {{ $this->evolutionEmployee?->name ?? 'Colaborador' }}
                    </p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Nota ponderada ao longo dos ciclos</p>
                </div>
                <button type="button" wire:click="$set('evolutionEmployeeId', null)"
                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
            <div x-ref="evoChart"></div>
        </div>
        @elseif($evolutionEmployeeId && count($this->employeeEvolutionData) === 0)
        <div class="mt-5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 text-center">
            <p class="text-sm text-slate-400 lato-regular">Dados insuficientes para exibir a evolução. São necessários ao menos 2 ciclos.</p>
        </div>
        @endif

    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: CICLOS (ADMIN)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isRhOuDp() && $activeTab === 'ciclos')
    <div class="mt-6 space-y-3">
        @forelse($this->cycles as $cycle)
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-4 flex flex-col sm:flex-row sm:items-center gap-3">
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-white">{{ $cycle->name }}</h3>
                    <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full {{ $cycle->status_color }}">{{ $cycle->status_label }}</span>
                </div>
                <p class="text-[11px] text-slate-400 lato-regular mt-1">
                    <x-lucide-calendar class="w-3 h-3 inline mr-1" />{{ $cycle->period_label }}
                    &nbsp;·&nbsp;
                    <x-lucide-user class="w-3 h-3 inline mr-1" />{{ $cycle->creator->name }}
                    &nbsp;·&nbsp;
                    {{ $cycle->manager_evaluations_count }} avaliação(ões)
                </p>
                @if($cycle->description)
                    <p class="text-xs text-slate-400 lato-regular mt-1">{{ $cycle->description }}</p>
                @endif
            </div>

            <div class="flex items-center gap-2 shrink-0 flex-wrap">
                @if($cycle->status === 'draft')
                    <button type="button" wire:click="requestActivateCycle({{ $cycle->id }})"
                            class="flex items-center gap-1 text-xs lato-bold px-3 py-1.5 rounded-lg bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300 transition cursor-pointer">
                        <x-lucide-play class="w-3 h-3" /> Ativar
                    </button>
                    <button type="button" wire:click="abrirCicloModal({{ $cycle->id }})"
                            class="flex items-center gap-1 text-xs lato-bold px-3 py-1.5 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300 transition cursor-pointer">
                        <x-lucide-pencil class="w-3 h-3" /> Editar
                    </button>
                    <button type="button" wire:click="requestDeleteCycle({{ $cycle->id }})"
                            class="flex items-center gap-1 text-xs lato-bold px-3 py-1.5 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 dark:bg-red-900/20 dark:text-red-400 transition cursor-pointer">
                        <x-lucide-trash-2 class="w-3 h-3" /> Excluir
                    </button>
                @elseif($cycle->status === 'active')
                    <button type="button" wire:click="requestCloseCycle({{ $cycle->id }})"
                            class="flex items-center gap-1 text-xs lato-bold px-3 py-1.5 rounded-lg bg-rose-100 text-rose-700 hover:bg-rose-200 dark:bg-rose-900/30 dark:text-rose-300 transition cursor-pointer">
                        <x-lucide-stop-circle class="w-3 h-3" /> Encerrar
                    </button>
                @else
                    <span class="text-[10px] text-slate-400 lato-regular">Encerrado em {{ $cycle->updated_at->format('d/m/Y') }}</span>
                @endif
            </div>
        </div>
        @empty
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-12 text-center">
                <x-lucide-clipboard-list class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
                <p class="text-sm text-slate-400 lato-regular">Nenhum ciclo cadastrado ainda.</p>
                <button type="button" wire:click="abrirCicloModal"
                        class="mt-3 text-xs lato-bold text-indigo-600 hover:underline cursor-pointer">
                    Criar primeiro ciclo
                </button>
            </div>
        @endforelse
    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: CRITÉRIOS (ADMIN)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isRhOuDp() && $activeTab === 'criterios')
    <div class="mt-6 space-y-2">
        @forelse($this->allCriteria as $crit)
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl px-5 py-4 flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg {{ $crit->is_active ? 'bg-indigo-100 dark:bg-indigo-900/30' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center shrink-0">
                <x-lucide-award class="w-4 h-4 {{ $crit->is_active ? 'text-indigo-600' : 'text-slate-400' }}" />
            </div>

            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $crit->name }}</p>
                    @if($crit->is_default)
                        <span class="text-[9px] lato-bold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 uppercase tracking-wide">padrão</span>
                    @endif
                    @if(!$crit->is_active)
                        <span class="text-[9px] lato-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 dark:bg-slate-700 uppercase tracking-wide">inativo</span>
                    @endif
                    <span class="text-[9px] lato-bold px-1.5 py-0.5 rounded bg-slate-50 text-slate-500 dark:bg-slate-700/50 dark:text-slate-400 border border-slate-200 dark:border-slate-600 uppercase tracking-wide">
                        {{ $crit->responseTypeLabel }}
                    </span>
                    @if(($crit->weight ?? 1.0) != 1.0)
                    <span class="text-[9px] lato-bold px-1.5 py-0.5 rounded bg-amber-50 text-amber-600 dark:bg-amber-900/20 dark:text-amber-400 border border-amber-200 dark:border-amber-700 uppercase tracking-wide">
                        ×{{ number_format($crit->weight, 1) }}
                    </span>
                    @endif
                </div>
                @if($crit->description)
                    <p class="text-xs text-slate-400 lato-regular mt-0.5 line-clamp-1">{{ $crit->description }}</p>
                @endif
                @if(($crit->response_type ?? 'scale') === 'multiple_choice' && !empty($crit->options))
                    <p class="text-[10px] text-slate-400 lato-regular mt-0.5">Opções: {{ implode(' · ', $crit->options) }}</p>
                @endif
            </div>

            <div class="flex items-center gap-2 shrink-0">
                <button type="button" wire:click="toggleCriterion({{ $crit->id }})"
                        class="text-xs lato-bold px-3 py-1.5 rounded-lg transition cursor-pointer
                               {{ $crit->is_active ? 'bg-slate-100 text-slate-600 hover:bg-slate-200 dark:bg-slate-700 dark:text-slate-300' : 'bg-emerald-100 text-emerald-700 hover:bg-emerald-200 dark:bg-emerald-900/30 dark:text-emerald-300' }}">
                    {{ $crit->is_active ? 'Desativar' : 'Ativar' }}
                </button>
                <button type="button" wire:click="abrirCriterioModal({{ $crit->id }})"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    <x-lucide-pencil class="w-3.5 h-3.5" />
                </button>
                @if(!$crit->is_default)
                <button type="button" wire:click="deleteCriterion({{ $crit->id }})"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer disabled:opacity-60"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="deleteCriterion" class="flex items-center gap-1.5">
                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                    </span>
                    <span wire:loading wire:target="deleteCriterion" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
                @endif
            </div>
        </div>
        @empty
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-10 text-center">
                <p class="text-sm text-slate-400 lato-regular">Nenhum critério cadastrado.</p>
            </div>
        @endforelse
    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: RANKING (ADMIN)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isRhOuDp() && $activeTab === 'ranking')
    <div class="mt-6 space-y-4">

        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">

            {{-- Cabeçalho tabela --}}
            <div class="hidden sm:grid grid-cols-12 px-5 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50
                        text-[10px] lato-bold text-slate-400 uppercase tracking-wider">
                <div class="col-span-1 flex justify-center">
                    <x-lucide-hash class="w-3.5 h-3.5" />
                </div>
                <div class="col-span-3 flex items-center gap-1">
                    <x-lucide-user class="w-3.5 h-3.5" /> Gerente
                </div>
                <div class="col-span-2 flex items-center gap-1">
                    <x-lucide-building-2 class="w-3.5 h-3.5" /> Departamento
                </div>
                <div class="col-span-2 flex items-center justify-center gap-1 text-indigo-500">
                    <x-lucide-star class="w-3.5 h-3.5" /> Aval. Gestor
                </div>
                <div class="col-span-2 flex items-center justify-center gap-1">
                    <x-lucide-heart class="w-3.5 h-3.5" /> Satisfação
                </div>
                <div class="col-span-2 flex items-center justify-end gap-1">
                    <x-lucide-refresh-cw class="w-3.5 h-3.5" /> Ciclos
                </div>
            </div>

            {{-- Linhas --}}
            <div class="divide-y divide-slate-50 dark:divide-slate-700/50">
            @forelse($this->ranking as $i => $item)
            @php
                $isTop3 = $i < 3;

                // Cores da nota de avaliação do gestor
                $evalColor = match(true) {
                    $item->manager_eval_score === null  => null,
                    $item->manager_eval_score >= 8      => 'text-emerald-600 dark:text-emerald-400',
                    $item->manager_eval_score >= 6      => 'text-amber-600 dark:text-amber-400',
                    default                             => 'text-red-500 dark:text-red-400',
                };
                $evalBadge = match(true) {
                    $item->manager_eval_score === null  => null,
                    $item->manager_eval_score >= 8      => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400',
                    $item->manager_eval_score >= 6      => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400',
                    default                             => 'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-400',
                };
                $evalLabel = match(true) {
                    $item->manager_eval_score === null  => null,
                    $item->manager_eval_score >= 8      => 'Excelente',
                    $item->manager_eval_score >= 6      => 'Regular',
                    default                             => 'Atenção',
                };
                $evalBarColor = match(true) {
                    $item->manager_eval_score === null  => 'bg-slate-200',
                    $item->manager_eval_score >= 8      => 'bg-emerald-500',
                    $item->manager_eval_score >= 6      => 'bg-amber-500',
                    default                             => 'bg-red-500',
                };
                $evalBarWidth = $item->manager_eval_score !== null
                    ? min(100, round(($item->manager_eval_score / 10) * 100)) : 0;

                // Cores da satisfação
                $satColor = match(true) {
                    $item->survey_score === null => null,
                    $item->survey_score >= 7    => 'text-emerald-600 dark:text-emerald-400',
                    $item->survey_score >= 5    => 'text-amber-600 dark:text-amber-400',
                    default                     => 'text-red-500 dark:text-red-400',
                };
                $satBarColor = match(true) {
                    $item->survey_score === null => 'bg-slate-200',
                    $item->survey_score >= 7    => 'bg-emerald-500',
                    $item->survey_score >= 5    => 'bg-amber-500',
                    default                     => 'bg-red-500',
                };
                $satBarWidth = $item->survey_score !== null
                    ? min(100, round(($item->survey_score / 10) * 100)) : 0;
            @endphp

            <div class="grid grid-cols-2 sm:grid-cols-12 gap-3 sm:gap-0 px-4 sm:px-5 py-3.5 items-center
                        {{ $isTop3 ? 'hover:bg-amber-50/60 dark:hover:bg-amber-900/10' : 'hover:bg-slate-50 dark:hover:bg-slate-700/30' }} transition">

                {{-- Posição / Medalha --}}
                <div class="col-span-1 flex items-center justify-center">
                    @if($i === 0)
                        <x-lucide-trophy class="w-5 h-5 text-amber-400" title="1º lugar" />
                    @elseif($i === 1)
                        <x-lucide-award class="w-5 h-5 text-slate-400" title="2º lugar" />
                    @elseif($i === 2)
                        <x-lucide-award class="w-5 h-5 text-amber-600" title="3º lugar" />
                    @else
                        <span class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center
                                     text-[11px] lato-bold text-slate-500 dark:text-slate-400">
                            {{ $i + 1 }}
                        </span>
                    @endif
                </div>

                {{-- Gerente --}}
                <div class="col-span-1 sm:col-span-3 flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600
                                flex items-center justify-center text-white text-xs lato-black shrink-0">
                        {{ mb_strtoupper(mb_substr($item->manager->name, 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <span class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate block">
                            {{ $item->manager->name }}
                        </span>
                        {{-- Departamento no mobile --}}
                        <span class="sm:hidden text-xs text-slate-400 lato-regular flex items-center gap-1 truncate">
                            <x-lucide-building-2 class="w-3 h-3 shrink-0" />
                            {{ $item->manager->department?->name ?? '—' }}
                        </span>
                    </div>
                </div>

                {{-- Departamento (desktop) --}}
                <div class="hidden sm:flex col-span-2 items-center gap-1.5 min-w-0">
                    <x-lucide-building-2 class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0" />
                    <span class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">
                        {{ $item->manager->department?->name ?? '—' }}
                    </span>
                </div>

                {{-- Avaliação do Gestor --}}
                <div class="col-span-1 sm:col-span-2 flex flex-col items-center justify-center gap-0.5">
                    @if($item->manager_eval_score !== null)
                        <span class="text-sm lato-black {{ $evalColor }}">
                            {{ number_format($item->manager_eval_score, 1) }}
                        </span>
                        {{-- Badge --}}
                        <span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded-full text-[9px] lato-bold {{ $evalBadge }}">
                            @if($item->manager_eval_score >= 8)
                                <x-lucide-star class="w-2.5 h-2.5" />
                            @elseif($item->manager_eval_score >= 6)
                                <x-lucide-minus-circle class="w-2.5 h-2.5" />
                            @else
                                <x-lucide-alert-triangle class="w-2.5 h-2.5" />
                            @endif
                            {{ $evalLabel }}
                        </span>
                        {{-- Barra --}}
                        <div class="hidden sm:block w-full h-1 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mt-0.5">
                            <div class="h-full rounded-full {{ $evalBarColor }}" style="width: {{ $evalBarWidth }}%"></div>
                        </div>
                        <span class="text-[9px] text-slate-400 lato-regular flex items-center gap-0.5">
                            <x-lucide-message-square class="w-2.5 h-2.5" />
                            {{ $item->manager_eval_count }} resp.
                        </span>
                    @else
                        <x-lucide-minus class="w-4 h-4 text-slate-300 dark:text-slate-600" />
                    @endif
                </div>

                {{-- Satisfação --}}
                <div class="hidden sm:flex col-span-2 flex-col items-center justify-center gap-0.5">
                    @if($item->survey_score !== null)
                        <span class="text-sm lato-black {{ $satColor }}">
                            {{ number_format($item->survey_score, 1) }}
                            <span class="text-[10px] font-normal text-slate-400">/10</span>
                        </span>
                        <div class="w-full h-1 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mt-0.5">
                            <div class="h-full rounded-full {{ $satBarColor }}" style="width: {{ $satBarWidth }}%"></div>
                        </div>
                    @else
                        <x-lucide-minus class="w-4 h-4 text-slate-300 dark:text-slate-600" />
                    @endif
                </div>

                {{-- Ciclos --}}
                <div class="hidden sm:flex col-span-2 items-center justify-end">
                    <span class="inline-flex items-center gap-1 text-xs lato-bold
                                 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300
                                 px-2 py-1 rounded-lg">
                        <x-lucide-refresh-cw class="w-3 h-3" />
                        {{ $item->cycles_completed }}
                    </span>
                </div>

            </div>
            @empty
                <div class="p-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                        <x-lucide-trophy class="w-7 h-7 text-slate-300 dark:text-slate-600" />
                    </div>
                    <p class="text-sm lato-bold text-slate-500 dark:text-slate-400">Nenhum gerente cadastrado ainda.</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular mt-1">O ranking aparece assim que houver ciclos de avaliação concluídos.</p>
                </div>
            @endforelse
            </div>

            {{-- Rodapé --}}
            @if($this->ranking->count() > 0)
            <div class="px-5 py-2.5 bg-slate-50 dark:bg-slate-700/30 border-t border-slate-100 dark:border-slate-700
                        flex items-center justify-between text-[11px] lato-regular text-slate-400">
                <span class="flex items-center gap-1.5">
                    <x-lucide-users class="w-3.5 h-3.5" />
                    {{ $this->ranking->count() }} {{ $this->ranking->count() === 1 ? 'gerente' : 'gerentes' }}
                </span>
                <span class="hidden sm:flex items-center gap-1.5">
                    <x-lucide-info class="w-3 h-3" />
                    Ordenado pela nota de Avaliação do Gestor
                </span>
            </div>
            @endif

        </div>

        {{-- Legenda --}}
        <div class="flex flex-wrap items-center gap-4 text-[11px] lato-regular text-slate-400 px-1">
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
            <span class="hidden sm:flex items-center gap-1.5 ml-auto">
                <x-lucide-star class="w-3 h-3 text-indigo-400" />
                Aval. Gestor = perguntas marcadas como "Avaliação do Gestor" (0–10)
            </span>
        </div>

    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: AVALIAÇÕES (ADMIN — OVERVIEW DO CICLO ATIVO)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isRhOuDp() && $activeTab === 'avaliacoes')
    <div class="mt-6">
        @if(!$this->activeCycle)
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-12 text-center">
                <x-lucide-calendar-off class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
                <p class="text-sm text-slate-400 lato-regular">Nenhum ciclo ativo. Ative um ciclo na aba <strong>Ciclos</strong>.</p>
            </div>
        @else
            <div class="mb-4 px-1">
                <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $this->activeCycle->name }}</p>
                <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $this->activeCycle->period_label }}</p>
            </div>

            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                <div class="grid grid-cols-12 px-5 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
                    <div class="col-span-5 text-[10px] lato-bold text-slate-400 uppercase tracking-wide">Gerente</div>
                    <div class="col-span-3 text-[10px] lato-bold text-slate-400 uppercase tracking-wide">Departamento</div>
                    <div class="col-span-2 text-[10px] lato-bold text-slate-400 uppercase tracking-wide">Status</div>
                    <div class="col-span-2 text-[10px] lato-bold text-slate-400 uppercase tracking-wide text-right">Finalizado em</div>
                </div>

                @forelse($this->evaluationsOverview as $item)
                <div class="grid grid-cols-12 px-5 py-3.5 items-center border-b border-slate-50 dark:border-slate-700/50 last:border-0 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                    <div class="col-span-5 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-xs lato-black shrink-0">
                            {{ mb_strtoupper(mb_substr($item->manager->name, 0, 1)) }}
                        </div>
                        <span class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $item->manager->name }}</span>
                    </div>
                    <div class="col-span-3">
                        <span class="text-xs text-slate-500 lato-regular truncate">{{ $item->manager->department?->name ?? '—' }}</span>
                    </div>
                    <div class="col-span-2">
                        @php
                            $stColor = match($item->status) {
                                'not_started' => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
                                'in_progress' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                                'completed'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                default       => 'bg-slate-100 text-slate-500',
                            };
                            $stLabel = match($item->status) {
                                'not_started' => 'Não iniciada',
                                'in_progress' => 'Em andamento',
                                'completed'   => 'Finalizada',
                                default       => $item->status,
                            };
                        @endphp
                        <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full {{ $stColor }}">{{ $stLabel }}</span>
                    </div>
                    <div class="col-span-2 text-right">
                        <span class="text-xs text-slate-400 lato-regular">
                            {{ $item->completed_at ? $item->completed_at->format('d/m/Y') : '—' }}
                        </span>
                    </div>
                </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-sm text-slate-400 lato-regular">Nenhum gerente cadastrado.</p>
                    </div>
                @endforelse
            </div>
        @endif
    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: AUTOAVALIAÇÃO (COLABORADOR)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isEmployee() && $activeTab === 'autoavaliacao')
    <div class="mt-6" wire:init="loadSelfEvaluationData">

        @if(!$this->activeCycle)
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-12 text-center">
                <x-lucide-calendar-off class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
                <p class="text-sm text-slate-400 lato-regular">Nenhum ciclo de avaliação ativo no momento.</p>
            </div>

        @elseif($this->selfEvaluationCompleted)
            <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700 rounded-2xl p-8 text-center">
                <x-lucide-check-circle class="w-10 h-10 text-emerald-500 mx-auto mb-3" />
                <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300 lato-black mb-1">Autoavaliação enviada!</h3>
                <p class="text-xs text-emerald-600 dark:text-emerald-400 lato-regular">
                    Sua autoavaliação para o ciclo <strong>{{ $this->activeCycle->name }}</strong> foi registrada com sucesso.
                    Quando o RH publicar os resultados, você poderá conferir o comparativo na aba "Meu Resultado".
                </p>
            </div>

        @else
            <div class="mb-5">
                <div class="flex items-center justify-between">
                    <div>
                        <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-black">{{ $this->activeCycle->name }}</h2>
                        <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ $this->activeCycle->period_label }} &bull; Avalie a si mesmo em cada critério abaixo</p>
                    </div>
                    <span class="text-[10px] px-2.5 py-1 rounded-full lato-bold {{ $this->mySelfEvaluation?->status_color ?? 'bg-slate-100 text-slate-500' }}">
                        {{ $this->mySelfEvaluation?->status_label ?? 'Pendente' }}
                    </span>
                </div>
            </div>

            <div class="space-y-4">
                @foreach($this->criteria as $criterion)
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                    <div class="flex items-start justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-sm font-bold text-slate-800 dark:text-white lato-black">{{ $criterion->name }}</h3>
                            @if($criterion->description)
                                <p class="text-xs text-slate-500 lato-regular mt-0.5">{{ $criterion->description }}</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-2 shrink-0">
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300 lato-bold">
                                {{ $criterion->responseTypeLabel }}
                            </span>
                            @if(($criterion->weight ?? 1.0) != 1.0)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300 lato-bold">
                                ×{{ number_format($criterion->weight, 1) }}
                            </span>
                            @endif
                        </div>
                    </div>

                    {{-- Escala 1-5 --}}
                    @if(($criterion->response_type ?? 'scale') === 'scale')
                    <div class="flex gap-2">
                        @for($i = 1; $i <= 5; $i++)
                        <button type="button" wire:click="saveSelfScore({{ $criterion->id }}, {{ $i }})"
                                class="flex-1 py-2.5 rounded-xl text-sm lato-bold border transition cursor-pointer
                                       {{ ($selfScores[$criterion->id] ?? null) == $i
                                          ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm'
                                          : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:border-indigo-400 hover:text-indigo-600' }}"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveSelfScore" class="flex items-center gap-1.5">
                                {{ $i }}
                            </span>
                            <span wire:loading wire:target="saveSelfScore" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                        @endfor
                    </div>
                    @php $labels = ['Insatisfatório','Abaixo do esperado','Atende ao esperado','Acima do esperado','Excepcional']; @endphp
                    @if(isset($selfScores[$criterion->id]))
                    <p class="text-[11px] text-indigo-600 lato-bold mt-2 text-center">{{ $labels[$selfScores[$criterion->id]-1] ?? '' }}</p>
                    @endif

                    {{-- Sim / Não --}}
                    @elseif(($criterion->response_type ?? 'scale') === 'boolean')
                    <div class="flex gap-3">
                        <button type="button" wire:click="saveSelfScore({{ $criterion->id }}, 0)"
                                class="flex-1 py-2.5 rounded-xl text-sm lato-bold border transition cursor-pointer
                                       {{ ($selfScores[$criterion->id] ?? null) === 0
                                          ? 'bg-red-500 border-red-500 text-white shadow-sm'
                                          : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:border-red-300 hover:text-red-500' }}"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveSelfScore" class="flex items-center gap-1.5">
                                Não
                            </span>
                            <span wire:loading wire:target="saveSelfScore" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                        <button type="button" wire:click="saveSelfScore({{ $criterion->id }}, 1)"
                                class="flex-1 py-2.5 rounded-xl text-sm lato-bold border transition cursor-pointer
                                       {{ ($selfScores[$criterion->id] ?? null) === 1
                                          ? 'bg-emerald-500 border-emerald-500 text-white shadow-sm'
                                          : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:border-emerald-300 hover:text-emerald-500' }}"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveSelfScore" class="flex items-center gap-1.5">
                                Sim
                            </span>
                            <span wire:loading wire:target="saveSelfScore" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                    </div>

                    {{-- Múltipla escolha --}}
                    @elseif(($criterion->response_type ?? 'scale') === 'multiple_choice')
                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                        @foreach(($criterion->options ?? []) as $idx => $option)
                        <button type="button" wire:click="saveSelfScore({{ $criterion->id }}, {{ $idx }})"
                                class="px-3 py-2 rounded-xl text-xs lato-bold border transition cursor-pointer text-center
                                       {{ ($selfScores[$criterion->id] ?? null) == $idx
                                          ? 'bg-indigo-600 border-indigo-600 text-white shadow-sm'
                                          : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-400 hover:border-indigo-400 hover:text-indigo-600' }}"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveSelfScore" class="flex items-center gap-1.5">
                                {{ $option }}
                            </span>
                            <span wire:loading wire:target="saveSelfScore" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                        @endforeach
                    </div>
                    @endif

                    {{-- Comentário --}}
                    @if(isset($selfScores[$criterion->id]))
                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <input type="text" wire:model="selfComments.{{ $criterion->id }}"
                               wire:blur="saveSelfComment({{ $criterion->id }})"
                               placeholder="Comentário opcional..."
                               class="w-full px-3 py-2 text-xs border border-slate-200 dark:border-slate-600 rounded-lg
                                      bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300
                                      placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-indigo-500/40 lato-regular" />
                    </div>
                    @endif
                </div>
                @endforeach
            </div>

            {{-- Botão de envio --}}
            <div class="mt-6 flex justify-end">
                <button type="button" wire:click="submitSelfEvaluation"
                        wire:loading.attr="disabled" wire:target="submitSelfEvaluation"
                        class="flex items-center gap-2 px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm lato-bold rounded-xl shadow-sm transition cursor-pointer disabled:opacity-60">
                    <x-lucide-send class="w-4 h-4" />
                    Enviar Autoavaliação
                </button>
            </div>
        @endif
    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: MEU RESULTADO (COLABORADOR)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isEmployee() && $activeTab === 'resultado')
    <div class="mt-6">
        @if(!$this->myManagerEvaluationResult)
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-12 text-center">
                <x-lucide-lock class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
                <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">Resultado ainda não disponível.</p>
                <p class="text-xs text-slate-400 lato-regular mt-1">O RH publicará os resultados após o encerramento do ciclo.</p>
            </div>
        @else
            @php $result = $this->myManagerEvaluationResult; @endphp
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Card de nota --}}
                <div class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl p-6 text-white">
                    <p class="text-xs opacity-75 lato-regular mb-1">Ciclo: {{ $result->cycle->name }}</p>
                    <p class="text-4xl font-bold lato-black">
                        {{ $result->final_score !== null ? number_format($result->final_score, 2, ',', '') : '—' }}
                        <span class="text-xl font-normal opacity-70">/5</span>
                    </p>
                    <p class="text-xs opacity-75 mt-2 lato-regular">Nota do gerente (média ponderada)</p>
                    <div class="mt-4 pt-4 border-t border-white/20">
                        <p class="text-xs opacity-75 lato-regular">Avaliado por: <span class="font-bold">{{ $result->manager->name }}</span></p>
                        <p class="text-xs opacity-75 lato-regular mt-0.5">Em: {{ $result->completed_at?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                </div>

                {{-- Comparativo por critério --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-800 dark:text-white lato-black">Comparativo por critério</h3>
                        <p class="text-xs text-slate-400 lato-regular mt-0.5">Sua autoavaliação vs a avaliação do gerente</p>
                    </div>
                    <div class="divide-y divide-slate-50 dark:divide-slate-700/50">
                        @foreach($result->comparison as $comp)
                        <div class="px-5 py-3.5 grid grid-cols-12 items-center gap-3">
                            <div class="col-span-5">
                                <p class="text-xs font-bold text-slate-700 dark:text-slate-200 lato-bold">{{ $comp->criterion->name }}</p>
                                @if($comp->comment)
                                    <p class="text-[11px] text-slate-400 italic mt-0.5 leading-snug">"{{ $comp->comment }}"</p>
                                @endif
                            </div>
                            <div class="col-span-3 text-center">
                                @php
                                    $mScore = $comp->manager_score;
                                    $mColor = match(true) {
                                        $mScore >= 5    => 'text-emerald-600',
                                        $mScore >= 4    => 'text-green-600',
                                        $mScore >= 3    => 'text-amber-600',
                                        $mScore >= 2    => 'text-orange-600',
                                        default         => 'text-red-600',
                                    };
                                @endphp
                                <p class="text-xs text-slate-400 lato-regular mb-0.5">Gerente</p>
                                <p class="text-lg font-bold lato-black {{ $mColor }}">{{ $comp->manager_display }}</p>
                                @if($comp->is_calibrated)
                                    <p class="text-[10px] text-indigo-400 lato-regular mt-0.5 flex items-center justify-center gap-0.5">
                                        <span>✦</span> calibrado
                                        <span class="text-slate-400 ml-1">(orig. {{ $comp->original_display }})</span>
                                    </p>
                                @endif
                            </div>
                            <div class="col-span-4 text-center">
                                <p class="text-xs text-slate-400 lato-regular mb-0.5">Autoavaliação</p>
                                @if($comp->self_score !== null)
                                    <p class="text-lg font-bold lato-black text-indigo-500">{{ $comp->self_display }}</p>
                                @else
                                    <p class="text-xs text-slate-300 dark:text-slate-600">Não respondida</p>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: CALIBRAÇÃO (ADMIN/RH)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if(auth()->user()->isRhOuDp() && $activeTab === 'calibracao')
    @php
        $calCycles     = $this->allCycles->where('status', '!=', 'draft')->values();
        $selCycle      = $calCycles->firstWhere('id', $calibrationCycleId);
        $cycleIsActive = $selCycle && $selCycle->status === 'active';
    @endphp
    <div class="mt-6 space-y-4">

        {{-- ── Cabeçalho da aba ──────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-sm lato-black text-slate-800 dark:text-white flex items-center gap-2">
                    <x-lucide-sliders-horizontal class="w-4 h-4 text-indigo-500" />
                    Calibração de Notas
                </h2>
                <p class="text-[11px] text-slate-400 lato-regular mt-0.5">
                    Ajuste manual das notas atribuídas pelos gerentes após revisão do RH/DP
                </p>
            </div>
            @if($selCycle)
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] lato-bold
                         {{ $cycleIsActive
                             ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800'
                             : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-slate-600' }}">
                @if($cycleIsActive)
                    <x-lucide-circle-dot class="w-3 h-3" />
                    Ciclo ativo
                @else
                    <x-lucide-lock class="w-3 h-3" />
                    Ciclo {{ $selCycle->statusLabel }}
                @endif
            </span>
            @endif
        </div>

        {{-- ── Seletor de ciclo ─────────────────────────────────────────── --}}
        <div x-data="{ open: false }" class="relative">
            <button
                type="button"
                @click="open = !open"
                @keydown.escape="open = false"
                class="w-full flex items-center justify-between gap-3 px-4 py-3.5
                       bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                       rounded-2xl hover:border-indigo-300 dark:hover:border-indigo-600
                       focus:outline-none focus:ring-2 focus:ring-indigo-400/40 transition">

                <div class="flex items-center gap-3 min-w-0">
                    <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0
                                {{ $selCycle ? ($cycleIsActive ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700') : 'bg-slate-100 dark:bg-slate-700' }}">
                        @if($selCycle && !$cycleIsActive)
                            <x-lucide-lock class="w-4 h-4 text-slate-400" />
                        @else
                            <x-lucide-sliders-horizontal class="w-4 h-4 {{ $selCycle ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}" />
                        @endif
                    </div>
                    <div class="text-left min-w-0">
                        @if($selCycle)
                            <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $selCycle->name }}</p>
                            <p class="text-[11px] text-slate-400 lato-regular">
                                {{ $selCycle->start_date->format('d/m/Y') }} → {{ $selCycle->end_date->format('d/m/Y') }}
                            </p>
                        @else
                            <p class="text-sm lato-regular text-slate-400">Selecione um ciclo para calibrar...</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2 shrink-0">
                    @if($selCycle)
                        @php
                            $stCl = match($selCycle->status) {
                                'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                                'closed' => 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
                                default  => 'bg-slate-100 text-slate-400',
                            };
                        @endphp
                        <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full {{ $stCl }}">
                            {{ $selCycle->statusLabel }}
                        </span>
                    @endif
                    <x-lucide-chevron-down class="w-4 h-4 text-slate-400 transition-transform duration-200"
                                           x-bind:class="open ? 'rotate-180' : ''" />
                </div>
            </button>

            {{-- Dropdown de ciclos --}}
            <div
                x-show="open"
                x-transition:enter="transition ease-out duration-150"
                x-transition:enter-start="opacity-0 translate-y-1"
                x-transition:enter-end="opacity-100 translate-y-0"
                x-transition:leave="transition ease-in duration-100"
                x-transition:leave-start="opacity-100 translate-y-0"
                x-transition:leave-end="opacity-0 translate-y-1"
                @click.outside="open = false"
                class="absolute z-30 top-full left-0 right-0 mt-2
                       bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                       rounded-2xl shadow-xl overflow-hidden"
                style="display:none">

                @if($calCycles->isEmpty())
                    <div class="px-5 py-8 text-center">
                        <x-lucide-calendar-off class="w-7 h-7 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                        <p class="text-xs text-slate-400 lato-regular">Nenhum ciclo disponível para calibração.</p>
                    </div>
                @else
                    <div class="p-2 max-h-72 overflow-y-auto">
                        @foreach($calCycles as $cy)
                        @php
                            $isSelected  = $calibrationCycleId == $cy->id;
                            $cyIsActive  = $cy->status === 'active';
                            $stBadge     = match($cy->status) {
                                'active' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300',
                                'closed' => 'bg-rose-100 text-rose-600 dark:bg-rose-900/30 dark:text-rose-400',
                                default  => 'bg-slate-100 text-slate-400',
                            };
                        @endphp
                        <button
                            type="button"
                            wire:click="$set('calibrationCycleId', {{ $cy->id }})"
                            @click="open = false"
                            class="w-full flex items-center gap-3 px-3 py-3 rounded-xl text-left transition
                                   {{ $isSelected ? 'bg-indigo-50 dark:bg-indigo-900/30' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">

                            <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0
                                        {{ $isSelected ? 'bg-indigo-600' : ($cyIsActive ? 'bg-emerald-100 dark:bg-emerald-900/30' : 'bg-slate-100 dark:bg-slate-700') }}">
                                @if($isSelected)
                                    <x-lucide-check class="w-4 h-4 text-white" />
                                @elseif($cyIsActive)
                                    <x-lucide-sliders-horizontal class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                                @else
                                    <x-lucide-lock class="w-4 h-4 text-slate-400" />
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <p class="text-sm lato-bold truncate
                                          {{ $isSelected ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-700 dark:text-slate-200' }}">
                                    {{ $cy->name }}
                                </p>
                                <p class="text-[11px] text-slate-400 lato-regular mt-0.5">
                                    {{ $cy->start_date->format('d/m/Y') }} → {{ $cy->end_date->format('d/m/Y') }}
                                </p>
                            </div>

                            <div class="flex flex-col items-end gap-1 shrink-0">
                                <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full {{ $stBadge }}">
                                    {{ $cy->statusLabel }}
                                </span>
                                @if($cy->results_published_at)
                                <span class="inline-flex items-center gap-0.5 text-[10px] text-emerald-500 lato-regular">
                                    <x-lucide-check class="w-2.5 h-2.5" /> Publicado
                                </span>
                                @endif
                            </div>
                        </button>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

        {{-- ── Aviso de ciclo inativo ────────────────────────────────────── --}}
        @if($selCycle && !$cycleIsActive)
        <div class="flex items-start gap-3 px-4 py-3.5 rounded-2xl
                    bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/60">
            <div class="w-8 h-8 rounded-xl bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0 mt-0.5">
                <x-lucide-lock class="w-4 h-4 text-amber-600 dark:text-amber-400" />
            </div>
            <div>
                <p class="text-sm lato-bold text-amber-800 dark:text-amber-300">Calibração bloqueada</p>
                <p class="text-xs text-amber-700/80 dark:text-amber-400/80 lato-regular mt-0.5">
                    Este ciclo está <span class="lato-bold">{{ $selCycle->statusLabel }}</span>.
                    Só é possível calibrar notas em ciclos com status <span class="lato-bold">Ativo</span>.
                </p>
            </div>
        </div>
        @endif

        {{-- ── Ações do ciclo selecionado ───────────────────────────────── --}}
        @if($calibrationCycleId)
        <div class="flex items-center gap-2 flex-wrap">
            <a href="{{ route('avaliacoes.export.pdf', $calibrationCycleId) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs lato-bold transition
                      bg-red-50 hover:bg-red-100 dark:bg-red-900/20 dark:hover:bg-red-900/40
                      text-red-600 dark:text-red-400 border border-red-200 dark:border-red-800">
                <x-lucide-file-text class="w-3.5 h-3.5" />
                Exportar PDF
            </a>
            <a href="{{ route('avaliacoes.export.excel', $calibrationCycleId) }}" target="_blank"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-xs lato-bold transition
                      bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:hover:bg-emerald-900/40
                      text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                <x-lucide-table class="w-3.5 h-3.5" />
                Exportar Excel
            </a>

            <div class="ml-auto">
                @if($selCycle && !$selCycle->results_published_at)
                <button
                    type="button"
                    wire:click="publishResults({{ $calibrationCycleId }})"
                    wire:confirm="Publicar os resultados deste ciclo? Os colaboradores poderão visualizar suas notas."
                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs lato-bold
                           bg-indigo-600 hover:bg-indigo-700 text-white shadow-sm transition">
                    <x-lucide-send class="w-3.5 h-3.5" />
                    Publicar resultados
                </button>
                @elseif($selCycle && $selCycle->results_published_at)
                <span class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl text-[11px] lato-bold
                             bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300
                             border border-emerald-200 dark:border-emerald-700">
                    <x-lucide-check-circle class="w-3.5 h-3.5" />
                    Publicado em {{ $selCycle->results_published_at->format('d/m/Y') }}
                </span>
                @endif
            </div>
        </div>
        @endif

        {{-- ── Conteúdo principal ────────────────────────────────────────── --}}
        @if(!$calibrationCycleId)
            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-12 text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                    <x-lucide-sliders-horizontal class="w-6 h-6 text-slate-400" />
                </div>
                <p class="text-sm lato-bold text-slate-500 dark:text-slate-400">Nenhum ciclo selecionado</p>
                <p class="text-xs text-slate-400 lato-regular mt-1">Selecione um ciclo acima para iniciar a calibração.</p>
            </div>
        @else

            @if($this->calibrationManagers->isEmpty())
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-12 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-4">
                        <x-lucide-users class="w-6 h-6 text-slate-400" />
                    </div>
                    <p class="text-sm lato-bold text-slate-500 dark:text-slate-400">Nenhum gerente disponível</p>
                    <p class="text-xs text-slate-400 lato-regular mt-1">Nenhum gerente finalizou a avaliação neste ciclo.</p>
                </div>
            @else
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4" style="height: calc(100vh - 380px); min-height: 480px;">

                {{-- ── Coluna 1: Gerentes ── --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80 shrink-0 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-lucide-user class="w-3.5 h-3.5 text-slate-400" />
                            <p class="text-[10px] lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Gerentes</p>
                        </div>
                        <span class="text-[10px] lato-bold text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full">
                            {{ $this->calibrationManagers->count() }}
                        </span>
                    </div>
                    <div class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-700/50">
                        @foreach($this->calibrationManagers as $mgr)
                        @php $isMgrSelected = $calibrationManagerId === $mgr->id; @endphp
                        <button type="button" wire:click="selectCalibrationManager({{ $mgr->id }})"
                                class="w-full flex items-center gap-3 px-4 py-3.5 text-left transition cursor-pointer
                                       {{ $isMgrSelected
                                           ? 'bg-indigo-50 dark:bg-indigo-900/20 border-l-2 border-indigo-500'
                                           : 'hover:bg-slate-50 dark:hover:bg-slate-700/30 border-l-2 border-transparent' }}">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-xs lato-black shrink-0 shadow-sm">
                                {{ mb_strtoupper(mb_substr($mgr->name, 0, 1)) }}
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-xs lato-bold truncate
                                          {{ $isMgrSelected ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-700 dark:text-slate-200' }}">
                                    {{ $mgr->name }}
                                </p>
                                <p class="text-[10px] text-slate-400 lato-regular truncate mt-0.5">{{ $mgr->department?->name ?? '—' }}</p>
                            </div>
                            @if($isMgrSelected)
                                <x-lucide-chevron-right class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                            @endif
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- ── Coluna 2: Colaboradores ── --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden flex flex-col">
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80 shrink-0 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <x-lucide-users class="w-3.5 h-3.5 text-slate-400" />
                            <p class="text-[10px] lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Colaboradores</p>
                        </div>
                        @if($calibrationManagerId && $this->calibrationEmployees->isNotEmpty())
                        <span class="text-[10px] lato-bold text-slate-400 bg-slate-100 dark:bg-slate-700 px-2 py-0.5 rounded-full">
                            {{ $this->calibrationEmployees->count() }}
                        </span>
                        @endif
                    </div>
                    <div class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-700/50">
                        @if(!$calibrationManagerId)
                            <div class="flex flex-col items-center justify-center h-full text-center p-6">
                                <x-lucide-mouse-pointer-click class="w-7 h-7 text-slate-200 dark:text-slate-600 mb-2" />
                                <p class="text-xs text-slate-400 lato-regular">Selecione um gerente</p>
                            </div>
                        @elseif($this->calibrationEmployees->isEmpty())
                            <div class="flex flex-col items-center justify-center h-full text-center p-6">
                                <x-lucide-users class="w-7 h-7 text-slate-200 dark:text-slate-600 mb-2" />
                                <p class="text-xs text-slate-400 lato-regular">Nenhum colaborador avaliado</p>
                            </div>
                        @else
                            @foreach($this->calibrationEmployees as $emp)
                            @php $isEmpSelected = $calibrationEmployeeId === $emp->id; @endphp
                            <button type="button" wire:click="loadCalibrationEntries({{ $emp->id }})"
                                    class="w-full flex items-center gap-3 px-4 py-3.5 text-left transition cursor-pointer
                                           {{ $isEmpSelected
                                               ? 'bg-indigo-50 dark:bg-indigo-900/20 border-l-2 border-indigo-500'
                                               : 'hover:bg-slate-50 dark:hover:bg-slate-700/30 border-l-2 border-transparent' }}">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-400 to-slate-500 flex items-center justify-center text-white text-[10px] lato-black shrink-0">
                                    {{ mb_strtoupper(mb_substr($emp->name, 0, 1)) }}
                                </div>
                                <p class="text-xs lato-bold truncate flex-1
                                          {{ $isEmpSelected ? 'text-indigo-700 dark:text-indigo-300' : 'text-slate-700 dark:text-slate-200' }}">
                                    {{ $emp->name }}
                                </p>
                                @if($isEmpSelected)
                                    <x-lucide-chevron-right class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                                @endif
                            </button>
                            @endforeach
                        @endif
                    </div>
                </div>

                {{-- ── Coluna 3: Calibrar notas ── --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden flex flex-col">

                    @if(!$calibrationEmployeeId)
                        {{-- Estado vazio --}}
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80 shrink-0 flex items-center gap-2">
                            <x-lucide-sliders-horizontal class="w-3.5 h-3.5 text-slate-400" />
                            <p class="text-[10px] lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Calibrar notas</p>
                        </div>
                        <div class="flex flex-col items-center justify-center flex-1 text-center p-6">
                            <x-lucide-sliders-horizontal class="w-7 h-7 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Selecione um colaborador</p>
                        </div>

                    @elseif($this->calibrationEntries->isEmpty())
                        {{-- Sem entradas --}}
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80 shrink-0 flex items-center gap-2">
                            <x-lucide-sliders-horizontal class="w-3.5 h-3.5 text-slate-400" />
                            <p class="text-[10px] lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Calibrar notas</p>
                        </div>
                        <div class="flex flex-col items-center justify-center flex-1 text-center p-6">
                            <x-lucide-file-x class="w-7 h-7 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Sem critérios encontrados</p>
                        </div>

                    @else
                        @php
                            $allCalibrated   = $this->calibrationEntries->every(fn($e) => $e->calibrated_at !== null);
                            $someCalibrated  = !$allCalibrated && $this->calibrationEntries->some(fn($e) => $e->calibrated_at !== null);
                            $lastCalibratedAt = $allCalibrated
                                ? $this->calibrationEntries->max('calibrated_at')
                                : null;
                            $canCalibrate = $cycleIsActive && !$allCalibrated;
                        @endphp

                        {{-- Cabeçalho com status --}}
                        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50/80 dark:bg-slate-800/80 shrink-0 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                @if($allCalibrated)
                                    <x-lucide-check-circle class="w-3.5 h-3.5 text-emerald-500" />
                                    <p class="text-[10px] lato-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-widest">Calibrado</p>
                                @elseif(!$cycleIsActive)
                                    <x-lucide-lock class="w-3.5 h-3.5 text-amber-500" />
                                    <p class="text-[10px] lato-bold text-amber-600 dark:text-amber-400 uppercase tracking-widest">Bloqueado</p>
                                @else
                                    <x-lucide-sliders-horizontal class="w-3.5 h-3.5 text-indigo-500" />
                                    <p class="text-[10px] lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-widest">Calibrar notas</p>
                                @endif
                            </div>
                            <span class="text-[10px] lato-regular text-slate-400">
                                {{ $this->calibrationEntries->count() }} {{ $this->calibrationEntries->count() === 1 ? 'critério' : 'critérios' }}
                            </span>
                        </div>

                        {{-- Bloco de aviso: ciclo não ativo --}}
                        @if(!$cycleIsActive)
                        <div class="flex flex-col items-center justify-center flex-1 text-center p-8">
                            <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mx-auto mb-4">
                                <x-lucide-lock class="w-6 h-6 text-amber-500" />
                            </div>
                            <p class="text-sm lato-bold text-slate-600 dark:text-slate-300 mb-1">Calibração bloqueada</p>
                            <p class="text-xs text-slate-400 lato-regular leading-relaxed">
                                O ciclo <span class="lato-bold">{{ $selCycle->name }}</span> está
                                <span class="lato-bold text-amber-600 dark:text-amber-400">{{ $selCycle->statusLabel }}</span>.
                                <br>Ative o ciclo para liberar a calibração.
                            </p>
                        </div>

                        {{-- Bloco de aviso: já calibrado --}}
                        @elseif($allCalibrated)
                        <div class="px-4 py-3 bg-emerald-50/60 dark:bg-emerald-900/10 border-b border-emerald-100 dark:border-emerald-900/30 shrink-0">
                            <div class="flex items-center gap-2">
                                <x-lucide-check-circle class="w-4 h-4 text-emerald-500 shrink-0" />
                                <div>
                                    <p class="text-xs lato-bold text-emerald-700 dark:text-emerald-400">Todos os critérios calibrados</p>
                                    @if($lastCalibratedAt)
                                    <p class="text-[10px] text-emerald-600/70 dark:text-emerald-500/70 lato-regular">
                                        Última calibração em {{ \Carbon\Carbon::parse($lastCalibratedAt)->format('d/m/Y \à\s H:i') }}
                                    </p>
                                    @endif
                                </div>
                            </div>
                        </div>
                        {{-- Exibição somente-leitura das notas calibradas --}}
                        <div class="flex-1 overflow-y-auto">
                            <div class="p-4 space-y-3">
                                @foreach($this->calibrationEntries as $entry)
                                <div class="rounded-xl border border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/50 p-3">
                                    <div class="flex items-start justify-between gap-2 mb-2">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $entry->criterion->name }}</p>
                                        <span class="inline-flex items-center gap-1 text-[10px] px-1.5 py-0.5 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 lato-bold shrink-0">
                                            <x-lucide-check class="w-2.5 h-2.5" /> Calibrado
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-3 text-[11px] text-slate-500 lato-regular">
                                        <span>Original: <span class="lato-bold text-slate-600 dark:text-slate-300">{{ $entry->displayValue }}</span></span>
                                        @if($entry->calibrated_score !== null)
                                        <span class="text-slate-300 dark:text-slate-600">→</span>
                                        <span>Calibrado: <span class="lato-bold text-emerald-600 dark:text-emerald-400">{{ $entry->calibrated_score }}</span></span>
                                        @endif
                                    </div>
                                    @if($entry->calibration_note)
                                    <p class="text-[10px] text-slate-400 lato-regular mt-1.5 italic">"{{ $entry->calibration_note }}"</p>
                                    @endif
                                    @if($entry->calibrated_at)
                                    <p class="text-[10px] text-slate-300 dark:text-slate-600 lato-regular mt-1">
                                        {{ \Carbon\Carbon::parse($entry->calibrated_at)->format('d/m/Y H:i') }}
                                    </p>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700 shrink-0">
                            <div class="flex items-center justify-center gap-2 py-2 rounded-xl bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600">
                                <x-lucide-lock class="w-3.5 h-3.5 text-slate-400" />
                                <span class="text-xs text-slate-400 lato-bold">Calibração já realizada</span>
                            </div>
                        </div>

                        @else
                        {{-- Formulário de calibração normal --}}
                        @if($someCalibrated)
                        <div class="px-4 py-2.5 bg-amber-50/60 dark:bg-amber-900/10 border-b border-amber-100 dark:border-amber-900/30 shrink-0">
                            <p class="text-[10px] text-amber-600 dark:text-amber-400 lato-regular flex items-center gap-1.5">
                                <x-lucide-info class="w-3 h-3 shrink-0" />
                                Alguns critérios já foram calibrados anteriormente.
                            </p>
                        </div>
                        @endif
                        <div class="flex-1 overflow-y-auto">
                            <div class="p-4 space-y-4">
                                @foreach($this->calibrationEntries as $entry)
                                <div class="pb-4 border-b border-slate-100 dark:border-slate-700/50 last:border-0 last:pb-0">
                                    <div class="flex items-center justify-between mb-2.5">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $entry->criterion->name }}</p>
                                        <div class="flex items-center gap-1.5 shrink-0">
                                            <span class="text-[10px] text-slate-400 lato-regular">
                                                Original: <span class="lato-bold text-slate-600 dark:text-slate-300">{{ $entry->displayValue }}</span>
                                            </span>
                                            @if($entry->calibrated_score !== null)
                                                <span class="inline-flex items-center gap-0.5 text-[10px] px-1.5 py-0.5 rounded-full bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 lato-bold">
                                                    <x-lucide-refresh-cw class="w-2.5 h-2.5" /> Recalibrando
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    @if(($entry->criterion->response_type ?? 'scale') === 'scale')
                                    <div class="flex gap-1.5 mb-2.5">
                                        @for($i = 1; $i <= 5; $i++)
                                        <button type="button" wire:click="$set('calibrationScores.{{ $entry->id }}', {{ $i }})"
                                                class="flex-1 py-2.5 rounded-xl text-xs lato-bold border-2 transition cursor-pointer
                                                       {{ ($calibrationScores[$entry->id] ?? null) == $i
                                                          ? 'bg-amber-500 border-amber-500 text-white shadow-sm'
                                                          : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-amber-400 hover:text-amber-600 dark:hover:border-amber-500' }}">
                                            {{ $i }}
                                        </button>
                                        @endfor
                                    </div>
                                    @elseif(($entry->criterion->response_type ?? 'scale') === 'boolean')
                                    <div class="flex gap-2 mb-2.5">
                                        <button type="button" wire:click="$set('calibrationScores.{{ $entry->id }}', 0)"
                                                class="flex-1 py-2.5 rounded-xl text-xs lato-bold border-2 transition cursor-pointer flex items-center justify-center gap-1.5
                                                       {{ ($calibrationScores[$entry->id] ?? null) == 0 ? 'bg-red-500 border-red-500 text-white shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-500 hover:border-red-400 hover:text-red-600' }}">
                                            <x-lucide-x class="w-3 h-3" /> Não
                                        </button>
                                        <button type="button" wire:click="$set('calibrationScores.{{ $entry->id }}', 1)"
                                                class="flex-1 py-2.5 rounded-xl text-xs lato-bold border-2 transition cursor-pointer flex items-center justify-center gap-1.5
                                                       {{ ($calibrationScores[$entry->id] ?? null) == 1 ? 'bg-emerald-500 border-emerald-500 text-white shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-500 hover:border-emerald-400 hover:text-emerald-600' }}">
                                            <x-lucide-check class="w-3 h-3" /> Sim
                                        </button>
                                    </div>
                                    @elseif(($entry->criterion->response_type ?? 'scale') === 'multiple_choice')
                                    <div class="grid grid-cols-2 gap-1.5 mb-2.5">
                                        @foreach(($entry->criterion->options ?? []) as $idx => $option)
                                        <button type="button" wire:click="$set('calibrationScores.{{ $entry->id }}', {{ $idx }})"
                                                class="px-2 py-2 rounded-xl text-[11px] lato-bold border-2 transition cursor-pointer text-center
                                                       {{ ($calibrationScores[$entry->id] ?? null) == $idx ? 'bg-amber-500 border-amber-500 text-white shadow-sm' : 'border-slate-200 dark:border-slate-600 text-slate-500 hover:border-amber-400 hover:text-amber-600' }}">
                                            {{ $option }}
                                        </button>
                                        @endforeach
                                    </div>
                                    @endif

                                    <input type="text" wire:model="calibrationNotes.{{ $entry->id }}"
                                           placeholder="Justificativa da calibração (opcional)..."
                                           class="w-full px-3 py-1.5 text-xs border border-slate-200 dark:border-slate-600 rounded-lg
                                                  bg-slate-50 dark:bg-slate-700/50 text-slate-700 dark:text-slate-300
                                                  placeholder-slate-400 focus:outline-none focus:ring-1 focus:ring-amber-500/40 lato-regular" />
                                </div>
                                @endforeach
                            </div>
                        </div>

                        <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700 shrink-0">
                            <button type="button" wire:click="saveCalibration"
                                    wire:loading.attr="disabled" wire:target="saveCalibration"
                                    class="w-full relative flex items-center justify-center gap-2 py-2.5
                                           bg-amber-500 hover:bg-amber-600 active:bg-amber-700
                                           text-white text-xs lato-bold rounded-xl shadow-sm
                                           transition cursor-pointer disabled:opacity-70 overflow-hidden">
                                <span wire:loading wire:target="saveCalibration"
                                      class="absolute inset-0 bg-gradient-to-r from-transparent via-white/20 to-transparent -translate-x-full animate-[shimmer_1.2s_infinite]"></span>
                                <span wire:loading.remove wire:target="saveCalibration" class="flex items-center gap-2">
                                    <x-lucide-save class="w-3.5 h-3.5" />
                                    Salvar calibração
                                </span>
                                <span wire:loading wire:target="saveCalibration" class="flex items-center gap-2">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"/>
                                    </svg>
                                </span>
                            </button>
                        </div>
                        {{-- /footer salvar --}}
                        @endif
                        {{-- /else calibração normal --}}
                    </div>
                    {{-- /flex-1 overflow panel --}}
                </div>
                {{-- /painel de entradas --}}
            @endif
            {{-- /!calibrationEmployeeId @else --}}

            @endif
            {{-- /calibrationManagers @else --}}

        @endif
        {{-- /!calibrationCycleId @else --}}

@endif
