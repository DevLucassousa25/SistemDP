<div class="p-4 sm:p-6 lg:p-8" x-data
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
            <h1
                class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white font-display tracking-tight lato-black">
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
                    class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20 text-white text-sm font-medium px-4 py-2.5 rounded-lg transition cursor-pointer lato-bold">
                    <x-lucide-circle-plus class="w-4 h-4" />
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
                <p
                    class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">
                    {{ $this->stats['total'] }}</p>
            </div>
            <div class="text-slate-400">
                <x-lucide-clipboard-list class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>

        <div class="bg-[#D1FAE5] dark:bg-emerald-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-emerald-200 lato-regular">Ativas</p>
                <p
                    class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">
                    {{ $this->stats['ativas'] }}</p>
            </div>
            <div class="text-emerald-500">
                <x-lucide-play-circle class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>

        <div class="bg-[#FEF3C7] dark:bg-amber-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-amber-200 lato-regular">Rascunhos</p>
                <p
                    class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">
                    {{ $this->stats['rascunho'] }}</p>
            </div>
            <div class="text-amber-500">
                <x-lucide-file-edit class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>

        <div class="bg-[#E0E7FF] dark:bg-indigo-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-700 dark:text-indigo-200 lato-regular">Encerradas</p>
                <p
                    class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">
                    {{ $this->stats['encerradas'] }}</p>
            </div>
            <div class="text-indigo-500">
                <x-lucide-check-circle class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>
    </div>

    {{-- ───── Barra de filtros ──────────────────────────────────────── --}}
    <div
        class="mt-6 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row gap-3">
        <div class="relative flex-1">
            <x-lucide-search
                class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Buscar por título ou descrição..."
                class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 border border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition" />
        </div>

        {{-- Filtros de status: disponível para RH/Admin e Gerentes --}}
        @if (auth()->user()?->podeGerenciarPesquisas())
            <div
                class="flex items-center gap-1 bg-slate-50 dark:bg-slate-700 rounded-lg p-1 border border-slate-200 dark:border-slate-600 overflow-x-auto">
                @foreach (['todas' => 'Todas', 'ativa' => 'Ativas', 'rascunho' => 'Rascunhos', 'encerrada' => 'Encerradas'] as $valor => $label)
                    <button type="button" wire:click="$set('statusFilter', '{{ $valor }}')"
                        @class([
                            'px-3 py-1.5 text-xs lato-bold rounded-md transition cursor-pointer whitespace-nowrap',
                            'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400 shadow-sm' =>
                                $statusFilter === $valor,
                            'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' =>
                                $statusFilter !== $valor,
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
            <div
                class="bg-white dark:bg-slate-800 border border-dashed border-slate-100 dark:border-slate-700 rounded-2xl p-10 flex flex-col items-center text-center">
                <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                    <x-lucide-clipboard-list class="w-7 h-7 text-slate-400" />
                </div>
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Nenhuma pesquisa encontrada
                </h3>
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
                        $authUser = auth()->user();
                        $creadorEhGerente = $survey->creator?->isGerente() ?? false;

                        // Pesquisa criada por gerente → só o próprio criador pode gerir.
                        // Pesquisa criada por RH/Admin → qualquer RH/Admin pode gerir.
                        $podeGerir = $creadorEhGerente ? $authUser->id === $survey->created_by : $authUser->isRhOuDp();

                        $statusClasses = match ($survey->status) {
                            'ativa' => 'bg-emerald-50 text-emerald-700 border border-emerald-200',
                            'rascunho' => 'bg-amber-50 text-amber-700 border border-amber-200',
                            'encerrada' => 'bg-slate-100 text-slate-600 border border-slate-200',
                            default => 'bg-slate-100 text-slate-600 border border-slate-200',
                        };
                        $statusDot = match ($survey->status) {
                            'ativa' => 'bg-emerald-500 animate-pulse',
                            'rascunho' => 'bg-amber-500',
                            'encerrada' => 'bg-slate-400',
                            default => 'bg-slate-400',
                        };
                        $audienceCount =
                            $survey->target_audience === 'departamentos'
                                ? count($survey->target_department_ids ?? [])
                                : null;

                        $jaRespondeu = isset($idsRespondidos[$survey->id]);

                        // Pesquisas de clima com perguntas de avaliação do gestor (visível ao DP/RH)
                        $temAvaliacaoGestor =
                            $authUser->isRhOuDp() &&
                            $survey->questions->where('is_manager_evaluation', true)->isNotEmpty();
                    @endphp

                    <div
                        class="group relative bg-white dark:bg-slate-800 rounded-2xl p-5 border flex flex-col transition-colors duration-200
                                border-slate-100 dark:border-slate-700">

                        {{-- Header do card --}}
                        <div class="flex items-start gap-3 mb-3">
                            <div
                                class="w-11 h-11 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shrink-0 shadow-sm">
                                <x-lucide-clipboard-list class="w-5 h-5 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-slate-800 dark:text-white lato-black leading-tight line-clamp-2"
                                    title="{{ $survey->title }}">
                                    {{ $survey->title }}
                                </h3>
                                <div class="mt-1.5 flex flex-wrap items-center gap-1.5">
                                    <span
                                        class="inline-flex items-center gap-1.5 text-[11px] font-semibold lato-bold px-2 py-0.5 rounded-full {{ $statusClasses }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                                        {{ $survey->status_label }}
                                    </span>

                                    @if ($temAvaliacaoGestor)
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] lato-bold px-2 py-0.5 rounded-full
                                                     bg-violet-50 text-violet-700 border border-violet-200
                                                     dark:bg-violet-900/20 dark:text-violet-300 dark:border-violet-700/50">
                                            <x-lucide-user-check class="w-3 h-3" />
                                            Clima
                                        </span>
                                    @endif

                                    @if ($jaRespondeu)
                                        <span
                                            class="inline-flex items-center gap-1 text-[11px] lato-bold px-2 py-0.5 rounded-full
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
                                        {{ $survey->start_date->format('d/m/Y') }} até
                                        {{ $survey->end_date->format('d/m/Y') }}
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
                                    <span class="lato-regular">{{ $audienceCount }}
                                        {{ $audienceCount === 1 ? 'departamento' : 'departamentos' }}</span>
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
                        <div
                            class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-2">
                            <div class="flex items-center gap-1.5 text-[11px] text-slate-400 lato-regular min-w-0">
                                <x-lucide-user-circle class="w-3.5 h-3.5 shrink-0" />
                                <span class="truncate">{{ $survey->creator?->name ?? 'Sistema' }}</span>
                            </div>

                            @if ($podeGerir)
                                {{-- Ações para RH/Admin ou gerente dono da pesquisa --}}
                                <div class="flex items-center gap-1 shrink-0" x-data="{ open: false }"
                                    @click.away="open = false">

                                    {{-- Ativar / Encerrar --}}
                                    @if ($survey->status === 'rascunho')
                                        <button type="button" wire:click="ativar({{ $survey->id }})"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition cursor-pointer"
                                            title="Ativar pesquisa">
                                            <x-lucide-play class="w-3.5 h-3.5" />
                                        </button>
                                    @elseif ($survey->status === 'ativa')
                                        <button type="button" wire:click="encerrar({{ $survey->id }})"
                                            class="w-7 h-7 flex items-center justify-center rounded-lg text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900/30 transition cursor-pointer"
                                            title="Encerrar pesquisa">
                                            <x-lucide-square class="w-3.5 h-3.5" />
                                        </button>
                                    @endif

                                    {{-- Gerenciar perguntas --}}
                                    <button type="button"
                                        wire:click="$dispatch('abrir-modal-perguntas', { surveyId: {{ $survey->id }} })"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:text-indigo-600 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition cursor-pointer"
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
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
                                        title="Editar pesquisa">
                                        <x-lucide-pencil class="w-3.5 h-3.5" />
                                    </button>

                                    {{-- Mais ações --}}
                                    <button type="button" @click="open = !open"
                                        class="w-7 h-7 flex items-center justify-center rounded-lg text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
                                        title="Mais ações">
                                        <x-lucide-more-vertical class="w-3.5 h-3.5" />
                                    </button>

                                    <div x-show="open" x-transition.opacity.duration.150ms style="display:none;"
                                        class="absolute right-4 bottom-10 z-20 w-48 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg shadow-xl overflow-hidden">
                                        <button type="button" wire:click="duplicar({{ $survey->id }})"
                                            @click="open = false"
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
                                        <button type="button" wire:click="confirmarExclusao({{ $survey->id }})"
                                            @click="open = false"
                                            class="w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-red-600 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer disabled:opacity-60"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="confirmarExclusao" class="flex items-center gap-1.5">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            Excluir
                                            </span>
                                            <span wire:loading wire:target="confirmarExclusao" class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            </span>
                                        </button>
                                    </div>
                                </div>
                            @else
                                {{-- Colaborador comum ou gerente vendo pesquisa de outro --}}
                                @if ($jaRespondeu)
                                    <span
                                        class="flex items-center gap-1.5 px-3 py-1.5 text-xs lato-bold rounded-lg
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
    @include('livewire.pages.surveys.modals._modal-pesquisa')
    @include('livewire.pages.surveys.modals._modal-excluir')

</div>
