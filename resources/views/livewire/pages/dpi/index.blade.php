<div class="p-4 sm:p-6 lg:p-8 space-y-6" wire:poll.15s="refreshIfIdle">

    {{-- ── Cabeçalho ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white lato-black tracking-tight">
                DPI — Desenvolvimento Profissional Individual
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mt-1">
                Planeje e acompanhe seu crescimento profissional ao longo do ano
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">

            {{-- Indicador de atualização em tempo real --}}
            <span wire:loading.flex wire:target="refreshIfIdle"
                  class="hidden items-center gap-1.5 text-[11px] text-slate-400 lato-regular">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                Atualizando...
            </span>

        {{-- Seletor de ano --}}
        <div class="relative">
            <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <select wire:model.live="selectedYear"
                    class="appearance-none pl-9 pr-8 py-2.5 text-sm lato-bold rounded-xl
                           bg-slate-50 dark:bg-slate-700
                           text-slate-700 dark:text-slate-200
                           border border-slate-200 dark:border-slate-600
                           focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                           cursor-pointer transition">
                @foreach ($this->years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
            <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
        </div>
        </div>{{-- fim flex gap-3 --}}
    </div>

    {{-- ── Tabs ─────────────────────────────────────────────────────── --}}
    @if (auth()->user()->isGerente() || auth()->user()->isRhOuDp())
        @php $pendingCount = auth()->user()->isRhOuDp() ? $this->pendingGerentePlans->count() : 0; @endphp
        <div class="flex flex-wrap items-center gap-1 bg-white dark:bg-slate-800
                    border border-slate-200 dark:border-slate-700
                    rounded-xl p-1 w-fit">

            {{-- Tab Meu Plano --}}
            <button wire:click="$set('activeTab','meu_plano')" type="button"
                    class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                           {{ $activeTab === 'meu_plano'
                               ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                               : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <x-lucide-notebook-pen class="w-3.5 h-3.5" /> Meu Plano
            </button>

            {{-- Tab Equipe --}}
            <button wire:click="$set('activeTab','equipe')" type="button"
                    class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                           {{ $activeTab === 'equipe'
                               ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                               : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <x-lucide-users class="w-3.5 h-3.5" /> Equipe
            </button>

            {{-- Tab Análises --}}
            <button wire:click="$set('activeTab','analises')" type="button"
                    class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                           {{ $activeTab === 'analises'
                               ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                               : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <x-lucide-brain-circuit class="w-3.5 h-3.5" /> Análises
            </button>

            {{-- Tab Visualizações --}}
            @if (auth()->user()->isRhOuDp() || auth()->user()->isGerente())
                <button wire:click="$set('activeTab','visualizacoes')" type="button"
                        class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === 'visualizacoes'
                                   ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                                   : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-calendar-days class="w-3.5 h-3.5" /> Visualizações
                </button>
            @endif

            {{-- Tab Aprovações: apenas RH/DP --}}
            @if (auth()->user()->isRhOuDp())
                <button wire:click="$set('activeTab','aprovacoes')" type="button"
                        class="relative flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === 'aprovacoes'
                                   ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                                   : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-clipboard-check class="w-3.5 h-3.5" /> Aprovações
                    @if ($pendingCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1
                                     rounded-full bg-amber-500 text-white text-[10px] lato-bold
                                     flex items-center justify-center leading-none">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </button>

                {{-- Tab Relatório: apenas RH/DP --}}
                <button wire:click="$set('activeTab','relatorio')" type="button"
                        class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === 'relatorio'
                                   ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                                   : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-file-bar-chart class="w-3.5 h-3.5" /> Relatório
                </button>
            @endif

            {{-- Tab Relatório do Setor: apenas Gerente --}}
            @if (auth()->user()->isGerente())
                <button wire:click="$set('activeTab','relatorio_setor')" type="button"
                        class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === 'relatorio_setor'
                                   ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                                   : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-bar-chart-3 class="w-3.5 h-3.5" /> Meu Setor
                </button>
            @endif
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         ABA: MEU PLANO
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'meu_plano')
        @php $plan = $this->myPlan; @endphp

        {{-- Sem plano --}}
        @if (! $plan)
            <div class="flex flex-col items-center justify-center py-20 text-center
                        bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-100
                            dark:from-blue-900/30 dark:to-indigo-900/30
                            flex items-center justify-center mb-4">
                    <x-lucide-book-open class="w-7 h-7 text-blue-500" />
                </div>
                <h2 class="text-lg font-bold text-slate-700 dark:text-slate-200 lato-bold mb-1">
                    Nenhum plano para {{ $selectedYear }}
                </h2>

                @if (auth()->user()->isGerente() || auth()->user()->isRhOuDp())
                    <p class="text-sm text-slate-400 lato-regular mb-6 max-w-xs">
                        Crie seu Plano de Desenvolvimento Individual e defina seus objetivos para o ano.
                    </p>
                    <button wire:click="createPlan" type="button"
                            class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm lato-bold
                                   bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                   hover:from-blue-600 hover:to-indigo-700
                                   shadow-lg shadow-blue-500/20 transition cursor-pointer">
                        <x-lucide-plus class="w-4 h-4" />
                        Criar Plano {{ $selectedYear }}
                    </button>
                @else
                    <p class="text-sm text-slate-400 lato-regular mb-3 max-w-xs">
                        Seu plano ainda não foi criado para este ano.
                    </p>
                    <p class="text-xs text-slate-400 lato-regular flex items-center gap-1.5">
                        <x-lucide-clock class="w-3.5 h-3.5 text-slate-300" />
                        Aguarde seu gestor ou o RH iniciar seu DPI.
                    </p>
                @endif
            </div>

        {{-- Plano existente --}}
        @else
            @php
                $isManager = auth()->user()->isGerente() || auth()->user()->isRhOuDp();

                // Plano do RH para o Gestor atual: fluxo diferenciado (RH aprova no final)
                $planCreatedByRhForGerente = $plan->wasCreatedByRh() && auth()->user()->isGerente();

                // Gestor edita estrutura (competências/ações) apenas no próprio plano auto-criado
                $canEdit = $isManager
                    && ! $planCreatedByRhForGerente
                    && in_array($plan->status, ['rascunho', 'reprovado']);

                // Gestor pode executar ações (toggle/upload) no próprio plano RH quando 'aprovado' ou 'reprovado'
                $gerenteCanExecute = $planCreatedByRhForGerente
                    && in_array($plan->status, ['aprovado', 'reprovado']);

                $statusColors = [
                    'rascunho'  => ['bg-slate-100 dark:bg-slate-700', 'text-slate-600 dark:text-slate-300'],
                    'enviado'   => ['bg-amber-50 dark:bg-amber-900/20',  'text-amber-700 dark:text-amber-300'],
                    'aprovado'  => ['bg-emerald-50 dark:bg-emerald-900/20', 'text-emerald-700 dark:text-emerald-300'],
                    'reprovado' => ['bg-red-50 dark:bg-red-900/20', 'text-red-600 dark:text-red-400'],
                    'concluido' => ['bg-teal-50 dark:bg-teal-900/20', 'text-teal-700 dark:text-teal-300'],
                ];
                [$sbg, $stx] = $statusColors[$plan->status] ?? $statusColors['rascunho'];
            @endphp

            {{-- Barra de status + ações --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 sm:p-5
                        flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="flex items-center gap-4 flex-wrap">
                    {{-- Progress ring simulado --}}
                    <div class="relative w-14 h-14 shrink-0">
                        <svg class="w-14 h-14 -rotate-90" viewBox="0 0 56 56">
                            <circle cx="28" cy="28" r="22" fill="none" stroke-width="5"
                                    class="stroke-slate-100 dark:stroke-slate-700"/>
                            <circle cx="28" cy="28" r="22" fill="none" stroke-width="5"
                                    stroke-dasharray="{{ 2 * 3.14159 * 22 }}"
                                    stroke-dashoffset="{{ 2 * 3.14159 * 22 * (1 - $plan->progress_percent / 100) }}"
                                    stroke-linecap="round"
                                    class="{{ $plan->progress_percent === 100 ? 'stroke-emerald-500' : 'stroke-blue-500' }}"/>
                        </svg>
                        <span class="absolute inset-0 flex items-center justify-center
                                     text-xs font-bold lato-bold text-slate-700 dark:text-slate-200">
                            {{ $plan->progress_percent }}%
                        </span>
                    </div>
                    <div>
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-sm font-bold text-slate-800 dark:text-white lato-bold">
                                Plano DPI {{ $selectedYear }}
                            </span>
                            <span class="text-[11px] lato-bold px-2 py-0.5 rounded-full {{ $sbg }} {{ $stx }}">
                                {{ $plan->status_label }}
                            </span>
                        </div>
                        <p class="text-xs text-slate-400 lato-regular">
                            {{ $plan->goals->count() }} {{ $plan->goals->count() === 1 ? 'competência' : 'competências' }}
                            ·
                            {{ $plan->actions->count() }} {{ $plan->actions->count() === 1 ? 'ação' : 'ações' }}
                        </p>
                        @if ($plan->manager_feedback)
                            <p class="text-xs mt-1 {{ $plan->status === 'reprovado' ? 'text-red-500' : 'text-emerald-600' }} lato-regular">
                                <span class="font-semibold">Feedback:</span> {{ $plan->manager_feedback }}
                            </p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center gap-2 flex-wrap">

                    {{-- Gerenciar Templates: sempre visível para qualquer RH ou Gerente --}}
                    @if ($isManager)
                        <button wire:click="openManageTemplates" type="button"
                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                       bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                                       text-slate-500 dark:text-slate-400
                                       hover:bg-slate-50 dark:hover:bg-slate-700
                                       shadow-sm transition cursor-pointer">
                            <x-lucide-settings-2 class="w-3.5 h-3.5" /> Templates
                        </button>
                    @endif

                    @if ($planCreatedByRhForGerente)
                        {{-- ── DPI do Gestor (criado pelo RH): Gestor executa → envia ao RH → RH conclui ── --}}
                        @if ($plan->status === 'rascunho')
                            {{-- Em preparação pelo RH, Gestor aguarda --}}
                            <span class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                         bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400">
                                <x-lucide-clock class="w-3.5 h-3.5" /> Em preparação pelo RH
                            </span>
                        @elseif ($plan->status === 'aprovado')
                            {{-- Gestor está executando as ações --}}
                            <button wire:click="submitGerentePlanToRh" type="button"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                           bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white
                                           shadow-md shadow-emerald-500/20 transition cursor-pointer">
                                <x-lucide-send class="w-3.5 h-3.5" /> Finalizar e Enviar ao RH
                            </button>
                        @elseif ($plan->status === 'enviado')
                            {{-- Aguardando revisão do RH --}}
                            <span class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                         bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400
                                         border border-amber-200 dark:border-amber-800/40">
                                <x-lucide-hourglass class="w-3.5 h-3.5" /> Aguardando revisão do RH
                            </span>
                        @elseif ($plan->status === 'reprovado')
                            {{-- RH devolveu para correções --}}
                            <button wire:click="submitGerentePlanToRh" type="button"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                           bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white
                                           shadow-md shadow-amber-500/20 transition cursor-pointer">
                                <x-lucide-refresh-cw class="w-3.5 h-3.5" /> Corrigido — Reenviar ao RH
                            </button>
                        @elseif ($plan->status === 'concluido')
                            {{-- Aprovado pelo RH --}}
                            <span class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                         bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400
                                         border border-teal-200 dark:border-teal-800/40">
                                <x-lucide-shield-check class="w-3.5 h-3.5" /> DPI aprovado pelo RH
                            </span>
                        @endif

                    @else
                        {{-- ── DPI do próprio Gestor/RH (auto-gerenciado) ── --}}
                        @if ($canEdit)
                            {{-- Usar Template: só quando há templates e o plano é editável --}}
                            @if ($this->availableTemplates->count() > 0)
                                <button wire:click="openTemplateModal" type="button"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                               bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-700
                                               text-indigo-600 dark:text-indigo-400
                                               hover:bg-indigo-50 dark:hover:bg-indigo-900/30
                                               shadow-sm transition cursor-pointer">
                                    <x-lucide-layout-template class="w-3.5 h-3.5" /> Usar Template
                                </button>
                            @endif

                            <button wire:click="openGoalModal" type="button"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                           bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                                           shadow-md shadow-blue-500/20 transition cursor-pointer">
                                <x-lucide-plus class="w-3.5 h-3.5" /> Competência
                            </button>

                            @if ($plan->goals->count() > 0)
                                <button wire:click="submitPlan" type="button"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                               bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white
                                               shadow-md shadow-emerald-500/20 transition cursor-pointer">
                                    <x-lucide-rocket class="w-3.5 h-3.5" /> Publicar Plano
                                </button>
                            @endif
                        @elseif ($plan->status === 'aprovado')
                            <button wire:click="retractPlan" type="button"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                           border border-slate-200 dark:border-slate-700
                                           text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700
                                           transition cursor-pointer">
                                <x-lucide-pencil class="w-3.5 h-3.5" /> Reabrir para edição
                            </button>
                        @endif
                    @endif

                </div>
            </div>

            {{-- Lista de Competências --}}
            @if ($plan->goals->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-center
                            bg-white dark:bg-slate-800 rounded-2xl border border-dashed
                            border-slate-200 dark:border-slate-700">
                    <x-lucide-brain class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2" />
                    <p class="text-sm text-slate-400 lato-regular">Nenhuma competência mapeada ainda.</p>
                    @if ($canEdit)
                        <button wire:click="openGoalModal" type="button"
                                class="mt-3 text-sm text-blue-500 hover:text-blue-600 lato-bold transition cursor-pointer">
                            + Mapear primeira competência
                        </button>
                    @endif
                </div>
            @else
                <div class="space-y-4">
                    @foreach ($plan->goals as $goal)
                        @php
                            $prioCfg = match($goal->priority) {
                                'alta'  => ['bg-red-50 dark:bg-red-900/20', 'text-red-600 dark:text-red-400', 'Alta'],
                                'media' => ['bg-amber-50 dark:bg-amber-900/20', 'text-amber-600 dark:text-amber-400', 'Média'],
                                'baixa' => ['bg-blue-50 dark:bg-blue-900/20', 'text-blue-500 dark:text-blue-400', 'Baixa'],
                                default => ['bg-slate-50 dark:bg-slate-700', 'text-slate-500 dark:text-slate-400', $goal->priority],
                            };
                            [$pbg, $ptx, $plabel] = $prioCfg;
                        @endphp

                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                            {{-- Header da competência --}}
                            <div class="p-4 sm:p-5">
                                <div class="flex items-start justify-between gap-3">
                                    <div class="flex-1 min-w-0">
                                        <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                            <span class="text-[11px] lato-bold px-2 py-0.5 rounded-full {{ $pbg }} {{ $ptx }}">
                                                {{ $plabel }}
                                            </span>
                                            <span class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                                <x-lucide-gauge class="w-3 h-3" />
                                                {{ $goal->nivel_atual_label }} → {{ $goal->nivel_meta_label }}
                                            </span>
                                        </div>
                                        <h3 class="text-sm font-bold text-slate-800 dark:text-white lato-bold leading-snug">
                                            {{ $goal->name }}
                                        </h3>

                                        {{-- Indicador visual de níveis (5 círculos) --}}
                                        <div class="flex items-center gap-1.5 mt-2.5">
                                            @for ($lvl = 1; $lvl <= 5; $lvl++)
                                                @if ($lvl <= $goal->nivel_atual)
                                                    <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center shrink-0"
                                                         title="{{ \App\Models\DpiGoal::nivelLabels()[$lvl] ?? $lvl }}">
                                                        <span class="text-[9px] font-bold text-white">{{ $lvl }}</span>
                                                    </div>
                                                @elseif ($lvl === $goal->nivel_meta)
                                                    <div class="w-6 h-6 rounded-full border-2 border-indigo-400 flex items-center justify-center shrink-0"
                                                         title="{{ \App\Models\DpiGoal::nivelLabels()[$lvl] ?? $lvl }}">
                                                        <span class="text-[9px] font-bold text-indigo-500">{{ $lvl }}</span>
                                                    </div>
                                                @else
                                                    <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0"
                                                         title="{{ \App\Models\DpiGoal::nivelLabels()[$lvl] ?? $lvl }}">
                                                        <span class="text-[9px] text-slate-400 dark:text-slate-500">{{ $lvl }}</span>
                                                    </div>
                                                @endif
                                            @endfor
                                            <span class="text-[11px] text-slate-400 lato-regular ml-1">
                                                {{ $goal->progress_percent }}% do objetivo
                                            </span>
                                        </div>
                                    </div>

                                    {{-- Botões de ação --}}
                                    <div class="flex items-center gap-1 shrink-0">
                                        @if ($canEdit)
                                            <button wire:click="openGoalModal({{ $goal->id }})" type="button"
                                                    title="Editar competência / ações"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg
                                                           text-slate-400 hover:text-slate-700 hover:bg-slate-100
                                                           dark:hover:bg-slate-700 transition cursor-pointer">
                                                <x-lucide-pencil class="w-3.5 h-3.5" />
                                            </button>
                                            <button wire:click="confirmDeleteGoal({{ $goal->id }})" type="button"
                                                    title="Remover competência"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg
                                                           text-slate-400 hover:text-red-500 hover:bg-red-50
                                                           dark:hover:bg-red-900/20 transition cursor-pointer">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </button>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            {{-- Ações de desenvolvimento --}}
                            @if ($goal->actions->isNotEmpty())
                                <div class="border-t border-slate-100 dark:border-slate-700">
                                    @foreach ($goal->actions as $action)
                                        <div class="flex items-start gap-3 px-5 py-3
                                                    hover:bg-slate-50 dark:hover:bg-slate-700/30
                                                    transition border-b border-slate-50 dark:border-slate-700/50 last:border-0">

                                            {{-- Toggle status:
                                                 - Gestor/RH: sempre pode
                                                 - Gestor no próprio plano RH: só quando ativo (gerenteCanExecute)
                                                 - Funcionário: indicador estático --}}
                                            @if ($isManager || $gerenteCanExecute)
                                                <button wire:click="toggleActionStatus({{ $action->id }})" type="button"
                                                        title="{{ $action->status_label }}"
                                                        class="mt-0.5 w-4 h-4 rounded-full border-2 shrink-0 transition cursor-pointer
                                                               {{ $action->status === 'concluido'
                                                                   ? 'bg-emerald-500 border-emerald-500'
                                                                   : ($action->status === 'em_andamento'
                                                                       ? 'border-blue-500 bg-blue-100 dark:bg-blue-900/30'
                                                                       : 'border-slate-300 dark:border-slate-600 bg-transparent') }}">
                                                </button>
                                            @else
                                                {{-- Funcionário / Gestor em estado não-executável: indicador estático --}}
                                                <span class="mt-0.5 w-4 h-4 rounded-full border-2 shrink-0
                                                             {{ $action->status === 'concluido'
                                                                 ? 'bg-emerald-500 border-emerald-500'
                                                                 : ($action->status === 'em_andamento'
                                                                     ? 'border-blue-500 bg-blue-100 dark:bg-blue-900/30'
                                                                     : 'border-slate-300 dark:border-slate-600 bg-transparent') }}">
                                                </span>
                                            @endif

                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="min-w-0">
                                                        <p class="text-sm lato-regular text-slate-700 dark:text-slate-200
                                                                   leading-snug {{ $action->status === 'concluido' ? 'line-through text-slate-400' : '' }}">
                                                            {{ $action->title }}
                                                        </p>
                                                        <div class="flex flex-wrap items-center gap-2 mt-1">
                                                            <span class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                                                <x-lucide-tag class="w-3 h-3" />
                                                                {{ $action->type_label }}
                                                            </span>
                                                            @if ($action->target_date)
                                                                <span class="text-[11px] {{ $action->target_date->isPast() && $action->status !== 'concluido' ? 'text-red-400' : 'text-slate-400' }} lato-regular flex items-center gap-1">
                                                                    <x-lucide-calendar class="w-3 h-3" />
                                                                    {{ $action->target_date->format('d/m/Y') }}
                                                                </span>
                                                            @endif
                                                            @if ($action->description)
                                                                <span class="text-[11px] text-slate-400 lato-regular truncate max-w-[180px]">
                                                                    · {{ Str::limit($action->description, 60) }}
                                                                </span>
                                                            @endif
                                                            @if ($action->attachment_name)
                                                                <span class="text-[11px] text-indigo-500 lato-bold flex items-center gap-1">
                                                                    <x-lucide-paperclip class="w-3 h-3" />
                                                                    {{ Str::limit($action->attachment_name, 30) }}
                                                                </span>
                                                            @endif
                                                            @if ($action->validated_at && $action->status === 'concluido')
                                                                <span class="text-[11px] text-emerald-500 lato-regular flex items-center gap-1">
                                                                    <x-lucide-shield-check class="w-3 h-3" />
                                                                    Validado em {{ $action->validated_at->format('d/m/Y') }}
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if ($isManager || $gerenteCanExecute)
                                                        <div class="flex items-center gap-0.5 shrink-0">
                                                            {{-- Upload de evidência: Gestor/RH no próprio plano ou plano RH --}}
                                                            <button wire:click="openUploadModal({{ $action->id }})" type="button"
                                                                    title="{{ $action->attachment_name ? 'Substituir evidência' : 'Anexar evidência (diploma, certificado...)' }}"
                                                                    class="w-6 h-6 flex items-center justify-center rounded-lg transition cursor-pointer
                                                                           {{ $action->attachment_name ? 'text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20' : 'text-slate-300 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20' }}">
                                                                <x-lucide-paperclip class="w-3 h-3" />
                                                            </button>
                                                            @if ($canEdit)
                                                                {{-- Editar/excluir estrutura apenas para planos auto-gerenciados --}}
                                                                <button wire:click="openGoalModal({{ $goal->id }})" type="button"
                                                                        title="Editar ação"
                                                                        class="w-6 h-6 flex items-center justify-center rounded-lg
                                                                               text-slate-300 hover:text-slate-600 dark:hover:text-slate-300
                                                                               hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                                                    <x-lucide-pencil class="w-3 h-3" />
                                                                </button>
                                                                <button wire:click="confirmDeleteAction({{ $action->id }})" type="button"
                                                                        class="w-6 h-6 flex items-center justify-center rounded-lg
                                                                               text-slate-300 hover:text-red-500
                                                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                                                                    <x-lucide-trash-2 class="w-3 h-3" />
                                                                </button>
                                                            @endif
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @elseif ($canEdit)
                                <div class="border-t border-slate-100 dark:border-slate-700 px-5 py-3">
                                    <button wire:click="openGoalModal({{ $goal->id }})" type="button"
                                            class="text-xs text-slate-400 hover:text-blue-500 lato-regular transition cursor-pointer
                                                   flex items-center gap-1">
                                        <x-lucide-plus class="w-3 h-3" /> Adicionar ação de desenvolvimento
                                    </button>
                                </div>
                            @endif

                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         MODAL: APLICAR TEMPLATE
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($templateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data
             x-on:keydown.escape.window="$wire.closeTemplateModal()">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="closeTemplateModal"></div>

            <div class="relative w-full max-w-2xl max-h-[85vh] flex flex-col
                        bg-white dark:bg-slate-800
                        rounded-2xl border border-slate-200 dark:border-slate-700
                        shadow-2xl">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                            <x-lucide-layout-template class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 lato-bold">Aplicar Template</h2>
                            <p class="text-xs text-slate-400">Selecione um template para adicionar ao seu plano</p>
                        </div>
                    </div>
                    <button wire:click="closeTemplateModal" type="button"
                            class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Lista de templates --}}
                <div class="flex-1 overflow-y-auto p-4 space-y-3">
                    @forelse ($this->availableTemplates as $tpl)
                        <div class="rounded-xl border border-slate-200 dark:border-slate-700
                                    bg-slate-50 dark:bg-slate-800/60 overflow-hidden">

                            {{-- Info + botão --}}
                            <div class="flex items-start gap-3 px-4 py-3">
                                <div class="w-9 h-9 rounded-lg bg-indigo-100 dark:bg-indigo-900/40
                                            flex items-center justify-center shrink-0 mt-0.5">
                                    <x-lucide-layers class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100 lato-bold">{{ $tpl->name }}</p>
                                    @if ($tpl->description)
                                        <p class="text-xs text-slate-400 mt-0.5">{{ $tpl->description }}</p>
                                    @endif
                                    <div class="flex items-center gap-3 mt-1.5 text-xs text-slate-400">
                                        <span class="flex items-center gap-1">
                                            <x-lucide-target class="w-3 h-3" />
                                            {{ $tpl->goals->count() }} {{ $tpl->goals->count() === 1 ? 'competência' : 'competências' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <x-lucide-zap class="w-3 h-3" />
                                            {{ $tpl->totalActionsCount() }} {{ $tpl->totalActionsCount() === 1 ? 'ação' : 'ações' }}
                                        </span>
                                        <span class="flex items-center gap-1">
                                            <x-lucide-user class="w-3 h-3" />
                                            {{ $tpl->creator->name ?? '—' }}
                                        </span>
                                    </div>
                                </div>
                                <button wire:click="applyTemplate({{ $tpl->id }})"
                                        wire:loading.attr="disabled"
                                        type="button"
                                        class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs lato-bold
                                               bg-indigo-600 hover:bg-indigo-700 text-white
                                               shadow-sm shadow-indigo-500/30 transition cursor-pointer">
                                    <x-lucide-plus class="w-3.5 h-3.5" /> Aplicar
                                </button>
                            </div>

                            {{-- Preview das metas --}}
                            @if ($tpl->goals->count() > 0)
                                <div class="border-t border-slate-200 dark:border-slate-700 px-4 py-2.5 space-y-1.5">
                                    @foreach ($tpl->goals as $tGoal)
                                        <div class="flex items-start gap-2">
                                            <span class="mt-0.5 text-[10px] font-bold px-1.5 py-0.5 rounded
                                                         {{ match($tGoal->priority) {
                                                             'alta'  => 'bg-red-100 text-red-600 dark:bg-red-900/40 dark:text-red-400',
                                                             'media' => 'bg-amber-100 text-amber-600 dark:bg-amber-900/40 dark:text-amber-400',
                                                             default => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
                                                         } }}">
                                                {{ strtoupper(substr($tGoal->priority, 0, 3)) }}
                                            </span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold text-slate-700 dark:text-slate-200">{{ $tGoal->name }}</p>
                                                @if ($tGoal->actions->count() > 0)
                                                    <div class="flex flex-wrap gap-1 mt-1">
                                                        @foreach ($tGoal->actions as $tAct)
                                                            <span class="text-[10px] px-1.5 py-0.5 rounded-full
                                                                         {{ match($tAct->type) {
                                                                             'curso'        => 'bg-indigo-50 text-indigo-600 dark:bg-indigo-900/30 dark:text-indigo-300',
                                                                             'certificacao' => 'bg-amber-50 text-amber-600 dark:bg-amber-900/30 dark:text-amber-300',
                                                                             'leitura'      => 'bg-emerald-50 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-300',
                                                                             'mentoria'     => 'bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-300',
                                                                             'projeto'      => 'bg-orange-50 text-orange-600 dark:bg-orange-900/30 dark:text-orange-300',
                                                                             'workshop'     => 'bg-pink-50 text-pink-600 dark:bg-pink-900/30 dark:text-pink-300',
                                                                             default        => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-300',
                                                                         } }}">
                                                                {{ $tAct->type_label }}
                                                            </span>
                                                        @endforeach
                                                        @foreach ($tGoal->actions->take(2) as $tAct)
                                                            <span class="text-[10px] text-slate-400 dark:text-slate-500 truncate max-w-[140px]">
                                                                {{ $tAct->title }}
                                                            </span>
                                                        @endforeach
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    @empty
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <x-lucide-layout-template class="w-10 h-10 text-slate-200 dark:text-slate-600 mb-3" />
                            <p class="text-sm text-slate-400">Nenhum template disponível ainda.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         MODAL: GERENCIAR TEMPLATES
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($manageTemplateModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data
             x-on:keydown.escape.window="$wire.closeManageTemplates()">

            {{-- Backdrop --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm"
                 wire:click="closeManageTemplates"></div>

            <div class="relative w-full max-w-3xl max-h-[90vh] flex flex-col
                        bg-white dark:bg-slate-800
                        rounded-2xl border border-slate-200 dark:border-slate-700
                        shadow-2xl">

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                            <x-lucide-settings-2 class="w-4 h-4 text-slate-600 dark:text-slate-300" />
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 lato-bold">Gerenciar Templates</h2>
                            <p class="text-xs text-slate-400">Crie e edite modelos de plano DPI</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2">
                        @if ($templateTab === 'list')
                            <button wire:click="startCreateTemplate" type="button"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs lato-bold
                                           bg-indigo-600 hover:bg-indigo-700 text-white transition cursor-pointer">
                                <x-lucide-plus class="w-3.5 h-3.5" /> Novo Template
                            </button>
                        @else
                            <button wire:click="$set('templateTab','list')" type="button"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs lato-bold
                                           bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600
                                           text-slate-600 dark:text-slate-300 transition cursor-pointer">
                                <x-lucide-arrow-left class="w-3.5 h-3.5" /> Voltar
                            </button>
                        @endif
                        <button wire:click="closeManageTemplates" type="button"
                                class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 transition cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {{-- LISTA de templates --}}
                @if ($templateTab === 'list')
                    <div class="flex-1 overflow-y-auto p-4 space-y-2">
                        @forelse ($this->availableTemplates as $tpl)
                            <div class="flex items-center gap-3 px-4 py-3 rounded-xl
                                        border border-slate-200 dark:border-slate-700
                                        bg-slate-50 dark:bg-slate-800/50">
                                <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40
                                            flex items-center justify-center shrink-0">
                                    <x-lucide-layers class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-bold text-slate-800 dark:text-slate-100 lato-bold truncate">{{ $tpl->name }}</p>
                                    <p class="text-xs text-slate-400">
                                        {{ $tpl->goals->count() }} competências · {{ $tpl->totalActionsCount() }} ações
                                        · por {{ $tpl->creator->name ?? '—' }}
                                    </p>
                                </div>
                                {{-- Toggle ativo --}}
                                <button wire:click="toggleTemplateActive({{ $tpl->id }})" type="button"
                                        title="{{ $tpl->is_active ? 'Desativar' : 'Ativar' }}"
                                        class="p-1.5 rounded-lg transition cursor-pointer
                                               {{ $tpl->is_active
                                                   ? 'text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/20'
                                                   : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                                    @if ($tpl->is_active)
                                        <x-lucide-eye class="w-4 h-4" />
                                    @else
                                        <x-lucide-eye-off class="w-4 h-4" />
                                    @endif
                                </button>
                                {{-- Editar --}}
                                <button wire:click="editTemplate({{ $tpl->id }})" type="button"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-indigo-600
                                               hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer">
                                    <x-lucide-pencil class="w-4 h-4" />
                                </button>
                                {{-- Deletar --}}
                                <button wire:click="deleteTemplate({{ $tpl->id }})"
                                        wire:confirm="Tem certeza que deseja excluir este template?"
                                        type="button"
                                        class="p-1.5 rounded-lg text-slate-400 hover:text-red-500
                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </div>
                        @empty
                            <div class="flex flex-col items-center justify-center py-14 text-center">
                                <x-lucide-layout-template class="w-10 h-10 text-slate-200 dark:text-slate-600 mb-3" />
                                <p class="text-sm text-slate-500 dark:text-slate-400 lato-bold">Nenhum template criado</p>
                                <p class="text-xs text-slate-400 mt-1">Clique em "Novo Template" para começar</p>
                            </div>
                        @endforelse
                    </div>

                {{-- FORMULÁRIO criar/editar --}}
                @elseif ($templateTab === 'create')
                    <div class="flex-1 overflow-y-auto p-5 space-y-5">

                        {{-- Nome e descrição --}}
                        <div class="grid grid-cols-1 gap-3">
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                                    Nome do Template <span class="text-red-400">*</span>
                                </label>
                                <input wire:model="tplName" type="text" placeholder="Ex: Liderança Técnica, Gestão de Projetos..."
                                       class="w-full text-sm px-3 py-2 rounded-lg border
                                              border-slate-200 dark:border-slate-600
                                              bg-white dark:bg-slate-700
                                              text-slate-800 dark:text-slate-100
                                              focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-indigo-400
                                              placeholder-slate-300 dark:placeholder-slate-500" />
                                @error('tplName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 dark:text-slate-300 mb-1">
                                    Descrição <span class="text-slate-400 font-normal">(opcional)</span>
                                </label>
                                <textarea wire:model="tplDescription" rows="2"
                                          placeholder="Descreva o propósito deste template..."
                                          class="w-full text-sm px-3 py-2 rounded-lg border
                                                 border-slate-200 dark:border-slate-600
                                                 bg-white dark:bg-slate-700
                                                 text-slate-800 dark:text-slate-100
                                                 focus:outline-none focus:ring-2 focus:ring-indigo-400
                                                 placeholder-slate-300 dark:placeholder-slate-500 resize-none"></textarea>
                            </div>
                        </div>

                        {{-- Metas --}}
                        <div>
                            <div class="flex items-center justify-between mb-3">
                                <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">
                                    Competências / Metas <span class="text-red-400">*</span>
                                </label>
                                <button wire:click="addTemplateGoalRow" type="button"
                                        class="flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400
                                               hover:text-indigo-800 dark:hover:text-indigo-200 transition cursor-pointer lato-bold">
                                    <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar Meta
                                </button>
                            </div>
                            @error('tplGoals') <p class="text-xs text-red-500 mb-2">{{ $message }}</p> @enderror

                            @if (count($tplGoals) === 0)
                                <div class="flex items-center justify-center py-8 rounded-xl border-2 border-dashed
                                            border-slate-200 dark:border-slate-700 text-slate-400 text-sm">
                                    <x-lucide-plus class="w-4 h-4 mr-1.5" /> Adicione pelo menos uma meta
                                </div>
                            @endif

                            <div class="space-y-3">
                                @foreach ($tplGoals as $gi => $goal)
                                    <div class="rounded-xl border border-slate-200 dark:border-slate-700
                                                bg-slate-50 dark:bg-slate-800/50 overflow-hidden">

                                        {{-- Cabeçalho da meta --}}
                                        <div class="flex items-start gap-2 p-3">
                                            <div class="flex-1 grid grid-cols-12 gap-2">
                                                {{-- Nome --}}
                                                <div class="col-span-12 sm:col-span-5">
                                                    <input wire:model="tplGoals.{{ $gi }}.name"
                                                           type="text" placeholder="Nome da competência..."
                                                           class="w-full text-sm px-2.5 py-1.5 rounded-lg border
                                                                  border-slate-200 dark:border-slate-600
                                                                  bg-white dark:bg-slate-700
                                                                  text-slate-800 dark:text-slate-100
                                                                  focus:outline-none focus:ring-2 focus:ring-indigo-400
                                                                  placeholder-slate-300 dark:placeholder-slate-500" />
                                                    @error("tplGoals.{$gi}.name") <p class="text-xs text-red-500 mt-0.5">{{ $message }}</p> @enderror
                                                </div>
                                                {{-- Prioridade --}}
                                                <div class="col-span-4 sm:col-span-3">
                                                    <select wire:model="tplGoals.{{ $gi }}.priority"
                                                            class="w-full text-sm px-2.5 py-1.5 rounded-lg border
                                                                   border-slate-200 dark:border-slate-600
                                                                   bg-white dark:bg-slate-700
                                                                   text-slate-800 dark:text-slate-100
                                                                   focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                                        <option value="alta">Alta</option>
                                                        <option value="media">Média</option>
                                                        <option value="baixa">Baixa</option>
                                                    </select>
                                                </div>
                                                {{-- Nível atual --}}
                                                <div class="col-span-4 sm:col-span-2">
                                                    <select wire:model="tplGoals.{{ $gi }}.nivel_atual"
                                                            class="w-full text-sm px-2.5 py-1.5 rounded-lg border
                                                                   border-slate-200 dark:border-slate-600
                                                                   bg-white dark:bg-slate-700
                                                                   text-slate-800 dark:text-slate-100
                                                                   focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                                        @foreach ([1=>'Básico',2=>'Elementar',3=>'Intermediário',4=>'Avançado',5=>'Expert'] as $nv => $nl)
                                                            <option value="{{ $nv }}">N{{ $nv }} {{ $nl }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                                {{-- Nível meta --}}
                                                <div class="col-span-4 sm:col-span-2">
                                                    <select wire:model="tplGoals.{{ $gi }}.nivel_meta"
                                                            class="w-full text-sm px-2.5 py-1.5 rounded-lg border
                                                                   border-slate-200 dark:border-slate-600
                                                                   bg-white dark:bg-slate-700
                                                                   text-slate-800 dark:text-slate-100
                                                                   focus:outline-none focus:ring-2 focus:ring-indigo-400">
                                                        @foreach ([1=>'Básico',2=>'Elementar',3=>'Intermediário',4=>'Avançado',5=>'Expert'] as $nv => $nl)
                                                            <option value="{{ $nv }}">N{{ $nv }} {{ $nl }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </div>
                                            {{-- Remover meta --}}
                                            <button wire:click="removeTemplateGoalRow({{ $gi }})" type="button"
                                                    class="p-1.5 rounded-lg text-slate-300 hover:text-red-500
                                                           hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer shrink-0">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>

                                        {{-- Ações de Desenvolvimento --}}
                                        <div class="border-t border-slate-200 dark:border-slate-700">
                                            @php
                                                $tplActionTypes = [
                                                    'curso'        => ['graduation-cap', 'text-blue-500',   'bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300',   'Curso'],
                                                    'certificacao' => ['award',          'text-amber-500',  'bg-amber-50 dark:bg-amber-900/30 text-amber-600 dark:text-amber-300', 'Certificação'],
                                                    'leitura'      => ['book-open',      'text-emerald-500','bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-300','Leitura'],
                                                    'mentoria'     => ['users',          'text-violet-500', 'bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-300', 'Mentoria'],
                                                    'projeto'      => ['folder-open',    'text-orange-500', 'bg-orange-50 dark:bg-orange-900/30 text-orange-600 dark:text-orange-300','Projeto'],
                                                    'workshop'     => ['presentation',   'text-pink-500',   'bg-pink-50 dark:bg-pink-900/30 text-pink-600 dark:text-pink-300',     'Workshop'],
                                                    'outro'        => ['circle-dot',     'text-slate-400',  'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300',   'Outro'],
                                                ];
                                            @endphp

                                            {{-- Header da seção --}}
                                            <div class="flex items-center justify-between px-3 pt-2.5 pb-1.5">
                                                <p class="text-[10px] lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                                    Ações de Desenvolvimento
                                                </p>
                                                <button wire:click="addTemplateActionRow({{ $gi }})" type="button"
                                                        class="flex items-center gap-1 text-xs lato-bold
                                                               text-blue-500 hover:text-blue-600 dark:hover:text-blue-400 transition cursor-pointer">
                                                    <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar ação
                                                </button>
                                            </div>

                                            {{-- Lista de ações --}}
                                            @if (count($goal['actions'] ?? []) > 0)
                                                <div class="divide-y divide-slate-100 dark:divide-slate-700
                                                            border-t border-slate-100 dark:border-slate-700">
                                                    @foreach ($goal['actions'] as $ai => $action)
                                                        @php
                                                            $aType = $action['type'] ?? 'outro';
                                                            [$aIco, $aClr, $aBadge, $aLabel] = $tplActionTypes[$aType] ?? $tplActionTypes['outro'];
                                                        @endphp
                                                        <div class="px-3 py-2.5 bg-white dark:bg-slate-800/40 space-y-2">
                                                            {{-- Linha 1: ícone + título + remover --}}
                                                            <div class="flex items-start gap-2">
                                                                <x-dynamic-component :component="'lucide-'.$aIco"
                                                                    class="w-4 h-4 {{ $aClr }} shrink-0 mt-1" />
                                                                <input wire:model="tplGoals.{{ $gi }}.actions.{{ $ai }}.title"
                                                                       type="text" placeholder="Título da ação de desenvolvimento..."
                                                                       class="flex-1 text-sm px-2.5 py-1.5 rounded-lg border
                                                                              border-slate-200 dark:border-slate-600
                                                                              bg-white dark:bg-slate-700
                                                                              text-slate-700 dark:text-slate-200
                                                                              focus:outline-none focus:ring-2 focus:ring-indigo-400
                                                                              placeholder-slate-300 dark:placeholder-slate-500" />
                                                                <button wire:click="removeTemplateActionRow({{ $gi }}, {{ $ai }})" type="button"
                                                                        class="p-1 rounded-lg text-slate-300 hover:text-red-500
                                                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer shrink-0 mt-0.5">
                                                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                                </button>
                                                            </div>
                                                            {{-- Linha 2: tipo + descrição --}}
                                                            <div class="flex items-start gap-2 pl-6">
                                                                <select wire:model="tplGoals.{{ $gi }}.actions.{{ $ai }}.type"
                                                                        class="text-xs px-2 py-1.5 rounded-lg border
                                                                               border-slate-200 dark:border-slate-600
                                                                               bg-white dark:bg-slate-700
                                                                               text-slate-700 dark:text-slate-200
                                                                               focus:outline-none focus:ring-2 focus:ring-indigo-400 shrink-0">
                                                                    <option value="curso">Curso</option>
                                                                    <option value="certificacao">Certificação</option>
                                                                    <option value="leitura">Leitura</option>
                                                                    <option value="mentoria">Mentoria</option>
                                                                    <option value="projeto">Projeto</option>
                                                                    <option value="workshop">Workshop</option>
                                                                    <option value="outro">Outro</option>
                                                                </select>
                                                                <input wire:model="tplGoals.{{ $gi }}.actions.{{ $ai }}.description"
                                                                       type="text" placeholder="Descrição (opcional)..."
                                                                       class="flex-1 text-xs px-2.5 py-1.5 rounded-lg border
                                                                              border-slate-200 dark:border-slate-600
                                                                              bg-white dark:bg-slate-700
                                                                              text-slate-500 dark:text-slate-400
                                                                              focus:outline-none focus:ring-2 focus:ring-indigo-400
                                                                              placeholder-slate-300 dark:placeholder-slate-500" />
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @else
                                                <p class="px-3 pb-3 text-xs text-slate-400 dark:text-slate-500 lato-regular italic">
                                                    Nenhuma ação adicionada ainda.
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Footer do form --}}
                    <div class="shrink-0 flex items-center justify-end gap-2 px-5 py-4
                                border-t border-slate-100 dark:border-slate-700">
                        <button wire:click="$set('templateTab','list')" type="button"
                                class="px-4 py-2 rounded-lg text-sm lato-bold
                                       bg-slate-100 dark:bg-slate-700
                                       text-slate-600 dark:text-slate-300
                                       hover:bg-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="saveTemplate" type="button"
                                wire:loading.attr="disabled"
                                class="flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm lato-bold
                                       bg-indigo-600 hover:bg-indigo-700 text-white
                                       shadow-sm shadow-indigo-500/20 transition cursor-pointer
                                       disabled:opacity-60">
                            <x-lucide-save class="w-3.5 h-3.5" />
                            <span wire:loading.remove wire:target="saveTemplate">
                                {{ $editTemplateId ? 'Atualizar Template' : 'Salvar Template' }}
                            </span>
                            <span wire:loading wire:target="saveTemplate">Salvando...</span>
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         ABA: EQUIPE
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'equipe' && (auth()->user()->isGerente() || auth()->user()->isRhOuDp()))
        <div class="grid grid-cols-1 lg:grid-cols-4 gap-5">

            {{-- Lista de membros --}}
            <div class="lg:col-span-1">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wider">
                            Colaboradores
                        </p>
                    </div>
                    <div class="max-h-[520px] overflow-y-auto">
                        @php
                            $allMembers   = $this->teamMembers;
                            $viewerIsRh   = auth()->user()->isRhOuDp();
                            // RH: agrupa por departamento; Gerente: lista plana num único grupo anônimo
                            $memberGroups = $viewerIsRh
                                ? $allMembers->groupBy(fn ($m) => $m->department?->name ?? 'Sem departamento')->sortKeys()
                                : collect(['_' => $allMembers]);
                        @endphp

                        @if ($allMembers->isEmpty())
                            <div class="py-8 text-center text-sm text-slate-400 lato-regular">
                                Nenhum colaborador encontrado.
                            </div>
                        @else
                            @foreach ($memberGroups as $deptName => $members)
                                {{-- Cabeçalho do departamento (apenas para RH) --}}
                                @if ($viewerIsRh)
                                    <div class="px-4 py-2 bg-slate-50 dark:bg-slate-700/50
                                                border-b border-slate-100 dark:border-slate-700
                                                sticky top-0 z-10">
                                        <p class="text-[10px] font-bold text-slate-500 dark:text-slate-400
                                                   lato-bold uppercase tracking-wider flex items-center gap-1.5">
                                            <x-lucide-building-2 class="w-3 h-3" />
                                            {{ $deptName }}
                                            <span class="ml-auto font-normal normal-case tracking-normal text-slate-400">
                                                {{ $members->count() }}
                                            </span>
                                        </p>
                                    </div>
                                @endif

                                <div class="divide-y divide-slate-100 dark:divide-slate-700">
                                    @foreach ($members as $member)
                                        @php
                                            $memberPlan = $member->dpiPlans->first();
                                            $mDot = match($memberPlan?->status) {
                                                'aprovado'  => 'bg-emerald-500',
                                                'enviado'   => 'bg-amber-400',
                                                'reprovado' => 'bg-red-400',
                                                'rascunho'  => 'bg-slate-400',
                                                'concluido' => 'bg-teal-500',
                                                default     => 'bg-slate-200 dark:bg-slate-600',
                                            };
                                            $parts    = explode(' ', trim($member->name));
                                            $initials = strtoupper(substr($parts[0],0,1) . (isset($parts[1]) ? substr($parts[1],0,1) : ''));
                                            $palette  = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                            $color    = $palette[abs(crc32($member->name)) % count($palette)];
                                        @endphp
                                        <button wire:click="selectTeamUser({{ $member->id }})" type="button"
                                                class="w-full flex items-center gap-3 px-4 py-3 text-left transition cursor-pointer
                                                       {{ $teamUserId === $member->id
                                                           ? 'bg-blue-50 dark:bg-blue-900/20'
                                                           : 'hover:bg-slate-50 dark:hover:bg-slate-700/40' }}">
                                            <span class="w-8 h-8 rounded-full {{ $color }} text-white text-xs lato-bold
                                                         flex items-center justify-center shrink-0">{{ $initials }}</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $member->name }}</p>
                                                <p class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $mDot }}"></span>
                                                    {{ $memberPlan ? $memberPlan->status_label : 'Sem plano' }}
                                                </p>
                                            </div>
                                        </button>
                                    @endforeach
                                </div>
                            @endforeach
                        @endif
                    </div>
                </div>
            </div>

            {{-- Plano do colaborador selecionado --}}
            <div class="lg:col-span-3">
                @if (! $teamUserId)
                    <div class="flex flex-col items-center justify-center h-64
                                bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <x-lucide-mouse-pointer-2 class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2" />
                        <p class="text-sm text-slate-400 lato-regular">Selecione um colaborador</p>
                    </div>
                @elseif (! $this->teamPlan)
                    <div class="flex flex-col items-center justify-center py-20 text-center
                                bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-100 to-purple-100
                                    dark:from-blue-900/30 dark:to-purple-900/30
                                    flex items-center justify-center mb-4">
                            <x-lucide-book-open class="w-7 h-7 text-blue-500" />
                        </div>
                        <h2 class="text-lg font-bold text-slate-700 dark:text-slate-200 lato-bold mb-1">
                            Nenhum plano para {{ $selectedYear }}
                        </h2>
                        <p class="text-sm text-slate-400 lato-regular mb-6 max-w-xs">
                            Este colaborador ainda não possui um Plano de Desenvolvimento Individual para este ano.
                        </p>
                        <button wire:click="createPlanForEmployee({{ $teamUserId }})" type="button"
                                class="flex items-center gap-2 px-5 py-2.5 rounded-xl text-sm lato-bold
                                       bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                       hover:from-blue-600 hover:to-indigo-700
                                       shadow-lg shadow-blue-500/20 transition cursor-pointer">
                            <x-lucide-plus class="w-4 h-4" />
                            Criar Plano {{ $selectedYear }}
                        </button>
                    </div>
                @else
                    @php
                        $tp = $this->teamPlan;
                        $statusColors2 = [
                            'rascunho'  => ['bg-slate-100 dark:bg-slate-700', 'text-slate-600 dark:text-slate-300'],
                            'enviado'   => ['bg-amber-50 dark:bg-amber-900/20',  'text-amber-700 dark:text-amber-300'],
                            'aprovado'  => ['bg-emerald-50 dark:bg-emerald-900/20', 'text-emerald-700 dark:text-emerald-300'],
                            'reprovado' => ['bg-red-50 dark:bg-red-900/20', 'text-red-600 dark:text-red-400'],
                            'concluido' => ['bg-teal-50 dark:bg-teal-900/20', 'text-teal-700 dark:text-teal-300'],
                        ];
                        [$tbg, $ttx] = $statusColors2[$tp->status] ?? $statusColors2['rascunho'];
                    @endphp

                    <div class="space-y-4">

                        {{-- Header do plano da equipe --}}
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 sm:p-5
                                    flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                            <div class="flex items-center gap-4">
                                <div class="relative w-12 h-12 shrink-0">
                                    <svg class="w-12 h-12 -rotate-90" viewBox="0 0 48 48">
                                        <circle cx="24" cy="24" r="18" fill="none" stroke-width="4"
                                                class="stroke-slate-100 dark:stroke-slate-700"/>
                                        <circle cx="24" cy="24" r="18" fill="none" stroke-width="4"
                                                stroke-dasharray="{{ 2 * 3.14159 * 18 }}"
                                                stroke-dashoffset="{{ 2 * 3.14159 * 18 * (1 - $tp->progress_percent / 100) }}"
                                                stroke-linecap="round"
                                                class="{{ $tp->progress_percent === 100 ? 'stroke-emerald-500' : 'stroke-blue-500' }}"/>
                                    </svg>
                                    <span class="absolute inset-0 flex items-center justify-center text-[11px] font-bold lato-bold text-slate-700 dark:text-slate-200">
                                        {{ $tp->progress_percent }}%
                                    </span>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 mb-0.5">
                                        <span class="text-sm font-bold text-slate-800 dark:text-white lato-bold">
                                            {{ $tp->user->name }}
                                        </span>
                                        <span class="text-[11px] lato-bold px-2 py-0.5 rounded-full {{ $tbg }} {{ $ttx }}">
                                            {{ $tp->status_label }}
                                        </span>
                                    </div>
                                    <p class="text-xs text-slate-400 lato-regular">
                                        {{ $tp->goals->count() }} competências · {{ $tp->actions->count() }} ações
                                    </p>
                                    @if ($tp->manager_feedback)
                                        <p class="text-xs mt-0.5 text-slate-400 lato-regular">
                                            <span class="font-semibold">Feedback anterior:</span> {{ $tp->manager_feedback }}
                                        </p>
                                    @endif

                                    {{-- ── Integração: badge de avaliação ── --}}
                                    @php $evalData = $this->teamMemberEvaluationData; @endphp
                                    @if ($evalData)
                                        @php
                                            $evalColorMap = [
                                                'emerald' => ['bg-emerald-50 dark:bg-emerald-900/20', 'text-emerald-700 dark:text-emerald-400'],
                                                'blue'    => ['bg-blue-50 dark:bg-blue-900/20', 'text-blue-700 dark:text-blue-400'],
                                                'amber'   => ['bg-amber-50 dark:bg-amber-900/20', 'text-amber-700 dark:text-amber-400'],
                                            ];
                                            [$evalBg, $evalTx] = $evalColorMap[$evalData['color']] ?? $evalColorMap['blue'];
                                        @endphp
                                        <div class="flex items-center gap-2 mt-1.5 flex-wrap">
                                            <a href="{{ route('avaliacoes') }}"
                                               class="flex items-center gap-1.5 text-[11px] lato-bold px-2 py-0.5 rounded-full {{ $evalBg }} {{ $evalTx }} transition hover:opacity-80">
                                                <x-lucide-star class="w-3 h-3" />
                                                Avaliação: {{ number_format($evalData['score'], 1) }}/5 · {{ $evalData['label'] }}
                                            </a>
                                            <span class="text-[10px] text-slate-400 lato-regular">{{ $evalData['cycle_name'] }} · {{ $evalData['type'] }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>

                            @php
                            $tpCreatedByRh      = $tp->wasCreatedByRh();
                            $tpCreatedByManager = $tp->wasCreatedByManager();
                            $currentIsRh        = auth()->user()->isRhOuDp();
                            $currentIsManager   = auth()->user()->isGerente();
                            $tpUserIsGerente    = $tp->user->isGerente(); // plano pertence a um Gestor

                            // Para plano de Gestor: somente RH edita estrutura (quando rascunho/reprovado)
                            // Para plano de Funcionário: Gerente edita quando rascunho/reprovado
                            //   ou quando enviado (RH criou e Gerente está revisando antes de publicar)
                            $tpCanEdit = $tpUserIsGerente
                                ? ($currentIsRh && in_array($tp->status, ['rascunho', 'reprovado']))
                                : (in_array($tp->status, ['rascunho', 'reprovado'])
                                    || ($tp->status === 'enviado' && $currentIsManager));

                            $statusColors3 = [
                                'rascunho'  => ['bg-slate-100 dark:bg-slate-700', 'text-slate-600 dark:text-slate-300'],
                                'enviado'   => ['bg-amber-50 dark:bg-amber-900/20',  'text-amber-700 dark:text-amber-300'],
                                'aprovado'  => ['bg-emerald-50 dark:bg-emerald-900/20', 'text-emerald-700 dark:text-emerald-300'],
                                'reprovado' => ['bg-red-50 dark:bg-red-900/20', 'text-red-600 dark:text-red-400'],
                                'concluido' => ['bg-teal-50 dark:bg-teal-900/20', 'text-teal-700 dark:text-teal-300'],
                            ];
                        @endphp

                            <div class="flex items-center gap-2 flex-wrap">

                                {{-- ── Integração: botão Agendar 1:1 ── --}}
                                <button wire:click="openMeetingModal({{ $tp->user_id }})" type="button"
                                        title="Agendar reunião de acompanhamento DPI"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                               border border-slate-200 dark:border-slate-700
                                               text-slate-500 dark:text-slate-400
                                               hover:text-indigo-600 hover:border-indigo-300 hover:bg-indigo-50
                                               dark:hover:text-indigo-400 dark:hover:border-indigo-700 dark:hover:bg-indigo-900/20
                                               transition cursor-pointer">
                                    <x-lucide-calendar-plus class="w-3.5 h-3.5" /> 1:1
                                </button>

                                @if ($tpUserIsGerente)
                                    {{-- ══════════════════════════════════════════════════════
                                         PLANO DO GESTOR: RH cria/edita/publica → Gestor executa
                                         → Gestor envia → RH aprova/devolve
                                    ══════════════════════════════════════════════════════ --}}

                                    {{-- + Competência: RH edita estrutura quando rascunho/reprovado --}}
                                    @if ($tpCanEdit)
                                        @if ($this->availableTemplates->count() > 0)
                                            <button wire:click="openTemplateModal({{ $tp->id }})" type="button"
                                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                           bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-700
                                                           text-indigo-600 dark:text-indigo-400
                                                           hover:bg-indigo-50 dark:hover:bg-indigo-900/30
                                                           shadow-sm transition cursor-pointer">
                                                <x-lucide-layout-template class="w-3.5 h-3.5" /> Usar Template
                                            </button>
                                        @endif
                                        <button wire:click="openGoalModalForEmployee" type="button"
                                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                       bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                                       hover:from-blue-600 hover:to-indigo-700
                                                       shadow-md shadow-blue-500/20 transition cursor-pointer">
                                            <x-lucide-plus class="w-3.5 h-3.5" /> Competência
                                        </button>
                                    @endif

                                    {{-- RH publica para o Gestor iniciar a execução --}}
                                    @if (in_array($tp->status, ['rascunho', 'reprovado']) && $currentIsRh && $tp->goals->count() > 0)
                                        <button wire:click="publishPlanForEmployee({{ $tp->id }})" type="button"
                                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                       bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white
                                                       shadow-md shadow-emerald-500/20 transition cursor-pointer">
                                            <x-lucide-rocket class="w-3.5 h-3.5" /> Publicar para Gestor
                                        </button>
                                    @endif

                                    {{-- Gestor enviou o DPI → RH revisa e aprova/devolve --}}
                                    @if ($tp->status === 'enviado' && $currentIsRh)
                                        <button wire:click="openFeedbackModal({{ $tp->id }}, 'aprovado')" type="button"
                                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                       bg-teal-500 hover:bg-teal-600 text-white
                                                       shadow-sm transition cursor-pointer">
                                            <x-lucide-check class="w-3.5 h-3.5" /> Aprovar e Concluir
                                        </button>
                                        <button wire:click="openFeedbackModal({{ $tp->id }}, 'reprovado')" type="button"
                                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                       border border-red-200 dark:border-red-800
                                                       text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20
                                                       transition cursor-pointer">
                                            <x-lucide-x class="w-3.5 h-3.5" /> Devolver ao Gestor
                                        </button>
                                    @endif

                                    {{-- Gestor aguardando ou plano aprovado: RH pode reabrir se necessário --}}
                                    @if ($tp->status === 'aprovado' && $currentIsRh)
                                        <span class="flex items-center gap-1.5 text-xs lato-regular text-emerald-600 dark:text-emerald-400">
                                            <x-lucide-loader class="w-3.5 h-3.5" /> Gestor em execução
                                        </span>
                                        <button wire:click="retractFromManager({{ $tp->id }})" type="button"
                                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                       border border-slate-200 dark:border-slate-700
                                                       text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700
                                                       transition cursor-pointer">
                                            <x-lucide-undo-2 class="w-3.5 h-3.5" /> Reabrir
                                        </button>
                                    @endif

                                    @if ($tp->status === 'concluido')
                                        <span class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                     bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400
                                                     border border-teal-200 dark:border-teal-800/40">
                                            <x-lucide-shield-check class="w-3.5 h-3.5" /> DPI concluído
                                        </span>
                                    @endif

                                @else
                                    {{-- ══════════════════════════════════════════════════════
                                         PLANO DO FUNCIONÁRIO: fluxo original
                                         Gerente cria → publica direto
                                         RH cria → Gerente aprova → publica
                                    ══════════════════════════════════════════════════════ --}}

                                    {{-- + Competência --}}
                                    @if ($tpCanEdit)
                                        @if ($this->availableTemplates->count() > 0)
                                            <button wire:click="openTemplateModal({{ $tp->id }})" type="button"
                                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                           bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-700
                                                           text-indigo-600 dark:text-indigo-400
                                                           hover:bg-indigo-50 dark:hover:bg-indigo-900/30
                                                           shadow-sm transition cursor-pointer">
                                                <x-lucide-layout-template class="w-3.5 h-3.5" /> Usar Template
                                            </button>
                                        @endif
                                        <button wire:click="openGoalModalForEmployee" type="button"
                                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                       bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                                       hover:from-blue-600 hover:to-indigo-700
                                                       shadow-md shadow-blue-500/20 transition cursor-pointer">
                                            <x-lucide-plus class="w-3.5 h-3.5" /> Competência
                                        </button>
                                    @endif

                                    {{-- Gerente só publica quando status é rascunho (plano fresco).
                                         Se foi devolvido (reprovado), o RH precisa corrigir e re-enviar. --}}
                                    @if ($tp->status === 'rascunho' && $tp->goals->count() > 0 && $currentIsManager)
                                        <button wire:click="publishPlanForEmployee({{ $tp->id }})" type="button"
                                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                       bg-gradient-to-r from-emerald-500 to-teal-600 hover:from-emerald-600 hover:to-teal-700 text-white
                                                       shadow-md shadow-emerald-500/20 transition cursor-pointer">
                                            <x-lucide-rocket class="w-3.5 h-3.5" /> Publicar para Funcionário
                                        </button>
                                    @endif

                                    {{-- RH/DP: envia para Gerente revisar (rascunho ou devolvido após correção) --}}
                                    @if (in_array($tp->status, ['rascunho', 'reprovado']) && $tp->goals->count() > 0 && $currentIsRh)
                                        <button wire:click="submitPlanToManager({{ $tp->id }})" type="button"
                                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                       bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600 text-white
                                                       shadow-md shadow-amber-500/20 transition cursor-pointer">
                                            <x-lucide-send class="w-3.5 h-3.5" /> Enviar para Gerente
                                        </button>
                                    @endif

                                    @if ($tp->status === 'enviado')
                                        @if ($currentIsManager)
                                            {{-- Gerente: aprova ou devolve ao RH --}}
                                            <button wire:click="openFeedbackModal({{ $tp->id }}, 'aprovado')" type="button"
                                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                           bg-emerald-500 hover:bg-emerald-600 text-white
                                                           shadow-sm transition cursor-pointer">
                                                <x-lucide-check class="w-3.5 h-3.5" /> Aprovar e Publicar
                                            </button>
                                            <button wire:click="openFeedbackModal({{ $tp->id }}, 'reprovado')" type="button"
                                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                           border border-red-200 dark:border-red-800
                                                           text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20
                                                           transition cursor-pointer">
                                                <x-lucide-x class="w-3.5 h-3.5" /> Devolver ao RH
                                            </button>
                                        @elseif ($currentIsRh)
                                            {{-- RH: pode retirar --}}
                                            <button wire:click="retractFromManager({{ $tp->id }})" type="button"
                                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                           border border-slate-200 dark:border-slate-700
                                                           text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700
                                                           transition cursor-pointer">
                                                <x-lucide-undo-2 class="w-3.5 h-3.5" /> Retirar
                                            </button>
                                        @endif
                                    @endif

                                    @if ($tp->status === 'aprovado')
                                        <button wire:click="retractFromManager({{ $tp->id }})" type="button"
                                                class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                                       border border-slate-200 dark:border-slate-700
                                                       text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700
                                                       transition cursor-pointer">
                                            <x-lucide-pencil class="w-3.5 h-3.5" /> Reabrir
                                        </button>
                                    @endif

                                @endif
                            </div>
                        </div>

                        {{-- Objetivos --}}
                        @if ($tp->goals->isEmpty())
                            <div class="flex flex-col items-center justify-center py-12 text-center
                                        bg-white dark:bg-slate-800 rounded-2xl border border-dashed
                                        border-slate-200 dark:border-slate-700">
                                <x-lucide-brain class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2" />
                                <p class="text-sm text-slate-400 lato-regular">Nenhuma competência mapeada ainda.</p>
                                @if ($tpCanEdit)
                                    <button wire:click="openGoalModalForEmployee" type="button"
                                            class="mt-3 text-sm text-blue-500 hover:text-blue-600 lato-bold transition cursor-pointer">
                                        + Mapear primeira competência
                                    </button>
                                @endif
                            </div>
                        @else
                            {{-- ── Integração: reuniões recentes de acompanhamento ── --}}
                            @php $recentMeetings = $this->teamMemberMeetings; @endphp
                            @if ($recentMeetings->isNotEmpty())
                                <div class="bg-indigo-50 dark:bg-indigo-900/10 rounded-2xl border border-indigo-100 dark:border-indigo-900/40 px-5 py-3">
                                    <p class="text-[11px] lato-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider mb-2 flex items-center gap-1.5">
                                        <x-lucide-calendar class="w-3 h-3" /> Reuniões 1:1 de acompanhamento DPI
                                    </p>
                                    <div class="space-y-1.5">
                                        @foreach ($recentMeetings as $rm)
                                            <div class="flex items-center justify-between gap-3">
                                                <div class="flex items-center gap-2 min-w-0">
                                                    <span class="w-1.5 h-1.5 rounded-full {{ $rm->colorClass() }} shrink-0"></span>
                                                    <span class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $rm->title }}</span>
                                                    <span class="text-[11px] text-slate-400 lato-regular shrink-0">{{ $rm->start_time->format('d/m/Y H:i') }}</span>
                                                </div>
                                                <a href="{{ route('reunioes.details', $rm->id) }}"
                                                   class="text-[11px] lato-bold text-indigo-500 hover:text-indigo-700 dark:text-indigo-400 transition shrink-0">
                                                    Ver →
                                                </a>
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            <div class="space-y-4">
                            @foreach ($tp->goals as $goal)
                                @php
                                    $prioCfg2 = match($goal->priority) {
                                        'alta'  => ['bg-red-50 dark:bg-red-900/20', 'text-red-600 dark:text-red-400', 'Alta'],
                                        'media' => ['bg-amber-50 dark:bg-amber-900/20', 'text-amber-600 dark:text-amber-400', 'Média'],
                                        'baixa' => ['bg-blue-50 dark:bg-blue-900/20', 'text-blue-500 dark:text-blue-400', 'Baixa'],
                                        default => ['bg-slate-50 dark:bg-slate-700', 'text-slate-500 dark:text-slate-400', $goal->priority],
                                    };
                                    [$pbg2, $ptx2, $plabel2] = $prioCfg2;
                                @endphp
                                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                                    {{-- Header da competência --}}
                                    <div class="p-4 sm:p-5">
                                        <div class="flex items-start justify-between gap-3">
                                            <div class="flex-1 min-w-0">
                                                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                                                    <span class="text-[11px] lato-bold px-2 py-0.5 rounded-full {{ $pbg2 }} {{ $ptx2 }}">
                                                        {{ $plabel2 }}
                                                    </span>
                                                    <span class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                                        <x-lucide-gauge class="w-3 h-3" />
                                                        {{ $goal->nivel_atual_label }} → {{ $goal->nivel_meta_label }}
                                                    </span>
                                                </div>
                                                <h3 class="text-sm font-bold text-slate-800 dark:text-white lato-bold leading-snug">
                                                    {{ $goal->name }}
                                                </h3>

                                                {{-- Indicador visual de níveis (5 círculos) --}}
                                                <div class="flex items-center gap-1.5 mt-2.5">
                                                    @for ($lvl = 1; $lvl <= 5; $lvl++)
                                                        @if ($lvl <= $goal->nivel_atual)
                                                            <div class="w-6 h-6 rounded-full bg-blue-500 flex items-center justify-center shrink-0"
                                                                 title="{{ \App\Models\DpiGoal::nivelLabels()[$lvl] ?? $lvl }}">
                                                                <span class="text-[9px] font-bold text-white">{{ $lvl }}</span>
                                                            </div>
                                                        @elseif ($lvl === $goal->nivel_meta)
                                                            <div class="w-6 h-6 rounded-full border-2 border-indigo-400 flex items-center justify-center shrink-0"
                                                                 title="{{ \App\Models\DpiGoal::nivelLabels()[$lvl] ?? $lvl }}">
                                                                <span class="text-[9px] font-bold text-indigo-500">{{ $lvl }}</span>
                                                            </div>
                                                        @else
                                                            <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0"
                                                                 title="{{ \App\Models\DpiGoal::nivelLabels()[$lvl] ?? $lvl }}">
                                                                <span class="text-[9px] text-slate-400 dark:text-slate-500">{{ $lvl }}</span>
                                                            </div>
                                                        @endif
                                                    @endfor
                                                    <span class="text-[11px] text-slate-400 lato-regular ml-1">
                                                        {{ $goal->progress_percent }}% do objetivo
                                                    </span>
                                                </div>
                                            </div>

                                            @if ($tpCanEdit)
                                                <div class="flex items-center gap-1 shrink-0">
                                                    <button wire:click="openGoalModalForEmployee({{ $goal->id }})" type="button"
                                                            title="Editar competência / ações"
                                                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                                                   text-slate-400 hover:text-slate-700 hover:bg-slate-100
                                                                   dark:hover:bg-slate-700 transition cursor-pointer">
                                                        <x-lucide-pencil class="w-3.5 h-3.5" />
                                                    </button>
                                                    <button wire:click="confirmDeleteGoal({{ $goal->id }})" type="button"
                                                            title="Remover competência"
                                                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                                                   text-slate-400 hover:text-red-500 hover:bg-red-50
                                                                   dark:hover:bg-red-900/20 transition cursor-pointer">
                                                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                    </button>
                                                </div>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Ações de desenvolvimento --}}
                                    @if ($goal->actions->isNotEmpty())
                                        <div class="border-t border-slate-100 dark:border-slate-700">
                                            @foreach ($goal->actions as $action)
                                                <div class="flex items-start gap-3 px-5 py-3
                                                            hover:bg-slate-50 dark:hover:bg-slate-700/30
                                                            transition border-b border-slate-50 dark:border-slate-700/50 last:border-0">
                                                    {{-- Toggle status:
                                                         - Plano de Funcionário: Gerente/RH pode executar
                                                         - Plano de Gestor: somente o próprio Gestor executa
                                                           (RH vê como indicador estático aqui na Equipe) --}}
                                                    @if (! $tpUserIsGerente)
                                                        <button wire:click="toggleActionStatus({{ $action->id }})" type="button"
                                                                title="{{ $action->status_label }}"
                                                                class="mt-0.5 w-4 h-4 rounded-full border-2 shrink-0 transition cursor-pointer
                                                                       {{ $action->status === 'concluido'
                                                                           ? 'bg-emerald-500 border-emerald-500'
                                                                           : ($action->status === 'em_andamento'
                                                                               ? 'border-blue-500 bg-blue-100 dark:bg-blue-900/30'
                                                                               : 'border-slate-300 dark:border-slate-600 bg-transparent') }}">
                                                        </button>
                                                    @else
                                                        <span class="mt-0.5 w-4 h-4 rounded-full border-2 shrink-0
                                                                     {{ $action->status === 'concluido'
                                                                         ? 'bg-emerald-500 border-emerald-500'
                                                                         : ($action->status === 'em_andamento'
                                                                             ? 'border-blue-500 bg-blue-100 dark:bg-blue-900/30'
                                                                             : 'border-slate-300 dark:border-slate-600 bg-transparent') }}">
                                                        </span>
                                                    @endif
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-start justify-between gap-2">
                                                            <div class="min-w-0">
                                                                <p class="text-sm lato-regular text-slate-700 dark:text-slate-200
                                                                           leading-snug {{ $action->status === 'concluido' ? 'line-through text-slate-400' : '' }}">
                                                                    {{ $action->title }}
                                                                </p>
                                                                <div class="flex flex-wrap items-center gap-2 mt-1">
                                                                    <span class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                                                        <x-lucide-tag class="w-3 h-3" />
                                                                        {{ $action->type_label }}
                                                                    </span>
                                                                    @if ($action->target_date)
                                                                        <span class="text-[11px] {{ $action->target_date->isPast() && $action->status !== 'concluido' ? 'text-red-400' : 'text-slate-400' }} lato-regular flex items-center gap-1">
                                                                            <x-lucide-calendar class="w-3 h-3" />
                                                                            {{ $action->target_date->format('d/m/Y') }}
                                                                        </span>
                                                                    @endif
                                                                    @if ($action->description)
                                                                        <span class="text-[11px] text-slate-400 lato-regular truncate max-w-[180px]">
                                                                            · {{ Str::limit($action->description, 60) }}
                                                                        </span>
                                                                    @endif
                                                                    @if ($action->attachment_name)
                                                                        <span class="text-[11px] text-indigo-500 lato-bold flex items-center gap-1">
                                                                            <x-lucide-paperclip class="w-3 h-3" />
                                                                            {{ Str::limit($action->attachment_name, 30) }}
                                                                        </span>
                                                                    @endif
                                                                    @if ($action->validated_at && $action->status === 'concluido')
                                                                        <span class="text-[11px] text-emerald-500 lato-regular flex items-center gap-1">
                                                                            <x-lucide-shield-check class="w-3 h-3" />
                                                                            Validado em {{ $action->validated_at->format('d/m/Y') }}
                                                                        </span>
                                                                    @endif
                                                                    {{-- ── Integração: badge de tarefa vinculada ── --}}
                                                                    @if ($action->task_id && $action->task)
                                                                        <a href="{{ route('tarefas') }}"
                                                                           class="text-[11px] lato-bold flex items-center gap-1 px-1.5 py-0.5 rounded
                                                                                  {{ $action->task->status === 'concluida' ? 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20' : 'text-indigo-600 dark:text-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' }}">
                                                                            <x-lucide-check-square class="w-3 h-3" />
                                                                            {{ $action->task->status_label }}
                                                                        </a>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                            <div class="flex items-center gap-0.5 shrink-0">
                                                                {{-- Upload de evidência:
                                                                     Para plano de Funcionário: Gerente/RH pode anexar
                                                                     Para plano de Gestor: somente o Gestor faz upload (em Meu Plano)
                                                                     Exibe o ícone de evidência existente, mas sem botão de upload --}}
                                                                @if (! $tpUserIsGerente)
                                                                    <button wire:click="openUploadModal({{ $action->id }})" type="button"
                                                                            title="{{ $action->attachment_name ? 'Substituir evidência' : 'Anexar evidência (diploma, certificado...)' }}"
                                                                            class="w-6 h-6 flex items-center justify-center rounded-lg transition cursor-pointer
                                                                                   {{ $action->attachment_name ? 'text-indigo-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20' : 'text-slate-300 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20' }}">
                                                                        <x-lucide-paperclip class="w-3 h-3" />
                                                                    </button>
                                                                @elseif ($action->attachment_name)
                                                                    {{-- Só mostra ícone indicativo se já tem arquivo --}}
                                                                    <span title="{{ $action->attachment_name }}"
                                                                          class="w-6 h-6 flex items-center justify-center text-indigo-400">
                                                                        <x-lucide-paperclip class="w-3 h-3" />
                                                                    </span>
                                                                @endif
                                                                {{-- ── Integração: botão criar/desvincular tarefa ── --}}
                                                                @if (! $action->task_id)
                                                                    <button wire:click="createTaskForAction({{ $action->id }})" type="button"
                                                                            title="Criar tarefa vinculada"
                                                                            class="w-6 h-6 flex items-center justify-center rounded-lg
                                                                                   text-slate-300 hover:text-indigo-500 hover:bg-indigo-50
                                                                                   dark:hover:bg-indigo-900/20 transition cursor-pointer">
                                                                        <x-lucide-list-plus class="w-3 h-3" />
                                                                    </button>
                                                                @else
                                                                    <button wire:click="detachTaskFromAction({{ $action->id }})" type="button"
                                                                            title="Remover vínculo com tarefa"
                                                                            class="w-6 h-6 flex items-center justify-center rounded-lg
                                                                                   text-indigo-400 hover:text-red-500 hover:bg-red-50
                                                                                   dark:hover:bg-red-900/20 transition cursor-pointer">
                                                                        <x-lucide-link-2-off class="w-3 h-3" />
                                                                    </button>
                                                                @endif
                                                                @if ($tpCanEdit)
                                                                    <button wire:click="openGoalModalForEmployee({{ $goal->id }})" type="button"
                                                                            title="Editar ação"
                                                                            class="w-6 h-6 flex items-center justify-center rounded-lg
                                                                                   text-slate-300 hover:text-slate-600 dark:hover:text-slate-300
                                                                                   hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                                                        <x-lucide-pencil class="w-3 h-3" />
                                                                    </button>
                                                                    <button wire:click="confirmDeleteAction({{ $action->id }})" type="button"
                                                                            class="w-6 h-6 flex items-center justify-center rounded-lg
                                                                                   text-slate-300 hover:text-red-500
                                                                                   hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                                                                        <x-lucide-trash-2 class="w-3 h-3" />
                                                                    </button>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>
                                    @elseif ($tpCanEdit)
                                        <div class="border-t border-slate-100 dark:border-slate-700 px-5 py-3">
                                            <button wire:click="openGoalModalForEmployee({{ $goal->id }})" type="button"
                                                    class="text-xs text-slate-400 hover:text-blue-500 lato-regular transition cursor-pointer
                                                           flex items-center gap-1">
                                                <x-lucide-plus class="w-3 h-3" /> Adicionar ação de desenvolvimento
                                            </button>
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                            </div>
                        @endif

                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         ABA: APROVAÇÕES (RH — DPI de Gestores aguardando revisão)
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'aprovacoes' && auth()->user()->isRhOuDp())
        @php $pending = $this->pendingGerentePlans; @endphp

        <div class="space-y-4">

            {{-- Cabeçalho da aba --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                    <x-lucide-clock class="w-4.5 h-4.5 text-amber-600 dark:text-amber-400" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white lato-bold">
                        DPI de Gestores — Pendentes de Aprovação
                    </h2>
                    <p class="text-xs text-slate-400 lato-regular">
                        {{ $pending->count() === 0 ? 'Nenhum plano aguardando revisão' : $pending->count() . ' plano(s) enviado(s) pelo gestor aguardando sua revisão' }}
                    </p>
                </div>
            </div>

            @if ($pending->isEmpty())
                {{-- Estado vazio --}}
                <div class="flex flex-col items-center justify-center py-20
                            bg-white dark:bg-slate-800 rounded-2xl border border-dashed
                            border-slate-200 dark:border-slate-700 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-emerald-50 dark:bg-emerald-900/20
                                flex items-center justify-center mb-3">
                        <x-lucide-check-circle class="w-7 h-7 text-emerald-500" />
                    </div>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300 lato-bold mb-1">
                        Tudo em dia!
                    </p>
                    <p class="text-xs text-slate-400 lato-regular">
                        Nenhum DPI de gestor aguardando aprovação para {{ $selectedYear }}.
                    </p>
                </div>
            @else
                @foreach ($pending as $gp)
                    @php
                        $gpParts    = explode(' ', trim($gp->user->name));
                        $gpInitials = strtoupper(substr($gpParts[0],0,1) . (isset($gpParts[1]) ? substr($gpParts[1],0,1) : ''));
                        $gpPalette  = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                        $gpColor    = $gpPalette[abs(crc32($gp->user->name)) % count($gpPalette)];
                        $gpProgress = $gp->progress_percent;
                        $gpGoals    = $gp->goals;
                        $gpDone     = $gpGoals->sum(fn ($g) => $g->actions->where('status','concluido')->count());
                        $gpTotal    = $gpGoals->sum(fn ($g) => $g->actions->count());
                    @endphp

                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700
                                shadow-sm overflow-hidden"
                         x-data="{ expanded: false }">

                        {{-- Cabeçalho do card --}}
                        <div class="flex items-center gap-4 px-5 py-4">

                            {{-- Avatar + progresso --}}
                            <div class="relative shrink-0">
                                <svg class="w-12 h-12 -rotate-90" viewBox="0 0 48 48">
                                    <circle cx="24" cy="24" r="18" fill="none" stroke-width="4"
                                            class="stroke-slate-100 dark:stroke-slate-700"/>
                                    <circle cx="24" cy="24" r="18" fill="none" stroke-width="4"
                                            stroke-dasharray="{{ 2 * 3.14159 * 18 }}"
                                            stroke-dashoffset="{{ 2 * 3.14159 * 18 * (1 - $gpProgress / 100) }}"
                                            stroke-linecap="round"
                                            class="{{ $gpProgress === 100 ? 'stroke-emerald-500' : 'stroke-amber-400' }}"/>
                                </svg>
                                <span class="absolute inset-0 flex items-center justify-center
                                             text-[10px] font-bold lato-bold text-slate-700 dark:text-slate-200">
                                    {{ $gpProgress }}%
                                </span>
                            </div>

                            {{-- Dados do gestor --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <span class="w-7 h-7 rounded-full {{ $gpColor }} text-white text-xs lato-bold
                                                 flex items-center justify-center shrink-0">{{ $gpInitials }}</span>
                                    <span class="text-sm font-bold text-slate-800 dark:text-white lato-bold truncate">
                                        {{ $gp->user->name }}
                                    </span>
                                    @if ($gp->user->department)
                                        <span class="text-[11px] lato-regular text-slate-400 flex items-center gap-1">
                                            <x-lucide-building-2 class="w-3 h-3" />
                                            {{ $gp->user->department->name }}
                                        </span>
                                    @endif
                                    <span class="text-[11px] lato-bold px-2 py-0.5 rounded-full
                                                 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300">
                                        Aguardando RH
                                    </span>
                                </div>
                                <div class="flex items-center gap-4 mt-1.5">
                                    <span class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                        <x-lucide-brain class="w-3 h-3" />
                                        {{ $gpGoals->count() }} competência(s)
                                    </span>
                                    <span class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                        <x-lucide-check-square class="w-3 h-3" />
                                        {{ $gpDone }}/{{ $gpTotal }} ações concluídas
                                    </span>
                                    <span class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                        <x-lucide-calendar class="w-3 h-3" />
                                        Enviado {{ $gp->updated_at->diffForHumans() }}
                                    </span>
                                </div>
                            </div>

                            {{-- Ações --}}
                            <div class="flex items-center gap-2 shrink-0 flex-wrap justify-end">
                                <button wire:click="openFeedbackModal({{ $gp->id }}, 'aprovado')" type="button"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                               bg-teal-500 hover:bg-teal-600 text-white
                                               shadow-sm shadow-teal-500/20 transition cursor-pointer">
                                    <x-lucide-check class="w-3.5 h-3.5" /> Aprovar
                                </button>
                                <button wire:click="openFeedbackModal({{ $gp->id }}, 'reprovado')" type="button"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl text-sm lato-bold
                                               border border-red-200 dark:border-red-800
                                               text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20
                                               transition cursor-pointer">
                                    <x-lucide-x class="w-3.5 h-3.5" /> Devolver
                                </button>
                                <button @click="expanded = !expanded" type="button"
                                        class="w-8 h-8 flex items-center justify-center rounded-xl
                                               text-slate-400 hover:text-slate-600 hover:bg-slate-100
                                               dark:hover:bg-slate-700 transition cursor-pointer">
                                    <x-lucide-chevron-down class="w-4 h-4 transition-transform"
                                                           ::class="{ 'rotate-180': expanded }" />
                                </button>
                            </div>
                        </div>

                        {{-- Detalhes expansíveis: competências e ações --}}
                        <div x-show="expanded"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="border-t border-slate-100 dark:border-slate-700">

                            @if ($gpGoals->isEmpty())
                                <p class="px-5 py-4 text-xs text-slate-400 lato-regular">
                                    Nenhuma competência registrada.
                                </p>
                            @else
                                @foreach ($gpGoals as $gpGoal)
                                    @php
                                        $gpPrio = match($gpGoal->priority) {
                                            'alta'  => ['bg-red-50 dark:bg-red-900/20', 'text-red-600 dark:text-red-400', 'Alta'],
                                            'media' => ['bg-amber-50 dark:bg-amber-900/20', 'text-amber-600 dark:text-amber-400', 'Média'],
                                            'baixa' => ['bg-blue-50 dark:bg-blue-900/20', 'text-blue-500 dark:text-blue-400', 'Baixa'],
                                            default => ['bg-slate-50 dark:bg-slate-700', 'text-slate-500', $gpGoal->priority],
                                        };
                                    @endphp
                                    <div class="px-5 py-3 border-b border-slate-50 dark:border-slate-700/60 last:border-0">
                                        {{-- Competência --}}
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs lato-bold {{ $gpPrio[0] }} {{ $gpPrio[1] }}
                                                         px-2 py-0.5 rounded-full">{{ $gpPrio[2] }}</span>
                                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">
                                                {{ $gpGoal->name }}
                                            </p>
                                            <span class="ml-auto text-[11px] text-slate-400 lato-regular">
                                                Nível {{ $gpGoal->nivel_atual }}/{{ $gpGoal->nivel_meta }}
                                            </span>
                                        </div>
                                        {{-- Ações da competência --}}
                                        @if ($gpGoal->actions->isNotEmpty())
                                            <div class="space-y-1 pl-2">
                                                @foreach ($gpGoal->actions as $gpAction)
                                                    <div class="flex items-center gap-2 text-xs lato-regular text-slate-500 dark:text-slate-400">
                                                        <span class="w-3 h-3 rounded-full border-2 shrink-0
                                                                     {{ $gpAction->status === 'concluido'
                                                                         ? 'bg-emerald-500 border-emerald-500'
                                                                         : ($gpAction->status === 'em_andamento'
                                                                             ? 'border-blue-400 bg-blue-100'
                                                                             : 'border-slate-300') }}">
                                                        </span>
                                                        <span class="{{ $gpAction->status === 'concluido' ? 'line-through text-slate-300' : '' }}">
                                                            {{ $gpAction->title }}
                                                        </span>
                                                        @if ($gpAction->attachment_name)
                                                            <x-lucide-paperclip class="w-3 h-3 text-indigo-400 shrink-0" title="{{ $gpAction->attachment_name }}" />
                                                        @endif
                                                        <span class="ml-auto text-[10px] text-slate-400">
                                                            {{ $gpAction->type_label }}
                                                        </span>
                                                    </div>
                                                @endforeach
                                            </div>
                                        @else
                                            <p class="pl-2 text-[11px] text-slate-300 dark:text-slate-600 lato-regular">
                                                Nenhuma ação registrada.
                                            </p>
                                        @endif
                                    </div>
                                @endforeach

                                {{-- Feedback anterior se houver --}}
                                @if ($gp->manager_feedback)
                                    <div class="px-5 py-3 bg-slate-50 dark:bg-slate-700/40 border-t border-slate-100 dark:border-slate-700">
                                        <p class="text-[11px] lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                                            Feedback anterior
                                        </p>
                                        <p class="text-xs lato-regular text-slate-600 dark:text-slate-300">
                                            {{ $gp->manager_feedback }}
                                        </p>
                                    </div>
                                @endif
                            @endif
                        </div>

                    </div>
                @endforeach
            @endif

        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         ABA: RELATÓRIO (RH/DP)
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'relatorio' && auth()->user()->isRhOuDp())
        @php $s = $this->reportStats; @endphp

        <div class="space-y-5">

            {{-- ── KPI Cards ─────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div class="flex-1">
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">
                            {{ $s['with_plan'] }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $s['total_users'] }}</span>
                        </p>
                        <p class="text-xs text-slate-400 lato-regular">Colaboradores com plano</p>
                        @if ($s['total_users'] > 0)
                            <div class="mt-1.5 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full"
                                     style="width: {{ round($s['with_plan'] / $s['total_users'] * 100) }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-trending-up class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $s['avg_progress'] }}%</p>
                        <p class="text-xs text-slate-400 lato-regular">Progresso médio</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-check-circle class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">
                            {{ $s['done_actions'] }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $s['total_actions'] }}</span>
                        </p>
                        <p class="text-xs text-slate-400 lato-regular">Ações concluídas</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-clock class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $s['enviado'] }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Aguardando aprovação</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                        <x-lucide-file-x class="w-4 h-4 text-slate-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $s['without_plan'] }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Sem plano</p>
                    </div>
                </div>

            </div>

            {{-- ── Gráfico de status + Tabela por departamento ────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Donut de status --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold mb-4">
                        Distribuição por status
                    </h3>
                    @if (($s['with_plan'] ?? 0) > 0)
                        {{-- wire:ignore: impede Livewire de destruir o gráfico a cada re-render --}}
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-report-chart');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'donut', height: 200, background: 'transparent', toolbar: { show: false } },
                                        series:      [{{ $s['rascunho'] }}, {{ $s['enviado'] }}, {{ $s['aprovado'] }}, {{ $s['reprovado'] }}, {{ $s['concluido'] }}],
                                        labels:      ['Em preparação','Aguardando','Ativos','Devolvidos','Concluídos'],
                                        colors:      ['#94a3b8','#f59e0b','#10b981','#ef4444','#14b8a6'],
                                        legend:      { position: 'bottom', labels: { colors: dark ? '#94a3b8' : '#64748b' } },
                                        dataLabels:  { style: { fontSize: '11px' } },
                                        plotOptions: { pie: { donut: { size: '65%' } } },
                                        theme:       { mode: dark ? 'dark' : 'light' },
                                        tooltip:     { theme: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })()
                             ">
                            <div id="dpi-report-chart" style="min-height:200px;"></div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-pie-chart class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum dado para {{ $selectedYear }}</p>
                        </div>
                    @endif

                    {{-- Legenda com contagens --}}
                    <div class="mt-4 space-y-1.5">
                        @foreach ([
                            ['Em preparação', 'bg-slate-400',   $s['rascunho']],
                            ['Aguardando',    'bg-amber-400',   $s['enviado']],
                            ['Ativos',        'bg-emerald-500', $s['aprovado']],
                            ['Devolvidos',    'bg-red-400',     $s['reprovado']],
                            ['Concluídos',    'bg-teal-500',    $s['concluido']],
                        ] as [$lbl, $dot, $cnt])
                            <div class="flex items-center justify-between text-xs lato-regular">
                                <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                    <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span>{{ $lbl }}
                                </span>
                                <span class="lato-bold text-slate-700 dark:text-slate-200">{{ $cnt }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Tabela por departamento --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Por departamento</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs lato-regular">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-700 text-[10px] lato-bold uppercase tracking-wider text-slate-400">
                                    <th class="px-5 py-2.5 text-left">Departamento</th>
                                    <th class="px-3 py-2.5 text-center">Total</th>
                                    <th class="px-3 py-2.5 text-center">Com plano</th>
                                    <th class="px-3 py-2.5 text-center">Progresso</th>
                                    <th class="px-3 py-2.5 text-center">Ativos</th>
                                    <th class="px-3 py-2.5 text-center">Aguardando</th>
                                    <th class="px-3 py-2.5 text-center">Concluídos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                @forelse ($this->reportByDepartment as $dept)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-200">{{ $dept['name'] }}</td>
                                        <td class="px-3 py-3 text-center text-slate-400">{{ $dept['total_users'] }}</td>
                                        <td class="px-3 py-3 text-center {{ $dept['with_plan'] > 0 ? 'text-indigo-600 dark:text-indigo-400 lato-bold' : 'text-slate-300' }}">
                                            {{ $dept['with_plan'] }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-1.5 justify-center">
                                                <div class="w-16 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $dept['avg_progress'] >= 75 ? 'bg-emerald-500' : ($dept['avg_progress'] >= 40 ? 'bg-amber-400' : 'bg-slate-400') }}"
                                                         style="width:{{ $dept['avg_progress'] }}%"></div>
                                                </div>
                                                <span class="text-slate-400 w-8 text-right">{{ $dept['avg_progress'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($dept['aprovado'] > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 lato-bold">{{ $dept['aprovado'] }}</span>
                                            @else <span class="text-slate-300">—</span> @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($dept['enviado'] > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 lato-bold">{{ $dept['enviado'] }}</span>
                                            @else <span class="text-slate-300">—</span> @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($dept['concluido'] > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 lato-bold">{{ $dept['concluido'] }}</span>
                                            @else <span class="text-slate-300">—</span> @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Nenhum departamento cadastrado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── Ações por tipo + Ranking de progresso ───────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Gráfico: ações por tipo --}}
                @php $abt = $this->reportActionsByType; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold mb-4">
                        Ações por tipo
                    </h3>
                    @if (array_sum($abt['series'] ?? []) > 0)
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-actions-type-chart');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'bar', height: 220, background: 'transparent', toolbar: { show: false } },
                                        series: [
                                            { name: 'Total',      data: {{ json_encode($abt['series']) }} },
                                            { name: 'Concluídas', data: {{ json_encode($abt['doneSeries']) }} },
                                        ],
                                        xaxis:       { categories: {{ json_encode($abt['labels']) }}, labels: { style: { colors: dark ? '#94a3b8' : '#64748b', fontSize: '11px' } } },
                                        yaxis:       { labels: { style: { colors: dark ? '#94a3b8' : '#64748b' } } },
                                        colors:      ['#6366f1','#10b981'],
                                        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                                        dataLabels:  { enabled: false },
                                        grid:        { borderColor: dark ? '#334155' : '#f1f5f9' },
                                        legend:      { labels: { colors: dark ? '#94a3b8' : '#64748b' } },
                                        theme:       { mode: dark ? 'dark' : 'light' },
                                        tooltip:     { theme: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })()
                             ">
                            <div id="dpi-actions-type-chart" style="min-height:220px;"></div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-bar-chart-2 class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhuma ação registrada em {{ $selectedYear }}</p>
                        </div>
                    @endif
                </div>

                {{-- Ranking de progresso --}}
                @php $ranking = $this->reportProgressRanking; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Ranking de progresso</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Planos ativos/concluídos por progresso</p>
                    </div>

                    @if ($ranking['top']->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-trophy class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum plano ativo em {{ $selectedYear }}</p>
                        </div>
                    @else
                        {{-- Top 5 --}}
                        <div class="px-5 pt-4 pb-2">
                            <p class="text-[10px] lato-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1">
                                <x-lucide-trending-up class="w-3 h-3 text-emerald-500" /> Maiores progressos
                            </p>
                            <div class="space-y-2">
                                @foreach ($ranking['top'] as $i => $row)
                                    @php
                                        $rParts = explode(' ', trim($row['plan']->user->name));
                                        $rInit  = strtoupper(substr($rParts[0],0,1).(isset($rParts[1])?substr($rParts[1],0,1):''));
                                        $rPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                        $rBg    = $rPal[abs(crc32($row['plan']->user->name)) % count($rPal)];
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <span class="text-[11px] lato-bold text-slate-300 dark:text-slate-600 w-4 text-right shrink-0">{{ $i+1 }}</span>
                                        <span class="w-7 h-7 rounded-full {{ $rBg }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0">{{ $rInit }}</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $row['plan']->user->name }}</p>
                                            <p class="text-[10px] text-slate-400 lato-regular">{{ $row['plan']->user->department?->name ?? '—' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <div class="w-20 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-emerald-500 rounded-full" style="width:{{ $row['progress'] }}%"></div>
                                            </div>
                                            <span class="text-xs lato-bold text-emerald-600 dark:text-emerald-400 w-8 text-right">{{ $row['progress'] }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if ($ranking['bottom']->isNotEmpty())
                            <div class="px-5 pt-3 pb-4 border-t border-slate-50 dark:border-slate-700/60 mt-2">
                                <p class="text-[10px] lato-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1">
                                    <x-lucide-trending-down class="w-3 h-3 text-amber-500" /> Precisam de atenção
                                </p>
                                <div class="space-y-2">
                                    @foreach ($ranking['bottom'] as $i => $row)
                                        @php
                                            $bParts = explode(' ', trim($row['plan']->user->name));
                                            $bInit  = strtoupper(substr($bParts[0],0,1).(isset($bParts[1])?substr($bParts[1],0,1):''));
                                            $bBg    = $rPal[abs(crc32($row['plan']->user->name)) % count($rPal)];
                                        @endphp
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-full {{ $bBg }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0">{{ $bInit }}</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $row['plan']->user->name }}</p>
                                                <p class="text-[10px] text-slate-400 lato-regular">{{ $row['plan']->user->department?->name ?? '—' }}</p>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <div class="w-20 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                    <div class="h-full bg-amber-400 rounded-full" style="width:{{ $row['progress'] }}%"></div>
                                                </div>
                                                <span class="text-xs lato-bold text-amber-600 dark:text-amber-400 w-8 text-right">{{ $row['progress'] }}%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

            </div>

            {{-- ── Prazos vencidos + Colaboradores sem plano ────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Ações com prazo vencido --}}
                @php $overdue = $this->reportOverdueActions; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold flex items-center gap-2">
                                <x-lucide-alarm-clock class="w-4 h-4 text-red-500" />
                                Prazos vencidos
                            </h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Ações não concluídas após a data limite</p>
                        </div>
                        @if ($overdue->isNotEmpty())
                            <span class="px-2.5 py-1 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs lato-bold">
                                {{ $overdue->count() }}
                            </span>
                        @endif
                    </div>

                    @if ($overdue->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-check-circle class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum prazo vencido!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 dark:divide-slate-700/50 max-h-64 overflow-y-auto">
                            @foreach ($overdue as $act)
                                @php $daysLate = now()->diffInDays($act->target_date, false) * -1; @endphp
                                <div class="flex items-start gap-3 px-5 py-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0 mt-0.5">
                                        <x-lucide-clock class="w-3.5 h-3.5 text-red-500" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $act->title }}</p>
                                        <p class="text-[11px] text-slate-400 lato-regular truncate">
                                            {{ $act->goal->plan->user->name }} · {{ $act->goal->plan->user->department?->name ?? '—' }}
                                        </p>
                                        <p class="text-[11px] text-red-500 lato-bold mt-0.5 flex items-center gap-1">
                                            <x-lucide-calendar-x class="w-3 h-3" />
                                            {{ $act->target_date->format('d/m/Y') }} · {{ $daysLate }}d atrasada
                                        </p>
                                    </div>
                                    <span class="text-[10px] lato-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 shrink-0">
                                        {{ $act->type_label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Colaboradores sem plano --}}
                @php $noPlan = $this->reportUsersWithoutPlan; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold flex items-center gap-2">
                                <x-lucide-user-x class="w-4 h-4 text-slate-400" />
                                Sem plano em {{ $selectedYear }}
                            </h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Colaboradores ativos sem DPI cadastrado</p>
                        </div>
                        @if ($noPlan->isNotEmpty())
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold">
                                {{ $noPlan->count() }}
                            </span>
                        @endif
                    </div>

                    @if ($noPlan->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-check-circle class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Todos têm plano em {{ $selectedYear }}!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 dark:divide-slate-700/50 max-h-64 overflow-y-auto">
                            @foreach ($noPlan as $u)
                                @php
                                    $uParts = explode(' ', trim($u->name));
                                    $uInit  = strtoupper(substr($uParts[0],0,1).(isset($uParts[1])?substr($uParts[1],0,1):''));
                                    $uPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                    $uBg    = $uPal[abs(crc32($u->name)) % count($uPal)];
                                @endphp
                                <div class="flex items-center gap-3 px-5 py-3">
                                    <span class="w-8 h-8 rounded-full {{ $uBg }} text-white text-xs lato-bold
                                                 flex items-center justify-center shrink-0">{{ $uInit }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $u->name }}</p>
                                        <p class="text-[11px] text-slate-400 lato-regular truncate flex items-center gap-1">
                                            <x-lucide-building-2 class="w-3 h-3" />
                                            {{ $u->department?->name ?? 'Sem departamento' }}
                                        </p>
                                    </div>
                                    <span class="text-[10px] lato-regular text-slate-400 shrink-0">
                                        {{ $u->accessProfile?->name ?? '—' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- ── Listagem individual de planos ───────────────────────── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                {{-- Filtros --}}
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col gap-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold shrink-0">
                            Planos individuais
                            <span class="ml-1 text-xs font-normal text-slate-400 lato-regular">({{ $this->reportPlans->count() }})</span>
                        </h3>
                    </div>

                    {{-- Linha de filtros --}}
                    <div class="flex flex-col gap-2.5">

                        {{-- Busca --}}
                        <div class="relative w-full">
                            <x-lucide-search class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                            <input type="text" wire:model.live.debounce.300ms="reportSearch"
                                   placeholder="Buscar colaborador..."
                                   class="w-full pl-9 pr-8 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                                          placeholder-slate-400 transition" />
                            @if ($reportSearch)
                                <button wire:click="$set('reportSearch', '')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>

                        {{-- Dropdowns --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">

                            {{-- Filtro Status --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button"
                                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                        {{ $reportFilterStatus ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    <div class="flex items-center gap-1.5 truncate">
                                        @php
                                            $statusDot = match($reportFilterStatus) {
                                                'aprovado'  => 'bg-emerald-400',
                                                'reprovado' => 'bg-red-400',
                                                'enviado'   => 'bg-amber-400',
                                                'concluido' => 'bg-teal-400',
                                                'rascunho'  => 'bg-slate-400',
                                                default     => null,
                                            };
                                        @endphp
                                        @if ($statusDot)
                                            <span class="w-2 h-2 rounded-full {{ $statusDot }} flex-shrink-0"></span>
                                        @else
                                            <x-lucide-circle-check class="w-3.5 h-3.5 flex-shrink-0" />
                                        @endif
                                        <span class="truncate text-xs lato-bold">
                                            {{ match($reportFilterStatus) {
                                                'rascunho'  => 'Em preparação',
                                                'enviado'   => 'Aguardando',
                                                'aprovado'  => 'Ativos',
                                                'reprovado' => 'Devolvidos',
                                                'concluido' => 'Concluídos',
                                                default     => 'Status',
                                            } }}
                                        </span>
                                    </div>
                                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                                        x-bind:class="open ? 'rotate-180' : ''" />
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                     class="absolute mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                                    <button @click="open=false; $wire.set('reportFilterStatus', '')" type="button"
                                            class="w-full text-left px-3 py-2 text-xs lato-regular hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                        <x-lucide-circle-check class="w-3.5 h-3.5" /> Todos os status
                                    </button>
                                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                    @foreach ([
                                        'rascunho'  => ['Em preparação', 'bg-slate-400'],
                                        'enviado'   => ['Aguardando',    'bg-amber-400'],
                                        'aprovado'  => ['Ativos',        'bg-emerald-400'],
                                        'reprovado' => ['Devolvidos',    'bg-red-400'],
                                        'concluido' => ['Concluídos',    'bg-teal-400'],
                                    ] as $val => [$label, $dot])
                                        <button @click="open=false; $wire.set('reportFilterStatus', '{{ $val }}')" type="button"
                                                class="w-full text-left px-3 py-2 text-xs lato-regular flex items-center gap-2 transition
                                                {{ $reportFilterStatus === $val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                            <span class="w-2 h-2 rounded-full {{ $dot }} flex-shrink-0"></span>
                                            {{ $label }}
                                            @if ($reportFilterStatus === $val)
                                                <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" />
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Filtro Departamento --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button"
                                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                        {{ $reportFilterDept ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <x-lucide-building-2 class="w-3.5 h-3.5 flex-shrink-0" />
                                        <span class="truncate text-xs lato-bold">
                                            @php $deptName = $reportFilterDept ? ($this->reportDepartments->find($reportFilterDept)?->name ?? 'Depto.') : 'Departamento'; @endphp
                                            {{ $deptName }}
                                        </span>
                                    </div>
                                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                                        x-bind:class="open ? 'rotate-180' : ''" />
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                     class="absolute mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                                    <button @click="open=false; $wire.set('reportFilterDept', '')" type="button"
                                            class="w-full text-left px-3 py-2 text-xs lato-regular hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                        <x-lucide-building-2 class="w-3.5 h-3.5" /> Todos os departamentos
                                    </button>
                                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                    <div class="overflow-y-auto max-h-48">
                                        @foreach ($this->reportDepartments as $dept)
                                            <button @click="open=false; $wire.set('reportFilterDept', '{{ $dept->id }}')" type="button"
                                                    class="w-full text-left px-3 py-2 text-xs lato-regular flex items-center justify-between transition
                                                    {{ $reportFilterDept == $dept->id ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                                {{ $dept->name }}
                                                @if ($reportFilterDept == $dept->id)
                                                    <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" />
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Limpar filtros --}}
                            @if ($reportSearch || $reportFilterStatus || $reportFilterDept)
                                <button wire:click="clearReportFilters" type="button"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs lato-bold
                                               text-red-400 hover:text-red-600 border border-red-200 dark:border-red-900/50
                                               hover:border-red-400 rounded-xl bg-red-50 dark:bg-red-900/10
                                               hover:bg-red-100 dark:hover:bg-red-900/20 transition cursor-pointer">
                                    <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar filtros
                                </button>
                            @endif
                        </div>

                        {{-- Chips de filtros ativos --}}
                        @if ($reportSearch || $reportFilterStatus || $reportFilterDept)
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] text-slate-400 lato-regular">Filtros ativos:</span>
                                @if ($reportSearch)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] lato-bold">
                                        <x-lucide-search class="w-2.5 h-2.5" />
                                        "{{ Str::limit($reportSearch, 18) }}"
                                        <button wire:click="$set('reportSearch', '')" class="ml-0.5 hover:text-slate-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                                @if ($reportFilterStatus)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] lato-bold">
                                        {{ match($reportFilterStatus) { 'rascunho' => 'Em preparação', 'enviado' => 'Aguardando', 'aprovado' => 'Ativos', 'reprovado' => 'Devolvidos', 'concluido' => 'Concluídos', default => $reportFilterStatus } }}
                                        <button wire:click="$set('reportFilterStatus', '')" class="ml-0.5 hover:text-indigo-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                                @if ($reportFilterDept)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] lato-bold">
                                        <x-lucide-building-2 class="w-2.5 h-2.5" />
                                        {{ $this->reportDepartments->find($reportFilterDept)?->name }}
                                        <button wire:click="$set('reportFilterDept', '')" class="ml-0.5 hover:text-indigo-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tabela --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs lato-regular">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-700 text-[10px] lato-bold uppercase tracking-wider text-slate-400">
                                <th class="px-5 py-2.5 text-left">Colaborador</th>
                                <th class="px-3 py-2.5 text-left">Departamento</th>
                                <th class="px-3 py-2.5 text-left">Perfil</th>
                                <th class="px-3 py-2.5 text-center">Status</th>
                                <th class="px-3 py-2.5 text-center">Progresso</th>
                                <th class="px-3 py-2.5 text-center">Competências</th>
                                <th class="px-3 py-2.5 text-center">Ações</th>
                                <th class="px-3 py-2.5 text-center">Concluídas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                            @forelse ($this->reportPlans as $plan)
                                @php
                                    $rSc = match($plan->status) {
                                        'rascunho'  => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                                        'enviado'   => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300',
                                        'aprovado'  => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300',
                                        'reprovado' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                                        'concluido' => 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300',
                                        default     => 'bg-slate-100 text-slate-500',
                                    };
                                    $rTotal = $plan->actions->count();
                                    $rDone  = $plan->actions->where('status','concluido')->count();
                                    $rParts = explode(' ', trim($plan->user->name));
                                    $rInit  = strtoupper(substr($rParts[0],0,1) . (isset($rParts[1]) ? substr($rParts[1],0,1) : ''));
                                    $rPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                    $rBg    = $rPal[abs(crc32($plan->user->name)) % count($rPal)];
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-7 h-7 rounded-full {{ $rBg }} text-white text-[10px] lato-bold
                                                         flex items-center justify-center shrink-0">{{ $rInit }}</span>
                                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $plan->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-400">{{ $plan->user->department?->name ?? '—' }}</td>
                                    <td class="px-3 py-3 text-slate-400">{{ $plan->user->accessProfile?->name ?? '—' }}</td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $rSc }}">{{ $plan->status_label }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-1.5 justify-center">
                                            <div class="w-14 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $plan->progress_percent >= 75 ? 'bg-emerald-500' : ($plan->progress_percent >= 40 ? 'bg-amber-400' : 'bg-slate-400') }}"
                                                     style="width:{{ $plan->progress_percent }}%"></div>
                                            </div>
                                            <span class="text-slate-400 w-8 text-right">{{ $plan->progress_percent }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center text-slate-400">{{ $plan->goals->count() }}</td>
                                    <td class="px-3 py-3 text-center text-slate-400">{{ $rTotal }}</td>
                                    <td class="px-3 py-3 text-center">
                                        @if ($rTotal > 0)
                                            <span class="{{ $rDone === $rTotal ? 'text-emerald-600 dark:text-emerald-400 lato-bold' : 'text-slate-400' }}">
                                                {{ $rDone }}/{{ $rTotal }}
                                            </span>
                                        @else
                                            <span class="text-slate-300 dark:text-slate-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-10 text-center">
                                        <x-lucide-file-search class="w-7 h-7 text-slate-200 dark:text-slate-600 mx-auto mb-2" />
                                        <p class="text-sm text-slate-400 lato-regular">Nenhum plano encontrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         ABA: RELATÓRIO DO SETOR (Gerente)
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'relatorio_setor' && auth()->user()->isGerente())
        @php
            $gs = $this->reportSetorStats;
            $gUser = auth()->user();
        @endphp

        <div class="space-y-5">

            {{-- ── Cabeçalho do setor ─────────────────────────────────── --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-violet-100
                            dark:from-indigo-900/30 dark:to-violet-900/30 flex items-center justify-center shrink-0">
                    <x-lucide-building-2 class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white lato-bold">
                        Relatório do Setor — {{ $selectedYear }}
                    </h2>
                    <p class="text-xs text-slate-400 lato-regular">
                        {{ $gUser->department?->name ?? 'Seu departamento' }} · {{ $gs['total_members'] ?? 0 }} colaborador(es) direto(s)
                    </p>
                </div>
            </div>

            {{-- ── KPI Cards ─────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

                {{-- Colaboradores com plano --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div class="flex-1">
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">
                            {{ $gs['with_plan'] ?? 0 }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $gs['total_members'] ?? 0 }}</span>
                        </p>
                        <p class="text-xs text-slate-400 lato-regular">Com plano criado</p>
                        @if (($gs['total_members'] ?? 0) > 0)
                            <div class="mt-1.5 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full"
                                     style="width: {{ round(($gs['with_plan'] / $gs['total_members']) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Progresso médio --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-trending-up class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $gs['avg_progress'] ?? 0 }}%</p>
                        <p class="text-xs text-slate-400 lato-regular">Progresso médio</p>
                    </div>
                </div>

                {{-- Ações concluídas --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-check-circle class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">
                            {{ $gs['done_actions'] ?? 0 }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $gs['total_actions'] ?? 0 }}</span>
                        </p>
                        <p class="text-xs text-slate-400 lato-regular">Ações concluídas</p>
                    </div>
                </div>

                {{-- Planos ativos --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-shield-check class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $gs['aprovado'] ?? 0 }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Planos ativos</p>
                    </div>
                </div>

                {{-- Sem plano --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ ($gs['without_plan'] ?? 0) > 0 ? 'bg-red-100 dark:bg-red-900/30' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center shrink-0">
                        <x-lucide-user-x class="w-4 h-4 {{ ($gs['without_plan'] ?? 0) > 0 ? 'text-red-500' : 'text-slate-400' }}" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black {{ ($gs['without_plan'] ?? 0) > 0 ? 'text-red-500' : 'text-slate-800 dark:text-white' }}">{{ $gs['without_plan'] ?? 0 }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Sem plano</p>
                    </div>
                </div>

            </div>

            {{-- ── Gráfico de status + Ações por status ───────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Donut: distribuição de planos por status --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold mb-4">
                        Status dos planos
                    </h3>
                    @if (($gs['with_plan'] ?? 0) > 0)
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-setor-status-chart');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'donut', height: 180, background: 'transparent', toolbar: { show: false } },
                                        series:      [{{ $gs['rascunho'] }}, {{ $gs['enviado'] }}, {{ $gs['aprovado'] }}, {{ $gs['reprovado'] }}, {{ $gs['concluido'] }}],
                                        labels:      ['Em preparação','Aguardando','Ativos','Devolvidos','Concluídos'],
                                        colors:      ['#94a3b8','#f59e0b','#10b981','#ef4444','#14b8a6'],
                                        legend:      { position: 'bottom', labels: { colors: dark ? '#94a3b8' : '#64748b' }, fontSize: '11px' },
                                        dataLabels:  { style: { fontSize: '10px' } },
                                        plotOptions: { pie: { donut: { size: '60%' } } },
                                        theme:       { mode: dark ? 'dark' : 'light' },
                                        tooltip:     { theme: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })()
                             ">
                            <div id="dpi-setor-status-chart" style="min-height:180px;"></div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-pie-chart class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum plano em {{ $selectedYear }}</p>
                        </div>
                    @endif

                    {{-- Mini-legenda com contagens --}}
                    <div class="mt-3 space-y-1.5">
                        @foreach ([
                            ['Em preparação', 'bg-slate-400',   $gs['rascunho']  ?? 0],
                            ['Aguardando',    'bg-amber-400',   $gs['enviado']   ?? 0],
                            ['Ativos',        'bg-emerald-500', $gs['aprovado']  ?? 0],
                            ['Devolvidos',    'bg-red-400',     $gs['reprovado'] ?? 0],
                            ['Concluídos',    'bg-teal-500',    $gs['concluido'] ?? 0],
                        ] as [$lbl, $dot, $cnt])
                            <div class="flex items-center justify-between text-xs lato-regular">
                                <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                    <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span>{{ $lbl }}
                                </span>
                                <span class="lato-bold text-slate-700 dark:text-slate-200">{{ $cnt }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Donut: distribuição de ações por status --}}
                @php $gas = $this->reportSetorActionStatus; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold mb-4">
                        Status das ações
                    </h3>
                    @if (($gas['total'] ?? 0) > 0)
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-setor-actions-chart');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'donut', height: 180, background: 'transparent', toolbar: { show: false } },
                                        series:      [{{ $gas['pendente'] }}, {{ $gas['em_andamento'] }}, {{ $gas['concluido'] }}],
                                        labels:      ['Pendente','Em andamento','Concluída'],
                                        colors:      ['#cbd5e1','#6366f1','#10b981'],
                                        legend:      { position: 'bottom', labels: { colors: dark ? '#94a3b8' : '#64748b' }, fontSize: '11px' },
                                        dataLabels:  { style: { fontSize: '10px' } },
                                        plotOptions: { pie: { donut: { size: '60%' } } },
                                        theme:       { mode: dark ? 'dark' : 'light' },
                                        tooltip:     { theme: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })()
                             ">
                            <div id="dpi-setor-actions-chart" style="min-height:180px;"></div>
                        </div>

                        {{-- Totalizadores --}}
                        <div class="mt-3 space-y-1.5">
                            @foreach ([
                                ['Pendentes',    'bg-slate-300',   $gas['pendente']],
                                ['Em andamento', 'bg-indigo-500',  $gas['em_andamento']],
                                ['Concluídas',   'bg-emerald-500', $gas['concluido']],
                            ] as [$lbl, $dot, $cnt])
                                <div class="flex items-center justify-between text-xs lato-regular">
                                    <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                        <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span>{{ $lbl }}
                                    </span>
                                    <span class="lato-bold text-slate-700 dark:text-slate-200">{{ $cnt }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-bar-chart-2 class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhuma ação registrada</p>
                        </div>
                    @endif
                </div>

                {{-- Ranking de progresso do setor --}}
                @php $grank = $this->reportSetorRanking; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Ranking do setor</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Progresso por colaborador</p>
                    </div>

                    @if ($grank['top']->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-trophy class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum plano ativo em {{ $selectedYear }}</p>
                        </div>
                    @else
                        <div class="px-5 py-4 space-y-3">
                            @foreach ($grank['top'] as $i => $row)
                                @php
                                    $grParts = explode(' ', trim($row['plan']->user->name));
                                    $grInit  = strtoupper(substr($grParts[0],0,1).(isset($grParts[1])?substr($grParts[1],0,1):''));
                                    $grPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                    $grBg    = $grPal[abs(crc32($row['plan']->user->name)) % count($grPal)];
                                    $grPct   = $row['progress'];
                                    $grBar   = $grPct >= 75 ? 'bg-emerald-500' : ($grPct >= 40 ? 'bg-amber-400' : 'bg-slate-400');
                                @endphp
                                <div class="flex items-center gap-3">
                                    <span class="text-[11px] lato-bold text-slate-300 dark:text-slate-600 w-4 text-right shrink-0">{{ $i+1 }}</span>
                                    <span class="w-7 h-7 rounded-full {{ $grBg }} text-white text-[10px] lato-bold
                                                 flex items-center justify-center shrink-0">{{ $grInit }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $row['plan']->user->name }}</p>
                                        <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mt-1">
                                            <div class="h-full {{ $grBar }} rounded-full" style="width:{{ $grPct }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs lato-bold w-9 text-right shrink-0
                                                 {{ $grPct >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($grPct >= 40 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400') }}">
                                        {{ $grPct }}%
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- ── Prazos vencidos + Sem plano ─────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Ações com prazo vencido --}}
                @php $govd = $this->reportSetorOverdue; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold flex items-center gap-2">
                                <x-lucide-alarm-clock class="w-4 h-4 text-red-500" />
                                Prazos vencidos no setor
                            </h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Ações não concluídas após a data limite</p>
                        </div>
                        @if ($govd->isNotEmpty())
                            <span class="px-2.5 py-1 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs lato-bold">
                                {{ $govd->count() }}
                            </span>
                        @endif
                    </div>

                    @if ($govd->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-check-circle class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum prazo vencido!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 dark:divide-slate-700/50 max-h-72 overflow-y-auto">
                            @foreach ($govd as $gact)
                                @php $gdLate = now()->diffInDays($gact->target_date, false) * -1; @endphp
                                <div class="flex items-start gap-3 px-5 py-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0 mt-0.5">
                                        <x-lucide-clock class="w-3.5 h-3.5 text-red-500" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $gact->title }}</p>
                                        <p class="text-[11px] text-slate-400 lato-regular truncate">
                                            {{ $gact->goal->plan->user->name }}
                                        </p>
                                        <p class="text-[11px] text-red-500 lato-bold mt-0.5 flex items-center gap-1">
                                            <x-lucide-calendar-x class="w-3 h-3" />
                                            {{ $gact->target_date->format('d/m/Y') }} · {{ $gdLate }}d atrasada
                                        </p>
                                    </div>
                                    <span class="text-[10px] lato-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 shrink-0">
                                        {{ $gact->type_label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Colaboradores sem plano --}}
                @php $gnp = $this->reportSetorWithoutPlan; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold flex items-center gap-2">
                                <x-lucide-user-x class="w-4 h-4 text-slate-400" />
                                Sem plano em {{ $selectedYear }}
                            </h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Colaboradores do setor sem DPI cadastrado</p>
                        </div>
                        @if ($gnp->isNotEmpty())
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold">
                                {{ $gnp->count() }}
                            </span>
                        @endif
                    </div>

                    @if ($gnp->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-check-circle class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Todos têm plano em {{ $selectedYear }}!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 dark:divide-slate-700/50 max-h-72 overflow-y-auto">
                            @foreach ($gnp as $gnu)
                                @php
                                    $gnParts = explode(' ', trim($gnu->name));
                                    $gnInit  = strtoupper(substr($gnParts[0],0,1).(isset($gnParts[1])?substr($gnParts[1],0,1):''));
                                    $gnPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                    $gnBg    = $gnPal[abs(crc32($gnu->name)) % count($gnPal)];
                                @endphp
                                <div class="flex items-center gap-3 px-5 py-3">
                                    <span class="w-8 h-8 rounded-full {{ $gnBg }} text-white text-xs lato-bold
                                                 flex items-center justify-center shrink-0">{{ $gnInit }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $gnu->name }}</p>
                                        <p class="text-[11px] text-slate-400 lato-regular">{{ $gnu->accessProfile?->name ?? '—' }}</p>
                                    </div>
                                    <button wire:click="createPlanForEmployee({{ $gnu->id }})" type="button"
                                            title="Criar plano para este colaborador"
                                            class="flex items-center gap-1 text-[11px] lato-bold text-indigo-500 hover:text-indigo-700
                                                   dark:text-indigo-400 dark:hover:text-indigo-300 transition cursor-pointer">
                                        <x-lucide-plus class="w-3 h-3" /> Criar plano
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- ── Tabela individual de planos do setor ─────────────────── --}}
            @php $gplans = $this->reportSetorPlans; @endphp
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">
                            Planos individuais
                            <span class="ml-1 text-xs font-normal text-slate-400 lato-regular">({{ $gplans->count() }})</span>
                        </h3>
                    </div>

                    {{-- Linha de filtros --}}
                    <div class="flex flex-col gap-2.5">

                        {{-- Busca --}}
                        <div class="relative w-full">
                            <x-lucide-search class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                            <input type="text" wire:model.live.debounce.300ms="gerenteReportSearch"
                                   placeholder="Buscar colaborador..."
                                   class="w-full pl-9 pr-8 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                                          placeholder-slate-400 transition" />
                            @if ($gerenteReportSearch)
                                <button wire:click="$set('gerenteReportSearch', '')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>

                        {{-- Dropdowns --}}
                        <div class="grid grid-cols-2 gap-2">

                            {{-- Filtro Status --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button"
                                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                        {{ $gerenteReportStatus ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    <div class="flex items-center gap-1.5 truncate">
                                        @php
                                            $gStatusDot = match($gerenteReportStatus) {
                                                'aprovado'  => 'bg-emerald-400',
                                                'reprovado' => 'bg-red-400',
                                                'enviado'   => 'bg-amber-400',
                                                'concluido' => 'bg-teal-400',
                                                'rascunho'  => 'bg-slate-400',
                                                default     => null,
                                            };
                                        @endphp
                                        @if ($gStatusDot)
                                            <span class="w-2 h-2 rounded-full {{ $gStatusDot }} flex-shrink-0"></span>
                                        @else
                                            <x-lucide-circle-check class="w-3.5 h-3.5 flex-shrink-0" />
                                        @endif
                                        <span class="truncate text-xs lato-bold">
                                            {{ match($gerenteReportStatus) {
                                                'rascunho'  => 'Em preparação',
                                                'enviado'   => 'Aguardando',
                                                'aprovado'  => 'Ativos',
                                                'reprovado' => 'Devolvidos',
                                                'concluido' => 'Concluídos',
                                                default     => 'Status',
                                            } }}
                                        </span>
                                    </div>
                                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                                        x-bind:class="open ? 'rotate-180' : ''" />
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                     class="absolute mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                                    <button @click="open=false; $wire.set('gerenteReportStatus', '')" type="button"
                                            class="w-full text-left px-3 py-2 text-xs lato-regular hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                        <x-lucide-circle-check class="w-3.5 h-3.5" /> Todos os status
                                    </button>
                                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                    @foreach ([
                                        'rascunho'  => ['Em preparação', 'bg-slate-400'],
                                        'enviado'   => ['Aguardando',    'bg-amber-400'],
                                        'aprovado'  => ['Ativos',        'bg-emerald-400'],
                                        'reprovado' => ['Devolvidos',    'bg-red-400'],
                                        'concluido' => ['Concluídos',    'bg-teal-400'],
                                    ] as $val => [$label, $dot])
                                        <button @click="open=false; $wire.set('gerenteReportStatus', '{{ $val }}')" type="button"
                                                class="w-full text-left px-3 py-2 text-xs lato-regular flex items-center gap-2 transition
                                                {{ $gerenteReportStatus === $val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                            <span class="w-2 h-2 rounded-full {{ $dot }} flex-shrink-0"></span>
                                            {{ $label }}
                                            @if ($gerenteReportStatus === $val)
                                                <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" />
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Limpar filtros --}}
                            @if ($gerenteReportSearch || $gerenteReportStatus)
                                <button wire:click="clearGerenteReportFilters" type="button"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs lato-bold
                                               text-red-400 hover:text-red-600 border border-red-200 dark:border-red-900/50
                                               hover:border-red-400 rounded-xl bg-red-50 dark:bg-red-900/10
                                               hover:bg-red-100 dark:hover:bg-red-900/20 transition cursor-pointer">
                                    <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar filtros
                                </button>
                            @endif
                        </div>

                        {{-- Chips de filtros ativos --}}
                        @if ($gerenteReportSearch || $gerenteReportStatus)
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] text-slate-400 lato-regular">Filtros ativos:</span>
                                @if ($gerenteReportSearch)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] lato-bold">
                                        <x-lucide-search class="w-2.5 h-2.5" />
                                        "{{ Str::limit($gerenteReportSearch, 18) }}"
                                        <button wire:click="$set('gerenteReportSearch', '')" class="ml-0.5 hover:text-slate-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                                @if ($gerenteReportStatus)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] lato-bold">
                                        {{ match($gerenteReportStatus) { 'rascunho' => 'Em preparação', 'enviado' => 'Aguardando', 'aprovado' => 'Ativos', 'reprovado' => 'Devolvidos', 'concluido' => 'Concluídos', default => $gerenteReportStatus } }}
                                        <button wire:click="$set('gerenteReportStatus', '')" class="ml-0.5 hover:text-indigo-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                @if ($gplans->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <x-lucide-file-search class="w-7 h-7 text-slate-200 dark:text-slate-600 mx-auto mb-2" />
                        <p class="text-sm text-slate-400 lato-regular">Nenhum plano criado para o setor em {{ $selectedYear }}.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs lato-regular">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-700 text-[10px] lato-bold uppercase tracking-wider text-slate-400">
                                    <th class="px-5 py-2.5 text-left">Colaborador</th>
                                    <th class="px-3 py-2.5 text-center">Status</th>
                                    <th class="px-3 py-2.5 text-center">Progresso</th>
                                    <th class="px-3 py-2.5 text-center">Competências</th>
                                    <th class="px-3 py-2.5 text-center">Ações</th>
                                    <th class="px-3 py-2.5 text-center">Concluídas</th>
                                    <th class="px-3 py-2.5 text-center">Prazos vencidos</th>
                                    <th class="px-3 py-2.5 text-left">Ação rápida</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                @foreach ($gplans as $gp)
                                    @php
                                        $gpSc = match($gp->status) {
                                            'rascunho'  => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                                            'enviado'   => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300',
                                            'aprovado'  => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300',
                                            'reprovado' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                                            'concluido' => 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300',
                                            default     => 'bg-slate-100 text-slate-500',
                                        };
                                        $gpTotal    = $gp->actions->count();
                                        $gpDone     = $gp->actions->where('status','concluido')->count();
                                        $gpOverdue  = $gp->actions->filter(fn ($a) =>
                                            $a->status !== 'concluido' &&
                                            $a->target_date && $a->target_date->isPast()
                                        )->count();
                                        $gpParts = explode(' ', trim($gp->user->name));
                                        $gpInit  = strtoupper(substr($gpParts[0],0,1).(isset($gpParts[1])?substr($gpParts[1],0,1):''));
                                        $gpPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                        $gpBg    = $gpPal[abs(crc32($gp->user->name)) % count($gpPal)];
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition cursor-pointer"
                                        wire:click="goToTeamUser({{ $gp->user_id }})">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <span class="w-7 h-7 rounded-full {{ $gpBg }} text-white text-[10px] lato-bold
                                                             flex items-center justify-center shrink-0">{{ $gpInit }}</span>
                                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $gp->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center" wire:click.stop>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $gpSc }}">{{ $gp->status_label }}</span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-1.5 justify-center">
                                                <div class="w-14 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $gp->progress_percent >= 75 ? 'bg-emerald-500' : ($gp->progress_percent >= 40 ? 'bg-amber-400' : 'bg-slate-400') }}"
                                                         style="width:{{ $gp->progress_percent }}%"></div>
                                                </div>
                                                <span class="text-slate-400 w-8 text-right">{{ $gp->progress_percent }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center text-slate-400">{{ $gp->goals->count() }}</td>
                                        <td class="px-3 py-3 text-center text-slate-400">{{ $gpTotal }}</td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($gpTotal > 0)
                                                <span class="{{ $gpDone === $gpTotal && $gpTotal > 0 ? 'text-emerald-600 dark:text-emerald-400 lato-bold' : 'text-slate-400' }}">
                                                    {{ $gpDone }}/{{ $gpTotal }}
                                                </span>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($gpOverdue > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 lato-bold">
                                                    {{ $gpOverdue }}
                                                </span>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3" wire:click.stop>
                                            <button wire:click="goToTeamUser({{ $gp->user_id }})"
                                                    type="button"
                                                    class="text-[11px] lato-bold text-indigo-500 hover:text-indigo-700
                                                           dark:text-indigo-400 dark:hover:text-indigo-300 transition cursor-pointer
                                                           flex items-center gap-1">
                                                <x-lucide-arrow-right class="w-3 h-3" /> Ver plano
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         ABA: ANÁLISES AVANÇADAS
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'analises')
        @php $ap = $this->analyticsPersonal; @endphp
        <div class="space-y-6">

            {{-- ── Cabeçalho Análise Pessoal ─────────────────────────────── --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600
                            flex items-center justify-center shadow shadow-indigo-400/30">
                    <x-lucide-chart-no-axes-combined class="w-4.5 h-4.5 text-white" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white lato-bold">
                        Análise do Meu Plano — {{ $selectedYear }}
                    </h2>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Visão analítica detalhada do seu desenvolvimento</p>
                </div>
            </div>

            @if (empty($ap))
                {{-- Estado vazio --}}
                <div class="flex flex-col items-center justify-center py-20 text-center
                            bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <x-lucide-line-chart class="w-12 h-12 text-slate-200 dark:text-slate-600 mb-3" />
                    <p class="text-sm lato-bold text-slate-500">Nenhum plano DPI em {{ $selectedYear }}</p>
                    <p class="text-xs text-slate-400 lato-regular mt-1">Crie um plano para visualizar análises detalhadas</p>
                </div>
            @else

                {{-- ── KPIs pessoais ──────────────────────────────────── --}}
                @php
                    $apKpis = [
                        ['label' => 'Total de ações',  'value' => $ap['totalActions'], 'icon' => 'layers',          'ring' => 'ring-indigo-400/20',  'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',   'text' => 'text-indigo-600 dark:text-indigo-400'],
                        ['label' => 'Concluídas',       'value' => $ap['doneActions'],  'icon' => 'check-circle-2',  'ring' => 'ring-emerald-400/20', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                        ['label' => 'Em andamento',     'value' => $ap['inProgress'],   'icon' => 'zap',             'ring' => 'ring-blue-400/20',    'bg' => 'bg-blue-50 dark:bg-blue-900/20',       'text' => 'text-blue-600 dark:text-blue-400'],
                        ['label' => 'Pendentes',        'value' => $ap['pending'],      'icon' => 'clock',           'ring' => 'ring-slate-300/30',   'bg' => 'bg-slate-100 dark:bg-slate-700',        'text' => 'text-slate-500 dark:text-slate-400'],
                        ['label' => 'Atrasadas',        'value' => $ap['overdue'],      'icon' => 'alarm-clock',     'ring' => $ap['overdue'] > 0 ? 'ring-red-400/20' : 'ring-emerald-400/20', 'bg' => $ap['overdue'] > 0 ? 'bg-red-50 dark:bg-red-900/20' : 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => $ap['overdue'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'],
                    ];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    @foreach ($apKpis as $k)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 ring-1 {{ $k['ring'] }}">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 rounded-lg {{ $k['bg'] }} flex items-center justify-center">
                                    <x-dynamic-component :component="'lucide-' . $k['icon']" class="w-3.5 h-3.5 {{ $k['text'] }}" />
                                </div>
                                <p class="text-[11px] text-slate-400 lato-regular leading-tight">{{ $k['label'] }}</p>
                            </div>
                            <p class="text-2xl lato-black {{ $k['text'] }}">{{ $k['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- ── Gauge + Status Donut + Ações em Risco ─────────────── --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                    {{-- Gauge de progresso geral --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 flex flex-col">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-1">Progresso geral</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-3">{{ $ap['doneActions'] }} de {{ $ap['totalActions'] }} ações concluídas</p>
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-an-gauge');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:   { type: 'radialBar', height: 200, toolbar: { show: false }, background: 'transparent' },
                                        series:  [{{ $ap['progress'] }}],
                                        plotOptions: {
                                            radialBar: {
                                                startAngle: -90, endAngle: 90,
                                                hollow: { size: '60%' },
                                                dataLabels: {
                                                    name:  { show: true,  offsetY: -8,  color: dark?'#94a3b8':'#64748b', fontSize: '11px' },
                                                    value: { show: true,  offsetY: -40, color: dark?'#f1f5f9':'#1e293b', fontSize: '30px', fontWeight: 900,
                                                             formatter: function(v){ return v+'%'; } }
                                                }
                                            }
                                        },
                                        labels:  ['Concluído'],
                                        colors:  ['#6366f1'],
                                        fill:    { type: 'gradient', gradient: { shade: 'light', type: 'horizontal', gradientToColors: ['#8b5cf6'] } },
                                        theme:   { mode: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })();
                             ">
                            <div id="dpi-an-gauge"></div>
                        </div>
                        {{-- Barra de velocidade / projeção --}}
                        <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-700 space-y-2 text-[11px] lato-regular text-slate-400">
                            <div class="flex justify-between">
                                <span>Concluídas nos últimos 30 dias</span>
                                <span class="lato-bold text-slate-600 dark:text-slate-300">{{ $ap['recentDone'] }}</span>
                            </div>
                            @if ($ap['projectionDays'] !== null)
                                <div class="flex justify-between">
                                    <span>Projeção de conclusão</span>
                                    <span class="lato-bold text-indigo-500">~{{ $ap['projectionDays'] }} dias</span>
                                </div>
                            @elseif ($ap['doneActions'] === $ap['totalActions'] && $ap['totalActions'] > 0)
                                <div class="flex items-center gap-1 text-emerald-500 lato-bold">
                                    <x-lucide-check-circle class="w-3 h-3" /> Plano concluído!
                                </div>
                            @else
                                <div class="flex justify-between">
                                    <span>Projeção</span>
                                    <span class="text-slate-300 dark:text-slate-600">Sem dados suficientes</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Donut de distribuição por status --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-1">Distribuição por status</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-3">Como estão suas ações agora</p>
                        @if ($ap['totalActions'] > 0)
                            <div wire:ignore
                                 x-data
                                 x-init="
                                    (function init() {
                                        if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                        var el = $el.querySelector('#dpi-an-status');
                                        if (!el) return;
                                        if (el._chart) { el._chart.destroy(); }
                                        var dark = document.documentElement.classList.contains('dark');
                                        el._chart = new ApexCharts(el, {
                                            chart:       { type: 'donut', height: 210, background: 'transparent', toolbar: { show: false } },
                                            series:      [{{ $ap['doneActions'] }}, {{ $ap['inProgress'] }}, {{ $ap['pending'] }}, {{ $ap['overdue'] }}],
                                            labels:      ['Concluídas','Em andamento','Pendentes','Atrasadas'],
                                            colors:      ['#10b981','#3b82f6','#94a3b8','#ef4444'],
                                            legend:      { position: 'bottom', labels: { colors: dark?'#94a3b8':'#64748b' }, fontSize: '11px' },
                                            dataLabels:  { style: { fontSize: '11px' } },
                                            plotOptions: { pie: { donut: { size: '60%' } } },
                                            theme:       { mode: dark?'dark':'light' },
                                            tooltip:     { theme: dark?'dark':'light' },
                                        });
                                        el._chart.render();
                                    })();
                                 ">
                                <div id="dpi-an-status"></div>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-40 text-slate-300 dark:text-slate-600">
                                <x-lucide-pie-chart class="w-10 h-10" />
                            </div>
                        @endif
                    </div>

                    {{-- Ações em risco --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                                    <x-lucide-shield-alert class="w-3.5 h-3.5 text-amber-500" />
                                    Ações em risco
                                </h3>
                                <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Atrasadas ou prazo em 14 dias</p>
                            </div>
                            @if ($ap['atRisk']->isNotEmpty())
                                <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400">
                                    {{ $ap['atRisk']->count() }}
                                </span>
                            @endif
                        </div>
                        @if ($ap['atRisk']->isEmpty())
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <x-lucide-shield-check class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                                <p class="text-xs text-slate-400 lato-regular">Nenhuma ação em risco!</p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                @foreach ($ap['atRisk'] as $rAction)
                                    @php
                                        $daysLeft  = now()->diffInDays($rAction->target_date, false);
                                        $isOverdue = $daysLeft < 0;
                                        $rColor    = $isOverdue ? 'red' : ($daysLeft <= 7 ? 'amber' : 'yellow');
                                        $rBgMap    = ['red'=>'bg-red-50 dark:bg-red-900/20','amber'=>'bg-amber-50 dark:bg-amber-900/20','yellow'=>'bg-yellow-50 dark:bg-yellow-900/20'];
                                        $rTextMap  = ['red'=>'text-red-600 dark:text-red-400','amber'=>'text-amber-600 dark:text-amber-400','yellow'=>'text-yellow-600 dark:text-yellow-400'];
                                    @endphp
                                    <div class="flex items-start gap-3 px-4 py-3">
                                        <div class="w-7 h-7 rounded-lg {{ $rBgMap[$rColor] }} flex items-center justify-center shrink-0 mt-0.5">
                                            <x-lucide-clock class="w-3.5 h-3.5 {{ $rTextMap[$rColor] }}" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $rAction->title }}</p>
                                            <p class="text-[11px] text-slate-400 lato-regular truncate">{{ $rAction->goal->name }}</p>
                                            <p class="text-[10px] {{ $rTextMap[$rColor] }} lato-bold mt-0.5">
                                                {{ $isOverdue ? abs($daysLeft).'d atrasada' : ($daysLeft === 0 ? 'Vence hoje' : 'Vence em '.$daysLeft.'d') }}
                                                · {{ $rAction->target_date->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>{{-- /grid gauge+donut+risk --}}

                {{-- ── Radar de Competências + Evolução Mensal ───────────── --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    {{-- Radar: nível atual vs meta por competência --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-0.5">Radar de competências</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-3">Nível atual vs meta definida</p>
                        @if (count($ap['radarLabels']) >= 2)
                            <div wire:ignore
                                 x-data
                                 x-init="
                                    (function init() {
                                        if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                        var el = $el.querySelector('#dpi-an-radar');
                                        if (!el) return;
                                        if (el._chart) { el._chart.destroy(); }
                                        var dark = document.documentElement.classList.contains('dark');
                                        el._chart = new ApexCharts(el, {
                                            chart:   { type: 'radar', height: 280, toolbar: { show: false }, background: 'transparent' },
                                            series:  [
                                                { name: 'Nível Atual', data: {{ json_encode($ap['radarCurrent']) }} },
                                                { name: 'Meta',        data: {{ json_encode($ap['radarTarget'])  }} },
                                            ],
                                            labels:  {{ json_encode($ap['radarLabels']) }},
                                            colors:  ['#6366f1','#a78bfa'],
                                            fill:    { opacity: [0.3, 0.1] },
                                            stroke:  { width: [2, 2], dashArray: [0, 5] },
                                            markers: { size: 4 },
                                            yaxis:   { min: 0, max: 5, tickAmount: 5, labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '10px' } } },
                                            xaxis:   { labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '11px' } } },
                                            legend:  { labels: { colors: dark?'#94a3b8':'#64748b' } },
                                            theme:   { mode: dark?'dark':'light' },
                                            tooltip: { theme: dark?'dark':'light' },
                                        });
                                        el._chart.render();
                                    })();
                                 ">
                                <div id="dpi-an-radar"></div>
                            </div>
                        @elseif (count($ap['radarLabels']) === 1)
                            {{-- Single goal: show a simple level bar instead --}}
                            @php $g0 = $ap['radarLabels'][0]; @endphp
                            <div class="flex flex-col gap-3 py-4">
                                <p class="text-xs lato-bold text-slate-600 dark:text-slate-300">{{ $g0 }}</p>
                                <div class="flex items-center gap-3">
                                    <span class="text-[11px] text-slate-400 w-20 shrink-0">Atual: <strong>{{ $ap['radarCurrent'][0] }}/5</strong></span>
                                    <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 rounded-full" style="width:{{ $ap['radarCurrent'][0] / 5 * 100 }}%"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[11px] text-slate-400 w-20 shrink-0">Meta: <strong>{{ $ap['radarTarget'][0] }}/5</strong></span>
                                    <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-violet-400 rounded-full" style="width:{{ $ap['radarTarget'][0] / 5 * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-40 text-slate-300 dark:text-slate-600">
                                <p class="text-xs text-slate-400 lato-regular">Adicione competências para ver o radar</p>
                            </div>
                        @endif

                        {{-- Prioridades footer --}}
                        @if (array_sum($ap['goalsByPriority']) > 0)
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center gap-4 text-[11px] lato-regular text-slate-400">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400"></span>Alta: {{ $ap['goalsByPriority']['alta'] }}</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Média: {{ $ap['goalsByPriority']['media'] }}</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>Baixa: {{ $ap['goalsByPriority']['baixa'] }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Evolução mensal de conclusões --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-0.5">Evolução de conclusões</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-3">Ações concluídas por mês nos últimos 6 meses</p>
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-an-monthly');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'area', height: 200, toolbar: { show: false }, background: 'transparent' },
                                        series:      [{ name: 'Concluídas', data: {{ json_encode($ap['monthCounts']) }} }],
                                        xaxis:       { categories: {{ json_encode($ap['monthLabels']) }}, labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '11px' } } },
                                        yaxis:       { min: 0, tickAmount: 4, labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '10px' } } },
                                        colors:      ['#10b981'],
                                        fill:        { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
                                        stroke:      { width: 2, curve: 'smooth' },
                                        dataLabels:  { enabled: true, style: { fontSize: '10px', colors: ['#10b981'] }, background: { enabled: false } },
                                        grid:        { borderColor: dark?'#334155':'#f1f5f9', strokeDashArray: 4 },
                                        theme:       { mode: dark?'dark':'light' },
                                        tooltip:     { theme: dark?'dark':'light' },
                                    });
                                    el._chart.render();
                                })();
                             ">
                            <div id="dpi-an-monthly"></div>
                        </div>

                        {{-- Velocidade + projeção textual --}}
                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 gap-3 text-[11px]">
                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                                <p class="text-slate-400 lato-regular">Ritmo (30 dias)</p>
                                <p class="text-base lato-black text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $ap['recentDone'] }}
                                    <span class="text-xs font-normal text-slate-400 lato-regular">ações</span>
                                </p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                                <p class="text-slate-400 lato-regular">Projeção final</p>
                                @if ($ap['projectionDays'] !== null)
                                    <p class="text-base lato-black text-violet-600 dark:text-violet-400 mt-0.5">~{{ $ap['projectionDays'] }}d</p>
                                @elseif ($ap['doneActions'] === $ap['totalActions'] && $ap['totalActions'] > 0)
                                    <p class="text-base lato-black text-emerald-600 mt-0.5">Concluído ✓</p>
                                @else
                                    <p class="text-slate-300 dark:text-slate-600 lato-regular mt-0.5">—</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>{{-- /grid radar+monthly --}}

                {{-- ── Ações por tipo ──────────────────────────────────── --}}
                @if (! empty($ap['typeData']))
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-0.5">Ações por tipo</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-4">Total e concluídas por categoria de desenvolvimento</p>
                        <div class="space-y-3">
                            @php
                                $typeColors = ['Curso'=>'indigo','Certificação'=>'violet','Leitura'=>'teal','Mentoria'=>'purple','Projeto'=>'orange','Workshop'=>'pink','Outro'=>'slate'];
                                $typeHex    = ['Curso'=>'#6366f1','Certificação'=>'#8b5cf6','Leitura'=>'#14b8a6','Mentoria'=>'#a855f7','Projeto'=>'#f97316','Workshop'=>'#ec4899','Outro'=>'#94a3b8'];
                            @endphp
                            @foreach ($ap['typeData'] as $td)
                                @php
                                    $tdPct = $td['total'] > 0 ? round($td['done'] / $td['total'] * 100) : 0;
                                    $tdHex = $typeHex[$td['label']] ?? '#94a3b8';
                                @endphp
                                <div class="flex items-center gap-3">
                                    <span class="text-xs lato-bold text-slate-600 dark:text-slate-300 w-24 shrink-0 truncate">{{ $td['label'] }}</span>
                                    <div class="flex-1 h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all" style="width:{{ $tdPct }}%; background-color:{{ $tdHex }};"></div>
                                    </div>
                                    <span class="text-[11px] lato-bold text-slate-500 dark:text-slate-400 w-16 text-right shrink-0">
                                        {{ $td['done'] }}/{{ $td['total'] }} ({{ $tdPct }}%)
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            @endif{{-- /!empty($ap) --}}

            {{-- ════════════════════════════════════════════════════════
                 ANÁLISE DA EQUIPE (Gerente + RH)
            ════════════════════════════════════════════════════════ --}}
            @if (auth()->user()->isGerente() || auth()->user()->isRhOuDp())
                @php $at = $this->analyticsTeam; @endphp
                @if (! empty($at))

                    {{-- Divisória --}}
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-6 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600
                                    flex items-center justify-center shadow shadow-emerald-400/30">
                            <x-lucide-users class="w-4.5 h-4.5 text-white" />
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white lato-bold">
                                Análise da Equipe — {{ $selectedYear }}
                            </h2>
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">Visão consolidada do desenvolvimento da sua equipe</p>
                        </div>
                    </div>

                    {{-- KPIs da equipe --}}
                    @php
                        $teamKpis = [
                            ['label'=>'Colaboradores', 'value'=> $at['totalMembers'], 'sub'=> $at['withPlan'].' com plano',     'icon'=>'users',       'bg'=>'bg-indigo-50 dark:bg-indigo-900/20',   'text'=>'text-indigo-600 dark:text-indigo-400'],
                            ['label'=>'Progresso médio','value'=> $at['avgProgress'].'%','sub'=> 'média do time',              'icon'=>'trending-up', 'bg'=>'bg-blue-50 dark:bg-blue-900/20',       'text'=>'text-blue-600 dark:text-blue-400'],
                            ['label'=>'Em risco',       'value'=> $at['atRiskCount'], 'sub'=> 'com ações atrasadas',           'icon'=>'alert-circle','bg'=> $at['atRiskCount']>0?'bg-red-50 dark:bg-red-900/20':'bg-emerald-50 dark:bg-emerald-900/20','text'=> $at['atRiskCount']>0?'text-red-600 dark:text-red-400':'text-emerald-600 dark:text-emerald-400'],
                            ['label'=>'Health Score',   'value'=> $at['healthScore'].'%','sub'=> 'índice de saúde do time',   'icon'=>'heart-pulse', 'bg'=> $at['healthScore']>=80?'bg-emerald-50 dark:bg-emerald-900/20':($at['healthScore']>=60?'bg-amber-50 dark:bg-amber-900/20':'bg-red-50 dark:bg-red-900/20'),'text'=> $at['healthScore']>=80?'text-emerald-600 dark:text-emerald-400':($at['healthScore']>=60?'text-amber-600 dark:text-amber-400':'text-red-600 dark:text-red-400')],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($teamKpis as $tk)
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 rounded-lg {{ $tk['bg'] }} flex items-center justify-center">
                                        <x-dynamic-component :component="'lucide-' . $tk['icon']" class="w-3.5 h-3.5 {{ $tk['text'] }}" />
                                    </div>
                                    <p class="text-[11px] text-slate-400 lato-regular">{{ $tk['label'] }}</p>
                                </div>
                                <p class="text-xl lato-black {{ $tk['text'] }}">{{ $tk['value'] }}</p>
                                <p class="text-[10px] text-slate-400 lato-regular mt-0.5">{{ $tk['sub'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Gráfico de progresso por colaborador --}}
                    @if (count($at['chartNames']) > 0)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                            <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-0.5">Progresso por colaborador</h3>
                            <p class="text-[11px] text-slate-400 lato-regular mb-3">Ordenado do mais avançado para o menos avançado</p>
                            <div wire:ignore
                                 x-data
                                 x-init="
                                    (function init() {
                                        if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                        var el = $el.querySelector('#dpi-an-team-progress');
                                        if (!el) return;
                                        if (el._chart) { el._chart.destroy(); }
                                        var dark = document.documentElement.classList.contains('dark');
                                        var h = Math.max(180, {{ count($at['chartNames']) }} * 44 + 60);
                                        el._chart = new ApexCharts(el, {
                                            chart:       { type: 'bar', height: h, toolbar: { show: false }, background: 'transparent' },
                                            series:      [{ name: 'Progresso (%)', data: {{ json_encode($at['chartProgress']) }} }],
                                            plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '55%',
                                                           dataLabels: { position: 'right' } } },
                                            dataLabels:  { enabled: true, formatter: function(val){ return val+'%'; },
                                                           style: { fontSize: '11px', colors: [dark?'#94a3b8':'#475569'] },
                                                           offsetX: 8 },
                                            xaxis:       { categories: {{ json_encode($at['chartNames']) }},
                                                           max: 100,
                                                           labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '11px' } } },
                                            yaxis:       { labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '11px' } } },
                                            colors:      ['#6366f1'],
                                            fill:        { type: 'gradient', gradient: { gradientToColors: ['#8b5cf6'], type: 'horizontal' } },
                                            grid:        { borderColor: dark?'#334155':'#f1f5f9', strokeDashArray: 4 },
                                            theme:       { mode: dark?'dark':'light' },
                                            tooltip:     { theme: dark?'dark':'light' },
                                        });
                                        el._chart.render();
                                    })();
                                 ">
                                <div id="dpi-an-team-progress"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Tabela de membros com health score --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                            <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200">Detalhamento por colaborador</h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Progresso, conclusões e saúde do plano individual</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs lato-regular">
                                <thead>
                                    <tr class="border-b border-slate-100 dark:border-slate-700">
                                        <th class="text-left px-5 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Colaborador</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Status plano</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Progresso</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Concluídas</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Em andamento</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Atrasadas</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Health</th>
                                        <th class="px-3 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                    @foreach ($at['rows'] as $tr)
                                        @php
                                            $trPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                            $trBg    = $trPal[abs(crc32($tr['name'])) % count($trPal)];
                                            $trParts = explode(' ', trim($tr['name']));
                                            $trInit  = strtoupper(substr($trParts[0],0,1).(isset($trParts[1])?substr($trParts[1],0,1):''));
                                            $trBar   = $tr['progress'] >= 75 ? 'bg-emerald-500' : ($tr['progress'] >= 40 ? 'bg-amber-400' : 'bg-slate-300 dark:bg-slate-600');
                                            $trSc    = match($tr['status']) {
                                                'aprovado'  => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400',
                                                'enviado'   => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400',
                                                'concluido' => 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400',
                                                'reprovado' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400',
                                                default     => 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400',
                                            };
                                            $trStLbl = match($tr['status']) {
                                                'aprovado'  => 'Ativo',
                                                'enviado'   => 'Em revisão',
                                                'concluido' => 'Concluído',
                                                'reprovado' => 'Devolvido',
                                                default     => 'Rascunho',
                                            };
                                            $trHealth     = $tr['health'];
                                            $trHealthText = $trHealth >= 80 ? 'text-emerald-600 dark:text-emerald-400' : ($trHealth >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
                                            $trHealthBg   = $trHealth >= 80 ? 'bg-emerald-50 dark:bg-emerald-900/20' : ($trHealth >= 60 ? 'bg-amber-50 dark:bg-amber-900/20' : 'bg-red-50 dark:bg-red-900/20');
                                        @endphp
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                            <td class="px-5 py-3">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-7 h-7 rounded-full {{ $trBg }} text-white text-[10px] lato-bold
                                                                 flex items-center justify-center shrink-0">{{ $trInit }}</span>
                                                    <span class="lato-bold text-slate-700 dark:text-slate-200 truncate max-w-[140px]">{{ $tr['name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $trSc }}">{{ $trStLbl }}</span>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="flex items-center gap-2 justify-center">
                                                    <div class="w-16 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                        <div class="h-full {{ $trBar }} rounded-full" style="width:{{ $tr['progress'] }}%"></div>
                                                    </div>
                                                    <span class="text-[11px] lato-bold text-slate-500 dark:text-slate-400 w-8">{{ $tr['progress'] }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-center lato-bold text-emerald-600 dark:text-emerald-400">{{ $tr['done'] }}</td>
                                            <td class="px-3 py-3 text-center lato-bold text-blue-500 dark:text-blue-400">{{ $tr['in_prog'] }}</td>
                                            <td class="px-3 py-3 text-center">
                                                @if ($tr['overdue'] > 0)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                                                        {{ $tr['overdue'] }}
                                                    </span>
                                                @else
                                                    <x-lucide-check class="w-3.5 h-3.5 text-emerald-400 mx-auto" />
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $trHealthBg }} {{ $trHealthText }}">
                                                    {{ $trHealth }}%
                                                </span>
                                            </td>
                                            <td class="px-3 py-3">
                                                <button wire:click="goToTeamUser({{ $tr['user_id'] }})"
                                                        type="button"
                                                        class="text-[11px] lato-bold text-indigo-500 hover:text-indigo-700
                                                               dark:text-indigo-400 dark:hover:text-indigo-300 transition cursor-pointer
                                                               flex items-center gap-1">
                                                    <x-lucide-arrow-right class="w-3 h-3" /> Ver
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Sem planos --}}
                        @php $noPlan = $at['totalMembers'] - $at['withPlan']; @endphp
                        @if ($noPlan > 0)
                            <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-700
                                        bg-amber-50/50 dark:bg-amber-900/10
                                        flex items-center gap-2 text-[11px] text-amber-700 dark:text-amber-400 lato-regular">
                                <x-lucide-alert-triangle class="w-3.5 h-3.5 shrink-0" />
                                {{ $noPlan }} {{ $noPlan === 1 ? 'colaborador não tem' : 'colaboradores não têm' }} plano em {{ $selectedYear }}
                                — acesse a aba <strong class="lato-bold ml-0.5">Equipe</strong> para criar.
                            </div>
                        @endif

                    </div>{{-- /tabela membros --}}

                @endif{{-- /!empty($at) --}}
            @endif{{-- /isGerente or isRhOuDp --}}

        </div>{{-- /space-y-6 --}}
    @endif{{-- /activeTab === analises --}}

    {{-- ════════════════════════════════════════════════════════════════
         ABA: VISUALIZAÇÕES ALTERNATIVAS (Gantt + Radar)
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'visualizacoes')
        @php
            $vp      = $this->vizPlan;
            $gd      = $this->ganttData;
            $rd      = $this->radarCompetenciasData;
            $canPick = auth()->user()->isRhOuDp() || auth()->user()->isGerente();
        @endphp

        <div class="space-y-6">

            {{-- ── Cabeçalho + seletor de usuário + toggle Gantt/Radar ── --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <x-lucide-calendar-days class="w-5 h-5 text-indigo-500" />
                    <h2 class="text-lg lato-bold text-slate-800 dark:text-slate-100">Visualizações Alternativas</h2>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    {{-- Seletor de colaborador (RH / Gerente) — dropdown customizado --}}
                    @if ($canPick)
                        @php
                            $tmOptions = $this->teamMembers->map(fn($tm) => ['id' => $tm->id, 'name' => $tm->name])->values()->toArray();
                        @endphp
                        <div class="relative"
                             x-data="{
                                open: false,
                                selectedId: @entangle('vizUserId').live,
                                options: @js($tmOptions),
                                get selectedLabel() {
                                    if (!this.selectedId) return '— Meu plano —';
                                    const opt = this.options.find(o => String(o.id) === String(this.selectedId));
                                    return opt ? opt.name : '— Meu plano —';
                                },
                                select(id) {
                                    this.selectedId = id;
                                    this.open = false;
                                }
                             }"
                             x-on:keydown.escape="open = false"
                             x-on:click.outside="open = false">

                            {{-- Botão trigger --}}
                            <button type="button"
                                    x-on:click="open = !open"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm font-medium
                                           shadow-sm cursor-pointer select-none transition-all duration-150
                                           border-slate-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800
                                           text-slate-700 dark:text-slate-200
                                           hover:border-indigo-400 dark:hover:border-indigo-500
                                           hover:bg-slate-50 dark:hover:bg-slate-700"
                                    x-bind:class="open ? 'border-indigo-400 ring-2 ring-indigo-300 dark:ring-indigo-700' : ''">
                                <x-lucide-user class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                                <span x-text="selectedLabel" class="max-w-[160px] truncate"></span>
                                <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0 transition-transform duration-200"
                                                       x-bind:class="open ? 'rotate-180' : ''" />
                            </button>

                            {{-- Lista de opções --}}
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                                 class="absolute left-0 top-full mt-1.5 z-50 min-w-full w-max max-w-xs
                                        rounded-xl border border-slate-200 dark:border-slate-600
                                        bg-white dark:bg-slate-800
                                        shadow-lg dark:shadow-slate-900/60
                                        overflow-hidden"
                                 style="display:none">

                                {{-- Meu plano --}}
                                <button type="button"
                                        x-on:click="select('')"
                                        class="flex items-center gap-2.5 w-full px-3.5 py-2.5 text-sm text-left
                                               transition-colors duration-100 cursor-pointer group"
                                        x-bind:class="!selectedId
                                            ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-semibold'
                                            : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/60 font-normal'">
                                    <x-lucide-star class="w-3.5 h-3.5 shrink-0 text-indigo-400" />
                                    <span>— Meu plano —</span>
                                    <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500 transition-opacity"
                                                    x-bind:class="!selectedId ? 'opacity-100' : 'opacity-0'" />
                                </button>

                                {{-- Divider --}}
                                <div class="h-px bg-slate-100 dark:bg-slate-700 mx-2"></div>

                                {{-- Membros --}}
                                <template x-for="opt in options" x-bind:key="opt.id">
                                    <button type="button"
                                            x-on:click="select(opt.id)"
                                            class="flex items-center gap-2.5 w-full px-3.5 py-2.5 text-sm text-left
                                                   transition-colors duration-100 cursor-pointer"
                                            x-bind:class="String(selectedId) === String(opt.id)
                                                ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-semibold'
                                                : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 font-normal'">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50
                                                    flex items-center justify-center shrink-0">
                                            <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase leading-none"
                                                  x-text="opt.name.charAt(0)"></span>
                                        </div>
                                        <span x-text="opt.name" class="truncate"></span>
                                        <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500 transition-opacity shrink-0"
                                                        x-bind:class="String(selectedId) === String(opt.id) ? 'opacity-100' : 'opacity-0'" />
                                    </button>
                                </template>
                            </div>
                        </div>
                    @endif

                    {{-- Toggle Gantt / Radar --}}
                    <div class="inline-flex rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden text-sm">
                        <button wire:click="$set('vizView','gantt')" type="button"
                                class="flex items-center gap-1.5 px-3 py-1.5 transition cursor-pointer
                                       {{ $vizView === 'gantt'
                                           ? 'bg-indigo-600 text-white'
                                           : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <x-lucide-gantt-chart class="w-3.5 h-3.5" /> Linha do Tempo
                        </button>
                        <button wire:click="$set('vizView','radar')" type="button"
                                class="flex items-center gap-1.5 px-3 py-1.5 transition cursor-pointer
                                       {{ $vizView === 'radar'
                                           ? 'bg-indigo-600 text-white'
                                           : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <x-lucide-radar class="w-3.5 h-3.5" /> Radar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sem plano --}}
            @if (! $vp)
                <div class="flex flex-col items-center justify-center py-16 text-slate-400 dark:text-slate-500 gap-3">
                    <x-lucide-file-search class="w-12 h-12 opacity-40" />
                    <p class="text-sm">Nenhum plano DPI encontrado para {{ $selectedYear }}.</p>
                </div>

            {{-- ──────────── GANTT (ApexGantt) ──────────── --}}
            @elseif ($vizView === 'gantt')
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                    {{-- Cabeçalho --}}
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">Linha do Tempo — {{ $selectedYear }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ count($gd['series']) }} tarefas
                                @if ($gd['no_date_count'] > 0)
                                    · <span class="text-amber-500">{{ $gd['no_date_count'] }} ações sem prazo (não exibidas)</span>
                                @endif
                            </p>
                        </div>
                        {{-- Legenda de cores --}}
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#6366f1] inline-block"></span> Objetivo</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#10b981] inline-block"></span> Concluída</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#3b82f6] inline-block"></span> Em andamento</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#94a3b8] inline-block"></span> Pendente</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#ef4444] inline-block"></span> Atrasada</span>
                        </div>
                    </div>

                    {{-- Gráfico --}}
                    @if (empty($gd['series']))
                        <div class="flex flex-col items-center justify-center py-14 text-slate-400 dark:text-slate-500 gap-3">
                            <x-lucide-calendar-x-2 class="w-10 h-10 opacity-40" />
                            <p class="text-sm">Nenhuma ação com prazo definido encontrada.</p>
                        </div>
                    @else
                        @php
                            $ganttRowCount = count($gd['series']);
                            $ganttH        = max(500, $ganttRowCount * 42 + 100);
                        @endphp
                        <div wire:ignore
                             x-data="{ ganttSeries: @js($gd['series']) }"
                             x-init="
                                (function init() {
                                    if (typeof ApexGantt === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#gantt-viz-dpi');
                                    if (!el) return;
                                    if (el._gantt && !el._gantt.isDestroyed()) { el._gantt.destroy(); }
                                    var _stored = localStorage.getItem('theme');
                                    var dark = _stored === 'dark' ||
                                               (!_stored && window.matchMedia('(prefers-color-scheme: dark)').matches) ||
                                               document.documentElement.classList.contains('dark');

                                    var lightTheme = {
                                        backgroundColor:   '#ffffff',
                                        headerBackground:  '#f1f5f9',
                                        fontColor:         '#334155',
                                        borderColor:       '#e2e8f0',
                                        cellBorderColor:   '#e2e8f0',
                                        barTextColor:      '#ffffff',
                                        arrowColor:        '#6366f1',
                                        tooltipBGColor:    '#ffffff',
                                        tooltipBorderColor:'#cbd5e1',
                                        rowBackgroundColors: ['#ffffff', '#f8fafc'],
                                        annotationBgColor:   '#e0e7ff',
                                        annotationBorderColor: '#6366f1',
                                    };
                                    var darkTheme = {
                                        backgroundColor:   '#1e293b',
                                        headerBackground:  '#0f172a',
                                        fontColor:         '#cbd5e1',
                                        borderColor:       '#334155',
                                        cellBorderColor:   '#334155',
                                        barTextColor:      '#ffffff',
                                        arrowColor:        '#818cf8',
                                        tooltipBGColor:    '#1e293b',
                                        tooltipBorderColor:'#475569',
                                        rowBackgroundColors: ['#1e293b', '#172033'],
                                        annotationBgColor:   '#312e81',
                                        annotationBorderColor: '#818cf8',
                                    };
                                    var t = dark ? darkTheme : lightTheme;

                                    el._gantt = new ApexGantt(el, {
                                        series: $data.ganttSeries,
                                        inputDateFormat: 'YYYY-MM-DD',
                                        theme: dark ? 'dark' : 'light',
                                        height: {{ $ganttH }},
                                        fontFamily: 'Lato, sans-serif',
                                        fontSize: '13px',
                                        rowHeight: 40,
                                        tasksContainerWidth: 320,
                                        barMargin: 5,
                                        barBorderRadius: '5px',
                                        enableTaskDrag: false,
                                        enableTaskResize: false,
                                        enableTaskEdit: false,
                                        enableExport: false,
                                        enableTooltip: true,
                                        toolbarItems: [],
                                        backgroundColor:       t.backgroundColor,
                                        headerBackground:      t.headerBackground,
                                        fontColor:             t.fontColor,
                                        borderColor:           t.borderColor,
                                        cellBorderColor:       t.cellBorderColor,
                                        cellBorderWidth:       '1px',
                                        barTextColor:          t.barTextColor,
                                        arrowColor:            t.arrowColor,
                                        tooltipBGColor:        t.tooltipBGColor,
                                        tooltipBorderColor:    t.tooltipBorderColor,
                                        rowBackgroundColors:   t.rowBackgroundColors,
                                        annotationBgColor:     t.annotationBgColor,
                                        annotationBorderColor: t.annotationBorderColor,
                                        annotations: [{
                                            x1: new Date().toISOString().slice(0, 10),
                                            label: { text: 'Hoje', fontColor: dark ? '#f1f5f9' : '#1e293b', fontSize: '11px', fontWeight: 'bold' },
                                        }],
                                    });
                                    el._gantt.render();
                                })()
                             ">
                            <div id="gantt-viz-dpi" style="min-height:{{ $ganttH }}px;"></div>
                            <style>
                                /* Oculta toolbar de zoom nativo do ApexGantt */
                                #gantt-viz-dpi .apexgantt-toolbar { display: none !important; }
                            </style>
                        </div>
                    @endif

                </div>

            {{-- ──────────── RADAR DE COMPETÊNCIAS ──────────── --}}
            @elseif ($vizView === 'radar')
                @if (empty($rd))
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400 dark:text-slate-500 gap-3">
                        <x-lucide-target class="w-12 h-12 opacity-40" />
                        <p class="text-sm">Nenhuma competência cadastrada no plano.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

                        {{-- Radar chart (col. esquerda, ocupa 2 de 5) --}}
                        <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 flex flex-col">
                            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 mb-1">Radar de Competências</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Nível atual vs. meta por competência</p>

                            @php
                                $radarVizLabels  = array_values(array_column($rd, 'short'));
                                $radarVizCurrent = array_values(array_map(fn($v) => (float)($v ?? 0), array_column($rd, 'nivel_atual')));
                                $radarVizTarget  = array_values(array_map(fn($v) => (float)($v ?? 0), array_column($rd, 'nivel_meta')));
                                $radarCanRender  = count($radarVizLabels) >= 3;
                            @endphp

                            @if (! $radarCanRender)
                                <div class="flex flex-col items-center justify-center flex-1 py-10 text-slate-400 dark:text-slate-500 gap-2">
                                    <x-lucide-triangle-alert class="w-8 h-8 opacity-40" />
                                    <p class="text-xs text-center">O radar requer pelo menos 3 competências.<br>Adicione mais objetivos ao plano.</p>
                                </div>
                            @else
                            <div wire:ignore
                                 x-data="{ radarCurrent: @js($radarVizCurrent), radarTarget: @js($radarVizTarget), radarLabels: @js($radarVizLabels) }"
                                 x-init="
                                    (function init() {
                                        if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                        var el = $el.querySelector('#radar-viz-dpi');
                                        if (!el) return;
                                        if (el._chart) { el._chart.destroy(); }
                                        var dark = document.documentElement.classList.contains('dark');
                                        var cur = $data.radarCurrent.map(function(v) { return isNaN(v) || v === null ? 0 : Number(v); });
                                        var tgt = $data.radarTarget.map(function(v)  { return isNaN(v) || v === null ? 0 : Number(v); });
                                        el._chart = new ApexCharts(el, {
                                            series: [
                                                { name: 'Nível Atual', data: cur },
                                                { name: 'Meta',        data: tgt },
                                            ],
                                            chart: {
                                                type: 'radar',
                                                height: 380,
                                                toolbar: { show: false },
                                                background: 'transparent',
                                                fontFamily: 'Lato, sans-serif',
                                            },
                                            xaxis: { categories: $data.radarLabels },
                                            yaxis: { min: 0, max: 5, tickAmount: 5, show: false },
                                            stroke: { width: [2, 2], dashArray: [0, 5] },
                                            fill:   { opacity: [0.25, 0.08] },
                                            colors: ['#6366f1', '#f59e0b'],
                                            markers: { size: [4, 4] },
                                            legend: {
                                                show: true,
                                                position: 'bottom',
                                                labels: { colors: dark ? '#cbd5e1' : '#475569' },
                                            },
                                            tooltip: {
                                                y: { formatter: function(v) { return 'Nível ' + v; } }
                                            },
                                            plotOptions: {
                                                radar: {
                                                    polygons: {
                                                        strokeColors: dark ? '#334155' : '#e2e8f0',
                                                        connectorColors: dark ? '#334155' : '#e2e8f0',
                                                        fill: { colors: [dark ? '#1e293b' : '#f8fafc', dark ? '#0f172a' : '#f1f5f9'] },
                                                    }
                                                }
                                            },
                                            theme: { mode: dark ? 'dark' : 'light' },
                                        });
                                        el._chart.render();
                                    })()
                                 "
                                 class="flex-1">
                                <div id="radar-viz-dpi" style="min-height:380px;"></div>
                            </div>
                            @endif
                        </div>

                        {{-- Cards de competência (col. direita, ocupa 3 de 5) --}}
                        <div class="xl:col-span-3 space-y-4">
                            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">Detalhamento por Competência</h3>

                            @foreach ($rd as $comp)
                                @php
                                    $prioColor = match($comp['priority']) {
                                        'alta'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'media' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                                    };
                                    $prioLabel = ['alta' => 'Alta', 'media' => 'Média', 'baixa' => 'Baixa'][$comp['priority']] ?? $comp['priority'];
                                @endphp
                                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">

                                    {{-- Nome + prioridade + gap --}}
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $comp['name'] }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="text-xs px-2 py-0.5 rounded-full {{ $prioColor }}">{{ $prioLabel }}</span>
                                            @if ($comp['gap'] > 0)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                                    Gap +{{ $comp['gap'] }}
                                                </span>
                                            @else
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                    Meta atingida
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Nível: 5 segmentos preenchidos --}}
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-xs text-slate-500 dark:text-slate-400 w-16 shrink-0">Nível atual</span>
                                        <div class="flex gap-1">
                                            @for ($dot = 1; $dot <= 5; $dot++)
                                                <div class="w-6 h-2.5 rounded-sm transition-colors
                                                    {{ $dot <= $comp['nivel_atual']
                                                        ? 'bg-indigo-500'
                                                        : ($dot <= $comp['nivel_meta']
                                                            ? 'bg-indigo-200 dark:bg-indigo-900/50'
                                                            : 'bg-slate-100 dark:bg-slate-700') }}">
                                                </div>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $comp['nivel_atual'] }}/{{ $comp['nivel_meta'] }}
                                        </span>
                                    </div>

                                    {{-- Progresso das ações --}}
                                    @if ($comp['total'] > 0)
                                        <div class="mb-2">
                                            <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                                                <span>Ações: {{ $comp['done'] }}/{{ $comp['total'] }} concluídas</span>
                                                <span class="lato-bold {{ $comp['progress'] >= 75 ? 'text-emerald-600' : ($comp['progress'] >= 40 ? 'text-indigo-600' : 'text-slate-500') }}">
                                                    {{ $comp['progress'] }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all
                                                    {{ $comp['progress'] >= 75 ? 'bg-emerald-500' : ($comp['progress'] >= 40 ? 'bg-indigo-500' : 'bg-slate-400') }}"
                                                     style="width:{{ $comp['progress'] }}%">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Mini contadores --}}
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @if ($comp['in_prog'] > 0)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                    {{ $comp['in_prog'] }} em andamento
                                                </span>
                                            @endif
                                            @if ($comp['pending'] > 0)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                                    {{ $comp['pending'] }} pendente{{ $comp['pending'] > 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                            @if ($comp['overdue'] > 0)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                    ⚠ {{ $comp['overdue'] }} atrasada{{ $comp['overdue'] > 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-400 dark:text-slate-500 italic">Nenhuma ação cadastrada.</p>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

        </div>{{-- /space-y-6 --}}
    @endif{{-- /activeTab === visualizacoes --}}

    {{-- ════════════════════════════════════════════════════════════════
         MODAL ÚNICO: Competência + Ações de Desenvolvimento
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($goalModal)
        @php $nivelOpts = \App\Models\DpiGoal::nivelLabels(); @endphp
        <div x-data="{ show: false }" x-init="$nextTick(() => show = true)">

            {{-- Backdrop --}}
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm"
                 wire:click="$set('goalModal', false)"></div>

            {{-- Wrapper de posicionamento (sem x-show) --}}
            <div class="fixed inset-x-0 bottom-0 z-50 pointer-events-none
                        sm:inset-0 sm:flex sm:items-center sm:justify-center sm:p-4">

                {{-- Painel com transição --}}
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-y-full sm:translate-y-4 sm:opacity-0"
                     x-transition:enter-end="translate-y-0 sm:opacity-100"
                     class="pointer-events-auto w-full sm:max-w-2xl max-h-[92vh] flex flex-col
                            bg-white dark:bg-slate-800 rounded-t-2xl sm:rounded-2xl
                            border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700 shadow-2xl">

                {{-- Handle (só mobile) --}}
                <div class="flex sm:hidden justify-center pt-3 pb-1 shrink-0">
                    <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-600"></div>
                </div>

                {{-- Cabeçalho --}}
                <div class="flex items-center justify-between px-6 pt-4 sm:pt-5 pb-4 shrink-0
                            border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100
                                    dark:from-blue-900/30 dark:to-indigo-900/30 flex items-center justify-center">
                            <x-lucide-brain class="w-4 h-4 text-indigo-500" />
                        </div>
                        <h3 class="text-base lato-bold text-slate-800 dark:text-white">
                            {{ $editGoalId ? 'Editar Competência' : 'Nova Competência' }}
                        </h3>
                    </div>
                    <button wire:click="$set('goalModal', false)" type="button"
                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                   text-slate-400 hover:text-slate-700 hover:bg-slate-100
                                   dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Conteúdo scrollável --}}
                <div class="overflow-y-auto flex-1 px-6 py-5">
                <div class="space-y-5">

                    {{-- ── SEÇÃO 1: Mapeamento da Competência ── --}}
                    <div>
                        <p class="text-[11px] lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">
                            Mapeamento de Competência
                        </p>
                        <div class="space-y-4">

                            {{-- Nome --}}
                            <div>
                                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                    Nome da Competência <span class="text-red-400">*</span>
                                </label>
                                <input wire:model="compNome" type="text"
                                       placeholder="Ex: Comunicação assertiva, Liderança de equipes..."
                                       class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                              bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                              border-slate-200 dark:border-slate-700
                                              focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                                @error('compNome') <p class="mt-1 text-xs text-red-400 lato-regular">{{ $message }}</p> @enderror
                            </div>

                            {{-- Prioridade + Níveis (grid 3 colunas) --}}
                            <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                                {{-- Prioridade --}}
                                <div>
                                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Prioridade</label>
                                    <div x-data="{ open: false, val: @entangle('compPrioridade') }"
                                         @click.outside="open = false" class="relative">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center gap-2 pl-3 pr-8 py-2.5 text-sm lato-bold rounded-xl
                                                       bg-slate-50 dark:bg-slate-700 text-slate-700 dark:text-slate-200
                                                       border border-slate-200 dark:border-slate-600
                                                       focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition text-left">
                                            <span x-show="val === 'alta'"  class="shrink-0"><x-lucide-arrow-up  class="w-4 h-4 text-red-500" /></span>
                                            <span x-show="val === 'media'" class="shrink-0"><x-lucide-minus      class="w-4 h-4 text-amber-500" /></span>
                                            <span x-show="val === 'baixa'" class="shrink-0"><x-lucide-arrow-down class="w-4 h-4 text-blue-400" /></span>
                                            <span x-text="{ alta: 'Alta', media: 'Média', baixa: 'Baixa' }[val]" class="truncate"></span>
                                        </button>
                                        <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                                               ::class="{ 'rotate-180': open }" />
                                        <div x-show="open" x-transition
                                             class="absolute z-[60] w-full mt-1 bg-white dark:bg-slate-800 rounded-xl
                                                    border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                            @foreach ([['alta','Alta','arrow-up','text-red-500'],['media','Média','minus','text-amber-500'],['baixa','Baixa','arrow-down','text-blue-400']] as [$v,$l,$i,$c])
                                                <button type="button" @click="val = '{{ $v }}'; open = false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm lato-regular hover:bg-slate-50 dark:hover:bg-slate-700/60 transition cursor-pointer"
                                                        :class="{ 'bg-indigo-50 dark:bg-indigo-900/20 lato-bold': val === '{{ $v }}' }">
                                                    <x-dynamic-component :component="'lucide-' . $i" class="w-4 h-4 shrink-0 {{ $c }}" />
                                                    <span class="text-slate-700 dark:text-slate-200 flex-1 text-left">{{ $l }}</span>
                                                    <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0" x-show="val === '{{ $v }}'" />
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Nível Atual --}}
                                <div>
                                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                        Nível Atual
                                    </label>
                                    <div x-data="{ open: false, val: @entangle('compNivelAtual') }"
                                         @click.outside="open = false" class="relative">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center gap-2.5 pl-3 pr-8 py-2.5 rounded-xl
                                                       bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                                       focus:outline-none focus:ring-2 focus:ring-blue-400/40 cursor-pointer transition text-left">
                                            @foreach ($nivelOpts as $nv => $nl)
                                                <span x-show="val == {{ $nv }}" class="flex items-center gap-2 w-full">
                                                    <span class="w-6 h-6 rounded-full bg-blue-500 text-white text-xs lato-bold flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/30">{{ $nv }}</span>
                                                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $nl }}</span>
                                                </span>
                                            @endforeach
                                        </button>
                                        <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none transition-transform duration-200"
                                                               ::class="{ 'rotate-180': open }" />
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="absolute z-[60] w-full mt-1 bg-white dark:bg-slate-800 rounded-xl
                                                    border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                            @foreach ($nivelOpts as $nv => $nl)
                                                <button type="button" @click="val = {{ $nv }}; open = false"
                                                        class="w-full flex items-center gap-3 px-3 py-2.5 transition cursor-pointer
                                                               hover:bg-slate-50 dark:hover:bg-slate-700/60"
                                                        :class="{ 'bg-blue-50 dark:bg-blue-900/20': val == {{ $nv }} }">
                                                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs lato-bold shrink-0 transition"
                                                          :class="val == {{ $nv }}
                                                              ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/30'
                                                              : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">{{ $nv }}</span>
                                                    <div class="flex-1 text-left">
                                                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $nl }}</p>
                                                        @php $descs = ['Sem conhecimento na área','Conhecimento introdutório','Aplica com supervisão','Aplica com autonomia','Referência na área']; @endphp
                                                        <p class="text-[11px] text-slate-400 lato-regular">{{ $descs[$nv - 1] }}</p>
                                                    </div>
                                                    <x-lucide-check class="w-3.5 h-3.5 text-blue-500 shrink-0"
                                                                    x-show="val == {{ $nv }}" />
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>

                                {{-- Nível Meta --}}
                                <div>
                                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                        Nível Meta
                                    </label>
                                    <div x-data="{ open: false, val: @entangle('compNivelMeta') }"
                                         @click.outside="open = false" class="relative">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center gap-2.5 pl-3 pr-8 py-2.5 rounded-xl
                                                       bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                                       focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition text-left">
                                            @foreach ($nivelOpts as $nv => $nl)
                                                <span x-show="val == {{ $nv }}" class="flex items-center gap-2 w-full">
                                                    <span class="w-6 h-6 rounded-full border-2 border-indigo-400 text-indigo-500 text-xs lato-bold flex items-center justify-center shrink-0 bg-indigo-50 dark:bg-indigo-900/20">{{ $nv }}</span>
                                                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $nl }}</span>
                                                </span>
                                            @endforeach
                                        </button>
                                        <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none transition-transform duration-200"
                                                               ::class="{ 'rotate-180': open }" />
                                        <div x-show="open"
                                             x-transition:enter="transition ease-out duration-150"
                                             x-transition:enter-start="opacity-0 -translate-y-1"
                                             x-transition:enter-end="opacity-100 translate-y-0"
                                             class="absolute z-[60] w-full mt-1 bg-white dark:bg-slate-800 rounded-xl
                                                    border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                            @foreach ($nivelOpts as $nv => $nl)
                                                <button type="button" @click="val = {{ $nv }}; open = false"
                                                        class="w-full flex items-center gap-3 px-3 py-2.5 transition cursor-pointer
                                                               hover:bg-slate-50 dark:hover:bg-slate-700/60"
                                                        :class="{ 'bg-indigo-50 dark:bg-indigo-900/20': val == {{ $nv }} }">
                                                    <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs lato-bold shrink-0 transition"
                                                          :class="val == {{ $nv }}
                                                              ? 'border-2 border-indigo-400 text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                                                              : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">{{ $nv }}</span>
                                                    <div class="flex-1 text-left">
                                                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $nl }}</p>
                                                        @php $descs = ['Sem conhecimento na área','Conhecimento introdutório','Aplica com supervisão','Aplica com autonomia','Referência na área']; @endphp
                                                        <p class="text-[11px] text-slate-400 lato-regular">{{ $descs[$nv - 1] }}</p>
                                                    </div>
                                                    <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0"
                                                                    x-show="val == {{ $nv }}" />
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Preview visual dos níveis --}}
                            <div class="bg-slate-50 dark:bg-slate-900/40 rounded-xl px-4 py-3 space-y-2">
                                <div class="flex items-center justify-center gap-2">
                                    @foreach ($nivelOpts as $n => $lbl)
                                        @if ($n <= $compNivelAtual)
                                            <span class="w-8 h-8 rounded-full bg-blue-500 text-white text-xs lato-bold flex items-center justify-center shadow-sm shadow-blue-500/30" title="{{ $lbl }}">{{ $n }}</span>
                                        @elseif ($n == $compNivelMeta)
                                            <span class="w-8 h-8 rounded-full border-2 border-indigo-400 text-indigo-500 text-xs lato-bold flex items-center justify-center bg-indigo-50 dark:bg-indigo-900/20" title="Meta: {{ $lbl }}">{{ $n }}</span>
                                        @else
                                            <span class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 text-slate-400 text-xs flex items-center justify-center">{{ $n }}</span>
                                        @endif
                                    @endforeach
                                </div>
                                <div class="flex items-center justify-center gap-4">
                                    <span class="text-[11px] text-blue-500 lato-bold flex items-center gap-1">
                                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                                        Atual: {{ $nivelOpts[$compNivelAtual] ?? '' }}
                                    </span>
                                    <span class="text-[11px] text-indigo-500 lato-bold flex items-center gap-1">
                                        <span class="w-2.5 h-2.5 rounded-full border-2 border-indigo-400 inline-block"></span>
                                        Meta: {{ $nivelOpts[$compNivelMeta] ?? '' }}
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── SEÇÃO 2: Ações de Desenvolvimento ── --}}
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-5">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[11px] lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                Ações de Desenvolvimento
                            </p>
                            @if (! $showInlineForm)
                                <button wire:click="$set('showInlineForm', true)" type="button"
                                        class="flex items-center gap-1 text-xs lato-bold text-blue-500 hover:text-blue-600 transition cursor-pointer">
                                    <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar ação
                                </button>
                            @endif
                        </div>

                        {{-- Lista de ações salvas (editando competência existente) --}}
                        @if ($editGoalId && $this->editingGoalActions->isNotEmpty())
                            <div class="mb-3 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($this->editingGoalActions as $ia)
                                    @php
                                        $iaTypes = ['curso'=>['graduation-cap','text-blue-500'],'certificacao'=>['award','text-amber-500'],'leitura'=>['book-open','text-emerald-500'],'mentoria'=>['users','text-violet-500'],'projeto'=>['folder-open','text-orange-500'],'workshop'=>['presentation','text-pink-500'],'outro'=>['circle-dot','text-slate-400']];
                                        [$iaIco, $iaClr] = $iaTypes[$ia->type] ?? ['circle-dot','text-slate-400'];
                                    @endphp
                                    <div class="flex items-start gap-3 px-4 py-2.5 bg-white dark:bg-slate-800/50
                                                {{ $editingInlineActionId === $ia->id ? 'bg-indigo-50 dark:bg-indigo-900/10' : '' }}">
                                        <div class="mt-0.5 shrink-0">
                                            <x-dynamic-component :component="'lucide-'.$iaIco" class="w-4 h-4 {{ $iaClr }}" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 leading-snug
                                                       {{ $ia->status === 'concluido' ? 'line-through text-slate-400' : '' }}">
                                                {{ $ia->title }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-0.5">
                                                <span class="text-[11px] text-slate-400 lato-regular">{{ $ia->type_label }}</span>
                                                @if ($ia->target_date)
                                                    <span class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                                        · <x-lucide-calendar class="w-3 h-3" /> {{ $ia->target_date->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                                @php
                                                    $iaSt = match($ia->status) { 'concluido' => ['text-emerald-600','Concluído'], 'em_andamento' => ['text-blue-500','Em andamento'], default => ['text-slate-400','Pendente'] };
                                                @endphp
                                                <span class="text-[11px] lato-bold {{ $iaSt[0] }}">· {{ $iaSt[1] }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-0.5 shrink-0">
                                            <button wire:click="editInlineAction({{ $ia->id }})" type="button"
                                                    class="w-6 h-6 flex items-center justify-center rounded-lg
                                                           text-slate-300 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer">
                                                <x-lucide-pencil class="w-3 h-3" />
                                            </button>
                                            <button wire:click="deleteInlineAction({{ $ia->id }})" type="button"
                                                    class="w-6 h-6 flex items-center justify-center rounded-lg
                                                           text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                                                <x-lucide-trash-2 class="w-3 h-3" />
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Lista de ações pendentes (nova competência) --}}
                        @if (! $editGoalId && count($pendingActions) > 0)
                            <div class="mb-3 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($pendingActions as $pi => $pa)
                                    @php
                                        $paTypes = ['curso'=>['graduation-cap','text-blue-500'],'certificacao'=>['award','text-amber-500'],'leitura'=>['book-open','text-emerald-500'],'mentoria'=>['users','text-violet-500'],'projeto'=>['folder-open','text-orange-500'],'workshop'=>['presentation','text-pink-500'],'outro'=>['circle-dot','text-slate-400']];
                                        [$paIco, $paClr] = $paTypes[$pa['type']] ?? ['circle-dot','text-slate-400'];
                                        $paTypeLabels = ['curso'=>'Curso','certificacao'=>'Certificação','leitura'=>'Leitura','mentoria'=>'Mentoria','projeto'=>'Projeto','workshop'=>'Workshop','outro'=>'Outro'];
                                    @endphp
                                    <div class="flex items-center gap-3 px-4 py-2.5 bg-white dark:bg-slate-800/50
                                                {{ $editingInlineActionIndex === $pi ? 'bg-indigo-50 dark:bg-indigo-900/10' : '' }}">
                                        <x-dynamic-component :component="'lucide-'.$paIco" class="w-4 h-4 {{ $paClr }} shrink-0" />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate">{{ $pa['title'] }}</p>
                                            <p class="text-[11px] text-slate-400 lato-regular">
                                                {{ $paTypeLabels[$pa['type']] ?? $pa['type'] }}
                                                @if ($pa['date']) · {{ \Carbon\Carbon::parse($pa['date'])->format('d/m/Y') }} @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-0.5 shrink-0">
                                            <button wire:click="editPendingAction({{ $pi }})" type="button"
                                                    class="w-6 h-6 flex items-center justify-center rounded-lg
                                                           text-slate-300 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer">
                                                <x-lucide-pencil class="w-3 h-3" />
                                            </button>
                                            <button wire:click="removePendingAction({{ $pi }})" type="button"
                                                    class="w-6 h-6 flex items-center justify-center rounded-lg
                                                           text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                                                <x-lucide-trash-2 class="w-3 h-3" />
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Estado vazio das ações --}}
                        @if (! $showInlineForm)
                            @php
                                $hasActions = $editGoalId
                                    ? $this->editingGoalActions->isNotEmpty()
                                    : count($pendingActions) > 0;
                            @endphp
                            @if (! $hasActions)
                                <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700
                                            py-4 text-center">
                                    <p class="text-xs text-slate-400 lato-regular">Nenhuma ação adicionada ainda.</p>
                                </div>
                            @endif
                        @endif

                        {{-- Formulário inline de ação --}}
                        @if ($showInlineForm)
                            <div class="bg-slate-50 dark:bg-slate-900/40 rounded-xl border border-slate-200 dark:border-slate-700 p-4 space-y-3">
                                <p class="text-[11px] lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    {{ $editingInlineActionId || $editingInlineActionIndex !== null ? 'Editar ação' : 'Nova ação' }}
                                </p>

                                {{-- Título --}}
                                <div>
                                    <input wire:model="inlineActionTitle" type="text"
                                           placeholder="Título da ação *"
                                           class="w-full px-3.5 py-2 text-sm lato-regular rounded-xl border
                                                  bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                  border-slate-200 dark:border-slate-700
                                                  focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                                    @error('inlineActionTitle') <p class="mt-1 text-xs text-red-400 lato-regular">{{ $message }}</p> @enderror
                                </div>

                                {{-- Tipo + Prazo --}}
                                <div class="grid grid-cols-2 gap-3">

                                    {{-- Tipo (dropdown com ícones) --}}
                                    <div x-data="{ open: false, val: @entangle('inlineActionType') }"
                                         @click.outside="open = false" class="relative">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center gap-2 pl-3 pr-8 py-2 text-sm lato-bold rounded-xl
                                                       bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                       border border-slate-200 dark:border-slate-700
                                                       focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition text-left">
                                            <span x-show="val === 'curso'"        class="shrink-0"><x-lucide-graduation-cap class="w-4 h-4 text-blue-500" /></span>
                                            <span x-show="val === 'certificacao'" class="shrink-0"><x-lucide-award          class="w-4 h-4 text-amber-500" /></span>
                                            <span x-show="val === 'leitura'"      class="shrink-0"><x-lucide-book-open      class="w-4 h-4 text-emerald-500" /></span>
                                            <span x-show="val === 'mentoria'"     class="shrink-0"><x-lucide-users          class="w-4 h-4 text-violet-500" /></span>
                                            <span x-show="val === 'projeto'"      class="shrink-0"><x-lucide-folder-open    class="w-4 h-4 text-orange-500" /></span>
                                            <span x-show="val === 'workshop'"     class="shrink-0"><x-lucide-presentation   class="w-4 h-4 text-pink-500" /></span>
                                            <span x-show="val === 'outro'"        class="shrink-0"><x-lucide-circle-dot     class="w-4 h-4 text-slate-400" /></span>
                                            <span x-text="{ curso: 'Curso', certificacao: 'Certificação', leitura: 'Leitura', mentoria: 'Mentoria', projeto: 'Projeto', workshop: 'Workshop', outro: 'Outro' }[val]" class="truncate"></span>
                                        </button>
                                        <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                                               ::class="{ 'rotate-180': open }" />
                                        <div x-show="open" x-transition
                                             class="absolute z-[70] w-full mt-1 bg-white dark:bg-slate-800 rounded-xl
                                                    border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                            @foreach ([
                                                ['curso','Curso','graduation-cap','text-blue-500'],
                                                ['certificacao','Certificação','award','text-amber-500'],
                                                ['leitura','Leitura','book-open','text-emerald-500'],
                                                ['mentoria','Mentoria','users','text-violet-500'],
                                                ['projeto','Projeto','folder-open','text-orange-500'],
                                                ['workshop','Workshop','presentation','text-pink-500'],
                                                ['outro','Outro','circle-dot','text-slate-400'],
                                            ] as [$v,$l,$i,$c])
                                                <button type="button" @click="val='{{ $v }}'; open=false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm lato-regular
                                                               hover:bg-slate-50 dark:hover:bg-slate-700/60 transition cursor-pointer"
                                                        :class="{ 'bg-indigo-50 dark:bg-indigo-900/20 lato-bold': val === '{{ $v }}' }">
                                                    <x-dynamic-component :component="'lucide-' . $i" class="w-4 h-4 shrink-0 {{ $c }}" />
                                                    <span class="text-slate-700 dark:text-slate-200 flex-1 text-left">{{ $l }}</span>
                                                    <x-lucide-check class="w-3 h-3 text-indigo-500 shrink-0" x-show="val === '{{ $v }}'" />
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Prazo --}}
                                    <div class="relative">
                                        <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                        <input wire:model="inlineActionDate" type="date"
                                               class="w-full appearance-none pl-9 pr-3 py-2 text-sm lato-regular rounded-xl border
                                                      bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                      border-slate-200 dark:border-slate-700
                                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition" />
                                    </div>
                                </div>

                                {{-- Descrição --}}
                                <textarea wire:model="inlineActionDesc" rows="2"
                                          placeholder="Descrição opcional (instituição, link, detalhes...)"
                                          class="w-full px-3.5 py-2 text-sm lato-regular rounded-xl border resize-none
                                                 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                 border-slate-200 dark:border-slate-700
                                                 focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition"></textarea>

                                {{-- Botões inline --}}
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="resetInlineActionForm" type="button"
                                            class="px-3 py-1.5 text-xs lato-bold rounded-lg border border-slate-200 dark:border-slate-700
                                                   text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                        Cancelar
                                    </button>
                                    <button wire:click="saveInlineAction" type="button"
                                            class="px-4 py-1.5 text-xs lato-bold rounded-lg
                                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                                                   shadow-sm shadow-blue-500/20 transition cursor-pointer">
                                        {{ $editingInlineActionId || $editingInlineActionIndex !== null ? 'Salvar' : '+ Adicionar' }}
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                </div>{{-- fim overflow-y-auto --}}

                {{-- Rodapé fixo --}}
                <div class="shrink-0 px-6 py-4 border-t border-slate-100 dark:border-slate-700
                            bg-white dark:bg-slate-800 flex gap-3">
                    <button wire:click="$set('goalModal', false)" type="button"
                            class="flex-1 py-2.5 text-sm lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                                   text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="saveGoal" type="button"
                            class="flex-1 py-2.5 text-sm lato-bold rounded-xl
                                   bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                   hover:from-blue-600 hover:to-indigo-700
                                   shadow-md shadow-blue-500/20 transition cursor-pointer">
                        <span wire:loading.remove wire:target="saveGoal" class="flex items-center justify-center gap-2">
                             <x-lucide-save class="w-3.5 h-3.5" />
                            {{ $editGoalId ? 'Salvar competência' : 'Criar competência' }}
                        </span>
                         <span wire:loading wire:target="saveGoal" class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4l3-3-3-3v4a8 8 0 00-8 8h4z"/>
                            </svg>
                        </span>
                    </button>
                </div>{{-- fim rodapé --}}
                </div>{{-- fim painel --}}
            </div>{{-- fim wrapper posicionamento --}}
        </div>{{-- fim x-data --}}
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         MODAL: Feedback do gestor
    ═════════════════════════════════════════════════════════════════ --}}
    {{-- ════════════════════════════════════════════════════════════════
         MODAL: Upload de evidência (diploma, certificado, etc.)
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($feedbackModal)
        <div x-data="{ show: false }" x-init="$nextTick(() => show = true)">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm"
                 wire:click="$set('feedbackModal', false)"></div>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="pointer-events-auto w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl
                            border border-slate-200 dark:border-slate-700 shadow-2xl">
                    <div class="flex items-center justify-between px-6 pt-5 pb-4
                                border-b border-slate-100 dark:border-slate-700">
                        <h2 class="text-base lato-bold text-slate-800 dark:text-slate-100">
                            {{ $reviewAction === 'aprovado' ? 'Aprovar Plano' : 'Devolver para Revisão' }}
                        </h2>
                        <button wire:click="$set('feedbackModal', false)" type="button"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600
                                       hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                    <div class="px-6 py-5 space-y-4">
                        <div>
                            <label class="block text-sm lato-bold text-slate-700 dark:text-slate-300 mb-2">Decisão</label>
                            <div class="flex gap-3">
                                <button wire:click="$set('reviewAction','aprovado')" type="button"
                                        class="flex-1 py-2 text-sm rounded-lg border transition cursor-pointer
                                               {{ $reviewAction === 'aprovado'
                                                   ? 'bg-emerald-500 border-emerald-500 text-white'
                                                   : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300' }}">
                                    ✓ Aprovar
                                </button>
                                <button wire:click="$set('reviewAction','reprovado')" type="button"
                                        class="flex-1 py-2 text-sm rounded-lg border transition cursor-pointer
                                               {{ $reviewAction === 'reprovado'
                                                   ? 'bg-red-500 border-red-500 text-white'
                                                   : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300' }}">
                                    ✕ Devolver
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm lato-bold text-slate-700 dark:text-slate-300 mb-1">
                                Feedback
                                @if ($reviewAction === 'reprovado') <span class="text-red-500">*</span> @endif
                            </label>
                            <textarea wire:model="reviewFeedback" rows="4"
                                      placeholder="{{ $reviewAction === 'reprovado' ? 'Descreva o motivo da devolução…' : 'Comentário opcional…' }}"
                                      class="w-full text-sm border border-slate-200 dark:border-slate-600 rounded-lg px-3 py-2
                                             bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100
                                             focus:outline-none focus:ring-2 focus:ring-indigo-400 resize-none"></textarea>
                            @error('reviewFeedback') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                        </div>
                    </div>
                    <div class="flex justify-end gap-3 px-6 pb-5">
                        <button wire:click="$set('feedbackModal', false)" type="button"
                                class="px-4 py-2 text-sm text-slate-600 dark:text-slate-300
                                       hover:text-slate-800 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="submitReview" type="button"
                                class="px-5 py-2 text-sm lato-bold rounded-lg text-white transition cursor-pointer
                                       {{ $reviewAction === 'aprovado' ? 'bg-emerald-600 hover:bg-emerald-700' : 'bg-red-600 hover:bg-red-700' }}">
                            {{ $reviewAction === 'aprovado' ? 'Confirmar Aprovação' : 'Confirmar Devolução' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         MODAL CONFIRMAR EXCLUSÃO
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($deleteModal)
        <div x-data="{ show: false }" x-init="$nextTick(() => show = true)">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm"
                 wire:click="$set('deleteModal', false)"></div>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="pointer-events-auto w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl
                            border border-slate-200 dark:border-slate-700 shadow-2xl p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                        <x-lucide-trash-2 class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <h2 class="text-base lato-bold text-slate-800 dark:text-slate-100 mb-2">Confirmar exclusão</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                        @if ($deleteType === 'goal')
                            Tem certeza que deseja remover esta competência e todas as suas ações?
                        @elseif ($deleteType === 'action')
                            Tem certeza que deseja remover esta ação de desenvolvimento?
                        @else
                            Esta ação não pode ser desfeita.
                        @endif
                    </p>
                    <div class="flex gap-3">
                        <button wire:click="$set('deleteModal', false)" type="button"
                                class="flex-1 px-4 py-2 text-sm border border-slate-200 dark:border-slate-600 rounded-lg
                                       text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700
                                       transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="confirmDelete" type="button"
                                class="flex-1 px-4 py-2 text-sm lato-bold bg-red-600 hover:bg-red-700 text-white
                                       rounded-lg transition cursor-pointer">
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if ($uploadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data="{ show: false, dragging: false }" x-init="$nextTick(() => show = true)">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                 wire:click="$set('uploadModal', false)"></div>
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="relative z-10 w-full max-w-md bg-white dark:bg-slate-800
                        rounded-2xl border border-slate-200 dark:border-slate-700
                        shadow-2xl p-6 space-y-5">

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <x-lucide-paperclip class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white lato-bold">Anexar Evidência</h3>
                    </div>
                    <button wire:click="$set('uploadModal', false)" type="button"
                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                   text-slate-400 hover:text-slate-700 hover:bg-slate-100
                                   dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <p class="text-xs text-slate-400 lato-regular -mt-2">
                    Diploma, certificado, comprovante ou outro documento comprobatório.
                    Formatos: PDF, imagem, Word, Excel. Máx. 10 MB.
                </p>

                {{-- Área de upload --}}
                <div x-on:dragover.prevent="dragging = true"
                     x-on:dragleave.prevent="dragging = false"
                     x-on:drop.prevent="dragging = false"
                     :class="dragging ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40'"
                     class="rounded-xl border-2 border-dashed px-4 py-6 text-center transition cursor-pointer"
                     onclick="document.getElementById('evidenceFileInput').click()">
                    <x-lucide-upload-cloud class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                    <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">
                        Arraste o arquivo aqui ou <span class="text-indigo-500 lato-bold">clique para selecionar</span>
                    </p>
                    <input id="evidenceFileInput" type="file"
                           wire:model="evidenceFile"
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                           class="hidden" />
                </div>

                @if ($evidenceFile)
                    <div class="flex items-center gap-3 px-3 py-2.5 bg-indigo-50 dark:bg-indigo-900/20
                                rounded-xl border border-indigo-200 dark:border-indigo-800">
                        <x-lucide-file class="w-5 h-5 text-indigo-500 shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">
                                {{ $evidenceFile->getClientOriginalName() }}
                            </p>
                            <p class="text-[11px] text-slate-400 lato-regular">
                                {{ number_format($evidenceFile->getSize() / 1024, 1) }} KB
                            </p>
                        </div>
                        <button wire:click="$set('evidenceFile', null)" type="button"
                                class="w-5 h-5 flex items-center justify-center rounded text-slate-400 hover:text-red-500 transition cursor-pointer">
                            <x-lucide-x class="w-3.5 h-3.5" />
                        </button>
                    </div>
                @endif

                @error('evidenceFile')
                    <p class="text-xs text-red-400 lato-regular flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3.5 h-3.5" /> {{ $message }}
                    </p>
                @enderror

                <div class="flex justify-end gap-2 pt-1">
                    <button wire:click="$set('uploadModal', false)" type="button"
                            class="px-4 py-2 text-sm lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                                   text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="saveEvidenceUpload" type="button"
                            class="px-5 py-2 text-sm lato-bold rounded-xl
                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                                   shadow-md shadow-blue-500/20 transition cursor-pointer
                                   disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ ! $evidenceFile ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="saveEvidenceUpload">Salvar Evidência</span>
                        <span wire:loading wire:target="saveEvidenceUpload">Enviando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════
         MODAL: Agendar Reunião 1:1 de Acompanhamento DPI
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($meetingModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data="{ show: false }" x-init="$nextTick(() => show = true)">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                 wire:click="$set('meetingModal', false)"></div>

            <div class="relative z-10 w-full max-w-lg bg-white dark:bg-slate-800
                        rounded-2xl border border-slate-200 dark:border-slate-700
                        shadow-2xl p-6 space-y-4 max-h-[90vh] overflow-y-auto">

                {{-- Cabeçalho --}}
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <x-lucide-calendar-plus class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <h3 class="text-base lato-bold text-slate-800 dark:text-white">Agendar reunião 1:1</h3>
                    </div>
                    <button wire:click="$set('meetingModal', false)" type="button"
                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                   text-slate-400 hover:text-slate-700 hover:bg-slate-100
                                   dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Título --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Título <span class="text-red-400">*</span>
                    </label>
                    <input wire:model="meetingTitle" type="text"
                           class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                  bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                  border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                    @error('meetingTitle') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                {{-- Data + Hora --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                            Data <span class="text-red-400">*</span>
                        </label>
                        <input wire:model="meetingDate" type="date"
                               x-on:change="$wire.set('meetingDate', $event.target.value)"
                               class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                      bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      border-slate-200 dark:border-slate-700
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                        @error('meetingDate') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                            Horário <span class="text-red-400">*</span>
                        </label>
                        <input wire:model="meetingStartTime" type="time"
                               x-on:change="$wire.set('meetingStartTime', $event.target.value)"
                               class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                      bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      border-slate-200 dark:border-slate-700
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                        @error('meetingStartTime') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Duração --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Duração</label>
                    <select wire:model="meetingDuration"
                            x-on:change="$wire.set('meetingDuration', $event.target.value)"
                            class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                   bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                   border-slate-200 dark:border-slate-700
                                   focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition">
                        <option value="30">30 minutos</option>
                        <option value="45">45 minutos</option>
                        <option value="60">1 hora</option>
                        <option value="90">1h 30min</option>
                        <option value="120">2 horas</option>
                    </select>
                </div>

                {{-- Tipo de local: Online / Presencial --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                        Tipo de reunião
                    </label>
                    <div class="inline-flex rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden w-full">
                        <button wire:click="$set('meetingLocationType','online')" type="button"
                                class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-sm transition cursor-pointer
                                       {{ $meetingLocationType === 'online'
                                           ? 'bg-indigo-600 text-white lato-bold'
                                           : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <x-lucide-video class="w-3.5 h-3.5" /> Online
                        </button>
                        <button wire:click="$set('meetingLocationType','presencial')" type="button"
                                class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-sm transition cursor-pointer
                                       {{ $meetingLocationType === 'presencial'
                                           ? 'bg-indigo-600 text-white lato-bold'
                                           : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <x-lucide-map-pin class="w-3.5 h-3.5" /> Presencial
                        </button>
                    </div>
                </div>

                {{-- Online: plataforma + link --}}
                @if ($meetingLocationType === 'online')
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Plataforma</label>
                        <select wire:model="meetingPlatform"
                                class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                       bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                       border-slate-200 dark:border-slate-700
                                       focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition">
                            <option value="google_meet">Google Meet</option>
                            <option value="teams">Microsoft Teams</option>
                            <option value="zoom">Zoom</option>
                            <option value="outro">Outro</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Link da reunião</label>
                        <input wire:model="meetingLink" type="url"
                               placeholder="https://meet.google.com/..."
                               class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                      bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      border-slate-200 dark:border-slate-700
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                        @error('meetingLink') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Presencial: seleção de sala --}}
                @if ($meetingLocationType === 'presencial')
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                            Sala <span class="text-red-400">*</span>
                        </label>

                        @if (! $meetingDate || ! $meetingStartTime)
                            <div class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20
                                        border border-amber-200 dark:border-amber-700 rounded-xl px-3 py-2">
                                Informe a data e horário para ver as salas disponíveis.
                            </div>
                        @else
                            <div wire:loading wire:target="meetingDate,meetingStartTime,meetingDuration,meetingLocationType"
                                 class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 py-2">
                                <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                    <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                                    <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                </svg>
                                Verificando disponibilidade…
                            </div>
                            <div wire:loading.remove wire:target="meetingDate,meetingStartTime,meetingDuration,meetingLocationType"
                                 class="grid grid-cols-1 gap-2">
                                @foreach ($this->availableRoomsForMeeting as $room)
                                    @php $isAvail = $room->getAttribute('is_available'); @endphp
                                    <button
                                        @if ($isAvail) wire:click="$set('meetingRoomId', {{ $room->id }})" @endif
                                        type="button"
                                        class="flex items-center gap-3 p-3 rounded-xl border-2 text-left transition
                                               {{ ! $isAvail
                                                   ? 'opacity-50 cursor-not-allowed border-slate-100 dark:border-slate-700'
                                                   : ($meetingRoomId === $room->id
                                                       ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 cursor-pointer'
                                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 hover:border-indigo-300 cursor-pointer') }}">
                                        <div class="w-5 h-5 rounded-full border-2 shrink-0 flex items-center justify-center
                                                    {{ $meetingRoomId === $room->id
                                                        ? 'border-indigo-500 bg-indigo-500'
                                                        : 'border-slate-300 dark:border-slate-600' }}">
                                            @if ($meetingRoomId === $room->id)
                                                <x-lucide-check class="w-3 h-3 text-white" />
                                            @endif
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap">
                                                <p class="text-sm lato-bold text-slate-800 dark:text-slate-100">{{ $room->name }}</p>
                                                <span class="text-xs px-1.5 py-0.5 rounded-full
                                                    {{ $isAvail
                                                        ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                                        : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                                    {{ $isAvail ? 'Disponível' : 'Ocupada' }}
                                                </span>
                                            </div>
                                            <div class="flex items-center gap-3 mt-0.5 text-xs text-slate-400 flex-wrap">
                                                <span><x-lucide-users class="w-3 h-3 inline" /> {{ $room->capacity }}</span>
                                                @if ($room->has_video_conference) <span><x-lucide-video class="w-3 h-3 inline" /> Vídeo</span> @endif
                                                @if ($room->has_projector) <span><x-lucide-projector class="w-3 h-3 inline" /> Projetor</span> @endif
                                                @if ($room->has_whiteboard) <span><x-lucide-pencil-ruler class="w-3 h-3 inline" /> Quadro</span> @endif
                                                @if ($room->has_wifi) <span><x-lucide-wifi class="w-3 h-3 inline" /> Wi-Fi</span> @endif
                                            </div>
                                        </div>
                                    </button>
                                @endforeach
                                @if ($this->availableRoomsForMeeting->isEmpty())
                                    <p class="text-xs text-slate-400 text-center py-3">Nenhuma sala cadastrada.</p>
                                @endif
                            </div>
                        @endif
                        @error('meetingRoomId') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>
                @endif

                {{-- Rodapé --}}
                <div class="flex justify-end gap-3 pt-2 border-t border-slate-100 dark:border-slate-700">
                    <button wire:click="$set('meetingModal', false)" type="button"
                            class="px-4 py-2 text-sm lato-regular text-slate-500 dark:text-slate-400
                                   hover:text-slate-700 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="saveMeeting" type="button"
                            class="px-5 py-2 text-sm lato-bold rounded-xl text-white transition cursor-pointer
                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                                   shadow-md shadow-indigo-500/20">
                        {{ $meetingLocationType === 'presencial' ? 'Agendar e Reservar Sala' : 'Agendar reunião' }}
                    </button>
                </div>

            </div>{{-- /relative z-10 --}}
        </div>{{-- /fixed inset-0 --}}
    @endif{{-- /meetingModal --}}
</div>{{-- /wire:poll wrapper --}}
