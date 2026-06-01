@if ($currEditModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('currEditModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between z-10">
            <h2 class="text-base lato-black text-slate-800 dark:text-white">Editar Currículo</h2>
            <button wire:click="$set('currEditModal', false)" type="button" class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition"><x-lucide-x class="w-5 h-5" /></button>
        </div>
        <div class="p-3 md:p-6 space-y-3 md:space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Nome completo</label>
                    <input type="text" wire:model="editNome" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                    @error('editNome') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Email</label>
                    <input type="email" wire:model="editEmail" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                    @error('editEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Telefone</label>
                    <input type="text" wire:model="editTelefone" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Cidade</label>
                    <input type="text" wire:model="editCidade" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Estado (UF)</label>
                    <input type="text" wire:model="editEstado" maxlength="2" placeholder="SP" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Área de interesse</label>
                    <input type="text" wire:model="editArea" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Escolaridade</label>
                    <select wire:model="editEscolaridade" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular">
                        <option value="">Selecione</option>
                        @foreach ($escLabels as $val => $lbl) <option value="{{ $val }}">{{ $lbl }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Pretensão salarial (R$)</label>
                    <input type="number" wire:model="editPretensao" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                    <select wire:model="editStatus" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular">
                        <option value="ativo">Ativo</option>
                        <option value="favorito">Favorito</option>
                        <option value="banco_talentos">Banco de Talentos</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Resumo profissional</label>
                    <textarea wire:model="editResumo" rows="3" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Notas internas</label>
                    <textarea wire:model="editNotas" rows="3" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                </div>
            </div>
        </div>
        <div class="sticky bottom-0 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-end gap-3">
            <button wire:click="$set('currEditModal', false)" type="button"
                    class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
           <button wire:click="saveCurrEdit" type="button" wire:loading.attr="disabled" wire:target="saveCurrEdit"
                class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">

                <span wire:loading wire:target="saveCurrEdit">
                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                </span>

                <span wire:loading.remove wire:target="saveCurrEdit" class="flex items-center gap-1.5">
                    <x-lucide-circle-check class="w-4 h-4" />
                    Confirmar
                </span>
            </button>
        </div>
    </div>
</div>
@endif
