@php
    $categoriaLabels = [
        'notebook' => 'Notebook',
        'desktop' => 'Desktop',
        'monitor' => 'Monitor',
        'teclado' => 'Teclado',
        'mouse' => 'Mouse',
        'headset' => 'Headset',
        'cracha' => 'Crachá',
        'epi' => 'EPI',
        'cadeira' => 'Cadeira',
        'celular' => 'Celular',
        'outros' => 'Outros',
    ];
    $categoriaCores = [
        'notebook' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'desktop' => 'bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300',
        'monitor' => 'bg-cyan-100 text-cyan-700 dark:bg-cyan-900/30 dark:text-cyan-300',
        'teclado' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        'mouse' => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
        'headset' => 'bg-purple-100 text-purple-700 dark:bg-purple-900/30 dark:text-purple-300',
        'cracha' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
        'epi' => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
        'cadeira' => 'bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300',
        'celular' => 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300',
        'outros' => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
    ];
    $statusCls = [
        'disponivel' => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'em_uso' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
        'manutencao' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'descartado' => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
    ];
    $statusLabel = [
        'disponivel' => 'Disponível',
        'em_uso' => 'Em Uso',
        'manutencao' => 'Manutenção',
        'descartado' => 'Descartado',
    ];
    $condicaoLabel = [
        'novo' => 'Novo',
        'bom' => 'Bom',
        'regular' => 'Regular',
        'danificado' => 'Danificado',
    ];
    $navItems = [
        ['id' => 'dashboard',  'label' => 'Dashboard',   'icon' => 'layout-dashboard', 'dot' => 'bg-blue-500'],
        ['id' => 'ativos',     'label' => 'Ativos',      'icon' => 'package',          'dot' => 'bg-indigo-500'],
        ['id' => 'entregas',   'label' => 'Entregas',    'icon' => 'truck',            'dot' => 'bg-teal-500'],
        ['id' => 'manutencao', 'label' => 'Manutenção',  'icon' => 'wrench',           'dot' => 'bg-amber-500'],
        ['id' => 'inventario', 'label' => 'Inventário',  'icon' => 'clipboard-list',   'dot' => 'bg-indigo-600'],
        ['id' => 'relatorios', 'label' => 'Relatórios',  'icon' => 'bar-chart-2',      'dot' => 'bg-rose-500'],
        ['id' => 'termo',      'label' => 'Modelo do Termo','icon' => 'file-text',       'dot' => 'bg-emerald-500'],
    ];
    $manutStatusCls = [
        'aberta'       => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
        'em_andamento' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
        'concluida'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'cancelada'    => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
    ];
    $manutStatusLabel = [
        'aberta'       => 'Aberta',
        'em_andamento' => 'Em andamento',
        'concluida'    => 'Concluída',
        'cancelada'    => 'Cancelada',
    ];
    $invItemCls = [
        'pendente'       => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
        'encontrado'     => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
        'nao_encontrado' => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
        'divergencia'    => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
    ];
@endphp

<div class="h-full flex flex-col">{{-- wrapper raiz único exigido pelo Livewire --}}
    <div class="flex overflow-hidden bg-slate-50 dark:bg-slate-900 flex-1" x-data="{ nav: false }" style="min-height:0">

        {{-- Overlay mobile --}}
        <div x-show="nav" @click="nav=false" style="cursor:pointer;display:none"
            class="fixed inset-0 bg-black/40 z-30 lg:hidden" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"></div>

        {{-- ── SIDEBAR ───────────────────────────────────────────────── --}}
        <aside :class="nav ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
            class="fixed lg:relative inset-y-0 left-0 z-40 w-60 shrink-0
                  bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700
                  flex flex-col transition-transform duration-200 ease-in-out">

            {{-- Logo --}}
            <div class="px-5 py-5 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div
                        class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                        <x-lucide-monitor class="w-4 h-4 text-white" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm lato-black text-slate-800 dark:text-white leading-tight">Equipamentos</p>
                        <p class="text-[11px] text-slate-400 lato-regular">Controle de Ativos</p>
                    </div>
                    <button @click="nav=false" type="button"
                        class="cursor-pointer lg:hidden p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- Nav --}}
            <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
                @foreach ($navItems as $item)
                    <button wire:click="setAba('{{ $item['id'] }}')" @click="nav=false" type="button"
                        class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition lato-regular cursor-pointer
                           {{ $aba === $item['id']
                               ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 lato-bold'
                               : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                        <x-dynamic-component :component="'lucide-' . $item['icon']"
                            class="w-4 h-4 shrink-0 {{ $aba === $item['id'] ? 'text-blue-500' : 'text-slate-400' }}" />
                        {{ $item['label'] }}
                        @if ($aba === $item['id'])
                            <div class="ml-auto w-1.5 h-1.5 rounded-full {{ $item['dot'] }}"></div>
                        @endif
                    </button>
                @endforeach

                <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-700">
                    <a href="{{ route('dashboard') }}"
                        class="cursor-pointer flex items-center gap-3 px-3 py-2 rounded-xl text-xs text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition lato-regular">
                        <x-lucide-arrow-left class="w-3.5 h-3.5" /> Voltar
                    </a>
                </div>
            </nav>

            {{-- Stats rápidos --}}
            @php $ds = $this->dashStats; @endphp
            <div class="p-3 border-t border-slate-200 dark:border-slate-700 space-y-1">
                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900">
                    <span class="text-xs text-slate-500 lato-regular">Total ativos</span>
                    <span class="text-xs lato-black text-slate-800 dark:text-white">{{ $ds['total'] }}</span>
                </div>
                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900">
                    <span class="text-xs text-slate-500 lato-regular">Disponíveis</span>
                    <span class="text-xs lato-black text-green-600">{{ $ds['disponivel'] }}</span>
                </div>
                <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900">
                    <span class="text-xs text-slate-500 lato-regular">Em uso</span>
                    <span class="text-xs lato-black text-blue-600">{{ $ds['emUso'] }}</span>
                </div>
            </div>
        </aside>

        {{-- ── ÁREA DE CONTEÚDO ─────────────────────────────────────── --}}
        <div class="flex-1 flex flex-col min-w-0 overflow-x-hidden">

            {{-- Header mobile --}}
            <header
                class="lg:hidden sticky top-0 z-20 flex items-center gap-3 px-4 py-3
                        bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shrink-0">
                <button @click="nav=true" type="button"
                    class="cursor-pointer p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-menu class="w-5 h-5" />
                </button>
                <span
                    class="text-sm lato-bold text-slate-800 dark:text-white flex-1 truncate capitalize">{{ $aba }}</span>
            </header>

            <div class="flex-1 overflow-y-auto min-h-0">

                {{-- ══════════════════════════════════════════════════════════
             ABA: DASHBOARD
        ══════════════════════════════════════════════════════════ --}}
                @if ($aba === 'dashboard')
                    <div class="p-4 md:p-6 space-y-5">
                        <div>
                            <h1 class="text-xl lato-black text-slate-800 dark:text-white">Controle de Equipamentos</h1>
                            <p class="text-sm text-slate-400 lato-regular mt-0.5">Registro de ativos, entregas e
                                checklists</p>
                        </div>

                        {{-- KPIs --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-5 gap-4">
                            @php
                                $kpis = [
                                    [
                                        'label' => 'Total de ativos',
                                        'value' => $ds['total'],
                                        'icon' => 'package',
                                        'color' => 'from-blue-500 to-indigo-600',
                                    ],
                                    [
                                        'label' => 'Disponíveis',
                                        'value' => $ds['disponivel'],
                                        'icon' => 'check-circle',
                                        'color' => 'from-green-500 to-emerald-600',
                                    ],
                                    [
                                        'label' => 'Em uso',
                                        'value' => $ds['emUso'],
                                        'icon' => 'users',
                                        'color' => 'from-blue-500 to-cyan-600',
                                    ],
                                    [
                                        'label' => 'Manutenção',
                                        'value' => $ds['manutencao'],
                                        'icon' => 'wrench',
                                        'color' => 'from-amber-500 to-orange-500',
                                    ],
                                    [
                                        'label' => 'Entregas hoje',
                                        'value' => $ds['entregasHoje'],
                                        'icon' => 'truck',
                                        'color' => 'from-teal-500 to-cyan-600',
                                    ],
                                ];
                            @endphp
                            @foreach ($kpis as $kpi)
                                <div
                                    class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $kpi['color'] }} flex items-center justify-center mb-3">
                                        <x-dynamic-component :component="'lucide-' . $kpi['icon']" class="w-4 h-4 text-white" />
                                    </div>
                                    <p class="text-2xl lato-bold text-slate-800 dark:text-slate-100">
                                        {{ $kpi['value'] }}</p>
                                    <p class="text-xs text-slate-500 lato-regular mt-0.5">{{ $kpi['label'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                            {{-- Por categoria --}}
                            <div
                                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                                <h3
                                    class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                                    <x-lucide-bar-chart-2 class="w-4 h-4 text-indigo-500" /> Ativos por categoria
                                </h3>
                                @if (empty($ds['porCategoria']))
                                    <p class="text-sm text-slate-400 text-center py-6">Nenhum ativo cadastrado ainda.
                                    </p>
                                @else
                                    @php $maxCat = max($ds['porCategoria'] ?: [1]); @endphp
                                    <div class="space-y-3">
                                        @foreach ($ds['porCategoria'] as $cat => $total)
                                            @php $pct = $maxCat > 0 ? round($total / $maxCat * 100) : 0; @endphp
                                            <div>
                                                <div class="flex items-center justify-between text-xs mb-1">
                                                    <span
                                                        class="lato-regular text-slate-600 dark:text-slate-300">{{ $categoriaLabels[$cat] ?? $cat }}</span>
                                                    <span
                                                        class="lato-bold text-slate-700 dark:text-slate-200">{{ $total }}</span>
                                                </div>
                                                <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full">
                                                    <div class="h-full bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full"
                                                        style="width:{{ $pct }}%"></div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @endif
                            </div>

                            {{-- Ações rápidas --}}
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 flex flex-col gap-4">
                                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                                    <x-lucide-zap class="w-4 h-4 text-amber-400" /> Ações rápidas
                                </h3>
                                <div class="space-y-2">
                                    <button wire:click="novoEquipamento"
                                        class="cursor-pointer group w-full flex items-center gap-3 px-4 py-3 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:opacity-90 transition text-left">
                                        <div class="w-8 h-8 rounded-lg bg-white/20 flex items-center justify-center shrink-0">
                                            <x-lucide-circle-plus class="w-4 h-4" />
                                        </div>
                                        <div>
                                            <p class="text-sm lato-bold leading-none">Cadastrar novo ativo</p>
                                            <p class="text-[11px] text-white/70 lato-regular mt-0.5">Registrar equipamento no sistema</p>
                                        </div>
                                        <x-lucide-chevron-right class="w-4 h-4 ml-auto opacity-60 group-hover:translate-x-0.5 transition-transform" />
                                    </button>

                                    <button wire:click="setAba('ativos')"
                                        class="cursor-pointer group w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
                                        <div class="w-8 h-8 rounded-lg bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-package class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                                        </div>
                                        <div>
                                            <p class="text-sm lato-bold leading-none">Ver todos os ativos</p>
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">{{ $ds['total'] }} equipamentos cadastrados</p>
                                        </div>
                                        <x-lucide-chevron-right class="w-4 h-4 ml-auto text-slate-300 group-hover:translate-x-0.5 transition-transform" />
                                    </button>

                                    <button wire:click="setAba('entregas')"
                                        class="cursor-pointer group w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
                                        <div class="w-8 h-8 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-truck class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                                        </div>
                                        <div>
                                            <p class="text-sm lato-bold leading-none">Entregas e devoluções</p>
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">{{ $ds['entregasHoje'] }} movimentaç{{ $ds['entregasHoje'] === 1 ? 'ão' : 'ões' }} hoje</p>
                                        </div>
                                        <x-lucide-chevron-right class="w-4 h-4 ml-auto text-slate-300 group-hover:translate-x-0.5 transition-transform" />
                                    </button>

                                    <button wire:click="setAba('manutencao')"
                                        class="cursor-pointer group w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/50 transition text-left">
                                        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-wrench class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                                        </div>
                                        <div>
                                            <p class="text-sm lato-bold leading-none">Manutenções</p>
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">{{ $ds['manutAbertas'] }} abertas / em andamento</p>
                                        </div>
                                        <x-lucide-chevron-right class="w-4 h-4 ml-auto text-slate-300 group-hover:translate-x-0.5 transition-transform" />
                                    </button>
                                </div>
                            </div>
                        </div>

                        {{-- Alertas de garantia --}}
                        @if ($ds['garantiaVencendo'] > 0 || $ds['garantiaVencida'] > 0)
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                @if ($ds['garantiaVencida'] > 0)
                                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-red-50 dark:bg-red-900/10 border border-red-200/60 dark:border-red-800/30">
                                        <div class="w-9 h-9 rounded-xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-shield-x class="w-4 h-4 text-red-500" />
                                        </div>
                                        <div>
                                            <p class="text-sm lato-bold text-red-700 dark:text-red-400">{{ $ds['garantiaVencida'] }} garantia(s) vencida(s)</p>
                                            <button wire:click="setAba('ativos')" class="cursor-pointer text-xs text-red-500 lato-regular hover:underline">Ver ativos →</button>
                                        </div>
                                    </div>
                                @endif
                                @if ($ds['garantiaVencendo'] > 0)
                                    <div class="flex items-center gap-3 p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/10 border border-amber-200/60 dark:border-amber-800/30">
                                        <div class="w-9 h-9 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-shield-alert class="w-4 h-4 text-amber-500" />
                                        </div>
                                        <div>
                                            <p class="text-sm lato-bold text-amber-700 dark:text-amber-400">{{ $ds['garantiaVencendo'] }} garantia(s) vencendo em 30 dias</p>
                                            <button wire:click="setAba('ativos')" class="cursor-pointer text-xs text-amber-500 lato-regular hover:underline">Ver ativos →</button>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endif

                        {{-- Valor do patrimônio --}}
                        @if ($ds['valorTotal'] > 0)
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-600 rounded-2xl p-5 text-white">
                                <div class="flex items-center justify-between">
                                    <div>
                                        <p class="text-[11px] lato-bold text-white/70 uppercase tracking-widest mb-1">Valor total do patrimônio</p>
                                        <p class="text-2xl lato-black">R$ {{ number_format($ds['valorTotal'], 2, ',', '.') }}</p>
                                        <p class="text-xs text-white/60 lato-regular mt-0.5">Valor de aquisição de {{ $ds['total'] }} ativo(s)</p>
                                    </div>
                                    <div class="w-12 h-12 rounded-2xl bg-white/15 flex items-center justify-center shrink-0">
                                        <x-lucide-trending-up class="w-6 h-6 text-white" />
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════════════════
             ABA: ATIVOS
        ══════════════════════════════════════════════════════════ --}}
                @if ($aba === 'ativos')
                    <div class="p-4 md:p-6 space-y-4">
                        {{-- Header --}}
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h1 class="text-xl lato-black text-slate-800 dark:text-white">Ativos</h1>
                                <p class="text-sm text-slate-400 lato-regular">Catálogo de equipamentos da empresa</p>
                            </div>
                            <button wire:click="novoEquipamento"
                                class="cursor-pointer flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:opacity-90 transition shrink-0">
                                <x-lucide-circle-plus class="w-4 h-4" /> Novo ativo
                            </button>
                        </div>

                        {{-- Filtros --}}
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3"
                            x-data="{
                                openCategoria: false,
                                openStatus: false,
                                catMap: @js($categoriaLabels),
                                statusMap: @js($statusLabel),
                            }">

                            {{-- Busca --}}
                            <div class="relative w-full">
                                <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                                <input type="text" wire:model.live.debounce.300ms="searchAtivo"
                                    placeholder="Buscar por nome, série, patrimônio..."
                                    class="w-full pl-10 pr-9 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400/40 lato-regular transition" />
                                @if ($searchAtivo)
                                    <button wire:click="$set('searchAtivo', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <x-lucide-x class="w-3.5 h-3.5" />
                                    </button>
                                @endif
                            </div>

                            {{-- Chips de filtro --}}
                            <div class="flex flex-wrap gap-2">

                                {{-- Categoria --}}
                                <div class="relative" @click.away="openCategoria = false">
                                    <button type="button" @click="openCategoria = !openCategoria"
                                            :class="$wire.filtroCategoria ? 'border-blue-400 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400'"
                                            class="flex items-center gap-1.5 px-3 py-2 text-xs lato-bold border rounded-xl bg-white dark:bg-slate-700 transition cursor-pointer whitespace-nowrap">
                                        <x-lucide-tag class="w-3.5 h-3.5 flex-shrink-0" />
                                        <span x-text="$wire.filtroCategoria ? catMap[$wire.filtroCategoria] : 'Categoria'"></span>
                                        <x-lucide-chevron-down class="w-3 h-3 flex-shrink-0 transition-transform" x-bind:class="openCategoria ? 'rotate-180' : ''" />
                                    </button>
                                    <div x-show="openCategoria" x-cloak x-transition
                                         class="absolute mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                                        <button type="button" @click="openCategoria=false; $wire.set('filtroCategoria', '')"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 flex items-center gap-2 lato-regular">
                                            <x-lucide-tag class="w-3.5 h-3.5" /> Todas as categorias
                                        </button>
                                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                        @php
                                            $catMeta = [
                                                'notebook' => ['dot' => 'bg-blue-500',   'icon' => 'laptop',        'label' => 'Notebook'],
                                                'desktop'  => ['dot' => 'bg-indigo-500', 'icon' => 'monitor',       'label' => 'Desktop'],
                                                'monitor'  => ['dot' => 'bg-cyan-500',   'icon' => 'monitor',       'label' => 'Monitor'],
                                                'teclado'  => ['dot' => 'bg-slate-500',  'icon' => 'keyboard',      'label' => 'Teclado'],
                                                'mouse'    => ['dot' => 'bg-slate-400',  'icon' => 'mouse-pointer', 'label' => 'Mouse'],
                                                'headset'  => ['dot' => 'bg-indigo-600', 'icon' => 'headphones',    'label' => 'Headset'],
                                                'cracha'   => ['dot' => 'bg-amber-500',  'icon' => 'badge',         'label' => 'Crachá'],
                                                'epi'      => ['dot' => 'bg-orange-500', 'icon' => 'shield',        'label' => 'EPI'],
                                                'cadeira'  => ['dot' => 'bg-teal-500',   'icon' => 'armchair',      'label' => 'Cadeira'],
                                                'celular'  => ['dot' => 'bg-rose-500',   'icon' => 'smartphone',    'label' => 'Celular'],
                                                'outros'   => ['dot' => 'bg-slate-300',  'icon' => 'package',       'label' => 'Outros'],
                                            ];
                                        @endphp
                                        @foreach ($catMeta as $catVal => $catInfo)
                                        <button type="button" @click="openCategoria=false; $wire.set('filtroCategoria', '{{ $catVal }}')"
                                                :class="$wire.filtroCategoria === '{{ $catVal }}' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular">
                                            <span class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ $catInfo['dot'] }} shrink-0"></span>
                                                {{ $catInfo['label'] }}
                                            </span>
                                            <x-lucide-check class="w-3.5 h-3.5 text-blue-500" x-show="$wire.filtroCategoria === '{{ $catVal }}'" />
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Status --}}
                                <div class="relative" @click.away="openStatus = false">
                                    <button type="button" @click="openStatus = !openStatus"
                                            :class="$wire.filtroStatus ? 'border-indigo-400 bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400'"
                                            class="flex items-center gap-1.5 px-3 py-2 text-xs lato-bold border rounded-xl bg-white dark:bg-slate-700 transition cursor-pointer whitespace-nowrap">
                                        <x-lucide-activity class="w-3.5 h-3.5 flex-shrink-0" />
                                        <span x-text="$wire.filtroStatus ? statusMap[$wire.filtroStatus] : 'Status'"></span>
                                        <x-lucide-chevron-down class="w-3 h-3 flex-shrink-0 transition-transform" x-bind:class="openStatus ? 'rotate-180' : ''" />
                                    </button>
                                    <div x-show="openStatus" x-cloak x-transition
                                         class="absolute mt-1 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                                        <button type="button" @click="openStatus=false; $wire.set('filtroStatus', '')"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 flex items-center gap-2 lato-regular">
                                            <x-lucide-activity class="w-3.5 h-3.5" /> Todos os status
                                        </button>
                                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                        @php
                                            $ativoStatusDots = [
                                                'disponivel' => ['dot' => 'bg-emerald-500', 'label' => 'Disponível'],
                                                'em_uso'     => ['dot' => 'bg-blue-500',    'label' => 'Em Uso'],
                                                'manutencao' => ['dot' => 'bg-amber-400',   'label' => 'Manutenção'],
                                                'descartado' => ['dot' => 'bg-slate-400',   'label' => 'Descartado'],
                                            ];
                                        @endphp
                                        @foreach ($ativoStatusDots as $stVal => $stInfo)
                                        <button type="button" @click="openStatus=false; $wire.set('filtroStatus', '{{ $stVal }}')"
                                                :class="$wire.filtroStatus === '{{ $stVal }}' ? 'bg-indigo-50 text-indigo-700 dark:bg-indigo-900/20 dark:text-indigo-300 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular">
                                            <span class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ $stInfo['dot'] }} shrink-0"></span>
                                                {{ $stInfo['label'] }}
                                            </span>
                                            <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" x-show="$wire.filtroStatus === '{{ $stVal }}'" />
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>

                        {{-- Tabela --}}
                        @php $eqs = $this->equipamentos; @endphp
                        @if ($eqs->isEmpty())
                            <div
                                class="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                                <x-lucide-package class="w-12 h-12 text-slate-300 mb-3" />
                                <p class="text-sm lato-bold text-slate-500">Nenhum ativo encontrado</p>
                                <p class="text-xs text-slate-400 mt-1 lato-regular">Clique em "Novo ativo" para
                                    cadastrar</p>
                            </div>
                        @else
                            <div
                                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-slate-100 dark:border-slate-700">
                                                <th
                                                    class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                                    Ativo</th>
                                                <th
                                                    class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                                    Categoria</th>
                                                <th
                                                    class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide hidden md:table-cell">
                                                    Nº Série / Patrimônio</th>
                                                <th
                                                    class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                                    Status</th>
                                                <th
                                                    class="text-right px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                                    Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                            @foreach ($eqs as $eq)
                                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                                                    <td class="px-4 py-3">
                                                        <p class="lato-bold text-slate-700 dark:text-slate-200">
                                                            {{ $eq->nome }}</p>
                                                        @if ($eq->marca || $eq->modelo)
                                                            <p class="text-xs text-slate-400 lato-regular">
                                                                {{ trim($eq->marca . ' ' . $eq->modelo) }}</p>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs lato-bold {{ $categoriaCores[$eq->categoria] ?? 'bg-slate-100 text-slate-500' }}">
                                                            {{ $categoriaLabels[$eq->categoria] ?? $eq->categoria }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 hidden md:table-cell">
                                                        <p class="text-xs text-slate-500 lato-regular">
                                                            {{ $eq->numero_serie ?? '—' }}
                                                            @if ($eq->codigo_patrimonio)
                                                                <span class="block text-slate-400">Pat.
                                                                    {{ $eq->codigo_patrimonio }}</span>
                                                            @endif
                                                        </p>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span
                                                            class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs lato-bold {{ $statusCls[$eq->status] ?? '' }}">
                                                            {{ $statusLabel[$eq->status] ?? $eq->status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <div class="flex items-center justify-end gap-1">
                                                            @if ($eq->status === 'disponivel')
                                                                <button wire:click="abrirEntrega({{ $eq->id }})"
                                                                    class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs lato-bold bg-teal-50 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300 hover:bg-teal-100 transition mr-1">
                                                                    <x-lucide-truck class="w-3 h-3" /> Entregar
                                                                </button>
                                                            @endif
                                                            <button wire:click="openEquipDrawer({{ $eq->id }})"
                                                                class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-indigo-500 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition">
                                                                <x-lucide-eye class="w-3.5 h-3.5" />
                                                            </button>
                                                            <button
                                                                wire:click="editarEquipamento({{ $eq->id }})"
                                                                class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                                                <x-lucide-pencil class="w-3.5 h-3.5" />
                                                            </button>
                                                            <button
                                                                wire:click="abrirConfirm('Excluir ativo?', 'Deseja excluir este ativo permanentemente? Só é possível se não estiver atribuído.', 'excluirEquipamento', {{ $eq->id }})"
                                                                class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════════════════
             ABA: ENTREGAS
        ══════════════════════════════════════════════════════════ --}}
                @if ($aba === 'entregas')
                    <div class="p-4 md:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h1 class="text-xl lato-black text-slate-800 dark:text-white">Entregas e Devoluções
                                </h1>
                                <p class="text-sm text-slate-400 lato-regular">Histórico de movimentação de ativos</p>
                            </div>
                        </div>

                        {{-- Filtros --}}
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3"
                            x-data="{ openTipo: false }">

                            {{-- Busca --}}
                            <div class="relative w-full">
                                <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                                <input type="text" wire:model.live.debounce.300ms="searchEntrega"
                                    placeholder="Buscar por funcionário ou equipamento..."
                                    class="w-full pl-10 pr-9 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400/40 lato-regular transition" />
                                @if ($searchEntrega)
                                    <button wire:click="$set('searchEntrega', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <x-lucide-x class="w-3.5 h-3.5" />
                                    </button>
                                @endif
                            </div>

                            {{-- Chips de filtro --}}
                            <div class="flex flex-wrap gap-2">

                                {{-- Tipo --}}
                                <div class="relative" @click.away="openTipo = false">
                                    <button type="button" @click="openTipo = !openTipo"
                                            :class="$wire.filtroTipoEntrega ? 'border-teal-400 bg-teal-50 text-teal-700 dark:bg-teal-900/20 dark:text-teal-300' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400'"
                                            class="flex items-center gap-1.5 px-3 py-2 text-xs lato-bold border rounded-xl bg-white dark:bg-slate-700 transition cursor-pointer whitespace-nowrap">
                                        <x-lucide-truck class="w-3.5 h-3.5 flex-shrink-0" />
                                        <span x-text="$wire.filtroTipoEntrega === 'entrega' ? 'Entrega' : ($wire.filtroTipoEntrega === 'devolucao' ? 'Devolução' : 'Tipo')"></span>
                                        <x-lucide-chevron-down class="w-3 h-3 flex-shrink-0 transition-transform" x-bind:class="openTipo ? 'rotate-180' : ''" />
                                    </button>
                                    <div x-show="openTipo" x-cloak x-transition
                                         class="absolute mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                                        <button type="button" @click="openTipo=false; $wire.set('filtroTipoEntrega', '')"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 flex items-center gap-2 lato-regular">
                                            <x-lucide-truck class="w-3.5 h-3.5" /> Todos os tipos
                                        </button>
                                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                        <button type="button" @click="openTipo=false; $wire.set('filtroTipoEntrega', 'entrega')"
                                                :class="$wire.filtroTipoEntrega === 'entrega' ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/20 dark:text-teal-300 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular">
                                            <span class="flex items-center gap-1.5"><x-lucide-arrow-right class="w-3.5 h-3.5 text-teal-500 flex-shrink-0" /> Entrega</span>
                                            <x-lucide-check class="w-3.5 h-3.5 text-teal-500" x-show="$wire.filtroTipoEntrega === 'entrega'" />
                                        </button>
                                        <button type="button" @click="openTipo=false; $wire.set('filtroTipoEntrega', 'devolucao')"
                                                :class="$wire.filtroTipoEntrega === 'devolucao' ? 'bg-teal-50 text-teal-700 dark:bg-teal-900/20 dark:text-teal-300 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular">
                                            <span class="flex items-center gap-1.5"><x-lucide-rotate-ccw class="w-3.5 h-3.5 text-orange-500 flex-shrink-0" /> Devolução</span>
                                            <x-lucide-check class="w-3.5 h-3.5 text-teal-500" x-show="$wire.filtroTipoEntrega === 'devolucao'" />
                                        </button>
                                    </div>
                                </div>

                            </div>
                        </div>

                        @php $atrs = $this->atribuicoes; @endphp
                        @if ($atrs->isEmpty())
                            <div
                                class="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                                <x-lucide-truck class="w-12 h-12 text-slate-300 mb-3" />
                                <p class="text-sm lato-bold text-slate-500">Nenhuma movimentação registrada</p>
                                <p class="text-xs text-slate-400 mt-1 lato-regular">As entregas e devoluções aparecerão
                                    aqui</p>
                            </div>
                        @else
                            <div
                                class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-slate-100 dark:border-slate-700">
                                                <th
                                                    class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide">
                                                    Equipamento</th>
                                                <th
                                                    class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide">
                                                    Funcionário</th>
                                                <th
                                                    class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide hidden md:table-cell">
                                                    Tipo</th>
                                                <th
                                                    class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide hidden lg:table-cell">
                                                    Data entrega</th>
                                                <th
                                                    class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide hidden lg:table-cell">
                                                    Devolução</th>
                                                <th
                                                    class="text-right px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide">
                                                    Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                            @foreach ($atrs as $atr)
                                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                                                    <td class="px-4 py-3">
                                                        <p class="lato-bold text-slate-700 dark:text-slate-200">
                                                            {{ $atr->equipamento->nome }}</p>
                                                        <span
                                                            class="text-xs {{ $categoriaCores[$atr->equipamento->categoria] ?? 'text-slate-400' }} px-1.5 py-0.5 rounded-md">
                                                            {{ $categoriaLabels[$atr->equipamento->categoria] ?? $atr->equipamento->categoria }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <p class="lato-regular text-slate-700 dark:text-slate-200">
                                                            {{ $atr->funcionario->name }}</p>
                                                        <p class="text-xs text-slate-400">
                                                            {{ $atr->funcionario->email }}</p>
                                                    </td>
                                                    <td class="px-4 py-3 hidden md:table-cell">
                                                        @if ($atr->tipo === 'entrega')
                                                            <span
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs lato-bold bg-teal-100 text-teal-700 dark:bg-teal-900/30 dark:text-teal-300">
                                                                <x-lucide-truck class="w-3 h-3" /> Entrega
                                                            </span>
                                                        @else
                                                            <span
                                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-lg text-xs lato-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">
                                                                <x-lucide-rotate-ccw class="w-3 h-3" /> Devolução
                                                            </span>
                                                        @endif
                                                    </td>
                                                    <td
                                                        class="px-4 py-3 hidden lg:table-cell text-xs text-slate-500 lato-regular">
                                                        {{ $atr->data_entrega?->format('d/m/Y') ?? '—' }}
                                                    </td>
                                                    <td class="px-4 py-3 hidden lg:table-cell text-xs lato-regular">
                                                        @if ($atr->data_devolucao)
                                                            <span
                                                                class="text-green-600">{{ $atr->data_devolucao->format('d/m/Y') }}</span>
                                                        @else
                                                            <span class="text-slate-400">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        @if ($atr->tipo === 'entrega' && !$atr->data_devolucao)
                                                            <button wire:click="abrirDevolucao({{ $atr->id }})"
                                                                class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs lato-bold bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300 hover:bg-amber-100 transition">
                                                                <x-lucide-rotate-ccw class="w-3 h-3" /> Devolver
                                                            </button>
                                                        @else
                                                            <span
                                                                class="text-xs text-slate-400 lato-regular">Concluído</span>
                                                        @endif
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════════════════
             ABA: MANUTENÇÃO
        ══════════════════════════════════════════════════════════ --}}
                @if ($aba === 'manutencao')
                    <div class="p-4 md:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h1 class="text-xl lato-black text-slate-800 dark:text-white">Manutenções</h1>
                                <p class="text-sm text-slate-400 lato-regular">Ordens de serviço e histórico de reparos</p>
                            </div>
                            <button wire:click="novaManutencao()"
                                class="cursor-pointer flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:opacity-90 transition w-full sm:w-auto shrink-0">
                                <x-lucide-circle-plus class="w-4 h-4" /> Nova manutenção
                            </button>
                        </div>

                        {{-- Filtros --}}
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3"
                            x-data="{
                                openStatus: false,
                                statusMap: { aberta: 'Aberta', em_andamento: 'Em andamento', concluida: 'Concluída', cancelada: 'Cancelada' },
                            }">

                            {{-- Busca --}}
                            <div class="relative w-full">
                                <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                                <input type="text" wire:model.live.debounce.300ms="searchManutencao"
                                    placeholder="Buscar por título ou equipamento..."
                                    class="w-full pl-10 pr-9 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400/40 lato-regular transition" />
                                @if ($searchManutencao)
                                    <button wire:click="$set('searchManutencao', '')" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                        <x-lucide-x class="w-3.5 h-3.5" />
                                    </button>
                                @endif
                            </div>

                            {{-- Chips de filtro --}}
                            <div class="flex flex-wrap gap-2">

                                {{-- Status --}}
                                <div class="relative" @click.away="openStatus = false">
                                    <button type="button" @click="openStatus = !openStatus"
                                            :class="$wire.filtroManutStatus ? 'border-blue-400 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400'"
                                            class="flex items-center gap-1.5 px-3 py-2 text-xs lato-bold border rounded-xl bg-white dark:bg-slate-700 transition cursor-pointer whitespace-nowrap">
                                        <x-lucide-wrench class="w-3.5 h-3.5 flex-shrink-0" />
                                        <span x-text="$wire.filtroManutStatus ? statusMap[$wire.filtroManutStatus] : 'Status'"></span>
                                        <x-lucide-chevron-down class="w-3 h-3 flex-shrink-0 transition-transform" x-bind:class="openStatus ? 'rotate-180' : ''" />
                                    </button>
                                    <div x-show="openStatus" x-cloak x-transition
                                         class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                                        <button type="button" @click="openStatus=false; $wire.set('filtroManutStatus', '')"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 flex items-center gap-2 lato-regular">
                                            <x-lucide-wrench class="w-3.5 h-3.5" /> Todos os status
                                        </button>
                                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                        @php
                                            $manutStatusDots = [
                                                'aberta'       => ['dot' => 'bg-amber-400',  'label' => 'Aberta'],
                                                'em_andamento' => ['dot' => 'bg-blue-500',   'label' => 'Em andamento'],
                                                'concluida'    => ['dot' => 'bg-emerald-500','label' => 'Concluída'],
                                                'cancelada'    => ['dot' => 'bg-slate-400',  'label' => 'Cancelada'],
                                            ];
                                        @endphp
                                        @foreach ($manutStatusDots as $stVal => $stInfo)
                                        <button type="button" @click="openStatus=false; $wire.set('filtroManutStatus', '{{ $stVal }}')"
                                                :class="$wire.filtroManutStatus === '{{ $stVal }}' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular">
                                            <span class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ $stInfo['dot'] }} shrink-0"></span>
                                                {{ $stInfo['label'] }}
                                            </span>
                                            <x-lucide-check class="w-3.5 h-3.5 text-blue-500" x-show="$wire.filtroManutStatus === '{{ $stVal }}'" />
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                            </div>
                        </div>

                        @php $mnts = $this->manutencoes; @endphp
                        @if ($mnts->isEmpty())
                            <div class="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                                <x-lucide-wrench class="w-12 h-12 text-slate-300 mb-3" />
                                <p class="text-sm lato-bold text-slate-500">Nenhuma manutenção registrada</p>
                                <p class="text-xs text-slate-400 mt-1 lato-regular">Clique em "Nova manutenção" para registrar</p>
                            </div>
                        @else
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="overflow-x-auto">
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="border-b border-slate-100 dark:border-slate-700">
                                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide">Equipamento</th>
                                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide">Título</th>
                                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide hidden md:table-cell">Tipo</th>
                                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide">Status</th>
                                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Entrada</th>
                                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide hidden lg:table-cell">Custo real</th>
                                                <th class="text-right px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wide">Ações</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                            @foreach ($mnts as $mn)
                                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                                                    <td class="px-4 py-3">
                                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $mn->equipamento->nome }}</p>
                                                        <span class="text-[10px] text-slate-400 lato-regular">{{ $categoriaLabels[$mn->equipamento->categoria] ?? $mn->equipamento->categoria }}</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <p class="text-xs lato-regular text-slate-700 dark:text-slate-200">{{ $mn->titulo }}</p>
                                                        @if ($mn->fornecedor)
                                                            <p class="text-[10px] text-slate-400">{{ $mn->fornecedor }}</p>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 hidden md:table-cell">
                                                        <span class="text-xs lato-regular text-slate-500">{{ $mn->tipo_label }}</span>
                                                    </td>
                                                    <td class="px-4 py-3">
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-lg text-xs lato-bold {{ $manutStatusCls[$mn->status] ?? '' }}">
                                                            {{ $manutStatusLabel[$mn->status] ?? $mn->status }}
                                                        </span>
                                                    </td>
                                                    <td class="px-4 py-3 hidden lg:table-cell text-xs text-slate-500 lato-regular">
                                                        {{ $mn->data_entrada?->format('d/m/Y') }}
                                                        @if ($mn->data_previsao)
                                                            <span class="block text-[10px] text-slate-400">Prev. {{ $mn->data_previsao->format('d/m/Y') }}</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 hidden lg:table-cell text-xs lato-bold">
                                                        @if ($mn->custo_real)
                                                            <span class="text-slate-700 dark:text-slate-200">R$ {{ number_format((float)$mn->custo_real, 2, ',', '.') }}</span>
                                                        @elseif ($mn->custo_estimado)
                                                            <span class="text-slate-400">~R$ {{ number_format((float)$mn->custo_estimado, 2, ',', '.') }}</span>
                                                        @else
                                                            <span class="text-slate-300">—</span>
                                                        @endif
                                                    </td>
                                                    <td class="px-4 py-3 text-right">
                                                        <div class="flex items-center justify-end gap-1">
                                                            {{-- Play: aberta → em andamento --}}
                                                            @if ($mn->status === 'aberta')
                                                                <button wire:click="iniciarManutencao({{ $mn->id }})"
                                                                    title="Iniciar manutenção"
                                                                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition">
                                                                    <x-lucide-play class="w-3.5 h-3.5" />
                                                                </button>
                                                            @endif
                                                            {{-- Encerrar: aberta ou em_andamento --}}
                                                            @if (in_array($mn->status, ['aberta', 'em_andamento']))
                                                                <button wire:click="abrirEncerrarManut({{ $mn->id }})"
                                                                    title="Encerrar manutenção"
                                                                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-indigo-600 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition">
                                                                    <x-lucide-flag class="w-3.5 h-3.5" />
                                                                </button>
                                                            @endif
                                                            <button wire:click="editarManutencao({{ $mn->id }})"
                                                                title="Editar"
                                                                class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                                                <x-lucide-pencil class="w-3.5 h-3.5" />
                                                            </button>
                                                            <button wire:click="abrirConfirm('Remover manutenção?', 'Remover este registro de manutenção permanentemente?', 'excluirManutencao', {{ $mn->id }})"
                                                                title="Remover"
                                                                class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                            </button>
                                                        </div>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════════════════
             ABA: INVENTÁRIO
        ══════════════════════════════════════════════════════════ --}}
                @if ($aba === 'inventario')
                    <div class="p-4 md:p-6 space-y-4">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h1 class="text-xl lato-black text-slate-800 dark:text-white">Inventário</h1>
                                <p class="text-sm text-slate-400 lato-regular">Conferência física periódica dos ativos</p>
                            </div>
                            <button wire:click="$set('modalInventario', true)"
                                class="cursor-pointer flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:opacity-90 transition w-full sm:w-auto shrink-0">
                                <x-lucide-clipboard-list class="w-4 h-4" /> Iniciar inventário
                            </button>
                        </div>

                        @php $invAtivo = $this->inventarioAtivo; @endphp

                        {{-- Inventário em andamento aberto --}}
                        @if ($invAtivo)
                            @php $prog = $invAtivo->progresso; @endphp
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                                    <div>
                                        <h3 class="text-sm lato-bold text-slate-800 dark:text-white">{{ $invAtivo->titulo }}</h3>
                                        <p class="text-xs text-slate-400 lato-regular">Iniciado em {{ $invAtivo->data_inicio->format('d/m/Y') }} · {{ $prog['conferidos'] }}/{{ $prog['total'] }} conferidos</p>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        @if ($invAtivo->status === 'em_andamento')
                                            <button wire:click="abrirConfirm('Cancelar inventário?', 'O inventário será marcado como cancelado e todos os itens pendentes serão descartados. Esta ação não pode ser desfeita.', 'cancelarInventario', {{ $invAtivo->id }}, 'danger', 'Cancelar inventário')"
                                                class="cursor-pointer px-3 py-1.5 rounded-xl text-xs lato-bold bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400 hover:bg-rose-50 hover:text-rose-600 dark:hover:bg-rose-900/20 dark:hover:text-rose-400 transition">
                                                <x-lucide-ban class="w-3.5 h-3.5 inline mr-1" />Cancelar
                                            </button>
                                            <button wire:click="abrirConfirm('Concluir inventário?', 'Confirmar a conclusão deste inventário? Esta ação não pode ser desfeita.', 'concluirInventario', {{ $invAtivo->id }}, 'warning', 'Concluir')"
                                                class="cursor-pointer px-3 py-1.5 rounded-xl text-xs lato-bold bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300 hover:bg-green-200 transition">
                                                <x-lucide-check class="w-3.5 h-3.5 inline mr-1" />Concluir
                                            </button>
                                        @endif
                                        <button wire:click="fecharInventario" class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                            <x-lucide-x class="w-4 h-4" />
                                        </button>
                                    </div>
                                </div>

                                {{-- Barra de progresso --}}
                                <div class="px-5 py-3 bg-slate-50 dark:bg-slate-900/30 border-b border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center justify-between text-xs lato-regular mb-1.5">
                                        <span class="text-slate-500">Progresso</span>
                                        <div class="flex items-center gap-3">
                                            <span class="text-green-600">✓ {{ $prog['encontrados'] }} encontrado(s)</span>
                                            @if ($prog['naoEncontrados'] > 0)
                                                <span class="text-red-500">✗ {{ $prog['naoEncontrados'] }} não encontrado(s)</span>
                                            @endif
                                            @if ($prog['divergencias'] > 0)
                                                <span class="text-amber-500">⚠ {{ $prog['divergencias'] }} divergência(s)</span>
                                            @endif
                                        </div>
                                    </div>
                                    @php $pctProg = $prog['total'] > 0 ? round($prog['conferidos'] / $prog['total'] * 100) : 0; @endphp
                                    <div class="h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-500 rounded-full transition-all" style="width:{{ $pctProg }}%"></div>
                                    </div>
                                </div>

                                {{-- Lista de itens --}}
                                <div class="divide-y divide-slate-100 dark:divide-slate-700 max-h-[500px] overflow-y-auto">
                                    @foreach ($invAtivo->itens as $item)
                                        <div class="flex items-center gap-3 px-5 py-3 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                            <div class="flex-1 min-w-0">
                                                <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $item->equipamento->nome }}</p>
                                                <p class="text-[11px] text-slate-400 lato-regular">
                                                    {{ $categoriaLabels[$item->equipamento->categoria] ?? $item->equipamento->categoria }}
                                                    @if($item->observacao)
                                                        · <span class="{{ in_array($item->status, ['divergencia']) ? 'text-amber-600 dark:text-amber-400' : 'text-red-500 dark:text-red-400' }}">{{ $item->observacao }}</span>
                                                    @endif
                                                    @if ($item->equipamento->codigo_patrimonio)· {{ $item->equipamento->codigo_patrimonio }}@endif
                                                    @if ($item->equipamento->local)· {{ $item->equipamento->local }}@endif
                                                </p>
                                            </div>
                                            <span class="text-[10px] lato-bold px-2 py-0.5 rounded-md shrink-0 {{ $invItemCls[$item->status] ?? '' }}">
                                                {{ $item->status_label }}
                                            </span>
                                            @if ($invAtivo->status === 'em_andamento')
                                                <div class="flex items-center gap-1 shrink-0" x-data="{ open: false }">
                                                    <button @click="open = !open" class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                                        <x-lucide-more-horizontal class="w-4 h-4" />
                                                    </button>
                                                    <div x-show="open" @click.outside="open = false" x-transition style="display:none"
                                                        class="absolute z-20 right-8 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl overflow-hidden text-xs w-44">
                                                        <button @click="$wire.conferirItem({{ $item->id }}, 'encontrado', null); open = false"
                                                            class="cursor-pointer w-full text-left px-3 py-2.5 hover:bg-green-50 dark:hover:bg-green-900/20 text-green-700 dark:text-green-400 lato-bold flex items-center gap-2">
                                                            <x-lucide-check class="w-3.5 h-3.5" /> Encontrado
                                                        </button>
                                                        <div class="border-t border-slate-100 dark:border-slate-700"></div>
                                                        <button @click="$wire.abrirObsItem({{ $item->id }}, 'nao_encontrado', '{{ addslashes($item->equipamento->nome) }}'); open = false"
                                                            class="cursor-pointer w-full text-left px-3 py-2.5 hover:bg-red-50 dark:hover:bg-red-900/20 text-red-600 dark:text-red-400 lato-bold flex items-center gap-2">
                                                            <x-lucide-x class="w-3.5 h-3.5" /> Não encontrado
                                                        </button>
                                                        <button @click="$wire.abrirObsItem({{ $item->id }}, 'divergencia', '{{ addslashes($item->equipamento->nome) }}'); open = false"
                                                            class="cursor-pointer w-full text-left px-3 py-2.5 hover:bg-amber-50 dark:hover:bg-amber-900/20 text-amber-600 dark:text-amber-400 lato-bold flex items-center gap-2">
                                                            <x-lucide-alert-triangle class="w-3.5 h-3.5" /> Divergência
                                                        </button>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Histórico de inventários --}}
                        @php $invs = $this->inventarios; @endphp
                        <div>
                            <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3">Histórico</p>
                            @if ($invs->isEmpty())
                                <div class="flex flex-col items-center justify-center py-16 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                                    <x-lucide-clipboard-list class="w-12 h-12 text-slate-300 mb-3" />
                                    <p class="text-sm lato-bold text-slate-500">Nenhum inventário realizado</p>
                                    <p class="text-xs text-slate-400 mt-1 lato-regular">Clique em "Iniciar inventário" para começar</p>
                                </div>
                            @else
                                <div class="space-y-2">
                                    @foreach ($invs as $inv)
                                        @php $p = $inv->progresso; @endphp
                                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
                                            <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0
                                                {{ $inv->status === 'em_andamento' ? 'bg-violet-100 dark:bg-violet-900/30' : ($inv->status === 'concluido' ? 'bg-green-100 dark:bg-green-900/30' : 'bg-slate-100 dark:bg-slate-700') }}">
                                                <x-lucide-clipboard-list class="w-4 h-4 {{ $inv->status === 'em_andamento' ? 'text-indigo-600' : ($inv->status === 'concluido' ? 'text-green-600' : 'text-slate-400') }}" />
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2">
                                                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $inv->titulo }}</p>
                                                    <span class="shrink-0 text-[10px] lato-bold px-2 py-0.5 rounded-md
                                                        {{ $inv->status === 'em_andamento' ? 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300' : ($inv->status === 'concluido' ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300' : 'bg-slate-100 text-slate-500') }}">
                                                        {{ $inv->status_label }}
                                                    </span>
                                                </div>
                                                <p class="text-xs text-slate-400 lato-regular">
                                                    {{ $inv->data_inicio->format('d/m/Y') }}
                                                    @if ($inv->data_conclusao)· Concluído {{ $inv->data_conclusao->format('d/m/Y') }}@endif
                                                    · {{ $p['conferidos'] }}/{{ $p['total'] }} conferidos
                                                    @if ($p['naoEncontrados'] > 0)
                                                        · <span class="text-red-500">{{ $p['naoEncontrados'] }} não encontrado(s)</span>
                                                    @endif
                                                </p>
                                            </div>
                                            <button wire:click="abrirInventario({{ $inv->id }})"
                                                class="cursor-pointer shrink-0 px-3 py-1.5 rounded-xl text-xs lato-bold text-indigo-600 dark:text-indigo-400 bg-violet-50 dark:bg-violet-900/20 hover:bg-violet-100 transition">
                                                {{ $inv->status === 'em_andamento' ? 'Continuar' : 'Ver detalhes' }}
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════════════════
             ABA: RELATÓRIOS
        ══════════════════════════════════════════════════════════ --}}
                @if ($aba === 'relatorios')
                    <div class="p-4 md:p-6 space-y-5">
                        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                            <div>
                                <h1 class="text-xl lato-black text-slate-800 dark:text-white">Relatórios</h1>
                                <p class="text-sm text-slate-400 lato-regular">Análises e exportação de dados</p>
                            </div>
                            <div class="flex items-center gap-2">
                                <button wire:click="exportarAtivosCSV"
                                    class="cursor-pointer flex items-center gap-2 px-3 py-2 rounded-xl text-xs lato-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition">
                                    <x-lucide-download class="w-3.5 h-3.5" /> Exportar Ativos
                                </button>
                                <button wire:click="exportarEntregasCSV"
                                    class="cursor-pointer flex items-center gap-2 px-3 py-2 rounded-xl text-xs lato-bold bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 hover:bg-slate-50 transition">
                                    <x-lucide-download class="w-3.5 h-3.5" /> Exportar Entregas
                                </button>
                            </div>
                        </div>

                        {{-- Filtros de período --}}
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3"
                            x-data="{
                                openCategoria: false,
                                openPeriodo: false,
                                catMap: @js($categoriaLabels),
                                periodoMap: { este_mes: 'Este mês', ultimo_mes: 'Último mês', ultimos_3_meses: 'Últimos 3 meses', este_ano: 'Este ano' },
                                aplicarPeriodo(p) {
                                    const hoje = new Date();
                                    let de, ate = hoje.toISOString().split('T')[0];
                                    if (p === 'este_mes') { de = new Date(hoje.getFullYear(), hoje.getMonth(), 1).toISOString().split('T')[0]; }
                                    else if (p === 'ultimo_mes') { const d = new Date(hoje.getFullYear(), hoje.getMonth()-1, 1); de = d.toISOString().split('T')[0]; ate = new Date(hoje.getFullYear(), hoje.getMonth(), 0).toISOString().split('T')[0]; }
                                    else if (p === 'ultimos_3_meses') { de = new Date(hoje.getFullYear(), hoje.getMonth()-3, 1).toISOString().split('T')[0]; }
                                    else if (p === 'este_ano') { de = new Date(hoje.getFullYear(), 0, 1).toISOString().split('T')[0]; }
                                    $wire.set('relPeriodoDe', de);
                                    $wire.set('relPeriodoAte', ate);
                                    this.openPeriodo = false;
                                }
                            }">

                            {{-- Linha superior: datas + chips — todos com mesma altura (py-2 text-xs lato-bold) --}}
                            <div class="flex flex-wrap items-center gap-2">

                                {{-- De --}}
                                <div class="relative">
                                    <x-lucide-calendar class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                                    <input wire:model.live="relPeriodoDe" type="date"
                                        class="pl-8 pr-3 py-2 text-xs lato-bold border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/40 cursor-pointer" />
                                </div>

                                <span class="text-slate-300 dark:text-slate-600 text-xs lato-bold select-none">→</span>

                                {{-- Até --}}
                                <div class="relative">
                                    <x-lucide-calendar class="w-3.5 h-3.5 absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                                    <input wire:model.live="relPeriodoAte" type="date"
                                        class="pl-8 pr-3 py-2 text-xs lato-bold border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/40 cursor-pointer" />
                                </div>

                                {{-- Divisor --}}
                                <div class="h-5 w-px bg-slate-200 dark:bg-slate-700 mx-1 hidden sm:block"></div>

                                {{-- Período rápido --}}
                                <div class="relative" @click.away="openPeriodo = false">
                                    <button type="button" @click="openPeriodo = !openPeriodo"
                                            class="flex items-center gap-1.5 px-3 py-2 text-xs lato-bold border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-500 dark:text-slate-400 transition cursor-pointer whitespace-nowrap hover:border-blue-300 hover:text-blue-600">
                                        <x-lucide-calendar-range class="w-3.5 h-3.5 flex-shrink-0" />
                                        Atalho
                                        <x-lucide-chevron-down class="w-3 h-3 flex-shrink-0 transition-transform" x-bind:class="openPeriodo ? 'rotate-180' : ''" />
                                    </button>
                                    <div x-show="openPeriodo" x-cloak x-transition
                                         class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                                        @php
                                            $atalhos = [
                                                'este_mes'        => ['icon' => 'calendar-days',  'dot' => 'bg-blue-400',    'label' => 'Este mês'],
                                                'ultimo_mes'      => ['icon' => 'calendar-minus',  'dot' => 'bg-slate-400',   'label' => 'Último mês'],
                                                'ultimos_3_meses' => ['icon' => 'calendar-clock',  'dot' => 'bg-violet-400',  'label' => 'Últimos 3 meses'],
                                                'este_ano'        => ['icon' => 'calendar-fold',   'dot' => 'bg-indigo-400',  'label' => 'Este ano'],
                                            ];
                                        @endphp
                                        @foreach ($atalhos as $pVal => $pInfo)
                                        <button type="button" @click="aplicarPeriodo('{{ $pVal }}')"
                                                class="w-full text-left px-3 py-2 text-sm text-slate-700 dark:text-slate-200 hover:bg-blue-50 dark:hover:bg-slate-700 hover:text-blue-700 lato-regular transition flex items-center gap-2">
                                            <span class="w-2 h-2 rounded-full {{ $pInfo['dot'] }} shrink-0"></span>
                                            {{ $pInfo['label'] }}
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Categoria --}}
                                <div class="relative self-end" @click.away="openCategoria = false">
                                    <button type="button" @click="openCategoria = !openCategoria"
                                            :class="$wire.relCategoria ? 'border-blue-400 bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400'"
                                            class="flex items-center gap-1.5 px-3 py-2 text-xs lato-bold border rounded-xl bg-white dark:bg-slate-700 transition cursor-pointer whitespace-nowrap">
                                        <x-lucide-tag class="w-3.5 h-3.5 flex-shrink-0" />
                                        <span x-text="$wire.relCategoria ? catMap[$wire.relCategoria] : 'Categoria'"></span>
                                        <x-lucide-chevron-down class="w-3 h-3 flex-shrink-0 transition-transform" x-bind:class="openCategoria ? 'rotate-180' : ''" />
                                    </button>
                                    <div x-show="openCategoria" x-cloak x-transition
                                         class="absolute mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                                        <button type="button" @click="openCategoria=false; $wire.set('relCategoria', '')"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-slate-50 dark:hover:bg-slate-700 text-slate-500 flex items-center gap-2 lato-regular">
                                            <x-lucide-tag class="w-3.5 h-3.5" /> Todas as categorias
                                        </button>
                                        <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                        @php
                                            $relCatMeta = [
                                                'notebook' => ['dot' => 'bg-blue-500',   'label' => 'Notebook'],
                                                'desktop'  => ['dot' => 'bg-indigo-500', 'label' => 'Desktop'],
                                                'monitor'  => ['dot' => 'bg-cyan-500',   'label' => 'Monitor'],
                                                'teclado'  => ['dot' => 'bg-slate-500',  'label' => 'Teclado'],
                                                'mouse'    => ['dot' => 'bg-slate-400',  'label' => 'Mouse'],
                                                'headset'  => ['dot' => 'bg-indigo-600', 'label' => 'Headset'],
                                                'cracha'   => ['dot' => 'bg-amber-500',  'label' => 'Crachá'],
                                                'epi'      => ['dot' => 'bg-orange-500', 'label' => 'EPI'],
                                                'cadeira'  => ['dot' => 'bg-teal-500',   'label' => 'Cadeira'],
                                                'celular'  => ['dot' => 'bg-rose-500',   'label' => 'Celular'],
                                                'outros'   => ['dot' => 'bg-slate-300',  'label' => 'Outros'],
                                            ];
                                        @endphp
                                        @foreach ($relCatMeta as $catVal => $catInfo)
                                        <button type="button" @click="openCategoria=false; $wire.set('relCategoria', '{{ $catVal }}')"
                                                :class="$wire.relCategoria === '{{ $catVal }}' ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300 font-semibold' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'"
                                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular">
                                            <span class="flex items-center gap-2">
                                                <span class="w-2 h-2 rounded-full {{ $catInfo['dot'] }} shrink-0"></span>
                                                {{ $catInfo['label'] }}
                                            </span>
                                            <x-lucide-check class="w-3.5 h-3.5 text-blue-500" x-show="$wire.relCategoria === '{{ $catVal }}'" />
                                        </button>
                                        @endforeach
                                    </div>
                                </div>

                                {{-- Limpar filtros --}}
                                @if ($relPeriodoDe || $relPeriodoAte || $relCategoria)
                                <div class="self-end">
                                    <button wire:click="$set('relPeriodoDe', ''); $set('relPeriodoAte', ''); $set('relCategoria', '')" type="button"
                                            class="cursor-pointer flex items-center gap-1 px-2.5 py-2 text-xs lato-bold text-slate-400 hover:text-red-500 transition">
                                        <x-lucide-x class="w-3.5 h-3.5" /> Limpar
                                    </button>
                                </div>
                                @endif

                            </div>
                        </div>

                        @php $rel = $this->relatorioData; @endphp

                        {{-- KPIs do período --}}
                        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                            @php
                                $relKpis = [
                                    ['label' => 'Entregas no período', 'value' => $rel['entregas']->count(), 'icon' => 'truck', 'color' => 'from-teal-500 to-cyan-600'],
                                    ['label' => 'Devoluções no período', 'value' => $rel['devolucoes']->count(), 'icon' => 'rotate-ccw', 'color' => 'from-amber-500 to-orange-500'],
                                    ['label' => 'Valor do patrimônio', 'value' => 'R$ ' . number_format($rel['valorPatrimonio'], 0, ',', '.'), 'icon' => 'trending-up', 'color' => 'from-blue-500 to-indigo-600'],
                                    ['label' => 'Custo em manutenção', 'value' => 'R$ ' . number_format($rel['custoManutencao'], 0, ',', '.'), 'icon' => 'wrench', 'color' => 'from-red-500 to-rose-600'],
                                ];
                            @endphp
                            @foreach ($relKpis as $kpi)
                                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $kpi['color'] }} flex items-center justify-center mb-3">
                                        <x-dynamic-component :component="'lucide-' . $kpi['icon']" class="w-4 h-4 text-white" />
                                    </div>
                                    <p class="text-xl lato-bold text-slate-800 dark:text-slate-100">{{ $kpi['value'] }}</p>
                                    <p class="text-xs text-slate-500 lato-regular mt-0.5">{{ $kpi['label'] }}</p>
                                </div>
                            @endforeach
                        </div>

                        {{-- Por departamento --}}
                        @if ($rel['porDepartamento']->isNotEmpty())
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                                    <x-lucide-building-2 class="w-4 h-4 text-rose-500" /> Entregas por departamento
                                </h3>
                                @php $maxDepto = max($rel['porDepartamento']->values()->toArray() ?: [1]); @endphp
                                <div class="space-y-3">
                                    @foreach ($rel['porDepartamento'] as $depto => $qtd)
                                        @php $pct = $maxDepto > 0 ? round($qtd / $maxDepto * 100) : 0; @endphp
                                        <div>
                                            <div class="flex items-center justify-between text-xs mb-1">
                                                <span class="lato-regular text-slate-600 dark:text-slate-300">{{ $depto }}</span>
                                                <span class="lato-bold text-slate-700 dark:text-slate-200">{{ $qtd }}</span>
                                            </div>
                                            <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full">
                                                <div class="h-full bg-gradient-to-r from-rose-400 to-pink-500 rounded-full" style="width:{{ $pct }}%"></div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        {{-- Últimas entregas do período --}}
                        @if ($rel['entregas']->isNotEmpty())
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                                <div class="px-5 py-3 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200">Entregas no período</h3>
                                    <span class="text-xs text-slate-400 lato-regular">{{ $rel['entregas']->count() }} registros</span>
                                </div>
                                <div class="overflow-x-auto max-h-64">
                                    <table class="w-full text-sm">
                                        <thead class="sticky top-0 bg-white dark:bg-slate-800 z-10">
                                            <tr class="border-b border-slate-100 dark:border-slate-700">
                                                <th class="text-left px-4 py-2 text-xs lato-bold text-slate-500 uppercase tracking-wide">Ativo</th>
                                                <th class="text-left px-4 py-2 text-xs lato-bold text-slate-500 uppercase tracking-wide">Funcionário</th>
                                                <th class="text-left px-4 py-2 text-xs lato-bold text-slate-500 uppercase tracking-wide hidden md:table-cell">Departamento</th>
                                                <th class="text-left px-4 py-2 text-xs lato-bold text-slate-500 uppercase tracking-wide">Data</th>
                                                <th class="text-right px-4 py-2 text-xs lato-bold text-slate-500 uppercase tracking-wide">Termo</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                            @foreach ($rel['entregas'] as $ent)
                                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30">
                                                    <td class="px-4 py-2.5 text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $ent->equipamento->nome }}</td>
                                                    <td class="px-4 py-2.5 text-xs lato-regular text-slate-600 dark:text-slate-300">{{ $ent->funcionario->name }}</td>
                                                    <td class="px-4 py-2.5 text-xs text-slate-400 hidden md:table-cell">{{ $ent->funcionario->department?->name ?? '—' }}</td>
                                                    <td class="px-4 py-2.5 text-xs text-slate-500">{{ $ent->data_entrega?->format('d/m/Y') }}</td>
                                                    <td class="px-4 py-2.5 text-right">
                                                        <button wire:click="abrirGerarTermo({{ $ent->id }})" type="button"
                                                            class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs lato-bold bg-violet-50 text-violet-700 dark:bg-violet-900/20 dark:text-violet-300 hover:bg-violet-100 transition">
                                                            <x-lucide-file-text class="w-3 h-3" /> Gerar Termo
                                                        </button>
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- ══════════════════════════════════════════════════════════
                 ABA: MODELO DO TERMO
                ══════════════════════════════════════════════════════════ --}}
                @if ($aba === 'termo')
                <div class="p-4 md:p-6 space-y-5"
                     x-data="{
                         previewOpen: false,
                         varOpen: false,
                         vars: [
                             { code: '{empresa.nome}',         desc: 'Nome da empresa' },
                             { code: '{empresa.cnpj}',         desc: 'CNPJ da empresa' },
                             { code: '{funcionario.nome}',     desc: 'Nome do funcionário' },
                             { code: '{funcionario.cargo}',    desc: 'Cargo do funcionário' },
                             { code: '{funcionario.depto}',    desc: 'Departamento' },
                             { code: '{funcionario.email}',    desc: 'E-mail do funcionário' },
                             { code: '{equipamento.nome}',     desc: 'Nome do equipamento' },
                             { code: '{equipamento.categoria}',desc: 'Categoria' },
                             { code: '{equipamento.marca}',    desc: 'Marca' },
                             { code: '{equipamento.modelo}',   desc: 'Modelo' },
                             { code: '{equipamento.serie}',    desc: 'Número de série' },
                             { code: '{equipamento.patrimonio}',desc:'Código de patrimônio' },
                             { code: '{equipamento.condicao}', desc: 'Condição na entrega' },
                             { code: '{data.entrega}',         desc: 'Data de entrega' },
                             { code: '{data.geracao}',         desc: 'Data de geração do documento' },
                             { code: '{termo.numero}',         desc: 'Número do termo' },
                             { code: '{responsavel.nome}',     desc: 'Nome do responsável (entregador)' },
                         ]
                     }">

                    {{-- Header --}}
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                        <div>
                            <h1 class="text-xl lato-black text-slate-800 dark:text-white">Modelo do Termo</h1>
                            <p class="text-sm text-slate-400 lato-regular mt-0.5">Personalize o conteúdo do Termo de Entrega de Equipamento</p>
                        </div>
                        <div class="flex items-center gap-2 flex-wrap">
                            {{-- Variáveis disponíveis --}}
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open=!open" type="button"
                                    class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs lato-bold text-violet-700 dark:text-violet-300 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-700/40 hover:bg-violet-100 dark:hover:bg-violet-900/30 transition">
                                    <x-lucide-code class="w-3.5 h-3.5" /> Variáveis disponíveis
                                    <span :class="open && 'rotate-180'" style="transition:transform .2s;display:inline-flex"><x-lucide-chevron-down class="w-3 h-3" /></span>
                                </button>
                                <div x-show="open" @click.outside="open=false" x-transition
                                    class="absolute right-0 top-full mt-1 z-50 w-72 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl overflow-hidden">
                                    <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">Use nas cláusulas e textos</p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">Clique para copiar</p>
                                    </div>
                                    <div class="max-h-64 overflow-y-auto p-2 space-y-0.5">
                                        <template x-for="v in vars" :key="v.code">
                                            <button @click="navigator.clipboard.writeText(v.code); open=false" type="button"
                                                class="cursor-pointer w-full flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 text-left transition group">
                                                <code class="text-[10px] lato-bold text-indigo-600 dark:text-indigo-400 bg-violet-50 dark:bg-violet-900/30 px-1.5 py-0.5 rounded font-mono group-hover:bg-violet-100" x-text="v.code"></code>
                                                <span class="text-[11px] text-slate-500 dark:text-slate-400 flex-1 truncate" x-text="v.desc"></span>
                                                <x-lucide-copy class="w-3 h-3 text-slate-300 group-hover:text-indigo-400 shrink-0" />
                                            </button>
                                        </template>
                                    </div>
                                </div>
                            </div>
                            <button @click="previewOpen=true" type="button"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs lato-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700 transition shadow-sm">
                                <x-lucide-eye class="w-3.5 h-3.5" /> Pré-visualizar
                            </button>
                            <button wire:click="abrirConfirm('Restaurar padrão?', 'Restaurar o modelo padrão? Todas as alterações não salvas serão perdidas.', 'resetarTermoConfig', null, 'warning', 'Restaurar')"
                                type="button"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-xs lato-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-700/40 hover:bg-rose-100 transition">
                                <x-lucide-rotate-ccw class="w-3.5 h-3.5" /> Restaurar padrão
                            </button>
                            <button wire:click="salvarTermoConfig" type="button"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-4 py-2 rounded-xl text-xs lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition shadow-sm disabled:opacity-60"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="salvarTermoConfig" class="flex items-center gap-1.5">
                                    <x-lucide-save class="w-3.5 h-3.5" /> Salvar modelo
                                </span>
                                <span wire:loading wire:target="salvarTermoConfig" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </span>
                            </button>
                        </div>
                    </div>

                    {{-- ── Barra de contexto do template ────────────────────── --}}
                    <div class="flex items-center justify-between gap-3 px-4 py-2.5 rounded-xl
                        {{ $termoTemplateEhPadrao
                            ? 'bg-amber-50 dark:bg-amber-900/10 border border-amber-200 dark:border-amber-800/40'
                            : 'bg-blue-50 dark:bg-blue-900/10 border border-blue-200 dark:border-blue-800/40' }}">
                        <div class="flex items-center gap-2 min-w-0">
                            {{-- Botão Biblioteca de Templates --}}
                            <button wire:click="$set('modalBibliotecaTemplates', true)" type="button"
                                class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs lato-bold bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-200 hover:border-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition shadow-sm">
                                <x-lucide-layout-template class="w-3.5 h-3.5" />
                                Template
                            </button>
                            <span class="text-slate-300 dark:text-slate-600">|</span>
                            <x-lucide-pencil class="w-3.5 h-3.5 {{ $termoTemplateEhPadrao ? 'text-amber-500' : 'text-blue-500' }} shrink-0" />
                            <span class="text-xs lato-regular text-slate-500 dark:text-slate-400 hidden sm:inline">Editando:</span>
                            <span class="text-xs lato-bold text-slate-800 dark:text-white truncate">{{ $termoTemplateNome }}</span>
                            @if($termoTemplateEhPadrao)
                                <span class="shrink-0 text-[9px] lato-bold bg-amber-100 text-amber-700 px-1.5 py-0.5 rounded-full border border-amber-200">Padrão</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-1.5 shrink-0">
                            <button wire:click="abrirModalRenomear" type="button"
                                class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] lato-bold text-slate-500 dark:text-slate-400 hover:bg-white dark:hover:bg-slate-700 border border-transparent hover:border-slate-200 dark:hover:border-slate-600 transition">
                                <x-lucide-pencil class="w-3 h-3" /> Renomear
                            </button>
                            @if(! $termoTemplateEhPadrao && $termoTemplateId)
                                <button wire:click="definirTemplatePadrao({{ $termoTemplateId }})" type="button"
                                    class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-[11px] lato-bold text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 border border-transparent hover:border-amber-200 dark:hover:border-amber-700/40 transition">
                                    <x-lucide-star class="w-3 h-3" /> Definir padrão
                                </button>
                            @endif
                        </div>
                    </div>

                    <div class="grid grid-cols-1 xl:grid-cols-3 gap-5">

                        {{-- ── Coluna principal (esquerda) ────────────── --}}
                        <div class="xl:col-span-2 space-y-4">

                            {{-- Seção 0: Logo da empresa --}}
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                                <div class="flex items-center gap-2 px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                                    <div class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                        <x-lucide-image class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                                    </div>
                                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Logo da empresa</span>
                                    <span class="ml-auto text-[10px] text-slate-400 lato-regular">PNG, JPG ou SVG · máx. 2 MB</span>
                                </div>
                                <div class="p-4">
                                    <div class="flex flex-col sm:flex-row gap-4 items-start">

                                        {{-- Preview / Upload --}}
                                        <div class="shrink-0">
                                            @if($termoLogoPath)
                                                <div class="relative group">
                                                    <img src="{{ asset('storage/' . $termoLogoPath) }}"
                                                         alt="Logo"
                                                         class="h-20 max-w-[200px] object-contain rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 p-2">
                                                    <button wire:click="abrirConfirm('Remover logo?', 'Remover o logo deste template?', 'removerLogo')"
                                                        class="cursor-pointer absolute -top-2 -right-2 w-6 h-6 bg-rose-500 hover:bg-rose-600 text-white rounded-full flex items-center justify-center shadow-sm opacity-0 group-hover:opacity-100 transition">
                                                        <x-lucide-x class="w-3 h-3" />
                                                    </button>
                                                </div>
                                            @elseif($termoLogoFile)
                                                <img src="{{ $termoLogoFile->temporaryUrl() }}"
                                                     alt="Preview"
                                                     class="h-20 max-w-[200px] object-contain rounded-xl border border-dashed border-emerald-400 bg-emerald-50 dark:bg-emerald-900/10 p-2">
                                            @else
                                                <label for="logo-upload"
                                                    class="cursor-pointer flex flex-col items-center justify-center w-36 h-20 rounded-xl border-2 border-dashed border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/10 transition group">
                                                    <x-lucide-upload-cloud class="w-6 h-6 text-slate-400 group-hover:text-blue-500 mb-1" />
                                                    <span class="text-[11px] text-slate-400 group-hover:text-blue-500 lato-bold">Importar logo</span>
                                                </label>
                                            @endif
                                            <input id="logo-upload" type="file" wire:model="termoLogoFile" accept="image/*" class="hidden">
                                            @if(!$termoLogoPath && !$termoLogoFile)
                                            @else
                                                <label for="logo-upload" class="cursor-pointer mt-2 block text-center text-[11px] text-blue-500 hover:text-blue-600 lato-bold">
                                                    Trocar logo
                                                </label>
                                                <input id="logo-upload" type="file" wire:model="termoLogoFile" accept="image/*" class="hidden">
                                            @endif
                                            @error('termoLogoFile') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                        </div>

                                        {{-- Posição do logo --}}
                                        <div class="flex-1 space-y-2">
                                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">
                                                Posição no documento
                                            </label>
                                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                                                @foreach([
                                                    ['value' => 'esquerda', 'icon' => 'align-left',   'label' => 'Esquerda'],
                                                    ['value' => 'centro',   'icon' => 'align-center', 'label' => 'Centro'],
                                                    ['value' => 'direita',  'icon' => 'align-right',  'label' => 'Direita'],
                                                    ['value' => 'nenhum',   'icon' => 'eye-off',      'label' => 'Ocultar'],
                                                ] as $pos)
                                                    <button type="button"
                                                        wire:click="$set('termoLogoPosicao', '{{ $pos['value'] }}')"
                                                        class="cursor-pointer flex flex-col items-center gap-1 py-2.5 px-2 rounded-xl border text-xs lato-bold transition
                                                            {{ $termoLogoPosicao === $pos['value']
                                                                ? 'border-blue-500 bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400'
                                                                : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-500 bg-white dark:bg-slate-900' }}">
                                                        <x-dynamic-component :component="'lucide-' . $pos['icon']" class="w-4 h-4" />
                                                        {{ $pos['label'] }}
                                                    </button>
                                                @endforeach
                                            </div>
                                            <p class="text-[11px] text-slate-400 lato-regular mt-1">
                                                Define onde o logo aparece no cabeçalho do termo impresso.
                                                @if($termoLogoPosicao === 'nenhum') <span class="text-amber-500">O logo não será exibido no documento.</span> @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Seção 1: Cabeçalho --}}
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                                <div class="flex items-center gap-2 px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                                    <div class="w-6 h-6 rounded-lg bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center">
                                        <x-lucide-heading class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                    </div>
                                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Cabeçalho do documento</span>
                                </div>
                                <div class="p-4 space-y-3">
                                    <div>
                                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Título principal *</label>
                                        <input wire:model="termoTitulo" type="text"
                                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition lato-regular"
                                            placeholder="Ex: Termo de Entrega e Responsabilidade de Equipamento">
                                        @error('termoTitulo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Subtítulo / descrição <span class="text-slate-300 normal-case font-normal">(opcional)</span></label>
                                        <input wire:model="termoSubtitulo" type="text"
                                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition lato-regular"
                                            placeholder="Ex: Gerado em {data.geracao} · Nº {termo.numero}">
                                    </div>
                                    <div>
                                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5 uppercase tracking-wide">Texto introdutório <span class="text-slate-300 normal-case font-normal">(aparece antes das cláusulas)</span></label>
                                        <textarea wire:model="termoIntroTexto" rows="3"
                                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition resize-none lato-regular"
                                            placeholder="Ex: Pelo presente instrumento, a empresa {empresa.nome} entrega ao funcionário {funcionario.nome} o equipamento abaixo discriminado..."></textarea>
                                    </div>
                                </div>
                            </div>

                            {{-- Seção 2: Cláusulas --}}
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                                            <x-lucide-list-ordered class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                                        </div>
                                        <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Cláusulas e condições</span>
                                        <span class="text-xs lato-regular text-slate-400">({{ count($termoClausulas) }})</span>
                                    </div>
                                    <button wire:click="addClausula" type="button"
                                        class="cursor-pointer inline-flex items-center gap-1 px-2.5 py-1.5 rounded-lg text-xs lato-bold text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 hover:bg-blue-100 dark:hover:bg-blue-900/30 transition">
                                        <x-lucide-plus class="w-3.5 h-3.5" /> Nova cláusula
                                    </button>
                                </div>
                                <div class="p-4 space-y-3">
                                    @forelse($termoClausulas as $idx => $clausula)
                                        <div class="group flex gap-2 items-start bg-slate-50 dark:bg-slate-900/40 border border-slate-200 dark:border-slate-700/60 rounded-xl p-3 transition hover:border-blue-200 dark:hover:border-blue-700/40">
                                            {{-- Número + controles --}}
                                            <div class="flex flex-col items-center gap-1 shrink-0 pt-0.5">
                                                <div class="w-6 h-6 rounded-lg bg-blue-500 flex items-center justify-center text-white text-[10px] lato-black">
                                                    {{ $idx + 1 }}
                                                </div>
                                                <button wire:click="moverClausula({{ $idx }}, 'up')"
                                                    type="button" @class(['invisible' => $idx === 0])
                                                    class="cursor-pointer p-0.5 rounded text-slate-300 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                                    <x-lucide-chevron-up class="w-3.5 h-3.5" />
                                                </button>
                                                <button wire:click="moverClausula({{ $idx }}, 'down')"
                                                    type="button" @class(['invisible' => $idx === count($termoClausulas) - 1])
                                                    class="cursor-pointer p-0.5 rounded text-slate-300 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                                    <x-lucide-chevron-down class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                            {{-- Textarea --}}
                                            <div class="flex-1 min-w-0">
                                                <textarea wire:model="termoClausulas.{{ $idx }}.texto"
                                                    rows="3"
                                                    class="w-full px-2.5 py-2 text-sm border-0 bg-transparent text-slate-700 dark:text-slate-200 focus:ring-0 outline-none resize-none lato-regular placeholder-slate-300"
                                                    placeholder="Digite o texto desta cláusula. Use **texto** para negrito e variáveis como {funcionario.nome}..."></textarea>
                                                <p class="text-[10px] text-slate-300 dark:text-slate-600 px-0.5">Use **texto** para <strong>negrito</strong> · Variáveis: {funcionario.nome}, {equipamento.nome}...</p>
                                            </div>
                                            {{-- Excluir --}}
                                            <button wire:click="removerClausula({{ $idx }})" type="button"
                                                class="cursor-pointer shrink-0 p-1.5 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 opacity-0 group-hover:opacity-100 transition">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    @empty
                                        <div class="text-center py-8">
                                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                                                <x-lucide-list class="w-5 h-5 text-slate-300" />
                                            </div>
                                            <p class="text-sm text-slate-400 lato-regular">Nenhuma cláusula adicionada</p>
                                            <button wire:click="addClausula" type="button"
                                                class="cursor-pointer mt-2 text-xs text-blue-500 hover:text-blue-700 lato-bold transition">+ Adicionar primeira cláusula</button>
                                        </div>
                                    @endforelse

                                    {{-- Botão adicionar no rodapé --}}
                                    @if(count($termoClausulas) > 0)
                                        <button wire:click="addClausula" type="button"
                                            class="cursor-pointer w-full flex items-center justify-center gap-1.5 py-2 border border-dashed border-blue-300 dark:border-blue-700/40 rounded-xl text-xs lato-bold text-blue-500 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                            <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar cláusula
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Seção 4: Rodapé --}}
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                                <div class="flex items-center gap-2 px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                                    <div class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                                        <x-lucide-align-justify class="w-3.5 h-3.5 text-slate-500" />
                                    </div>
                                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Rodapé do documento</span>
                                </div>
                                <div class="p-4">
                                    <input wire:model="termoRodapeTexto" type="text"
                                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none transition lato-regular"
                                        placeholder="Ex: Documento de controle interno · {empresa.nome}">
                                    <p class="text-[11px] text-slate-400 mt-1.5 lato-regular">Texto exibido no rodapé do documento impresso.</p>
                                </div>
                            </div>
                        </div>

                        {{-- ── Coluna lateral (direita) ───────────────── --}}
                        <div class="space-y-4">

                            {{-- Seção 3: Campos visíveis --}}
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                                <div class="flex items-center gap-2 px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                                    <div class="w-6 h-6 rounded-lg bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center">
                                        <x-lucide-toggle-right class="w-3.5 h-3.5 text-teal-600 dark:text-teal-400" />
                                    </div>
                                    <div>
                                        <span class="text-sm lato-bold text-slate-700 dark:text-slate-200 block">Campos visíveis</span>
                                        <span class="text-[10px] text-slate-400 lato-regular">Informações exibidas no documento</span>
                                    </div>
                                </div>
                                <div class="p-4 space-y-1">
                                    <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-wide mb-2">Dados do equipamento</p>
                                    @foreach(['numero_serie','codigo_patrimonio','marca','modelo','condicao','data_entrega','garantia','local','valor'] as $campo)
                                        @php $ativo = in_array($campo, $termoCamposVisiveis); @endphp
                                        <label class="cursor-pointer flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">
                                            <div wire:click="toggleCampoVisivel('{{ $campo }}')"
                                                class="w-8 h-4 rounded-full transition-colors relative cursor-pointer shrink-0 {{ $ativo ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-600' }}">
                                                <div class="absolute top-0.5 w-3 h-3 bg-white rounded-full shadow transition-all {{ $ativo ? 'right-0.5' : 'left-0.5' }}"></div>
                                            </div>
                                            <span class="text-xs lato-regular {{ $ativo ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400' }}">
                                                {{ $termoCamposDisponiveis[$campo] }}
                                            </span>
                                        </label>
                                    @endforeach

                                    <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-wide mt-3 mb-2 pt-3 border-t border-slate-100 dark:border-slate-700">Dados do funcionário</p>
                                    @foreach(['departamento','cargo','email','observacoes'] as $campo)
                                        @php $ativo = in_array($campo, $termoCamposVisiveis); @endphp
                                        <label class="cursor-pointer flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">
                                            <div wire:click="toggleCampoVisivel('{{ $campo }}')"
                                                class="w-8 h-4 rounded-full transition-colors relative cursor-pointer shrink-0 {{ $ativo ? 'bg-teal-500' : 'bg-slate-200 dark:bg-slate-600' }}">
                                                <div class="absolute top-0.5 w-3 h-3 bg-white rounded-full shadow transition-all {{ $ativo ? 'right-0.5' : 'left-0.5' }}"></div>
                                            </div>
                                            <span class="text-xs lato-regular {{ $ativo ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400' }}">
                                                {{ $termoCamposDisponiveis[$campo] }}
                                            </span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Seção 5: Assinaturas --}}
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center">
                                            <x-lucide-pen-line class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                                        </div>
                                        <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Assinaturas</span>
                                    </div>
                                    @if(count($termoAssinaturas) < 4)
                                        <button wire:click="addAssinatura" type="button"
                                            class="cursor-pointer inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] lato-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 transition">
                                            <x-lucide-plus class="w-3 h-3" /> Adicionar
                                        </button>
                                    @endif
                                </div>
                                <div class="p-4 space-y-3">
                                    @foreach($termoAssinaturas as $idx => $ass)
                                        <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-3 space-y-2 group">
                                            <div class="flex items-center justify-between mb-0.5">
                                                <span class="text-[10px] lato-bold text-slate-400 uppercase tracking-wide">Assinatura {{ $idx + 1 }}</span>
                                                @if(count($termoAssinaturas) > 1)
                                                    <button wire:click="removerAssinatura({{ $idx }})" type="button"
                                                        class="cursor-pointer p-1 rounded text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 opacity-0 group-hover:opacity-100 transition">
                                                        <x-lucide-x class="w-3 h-3" />
                                                    </button>
                                                @endif
                                            </div>
                                            <input wire:model="termoAssinaturas.{{ $idx }}.label"
                                                type="text"
                                                class="w-full px-2.5 py-1.5 text-xs border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none transition lato-regular"
                                                placeholder="Nome / Cargo (ex: Funcionário)">
                                            <input wire:model="termoAssinaturas.{{ $idx }}.papel"
                                                type="text"
                                                class="w-full px-2.5 py-1.5 text-xs border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 text-slate-500 dark:text-slate-400 focus:ring-2 focus:ring-amber-400 focus:border-amber-400 outline-none transition lato-regular"
                                                placeholder="Subtítulo (ex: Responsável pela entrega)">
                                        </div>
                                    @endforeach
                                    <p class="text-[10px] text-slate-400 lato-regular text-center">Máximo de 4 áreas de assinatura</p>
                                </div>
                            </div>

                            {{-- Seção: Campos Extras (preenchidos antes de gerar) --}}
                            <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                                <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-lg bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center">
                                            <x-lucide-clipboard-list class="w-3.5 h-3.5 text-rose-600 dark:text-rose-400" />
                                        </div>
                                        <div>
                                            <span class="text-sm lato-bold text-slate-700 dark:text-slate-200 block">Campos extras</span>
                                            <span class="text-[10px] text-slate-400 lato-regular">Preenchidos pelo TI/RH antes de gerar o PDF</span>
                                        </div>
                                    </div>
                                    <button wire:click="addCampoExtraCustom" type="button"
                                        class="cursor-pointer inline-flex items-center gap-1 px-2 py-1 rounded-lg text-[10px] lato-bold text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 hover:bg-rose-100 transition">
                                        <x-lucide-plus class="w-3 h-3" /> Personalizado
                                    </button>
                                </div>
                                <div class="p-4 space-y-1.5">
                                    @foreach($termoCamposExtras as $idx => $campo)
                                        <div class="group flex items-center gap-2 px-2 py-1.5 rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700/40 transition">
                                            {{-- Toggle ativo --}}
                                            @if($campo['tipo'] === 'predefinido')
                                                <div wire:click="toggleCampoExtra('{{ $campo['id'] }}')"
                                                    class="w-8 h-4 rounded-full transition-colors relative cursor-pointer shrink-0 {{ ($campo['ativo'] ?? false) ? 'bg-rose-500' : 'bg-slate-200 dark:bg-slate-600' }}">
                                                    <div class="absolute top-0.5 w-3 h-3 bg-white rounded-full shadow transition-all {{ ($campo['ativo'] ?? false) ? 'right-0.5' : 'left-0.5' }}"></div>
                                                </div>
                                                <span class="text-xs lato-regular flex-1 {{ ($campo['ativo'] ?? false) ? 'text-slate-700 dark:text-slate-200' : 'text-slate-400' }}">
                                                    {{ $campo['label'] }}
                                                    @if(!empty($campo['mascara']))
                                                        <span class="text-[10px] text-slate-300 font-mono ml-1">{{ $campo['mascara'] }}</span>
                                                    @endif
                                                </span>
                                            @else
                                                {{-- Campo customizado: editar label --}}
                                                <div class="w-4 h-4 shrink-0 flex items-center justify-center">
                                                    <div class="w-2 h-2 rounded-full bg-rose-400"></div>
                                                </div>
                                                <input wire:model="termoCamposExtras.{{ $idx }}.label"
                                                    type="text"
                                                    class="flex-1 px-2 py-1 text-xs border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:ring-1 focus:ring-rose-400 focus:border-rose-400 outline-none transition lato-regular"
                                                    placeholder="Nome do campo (ex: Número do Crachá)">
                                                <button wire:click="removerCampoExtra({{ $idx }})" type="button"
                                                    class="cursor-pointer opacity-0 group-hover:opacity-100 p-1 rounded text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
                                                    <x-lucide-x class="w-3 h-3" />
                                                </button>
                                            @endif
                                        </div>
                                    @endforeach
                                    <p class="text-[10px] text-slate-400 lato-regular pt-1 px-2">Campos ativos aparecem no modal antes de gerar o PDF.</p>
                                </div>
                            </div>

                            {{-- Dica --}}
                            <div class="bg-gradient-to-br from-emerald-50 to-teal-50 dark:from-emerald-900/10 dark:to-teal-900/10 border border-emerald-200 dark:border-emerald-700/30 rounded-2xl p-4">
                                <div class="flex gap-2.5">
                                    <x-lucide-lightbulb class="w-4 h-4 text-emerald-500 shrink-0 mt-0.5" />
                                    <div>
                                        <p class="text-xs lato-bold text-emerald-700 dark:text-emerald-400 mb-1">Dica de formatação</p>
                                        <p class="text-[11px] text-emerald-600/80 dark:text-emerald-400/70 lato-regular leading-relaxed">
                                            Use <code class="bg-emerald-100 dark:bg-emerald-900/30 px-1 rounded font-mono text-emerald-700 dark:text-emerald-300">**texto**</code> para deixar palavras em <strong>negrito</strong> dentro das cláusulas.
                                            Insira variáveis como <code class="bg-emerald-100 dark:bg-emerald-900/30 px-1 rounded font-mono text-[10px] text-emerald-700 dark:text-emerald-300">{funcionario.nome}</code> para preencher dados automaticamente.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- ── Botão salvar mobile/final ──────────────────── --}}
                    <div class="flex items-center justify-end pt-2 border-t border-slate-200 dark:border-slate-700">
                        <button wire:click="salvarTermoConfig" type="button"
                            class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition shadow-sm disabled:opacity-60"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="salvarTermoConfig" class="flex items-center gap-1.5">
                                <x-lucide-save class="w-4 h-4" /> Salvar modelo do termo
                            </span>
                            <span wire:loading wire:target="salvarTermoConfig" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                    </div>

                    {{-- ── Modal Pré-visualização ──────────────────────── --}}
                    <div x-show="previewOpen"
                        x-data="{ sheetOpen: false }"
                        x-init="$watch('previewOpen', v => { if(v) $nextTick(() => sheetOpen = true); else sheetOpen = false; })"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
                        class="fixed inset-0 z-50 bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center"
                        @click.self="sheetOpen=false; setTimeout(() => previewOpen=false, 300)" style="display:none">

                        <div class="w-full sm:max-w-3xl sm:mx-4 bg-white dark:bg-slate-900 rounded-t-3xl sm:rounded-2xl shadow-2xl flex flex-col
                                    transition-transform duration-300 ease-out will-change-transform"
                            :class="sheetOpen ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
                            style="max-height: 92dvh;"
                            @click.stop>

                            {{-- Drag handle --}}
                            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                            </div>

                            {{-- Header modal --}}
                            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 shrink-0">
                                <div class="flex items-center gap-2 flex-wrap">
                                    <x-lucide-eye class="w-4 h-4 text-slate-500" />
                                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Pré-visualização do Termo</span>
                                    <span class="text-[10px] px-2 py-0.5 bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 rounded-full lato-bold">Com dados de exemplo</span>
                                </div>
                                <button @click="previewOpen=false" type="button"
                                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                    <x-lucide-x class="w-4 h-4" />
                                </button>
                            </div>

                            {{-- Preview content --}}
                            <div class="overflow-y-auto flex-1" style="background:#edf2f7;">
                                @php
                                    $pvEmpresa = \App\Models\ConfiguracaoEmpresa::instancia();
                                    $pvNome    = $pvEmpresa->nome_empresa ?? 'Empresa';
                                    $pvIniciais = mb_strtoupper(mb_substr(preg_replace('/[^A-Za-z\x{00C0}-\x{00FF}]/u', '', $pvNome), 0, 2));
                                    $pvLogoPath = $termoLogoPath;
                                    $pvLogoUrl  = $pvLogoPath ? asset('storage/' . $pvLogoPath) : null;
                                    $pvPosicao  = $termoLogoPosicao;

                                    $pvVars = [
                                        '{empresa.nome}'          => $pvNome,
                                        '{empresa.cnpj}'          => $pvEmpresa->cnpj ?? '00.000.000/0001-00',
                                        '{funcionario.nome}'      => 'João Silva',
                                        '{funcionario.cargo}'     => 'Analista de TI',
                                        '{funcionario.depto}'     => 'Tecnologia da Informação',
                                        '{funcionario.email}'     => 'joao.silva@empresa.com',
                                        '{equipamento.nome}'      => 'Notebook Dell Inspiron',
                                        '{equipamento.categoria}' => 'Notebook',
                                        '{equipamento.marca}'     => 'Dell',
                                        '{equipamento.modelo}'    => 'Inspiron 15 5520',
                                        '{equipamento.serie}'     => 'SN-ABC123456',
                                        '{equipamento.patrimonio}'=> 'PAT-00042',
                                        '{equipamento.condicao}'  => 'Novo',
                                        '{data.entrega}'          => now()->format('d/m/Y'),
                                        '{data.geracao}'          => now()->format('d/m/Y'),
                                        '{termo.numero}'          => '000001',
                                        '{responsavel.nome}'      => 'Maria Souza',
                                    ];
                                    $pvText = fn(string $t) => str_replace(array_keys($pvVars), array_values($pvVars), $t);
                                    $pvBold = fn(string $t) => preg_replace('/\*\*(.+?)\*\*/', '<strong>$1</strong>', $pvText($t));

                                    $pvCamposEqLabels = [
                                        'numero_serie'=>['Nº de Série','SN-ABC123456'],
                                        'codigo_patrimonio'=>['Cód. Patrimônio','PAT-00042'],
                                        'marca'=>['Marca','Dell'],
                                        'modelo'=>['Modelo','Inspiron 15 5520'],
                                        'condicao'=>['Condição na Entrega','Novo'],
                                        'data_entrega'=>['Data de Entrega', now()->format('d/m/Y')],
                                        'garantia'=>['Garantia até','—'],
                                        'local'=>['Local/Setor','Sede'],
                                        'valor'=>['Valor do Bem','—'],
                                    ];
                                    $pvCamposAtivosEq = array_filter(
                                        array_keys($pvCamposEqLabels),
                                        fn($k) => in_array($k, $termoCamposVisiveis)
                                    );

                                    $pvNumAss = count($termoAssinaturas);
                                    $pvSigCols = match(true) { $pvNumAss >= 4 => '1fr 1fr 1fr 1fr', $pvNumAss === 3 => '1fr 1fr 1fr', $pvNumAss === 1 => '1fr', default => '1fr 1fr' };
                                @endphp

                                {{-- Folha --}}
                                <div style="background:#fff;max-width:680px;margin:20px auto;border-radius:4px;box-shadow:0 2px 20px rgba(0,0,0,.10);overflow:hidden;font-family:'Segoe UI',Arial,sans-serif;font-size:12px;color:#1a202c;line-height:1.55;">

                                    {{-- Faixa accent --}}
                                    <div style="height:5px;background:linear-gradient(90deg,#1e40af 0%,#3b82f6 50%,#60a5fa 100%);"></div>

                                    <div style="padding:28px 36px 32px;">

                                        {{-- Cabeçalho com logo posicionável --}}
                                        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:16px;padding-bottom:18px;margin-bottom:18px;border-bottom:2px solid #e2e8f0;">
                                            @if($pvPosicao === 'esquerda')
                                                {{-- Logo esq | Nome centro | Meta dir --}}
                                                <div style="flex-shrink:0;">
                                                    @if($pvLogoUrl)
                                                        <img src="{{ $pvLogoUrl }}" style="max-height:52px;max-width:140px;object-fit:contain;display:block;" alt="Logo">
                                                    @else
                                                        <div style="width:52px;height:52px;background:linear-gradient(135deg,#1e40af,#3b82f6);border-radius:10px;display:flex;align-items:center;justify-content:center;">
                                                            <span style="color:#fff;font-size:18px;font-weight:900;">{{ $pvIniciais }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div style="flex:1;text-align:center;">
                                                    <div style="font-size:14px;font-weight:800;color:#1e3a8a;">{{ $pvNome }}</div>
                                                    <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Departamento de TI / Recursos Humanos</div>
                                                </div>
                                                <div style="text-align:right;flex-shrink:0;">
                                                    <div style="font-size:8px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;">Nº do Documento</div>
                                                    <div style="font-size:12px;font-weight:700;color:#1e293b;">#000001</div>
                                                    <div style="font-size:9px;color:#64748b;margin-top:3px;">{{ now()->format('d/m/Y') }}</div>
                                                </div>

                                            @elseif($pvPosicao === 'centro')
                                                <div style="flex:1;">
                                                    <div style="font-size:14px;font-weight:800;color:#1e3a8a;">{{ $pvNome }}</div>
                                                    <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Departamento de TI / Recursos Humanos</div>
                                                </div>
                                                <div style="flex-shrink:0;text-align:center;">
                                                    @if($pvLogoUrl)
                                                        <img src="{{ $pvLogoUrl }}" style="max-height:52px;max-width:140px;object-fit:contain;display:block;margin:0 auto;" alt="Logo">
                                                    @else
                                                        <div style="width:52px;height:52px;background:linear-gradient(135deg,#1e40af,#3b82f6);border-radius:10px;display:flex;align-items:center;justify-content:center;margin:0 auto;">
                                                            <span style="color:#fff;font-size:18px;font-weight:900;">{{ $pvIniciais }}</span>
                                                        </div>
                                                    @endif
                                                </div>
                                                <div style="flex:1;text-align:right;">
                                                    <div style="font-size:8px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;">Nº do Documento</div>
                                                    <div style="font-size:12px;font-weight:700;color:#1e293b;">#000001</div>
                                                    <div style="font-size:9px;color:#64748b;margin-top:3px;">{{ now()->format('d/m/Y') }}</div>
                                                </div>

                                            @elseif($pvPosicao === 'direita')
                                                <div style="flex:1;">
                                                    <div style="font-size:14px;font-weight:800;color:#1e3a8a;">{{ $pvNome }}</div>
                                                    <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Departamento de TI / Recursos Humanos</div>
                                                </div>
                                                <div style="text-align:center;flex-shrink:0;">
                                                    <div style="font-size:8px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;">Nº do Documento</div>
                                                    <div style="font-size:12px;font-weight:700;color:#1e293b;">#000001</div>
                                                    <div style="font-size:9px;color:#64748b;margin-top:3px;">{{ now()->format('d/m/Y') }}</div>
                                                </div>
                                                <div style="flex-shrink:0;text-align:right;">
                                                    @if($pvLogoUrl)
                                                        <img src="{{ $pvLogoUrl }}" style="max-height:52px;max-width:140px;object-fit:contain;display:block;margin-left:auto;" alt="Logo">
                                                    @else
                                                        <div style="width:52px;height:52px;background:linear-gradient(135deg,#1e40af,#3b82f6);border-radius:10px;display:flex;align-items:center;justify-content:center;margin-left:auto;">
                                                            <span style="color:#fff;font-size:18px;font-weight:900;">{{ $pvIniciais }}</span>
                                                        </div>
                                                    @endif
                                                </div>

                                            @else
                                                {{-- Sem logo --}}
                                                <div>
                                                    <div style="font-size:14px;font-weight:800;color:#1e3a8a;">{{ $pvNome }}</div>
                                                    <div style="font-size:10px;color:#94a3b8;margin-top:2px;">Departamento de TI / Recursos Humanos</div>
                                                </div>
                                                <div style="text-align:right;flex-shrink:0;">
                                                    <div style="font-size:8px;color:#94a3b8;text-transform:uppercase;letter-spacing:1px;">Nº do Documento</div>
                                                    <div style="font-size:12px;font-weight:700;color:#1e293b;">#000001</div>
                                                    <div style="font-size:9px;color:#64748b;margin-top:3px;">{{ now()->format('d/m/Y') }}</div>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Título --}}
                                        <div style="text-align:center;padding:12px 16px;background:#f8fafc;border:1px solid #e2e8f0;border-radius:6px;margin-bottom:18px;">
                                            <div style="font-size:13px;font-weight:800;color:#1e293b;text-transform:uppercase;letter-spacing:1px;">{{ $termoTitulo ?: 'Título do Documento' }}</div>
                                            @if($termoSubtitulo)
                                                <div style="font-size:10px;color:#94a3b8;margin-top:3px;">{!! $pvText($termoSubtitulo) !!}</div>
                                            @else
                                                <div style="font-size:10px;color:#94a3b8;margin-top:3px;">Gerado em {{ now()->format('d/m/Y') }} &nbsp;·&nbsp; Documento Nº 000001</div>
                                            @endif
                                        </div>

                                        {{-- Intro --}}
                                        @if($termoIntroTexto)
                                            <div style="background:#eff6ff;border-left:4px solid #3b82f6;border-radius:0 6px 6px 0;padding:10px 14px;margin-bottom:16px;font-size:11px;color:#1e40af;line-height:1.6;">
                                                {!! $pvBold($termoIntroTexto) !!}
                                            </div>
                                        @endif

                                        {{-- Dados do Equipamento --}}
                                        @if(count($pvCamposAtivosEq))
                                            <div style="margin-bottom:16px;">
                                                <div style="display:flex;align-items:center;gap:7px;margin-bottom:10px;padding-bottom:5px;border-bottom:1.5px solid #e2e8f0;">
                                                    <div style="width:7px;height:7px;border-radius:50%;background:#3b82f6;flex-shrink:0;"></div>
                                                    <span style="font-size:8.5px;font-weight:800;text-transform:uppercase;letter-spacing:1.3px;color:#475569;">Dados do Equipamento</span>
                                                </div>
                                                <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:9px 18px;">
                                                    @foreach($pvCamposAtivosEq as $c)
                                                        <div>
                                                            <div style="font-size:8px;font-weight:700;text-transform:uppercase;letter-spacing:.7px;color:#94a3b8;margin-bottom:2px;">{{ $pvCamposEqLabels[$c][0] }}</div>
                                                            <div style="font-size:11.5px;font-weight:600;color:#1e293b;">{{ $pvCamposEqLabels[$c][1] }}</div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif

                                        {{-- Dados do Funcionário --}}
                                        <div style="margin-bottom:16px;">
                                            <div style="display:flex;align-items:center;gap:7px;margin-bottom:10px;padding-bottom:5px;border-bottom:1.5px solid #e2e8f0;">
                                                <div style="width:7px;height:7px;border-radius:50%;background:#10b981;flex-shrink:0;"></div>
                                                <span style="font-size:8.5px;font-weight:800;text-transform:uppercase;letter-spacing:1.3px;color:#475569;">Dados do Funcionário / Responsável</span>
                                            </div>
                                            <div style="display:grid;grid-template-columns:1fr 1fr;gap:9px 18px;">
                                                <div>
                                                    <div style="font-size:8px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:2px;">Nome Completo</div>
                                                    <div style="font-size:11.5px;font-weight:600;color:#1e293b;">João Silva</div>
                                                </div>
                                                @if(in_array('departamento', $termoCamposVisiveis))
                                                <div>
                                                    <div style="font-size:8px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:2px;">Departamento</div>
                                                    <div style="font-size:11.5px;font-weight:600;color:#1e293b;">Tecnologia da Informação</div>
                                                </div>
                                                @endif
                                                @if(in_array('cargo', $termoCamposVisiveis))
                                                <div>
                                                    <div style="font-size:8px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:2px;">Cargo</div>
                                                    <div style="font-size:11.5px;font-weight:600;color:#1e293b;">Analista de TI</div>
                                                </div>
                                                @endif
                                                @if(in_array('email', $termoCamposVisiveis))
                                                <div>
                                                    <div style="font-size:8px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:2px;">E-mail</div>
                                                    <div style="font-size:11.5px;font-weight:600;color:#1e293b;">joao.silva@empresa.com</div>
                                                </div>
                                                @endif
                                                {{-- Campos extras ativos --}}
                                                @foreach($termoCamposExtras as $ce)
                                                    @if(!empty($ce['ativo']))
                                                    <div>
                                                        <div style="font-size:8px;font-weight:700;text-transform:uppercase;color:#94a3b8;margin-bottom:2px;">{{ $ce['label'] }}</div>
                                                        <div style="font-size:11.5px;font-weight:600;color:#94a3b8;font-style:italic;">a preencher</div>
                                                    </div>
                                                    @endif
                                                @endforeach
                                            </div>
                                        </div>

                                        {{-- Cláusulas --}}
                                        @php $pvClausulas = array_filter($termoClausulas, fn($c) => !empty(trim($c['texto'] ?? ''))); @endphp
                                        @if(count($pvClausulas) > 0)
                                            <div style="background:#f8fafc;border:1px solid #e2e8f0;border-radius:8px;padding:14px 16px;margin-bottom:20px;">
                                                <div style="font-size:8.5px;font-weight:800;text-transform:uppercase;letter-spacing:1.3px;color:#64748b;margin-bottom:10px;">Termos e Condições de Uso</div>
                                                <ol style="list-style:none;padding:0;margin:0;counter-reset:clause;">
                                                    @foreach($pvClausulas as $c)
                                                        <li style="counter-increment:clause;display:flex;gap:8px;margin-bottom:7px;font-size:10.5px;color:#374151;line-height:1.55;">
                                                            <span style="font-weight:700;color:#3b82f6;min-width:16px;flex-shrink:0;">{{ $loop->iteration }}.</span>
                                                            <span>{!! $pvBold($c['texto']) !!}</span>
                                                        </li>
                                                    @endforeach
                                                </ol>
                                            </div>
                                        @endif

                                        {{-- Assinaturas --}}
                                        <div style="display:grid;grid-template-columns:{{ $pvSigCols }};gap:24px;margin-top:24px;">
                                            @foreach($termoAssinaturas as $ass)
                                                <div style="text-align:center;">
                                                    <div style="height:44px;"></div>
                                                    <div style="border-top:1.5px solid #334155;padding-top:8px;">
                                                        <div style="font-size:11px;font-weight:700;color:#1e293b;">{{ $ass['label'] ?? 'Assinatura' }}</div>
                                                        @if(!empty($ass['papel']))<div style="font-size:9.5px;color:#94a3b8;margin-top:2px;">{{ $ass['papel'] }}</div>@endif
                                                    </div>
                                                </div>
                                            @endforeach
                                        </div>

                                        {{-- Rodapé --}}
                                        <div style="margin-top:22px;padding-top:12px;border-top:1px solid #e2e8f0;display:flex;justify-content:space-between;align-items:center;">
                                            <span style="font-size:9px;color:#94a3b8;">{{ $pvText($termoRodapeTexto ?: 'Documento de controle interno') }}</span>
                                            <div style="text-align:right;">
                                                <div style="font-size:10px;font-weight:700;color:#64748b;font-family:monospace;">TERMO-000001</div>
                                                <div style="font-size:9px;color:#94a3b8;">Gerado em {{ now()->format('d/m/Y H:i') }}</div>
                                            </div>
                                        </div>

                                    </div>{{-- /padding --}}

                                    {{-- Faixa accent inferior --}}
                                    <div style="height:4px;background:linear-gradient(90deg,#1e40af 0%,#3b82f6 50%,#60a5fa 100%);"></div>
                                </div>{{-- /folha --}}
                            </div>

                            {{-- Footer modal --}}
                            <div class="flex items-center justify-between gap-3 px-5 py-3 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 shrink-0
                                        pb-[max(0.75rem,env(safe-area-inset-bottom))]">
                                <p class="text-xs text-slate-400 lato-regular">Dados de exemplo — o documento real usa informações reais do funcionário e equipamento</p>
                                <button @click="sheetOpen=false; setTimeout(() => previewOpen=false, 300)" type="button"
                                    class="cursor-pointer shrink-0 px-4 py-2 rounded-xl text-xs lato-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition">
                                    Fechar
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
                @endif

            </div>{{-- fim overflow-y --}}
        </div>{{-- fim área de conteúdo --}}
    </div>{{-- fim flex --}}

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: GERAR TERMO — Preenchimento de campos extras antes do PDF
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalGerarTermo"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        style="display:none" @keydown.escape.window="$wire.set('modalGerarTermo', false)">

        <div @click.stop x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden">

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                        <x-lucide-file-text class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <p class="text-sm lato-black text-slate-800 dark:text-white">Gerar Termo de Responsabilidade</p>
                        <p class="text-xs text-slate-400 lato-regular mt-0.5">Preencha as informações complementares</p>
                    </div>
                </div>
                <button wire:click="$set('modalGerarTermo', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="p-5 space-y-4">

                {{-- Aviso contextual --}}
                @if(count($gerarTermoCampos))
                    <div class="flex gap-2.5 bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-700/30 rounded-xl px-3 py-2.5">
                        <x-lucide-info class="w-4 h-4 text-indigo-500 shrink-0 mt-0.5" />
                        <p class="text-xs text-violet-700 dark:text-violet-300 lato-regular leading-relaxed">
                            Estes campos serão incluídos no PDF. Deixe em branco os que não se aplicam.
                        </p>
                    </div>

                    {{-- Campos extras a preencher --}}
                    @foreach($gerarTermoCampos as $campo)
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">
                                {{ $campo['label'] ?? 'Campo' }}
                                @if(!empty($campo['mascara']))
                                    <span class="normal-case font-normal text-slate-300 ml-1">{{ $campo['mascara'] }}</span>
                                @endif
                            </label>
                            @if(($campo['id'] ?? '') === 'cpf')
                                {{-- CPF: bloqueado, apenas leitura --}}
                                <div class="relative">
                                    <input type="text"
                                        value="{{ $gerarTermoExtras['cpf'] ?? '' }}"
                                        readonly
                                        class="w-full px-3 py-2.5 pr-9 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900/60 text-slate-700 dark:text-slate-300 lato-regular cursor-not-allowed select-none" />
                                    <x-lucide-lock class="w-3.5 h-3.5 text-slate-400 absolute right-3 top-1/2 -translate-y-1/2" />
                                </div>
                            @else
                                <input wire:model="gerarTermoExtras.{{ $campo['id'] }}"
                                    type="text"
                                    class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition lato-regular"
                                    placeholder="{{ $campo['label'] ?? '' }} do funcionário">
                            @endif
                        </div>
                    @endforeach
                @else
                    <div class="flex flex-col items-center gap-2 py-4 text-center">
                        <x-lucide-file-check class="w-8 h-8 text-emerald-400" />
                        <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">O template selecionado não possui campos extras.</p>
                        <p class="text-xs text-slate-400">O PDF será gerado diretamente.</p>
                    </div>
                @endif
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between gap-3 px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                <button wire:click="$set('modalGerarTermo', false)" type="button"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition">
                    Cancelar
                </button>
                <button wire:click="gerarTermo" type="button"
                    class="cursor-pointer inline-flex items-center gap-2 px-5 py-2 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition shadow-sm">
                    <x-lucide-external-link class="w-4 h-4" /> Gerar PDF
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: NOVO TEMPLATE DE TERMO
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalSalvarTemplate"
        x-data="{ sheetOpen: false }"
        x-init="$watch('$wire.modalSalvarTemplate', v => { if(v) $nextTick(() => sheetOpen = true); else sheetOpen = false; })"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] bg-black/50 backdrop-blur-sm flex items-end sm:items-center justify-center"
        style="display:none" @keydown.escape.window="sheetOpen=false; setTimeout(() => $wire.set('modalSalvarTemplate', false), 300)"
        @click.self="sheetOpen=false; setTimeout(() => $wire.set('modalSalvarTemplate', false), 300)">

        <div @click.stop
            class="w-full sm:max-w-sm sm:mx-4 bg-white dark:bg-slate-800 shadow-2xl flex flex-col
                   rounded-t-3xl sm:rounded-2xl
                   border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                   transition-transform duration-300 ease-out will-change-transform"
            :class="sheetOpen ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'">

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="w-9 h-9 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                    <x-lucide-layout-template class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                </div>
                <div class="flex-1">
                    <p class="text-sm lato-bold text-slate-800 dark:text-white">Novo Template</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Salva o estado atual do editor como um novo template</p>
                </div>
                <button @click="sheetOpen=false; setTimeout(() => $wire.set('modalSalvarTemplate', false), 300)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <div class="p-5">
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Nome do Template *</label>
                <input wire:model="novoTemplateNome" type="text"
                    placeholder="Ex: Termo de Notebook, Termo EPI..."
                    @keydown.enter="$wire.confirmarNovoTemplate()"
                    class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 outline-none transition lato-regular"
                    autofocus>
                @error('novoTemplateNome') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-2 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button @click="sheetOpen=false; setTimeout(() => $wire.set('modalSalvarTemplate', false), 300)" type="button"
                    class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="confirmarNovoTemplate" type="button"
                    class="cursor-pointer flex-1 inline-flex items-center justify-center gap-1.5 px-5 py-2.5 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition shadow-sm disabled:opacity-60"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmarNovoTemplate" class="flex items-center gap-1.5">
                        <x-lucide-save class="w-4 h-4" /> Criar template
                    </span>
                    <span wire:loading wire:target="confirmarNovoTemplate" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: RENOMEAR TEMPLATE
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalRenomearTemplate"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        style="display:none" @keydown.escape.window="$wire.set('modalRenomearTemplate', false)">

        <div @click.stop class="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <div class="w-9 h-9 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                    <x-lucide-pencil class="w-4 h-4 text-slate-600 dark:text-slate-300" />
                </div>
                <p class="text-sm lato-bold text-slate-800 dark:text-white flex-1">Renomear Template</p>
                <button wire:click="$set('modalRenomearTemplate', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <div class="p-5">
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5 uppercase tracking-wide">Novo Nome *</label>
                <input wire:model="renomearTemplateNome" type="text"
                    @keydown.enter="$wire.confirmarRenomear()"
                    class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 focus:ring-2 focus:ring-slate-500 focus:border-slate-500 outline-none transition lato-regular"
                    autofocus>
                @error('renomearTemplateNome') <p class="text-xs text-rose-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                <button wire:click="$set('modalRenomearTemplate', false)" type="button"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 transition">
                    Cancelar
                </button>
                <button wire:click="confirmarRenomear" type="button"
                    class="cursor-pointer inline-flex items-center gap-1.5 px-5 py-2 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-slate-600 to-slate-700 hover:opacity-90 transition shadow-sm disabled:opacity-60"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmarRenomear" class="flex items-center gap-1.5">
                        <x-lucide-check class="w-4 h-4" /> Confirmar
                    </span>
                    <span wire:loading wire:target="confirmarRenomear" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: BIBLIOTECA DE TEMPLATES
═══════════════════════════════════════════════════════════════════════ --}}
    @php
    $tplMeta = function($tpl) {
        $nome = strtolower($tpl->nome);
        if (str_contains($nome, 'notebook') || str_contains($nome, 'desktop') || str_contains($nome, 'informátic'))
            return ['icon' => 'laptop', 'color' => 'bg-blue-500',   'tag' => 'Informática', 'tagCls' => 'bg-blue-100 text-blue-700'];
        if (str_contains($nome, 'epi') || str_contains($nome, 'proteção'))
            return ['icon' => 'shield-check', 'color' => 'bg-emerald-500', 'tag' => 'EPI', 'tagCls' => 'bg-emerald-100 text-emerald-700'];
        if (str_contains($nome, 'celular') || str_contains($nome, 'dispositivo'))
            return ['icon' => 'smartphone', 'color' => 'bg-amber-500', 'tag' => 'Celular', 'tagCls' => 'bg-amber-100 text-amber-700'];
        if (str_contains($nome, 'simplificado') || str_contains($nome, 'recibo'))
            return ['icon' => 'file-text', 'color' => 'bg-slate-500',  'tag' => 'Simplificado', 'tagCls' => 'bg-slate-100 text-slate-600'];
        if (str_contains($nome, 'pessoal') || str_contains($nome, 'dados'))
            return ['icon' => 'user-check', 'color' => 'bg-rose-500',  'tag' => 'Pessoal', 'tagCls' => 'bg-rose-100 text-rose-700'];
        return ['icon' => 'layout-template', 'color' => 'bg-indigo-600', 'tag' => 'Geral', 'tagCls' => 'bg-violet-100 text-violet-700'];
    };
    @endphp

    <div x-show="$wire.modalBibliotecaTemplates"
        x-data="{ busca: '', tab: 'todos', sheetOpen: false }"
        x-init="$watch('$wire.modalBibliotecaTemplates', v => { if(v) $nextTick(() => sheetOpen = true); else sheetOpen = false; })"
        x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
        style="display:none" @keydown.escape.window="sheetOpen=false; setTimeout(() => $wire.set('modalBibliotecaTemplates', false), 300)"
        @click.self="sheetOpen=false; setTimeout(() => $wire.set('modalBibliotecaTemplates', false), 300)">

        <div @click.stop
            class="w-full sm:max-w-3xl sm:mx-4 bg-white dark:bg-slate-800 shadow-2xl flex flex-col
                   rounded-t-3xl sm:rounded-2xl
                   border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                   transition-transform duration-300 ease-out will-change-transform"
            :class="sheetOpen ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
            style="max-height: 92dvh;">

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-center gap-3 px-5 pt-4 pb-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0 shadow-sm">
                    <x-lucide-layout-template class="w-4 h-4 text-white" />
                </div>
                <div class="flex-1 min-w-0">
                    <h2 class="text-sm lato-bold text-slate-800 dark:text-white">Biblioteca de Templates</h2>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Escolha um modelo pronto ou crie o seu</p>
                </div>
                <button wire:click="abrirModalNovoTemplate" type="button"
                    class="cursor-pointer inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition shadow-sm shrink-0">
                    <x-lucide-plus class="w-3.5 h-3.5" /> Novo template
                </button>
                <button @click="sheetOpen=false; setTimeout(() => $wire.set('modalBibliotecaTemplates', false), 300)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Search + Tabs --}}
            <div class="px-6 pt-4 pb-3 border-b border-slate-100 dark:border-slate-700 shrink-0 space-y-3">
                <div class="relative">
                    <x-lucide-search class="w-4 h-4 text-slate-400 absolute left-3 top-1/2 -translate-y-1/2" />
                    <input type="text" x-model="busca" placeholder="Buscar templates..."
                        class="w-full pl-9 pr-4 py-2 rounded-xl border border-slate-200 dark:border-slate-600 bg-slate-50 dark:bg-slate-900 text-sm text-slate-700 dark:text-slate-200 lato-regular focus:outline-none focus:ring-2 focus:ring-indigo-400 placeholder-slate-400" />
                </div>
                <div class="flex items-center gap-1 overflow-x-auto">
                    @php
                    $tabs = [
                        'todos'        => 'Todos',
                        'Informática'  => 'Informática',
                        'EPI'          => 'EPI',
                        'Celular'      => 'Celular',
                        'Simplificado' => 'Simplificado',
                        'Pessoal'      => 'Pessoal',
                        'Geral'        => 'Geral',
                    ];
                    @endphp
                    @foreach($tabs as $tabKey => $tabLabel)
                        <button type="button" @click="tab = '{{ $tabKey }}'"
                            :class="tab === '{{ $tabKey }}'
                                ? 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 border-violet-300 dark:border-violet-700'
                                : 'text-slate-500 dark:text-slate-400 border-transparent hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700'"
                            class="cursor-pointer shrink-0 px-3 py-1.5 rounded-lg text-sm lato-bold border transition">
                            {{ $tabLabel }}
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Grid de templates --}}
            <div class="flex-1 overflow-y-auto px-6 py-5">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    @foreach($this->todosTemplates as $tpl)
                        @php $meta = $tplMeta($tpl); @endphp
                        <div wire:key="bib-{{ $tpl->id }}"
                            x-show="
                                (tab === 'todos' || tab === '{{ $meta['tag'] }}') &&
                                (busca === '' || '{{ strtolower($tpl->nome . ' ' . $tpl->titulo) }}'.includes(busca.toLowerCase()))
                            "
                            class="border border-slate-200 dark:border-slate-700 rounded-2xl p-4 flex flex-col gap-3 bg-white dark:bg-slate-800/60 hover:border-violet-300 dark:hover:border-violet-700 hover:shadow-sm transition group">

                            {{-- Card header --}}
                            <div class="flex items-start gap-3">
                                <div class="w-10 h-10 rounded-xl {{ $meta['color'] }} flex items-center justify-center shrink-0 shadow-sm">
                                    <x-dynamic-component :component="'lucide-' . $meta['icon']" class="w-5 h-5 text-white" />
                                </div>
                                <div class="flex-1 min-w-0">
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-sm lato-bold text-slate-800 dark:text-white">{{ $tpl->nome }}</span>
                                        @if($tpl->padrao)
                                            <span class="text-[10px] lato-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 px-2 py-0.5 rounded-full border border-amber-200 dark:border-amber-700/40">Padrão</span>
                                        @else
                                            <span class="text-[10px] lato-bold bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 px-2 py-0.5 rounded-full">Sistema</span>
                                        @endif
                                    </div>
                                    <span class="inline-block mt-1 text-[11px] lato-bold px-2 py-0.5 rounded-md {{ $meta['tagCls'] }}">{{ $meta['tag'] }}</span>
                                </div>
                                {{-- Ações inline --}}
                                <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition shrink-0">
                                    @if(! $tpl->padrao)
                                        <button wire:click="definirTemplatePadrao({{ $tpl->id }})" title="Definir como padrão"
                                            class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-amber-500 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                                            <x-lucide-star class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                    <button wire:click="duplicarTemplate({{ $tpl->id }})" title="Duplicar"
                                        class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                                        <x-lucide-copy class="w-3.5 h-3.5" />
                                    </button>
                                    <button wire:click="abrirModalRenomear" title="Renomear"
                                        class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                        <x-lucide-pencil class="w-3.5 h-3.5" />
                                    </button>
                                    @if($this->todosTemplates->count() > 1)
                                        <button wire:click="abrirConfirmExcluirTemplate({{ $tpl->id }})" title="Excluir"
                                            class="cursor-pointer p-1.5 rounded-lg text-slate-300 hover:text-rose-500 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        </button>
                                    @endif
                                </div>
                            </div>

                            {{-- Descrição --}}
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular leading-relaxed line-clamp-2">
                                {{ Str::limit(strip_tags($tpl->titulo), 80) }}
                            </p>

                            {{-- Stats --}}
                            <div class="flex items-center gap-1 text-[11px] text-slate-400 lato-regular">
                                <x-lucide-list class="w-3.5 h-3.5" />
                                {{ count($tpl->clausulas ?? []) }} {{ count($tpl->clausulas ?? []) === 1 ? 'cláusula' : 'cláusulas' }}
                                @if(count(array_filter($tpl->campos_extras ?? [], fn($c) => !empty($c['ativo']))) > 0)
                                    <span class="mx-1">·</span>
                                    <x-lucide-file-text class="w-3.5 h-3.5" />
                                    {{ count(array_filter($tpl->campos_extras ?? [], fn($c) => !empty($c['ativo']))) }} campos extras
                                @endif
                            </div>

                            {{-- Botão usar --}}
                            <button wire:click="carregarTemplate({{ $tpl->id }})" type="button"
                                class="cursor-pointer w-full flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition shadow-sm mt-auto">
                                <x-lucide-circle-check class="w-4 h-4" /> Usar template
                            </button>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-between px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <span class="text-xs text-slate-400 lato-regular">{{ $this->todosTemplates->count() }} templates disponíveis</span>
                <button @click="sheetOpen=false; setTimeout(() => $wire.set('modalBibliotecaTemplates', false), 300)" type="button"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition">
                    Cancelar
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: OBSERVAÇÃO DE ITEM (DIVERGÊNCIA / NÃO ENCONTRADO)
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalObsItem"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        style="display:none" @keydown.escape.window="$wire.set('modalObsItem', false)">

        <div @click.stop
            class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            {{-- Barra de cor dinâmica --}}
            <div class="h-1 w-full"
                :class="$wire.obsItemStatus === 'divergencia'
                    ? 'bg-gradient-to-r from-amber-400 to-yellow-500'
                    : 'bg-gradient-to-r from-rose-500 to-red-600'">
            </div>

            {{-- Header --}}
            <div class="flex items-center gap-3 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
                <div class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0"
                    :class="$wire.obsItemStatus === 'divergencia'
                        ? 'bg-amber-100 dark:bg-amber-900/30'
                        : 'bg-rose-100 dark:bg-rose-900/30'">
                    <template x-if="$wire.obsItemStatus === 'divergencia'">
                        <svg class="w-4.5 h-4.5 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                    </template>
                    <template x-if="$wire.obsItemStatus !== 'divergencia'">
                        <svg class="w-4.5 h-4.5 text-rose-600 dark:text-rose-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m9.75 9.75 4.5 4.5m0-4.5-4.5 4.5M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" /></svg>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-bold text-slate-800 dark:text-white"
                        x-text="$wire.obsItemStatus === 'divergencia' ? 'Registrar divergência' : 'Item não encontrado'">
                    </p>
                    <p class="text-xs text-slate-400 lato-regular truncate mt-0.5" x-text="$wire.obsItemNome"></p>
                </div>
                <button wire:click="$set('modalObsItem', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Corpo --}}
            <div class="px-5 py-4 space-y-3">
                {{-- Orientação contextual --}}
                <div class="rounded-xl px-4 py-3 text-xs lato-regular leading-relaxed"
                    :class="$wire.obsItemStatus === 'divergencia'
                        ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40 text-amber-700 dark:text-amber-300'
                        : 'bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/40 text-rose-700 dark:text-rose-300'">
                    <template x-if="$wire.obsItemStatus === 'divergencia'">
                        <span>Descreva o que está diferente: número de série incorreto, estado diferente do registrado, acessórios faltando, localização diferente etc.</span>
                    </template>
                    <template x-if="$wire.obsItemStatus !== 'divergencia'">
                        <span>Informe o que você sabe: último responsável visto, localização esperada, se pode ter sido levado por outro colaborador, se houve furto ou extravio etc.</span>
                    </template>
                </div>

                {{-- Textarea --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">
                        Explicação <span class="text-rose-500">*</span>
                    </label>
                    <textarea wire:model="obsItemTexto" rows="4"
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 px-3 py-2.5 lato-regular resize-none focus:outline-none focus:ring-2 focus:ring-amber-400 dark:focus:ring-amber-500 placeholder-slate-300 dark:placeholder-slate-600"
                        :placeholder="$wire.obsItemStatus === 'divergencia'
                            ? 'Ex: O número de série físico é SN-9981 mas o sistema registra SN-9980...'
                            : 'Ex: O equipamento não foi localizado no setor de TI. Último uso registrado foi por João Silva em 15/05...'"
                    ></textarea>
                    @error('obsItemTexto')
                        <p class="text-xs text-rose-500 lato-regular mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                <button wire:click="$set('modalObsItem', false)" type="button"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition">
                    Cancelar
                </button>
                <button wire:click="salvarObsItem" type="button"
                    class="cursor-pointer inline-flex items-center gap-1.5 px-5 py-2 rounded-xl text-sm lato-bold text-white transition shadow-sm disabled:opacity-60"
                    :class="$wire.obsItemStatus === 'divergencia'
                        ? 'bg-gradient-to-r from-amber-500 to-yellow-500 hover:opacity-90'
                        : 'bg-gradient-to-r from-rose-500 to-red-600 hover:opacity-90'"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarObsItem" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    Registrar
                    </span>
                    <span wire:loading wire:target="salvarObsItem" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     DIALOG: CONFIRMAÇÃO GENÉRICA
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.confirmDialog"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[70] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        style="display:none" @keydown.escape.window="$wire.set('confirmDialog', false)">

        <div @click.stop
            class="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            {{-- Barra de cor no topo --}}
            <div class="h-1 w-full"
                :class="$wire.confirmType === 'warning'
                    ? 'bg-gradient-to-r from-amber-400 to-yellow-500'
                    : 'bg-gradient-to-r from-rose-500 to-red-600'">
            </div>

            {{-- Header --}}
            <div class="flex items-center gap-3 px-5 pt-5 pb-4">
                <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0"
                    :class="$wire.confirmType === 'warning'
                        ? 'bg-amber-100 dark:bg-amber-900/30'
                        : 'bg-rose-100 dark:bg-rose-900/30'">
                    <template x-if="$wire.confirmType === 'warning'">
                        <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" /></svg>
                    </template>
                    <template x-if="$wire.confirmType !== 'warning'">
                        <svg class="w-5 h-5 text-rose-600 dark:text-rose-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>
                    </template>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-bold text-slate-800 dark:text-white" x-text="$wire.confirmTitle"></p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Esta ação exige confirmação.</p>
                </div>
                <button wire:click="$set('confirmDialog', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Corpo --}}
            <div class="px-5 pb-5">
                <div class="rounded-xl px-4 py-3 flex items-start gap-2.5"
                    :class="$wire.confirmType === 'warning'
                        ? 'bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800/40'
                        : 'bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/40'">
                    <svg class="w-4 h-4 shrink-0 mt-0.5"
                        :class="$wire.confirmType === 'warning' ? 'text-amber-500' : 'text-rose-500'"
                        xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9.303 3.376c.866 1.5-.217 3.374-1.948 3.374H4.644c-1.73 0-2.813-1.874-1.948-3.374L10.05 3.378c.866-1.5 3.032-1.5 3.898 0L21.303 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                    </svg>
                    <p class="text-xs lato-regular leading-relaxed"
                        :class="$wire.confirmType === 'warning' ? 'text-amber-700 dark:text-amber-300' : 'text-rose-700 dark:text-rose-300'"
                        x-text="$wire.confirmMessage">
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                <button wire:click="$set('confirmDialog', false)" type="button"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition">
                    Cancelar
                </button>
                <button wire:click="executarConfirmacao" type="button"
                    class="cursor-pointer inline-flex items-center gap-1.5 px-5 py-2 rounded-xl text-sm lato-bold text-white transition shadow-sm disabled:opacity-60"
                    :class="$wire.confirmType === 'warning'
                        ? 'bg-gradient-to-r from-amber-500 to-yellow-500 hover:opacity-90'
                        : 'bg-gradient-to-r from-rose-500 to-red-600 hover:opacity-90'"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="executarConfirmacao" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" /></svg>
                    <span x-text="$wire.confirmLabel"></span>
                    </span>
                    <span wire:loading wire:target="executarConfirmacao" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: CONFIRMAR EXCLUSÃO DE TEMPLATE
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalConfirmExcluirTemplate"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-[60] flex items-center justify-center bg-black/50 backdrop-blur-sm p-4"
        style="display:none" @keydown.escape.window="$wire.set('modalConfirmExcluirTemplate', false)">

        <div @click.stop
            class="relative w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">


            {{-- Header --}}
            <div class="flex items-center gap-3 px-5 pt-5 pb-4">
                <div class="w-10 h-10 rounded-xl bg-rose-100 dark:bg-rose-900/30 flex items-center justify-center shrink-0">
                    <x-lucide-trash-2 class="w-5 h-5 text-rose-600 dark:text-rose-400" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-bold text-slate-800 dark:text-white">Excluir template?</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5 truncate">Esta ação não pode ser desfeita.</p>
                </div>
                <button wire:click="$set('modalConfirmExcluirTemplate', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Corpo --}}
            <div class="px-5 pb-5">
                <div class="rounded-xl bg-rose-50 dark:bg-rose-900/20 border border-rose-200 dark:border-rose-800/40 px-4 py-3 flex items-start gap-2.5">
                    <x-lucide-alert-triangle class="w-4 h-4 text-rose-500 shrink-0 mt-0.5" />
                    <p class="text-xs text-rose-700 dark:text-rose-300 lato-regular leading-relaxed">
                        O template
                        <span class="lato-bold">"{{ $excluirTemplateNome }}"</span>
                        será excluído permanentemente. Termos já gerados não são afetados.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                <button wire:click="$set('modalConfirmExcluirTemplate', false)" type="button"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition">
                    Cancelar
                </button>
                <button wire:click="confirmarExcluirTemplate" type="button"
                    class="cursor-pointer inline-flex items-center gap-1.5 px-5 py-2 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-rose-500 to-red-600 hover:opacity-90 transition shadow-sm disabled:opacity-60"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmarExcluirTemplate" class="flex items-center gap-1.5">
                        <x-lucide-trash-2 class="w-4 h-4" /> Excluir
                    </span>
                    <span wire:loading wire:target="confirmarExcluirTemplate" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: ENCERRAR MANUTENÇÃO
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalEncerrarManut"
        x-transition:enter="transition ease-out duration-150" x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/60 backdrop-blur-sm p-4"
        style="display:none" @keydown.escape.window="$wire.set('modalEncerrarManut', false)">

        <div @click.stop
            class="relative w-full max-w-md bg-white dark:bg-slate-800 rounded-2xl shadow-2xl overflow-hidden"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

            {{-- Linha violet no topo --}}
            <div class="h-1 w-full bg-gradient-to-r from-blue-500 to-indigo-600"></div>

            {{-- Header --}}
            <div class="flex items-center gap-3 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
                <div class="w-9 h-9 rounded-xl bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center shrink-0">
                    <x-lucide-flag class="w-4.5 h-4.5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div class="flex-1">
                    <p class="text-sm lato-bold text-slate-800 dark:text-white">Encerrar manutenção</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Registre a conclusão e os dados finais</p>
                </div>
                <button wire:click="$set('modalEncerrarManut', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Corpo --}}
            <div class="px-5 py-5 space-y-4">

                {{-- Data de conclusão + Custo real (grid) --}}
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">
                            Data de conclusão <span class="text-rose-500">*</span>
                        </label>
                        <input type="date" wire:model="encerrarDataConclusao"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 px-3 py-2 lato-regular focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        @error('encerrarDataConclusao')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">
                            Custo real (R$)
                        </label>
                        <input type="number" wire:model="encerrarCustoReal" step="0.01" min="0"
                            placeholder="0,00"
                            class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 px-3 py-2 lato-regular focus:outline-none focus:ring-2 focus:ring-indigo-400" />
                        @error('encerrarCustoReal')
                            <p class="text-xs text-rose-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                {{-- Resolução --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-300 mb-1.5">
                        O que foi feito / Resolução
                    </label>
                    <textarea wire:model="encerrarResolucao" rows="4"
                        placeholder="Descreva o que foi realizado, peças trocadas, diagnóstico final..."
                        class="w-full rounded-xl border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 px-3 py-2.5 lato-regular resize-none focus:outline-none focus:ring-2 focus:ring-indigo-400 placeholder-slate-300 dark:placeholder-slate-600">
                    </textarea>
                </div>

                {{-- Info: equipamento volta a disponível --}}
                <div class="flex items-start gap-2 rounded-xl bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800/40 px-3 py-2.5">
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                    <p class="text-xs text-emerald-700 dark:text-emerald-300 lato-regular">
                        O equipamento será automaticamente marcado como <span class="lato-bold">Disponível</span> ao encerrar.
                    </p>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 px-5 py-3.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/60">
                <button wire:click="$set('modalEncerrarManut', false)" type="button"
                    class="cursor-pointer px-4 py-2 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-600 transition">
                    Cancelar
                </button>
                <button wire:click="confirmarEncerrarManut" type="button"
                    class="cursor-pointer inline-flex items-center gap-1.5 px-5 py-2 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition shadow-sm disabled:opacity-60"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="confirmarEncerrarManut" class="flex items-center gap-1.5">
                        <x-lucide-flag class="w-4 h-4" /> Encerrar
                    </span>
                    <span wire:loading wire:target="confirmarEncerrarManut" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: CADASTRO / EDIÇÃO DE EQUIPAMENTO
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalEquipamento"
        x-data="{ sheetOpen: false }"
        x-init="$watch('$wire.modalEquipamento', v => { if(v) $nextTick(() => sheetOpen = true); else sheetOpen = false; })"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
        style="display:none" @click.self="sheetOpen = false; setTimeout(() => $wire.set('modalEquipamento', false), 300)">

        <div class="bg-white dark:bg-slate-800 shadow-2xl w-full sm:max-w-2xl sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
            :class="sheetOpen ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
            style="max-height: 92dvh;"
            @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            {{-- ── Header ──────────────────────────────────────────────── --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                        <x-lucide-package class="w-3.5 h-3.5 text-white" />
                    </span>
                    {{ $modoEquipamento === 'create' ? 'Novo Ativo' : 'Editar Ativo' }}
                </h3>
                <button @click="sheetOpen = false; setTimeout(() => $wire.set('modalEquipamento', false), 300)" type="button"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- ── Body ────────────────────────────────────────────────── --}}
            <div class="overflow-y-auto flex-1 p-5 space-y-6">

                {{-- Seção 1: Identificação --}}
                <div>
                    <p
                        class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <x-lucide-tag class="w-3.5 h-3.5" /> Identificação
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nome do
                                ativo <span class="text-red-400">*</span></label>
                            <input wire:model="eqNome" placeholder="Ex: Notebook Dell Latitude 5510..."
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                            @error('eqNome')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        {{-- Categoria: grid de ícone-cards --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Categoria
                                <span class="text-red-400">*</span></label>
                            @php
                                $catOpts = [
                                    'notebook' => ['Notebook', 'laptop-2'],
                                    'desktop' => ['Desktop', 'monitor'],
                                    'monitor' => ['Monitor', 'monitor-dot'],
                                    'teclado' => ['Teclado', 'keyboard'],
                                    'mouse' => ['Mouse', 'mouse-pointer-2'],
                                    'headset' => ['Headset', 'headphones'],
                                    'cracha' => ['Crachá', 'id-card'],
                                    'epi' => ['EPI', 'hard-hat'],
                                    'celular' => ['Celular', 'smartphone'],
                                    'cadeira' => ['Cadeira', 'sofa'],
                                    'outros' => ['Outros', 'package'],
                                ];
                            @endphp
                            <div class="grid grid-cols-4 sm:grid-cols-6 gap-1.5">
                                @foreach ($catOpts as $cv => [$cl, $ci])
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model="eqCategoria" value="{{ $cv }}"
                                            class="sr-only peer" />
                                        <div
                                            class="flex flex-col items-center gap-1 py-2 px-1 rounded-xl border text-center transition
                                            peer-checked:border-indigo-400 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300
                                            border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800">
                                            <x-dynamic-component :component="'lucide-' . $ci" class="w-4 h-4 shrink-0" />
                                            <span class="text-[9px] lato-bold leading-none">{{ $cl }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        {{-- Status: radio pills coloridos --}}
                        <div class="sm:col-span-2">
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Status <span
                                    class="text-red-400">*</span></label>
                            @php
                                $stOpts = [
                                    'disponivel' => [
                                        'Disponível',
                                        'check-circle',
                                        'peer-checked:border-green-400 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 peer-checked:text-green-700 dark:peer-checked:text-green-300',
                                    ],
                                    'em_uso' => [
                                        'Em Uso',
                                        'users',
                                        'peer-checked:border-blue-400 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:text-blue-700 dark:peer-checked:text-blue-300',
                                    ],
                                    'manutencao' => [
                                        'Manutenção',
                                        'wrench',
                                        'peer-checked:border-amber-400 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 peer-checked:text-amber-700 dark:peer-checked:text-amber-300',
                                    ],
                                    'descartado' => [
                                        'Descartado',
                                        'trash-2',
                                        'peer-checked:border-slate-400 peer-checked:bg-slate-100 dark:peer-checked:bg-slate-700 peer-checked:text-slate-700 dark:peer-checked:text-slate-300',
                                    ],
                                ];
                            @endphp
                            <div class="grid grid-cols-4 gap-1.5">
                                @foreach ($stOpts as $sv => [$sl, $si, $sc])
                                    <label class="cursor-pointer">
                                        <input type="radio" wire:model="eqStatus" value="{{ $sv }}"
                                            class="sr-only peer" />
                                        <div
                                            class="flex flex-col items-center gap-1 py-2 px-1 rounded-xl border text-center transition
                                            {{ $sc }}
                                            border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800">
                                            <x-dynamic-component :component="'lucide-' . $si" class="w-4 h-4 shrink-0" />
                                            <span class="text-[9px] lato-bold leading-none">{{ $sl }}</span>
                                        </div>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

                {{-- Seção 2: Detalhes técnicos --}}
                <div>
                    <p
                        class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <x-lucide-cpu class="w-3.5 h-3.5" /> Detalhes técnicos
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label
                                class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Marca</label>
                            <input wire:model="eqMarca" placeholder="Dell, Apple, Lenovo..."
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                        </div>
                        <div>
                            <label
                                class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Modelo</label>
                            <input wire:model="eqModelo" placeholder="Latitude 5510, MacBook Pro..."
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Número de
                                Série</label>
                            <input wire:model="eqNumeroSerie" placeholder="SN123456..."
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                            @error('eqNumeroSerie')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Código de
                                Patrimônio</label>
                            <input wire:model="eqCodPatrimonio" placeholder="PAT-001..."
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                            @error('eqCodPatrimonio')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

                {{-- Seção 3: Aquisição --}}
                <div>
                    <p
                        class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <x-lucide-receipt class="w-3.5 h-3.5" /> Aquisição
                    </p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Data de
                                Aquisição</label>
                            <input wire:model="eqDataAquisicao" type="date"
                                class="cursor-pointer w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular transition" />
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Garantia até</label>
                            <input wire:model="eqDataGarantia" type="date"
                                class="cursor-pointer w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular transition" />
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Local / Localização</label>
                            <input wire:model="eqLocal" placeholder="Ex: Sala TI, Andar 2, Filial SP..."
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Valor
                                (R$)</label>
                            <div class="relative">
                                <span
                                    class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 lato-regular">R$</span>
                                <input wire:model="eqValor" type="number" step="0.01" min="0"
                                    placeholder="0,00"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                          lato-regular placeholder-slate-400 transition" />
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Taxa de depreciação (% ao ano)</label>
                            <input wire:model="eqTaxaDepreciacao" type="number" step="0.1" min="0" max="100" placeholder="Ex: 20"
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                        </div>
                        <div class="sm:col-span-2">
                            <label
                                class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição</label>
                            <textarea wire:model="eqDescricao" rows="2" placeholder="Informações adicionais sobre o ativo..."
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                         bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                         focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                         lato-regular placeholder-slate-400 transition resize-none"></textarea>
                        </div>
                    </div>
                </div>

                <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

                {{-- Seção 4: Observações --}}
                <div>
                    <p
                        class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                        <x-lucide-message-square class="w-3.5 h-3.5" /> Observações internas
                    </p>
                    <textarea wire:model="eqObservacoes" rows="2" placeholder="Notas internas, defeitos conhecidos, histórico..."
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                 lato-regular placeholder-slate-400 transition resize-none"></textarea>
                </div>

            </div>

            {{-- ── Footer ──────────────────────────────────────────────── --}}
            <div class="flex items-center justify-between px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <p class="text-xs text-slate-400 lato-regular hidden sm:block">
                    <span class="text-red-400">*</span> Campos obrigatórios
                </p>
                <div class="flex items-center gap-3 w-full sm:w-auto">
                    <button @click="sheetOpen = false; setTimeout(() => $wire.set('modalEquipamento', false), 300)" type="button"
                        class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="salvarEquipamento" type="button"
                        class="cursor-pointer flex-1 sm:flex-none px-5 py-2.5 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition flex items-center justify-center gap-2 shadow-sm">

                        <span wire:loading wire:target="salvarEquipamento">
                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        </span>

                        <span wire:loading.remove wire:target="salvarEquipamento" class="flex items-center gap-1.5">
                            @if ($modoEquipamento === 'create')
                                <x-lucide-circle-check class="w-4 h-4" />
                                Confirmar
                            @else
                                <x-lucide-square-pen class="w-4 h-4" />
                                Salvar Alterações
                            @endif
                        </span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: ENTREGA / DEVOLUÇÃO
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalAtribuicao"
        x-data="{ sheetOpen: false }"
        x-init="$watch('$wire.modalAtribuicao', v => { if(v) $nextTick(() => sheetOpen = true); else sheetOpen = false; })"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
        style="display:none" @click.self="sheetOpen = false; setTimeout(() => $wire.set('modalAtribuicao', false), 300)">

        <div class="bg-white dark:bg-slate-800 shadow-2xl w-full sm:max-w-md sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
            :class="sheetOpen ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
            style="max-height: 92dvh;"
            @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0
                                 {{ $modoAtribuicao === 'entrega' ? 'bg-gradient-to-br from-teal-500 to-cyan-600' : 'bg-gradient-to-br from-amber-500 to-orange-500' }}">
                        @if ($modoAtribuicao === 'entrega')
                            <x-lucide-truck class="w-3.5 h-3.5 text-white" />
                        @else
                            <x-lucide-rotate-ccw class="w-3.5 h-3.5 text-white" />
                        @endif
                    </span>
                    {{ $modoAtribuicao === 'entrega' ? 'Registrar Entrega' : 'Registrar Devolução' }}
                </h3>
                <button @click="sheetOpen = false; setTimeout(() => $wire.set('modalAtribuicao', false), 300)" type="button"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="flex-1 overflow-y-auto p-5 space-y-5">
                {{-- Funcionário --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                        Funcionário <span class="text-red-400">*</span>
                    </label>
                    @if ($modoAtribuicao === 'entrega')
                        @php
                            $funcsJson = $this->funcionarios
                                ->map(fn($f) => ['id' => $f->id, 'name' => $f->name])
                                ->values();
                        @endphp
                        <div x-data="{
                            search: '',
                            open: false,
                            funcs: @js($funcsJson),
                            get filtered() {
                                const q = this.search.toLowerCase();
                                if (!q) return this.funcs;
                                return this.funcs.filter(f => f.name.toLowerCase().includes(q));
                            }
                        }" @click.outside="open = false" x-init="$watch('$wire.modalAtribuicao', v => { if (!v) { search = ''; } })">
                            <div class="relative">
                                <x-lucide-search
                                    class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="text" x-model="search" @focus="open = true"
                                    placeholder="Buscar funcionário..." autocomplete="off"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                          lato-regular placeholder-slate-400 transition" />
                                <div x-show="open" x-transition style="display:none"
                                    class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                    <template x-if="filtered.length === 0">
                                        <p class="text-xs text-slate-400 text-center py-3 lato-regular">Nenhum
                                            funcionário encontrado</p>
                                    </template>
                                    <template x-for="func in filtered" :key="func.id">
                                        <button type="button"
                                            @click="$wire.set('atFuncionarioId', func.id); search = func.name; open = false"
                                            class="cursor-pointer w-full text-left px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 lato-regular flex items-center gap-2.5 transition">
                                            <span
                                                class="w-6 h-6 rounded-full bg-gradient-to-br from-slate-400 to-slate-500 flex items-center justify-center text-[10px] lato-bold text-white shrink-0"
                                                x-text="func.name.charAt(0).toUpperCase()"></span>
                                            <span x-text="func.name"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        @error('atFuncionarioId')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    @else
                        @php $funcSel = $this->funcionarios->firstWhere('id', $atFuncionarioId); @endphp
                        <div
                            class="px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 text-sm text-slate-700 dark:text-slate-200 lato-regular border border-slate-200 dark:border-slate-700 flex items-center gap-2">
                            <x-lucide-user class="w-4 h-4 text-slate-400 shrink-0" />
                            {{ $funcSel?->name ?? '—' }}
                        </div>
                    @endif
                </div>

                {{-- Data --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                        {{ $modoAtribuicao === 'entrega' ? 'Data de entrega' : 'Data de devolução' }} <span
                            class="text-red-400">*</span>
                    </label>
                    <input wire:model="atDataEntrega" type="date"
                        class="cursor-pointer w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                              bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                              lato-regular transition" />
                    @error('atDataEntrega')
                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                {{-- Condição: radio cards coloridos --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Condição <span
                            class="text-red-400">*</span></label>
                    @php
                        $condOpts = [
                            'novo' => [
                                'Novo',
                                'star',
                                'peer-checked:border-green-400 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 peer-checked:text-green-700 dark:peer-checked:text-green-300',
                            ],
                            'bom' => [
                                'Bom',
                                'check-circle',
                                'peer-checked:border-blue-400 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:text-blue-700 dark:peer-checked:text-blue-300',
                            ],
                            'regular' => [
                                'Regular',
                                'alert-circle',
                                'peer-checked:border-amber-400 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 peer-checked:text-amber-700 dark:peer-checked:text-amber-300',
                            ],
                            'danificado' => [
                                'Danificado',
                                'alert-triangle',
                                'peer-checked:border-red-400 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:text-red-700 dark:peer-checked:text-red-300',
                            ],
                        ];
                    @endphp
                    <div class="grid grid-cols-4 gap-1.5">
                        @foreach ($condOpts as $cv => [$cl, $ci, $cc])
                            <label class="cursor-pointer">
                                <input type="radio" wire:model="atCondicaoEntrega" value="{{ $cv }}"
                                    class="sr-only peer" />
                                <div
                                    class="flex flex-col items-center gap-1 py-2 px-1 rounded-xl border text-center transition
                                    {{ $cc }}
                                    border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800">
                                    <x-dynamic-component :component="'lucide-' . $ci" class="w-4 h-4 shrink-0" />
                                    <span class="text-[9px] lato-bold leading-none">{{ $cl }}</span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </div>

                {{-- Observações --}}
                <div>
                    <label
                        class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Observações</label>
                    <textarea wire:model="atObservacoes" rows="3"
                        placeholder="Anote detalhes sobre a {{ $modoAtribuicao === 'entrega' ? 'entrega' : 'devolução' }} ou estado do equipamento..."
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                 lato-regular placeholder-slate-400 transition resize-none"></textarea>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button @click="sheetOpen = false; setTimeout(() => $wire.set('modalAtribuicao', false), 300)" type="button"
                    class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="salvarAtribuicao" type="button"
                    class="cursor-pointer flex-1 px-5 py-2.5 rounded-xl text-sm lato-bold text-white transition hover:opacity-90 flex items-center justify-center gap-2 shadow-sm
                           {{ $modoAtribuicao === 'entrega' ? 'bg-gradient-to-r from-teal-500 to-cyan-600' : 'bg-gradient-to-r from-amber-500 to-orange-500' }}"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarAtribuicao" class="flex items-center gap-1.5">
                        <x-lucide-check class="w-3.5 h-3.5" />
                    {{ $modoAtribuicao === 'entrega' ? 'Confirmar entrega' : 'Confirmar devolução' }}
                    </span>
                    <span wire:loading wire:target="salvarAtribuicao" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: MANUTENÇÃO
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalManutencao"
        x-data="{ sheetOpen: false }"
        x-init="$watch('$wire.modalManutencao', v => { if(v) $nextTick(() => sheetOpen = true); else sheetOpen = false; })"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
        style="display:none" @click.self="sheetOpen = false; setTimeout(() => $wire.set('modalManutencao', false), 300)">

        <div class="bg-white dark:bg-slate-800 shadow-2xl w-full sm:max-w-2xl sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
            :class="sheetOpen ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
            style="max-height: 92dvh;"
            @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center shrink-0">
                        <x-lucide-wrench class="w-3.5 h-3.5 text-white" />
                    </span>
                    {{ $modoManutencao === 'create' ? 'Nova Manutenção' : 'Editar Manutenção' }}
                </h3>
                <button @click="sheetOpen = false; setTimeout(() => $wire.set('modalManutencao', false), 300)" type="button"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            <div class="overflow-y-auto flex-1 p-5 space-y-5">
                {{-- Equipamento --}}
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Equipamento <span class="text-red-400">*</span></label>
                    @php $eqsJson = $this->equipamentos->map(fn($e) => ['id' => $e->id, 'nome' => $e->nome])->values(); @endphp
                    <div x-data="{
                        search: '',
                        open: false,
                        items: @js($eqsJson),
                        get filtered() {
                            const q = this.search.toLowerCase();
                            return q ? this.items.filter(i => i.nome.toLowerCase().includes(q)) : this.items;
                        }
                    }" @click.outside="open = false">
                        <div class="relative">
                            <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input type="text" x-model="search" @focus="open = true" placeholder="Buscar equipamento..." autocomplete="off"
                                class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular placeholder-slate-400 transition" />
                            <div x-show="open" x-transition style="display:none"
                                class="absolute z-50 w-full mt-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl max-h-48 overflow-y-auto">
                                <template x-for="item in filtered" :key="item.id">
                                    <button type="button"
                                        @click="$wire.setManutEquipamento(item.id); search = item.nome; open = false"
                                        class="cursor-pointer w-full text-left px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 hover:bg-amber-50 dark:hover:bg-amber-900/20 lato-regular transition"
                                        x-text="item.nome"></button>
                                </template>
                            </div>
                        </div>
                    </div>
                    @error('manutEquipamentoId')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Título <span class="text-red-400">*</span></label>
                        <input wire:model="mnTitulo" placeholder="Ex: Substituição de tela, limpeza preventiva..."
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular placeholder-slate-400 transition" />
                        @error('mnTitulo')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                    </div>

                    {{-- Tipo --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Tipo</label>
                        <div class="grid grid-cols-3 gap-1.5">
                            @foreach (['corretiva' => ['Corretiva', 'alert-circle'], 'preventiva' => ['Preventiva', 'shield-check'], 'calibracao' => ['Calibração', 'settings']] as $tv => [$tl, $ti])
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="mnTipo" value="{{ $tv }}" class="sr-only peer" />
                                    <div class="flex flex-col items-center gap-1 py-2 px-1 rounded-xl border text-center transition
                                        peer-checked:border-amber-400 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 peer-checked:text-amber-700
                                        border-slate-200 dark:border-slate-700 text-slate-500 hover:border-slate-300 hover:bg-slate-50">
                                        <x-dynamic-component :component="'lucide-' . $ti" class="w-4 h-4 shrink-0" />
                                        <span class="text-[9px] lato-bold leading-none">{{ $tl }}</span>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Status</label>
                        <div class="grid grid-cols-2 gap-1.5">
                            @foreach (['aberta' => 'Aberta', 'em_andamento' => 'Em andamento', 'concluida' => 'Concluída', 'cancelada' => 'Cancelada'] as $sv => $sl)
                                <label class="cursor-pointer">
                                    <input type="radio" wire:model="mnStatus" value="{{ $sv }}" class="sr-only peer" />
                                    <div class="flex items-center justify-center px-2 py-2 rounded-xl border text-center text-[10px] lato-bold transition
                                        peer-checked:border-amber-400 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 peer-checked:text-amber-700
                                        border-slate-200 dark:border-slate-700 text-slate-500 hover:border-slate-300 hover:bg-slate-50">
                                        {{ $sl }}
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Data de entrada <span class="text-red-400">*</span></label>
                        <input wire:model="mnDataEntrada" type="date"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Previsão de retorno</label>
                        <input wire:model="mnDataPrevisao" type="date"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Data de conclusão</label>
                        <input wire:model="mnDataConclusao" type="date"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Fornecedor / Técnico</label>
                        <input wire:model="mnFornecedor" placeholder="Nome do fornecedor ou técnico..."
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular placeholder-slate-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Custo estimado (R$)</label>
                        <input wire:model="mnCustoEstimado" type="number" step="0.01" min="0" placeholder="0,00"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular placeholder-slate-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Custo real (R$)</label>
                        <input wire:model="mnCustoReal" type="number" step="0.01" min="0" placeholder="0,00"
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular placeholder-slate-400 transition" />
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição do problema</label>
                        <textarea wire:model="mnDescricao" rows="2" placeholder="Descreva o problema ou o tipo de manutenção..."
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular placeholder-slate-400 transition resize-none"></textarea>
                    </div>
                    <div class="sm:col-span-2">
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Resolução / O que foi feito</label>
                        <textarea wire:model="mnResolucao" rows="2" placeholder="Descreva o que foi realizado..."
                            class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400/30 lato-regular placeholder-slate-400 transition resize-none"></textarea>
                    </div>
                </div>
            </div>

            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button @click="sheetOpen = false; setTimeout(() => $wire.set('modalManutencao', false), 300)" type="button"
                    class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
                <button wire:click="salvarManutencao" type="button"
                    class="cursor-pointer flex-1 px-5 py-2.5 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition flex items-center justify-center gap-2 shadow-sm disabled:opacity-60"
                    wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="salvarManutencao" class="flex items-center gap-1.5">
                        <x-lucide-check class="w-4 h-4" />
                    {{ $modoManutencao === 'create' ? 'Registrar' : 'Salvar alterações' }}
                    </span>
                    <span wire:loading wire:target="salvarManutencao" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                    </span>
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     MODAL: INICIAR INVENTÁRIO
═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="$wire.modalInventario"
        x-data="{ sheetOpen: false }"
        x-init="$watch('$wire.modalInventario', v => { if(v) $nextTick(() => sheetOpen = true); else sheetOpen = false; })"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
        style="display:none" @click.self="sheetOpen = false; setTimeout(() => $wire.set('modalInventario', false), 300)">

        <div class="bg-white dark:bg-slate-800 shadow-2xl w-full sm:max-w-md sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
            :class="sheetOpen ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
            @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                        <x-lucide-clipboard-list class="w-3.5 h-3.5 text-white" />
                    </span>
                    Iniciar inventário
                </h3>
                <button @click="sheetOpen = false; setTimeout(() => $wire.set('modalInventario', false), 300)" type="button"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
            <div class="p-5 space-y-4">
                <p class="text-xs text-slate-500 lato-regular">Um inventário cria uma lista de todos os ativos ativos para conferência física. Você poderá marcar cada item como encontrado, não encontrado ou com divergência.</p>
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Título do inventário <span class="text-red-400">*</span></label>
                    <input wire:model="invTitulo" placeholder="Ex: Inventário Anual 2026, Conferência Q2..."
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular placeholder-slate-400 transition" />
                    @error('invTitulo')<p class="text-xs text-red-500 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Observações</label>
                    <textarea wire:model="invObservacoes" rows="2" placeholder="Instruções ou notas para os conferentes..."
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular placeholder-slate-400 transition resize-none"></textarea>
                </div>
            </div>
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button @click="sheetOpen = false; setTimeout(() => $wire.set('modalInventario', false), 300)" type="button"
                    class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 rounded-xl text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
                <button wire:click="iniciarInventario" type="button"
                    class="cursor-pointer flex-1 px-5 py-2.5 rounded-xl text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:opacity-90 transition flex items-center justify-center gap-2 shadow-sm">
                    <x-lucide-play class="w-4 h-4" /> Iniciar
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════
     DRAWER: DETALHES DO EQUIPAMENTO
═══════════════════════════════════════════════════════════════════════ --}}
    {{-- Overlay --}}
    <div x-show="$wire.equipDrawerOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="$wire.closeEquipDrawer()"
        class="fixed inset-0 z-40 bg-black/50 backdrop-blur-sm"
        style="display:none"></div>

    {{-- Painel --}}
    <div x-show="$wire.equipDrawerOpen"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="translate-x-full"
        x-transition:enter-end="translate-x-0"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="translate-x-0"
        x-transition:leave-end="translate-x-full"
        class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] lg:w-[520px] bg-white dark:bg-slate-800 shadow-2xl flex flex-col"
        style="display:none" @click.stop>

        @php $ed = $this->equipDrawerData; @endphp

        @php
            $ed = $this->equipDrawerData;
            $catIcons = [
                'notebook' => 'laptop-2', 'desktop' => 'monitor', 'monitor' => 'monitor-dot',
                'teclado' => 'keyboard', 'mouse' => 'mouse-pointer-2', 'headset' => 'headphones',
                'cracha' => 'id-card', 'epi' => 'hard-hat', 'celular' => 'smartphone',
                'cadeira' => 'sofa', 'outros' => 'package',
            ];
        @endphp

        @if ($ed)
            @php
                $atribAtiva = $ed->atribuicoes->firstWhere(fn($a) => $a->tipo === 'entrega' && is_null($a->data_devolucao));
                $statusConfig = [
                    'disponivel' => ['dot' => 'bg-emerald-400', 'badge' => 'bg-emerald-400/15 text-emerald-300 ring-1 ring-emerald-400/30', 'label' => 'Disponível'],
                    'em_uso'     => ['dot' => 'bg-blue-400',    'badge' => 'bg-blue-400/15 text-blue-300 ring-1 ring-blue-400/30',       'label' => 'Em Uso'],
                    'manutencao' => ['dot' => 'bg-amber-400',   'badge' => 'bg-amber-400/15 text-amber-300 ring-1 ring-amber-400/30',    'label' => 'Manutenção'],
                    'descartado' => ['dot' => 'bg-slate-400',   'badge' => 'bg-slate-400/15 text-slate-400 ring-1 ring-slate-500/30',   'label' => 'Descartado'],
                ];
                $sc = $statusConfig[$ed->status] ?? $statusConfig['descartado'];
                $catIcon = $catIcons[$ed->categoria] ?? 'package';
            @endphp

            {{-- ── Header ──────────────────────────────────────────────────── --}}
            <div class="shrink-0 relative overflow-hidden" style="background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 50%, #6d28d9 100%);">
                {{-- Decoração de fundo --}}
                <div class="absolute inset-0 opacity-10">
                    <div class="absolute -top-6 -right-6 w-32 h-32 rounded-full bg-white"></div>
                    <div class="absolute bottom-0 left-1/3 w-20 h-20 rounded-full bg-white"></div>
                </div>

                <div class="relative px-5 pt-5 pb-4">
                    <div class="flex items-start justify-between gap-3 mb-4">
                        {{-- Ícone da categoria --}}
                        <div class="w-12 h-12 rounded-2xl bg-white/15 backdrop-blur flex items-center justify-center shrink-0 ring-1 ring-white/20">
                            <x-dynamic-component :component="'lucide-' . $catIcon" class="w-6 h-6 text-white" />
                        </div>
                        <button wire:click="closeEquipDrawer" type="button"
                            class="cursor-pointer p-1.5 rounded-xl text-white/50 hover:text-white hover:bg-white/10 transition shrink-0">
                            <x-lucide-x class="w-5 h-5" />
                        </button>
                    </div>

                    <div>
                        <h2 class="text-xl lato-black text-white leading-tight">{{ $ed->nome }}</h2>
                        @if ($ed->marca || $ed->modelo)
                            <p class="text-sm text-white/60 lato-regular mt-0.5">{{ trim($ed->marca . ' ' . $ed->modelo) }}</p>
                        @endif
                    </div>

                    {{-- Badges --}}
                    <div class="flex items-center gap-2 mt-3">
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] lato-bold {{ $sc['badge'] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                            {{ $sc['label'] }}
                        </span>
                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] lato-bold bg-white/10 text-white/80">
                            {{ $categoriaLabels[$ed->categoria] ?? $ed->categoria }}
                        </span>
                        @if ($ed->codigo_patrimonio)
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] lato-regular bg-white/10 text-white/60 font-mono">
                                {{ $ed->codigo_patrimonio }}
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- ── Body ─────────────────────────────────────────────────────── --}}
            <div class="flex-1 overflow-y-auto bg-slate-50 dark:bg-slate-900">

                {{-- Portador atual --}}
                @if ($atribAtiva)
                    <div class="mx-4 mt-4 rounded-2xl overflow-hidden border border-blue-200/60 dark:border-blue-700/40">
                        {{-- Faixa de título --}}
                        <div class="flex items-center gap-2 px-4 py-2.5 bg-blue-600 dark:bg-blue-700">
                            <x-lucide-user-check class="w-3.5 h-3.5 text-blue-100" />
                            <span class="text-[11px] lato-bold text-blue-100 uppercase tracking-wide">Com este funcionário</span>
                            <span class="ml-auto text-[10px] lato-regular text-blue-200">desde {{ $atribAtiva->data_entrega?->format('d/m/Y') }}</span>
                        </div>
                        {{-- Conteúdo --}}
                        <div class="bg-white dark:bg-slate-800 p-4">
                            <div class="flex items-center gap-3">
                                <div class="w-11 h-11 rounded-full bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center text-base lato-black text-white shrink-0 ring-2 ring-blue-200 dark:ring-blue-700">
                                    {{ mb_strtoupper(mb_substr($atribAtiva->funcionario->name, 0, 1)) }}
                                </div>
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $atribAtiva->funcionario->name }}</p>
                                    <p class="text-xs text-slate-500 lato-regular">
                                        {{ $atribAtiva->funcionario->department?->name ?? '' }}
                                        @if ($atribAtiva->funcionario->position)
                                            · {{ $atribAtiva->funcionario->position }}
                                        @endif
                                    </p>
                                </div>
                                <span class="shrink-0 px-2.5 py-1 rounded-lg text-[11px] lato-bold bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-300">
                                    {{ $condicaoLabel[$atribAtiva->condicao_entrega] ?? $atribAtiva->condicao_entrega }}
                                </span>
                            </div>
                            @if ($atribAtiva->responsavel || $atribAtiva->observacoes)
                                <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 space-y-1">
                                    @if ($atribAtiva->responsavel)
                                        <p class="text-[11px] text-slate-400 lato-regular">Registrado por <span class="text-slate-600 dark:text-slate-300 lato-bold">{{ $atribAtiva->responsavel->name }}</span></p>
                                    @endif
                                    @if ($atribAtiva->observacoes)
                                        <p class="text-[11px] text-slate-400 lato-regular italic">{{ $atribAtiva->observacoes }}</p>
                                    @endif
                                </div>
                            @endif
                        </div>
                    </div>
                @elseif ($ed->status === 'disponivel')
                    <div class="mx-4 mt-4 flex items-center gap-3 px-4 py-3 rounded-2xl bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200/60 dark:border-emerald-700/30">
                        <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                            <x-lucide-check-circle class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <p class="text-sm lato-bold text-emerald-700 dark:text-emerald-300">Disponível para entrega</p>
                            <p class="text-xs text-emerald-500/80 lato-regular">Nenhum funcionário com este equipamento</p>
                        </div>
                    </div>
                @endif

                {{-- Especificações técnicas --}}
                <div class="px-4 mt-5">
                    <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                        <x-lucide-cpu class="w-3 h-3" /> Especificações
                    </p>
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/70 dark:border-slate-700/60 divide-y divide-slate-100 dark:divide-slate-700/60 overflow-hidden">
                        @if ($ed->numero_serie)
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs text-slate-400 lato-regular">Número de Série</span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200 font-mono">{{ $ed->numero_serie }}</span>
                            </div>
                        @endif
                        @if ($ed->codigo_patrimonio)
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs text-slate-400 lato-regular">Patrimônio</span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200 font-mono">{{ $ed->codigo_patrimonio }}</span>
                            </div>
                        @endif
                        @if ($ed->marca)
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs text-slate-400 lato-regular">Marca</span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $ed->marca }}</span>
                            </div>
                        @endif
                        @if ($ed->modelo)
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs text-slate-400 lato-regular">Modelo</span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $ed->modelo }}</span>
                            </div>
                        @endif
                        @if ($ed->data_aquisicao)
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs text-slate-400 lato-regular">Adquirido em</span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $ed->data_aquisicao->format('d/m/Y') }}</span>
                            </div>
                        @endif
                        @if ($ed->valor)
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs text-slate-400 lato-regular">Valor</span>
                                <span class="text-xs lato-bold text-emerald-600 dark:text-emerald-400">R$ {{ number_format((float)$ed->valor, 2, ',', '.') }}</span>
                            </div>
                        @endif
                        @if ($ed->cadastradoPor)
                            <div class="flex items-center justify-between px-4 py-3">
                                <span class="text-xs text-slate-400 lato-regular">Cadastrado por</span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $ed->cadastradoPor->name }}</span>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Descrição + Observações --}}
                @if ($ed->descricao || $ed->observacoes)
                    <div class="px-4 mt-4 space-y-3">
                        @if ($ed->descricao)
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/70 dark:border-slate-700/60 p-4">
                                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                    <x-lucide-align-left class="w-3 h-3" /> Descrição
                                </p>
                                <p class="text-xs lato-regular text-slate-600 dark:text-slate-300 leading-relaxed">{{ $ed->descricao }}</p>
                            </div>
                        @endif
                        @if ($ed->observacoes)
                            <div class="bg-amber-50 dark:bg-amber-900/10 rounded-2xl border border-amber-200/60 dark:border-amber-700/30 p-4">
                                <p class="text-[10px] lato-bold text-amber-500 uppercase tracking-widest mb-2 flex items-center gap-1.5">
                                    <x-lucide-alert-circle class="w-3 h-3" /> Observações internas
                                </p>
                                <p class="text-xs lato-regular text-slate-600 dark:text-slate-300 leading-relaxed">{{ $ed->observacoes }}</p>
                            </div>
                        @endif
                    </div>
                @endif

                {{-- Histórico --}}
                <div class="px-4 mt-5 mb-4">
                    <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-1.5">
                        <x-lucide-history class="w-3 h-3" /> Histórico de movimentações
                        <span class="ml-auto text-[10px] lato-regular normal-case">{{ $ed->atribuicoes->count() }} registro(s)</span>
                    </p>

                    @if ($ed->atribuicoes->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200/70 dark:border-slate-700/60">
                            <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                                <x-lucide-history class="w-5 h-5 text-slate-300 dark:text-slate-500" />
                            </div>
                            <p class="text-sm lato-bold text-slate-400">Sem movimentações</p>
                            <p class="text-xs text-slate-300 dark:text-slate-600 lato-regular mt-0.5">As entregas aparecerão aqui</p>
                        </div>
                    @else
                        <div class="relative">
                            {{-- Linha vertical do timeline --}}
                            <div class="absolute left-[19px] top-3 bottom-3 w-px bg-slate-200 dark:bg-slate-700"></div>
                            <div class="space-y-3">
                                @foreach ($ed->atribuicoes as $mov)
                                    <div class="flex items-start gap-3">
                                        <div class="w-6 h-6 rounded bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0 mt-0.5">
                                            <x-lucide-tag class="w-3 h-3 text-slate-400" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs text-slate-600 dark:text-slate-300 lato-regular">{{ $mov->usuario?->name ?? '—' }}</p>
                                            <p class="text-[10px] text-slate-400 lato-regular">{{ $mov->created_at?->format('d/m/Y') }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
