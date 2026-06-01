{{-- ── Modal: Objetivo ───────────────────────────────────────────────── --}}
@if ($objectiveModal)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('objectiveModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[92vh] sm:max-h-[88vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                {{ $objectiveMode === 'create' ? 'bg-gradient-to-br from-blue-500 to-indigo-600' : 'bg-gradient-to-br from-blue-500 to-indigo-600' }}">
                        <x-lucide-target class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">
                            {{ $objectiveMode === 'create' ? 'Novo Objetivo' : 'Editar Objetivo' }}
                        </h3>
                        <p class="text-[11px] text-slate-400 mt-0.5">
                            {{ $objectiveMode === 'create' ? 'Defina o que você quer alcançar' : 'Atualize os dados do objetivo' }}
                        </p>
                    </div>
                </div>
                <button wire:click="$set('objectiveModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body --}}
        <div class="flex-1 overflow-y-auto px-5 py-5 space-y-5">

            {{-- Banner de escopo para gerentes --}}
            @if ($isManager)
            <div class="flex items-start gap-3 bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700/40 rounded-xl px-4 py-3">
                <x-lucide-shield class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
                <div>
                    <p class="text-xs font-semibold text-indigo-700 dark:text-indigo-300">Escopo restrito ao seu departamento</p>
                    <p class="text-[11px] text-indigo-500 dark:text-indigo-400 mt-0.5 leading-relaxed">
                        Você pode criar OKRs do tipo <strong>Departamento</strong> ou <strong>Individual</strong> somente para membros do seu departamento.
                    </p>
                </div>
            </div>
            @endif

            {{-- Título --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Título do Objetivo *</label>
                <input wire:model="objTitle" type="text" placeholder="O que você quer alcançar?"
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                @error('objTitle') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Descrição --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Descrição</label>
                <textarea wire:model="objDescription" rows="2" placeholder="Contexto adicional (opcional)..."
                    class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                           rounded-xl text-slate-800 dark:text-white placeholder-slate-400
                           focus:outline-none focus:ring-2 focus:ring-indigo-500/40 resize-none transition"></textarea>
            </div>

            {{-- Nível: pills visuais --}}
            <div>
                <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-2">Nível *</label>
                <div class="grid gap-2
                    {{ ($isRhAdmin) ? 'grid-cols-3' : ($isManager ? 'grid-cols-2' : 'grid-cols-1') }}">
                    @if ($isRhAdmin)
                        <button wire:click="$set('objLevel', 'company')" type="button"
                            class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition cursor-pointer
                                   {{ $objLevel === 'company'
                                       ? 'border-indigo-400 bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300'
                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-500 dark:text-slate-400 hover:border-violet-300 hover:bg-violet-50/50' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                        {{ $objLevel === 'company' ? 'bg-violet-100 dark:bg-violet-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                                <x-lucide-building-2 class="w-4 h-4 {{ $objLevel === 'company' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}" />
                            </div>
                            <span class="text-xs font-semibold">Empresa</span>
                        </button>
                    @endif
                    @if ($isRhAdmin || $isManager)
                        <button wire:click="$set('objLevel', 'department')" type="button"
                            class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition cursor-pointer
                                   {{ $objLevel === 'department'
                                       ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300'
                                       : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-500 dark:text-slate-400 hover:border-indigo-300 hover:bg-indigo-50/50' }}">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                        {{ $objLevel === 'department' ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                                <x-lucide-users class="w-4 h-4 {{ $objLevel === 'department' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400' }}" />
                            </div>
                            <span class="text-xs font-semibold">Departamento</span>
                        </button>
                    @endif
                    <button wire:click="$set('objLevel', 'individual')" type="button"
                        class="flex flex-col items-center gap-1.5 p-3 rounded-xl border-2 transition cursor-pointer
                               {{ $objLevel === 'individual'
                                   ? 'border-teal-400 bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300'
                                   : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/40 text-slate-500 dark:text-slate-400 hover:border-teal-300 hover:bg-teal-50/50' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center
                                    {{ $objLevel === 'individual' ? 'bg-teal-100 dark:bg-teal-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-lucide-user class="w-4 h-4 {{ $objLevel === 'individual' ? 'text-teal-600 dark:text-teal-400' : 'text-slate-400' }}" />
                        </div>
                        <span class="text-xs font-semibold">Individual</span>
                    </button>
                </div>
            </div>

            {{-- Status + Responsável lado a lado --}}
            <div class="grid grid-cols-2 gap-3">

                {{-- Status com dot --}}
                <div x-data="{
                    open: false,
                    val: $wire.entangle('objStatus'),
                    opts: [
                        { value: 'not_started', label: 'Não iniciado', dot: 'bg-slate-400' },
                        { value: 'on_track',    label: 'No prazo',     dot: 'bg-emerald-400' },
                        { value: 'at_risk',     label: 'Em risco',     dot: 'bg-amber-400' },
                        { value: 'behind',      label: 'Atrasado',     dot: 'bg-rose-400' },
                        { value: 'completed',   label: 'Concluído',    dot: 'bg-blue-400' },
                    ],
                    get selected() { return this.opts.find(o => o.value === this.val) ?? this.opts[0] }
                }" class="relative">
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                    <button @click="open = !open" type="button"
                        class="w-full flex items-center gap-2 px-3.5 py-2.5 text-sm
                               bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl text-slate-800 dark:text-white cursor-pointer
                               focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left">
                        <span class="w-2 h-2 rounded-full shrink-0" :class="selected.dot"></span>
                        <span class="flex-1 font-medium truncate text-sm" x-text="selected.label"></span>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" ::class="open ? 'rotate-180' : ''" />
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
                                class="w-full flex items-center gap-2.5 px-3.5 py-2 text-sm cursor-pointer transition"
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

                {{-- Responsável com avatar + busca --}}
                <div x-data="{
                    open: false,
                    query: '',
                    val: $wire.entangle('objOwnerId'),
                    opts: [
                        { value: '', name: 'Nenhum', initials: '' },
                        @foreach ($this->users as $u)
                        { value: {{ $u->id }}, name: '{{ addslashes($u->name) }}', initials: '{{ strtoupper(mb_substr($u->name, 0, 1)) }}{{ strtoupper(mb_substr(explode(" ", trim($u->name))[1] ?? "", 0, 1)) }}' },
                        @endforeach
                    ],
                    get filtered() {
                        if (!this.query) return this.opts;
                        return this.opts.filter(o => o.name.toLowerCase().includes(this.query.toLowerCase()));
                    },
                    get selected() { return this.opts.find(o => String(o.value) === String(this.val)) ?? this.opts[0] },
                    openAndFocus() { this.open = true; this.$nextTick(() => this.$refs.ownerSearch?.focus()) }
                }" class="relative" @keydown.escape="open = false">

                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">Responsável</label>

                    {{-- Trigger --}}
                    <button @click="openAndFocus()" type="button"
                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm
                               bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left">
                        <template x-if="selected.value !== ''">
                            <span class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600
                                         flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                  x-text="selected.initials"></span>
                        </template>
                        <template x-if="selected.value === ''">
                            <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                <x-lucide-user class="w-3.5 h-3.5 text-slate-400" />
                            </div>
                        </template>
                        <span class="flex-1 truncate text-sm"
                              :class="selected.value === '' ? 'text-slate-400' : 'font-medium text-slate-800 dark:text-white'"
                              x-text="selected.name"></span>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform"
                                               ::class="open ? 'rotate-180' : ''" />
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" @click.outside="open = false; query = ''"
                         x-transition:enter="transition ease-out duration-100"
                         x-transition:enter-start="opacity-0 -translate-y-1"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-transition:leave="transition ease-in duration-75"
                         x-transition:leave-start="opacity-100 translate-y-0"
                         x-transition:leave-end="opacity-0 -translate-y-1"
                         class="absolute z-30 right-0 w-full mt-1.5 bg-white dark:bg-slate-800
                                rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                        {{-- Busca --}}
                        <div class="px-3 pt-3 pb-2 border-b border-slate-100 dark:border-slate-700">
                            <div class="relative">
                                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                <input x-ref="ownerSearch"
                                       x-model="query"
                                       type="text"
                                       placeholder="Buscar responsável..."
                                       class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700/60
                                              border border-slate-200 dark:border-slate-600 rounded-lg
                                              text-slate-800 dark:text-white placeholder-slate-400
                                              focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                                <button x-show="query" @click="query = ''; $refs.ownerSearch.focus()" type="button"
                                    class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                    <x-lucide-x class="w-3 h-3" />
                                </button>
                            </div>
                        </div>

                        {{-- Lista --}}
                        <div class="max-h-48 overflow-y-auto py-1.5">
                            <template x-for="opt in filtered" :key="opt.value">
                                <button @click="val = opt.value; open = false; query = ''" type="button"
                                    class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm cursor-pointer transition"
                                    :class="String(val) === String(opt.value)
                                        ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                        : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                    <template x-if="opt.value !== ''">
                                        <span class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-indigo-600
                                                     flex items-center justify-center text-white text-[10px] font-bold shrink-0"
                                              x-text="opt.initials"></span>
                                    </template>
                                    <template x-if="opt.value === ''">
                                        <div class="w-7 h-7 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                            <x-lucide-user class="w-3.5 h-3.5 text-slate-400" />
                                        </div>
                                    </template>
                                    <span x-text="opt.name" class="flex-1 text-left truncate"></span>
                                    <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0"
                                                    x-show="String(val) === String(opt.value)" />
                                </button>
                            </template>

                            {{-- Sem resultados --}}
                            <div x-show="filtered.length === 0"
                                 class="px-4 py-5 text-center text-xs text-slate-400">
                                <x-lucide-user-x class="w-5 h-5 mx-auto mb-1.5 opacity-40" />
                                Nenhum usuário encontrado
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Departamento com busca (condicional) --}}
            @if ($objLevel === 'department')
                @if ($isManager)
                    {{-- Gerente: departamento travado (apenas o dele) --}}
                    <div>
                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                            Departamento
                        </label>
                        <div class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl border border-indigo-200 dark:border-indigo-700/50 bg-indigo-50 dark:bg-indigo-900/10">
                            <div class="w-7 h-7 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center shrink-0">
                                <x-lucide-building-2 class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
                            </div>
                            <span class="flex-1 text-sm font-medium text-indigo-700 dark:text-indigo-300 truncate">
                                {{ $this->departments->first()?->name ?? 'Seu departamento' }}
                            </span>
                            <x-lucide-lock class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                        </div>
                    </div>
                @else
                    {{-- RH/Admin: dropdown completo --}}
                    <div x-data="{
                        open: false,
                        query: '',
                        val: $wire.entangle('objDepartmentId'),
                        opts: [
                            { value: '', name: 'Selecione um departamento' },
                            @foreach ($this->departments as $dept)
                            { value: {{ $dept->id }}, name: '{{ addslashes($dept->name) }}' },
                            @endforeach
                        ],
                        get filtered() {
                            if (!this.query) return this.opts;
                            return this.opts.filter(o => o.name.toLowerCase().includes(this.query.toLowerCase()));
                        },
                        get selected() { return this.opts.find(o => String(o.value) === String(this.val)) ?? this.opts[0] },
                        openAndFocus() { this.open = true; this.$nextTick(() => this.$refs.search?.focus()) }
                    }" class="relative" @keydown.escape="open = false">

                        <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                            Departamento *
                        </label>

                        <button @click="openAndFocus()" type="button"
                            class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm
                                   bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                                   rounded-xl cursor-pointer focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition text-left"
                            :class="selected.value !== '' ? 'text-slate-800 dark:text-white' : 'text-slate-400'">
                            <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0 transition-colors"
                                 :class="selected.value !== '' ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                                <x-lucide-building-2 class="w-3.5 h-3.5 transition-colors"
                                                     ::class="selected.value !== '' ? 'text-indigo-600 dark:text-indigo-400' : 'text-slate-400'" />
                            </div>
                            <span class="flex-1 truncate font-medium" x-text="selected.name"></span>
                            <x-lucide-chevron-down class="w-4 h-4 text-slate-400 shrink-0 transition-transform"
                                                   ::class="open ? 'rotate-180' : ''" />
                        </button>

                        <div x-show="open" @click.outside="open = false; query = ''"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 -translate-y-1"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 -translate-y-1"
                             class="absolute z-30 w-full mt-1.5 bg-white dark:bg-slate-800
                                    rounded-xl shadow-xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="px-3 pt-3 pb-2 border-b border-slate-100 dark:border-slate-700">
                                <div class="relative">
                                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                    <input x-ref="search" x-model="query" type="text" placeholder="Buscar departamento..."
                                        class="w-full pl-8 pr-3 py-2 text-sm bg-slate-50 dark:bg-slate-700/60
                                               border border-slate-200 dark:border-slate-600 rounded-lg
                                               text-slate-800 dark:text-white placeholder-slate-400
                                               focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition" />
                                    <button x-show="query" @click="query = ''; $refs.search.focus()" type="button"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                        <x-lucide-x class="w-3 h-3" />
                                    </button>
                                </div>
                            </div>
                            <div class="max-h-48 overflow-y-auto py-1.5">
                                <template x-for="opt in filtered" :key="opt.value">
                                    <button @click="val = opt.value; open = false; query = ''" type="button"
                                        class="w-full flex items-center gap-2.5 px-3.5 py-2.5 text-sm cursor-pointer transition"
                                        :class="String(val) === String(opt.value)
                                            ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 font-semibold'
                                            : 'text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                        <div class="w-6 h-6 rounded-md flex items-center justify-center shrink-0 transition-colors"
                                             :class="String(val) === String(opt.value) ? 'bg-indigo-100 dark:bg-indigo-900/40' : 'bg-slate-100 dark:bg-slate-700'">
                                            <x-lucide-building-2 class="w-3 h-3"
                                                ::class="String(val) === String(opt.value) ? 'text-indigo-500' : 'text-slate-400'" />
                                        </div>
                                        <span x-text="opt.name" class="flex-1 text-left truncate"></span>
                                        <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0"
                                                        x-show="String(val) === String(opt.value)" />
                                    </button>
                                </template>
                                <div x-show="filtered.length === 0" class="px-4 py-5 text-center text-xs text-slate-400">
                                    <x-lucide-search class="w-5 h-5 mx-auto mb-1.5 opacity-40" />
                                    Nenhum departamento encontrado
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif

            {{-- Alinhar a objetivo pai --}}
            @if ($this->parentObjectives->isNotEmpty())
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1.5">
                        Objetivo pai
                        <span class="font-normal text-slate-400">(opcional)</span>
                    </label>
                    <select wire:model="objParentId"
                        class="w-full px-3.5 py-2.5 text-sm bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                               rounded-xl text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-indigo-500/40 cursor-pointer">
                        @foreach ($this->parentObjectives as $parent)
                            <option value="{{ $parent->id }}">[{{ ucfirst($parent->level) }}] {{ $parent->title }}</option>
                        @endforeach
                    </select>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-between items-center gap-3 px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('objectiveModal', false)"
                class="px-4 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Cancelar
            </button>
            <button wire:click="saveObjective" wire:loading.attr="disabled" wire:target="saveObjective"
                class="flex items-center gap-2 px-5 py-2.5 text-sm font-semibold text-white rounded-xl transition cursor-pointer
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       shadow-md shadow-blue-500/20 disabled:opacity-60">
                <x-lucide-check class="w-4 h-4" />
                {{ $objectiveMode === 'create' ? 'Confirmar' : 'Salvar Alterações' }}
            </button>
        </div>
    </div>
</div>
@endif
