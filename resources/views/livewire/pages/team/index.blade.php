<div class="p-4 sm:p-6 lg:p-8">

    {{-- ───── Cabeçalho ─────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-center sm:text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white font-display tracking-tight lato-black">
                Meu Time
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5 lato-regular">
                Conheça os membros da sua equipe
            </p>
        </div>

        @if ($this->meuDepartamento)
            <div class="flex items-center gap-2 px-3 py-2 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl self-center sm:self-auto">
                <div class="w-8 h-8 rounded-lg bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shrink-0">
                    <x-lucide-building-2 class="w-4 h-4 text-white" />
                </div>
                <div class="min-w-0">
                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Departamento</p>
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 lato-bold truncate">{{ $this->meuDepartamento->name }}</p>
                </div>
            </div>
        @endif
    </div>

    {{-- ───── Sem departamento ──────────────────────────────────────── --}}
    @if (! $this->meuDepartamento)
        <div class="mt-10 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-8 sm:p-12 flex flex-col items-center text-center">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mb-4">
                <x-lucide-building-2 class="w-8 h-8 text-amber-500" />
            </div>
            <h3 class="text-lg font-bold text-slate-800 dark:text-white lato-black">Você ainda não está em um time</h3>
            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-2 max-w-md">
                Para visualizar os membros da sua equipe, peça ao RH para vincular você a um departamento.
            </p>
        </div>
    @else

        {{-- ───── Cards de estatísticas ─────────────────────────────── --}}
        <div class="mt-8 grid grid-cols-2 sm:grid-cols-4 gap-3 sm:gap-4">
            <div class="bg-[#F1F5F9] dark:bg-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-slate-400 lato-regular">Total do Time</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">{{ $this->stats['total'] }}</p>
                </div>
                <div class="text-slate-400">
                    <x-lucide-users class="w-8 h-8 sm:w-10 sm:h-10" />
                </div>
            </div>

            <div class="bg-[#D1FAE5] dark:bg-emerald-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-emerald-200 lato-regular">Ativos</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">{{ $this->stats['ativos'] }}</p>
                </div>
                <div class="text-emerald-500">
                    <x-lucide-user-check class="w-8 h-8 sm:w-10 sm:h-10" />
                </div>
            </div>

            <div class="bg-[#FEE2E2] dark:bg-red-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-red-200 lato-regular">Inativos</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">{{ $this->stats['inativos'] }}</p>
                </div>
                <div class="text-red-500">
                    <x-lucide-user-x class="w-8 h-8 sm:w-10 sm:h-10" />
                </div>
            </div>

            <div class="bg-[#DBEAFE] dark:bg-blue-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
                <div>
                    <p class="text-xs sm:text-sm text-gray-700 dark:text-blue-200 lato-regular">Gerentes</p>
                    <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white mt-1 sm:mt-2 lato-black">{{ $this->stats['gerentes'] }}</p>
                </div>
                <div class="text-blue-500">
                    <x-lucide-briefcase class="w-8 h-8 sm:w-10 sm:h-10" />
                </div>
            </div>
        </div>

        {{-- ───── Destaque: Gerente ─────────────────────────────────── --}}
        @if ($this->gerente)
            <div class="mt-6 relative overflow-hidden bg-gradient-to-r from-emerald-500 via-emerald-500 to-teal-500 rounded-2xl">
                {{-- Decoração --}}
                <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/10"></div>
                <div class="absolute -right-16 -bottom-10 w-48 h-48 rounded-full bg-white/5"></div>

                <div class="relative flex flex-col sm:flex-row items-center gap-4 p-5 sm:p-6">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 rounded-2xl bg-white/20 backdrop-blur-sm border-2 border-white/40 flex items-center justify-center shrink-0 text-white text-xl sm:text-2xl font-bold lato-black">
                        {{ \Illuminate\Support\Str::of($this->gerente->name)->explode(' ')->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('') }}
                    </div>

                    <div class="text-center sm:text-left flex-1 min-w-0">
                        <div class="flex items-center justify-center sm:justify-start gap-2 mb-1">
                            <x-lucide-crown class="w-4 h-4 text-amber-200" />
                            <span class="text-[11px] uppercase tracking-wider text-emerald-50 lato-bold">Líder do Time</span>
                        </div>
                        <h3 class="text-lg sm:text-xl font-bold text-white lato-black truncate">{{ $this->gerente->name }}</h3>
                        <p class="text-sm text-emerald-50 lato-regular mt-0.5 truncate">{{ $this->gerente->position ?? 'Gerente' }}</p>
                        <p class="text-xs text-emerald-100/90 lato-regular mt-1 flex items-center justify-center sm:justify-start gap-1.5">
                            <x-lucide-mail class="w-3 h-3" />
                            {{ $this->gerente->email }}
                        </p>
                    </div>

                    @if (auth()->user()?->isRhOuDp())
                        <a href="{{ route('users.details', $this->gerente->id) }}"
                            class="flex items-center gap-1.5 bg-white/20 hover:bg-white/30 backdrop-blur-sm border border-white/30 text-white text-xs font-semibold lato-bold px-3 py-2 rounded-lg transition shrink-0">
                            <x-lucide-external-link class="w-3.5 h-3.5" />
                            Ver perfil
                        </a>
                    @endif
                </div>
            </div>
        @endif

        {{-- ───── Barra de filtros ──────────────────────────────────── --}}
        <div class="mt-6 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-3 sm:p-4 flex flex-col sm:flex-row gap-3">
            {{-- Busca --}}
            <div class="relative flex-1">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input type="text" wire:model.live.debounce.300ms="search"
                    placeholder="Buscar por nome, e-mail ou cargo..."
                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-slate-50 dark:bg-slate-700
                           text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500
                           border border-slate-200 dark:border-slate-600
                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                           transition duration-150" />
            </div>

            {{-- Filtro status --}}
            <div class="flex items-center gap-1 bg-slate-50 dark:bg-slate-700 rounded-lg p-1 border border-slate-200 dark:border-slate-600">
                @foreach (['todos' => 'Todos', 'ativos' => 'Ativos', 'inativos' => 'Inativos'] as $valor => $label)
                    <button type="button" wire:click="$set('statusFilter', '{{ $valor }}')"
                        @class([
                            'flex-1 sm:flex-initial px-3 py-1.5 text-xs lato-bold rounded-md transition cursor-pointer whitespace-nowrap',
                            'bg-white dark:bg-slate-800 text-emerald-600 dark:text-emerald-400' => $statusFilter === $valor,
                            'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' => $statusFilter !== $valor,
                        ])>
                        {{ $label }}
                    </button>
                @endforeach
            </div>

            @if (trim($search) !== '' || $statusFilter !== 'todos')
                <button type="button" wire:click="limparFiltros"
                    class="flex items-center justify-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg bg-slate-100 hover:bg-slate-200 dark:bg-slate-700 dark:hover:bg-slate-600 text-slate-600 dark:text-slate-300 transition cursor-pointer whitespace-nowrap">
                    <x-lucide-x class="w-3 h-3" />
                    Limpar
                </button>
            @endif
        </div>

        {{-- ───── Drawer lateral de detalhes (usuários comuns) ─────── --}}
        @if (! auth()->user()?->isRhOuDp())
            @php $membroDrawer = $this->membroSelecionado; @endphp

            {{-- Backdrop --}}
            <div
                x-data
                x-show="$wire.drawerOpen"
                x-transition.opacity.duration.200ms
                @keydown.escape.window="$wire.drawerOpen && $wire.call('fecharDrawer')"
                class="fixed inset-0 z-[60] bg-slate-900/50 backdrop-blur-sm"
                wire:click="fecharDrawer"
                style="display: none;">
            </div>

            {{-- Painel lateral --}}
            <aside
                class="fixed top-0 right-0 z-[70] h-screen w-full sm:max-w-md bg-white dark:bg-slate-800 shadow-2xl flex flex-col
                       transform transition-transform duration-300 ease-out
                       {{ $drawerOpen ? 'translate-x-0' : 'translate-x-full' }}">

                @if ($membroDrawer)
                    @php
                        $ehGerente = $membroDrawer->accessProfile?->slug === 'manager';
                        $ehAdmin   = $membroDrawer->accessProfile?->slug === 'administrator';
                        $ehRh      = $membroDrawer->accessProfile?->slug === 'hr';
                        $ehEu      = $membroDrawer->id === auth()->id();
                        $iniciais  = \Illuminate\Support\Str::of($membroDrawer->name)->explode(' ')->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
                    @endphp

                    {{-- Header com gradient --}}
                    <div class="relative overflow-hidden flex-shrink-0
                                {{ $ehGerente || $ehEu ? 'bg-gradient-to-br from-emerald-500 via-emerald-500 to-teal-500' : '' }}
                                {{ $ehAdmin && ! $ehEu ? 'bg-gradient-to-br from-red-400 to-rose-500' : '' }}
                                {{ $ehRh && ! $ehEu && ! $ehAdmin ? 'bg-gradient-to-br from-blue-400 to-indigo-500' : '' }}
                                {{ ! $ehEu && ! $ehGerente && ! $ehAdmin && ! $ehRh ? 'bg-gradient-to-br from-slate-500 to-slate-600' : '' }}">

                        {{-- Decoração --}}
                        <div class="absolute -right-8 -top-8 w-40 h-40 rounded-full bg-white/10"></div>
                        <div class="absolute -left-10 -bottom-16 w-52 h-52 rounded-full bg-white/5"></div>

                        {{-- Botão fechar --}}
                        <button type="button" wire:click="fecharDrawer"
                            class="absolute top-3 right-3 w-9 h-9 flex items-center justify-center rounded-lg bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white transition cursor-pointer z-10"
                            aria-label="Fechar">
                            <x-lucide-x class="w-4 h-4" />
                        </button>

                        <div class="relative px-6 pt-8 pb-6 flex flex-col items-center text-center">
                            <div class="w-24 h-24 rounded-2xl bg-white/20 backdrop-blur-sm border-2 border-white/40 flex items-center justify-center text-white text-3xl font-bold lato-black shadow-lg mb-3">
                                {{ $iniciais ?: '??' }}
                            </div>

                            @if ($ehGerente)
                                <span class="inline-flex items-center gap-1 bg-amber-400/90 text-amber-900 text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full mb-2">
                                    <x-lucide-crown class="w-3 h-3" />
                                    Líder do Time
                                </span>
                            @elseif ($ehEu)
                                <span class="inline-flex items-center bg-white/30 text-white text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full mb-2">
                                    Você
                                </span>
                            @endif

                            <h2 class="text-xl font-bold text-white lato-black leading-tight">{{ $membroDrawer->name }}</h2>
                            <p class="text-sm text-white/90 lato-regular mt-1">
                                {{ $membroDrawer->position ?? 'Sem cargo definido' }}
                            </p>

                            {{-- Badge status --}}
                            <div class="mt-3">
                                @if ($membroDrawer->is_active)
                                    <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-sm text-white text-[11px] font-semibold lato-bold px-2.5 py-1 rounded-full border border-white/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-300 animate-pulse"></span>
                                        Ativo
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 bg-white/20 backdrop-blur-sm text-white text-[11px] font-semibold lato-bold px-2.5 py-1 rounded-full border border-white/30">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-300"></span>
                                        Inativo
                                    </span>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- Conteúdo --}}
                    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-5 bg-slate-50/40 dark:bg-slate-800">

                        {{-- Seção: Contato --}}
                        <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <x-lucide-mail class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                                    Contato
                                </h3>
                            </div>

                            <a href="mailto:{{ $membroDrawer->email }}"
                                class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 border border-slate-100 dark:border-slate-700 transition group">
                                <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center shrink-0 group-hover:border-emerald-300">
                                    <x-lucide-at-sign class="w-4 h-4 text-slate-500 group-hover:text-emerald-600" />
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">E-mail</p>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 lato-bold truncate">{{ $membroDrawer->email }}</p>
                                </div>
                                <x-lucide-external-link class="w-4 h-4 text-slate-300 group-hover:text-emerald-500 shrink-0" />
                            </a>
                        </section>

                        {{-- Seção: Cargo e departamento --}}
                        <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <x-lucide-briefcase class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                                    Cargo e Departamento
                                </h3>
                            </div>

                            <div class="space-y-2">
                                <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                    <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center shrink-0">
                                        <x-lucide-id-card class="w-4 h-4 text-slate-500" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Cargo</p>
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 lato-bold">{{ $membroDrawer->position ?? '—' }}</p>
                                    </div>
                                </div>

                                <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                    <div class="w-9 h-9 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center shrink-0">
                                        <x-lucide-building-2 class="w-4 h-4 text-slate-500" />
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Departamento</p>
                                        <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 lato-bold truncate">{{ $membroDrawer->department?->name ?? '—' }}</p>
                                    </div>
                                </div>
                            </div>
                        </section>

                        {{-- Seção: Perfil de Acesso --}}
                        <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4">
                            <div class="flex items-center gap-2 mb-3">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <x-lucide-shield class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                                    Perfil de Acesso
                                </h3>
                            </div>

                            <div class="flex items-center gap-3 p-3 rounded-lg bg-slate-50 dark:bg-slate-800 border border-slate-100 dark:border-slate-700">
                                <div @class([
                                    'w-9 h-9 rounded-lg flex items-center justify-center shrink-0',
                                    'bg-red-50 text-red-500'         => $ehAdmin,
                                    'bg-blue-50 text-blue-500'       => $ehRh,
                                    'bg-emerald-50 text-emerald-500' => $ehGerente,
                                    'bg-slate-100 text-slate-500'    => ! $ehAdmin && ! $ehRh && ! $ehGerente,
                                ])>
                                    @if ($ehAdmin)
                                        <x-lucide-shield-check class="w-4 h-4" />
                                    @elseif ($ehRh)
                                        <x-lucide-users class="w-4 h-4" />
                                    @elseif ($ehGerente)
                                        <x-lucide-briefcase class="w-4 h-4" />
                                    @else
                                        <x-lucide-user class="w-4 h-4" />
                                    @endif
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide">Função no sistema</p>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 lato-bold truncate">
                                        {{ $membroDrawer->accessProfile?->name ?? 'Sem perfil' }}
                                    </p>
                                </div>
                            </div>
                        </section>

                        {{-- Nota de privacidade --}}
                        <div class="flex items-start gap-2 bg-slate-100/50 dark:bg-slate-700/40 border border-slate-200 dark:border-slate-700 rounded-lg px-3 py-2.5">
                            <x-lucide-info class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" />
                            <p class="text-[11px] text-slate-500 dark:text-slate-400 lato-regular leading-snug">
                                Você está vendo apenas as informações públicas do colega. Dados sensíveis só podem ser acessados pelo RH e Administradores.
                            </p>
                        </div>
                    </div>

                    {{-- Footer com ações --}}
                    <div class="flex-shrink-0 px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800 flex gap-3">
                        <a href="mailto:{{ $membroDrawer->email }}"
                            class="flex-1 flex items-center justify-center gap-1.5 px-4 py-2.5 text-sm lato-bold rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer shadow-sm shadow-emerald-200/50">
                            <x-lucide-mail class="w-4 h-4" />
                            Enviar e-mail
                        </a>
                        <button type="button" wire:click="fecharDrawer"
                            class="px-4 py-2.5 text-sm lato-bold rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-600 transition cursor-pointer">
                            Fechar
                        </button>
                    </div>
                @else
                    {{-- Fallback vazio --}}
                    <div class="flex flex-col items-center justify-center h-full p-8 text-center">
                        <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                            <x-lucide-user class="w-7 h-7 text-slate-400" />
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">Selecione um membro para ver os detalhes.</p>
                    </div>
                @endif
            </aside>
        @endif

        {{-- ───── Grid de membros ───────────────────────────────────── --}}
        <div class="mt-6">
            @if ($this->membros->isEmpty())
                <div class="bg-white dark:bg-slate-800 border border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-10 flex flex-col items-center text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                        <x-lucide-users class="w-7 h-7 text-slate-400" />
                    </div>
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Nenhum membro encontrado</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-1">
                        Tente ajustar os filtros ou limpar a busca.
                    </p>
                </div>
            @else
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4">
                    @foreach ($this->membros as $membro)
                        @php
                            $ehEu       = $membro->id === auth()->id();
                            $ehGerente  = $membro->accessProfile?->slug === 'manager';
                            $ehAdmin    = $membro->accessProfile?->slug === 'administrator';
                            $ehRh       = $membro->accessProfile?->slug === 'hr';
                            $iniciais   = \Illuminate\Support\Str::of($membro->name)->explode(' ')->take(2)->map(fn ($p) => mb_substr($p, 0, 1))->implode('');
                        @endphp

                        <div @class([
                            'group relative bg-white dark:bg-slate-800 rounded-2xl p-4 sm:p-5 border transition hover:shadow-md hover:-translate-y-0.5',
                            'border-emerald-300 dark:border-emerald-700 ring-0 ring-emerald-100 dark:ring-emerald-900/40' => $ehEu,
                            'border-slate-200 dark:border-slate-700' => ! $ehEu,
                            'opacity-70' => ! $membro->is_active,
                        ])>
                            {{-- Badge "Você" --}}
                            @if ($ehEu)
                                <span class="absolute top-3 right-3 bg-emerald-500 text-white text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full flex items-center gap-1">
                                    <x-lucide-user class="w-3 h-3" />
                                    Você
                                </span>
                            @elseif ($ehGerente)
                                <span class="absolute top-3 right-3 flex items-center gap-1 bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300 text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-800">
                                    <x-lucide-crown class="w-3 h-3" />
                                    Gerente
                                </span>
                            @elseif (! $membro->is_active)
                                <span class="absolute top-3 right-3 bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[10px] font-bold lato-bold px-2 py-0.5 rounded-full">
                                    Inativo
                                </span>
                            @endif

                            {{-- Avatar --}}
                            <div class="flex justify-center mb-3">
                                <div @class([
                                    'w-16 h-16 sm:w-20 sm:h-20 rounded-2xl flex items-center justify-center text-white text-lg sm:text-xl font-bold lato-black shadow-sm',
                                    'bg-gradient-to-br from-emerald-500 to-teal-500'    => $ehEu || $ehGerente,
                                    'bg-gradient-to-br from-red-400 to-rose-500'        => $ehAdmin && ! $ehEu,
                                    'bg-gradient-to-br from-blue-400 to-indigo-500'     => $ehRh && ! $ehEu && ! $ehAdmin,
                                    'bg-gradient-to-br from-slate-400 to-slate-500'     => ! $ehEu && ! $ehGerente && ! $ehAdmin && ! $ehRh,
                                ])>
                                    {{ $iniciais ?: '??' }}
                                </div>
                            </div>

                            {{-- Nome + Cargo --}}
                            <div class="text-center">
                                <h3 class="text-sm sm:text-base font-bold text-slate-800 dark:text-white lato-black truncate" title="{{ $membro->name }}">
                                    {{ $membro->name }}
                                </h3>
                                <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5 truncate" title="{{ $membro->position }}">
                                    {{ $membro->position ?? 'Sem cargo' }}
                                </p>
                            </div>

                            {{-- Divisor --}}
                            <div class="my-3 border-t border-slate-100 dark:border-slate-700"></div>

                            {{-- Dados --}}
                            <div class="space-y-2">
                                <a href="mailto:{{ $membro->email }}"
                                   class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300 hover:text-emerald-600 dark:hover:text-emerald-400 transition">
                                    <div class="w-6 h-6 rounded-md bg-slate-50 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                        <x-lucide-mail class="w-3 h-3 text-slate-400" />
                                    </div>
                                    <span class="truncate lato-regular" title="{{ $membro->email }}">{{ $membro->email }}</span>
                                </a>

                                <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                    <div class="w-6 h-6 rounded-md bg-slate-50 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                        <x-lucide-shield class="w-3 h-3 text-slate-400" />
                                    </div>
                                    <span class="truncate lato-regular">
                                        {{ $membro->accessProfile?->name ?? 'Sem perfil' }}
                                    </span>
                                </div>
                            </div>

                            {{-- Ação (RH/Admin → perfil completo | demais → drawer) --}}
                            @if (auth()->user()?->isRhOuDp())
                                <a href="{{ route('users.details', $membro->id) }}"
                                    class="mt-3 w-full flex items-center justify-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg bg-slate-50 hover:bg-emerald-50 dark:bg-slate-700 dark:hover:bg-emerald-900/30 text-slate-600 hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400 transition cursor-pointer">
                                    <x-lucide-user class="w-3.5 h-3.5" />
                                    Ver perfil
                                </a>
                            @else
                                <button type="button" wire:click="abrirDetalhesMembro({{ $membro->id }})"
                                    class="mt-3 w-full flex items-center justify-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg bg-slate-50 hover:bg-emerald-50 dark:bg-slate-700 dark:hover:bg-emerald-900/30 text-slate-600 hover:text-emerald-700 dark:text-slate-300 dark:hover:text-emerald-400 transition cursor-pointer">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                    Ver detalhes
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif
</div>
