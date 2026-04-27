<div x-data
     x-effect="document.body.style.overflow = ($wire.modalOpen) ? 'hidden' : ''">

    {{-- ═══════════════════════════════════════════════════════════════════
         MODAL PRINCIPAL
    ════════════════════════════════════════════════════════════════════ --}}
    @if ($modalOpen)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                 wire:click="fecharModal"></div>

            {{-- Painel --}}
            <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh]
                        rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 flex flex-col overflow-hidden">

                {{-- ── Header ───────────────────────────────────────────── --}}
                <div class="flex-shrink-0 border-b border-slate-100 dark:border-slate-700">

                    <div class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 sm:py-5">
                        <div class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500
                                    flex items-center justify-center shadow-sm">
                            <x-lucide-list-checks class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white lato-black leading-tight truncate">
                                Perguntas da Pesquisa
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
                </div>

                {{-- ── Body ────────────────────────────────────────────── --}}
                <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 space-y-4 bg-slate-50/40 dark:bg-slate-800/60">

                    {{-- ── Formulário inline (sempre no TOPO para ficar visível) ── --}}
                    @if ($showForm)
                        <div x-data
                             x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }))"
                             class="bg-white dark:bg-slate-700/60 rounded-xl border border-violet-100
                                    dark:border-violet-800/30 p-4 space-y-4 shadow-sm">

                            <div class="flex items-center justify-between">
                                <h3 class="text-sm font-bold text-slate-700 dark:text-white lato-black flex items-center gap-2">
                                    <x-lucide-plus-circle class="w-4 h-4 text-emerald-500" />
                                    {{ $editingId ? 'Editar pergunta' : 'Nova pergunta' }}
                                </h3>
                                <button wire:click="cancelarForm"
                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400
                                           hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-600
                                           transition cursor-pointer">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>
                            </div>

                            {{-- Seletor de tipo --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold lato-bold text-slate-500
                                              dark:text-slate-400 uppercase tracking-wide">
                                    Tipo de pergunta <span class="text-red-500">*</span>
                                </label>
                                <div class="grid grid-cols-3 gap-2">

                                    {{-- Escala --}}
                                    <label @class([
                                        'flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer transition',
                                        'border-blue-400 bg-blue-50 dark:bg-blue-900/20 text-blue-700' => $questionType === 'escala',
                                        'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-500 hover:border-slate-300' => $questionType !== 'escala',
                                    ])>
                                        <input type="radio" wire:model.live="questionType"
                                               value="escala" class="sr-only">
                                        <x-lucide-bar-chart-2 class="w-5 h-5" />
                                        <span class="text-[11px] lato-bold text-center leading-tight">
                                            Escala<br>0 – 10
                                        </span>
                                    </label>

                                    {{-- Múltipla escolha --}}
                                    <label @class([
                                        'flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer transition',
                                        'border-violet-400 bg-violet-50 dark:bg-violet-900/20 text-violet-700' => $questionType === 'multipla_escolha',
                                        'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-500 hover:border-slate-300' => $questionType !== 'multipla_escolha',
                                    ])>
                                        <input type="radio" wire:model.live="questionType"
                                               value="multipla_escolha" class="sr-only">
                                        <x-lucide-list-checks class="w-5 h-5" />
                                        <span class="text-[11px] lato-bold text-center leading-tight">
                                            Múltipla<br>escolha
                                        </span>
                                    </label>

                                    {{-- Texto livre --}}
                                    <label @class([
                                        'flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 cursor-pointer transition',
                                        'border-amber-400 bg-amber-50 dark:bg-amber-900/20 text-amber-700' => $questionType === 'texto_livre',
                                        'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-500 hover:border-slate-300' => $questionType !== 'texto_livre',
                                    ])>
                                        <input type="radio" wire:model.live="questionType"
                                               value="texto_livre" class="sr-only">
                                        <x-lucide-align-left class="w-5 h-5" />
                                        <span class="text-[11px] lato-bold text-center leading-tight">
                                            Texto<br>livre
                                        </span>
                                    </label>

                                </div>
                                @error('questionType')
                                    <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                        <x-lucide-alert-circle class="w-3 h-3" />{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Texto da pergunta --}}
                            <div class="space-y-1.5">
                                <label class="text-xs font-semibold lato-bold text-slate-500
                                              dark:text-slate-400 uppercase tracking-wide">
                                    Pergunta <span class="text-red-500">*</span>
                                </label>
                                <textarea wire:model.blur="questionText" rows="2"
                                    placeholder="Ex: Como você avalia o ambiente de trabalho?"
                                    class="w-full px-3 py-2 text-sm lato-regular rounded-lg resize-none
                                           bg-slate-50 dark:bg-slate-600 text-slate-800 dark:text-white
                                           placeholder-slate-400 focus:outline-none focus:ring-2
                                           focus:ring-emerald-500/40 focus:border-emerald-400 transition
                                           {{ $errors->has('questionText') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-500' }}"></textarea>
                                @error('questionText')
                                    <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                        <x-lucide-alert-circle class="w-3 h-3" />{{ $message }}
                                    </p>
                                @enderror
                            </div>

                            {{-- Opções (múltipla escolha) --}}
                            @if ($questionType === 'multipla_escolha')
                                <div class="space-y-2">
                                    <label class="text-xs font-semibold lato-bold text-slate-500
                                                  dark:text-slate-400 uppercase tracking-wide">
                                        Opções <span class="text-red-500">*</span>
                                    </label>

                                    <div class="space-y-2">
                                        @foreach ($options as $idx => $opcao)
                                            <div class="flex items-center gap-2">
                                                <div class="w-5 h-5 rounded-full border-2 border-violet-300
                                                            dark:border-violet-600 shrink-0 flex items-center
                                                            justify-center">
                                                    <div class="w-2 h-2 rounded-full bg-violet-300
                                                                dark:bg-violet-600"></div>
                                                </div>
                                                <input type="text"
                                                    wire:model.blur="options.{{ $idx }}"
                                                    placeholder="Opção {{ $idx + 1 }}"
                                                    class="flex-1 px-3 py-1.5 text-sm lato-regular rounded-lg
                                                           bg-slate-50 dark:bg-slate-600 text-slate-800 dark:text-white
                                                           placeholder-slate-400 border border-slate-200
                                                           dark:border-slate-500 focus:outline-none focus:ring-2
                                                           focus:ring-violet-400/40 focus:border-violet-400 transition
                                                           {{ $errors->has("options.{$idx}") ? 'border-red-400' : '' }}">
                                                @if (count($options) > 2)
                                                    <button wire:click="removerOpcao({{ $idx }})"
                                                        class="w-6 h-6 flex items-center justify-center rounded
                                                               text-slate-400 hover:text-red-500 hover:bg-red-50
                                                               dark:hover:bg-red-900/20 transition cursor-pointer shrink-0">
                                                        <x-lucide-x class="w-3.5 h-3.5" />
                                                    </button>
                                                @endif
                                            </div>
                                            @error("options.{$idx}")
                                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular ml-7">
                                                    <x-lucide-alert-circle class="w-3 h-3" />{{ $message }}
                                                </p>
                                            @enderror
                                        @endforeach
                                    </div>

                                    @error('options')
                                        <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                            <x-lucide-alert-circle class="w-3 h-3" />{{ $message }}
                                        </p>
                                    @enderror

                                    <button wire:click="adicionarOpcao"
                                        class="flex items-center gap-1.5 text-xs lato-bold text-violet-600
                                               hover:text-violet-700 transition cursor-pointer mt-1">
                                        <x-lucide-plus class="w-3.5 h-3.5" />
                                        Adicionar opção
                                    </button>
                                </div>
                            @endif

                            {{-- Preview escala --}}
                            @if ($questionType === 'escala')
                                <div class="rounded-lg border border-blue-100 dark:border-blue-800/40
                                            bg-blue-50/50 dark:bg-blue-900/10 p-3 space-y-2">
                                    <p class="text-[11px] lato-bold text-blue-600 dark:text-blue-400 uppercase tracking-wide">
                                        Preview da escala
                                    </p>
                                    <div class="flex items-center gap-1 flex-wrap">
                                        @for ($n = 0; $n <= 10; $n++)
                                            <span class="w-8 h-8 flex items-center justify-center rounded-lg
                                                         text-xs lato-bold border
                                                         {{ $n <= 6
                                                             ? 'bg-red-50 border-red-200 text-red-500'
                                                             : ($n <= 8
                                                                 ? 'bg-amber-50 border-amber-200 text-amber-500'
                                                                 : 'bg-emerald-50 border-emerald-200 text-emerald-600') }}">
                                                {{ $n }}
                                            </span>
                                        @endfor
                                    </div>
                                    <div class="flex justify-between text-[10px] lato-regular text-slate-400">
                                        <span>Péssimo</span>
                                        <span>Excelente</span>
                                    </div>
                                </div>
                            @endif

                            {{-- Preview texto livre --}}
                            @if ($questionType === 'texto_livre')
                                <div class="rounded-lg border border-amber-100 dark:border-amber-800/40
                                            bg-amber-50/50 dark:bg-amber-900/10 p-3 space-y-1.5">
                                    <p class="text-[11px] lato-bold text-amber-600 dark:text-amber-400 uppercase tracking-wide">
                                        Preview da resposta
                                    </p>
                                    <div class="h-16 rounded-lg border border-dashed border-amber-200
                                                dark:border-amber-800/40 bg-white dark:bg-slate-700/40
                                                flex items-start p-2">
                                        <span class="text-xs lato-regular text-slate-400 italic">
                                            O colaborador digitará a resposta aqui...
                                        </span>
                                    </div>
                                </div>
                            @endif

                            {{-- Obrigatória --}}
                            <div class="flex items-center justify-between p-2.5 rounded-lg border
                                        {{ $questionRequired
                                            ? 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/10 dark:border-emerald-800/40'
                                            : 'bg-slate-50 border-slate-200 dark:bg-slate-700/40 dark:border-slate-600' }}">
                                <div class="flex items-center gap-2">
                                    <x-lucide-asterisk class="w-4 h-4 {{ $questionRequired ? 'text-emerald-600' : 'text-slate-400' }}" />
                                    <span class="text-xs lato-bold {{ $questionRequired ? 'text-emerald-700 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-300' }}">
                                        Resposta obrigatória
                                    </span>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model.live="questionRequired" class="sr-only peer">
                                    <div class="w-10 h-[1.375rem] bg-slate-300 rounded-full peer
                                                peer-checked:bg-emerald-500
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                after:bg-white after:rounded-full after:h-[1.125rem] after:w-[1.125rem]
                                                after:transition-all peer-checked:after:translate-x-full
                                                transition-colors"></div>
                                </label>
                            </div>

                            {{-- Avaliação do Gestor --}}
                            <div class="flex items-center justify-between p-2.5 rounded-lg border
                                        {{ $questionIsManagerEval
                                            ? 'bg-violet-50 border-violet-200 dark:bg-violet-900/10 dark:border-violet-800/40'
                                            : 'bg-slate-50 border-slate-200 dark:bg-slate-700/40 dark:border-slate-600' }}">
                                <div class="flex items-center gap-2">
                                    <x-lucide-user-check class="w-4 h-4 {{ $questionIsManagerEval ? 'text-violet-600' : 'text-slate-400' }}" />
                                    <div>
                                        <span class="text-xs lato-bold {{ $questionIsManagerEval ? 'text-violet-700 dark:text-violet-300' : 'text-slate-600 dark:text-slate-300' }}">
                                            Avaliação do Gestor
                                        </span>
                                        <p class="text-[10px] lato-regular text-slate-400 leading-none mt-0.5">
                                            Resposta usada para avaliar o desempenho do gestor do setor
                                        </p>
                                    </div>
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model.live="questionIsManagerEval" class="sr-only peer">
                                    <div class="w-10 h-[1.375rem] bg-slate-300 rounded-full peer
                                                peer-checked:bg-violet-500
                                                after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                                after:bg-white after:rounded-full after:h-[1.125rem] after:w-[1.125rem]
                                                after:transition-all peer-checked:after:translate-x-full
                                                transition-colors"></div>
                                </label>
                            </div>

                            {{-- Botões do formulário --}}
                            <div class="flex gap-2 pt-1">
                                <button wire:click="cancelarForm"
                                    class="flex-1 px-4 py-2 text-sm lato-bold rounded-lg text-slate-600
                                           bg-white dark:bg-slate-600 border border-slate-200 dark:border-slate-500
                                           hover:bg-slate-50 dark:hover:bg-slate-500 transition cursor-pointer">
                                    Cancelar
                                </button>
                                <button wire:click="salvarPergunta"
                                    wire:loading.attr="disabled" wire:target="salvarPergunta"
                                    class="flex-1 px-4 py-2 text-sm lato-bold rounded-lg
                                           bg-emerald-600 hover:bg-emerald-700 text-white
                                           flex items-center justify-center gap-2 cursor-pointer
                                           shadow-sm transition">
                                    <svg wire:loading wire:target="salvarPergunta"
                                         class="w-4 h-4 animate-spin"
                                         xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                                stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                              d="M4 12a8 8 0 018-8v8H4z"></path>
                                    </svg>
                                    <span wire:loading.remove wire:target="salvarPergunta"
                                          class="flex items-center gap-1.5">
                                        <x-lucide-check class="w-4 h-4" />
                                        {{ $editingId ? 'Salvar alterações' : 'Adicionar pergunta' }}
                                    </span>
                                    <span wire:loading wire:target="salvarPergunta">Salvando...</span>
                                </button>
                            </div>
                        </div>
                    @endif

                    {{-- Lista de perguntas --}}
                    @if ($this->perguntas->isEmpty() && ! $showForm)
                        <div class="flex flex-col items-center justify-center py-12 text-center">
                            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700
                                        flex items-center justify-center mb-3">
                                <x-lucide-circle-help class="w-7 h-7 text-slate-400" />
                            </div>
                            <p class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">
                                Nenhuma pergunta ainda
                            </p>
                            <p class="text-xs text-slate-400 lato-regular mt-1 max-w-xs">
                                Adicione perguntas para que os colaboradores possam responder.
                            </p>
                        </div>
                    @else
                        {{-- Divisor quando formulário e lista aparecem juntos --}}
                        @if ($showForm && $this->perguntas->isNotEmpty())
                            <div class="flex items-center gap-2">
                                <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
                                <span class="text-[11px] lato-bold text-slate-400 uppercase tracking-wider">
                                    Perguntas ({{ $this->perguntas->count() }})
                                </span>
                                <div class="flex-1 h-px bg-slate-200 dark:bg-slate-700"></div>
                            </div>
                        @endif

                        <div class="space-y-2.5">
                            @foreach ($this->perguntas as $i => $pergunta)
                                @php
                                    $typeBadge = match ($pergunta->type) {
                                        'escala'           => 'bg-blue-50 text-blue-700 border-blue-200',
                                        'multipla_escolha' => 'bg-violet-50 text-violet-700 border-violet-200',
                                        'texto_livre'      => 'bg-amber-50 text-amber-700 border-amber-200',
                                        default            => 'bg-slate-100 text-slate-600 border-slate-200',
                                    };
                                    $typeIcon = match ($pergunta->type) {
                                        'escala'           => 'lucide-bar-chart-2',
                                        'multipla_escolha' => 'lucide-list-checks',
                                        'texto_livre'      => 'lucide-align-left',
                                        default            => 'lucide-circle-help',
                                    };
                                @endphp

                                <div class="bg-white dark:bg-slate-700/60 rounded-xl border border-slate-200
                                            dark:border-slate-600 p-3.5 flex gap-3 items-start group transition
                                            hover:border-slate-300 dark:hover:border-slate-500">

                                    {{-- Número --}}
                                    <div class="shrink-0 w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-600
                                                flex items-center justify-center text-[11px] font-bold
                                                text-slate-500 dark:text-slate-300 lato-black mt-0.5">
                                        {{ $i + 1 }}
                                    </div>

                                    {{-- Conteúdo --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm lato-bold text-slate-800 dark:text-white leading-snug">
                                            {{ $pergunta->question }}
                                            @if ($pergunta->required)
                                                <span class="text-red-400 ml-0.5">*</span>
                                            @endif
                                        </p>

                                        {{-- Badges --}}
                                        <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                            <span class="inline-flex items-center gap-1 text-[11px] lato-bold
                                                         px-2 py-0.5 rounded-full border {{ $typeBadge }}">
                                                <x-dynamic-component :component="$typeIcon" class="w-3 h-3" />
                                                {{ $pergunta->type_label }}
                                            </span>

                                            @if ($pergunta->is_manager_evaluation)
                                                <span class="inline-flex items-center gap-1 text-[11px] lato-bold
                                                             px-2 py-0.5 rounded-full border
                                                             bg-violet-50 text-violet-700 border-violet-200
                                                             dark:bg-violet-900/20 dark:text-violet-300 dark:border-violet-800">
                                                    <x-lucide-user-check class="w-3 h-3" />
                                                    Avaliação do Gestor
                                                </span>
                                            @endif
                                        </div>

                                        {{-- Preview opções --}}
                                        @if ($pergunta->type === 'multipla_escolha' && ! empty($pergunta->options))
                                            <div class="mt-2 flex flex-wrap gap-1.5">
                                                @foreach ($pergunta->options as $opcao)
                                                    <span class="text-[11px] lato-regular px-2 py-0.5 rounded-full
                                                                 bg-violet-50 dark:bg-violet-900/20 text-violet-600
                                                                 dark:text-violet-300 border border-violet-100
                                                                 dark:border-violet-800">
                                                        {{ $opcao }}
                                                    </span>
                                                @endforeach
                                            </div>
                                        @elseif ($pergunta->type === 'escala')
                                            {{-- Mini escala decorativa --}}
                                            <div class="mt-2 flex items-center gap-0.5">
                                                @for ($n = 0; $n <= 10; $n++)
                                                    <span class="w-5 h-5 flex items-center justify-center rounded text-[10px]
                                                                 lato-bold border
                                                                 {{ $n <= 6
                                                                     ? 'bg-red-50 border-red-200 text-red-500'
                                                                     : ($n <= 8
                                                                         ? 'bg-amber-50 border-amber-200 text-amber-500'
                                                                         : 'bg-emerald-50 border-emerald-200 text-emerald-600') }}">
                                                        {{ $n }}
                                                    </span>
                                                @endfor
                                            </div>
                                        @elseif ($pergunta->type === 'texto_livre')
                                            <div class="mt-2 h-7 rounded-lg border border-dashed border-amber-200
                                                        bg-amber-50/50 dark:bg-amber-900/10 dark:border-amber-800/40
                                                        flex items-center px-2">
                                                <span class="text-[11px] lato-regular text-amber-400 italic">
                                                    Campo de texto livre...
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    {{-- Ações --}}
                                    <div class="shrink-0 flex flex-col gap-1 opacity-0 group-hover:opacity-100 transition">
                                        <button wire:click="moverCima({{ $pergunta->id }})"
                                            @if ($i === 0) disabled @endif
                                            class="w-6 h-6 flex items-center justify-center rounded text-slate-400
                                                   hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-600
                                                   disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer"
                                            title="Mover para cima">
                                            <x-lucide-chevron-up class="w-3.5 h-3.5" />
                                        </button>
                                        <button wire:click="moverBaixo({{ $pergunta->id }})"
                                            @if ($i === $this->perguntas->count() - 1) disabled @endif
                                            class="w-6 h-6 flex items-center justify-center rounded text-slate-400
                                                   hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-600
                                                   disabled:opacity-30 disabled:cursor-not-allowed transition cursor-pointer"
                                            title="Mover para baixo">
                                            <x-lucide-chevron-down class="w-3.5 h-3.5" />
                                        </button>
                                        <button wire:click="editarPergunta({{ $pergunta->id }})"
                                            class="w-6 h-6 flex items-center justify-center rounded text-slate-400
                                                   hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20
                                                   transition cursor-pointer"
                                            title="Editar">
                                            <x-lucide-pencil class="w-3.5 h-3.5" />
                                        </button>
                                        <button wire:click="confirmarExclusao({{ $pergunta->id }})"
                                            class="w-6 h-6 flex items-center justify-center rounded text-slate-400
                                                   hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20
                                                   transition cursor-pointer"
                                            title="Remover">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @endif

                </div>

                {{-- ── Footer ───────────────────────────────────────────── --}}
                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100
                            dark:border-slate-700 bg-white dark:bg-slate-800
                            flex items-center justify-between gap-3">

                    <div class="flex items-center gap-1.5 text-xs text-slate-400 lato-regular">
                        <x-lucide-info class="w-3.5 h-3.5" />
                        <span>{{ $this->perguntas->count() }} {{ $this->perguntas->count() === 1 ? 'pergunta' : 'perguntas' }}</span>
                    </div>

                    <div class="flex gap-2">
                        <button wire:click="fecharModal"
                            class="px-4 py-2 text-sm lato-bold rounded-lg text-slate-600 bg-white
                                   border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                            Fechar
                        </button>
                        @if (! $showForm)
                            <button wire:click="abrirFormulario"
                                class="flex items-center gap-1.5 px-4 py-2 text-sm lato-bold rounded-lg
                                       bg-emerald-600 hover:bg-emerald-700 text-white shadow-sm transition
                                       cursor-pointer">
                                <x-lucide-plus class="w-4 h-4" />
                                Nova pergunta
                            </button>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════════
         MODAL DE CONFIRMAÇÃO DE EXCLUSÃO
    ════════════════════════════════════════════════════════════════════ --}}
    @if ($confirmDelete)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                 wire:click="cancelarExclusao"></div>

            <div class="relative bg-white dark:bg-slate-800 w-full max-w-sm rounded-2xl
                        shadow-2xl z-10 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-12 h-12 rounded-2xl bg-red-50 dark:bg-red-900/20
                                flex items-center justify-center mx-auto mb-3">
                        <x-lucide-trash-2 class="w-6 h-6 text-red-500" />
                    </div>
                    <h3 class="text-base font-bold text-slate-800 dark:text-white lato-black">
                        Remover pergunta?
                    </h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-1.5">
                        Esta ação não pode ser desfeita.
                    </p>
                </div>
                <div class="px-6 py-3 border-t border-slate-100 dark:border-slate-700
                            bg-slate-50 dark:bg-slate-700/40 flex gap-2">
                    <button wire:click="cancelarExclusao"
                        class="flex-1 px-4 py-2 text-sm lato-bold rounded-lg text-slate-600
                               bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="excluirPergunta"
                        class="flex-1 px-4 py-2 text-sm lato-bold rounded-lg
                               bg-red-500 hover:bg-red-600 text-white transition cursor-pointer">
                        Remover
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
