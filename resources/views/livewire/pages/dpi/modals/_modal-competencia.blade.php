    {{-- ════════════════════════════════════════════════════════════════
         MODAL ÚNICO: Competência + Ações de Desenvolvimento
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($goalModal)
        @php $nivelOpts = \App\Models\DpiGoal::nivelLabels(); @endphp
        <div x-data="{ show: false }" x-init="$nextTick(() => show = true)">

            {{-- Backdrop --}}
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm"
                 wire:click="$set('goalModal', false)"></div>

            {{-- Wrapper de posicionamento (sem x-show) --}}
            <div class="fixed inset-x-0 bottom-0 z-50 pointer-events-none
                        sm:inset-0 sm:flex sm:items-center sm:justify-center sm:p-4">

                {{-- Painel com transição --}}
                <div x-show="show"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="translate-y-full sm:translate-y-4 sm:opacity-0"
                     x-transition:enter-end="translate-y-0 sm:opacity-100"
                     class="pointer-events-auto w-full sm:max-w-2xl max-h-[92vh] flex flex-col
                            bg-white dark:bg-slate-800 rounded-t-2xl sm:rounded-2xl
                            border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700 shadow-2xl">

                {{-- Handle (só mobile) --}}
                <div class="flex sm:hidden justify-center pt-3 pb-1 shrink-0">
                    <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-600"></div>
                </div>

                {{-- Cabeçalho --}}
                <div class="flex items-center justify-between px-6 pt-4 sm:pt-5 pb-4 shrink-0
                            border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-100 to-indigo-100
                                    dark:from-blue-900/30 dark:to-indigo-900/30 flex items-center justify-center">
                            <x-lucide-brain class="w-4 h-4 text-indigo-500" />
                        </div>
                        <h3 class="text-base lato-bold text-slate-800 dark:text-white">
                            {{ $editGoalId ? 'Editar Competência' : 'Nova Competência' }}
                        </h3>
                    </div>
                    <button wire:click="$set('goalModal', false)" type="button"
                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                   text-slate-400 hover:text-slate-700 hover:bg-slate-100
                                   dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Conteúdo scrollável --}}
                <div class="overflow-y-auto flex-1 px-6 py-5">
                <div class="space-y-5">

                    {{-- ── SEÇÃO 1: Mapeamento da Competência ── --}}
                    <div>
                        <p class="text-[11px] lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest mb-3">
                            Mapeamento de Competência
                        </p>
                        <div class="space-y-4">

                            {{-- Nome --}}
                            <div>
                                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                    Nome da Competência <span class="text-red-400">*</span>
                                </label>
                                <input wire:model="compNome" type="text"
                                       placeholder="Ex: Comunicação assertiva, Liderança de equipes..."
                                       class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                              bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                              border-slate-200 dark:border-slate-700
                                              focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                                @error('compNome') <p class="mt-1 text-xs text-red-400 lato-regular">{{ $message }}</p> @enderror
                            </div>

                            {{-- Wrapper reativo: nivelAtual + nivelMeta sincronizados com Livewire --}}
                            @php $nivelLabelsJs = collect($nivelOpts)->map(fn($l,$k) => "$k: '$l'")->implode(', '); @endphp
                            <div x-data="{
                                    nivelAtual: @entangle('compNivelAtual'),
                                    nivelMeta:  @entangle('compNivelMeta'),
                                    nivelLabels: { {{ $nivelLabelsJs }} }
                                 }"
                                 class="space-y-3">

                                {{-- Prioridade + Níveis (grid 3 colunas) --}}
                                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">

                                    {{-- Prioridade --}}
                                    <div>
                                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Prioridade</label>
                                        <div x-data="{ open: false, val: @entangle('compPrioridade') }"
                                             @click.outside="open = false" class="relative">
                                            <button type="button" @click="open = !open"
                                                    class="w-full flex items-center gap-2 pl-3 pr-8 py-2.5 text-sm lato-bold rounded-xl
                                                           bg-slate-50 dark:bg-slate-700 text-slate-700 dark:text-slate-200
                                                           border border-slate-200 dark:border-slate-600
                                                           focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition text-left">
                                                <span x-show="val === 'alta'"  class="shrink-0"><x-lucide-arrow-up  class="w-4 h-4 text-red-500" /></span>
                                                <span x-show="val === 'media'" class="shrink-0"><x-lucide-minus      class="w-4 h-4 text-amber-500" /></span>
                                                <span x-show="val === 'baixa'" class="shrink-0"><x-lucide-arrow-down class="w-4 h-4 text-blue-400" /></span>
                                                <span x-text="{ alta: 'Alta', media: 'Média', baixa: 'Baixa' }[val]" class="truncate"></span>
                                            </button>
                                            <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                                                   ::class="{ 'rotate-180': open }" />
                                            <div x-show="open" x-transition
                                                 class="absolute z-[60] w-full mt-1 bg-white dark:bg-slate-800 rounded-xl
                                                        border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                                @foreach ([['alta','Alta','arrow-up','text-red-500'],['media','Média','minus','text-amber-500'],['baixa','Baixa','arrow-down','text-blue-400']] as [$v,$l,$i,$c])
                                                    <button type="button" @click="val = '{{ $v }}'; open = false"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2.5 text-sm lato-regular hover:bg-slate-50 dark:hover:bg-slate-700/60 transition cursor-pointer"
                                                            :class="{ 'bg-indigo-50 dark:bg-indigo-900/20 lato-bold': val === '{{ $v }}' }">
                                                        <x-dynamic-component :component="'lucide-' . $i" class="w-4 h-4 shrink-0 {{ $c }}" />
                                                        <span class="text-slate-700 dark:text-slate-200 flex-1 text-left">{{ $l }}</span>
                                                        <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0" x-show="val === '{{ $v }}'" />
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Nível Atual --}}
                                    <div>
                                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                            Nível Atual
                                        </label>
                                        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                            <button type="button" @click="open = !open"
                                                    class="w-full flex items-center gap-2.5 pl-3 pr-8 py-2.5 rounded-xl
                                                           bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                                           focus:outline-none focus:ring-2 focus:ring-blue-400/40 cursor-pointer transition text-left">
                                                @foreach ($nivelOpts as $nv => $nl)
                                                    <span x-show="nivelAtual == {{ $nv }}" class="flex items-center gap-2 w-full">
                                                        <span class="w-6 h-6 rounded-full bg-blue-500 text-white text-xs lato-bold flex items-center justify-center shrink-0 shadow-sm shadow-blue-500/30">{{ $nv }}</span>
                                                        <span class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $nl }}</span>
                                                    </span>
                                                @endforeach
                                            </button>
                                            <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none transition-transform duration-200"
                                                                   ::class="{ 'rotate-180': open }" />
                                            <div x-show="open"
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                 class="absolute z-[60] w-full mt-1 bg-white dark:bg-slate-800 rounded-xl
                                                        border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                                @php $descs = ['Sem conhecimento na área','Conhecimento introdutório','Aplica com supervisão','Aplica com autonomia','Referência na área']; @endphp
                                                @foreach ($nivelOpts as $nv => $nl)
                                                    <button type="button" @click="nivelAtual = {{ $nv }}; open = false"
                                                            class="w-full flex items-center gap-3 px-3 py-2.5 transition cursor-pointer
                                                                   hover:bg-slate-50 dark:hover:bg-slate-700/60"
                                                            :class="{ 'bg-blue-50 dark:bg-blue-900/20': nivelAtual == {{ $nv }} }">
                                                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs lato-bold shrink-0 transition"
                                                              :class="nivelAtual == {{ $nv }}
                                                                  ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/30'
                                                                  : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">{{ $nv }}</span>
                                                        <div class="flex-1 text-left">
                                                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $nl }}</p>
                                                            <p class="text-[11px] text-slate-400 lato-regular">{{ $descs[$nv - 1] }}</p>
                                                        </div>
                                                        <x-lucide-check class="w-3.5 h-3.5 text-blue-500 shrink-0"
                                                                        x-show="nivelAtual == {{ $nv }}" />
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Nível Meta --}}
                                    <div>
                                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                            Nível Meta
                                        </label>
                                        <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                                            <button type="button" @click="open = !open"
                                                    class="w-full flex items-center gap-2.5 pl-3 pr-8 py-2.5 rounded-xl
                                                           bg-slate-50 dark:bg-slate-700 border border-slate-200 dark:border-slate-600
                                                           focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition text-left">
                                                @foreach ($nivelOpts as $nv => $nl)
                                                    <span x-show="nivelMeta == {{ $nv }}" class="flex items-center gap-2 w-full">
                                                        <span class="w-6 h-6 rounded-full border-2 border-indigo-400 text-indigo-500 text-xs lato-bold flex items-center justify-center shrink-0 bg-indigo-50 dark:bg-indigo-900/20">{{ $nv }}</span>
                                                        <span class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $nl }}</span>
                                                    </span>
                                                @endforeach
                                            </button>
                                            <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none transition-transform duration-200"
                                                                   ::class="{ 'rotate-180': open }" />
                                            <div x-show="open"
                                                 x-transition:enter="transition ease-out duration-150"
                                                 x-transition:enter-start="opacity-0 -translate-y-1"
                                                 x-transition:enter-end="opacity-100 translate-y-0"
                                                 class="absolute z-[60] w-full mt-1 bg-white dark:bg-slate-800 rounded-xl
                                                        border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                                @php $descs = ['Sem conhecimento na área','Conhecimento introdutório','Aplica com supervisão','Aplica com autonomia','Referência na área']; @endphp
                                                @foreach ($nivelOpts as $nv => $nl)
                                                    <button type="button" @click="nivelMeta = {{ $nv }}; open = false"
                                                            class="w-full flex items-center gap-3 px-3 py-2.5 transition cursor-pointer
                                                                   hover:bg-slate-50 dark:hover:bg-slate-700/60"
                                                            :class="{ 'bg-indigo-50 dark:bg-indigo-900/20': nivelMeta == {{ $nv }} }">
                                                        <span class="w-7 h-7 rounded-full flex items-center justify-center text-xs lato-bold shrink-0 transition"
                                                              :class="nivelMeta == {{ $nv }}
                                                                  ? 'border-2 border-indigo-400 text-indigo-500 bg-indigo-50 dark:bg-indigo-900/30'
                                                                  : 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400'">{{ $nv }}</span>
                                                        <div class="flex-1 text-left">
                                                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $nl }}</p>
                                                            <p class="text-[11px] text-slate-400 lato-regular">{{ $descs[$nv - 1] }}</p>
                                                        </div>
                                                        <x-lucide-check class="w-3.5 h-3.5 text-indigo-500 shrink-0"
                                                                        x-show="nivelMeta == {{ $nv }}" />
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                {{-- Preview visual dos níveis — 100% Alpine, atualiza ao instante --}}
                                <div class="bg-slate-50 dark:bg-slate-900/40 rounded-xl px-4 py-3 space-y-2">
                                    <div class="flex items-center justify-center gap-2">
                                        @foreach ($nivelOpts as $n => $lbl)
                                            <span class="w-8 h-8 rounded-full text-xs lato-bold flex items-center justify-center transition-all duration-200"
                                                  :class="{{ $n }} <= nivelAtual
                                                      ? 'bg-blue-500 text-white shadow-sm shadow-blue-500/30'
                                                      : ({{ $n }} == nivelMeta
                                                          ? 'border-2 border-indigo-400 text-indigo-500 bg-indigo-50 dark:bg-indigo-900/20'
                                                          : 'bg-slate-200 dark:bg-slate-700 text-slate-400')"
                                                  title="{{ $lbl }}">{{ $n }}</span>
                                        @endforeach
                                    </div>
                                    <div class="flex items-center justify-center gap-4">
                                        <span class="text-[11px] text-blue-500 lato-bold flex items-center gap-1">
                                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500 inline-block"></span>
                                            Atual: <span x-text="nivelLabels[nivelAtual]"></span>
                                        </span>
                                        <span class="text-[11px] text-indigo-500 lato-bold flex items-center gap-1">
                                            <span class="w-2.5 h-2.5 rounded-full border-2 border-indigo-400 inline-block"></span>
                                            Meta: <span x-text="nivelLabels[nivelMeta]"></span>
                                        </span>
                                    </div>
                                </div>

                            </div>{{-- fim wrapper reativo --}}
                        </div>
                    </div>

                    {{-- ── SEÇÃO 2: Ações de Desenvolvimento ── --}}
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-5">
                        <div class="flex items-center justify-between mb-3">
                            <p class="text-[11px] lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-widest">
                                Ações de Desenvolvimento
                            </p>
                            @if (! $showInlineForm)
                                <button wire:click="$set('showInlineForm', true)" type="button"
                                        class="flex items-center gap-1 text-xs lato-bold text-blue-500 hover:text-blue-600 transition cursor-pointer">
                                    <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar ação
                                </button>
                            @endif
                        </div>

                        {{-- Lista de ações salvas (editando competência existente) --}}
                        @if ($editGoalId && $this->editingGoalActions->isNotEmpty())
                            <div class="mb-3 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($this->editingGoalActions as $ia)
                                    @php
                                        $iaTypes = ['curso'=>['graduation-cap','text-blue-500'],'certificacao'=>['award','text-amber-500'],'leitura'=>['book-open','text-emerald-500'],'mentoria'=>['users','text-indigo-500'],'projeto'=>['folder-open','text-orange-500'],'workshop'=>['presentation','text-pink-500'],'outro'=>['circle-dot','text-slate-400']];
                                        [$iaIco, $iaClr] = $iaTypes[$ia->type] ?? ['circle-dot','text-slate-400'];
                                    @endphp
                                    <div class="flex items-start gap-3 px-4 py-2.5 bg-white dark:bg-slate-800/50
                                                {{ $editingInlineActionId === $ia->id ? 'bg-indigo-50 dark:bg-indigo-900/10' : '' }}">
                                        <div class="mt-0.5 shrink-0">
                                            <x-dynamic-component :component="'lucide-'.$iaIco" class="w-4 h-4 {{ $iaClr }}" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 leading-snug
                                                       {{ $ia->status === 'concluido' ? 'line-through text-slate-400' : '' }}">
                                                {{ $ia->title }}
                                            </p>
                                            <div class="flex items-center gap-2 mt-0.5 flex-wrap">
                                                <span class="text-[11px] text-slate-400 lato-regular">{{ $ia->type_label }}</span>
                                                @if ($ia->treinamento)
                                                    <span class="text-[11px] text-blue-500 lato-bold flex items-center gap-1">
                                                        · <x-lucide-graduation-cap class="w-3 h-3" /> {{ Str::limit($ia->treinamento->titulo, 35) }}
                                                    </span>
                                                @endif
                                                @if ($ia->target_date)
                                                    <span class="text-[11px] text-slate-400 lato-regular flex items-center gap-1">
                                                        · <x-lucide-calendar class="w-3 h-3" /> {{ $ia->target_date->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                                @php
                                                    $iaSt = match($ia->status) { 'concluido' => ['text-emerald-600','Concluído'], 'em_andamento' => ['text-blue-500','Em andamento'], default => ['text-slate-400','Pendente'] };
                                                @endphp
                                                <span class="text-[11px] lato-bold {{ $iaSt[0] }}">· {{ $iaSt[1] }}</span>
                                            </div>
                                        </div>
                                        <div class="flex items-center gap-0.5 shrink-0">
                                            <button wire:click="editInlineAction({{ $ia->id }})" type="button"
                                                    class="w-6 h-6 flex items-center justify-center rounded-lg
                                                           text-slate-300 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer">
                                                <x-lucide-pencil class="w-3 h-3" />
                                            </button>
                                            <button wire:click="deleteInlineAction({{ $ia->id }})" type="button"
                                                    class="w-6 h-6 flex items-center justify-center rounded-lg
                                                           text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer disabled:opacity-60"
                                                wire:loading.attr="disabled">
                                                <span wire:loading.remove wire:target="deleteInlineAction" class="flex items-center gap-1.5">
                                                    <x-lucide-trash-2 class="w-3 h-3" />
                                                </span>
                                                <span wire:loading wire:target="deleteInlineAction" class="flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                </span>
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Lista de ações pendentes (nova competência) --}}
                        @if (! $editGoalId && count($pendingActions) > 0)
                            <div class="mb-3 rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($pendingActions as $pi => $pa)
                                    @php
                                        $paTypes = ['curso'=>['graduation-cap','text-blue-500'],'certificacao'=>['award','text-amber-500'],'leitura'=>['book-open','text-emerald-500'],'mentoria'=>['users','text-indigo-500'],'projeto'=>['folder-open','text-orange-500'],'workshop'=>['presentation','text-pink-500'],'outro'=>['circle-dot','text-slate-400']];
                                        [$paIco, $paClr] = $paTypes[$pa['type']] ?? ['circle-dot','text-slate-400'];
                                        $paTypeLabels = ['curso'=>'Curso','certificacao'=>'Certificação','leitura'=>'Leitura','mentoria'=>'Mentoria','projeto'=>'Projeto','workshop'=>'Workshop','outro'=>'Outro'];
                                    @endphp
                                    <div class="flex items-center gap-3 px-4 py-2.5 bg-white dark:bg-slate-800/50
                                                {{ $editingInlineActionIndex === $pi ? 'bg-indigo-50 dark:bg-indigo-900/10' : '' }}">
                                        <x-dynamic-component :component="'lucide-'.$paIco" class="w-4 h-4 {{ $paClr }} shrink-0" />
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate">{{ $pa['title'] }}</p>
                                            <p class="text-[11px] text-slate-400 lato-regular flex items-center flex-wrap gap-1">
                                                <span>{{ $paTypeLabels[$pa['type']] ?? $pa['type'] }}</span>
                                                @if (!empty($pa['treinamento_id']))
                                                    @php $paTr = $this->treinamentosDisponiveis->firstWhere('id', $pa['treinamento_id']); @endphp
                                                    @if ($paTr)
                                                        <span class="text-blue-500 lato-bold flex items-center gap-0.5">
                                                            · <x-lucide-graduation-cap class="w-3 h-3" /> {{ Str::limit($paTr->titulo, 35) }}
                                                        </span>
                                                    @endif
                                                @endif
                                                @if ($pa['date']) <span>· {{ \Carbon\Carbon::parse($pa['date'])->format('d/m/Y') }}</span> @endif
                                            </p>
                                        </div>
                                        <div class="flex items-center gap-0.5 shrink-0">
                                            <button wire:click="editPendingAction({{ $pi }})" type="button"
                                                    class="w-6 h-6 flex items-center justify-center rounded-lg
                                                           text-slate-300 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer">
                                                <x-lucide-pencil class="w-3 h-3" />
                                            </button>
                                            <button wire:click="removePendingAction({{ $pi }})" type="button"
                                                    class="w-6 h-6 flex items-center justify-center rounded-lg
                                                           text-slate-300 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                                                <x-lucide-trash-2 class="w-3 h-3" />
                                            </button>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif

                        {{-- Estado vazio das ações --}}
                        @if (! $showInlineForm)
                            @php
                                $hasActions = $editGoalId
                                    ? $this->editingGoalActions->isNotEmpty()
                                    : count($pendingActions) > 0;
                            @endphp
                            @if (! $hasActions)
                                <div class="rounded-xl border border-dashed border-slate-200 dark:border-slate-700
                                            py-4 text-center">
                                    <p class="text-xs text-slate-400 lato-regular">Nenhuma ação adicionada ainda.</p>
                                </div>
                            @endif
                        @endif

                        {{-- Formulário inline de ação --}}
                        @if ($showInlineForm)
                            <div class="bg-slate-50 dark:bg-slate-900/40 rounded-xl border border-slate-200 dark:border-slate-700 p-4 space-y-3">
                                <p class="text-[11px] lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">
                                    {{ $editingInlineActionId || $editingInlineActionIndex !== null ? 'Editar ação' : 'Nova ação' }}
                                </p>

                                {{-- Título --}}
                                <div>
                                    <input wire:model="inlineActionTitle" type="text"
                                           placeholder="Título da ação *"
                                           class="w-full px-3.5 py-2 text-sm lato-regular rounded-xl border
                                                  bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                  border-slate-200 dark:border-slate-700
                                                  focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                                    @error('inlineActionTitle') <p class="mt-1 text-xs text-red-400 lato-regular">{{ $message }}</p> @enderror
                                </div>

                                {{-- Tipo + Prazo --}}
                                <div class="grid grid-cols-2 gap-3">

                                    {{-- Tipo (dropdown com ícones) --}}
                                    <div x-data="{ open: false, val: @entangle('inlineActionType') }"
                                         @click.outside="open = false" class="relative">
                                        <button type="button" @click="open = !open"
                                                class="w-full flex items-center gap-2 pl-3 pr-8 py-2 text-sm lato-bold rounded-xl
                                                       bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                       border border-slate-200 dark:border-slate-700
                                                       focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition text-left">
                                            <span x-show="val === 'curso'"        class="shrink-0"><x-lucide-graduation-cap class="w-4 h-4 text-blue-500" /></span>
                                            <span x-show="val === 'certificacao'" class="shrink-0"><x-lucide-award          class="w-4 h-4 text-amber-500" /></span>
                                            <span x-show="val === 'leitura'"      class="shrink-0"><x-lucide-book-open      class="w-4 h-4 text-emerald-500" /></span>
                                            <span x-show="val === 'mentoria'"     class="shrink-0"><x-lucide-users          class="w-4 h-4 text-indigo-500" /></span>
                                            <span x-show="val === 'projeto'"      class="shrink-0"><x-lucide-folder-open    class="w-4 h-4 text-orange-500" /></span>
                                            <span x-show="val === 'workshop'"     class="shrink-0"><x-lucide-presentation   class="w-4 h-4 text-pink-500" /></span>
                                            <span x-show="val === 'outro'"        class="shrink-0"><x-lucide-circle-dot     class="w-4 h-4 text-slate-400" /></span>
                                            <span x-text="{ curso: 'Curso', certificacao: 'Certificação', leitura: 'Leitura', mentoria: 'Mentoria', projeto: 'Projeto', workshop: 'Workshop', outro: 'Outro' }[val]" class="truncate"></span>
                                        </button>
                                        <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                                               ::class="{ 'rotate-180': open }" />
                                        <div x-show="open" x-transition
                                             class="absolute z-[70] w-full mt-1 bg-white dark:bg-slate-800 rounded-xl
                                                    border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                            @foreach ([
                                                ['curso','Curso','graduation-cap','text-blue-500'],
                                                ['certificacao','Certificação','award','text-amber-500'],
                                                ['leitura','Leitura','book-open','text-emerald-500'],
                                                ['mentoria','Mentoria','users','text-indigo-500'],
                                                ['projeto','Projeto','folder-open','text-orange-500'],
                                                ['workshop','Workshop','presentation','text-pink-500'],
                                                ['outro','Outro','circle-dot','text-slate-400'],
                                            ] as [$v,$l,$i,$c])
                                                <button type="button" @click="val='{{ $v }}'; open=false"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm lato-regular
                                                               hover:bg-slate-50 dark:hover:bg-slate-700/60 transition cursor-pointer"
                                                        :class="{ 'bg-indigo-50 dark:bg-indigo-900/20 lato-bold': val === '{{ $v }}' }">
                                                    <x-dynamic-component :component="'lucide-' . $i" class="w-4 h-4 shrink-0 {{ $c }}" />
                                                    <span class="text-slate-700 dark:text-slate-200 flex-1 text-left">{{ $l }}</span>
                                                    <x-lucide-check class="w-3 h-3 text-indigo-500 shrink-0" x-show="val === '{{ $v }}'" />
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>

                                    {{-- Prazo --}}
                                    <div class="relative">
                                        <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                        <input wire:model="inlineActionDate" type="date"
                                               class="w-full appearance-none pl-9 pr-3 py-2 text-sm lato-regular rounded-xl border
                                                      bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                      border-slate-200 dark:border-slate-700
                                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition" />
                                    </div>
                                </div>

                                {{-- Seletor de curso (apenas quando tipo = curso) --}}
                                @if ($inlineActionType === 'curso')
                                    <div x-data="{ open: false, search: '' }" @click.outside="open = false" class="relative">
                                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1">
                                            Vincular curso <span class="text-slate-400 lato-regular normal-case tracking-normal">(opcional)</span>
                                        </label>
                                        <button type="button" @click="open = !open; $nextTick(() => { if(open) $refs.searchInput.focus() })"
                                                class="w-full flex items-center gap-2 pl-3 pr-8 py-2 text-sm lato-regular rounded-xl
                                                       bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                       border border-slate-200 dark:border-slate-700
                                                       focus:outline-none focus:ring-2 focus:ring-blue-400/40 cursor-pointer transition text-left">
                                            @if ($inlineActionTreinamentoId)
                                                @php $cursoSelecionado = $this->treinamentosDisponiveis->firstWhere('id', $inlineActionTreinamentoId); @endphp
                                                <x-lucide-graduation-cap class="w-3.5 h-3.5 text-blue-500 shrink-0" />
                                                <span class="truncate text-blue-600 dark:text-blue-400">{{ $cursoSelecionado?->titulo ?? 'Curso vinculado' }}</span>
                                            @else
                                                <x-lucide-search class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                                <span class="text-slate-400">Buscar e vincular curso do sistema...</span>
                                            @endif
                                        </button>
                                        @if ($inlineActionTreinamentoId)
                                            <button type="button" wire:click="$set('inlineActionTreinamentoId', null)"
                                                    class="absolute right-2.5 top-1/2 mt-3 -translate-y-1/2 w-5 h-5 flex items-center justify-center
                                                           text-slate-400 hover:text-red-400 transition cursor-pointer">
                                                <x-lucide-x class="w-3.5 h-3.5" />
                                            </button>
                                        @else
                                            <x-lucide-chevron-down class="absolute right-3 top-1/2 mt-3 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none"
                                                                   ::class="{ 'rotate-180': open }" />
                                        @endif
                                        <div x-show="open" x-transition
                                             class="absolute z-[80] w-full mt-1 bg-white dark:bg-slate-800 rounded-xl
                                                    border border-slate-200 dark:border-slate-700 shadow-xl overflow-hidden">
                                            <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                                                <input x-ref="searchInput" x-model="search" type="text"
                                                       placeholder="Filtrar cursos..."
                                                       class="w-full px-3 py-1.5 text-sm lato-regular rounded-lg border
                                                              bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                                              border-slate-200 dark:border-slate-700
                                                              focus:outline-none focus:ring-2 focus:ring-blue-400/40" />
                                            </div>
                                            <div class="max-h-44 overflow-y-auto">
                                                {{-- opção nenhum --}}
                                                <button type="button"
                                                        @click="open = false; search = ''"
                                                        wire:click="$set('inlineActionTreinamentoId', null)"
                                                        class="w-full flex items-center gap-2.5 px-3 py-2 text-sm lato-regular
                                                               hover:bg-slate-50 dark:hover:bg-slate-700/60 transition cursor-pointer text-left">
                                                    <x-lucide-circle-slash class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                                    <span class="text-slate-400 italic">Nenhum curso vinculado</span>
                                                </button>
                                                @foreach ($this->treinamentosDisponiveis as $tr)
                                                    <button type="button"
                                                            x-show="search === '' || '{{ strtolower($tr->titulo) }}'.includes(search.toLowerCase())"
                                                            @click="open = false; search = ''"
                                                            wire:click="$set('inlineActionTreinamentoId', {{ $tr->id }})"
                                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm lato-regular
                                                                   hover:bg-blue-50 dark:hover:bg-blue-900/20 transition cursor-pointer text-left
                                                                   {{ $inlineActionTreinamentoId === $tr->id ? 'bg-blue-50 dark:bg-blue-900/20' : '' }}">
                                                        <x-lucide-graduation-cap class="w-3.5 h-3.5 text-blue-400 shrink-0" />
                                                        <span class="flex-1 text-slate-700 dark:text-slate-200 truncate">{{ $tr->titulo }}</span>
                                                        <span class="text-[11px] text-slate-400 shrink-0">{{ $tr->carga_horaria }}</span>
                                                        @if ($inlineActionTreinamentoId === $tr->id)
                                                            <x-lucide-check class="w-3 h-3 text-blue-500 shrink-0" />
                                                        @endif
                                                    </button>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                @endif

                                {{-- Descrição --}}
                                <textarea wire:model="inlineActionDesc" rows="2"
                                          placeholder="Descrição opcional (instituição, link, detalhes...)"
                                          class="w-full px-3.5 py-2 text-sm lato-regular rounded-xl border resize-none
                                                 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                 border-slate-200 dark:border-slate-700
                                                 focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition"></textarea>

                                {{-- Botões inline --}}
                                <div class="flex items-center justify-end gap-2">
                                    <button wire:click="resetInlineActionForm" type="button"
                                            class="px-3 py-1.5 text-xs lato-bold rounded-lg border border-slate-200 dark:border-slate-700
                                                   text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                        Cancelar
                                    </button>
                                    <button wire:click="saveInlineAction" type="button"
                                            class="px-4 py-1.5 text-xs lato-bold rounded-lg
                                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                                                   shadow-sm shadow-blue-500/20 transition cursor-pointer disabled:opacity-60"
                                        wire:loading.attr="disabled">
                                        <span wire:loading.remove wire:target="saveInlineAction" class="flex items-center gap-1.5">
                                            {{ $editingInlineActionId || $editingInlineActionIndex !== null ? 'Salvar' : '+ Adicionar' }}
                                        </span>
                                        <span wire:loading wire:target="saveInlineAction" class="flex items-center gap-1.5">
                                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        </span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

                </div>{{-- fim overflow-y-auto --}}

                {{-- Rodapé fixo --}}
                <div class="shrink-0 px-6 py-4 border-t border-slate-100 dark:border-slate-700
                            bg-white dark:bg-slate-800 flex gap-3">
                    <button wire:click="$set('goalModal', false)" type="button"
                            class="flex-1 py-2.5 text-sm lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                                   text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="saveGoal" type="button"
                            class="flex-1 py-2.5 text-sm lato-bold rounded-xl
                                   bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                   shadow-md shadow-blue-500/20 transition cursor-pointer disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveGoal" class="flex items-center gap-1.5">
                            {{ isset($goalModalMode) && $goalModalMode === 'edit' ? 'Salvar alterações' : 'Adicionar competência' }}
                        </span>
                        <span wire:loading wire:target="saveGoal" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif
