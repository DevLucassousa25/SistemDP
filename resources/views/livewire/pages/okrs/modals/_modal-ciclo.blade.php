{{-- ── Modal: Ciclo ─────────────────────────────────────────────────── --}}
@if ($cycleModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('cycleModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-md
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[92vh] sm:max-h-[88vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                {{ $cycleMode === 'create' ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }}">
                        <x-lucide-refresh-cw class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
                            {{ $cycleMode === 'create' ? 'Novo Ciclo' : 'Editar Ciclo' }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $cycleMode === 'create' ? 'Defina o período e configurações' : 'Atualize os dados do ciclo' }}
                        </p>
                    </div>
                </div>
                <button wire:click="$set('cycleModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-4">
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Nome do ciclo *</label>
                <input wire:model="cycleName" type="text" placeholder="Ex: Q2 2025, Semestral 2025..."
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                @error('cycleName') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Início *</label>
                    <input wire:model="cycleStart" type="date"
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Fim *</label>
                    <input wire:model="cycleEnd" type="date"
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                </div>
            </div>
            {{-- Status com dot colorido --}}
            <div x-data="{
                open: false,
                val: $wire.entangle('cycleStatus'),
                opts: [
                    { value: 'planning', label: 'Planejamento', dot: 'bg-amber-400',   bg: 'bg-amber-50 dark:bg-amber-900/30',   text: 'text-amber-700 dark:text-amber-400' },
                    { value: 'active',   label: 'Ativo',         dot: 'bg-emerald-400', bg: 'bg-emerald-50 dark:bg-emerald-900/30', text: 'text-emerald-700 dark:text-emerald-400' },
                    { value: 'closed',   label: 'Encerrado',     dot: 'bg-slate-400',   bg: 'bg-slate-100 dark:bg-slate-800',     text: 'text-slate-500 dark:text-slate-400' },
                ],
                get selected() { return this.opts.find(o => o.value === this.val) ?? this.opts[0] }
            }" class="relative">
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                <button @click="open = !open" type="button"
                    class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm
                           bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white cursor-pointer
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left">
                    <span class="w-2 h-2 rounded-full shrink-0 transition-colors" :class="selected.dot"></span>
                    <span class="flex-1 font-medium" x-text="selected.label"></span>
                    <x-lucide-chevron-down class="w-4 h-4 text-slate-400 shrink-0 transition-transform" ::class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-75"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95"
                     class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800
                            rounded-xl shadow-lg border border-slate-200 dark:border-slate-700 py-1.5 overflow-hidden">
                    <template x-for="opt in opts" :key="opt.value">
                        <button @click="val = opt.value; open = false" type="button"
                            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm cursor-pointer transition"
                            :class="val === opt.value
                                ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                            <span class="w-2 h-2 rounded-full shrink-0" :class="opt.dot"></span>
                            <span x-text="opt.label" class="flex-1 text-left"></span>
                            <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" x-show="val === opt.value" />
                        </button>
                    </template>
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Descrição</label>
                <textarea wire:model="cycleDescription" rows="3" placeholder="Opcional..."
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 resize-none transition"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-between items-center gap-3 px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('cycleModal', false)"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Cancelar
            </button>
            <button wire:click="saveCycle" wire:loading.attr="disabled" wire:target="saveCycle"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition cursor-pointer
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       shadow-md shadow-blue-500/20 disabled:opacity-60">
                <span wire:loading.remove wire:target="saveCycle">
                    {{ $cycleMode === 'create' ? 'Criar Ciclo' : 'Salvar Alterações' }}
                </span>
                <span wire:loading wire:target="saveCycle">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

