@if ($currDeleteModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="$set('currDeleteModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-sm p-6">
        <div class="flex flex-col items-center text-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center"><x-lucide-trash-2 class="w-7 h-7 text-red-500" /></div>
            <div>
                <h3 class="text-base lato-black text-slate-800 dark:text-white">Excluir currículo?</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-1">Esta ação não pode ser desfeita.</p>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button wire:click="$set('currDeleteModal', false)" type="button"
                    class="cursor-pointer flex-1 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
            <button wire:click="deleteCurriculo" type="button" wire:loading.attr="disabled" wire:target="deleteCurriculo"
                    class="cursor-pointer flex-1 px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm lato-bold hover:bg-red-600 transition disabled:opacity-60">
                <span wire:loading.remove wire:target="deleteCurriculo">Confirmar</span>
                <span wire:loading wire:target="deleteCurriculo">Excluindo...</span>
            </button>
        </div>
    </div>
</div>
@endif
