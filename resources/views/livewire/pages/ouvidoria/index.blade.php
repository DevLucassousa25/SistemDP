<div class="p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="text-center sm:text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white font-display tracking-tight">Ouvidoria</h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5 font-normal">Canal seguro e confidencial para manifestações</p>
        </div>

        <div class="flex items-center gap-2">
            <button wire:click="abrirModalOuvidoria"
                class="w-full sm:w-auto flex items-center justify-center gap-2
                       bg-emerald-500 hover:bg-emerald-600 dark:bg-emerald-600 dark:hover:bg-emerald-700
                       text-white text-sm font-medium
                       px-4 py-2.5
                       rounded-lg shadow-sm
                       transition cursor-pointer lato-bold">
                <x-lucide-plus class="w-4 h-4" />
                Nova Manifestação
            </button>
        </div>
    </div>

    <div class="flex items-start gap-4 p-4 rounded-xl border border-green-200 dark:border-green-900/50 bg-green-50 dark:bg-green-900/20 text-green-900 dark:text-green-100">
        <!-- Icon -->
        <div class="flex items-center justify-center w-12 h-12 rounded-2xl bg-green-100 dark:bg-green-900/40">
            <x-lucide-shield class="w-6 h-6 text-green-600 dark:text-green-400" />
        </div>

        <!-- Content -->
        <div>
            <h3 class="font-semibold text-gray-800 dark:text-gray-100 lato-bold">
                Sigilo Garantido
            </h3>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400 leading-relaxed">
                Todas as manifestações são tratadas com absoluta confidencialidade.
                Você pode optar por enviar de forma anônima. Nenhuma retaliação será tolerada.
            </p>
        </div>
    </div>

    <div class="mt-8">
        @livewire('components.ouvidoria.ouvidoria-stats')
    </div>

    {{-- ── Painel de Auto-Encerramento Global (apenas RH/Admin) ─────────────── --}}
    @if (auth()->user()->isRhOuDp())
    <div class="mt-6 bg-white dark:bg-gray-800 rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">

        {{-- Header --}}
        <div class="px-5 py-3.5 flex items-center gap-2.5">
            <div class="w-7 h-7 rounded-lg flex items-center justify-center flex-shrink-0
                        {{ $configAtiva ? 'bg-emerald-50 dark:bg-emerald-900/30' : 'bg-gray-100 dark:bg-gray-700' }}">
                <x-lucide-bot class="w-3.5 h-3.5 {{ $configAtiva ? 'text-emerald-500 dark:text-emerald-400' : 'text-gray-400 dark:text-gray-500' }}" />
            </div>
            <div class="flex-1 min-w-0">
                <h2 class="text-sm font-semibold text-gray-700 dark:text-gray-200">Auto-Encerramento Global</h2>
                <p class="text-[11px] text-gray-400 dark:text-gray-500 mt-0.5">
                    Aplica-se a todas as ouvidorias abertas sem desativação individual.
                </p>
            </div>

            @if (! $editandoConfig)
                {{-- Badge de status --}}
                <span class="flex-shrink-0 inline-flex items-center gap-1 text-[11px] font-semibold px-2.5 py-1 rounded-full
                    {{ $configAtiva
                        ? 'bg-emerald-50 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                        : 'bg-gray-100 dark:bg-gray-700 text-gray-400 dark:text-gray-500' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $configAtiva ? 'bg-emerald-500' : 'bg-gray-400' }}"></span>
                    {{ $configAtiva ? 'Ativo' : 'Inativo' }}
                </span>

                {{-- Botão editar --}}
                <button wire:click="toggleEditarConfig"
                    class="flex-shrink-0 p-1.5 rounded-lg text-gray-400 dark:text-gray-500
                           hover:text-gray-600 dark:hover:text-gray-300
                           hover:bg-gray-100 dark:hover:bg-gray-700
                           transition cursor-pointer"
                    title="Editar configuração">
                    <x-lucide-pencil class="w-3.5 h-3.5" />
                </button>
            @else
                <span class="flex-shrink-0 text-[11px] font-semibold px-2.5 py-1 rounded-full
                             bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400">
                    Editando
                </span>
            @endif
        </div>

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- MODO VISUALIZAÇÃO --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        @if (! $editandoConfig)
        <div class="border-t border-gray-100 dark:border-gray-700 px-5 py-4">
            @if ($configAtiva)
                {{-- Estado: ATIVO — exibe resumo completo --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">

                    {{-- Prazo --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-blue-50 dark:bg-blue-900/20 flex items-center justify-center flex-shrink-0">
                            <x-lucide-clock class="w-4 h-4 text-blue-500 dark:text-blue-400" />
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Prazo de inatividade</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 mt-0.5">
                                {{ $prazoValor }} {{ $prazoUnidade }}
                            </p>
                            @if ($prazoUnidade === 'dias')
                                <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ (int)$prazoValor * 24 }}h no total</p>
                            @else
                                <p class="text-[10px] text-gray-400 dark:text-gray-500">{{ round((int)$prazoValor / 24, 1) }} dias no total</p>
                            @endif
                        </div>
                    </div>

                    {{-- Cobertura --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-purple-50 dark:bg-purple-900/20 flex items-center justify-center flex-shrink-0">
                            <x-lucide-layers class="w-4 h-4 text-purple-500 dark:text-purple-400" />
                        </div>
                        <div>
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Cobertura</p>
                            <p class="text-sm font-semibold text-gray-800 dark:text-gray-100 mt-0.5">Todas as ouvidorias</p>
                            <p class="text-[10px] text-gray-400 dark:text-gray-500">exceto com desat. individual</p>
                        </div>
                    </div>

                    {{-- Mensagem --}}
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center flex-shrink-0">
                            <x-lucide-message-square-text class="w-4 h-4 text-amber-500 dark:text-amber-400" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-[10px] font-semibold text-gray-400 dark:text-gray-500 uppercase tracking-wider">Mensagem padrão</p>
                            <p class="text-xs text-gray-600 dark:text-gray-300 mt-0.5 line-clamp-2 leading-relaxed">
                                {{ $mensagemAutoEncerramento }}
                            </p>
                        </div>
                    </div>

                </div>

            @else
                {{-- Estado: INATIVO --}}
                <div class="flex items-center gap-3 py-1">
                    <x-lucide-info class="w-4 h-4 text-gray-400 dark:text-gray-500 flex-shrink-0" />
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        O auto-encerramento está <strong class="text-gray-700 dark:text-gray-200">desativado</strong>.
                        Clique em <strong class="text-gray-700 dark:text-gray-200">Editar</strong> para configurar e ativar.
                    </p>
                </div>
            @endif
        </div>
        @endif

        {{-- ══════════════════════════════════════════════════════════════════ --}}
        {{-- MODO EDIÇÃO --}}
        {{-- ══════════════════════════════════════════════════════════════════ --}}
        @if ($editandoConfig)
        <div class="border-t border-gray-100 dark:border-gray-700 px-5 py-5 space-y-5">

            {{-- Toggle: ativar / desativar --}}
            <div class="flex items-start justify-between gap-4 p-4 rounded-xl
                        bg-gray-50 dark:bg-gray-700/40 border border-gray-100 dark:border-gray-700">
                <div>
                    <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Ativar auto-encerramento</p>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">
                        Ouvidorias sem resposta dentro do prazo serão encerradas automaticamente
                        com o envio da mensagem abaixo.
                    </p>
                </div>
                <button
                    type="button"
                    wire:click="$toggle('configAtiva')"
                    class="relative flex-shrink-0 mt-0.5 w-11 h-6 rounded-full transition-colors cursor-pointer
                           focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2
                           dark:focus:ring-offset-gray-800
                           {{ $configAtiva ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-600' }}"
                    role="switch"
                    aria-checked="{{ $configAtiva ? 'true' : 'false' }}"
                >
                    <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform
                                 {{ $configAtiva ? 'translate-x-5' : 'translate-x-0' }}"></span>
                </button>
            </div>

            {{-- Prazo de inatividade --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                    Prazo máximo de inatividade
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    Tempo sem atividade antes do encerramento automático.
                </p>
                <div class="flex gap-2">
                    <input
                        type="number"
                        wire:model="prazoValor"
                        min="1"
                        max="999"
                        class="w-28 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                               bg-white dark:bg-gray-700/60 text-gray-800 dark:text-gray-100
                               focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500
                               focus:border-transparent transition"
                    />
                    <select
                        wire:model="prazoUnidade"
                        class="flex-1 px-3 py-2 text-sm rounded-lg border border-gray-200 dark:border-gray-600
                               bg-white dark:bg-gray-700/60 text-gray-800 dark:text-gray-100
                               focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500
                               focus:border-transparent transition cursor-pointer"
                    >
                        <option value="horas">Horas</option>
                        <option value="dias">Dias</option>
                    </select>
                </div>
                @error('prazoValor')
                    <p class="mt-1.5 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                @enderror
            </div>

            {{-- Mensagem automática --}}
            <div>
                <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1">
                    Mensagem de encerramento automático
                </label>
                <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                    Enviada ao solicitante quando a ouvidoria for encerrada automaticamente.
                </p>
                <textarea
                    wire:model="mensagemAutoEncerramento"
                    rows="4"
                    maxlength="2000"
                    placeholder="Digite a mensagem padrão de encerramento..."
                    class="w-full px-3.5 py-3 text-sm rounded-xl border border-gray-200 dark:border-gray-600
                           bg-white dark:bg-gray-700/60 text-gray-800 dark:text-gray-100
                           placeholder-gray-400 dark:placeholder-gray-500
                           focus:outline-none focus:ring-2 focus:ring-emerald-400 dark:focus:ring-emerald-500
                           focus:border-transparent resize-none transition"
                ></textarea>
                <div class="flex items-center justify-between mt-1">
                    @error('mensagemAutoEncerramento')
                        <p class="text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                    @else
                        <span></span>
                    @enderror
                    <p class="text-[10px] text-gray-400 dark:text-gray-500 text-right">
                        {{ mb_strlen($mensagemAutoEncerramento) }}/2000
                    </p>
                </div>
            </div>

            {{-- Ações --}}
            <div class="flex gap-2 pt-1">
                <button wire:click="toggleEditarConfig"
                    wire:loading.attr="disabled"
                    wire:target="salvarConfig"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-medium
                           border border-gray-200 dark:border-gray-600
                           text-gray-600 dark:text-gray-300
                           hover:bg-gray-50 dark:hover:bg-gray-700/50
                           transition cursor-pointer disabled:opacity-50">
                    <x-lucide-x class="w-3.5 h-3.5" />
                    Cancelar
                </button>

                <button wire:click="salvarConfig"
                    wire:loading.attr="disabled"
                    wire:target="salvarConfig"
                    class="flex-1 inline-flex items-center justify-center gap-1.5 px-4 py-2.5 rounded-lg text-sm font-medium
                           {{ $configAtiva
                               ? 'bg-emerald-500 hover:bg-emerald-600 dark:bg-emerald-600 dark:hover:bg-emerald-700'
                               : 'bg-gray-700 hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500' }}
                           text-white transition cursor-pointer disabled:opacity-60">
                    <x-lucide-save class="w-3.5 h-3.5" wire:loading.remove wire:target="salvarConfig" />
                    <svg wire:loading wire:target="salvarConfig" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    {{ $configAtiva ? 'Salvar e Ativar' : 'Salvar' }}
                </button>
            </div>

        </div>
        @endif

    </div>
    @endif

    <div class="mt-8">
        @livewire('components.ui.table.manifestacao-table')
    </div>

    {{-- Modal de cadastro de manifestação --}}
    @livewire('components.ui.modal.ouvidoria-create-modal')

    {{-- ── Modal de sucesso do Auto-Encerramento ──────────────────────────── --}}
    @if ($modalSucesso)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        {{-- Backdrop --}}
        <div wire:click="$set('modalSucesso', false)"
             class="absolute inset-0 bg-black/40 dark:bg-black/60 backdrop-blur-sm"></div>

        {{-- Card --}}
        <div class="relative w-full max-w-sm bg-white dark:bg-gray-800 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Topo colorido --}}
            <div class="h-1.5 w-full {{ $modalSucessoAtivou ? 'bg-emerald-500' : 'bg-gray-400' }}"></div>

            <div class="px-6 pt-6 pb-7 text-center">

                {{-- Ícone --}}
                <div class="mx-auto mb-4 w-14 h-14 rounded-full flex items-center justify-center
                            {{ $modalSucessoAtivou
                                ? 'bg-emerald-50 dark:bg-emerald-900/30'
                                : 'bg-gray-100 dark:bg-gray-700' }}">
                    @if ($modalSucessoAtivou)
                        <x-lucide-bot class="w-7 h-7 text-emerald-500 dark:text-emerald-400" />
                    @else
                        <x-lucide-save class="w-7 h-7 text-gray-500 dark:text-gray-400" />
                    @endif
                </div>

                {{-- Título --}}
                <h3 class="text-base font-bold text-gray-900 dark:text-white mb-2">
                    {{ $modalSucessoAtivou ? 'Auto-Encerramento Ativado!' : 'Configuração Salva' }}
                </h3>

                {{-- Mensagem --}}
                <p class="text-sm text-gray-500 dark:text-gray-400 leading-relaxed">
                    {{ $modalSucessoMensagem }}
                </p>

                @if ($modalSucessoAtivou)
                    {{-- Resumo do que foi configurado --}}
                    <div class="mt-4 p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-100 dark:border-emerald-800/40 text-left space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-gray-500 dark:text-gray-400">Prazo de inatividade</span>
                            <span class="font-semibold text-gray-800 dark:text-gray-100">{{ $prazoValor }} {{ $prazoUnidade }}</span>
                        </div>
                        <div class="flex items-start justify-between text-xs gap-4">
                            <span class="text-gray-500 dark:text-gray-400 flex-shrink-0">Mensagem padrão</span>
                            <span class="font-medium text-gray-700 dark:text-gray-200 text-right line-clamp-2">{{ $mensagemAutoEncerramento }}</span>
                        </div>
                    </div>
                @endif

                {{-- Botão OK --}}
                <button wire:click="$set('modalSucesso', false)"
                    class="mt-5 w-full inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                           {{ $modalSucessoAtivou
                               ? 'bg-emerald-500 hover:bg-emerald-600 dark:bg-emerald-600 dark:hover:bg-emerald-700'
                               : 'bg-gray-700 hover:bg-gray-800 dark:bg-gray-600 dark:hover:bg-gray-500' }}
                           text-white transition cursor-pointer">
                    <x-lucide-check class="w-4 h-4" />
                    Entendido
                </button>

            </div>
        </div>
    </div>
    @endif

</div>
