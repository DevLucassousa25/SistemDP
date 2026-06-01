    {{-- ════════════════════════════════════════════════════════════════
         MODAL CONFIRMAR EXCLUSÃO
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($deleteModal)
        <div x-data="{ show: false }" x-init="$nextTick(() => show = true)">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm"
                 wire:click="$set('deleteModal', false)"></div>
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 pointer-events-none">
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     class="pointer-events-auto w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl
                            border border-slate-200 dark:border-slate-700 shadow-2xl p-6 text-center">
                    <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
                        <x-lucide-trash-2 class="w-6 h-6 text-red-600 dark:text-red-400" />
                    </div>
                    <h2 class="text-base lato-bold text-slate-800 dark:text-slate-100 mb-2">Confirmar exclusão</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                        @if ($deleteType === 'goal')
                            Tem certeza que deseja remover esta competência e todas as suas ações?
                        @elseif ($deleteType === 'action')
                            Tem certeza que deseja remover esta ação de desenvolvimento?
                        @else
                            Esta ação não pode ser desfeita.
                        @endif
                    </p>
                    <div class="flex gap-3">
                        <button wire:click="$set('deleteModal', false)" type="button"
                                class="flex-1 px-4 py-2 text-sm border border-slate-200 dark:border-slate-600 rounded-lg
                                       text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700
                                       transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="confirmDelete" type="button"
                                class="flex-1 px-4 py-2 text-sm lato-bold bg-red-600 hover:bg-red-700 text-white
                                       rounded-lg transition cursor-pointer">
                            Excluir
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
