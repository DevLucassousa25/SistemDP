<div x-data
     x-effect="document.body.style.overflow = ($wire.modalOpen) ? 'hidden' : ''">

    @if ($modalOpen)
        <div class="fixed inset-0 z-[60] flex items-end sm:items-center justify-center sm:p-4">

            {{-- Overlay --}}
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"
                 wire:click="fecharModal"></div>

            {{-- Painel --}}
            <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-3xl max-h-[95vh] sm:max-h-[90vh]
                        rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 flex flex-col overflow-hidden">

                {{-- ══════════════════════════════════════════════
                     MODO: SELECIONAR TEMPLATE
                ═══════════════════════════════════════════════ --}}
                @if ($mode === 'select')

                    {{-- Header --}}
                    <div class="flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                        <div class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 sm:py-5">
                            <div class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-sm">
                                <x-lucide-layout-template class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white lato-black leading-tight">Biblioteca de Templates</h2>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5">Escolha um modelo pronto ou crie o seu</p>
                            </div>
                            <button wire:click="abrirCriarTemplate"
                                class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg
                                       bg-indigo-500 hover:bg-indigo-600 text-white transition cursor-pointer shadow-sm">
                                <x-lucide-plus class="w-3.5 h-3.5" />
                                <span class="hidden sm:inline">Novo template</span>
                            </button>
                            <button wire:click="fecharModal"
                                class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-700 transition cursor-pointer">
                                <x-lucide-x class="w-4 h-4" />
                            </button>
                        </div>

                        {{-- Busca + filtros --}}
                        <div class="px-4 sm:px-6 pb-3 flex flex-col sm:flex-row gap-2">
                            <div class="relative flex-1">
                                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="text" wire:model.live.debounce.250ms="search"
                                    placeholder="Buscar templates..."
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition" />
                            </div>
                            <div class="flex items-center gap-1 bg-slate-50 dark:bg-slate-700 rounded-lg p-1 border border-slate-200 dark:border-slate-600 overflow-x-auto">
                                @foreach(['todos' => 'Todos', 'nps' => 'NPS', 'clima' => 'Clima', 'feedback' => 'Feedback', 'rh' => 'RH', 'custom' => 'Meus'] as $val => $label)
                                    <button type="button" wire:click="$set('categoryFilter', '{{ $val }}')"
                                        @class([
                                            'px-2.5 py-1.5 text-xs lato-bold rounded-md transition cursor-pointer whitespace-nowrap',
                                            'bg-white dark:bg-slate-800 text-indigo-600 dark:text-indigo-400 shadow-sm' => $categoryFilter === $val,
                                            'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' => $categoryFilter !== $val,
                                        ])>{{ $label }}</button>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 bg-slate-50/40 dark:bg-slate-800/60">
                        @if ($this->templates->isEmpty())
                            <div class="flex flex-col items-center justify-center py-14 text-center">
                                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                                    <x-lucide-layout-template class="w-7 h-7 text-slate-400" />
                                </div>
                                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Nenhum template encontrado</h3>
                                <p class="text-xs text-slate-400 lato-regular mt-1 max-w-xs">Tente outro filtro ou crie um template personalizado.</p>
                                <button wire:click="abrirCriarTemplate"
                                    class="mt-4 flex items-center gap-1.5 px-4 py-2 text-xs lato-bold rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white transition cursor-pointer shadow-sm">
                                    <x-lucide-plus class="w-3.5 h-3.5" />
                                    Criar template
                                </button>
                            </div>
                        @else
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @foreach ($this->templates as $template)
                                    @php
                                        $authUser  = auth()->user();
                                        $canEdit   = ! $template->is_system && ($template->created_by === $authUser->id || $authUser->isRhOuDp());
                                        $canDelete = $canEdit;
                                        $iconBg    = match($template->category) {
                                            'nps'      => 'from-blue-500 to-indigo-500',
                                            'clima'    => 'from-emerald-500 to-teal-500',
                                            'feedback' => 'from-violet-500 to-purple-600',
                                            'rh'       => 'from-amber-500 to-orange-500',
                                            default    => 'from-slate-400 to-slate-500',
                                        };
                                    @endphp

                                    <div class="group bg-white dark:bg-slate-700/60 rounded-xl border border-slate-100 dark:border-slate-700 shadow-sm hover:shadow-md hover:border-indigo-200 dark:hover:border-indigo-700/50 transition-all duration-200 flex flex-col overflow-hidden">

                                        <div class="p-4 flex-1">
                                            <div class="flex items-start gap-3 mb-2">
                                                <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $iconBg }} flex items-center justify-center shrink-0 shadow-sm">
                                                    @if ($template->category === 'nps')      <x-lucide-star class="w-5 h-5 text-white" />
                                                    @elseif ($template->category === 'clima')    <x-lucide-sun class="w-5 h-5 text-white" />
                                                    @elseif ($template->category === 'feedback') <x-lucide-message-square class="w-5 h-5 text-white" />
                                                    @elseif ($template->category === 'rh')       <x-lucide-briefcase class="w-5 h-5 text-white" />
                                                    @else <x-lucide-layout-template class="w-5 h-5 text-white" />
                                                    @endif
                                                </div>
                                                <div class="flex-1 min-w-0">
                                                    <div class="flex items-center gap-1.5 flex-wrap">
                                                        <h3 class="text-sm lato-black text-slate-800 dark:text-white leading-tight">{{ $template->name }}</h3>
                                                        @if ($template->is_system)
                                                            <span class="inline-flex items-center gap-1 text-[10px] lato-bold px-1.5 py-0.5 rounded-full bg-indigo-50 text-indigo-600 border border-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-700/40">
                                                                <x-lucide-shield class="w-2.5 h-2.5" />Sistema
                                                            </span>
                                                        @else
                                                            <span class="inline-flex items-center gap-1 text-[10px] lato-bold px-1.5 py-0.5 rounded-full bg-slate-100 text-slate-500 border border-slate-200 dark:bg-slate-600 dark:text-slate-300 dark:border-slate-500">
                                                                <x-lucide-user class="w-2.5 h-2.5" />{{ $template->creator?->name ?? 'Personalizado' }}
                                                            </span>
                                                        @endif
                                                    </div>
                                                    <span class="inline-flex items-center mt-1 text-[10px] lato-bold px-2 py-0.5 rounded-full border {{ $template->category_color }}">
                                                        {{ $template->category_label }}
                                                    </span>
                                                </div>
                                            </div>

                                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular line-clamp-2 min-h-[2.5rem]">
                                                {{ $template->description ?: 'Sem descrição.' }}
                                            </p>

                                            <div class="mt-2.5 flex items-center gap-1.5 text-[11px] text-slate-400 lato-regular">
                                                <x-lucide-list class="w-3.5 h-3.5" />
                                                {{ $template->questions_count }} {{ $template->questions_count === 1 ? 'pergunta' : 'perguntas' }}
                                            </div>
                                        </div>

                                        <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50/60 dark:bg-slate-800/40 flex items-center gap-2">
                                            <button type="button" wire:click="usarTemplate({{ $template->id }})"
                                                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white transition cursor-pointer shadow-sm">
                                                <x-lucide-check class="w-3.5 h-3.5" />Usar template
                                            </button>
                                            @if ($canEdit)
                                                <button type="button" wire:click="editarTemplate({{ $template->id }})"
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer shrink-0"
                                                    title="Editar template">
                                                    <x-lucide-pencil class="w-3.5 h-3.5" />
                                                </button>
                                            @endif
                                            @if ($canDelete)
                                                <button type="button"
                                                    wire:click="excluirTemplate({{ $template->id }})"
                                                    wire:confirm="Excluir o template '{{ addslashes($template->name) }}'? Esta ação não pode ser desfeita."
                                                    class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer shrink-0"
                                                    title="Excluir template">
                                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Footer --}}
                    <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-between gap-3">
                        <p class="text-xs text-slate-400 lato-regular hidden sm:block">
                            {{ $this->templates->count() }} {{ $this->templates->count() === 1 ? 'template disponível' : 'templates disponíveis' }}
                        </p>
                        <button wire:click="fecharModal"
                            class="px-4 py-2 text-sm lato-bold rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                            Cancelar
                        </button>
                    </div>

                {{-- ══════════════════════════════════════════════
                     MODO: CRIAR / EDITAR TEMPLATE (FORM)
                ═══════════════════════════════════════════════ --}}
                @elseif ($mode === 'form')

                    {{-- Header --}}
                    <div class="flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                        <div class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 sm:py-5">
                            <button wire:click="voltarParaBiblioteca"
                                class="shrink-0 w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-700 transition cursor-pointer mt-0.5">
                                <x-lucide-arrow-left class="w-4 h-4" />
                            </button>
                            <div class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shadow-sm">
                                @if ($editingTemplateId)
                                    <x-lucide-pencil class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                                @else
                                    <x-lucide-plus class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                                @endif
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white lato-black leading-tight">
                                    {{ $editingTemplateId ? 'Editar Template' : 'Novo Template' }}
                                </h2>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5">
                                    {{ $editingTemplateId ? 'Atualize as informações e perguntas do template' : 'Crie um modelo personalizado com suas próprias perguntas' }}
                                </p>
                            </div>
                            <button wire:click="fecharModal"
                                class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-700 transition cursor-pointer">
                                <x-lucide-x class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    {{-- Body --}}
                    <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 space-y-5 bg-slate-50/40 dark:bg-slate-800/60">

                        {{-- ── Metadados ────────────────────────────────── --}}
                        <div class="bg-white dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-700 p-4 space-y-4">
                            <h3 class="text-xs lato-black text-slate-500 dark:text-slate-400 uppercase tracking-widest">Informações do template</h3>

                            {{-- Nome --}}
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1 text-xs lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                    Nome <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <x-lucide-type class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                    <input type="text" wire:model.blur="formName" placeholder="Ex: Avaliação Mensal do Departamento"
                                        class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-slate-50 dark:bg-slate-600 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition {{ $errors->has('formName') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}">
                                </div>
                                @error('formName') <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p> @enderror
                            </div>

                            {{-- Descrição --}}
                            <div class="space-y-1.5">
                                <label class="text-xs lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Descrição</label>
                                <textarea wire:model.blur="formDescription" rows="2" placeholder="Descreva quando usar este template..."
                                    class="w-full px-3 py-2 text-sm lato-regular rounded-lg bg-slate-50 dark:bg-slate-600 text-slate-800 dark:text-white placeholder-slate-400 border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 focus:border-indigo-400 transition resize-none"></textarea>
                            </div>

                            {{-- Categoria --}}
                            <div class="space-y-1.5">
                                <label class="text-xs lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Categoria <span class="text-red-500">*</span></label>
                                <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                                    @foreach(['nps' => ['NPS','from-blue-500 to-indigo-500'], 'clima' => ['Clima','from-emerald-500 to-teal-500'], 'feedback' => ['Feedback','from-violet-500 to-purple-600'], 'rh' => ['RH','from-amber-500 to-orange-500'], 'custom' => ['Personalizado','from-slate-400 to-slate-500']] as $val => [$label, $gradient])
                                        <label @class([
                                            'flex flex-col items-center gap-1.5 p-2.5 rounded-lg border cursor-pointer transition',
                                            'border-indigo-300 bg-indigo-50 dark:bg-indigo-900/20 dark:border-indigo-700/50' => $formCategory === $val,
                                            'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 hover:border-slate-300' => $formCategory !== $val,
                                        ])>
                                            <input type="radio" wire:model.live="formCategory" value="{{ $val }}" class="hidden">
                                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br {{ $gradient }} flex items-center justify-center shadow-sm">
                                                @if ($val === 'nps')      <x-lucide-star class="w-3.5 h-3.5 text-white" />
                                                @elseif ($val === 'clima')    <x-lucide-sun class="w-3.5 h-3.5 text-white" />
                                                @elseif ($val === 'feedback') <x-lucide-message-square class="w-3.5 h-3.5 text-white" />
                                                @elseif ($val === 'rh')       <x-lucide-briefcase class="w-3.5 h-3.5 text-white" />
                                                @else <x-lucide-layout-template class="w-3.5 h-3.5 text-white" />
                                                @endif
                                            </div>
                                            <span @class([
                                                'text-[10px] lato-bold text-center leading-tight',
                                                'text-indigo-700 dark:text-indigo-300' => $formCategory === $val,
                                                'text-slate-500 dark:text-slate-400'   => $formCategory !== $val,
                                            ])>{{ $label }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('formCategory') <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p> @enderror
                            </div>
                        </div>

                        {{-- ── Builder de perguntas ─────────────────────── --}}
                        <div class="bg-white dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden">
                            {{-- Cabeçalho da seção --}}
                            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
                                <div class="flex items-center gap-2">
                                    <x-lucide-list-checks class="w-4 h-4 text-indigo-500" />
                                    <h3 class="text-xs lato-black text-slate-700 dark:text-slate-200 uppercase tracking-widest">
                                        Perguntas
                                        <span class="ml-1 text-slate-400 font-normal normal-case tracking-normal">({{ count($formQuestions) }})</span>
                                    </h3>
                                </div>
                                @if (! $showQuestionForm)
                                    <button wire:click="abrirFormularioPergunta"
                                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200 dark:bg-indigo-900/20 dark:hover:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-700/40 transition cursor-pointer">
                                        <x-lucide-plus class="w-3.5 h-3.5" />
                                        Adicionar pergunta
                                    </button>
                                @endif
                            </div>

                            <div class="p-4 space-y-3">

                                {{-- Erro: sem perguntas --}}
                                @error('formQuestions')
                                    <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                        <x-lucide-alert-circle class="w-3.5 h-3.5" />{{ $message }}
                                    </p>
                                @enderror

                                {{-- ── Formulário inline de pergunta ─────── --}}
                                @if ($showQuestionForm)
                                    <div x-data x-init="$nextTick(() => $el.scrollIntoView({ behavior: 'smooth', block: 'nearest' }))"
                                         class="bg-indigo-50/60 dark:bg-indigo-900/10 rounded-xl border border-indigo-200 dark:border-indigo-700/40 p-4 space-y-3">

                                        <div class="flex items-center justify-between">
                                            <h4 class="text-xs lato-black text-indigo-700 dark:text-indigo-300 flex items-center gap-1.5">
                                                <x-lucide-plus-circle class="w-4 h-4" />
                                                {{ $editingQuestionIndex !== null ? 'Editar pergunta' : 'Nova pergunta' }}
                                            </h4>
                                            <button wire:click="cancelarFormPergunta"
                                                class="w-6 h-6 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                                                <x-lucide-x class="w-3.5 h-3.5" />
                                            </button>
                                        </div>

                                        {{-- Tipo --}}
                                        <div class="space-y-1.5">
                                            <label class="text-xs lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Tipo <span class="text-red-500">*</span></label>
                                            <div class="grid grid-cols-3 gap-2">
                                                @foreach(['escala' => ['Escala 0–10','lucide-bar-chart-2','border-blue-400 bg-blue-50 dark:bg-blue-900/20 text-blue-700'], 'multipla_escolha' => ['Múltipla escolha','lucide-list-checks','border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700'], 'texto_livre' => ['Texto livre','lucide-align-left','border-amber-400 bg-amber-50 dark:bg-amber-900/20 text-amber-700']] as $val => [$label, $icon, $activeClass])
                                                    <label @class([
                                                        'flex flex-col items-center gap-1.5 p-2.5 rounded-xl border-2 cursor-pointer transition',
                                                        $activeClass => $qType === $val,
                                                        'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-500 hover:border-slate-300' => $qType !== $val,
                                                    ])>
                                                        <input type="radio" wire:model.live="qType" value="{{ $val }}" class="hidden">
                                                        <x-dynamic-component :component="$icon" class="w-5 h-5" />
                                                        <span class="text-[10px] lato-bold text-center leading-tight">{{ $label }}</span>
                                                    </label>
                                                @endforeach
                                            </div>
                                            @error('qType') <p class="text-xs text-red-500 lato-regular">{{ $message }}</p> @enderror
                                        </div>

                                        {{-- Texto --}}
                                        <div class="space-y-1.5">
                                            <label class="text-xs lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Pergunta <span class="text-red-500">*</span></label>
                                            <textarea wire:model.blur="qText" rows="2" placeholder="Digite a pergunta..."
                                                class="w-full px-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition resize-none {{ $errors->has('qText') ? 'border-red-400' : '' }}"></textarea>
                                            @error('qText') <p class="text-xs text-red-500 lato-regular flex items-center gap-1"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p> @enderror
                                        </div>

                                        {{-- Opções (múltipla escolha) --}}
                                        @if ($qType === 'multipla_escolha')
                                            <div class="space-y-2">
                                                <label class="text-xs lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Opções <span class="text-red-500">*</span></label>
                                                @foreach ($qOptions as $i => $opt)
                                                    <div class="flex items-center gap-2">
                                                        <div class="w-5 h-5 rounded-full border-2 border-slate-300 dark:border-slate-500 shrink-0 flex items-center justify-center">
                                                            <span class="text-[9px] lato-bold text-slate-400">{{ $i + 1 }}</span>
                                                        </div>
                                                        <input type="text" wire:model.blur="qOptions.{{ $i }}" placeholder="Opção {{ $i + 1 }}"
                                                            class="flex-1 px-2.5 py-1.5 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition">
                                                        @if (count($qOptions) > 2)
                                                            <button wire:click="removerOpcao({{ $i }})"
                                                                class="w-6 h-6 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer shrink-0">
                                                                <x-lucide-x class="w-3 h-3" />
                                                            </button>
                                                        @endif
                                                    </div>
                                                @endforeach
                                                @error('qOptions') <p class="text-xs text-red-500 lato-regular">{{ $message }}</p> @enderror
                                                @error('qOptions.*') <p class="text-xs text-red-500 lato-regular">{{ $message }}</p> @enderror
                                                <button wire:click="adicionarOpcao"
                                                    class="flex items-center gap-1.5 text-xs lato-bold text-indigo-600 hover:text-indigo-700 dark:text-indigo-400 cursor-pointer transition">
                                                    <x-lucide-plus class="w-3.5 h-3.5" />
                                                    Adicionar opção
                                                </button>
                                            </div>
                                        @endif

                                        {{-- Obrigatória --}}
                                        <div class="flex items-center justify-between pt-1">
                                            <span class="text-xs lato-bold text-slate-600 dark:text-slate-300">Resposta obrigatória</span>
                                            <label class="relative inline-flex items-center cursor-pointer">
                                                <input type="checkbox" wire:model.live="qRequired" class="sr-only peer">
                                                <div class="w-10 h-5 bg-slate-300 rounded-full peer peer-checked:bg-indigo-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-full transition-colors" style="width:2.5rem;height:1.375rem;"></div>
                                            </label>
                                        </div>

                                        {{-- Botões --}}
                                        <div class="flex gap-2 pt-1">
                                            <button wire:click="cancelarFormPergunta"
                                                class="flex-1 px-3 py-2 text-xs lato-bold rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-300 transition cursor-pointer">
                                                Cancelar
                                            </button>
                                            <button wire:click="salvarPerguntaTemplate"
                                                class="flex-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white transition cursor-pointer">
                                                <x-lucide-check class="w-3.5 h-3.5" />
                                                {{ $editingQuestionIndex !== null ? 'Atualizar' : 'Adicionar' }}
                                            </button>
                                        </div>
                                    </div>
                                @endif

                                {{-- ── Lista de perguntas ────────────────── --}}
                                @if (empty($formQuestions) && ! $showQuestionForm)
                                    <div class="flex flex-col items-center justify-center py-8 text-center">
                                        <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-2">
                                            <x-lucide-list class="w-5 h-5 text-slate-400" />
                                        </div>
                                        <p class="text-xs text-slate-400 lato-regular">Nenhuma pergunta adicionada ainda.</p>
                                        <button wire:click="abrirFormularioPergunta"
                                            class="mt-3 flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200 dark:bg-indigo-900/20 dark:text-indigo-400 dark:border-indigo-700/40 transition cursor-pointer">
                                            <x-lucide-plus class="w-3.5 h-3.5" />Adicionar primeira pergunta
                                        </button>
                                    </div>
                                @else
                                    <div class="space-y-2">
                                        @foreach ($formQuestions as $idx => $q)
                                            @php
                                                $isEditing = $editingQuestionIndex === $idx;
                                                $typeColor = match($q['type']) {
                                                    'escala'           => 'text-blue-500 bg-blue-50 dark:bg-blue-900/20',
                                                    'multipla_escolha' => 'text-emerald-500 bg-emerald-50 dark:bg-emerald-900/20',
                                                    default            => 'text-amber-500 bg-amber-50 dark:bg-amber-900/20',
                                                };
                                                $typeLabel = match($q['type']) {
                                                    'escala'           => 'Escala',
                                                    'multipla_escolha' => 'Múltipla',
                                                    default            => 'Texto',
                                                };
                                            @endphp

                                            <div @class([
                                                'flex items-start gap-3 p-3 rounded-xl border transition',
                                                'border-indigo-200 bg-indigo-50/40 dark:border-indigo-700/40 dark:bg-indigo-900/5' => $isEditing,
                                                'border-slate-100 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800/30 hover:border-slate-200' => ! $isEditing,
                                            ])>
                                                {{-- Número --}}
                                                <span class="w-6 h-6 rounded-lg bg-slate-200 dark:bg-slate-600 text-slate-600 dark:text-slate-300 text-xs lato-black flex items-center justify-center shrink-0 mt-0.5">
                                                    {{ $idx + 1 }}
                                                </span>

                                                {{-- Conteúdo --}}
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 leading-snug line-clamp-2">
                                                        {{ $q['question'] }}
                                                    </p>
                                                    <div class="flex items-center gap-2 mt-1 flex-wrap">
                                                        <span class="inline-flex items-center gap-1 text-[10px] lato-bold px-1.5 py-0.5 rounded-md {{ $typeColor }}">
                                                            @if ($q['type'] === 'escala')
                                                                <x-lucide-bar-chart-2 class="w-2.5 h-2.5" />
                                                            @elseif ($q['type'] === 'multipla_escolha')
                                                                <x-lucide-list-checks class="w-2.5 h-2.5" />
                                                            @else
                                                                <x-lucide-align-left class="w-2.5 h-2.5" />
                                                            @endif
                                                            {{ $typeLabel }}
                                                        </span>
                                                        @if ($q['required'])
                                                            <span class="text-[10px] lato-bold text-red-500">* Obrigatória</span>
                                                        @endif
                                                        @if ($q['type'] === 'multipla_escolha' && ! empty($q['options']))
                                                            <span class="text-[10px] lato-regular text-slate-400">{{ count($q['options']) }} opções</span>
                                                        @endif
                                                    </div>
                                                </div>

                                                {{-- Ações --}}
                                                <div class="flex items-center gap-0.5 shrink-0">
                                                    <button wire:click="moverPerguntaCima({{ $idx }})" @disabled($idx === 0)
                                                        class="w-6 h-6 flex items-center justify-center rounded text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 transition cursor-pointer">
                                                        <x-lucide-chevron-up class="w-3.5 h-3.5" />
                                                    </button>
                                                    <button wire:click="moverPerguntaBaixo({{ $idx }})" @disabled($idx === count($formQuestions) - 1)
                                                        class="w-6 h-6 flex items-center justify-center rounded text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 disabled:opacity-30 transition cursor-pointer">
                                                        <x-lucide-chevron-down class="w-3.5 h-3.5" />
                                                    </button>
                                                    <button wire:click="editarPerguntaTemplate({{ $idx }})"
                                                        class="w-6 h-6 flex items-center justify-center rounded text-slate-400 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer">
                                                        <x-lucide-pencil class="w-3 h-3" />
                                                    </button>
                                                    <button wire:click="removerPerguntaTemplate({{ $idx }})"
                                                        class="w-6 h-6 flex items-center justify-center rounded text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                                                        <x-lucide-trash-2 class="w-3 h-3" />
                                                    </button>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>

                                    @if (! $showQuestionForm)
                                        <button wire:click="abrirFormularioPergunta"
                                            class="w-full mt-1 flex items-center justify-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg border border-dashed border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-indigo-400 hover:text-indigo-500 hover:bg-indigo-50/40 dark:hover:bg-indigo-900/10 transition cursor-pointer">
                                            <x-lucide-plus class="w-3.5 h-3.5" />
                                            Adicionar pergunta
                                        </button>
                                    @endif
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-between gap-3">
                        <p class="text-xs text-slate-400 lato-regular hidden sm:flex items-center gap-1.5">
                            <x-lucide-info class="w-3.5 h-3.5" />
                            {{ count($formQuestions) }} {{ count($formQuestions) === 1 ? 'pergunta' : 'perguntas' }} adicionada{{ count($formQuestions) === 1 ? '' : 's' }}
                        </p>
                        <div class="flex gap-2 flex-1 sm:flex-initial">
                            <button wire:click="voltarParaBiblioteca"
                                class="flex-1 sm:flex-none px-4 py-2 text-sm lato-bold rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                                Cancelar
                            </button>
                            <button wire:click="salvarTemplate"
                                wire:loading.attr="disabled" wire:target="salvarTemplate"
                                class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-5 py-2 text-sm lato-bold rounded-lg bg-indigo-500 hover:bg-indigo-600 text-white transition cursor-pointer shadow-sm">
                                <svg wire:loading wire:target="salvarTemplate" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                                </svg>
                                <span wire:loading.remove wire:target="salvarTemplate" class="flex items-center gap-1.5">
                                    <x-lucide-check class="w-4 h-4" />
                                    {{ $editingTemplateId ? 'Salvar alterações' : 'Criar template' }}
                                </span>
                                <span wire:loading wire:target="salvarTemplate">Salvando...</span>
                            </button>
                        </div>
                    </div>

                {{-- ══════════════════════════════════════════════
                     MODO: SALVAR PESQUISA EXISTENTE COMO TEMPLATE
                ═══════════════════════════════════════════════ --}}
                @elseif ($mode === 'save')

                    {{-- Header --}}
                    <div class="flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                        <div class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 sm:py-5">
                            <div class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shadow-sm">
                                <x-lucide-bookmark-plus class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white lato-black leading-tight">Salvar como Template</h2>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5">Reutilize esta pesquisa como modelo em criações futuras</p>
                            </div>
                            <button wire:click="fecharModal"
                                class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:text-white dark:hover:bg-slate-700 transition cursor-pointer">
                                <x-lucide-x class="w-4 h-4" />
                            </button>
                        </div>
                    </div>

                    <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 space-y-4 bg-slate-50/40 dark:bg-slate-800/60">
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1 text-xs lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Nome <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <x-lucide-type class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="text" wire:model.blur="templateName" placeholder="Ex: NPS Trimestral do Departamento"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-400 transition {{ $errors->has('templateName') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}">
                            </div>
                            @error('templateName') <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p> @enderror
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Descrição</label>
                            <textarea wire:model.blur="templateDescription" rows="3" placeholder="Descreva quando usar este template..."
                                class="w-full px-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-amber-500/40 focus:border-amber-400 transition resize-none"></textarea>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Categoria <span class="text-red-500">*</span></label>
                            <div class="grid grid-cols-3 sm:grid-cols-5 gap-2">
                                @foreach(['nps' => ['NPS','from-blue-500 to-indigo-500'], 'clima' => ['Clima','from-emerald-500 to-teal-500'], 'feedback' => ['Feedback','from-violet-500 to-purple-600'], 'rh' => ['RH','from-amber-500 to-orange-500'], 'custom' => ['Personalizado','from-slate-400 to-slate-500']] as $val => [$label, $gradient])
                                    <label @class([
                                        'flex flex-col items-center gap-1.5 p-2.5 rounded-lg border cursor-pointer transition',
                                        'border-amber-300 bg-amber-50 dark:bg-amber-900/10 dark:border-amber-700/50' => $templateCategory === $val,
                                        'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 hover:border-slate-300' => $templateCategory !== $val,
                                    ])>
                                        <input type="radio" wire:model.live="templateCategory" value="{{ $val }}" class="hidden">
                                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br {{ $gradient }} flex items-center justify-center shadow-sm">
                                            @if ($val === 'nps')      <x-lucide-star class="w-3.5 h-3.5 text-white" />
                                            @elseif ($val === 'clima')    <x-lucide-sun class="w-3.5 h-3.5 text-white" />
                                            @elseif ($val === 'feedback') <x-lucide-message-square class="w-3.5 h-3.5 text-white" />
                                            @elseif ($val === 'rh')       <x-lucide-briefcase class="w-3.5 h-3.5 text-white" />
                                            @else <x-lucide-layout-template class="w-3.5 h-3.5 text-white" />
                                            @endif
                                        </div>
                                        <span @class(['text-[10px] lato-bold text-center leading-tight', 'text-amber-700 dark:text-amber-300' => $templateCategory === $val, 'text-slate-500 dark:text-slate-400' => $templateCategory !== $val])>{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="flex items-start gap-2 p-3 rounded-lg bg-indigo-50 dark:bg-indigo-900/10 border border-indigo-100 dark:border-indigo-800/40">
                            <x-lucide-info class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
                            <p class="text-xs text-indigo-700 dark:text-indigo-300 lato-regular">Todas as perguntas desta pesquisa serão copiadas para o template.</p>
                        </div>
                    </div>

                    <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-end gap-2">
                        <button wire:click="fecharModal"
                            class="px-4 py-2 text-sm lato-bold rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 dark:bg-slate-700 dark:border-slate-600 dark:text-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="salvarComoTemplate"
                            wire:loading.attr="disabled" wire:target="salvarComoTemplate"
                            class="flex items-center gap-2 px-5 py-2 text-sm lato-bold rounded-lg bg-amber-500 hover:bg-amber-600 text-white transition cursor-pointer shadow-sm">
                            <svg wire:loading wire:target="salvarComoTemplate" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="salvarComoTemplate" class="flex items-center gap-1.5">
                                <x-lucide-bookmark-plus class="w-4 h-4" />Salvar template
                            </span>
                            <span wire:loading wire:target="salvarComoTemplate">Salvando...</span>
                        </button>
                    </div>

                @endif
            </div>
        </div>
    @endif
</div>
