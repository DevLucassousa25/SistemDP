{{-- ── Modal: Check-in ───────────────────────────────────────────────── --}}
@if ($checkinModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('checkinModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-md
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[92vh] sm:max-h-[88vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                bg-gradient-to-br from-amber-400 to-orange-500">
                        <x-lucide-zap class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">Check-in de Progresso</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">Atualize o valor atual deste resultado-chave</p>
                    </div>
                </div>
                <button wire:click="$set('checkinModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-5">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Valor atual *</label>
                <input wire:model="checkinValue" type="number" step="any" placeholder="0"
                    class="w-full px-3.5 py-3 text-lg font-bold bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-300
                           focus:outline-none focus:ring-2 focus:ring-amber-400/40 transition" />
                @error('checkinValue') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Confiança --}}
            <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-4">
                <div class="flex items-center justify-between mb-3">
                    <label class="text-xs font-semibold text-slate-600 dark:text-slate-300">Nível de confiança</label>
                    <span class="text-sm font-bold px-2.5 py-0.5 rounded-lg
                        {{ $checkinConfidence >= 8 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-400'
                         : ($checkinConfidence >= 5 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/40 dark:text-amber-400'
                         : 'bg-rose-100 text-rose-700 dark:bg-rose-900/40 dark:text-rose-400') }}">
                        {{ $checkinConfidence }}/10
                    </span>
                </div>
                <input wire:model.live="checkinConfidence" type="range" min="1" max="10"
                    class="w-full accent-indigo-600 cursor-pointer" />
                <div class="flex justify-between text-[10px] text-slate-400 mt-1.5 font-medium">
                    <span>😟 Baixa</span>
                    <span>😐 Média</span>
                    <span>😊 Alta</span>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Comentário</label>
                <textarea wire:model="checkinComment" rows="3"
                    placeholder="O que impulsionou ou bloqueou o progresso?"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 resize-none transition"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-between items-center gap-3 px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('checkinModal', false)"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Cancelar
            </button>
            <button wire:click="saveCheckin" wire:loading.attr="disabled" wire:target="saveCheckin"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition cursor-pointer
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       shadow-md shadow-blue-500/20 disabled:opacity-60">
                <x-lucide-zap class="w-3.5 h-3.5" wire:loading.remove wire:target="saveCheckin" />
                <span wire:loading.remove wire:target="saveCheckin">Registrar Check-in</span>
                <span wire:loading wire:target="saveCheckin">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

