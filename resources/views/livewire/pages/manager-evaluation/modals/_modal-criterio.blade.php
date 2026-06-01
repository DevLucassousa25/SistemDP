    {{-- ═══════════════════════════════════════════════════════════════
         MODAL: CRITÉRIO (criar / editar)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($criterionModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" wire:click="$set('criterionModal', false)"></div>
        <div class="relative z-10 bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-lg">

            <div class="flex items-center justify-between px-6 py-5 border-b border-slate-100 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-violet-100 dark:bg-violet-900/40 flex items-center justify-center">
                        <x-lucide-list-checks class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white lato-black">
                            {{ $editingCriterionId ? 'Editar Critério' : 'Novo Critério' }}
                        </h2>
                        <p class="text-[11px] text-slate-400 lato-regular">Configure nome, tipo de resposta e peso</p>
                    </div>
                </div>
                <button wire:click="$set('criterionModal', false)" type="button"
                        class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <div class="px-6 py-5 space-y-4">
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nome do critério <span class="text-red-400">*</span></label>
                    <input type="text" wire:model="criterionName" placeholder="Ex: Trabalho em equipe"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                    @error('criterionName') <p class="text-[11px] text-red-500 mt-1 lato-regular">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição</label>
                    <textarea wire:model="criterionDescription" rows="2" placeholder="Orientação para avaliadores (opcional)..."
                              class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Peso <span class="text-red-400">*</span></label>
                    <input type="number" wire:model="criterionWeight" step="0.1" min="0.1" max="10" placeholder="1.0"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/50 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 lato-regular" />
                    @error('criterionWeight') <p class="text-[11px] text-red-500 mt-1 lato-regular">{{ $message }}</p> @enderror
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 px-6 py-4 border-t border-slate-100 dark:border-slate-700">
                <button wire:click="$set('criterionModal', false)" type="button"
                        class="px-4 py-2 rounded-xl text-sm lato-bold cursor-pointer text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="saveCriterion" wire:loading.attr="disabled" wire:target="saveCriterion" type="button"
                        class="inline-flex items-center gap-2 px-5 py-2 rounded-xl cursor-pointer text-sm lato-bold bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20 text-white transition disabled:opacity-60">
                    {{-- <x-lucide-save class="w-3.5 h-3.5" />
                    {{ $editingCriterionId ? 'Salvar alterações' : 'Criar critério' }} --}}

                     <span wire:loading.remove wire:target="saveCriterion" class="flex items-center gap-1">
                            @if ($editingCriterionId)
                                <x-lucide-square-pen class="w-4 h-4" />
                            @else
                                <x-lucide-circle-check class="w-4 h-4" />
                            @endif
                                {{ $editingCriterionId ? 'Salvar Alterações' : 'Confirmar' }}
                        </span>

                        <span wire:loading wire:target="saveCriterion" class="flex items-center gap-2">
                                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        </span>
                </button>
            </div>
        </div>
    </div>
    @endif
