    @if ($modalOpen)
        @php $isGerente = auth()->user()?->isGerente(); @endphp

        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="fecharModal"></div>

            <div
                class="relative bg-white dark:bg-slate-800 w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh] flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden">

                {{-- Header --}}
                <div class="relative flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 sm:py-5">
                        <div
                            class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-sm">
                            <x-lucide-clipboard-list class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2
                                class="text-base sm:text-lg font-bold text-slate-800 dark:text-white lato-black leading-tight">
                                {{ $modalMode === 'create' ? 'Nova Pesquisa' : 'Editar Pesquisa' }}
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5">
                                @if ($isGerente)
                                    Pesquisa direcionada ao seu departamento
                                @elseif ($modalMode === 'create')
                                    Configure uma nova pesquisa de satisfação
                                @else
                                    Atualize as informações da pesquisa
                                @endif
                            </p>
                        </div>

                        {{-- Botão "Usar template" (apenas no modo criação) --}}
                        @if ($modalMode === 'create')
                            <button type="button" wire:click="$dispatch('abrir-modal-templates')"
                                class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg
                                       bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200
                                       dark:bg-indigo-900/20 dark:hover:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-700/40
                                       transition cursor-pointer"
                                title="Começar com um template pronto">
                                <x-lucide-layout-template class="w-3.5 h-3.5" />
                                <span class="hidden sm:inline">Template</span>
                            </button>
                        @endif
                        <button wire:click="fecharModal"
                            class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div
                    class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 sm:py-5 space-y-4 bg-slate-50/40 dark:bg-slate-800">

                    {{-- Banner: template aplicado --}}
                    @if (!empty($pendingTemplateQuestions))
                        <div
                            class="flex items-center gap-2.5 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/10
                                    border border-indigo-200 dark:border-indigo-700/40">
                            <x-lucide-layout-template class="w-4 h-4 text-indigo-500 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lato-bold text-indigo-700 dark:text-indigo-300">
                                    Template aplicado — {{ count($pendingTemplateQuestions) }}
                                    {{ count($pendingTemplateQuestions) === 1 ? 'pergunta' : 'perguntas' }} serão
                                    criadas automaticamente
                                </p>
                                <p class="text-[11px] lato-regular text-indigo-600/70 dark:text-indigo-400/70 mt-0.5">
                                    Você pode editar as perguntas depois de criar a pesquisa.
                                </p>
                            </div>
                            <button type="button" wire:click="$set('pendingTemplateQuestions', null)"
                                class="shrink-0 w-6 h-6 flex items-center justify-center rounded-lg
                                       text-indigo-400 hover:text-indigo-600 hover:bg-indigo-100
                                       dark:hover:bg-indigo-900/30 transition cursor-pointer">
                                <x-lucide-x class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    @endif

                    {{-- Título --}}
                    <div class="space-y-1.5">
                        <label
                            class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                            Título <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <x-lucide-type
                                class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input type="text" wire:model.blur="title"
                                placeholder="Ex: Clima organizacional - 1º trimestre"
                                class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition
                                       {{ $errors->has('title') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}">
                        </div>
                        @error('title')
                            <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle
                                    class="w-3 h-3" />{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Descrição --}}
                    <div class="space-y-1.5">
                        <label
                            class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                            Descrição
                        </label>
                        <textarea wire:model.blur="description" rows="3"
                            placeholder="Descreva o objetivo e o contexto desta pesquisa..."
                            class="w-full px-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition resize-none
                                   {{ $errors->has('description') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}"></textarea>
                        @error('description')
                            <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle
                                    class="w-3 h-3" />{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Datas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label
                                class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Data
                                inicial</label>
                            <div class="relative">
                                <x-lucide-calendar
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="date" wire:model.blur="startDate"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition border border-slate-200 dark:border-slate-600">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label
                                class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Data
                                final</label>
                            <div class="relative">
                                <x-lucide-calendar-check
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="date" wire:model.blur="endDate"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition
                                           {{ $errors->has('endDate') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}">
                            </div>
                            @error('endDate')
                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle
                                        class="w-3 h-3" />{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Aviso de feriados/eventos nas datas da pesquisa --}}
                    @if($startDate || $endDate)
                        <div class="space-y-2">
                            @if($startDate)
                                <x-calendario-aviso :data="$startDate" />
                            @endif
                            @if($endDate && $endDate !== $startDate)
                                <x-calendario-aviso :data="$endDate" />
                            @endif
                        </div>
                    @endif

                    {{-- Público-alvo --}}
                    <div class="space-y-2">
                        <label
                            class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Público-alvo</label>

                        @if ($isGerente)
                            {{-- Gerente: público-alvo bloqueado no próprio departamento --}}
                            <div
                                class="flex items-center gap-2.5 p-3 rounded-lg border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/10 dark:border-emerald-800/40">
                                <x-lucide-lock class="w-4 h-4 text-emerald-600 shrink-0" />
                                <div>
                                    <p class="text-xs lato-bold text-emerald-700 dark:text-emerald-300">Seu
                                        departamento</p>
                                    <p
                                        class="text-[11px] lato-regular text-emerald-600/70 dark:text-emerald-400/70 mt-0.5">
                                        Pesquisas criadas pelo gerente são restritas ao próprio departamento.
                                    </p>
                                </div>
                            </div>
                        @else
                            {{-- RH/Admin: seletor completo --}}
                            <div class="grid grid-cols-2 gap-2">
                                <label @class([
                                    'flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition',
                                    'bg-emerald-50 border-emerald-300 text-emerald-700' =>
                                        $targetAudience === 'todos',
                                    'bg-white border-slate-200 text-slate-600 hover:border-slate-300' =>
                                        $targetAudience !== 'todos',
                                ])>
                                    <input type="radio" wire:model.live="targetAudience" value="todos"
                                        class="hidden">
                                    <x-lucide-users class="w-4 h-4" />
                                    <span class="text-xs lato-bold">Todos</span>
                                </label>
                                <label @class([
                                    'flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition',
                                    'bg-emerald-50 border-emerald-300 text-emerald-700' =>
                                        $targetAudience === 'departamentos',
                                    'bg-white border-slate-200 text-slate-600 hover:border-slate-300' =>
                                        $targetAudience !== 'departamentos',
                                ])>
                                    <input type="radio" wire:model.live="targetAudience" value="departamentos"
                                        class="hidden">
                                    <x-lucide-building-2 class="w-4 h-4" />
                                    <span class="text-xs lato-bold">Departamentos</span>
                                </label>
                            </div>

                            @if ($targetAudience === 'departamentos')
                                <div
                                    class="mt-2 p-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg max-h-44 overflow-y-auto space-y-1.5">
                                    @forelse ($this->departamentos as $dept)
                                        <label
                                            class="flex items-center gap-2 p-1.5 rounded hover:bg-slate-50 dark:hover:bg-slate-600 cursor-pointer">
                                            <input type="checkbox" wire:model.live="targetDepartmentIds"
                                                value="{{ $dept->id }}"
                                                class="w-4 h-4 rounded border-slate-300 text-emerald-500 focus:ring-emerald-400">
                                            <span
                                                class="text-sm lato-regular text-slate-700 dark:text-slate-200">{{ $dept->name }}</span>
                                        </label>
                                    @empty
                                        <p class="text-xs text-slate-400 lato-regular text-center py-2">Nenhum
                                            departamento cadastrado.</p>
                                    @endforelse
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Ciclo de Avaliação (somente RH/Admin) --}}
                    @if (!$isGerente && $this->availableEvaluationCycles->isNotEmpty())
                        <div class="space-y-1.5">
                            <label
                                class="flex items-center gap-1.5 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                <x-lucide-link class="w-3.5 h-3.5 text-indigo-500" />
                                Ciclo de Avaliação
                                <span
                                    class="normal-case lato-regular text-slate-400 font-normal ml-1">(opcional)</span>
                            </label>
                            @php
                                $cycleOpts = $this->availableEvaluationCycles->map(fn($c) => [
                                    'id'     => $c->id,
                                    'name'   => $c->name,
                                    'status' => $c->status === 'active' ? 'Ativo' : 'Rascunho',
                                    'dot'    => $c->status === 'active' ? 'bg-emerald-400' : 'bg-amber-400',
                                ])->values()->toJson();
                                $selCycle = $evaluationCycleId
                                    ? $this->availableEvaluationCycles->firstWhere('id', $evaluationCycleId)
                                    : null;
                            @endphp
                            <div x-data="{
                                    open: false,
                                    cycles: {{ $cycleOpts }},
                                    selectedId: '{{ $evaluationCycleId }}',
                                    select(id) {
                                        this.selectedId = id;
                                        this.open = false;
                                        $wire.set('evaluationCycleId', id || null);
                                    },
                                    get selected() {
                                        return this.cycles.find(c => String(c.id) === String(this.selectedId)) || null;
                                    }
                                }"
                                 @click.outside="open = false"
                                 class="relative">

                                {{-- Trigger --}}
                                <button type="button" @click="open = !open"
                                    class="cursor-pointer w-full flex items-center gap-2.5 pl-3 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition"
                                    :class="open ? 'border-indigo-400 ring-2 ring-indigo-400/20' : ''">
                                    <x-lucide-refresh-cw class="w-4 h-4 text-slate-400 shrink-0" />
                                    <span class="flex-1 text-left truncate">
                                        <template x-if="selected">
                                            <span class="flex items-center gap-2">
                                                <span :class="selected.dot" class="w-2 h-2 rounded-full shrink-0"></span>
                                                <span x-text="selected.name" class="truncate"></span>
                                                <span class="text-xs text-slate-400 shrink-0" x-text="'(' + selected.status + ')'"></span>
                                            </span>
                                        </template>
                                        <template x-if="!selected">
                                            <span class="text-slate-400">Nenhum ciclo vinculado</span>
                                        </template>
                                    </span>
                                    <template x-if="selected">
                                        <span @click.stop="select('')"
                                              class="p-0.5 rounded hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-400 hover:text-slate-600 transition">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/></svg>
                                        </span>
                                    </template>
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                                </button>

                                {{-- Dropdown --}}
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                     x-transition:leave="transition ease-in duration-75"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0 scale-95"
                                     class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-lg overflow-hidden"
                                     style="display:none">
                                    <ul class="py-1">
                                        {{-- Opção "nenhum" --}}
                                        <li @click="select('')"
                                            class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer transition text-sm lato-regular"
                                            :class="!selectedId ? 'bg-slate-50 dark:bg-slate-700 text-slate-500' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                            <span class="w-2 h-2 rounded-full bg-slate-300 shrink-0"></span>
                                            <span class="flex-1">Nenhum ciclo vinculado</span>
                                            <template x-if="!selectedId">
                                                <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                            </template>
                                        </li>
                                        <template x-for="c in cycles" :key="c.id">
                                            <li @click="select(c.id)"
                                                class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer transition text-sm lato-regular"
                                                :class="String(selectedId) === String(c.id)
                                                    ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300'
                                                    : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                                <span :class="c.dot" class="w-2 h-2 rounded-full shrink-0"></span>
                                                <span class="flex-1 truncate" x-text="c.name"></span>
                                                <span class="text-xs shrink-0"
                                                      :class="String(selectedId) === String(c.id) ? 'text-indigo-400' : 'text-slate-400'"
                                                      x-text="c.status"></span>
                                                <template x-if="String(selectedId) === String(c.id)">
                                                    <svg class="w-3.5 h-3.5 text-indigo-500 shrink-0 ml-1" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                                </template>
                                            </li>
                                        </template>
                                    </ul>
                                </div>
                            </div>
                            <p class="text-[11px] lato-regular text-slate-400 flex items-center gap-1">
                                <x-lucide-info class="w-3 h-3 shrink-0" />
                                Vincula esta pesquisa a um ciclo de avaliação de desempenho.
                            </p>
                        </div>
                    @endif

                    {{-- Status + Anonimato --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        {{-- Status --}}
                        <div class="space-y-1.5">
                            <label
                                class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Status</label>
                            @php
                                $statusOpts = [
                                    'rascunho'  => ['label' => 'Rascunho',  'dot' => 'bg-amber-400'],
                                    'ativa'     => ['label' => 'Ativa',     'dot' => 'bg-emerald-400'],
                                    'encerrada' => ['label' => 'Encerrada', 'dot' => 'bg-slate-400'],
                                ];
                            @endphp
                            <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                <button type="button" @click="open = !open"
                                    class="cursor-pointer w-full flex items-center justify-between gap-2 pl-3 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition">
                                    <span class="flex items-center gap-2">
                                        <span class="w-2 h-2 rounded-full shrink-0 {{ $statusOpts[$status]['dot'] ?? 'bg-slate-400' }}"></span>
                                        {{ $statusOpts[$status]['label'] ?? $status }}
                                    </span>
                                    <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 transition-transform shrink-0" ::class="{'rotate-180': open}" />
                                </button>
                                <div x-show="open"
                                     x-transition:enter="transition ease-out duration-100"
                                     x-transition:enter-start="opacity-0 scale-95"
                                     x-transition:enter-end="opacity-100 scale-100"
                                     class="absolute top-full left-0 mt-1 z-50 w-full bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg py-1"
                                     style="display:none">
                                    @foreach($statusOpts as $val => $opt)
                                    <button type="button"
                                        wire:click="$set('status', '{{ $val }}')"
                                        @click="open = false"
                                        class="cursor-pointer w-full flex items-center gap-2.5 px-3 py-2 text-sm lato-regular transition
                                            {{ $status === $val ? 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                        <span class="w-2 h-2 rounded-full shrink-0 {{ $opt['dot'] }}"></span>
                                        {{ $opt['label'] }}
                                        @if($status === $val)
                                            <x-lucide-check class="w-3 h-3 ml-auto text-emerald-500" />
                                        @endif
                                    </button>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        {{-- Anonimato --}}
                        <div class="space-y-1.5">
                            <label
                                class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Respostas</label>
                            <div
                                class="flex items-center justify-between gap-3 p-2.5 rounded-lg border {{ $isAnonymous ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
                                <div class="flex items-center gap-2 min-w-0">
                                    @if ($isAnonymous)
                                        <x-lucide-eye-off class="w-4 h-4 text-emerald-600" />
                                        <span class="text-xs lato-bold text-emerald-700">Anônimas</span>
                                    @else
                                        <x-lucide-eye class="w-4 h-4 text-slate-500" />
                                        <span class="text-xs lato-bold text-slate-600">Identificadas</span>
                                    @endif
                                </div>
                                <label class="relative inline-flex items-center cursor-pointer shrink-0">
                                    <input type="checkbox" wire:model.live="isAnonymous" class="sr-only peer">
                                    <div class="w-10 h-5.5 bg-slate-300 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4.5 after:w-4.5 after:transition-all peer-checked:after:translate-x-full transition-colors"
                                        style="width:2.5rem; height:1.375rem;"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div
                    class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-between gap-3">
                    <p class="hidden sm:flex items-center gap-1.5 text-xs text-slate-400 lato-regular">
                        <x-lucide-info class="w-3.5 h-3.5" />
                        Campos com <span class="text-red-500 font-semibold">*</span> são obrigatórios
                    </p>
                    <div class="flex gap-2 flex-1 sm:flex-initial">
                        <button wire:click="fecharModal" type="button"
                            class="flex-1 sm:flex-none px-4 py-2 text-sm lato-bold rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="salvarPesquisa"
                            class="flex-1 sm:flex-none px-4 py-2 text-sm lato-bold rounded-lg text-white
                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                                   shadow-sm transition cursor-pointer">
                            {{ $modalMode === 'edit' ? 'Salvar alterações' : 'Criar pesquisa' }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
