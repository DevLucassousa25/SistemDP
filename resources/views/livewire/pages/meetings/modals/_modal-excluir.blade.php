    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl overflow-hidden">
            <div class="p-6">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center mx-auto mb-4">
                    <x-lucide-trash-2 class="w-5 h-5 text-red-500" />
                </div>
                <h3 class="text-base font-bold lato-bold text-slate-800 dark:text-white text-center mb-2">
                    Excluir Reuni
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 text-center lato-regular">
                    Esta a
 irrevers
vel. A reuni
o ser
 removida do calend
rio e todos os participantes perder
o o acesso.
                </p>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button wire:click="cancelDelete"
                        class="flex-1 px-4 py-2 text-sm lato-bold text-slate-600 dark:text-slate-300
                               border border-slate-200 dark:border-slate-600
                               hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition cursor-pointer">
                    Cancelar
                </button>
                <button wire:click="deleteReuniao"
                        class="flex-1 px-4 py-2 text-sm lato-bold text-white
                               bg-red-500 hover:bg-red-600 rounded-lg transition cursor-pointer disabled:opacity-60"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="deleteReuniao" class="flex items-center gap-1.5">
                        Sim, excluir
                    </span>
                    <span wire:loading wire:target="deleteReuniao" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif
