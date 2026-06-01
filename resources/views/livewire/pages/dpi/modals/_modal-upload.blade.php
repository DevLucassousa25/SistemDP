    @if ($uploadModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data="{ show: false, dragging: false }" x-init="$nextTick(() => show = true)">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                 wire:click="$set('uploadModal', false)"></div>
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 scale-95"
                 x-transition:enter-end="opacity-100 scale-100"
                 class="relative z-10 w-full max-w-md bg-white dark:bg-slate-800
                        rounded-2xl border border-slate-200 dark:border-slate-700
                        shadow-2xl p-6 space-y-5">

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <x-lucide-paperclip class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white lato-bold">Anexar Evidência</h3>
                    </div>
                    <button wire:click="$set('uploadModal', false)" type="button"
                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                   text-slate-400 hover:text-slate-700 hover:bg-slate-100
                                   dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <p class="text-xs text-slate-400 lato-regular -mt-2">
                    Diploma, certificado, comprovante ou outro documento comprobatório.
                    Formatos: PDF, imagem, Word, Excel. Máx. 10 MB.
                </p>

                {{-- Área de upload --}}
                <div x-on:dragover.prevent="dragging = true"
                     x-on:dragleave.prevent="dragging = false"
                     x-on:drop.prevent="dragging = false"
                     :class="dragging ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20' : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/40'"
                     class="rounded-xl border-2 border-dashed px-4 py-6 text-center transition cursor-pointer"
                     onclick="document.getElementById('evidenceFileInput').click()">
                    <x-lucide-upload-cloud class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                    <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">
                        Arraste o arquivo aqui ou <span class="text-indigo-500 lato-bold">clique para selecionar</span>
                    </p>
                    <input id="evidenceFileInput" type="file"
                           wire:model="evidenceFile"
                           accept=".pdf,.jpg,.jpeg,.png,.doc,.docx,.xls,.xlsx"
                           class="hidden" />
                </div>

                @if ($evidenceFile)
                    <div class="flex items-center gap-3 px-3 py-2.5 bg-indigo-50 dark:bg-indigo-900/20
                                rounded-xl border border-indigo-200 dark:border-indigo-800">
                        <x-lucide-file class="w-5 h-5 text-indigo-500 shrink-0" />
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">
                                {{ $evidenceFile->getClientOriginalName() }}
                            </p>
                            <p class="text-[11px] text-slate-400 lato-regular">
                                {{ number_format($evidenceFile->getSize() / 1024, 1) }} KB
                            </p>
                        </div>
                        <button wire:click="$set('evidenceFile', null)" type="button"
                                class="w-5 h-5 flex items-center justify-center rounded text-slate-400 hover:text-red-500 transition cursor-pointer">
                            <x-lucide-x class="w-3.5 h-3.5" />
                        </button>
                    </div>
                @endif

                @error('evidenceFile')
                    <p class="text-xs text-red-400 lato-regular flex items-center gap-1">
                        <x-lucide-alert-circle class="w-3.5 h-3.5" /> {{ $message }}
                    </p>
                @enderror

                <div class="flex justify-end gap-2 pt-1">
                    <button wire:click="$set('uploadModal', false)" type="button"
                            class="px-4 py-2 text-sm lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                                   text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="saveEvidenceUpload" type="button"
                            class="px-5 py-2 text-sm lato-bold rounded-xl
                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                                   shadow-md shadow-blue-500/20 transition cursor-pointer
                                   disabled:opacity-50 disabled:cursor-not-allowed"
                            {{ ! $evidenceFile ? 'disabled' : '' }}>
                        <span wire:loading.remove wire:target="saveEvidenceUpload">Salvar Evidência</span>
                        <span wire:loading wire:target="saveEvidenceUpload">Enviando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
