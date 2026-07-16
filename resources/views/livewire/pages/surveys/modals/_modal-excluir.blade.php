    {{-- ───── Modal de confirmação de exclusão ─────────────────────── --}}
    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="cancelarExclusao"></div>
            <div
                class="relative bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl z-10 overflow-hidden">
                <div class="p-6 text-center">
                    <div
                        class="w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center mx-auto mb-3">
                        <x-lucide-trash-2 class="w-7 h-7 text-red-500" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white lato-black">Excluir pesquisa?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-2">
                        Esta ação não pode ser desfeita. Todas as perguntas e respostas associadas serão removidas.
                    </p>
                </div>
                <div
                    class="px-6 py-3 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/40 flex gap-2">
                    <button wire:click="cancelarExclusao"
                        class="flex-1 px-4 py-2 text-sm lato-bold rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer">Cancelar</button>
                    <button wire:click="excluir"
                        class="flex-1 px-4 py-2 text-sm lato-bold rounded-lg bg-red-500 hover:bg-red-600 text-white transition cursor-pointer disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="excluir" class="flex items-center gap-1.5">
                            Excluir
                        </span>
                        <span wire:loading wire:target="excluir" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
