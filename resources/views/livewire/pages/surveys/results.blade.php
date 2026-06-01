<div class="p-4 sm:p-6 lg:p-8">

    {{-- ── ApexCharts CDN (sem defer para garantir carregamento síncrono) ── --}}
    @once
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endonce

    @php
        $survey  = $this->survey;
        $stats   = $this->stats;
        $daily   = $this->dailyResponses;
        $results = $this->questionResults;

        $statusClasses = match ($survey->status) {
            'ativa'     => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'rascunho'  => 'bg-amber-50 text-amber-700 border-amber-200',
            'encerrada' => 'bg-slate-100 text-slate-600 border-slate-200',
            default     => 'bg-slate-100 text-slate-600 border-slate-200',
        };
        $statusDot = match ($survey->status) {
            'ativa'     => 'bg-emerald-500 animate-pulse',
            'rascunho'  => 'bg-amber-500',
            'encerrada' => 'bg-slate-400',
            default     => 'bg-slate-400',
        };
    @endphp

    {{-- ───── Cabeçalho ──────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4">
        <div class="flex items-start gap-3">
            <a href="{{ route('pesquisas') }}"
               class="mt-0.5 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 dark:hover:text-white transition cursor-pointer shrink-0"
               title="Voltar para pesquisas">
                <x-lucide-arrow-left class="w-4 h-4" />
            </a>
            <div>
                <div class="flex flex-wrap items-center gap-2 mb-1.5">
                    <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white lato-black tracking-tight leading-tight">
                        {{ $survey->title }}
                    </h1>
                    <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold lato-bold px-2 py-0.5 rounded-full border {{ $statusClasses }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusDot }}"></span>
                        {{ $survey->status_label }}
                    </span>
                </div>
                <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">
                    Dashboard de Resultados
                    @if ($survey->creator)
                        · Criada por <span class="font-semibold text-slate-600 dark:text-slate-300">{{ $survey->creator->name }}</span>
                    @endif
                    @if ($survey->is_anonymous)
                        · <span class="inline-flex items-center gap-1"><x-lucide-eye-off class="w-3 h-3" /> Respostas anônimas</span>
                    @endif
                </p>
            </div>
        </div>

        {{-- Período + Exportações --}}
        <div class="flex flex-wrap items-center gap-2 shrink-0">
            <div class="flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400 lato-regular bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl px-3 py-3">
                <x-lucide-calendar class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                @if ($survey->start_date && $survey->end_date)
                    {{ $survey->start_date->format('d/m/Y') }} – {{ $survey->end_date->format('d/m/Y') }}
                @elseif ($survey->start_date)
                    A partir de {{ $survey->start_date->format('d/m/Y') }}
                @elseif ($survey->end_date)
                    Até {{ $survey->end_date->format('d/m/Y') }}
                @else
                    Sem período definido
                @endif
            </div>

            {{-- Dropdown de exportação --}}
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button type="button" @click="open = !open"
                        class="flex items-center gap-1.5 px-3 py-3 text-xs lato-bold rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white shadow-sm transition cursor-pointer">
                    <x-lucide-download class="w-3.5 h-3.5" />
                    Exportar
                    <x-lucide-chevron-down class="w-3 h-3 transition-transform duration-150" x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" x-cloak
                     class="absolute right-0 mt-1.5 z-30 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg overflow-hidden">
                    <a href="{{ route('pesquisas.export.pdf', $survey->id) }}" target="_blank"
                       class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition lato-regular">
                        <x-lucide-file-text class="w-4 h-4 text-rose-500" />
                        Exportar como PDF
                    </a>
                    <a href="{{ route('pesquisas.export.excel', $survey->id) }}"
                       class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition lato-regular border-t border-slate-100 dark:border-slate-700">
                        <x-lucide-table-2 class="w-4 h-4 text-emerald-500" />
                        Exportar como Excel
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- ───── Cards de estatísticas ───────────────────────────────────── --}}
    <div class="mt-8 grid grid-cols-2 lg:grid-cols-4 gap-3 sm:gap-4">

        <div class="bg-[#F1F5F9] dark:bg-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-slate-400 lato-regular">Respostas</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $stats['total_responses'] }}</p>
                <p class="text-[11px] text-slate-400 lato-regular mt-0.5">de {{ $stats['total_invited'] }} convidados</p>
            </div>
            <div class="text-slate-400">
                <x-lucide-message-square class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>

        <div class="bg-[#D1FAE5] dark:bg-emerald-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div class="flex-1 min-w-0 pr-2">
                <p class="text-xs sm:text-sm text-gray-600 dark:text-emerald-200 lato-regular">Taxa de resposta</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $stats['response_rate'] }}%</p>
                <div class="mt-1.5 w-full bg-emerald-200 dark:bg-emerald-800 rounded-full h-1.5">
                    <div class="bg-emerald-500 h-1.5 rounded-full" style="width: {{ min($stats['response_rate'], 100) }}%"></div>
                </div>
            </div>
            <div class="text-emerald-500 shrink-0">
                <x-lucide-percent class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>

        <div class="bg-[#E0E7FF] dark:bg-indigo-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-indigo-200 lato-regular">Perguntas</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $stats['total_questions'] }}</p>
                <p class="text-[11px] text-indigo-400 lato-regular mt-0.5">no questionário</p>
            </div>
            <div class="text-indigo-500">
                <x-lucide-list-checks class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>

        <div class="bg-[#FEF3C7] dark:bg-amber-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs sm:text-sm text-gray-600 dark:text-amber-200 lato-regular">Público-alvo</p>
                @if ($survey->target_audience === 'todos')
                    <p class="text-sm sm:text-base font-bold text-gray-900 dark:text-white mt-1 lato-black leading-tight">Todos</p>
                    <p class="text-[11px] text-amber-500 lato-regular mt-0.5">colaboradores</p>
                @else
                    @php $depCount = count($survey->target_department_ids ?? []); @endphp
                    <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $depCount }}</p>
                    <p class="text-[11px] text-amber-500 lato-regular mt-0.5">{{ $depCount === 1 ? 'departamento' : 'departamentos' }}</p>
                @endif
            </div>
            <div class="text-amber-500">
                <x-lucide-users class="w-8 h-8 sm:w-10 sm:h-10" />
            </div>
        </div>
    </div>

    {{-- ───── Evolução temporal ────────────────────────────────────────── --}}
    @if (count($daily) >= 1)
        <div class="mt-6 bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl p-5 sm:p-6">

            <div class="flex items-center justify-between mb-1">
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/20 flex items-center justify-center">
                        <x-lucide-trending-up class="w-4 h-4 text-emerald-500" />
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-slate-700 dark:text-white lato-black">Evolução das Respostas</h3>
                        <p class="text-[11px] text-slate-400 lato-regular">Respostas recebidas por dia</p>
                    </div>
                </div>
                <span class="text-xs text-slate-400 lato-regular">{{ count($daily) }} {{ count($daily) === 1 ? 'dia' : 'dias' }} com respostas</span>
            </div>

            <div wire:ignore id="apex-daily-wrap">
                <div id="apex-daily-{{ $survey->id }}"></div>
            </div>
        </div>

        <script>
        (function () {
            var rawLabels = @json(array_keys($daily));
            var data      = @json(array_values($daily));
            var isDark    = document.documentElement.classList.contains('dark');

            var formatted = rawLabels.map(function (d) {
                var parts = d.split('-');
                return parts[2] + '/' + parts[1];
            });

            function renderDaily() {
                if (typeof ApexCharts === 'undefined') { setTimeout(renderDaily, 80); return; }
                var el = document.getElementById('apex-daily-{{ $survey->id }}');
                if (!el || el.dataset.apex) return;
                el.dataset.apex = '1';

                new ApexCharts(el, {
                    series: [{ name: 'Respostas', data: data }],
                    chart: {
                        type: 'area',
                        height: 200,
                        toolbar: { show: false },
                        zoom: { enabled: false },
                        fontFamily: 'inherit',
                        animations: { enabled: true, speed: 600 },
                    },
                    colors: ['#10b981'],
                    fill: {
                        type: 'gradient',
                        gradient: { shadeIntensity: 1, opacityFrom: 0.25, opacityTo: 0.03, stops: [0, 90] },
                    },
                    stroke: { curve: 'smooth', width: 2.5 },
                    markers: { size: 4, colors: ['#10b981'], strokeWidth: 0, hover: { size: 6 } },
                    xaxis: {
                        categories: formatted,
                        labels: { style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' } },
                        axisBorder: { show: false },
                        axisTicks: { show: false },
                    },
                    yaxis: {
                        min: 0,
                        tickAmount: 3,
                        forceNiceScale: true,
                        labels: {
                            style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' },
                            formatter: function (v) { return Math.floor(v); },
                        },
                    },
                    grid: {
                        borderColor: isDark ? '#1e293b' : '#f1f5f9',
                        strokeDashArray: 4,
                        padding: { left: 4, right: 4 },
                    },
                    tooltip: {
                        theme: isDark ? 'dark' : 'light',
                        y: { formatter: function (v) { return v + (v === 1 ? ' resposta' : ' respostas'); } },
                        x: {
                            formatter: function (val, opts) {
                                var parts = rawLabels[opts.dataPointIndex].split('-');
                                return parts[2] + '/' + parts[1] + '/' + parts[0];
                            },
                        },
                    },
                    dataLabels: { enabled: false },
                }).render();
            }

            renderDaily();
        }());
        </script>
    @endif

    {{-- ───── Sem respostas ainda ─────────────────────────────────────── --}}
    @if ($stats['total_responses'] === 0)
        <div class="mt-6 bg-white dark:bg-slate-800 border border-dashed border-slate-200 dark:border-slate-700 rounded-2xl p-10 flex flex-col items-center text-center">
            <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                <x-lucide-inbox class="w-7 h-7 text-slate-400" />
            </div>
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Nenhuma resposta ainda</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-1 max-w-sm">
                Assim que os colaboradores responderem à pesquisa, os resultados aparecerão aqui.
            </p>
        </div>
    @endif

    {{-- ───── Resultados por pergunta ─────────────────────────────────── --}}
    @if ($stats['total_responses'] > 0 && count($results) > 0)
        <div class="mt-6 space-y-4">
            <h2 class="text-base font-bold text-slate-700 dark:text-white lato-black">Análise por Pergunta</h2>

            @foreach ($results as $i => $qr)
                <div class="bg-white dark:bg-slate-800 border border-slate-100 dark:border-slate-700 rounded-2xl overflow-hidden">

                    {{-- Header da pergunta --}}
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-start gap-3">
                        <div class="shrink-0 w-7 h-7 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center text-[11px] font-bold lato-black text-slate-500 dark:text-slate-300 mt-0.5">
                            {{ $i + 1 }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-bold text-slate-800 dark:text-white lato-black leading-snug">
                                {{ $qr['question'] }}
                            </p>
                            <div class="flex items-center gap-2 mt-1">
                                @if ($qr['type'] === 'escala')
                                    <span class="inline-flex items-center gap-1 text-[10px] lato-bold px-1.5 py-0.5 rounded-md bg-indigo-50 text-indigo-600 dark:bg-indigo-900/20 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700/40">
                                        <x-lucide-bar-chart-2 class="w-3 h-3" /> Escala 0–10
                                    </span>
                                @elseif ($qr['type'] === 'multipla_escolha')
                                    <span class="inline-flex items-center gap-1 text-[10px] lato-bold px-1.5 py-0.5 rounded-md bg-violet-50 text-indigo-600 dark:bg-violet-900/20 dark:text-indigo-400 border border-violet-200 dark:border-violet-700/40">
                                        <x-lucide-list-checks class="w-3 h-3" /> Múltipla escolha
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 text-[10px] lato-bold px-1.5 py-0.5 rounded-md bg-teal-50 text-teal-600 dark:bg-teal-900/20 dark:text-teal-400 border border-teal-200 dark:border-teal-700/40">
                                        <x-lucide-align-left class="w-3 h-3" /> Texto livre
                                    </span>
                                @endif
                                <span class="text-[11px] text-slate-400 lato-regular">
                                    {{ $qr['count'] }} {{ $qr['count'] === 1 ? 'resposta' : 'respostas' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- ─── Escala 0–10 ─────────────────────────────────── --}}
                    @if ($qr['type'] === 'escala' && $qr['count'] > 0)
                        <div class="p-5 space-y-5">

                            {{-- Métricas rápidas --}}
                            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                                <div class="text-center p-3 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                                    <p class="text-[11px] text-slate-500 lato-regular">Média</p>
                                    <p class="text-2xl font-bold lato-black mt-0.5 {{ $qr['average'] >= 7 ? 'text-emerald-600' : ($qr['average'] >= 5 ? 'text-amber-500' : 'text-red-500') }}">
                                        {{ $qr['average'] }}
                                    </p>
                                </div>
                                <div class="text-center p-3 rounded-xl bg-emerald-50 dark:bg-emerald-900/20">
                                    <p class="text-[11px] text-emerald-600 lato-regular">Promotores</p>
                                    <p class="text-2xl font-bold lato-black text-emerald-600 dark:text-emerald-400 mt-0.5">{{ $qr['promoters'] }}</p>
                                    <p class="text-[10px] text-emerald-500 lato-regular">nota 9–10</p>
                                </div>
                                <div class="text-center p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20">
                                    <p class="text-[11px] text-amber-600 lato-regular">Neutros</p>
                                    <p class="text-2xl font-bold lato-black text-amber-500 mt-0.5">{{ $qr['passives'] }}</p>
                                    <p class="text-[10px] text-amber-500 lato-regular">nota 7–8</p>
                                </div>
                                <div class="text-center p-3 rounded-xl bg-red-50 dark:bg-red-900/20">
                                    <p class="text-[11px] text-red-500 lato-regular">Detratores</p>
                                    <p class="text-2xl font-bold lato-black text-red-500 mt-0.5">{{ $qr['detractors'] }}</p>
                                    <p class="text-[10px] text-red-400 lato-regular">nota 0–6</p>
                                </div>
                            </div>

                            {{-- Gráfico de barras (distribuição 0–10) --}}
                            <div wire:ignore>
                                <div id="apex-scale-{{ $qr['id'] }}"></div>
                            </div>
                            <script>
                            (function () {
                                var dist  = @json($qr['distribution']);
                                var isDark = document.documentElement.classList.contains('dark');

                                function renderScale() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(renderScale, 80); return; }
                                    var el = document.getElementById('apex-scale-{{ $qr['id'] }}');
                                    if (!el || el.dataset.apex) return;
                                    el.dataset.apex = '1';

                                    new ApexCharts(el, {
                                        series: [{ name: 'Respostas', data: dist }],
                                        chart: {
                                            type: 'bar',
                                            height: 200,
                                            toolbar: { show: false },
                                            fontFamily: 'inherit',
                                            animations: { enabled: true, speed: 600 },
                                        },
                                        plotOptions: {
                                            bar: {
                                                distributed: true,
                                                borderRadius: 6,
                                                columnWidth: '65%',
                                                dataLabels: { position: 'top' },
                                            },
                                        },
                                        colors: [
                                            '#ef4444','#f97316','#f97316','#f97316','#eab308',
                                            '#eab308','#eab308','#facc15','#a3e635','#4ade80','#22c55e',
                                        ],
                                        dataLabels: {
                                            enabled: true,
                                            formatter: function (v) { return v > 0 ? v : ''; },
                                            offsetY: -6,
                                            style: {
                                                fontSize: '11px',
                                                colors: [isDark ? '#94a3b8' : '#475569'],
                                                fontWeight: '600',
                                            },
                                        },
                                        xaxis: {
                                            categories: ['0','1','2','3','4','5','6','7','8','9','10'],
                                            labels: {
                                                style: {
                                                    colors: Array(11).fill(isDark ? '#94a3b8' : '#64748b'),
                                                    fontSize: '12px',
                                                    fontWeight: '600',
                                                },
                                            },
                                            axisBorder: { show: false },
                                            axisTicks: { show: false },
                                        },
                                        yaxis: {
                                            min: 0,
                                            tickAmount: 3,
                                            forceNiceScale: true,
                                            labels: {
                                                style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' },
                                                formatter: function (v) { return Math.floor(v); },
                                            },
                                        },
                                        grid: {
                                            borderColor: isDark ? '#1e293b' : '#f8fafc',
                                            strokeDashArray: 4,
                                            padding: { left: 4, right: 4 },
                                        },
                                        legend: { show: false },
                                        tooltip: {
                                            theme: isDark ? 'dark' : 'light',
                                            y: { formatter: function (v) { return v + (v === 1 ? ' resposta' : ' respostas'); } },
                                            x: { formatter: function (v) { return 'Nota ' + v; } },
                                        },
                                    }).render();
                                }

                                renderScale();
                            }());
                            </script>

                            {{-- NPS --}}
                            @if ($qr['nps'] !== null && $qr['total'] > 0)
                                @php
                                    $pctPromotors  = $qr['total'] > 0 ? round(($qr['promoters']  / $qr['total']) * 100, 1) : 0;
                                    $pctPassives   = $qr['total'] > 0 ? round(($qr['passives']   / $qr['total']) * 100, 1) : 0;
                                    $pctDetractors = $qr['total'] > 0 ? round(($qr['detractors'] / $qr['total']) * 100, 1) : 0;
                                    $npsColor = $qr['nps'] >= 50 ? 'text-emerald-600' : ($qr['nps'] >= 0 ? 'text-amber-500' : 'text-red-500');
                                    $npsBg    = $qr['nps'] >= 50 ? 'bg-emerald-50 border-emerald-200 dark:bg-emerald-900/10 dark:border-emerald-800/40'
                                                                 : ($qr['nps'] >= 0 ? 'bg-amber-50 border-amber-200 dark:bg-amber-900/10 dark:border-amber-800/40'
                                                                                    : 'bg-red-50 border-red-200 dark:bg-red-900/10 dark:border-red-800/40');
                                    $npsLabel = $qr['nps'] >= 75 ? 'Excelente' : ($qr['nps'] >= 50 ? 'Muito bom' : ($qr['nps'] >= 0 ? 'Bom' : ($qr['nps'] >= -25 ? 'Atenção' : 'Crítico')));
                                @endphp
                                <div class="rounded-xl border {{ $npsBg }} p-4">
                                    <div class="flex items-center justify-between mb-3">
                                        <div>
                                            <p class="text-xs font-bold lato-black text-slate-600 dark:text-slate-300 uppercase tracking-wide">NPS Calculado</p>
                                            <p class="text-[11px] text-slate-400 lato-regular">Net Promoter Score</p>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-3xl font-bold lato-black {{ $npsColor }}">{{ $qr['nps'] > 0 ? '+' : '' }}{{ $qr['nps'] }}</span>
                                            <p class="text-[11px] lato-bold {{ $npsColor }}">{{ $npsLabel }}</p>
                                        </div>
                                    </div>
                                    <div class="flex rounded-full overflow-hidden h-3 gap-px">
                                        @if ($pctDetractors > 0)
                                            <div class="bg-red-400" style="width: {{ $pctDetractors }}%"></div>
                                        @endif
                                        @if ($pctPassives > 0)
                                            <div class="bg-amber-400" style="width: {{ $pctPassives }}%"></div>
                                        @endif
                                        @if ($pctPromotors > 0)
                                            <div class="bg-emerald-400" style="width: {{ $pctPromotors }}%"></div>
                                        @endif
                                    </div>
                                    <div class="flex items-center justify-between mt-2">
                                        <span class="flex items-center gap-1 text-[10px] text-red-500 lato-regular">
                                            <span class="w-2 h-2 rounded-full bg-red-400 inline-block"></span>
                                            Detratores {{ $pctDetractors }}%
                                        </span>
                                        <span class="flex items-center gap-1 text-[10px] text-amber-500 lato-regular">
                                            <span class="w-2 h-2 rounded-full bg-amber-400 inline-block"></span>
                                            Neutros {{ $pctPassives }}%
                                        </span>
                                        <span class="flex items-center gap-1 text-[10px] text-emerald-600 lato-regular">
                                            <span class="w-2 h-2 rounded-full bg-emerald-400 inline-block"></span>
                                            Promotores {{ $pctPromotors }}%
                                        </span>
                                    </div>
                                </div>
                            @endif
                        </div>

                    {{-- ─── Múltipla escolha ────────────────────────────── --}}
                    @elseif ($qr['type'] === 'multipla_escolha' && $qr['count'] > 0)
                        <div class="p-5">
                            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-center">

                                {{-- Legenda com barras CSS --}}
                                <div class="space-y-3">
                                    @php
                                        $mcColors = ['#10b981','#06b6d4','#8b5cf6','#f59e0b','#ef4444','#6366f1','#ec4899','#84cc16'];
                                        $ci = 0;
                                    @endphp
                                    @foreach ($qr['counts'] as $option => $count)
                                        @php $pct = $qr['percentages'][$option] ?? 0; @endphp
                                        <div>
                                            <div class="flex items-center justify-between mb-1">
                                                <span class="text-xs lato-regular text-slate-700 dark:text-slate-300 leading-tight flex-1 pr-2 line-clamp-2">{{ $option }}</span>
                                                <div class="flex items-center gap-2 shrink-0">
                                                    <span class="text-xs lato-bold text-slate-600 dark:text-slate-300">{{ $count }}</span>
                                                    <span class="text-xs lato-regular text-slate-400 w-10 text-right">{{ $pct }}%</span>
                                                </div>
                                            </div>
                                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                                                <div class="h-2 rounded-full"
                                                     style="width: {{ $pct }}%; background-color: {{ $mcColors[$ci % count($mcColors)] }};"></div>
                                            </div>
                                        </div>
                                        @php $ci++; @endphp
                                    @endforeach
                                </div>

                                {{-- Donut ApexCharts --}}
                                <div wire:ignore>
                                    <div id="apex-mc-{{ $qr['id'] }}"></div>
                                </div>
                                <script>
                                (function () {
                                    var counts = @json(array_values($qr['counts']));
                                    var labels = @json(array_keys($qr['counts']));
                                    var colors = ['#10b981','#06b6d4','#8b5cf6','#f59e0b','#ef4444','#6366f1','#ec4899','#84cc16'];
                                    var total  = counts.reduce(function (a, b) { return a + b; }, 0);
                                    var isDark = document.documentElement.classList.contains('dark');

                                    function renderDonut() {
                                        if (typeof ApexCharts === 'undefined') { setTimeout(renderDonut, 80); return; }
                                        var el = document.getElementById('apex-mc-{{ $qr['id'] }}');
                                        if (!el || el.dataset.apex) return;
                                        el.dataset.apex = '1';

                                        new ApexCharts(el, {
                                            series: counts,
                                            chart: {
                                                type: 'donut',
                                                height: 220,
                                                toolbar: { show: false },
                                                fontFamily: 'inherit',
                                                animations: { enabled: true, speed: 600 },
                                            },
                                            labels: labels,
                                            colors: colors.slice(0, counts.length),
                                            plotOptions: {
                                                pie: {
                                                    donut: {
                                                        size: '65%',
                                                        labels: {
                                                            show: true,
                                                            total: {
                                                                show: true,
                                                                label: 'Total',
                                                                fontSize: '11px',
                                                                color: isDark ? '#94a3b8' : '#64748b',
                                                                formatter: function () { return total; },
                                                            },
                                                            value: {
                                                                fontSize: '20px',
                                                                fontWeight: '700',
                                                                color: isDark ? '#f1f5f9' : '#0f172a',
                                                            },
                                                        },
                                                    },
                                                },
                                            },
                                            dataLabels: { enabled: false },
                                            legend: { show: false },
                                            stroke: { width: 0 },
                                            tooltip: {
                                                theme: isDark ? 'dark' : 'light',
                                                y: {
                                                    formatter: function (v) {
                                                        var pct = total > 0 ? Math.round((v / total) * 100) : 0;
                                                        return v + ' (' + pct + '%)';
                                                    },
                                                },
                                            },
                                        }).render();
                                    }

                                    renderDonut();
                                }());
                                </script>
                            </div>
                        </div>

                    {{-- ─── Texto livre ──────────────────────────────────── --}}
                    @elseif ($qr['type'] === 'texto_livre' && $qr['count'] > 0)
                        <div class="p-5 space-y-4">

                            {{-- Nuvem de palavras CSS --}}
                            @if (! empty($qr['word_freq']))
                                @php
                                    $maxFreq   = max($qr['word_freq']);
                                    $minFreq   = min($qr['word_freq']);
                                    $freqRange = max($maxFreq - $minFreq, 1);
                                    $wColors   = ['#10b981','#059669','#0d9488','#0891b2','#0ea5e9','#6366f1','#8b5cf6','#14b8a6'];
                                    $wci       = 0;
                                @endphp
                                <div>
                                    <p class="text-xs font-bold text-slate-500 dark:text-slate-400 lato-black uppercase tracking-wide mb-2">Palavras mais frequentes</p>
                                    <div class="flex flex-wrap gap-x-3 gap-y-2 p-4 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                        @foreach ($qr['word_freq'] as $word => $freq)
                                            @php
                                                $ratio  = ($freq - $minFreq) / $freqRange;
                                                $size   = round(11 + $ratio * 20);
                                                $color  = $wColors[$wci % count($wColors)];
                                                $opac   = round(0.55 + $ratio * 0.45, 2);
                                                $wci++;
                                            @endphp
                                            <span class="lato-bold cursor-default select-none transition-transform hover:scale-110 inline-block"
                                                  style="font-size: {{ $size }}px; color: {{ $color }}; opacity: {{ $opac }};"
                                                  title="{{ $freq }} {{ $freq === 1 ? 'ocorrência' : 'ocorrências' }}">
                                                {{ $word }}
                                            </span>
                                        @endforeach
                                    </div>
                                </div>
                            @endif

                            {{-- Lista de respostas --}}
                            <div x-data="{ showAll: false }">
                                <p class="text-xs font-bold text-slate-500 dark:text-slate-400 lato-black uppercase tracking-wide mb-2">
                                    Respostas individuais <span class="font-normal normal-case">({{ count($qr['texts']) }})</span>
                                </p>
                                <div class="space-y-2">
                                    @foreach ($qr['texts'] as $ti => $text)
                                        <div @if ($ti >= 3) x-show="showAll" style="display:none;" @endif>
                                            <div class="flex gap-2.5 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-700">
                                                <div class="shrink-0 w-6 h-6 rounded-full bg-slate-200 dark:bg-slate-600 flex items-center justify-center text-[10px] lato-black text-slate-500 dark:text-slate-300 mt-0.5">
                                                    {{ $ti + 1 }}
                                                </div>
                                                <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed">{{ $text }}</p>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                                @if (count($qr['texts']) > 3)
                                    <button type="button" @click="showAll = !showAll"
                                            class="mt-2 text-xs lato-bold text-emerald-600 dark:text-emerald-400 hover:text-emerald-700 transition cursor-pointer flex items-center gap-1">
                                        <x-lucide-chevron-down class="w-3.5 h-3.5 transition-transform duration-200" x-bind:class="showAll ? 'rotate-180' : ''" />
                                        <span x-text="showAll ? 'Ver menos' : 'Ver todas as {{ count($qr['texts']) }} respostas'"></span>
                                    </button>
                                @endif
                            </div>
                        </div>

                    {{-- ─── Sem respostas para esta pergunta ────────────── --}}
                    @else
                        <div class="p-5 flex items-center gap-2 text-sm text-slate-400 lato-regular">
                            <x-lucide-minus-circle class="w-4 h-4 shrink-0" />
                            Nenhuma resposta registrada para esta pergunta.
                        </div>
                    @endif

                </div>
            @endforeach
        </div>
    @endif

    {{-- ───── Rodapé ───────────────────────────────────────────────────── --}}
    <div class="mt-8 flex items-center justify-between text-[11px] text-slate-400 lato-regular">
        <span>Última atualização: {{ now()->format('d/m/Y H:i') }}</span>
        <a href="{{ route('pesquisas') }}" class="flex items-center gap-1 hover:text-slate-600 dark:hover:text-slate-200 transition">
            <x-lucide-arrow-left class="w-3 h-3" />
            Voltar para pesquisas
        </a>
    </div>

</div>
