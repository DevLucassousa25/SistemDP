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
                                   shadow-lg shadow-blue-500/20 transition cursor-pointer disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="createPlan" class="flex items-center gap-1.5">
                            <x-lucide-plus class="w-4 h-4" />
                        Criar Plano {{ $selectedYear }}
                        </span>
                        <span wire:loading wire:target="createPlan" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
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
                                           shadow-md shadow-emerald-500/20 transition cursor-pointer disabled:opacity-60"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitGerentePlanToRh" class="flex items-center gap-1.5">
                                    <x-lucide-send class="w-3.5 h-3.5" /> Finalizar e Enviar ao RH
                                </span>
                                <span wire:loading wire:target="submitGerentePlanToRh" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </span>
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
                                           shadow-md shadow-amber-500/20 transition cursor-pointer disabled:opacity-60"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="submitGerentePlanToRh" class="flex items-center gap-1.5">
                                    <x-lucide-refresh-cw class="w-3.5 h-3.5" /> Corrigido — Reenviar ao RH
                                </span>
                                <span wire:loading wire:target="submitGerentePlanToRh" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </span>
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
                                               shadow-md shadow-emerald-500/20 transition cursor-pointer disabled:opacity-60"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="submitPlan" class="flex items-center gap-1.5">
                                        <x-lucide-rocket class="w-3.5 h-3.5" /> Publicar Plano
                                    </span>
                                    <span wire:loading wire:target="submitPlan" class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    </span>
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
                                                           dark:hover:bg-red-900/20 transition cursor-pointer disabled:opacity-60"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="confirmDeleteGoal" class="flex items-center gap-1.5">
                                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                </span>
                                                <span wire:loading wire:target="confirmDeleteGoal" class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                </span>
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
                                                            @if ($action->treinamento)
                                                                <a href="{{ route('treinamentos') }}?curso={{ $action->treinamento->id }}"
                                                                   class="text-[11px] text-blue-500 lato-bold flex items-center gap-1 hover:underline">
                                                                    <x-lucide-graduation-cap class="w-3 h-3 shrink-0" />
                                                                    {{ Str::limit($action->treinamento->titulo, 40) }}
                                                                </a>
                                                            @endif
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
                                                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer disabled:opacity-60"
                                                                    wire:loading.attr="disabled">
                                                                    <span wire:loading.remove wire:target="confirmDeleteAction" class="flex items-center gap-1.5">
                                                                        <x-lucide-trash-2 class="w-3 h-3" />
                                                                    </span>
                                                                    <span wire:loading wire:target="confirmDeleteAction" class="flex items-center gap-1.5">
                                                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                                    </span>
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
        <div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center"
             x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.closeTemplateModal(), 300); } }"
             x-init="requestAnimationFrame(() => open = true)"
             x-on:keydown.escape.window="close()"
             @click.self="close()">

            <div class="w-full sm:max-w-2xl sm:mx-4 flex flex-col
                        bg-white dark:bg-slate-800
                        rounded-t-3xl sm:rounded-2xl
                        border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                        shadow-2xl transition-transform duration-300 ease-out will-change-transform"
                 :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
                 style="max-height: 88dvh;"
                 @click.stop>

                {{-- Drag handle --}}
                <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                    <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                </div>

                {{-- Header --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                            <x-lucide-layout-template class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <div>
                            <h2 class="text-sm font-bold text-slate-800 dark:text-slate-100 lato-bold">Aplicar Template</h2>
                            <p class="text-xs text-slate-400">Selecione um template para adicionar ao seu plano</p>
                        </div>
                    </div>
                    <button @click="close()" type="button"
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
                                                                             'mentoria'     => 'bg-violet-50 text-indigo-600 dark:bg-violet-900/30 dark:text-violet-300',
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
        <div class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center"
             x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.closeManageTemplates(), 300); } }"
             x-init="requestAnimationFrame(() => open = true)"
             x-on:keydown.escape.window="close()"
             @click.self="close()">

            <div class="w-full sm:max-w-3xl sm:mx-4 flex flex-col
                        bg-white dark:bg-slate-800
                        rounded-t-3xl sm:rounded-2xl
                        border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                        shadow-2xl transition-transform duration-300 ease-out will-change-transform"
                 :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
                 style="max-height: 92dvh;"
                 @click.stop>

                {{-- Drag handle --}}
                <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                    <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                </div>

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
                        <button @click="close()" type="button"
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
                                               hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer disabled:opacity-60"
                                    wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="deleteTemplate" class="flex items-center gap-1.5">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </span>
                                    <span wire:loading wire:target="deleteTemplate" class="flex items-center gap-1.5">
                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                    </span>
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
                                                    'mentoria'     => ['users',          'text-indigo-500', 'bg-violet-50 dark:bg-violet-900/30 text-indigo-600 dark:text-violet-300', 'Mentoria'],
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
                                border-t border-slate-100 dark:border-slate-700
                                pb-[max(1rem,env(safe-area-inset-bottom))]">
                        <button wire:click="$set('templateTab','list')" type="button"
                                class="px-4 py-2 rounded-lg text-sm lato-bold
                                       bg-slate-100 dark:bg-slate-700
                                       hover:bg-slate-200 dark:hover:bg-slate-600
                                       text-slate-600 dark:text-slate-300 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="saveTemplate" type="button"
                                class="px-4 py-2 rounded-lg text-sm lato-bold
                                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                                       text-white shadow-sm shadow-blue-500/20 transition cursor-pointer disabled:opacity-60"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveTemplate" class="flex items-center gap-1.5">
                                Salvar Template
                            </span>
                            <span wire:loading wire:target="saveTemplate" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                    </div>
                    {{-- /footer --}}

                @endif
                {{-- /templateTab list|create --}}

            </div>
            {{-- /modal inner --}}
        </div>
        {{-- /modal backdrop --}}
    @endif
    {{-- /manageTemplateModal --}}
