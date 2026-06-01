<div class="min-h-screen bg-[#f0f2f7] dark:bg-slate-950 flex flex-col items-center justify-start py-8 px-4" style="font-family: 'Inter', 'Lato', sans-serif;">

    {{-- ── INVÁLIDO --}}
    @if ($tela === 'invalido')
        <div class="w-full max-w-md mt-20 text-center">
            <div class="w-20 h-20 rounded-3xl bg-rose-100 flex items-center justify-center mx-auto mb-5 shadow-sm">
                <x-lucide-link-2-off class="w-9 h-9 text-rose-500" />
            </div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Link inválido</h1>
            <p class="text-slate-500 text-sm">Este link de teste não existe ou foi removido.</p>
        </div>

    {{-- ── EXPIRADO --}}
    @elseif ($tela === 'expirado')
        <div class="w-full max-w-md mt-20 text-center">
            <div class="w-20 h-20 rounded-3xl bg-amber-100 flex items-center justify-center mx-auto mb-5 shadow-sm">
                <x-lucide-alarm-clock-off class="w-9 h-9 text-amber-500" />
            </div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white mb-2">Link expirado</h1>
            <p class="text-slate-500 text-sm">O prazo para realizar este teste já passou.<br>Entre em contato com o recrutador para solicitar um novo link.</p>
        </div>

    {{-- ── BOAS-VINDAS --}}
    @elseif ($tela === 'boas_vindas')
        @php
            $teste     = $candidatoTeste->teste;
            $curriculo = $candidatoTeste->curriculo;
            $totalQ    = $teste->questoes->count();
            $tempo     = $teste->tempo_limite_minutos;
        @endphp

        <div class="w-full max-w-md">

            {{-- Logo --}}
            <div class="text-center mb-7">
                <div class="inline-flex items-center gap-2">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-sm">
                        <x-lucide-file-text class="w-4 h-4 text-white" />
                    </div>
                    <span class="text-base font-bold text-slate-700 dark:text-white">{{ config('app.name') }}</span>
                </div>
            </div>

            {{-- Card principal --}}
            <div class="bg-white dark:bg-slate-800 rounded-[28px] shadow-xl shadow-slate-200/80 dark:shadow-slate-900/80 overflow-hidden">

                {{-- Banner --}}
                <div class="relative bg-gradient-to-br from-indigo-500 via-indigo-600 to-violet-700 px-8 pt-8 pb-12 text-white text-center overflow-hidden">
                    {{-- Círculos decorativos --}}
                    <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white/10"></div>
                    <div class="absolute -bottom-4 -left-4 w-24 h-24 rounded-full bg-white/10"></div>
                    <div class="absolute top-4 left-8 w-3 h-3 rounded-full bg-white/20"></div>
                    <div class="absolute bottom-8 right-10 w-2 h-2 rounded-full bg-white/20"></div>

                    <div class="relative">
                        <div class="w-14 h-14 rounded-2xl bg-white/20 backdrop-blur flex items-center justify-center mx-auto mb-4">
                            <x-lucide-clipboard-list class="w-7 h-7 text-white" />
                        </div>
                        <h1 class="text-xl font-extrabold leading-tight mb-2">{{ $teste->titulo }}</h1>
                        @if ($curriculo)
                            <p class="text-indigo-200 text-sm">Olá, <span class="text-white font-bold">{{ $curriculo->nome }}</span>!</p>
                        @endif
                    </div>
                </div>

                {{-- Stats flutuantes --}}
                <div class="px-6 -mt-5 relative z-10 mb-5">
                    <div class="bg-white dark:bg-slate-700 rounded-2xl shadow-lg shadow-slate-200/60 dark:shadow-slate-900/50 grid grid-cols-2 divide-x divide-slate-100 dark:divide-slate-600">
                        <div class="px-5 py-4 text-center">
                            <p class="text-2xl font-extrabold text-indigo-600 dark:text-indigo-400">{{ $totalQ }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">{{ $totalQ === 1 ? 'questão' : 'questões' }}</p>
                        </div>
                        <div class="px-5 py-4 text-center">
                            @if ($tempo)
                                <p class="text-2xl font-extrabold text-violet-600 dark:text-violet-400">{{ $tempo }}<span class="text-sm font-semibold">min</span></p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">tempo limite</p>
                            @else
                                <p class="text-2xl font-extrabold text-violet-600 dark:text-violet-400">∞</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">sem limite</p>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="px-6 pb-7 space-y-4">

                    @if ($teste->descricao)
                        <p class="text-slate-500 dark:text-slate-400 text-sm text-center leading-relaxed">{{ $teste->descricao }}</p>
                    @endif

                    {{-- Expiração --}}
                    @if ($candidatoTeste->expira_at)
                        <div class="flex items-center gap-2.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 rounded-2xl px-4 py-3">
                            <x-lucide-calendar-x-2 class="w-4 h-4 text-amber-500 shrink-0" />
                            <p class="text-xs text-amber-700 dark:text-amber-400">
                                Este link expira em <strong>{{ $candidatoTeste->expira_at->format('d/m/Y \à\s H:i') }}</strong>.
                            </p>
                        </div>
                    @endif

                    {{-- Instruções --}}
                    @if ($teste->instrucoes)
                        <div class="bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 rounded-2xl px-4 py-3.5">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-[.15em] mb-2">Instruções</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line leading-relaxed">{{ $teste->instrucoes }}</p>
                        </div>
                    @endif

                    @if ($tempo)
                    <div class="flex items-start gap-2 bg-indigo-50 dark:bg-indigo-900/20 rounded-xl px-3 py-2.5">
                        <x-lucide-info class="w-3.5 h-3.5 text-indigo-400 shrink-0 mt-0.5" />
                        <p class="text-xs text-indigo-600 dark:text-indigo-400 leading-relaxed">O cronômetro inicia ao clicar em "Iniciar teste". Ao esgotar o tempo, as respostas são enviadas automaticamente.</p>
                    </div>
                    @endif

                    <button wire:click="iniciar" wire:loading.attr="disabled"
                            class="cursor-pointer w-full py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-extrabold text-sm tracking-wide transition-all shadow-lg shadow-indigo-500/30 hover:shadow-indigo-500/40 hover:-translate-y-0.5 active:translate-y-0 disabled:opacity-60 flex items-center justify-center gap-2">
                        <span wire:loading.remove wire:target="iniciar" class="flex items-center gap-2">
                            <x-lucide-play class="w-4 h-4" /> Iniciar teste
                        </span>
                        <span wire:loading wire:target="iniciar" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                            Iniciando…
                        </span>
                    </button>
                </div>
            </div>
        </div>

    {{-- ── EM ANDAMENTO --}}
    @elseif ($tela === 'em_andamento')
        @php
            $teste       = $candidatoTeste->teste;
            $questoes    = $teste->randomizar_questoes ? $teste->questoes->shuffle() : $teste->questoes;
            $totalQ      = $questoes->count();
            $tempoTotal  = $teste->tempo_limite_minutos ? $teste->tempo_limite_minutos * 60 : 0;
            $respondidas = collect($respostas)->filter(fn($v) => $v !== '' && $v !== null)->count();
            $pct         = $totalQ > 0 ? round($respondidas / $totalQ * 100) : 0;

            // Calcula segundos restantes com base no iniciado_at (respeita timezone)
            if ($tempoTotal > 0 && $candidatoTeste->iniciado_at) {
                $decorrido     = max(0, now()->timestamp - $candidatoTeste->iniciado_at->timestamp);
                $tempoRestante = max(0, $tempoTotal - $decorrido);
            } else {
                $tempoRestante = $tempoTotal;
            }
        @endphp

        <div class="w-full max-w-2xl"
             x-data="{
                totalSeg: {{ $tempoTotal }},
                restante: {{ $tempoRestante }},
                rodando: {{ $tempoTotal > 0 && $tempoRestante > 0 ? 'true' : 'false' }},
                enviando: false,
                get horas()    { return Math.floor(this.restante / 3600) },
                get minutos()  { return Math.floor((this.restante % 3600) / 60) },
                get segundos() { return this.restante % 60 },
                get display() {
                    if (this.totalSeg === 0) return null;
                    const h = String(this.horas).padStart(2,'0');
                    const m = String(this.minutos).padStart(2,'0');
                    const s = String(this.segundos).padStart(2,'0');
                    return this.horas > 0 ? h+':'+m+':'+s : m+':'+s;
                },
                get urgente() { return this.totalSeg > 0 && this.restante <= 60 },
                get aviso()   { return this.totalSeg > 0 && this.restante <= 300 && this.restante > 60 },
                get pctTempo() {
                    if (this.totalSeg === 0) return 100;
                    return Math.round((this.restante / this.totalSeg) * 100);
                },
                init() {
                    if (!this.rodando) return;
                    const tick = setInterval(() => {
                        if (this.restante <= 0) {
                            clearInterval(tick);
                            this.enviando = true;
                            $wire.call('concluir');
                            return;
                        }
                        this.restante--;
                    }, 1000);
                }
             }">

            {{-- Header sticky --}}
            <div class="sticky top-0 z-30 mb-5">
                <div class="bg-white/95 dark:bg-slate-800/95 backdrop-blur-md rounded-2xl shadow-lg shadow-slate-200/60 dark:shadow-slate-900/60 border border-slate-200/80 dark:border-slate-700/80 px-5 py-3.5 flex items-center gap-4">

                    {{-- Título --}}
                    <div class="min-w-0 flex-1">
                        <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">Teste em andamento</p>
                        <h1 class="text-sm font-extrabold text-slate-800 dark:text-white truncate leading-tight mt-0.5">{{ $teste->titulo }}</h1>
                    </div>

                    {{-- Progresso questões --}}
                    <div class="hidden sm:flex flex-col items-center shrink-0">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300 tabular-nums">{{ $respondidas }}/{{ $totalQ }}</span>
                        <span class="text-[10px] text-slate-400">respondidas</span>
                    </div>

                    {{-- Cronômetro --}}
                    @if ($tempoTotal > 0)
                    <div x-show="display" class="shrink-0">
                        <div :class="{
                                'bg-rose-500 shadow-rose-300/50': urgente,
                                'bg-amber-400 shadow-amber-300/50': aviso && !urgente,
                                'bg-gradient-to-r from-indigo-600 to-violet-600 shadow-indigo-300/30': !urgente && !aviso
                             }"
                             class="flex items-center gap-1.5 px-3.5 py-2 rounded-xl font-mono font-extrabold text-sm text-white shadow-md transition-all duration-500">
                            <svg :class="urgente ? 'animate-ping' : ''" class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6l4 2"/></svg>
                            <span x-text="display" class="tabular-nums"></span>
                        </div>
                        {{-- Barra de tempo --}}
                        <div class="mt-1.5 w-full bg-slate-200 dark:bg-slate-700 rounded-full h-1 overflow-hidden">
                            <div :class="{
                                    'bg-rose-500': urgente,
                                    'bg-amber-400': aviso && !urgente,
                                    'bg-gradient-to-r from-indigo-500 to-violet-500': !urgente && !aviso
                                 }"
                                 :style="'width:' + pctTempo + '%'"
                                 class="h-1 rounded-full transition-all duration-1000"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Alertas de tempo --}}
            @if ($tempoTotal > 0)
            <div x-show="urgente" x-cloak
                 class="mb-4 flex items-center gap-3 bg-rose-500 text-white rounded-2xl px-4 py-3 shadow-lg shadow-rose-500/30">
                <x-lucide-alarm-clock class="w-5 h-5 shrink-0 animate-bounce" />
                <p class="text-sm font-bold">Menos de 1 minuto! O teste será enviado automaticamente.</p>
            </div>
            <div x-show="aviso && !urgente" x-cloak
                 class="mb-4 flex items-center gap-3 bg-amber-400 text-amber-900 rounded-2xl px-4 py-3">
                <x-lucide-timer class="w-4 h-4 shrink-0" />
                <p class="text-sm font-semibold">Atenção: menos de 5 minutos restantes.</p>
            </div>
            @endif

            {{-- Barra progresso topo --}}
            <div class="mb-5 bg-white dark:bg-slate-800 rounded-2xl px-5 py-3.5 border border-slate-200 dark:border-slate-700 shadow-sm flex items-center gap-4">
                <div class="flex-1">
                    <div class="flex justify-between mb-1.5">
                        <span class="text-xs font-bold text-slate-600 dark:text-slate-300">Progresso</span>
                        <span class="text-xs font-bold {{ $pct === 100 ? 'text-indigo-600' : 'text-slate-400' }}">{{ $pct }}%</span>
                    </div>
                    <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                        <div class="h-2 rounded-full bg-gradient-to-r from-indigo-500 to-violet-500 transition-all duration-500"
                             style="width: {{ $pct }}%"></div>
                    </div>
                </div>
                <div class="shrink-0 text-right">
                    <span class="text-lg font-extrabold text-indigo-600 dark:text-indigo-400">{{ $respondidas }}</span>
                    <span class="text-xs text-slate-400 dark:text-slate-500">/{{ $totalQ }}</span>
                </div>
            </div>

            {{-- Questões --}}
            <div class="space-y-4">
                @php $letras = ['A','B','C','D','E','F']; @endphp
                @foreach ($questoes as $idx => $questao)
                    @php
                        $opcoes       = $teste->randomizar_opcoes ? $questao->opcoes->shuffle() : $questao->opcoes;
                        $valorInicial = $respostas[$questao->id] ?? '';
                    @endphp

                    <div x-data="{ sel: '{{ $valorInicial }}' }"
                         :class="sel !== '' ? 'border-indigo-300 dark:border-indigo-600/60 shadow-indigo-100 dark:shadow-indigo-900/20' : 'border-slate-200 dark:border-slate-700'"
                         class="bg-white dark:bg-slate-800 rounded-2xl border transition-all duration-200 shadow-sm overflow-hidden">

                        {{-- Header questão --}}
                        <div :class="sel !== '' ? 'bg-gradient-to-r from-indigo-50 to-violet-50/50 dark:from-indigo-900/20 dark:to-violet-900/10 border-b border-indigo-100 dark:border-indigo-800/30' : 'border-b border-slate-100 dark:border-slate-700/60'"
                             class="flex items-start gap-3.5 px-5 py-4">
                            <span :class="sel !== '' ? 'bg-indigo-600 text-white shadow-sm shadow-indigo-300/50' : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'"
                                  class="shrink-0 w-7 h-7 rounded-lg text-xs font-extrabold flex items-center justify-center transition-all">
                                {{ $idx + 1 }}
                            </span>
                            <p class="text-slate-800 dark:text-slate-100 text-sm font-semibold leading-relaxed flex-1 pt-0.5">
                                {{ $questao->enunciado }}
                            </p>
                            <div x-show="sel !== ''"
                                 class="shrink-0 w-5 h-5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center">
                                <svg class="w-3 h-3 text-indigo-600 dark:text-indigo-400" fill="none" stroke="currentColor" stroke-width="3" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                            </div>
                        </div>

                        {{-- Opções --}}
                        <div class="px-5 py-4">
                            @if ($questao->tipo === 'objetiva')
                                <div class="space-y-2">
                                    @foreach ($opcoes as $letra => $opcao)
                                    <button type="button"
                                            @click="sel === '{{ $opcao->id }}' ? (sel = '', $wire.set('respostas.{{ $questao->id }}', '')) : (sel = '{{ $opcao->id }}', $wire.set('respostas.{{ $questao->id }}', '{{ $opcao->id }}'))"
                                            x-bind:style="sel == '{{ $opcao->id }}' ? 'background:#eef2ff;border-color:#a5b4fc;' : ''"
                                            class="w-full flex items-center gap-3 rounded-xl border border-slate-200 transition-all duration-150 px-3.5 py-3 text-left cursor-pointer hover:border-indigo-200 hover:bg-slate-50">
                                        <div x-bind:style="sel == '{{ $opcao->id }}' ? 'background:#4f46e5;border-color:#4f46e5;color:white;' : ''"
                                             class="shrink-0 w-6 h-6 rounded-lg border-2 border-slate-300 flex items-center justify-center text-[10px] font-extrabold text-slate-400 transition-all">
                                            {{ $letras[$letra] ?? chr(65 + $letra) }}
                                        </div>
                                        <span x-bind:style="sel == '{{ $opcao->id }}' ? 'color:#3730a3;font-weight:600;' : ''"
                                              class="text-sm text-slate-700 leading-snug transition-colors flex-1">
                                            {{ $opcao->texto }}
                                        </span>
                                        <div x-show="sel == '{{ $opcao->id }}'"
                                             style="display:none"
                                             class="shrink-0 w-5 h-5 rounded-full bg-indigo-600 flex items-center justify-center">
                                            <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        </div>
                                    </button>
                                    @endforeach
                                </div>

                            @elseif ($questao->tipo === 'discursiva')
                                <textarea wire:model.lazy="respostas.{{ $questao->id }}"
                                          @input="sel = $event.target.value"
                                          rows="4"
                                          placeholder="Escreva sua resposta aqui..."
                                          class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-slate-800 dark:text-slate-200 text-sm px-4 py-3 resize-none focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 placeholder-slate-400 transition leading-relaxed">{{ $valorInicial }}</textarea>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Botão enviar --}}
            <div class="mt-6 pb-14">
                <div x-data="{ confirming: false }">

                    <div x-show="!confirming && !enviando">
                        <button type="button" @click="confirming = true"
                                class="cursor-pointer w-full py-4 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white font-extrabold text-sm tracking-wide transition-all shadow-xl shadow-indigo-500/30 hover:-translate-y-0.5 active:translate-y-0 flex items-center justify-center gap-2">
                            <x-lucide-send class="w-4 h-4" /> Enviar respostas
                        </button>
                    </div>

                    <div x-show="confirming && !enviando" x-cloak
                         class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-3xl p-7 text-center shadow-xl">
                        <div class="w-14 h-14 rounded-2xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center mx-auto mb-4">
                            <x-lucide-send class="w-7 h-7 text-amber-500" />
                        </div>
                        <h3 class="text-slate-800 dark:text-slate-100 font-extrabold text-base mb-1">Confirmar envio?</h3>
                        <p class="text-slate-400 text-xs mb-6">Após enviar, não será possível alterar suas respostas.</p>
                        <div class="flex gap-3">
                            <button type="button" @click="confirming = false"
                                    class="cursor-pointer flex-1 py-3 rounded-xl border-2 border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 text-sm font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                Revisar
                            </button>
                            <button wire:click="concluir" wire:loading.attr="disabled" wire:target="concluir"
                                    @click="enviando = true"
                                    class="cursor-pointer flex-1 py-3 rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 hover:from-indigo-700 hover:to-violet-700 text-white text-sm font-extrabold transition shadow-md disabled:opacity-60">
                                <span wire:loading.remove wire:target="concluir">Confirmar</span>
                                <span wire:loading wire:target="concluir" class="flex items-center justify-center gap-2">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg> Enviando…
                                </span>
                            </button>
                        </div>
                    </div>

                    <div x-show="enviando" x-cloak
                         class="bg-white dark:bg-slate-800 border border-indigo-200 dark:border-indigo-700/50 rounded-3xl p-8 text-center shadow-xl">
                        <svg class="w-12 h-12 animate-spin text-indigo-600 mx-auto mb-3" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/></svg>
                        <p class="text-base font-extrabold text-slate-800 dark:text-white mb-1">Enviando respostas…</p>
                        <p class="text-sm text-slate-400">Aguarde, não feche esta página.</p>
                    </div>
                </div>
            </div>
        </div>

    {{-- ── CONCLUÍDO --}}
    @elseif ($tela === 'concluido')
        @php
            $ct            = $candidatoTeste;
            $aprovado      = $ct->aprovado;
            $nota          = $ct->nota;
            $temDiscursiva = $ct->teste->questoes->where('tipo', 'discursiva')->count() > 0;
        @endphp

        <div class="w-full max-w-sm mt-6">
            <div class="bg-white dark:bg-slate-800 rounded-[28px] shadow-xl shadow-slate-200/80 dark:shadow-slate-900/80 overflow-hidden">

                @if ($temDiscursiva && $aprovado === null)
                <div class="bg-gradient-to-br from-blue-500 to-indigo-600 px-8 py-10 text-white text-center relative overflow-hidden">
                    <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full bg-white/10"></div>
                    <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-4 relative">
                        <x-lucide-clock class="w-8 h-8 text-white" />
                    </div>
                    <h1 class="text-xl font-extrabold mb-1">Enviado!</h1>
                    <p class="text-blue-200 text-sm">Aguardando avaliação</p>
                </div>
                @elseif ($aprovado)
                <div class="bg-gradient-to-br from-emerald-400 to-teal-600 px-8 py-10 text-white text-center relative overflow-hidden">
                    <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full bg-white/10"></div>
                    <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-4 relative">
                        <x-lucide-trophy class="w-8 h-8 text-white" />
                    </div>
                    <h1 class="text-xl font-extrabold mb-1">Parabéns!</h1>
                    <p class="text-emerald-100 text-sm">Você foi aprovado(a)</p>
                </div>
                @else
                <div class="bg-gradient-to-br from-slate-500 to-slate-700 px-8 py-10 text-white text-center relative overflow-hidden">
                    <div class="absolute -top-6 -right-6 w-28 h-28 rounded-full bg-white/10"></div>
                    <div class="w-16 h-16 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-4 relative">
                        <x-lucide-circle-check-big class="w-8 h-8 text-white" />
                    </div>
                    <h1 class="text-xl font-extrabold mb-1">Concluído</h1>
                    <p class="text-slate-300 text-sm">Obrigado por participar</p>
                </div>
                @endif

                <div class="px-6 py-6 space-y-4">

                    @if ($temDiscursiva && $aprovado === null)
                        <p class="text-slate-500 dark:text-slate-400 text-sm text-center leading-relaxed">
                            Suas respostas foram registradas. Este teste contém questões discursivas e será avaliado pelo recrutador em breve.
                        </p>
                    @endif

                    @if ($nota !== null && !($temDiscursiva && $aprovado === null))
                        @php $notaAprovacao = $ct->teste->nota_aprovacao ?? 60; @endphp
                        <div class="bg-slate-50 dark:bg-slate-900/50 rounded-2xl py-5 text-center border border-slate-200 dark:border-slate-700">
                            <p class="text-[10px] font-extrabold text-slate-400 uppercase tracking-widest mb-1">Sua nota</p>
                            <p class="text-6xl font-black {{ $aprovado ? 'text-emerald-500' : 'text-rose-500' }} leading-none tabular-nums">
                                {{ number_format($nota, 0, ',', '.') }}
                            </p>
                            <p class="text-slate-400 text-xs mt-2">de 100 pontos · mínimo {{ $notaAprovacao }}</p>
                        </div>
                    @endif

                    <div class="rounded-2xl border border-slate-200 dark:border-slate-700 divide-y divide-slate-100 dark:divide-slate-700/50 overflow-hidden">
                        @if ($ct->iniciado_at)
                        <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-slate-800">
                            <span class="text-slate-400 text-xs">Iniciado</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200 text-xs">{{ $ct->iniciado_at->format('d/m/Y H:i') }}</span>
                        </div>
                        @endif
                        @if ($ct->concluido_at)
                        <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-slate-800">
                            <span class="text-slate-400 text-xs">Concluído</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200 text-xs">{{ $ct->concluido_at->format('d/m/Y H:i') }}</span>
                        </div>
                        <div class="flex items-center justify-between px-4 py-3 bg-white dark:bg-slate-800">
                            <span class="text-slate-400 text-xs">Tempo total</span>
                            <span class="font-semibold text-slate-700 dark:text-slate-200 text-xs tabular-nums">{{ gmdate('H:i:s', $ct->tempo_decorrido) }}</span>
                        </div>
                        @endif
                    </div>

                    <p class="text-center text-xs text-slate-400 leading-relaxed pt-1">
                        Você pode fechar esta página com segurança.
                    </p>
                </div>
            </div>
        </div>
    @endif

</div>
