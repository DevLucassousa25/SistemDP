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

