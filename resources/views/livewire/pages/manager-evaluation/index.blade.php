<div class="p-4 sm:p-6 lg:p-8">

    {{-- ═══════════════════════════════════════════════════════════════
         CABEÇALHO
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white lato-black tracking-tight">
                Avaliação de Desempenho
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5 lato-regular">
                @if(auth()->user()->isGerente())
                    Avalie seus colaboradores e acompanhe seu histórico de desempenho
                @else
                    Gerencie ciclos, critérios e acompanhe o ranking dos gestores
                @endif
            </p>
        </div>

        @if(auth()->user()->isRhOuDp() && $activeTab === 'ciclos')
            <button type="button" wire:click="abrirCicloModal"
                    class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                           text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm transition cursor-pointer lato-bold">
                <x-lucide-plus class="w-4 h-4" />
                Novo Ciclo
            </button>
        @endif

        @if(auth()->user()->isRhOuDp() && $activeTab === 'criterios')
            <button type="button" wire:click="abrirCriterioModal"
                    class="flex items-center gap-2 bg-indigo-600 hover:bg-indigo-700 text-white
                           text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm transition cursor-pointer lato-bold">
                <x-lucide-plus class="w-4 h-4" />
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
            <div class="flex items-center justify-between mb-2">
                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">Progresso</p>
                <span class="text-xs lato-bold text-indigo-600">{{ $this->evaluationProgress['pct'] }}%</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5">
                <div class="bg-indigo-500 h-2.5 rounded-full transition-all duration-500"
                     style="width: {{ $this->evaluationProgress['pct'] }}%"></div>
            </div>
            <p class="text-[10px] text-slate-400 lato-regular mt-2">
                @if($this->evaluationProgress['pct'] === 100)
                    <span class="text-emerald-500">Todos avaliados!</span>
                @elseif($this->activeCycle)
                    {{ $this->evaluationProgress['total'] - $this->evaluationProgress['evaluated'] }} restante(s)
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
    <div class="mt-6 flex items-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 w-fit flex-wrap">
        @if(auth()->user()->isGerente())
            @foreach(['dashboard' => ['Dashboard', 'layout-dashboard'], 'avaliacao' => ['Avaliação', 'clipboard-check'], 'historico' => ['Histórico', 'clock']] as $tab => [$label, $icon])
                <button type="button" wire:click="$set('activeTab', '{{ $tab }}')"
                        class="px-4 py-2 text-xs lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === $tab ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    {{ $label }}
                </button>
            @endforeach
        @else
            @foreach(['ciclos' => 'Ciclos', 'criterios' => 'Critérios', 'ranking' => 'Ranking', 'avaliacoes' => 'Avaliações'] as $tab => $label)
                <button type="button" wire:click="$set('activeTab', '{{ $tab }}')"
                        class="px-4 py-2 text-xs lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === $tab ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    {{ $label }}
                </button>
            @endforeach
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
                             style="width: {{ $this->evaluationProgress['pct'] }}%"></div>
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
                <x-lucide-user-check class="w-5 h-5 text-violet-600" />
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
                    <p class="text-3xl font-black text-violet-600 dark:text-violet-400 lato-black">{{ number_format($survStats['avg'], 1) }}</p>
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
                    <span class="text-violet-500 lato-bold">Avaliação do Gestor</span>
                    nas pesquisas de clima para que os resultados apareçam aqui.
                </p>
            </div>
        @else
            @if($survStats['avg'] !== null)
                {{-- Barra de média geral --}}
                <div class="mb-5">
                    <div class="flex items-center justify-between mb-1.5">
                        <span class="text-xs text-slate-500 lato-regular">Média geral (escala 0–10)</span>
                        <span class="text-xs font-bold text-violet-600 lato-bold">{{ number_format($survStats['avg'], 1) }}/10</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                        <div class="bg-gradient-to-r from-violet-500 to-purple-600 h-2 rounded-full transition-all"
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

                        @if($result->type === 'escala' && $result->avg_scale !== null)
                            <div class="flex items-center gap-3">
                                <div class="flex-1 bg-slate-200 dark:bg-slate-600 rounded-full h-1.5">
                                    <div class="h-1.5 rounded-full transition-all
                                                {{ $result->avg_scale >= 7 ? 'bg-gradient-to-r from-emerald-400 to-emerald-500' : ($result->avg_scale >= 5 ? 'bg-gradient-to-r from-amber-400 to-amber-500' : 'bg-gradient-to-r from-red-400 to-red-500') }}"
                                         style="width: {{ min(100, ($result->avg_scale / 10) * 100) }}%"></div>
                                </div>
                                <span class="text-sm lato-black font-bold shrink-0
                                             {{ $result->avg_scale >= 7 ? 'text-emerald-600' : ($result->avg_scale >= 5 ? 'text-amber-600' : 'text-red-500') }}">
                                    {{ number_format($result->avg_scale, 1) }}<span class="text-[10px] text-slate-400 font-normal lato-regular">/10</span>
                                </span>
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
                                    <div class="sm:w-48 shrink-0">
                                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $criterion->name }}</p>
                                        @if($criterion->description)
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5 leading-relaxed">{{ $criterion->description }}</p>
                                        @endif
                                    </div>

                                    {{-- Star rating --}}
                                    <div class="flex flex-col gap-2 flex-1">
                                        <div class="flex gap-2" x-data="{ hover: 0 }">
                                            @for($s = 1; $s <= 5; $s++)
                                            <button type="button"
                                                    wire:click="saveScore({{ $criterion->id }}, {{ $s }})"
                                                    @mouseenter="hover = {{ $s }}"
                                                    @mouseleave="hover = 0"
                                                    :class="(hover > 0 ? hover >= {{ $s }} : (($wire.scores[{{ $criterion->id }}] ?? 0) >= {{ $s }})) ? 'text-amber-400 scale-110' : 'text-slate-200 dark:text-slate-600'"
                                                    class="transition-all duration-150 cursor-pointer hover:scale-125 focus:outline-none"
                                                    title="Nota {{ $s }}">
                                                <svg class="w-7 h-7" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                                                </svg>
                                            </button>
                                            @endfor

                                            @if(isset($scores[$criterion->id]))
                                                <span class="ml-1 self-center text-xs lato-bold text-slate-500 dark:text-slate-400">
                                                    {{ $scores[$criterion->id] }}/5
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Comentário --}}
                                        <textarea wire:model.lazy="entryComments.{{ $criterion->id }}"
                                                  @change.stop="$wire.saveComment({{ $criterion->id }})"
                                                  rows="1"
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
                <button type="button" wire:click="requestComplete"
                        class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-700 text-white
                               text-sm lato-bold px-5 py-2.5 rounded-xl shadow-sm transition cursor-pointer shrink-0">
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
        <div class="flex flex-wrap gap-2 mb-4">
            <select wire:model.live="historyYearFilter"
                    class="text-xs lato-bold border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2
                           bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 cursor-pointer">
                <option value="">Todos os anos</option>
                @foreach($this->availableYears as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>

            <select wire:model.live="historyCycleFilter"
                    class="text-xs lato-bold border border-slate-200 dark:border-slate-600 rounded-xl px-3 py-2
                           bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-500/30 cursor-pointer">
                <option value="">Todos os ciclos</option>
                @foreach($this->allCycles as $c)
                    <option value="{{ $c->id }}">{{ $c->name }}</option>
                @endforeach
            </select>
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
                        ->map(fn($g) => round($g->avg('score'), 1));
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
                            $empAvg = round($entries->avg('score'), 1);
                        @endphp
                        <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-3">
                            <div class="flex items-center justify-between mb-2">
                                <div class="flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-[10px] lato-black shrink-0">
                                        {{ mb_strtoupper(mb_substr($emp->name, 0, 1)) }}
                                    </div>
                                    <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $emp->name }}</span>
                                </div>
                                <span class="text-xs lato-black font-bold {{ $empAvg >= 4 ? 'text-emerald-600' : ($empAvg >= 3 ? 'text-amber-600' : 'text-red-500') }}">
                                    {{ $empAvg }}/5
                                </span>
                            </div>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-1.5">
                                @foreach($entries as $entry)
                                    <div class="flex items-center gap-1.5 text-[10px] lato-regular text-slate-500 dark:text-slate-400">
                                        <div class="flex gap-0.5">
                                            @for($s = 1; $s <= 5; $s++)
                                                <div class="w-2 h-2 rounded-full {{ $s <= $entry->score ? 'bg-amber-400' : 'bg-slate-200 dark:bg-slate-600' }}"></div>
                                            @endfor
                                        </div>
                                        <span class="truncate">{{ $entry->criterion->name }}</span>
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
                <div class="flex items-center gap-2">
                    <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $crit->name }}</p>
                    @if($crit->is_default)
                        <span class="text-[9px] lato-bold px-1.5 py-0.5 rounded bg-indigo-100 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-400 uppercase tracking-wide">padrão</span>
                    @endif
                    @if(!$crit->is_active)
                        <span class="text-[9px] lato-bold px-1.5 py-0.5 rounded bg-slate-100 text-slate-500 dark:bg-slate-700 uppercase tracking-wide">inativo</span>
                    @endif
                </div>
                @if($crit->description)
                    <p class="text-xs text-slate-400 lato-regular mt-0.5 line-clamp-1">{{ $crit->description }}</p>
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
                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
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
    <div class="mt-6">
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">

            {{-- Cabeçalho tabela --}}
            <div class="grid grid-cols-12 px-5 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
                <div class="col-span-1 text-[10px] lato-bold text-slate-400 uppercase tracking-wide">#</div>
                <div class="col-span-3 text-[10px] lato-bold text-slate-400 uppercase tracking-wide">Gerente</div>
                <div class="col-span-2 text-[10px] lato-bold text-slate-400 uppercase tracking-wide">Departamento</div>
                <div class="col-span-2 text-[10px] lato-bold text-violet-500 uppercase tracking-wide text-center">
                    Avaliação Gestor
                </div>
                <div class="col-span-2 text-[10px] lato-bold text-slate-400 uppercase tracking-wide text-center">Satisfação</div>
                <div class="col-span-2 text-[10px] lato-bold text-slate-400 uppercase tracking-wide text-right">Ciclos</div>
            </div>

            @forelse($this->ranking as $i => $item)
            <div class="grid grid-cols-12 px-5 py-3.5 items-center border-b border-slate-50 dark:border-slate-700/50 last:border-0
                        {{ $i < 3 ? 'hover:bg-amber-50/50 dark:hover:bg-amber-900/10' : 'hover:bg-slate-50 dark:hover:bg-slate-700/30' }} transition">

                {{-- Posição --}}
                <div class="col-span-1">
                    @if($i === 0)
                        <span class="text-lg">🥇</span>
                    @elseif($i === 1)
                        <span class="text-lg">🥈</span>
                    @elseif($i === 2)
                        <span class="text-lg">🥉</span>
                    @else
                        <span class="text-sm lato-bold text-slate-400">{{ $i + 1 }}º</span>
                    @endif
                </div>

                {{-- Gerente --}}
                <div class="col-span-3 flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600 flex items-center justify-center text-white text-xs lato-black shrink-0">
                        {{ mb_strtoupper(mb_substr($item->manager->name, 0, 1)) }}
                    </div>
                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $item->manager->name }}</span>
                </div>

                {{-- Departamento --}}
                <div class="col-span-2">
                    <span class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">{{ $item->manager->department?->name ?? '—' }}</span>
                </div>

                {{-- Nota avaliação do gestor (pesquisas is_manager_evaluation) --}}
                <div class="col-span-2 text-center">
                    @if($item->manager_eval_score !== null)
                        <div class="flex flex-col items-center gap-0.5">
                            <span class="text-sm lato-black font-bold text-violet-600 dark:text-violet-400">
                                {{ number_format($item->manager_eval_score, 1) }}
                            </span>
                            <span class="text-[9px] text-slate-400 lato-regular">{{ $item->manager_eval_count }} resp.</span>
                        </div>
                    @else
                        <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                    @endif
                </div>

                {{-- Nota satisfação (pesquisas) --}}
                <div class="col-span-2 text-center">
                    @if($item->survey_score !== null)
                        <span class="text-sm lato-black font-bold {{ $item->survey_score >= 7 ? 'text-emerald-600' : ($item->survey_score >= 5 ? 'text-amber-600' : 'text-red-500') }}">
                            {{ number_format($item->survey_score, 1) }}
                        </span>
                        <span class="text-[10px] text-slate-400">/10</span>
                    @else
                        <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                    @endif
                </div>

                {{-- Ciclos --}}
                <div class="col-span-2 text-right">
                    <span class="text-xs lato-bold text-slate-600 dark:text-slate-300">{{ $item->cycles_completed }}</span>
                </div>

            </div>
            @empty
                <div class="p-10 text-center">
                    <x-lucide-trophy class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                    <p class="text-sm text-slate-400 lato-regular">Nenhum gerente cadastrado ainda.</p>
                </div>
            @endforelse
        </div>
        <p class="text-[11px] text-slate-400 lato-regular mt-2 px-1">
            Ranking ordenado pela nota de <span class="text-violet-500 lato-bold">Avaliação do Gestor</span> — média das respostas às perguntas marcadas como "Avaliação do Gestor" nas pesquisas de clima (escala 0–10). <span class="text-slate-400">Satisfação</span> = média geral de todas as perguntas de escala das pesquisas do departamento.
        </p>
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
         MODAL: CICLO
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($cycleModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" wire:click="$set('cycleModal', false)"></div>
        <div class="relative z-10 bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md">

            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <x-lucide-clipboard-list class="w-4 h-4 text-indigo-600" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white lato-black">
                        {{ $editingCycleId ? 'Editar ciclo' : 'Novo ciclo de avaliação' }}
                    </h2>
                </div>
                <button type="button" wire:click="$set('cycleModal', false)"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Nome do ciclo <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="cycleName" placeholder="Ex: Avaliação Semestral 2026"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                  bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                    @error('cycleName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Data de início <span class="text-red-400">*</span></label>
                        <input type="date" wire:model="cycleStart"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                        @error('cycleStart') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Data de fim <span class="text-red-400">*</span></label>
                        <input type="date" wire:model="cycleEnd"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                        @error('cycleEnd') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Descrição</label>
                    <textarea wire:model="cycleDescription" rows="2" placeholder="Descreva o objetivo deste ciclo..."
                              class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                     bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                     placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular resize-none"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                <button type="button" wire:click="$set('cycleModal', false)"
                        class="px-4 py-2 text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition cursor-pointer">
                    Cancelar
                </button>
                <button type="button" wire:click="saveCycle"
                        wire:loading.attr="disabled" wire:target="saveCycle"
                        class="flex items-center gap-2 px-5 py-2 text-sm lato-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition cursor-pointer disabled:opacity-60">
                    <x-lucide-save class="w-3.5 h-3.5" />
                    {{ $editingCycleId ? 'Atualizar' : 'Criar ciclo' }}
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         MODAL: CRITÉRIO
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($criterionModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" wire:click="$set('criterionModal', false)"></div>
        <div class="relative z-10 bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm">

            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <x-lucide-award class="w-4 h-4 text-indigo-600" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white lato-black">
                        {{ $editingCriterionId ? 'Editar critério' : 'Novo critério' }}
                    </h2>
                </div>
                <button type="button" wire:click="$set('criterionModal', false)"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Nome <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="criterionName" placeholder="Ex: Relacionamento interpessoal"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                  bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                    @error('criterionName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Descrição</label>
                    <textarea wire:model="criterionDescription" rows="2" placeholder="Descreva o que este critério avalia..."
                              class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                     bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                     placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular resize-none"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                <button type="button" wire:click="$set('criterionModal', false)"
                        class="px-4 py-2 text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition cursor-pointer">
                    Cancelar
                </button>
                <button type="button" wire:click="saveCriterion"
                        wire:loading.attr="disabled" wire:target="saveCriterion"
                        class="flex items-center gap-2 px-5 py-2 text-sm lato-bold bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl shadow-sm transition cursor-pointer disabled:opacity-60">
                    <x-lucide-save class="w-3.5 h-3.5" />
                    {{ $editingCriterionId ? 'Atualizar' : 'Criar' }}
                </button>
            </div>
        </div>
    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         MODAL: CONFIRMAÇÃO
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($confirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" wire:click="$set('confirmModal', false)"></div>
        <div class="relative z-10 bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-6">

            @php
                $confirmConfig = match($confirmType) {
                    'activate_cycle'      => ['Ativar ciclo', 'Ao ativar, qualquer ciclo ativo atual será encerrado automaticamente. Os gerentes poderão iniciar suas avaliações.', 'Ativar', 'emerald'],
                    'close_cycle'         => ['Encerrar ciclo', 'Ao encerrar, os gerentes não poderão mais realizar ou editar avaliações neste ciclo.', 'Encerrar', 'rose'],
                    'delete_cycle'        => ['Excluir ciclo', 'Esta ação é irreversível. Todas as avaliações vinculadas a este ciclo serão excluídas.', 'Excluir', 'red'],
                    'complete_evaluation' => ['Finalizar avaliação', 'Após finalizar, os dados não poderão ser editados. Tem certeza que deseja continuar?', 'Finalizar', 'emerald'],
                    default               => ['Confirmar', 'Deseja confirmar esta ação?', 'Confirmar', 'indigo'],
                };
            @endphp

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-{{ $confirmConfig[3] }}-100 dark:bg-{{ $confirmConfig[3] }}-900/30 flex items-center justify-center shrink-0">
                    @if(in_array($confirmType, ['delete_cycle', 'close_cycle']))
                        <x-lucide-alert-triangle class="w-5 h-5 text-{{ $confirmConfig[3] }}-600" />
                    @else
                        <x-lucide-check-circle class="w-5 h-5 text-{{ $confirmConfig[3] }}-600" />
                    @endif
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white lato-black">{{ $confirmConfig[0] }}</h3>
            </div>

            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular leading-relaxed mb-6">
                {{ $confirmConfig[1] }}
            </p>

            <div class="flex gap-3 justify-end">
                <button type="button" wire:click="$set('confirmModal', false)"
                        class="px-4 py-2 text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition cursor-pointer">
                    Cancelar
                </button>
                <button type="button" wire:click="confirmAction"
                        wire:loading.attr="disabled" wire:target="confirmAction"
                        class="px-5 py-2 text-sm lato-bold text-white rounded-xl shadow-sm transition cursor-pointer disabled:opacity-60
                               bg-{{ $confirmConfig[3] }}-600 hover:bg-{{ $confirmConfig[3] }}-700">
                    {{ $confirmConfig[2] }}
                </button>
            </div>
        </div>
    </div>
    @endif

    <livewire:components.ui.modal.alert-modal />

</div>
