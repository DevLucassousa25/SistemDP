<div class="p-4 sm:p-6 lg:p-8 space-y-6" wire:poll.15s="refreshIfIdle">

    {{-- ── Cabeçalho ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white lato-black tracking-tight">
                DPI — Desenvolvimento Profissional Individual
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mt-1">
                Planeje e acompanhe seu crescimento profissional ao longo do ano
            </p>
        </div>

        <div class="flex items-center gap-3 shrink-0">

            {{-- Indicador de atualização em tempo real --}}
            <span wire:loading.flex wire:target="refreshIfIdle"
                  class="hidden items-center gap-1.5 text-[11px] text-slate-400 lato-regular">
                <span class="w-1.5 h-1.5 rounded-full bg-indigo-400 animate-pulse"></span>
                Atualizando...
            </span>

        {{-- Seletor de ano --}}
        <div class="relative">
            <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <select wire:model.live="selectedYear"
                    class="appearance-none pl-9 pr-8 py-2.5 text-sm lato-bold rounded-xl
                           bg-slate-50 dark:bg-slate-700
                           text-slate-700 dark:text-slate-200
                           border border-slate-200 dark:border-slate-600
                           focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                           cursor-pointer transition">
                @foreach ($this->years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
            <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
        </div>
        </div>{{-- fim flex gap-3 --}}
    </div>

    {{-- ── Tabs ─────────────────────────────────────────────────────── --}}
    @if (auth()->user()->isGerente() || auth()->user()->isRhOuDp())
        @php $pendingCount = auth()->user()->isRhOuDp() ? $this->pendingGerentePlans->count() : 0; @endphp
        <div class="flex flex-wrap items-center gap-1 bg-white dark:bg-slate-800
                    border border-slate-200 dark:border-slate-700
                    rounded-xl p-1 w-fit">

            {{-- Tab Meu Plano --}}
            <button wire:click="$set('activeTab','meu_plano')" type="button"
                    class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                           {{ $activeTab === 'meu_plano'
                               ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                               : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <x-lucide-notebook-pen class="w-3.5 h-3.5" /> Meu Plano
            </button>

            {{-- Tab Equipe --}}
            <button wire:click="$set('activeTab','equipe')" type="button"
                    class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                           {{ $activeTab === 'equipe'
                               ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                               : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <x-lucide-users class="w-3.5 h-3.5" /> Equipe
            </button>

            {{-- Tab Análises --}}
            <button wire:click="$set('activeTab','analises')" type="button"
                    class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                           {{ $activeTab === 'analises'
                               ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                               : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                <x-lucide-brain-circuit class="w-3.5 h-3.5" /> Análises
            </button>

            {{-- Tab Visualizações --}}
            @if (auth()->user()->isRhOuDp() || auth()->user()->isGerente())
                <button wire:click="$set('activeTab','visualizacoes')" type="button"
                        class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === 'visualizacoes'
                                   ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                                   : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-calendar-days class="w-3.5 h-3.5" /> Visualizações
                </button>
            @endif

            {{-- Tab Aprovações: apenas RH/DP --}}
            @if (auth()->user()->isRhOuDp())
                <button wire:click="$set('activeTab','aprovacoes')" type="button"
                        class="relative flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === 'aprovacoes'
                                   ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                                   : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-clipboard-check class="w-3.5 h-3.5" /> Aprovações
                    @if ($pendingCount > 0)
                        <span class="absolute -top-1.5 -right-1.5 min-w-[18px] h-[18px] px-1
                                     rounded-full bg-amber-500 text-white text-[10px] lato-bold
                                     flex items-center justify-center leading-none">
                            {{ $pendingCount }}
                        </span>
                    @endif
                </button>

                {{-- Tab Relatório: apenas RH/DP --}}
                <button wire:click="$set('activeTab','relatorio')" type="button"
                        class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === 'relatorio'
                                   ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                                   : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-file-bar-chart class="w-3.5 h-3.5" /> Relatório
                </button>
            @endif

            {{-- Tab Relatório do Setor: apenas Gerente --}}
            @if (auth()->user()->isGerente())
                <button wire:click="$set('activeTab','relatorio_setor')" type="button"
                        class="flex items-center gap-1.5 px-4 py-1.5 text-sm lato-bold rounded-lg transition cursor-pointer
                               {{ $activeTab === 'relatorio_setor'
                                   ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                                   : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    <x-lucide-bar-chart-3 class="w-3.5 h-3.5" /> Meu Setor
                </button>
            @endif
        </div>
    @endif

    @include('livewire.pages.dpi.tabs.meu-plano')
    @include('livewire.pages.dpi.tabs.equipe')
    @include('livewire.pages.dpi.tabs.aprovacoes')
    @include('livewire.pages.dpi.tabs.relatorio')
    @include('livewire.pages.dpi.tabs.relatorio-setor')
    @include('livewire.pages.dpi.tabs.analises')
    @include('livewire.pages.dpi.tabs.visualizacoes')

    {{-- ════ MODAIS ════ --}}
    @include('livewire.pages.dpi.modals._modal-competencia')
    @include('livewire.pages.dpi.modals._modal-feedback')
    @include('livewire.pages.dpi.modals._modal-delete')
    @include('livewire.pages.dpi.modals._modal-upload')
    @include('livewire.pages.dpi.modals._modal-meeting')
</div>{{-- /wire:poll wrapper --}}
