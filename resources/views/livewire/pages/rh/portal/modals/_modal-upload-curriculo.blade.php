@if ($uploadModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('uploadModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-md p-6"
         x-data="{ dragging: false }">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base lato-black text-slate-800 dark:text-white">Enviar Currículo</h2>
            <button wire:click="$set('uploadModal', false)" type="button" class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition"><x-lucide-x class="w-5 h-5" /></button>
        </div>
        <label for="currUpload" class="block w-full cursor-pointer"
               @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false" @drop.prevent="dragging=false">
            <div :class="dragging ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900'"
                 class="border-2 border-dashed rounded-2xl p-10 flex flex-col items-center gap-3 transition">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <x-lucide-file-up class="w-7 h-7 text-blue-500" />
                </div>
                <div class="text-center">
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">Arraste o arquivo ou clique para selecionar</p>
                    <p class="text-xs text-slate-400 lato-regular mt-1">PDF, DOC ou DOCX — máx. 5 MB</p>
                </div>
                <input id="currUpload" type="file" wire:model="arquivo" accept=".pdf,.doc,.docx" class="hidden" />
            </div>
        </label>
        @if ($arquivo)
        <div class="mt-3 flex items-center gap-2 p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
            <x-lucide-file-check class="w-4 h-4 text-green-500 shrink-0" />
            <span class="text-xs text-green-700 dark:text-green-300 lato-regular truncate">
                {{ is_object($arquivo) ? $arquivo->getClientOriginalName() : 'Arquivo selecionado' }}
            </span>
        </div>
        @endif
        @error('arquivo') <p class="mt-2 text-xs text-red-500 lato-regular">{{ $message }}</p> @enderror
        <div class="flex justify-end gap-3 mt-5">
            <button wire:click="$set('uploadModal', false)" type="button"
                    class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
            <button wire:click="uploadCurriculo" type="button" wire:loading.attr="disabled" wire:target="uploadCurriculo"
                    class="cursor-pointer relative inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                <span wire:loading.remove wire:target="uploadCurriculo"><x-lucide-sparkles class="w-4 h-4 inline" /> Enviar e Analisar</span>
                <span wire:loading wire:target="uploadCurriculo" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Analisando...
                </span>
            </button>
        </div>
    </div>
</div>
@endif
