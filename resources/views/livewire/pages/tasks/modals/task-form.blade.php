<div x-data="{ open: @entangle('open') }" x-effect="document.body.style.overflow = open ? 'hidden' : ''">
    @if ($open)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4" x-data="{
            assignSearch: '',
            assignOpen: false,
            assignedId: @entangle('assigned_to'),
            assignedLabel: '',
            users: @js($this->assignableUsers->map(fn($u) => ['id' => $u->id, 'name' => $u->name])),
            get filtered() {
                if (!this.assignSearch) return this.users;
                const q = this.assignSearch.toLowerCase();
                return this.users.filter(u => u.name.toLowerCase().includes(q));
            },
            select(u) { this.assignedId = u.id;
                this.assignedLabel = u.name;
                this.assignSearch = '';
                this.assignOpen = false; },
            clear() { this.assignedId = '';
                this.assignedLabel = '';
                this.assignSearch = '';
                this.assignOpen = false; },
            init() {
                this.$watch('assignedId', id => {
                    if (!id) { this.assignedLabel = ''; return; }
                    const u = this.users.find(u => u.id == id);
                    if (u) this.assignedLabel = u.name;
                });
                if (this.assignedId) {
                    const u = this.users.find(u => u.id == this.assignedId);
                    if (u) this.assignedLabel = u.name;
                }
            }
        }"
            @click.away="assignOpen = false">

            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="close"></div>

            <div
                class="relative bg-white dark:bg-slate-800 w-full sm:max-w-xl max-h-[96vh] sm:max-h-[88vh]
                        flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden">

                {{-- ── Header ── --}}
                <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-start justify-between">
                        <div class="flex items-center gap-3">
                            <div
                                class="w-10 h-10 rounded-xl flex items-center justify-center shadow-sm shrink-0
                                        {{ $editingId
                                            ? 'bg-gradient-to-br from-blue-500 to-blue-700'
                                            : 'bg-gradient-to-br from-emerald-500 to-emerald-700' }}">
                                @if ($editingId)
                                    <x-lucide-pencil class="w-4 h-4 text-white" />
                                @else
                                    <x-lucide-check-square class="w-4 h-4 text-white" />
                                @endif
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-800 dark:text-white lato-black leading-tight">
                                    {{ $editingId ? 'Editar Tarefa' : 'Nova Tarefa' }}
                                </h2>
                                <p class="text-[11px] text-slate-400 lato-regular mt-0.5">
                                    {{ $editingId ? 'Atualize os dados da tarefa' : 'Preencha as informações abaixo' }}
                                </p>
                            </div>
                        </div>
                        <button type="button" wire:click="close"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                                       hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                                       transition cursor-pointer shrink-0">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {{-- ── Corpo ── --}}
                <div class="flex-1 overflow-y-auto px-5 py-5 space-y-5">

                    {{-- Título --}}
                    <div class="space-y-1.5">
                        <label
                            class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1">
                            <x-lucide-type class="w-3.5 h-3.5" />
                            Título <span class="text-red-400">*</span>
                        </label>
                        <input type="text" wire:model="title" placeholder="Ex: Preparar relatório mensal..."
                            class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                      border text-slate-800 dark:text-white placeholder-slate-400
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition
                                      {{ $errors->has('title') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }}" />
                        @error('title')
                            <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                <x-lucide-circle-alert class="w-3 h-3" /> {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Descrição --}}
                    <div class="space-y-1.5">
                        <label
                            class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1">
                            <x-lucide-align-left class="w-3.5 h-3.5" />
                            Descrição
                            <span class="text-[10px] font-normal normal-case text-slate-400 ml-1">— opcional</span>
                        </label>
                        <textarea wire:model="description" rows="3"
                            placeholder="Adicione detalhes, contexto ou instruções sobre a tarefa..."
                            class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                         border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                         placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition resize-none"></textarea>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-100 dark:border-slate-700"></div>

                    {{-- Prazo + Prioridade --}}
                    <div class="grid grid-cols-2 gap-4">

                        {{-- Prazo --}}
                        <div class="space-y-1.5">
                            <label
                                class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1">
                                <x-lucide-calendar class="w-3.5 h-3.5" />
                                Prazo
                            </label>
                            <div class="relative">
                                <input type="date" wire:model="due_date"
                                    class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                              border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                              focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition" />
                            </div>
                        </div>

                        {{-- Prioridade --}}
                        <div class="space-y-1.5">
                            <label
                                class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1">
                                <x-lucide-flag class="w-3.5 h-3.5" />
                                Prioridade
                            </label>
                            <div class="flex gap-1.5">
                                @foreach ([
        'baixa' => ['Baixa', 'text-emerald-700 dark:text-emerald-400', 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-300 dark:border-emerald-700 ring-emerald-400', 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-emerald-300 hover:text-emerald-600'],
        'media' => ['Média', 'text-amber-700 dark:text-amber-400', 'bg-amber-50 dark:bg-amber-900/20 border-amber-300 dark:border-amber-700 ring-amber-400', 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-amber-300 hover:text-amber-600'],
        'alta' => ['Alta', 'text-red-700 dark:text-red-400', 'bg-red-50 dark:bg-red-900/20 border-red-300 dark:border-red-700 ring-red-400', 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-red-300 hover:text-red-600'],
    ] as $val => [$lbl, $activeText, $activeClass, $inactiveClass])
                                    <button type="button" wire:click="$set('priority', '{{ $val }}')"
                                        class="flex-1 py-2 text-xs lato-bold rounded-lg border transition cursor-pointer text-center
                                                   {{ $priority === $val ? $activeClass . ' ' . $activeText . ' ring-2 ring-offset-1' : $inactiveClass . ' bg-white dark:bg-slate-700/40' }}">
                                        {{ $lbl }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div class="space-y-1.5">
                        <label
                            class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1">
                            <x-lucide-activity class="w-3.5 h-3.5" />
                            Status
                        </label>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ([
        'pendente' => ['Pendente', 'bg-slate-50 dark:bg-slate-700 border-slate-300 dark:border-slate-500 text-slate-700 dark:text-slate-200 ring-slate-400', 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/50'],
        'em_andamento' => ['Em andamento', 'bg-blue-50 dark:bg-blue-900/20 border-blue-300 dark:border-blue-600 text-blue-700 dark:text-blue-300 ring-blue-400', 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:bg-blue-50 dark:hover:bg-blue-900/10 hover:text-blue-600'],
        'concluida' => ['Concluída', 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-300 dark:border-emerald-600 text-emerald-700 dark:text-emerald-300 ring-emerald-400', 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:bg-emerald-50 dark:hover:bg-emerald-900/10 hover:text-emerald-600'],
        'cancelada' => ['Cancelada', 'bg-rose-50 dark:bg-rose-900/20 border-rose-300 dark:border-rose-600 text-rose-700 dark:text-rose-300 ring-rose-400', 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:bg-rose-50 dark:hover:bg-rose-900/10 hover:text-rose-600'],
    ] as $val => [$lbl, $activeClass, $inactiveClass])
                                <button type="button" wire:click="$set('status', '{{ $val }}')"
                                    class="py-2.5 px-3 text-xs lato-bold rounded-xl border transition cursor-pointer flex items-center justify-center gap-1.5
                                               {{ $status === $val ? $activeClass . ' ring-2 ring-offset-1' : $inactiveClass . ' bg-white dark:bg-slate-700/40' }}">
                                    @if ($val === 'pendente')
                                        <x-lucide-clock class="w-3.5 h-3.5" />
                                    @elseif ($val === 'em_andamento')
                                        <x-lucide-loader-circle class="w-3.5 h-3.5" />
                                    @elseif ($val === 'concluida')
                                        <x-lucide-circle-check class="w-3.5 h-3.5" />
                                    @elseif ($val === 'cancelada')
                                        <x-lucide-circle-x class="w-3.5 h-3.5" />
                                    @endif
                                    {{ $lbl }}
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Atribuir para (só gerente, combobox) --}}
                    @if (auth()->user()->isGerente() && $this->assignableUsers->isNotEmpty())
                        <div class="space-y-1.5" @click.outside="assignOpen = false">
                            <label
                                class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1">
                                <x-lucide-user-check class="w-3.5 h-3.5" />
                                Atribuir para
                                <span class="text-[10px] font-normal normal-case text-slate-400 ml-1">— vazio = para
                                    mim</span>
                            </label>
                            <div class="relative">
                                <x-lucide-user
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none z-10" />

                                {{-- Selecionado --}}
                                <div x-show="assignedId && !assignOpen"
                                    class="flex items-center w-full pl-9 pr-9 py-2.5 text-sm lato-regular rounded-xl cursor-pointer
                                            bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                                            text-slate-800 dark:text-white"
                                    @click="assignOpen = true; $nextTick(() => $refs.assignInput.focus())">
                                    <span x-text="assignedLabel" class="truncate flex-1"></span>
                                </div>

                                {{-- Input de busca --}}
                                <input x-show="!assignedId || assignOpen" x-ref="assignInput" type="text"
                                    x-model="assignSearch" @focus="assignOpen = true" @input="assignOpen = true"
                                    placeholder="Buscar colaborador..."
                                    class="w-full pl-9 pr-9 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                              border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                              placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition" />

                                {{-- Limpar --}}
                                <button type="button" x-show="assignedId" @click="clear()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition cursor-pointer z-10">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>

                                {{-- Dropdown --}}
                                <div x-show="assignOpen && filtered.length > 0" x-cloak
                                    class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200
                                            dark:border-slate-600 rounded-xl shadow-xl overflow-hidden max-h-48 overflow-y-auto">
                                    <template x-for="u in filtered" :key="u.id">
                                        <button type="button" @click="select(u)"
                                            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm text-left
                                                       hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer"
                                            :class="{ 'bg-emerald-50 dark:bg-emerald-900/20': assignedId == u.id }">
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-400 to-slate-500
                                                        flex items-center justify-center text-white text-[10px] lato-black shrink-0"
                                                x-text="u.name.charAt(0).toUpperCase()"></div>
                                            <span
                                                class="flex-1 text-slate-800 dark:text-white lato-bold text-sm truncate"
                                                x-text="u.name"></span>
                                            <x-lucide-check class="w-4 h-4 text-emerald-500 shrink-0"
                                                x-show="assignedId == u.id" />
                                        </button>
                                    </template>
                                </div>

                                {{-- Sem resultados --}}
                                <div x-show="assignOpen && filtered.length === 0 && assignSearch.length > 0" x-cloak
                                    class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200
                                            dark:border-slate-600 rounded-xl shadow-lg px-4 py-3 text-sm text-slate-400 lato-regular">
                                    Nenhum resultado para "<span x-text="assignSearch"
                                        class="font-medium text-slate-600"></span>"
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Divider --}}
                    <div class="border-t border-slate-100 dark:border-slate-700"></div>

                    {{-- Tags --}}
                    <div class="space-y-2" x-data="{ newTag: '', newColor: '#10b981', showCreate: false }" @click.outside="showCreate = false">
                        <label
                            class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1">
                            <x-lucide-tag class="w-3.5 h-3.5" />
                            Tags
                        </label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($this->allTags as $tag)
                                <button type="button" wire:click="toggleSelectedTag({{ $tag->id }})"
                                    class="flex items-center gap-1 px-2.5 py-1 rounded-full text-xs lato-bold border-2 transition cursor-pointer"
                                    style="border-color: {{ $tag->color }}; color: {{ in_array($tag->id, $selectedTagIds) ? '#fff' : $tag->color }}; background: {{ in_array($tag->id, $selectedTagIds) ? $tag->color : 'transparent' }};">
                                    @if (in_array($tag->id, $selectedTagIds))
                                        <x-lucide-check class="w-3 h-3" />
                                    @endif
                                    {{ $tag->name }}
                                </button>
                            @endforeach

                            <button type="button" @click="showCreate = !showCreate"
                                class="flex items-center gap-1 px-2.5 py-1 rounded-full text-xs lato-bold border-2 border-dashed
                                           border-slate-300 dark:border-slate-600 text-slate-400 hover:border-emerald-400 hover:text-emerald-600 transition cursor-pointer">
                                <x-lucide-plus class="w-3 h-3" /> Nova tag
                            </button>
                        </div>

                        <div x-show="showCreate" x-transition class="flex items-center gap-2 pt-1">
                            <input type="color" x-model="newColor" wire:model="newTagColor"
                                class="w-8 h-8 rounded-lg border border-slate-200 cursor-pointer p-0.5" />
                            <input type="text" x-model="newTag" wire:model="newTagName"
                                placeholder="Nome da tag..." @keydown.enter.prevent="$wire.createTag()"
                                class="flex-1 px-3 py-1.5 text-xs lato-regular rounded-lg border border-slate-200 dark:border-slate-600
                                          bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition" />
                            <button type="button" wire:click="createTag"
                                class="px-3 py-1.5 text-xs lato-bold bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20 text-white rounded-lg transition cursor-pointer disabled:opacity-60"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="createTag" class="flex items-center gap-1.5">
                                    Criar
                                </span>
                                <span wire:loading wire:target="createTag" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-100 dark:border-slate-700"></div>

                    {{-- Recorrência --}}
                    <div class="space-y-2.5">
                        <label
                            class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1.5">
                            <x-lucide-repeat class="w-3.5 h-3.5" />
                            Recorrência
                        </label>

                        {{-- Chips de seleção --}}
                        <div class="grid grid-cols-5 gap-1.5">
                            @php
                                $recOpts = [
                                    '' => ['Nenhuma', 'lucide-ban', 'slate'],
                                    'daily' => ['Diária', 'lucide-sun', 'amber'],
                                    'weekly' => ['Semanal', 'lucide-calendar-days', 'blue'],
                                    'monthly' => ['Mensal', 'lucide-calendar', 'indigo'],
                                    'yearly' => ['Anual', 'lucide-star', 'violet'],
                                ];
                                $recColors = [
                                    'slate' => [
                                        'sel' =>
                                            'bg-slate-700 dark:bg-slate-200 border-slate-700 dark:border-slate-200 text-white dark:text-slate-800',
                                        'def' =>
                                            'bg-white dark:bg-slate-700/60 border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-slate-400 dark:hover:border-slate-400',
                                    ],
                                    'amber' => [
                                        'sel' => 'bg-amber-500 border-amber-500 text-white',
                                        'def' =>
                                            'bg-white dark:bg-slate-700/60 border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-amber-400 hover:text-amber-600 dark:hover:text-amber-400',
                                    ],
                                    'blue' => [
                                        'sel' => 'bg-blue-500 border-blue-500 text-white',
                                        'def' =>
                                            'bg-white dark:bg-slate-700/60 border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-blue-400 hover:text-blue-600 dark:hover:text-blue-400',
                                    ],
                                    'indigo' => [
                                        'sel' => 'bg-indigo-500 border-indigo-500 text-white',
                                        'def' =>
                                            'bg-white dark:bg-slate-700/60 border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400',
                                    ],
                                    'violet' => [
                                        'sel' => 'bg-indigo-600 border-indigo-500 text-white',
                                        'def' =>
                                            'bg-white dark:bg-slate-700/60 border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400',
                                    ],
                                ];
                            @endphp

                            @foreach ($recOpts as $val => [$label, $icon, $color])
                                @php
                                    $isSelected = $recurrence === $val;
                                    $cls = $isSelected ? $recColors[$color]['sel'] : $recColors[$color]['def'];
                                @endphp
                                <button type="button" wire:click="$set('recurrence', '{{ $val }}')"
                                    class="flex flex-col items-center gap-1 py-2.5 px-1 rounded-xl border text-center
                                               transition-all duration-150 cursor-pointer {{ $cls }}
                                               {{ $isSelected ? 'shadow-sm ring-2 ring-offset-1 ring-' . ($color === 'slate' ? 'slate-400' : $color . '-400') . '/40' : '' }}">
                                    <x-dynamic-component :component="$icon" class="w-4 h-4 shrink-0" />
                                    <span class="text-[10px] lato-bold leading-none">{{ $label }}</span>
                                </button>
                            @endforeach
                        </div>

                        {{-- Data de fim (só aparece quando há recorrência) --}}
                        @if ($recurrence)
                            <div class="flex items-center gap-2.5 px-3.5 py-2.5 rounded-xl
                                        bg-indigo-50/70 dark:bg-indigo-900/10
                                        border border-indigo-100 dark:border-indigo-800/40"
                                x-data x-show="true" x-transition:enter="transition ease-out duration-150"
                                x-transition:enter-start="opacity-0 -translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <x-lucide-calendar-x class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                                <span class="text-xs lato-bold text-indigo-600 dark:text-indigo-400 shrink-0">Encerrar
                                    em</span>
                                <input type="date" wire:model="recurrence_ends_at"
                                    class="flex-1 min-w-0 px-2.5 py-1 text-xs lato-regular rounded-lg
                                              bg-white dark:bg-slate-700/60
                                              border border-indigo-200 dark:border-indigo-700/50
                                              text-slate-800 dark:text-white
                                              focus:outline-none focus:ring-2 focus:ring-indigo-400/40 transition" />
                                <span class="text-[10px] text-indigo-400 lato-regular shrink-0">opcional</span>
                            </div>
                        @endif
                    </div>

                    {{-- Tempo estimado --}}
                    <div class="space-y-1.5">
                        <label
                            class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1.5">
                            <x-lucide-timer class="w-3.5 h-3.5" />
                            Tempo estimado
                        </label>
                        <div class="relative">
                            <input type="number" wire:model="estimated_minutes" min="1" max="9999"
                                placeholder="Ex: 60"
                                class="w-full pl-3 pr-12 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                          border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                          focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition" />
                            <span
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 lato-regular pointer-events-none">min</span>
                        </div>
                    </div>

                    {{-- Divider --}}
                    <div class="border-t border-slate-100 dark:border-slate-700"></div>

                    {{-- Subtarefas --}}
                    <div class="space-y-2">
                        <label
                            class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1">
                            <x-lucide-list-checks class="w-3.5 h-3.5" />
                            Subtarefas
                            @if (count($subtasks) > 0)
                                <span
                                    class="ml-1 text-[10px] font-normal normal-case bg-slate-100 dark:bg-slate-700
                                             text-slate-500 dark:text-slate-400 px-1.5 py-0.5 rounded-full lato-regular">
                                    {{ collect($subtasks)->where('completed', true)->count() }}/{{ count($subtasks) }}
                                </span>
                            @endif
                        </label>

                        {{-- Barra de progresso das subtarefas --}}
                        @if (count($subtasks) > 0)
                            @php
                                $subDone = collect($subtasks)->where('completed', true)->count();
                                $subTotal = count($subtasks);
                                $subPct = $subTotal > 0 ? round(($subDone / $subTotal) * 100) : 0;
                            @endphp
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 overflow-hidden">
                                <div class="h-1.5 rounded-full transition-all duration-500
                                            {{ $subPct === 100 ? 'bg-emerald-500' : 'bg-blue-400' }}"
                                    style="width: {{ $subPct }}%"></div>
                            </div>
                        @endif

                        {{-- Lista de subtarefas --}}
                        @if (count($subtasks) > 0)
                            <ul class="space-y-1.5">
                                @foreach ($subtasks as $idx => $sub)
                                    <li class="flex items-center gap-2 group/sub">
                                        <button type="button" wire:click="toggleSubtaskBuffer({{ $idx }})"
                                            class="w-4 h-4 rounded-full shrink-0 flex items-center justify-center transition cursor-pointer
                                                       {{ $sub['completed']
                                                           ? 'bg-emerald-500 hover:bg-emerald-600'
                                                           : 'border-2 border-slate-300 dark:border-slate-500 hover:border-emerald-400' }}">
                                            @if ($sub['completed'])
                                                <x-lucide-check class="w-2.5 h-2.5 text-white" />
                                            @endif
                                        </button>
                                        <span
                                            class="flex-1 text-sm lato-regular
                                                     {{ $sub['completed'] ? 'line-through text-slate-400 dark:text-slate-500' : 'text-slate-700 dark:text-slate-200' }}">
                                            {{ $sub['title'] }}
                                        </span>
                                        <button type="button" wire:click="removeSubtask({{ $idx }})"
                                            class="opacity-0 group-hover/sub:opacity-100 transition p-0.5 text-slate-300
                                                       hover:text-red-500 dark:hover:text-red-400 cursor-pointer rounded">
                                            <x-lucide-x class="w-3.5 h-3.5" />
                                        </button>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        {{-- Campo para nova subtarefa --}}
                        <div class="flex gap-2 items-center" x-data="{}"
                            @keydown.enter.prevent="if ($wire.newSubtaskTitle.trim()) $wire.addSubtask()">
                            <div class="relative flex-1">
                                <x-lucide-plus
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                <input type="text" wire:model="newSubtaskTitle"
                                    placeholder="Adicionar subtarefa... (Enter)"
                                    class="w-full pl-8 pr-3 py-2 text-sm lato-regular rounded-xl bg-slate-50 dark:bg-slate-700/50
                                              border border-dashed border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                              placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40
                                              focus:border-emerald-300 focus:bg-white dark:focus:bg-slate-700 transition" />
                            </div>
                            <button type="button" wire:click="addSubtask"
                                class="px-3 py-2 text-xs lato-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300
                                           rounded-xl hover:bg-emerald-50 dark:hover:bg-emerald-900/20 hover:text-emerald-700
                                           dark:hover:text-emerald-400 border border-slate-200 dark:border-slate-600
                                           hover:border-emerald-300 transition cursor-pointer shrink-0">
                                Adicionar
                            </button>
                        </div>
                    </div>

                </div>

                {{-- ── Footer ── --}}
                <div
                    class="flex-shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-700
                            bg-slate-50 dark:bg-slate-800/80 flex justify-between items-center gap-3">
                    @if ($editingId)
                        <button type="button" wire:click="requestDelete"
                            class="flex items-center gap-1.5 text-sm text-red-500 hover:text-red-700 lato-bold transition cursor-pointer
                                       px-3 py-2 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20">
                            <x-lucide-trash-2 class="w-4 h-4" />
                            Excluir
                        </button>
                    @else
                        <div></div>
                    @endif
                    <div class="flex gap-2">
                        <button type="button" wire:click="close"
                            class="px-4 py-2.5 text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200
                                       dark:border-slate-600 rounded-xl hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button type="button" wire:click="save" wire:loading.attr="disabled" wire:target="save"
                            class="flex items-center gap-2 px-5 py-2.5 text-sm lato-bold bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20
                                       disabled:opacity-60 text-white rounded-xl transition cursor-pointer">

                            <span wire:loading.remove wire:target="save" class="flex items-center gap-1">
                                @if ($editingId)
                                    <x-lucide-square-pen class="w-4 h-4" />
                                @else
                                    <x-lucide-circle-check class="w-4 h-4" />
                                @endif
                                {{ $editingId ? 'Salvar Alterações' : 'Confirmar' }}
                            </span>

                            <span wire:loading wire:target="save" class="flex items-center gap-2">
                                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                            </span>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
