<div class="flex">

    {{-- OVERLAY MOBILE --}}
    @if($open)
        <div
            class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden"
            wire:click="close"
        ></div>
    @endif

    {{-- SIDEBAR --}}
        <aside class="
        fixed lg:relative lg:top-0
        z-50 lg:z-auto
        h-screen
        bg-white dark:bg-slate-800
        border-r border-slate-200 dark:border-slate-700
        transform transition-all duration-300
        shrink-0
        {{ $collapsed ? 'w-20' : 'w-64' }}
        {{ $open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0' }}
    ">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-4 py-5 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-purple-500 flex items-center justify-center text-white font-semibold shrink-0">
                    P
                </div>
                @if(!$collapsed)
                    <span class="font-bold text-slate-800 dark:text-slate-100 text-lg font-display tracking-tight">PeopleHub</span>
                @endif
            </div>

            <button wire:click="close" class="text-slate-500 dark:text-slate-400 lg:hidden cursor-pointer p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition" aria-label="Fechar menu">
                <x-lucide-x class="w-5 h-5" />
            </button>

            <button wire:click="toggleCollapse" class="text-slate-500 dark:text-slate-400 hidden lg:block cursor-pointer p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition" aria-label="Recolher menu">
                <x-lucide-chevron-left class="w-5 h-5 transition {{ $collapsed ? 'rotate-180' : '' }}" />
            </button>
        </div>


        {{-- MENU --}}
        <nav class="px-3 py-6 space-y-6 text-sm overflow-y-auto h-[calc(100vh-8rem)]">

            {{-- PRINCIPAL --}}
            <div>
                @if(!$collapsed)
                    <button wire:click="toggleMenu('principal')" class="menu-title flex items-center justify-between w-full lato-regular">
                        Principal
                        <x-lucide-chevron-down class="chevron {{ $menus['principal'] ? 'rotate-180' : '' }} w-4" />
                    </button>
                @endif

                @if ($menus['principal'])
                    <div class="menu-items">
                        <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-layout-dashboard class="icon" />
                            @if(!$collapsed) Dashboard @endif
                        </a>
                        <a href="#" class="sidebar-link {{ request()->routeIs('feed') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-globe class="icon" />
                            @if(!$collapsed) Feed Social @endif
                        </a>
                        <a href="#" class="sidebar-link {{ request()->routeIs('publicacoes') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-send class="icon" />
                            @if(!$collapsed) Publicações @endif
                        </a>
                    </div>
                @endif
            </div>


            {{-- GESTÃO --}}
            <div>
                @if(!$collapsed)
                    <button wire:click="toggleMenu('gestao')" class="menu-title flex items-center justify-between w-full lato-regular">
                        Gestão
                        <x-lucide-chevron-down class="chevron {{ $menus['gestao'] ? 'rotate-180' : '' }} w-4" />
                    </button>
                @endif

                @if ($menus['gestao'])
                    <div class="menu-items">
                        <a href="#" class="sidebar-link {{ request()->routeIs('dpi') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-bar-chart-3 class="icon" />
                            @if(!$collapsed) DPI @endif
                        </a>
                        <a href="#" class="sidebar-link {{ request()->routeIs('okrs') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-target class="icon" />
                            @if(!$collapsed) OKRs @endif
                        </a>
                        <a href="{{ route('tarefas') }}" class="sidebar-link {{ request()->routeIs('tarefas') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-check-square class="icon" />
                            @if(!$collapsed) Tarefas @endif
                        </a>
                        @if(auth()->user()?->podeGerenciarPesquisas())
                        <a href="{{ route('avaliacoes') }}" class="sidebar-link {{ request()->routeIs('avaliacoes') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-trending-up class="icon" />
                            @if(!$collapsed) Avaliação @endif
                        </a>
                        @endif
                    </div>
                @endif
            </div>


            {{-- PESSOAS --}}
            <div>
                @if(!$collapsed)
                    <button wire:click="toggleMenu('pessoas')" class="menu-title flex items-center justify-between w-full lato-regular">
                        Pessoas
                        <x-lucide-chevron-down class="chevron {{ $menus['pessoas'] ? 'rotate-180' : '' }} w-4" />
                    </button>
                @endif

                @if ($menus['pessoas'])
                    <div class="menu-items">
                        <a href="{{ route('time') }}" class="sidebar-link {{ request()->routeIs('time') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-users class="icon" />
                            @if(!$collapsed) Meu Time @endif
                        </a>
                        <a href="#" class="sidebar-link {{ request()->routeIs('curriculos') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-file-text class="icon" />
                            @if(!$collapsed) Currículos @endif
                        </a>
                        <a href="{{ route('pesquisas') }}" class="sidebar-link {{ request()->routeIs('pesquisas') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-clipboard-list class="icon" />
                            @if(!$collapsed) Pesquisas @endif
                        </a>
                        @if(auth()->user()?->isRhOuDp())
                        <a href="{{ route('users') }}" class="sidebar-link {{ request()->routeIs('users') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-user-plus class="icon" />
                            @if(!$collapsed) Usuários @endif
                        </a>
                        @endif
                    </div>
                @endif
            </div>


            {{-- COLABORAÇÃO --}}
            <div>
                @if(!$collapsed)
                    <button wire:click="toggleMenu('colaboracao')" class="menu-title flex items-center justify-between w-full lato-regular">
                        Colaboração
                        <x-lucide-chevron-down class="chevron {{ $menus['colaboracao'] ? 'rotate-180' : '' }} w-4" />
                    </button>
                @endif

                @if ($menus['colaboracao'])
                    <div class="menu-items">
                        @if(auth()->user()?->podeGerenciarPesquisas())
                        <a href="{{ route('feedback') }}" class="sidebar-link {{ request()->routeIs('feedback') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-message-square class="icon" />
                            @if(!$collapsed) Feedbacks @endif
                        </a>
                        @endif
                        <a href="{{ route('reunioes') }}" class="sidebar-link {{ request()->routeIs('reunioes') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-calendar class="icon" />
                            @if(!$collapsed) Reuniões @endif
                        </a>
                        <a href="{{ route('rooms') }}" class="sidebar-link {{ request()->routeIs('rooms') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-building class="icon" />
                            @if(!$collapsed) Salas @endif
                        </a>
                        <a href="{{ route('ouvidoria') }}" class="sidebar-link {{ request()->routeIs('ouvidoria') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                            <x-lucide-alert-circle class="icon" />
                            @if(!$collapsed) Ouvidoria @endif
                        </a>
                    </div>
                @endif
            </div>

        </nav>


        {{-- USER --}}
        <div class="absolute bottom-0 w-full border-t border-slate-200 dark:border-slate-700 px-4 py-4 flex items-center gap-3 bg-white dark:bg-slate-800">
            <img src="https://i.pravatar.cc/40" class="w-10 h-10 rounded-full shrink-0">
            @if(!$collapsed)
                <div class="min-w-0">
                    @php
                        $nomes = explode(' ', auth()->user()->name);
                        $primeiroSegundo = $nomes[0] . (isset($nomes[1]) ? ' ' . $nomes[1] : '');
                    @endphp
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate">{{ $primeiroSegundo }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 font-normal">{{ auth()->user()->accessProfile->name }}</p>
                </div>
            @endif
        </div>

    </aside>

</div>
