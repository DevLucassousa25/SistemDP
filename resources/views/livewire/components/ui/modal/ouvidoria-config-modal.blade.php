<div>
@if ($modalAberto)
    {{-- Backdrop --}}
    <div
        wire:click="fechar"
        class="fixed inset-0 z-40 bg-black/40 dark:bg-black/60 backdrop-blur-sm"
        aria-hidden="true"
    ></div>

    {{-- Modal --}}
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4"
        role="dialog"
        aria-modal="true"
        aria-labelledby="config-ouvidoria-title"
    >
        <div class="w-full max-w-lg bg-white dark:bg-gray-800 rounded-2xl shadow-xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-gray-100 dark:border-gray-700">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                        <x-lucide-settings-2 class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <h2 id="config-ouvidoria-title" class="text-sm font-semibold text-gray-900 dark:text-white">
                            Configurações de Auto-Encerramento
                        </h2>
                        <p class="text-xs text-gray-400 dark:text-gray-500">Ouvidoria · RH / Administração</p>
                    </div>
                </div>
                <button wire:click="fechar"
                    class="p-1.5 rounded-lg text-gray-400 dark:text-gray-500 hover:text-gray-600 dark:hover:text-gray-300
                           hover:bg-gray-100 dark:hover:bg-gray-700 transition cursor-pointer">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="px-6 py-5 space-y-5">

                {{-- Toggle: Ativar / Desativar --}}
                <div class="flex items-start justify-between gap-4 p-4 rounded-xl bg-gray-50 dark:bg-gray-700/40
                            border border-gray-100 dark:border-gray-700">
                    <div>
                        <p class="text-sm font-semibold text-gray-800 dark:text-gray-100">Ativar auto-encerramento</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5 leading-relaxed">
                            Ouvidorias sem resposta do DP dentro do prazo serão encerradas automaticamente
                            com o envio da mensagem configurada abaixo.
                        </p>
                    </div>

                    {{-- Toggle switch --}}
                    <button
                        type="button"
                        wire:click="$toggle('autoEncerramentoAtivo')"
                        class="relative flex-shrink-0 mt-0.5 w-11 h-6 rounded-full transition-colors cursor-pointer
                               focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:ring-offset-2
                               dark:focus:ring-offset-gray-800
                               {{ $autoEncerramentoAtivo ? 'bg-emerald-500' : 'bg-gray-200 dark:bg-gray-600' }}"
                        role="switch"
                        aria-checked="{{ $autoEncerramentoAtivo ? 'true' : 'false' }}"
                    >
                        <span class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform
                                     {{ $autoEncerramentoAtivo ? 'translate-x-5' : 'translate-x-0' }}">
                        </span>
                    </button>
                </div>

                {{-- Prazo de inatividade --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Prazo máximo de inatividade
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        Tempo sem atividade (criação ou última resposta do DP) antes do encerramento automático.
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
                        <p class="mt-1 text-xs text-red-500 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Mensagem automática --}}
                <div>
                    <label class="block text-sm font-semibold text-gray-700 dark:text-gray-200 mb-1.5">
                        Mensagem de encerramento automático
                    </label>
                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-3">
                        Esta mensagem será enviada ao solicitante quando a ouvidoria for encerrada automaticamente.
                    </p>

                    <textarea
                        wire:model="mensagemAutoEncerramento"
                        rows="4"
                        maxlength="2000"
                        placeholder="Digite a mensagem padrão de encerramento..."
                        class="w-full px-3.5 py-3 text-sm rounded-xl border border-gray-200 dark:border-gray-600
                               bg-white dark:bg-gray-700/60
                               text-gray-800 dark:text-gray-100
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

                {{-- Aviso sobre o scheduler --}}
                <div class="flex items-start gap-2.5 p-3 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800/40">
                    <x-lucide-info class="w-4 h-4 text-amber-500 flex-shrink-0 mt-0.5" />
                    <p class="text-xs text-amber-700 dark:text-amber-300 leading-relaxed">
                        O auto-encerramento é executado pelo agendador do Laravel. Para que funcione em produção,
                        certifique-se de que o <code class="font-mono bg-amber-100 dark:bg-amber-900/40 px-1 rounded">php artisan schedule:run</code>
                        esteja configurado no cron do servidor.
                    </p>
                </div>

            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 dark:border-gray-700 bg-gray-50/50 dark:bg-gray-800/50">
                <button wire:click="fechar"
                    class="px-4 py-2 text-sm font-medium text-gray-600 dark:text-gray-300
                           hover:text-gray-800 dark:hover:text-white transition cursor-pointer">
                    Cancelar
                </button>
                <button wire:click="salvar" wire:loading.attr="disabled"
                    class="inline-flex items-center gap-1.5 px-5 py-2 rounded-lg text-sm font-medium
                           bg-emerald-500 hover:bg-emerald-600 dark:bg-emerald-600 dark:hover:bg-emerald-700
                           text-white transition cursor-pointer disabled:opacity-60">
                    <x-lucide-save class="w-3.5 h-3.5" wire:loading.remove wire:target="salvar" />
                    <svg wire:loading wire:target="salvar" class="animate-spin w-3.5 h-3.5" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                    </svg>
                    Salvar configurações
                </button>
            </div>

        </div>
    </div>
@endif
