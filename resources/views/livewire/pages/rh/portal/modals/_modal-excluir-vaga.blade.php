@if ($vagaDeleteModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
            <x-lucide-trash-2 class="w-6 h-6 text-red-500" />
        </div>
        <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 mb-1">Excluir vaga?</h3>
        <p class="text-xs text-slate-500 lato-regular mb-5">Todos os dados e candidaturas serão apagados permanentemente.</p>
        <div class="flex gap-2 justify-center">
            <button wire:click="$set('vagaDeleteModal', false)" type="button"
                    class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
            <button wire:click="deleteVaga" type="button" wire:loading.attr="disabled" wire:target="deleteVaga"
                    class="cursor-pointer px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm lato-bold hover:bg-red-600 disabled:opacity-50 transition">Excluir</button>
        </div>
    </div>
</div>
@endif
