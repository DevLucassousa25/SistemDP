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
                        <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-blue-100 to-indigo-600
                                    dark:from-blue-900/30 dark:to-indigo-600/30
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

