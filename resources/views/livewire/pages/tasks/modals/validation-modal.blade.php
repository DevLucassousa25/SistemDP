<div x-data="{ open: @entangle('open') }" x-effect="document.body.style.overflow = open ? 'hidden' : ''">
    @if ($open)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4"
             x-data x-init="$nextTick(() => $el.querySelector('textarea')?.focus())">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm"
                 wire:click="cancel"></div>

            {{-- Card --}}
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md
                         border border-slate-200 dark:border-slate-700 p-6">

                <div class="flex items-start gap-3 mb-4">
                    <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-shield-check class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-sm lato-bold text-slate-800 dark:text-slate-100">Validar conclusão da tarefa</p>
                        <p class="text-[11px] text-slate-400 lato-regular mt-0.5">
                            Descreva a evidência que comprova a conclusão.
                        </p>
                    </div>
                    <button type="button"
                            wire:click="cancel"
                            class="ml-auto text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <textarea wire:model="validationNote" rows="4"
                          placeholder="Ex: Funcionário entregou relatórios no prazo por 30 dias consecutivos, comprovado pelos registros do sistema..."
                          class="w-full px-3 py-2.5 text-sm lato-regular rounded-xl border border-slate-200 dark:border-slate-600
                                 bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400
                                 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition resize-none"></textarea>
                @error('validationNote')
                    <p class="text-xs text-red-500 lato-regular mt-1">{{ $message }}</p>
                @enderror

                <p class="text-[10px] text-slate-400 lato-regular mt-2 flex items-center gap-1">
                    <x-lucide-info class="w-3 h-3" />
                    A evidência fica registrada permanentemente no plano de ação.
                </p>

                <div class="flex items-center justify-end gap-2 mt-4">
                    <button type="button"
                            wire:click="cancel"
                            class="px-4 py-2 text-sm lato-bold text-slate-500 dark:text-slate-400
                                   hover:text-slate-700 dark:hover:text-slate-200 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="button"
                            wire:click="confirm"
                            wire:loading.attr="disabled" wire:target="confirm"
                            class="flex items-center gap-2 px-4 py-2 text-sm lato-bold
                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20 disabled:opacity-60 text-white rounded-xl transition cursor-pointer">
                        <span wire:loading.remove wire:target="confirm" class="flex items-center gap-2">
                            <x-lucide-shield-check class="w-4 h-4" />
                            Confirmar validação
                        </span>
                        <span wire:loading wire:target="confirm">Validando...</span>
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
