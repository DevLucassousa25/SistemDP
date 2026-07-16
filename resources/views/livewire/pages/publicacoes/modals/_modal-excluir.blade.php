    {{-- ════════════════════════════════════════════════════════════════
         MODAL — CONFIRMAR EXCLUSÃO
    ════════════════════════════════════════════════════════════════ --}}
    @if ($deleteModal)
        <div class="fixed inset-0 bg-black/40 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-sm p-6 space-y-4">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-trash-2 class="w-5 h-5 text-red-500" />
                    </div>
                    <div>
                        <h3 class="text-sm lato-black text-slate-800 dark:text-white">Excluir publicação</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-1">Esta ação é irreversível. A publicação e sua imagem de capa serão removidas permanentemente.</p>
                    </div>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button wire:click="$set('deleteModal', false)" type="button"
                            class="px-4 py-2 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="delete" type="button"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm lato-bold bg-red-600 hover:bg-red-700 text-white transition cursor-pointer disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="delete" class="flex items-center gap-1.5">
                            <x-lucide-trash-2 class="w-4 h-4" /> Excluir
                        </span>
                        <span wire:loading wire:target="delete" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
