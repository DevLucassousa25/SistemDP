{{-- Fundo com padrão sutil — sem min-h-screen pois o layout já controla o scroll via overflow-y-auto no <main> --}}
<div class="bg-slate-50 dark:bg-slate-950"
     style="background-image: radial-gradient(circle at 1px 1px, rgba(148,163,184,.12) 1px, transparent 0); background-size: 24px 24px;">

    {{-- ═══════════════════════════════════════════════════════════════
         ESTADO: SEM ACESSO
    ════════════════════════════════════════════════════════════════ --}}
    @if ($semAcesso)
        <div class="flex items-center justify-center min-h-[calc(100vh-64px)] px-4">
            <div class="max-w-md w-full text-center">
                <div class="relative inline-block mb-6">
                    <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-800 dark:to-slate-700 flex items-center justify-center mx-auto shadow-inner">
                        <x-lucide-lock class="w-10 h-10 text-slate-400 dark:text-slate-500" />
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 border-4 border-slate-50 dark:border-slate-950 flex items-center justify-center">
                        <x-lucide-x class="w-4 h-4 text-slate-500" />
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white lato-black">Acesso não disponível</h1>
                <p class="text-slate-500 dark:text-slate-400 lato-regular mt-3 text-sm leading-relaxed">{{ $semAcessoMotivo }}</p>
                <a href="{{ route('pesquisas') }}"
                   class="mt-7 inline-flex items-center gap-2 px-5 py-2.5 text-sm lato-bold rounded-xl
                          bg-slate-800 dark:bg-slate-700 hover:bg-slate-900 dark:hover:bg-slate-600 text-white transition shadow-sm">
                    <x-lucide-arrow-left class="w-4 h-4" />
                    Voltar às pesquisas
                </a>
            </div>
        </div>

    {{-- ═══════════════════════════════════════════════════════════════
         ESTADO: JÁ RESPONDEU
    ════════════════════════════════════════════════════════════════ --}}
    @elseif ($jaRespondeu)
        <div class="flex items-center justify-center min-h-[calc(100vh-64px)] px-4">
            <div class="max-w-md w-full text-center">
                <div class="relative inline-block mb-6">
                    <div class="w-24 h-24 rounded-3xl bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center mx-auto shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40">
                        <x-lucide-check-circle class="w-12 h-12 text-white" />
                    </div>
                    <div class="absolute -bottom-1 -right-1 w-8 h-8 rounded-full bg-emerald-500 border-4 border-slate-50 dark:border-slate-950 flex items-center justify-center">
                        <x-lucide-star class="w-3.5 h-3.5 text-white fill-white" />
                    </div>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white lato-black">Você já participou!</h1>
                <p class="text-slate-500 dark:text-slate-400 lato-regular mt-3 text-sm leading-relaxed">
                    Sua resposta já foi registrada. Agradecemos a sua colaboração!
                </p>
                <div class="mt-5 p-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 text-left">
                    <p class="text-[11px] lato-bold text-slate-400 uppercase tracking-widest mb-1">Pesquisa</p>
                    <p class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">{{ $this->survey->title }}</p>
                </div>
                <a href="{{ route('pesquisas') }}"
                   class="mt-5 inline-flex items-center gap-2 px-5 py-2.5 text-sm lato-bold rounded-xl
                          bg-emerald-500 hover:bg-emerald-600 text-white transition shadow-sm shadow-emerald-200 dark:shadow-emerald-900/30">
                    <x-lucide-arrow-left class="w-4 h-4" />
                    Voltar às pesquisas
                </a>
            </div>
        </div>

    {{-- ═══════════════════════════════════════════════════════════════
         ESTADO: ENVIADO COM SUCESSO
    ════════════════════════════════════════════════════════════════ --}}
    @elseif ($submitted)
        <div class="flex items-center justify-center min-h-[calc(100vh-64px)] px-4"
             x-data x-init="
                $el.querySelectorAll('[data-anim]').forEach((el, i) => {
                    el.style.opacity = 0;
                    el.style.transform = 'translateY(20px)';
                    setTimeout(() => {
                        el.style.transition = 'opacity .5s ease, transform .5s ease';
                        el.style.opacity = 1;
                        el.style.transform = 'translateY(0)';
                    }, i * 130);
                })
             ">
            <div class="max-w-md w-full text-center">

                {{-- Círculo principal --}}
                <div data-anim class="relative inline-flex items-center justify-center mb-6">
                    {{-- Anéis decorativos --}}
                    <div class="absolute w-36 h-36 rounded-full border-2 border-emerald-200/60 dark:border-emerald-800/40 animate-ping" style="animation-duration:2.5s"></div>
                    <div class="absolute w-28 h-28 rounded-full border-2 border-emerald-300/50 dark:border-emerald-700/40"></div>
                    <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-xl shadow-emerald-300/50 dark:shadow-emerald-900/50">
                        <x-lucide-check class="w-10 h-10 text-white" stroke-width="3" />
                    </div>
                </div>

                <h1 data-anim class="text-2xl sm:text-3xl font-bold text-slate-800 dark:text-white lato-black leading-tight">
                    Obrigado pelo seu<br>feedback!
                </h1>
                <p data-anim class="text-slate-500 dark:text-slate-400 lato-regular mt-3 text-sm leading-relaxed max-w-xs mx-auto">
                    Sua participação é muito importante para continuarmos melhorando.
                </p>

                {{-- Card da pesquisa --}}
                <div data-anim class="mt-6 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-4 text-left">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shrink-0">
                            <x-lucide-clipboard-list class="w-5 h-5 text-white" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] lato-bold text-slate-400 uppercase tracking-widest">Pesquisa respondida</p>
                            <p class="text-sm font-bold text-slate-800 dark:text-white lato-bold truncate mt-0.5">{{ $this->survey->title }}</p>
                        </div>
                        <x-lucide-check-circle class="w-5 h-5 text-emerald-500 shrink-0" />
                    </div>
                    @if ($this->survey->is_anonymous)
                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center gap-2">
                            <x-lucide-shield class="w-3.5 h-3.5 text-emerald-500 shrink-0" />
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">Sua identidade foi mantida em sigilo.</p>
                        </div>
                    @endif
                </div>

                <div data-anim class="mt-4 flex gap-2 justify-center">
                    <a href="{{ route('pesquisas') }}"
                       class="inline-flex items-center gap-2 px-5 py-2.5 text-sm lato-bold rounded-xl
                              bg-emerald-500 hover:bg-emerald-600 text-white transition shadow-md shadow-emerald-200 dark:shadow-emerald-900/30">
                        <x-lucide-arrow-left class="w-4 h-4" />
                        Voltar às pesquisas
                    </a>
                </div>
            </div>
        </div>

    {{-- ═══════════════════════════════════════════════════════════════
         ESTADO: FORMULÁRIO DE RESPOSTA
    ════════════════════════════════════════════════════════════════ --}}
    @else
        @php
            $survey    = $this->survey;
            $perguntas = $survey->questions;
            $progresso = $this->progresso;
        @endphp

        {{-- ── Barra de progresso sticky ──────────────────────────────── --}}
        <div class="sticky top-0 z-30 bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-b border-slate-200/60 dark:border-slate-800">
            <div class="max-w-2xl mx-auto px-4 sm:px-6 py-2.5 flex items-center gap-3">
                <a href="{{ route('pesquisas') }}"
                   class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                    <x-lucide-arrow-left class="w-4 h-4" />
                </a>
                <div class="flex-1 min-w-0">
                    <div class="flex items-center justify-between gap-2 mb-1">
                        <p class="text-xs lato-bold text-slate-600 dark:text-slate-300 truncate">{{ $survey->title }}</p>
                        <span class="text-xs lato-bold text-emerald-600 dark:text-emerald-400 shrink-0">
                            {{ $progresso['respondidas'] }}/{{ $progresso['total'] }}
                        </span>
                    </div>
                    <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                        <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 rounded-full transition-all duration-700 ease-out"
                             style="width: {{ $progresso['percent'] }}%"></div>
                    </div>
                </div>
                <span class="shrink-0 text-[11px] lato-bold text-slate-400 dark:text-slate-500">
                    {{ $progresso['percent'] }}%
                </span>
            </div>
        </div>

        <div class="max-w-2xl mx-auto px-4 sm:px-6 py-6 space-y-4">

            {{-- ── Hero da pesquisa ────────────────────────────────────── --}}
            <div class="relative overflow-hidden rounded-2xl">
                {{-- Gradiente de fundo --}}
                <div class="absolute inset-0 bg-gradient-to-br from-emerald-600 via-emerald-500 to-teal-500"></div>
                {{-- Círculos decorativos --}}
                <div class="absolute -top-8 -right-8 w-40 h-40 rounded-full bg-white/10"></div>
                <div class="absolute -bottom-12 -left-6 w-48 h-48 rounded-full bg-white/5"></div>
                <div class="absolute top-4 right-16 w-6 h-6 rounded-full bg-white/20"></div>

                <div class="relative p-5 sm:p-7">
                    <div class="flex items-start gap-4">
                        <div class="shrink-0 w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-white/20 backdrop-blur-sm
                                    flex items-center justify-center border border-white/30 shadow-sm">
                            <x-lucide-clipboard-list class="w-6 h-6 sm:w-7 sm:h-7 text-white" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h1 class="text-lg sm:text-xl font-bold text-white lato-black leading-snug">
                                {{ $survey->title }}
                            </h1>
                            @if ($survey->description)
                                <p class="text-sm text-emerald-100/80 lato-regular mt-1.5 leading-relaxed">
                                    {{ $survey->description }}
                                </p>
                            @endif
                        </div>
                    </div>

                    {{-- Meta badges --}}
                    <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-white/20">
                        @if ($survey->is_anonymous)
                            <span class="inline-flex items-center gap-1.5 text-[11px] lato-bold
                                         px-2.5 py-1 rounded-full bg-white/20 text-white border border-white/30 backdrop-blur-sm">
                                <x-lucide-shield class="w-3 h-3" />
                                Anônima
                            </span>
                        @endif
                        @if ($survey->end_date)
                            <span class="inline-flex items-center gap-1.5 text-[11px] lato-bold
                                         px-2.5 py-1 rounded-full bg-white/20 text-white border border-white/30 backdrop-blur-sm">
                                <x-lucide-calendar class="w-3 h-3" />
                                Até {{ $survey->end_date->format('d/m/Y') }}
                            </span>
                        @endif
                        <span class="inline-flex items-center gap-1.5 text-[11px] lato-bold
                                     px-2.5 py-1 rounded-full bg-white/20 text-white border border-white/30 backdrop-blur-sm">
                            <x-lucide-help-circle class="w-3 h-3" />
                            {{ $perguntas->count() }} {{ $perguntas->count() === 1 ? 'pergunta' : 'perguntas' }}
                        </span>
                        @php
                            $obrigatorias = $perguntas->where('required', true)->count();
                        @endphp
                        @if ($obrigatorias > 0)
                            <span class="inline-flex items-center gap-1.5 text-[11px] lato-bold
                                         px-2.5 py-1 rounded-full bg-white/20 text-white border border-white/30 backdrop-blur-sm">
                                <x-lucide-asterisk class="w-3 h-3" />
                                {{ $obrigatorias }} obrigatória{{ $obrigatorias > 1 ? 's' : '' }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Perguntas ───────────────────────────────────────────── --}}
            @foreach ($perguntas as $i => $pergunta)
                @php
                    $resposta    = $answers[$pergunta->id] ?? null;
                    $temResposta = $resposta !== null && $resposta !== '';

                    $accentColor = match ($pergunta->type) {
                        'escala'           => ['ring' => 'ring-blue-400/30',    'dot' => 'bg-blue-400',   'badge' => 'bg-blue-50 text-blue-700 border-blue-200 dark:bg-blue-900/20 dark:text-blue-300 dark:border-blue-800/30'],
                        'multipla_escolha' => ['ring' => 'ring-indigo-400/30',  'dot' => 'bg-violet-400', 'badge' => 'bg-violet-50 text-violet-700 border-violet-200 dark:bg-violet-900/20 dark:text-violet-300 dark:border-violet-800/30'],
                        'texto_livre'      => ['ring' => 'ring-amber-400/30',   'dot' => 'bg-amber-400',  'badge' => 'bg-amber-50 text-amber-700 border-amber-200 dark:bg-amber-900/20 dark:text-amber-300 dark:border-amber-800/30'],
                        default            => ['ring' => 'ring-slate-400/30',   'dot' => 'bg-slate-400',  'badge' => 'bg-slate-100 text-slate-600 border-slate-200'],
                    };
                    $typeIcon = match ($pergunta->type) {
                        'escala'           => 'lucide-bar-chart-2',
                        'multipla_escolha' => 'lucide-list-checks',
                        'texto_livre'      => 'lucide-align-left',
                        default            => 'lucide-circle-help',
                    };
                @endphp

                <div wire:key="pergunta-{{ $pergunta->id }}"
                     id="q{{ $pergunta->id }}"
                     class="group bg-white dark:bg-slate-800/90 rounded-2xl border transition-all duration-300
                            {{ $temResposta
                               ? 'border-emerald-200 dark:border-emerald-700/50 ring-1 ring-emerald-400/20'
                               : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600' }}">

                    {{-- Topo do card --}}
                    <div class="flex items-center gap-3 px-4 sm:px-5 pt-4 pb-3 border-b
                                {{ $temResposta ? 'border-emerald-100 dark:border-emerald-800/30' : 'border-slate-100 dark:border-slate-700' }}">

                        {{-- Número com estado --}}
                        <div class="shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-xs lato-black transition-colors duration-300
                                    {{ $temResposta
                                       ? 'bg-emerald-500 text-white shadow-sm shadow-emerald-200 dark:shadow-emerald-900/30'
                                       : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-300' }}">
                            @if ($temResposta)
                                <x-lucide-check class="w-4 h-4" stroke-width="3" />
                            @else
                                {{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}
                            @endif
                        </div>

                        {{-- Badge do tipo --}}
                        <span class="inline-flex items-center gap-1.5 text-[11px] lato-bold
                                     px-2.5 py-1 rounded-full border {{ $accentColor['badge'] }}">
                            <x-dynamic-component :component="$typeIcon" class="w-3 h-3" />
                            {{ $pergunta->type_label }}
                        </span>

                        {{-- Badge: pergunta avalia o gestor do setor --}}
                        @if ($pergunta->is_manager_evaluation)
                            <span class="inline-flex items-center gap-1 text-[11px] lato-bold
                                         px-2 py-0.5 rounded-full border
                                         bg-violet-50 text-violet-700 border-violet-200
                                         dark:bg-violet-900/20 dark:text-violet-300 dark:border-violet-700/40"
                                  title="Esta pergunta é usada para avaliar o seu gestor">
                                <x-lucide-user-check class="w-3 h-3" />
                                Avaliação do Gestor
                            </span>
                        @endif

                        @if ($pergunta->required)
                            <span class="text-[11px] lato-bold text-red-400 flex items-center gap-0.5">
                                <x-lucide-asterisk class="w-2.5 h-2.5" />
                                Obrigatória
                            </span>
                        @endif
                    </div>

                    {{-- Corpo do card --}}
                    <div class="px-4 sm:px-5 py-4">

                        {{-- Texto da pergunta --}}
                        <p class="text-sm sm:text-[15px] font-semibold text-slate-800 dark:text-slate-100 lato-bold leading-relaxed mb-5">
                            {{ $pergunta->question }}
                            @if ($pergunta->required)
                                <span class="text-red-400">*</span>
                            @endif
                        </p>

                        {{-- ════════════════════════════════════
                             INPUT: ESCALA 0 – 10
                        ════════════════════════════════════ --}}
                        @if ($pergunta->type === 'escala')
                            <div class="space-y-4">

                                {{-- Track visual com gradiente --}}
                                <div class="relative">
                                    {{-- Barra gradiente decorativa --}}
                                    <div class="h-2 rounded-full mb-3"
                                         style="background: linear-gradient(to right, #ef4444, #f97316, #eab308, #22c55e, #10b981)"></div>

                                    {{-- Botões de número --}}
                                    <div class="grid grid-cols-11 gap-1">
                                        @for ($n = 0; $n <= 10; $n++)
                                            @php
                                                $sel = $resposta === $n;
                                                // Cor baseada na faixa
                                                $btnClass = match(true) {
                                                    $n <= 4  => $sel
                                                        ? 'bg-red-500 border-red-500 text-white shadow-lg shadow-red-200 dark:shadow-red-900/40'
                                                        : 'border-red-200 dark:border-red-900/50 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 hover:border-red-300',
                                                    $n <= 6  => $sel
                                                        ? 'bg-orange-500 border-orange-500 text-white shadow-lg shadow-orange-200 dark:shadow-orange-900/40'
                                                        : 'border-orange-200 dark:border-orange-900/50 text-orange-500 dark:text-orange-400 hover:bg-orange-50 dark:hover:bg-orange-900/20 hover:border-orange-300',
                                                    $n <= 8  => $sel
                                                        ? 'bg-amber-500 border-amber-500 text-white shadow-lg shadow-amber-200 dark:shadow-amber-900/40'
                                                        : 'border-amber-200 dark:border-amber-900/50 text-amber-500 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 hover:border-amber-300',
                                                    default  => $sel
                                                        ? 'bg-emerald-500 border-emerald-500 text-white shadow-lg shadow-emerald-200 dark:shadow-emerald-900/40'
                                                        : 'border-emerald-200 dark:border-emerald-900/50 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:border-emerald-300',
                                                };
                                            @endphp
                                            <button type="button"
                                                wire:click="selecionarEscala({{ $pergunta->id }}, {{ $n }})"
                                                class="aspect-square flex items-center justify-center rounded-xl border-2 text-sm font-bold lato-black
                                                       transition-all duration-150 cursor-pointer select-none
                                                       {{ $sel ? '-translate-y-1' : '' }} {{ $btnClass }}">
                                                {{ $n }}
                                            </button>
                                        @endfor
                                    </div>
                                </div>

                                {{-- Legenda --}}
                                <div class="flex justify-between text-[11px] lato-regular text-slate-400 dark:text-slate-500 px-0.5">
                                    <span class="flex items-center gap-1.5">
                                        <span class="w-2 h-2 rounded-full bg-red-400 shrink-0"></span>
                                        Muito insatisfeito
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        Muito satisfeito
                                        <span class="w-2 h-2 rounded-full bg-emerald-400 shrink-0"></span>
                                    </span>
                                </div>

                                {{-- Feedback da seleção --}}
                                @if ($resposta !== null)
                                    @php
                                        $emoji  = match(true) { $resposta <= 4 => '😞', $resposta <= 6 => '😐', $resposta <= 8 => '🙂', default => '😄' };
                                        $label  = match(true) { $resposta <= 4 => 'Muito insatisfeito', $resposta <= 6 => 'Insatisfeito', $resposta <= 8 => 'Satisfeito', default => 'Muito satisfeito' };
                                        $fgClass = match(true) { $resposta <= 4 => 'text-red-600 bg-red-50 border-red-100 dark:bg-red-900/20 dark:border-red-900/30 dark:text-red-300', $resposta <= 6 => 'text-orange-600 bg-orange-50 border-orange-100 dark:bg-orange-900/20 dark:border-orange-900/30 dark:text-orange-300', $resposta <= 8 => 'text-amber-600 bg-amber-50 border-amber-100 dark:bg-amber-900/20 dark:border-amber-900/30 dark:text-amber-300', default => 'text-emerald-600 bg-emerald-50 border-emerald-100 dark:bg-emerald-900/20 dark:border-emerald-900/30 dark:text-emerald-300' };
                                    @endphp
                                    <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl border {{ $fgClass }} text-sm lato-bold">
                                        <span class="text-lg leading-none">{{ $emoji }}</span>
                                        <span>Nota <strong>{{ $resposta }}</strong> — {{ $label }}</span>
                                    </div>
                                @endif
                            </div>

                        {{-- ════════════════════════════════════
                             INPUT: MÚLTIPLA ESCOLHA
                        ════════════════════════════════════ --}}
                        @elseif ($pergunta->type === 'multipla_escolha')
                            <div class="space-y-2">
                                @foreach ($pergunta->options ?? [] as $idx => $opcao)
                                    @php $sel = $resposta === $opcao; @endphp

                                    {{--
                                        NÃO usar wire:model.live aqui.
                                        O radio com sr-only faz o browser "scrollar até ele" ao ser ativado,
                                        derrubando o scroll do <main>. Em vez disso usamos Alpine para
                                        capturar o clique, salvar a posição de scroll, chamar o método Livewire
                                        e restaurar o scroll após a re-renderização.
                                    --}}
                                    <div wire:key="opcao-{{ $pergunta->id }}-{{ $idx }}"
                                         x-data="{ opcao: {{ Js::from($opcao) }}, qId: {{ $pergunta->id }} }"
                                         x-on:click.prevent="
                                             const main = document.querySelector('main');
                                             const y = main ? main.scrollTop : window.scrollY;
                                             $wire.selecionarOpcao(qId, opcao).then(() => {
                                                 $nextTick(() => { if (main) main.scrollTop = y; else window.scrollTo(0, y); });
                                             });
                                         "
                                         class="flex items-center gap-3.5 px-4 py-3.5 rounded-xl border-2 cursor-pointer
                                                transition-all duration-150 select-none
                                                {{ $sel
                                                   ? 'border-indigo-400 bg-violet-50 dark:bg-violet-900/20 dark:border-indigo-500 shadow-sm shadow-violet-100 dark:shadow-indigo-500/20'
                                                   : 'border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-700/30 hover:border-violet-200 dark:hover:border-violet-800/60 hover:bg-violet-50/40 dark:hover:bg-violet-900/10' }}">

                                        {{-- Radio visual (decorativo, sem binding Livewire) --}}
                                        <div class="w-5 h-5 rounded-full border-2 shrink-0 flex items-center justify-center transition-all duration-150
                                                    {{ $sel ? 'border-indigo-500 bg-indigo-600' : 'border-slate-300 dark:border-slate-500 bg-white dark:bg-slate-700' }}">
                                            @if ($sel)
                                                <div class="w-2 h-2 rounded-full bg-white"></div>
                                            @endif
                                        </div>

                                        {{-- Letra da opção --}}
                                        <span class="shrink-0 w-6 h-6 rounded-md flex items-center justify-center text-[11px] lato-black
                                                     {{ $sel ? 'bg-violet-400 text-white' : 'bg-slate-200 dark:bg-slate-600 text-slate-500 dark:text-slate-300' }}">
                                            {{ chr(65 + $idx) }}
                                        </span>

                                        <span class="text-sm flex-1 lato-{{ $sel ? 'bold' : 'regular' }}
                                                     {{ $sel ? 'text-violet-800 dark:text-violet-200' : 'text-slate-700 dark:text-slate-200' }}">
                                            {{ $opcao }}
                                        </span>

                                        @if ($sel)
                                            <x-lucide-check-circle class="w-4 h-4 text-indigo-500 shrink-0" />
                                        @endif
                                    </div>
                                @endforeach
                            </div>

                        {{-- ════════════════════════════════════
                             INPUT: TEXTO LIVRE
                        ════════════════════════════════════ --}}
                        @elseif ($pergunta->type === 'texto_livre')
                            <div>
                                <div class="relative">
                                    <textarea
                                        wire:model.live="answers.{{ $pergunta->id }}"
                                        rows="5"
                                        placeholder="Escreva sua resposta aqui..."
                                        maxlength="2000"
                                        class="w-full px-4 py-3.5 text-sm lato-regular rounded-xl resize-none
                                               bg-slate-50 dark:bg-slate-700/50 text-slate-800 dark:text-slate-100
                                               placeholder-slate-400 dark:placeholder-slate-500 border-2 transition-all duration-150
                                               focus:outline-none focus:ring-4
                                               {{ $temResposta
                                                  ? 'border-amber-300 dark:border-amber-600 focus:border-amber-400 focus:ring-amber-400/20'
                                                  : 'border-slate-200 dark:border-slate-600 focus:border-amber-400 focus:ring-amber-400/20' }}"></textarea>

                                    {{-- Ícone decorativo --}}
                                    @if (! $temResposta)
                                        <div class="absolute bottom-3 right-3 text-slate-300 dark:text-slate-600 pointer-events-none">
                                            <x-lucide-pencil class="w-4 h-4" />
                                        </div>
                                    @else
                                        <div class="absolute bottom-3 right-3 text-amber-400 pointer-events-none">
                                            <x-lucide-check class="w-4 h-4" stroke-width="2.5" />
                                        </div>
                                    @endif
                                </div>

                                {{-- Contador de caracteres --}}
                                @php $charCount = mb_strlen($answers[$pergunta->id] ?? ''); @endphp
                                <div class="flex items-center justify-between mt-2 px-1">
                                    @if ($temResposta)
                                        <span class="text-[11px] lato-regular text-amber-500 flex items-center gap-1">
                                            <x-lucide-check class="w-3 h-3" />
                                            Resposta registrada
                                        </span>
                                    @else
                                        <span class="text-[11px] lato-regular text-slate-400">Mínimo recomendado: 10 caracteres</span>
                                    @endif
                                    <span class="text-[11px] lato-bold {{ $charCount > 1800 ? 'text-orange-500' : 'text-slate-400' }}">
                                        {{ $charCount }}/2000
                                    </span>
                                </div>
                            </div>
                        @endif

                    </div>
                </div>
            @endforeach

            {{-- ── Rodapé de envio ─────────────────────────────────────── --}}
            <div class="bg-white dark:bg-slate-800/90 rounded-2xl border border-slate-200 dark:border-slate-700
                        shadow-sm overflow-hidden">

                {{-- Barra de progresso interna --}}
                <div class="h-1 bg-slate-100 dark:bg-slate-700">
                    <div class="h-full bg-gradient-to-r from-emerald-500 to-teal-400 transition-all duration-700"
                         style="width: {{ $progresso['percent'] }}%"></div>
                </div>

                <div class="p-4 sm:p-5 flex flex-col sm:flex-row items-center justify-between gap-4">
                    {{-- Info --}}
                    <div class="flex items-center gap-4 w-full sm:w-auto">
                        <div class="text-center">
                            <p class="text-xl lato-black font-bold text-slate-800 dark:text-white leading-none">
                                {{ $progresso['respondidas'] }}/{{ $progresso['total'] }}
                            </p>
                            <p class="text-[11px] lato-regular text-slate-400 mt-0.5">respondidas</p>
                        </div>
                        <div class="w-px h-10 bg-slate-100 dark:bg-slate-700 shrink-0"></div>
                        @php $faltam = $perguntas->where('required', true)->filter(fn($q) => ($answers[$q->id] ?? null) === null || ($answers[$q->id] ?? '') === '')->count(); @endphp
                        @if ($faltam > 0)
                            <div>
                                <p class="text-sm lato-bold text-red-500">{{ $faltam }} obrigatória{{ $faltam > 1 ? 's' : '' }} pendente{{ $faltam > 1 ? 's' : '' }}</p>
                                <p class="text-[11px] lato-regular text-slate-400 mt-0.5">Preencha para enviar</p>
                            </div>
                        @else
                            <div>
                                <p class="text-sm lato-bold text-emerald-600 dark:text-emerald-400">Tudo pronto!</p>
                                <p class="text-[11px] lato-regular text-slate-400 mt-0.5">Pode enviar sua resposta</p>
                            </div>
                        @endif
                    </div>

                    {{-- Ações --}}
                    <div class="flex items-center gap-2 w-full sm:w-auto">
                        <a href="{{ route('pesquisas') }}"
                           class="flex-1 sm:flex-none px-4 py-2.5 text-sm lato-bold rounded-xl text-slate-600 dark:text-slate-300
                                  bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition text-center">
                            Cancelar
                        </a>

                        <button wire:click="enviar"
                            wire:loading.attr="disabled"
                            wire:target="enviar"
                            @if (! $this->podeEnviar) disabled @endif
                            class="flex-1 sm:flex-none px-6 py-2.5 text-sm lato-bold rounded-xl text-white
                                   flex items-center justify-center gap-2 transition-all duration-200
                                   {{ $this->podeEnviar
                                      ? 'bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20 cursor-pointer hover:-translate-y-0.5 active:translate-y-0'
                                      : 'bg-slate-300 dark:bg-slate-600 cursor-not-allowed opacity-60' }}">
                            <svg wire:loading wire:target="enviar"
                                 class="w-4 h-4 animate-spin"
                                 xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="enviar" class="flex items-center gap-2">
                                <x-lucide-send class="w-4 h-4" />
                                Enviar respostas
                            </span>
                            <span wire:loading wire:target="enviar">Enviando...</span>
                        </button>
                    </div>
                </div>
            </div>

            <div class="h-8"></div>
        </div>
    @endif

</div>
