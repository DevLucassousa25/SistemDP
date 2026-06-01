<div class="flex">

    {{-- OVERLAY MOBILE --}}
    @if($open)
        <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm lg:hidden" wire:click="close"></div>
    @endif

    {{-- SIDEBAR --}}
    <aside class="
        fixed lg:relative lg:top-0
        z-50 lg:z-auto
        h-screen
        bg-white dark:bg-slate-800
        border-r border-slate-200 dark:border-slate-700
        transform transition-all duration-300
        shrink-0 flex flex-col
        {{ $collapsed ? 'w-20' : 'w-64' }}
        {{ $open ? 'translate-x-0' : '-translate-x-full lg:translate-x-0' }}
    ">

        {{-- HEADER --}}
        <div class="flex items-center justify-between px-4 py-5 border-b border-slate-200 dark:border-slate-700 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 flex items-center justify-center text-white font-semibold shrink-0">
                    P
                </div>
                @if(!$collapsed)
                    <span class="font-bold text-slate-800 dark:text-slate-100 text-lg font-display tracking-tight">PeopleHub</span>
                @endif
            </div>

            <button wire:click="close" class="text-slate-500 dark:text-slate-400 lg:hidden cursor-pointer p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-5 h-5" />
            </button>
            <button wire:click="toggleCollapse" class="text-slate-500 dark:text-slate-400 hidden lg:block cursor-pointer p-1 rounded hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-chevron-left class="w-5 h-5 transition {{ $collapsed ? 'rotate-180' : '' }}" />
            </button>
        </div>

        {{-- MENU --}}
        <nav class="px-3 py-4 space-y-5 text-sm overflow-y-auto flex-1">

            {{-- PRINCIPAL --}}
            @if(!($editMode))
            <div>
                @if(!$collapsed)
                    <button wire:click="toggleMenu('principal')" class="menu-title flex items-center justify-between w-full lato-regular">
                        Principal
                        <x-lucide-chevron-down class="chevron {{ $menus['principal'] ? 'rotate-180' : '' }} w-4" />
                    </button>
                @endif
                @if($menus['principal'])
                <div class="menu-items">
                    @if(!$this->isHidden('dashboard'))
                    <a href="{{ route('dashboard') }}" class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-layout-dashboard class="icon" />
                        @if(!$collapsed) Dashboard @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('feed'))
                    <a href="{{ route('feed') }}" class="sidebar-link {{ request()->routeIs('feed') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-globe class="icon" />
                        @if(!$collapsed) Feed Social @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('communities'))
                    <a href="{{ route('communities') }}" class="sidebar-link {{ request()->routeIs('communities*') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-users class="icon" />
                        @if(!$collapsed) Comunidades @endif
                    </a>
                    @endif
                    @if(!auth()->user()?->isAdmin() && !$this->isHidden('humor'))
                    <a href="{{ route('humor.pessoal') }}" class="sidebar-link {{ request()->routeIs('humor.pessoal') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-activity class="icon" />
                        @if(!$collapsed) Meu Humor @endif
                    </a>
                    @endif
                    @if(auth()->user()?->isRhOuDp() && !$this->isHidden('publicacoes'))
                    <a href="{{ route('publicacoes') }}" class="sidebar-link {{ request()->routeIs('publicacoes') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-send class="icon" />
                        @if(!$collapsed) Publicações @endif
                    </a>
                    @endif
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
                @if($menus['gestao'])
                <div class="menu-items">
                    @if(!$this->isHidden('dpi'))
                    <a href="{{ route('dpi') }}" class="sidebar-link {{ request()->routeIs('dpi') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-bar-chart-3 class="icon" />
                        @if(!$collapsed) DPI @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('okrs'))
                    <a href="{{ route('okrs') }}" class="sidebar-link {{ request()->routeIs('okrs') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-target class="icon" />
                        @if(!$collapsed) OKRs @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('tarefas'))
                    <a href="{{ route('tarefas') }}" class="sidebar-link {{ request()->routeIs('tarefas') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-check-square class="icon" />
                        @if(!$collapsed) Tarefas @endif
                    </a>
                    @endif
                    @if(!auth()->user()?->isRhOuDp() && !$this->isHidden('solicitacoes'))
                    <a href="{{ route('solicitacoes') }}" class="sidebar-link {{ request()->routeIs('solicitacoes') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-inbox class="icon" />
                        @if(!$collapsed) Solicitações RH @endif
                    </a>
                    @endif
                    @if(auth()->user()?->podeAvaliar() && !$this->isHidden('avaliacoes'))
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
                @if($menus['pessoas'])
                <div class="menu-items">
                    @if(!$this->isHidden('time'))
                    <a href="{{ route('time') }}" class="sidebar-link {{ request()->routeIs('time') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-users class="icon" />
                        @if(!$collapsed) Meu Time @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('organograma'))
                    <a href="{{ route('organograma') }}" class="sidebar-link {{ request()->routeIs('organograma') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-network class="icon" />
                        @if(!$collapsed) Organograma @endif
                    </a>
                    @endif
                    @if(auth()->user()?->isRhOuDp() && !$this->isHidden('portal_rs'))
                    <a href="{{ route('rh.curriculos') }}" class="sidebar-link {{ request()->routeIs('rh.curriculos') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-file-text class="icon" />
                        @if(!$collapsed) Portal R&S @endif
                    </a>
                    @endif
                    @php $isTiDept = str_contains(strtolower(auth()->user()?->department?->name ?? ''), 'ti') || str_contains(strtolower(auth()->user()?->department?->name ?? ''), 'tecnologia'); @endphp
                    @if((auth()->user()?->isRhOuDp() || $isTiDept) && !$this->isHidden('equipamentos'))
                    <a href="{{ route('equipamentos') }}" class="sidebar-link {{ request()->routeIs('equipamentos') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-monitor class="icon" />
                        @if(!$collapsed) Equipamentos @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('treinamentos'))
                    @if(!$collapsed)
                        <button wire:click="toggleMenu('treinamentos')" class="sidebar-link w-full {{ request()->routeIs('treinamentos*') ? 'active' : '' }} justify-between lato-regular">
                            <span class="flex items-center gap-2"><x-lucide-graduation-cap class="icon shrink-0" /> Treinamentos</span>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 transition-transform duration-200 shrink-0 {{ $menus['treinamentos'] ? 'rotate-180' : '' }}" />
                        </button>
                    @else
                        <a href="{{ route('treinamentos') }}" class="sidebar-link justify-center {{ request()->routeIs('treinamentos*') ? 'active' : '' }} lato-regular">
                            <x-lucide-graduation-cap class="icon" />
                        </a>
                    @endif
                    @if($menus['treinamentos'] && !$collapsed)
                    <div class="pl-4 space-y-0.5">
                        <a href="{{ route('treinamentos') }}" wire:navigate class="sidebar-link text-xs {{ request()->routeIs('treinamentos') ? 'active' : '' }} lato-regular">
                            <x-lucide-book-open class="w-3.5 h-3.5 shrink-0" /> Catálogo
                        </a>
                        <a href="{{ route('treinamentos.trilhas') }}" wire:navigate class="sidebar-link text-xs {{ request()->routeIs('treinamentos.trilhas') ? 'active' : '' }} lato-regular">
                            <x-lucide-layers class="w-3.5 h-3.5 shrink-0" /> Trilhas
                        </a>
                        @if(auth()->user()?->isRhOuDp())
                        <a href="{{ route('treinamentos.gestao') }}" wire:navigate class="sidebar-link text-xs {{ request()->routeIs('treinamentos.gestao') ? 'active' : '' }} lato-regular">
                            <x-lucide-settings class="w-3.5 h-3.5 shrink-0" /> Gestão
                        </a>
                        <a href="{{ route('treinamentos.relatorios') }}" wire:navigate class="sidebar-link text-xs {{ request()->routeIs('treinamentos.relatorios') ? 'active' : '' }} lato-regular">
                            <x-lucide-bar-chart-2 class="w-3.5 h-3.5 shrink-0" /> Relatórios
                        </a>
                        @endif
                    </div>
                    @endif
                    @endif
                    @if(!$this->isHidden('pesquisas'))
                    <a href="{{ route('pesquisas') }}" class="sidebar-link {{ request()->routeIs('pesquisas') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-clipboard-list class="icon" />
                        @if(!$collapsed) Pesquisas @endif
                    </a>
                    @endif
                    @if(auth()->user()?->isRhOuDp() && !$this->isHidden('usuarios'))
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
                @if($menus['colaboracao'])
                <div class="menu-items">
                    @if(auth()->user()?->podeGerenciarPesquisas() && !$this->isHidden('feedbacks'))
                    <a href="{{ route('feedback') }}" class="sidebar-link {{ request()->routeIs('feedback') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-message-square class="icon" />
                        @if(!$collapsed) Feedbacks @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('calendario'))
                    <a href="{{ route('calendario') }}" class="sidebar-link {{ request()->routeIs('calendario') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-calendar-days class="icon" />
                        @if(!$collapsed) Calendário @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('reunioes'))
                    <a href="{{ route('reunioes') }}" class="sidebar-link {{ request()->routeIs('reunioes') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-calendar class="icon" />
                        @if(!$collapsed) Reuniões @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('salas'))
                    <a href="{{ route('rooms') }}" class="sidebar-link {{ request()->routeIs('rooms') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-building class="icon" />
                        @if(!$collapsed) Salas @endif
                    </a>
                    @endif
                    @if(!$this->isHidden('ouvidoria'))
                    <a href="{{ route('ouvidoria') }}" class="sidebar-link {{ request()->routeIs('ouvidoria') ? 'active' : '' }} {{ $collapsed ? 'justify-center' : '' }} lato-regular">
                        <x-lucide-alert-circle class="icon" />
                        @if(!$collapsed) Ouvidoria @endif
                    </a>
                    @endif
                </div>
                @endif
            </div>
            @endif {{-- /!editMode --}}

            {{-- ══════════════════════════════════════════════════════
                 PAINEL DE EDIÇÃO DO SIDEBAR
            ══════════════════════════════════════════════════════ --}}

            {{-- ══════════════════════════════════════════════════════
                 PAINEL DE EDIÇÃO DO SIDEBAR
            ══════════════════════════════════════════════════════ --}}
            @if($editMode)
            <div class="space-y-1 pb-2">
                <div class="flex items-center justify-between mb-4">
                    <div>
                        <p class="text-sm font-bold text-slate-800 dark:text-slate-100 lato-bold">Personalizar menu</p>
                        <p class="text-[10px] text-slate-400 lato-regular mt-0.5">Ative ou desative os itens que deseja ver</p>
                    </div>
                    <button wire:click="closeEditor" class="cursor-pointer w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-600 transition">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                </div>

                @foreach($this->allNavItems() as $sectionKey => $section)
                <div class="mb-3">
                    <p class="text-[9px] font-bold uppercase tracking-widest text-slate-400 lato-bold px-1 mb-1.5 flex items-center gap-1.5">
                        @php
                            $dotColor = match($section['color']) {
                                'blue'    => 'bg-blue-400',
                                'violet'  => 'bg-violet-400',
                                'emerald' => 'bg-emerald-400',
                                default   => 'bg-amber-400',
                            };
                        @endphp
                        <span class="w-1 h-3 rounded-full {{ $dotColor }}"></span>
                        {{ $section['section'] }}
                    </p>
                    <div class="space-y-0.5">
                        @foreach($section['items'] as $key => $item)
                        @php $isVisible = !$this->isHidden($key); @endphp
                        <button wire:click="toggleNavItem('{{ $key }}')"
                            class="cursor-pointer w-full flex items-center gap-2.5 px-2.5 py-2 rounded-xl transition group
                                   {{ $isVisible ? 'bg-slate-50 dark:bg-slate-700/40 hover:bg-blue-50 dark:hover:bg-blue-900/10' : 'opacity-50 hover:opacity-75 hover:bg-slate-50 dark:hover:bg-slate-700/30' }}">
                            <div class="w-6 h-6 rounded-lg flex items-center justify-center shrink-0 transition
                                        {{ $isVisible ? 'bg-blue-100 dark:bg-blue-900/30' : 'bg-slate-100 dark:bg-slate-700' }}">
                                <x-dynamic-component :component="'lucide-' . $item['icon']"
                                    class="w-3 h-3 {{ $isVisible ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400' }}" />
                            </div>
                            <span class="flex-1 text-left text-xs lato-regular truncate
                                         {{ $isVisible ? 'text-slate-700 dark:text-slate-200 font-medium' : 'text-slate-400 dark:text-slate-500 line-through' }}">
                                {{ $item['label'] }}
                            </span>
                            <div class="relative shrink-0 rounded-full transition-colors duration-200"
                                 style="height:18px;width:32px;background:{{ $isVisible ? '#3b82f6' : 'rgb(226 232 240)' }}">
                                <div class="absolute top-0.5 w-3.5 h-3.5 rounded-full bg-white shadow-sm transition-all duration-200"
                                     style="{{ $isVisible ? 'left:14px' : 'left:2px' }}"></div>
                            </div>
                        </button>
                        @endforeach
                    </div>
                </div>
                @endforeach

                @if(!empty($hiddenItems))
                <div class="pt-2 border-t border-slate-100 dark:border-slate-700 mt-2">
                    <button wire:click="resetPreferences"
                        class="cursor-pointer w-full flex items-center justify-center gap-1.5 text-xs text-slate-400 hover:text-rose-500 py-2 rounded-xl hover:bg-rose-50 dark:hover:bg-rose-900/10 transition lato-regular">
                        <x-lucide-rotate-ccw class="w-3 h-3" /> Restaurar padrão
                    </button>
                </div>
                @endif
            </div>
            @endif
            {{-- /editMode panel --}}

        </nav>

        {{-- RODAPÉ: Botão de edição + Avatar --}}
        <div class="border-t border-slate-200 dark:border-slate-700 shrink-0">

            @if(!$collapsed)
            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700/50">
                <button wire:click="{{ $editMode ? 'closeEditor' : 'openEditor' }}"
                    class="cursor-pointer w-full flex items-center gap-2 px-3 py-2 rounded-xl text-xs transition lato-regular
                           {{ $editMode ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 ring-1 ring-blue-200 dark:ring-blue-700/40' : 'text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                    @if($editMode)
                        <x-lucide-check class="w-3.5 h-3.5 shrink-0" />
                        <span class="flex-1 text-left font-semibold">Concluir edição</span>
                    @else
                        <x-lucide-sliders-horizontal class="w-3.5 h-3.5 shrink-0" />
                        <span class="flex-1 text-left">Personalizar menu</span>
                        @if(!empty($hiddenItems))
                            <span class="w-4 h-4 rounded-full bg-blue-500 text-white text-[9px] flex items-center justify-center font-bold shrink-0">{{ count($hiddenItems) }}</span>
                        @endif
                    @endif
                </button>
            </div>
            @else
            <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700/50 flex justify-center">
                <button wire:click="openEditor" title="Personalizar menu"
                    class="cursor-pointer w-9 h-9 flex items-center justify-center rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-600 transition relative">
                    <x-lucide-sliders-horizontal class="w-4 h-4" />
                    @if(!empty($hiddenItems))
                        <span class="absolute top-1 right-1 w-2 h-2 rounded-full bg-blue-500"></span>
                    @endif
                </button>
            </div>
            @endif

            @php
                $sidebarUser    = auth()->user();
                $sidebarNomes   = explode(' ', $sidebarUser->name);
                $sidebarNomeCurto = $sidebarNomes[0] . (isset($sidebarNomes[1]) ? ' ' . $sidebarNomes[1] : '');
            @endphp
            <a href="{{ route('profile') }}" wire:navigate
               class="flex items-center gap-3 px-4 py-3.5 bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                @if($sidebarUser->avatar)
                    <img src="{{ $sidebarUser->avatarUrl() }}" class="w-9 h-9 rounded-full object-cover shrink-0" />
                @else
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br {{ $sidebarUser->avatarColor() }} flex items-center justify-center shrink-0">
                        <span class="text-sm lato-bold text-white select-none">{{ $sidebarUser->initials() }}</span>
                    </div>
                @endif
                @if(!$collapsed)
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800 dark:text-slate-100 truncate lato-bold">{{ $sidebarNomeCurto }}</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ $sidebarUser->accessProfile?->name }}</p>
                </div>
                <x-lucide-chevron-right class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0" />
                @endif
            </a>
        </div>

    </aside>

</div>
