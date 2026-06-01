        @if ($aba === 'people-analytics')
        @php $pa = $this->peopleAnalytics; $depListPa = $this->departamentos; @endphp
        <div class="p-4 md:p-6 space-y-6">

            {{-- ── CABEÇALHO ─────────────────────────────────────────── --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white flex items-center gap-2">
                        <x-lucide-users-round class="w-5 h-5 text-purple-500" />
                        People Analytics
                    </h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">Visão unificada · headcount, turnover, humor, eNPS e performance</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    @if($paAno || $paDept)
                    <button wire:click="paLimpar" type="button"
                            class="cursor-pointer flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 lato-regular transition">
                        <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar
                    </button>
                    @endif
                    @php
                        $selDeptPa = $paDept ? $depListPa->firstWhere('id', $paDept) : null;
                    @endphp
                    <div x-data="{
                            open: false,
                            search: '',
                            get filtered() {
                                if (!this.search) return {{ $depListPa->toJson() }};
                                const q = this.search.toLowerCase();
                                return {{ $depListPa->toJson() }}.filter(d => d.name.toLowerCase().includes(q));
                            }
                         }"
                         @click.outside="open = false"
                         class="relative">

                        {{-- Trigger --}}
                        <button type="button" @click="open = !open"
                                class="cursor-pointer flex items-center gap-2 pl-3 pr-2.5 py-2 text-sm lato-regular rounded-xl border bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 transition min-w-[200px]"
                                :class="open ? 'border-purple-400 ring-2 ring-purple-400/20' : 'border-gray-200 dark:border-slate-600 hover:border-purple-300'">
                            <x-lucide-building-2 class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                            <span class="flex-1 text-left truncate">
                                @if($selDeptPa)
                                    <span class="text-slate-800 dark:text-slate-100 lato-bold">{{ $selDeptPa->name }}</span>
                                @else
                                    <span class="text-slate-400">Todos os departamentos</span>
                                @endif
                            </span>
                            @if($paDept)
                                <span wire:click.stop="$set('paDept', '')" @click.stop="open = false; $wire.call('paAplicar')"
                                      class="p-0.5 rounded hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-400 hover:text-slate-600 transition">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/></svg>
                                </span>
                            @endif
                            <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                        </button>

                        {{-- Dropdown --}}
                        <div x-show="open"
                             x-transition:enter="transition ease-out duration-150"
                             x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                             x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                             x-transition:leave="transition ease-in duration-100"
                             x-transition:leave-start="opacity-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 z-50 mt-1.5 w-full min-w-[220px] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl overflow-hidden"
                             style="display:none">

                            {{-- Search --}}
                            <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                                <div class="flex items-center gap-2 px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-600 focus-within:border-purple-400 focus-within:ring-1 focus-within:ring-purple-400/20 transition">
                                    <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg>
                                    <input x-model="search" type="text" placeholder="Buscar departamento..."
                                           x-ref="searchInput"
                                           x-init="$watch('open', v => v && $nextTick(() => $refs.searchInput.focus()))"
                                           @keydown.escape="open = false"
                                           class="flex-1 bg-transparent text-xs text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none" />
                                </div>
                            </div>

                            <ul class="max-h-52 overflow-y-auto py-1">
                                {{-- Todos --}}
                                <li wire:click="$set('paDept', '')" @click="open = false; search = ''; $wire.call('paAplicar')"
                                    class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer text-sm lato-regular transition
                                           {{ !$paDept ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                    <x-lucide-building-2 class="w-3.5 h-3.5 shrink-0 {{ !$paDept ? 'text-purple-500' : 'text-slate-400' }}" />
                                    <span class="flex-1">Todos os departamentos</span>
                                    @if(!$paDept)
                                        <svg class="w-3.5 h-3.5 text-purple-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                    @endif
                                </li>
                                <template x-for="dep in filtered" :key="dep.id">
                                    <li @click="$wire.set('paDept', dep.id); $wire.call('paAplicar'); open = false; search = ''"
                                        class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer text-sm lato-regular transition"
                                        :class="String({{ $paDept ?: 'null' }}) === String(dep.id)
                                            ? 'bg-purple-50 dark:bg-purple-900/20 text-purple-700 dark:text-purple-300'
                                            : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                        <span class="w-2 h-2 rounded-full bg-purple-400 shrink-0"></span>
                                        <span class="flex-1 truncate" x-text="dep.name"></span>
                                        <template x-if="String({{ $paDept ?: 'null' }}) === String(dep.id)">
                                            <svg class="w-3.5 h-3.5 text-purple-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                        </template>
                                    </li>
                                </template>
                                <template x-if="filtered.length === 0">
                                    <li class="px-3 py-4 text-center text-xs text-slate-400">Nenhum departamento encontrado</li>
                                </template>
                            </ul>

                            <div class="px-3 py-1.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                <span class="text-xs text-slate-400" x-text="filtered.length + ' departamento(s)'"></span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── KPI CARDS ──────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3">
                @php
                    $paKpis = [
                        [
                            'label' => 'Headcount',
                            'value' => $pa['headcount'],
                            'sub'   => 'ativos',
                            'icon'  => 'users',
                            'grad'  => 'from-blue-500 to-indigo-600',
                            'text'  => 'text-blue-600 dark:text-blue-400',
                        ],
                        [
                            'label' => 'Turnover 12m',
                            'value' => $pa['turnoverAnual'] . '%',
                            'sub'   => $pa['totalSaidas'] . ' saídas',
                            'icon'  => 'log-out',
                            'grad'  => $pa['turnoverAnual'] > 10 ? 'from-rose-500 to-red-600' : ($pa['turnoverAnual'] > 5 ? 'from-amber-500 to-orange-500' : 'from-emerald-500 to-teal-600'),
                            'text'  => $pa['turnoverAnual'] > 10 ? 'text-rose-600 dark:text-rose-400' : ($pa['turnoverAnual'] > 5 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'),
                        ],
                        [
                            'label' => 'Humor',
                            'value' => ($pa['humorAtual'] ?: '—') . ($pa['humorAtual'] ? '/100' : ''),
                            'sub'   => $pa['humorAtual'] >= 75 ? 'Ótimo' : ($pa['humorAtual'] >= 60 ? 'Bom' : ($pa['humorAtual'] >= 45 ? 'Regular' : 'Atenção')),
                            'icon'  => $pa['humorAtual'] >= 70 ? 'smile' : ($pa['humorAtual'] >= 50 ? 'meh' : 'frown'),
                            'grad'  => $pa['humorAtual'] >= 70 ? 'from-emerald-500 to-teal-500' : ($pa['humorAtual'] >= 50 ? 'from-amber-500 to-yellow-500' : 'from-rose-500 to-red-500'),
                            'text'  => $pa['humorAtual'] >= 70 ? 'text-emerald-600 dark:text-emerald-400' : ($pa['humorAtual'] >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400'),
                        ],
                        [
                            'label' => 'eNPS',
                            'value' => $pa['npsAtual'] !== null ? ($pa['npsAtual'] >= 0 ? '+' : '') . $pa['npsAtual'] : '—',
                            'sub'   => $pa['npsAtual'] !== null ? ($pa['npsAtual'] >= 50 ? 'Excelente' : ($pa['npsAtual'] >= 30 ? 'Bom' : ($pa['npsAtual'] >= 0 ? 'Neutro' : 'Crítico'))) : 'Sem dados',
                            'icon'  => 'trending-up',
                            'grad'  => $pa['npsAtual'] !== null && $pa['npsAtual'] >= 30 ? 'from-blue-500 to-indigo-600' : ($pa['npsAtual'] !== null && $pa['npsAtual'] >= 0 ? 'from-amber-500 to-orange-500' : 'from-rose-500 to-red-600'),
                            'text'  => 'text-indigo-600 dark:text-indigo-400',
                        ],
                        [
                            'label' => 'Performance DPI',
                            'value' => $pa['dpiScore'] ? $pa['dpiScore'] . '%' : '—',
                            'sub'   => 'nível atual/meta',
                            'icon'  => 'bar-chart-3',
                            'grad'  => 'from-teal-500 to-cyan-600',
                            'text'  => 'text-teal-600 dark:text-teal-400',
                        ],
                        [
                            'label' => 'OKR Progress',
                            'value' => $pa['okrProgress'] !== null ? $pa['okrProgress'] . '%' : '—',
                            'sub'   => 'key results ativos',
                            'icon'  => 'target',
                            'grad'  => 'from-orange-500 to-amber-600',
                            'text'  => 'text-orange-600 dark:text-orange-400',
                        ],
                    ];
                @endphp
                @foreach($paKpis as $kpi)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $kpi['grad'] }} flex items-center justify-center mb-3">
                        <x-dynamic-component :component="'lucide-'.$kpi['icon']" class="w-4 h-4 text-white" />
                    </div>
                    <p class="text-xl lato-black {{ $kpi['text'] }}">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-slate-500 lato-regular mt-0.5">{{ $kpi['label'] }}</p>
                    <p class="text-[10px] text-slate-400 lato-regular">{{ $kpi['sub'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- ── LINHA 1: Headcount + Turnover ──────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Headcount trend --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <x-lucide-users class="w-4 h-4 text-blue-500" /> Evolução de Headcount
                        </h3>
                        @if(count($pa['projMonths']) > 0)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 lato-bold">
                            + projeção 3m
                        </span>
                        @endif
                    </div>
                    <div id="pa-hc-chart" wire:ignore
                         x-data
                         x-init="
                            const labels  = @js(array_keys($pa['headcountTrend']));
                            const hcData  = @js(array_values($pa['headcountTrend']));
                            @if(count($pa['projMonths']) > 0)
                            const projLbl = @js(array_column($pa['projMonths'], 'label'));
                            const projVal = @js(array_column($pa['projMonths'], 'value'));
                            const allLbl  = [...labels, ...projLbl];
                            const series  = [
                                { name: 'Headcount real', data: [...hcData, ...Array(projLbl.length).fill(null)] },
                                { name: 'Projeção', data: [...Array(hcData.length - 1).fill(null), hcData[hcData.length-1], ...projVal] },
                            ];
                            @else
                            const allLbl  = labels;
                            const series  = [{ name: 'Headcount', data: hcData }];
                            @endif
                            new ApexCharts($el, {
                                chart: { type: 'area', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series,
                                xaxis: { categories: allLbl, labels: { style: { fontSize: '10px' } } },
                                yaxis: { labels: { style: { fontSize: '10px' }, formatter: v => Math.round(v) } },
                                colors: ['#6366f1', '#a78bfa'],
                                fill: { type: ['gradient', 'pattern'], gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05 }, pattern: { style: 'slantedLines', width: 4, height: 4, strokeWidth: 1 } },
                                stroke: { curve: 'smooth', width: [2, 2], dashArray: [0, 5] },
                                dataLabels: { enabled: false },
                                grid: { borderColor: 'rgba(148,163,184,0.12)' },
                                legend: { position: 'top', fontSize: '11px' },
                                tooltip: { y: { formatter: v => v !== null ? Math.round(v) + ' pessoas' : '' } },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                </div>

                {{-- Turnover trend --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <x-lucide-log-out class="w-4 h-4 text-rose-500" /> Turnover Mensal (%)
                        </h3>
                        <span class="text-xs lato-regular text-slate-400">Meta ≤ 1% / mês</span>
                    </div>
                    <div id="pa-tv-chart" wire:ignore
                         x-data
                         x-init="
                            const labels = @js(array_keys($pa['turnoverTrend']));
                            const data   = @js(array_values($pa['turnoverTrend']));
                            new ApexCharts($el, {
                                chart: { type: 'bar', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series: [{ name: 'Turnover %', data }],
                                xaxis: { categories: labels, labels: { style: { fontSize: '10px' } } },
                                yaxis: { labels: { style: { fontSize: '10px' }, formatter: v => v + '%' } },
                                colors: data.map(v => v > 1 ? '#f43f5e' : v > 0.5 ? '#f59e0b' : '#10b981'),
                                plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
                                annotations: { yaxis: [{ y: 1, borderColor: '#f59e0b', borderWidth: 1, strokeDashArray: 4, label: { text: 'Meta 1%', style: { fontSize: '10px', color: '#f59e0b', background: 'transparent' } } }] },
                                dataLabels: { enabled: false },
                                grid: { borderColor: 'rgba(148,163,184,0.12)' },
                                tooltip: { y: { formatter: v => v + '%' } },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                    {{-- breakdown por tipo --}}
                    @if(!empty($pa['turnoverByTipo']))
                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 flex flex-wrap gap-2">
                        @foreach($pa['turnoverByTipo'] as $tipo => $qtd)
                        @php $tipoLabel = \App\Models\RhDesligamento::$tipoLabels[$tipo] ?? $tipo; @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] lato-regular">
                            {{ $tipoLabel }}: <strong>{{ $qtd }}</strong>
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>

            </div>

            {{-- ── LINHA 2: Humor + eNPS ───────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Humor trend --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 mb-4">
                        <x-lucide-smile class="w-4 h-4 text-amber-500" /> Score de Humor Mensal
                        <span class="ml-auto text-xs text-slate-400 lato-regular font-normal">0–100</span>
                    </h3>
                    @php
                        $humorHasData = collect($pa['humorTrend'])->whereNotNull()->isNotEmpty();
                    @endphp
                    @if($humorHasData)
                    <div id="pa-humor-chart" wire:ignore
                         x-data
                         x-init="
                            const labels = @js(array_keys($pa['humorTrend']));
                            const raw    = @js(array_values($pa['humorTrend']));
                            new ApexCharts($el, {
                                chart: { type: 'line', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series: [{ name: 'Score Humor', data: raw }],
                                xaxis: { categories: labels, labels: { style: { fontSize: '10px' } } },
                                yaxis: { min: 0, max: 100, tickAmount: 5, labels: { style: { fontSize: '10px' }, formatter: v => v + '/100' } },
                                colors: ['#f59e0b'],
                                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02 } },
                                stroke: { curve: 'smooth', width: 2 },
                                markers: { size: 4 },
                                dataLabels: { enabled: false },
                                annotations: {
                                    yaxis: [
                                        { y: 70, borderColor: '#10b981', strokeDashArray: 4, label: { text: 'Bom (70)', style: { fontSize: '10px', color: '#10b981', background: 'transparent' } } },
                                        { y: 50, borderColor: '#f59e0b', strokeDashArray: 4, label: { text: 'Regular (50)', style: { fontSize: '10px', color: '#f59e0b', background: 'transparent' } } },
                                    ]
                                },
                                grid: { borderColor: 'rgba(148,163,184,0.12)' },
                                tooltip: { y: { formatter: v => v !== null ? v + '/100' : 'Sem dados' } },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <x-lucide-meh class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Nenhum check-in de humor registrado no período</p>
                    </div>
                    @endif
                </div>

                {{-- eNPS trend --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 mb-4">
                        <x-lucide-trending-up class="w-4 h-4 text-indigo-500" /> eNPS Histórico
                        <span class="ml-auto text-xs text-slate-400 lato-regular font-normal">Employee Net Promoter Score</span>
                    </h3>
                    @php $npsHasData = collect($pa['npsTrend'])->whereNotNull()->isNotEmpty(); @endphp
                    @if($npsHasData)
                    <div id="pa-nps-chart" wire:ignore
                         x-data
                         x-init="
                            const labels = @js(array_keys($pa['npsTrend']));
                            const raw    = @js(array_values($pa['npsTrend']));
                            new ApexCharts($el, {
                                chart: { type: 'line', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series: [{ name: 'eNPS', data: raw }],
                                xaxis: { categories: labels, labels: { style: { fontSize: '10px' } } },
                                yaxis: { min: -100, max: 100, tickAmount: 5, labels: { style: { fontSize: '10px' }, formatter: v => (v >= 0 ? '+' : '') + v } },
                                colors: ['#8b5cf6'],
                                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02 } },
                                stroke: { curve: 'smooth', width: 2 },
                                markers: { size: 4 },
                                dataLabels: { enabled: false },
                                annotations: {
                                    yaxis: [
                                        { y: 50,  borderColor: '#10b981', strokeDashArray: 4, label: { text: 'Excelente (50)', style: { fontSize: '10px', color: '#10b981', background: 'transparent' } } },
                                        { y: 0,   borderColor: '#f43f5e', strokeDashArray: 3, label: { text: 'Zero', style: { fontSize: '10px', color: '#f43f5e', background: 'transparent' } } },
                                    ]
                                },
                                grid: { borderColor: 'rgba(148,163,184,0.12)' },
                                tooltip: { y: { formatter: v => v !== null ? (v >= 0 ? '+' : '') + v : 'Sem dados' } },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <x-lucide-trending-down class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Nenhuma pesquisa de eNPS no período</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- ── COMO INTERPRETAR ─────────────────────────────────── --}}
            <details class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none list-none">
                    <div class="flex items-center gap-2.5">
                        <div class="w-7 h-7 rounded-lg bg-purple-50 dark:bg-purple-900/30 flex items-center justify-center shrink-0">
                            <x-lucide-book-open class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                        </div>
                        <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Como interpretar o People Analytics</span>
                    </div>
                    <x-lucide-chevron-down class="w-4 h-4 text-slate-400 transition-transform duration-200 group-open:rotate-180 shrink-0" />
                </summary>

                <div class="px-5 pb-5 pt-1 border-t border-slate-100 dark:border-slate-700 space-y-4">

                    <p class="text-xs lato-regular text-slate-500 dark:text-slate-400 leading-relaxed">
                        Visão unificada de todas as métricas de pessoas — headcount, turnover, humor, eNPS e performance — em um único painel. Use os filtros de departamento e ano para segmentar os dados.
                    </p>

                    <div>
                        <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Indicadores</p>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                            @foreach([
                                ['icon'=>'users',       'grad'=>'from-blue-400 to-indigo-500',   'title'=>'Headcount',       'desc'=>'Total de colaboradores ativos no período selecionado.'],
                                ['icon'=>'log-out',     'grad'=>'from-rose-400 to-pink-500',     'title'=>'Turnover 12m',    'desc'=>'Percentual de saídas nos últimos 12 meses. Meta ideal: abaixo de 1% ao mês.'],
                                ['icon'=>'smile',       'grad'=>'from-amber-400 to-orange-500',  'title'=>'Humor',           'desc'=>'Score médio de bem-estar (0–100) com base nos check-ins diários dos colaboradores.'],
                                ['icon'=>'star',        'grad'=>'from-indigo-400 to-violet-500', 'title'=>'eNPS',            'desc'=>'Employee Net Promoter Score — mede o quanto os colaboradores recomendariam a empresa. Acima de +50 é excelente.'],
                                ['icon'=>'bar-chart-2', 'grad'=>'from-teal-400 to-cyan-500',     'title'=>'Performance DPI', 'desc'=>'Média de atingimento das metas do DPI. Exibido como nível atual / meta.'],
                                ['icon'=>'target',      'grad'=>'from-orange-400 to-amber-500',  'title'=>'OKR Progress',    'desc'=>'Progresso médio dos key results ativos nos OKRs da empresa ou departamento.'],
                            ] as $item)
                            <div class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                                <div class="w-7 h-7 rounded-lg bg-gradient-to-br {{ $item['grad'] }} flex items-center justify-center shrink-0">
                                    <x-dynamic-component :component="'lucide-'.$item['icon']" class="w-3.5 h-3.5 text-white" />
                                </div>
                                <div>
                                    <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $item['title'] }}</p>
                                    <p class="text-[11px] lato-regular text-slate-400 leading-snug mt-0.5">{{ $item['desc'] }}</p>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                        @foreach([
                            ['icon'=>'filter',      'clr'=>'text-purple-500', 'title'=>'Filtre por departamento', 'desc'=>'Compare métricas entre áreas para identificar onde a atenção é mais necessária.'],
                            ['icon'=>'trending-up', 'clr'=>'text-emerald-500','title'=>'Acompanhe tendências',    'desc'=>'Os gráficos de linha mostram a evolução ao longo do tempo — observe padrões e sazonalidades.'],
                            ['icon'=>'zap',         'clr'=>'text-amber-500',  'title'=>'Insights automáticos',    'desc'=>'O painel gera alertas e sugestões com base nos dados — veja a seção de Insights no final da página.'],
                        ] as $tip)
                        <div class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                            <div class="w-7 h-7 rounded-lg bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 flex items-center justify-center shrink-0">
                                <x-dynamic-component :component="'lucide-'.$tip['icon']" class="w-3.5 h-3.5 {{ $tip['clr'] }}" />
                            </div>
                            <div>
                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $tip['title'] }}</p>
                                <p class="text-[11px] lato-regular text-slate-400 leading-snug mt-0.5">{{ $tip['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                </div>
            </details>

        </div>
        @endif
