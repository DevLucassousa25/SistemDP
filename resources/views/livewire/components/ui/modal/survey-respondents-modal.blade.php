<div x-data
     x-effect="document.body.style.overflow = ($wire.modalOpen) ? 'hidden' : ''">

    @if ($modalOpen)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                 wire:click="fecharModal"></div>

            {{-- Painel --}}
            <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh]
                        rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 flex flex-col overflow-hidden">

                {{-- ── Header ──────────────────────────────────────────── --}}
                <div class="flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 sm:py-5">

                        <div class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl
                                    {{ $isAnonymous ? 'bg-gradient-to-br from-slate-500 to-slate-600' : 'bg-gradient-to-br from-violet-500 to-purple-600' }}
                                    flex items-center justify-center shadow-sm">
                            @if ($isAnonymous)
                                <x-lucide-eye-off class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                            @else
                                <x-lucide-users class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white lato-black leading-tight">
                                Respondentes
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5 truncate"
                               title="{{ $surveyTitle }}">
                                {{ $surveyTitle }}
                            </p>
                        </div>

                        <button wire:click="fecharModal"
                            class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg
                                   text-slate-400 hover:text-slate-700 hover:bg-slate-100
                                   dark:hover:text-white dark:hover:bg-slate-700 transition cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>

                    {{-- Badge de anonimato + contador --}}
                    <div class="px-4 sm:px-6 pb-3 flex items-center gap-2 flex-wrap">
                        @if ($isAnonymous)
                            <span class="inline-flex items-center gap-1.5 text-[11px] lato-bold px-2.5 py-1 rounded-full
                                         bg-slate-100 text-slate-600 border border-slate-200
                                         dark:bg-slate-700 dark:text-slate-300 dark:border-slate-600">
                                <x-lucide-shield class="w-3 h-3" />
                                Pesquisa anônima — identidades protegidas
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-[11px] lato-bold px-2.5 py-1 rounded-full
                                         bg-violet-50 text-violet-700 border border-violet-200
                                         dark:bg-violet-900/20 dark:text-violet-400 dark:border-violet-700/40">
                                <x-lucide-eye class="w-3 h-3" />
                                Respostas identificadas
                            </span>
                        @endif

                        <span class="inline-flex items-center gap-1.5 text-[11px] lato-bold px-2.5 py-1 rounded-full
                                     bg-emerald-50 text-emerald-700 border border-emerald-200
                                     dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-700/40">
                            <x-lucide-check-circle class="w-3 h-3" />
                            {{ $this->responses->count() }} {{ $this->responses->count() === 1 ? 'resposta' : 'respostas' }}
                        </span>
                    </div>
                </div>

                {{-- ── Body ─────────────────────────────────────────────── --}}
                <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 space-y-3 bg-slate-50/40 dark:bg-slate-800/60">

                    @if ($this->responses->isEmpty())
                        {{-- Estado vazio --}}
                        <div class="flex flex-col items-center justify-center py-14 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700
                                        flex items-center justify-center mb-3">
                                <x-lucide-inbox class="w-7 h-7 text-slate-400" />
                            </div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">
                                Nenhuma resposta ainda
                            </h3>
                            <p class="text-xs text-slate-400 lato-regular mt-1 max-w-xs">
                                Assim que colaboradores responderem esta pesquisa, as respostas aparecerão aqui.
                            </p>
                        </div>

                    @elseif ($isAnonymous)
                        {{-- ═══════════════════════════════════════════════════
                             MODO ANÔNIMO — apenas contagem + data/hora
                        ════════════════════════════════════════════════════ --}}

                        <div class="bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/40
                                    rounded-xl p-3 flex items-start gap-2.5 mb-1">
                            <x-lucide-info class="w-4 h-4 text-amber-600 dark:text-amber-400 shrink-0 mt-0.5" />
                            <p class="text-xs text-amber-700 dark:text-amber-300 lato-regular">
                                Esta pesquisa é anônima. Somente o horário de envio é visível — nenhuma informação
                                de identidade é armazenada ou exibida.
                            </p>
                        </div>

                        <div class="space-y-2">
                            @foreach ($this->responses as $index => $response)
                                <div class="flex items-center gap-3 bg-white dark:bg-slate-700/60
                                            rounded-xl border border-slate-100 dark:border-slate-700
                                            px-4 py-3 shadow-sm">

                                    {{-- Avatar anônimo --}}
                                    <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-600
                                                flex items-center justify-center shrink-0">
                                        <x-lucide-user class="w-4 h-4 text-slate-400 dark:text-slate-500" />
                                    </div>

                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm lato-bold text-slate-500 dark:text-slate-400 italic">
                                            Respondente anônimo #{{ $index + 1 }}
                                        </p>
                                    </div>

                                    <div class="shrink-0 text-right">
                                        <p class="text-[11px] lato-regular text-slate-400 dark:text-slate-500">
                                            {{ $response->completed_at->format('d/m/Y') }}
                                        </p>
                                        <p class="text-[11px] lato-bold text-slate-500 dark:text-slate-400">
                                            {{ $response->completed_at->format('H:i') }}
                                        </p>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                    @else
                        {{-- ═══════════════════════════════════════════════════
                             MODO IDENTIFICADO — nome, data e expandir respostas
                        ════════════════════════════════════════════════════ --}}

                        <div class="space-y-2">
                            @foreach ($this->responses as $response)
                                @php
                                    $expanded = $expandedResponseId === $response->id;
                                    $initials = collect(explode(' ', $response->user?->name ?? 'U'))
                                        ->filter()
                                        ->map(fn($p) => strtoupper(substr($p, 0, 1)))
                                        ->take(2)
                                        ->implode('');
                                    $colors = [
                                        'from-violet-500 to-purple-600',
                                        'from-emerald-500 to-teal-500',
                                        'from-blue-500 to-indigo-600',
                                        'from-rose-500 to-pink-600',
                                        'from-amber-500 to-orange-500',
                                        'from-cyan-500 to-sky-600',
                                    ];
                                    $colorClass = $colors[$response->user_id % count($colors)];
                                @endphp

                                <div class="bg-white dark:bg-slate-700/60 rounded-xl border
                                            {{ $expanded ? 'border-violet-200 dark:border-violet-700/50 shadow-md' : 'border-slate-100 dark:border-slate-700 shadow-sm' }}
                                            overflow-hidden transition-all duration-200">

                                    {{-- Linha do respondente --}}
                                    <button type="button"
                                        wire:click="toggleExpand({{ $response->id }})"
                                        class="w-full flex items-center gap-3 px-4 py-3 text-left
                                               hover:bg-slate-50 dark:hover:bg-slate-700/40 transition cursor-pointer">

                                        {{-- Avatar com iniciais --}}
                                        <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ $colorClass }}
                                                    flex items-center justify-center shrink-0 shadow-sm">
                                            <span class="text-xs lato-black text-white">{{ $initials }}</span>
                                        </div>

                                        {{-- Nome --}}
                                        <div class="flex-1 min-w-0 text-left">
                                            <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">
                                                {{ $response->user?->name ?? 'Usuário removido' }}
                                            </p>
                                            <p class="text-[11px] lato-regular text-slate-400 dark:text-slate-500 truncate">
                                                {{ $response->user?->email ?? '' }}
                                            </p>
                                        </div>

                                        {{-- Data/hora --}}
                                        <div class="shrink-0 text-right mr-1">
                                            <p class="text-[11px] lato-regular text-slate-400 dark:text-slate-500">
                                                {{ $response->completed_at->format('d/m/Y') }}
                                            </p>
                                            <p class="text-[11px] lato-bold text-slate-500 dark:text-slate-400">
                                                {{ $response->completed_at->format('H:i') }}
                                            </p>
                                        </div>

                                        {{-- Ícone expandir --}}
                                        <div class="shrink-0 w-6 h-6 flex items-center justify-center
                                                    rounded-lg text-slate-400 dark:text-slate-500">
                                            @if ($expanded)
                                                <x-lucide-chevron-up class="w-4 h-4 text-violet-500" />
                                            @else
                                                <x-lucide-chevron-down class="w-4 h-4" />
                                            @endif
                                        </div>
                                    </button>

                                    {{-- Respostas expandidas --}}
                                    @if ($expanded)
                                        <div class="border-t border-slate-100 dark:border-slate-700
                                                    bg-slate-50/60 dark:bg-slate-800/40 px-4 py-3 space-y-3">

                                            @php
                                                $answersByQuestion = $response->answers->keyBy('question_id');
                                                $questions = $response->answers
                                                    ->map(fn($a) => $a->question)
                                                    ->filter()
                                                    ->unique('id')
                                                    ->sortBy('order');
                                            @endphp

                                            @if ($questions->isEmpty())
                                                <p class="text-xs text-slate-400 lato-regular text-center py-2">
                                                    Sem respostas registradas.
                                                </p>
                                            @else
                                                @foreach ($questions as $question)
                                                    @php
                                                        $answer = $answersByQuestion->get($question->id);
                                                    @endphp

                                                    <div class="space-y-1">
                                                        {{-- Pergunta --}}
                                                        <div class="flex items-start gap-2">
                                                            @if ($question->type === 'escala')
                                                                <x-lucide-bar-chart-2 class="w-3.5 h-3.5 text-blue-400 shrink-0 mt-0.5" />
                                                            @elseif ($question->type === 'multipla_escolha')
                                                                <x-lucide-list-checks class="w-3.5 h-3.5 text-emerald-400 shrink-0 mt-0.5" />
                                                            @else
                                                                <x-lucide-align-left class="w-3.5 h-3.5 text-amber-400 shrink-0 mt-0.5" />
                                                            @endif
                                                            <p class="text-xs lato-bold text-slate-600 dark:text-slate-300 leading-snug">
                                                                {{ $question->question }}
                                                            </p>
                                                        </div>

                                                        {{-- Resposta --}}
                                                        <div class="ml-5.5 pl-1">
                                                            @if (! $answer)
                                                                <span class="text-xs lato-regular text-slate-400 italic">
                                                                    Não respondida
                                                                </span>
                                                            @elseif ($question->type === 'escala')
                                                                @php
                                                                    $val = $answer->value_scale;
                                                                    $scaleColor = match(true) {
                                                                        $val >= 9  => 'bg-emerald-500 text-white',
                                                                        $val >= 7  => 'bg-amber-400 text-white',
                                                                        default    => 'bg-red-400 text-white',
                                                                    };
                                                                @endphp
                                                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-sm lato-black {{ $scaleColor }} shadow-sm">
                                                                    {{ $val }}
                                                                </span>
                                                            @elseif ($question->type === 'multipla_escolha')
                                                                <span class="inline-flex items-center gap-1.5 text-xs lato-bold px-2.5 py-1 rounded-full
                                                                             bg-emerald-50 text-emerald-700 border border-emerald-200
                                                                             dark:bg-emerald-900/20 dark:text-emerald-400 dark:border-emerald-700/40">
                                                                    <x-lucide-check class="w-3 h-3" />
                                                                    {{ $answer->value_option ?? '—' }}
                                                                </span>
                                                            @else
                                                                <p class="text-xs lato-regular text-slate-600 dark:text-slate-300
                                                                           bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                                                           rounded-lg px-3 py-2 leading-relaxed">
                                                                    {{ $answer->value_text ?: '—' }}
                                                                </p>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    @if (! $loop->last)
                                                        <div class="border-t border-slate-100 dark:border-slate-700/50 pt-1"></div>
                                                    @endif
                                                @endforeach
                                            @endif
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- ── Footer ───────────────────────────────────────────── --}}
                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700
                            bg-white dark:bg-slate-800 flex items-center justify-between gap-3">
                    <p class="text-xs text-slate-400 lato-regular flex items-center gap-1.5">
                        <x-lucide-clock class="w-3.5 h-3.5" />
                        @if ($this->responses->isNotEmpty())
                            Última resposta: {{ $this->responses->first()->completed_at->format('d/m/Y \à\s H:i') }}
                        @else
                            Nenhuma resposta recebida
                        @endif
                    </p>
                    <button wire:click="fecharModal"
                        class="px-4 py-2 text-sm lato-bold rounded-lg text-slate-600 bg-white border border-slate-200
                               hover:bg-slate-50 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200
                               dark:hover:bg-slate-600 transition cursor-pointer">
                        Fechar
                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
