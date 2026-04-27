<div x-data="{ open: @entangle('open') }" x-effect="document.body.style.overflow = open ? 'hidden' : ''">
    @if ($open)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="cancel"></div>
            <div class="relative bg-white dark:bg-slate-800 w-full max-w-sm rounded-2xl shadow-2xl z-10 p-6 text-center">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-lucide-trash-2 class="w-6 h-6 text-red-600" />
                </div>
                <h3 class="text-base font-semibold text-slate-800 dark:text-white lato-bold mb-1">Excluir tarefa?</h3>
                <p class="text-sm text-slate-400 lato-regular mb-6">Esta ação é permanente e não pode ser desfeita.</p>
                <div class="flex gap-3">
                    <button type="button" wire:click="cancel"
                            class="flex-1 px-4 py-2.5 text-sm lato-bold text-slate-600 border border-slate-200
                                   rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer dark:text-slate-300 dark:border-slate-600">
                        Cancelar
                    </button>
                    <button type="button" wire:click="confirm"
                            wire:loading.attr="disabled" wire:target="confirm"
                            class="flex-1 px-4 py-2.5 text-sm lato-bold bg-red-600 hover:bg-red-700 text-white
                                   rounded-xl shadow-sm transition cursor-pointer disabled:opacity-60">
                        <span wire:loading.remove wire:target="confirm">Excluir</span>
                        <span wire:loading wire:target="confirm">Excluindo...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
