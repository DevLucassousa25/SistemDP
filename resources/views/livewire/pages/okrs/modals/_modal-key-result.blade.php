{{-- ── Modal: Key Result ─────────────────────────────────────────────── --}}
@if ($krModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('krModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[92vh] sm:max-h-[88vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                {{ $krMode === 'create' ? 'bg-gradient-to-br from-teal-500 to-emerald-600' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }}">
                        <x-lucide-bar-chart-2 class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
                            {{ $krMode === 'create' ? 'Novo Key Result' : 'Editar Key Result' }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $krMode === 'create' ? 'Como você vai medir o sucesso?' : 'Atualize os dados do resultado-chave' }}
                        </p>
                    </div>
                </div>
                <button wire:click="$set('krModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-5">

            {{-- Título --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Título do Key Result *</label>
                <input wire:model="krTitle" type="text" placeholder="Ex: Aumentar NPS para 70 pontos"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                @error('krTitle') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tipo de medição: pills --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Tipo de medição</label>
                <div class="grid grid-cols-4 gap-2">

                    {{-- Numérico --}}
                    <button wire:click="$set('krType', 'numeric')" type="button"
                        class="flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 transition cursor-pointer
                               {{ $krType === 'numeric'
                                   ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-indigo-200 hover:bg-indigo-50/40' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                    {{ $krType === 'numeric' ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-hash class="w-3.5 h-3.5 {{ $krType === 'numeric' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-[10px] font-semibold leading-tight">Numérico</span>
                    </button>

                    {{-- Percentual --}}
                    <button wire:click="$set('krType', 'percentage')" type="button"
                        class="flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 transition cursor-pointer
                               {{ $krType === 'percentage'
                                   ? 'border-indigo-400 bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-violet-200 hover:bg-violet-50/40' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                    {{ $krType === 'percentage' ? 'bg-violet-100 dark:bg-violet-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-percent class="w-3.5 h-3.5 {{ $krType === 'percentage' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-[10px] font-semibold leading-tight">Percentual</span>
                    </button>

                    {{-- Monetário --}}
                    <button wire:click="$set('krType', 'currency')" type="button"
                        class="flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 transition cursor-pointer
                               {{ $krType === 'currency'
                                   ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-emerald-200 hover:bg-emerald-50/40' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                    {{ $krType === 'currency' ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-dollar-sign class="w-3.5 h-3.5 {{ $krType === 'currency' ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-[10px] font-semibold leading-tight">Monetário</span>
                    </button>

                    {{-- Booleano --}}
                    <button wire:click="$set('krType', 'boolean')" type="button"
                        class="flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 transition cursor-pointer
                               {{ $krType === 'boolean'
                                   ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-amber-200 hover:bg-amber-50/40' }}">
                        <div class="w-7 h-7 rounded-lg flex items-center justify-center
                                    {{ $krType === 'boolean' ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-toggle-left class="w-3.5 h-3.5 {{ $krType === 'boolean' ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-[10px] font-semibold leading-tight">Sim / Não</span>
                    </button>

                </div>
            </div>

            {{-- Unidade (apenas para numérico) --}}
            @if ($krType === 'numeric')
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                        Unidade <span class="font-normal text-slate-400">(opcional)</span>
                    </label>
                    <div class="relative">
                        <x-lucide-tag class="absolute left-3.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                        <input wire:model="krUnit" type="text" placeholder="usuários, vendas, tickets..."
                            class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                                   rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                    </div>
                </div>
            @endif

            {{-- Valores numéricos --}}
            @if ($krType !== 'boolean')
                <div class="bg-slate-50 dark:bg-slate-700/30 rounded-xl p-4">
                    <p class="text-[10px] font-semibold text-slate-400 uppercase tracking-wide mb-3">Valores</p>
                    <div class="grid grid-cols-3 gap-3">
                        @foreach ([
                            ['krInitial', 'Inicial',  '0',   'from-slate-400 to-slate-500'],
                            ['krTarget',  'Meta',     '100', 'from-indigo-500 to-violet-600'],
                            ['krCurrent', 'Atual',    '0',   'from-teal-400 to-emerald-500'],
                        ] as [$field, $label, $ph, $grad])
                            <div>
                                <div class="flex items-center gap-1.5 mb-1.5">
                                    <span class="w-2 h-2 rounded-full bg-gradient-to-br {{ $grad }} shrink-0"></span>
                                    <label class="text-xs font-semibold text-slate-500 dark:text-slate-400">{{ $label }}</label>
                                </div>
                                <input wire:model="{{ $field }}" type="number" step="any" placeholder="{{ $ph }}"
                                    class="w-full px-3 py-2.5 text-sm font-semibold text-center
                                           bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                                           rounded-xl text-slate-800 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                            </div>
                        @endforeach
                    </div>

                    {{-- Sufixo da unidade --}}
                    @if ($krType === 'percentage')
                        <p class="text-[10px] text-slate-400 mt-2 text-center">Valores em percentual (%)</p>
                    @elseif ($krType === 'currency')
                        <p class="text-[10px] text-slate-400 mt-2 text-center">Valores em reais (R$)</p>
                    @endif
                </div>
            @else
                {{-- Booleano --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Situação atual</label>
                    <div class="grid grid-cols-2 gap-2">
                        <button wire:click="$set('krCurrent', 0)" type="button"
                            class="flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 transition cursor-pointer text-sm font-semibold
                                   {{ (int)$krCurrent === 0
                                       ? 'border-rose-400 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300'
                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-slate-300' }}">
                            <x-lucide-x class="w-4 h-4" /> Não concluído
                        </button>
                        <button wire:click="$set('krCurrent', 1)" type="button"
                            class="flex items-center justify-center gap-2 py-2.5 rounded-xl border-2 transition cursor-pointer text-sm font-semibold
                                   {{ (int)$krCurrent === 1
                                       ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300'
                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-400 hover:border-slate-300' }}">
                            <x-lucide-check class="w-4 h-4" /> Concluído
                        </button>
                    </div>
                </div>
            @endif

            {{-- Responsável com busca + Data limite --}}
            <div class="grid grid-cols-2 gap-3">

                {{-- Responsável --}}
                <div x-data="{
                    open: false,
                    query: '',
                    val: $wire.entangle('krOwnerId'),
                    opts: [
                        { value: '', name: 'Nenhum', initials: '' },
                        @foreach ($this->users as $u)
                        { value: {{ $u->id }}, name: '{{ addslashes($u->name) }}', initials: '{{ strtoupper(mb_substr($u->name, 0, 1)) }}{{ strtoupper(mb_substr(explode(" ", trim($u->name))[1] ?? "", 0, 1)) }}' },
                        @endforeach
                    ],
                    get filtered() { return !this.query ? this.opts : this.opts.filter(o => o.name.toLowerCase().includes(this.query.toLowerCase())) },
                    get selected() { return this.opts.find(o => String(o.value) === String(this.val)) ?? this.opts[0] },
                    openAndFocus() { this.open = true; this.$nextTick(() => this.$refs.krOwnerSearch?.focus()) }
                }" class="relative" @keydown.escape="open = false">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Responsável</label>
                    <button @click="openAndFocus()" type="button"
                        class="w-full flex items-center gap-2 px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60
                               border border-slate-200 dark:border-slate-600 rounded-xl cursor-pointer
                               focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left">
                        <template x-if="selected.value !== ''">
                            <span class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600
                                         flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                  x-text="selected.initials"></span>
                        </template>
                        <template x-if="selected.value === ''">
                            <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                <x-lucide-user class="w-3 h-3 text-slate-400" />
                            </div>
                        </template>
                        <span class="flex-1 truncate text-sm"
                              :class="selected.value === '' ? 'text-slate-400' : 'font-medium text-slate-800 dark:text-white'"
                              x-text="selected.name"></span>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" ::class="open ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="open" @click.outside="open = false; query = ''"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute z-30 left-0 w-full mt-1.5 bg-white dark:bg-slate-800
                                rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-3 pt-3 pb-2 border-b border-slate-100 dark:border-slate-700">
                            <div class="relative">
                                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                <input x-ref="krOwnerSearch" x-model="query" type="text" placeholder="Buscar..."
                                    class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700/60
                                           border border-slate-200 dark:border-slate-600 rounded-lg
                                           text-slate-800 dark:text-white placeholder-slate-400
                                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                            </div>
                        </div>
                        <div class="max-h-44 overflow-y-auto py-1.5">
                            <template x-for="opt in filtered" :key="opt.value">
                                <button @click="val = opt.value; open = false; query = ''" type="button"
                                    class="w-full flex items-center gap-2 px-3.5 py-2 text-sm cursor-pointer transition"
                                    :class="String(val) === String(opt.value)
                                        ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                    <template x-if="opt.value !== ''">
                                        <span class="w-6 h-6 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600
                                                     flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                              x-text="opt.initials"></span>
                                    </template>
                                    <template x-if="opt.value === ''">
                                        <div class="w-6 h-6 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                            <x-lucide-user class="w-3 h-3 text-slate-400" />
                                        </div>
                                    </template>
                                    <span x-text="opt.name" class="flex-1 text-left truncate"></span>
                                    <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0" x-show="String(val) === String(opt.value)" />
                                </button>
                            </template>
                            <div x-show="filtered.length === 0" class="px-4 py-4 text-center text-xs text-slate-400">
                                Nenhum usuário encontrado
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Data limite --}}
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Data limite</label>
                    <div class="relative">
                        <x-lucide-calendar class="absolute left-3.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                        <input wire:model="krDueDate" type="date"
                            class="w-full pl-9 pr-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60
                                   border border-slate-200 dark:border-slate-600 rounded-xl
                                   text-slate-800 dark:text-white
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition cursor-pointer" />
                    </div>
                </div>
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Descrição <span class="font-normal text-slate-400">(opcional)</span></label>
                <textarea wire:model="krDescription" rows="2" placeholder="Contexto adicional..."
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 resize-none transition"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-between items-center gap-3 px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('krModal', false)"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Cancelar
            </button>
            <button wire:click="saveKr" wire:loading.attr="disabled" wire:target="saveKr"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition cursor-pointer
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       shadow-md shadow-blue-500/20 disabled:opacity-60">
                <span wire:loading.remove wire:target="saveKr">
                    {{ $krMode === 'create' ? 'Criar Key Result' : 'Salvar Alterações' }}
                </span>
                <span wire:loading wire:target="saveKr">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

