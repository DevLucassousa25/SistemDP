    {{-- ═══════════════════════════════════════════════════════════════
         MODAL: CICLO (criar / editar)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($cycleModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" wire:click="$set('cycleModal', false)"></div>
        <div class="relative z-10 bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-md">

            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                        <x-lucide-clipboard-list class="w-4 h-4 text-indigo-600" />
                    </div>
                    <h2 class="text-sm font-bold text-slate-800 dark:text-white lato-black">
                        {{ $editingCycleId ? 'Editar ciclo' : 'Novo ciclo de avaliação' }}
                    </h2>
                </div>
                <button type="button" wire:click="$set('cycleModal', false)"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Nome do ciclo <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="cycleName" placeholder="Ex: Avaliação Semestral 2026"
                           class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                  bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                    @error('cycleName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Data de início <span class="text-red-400">*</span></label>
                        <input type="date" wire:model="cycleStart"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                        @error('cycleStart') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Data de fim <span class="text-red-400">*</span></label>
                        <input type="date" wire:model="cycleEnd"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                        @error('cycleEnd') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">Descrição</label>
                    <textarea wire:model="cycleDescription" rows="2" placeholder="Descreva o objetivo deste ciclo..."
                              class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                     bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                     placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular resize-none"></textarea>
                </div>
            </div>

            <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 flex justify-end gap-3">
                <button type="button" wire:click="$set('cycleModal', false)"
                        class="px-4 py-2 text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition cursor-pointer">
                    Cancelar
                </button>
                <button type="button" wire:click="saveCycle"
                        wire:loading.attr="disabled" wire:target="saveCycle"
                        class="flex items-center gap-2 px-5 py-2 text-sm lato-bold bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20 text-white rounded-xl transition cursor-pointer disabled:opacity-60">
                    <x-lucide-save class="w-3.5 h-3.5" />
                    {{ $editingCycleId ? 'Atualizar' : 'Criar ciclo' }}
                </button>
            </div>
        </div>
    </div>
    @endif
