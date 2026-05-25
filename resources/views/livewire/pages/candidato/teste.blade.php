<div class="min-h-screen bg-slate-50 dark:bg-slate-900 flex flex-col items-center justify-start py-10 px-4">

    {{-- ── INVÁLIDO ──────────────────────────────────────────────────────────── --}}
    @if ($tela === 'invalido')
        <div class="w-full max-w-md mt-16 text-center">
            <div class="w-20 h-20 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center mx-auto mb-6">
                <x-lucide-link-2-off class="w-9 h-9 text-rose-500" />
            </div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Link inválido</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">Este link de teste não existe ou foi removido.</p>
        </div>

    {{-- ── EXPIRADO ───────────────────────────────────────────────────────────── --}}
    @elseif ($tela === 'expirado')
        <div class="w-full max-w-md mt-16 text-center">
            <div class="w-20 h-20 rounded-full bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-6">
                <x-lucide-alarm-clock-off class="w-9 h-9 text-amber-500" />
            </div>
            <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Link expirado</h1>
            <p class="text-slate-500 dark:text-slate-400 text-sm">
                O prazo para realizar este teste já passou.<br>
                Entre em contato com o recrutador para solicitar um novo link.
            </p>
        </div>

    {{-- ── BOAS-VINDAS ────────────────────────────────────────────────────────── --}}
    @elseif ($tela === 'boas_vindas')
        @php
            $teste    = $candidatoTeste->teste;
            $curriculo = $candidatoTeste->curriculo;
            $totalQ   = $teste->questoes->count();
            $tempo    = $teste->tempo_limite_minutos;
        @endphp

        <div class="w-full max-w-lg">

            {{-- Logo / marca --}}
            <div class="text-center mb-8">
                <span class="text-xl font-bold text-indigo-600 dark:text-indigo-400 tracking-tight">{{ config('app.name') }}</span>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                {{-- Header colorido --}}
                <div class="bg-gradient-to-r from-indigo-500 to-indigo-700 px-8 py-8 text-white text-center">
                    <div class="w-16 h-16 rounded-full bg-white/20 flex items-center justify-center mx-auto mb-4">
                        <x-lucide-file-text class="w-8 h-8 text-white" />
                    </div>
                    <h1 class="text-xl font-bold mb-1">{{ $teste->titulo }}</h1>
                    @if ($curriculo)
                        <p class="text-indigo-200 text-sm">Olá, <strong class="text-white">{{ $curriculo->nome }}</strong>! Você foi convidado(a) para este teste.</p>
                    @endif
                </div>

                {{-- Infos do teste --}}
                <div class="px-8 py-6">

                    @if ($teste->descricao)
                        <p class="text-slate-600 dark:text-slate-300 text-sm mb-6 text-center">{{ $teste->descricao }}</p>
                    @endif

                    {{-- Detalhes --}}
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 text-center">
                            <x-lucide-list-checks class="w-5 h-5 text-indigo-500 mx-auto mb-1" />
                            <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $totalQ }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400">{{ Str::plural('questão', $totalQ) }}</p>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-900 rounded-xl p-4 text-center">
                            <x-lucide-clock class="w-5 h-5 text-indigo-500 mx-auto mb-1" />
                            @if ($tempo)
                                <p class="text-2xl font-bold text-slate-800 dark:text-white">{{ $tempo }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">minutos</p>
                            @else
                                <p class="text-2xl font-bold text-slate-800 dark:text-white">—</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">sem limite</p>
                            @endif
                        </div>
                    </div>

                    {{-- Expiração --}}
                    @if ($candidatoTeste->expira_at)
                        <div class="flex items-center gap-2 bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 rounded-xl px-4 py-3 mb-6">
                            <x-lucide-calendar-x-2 class="w-4 h-4 text-amber-500 shrink-0" />
                            <p class="text-xs text-amber-700 dark:text-amber-400">
                                Este link expira em <strong>{{ $candidatoTeste->expira_at->format('d/m/Y \à\s H:i') }}</strong>.
                            </p>
                        </div>
                    @endif

                    {{-- Instruções --}}
                    @if ($teste->instrucoes)
                        <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800 rounded-xl px-4 py-3 mb-6">
                            <p class="text-xs font-semibold text-blue-700 dark:text-blue-400 mb-1 uppercase tracking-wide">Instruções</p>
                            <p class="text-sm text-blue-800 dark:text-blue-300 whitespace-pre-line">{{ $teste->instrucoes }}</p>
                        </div>
                    @endif

                    <button wire:click="iniciar" wire:loading.attr="disabled"
                            class="cursor-pointer w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition disabled:opacity-60 flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="iniciar">
                            <x-lucide-play class="w-4 h-4 inline mr-1" />
                            Iniciar teste
                        </span>
                        <span wire:loading wire:target="iniciar" class="flex items-center gap-2">
                            <x-lucide-loader-circle class="w-4 h-4 animate-spin" />
                            Iniciando…
                        </span>
                    </button>

                </div>
            </div>
        </div>

    {{-- ── EM ANDAMENTO ───────────────────────────────────────────────────────── --}}
    @elseif ($tela === 'em_andamento')
        @php
            $teste   = $candidatoTeste->teste;
            $questoes = $teste->randomizar_questoes
                ? $teste->questoes->shuffle()
                : $teste->questoes;
            $totalQ  = $questoes->count();
        @endphp

        <div class="w-full max-w-2xl">

            {{-- Header fixo --}}
            <div class="mb-6 flex items-center justify-between">
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-wide">Teste</p>
                    <h1 class="text-lg font-bold text-slate-800 dark:text-white">{{ $teste->titulo }}</h1>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $totalQ }} questões</p>
                    @if ($candidatoTeste->expira_at)
                        <p class="text-xs text-amber-500">Expira {{ $candidatoTeste->expira_at->diffForHumans() }}</p>
                    @endif
                </div>
            </div>

            {{-- Questões --}}
            <div class="space-y-6">
                @foreach ($questoes as $idx => $questao)
                    @php
                        $opcoes = $teste->randomizar_opcoes
                            ? $questao->opcoes->shuffle()
                            : $questao->opcoes;
                    @endphp

                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-sm p-6">

                        {{-- Número + enunciado --}}
                        <div class="flex gap-3 mb-5">
                            <span class="shrink-0 w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 text-xs font-bold flex items-center justify-center">
                                {{ $idx + 1 }}
                            </span>
                            <p class="text-slate-800 dark:text-slate-100 text-sm font-medium leading-relaxed pt-0.5">
                                {{ $questao->enunciado }}
                            </p>
                        </div>

                        {{-- Múltipla escolha --}}
                        @if ($questao->tipo === 'objetiva')
                            <div class="space-y-2 pl-10">
                                @foreach ($opcoes as $opcao)
                                    <label class="flex items-start gap-3 cursor-pointer group">
                                        <input type="radio"
                                               name="questao_{{ $questao->id }}"
                                               value="{{ $opcao->id }}"
                                               wire:model="respostas.{{ $questao->id }}"
                                               class="mt-0.5 accent-indigo-600 cursor-pointer" />
                                        <span class="text-sm text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white transition">
                                            {{ $opcao->texto }}
                                        </span>
                                    </label>
                                @endforeach
                            </div>

                        {{-- Discursiva --}}
                        @elseif ($questao->tipo === 'discursiva')
                            <div class="pl-10">
                                <textarea
                                    wire:model="respostas.{{ $questao->id }}"
                                    rows="4"
                                    placeholder="Escreva sua resposta aqui…"
                                    class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm px-4 py-3 resize-none focus:outline-none focus:ring-2 focus:ring-indigo-400 placeholder-slate-400 dark:placeholder-slate-500 transition">
                                </textarea>
                            </div>
                        @endif

                    </div>
                @endforeach
            </div>

            {{-- Botão enviar --}}
            <div class="mt-8 pb-10"
                 x-data="{ confirming: false }">

                <div x-show="!confirming">
                    <button type="button" x-on:click="confirming = true"
                            class="cursor-pointer w-full py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-sm transition flex items-center justify-center gap-2">
                        <x-lucide-send class="w-4 h-4" />
                        Enviar respostas
                    </button>
                </div>

                <div x-show="confirming" x-cloak
                     class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 text-center shadow-sm">
                    <x-lucide-alert-triangle class="w-8 h-8 text-amber-500 mx-auto mb-3" />
                    <p class="text-slate-800 dark:text-slate-100 font-semibold mb-1">Tem certeza?</p>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mb-5">
                        Após enviar, não será possível alterar suas respostas.
                    </p>
                    <div class="flex gap-3">
                        <button type="button" x-on:click="confirming = false"
                                class="cursor-pointer flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                            Revisar
                        </button>
                        <button wire:click="concluir" wire:loading.attr="disabled" wire:target="concluir"
                                class="cursor-pointer flex-1 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold transition disabled:opacity-60">
                            <span wire:loading.remove wire:target="concluir">Confirmar envio</span>
                            <span wire:loading wire:target="concluir" class="flex items-center justify-center gap-2">
                                <x-lucide-loader-circle class="w-4 h-4 animate-spin" />
                                Enviando…
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>

    {{-- ── CONCLUÍDO ──────────────────────────────────────────────────────────── --}}
    @elseif ($tela === 'concluido')
        @php
            $ct       = $candidatoTeste;
            $aprovado = $ct->aprovado;
            $nota     = $ct->nota;
            $temDiscursiva = $ct->teste->questoes->where('tipo', 'discursiva')->count() > 0;
        @endphp

        <div class="w-full max-w-md mt-10 text-center">

            @if ($temDiscursiva && $aprovado === null)
                {{-- Tem discursiva: aguardando revisão --}}
                <div class="w-20 h-20 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-6">
                    <x-lucide-clock class="w-9 h-9 text-blue-500" />
                </div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Respostas enviadas!</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">
                    Suas respostas foram registradas com sucesso.<br>
                    Este teste contém questões discursivas e será avaliado pelo recrutador em breve.
                </p>

            @elseif ($aprovado)
                {{-- Aprovado --}}
                <div class="w-20 h-20 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mx-auto mb-6">
                    <x-lucide-circle-check-big class="w-9 h-9 text-emerald-500" />
                </div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Parabéns!</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">
                    Você concluiu o teste com sucesso.
                </p>

            @else
                {{-- Não aprovado --}}
                <div class="w-20 h-20 rounded-full bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center mx-auto mb-6">
                    <x-lucide-circle-x class="w-9 h-9 text-rose-500" />
                </div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Teste concluído</h1>
                <p class="text-slate-500 dark:text-slate-400 text-sm mb-6">
                    Obrigado por participar. O recrutador analisará seu resultado.
                </p>
            @endif

            {{-- Card nota (só se tiver nota e não for apenas discursiva) --}}
            @if ($nota !== null && !($temDiscursiva && $aprovado === null))
                @php $notaAprovacao = $ct->teste->nota_aprovacao ?? 60; @endphp
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 shadow-sm mb-6">
                    <p class="text-xs text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-1">Sua nota</p>
                    <p class="text-5xl font-black {{ $aprovado ? 'text-emerald-500' : 'text-rose-500' }}">
                        {{ number_format($nota, 1, ',', '.') }}
                    </p>
                    <p class="text-slate-400 dark:text-slate-500 text-sm">/ 100 &nbsp;·&nbsp; Mínimo: {{ $notaAprovacao }}</p>
                </div>
            @endif

            <div class="bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-5 py-4 text-left">
                <p class="text-xs text-slate-400 dark:text-slate-500 mb-3 uppercase tracking-wide">Detalhes</p>
                <div class="space-y-2 text-sm text-slate-600 dark:text-slate-300">
                    @if ($ct->iniciado_at)
                        <div class="flex justify-between">
                            <span>Iniciado em</span>
                            <span class="font-medium">{{ $ct->iniciado_at->format('d/m/Y H:i') }}</span>
                        </div>
                    @endif
                    @if ($ct->concluido_at)
                        <div class="flex justify-between">
                            <span>Concluído em</span>
                            <span class="font-medium">{{ $ct->concluido_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span>Tempo total</span>
                            <span class="font-medium">{{ gmdate('H:i:s', $ct->tempo_decorrido) }}</span>
                        </div>
                    @endif
                </div>
            </div>

            <p class="mt-6 text-xs text-slate-400 dark:text-slate-500">
                Você pode fechar esta página com segurança.<br>
                Caso precise refazer o teste, entre em contato com o recrutador.
            </p>

        </div>
    @endif

</div>
