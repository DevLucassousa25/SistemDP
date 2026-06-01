{{-- ═══════════════════════════════════════════════════════════════════════
     MÓDULO DESLIGAMENTOS — Modais + Drawer
═══════════════════════════════════════════════════════════════════════ --}}

{{-- ── Modal: Criar / Editar Desligamento ─────────────────────────── --}}
<div x-data x-show="$wire.demModal" x-cloak style="display:none"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('demModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-xl flex flex-col" @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 shrink-0 rounded-t-2xl">
            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center shrink-0">
                    <x-lucide-user-minus class="w-3.5 h-3.5 text-white" />
                </span>
                {{ $demEditId ? 'Editar Desligamento' : 'Iniciar Desligamento' }}
            </h3>
            <button wire:click="$set('demModal', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-5 overflow-y-auto max-h-[75vh]">

            {{-- Card de alerta --}}
            @if (!$demEditId)
            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50">
                <x-lucide-triangle-alert class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                <p class="text-xs text-amber-700 dark:text-amber-400 lato-regular leading-relaxed">
                    Ao iniciar, será criado automaticamente um checklist padrão de offboarding com 12 itens.
                </p>
            </div>
            @endif

            {{-- Colaborador --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">
                    Colaborador <span class="text-rose-500">*</span>
                </label>

                @php
                    $colabOptions = $this->funcionariosAtivos->map(fn($f) => [
                        'id'       => $f->id,
                        'name'     => $f->name,
                        'position' => $f->position ?? '',
                        'initials' => collect(explode(' ', $f->name))->filter()->map(fn($w) => strtoupper($w[0]))->take(2)->implode(''),
                        'color'    => match(abs(crc32($f->name)) % 8) {
                            0 => 'bg-rose-500',   1 => 'bg-violet-500', 2 => 'bg-sky-500',
                            3 => 'bg-emerald-500',4 => 'bg-amber-500',  5 => 'bg-pink-500',
                            6 => 'bg-indigo-500', default => 'bg-teal-500',
                        },
                    ])->values()->toJson(JSON_HEX_APOS | JSON_HEX_QUOT);

                    $selectedName = '';
                    $selectedPosition = '';
                    $selectedInitials = '';
                    $selectedColor = 'bg-slate-400';
                    if ($demUserId) {
                        $sel = $this->funcionariosAtivos->firstWhere('id', $demUserId);
                        if ($sel) {
                            $selectedName = $sel->name;
                            $selectedPosition = $sel->position ?? '';
                            $selectedInitials = collect(explode(' ', $sel->name))->filter()->map(fn($w) => strtoupper($w[0]))->take(2)->implode('');
                            $selectedColor = match(abs(crc32($sel->name)) % 8) {
                                0 => 'bg-rose-500',   1 => 'bg-violet-500', 2 => 'bg-sky-500',
                                3 => 'bg-emerald-500',4 => 'bg-amber-500',  5 => 'bg-pink-500',
                                6 => 'bg-indigo-500', default => 'bg-teal-500',
                            };
                        }
                    }
                @endphp

                <div x-data="{
                        open: false,
                        search: '',
                        selected: { id: '{{ $demUserId }}', name: @js($selectedName), position: @js($selectedPosition), initials: @js($selectedInitials), color: @js($selectedColor) },
                        options: {{ $colabOptions }},
                        get filtered() {
                            if (!this.search) return this.options;
                            const q = this.search.toLowerCase();
                            return this.options.filter(o => o.name.toLowerCase().includes(q) || o.position.toLowerCase().includes(q));
                        },
                        select(opt) {
                            this.selected = opt;
                            this.open = false;
                            this.search = '';
                            $wire.set('demUserId', opt.id);
                        },
                        clear() {
                            this.selected = { id: '', name: '', position: '', initials: '', color: 'bg-slate-400' };
                            $wire.set('demUserId', '');
                        }
                    }"
                     @click.outside="open = false"
                     class="relative"
                     @if ($demEditId) x-init="$el.querySelectorAll('button,input').forEach(el => el.disabled = true)" @endif>

                    {{-- Trigger --}}
                    <button type="button" @click="open = !open"
                            class="cursor-pointer w-full flex items-center gap-3 px-3 py-2.5 border rounded-xl bg-slate-50 dark:bg-slate-900 text-sm transition
                                   focus:outline-none focus:ring-2 focus:ring-rose-400/30
                                   {{ $demEditId ? 'opacity-60 cursor-not-allowed border-slate-200 dark:border-slate-600' : 'border-slate-200 dark:border-slate-600 hover:border-rose-300 dark:hover:border-rose-700' }}"
                            :class="open ? 'border-rose-400 ring-2 ring-rose-400/20' : ''"
                            @if ($demEditId) disabled @endif>
                        <template x-if="selected.id">
                            <span :class="selected.color" class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs lato-bold shrink-0"
                                  x-text="selected.initials"></span>
                        </template>
                        <template x-if="!selected.id">
                            <span class="w-7 h-7 rounded-lg bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                <svg class="w-3.5 h-3.5 text-slate-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                            </span>
                        </template>
                        <span class="flex-1 text-left truncate">
                            <template x-if="selected.id">
                                <span>
                                    <span class="text-slate-800 dark:text-slate-100 lato-bold" x-text="selected.name"></span>
                                    <template x-if="selected.position">
                                        <span class="text-slate-400 text-xs ml-1" x-text="'· ' + selected.position"></span>
                                    </template>
                                </span>
                            </template>
                            <template x-if="!selected.id">
                                <span class="text-slate-400">Selecione o colaborador...</span>
                            </template>
                        </span>
                        <template x-if="selected.id && !{{ $demEditId ? 'true' : 'false' }}">
                            <span @click.stop="clear()" class="p-0.5 rounded-md hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 transition">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/></svg>
                            </span>
                        </template>
                        <svg class="w-4 h-4 text-slate-400 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>

                    {{-- Dropdown --}}
                    <div x-show="open" x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute z-50 w-full mt-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl overflow-hidden">

                        {{-- Search --}}
                        <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-2 px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-600 focus-within:border-rose-400 focus-within:ring-1 focus-within:ring-rose-400/20 transition">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg>
                                <input x-model="search" type="text" placeholder="Buscar colaborador..."
                                       x-ref="searchInput"
                                       @keydown.escape="open = false"
                                       x-init="$watch('open', v => v && $nextTick(() => $refs.searchInput.focus()))"
                                       class="flex-1 bg-transparent text-xs text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none" />
                                <template x-if="search">
                                    <button @click="search = ''" class="text-slate-400 hover:text-slate-600 transition">
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/></svg>
                                    </button>
                                </template>
                            </div>
                        </div>

                        {{-- List --}}
                        <ul class="max-h-52 overflow-y-auto py-1">
                            <template x-if="filtered.length === 0">
                                <li class="px-3 py-4 text-center text-xs text-slate-400">Nenhum colaborador encontrado</li>
                            </template>
                            <template x-for="opt in filtered" :key="opt.id">
                                <li @click="select(opt)"
                                    class="flex items-center gap-3 px-3 py-2.5 cursor-pointer hover:bg-rose-50 dark:hover:bg-rose-900/10 transition group"
                                    :class="selected.id == opt.id ? 'bg-rose-50 dark:bg-rose-900/10' : ''">
                                    <span :class="opt.color" class="w-7 h-7 rounded-lg flex items-center justify-center text-white text-xs lato-bold shrink-0"
                                          x-text="opt.initials"></span>
                                    <span class="flex-1 min-w-0">
                                        <span class="block text-sm text-slate-800 dark:text-slate-100 lato-bold truncate" x-text="opt.name"></span>
                                        <span class="block text-xs text-slate-400 truncate" x-text="opt.position" x-show="opt.position"></span>
                                    </span>
                                    <template x-if="selected.id == opt.id">
                                        <svg class="w-4 h-4 text-rose-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                    </template>
                                </li>
                            </template>
                        </ul>

                        {{-- Count --}}
                        <div class="px-3 py-1.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                            <span class="text-xs text-slate-400" x-text="filtered.length + ' colaborador(es)'"></span>
                        </div>
                    </div>
                </div>
                @error('demUserId') <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1"><x-lucide-circle-alert class="w-3 h-3" />{{ $message }}</p> @enderror
            </div>

            {{-- Tipo — cards clicáveis --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">
                    Tipo de Desligamento <span class="text-rose-500">*</span>
                </label>
                @php
                    $tipoIcons = [
                        'voluntario'      => 'log-out',
                        'sem_justa_causa' => 'briefcase',
                        'com_justa_causa' => 'shield-x',
                        'acordo_mutuo'    => 'handshake',
                        'aposentadoria'   => 'sun',
                    ];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach (\App\Models\RhDesligamento::$tipoLabels as $tk => $tv)
                    <button wire:click="$set('demTipo', '{{ $tk }}')" type="button"
                            class="cursor-pointer flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 text-center transition
                                   {{ $demTipo === $tk
                                      ? 'border-rose-500 bg-rose-50 dark:bg-rose-900/20'
                                      : 'border-slate-200 dark:border-slate-600 hover:border-rose-300 dark:hover:border-rose-700 bg-white dark:bg-slate-900' }}">
                        <x-dynamic-component :component="'lucide-' . ($tipoIcons[$tk] ?? 'circle')"
                                             class="w-4 h-4 {{ $demTipo === $tk ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}" />
                        <span class="text-xs lato-bold leading-tight {{ $demTipo === $tk ? 'text-rose-700 dark:text-rose-300' : 'text-slate-600 dark:text-slate-400' }}">{{ $tv }}</span>
                    </button>
                    @endforeach
                </div>
                @error('demTipo') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Datas --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Datas</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Data do Aviso</label>
                        <input wire:model="demDataAviso" type="date"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-400/30 focus:border-rose-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Último Dia <span class="text-rose-500">*</span></label>
                        <input wire:model="demDataUltimoDia" type="date"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-400/30 focus:border-rose-400 transition" />
                        @error('demDataUltimoDia') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Aviso Prévio --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Aviso Prévio</label>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex gap-2">
                        @foreach (['trabalhado' => 'Trabalhado', 'indenizado' => 'Indenizado'] as $apv => $apl)
                        <button wire:click="$set('demAvisoPrevioTipo', '{{ $apv }}')" type="button"
                                class="cursor-pointer flex-1 py-2.5 rounded-xl border-2 text-xs lato-bold transition
                                       {{ $demAvisoPrevioTipo === $apv
                                          ? 'border-rose-500 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300'
                                          : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-rose-300' }}">
                            {{ $apl }}
                        </button>
                        @endforeach
                    </div>
                    <div>
                        <div class="relative">
                            <input wire:model="demAvisoPrevioDias" type="number" min="0" max="90"
                                   class="w-full px-3 py-2.5 pr-10 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-400/30 transition" />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">dias</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Observações --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Observações internas</label>
                <textarea wire:model="demObservacoes" rows="2" placeholder="Contexto, acordos, motivos internos..."
                          class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-400/30 focus:border-rose-400 transition resize-none placeholder-slate-400"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/30 shrink-0">
            <button wire:click="$set('demModal', false)" type="button"
                    class="cursor-pointer px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition">
                Cancelar
            </button>
            <button wire:click="saveDem" type="button" wire:loading.attr="disabled" wire:target="saveDem"
                    class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-500 text-white text-sm lato-bold hover:from-rose-700 hover:to-red-600 transition shadow-sm shadow-rose-200 dark:shadow-rose-900/30 disabled:opacity-60">
                <span wire:loading.remove wire:target="saveDem" class="flex items-center gap-2">
                    <x-lucide-user-minus class="w-4 h-4" />
                    {{ $demEditId ? 'Salvar Alterações' : 'Iniciar Processo' }}
                </span>
                <span wire:loading wire:target="saveDem" class="flex items-center gap-2">
                    <x-lucide-loader-circle class="w-4 h-4 animate-spin" /> Salvando...
                </span>
            </button>
        </div>
    </div>
</div>

