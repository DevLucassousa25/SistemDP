<div class="p-4 sm:p-6 lg:p-8"
     x-data
     x-effect="document.body.style.overflow = ($wire.modalOpen || $wire.confirmDelete) ? 'hidden' : ''">

    {{-- Componente do modal de perguntas --}}
    <livewire:components.ui.modal.survey-questions-modal />

    {{-- Componente do modal de respondentes --}}
    <livewire:components.ui.modal.survey-respondents-modal />

    {{-- Componente do modal de templates --}}
    <livewire:components.ui.modal.survey-templates-modal />

    {{-- ───── Cabeçalho ─────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-center sm:text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white font-display tracking-tight lato-black">
                Pesquisas de Satisfação
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5 lato-regular">
                @if (auth()->user()?->isGerente())
                    Crie pesquisas para o seu departamento
                @else
                    Crie e gerencie pesquisas para colaboradores
                @endif
            </p>
        </div>

        @if (auth()->user()?->podeGerenciarPesquisas())
            <div class="w-full sm:w-auto flex flex-wrap items-center gap-2">
                <a href="{{ route('pesquisas.comparativo') }}"
                   class="flex items-center justify-center gap-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                          hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-600 dark:text-slate-300
                          text-sm font-medium px-4 py-2.5 rounded-lg transition cursor-pointer lato-bold">
                    <x-lucide-git-compare class="w-4 h-4" />
                    <span>Comparativo</span>
                </a>
                <button type="button" wire:click="abrirModal"
                    class="flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm transition cursor-pointer lato-bold">
                    <x-lucide-plus class="w-4 h-4" />
                    <span>Nova pesquisa</span>
                </button>
            </div>
        @endif
    </div>

    {{-- ───── Cards de estatísticas ─────────────────────────────────── --}}
    <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
        <div class="bg-[#F1F5F9] dark:bg-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-slate-400 lato-regular">Total</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">{{ $this->stats['total'] }}</p>
            </div>
            <div class="text-slate-400">
                <x-lucide-clipboard-list class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>

        <div class="bg-[#D1FAE5] dark:bg-emerald-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-emerald-200 lato-regular">Ativas</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">{{ $this->stats['ativas'] }}</p>
            </div>
            <div class="text-emerald-500">
                <x-lucide-play-circle class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>

        <div class="bg-[#FEF3C7] dark:bg-amber-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-amber-200 lato-regular">Rascunhos</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">{{ $this->stats['rascunho'] }}</p>
            </div>
            <div class="text-amber-500">
                <x-lucide-file-edit class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>

        <div class="bg-[#E0E7FF] dark:bg-indigo-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-indigo-200 lato-regular">Encerradas</p>
                <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">{{ $this->stats['encerradas'] }}</p>
            </div>
            <div class="text-indigo-500">
                <x-lucide-check-circle class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>
    </div>

    {{-- ───── Barra de filtros ──────────────────────────────────────── --}}
    <div class="mt-6 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por título ou descrição..."
                class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition" />
        </div>

        {{-- Filtros de status: disponível para RH/Admin e Gerentes --}}
        @if (auth()->user()?->podeGerenciarPesquisas())
            <div class="flex items-center gap-1 bg-slate-50 dark:bg-slate-700 rounded-lg p-1 border border-slate-200 dark:border-slate-600 overflow-x-auto">
                @foreach (['todas' => 'Todas', 'ativa' => 'Ativas', 'rascunho' => 'Rascunhos', 'encerrada' => 'Encerradas'] as $valor => $label)
                    <button type="button" wire:click="$set('statusFilter', '{{ $valor }}')"
                        @class([
                            'px-3 py-1.5 text-xs lato-bold rounded-md transition cursor-pointer whitespace-nowrap',
                            'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-sm' => $statusFilter === $valor,
                            'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' => $statusFilter !== $valor,
                        ])>
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        @endif

        @if (trim($search) !== '' || $statusFilter !== 'todas')
            <button type="button" wire:click="limparFiltros"
                class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 transition cursor-pointer whitespace-nowrap">
                <x-lucide-x class="w-3 h-3" />
                Limpar
            </button>
        @endif
    </div>

    {{-- ───── Grid de pesquisas ─────────────────────────────────────── --}}
    <div class="mt-6">
        @php
            $idsRespondidos = \App\Models\SurveyResponse::where('user_id', auth()->id())
                ->whereNotNull('completed_at')
                ->pluck('survey_id')
                ->flip()
                ->all();
        @endphp

        @if ($this->surveys->isEmpty())
            <div class="bg-white dark:bg-slate-800 border border-dashed border-slate-100 dark:border-slate-700 rounded-2xl p-10 flex flex-col items-center text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                    <x-lucide-clipboard-list class="w-7 h-7 text-slate-400" />
                </div>
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Nenhuma pesquisa encontrada</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-1 max-w-sm">
                    @if (auth()->user()?->isRhOuDp())
                        Crie uma nova pesquisa para começar a coletar feedback dos colaboradores.
                    @elseif (auth()->user()?->isGerente())
                        Crie pesquisas de satisfação direcionadas ao seu departamento.
                    @else
                        Assim que houver pesquisas ativas para você, elas aparecerão aqui.
                    @endif
                </p>
                @if (auth()->user()?->podeGerenciarPesquisas() && trim($search) === '' && $statusFilter === 'todas')
                    <button type="button" wire:click="abrirModal"
                        class="mt-4 flex items-center justify-center gap-2 bg-emerald-500 hover:bg-emerald-600 text-white text-xs lato-bold px-4 py-2 rounded-lg transition cursor-pointer">
                        <x-lucide-plus class="w-3.5 h-3.5" />
                        Criar primeira pesquisa
                    </button>
                @endif
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @foreach ($this->surveys as $survey)
                    @php
                        $authUser         = auth()->user();
                        $creadorEhGerente = $survey->creator?->isGerente() ?? false;

                        // Pesquisa criada por gerente → só o próprio criador pode gerir.
                        // Pesquisa criada por RH/Admin → qualquer RH/Admin pode gerir.
                        $podeGerir = $creadorEhGerente
                            ? ($authUser->id === $survey->created_by)
                            : $authUser->isRhOuDp();

                        $statusClasses = match ($survey->status) {
                            'ativa'     => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                            'rascunho'  => 'bg-amber-50 text-amber-700 border border-amber-200',
                            'encerrada' => 'bg-slate-100 text-slate-600 border border-slate-200',
                            default     => 'bg-slate-100 text-slate-600 border border-slate-200',
                        };
                        $statusDot = match ($survey->status) {
                            'ativa'     => 'bg-emerald-500 animate-pulse',
                            'rascunho'  => 'bg-amber-500',
                            'encerrada' => 'bg-slate-400',
                            default     => 'bg-slate-400',
                        };
                        $audienceCount = $survey->target_audience === 'departamentos'
                            ? count($survey->target_department_ids ?? [])
                            : null;

                        $jaRespondeu = isset($idsRespondidos[$survey->id]);
                    @endphp

                    <div class="group relative bg-white dark:bg-slate-800 rounded-2xl p-5 border flex flex-col transition-colors duration-200
                                border-slate-100 dark:border-slate-700">

                        {{-- Header do card --}}
                        <div class="flex items-start gap-3 mb-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shrink-0 shadow-sm">
                                <x-lucide-clipboard-list class="w-5 h-5 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-slate-800 dark:text-white lato-black leading-tight line-clamp-2" title="{{ $survey->title }}">
                                    {{ $survey->title }}
                                </h3>
                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold lato-bold px-2 py-0.5 rounded-full {{ $statusClasses }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                        {{ $survey->status_label }}
                                    </span>

                                    @if ($jaRespondeu)
                                        <span class="inline-flex items-center gap-1 text-[11px] lato-bold px-2 py-0.5 rounded-full
                                                     bg-emerald-100 text-emerald-700 border border-emerald-200
                                                     dark:bg-emerald-900/30 dark:text-emerald-400 dark:border-emerald-700/50">
                                            <x-lucide-check class="w-3 h-3" stroke-width="2.5" />
                                            Respondida
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        {{-- Descrição --}}
                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular line-clamp-3 min-h-[3rem]">
                            {{ $survey->description ?: 'Sem descrição.' }}
                        </p>

                        {{-- Divisor --}}
                        <div class="my-3 border-t border-slate-100 dark:border-slate-700"></div>

                        {{-- Meta --}}
                        <div class="space-y-1.5 text-xs">
                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                <x-lucide-calendar class="w-3.5 h-3.5 text-slate-400" />
                                <span class="lato-regular">
                                    @if ($survey->start_date && $survey->end_date)
                                        {{ $survey->start_date->format('d/m/Y') }} até {{ $survey->end_date->format('d/m/Y') }}
                                    @elseif ($survey->start_date)
                                        A partir de {{ $survey->start_date->format('d/m/Y') }}
                                    @elseif ($survey->end_date)
                                        Até {{ $survey->end_date->format('d/m/Y') }}
                                    @else
                                        Sem período definido
                                    @endif
                                </span>
                            </div>

                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                @if ($survey->target_audience === 'todos')
                                    <x-lucide-users class="w-3.5 h-3.5 text-slate-400" />
                                    <span class="lato-regular">Todos os colaboradores</span>
                                @else
                                    <x-lucide-building-2 class="w-3.5 h-3.5 text-slate-400" />
                                    <span class="lato-regular">{{ $audienceCount }} {{ $audienceCount === 1 ? 'departamento' : 'departamentos' }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-2 text-slate-600 dark:text-slate-300">
                                @if ($survey->is_anonymous)
                                    <x-lucide-eye-off class="w-3.5 h-3.5 text-slate-400" />
                                    <span class="lato-regular">Respostas anônimas</span>
                                @else
                                    <x-lucide-eye class="w-3.5 h-3.5 text-slate-400" />
                                    <span class="lato-regular">Respostas identificadas</span>
                                @endif
                            </div>
                        </div>

                        {{-- Rodapé / ações --}}
                        <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-400 lato-regular min-w-0">
                                <x-lucide-user-circle class="w-3.5 h-3.5 shrink-0" />
                                <span class="truncate">{{ $survey->creator?->name ?? 'Sistema' }}</span>
                            </div>

                            @if ($podeGerir)
                                {{-- Ações para RH/Admin ou gerente dono da pesquisa --}}
                                <div class="flex items-center gap-1 shrink-0" x-data="{ open: false }" @click.away="open = false">

                                    {{-- Ativar / Encerrar --}}
                                    @if ($survey->status === 'rascunho')
                                        <button type="button" wire:click="ativar({{ $survey->id }})"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition cursor-pointer" title="Ativar pesquisa">
                                            <x-lucide-play class="w-3.5 h-3.5" />
                                        </button>
                                    @elseif ($survey->status === 'ativa')
                                        <button type="button" wire:click="encerrar({{ $survey->id }})"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition cursor-pointer" title="Encerrar pesquisa">
                                            <x-lucide-square class="w-3.5 h-3.5" />
                                        </button>
                                    @endif

                                    {{-- Gerenciar perguntas --}}
                                    <button type="button"
                                        wire:click="$dispatch('abrir-modal-perguntas', { surveyId: {{ $survey->id }} })"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-violet-600 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition cursor-pointer"
                                        title="Gerenciar perguntas">
                                        <x-lucide-list-checks class="w-3.5 h-3.5" />
                                    </button>

                                    {{-- Ver respondentes --}}
                                    <button type="button"
                                        wire:click="$dispatch('abrir-modal-respondentes', { surveyId: {{ $survey->id }} })"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition cursor-pointer"
                                        title="Ver respondentes">
                                        <x-lucide-users class="w-3.5 h-3.5" />
                                    </button>

                                    {{-- Dashboard de resultados --}}
                                    <a href="{{ route('pesquisas.resultados', $survey->id) }}"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer"
                                        title="Ver resultados e análises">
                                        <x-lucide-bar-chart-2 class="w-3.5 h-3.5" />
                                    </a>

                                    {{-- Editar --}}
                                    <button type="button" wire:click="editar({{ $survey->id }})"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer" title="Editar pesquisa">
                                        <x-lucide-pencil class="w-3.5 h-3.5" />
                                    </button>

                                    {{-- Mais ações --}}
                                    <button type="button" @click="open = !open"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer" title="Mais ações">
                                        <x-lucide-more-vertical class="w-3.5 h-3.5" />
                                    </button>

                                    <div x-show="open" x-transition.opacity.duration.150ms style="display:none;"
                                        class="absolute right-4 bottom-10 z-20 w-48 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg shadow-xl overflow-hidden">
                                        <button type="button" wire:click="duplicar({{ $survey->id }})" @click="open = false"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-slate-700 dark:text-slate-200 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition cursor-pointer">
                                            <x-lucide-copy class="w-3.5 h-3.5 text-slate-400" />
                                            Duplicar
                                        </button>
                                        {{-- <button type="button"
                                            wire:click="abrirSalvarTemplate({{ $survey->id }})"
                                            @click="open = false"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer">
                                            <x-lucide-bookmark-plus class="w-3.5 h-3.5" />
                                            Salvar como template
                                        </button> --}}
                                        <div class="border-t border-slate-100 dark:border-slate-600"></div>
                                        <button type="button" wire:click="confirmarExclusao({{ $survey->id }})" @click="open = false"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            Excluir
                                        </button>
                                    </div>
                                </div>

                            @else
                                {{-- Colaborador comum ou gerente vendo pesquisa de outro --}}
                                @if ($jaRespondeu)
                                    <span class="flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg
                                                 bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400
                                                 border border-emerald-200 dark:border-emerald-700/50 cursor-default">
                                        <x-lucide-check-circle class="w-3.5 h-3.5" />
                                        Respondida
                                    </span>
                                @else
                                    <a href="{{ route('pesquisas.responder', $survey->id) }}"
                                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg bg-emerald-50 hover:bg-emerald-100 dark:bg-emerald-900/20 dark:hover:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400 transition cursor-pointer">
                                        <x-lucide-edit-3 class="w-3.5 h-3.5" />
                                        Responder
                                    </a>
                                @endif
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    </div>

    {{-- ───── Modal de criação/edição ──────────────────────────────── --}}
    @if ($modalOpen)
        @php $isGerente = auth()->user()?->isGerente(); @endphp

        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="fecharModal"></div>

            <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh] flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden">

                {{-- Header --}}
                <div class="relative flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 sm:py-5">
                        <div class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-sm">
                            <x-lucide-clipboard-list class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white lato-black leading-tight">
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
                            <button type="button"
                                wire:click="$dispatch('abrir-modal-templates')"
                                class="shrink-0 flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg
                                       bg-indigo-50 hover:bg-indigo-100 text-indigo-600 border border-indigo-200
                                       dark:bg-indigo-900/20 dark:hover:bg-indigo-900/30 dark:text-indigo-400 dark:border-indigo-700/40
                                       transition cursor-pointer"
                                title="Começar com um template pronto">
                                <x-lucide-layout-template class="w-3.5 h-3.5" />
                                <span class="hidden sm:inline">Template</span>
                            </button>
                        @endif
                        <button wire:click="fecharModal" class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 transition cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 sm:py-5 space-y-4 bg-slate-50/40 dark:bg-slate-800">

                    {{-- Banner: template aplicado --}}
                    @if (! empty($pendingTemplateQuestions))
                        <div class="flex items-center gap-2.5 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/10
                                    border border-indigo-200 dark:border-indigo-700/40">
                            <x-lucide-layout-template class="w-4 h-4 text-indigo-500 shrink-0" />
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lato-bold text-indigo-700 dark:text-indigo-300">
                                    Template aplicado — {{ count($pendingTemplateQuestions) }} {{ count($pendingTemplateQuestions) === 1 ? 'pergunta' : 'perguntas' }} serão criadas automaticamente
                                </p>
                                <p class="text-[11px] lato-regular text-indigo-600/70 dark:text-indigo-400/70 mt-0.5">
                                    Você pode editar as perguntas depois de criar a pesquisa.
                                </p>
                            </div>
                            <button type="button"
                                wire:click="$set('pendingTemplateQuestions', null)"
                                class="shrink-0 w-6 h-6 flex items-center justify-center rounded-lg
                                       text-indigo-400 hover:text-indigo-600 hover:bg-indigo-100
                                       dark:hover:bg-indigo-900/30 transition cursor-pointer">
                                <x-lucide-x class="w-3.5 h-3.5" />
                            </button>
                        </div>
                    @endif

                    {{-- Título --}}
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                            Título <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <x-lucide-type class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input type="text" wire:model.blur="title" placeholder="Ex: Clima organizacional - 1º trimestre"
                                class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition
                                       {{ $errors->has('title') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}">
                        </div>
                        @error('title') <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p> @enderror
                    </div>

                    {{-- Descrição --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                            Descrição
                        </label>
                        <textarea wire:model.blur="description" rows="3" placeholder="Descreva o objetivo e o contexto desta pesquisa..."
                            class="w-full px-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition resize-none
                                   {{ $errors->has('description') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}"></textarea>
                        @error('description') <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p> @enderror
                    </div>

                    {{-- Datas --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Data inicial</label>
                            <div class="relative">
                                <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="date" wire:model.blur="startDate"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition border border-slate-200 dark:border-slate-600">
                            </div>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Data final</label>
                            <div class="relative">
                                <x-lucide-calendar-check class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="date" wire:model.blur="endDate"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition
                                           {{ $errors->has('endDate') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}">
                            </div>
                            @error('endDate') <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Público-alvo --}}
                    <div class="space-y-2">
                        <label class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Público-alvo</label>

                        @if ($isGerente)
                            {{-- Gerente: público-alvo bloqueado no próprio departamento --}}
                            <div class="flex items-center gap-2.5 p-3 rounded-lg border border-emerald-200 bg-emerald-50 dark:bg-emerald-900/10 dark:border-emerald-800/40">
                                <x-lucide-lock class="w-4 h-4 text-emerald-600 shrink-0" />
                                <div>
                                    <p class="text-xs lato-bold text-emerald-700 dark:text-emerald-300">Seu departamento</p>
                                    <p class="text-[11px] lato-regular text-emerald-600/70 dark:text-emerald-400/70 mt-0.5">
                                        Pesquisas criadas pelo gerente são restritas ao próprio departamento.
                                    </p>
                                </div>
                            </div>
                        @else
                            {{-- RH/Admin: seletor completo --}}
                            <div class="grid grid-cols-2 gap-2">
                                <label @class([
                                    'flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition',
                                    'bg-emerald-50 border-emerald-300 text-emerald-700' => $targetAudience === 'todos',
                                    'bg-white border-slate-200 text-slate-600 hover:border-slate-300' => $targetAudience !== 'todos',
                                ])>
                                    <input type="radio" wire:model.live="targetAudience" value="todos" class="hidden">
                                    <x-lucide-users class="w-4 h-4" />
                                    <span class="text-xs lato-bold">Todos</span>
                                </label>
                                <label @class([
                                    'flex items-center gap-2 p-3 rounded-lg border cursor-pointer transition',
                                    'bg-emerald-50 border-emerald-300 text-emerald-700' => $targetAudience === 'departamentos',
                                    'bg-white border-slate-200 text-slate-600 hover:border-slate-300' => $targetAudience !== 'departamentos',
                                ])>
                                    <input type="radio" wire:model.live="targetAudience" value="departamentos" class="hidden">
                                    <x-lucide-building-2 class="w-4 h-4" />
                                    <span class="text-xs lato-bold">Departamentos</span>
                                </label>
                            </div>

                            @if ($targetAudience === 'departamentos')
                                <div class="mt-2 p-3 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg max-h-44 overflow-y-auto space-y-1.5">
                                    @forelse ($this->departamentos as $dept)
                                        <label class="flex items-center gap-2 p-1.5 rounded hover:bg-slate-50 dark:hover:bg-slate-600 cursor-pointer">
                                            <input type="checkbox" wire:model.live="targetDepartmentIds" value="{{ $dept->id }}"
                                                class="w-4 h-4 rounded border-slate-300 text-emerald-500 focus:ring-emerald-400">
                                            <span class="text-sm lato-regular text-slate-700 dark:text-slate-200">{{ $dept->name }}</span>
                                        </label>
                                    @empty
                                        <p class="text-xs text-slate-400 lato-regular text-center py-2">Nenhum departamento cadastrado.</p>
                                    @endforelse
                                </div>
                            @endif
                        @endif
                    </div>

                    {{-- Ciclo de Avaliação (somente RH/Admin) --}}
                    @if (! $isGerente && $this->availableEvaluationCycles->isNotEmpty())
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                <x-lucide-link class="w-3.5 h-3.5 text-violet-500" />
                                Ciclo de Avaliação
                                <span class="normal-case lato-regular text-slate-400 font-normal ml-1">(opcional)</span>
                            </label>
                            <div class="relative">
                                <x-lucide-refresh-cw class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <select wire:model="evaluationCycleId"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                           text-slate-800 dark:text-white focus:outline-none focus:ring-2
                                           focus:ring-violet-500/40 focus:border-violet-400 transition
                                           border border-slate-200 dark:border-slate-600 appearance-none cursor-pointer">
                                    <option value="">Nenhum ciclo vinculado</option>
                                    @foreach ($this->availableEvaluationCycles as $cycle)
                                        <option value="{{ $cycle->id }}">
                                            {{ $cycle->name }}
                                            ({{ $cycle->status === 'active' ? 'Ativo' : 'Rascunho' }})
                                        </option>
                                    @endforeach
                                </select>
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
                            <label class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Status</label>
                            <div class="relative">
                                <x-lucide-activity class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <select wire:model="status"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700 text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition border border-slate-200 dark:border-slate-600 appearance-none cursor-pointer">
                                    <option value="rascunho">Rascunho</option>
                                    <option value="ativa">Ativa</option>
                                    <option value="encerrada">Encerrada</option>
                                </select>
                            </div>
                        </div>

                        {{-- Anonimato --}}
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">Respostas</label>
                            <div class="flex items-center justify-between gap-3 p-2.5 rounded-lg border {{ $isAnonymous ? 'bg-emerald-50 border-emerald-200' : 'bg-slate-50 border-slate-200' }}">
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
                                    <div class="w-10 h-5.5 bg-slate-300 rounded-full peer peer-checked:bg-emerald-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-4.5 after:w-4.5 after:transition-all peer-checked:after:translate-x-full transition-colors" style="width:2.5rem; height:1.375rem;"></div>
                                </label>
                            </div>
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex items-center justify-between gap-3">
                    <p class="hidden sm:flex items-center gap-1.5 text-xs text-slate-400 lato-regular">
                        <x-lucide-info class="w-3.5 h-3.5" />
                        Campos com <span class="text-red-500 font-semibold">*</span> são obrigatórios
                    </p>
                    <div class="flex gap-2 flex-1 sm:flex-initial">
                        <button wire:click="fecharModal"
                            class="flex-1 sm:flex-none px-4 py-2 text-sm lato-bold rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer">
                            Cancelar
                        </button>
                        <button wire:click="salvar" wire:loading.attr="disabled" wire:target="salvar"
                            class="flex-1 sm:flex-none px-4 sm:px-6 py-2 text-sm lato-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white flex items-center justify-center gap-2 cursor-pointer shadow-sm transition">
                            <svg wire:loading wire:target="salvar" class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span wire:loading.remove wire:target="salvar" class="flex items-center gap-1.5">
                                <x-lucide-check class="w-4 h-4" />
                                {{ $modalMode === 'create' ? 'Criar pesquisa' : 'Salvar alterações' }}
                            </span>
                            <span wire:loading wire:target="salvar">Salvando...</span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif

    {{-- ───── Modal de confirmação de exclusão ─────────────────────── --}}
    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="cancelarExclusao"></div>
            <div class="relative bg-white dark:bg-slate-800 w-full max-w-md rounded-2xl shadow-2xl z-10 overflow-hidden">
                <div class="p-6 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-red-50 dark:bg-red-900/20 flex items-center justify-center mx-auto mb-3">
                        <x-lucide-trash-2 class="w-7 h-7 text-red-500" />
                    </div>
                    <h3 class="text-lg font-bold text-slate-800 dark:text-white lato-black">Excluir pesquisa?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-2">
                        Esta ação não pode ser desfeita. Todas as perguntas e respostas associadas serão removidas.
                    </p>
                </div>
                <div class="px-6 py-3 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/40 flex gap-2">
                    <button wire:click="cancelarExclusao" class="flex-1 px-4 py-2 text-sm lato-bold rounded-lg text-slate-600 bg-white border border-slate-200 hover:bg-slate-50 transition cursor-pointer">Cancelar</button>
                    <button wire:click="excluir" class="flex-1 px-4 py-2 text-sm lato-bold rounded-lg bg-red-500 hover:bg-red-600 text-white transition cursor-pointer">Excluir</button>
                </div>
            </div>
        </div>
    @endif

</div>
