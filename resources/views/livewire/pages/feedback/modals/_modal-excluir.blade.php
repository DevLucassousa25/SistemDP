    {{-- Modal de confirmação de exclusão --}}
    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="cancelarExclusao"></div>
            <div class="relative bg-white dark:bg-slate-800 w-full max-w-sm rounded-2xl shadow-2xl z-10 overflow-hidden p-6 text-center">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-lucide-trash-2 class="w-6 h-6 text-red-600" />
                </div>
                <h3 class="text-base font-semibold text-slate-800 dark:text-white lato-bold mb-1">Excluir feedback?</h3>
                <p class="text-sm text-slate-400 lato-regular mb-6">Esta ação é permanente e não pode ser desfeita.</p>
                <div class="flex gap-3">
                    <button type="button" wire:click="cancelarExclusao"
                            class="flex-1 px-4 py-2.5 text-sm lato-bold text-slate-600 border border-slate-200 rounded-xl
                                   hover:bg-slate-50 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="button" wire:click="excluir"
                            class="flex-1 px-4 py-2.5 text-sm lato-bold bg-red-600 hover:bg-red-700 text-white
                                   rounded-xl shadow-sm transition cursor-pointer disabled:opacity-60"
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
