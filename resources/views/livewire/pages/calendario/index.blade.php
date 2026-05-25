<div class="p-4 sm:p-6 lg:p-8 min-h-full">

    {{-- ── Cabeçalho ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
        <div class="text-center sm:text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white font-display tracking-tight">
                Calendário Corporativo
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5">
                Feriados, datas comemorativas e eventos da empresa
            </p>
        </div>

        @if($this->isRh)
            <button wire:click="abrirModal"
                class="w-full sm:w-auto flex items-center justify-center gap-2
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       dark:from-blue-500 dark:to-indigo-600 dark:hover:from-blue-600 dark:hover:to-indigo-700
                       text-white text-sm font-semibold lato-bold px-4 py-2.5 rounded-lg shadow-md shadow-blue-500/20 transition cursor-pointer">
                <x-lucide-plus class="w-4 h-4" />
                Novo Evento
            </button>
        @endif
    </div>


    {{-- ── Layout: calendário + painel lateral ─────────────────────────────── --}}
    <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━ CALENDÁRIO ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="w-full lg:flex-1 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">

            {{-- Navegação do mês --}}
            <div class="flex items-center justify-between px-4 py-4 sm:px-6 sm:py-5">
                <h2 class="text-base sm:text-lg font-bold lato-bold text-slate-800 dark:text-white capitalize truncate pr-2">
                    {{ $this->labelMes }}
                </h2>

                <div class="flex items-center gap-1 shrink-0">
                    <button wire:click="mesAnterior"
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600
                               text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-chevron-left class="w-4 h-4" />
                    </button>

                    <button wire:click="irHoje"
                        class="h-8 px-2.5 sm:px-3 text-xs sm:text-sm font-semibold lato-bold rounded-lg border border-slate-200 dark:border-slate-600
                               text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        Hoje
                    </button>

                    <button wire:click="mesProximo"
                        class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600
                               text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-chevron-right class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- Cabeçalho dias da semana --}}
            <div class="grid grid-cols-7 px-1.5 sm:px-2 mb-1">
                @foreach(['Seg','Ter','Qua','Qui','Sex','Sáb','Dom'] as $d)
                    <div class="py-1.5 sm:py-2 text-center text-[10px] sm:text-xs font-semibold lato-bold text-slate-400 dark:text-slate-500 tracking-wide">
                        {{ $d }}
                    </div>
                @endforeach
            </div>

            {{-- Grid dos dias --}}
            @php
                $hoje         = \Carbon\Carbon::today()->toDateString();
                $offset       = $this->offsetInicio;
                $totalDias    = $this->totalDiasMes;
                $ano          = $this->ano;
                $mes          = $this->mes;
                $mapa         = $this->mapaEventos;
                $diaSel       = $diaSelecionado;
                $totalCelulas = (int) ceil(($offset + $totalDias) / 7) * 7;
            @endphp

            <div class="grid grid-cols-7 gap-y-1 px-1.5 sm:px-2 pb-3 sm:pb-4">
                @for($cel = 0; $cel < $totalCelulas; $cel++)
                    @php
                        $diaNum  = $cel - $offset + 1;
                        $valido  = $diaNum >= 1 && $diaNum <= $totalDias;
                        $dataStr = $valido
                            ? \Carbon\Carbon::create($ano, $mes, $diaNum)->toDateString()
                            : null;
                        $evsCel  = $dataStr ? ($mapa[$dataStr] ?? []) : [];
                        $isHoje  = $dataStr === $hoje;
                        $isSel   = $dataStr === $diaSel;
                        $dow     = $valido ? \Carbon\Carbon::create($ano, $mes, $diaNum)->dayOfWeek : null;
                        $isFds   = $valido && in_array($dow, [0, 6]); // dom=0, sáb=6
                    @endphp

                    @if($valido)
                        <button
                            wire:click="selecionarDia('{{ $dataStr }}')"
                            class="relative flex flex-col items-center justify-start pt-1.5 sm:pt-2 pb-1.5 sm:pb-2
                                   h-12 sm:h-16 lg:h-20 rounded-lg sm:rounded-xl transition cursor-pointer group
                                   {{ $isSel
                                       ? 'bg-emerald-500 dark:bg-emerald-600'
                                       : ($isFds
                                           ? 'hover:bg-slate-50 dark:hover:bg-slate-700/50 bg-slate-50/60 dark:bg-slate-700/20'
                                           : 'hover:bg-slate-50 dark:hover:bg-slate-700/50') }}"
                        >
                            {{-- Número do dia --}}
                            <span class="text-xs sm:text-sm lato-bold leading-none transition
                                {{ $isSel
                                    ? 'text-white'
                                    : ($isHoje
                                        ? 'text-emerald-600 dark:text-emerald-400 font-extrabold'
                                        : ($isFds
                                            ? 'text-slate-400 dark:text-slate-500'
                                            : 'text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white')) }}">
                                {{ $diaNum }}
                            </span>

                            {{-- Pontos de eventos (no rodapé da célula) --}}
                            @if(!empty($evsCel))
                                <div class="absolute bottom-1.5 sm:bottom-2 flex items-center justify-center gap-0.5">
                                    @foreach(array_slice($evsCel, 0, 3) as $ev)
                                        <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full {{ $isSel ? 'bg-white/70' : '' }}"
                                              style="{{ $isSel ? '' : 'background-color:' . $ev->cor }}"></span>
                                    @endforeach
                                    @if(count($evsCel) > 3)
                                        <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full {{ $isSel ? 'bg-white/50' : 'bg-slate-300 dark:bg-slate-500' }}"></span>
                                    @endif
                                </div>
                            @endif
                        </button>
                    @else
                        <div class="h-12 sm:h-16 lg:h-20"></div>
                    @endif
                @endfor
            </div>

            {{-- Legenda --}}
            <div class="px-4 sm:px-6 py-3 border-t border-slate-100 dark:border-slate-700 flex flex-wrap gap-x-5 gap-y-1.5">
                @foreach(\App\Models\CalendarioEvento::TIPOS as $key => $label)
                    <div class="flex items-center gap-1.5">
                        <span class="w-2 h-2 rounded-full shrink-0"
                              style="background-color: {{ \App\Models\CalendarioEvento::CORES_PADRAO[$key] }}"></span>
                        <span class="text-[11px] text-slate-500 dark:text-slate-400 lato-regular">{{ $label }}</span>
                    </div>
                @endforeach
            </div>
        </div>


        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━ PAINEL LATERAL ━━━━━━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="lg:w-80 xl:w-96 shrink-0 flex flex-col gap-4">

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 flex flex-col">

                {{-- Cabeçalho do dia --}}
                <div class="px-4 sm:px-5 pt-4 sm:pt-5 pb-3 flex items-center justify-between border-b border-slate-100 dark:border-slate-700">
                    @if($diaSelecionado)
                        @php
                            $cs   = \Carbon\Carbon::parse($diaSelecionado);
                            $nDia = ['Domingo','Segunda-feira','Terça-feira','Quarta-feira','Quinta-feira','Sexta-feira','Sábado'];
                            $nMes = ['','Jan','Fev','Mar','Abr','Mai','Jun','Jul','Ago','Set','Out','Nov','Dez'];
                        @endphp
                        <div>
                            <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular">
                                {{ $nDia[$cs->dayOfWeek] }}
                            </p>
                            <h3 class="text-base sm:text-lg font-bold lato-bold text-slate-800 dark:text-white leading-snug">
                                {{ $cs->day }} de {{ $nMes[$cs->month] }} {{ $cs->year }}
                            </h3>
                        </div>
                    @else
                        <h3 class="text-base font-bold lato-bold text-slate-800 dark:text-white">Eventos do dia</h3>
                    @endif

                    @if($this->isRh && $diaSelecionado)
                        <button wire:click="abrirModal('{{ $diaSelecionado }}')"
                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600
                                   text-slate-500 dark:text-slate-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                   hover:text-emerald-600 dark:hover:text-emerald-400 hover:border-emerald-300 dark:hover:border-emerald-700
                                   transition cursor-pointer">
                            <x-lucide-plus class="w-4 h-4" />
                        </button>
                    @endif
                </div>

                {{-- Lista de eventos --}}
                @if($this->eventosDiaSelecionado->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 sm:py-12 px-6 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                            <x-lucide-calendar-x class="w-6 h-6 text-slate-400 dark:text-slate-500" />
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">Nenhum evento neste dia.</p>
                        @if($this->isRh)
                            <button wire:click="abrirModal('{{ $diaSelecionado }}')"
                                class="mt-3 text-xs text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer lato-regular">
                                + Adicionar evento
                            </button>
                        @endif
                    </div>
                @else
                    <div class="px-3 sm:px-4 py-3 sm:py-4 space-y-3">
                        @foreach($this->eventosDiaSelecionado as $evento)
                            @php
                                $tipoLabel = \App\Models\CalendarioEvento::TIPOS[$evento->tipo] ?? $evento->tipo;
                                $tipoBg = match($evento->tipo) {
                                    'feriado_nacional'  => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                                    'data_comemorativa' => 'bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400',
                                    default             => 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400',
                                };
                            @endphp

                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 group relative">

                                {{-- Barra lateral colorida --}}
                                <div class="absolute left-0 top-3 bottom-3 w-1 rounded-full"
                                     style="background-color: {{ $evento->cor }}"></div>

                                {{-- Ações RH --}}
                                @if($this->isRh)
                                    <div class="absolute top-3 right-3 flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition">
                                        <button wire:click="editarEvento({{ $evento->id }})"
                                            class="w-6 h-6 flex items-center justify-center rounded-md text-slate-400
                                                   hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition cursor-pointer">
                                            <x-lucide-pencil class="w-3.5 h-3.5" />
                                        </button>
                                        <button wire:click="excluirEvento({{ $evento->id }})"
                                            wire:confirm="Remover '{{ $evento->titulo }}'?"
                                            class="w-6 h-6 flex items-center justify-center rounded-md text-slate-400
                                                   hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition cursor-pointer">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @endif

                                {{-- Título --}}
                                <p class="text-sm font-bold lato-bold text-slate-800 dark:text-white leading-snug pl-3 pr-14">
                                    {{ $evento->titulo }}
                                </p>

                                {{-- Badges --}}
                                <div class="flex items-center flex-wrap gap-1.5 mt-2 pl-3">
                                    <span class="inline-flex items-center gap-1 text-[10px] font-semibold lato-bold px-1.5 py-0.5 rounded {{ $tipoBg }}">
                                        @if($evento->tipo === 'feriado_nacional')
                                            <x-lucide-landmark class="w-2.5 h-2.5" />
                                        @elseif($evento->tipo === 'data_comemorativa')
                                            <x-lucide-star class="w-2.5 h-2.5" />
                                        @else
                                            <x-lucide-building-2 class="w-2.5 h-2.5" />
                                        @endif
                                        {{ $tipoLabel }}
                                    </span>

                                    @if($evento->recorrente_anual)
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold lato-bold px-1.5 py-0.5 rounded
                                                     bg-slate-100 dark:bg-slate-600 text-slate-500 dark:text-slate-400">
                                            <x-lucide-repeat class="w-2.5 h-2.5" />
                                            Anual
                                        </span>
                                    @endif
                                </div>

                                {{-- Descrição --}}
                                @if($evento->descricao)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-snug lato-regular pl-3">
                                        {{ $evento->descricao }}
                                    </p>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>

    </div>{{-- /layout --}}


    {{-- ── Modal Criar / Editar Evento ─────────────────────────────────────── --}}
    @if($showModal)
        <div class="fixed inset-0 z-50 flex items-start justify-end"
             wire:click.self="fecharModal">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="fecharModal"></div>

            {{-- Drawer lateral --}}
            <div class="relative z-10 h-full w-full sm:w-[420px] bg-white dark:bg-slate-800
                        border-l border-slate-200 dark:border-slate-700
                        flex flex-col shadow-2xl overflow-hidden"
                 wire:click.stop>

                {{-- Header --}}
                <div class="flex items-center justify-between px-4 sm:px-6 py-4 sm:py-5
                            border-b border-slate-200 dark:border-slate-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600
                                    flex items-center justify-center shrink-0">
                            <x-lucide-calendar-plus class="w-4 h-4 text-white" />
                        </div>
                        <h2 class="text-base font-bold lato-bold text-slate-800 dark:text-white truncate">
                            {{ $isEditing ? 'Editar Evento' : 'Novo Evento' }}
                        </h2>
                    </div>
                    <button wire:click="fecharModal"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 shrink-0 ml-2
                               hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Body --}}
                <form wire:submit="salvarEvento" class="flex flex-col flex-1 overflow-hidden">
                    <div class="overflow-y-auto flex-1 px-4 sm:px-6 py-4 sm:py-5 space-y-4 sm:space-y-5">

                        {{-- Data --}}
                        <div>
                            <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                                Data <span class="text-red-400">*</span>
                            </label>
                            <input type="date" wire:model="formData"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                       transition lato-regular" />
                            @error('formData')
                                <p class="mt-1 text-xs text-red-500 lato-regular">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Título --}}
                        <div>
                            <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                                Título <span class="text-red-400">*</span>
                            </label>
                            <input type="text" wire:model="formTitulo" placeholder="Ex: Aniversário da Empresa"
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                       transition lato-regular" />
                            @error('formTitulo')
                                <p class="mt-1 text-xs text-red-500 lato-regular">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Tipo --}}
                        <div>
                            <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                                Tipo <span class="text-red-400">*</span>
                            </label>
                            <div class="grid grid-cols-1 gap-2">
                                @foreach(\App\Models\CalendarioEvento::TIPOS as $key => $label)
                                    @php
                                        $iconeTipo = match($key) {
                                            'feriado_nacional'  => 'landmark',
                                            'data_comemorativa' => 'star',
                                            default             => 'building-2',
                                        };
                                        $corTipo = match($key) {
                                            'feriado_nacional'  => 'text-red-500',
                                            'data_comemorativa' => 'text-amber-500',
                                            default             => 'text-blue-500',
                                        };
                                        $ativo = $formTipo === $key;
                                    @endphp
                                    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition
                                                  {{ $ativo
                                                      ? 'border-emerald-400 dark:border-emerald-600 bg-emerald-50 dark:bg-emerald-900/20'
                                                      : 'border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500' }}">
                                        <input type="radio" wire:model.live="formTipo" value="{{ $key }}" class="sr-only" />
                                        <div class="w-8 h-8 rounded-lg {{ $ativo ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center shrink-0 transition">
                                            <x-dynamic-component :component="'lucide-' . $iconeTipo"
                                                class="w-4 h-4 {{ $ativo ? 'text-emerald-600 dark:text-emerald-400' : $corTipo }}" />
                                        </div>
                                        <span class="text-sm lato-regular {{ $ativo ? 'font-semibold text-emerald-700 dark:text-emerald-300' : 'text-slate-700 dark:text-slate-300' }}">
                                            {{ $label }}
                                        </span>
                                        @if($ativo)
                                            <x-lucide-check class="w-4 h-4 text-emerald-500 ml-auto shrink-0" />
                                        @endif
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Cor + Recorrente --}}
                        <div class="flex items-end gap-5">
                            <div>
                                <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                                    Cor
                                </label>
                                <div class="flex items-center gap-2">
                                    <input type="color" wire:model="formCor"
                                        class="h-9 w-14 rounded-lg border border-slate-200 dark:border-slate-600
                                               bg-white dark:bg-slate-700 cursor-pointer p-1 transition" />
                                    <span class="text-xs text-slate-500 dark:text-slate-400 lato-regular font-mono">{{ $formCor }}</span>
                                </div>
                            </div>

                            <label class="flex items-center gap-2.5 cursor-pointer select-none pb-1">
                                <div class="relative">
                                    <input type="checkbox" wire:model="formRecorrente" class="sr-only peer" />
                                    <div class="w-9 h-5 bg-slate-200 dark:bg-slate-600 rounded-full peer-checked:bg-emerald-500 transition"></div>
                                    <div class="absolute top-0.5 left-0.5 w-4 h-4 bg-white rounded-full shadow peer-checked:translate-x-4 transition-transform"></div>
                                </div>
                                <span class="text-sm text-slate-700 dark:text-slate-300 lato-regular">Repete todo ano</span>
                            </label>
                        </div>

                        {{-- Descrição --}}
                        <div>
                            <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                                Descrição <span class="text-slate-400 font-normal normal-case">(opcional)</span>
                            </label>
                            <textarea wire:model="formDescricao" rows="3"
                                placeholder="Detalhes ou observações sobre o evento..."
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                       placeholder-slate-400 dark:placeholder-slate-500
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                       transition lato-regular resize-none"></textarea>
                            @error('formDescricao')
                                <p class="mt-1 text-xs text-red-500 lato-regular">{{ $message }}</p>
                            @enderror
                        </div>

                    </div>

                    {{-- Footer --}}
                    <div class="px-4 sm:px-6 py-4 border-t border-slate-200 dark:border-slate-700 shrink-0 flex gap-3">
                        <button type="button" wire:click="fecharModal"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold lato-bold text-slate-600 dark:text-slate-300
                                   bg-slate-100 dark:bg-slate-700 rounded-lg hover:bg-slate-200 dark:hover:bg-slate-600
                                   transition cursor-pointer">
                            Cancelar
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 text-sm font-semibold lato-bold text-white
                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                                   rounded-lg shadow-md shadow-blue-500/20 transition cursor-pointer"
                            wire:loading.attr="disabled" wire:loading.class="opacity-70 cursor-not-allowed">
                            <span wire:loading.remove wire:target="salvarEvento">
                                {{ $isEditing ? 'Salvar alterações' : 'Criar evento' }}
                            </span>
                            <span wire:loading wire:target="salvarEvento" class="flex items-center justify-center gap-2">
                                <svg class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/>
                                </svg>
                                Salvando...
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
