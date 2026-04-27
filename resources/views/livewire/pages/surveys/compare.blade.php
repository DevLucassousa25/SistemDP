<div class="p-4 sm:p-6 lg:p-8">

    {{-- ── ApexCharts CDN ── --}}
    @once
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endonce

    @php
        $palette      = ['#10b981', '#3b82f6', '#8b5cf6', '#f59e0b'];
        $paletteBg    = ['rgba(16,185,129,.12)', 'rgba(59,130,246,.12)', 'rgba(139,92,246,.12)', 'rgba(245,158,11,.12)'];
        $paletteLabel = ['Pesquisa 1', 'Pesquisa 2', 'Pesquisa 3', 'Pesquisa 4'];
    @endphp

    {{-- ───── Cabeçalho ─────────────────────────────────────────────── --}}
    <div class="flex items-start gap-3">
        <a href="{{ route('pesquisas') }}"
           class="mt-0.5 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                  hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 dark:hover:text-white
                  transition cursor-pointer shrink-0"
           title="Voltar para pesquisas">
            <x-lucide-arrow-left class="w-4 h-4" />
        </a>
        <div>
            <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white lato-black tracking-tight">
                Comparativo entre Pesquisas
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1 lato-regular">
                Selecione edições da mesma pesquisa aplicada periodicamente para acompanhar a evolução dos indicadores
            </p>
        </div>
    </div>

    {{-- ───── Painel de seleção ─────────────────────────────────────── --}}
    <div class="mt-8 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 sm:p-6">

        <div class="flex items-center justify-between mb-1">
            <h2 class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold">
                Selecione as pesquisas
            </h2>
            <span class="text-[11px] text-slate-400 lato-regular">
                {{ count($selectedIds) }}/4 selecionadas
            </span>
        </div>
        <p class="text-xs text-slate-400 lato-regular mb-4">
            Selecione 2 a 4 pesquisas similares (mesmas perguntas). Apenas pesquisas com respostas são exibidas.
        </p>

        @if ($this->accessibleSurveys->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <x-lucide-inbox class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2" />
                <p class="text-sm text-slate-400 lato-regular">Nenhuma pesquisa com respostas encontrada.</p>
                <p class="text-xs text-slate-300 dark:text-slate-600 mt-1">Encerre ou aguarde respostas nas pesquisas ativas.</p>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 gap-2.5">
                @foreach ($this->accessibleSurveys as $s)
                    @php
                        $isSelected  = in_array($s->id, $selectedIds);
                        $isDisabled  = ! $isSelected && count($selectedIds) >= 4;
                        $statusDot   = match ($s->status) {
                            'ativa'     => '#10b981',
                            'encerrada' => '#94a3b8',
                            default     => '#f59e0b',
                        };
                        $selectedIdx = array_search($s->id, $selectedIds);
                        $accentColor = $selectedIdx !== false ? $palette[$selectedIdx] : null;
                    @endphp

                    <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition select-none
                                  {{ $isDisabled ? 'opacity-40 cursor-not-allowed' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50' }}"
                           style="{{ $isSelected
                               ? 'border-color:' . $accentColor . ';background-color:' . $paletteBg[$selectedIdx] . ';'
                               : '' }}
                                  {{ ! $isSelected ? 'border-color:#e2e8f0' : '' }}">

                        <input type="checkbox"
                               wire:model.live="selectedIds"
                               value="{{ $s->id }}"
                               {{ $isDisabled ? 'disabled' : '' }}
                               class="rounded accent-emerald-500 shrink-0 w-4 h-4 cursor-pointer">

                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-1.5">
                                @if ($isSelected)
                                    <span class="text-[10px] font-bold lato-bold px-1.5 py-0.5 rounded text-white shrink-0"
                                          style="background-color: {{ $accentColor }}">
                                        P{{ $selectedIdx + 1 }}
                                    </span>
                                @endif
                                <p class="text-sm font-semibold text-slate-800 dark:text-white truncate lato-bold">
                                    {{ $s->title }}
                                </p>
                            </div>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5 flex items-center gap-1.5">
                                <span class="inline-block w-1.5 h-1.5 rounded-full shrink-0"
                                      style="background-color: {{ $statusDot }}"></span>
                                {{ $s->status_label }}
                                @if ($s->start_date)
                                    · {{ $s->start_date->format('d/m/Y') }}
                                    @if ($s->end_date) – {{ $s->end_date->format('d/m/Y') }} @endif
                                @endif
                                · {{ $s->questions->count() }} perguntas
                            </p>
                        </div>
                    </label>
                @endforeach
            </div>

            {{-- Feedback de seleção --}}
            <div class="mt-4">
                @if ($similarityError)
                    <div class="flex items-start gap-2 p-3.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl text-sm text-amber-700 dark:text-amber-300">
                        <x-lucide-alert-triangle class="w-4 h-4 shrink-0 mt-0.5" />
                        <span class="lato-regular">{{ $similarityError }}</span>
                    </div>
                @elseif (count($selectedIds) >= 2)
                    <div class="flex items-center gap-2 p-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 rounded-xl text-sm text-emerald-700 dark:text-emerald-300">
                        <x-lucide-check-circle class="w-4 h-4 shrink-0" />
                        <span class="lato-regular">
                            {{ count($selectedIds) }} pesquisas selecionadas
                            @if ($this->comparison)
                                — {{ $this->comparison['shared_count'] }} {{ $this->comparison['shared_count'] === 1 ? 'pergunta compartilhada' : 'perguntas compartilhadas' }}
                            @endif
                        </span>
                    </div>
                @elseif (count($selectedIds) === 1)
                    <p class="text-xs text-slate-400 lato-regular flex items-center gap-1.5">
                        <x-lucide-info class="w-3.5 h-3.5" />
                        Selecione pelo menos mais uma pesquisa similar para comparar
                    </p>
                @endif
            </div>
        @endif
    </div>

    {{-- ───── Resultados do comparativo ────────────────────────────── --}}
    @if ($comparison = $this->comparison)

        @php
            $orderedIds    = $comparison['ordered_ids'];
            $stats         = $comparison['stats'];
            $surveyLabels  = collect($orderedIds)->map(fn ($id) => $stats[$id]['label'])->values()->toArray();
            $surveyCount   = count($orderedIds);
        @endphp

        {{-- Cards resumo por pesquisa --}}
        <div class="mt-6 grid gap-4"
             style="grid-template-columns: repeat({{ $surveyCount }}, minmax(0, 1fr))">
            @foreach ($orderedIds as $i => $sid)
                @php $s = $stats[$sid]; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 sm:p-5">
                    <div class="flex items-center gap-2 mb-3">
                        <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $palette[$i] }}"></div>
                        <p class="text-[11px] font-semibold lato-bold uppercase tracking-wide"
                           style="color: {{ $palette[$i] }}">
                            {{ $paletteLabel[$i] }}
                        </p>
                    </div>
                    <p class="text-sm font-semibold text-slate-800 dark:text-white leading-tight lato-bold mb-1">
                        {{ $s['title'] }}
                    </p>
                    @if ($s['period'])
                        <p class="text-[11px] text-slate-400 lato-regular mb-3">{{ $s['period'] }}</p>
                    @endif
                    <div class="grid grid-cols-2 gap-2 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <div>
                            <p class="text-[10px] text-slate-400 lato-regular uppercase tracking-wide">Respostas</p>
                            <p class="text-xl font-bold text-slate-800 dark:text-white lato-black">{{ $s['total_responses'] }}</p>
                            <p class="text-[10px] text-slate-400 lato-regular">de {{ $s['total_invited'] }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 lato-regular uppercase tracking-wide">Taxa</p>
                            <p class="text-xl font-bold text-slate-800 dark:text-white lato-black">{{ $s['response_rate'] }}%</p>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Gráfico de taxa de resposta --}}
        <div class="mt-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 sm:p-6">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">
                Taxa de Resposta por Edição
            </p>
            <div wire:ignore id="apex-compare-rate" style="min-height:180px"></div>
        </div>

        <script>
        (function () {
            var labels    = @json($surveyLabels);
            var rates     = @json(collect($orderedIds)->map(fn ($id) => $stats[$id]['response_rate'])->values()->toArray());
            var colors    = @json($palette);
            function renderRate() {
                if (typeof ApexCharts === 'undefined') { setTimeout(renderRate, 80); return; }
                var el = document.getElementById('apex-compare-rate');
                if (!el || el.dataset.apex) return;
                el.dataset.apex = '1';
                var isDark = document.documentElement.classList.contains('dark');
                var tc = isDark ? '#94a3b8' : '#64748b';
                new ApexCharts(el, {
                    chart: { type: 'line', height: 200, toolbar: { show: false }, fontFamily: 'inherit', zoom: { enabled: false } },
                    series: [{ name: 'Taxa de Resposta', data: rates }],
                    colors: ['#10b981'],
                    stroke: { curve: 'smooth', width: 2.5 },
                    markers: { size: 7, colors: colors.slice(0, rates.length), strokeColors: '#fff', strokeWidth: 2 },
                    xaxis: { categories: labels, labels: { style: { colors: tc, fontSize: '12px' } } },
                    yaxis: { min: 0, max: 100, tickAmount: 4, labels: { formatter: function (v) { return v + '%'; }, style: { colors: tc } } },
                    grid: { borderColor: isDark ? '#334155' : '#e2e8f0' },
                    dataLabels: { enabled: true, formatter: function (v) { return v + '%'; }, style: { fontSize: '11px', fontFamily: 'inherit' }, background: { enabled: false } },
                    tooltip: { y: { formatter: function (v) { return v + '%'; } } },
                }).render();
            }
            renderRate();
        }());
        </script>

        {{-- ───── Bloco por pergunta compartilhada ───────────────────── --}}
        @foreach ($comparison['question_analytics'] as $qi => $qa)
            @php
                $typeLabel = match ($qa['type']) {
                    'escala'          => 'Escala 0–10',
                    'multipla_escolha'=> 'Múltipla escolha',
                    default           => 'Texto livre',
                };
                $typeBg = match ($qa['type']) {
                    'escala'          => 'background:#eef2ff;color:#4f46e5',
                    'multipla_escolha'=> 'background:#fffbeb;color:#b45309',
                    default           => 'background:#f1f5f9;color:#475569',
                };
            @endphp

            <div class="mt-5 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 sm:p-6">

                {{-- Cabeçalho da pergunta --}}
                <div class="flex flex-wrap items-start gap-2 mb-5">
                    <span class="text-[11px] font-semibold lato-bold px-2 py-0.5 rounded-md shrink-0 mt-0.5"
                          style="{{ $typeBg }}">
                        {{ $typeLabel }}
                    </span>
                    <p class="text-sm font-semibold text-slate-800 dark:text-white leading-snug lato-bold">
                        {{ $qa['text'] }}
                    </p>
                </div>

                {{-- ── ESCALA ──────────────────────────────────────────── --}}
                @if ($qa['type'] === 'escala')
                    @php
                        $npsArr        = collect($orderedIds)->map(fn ($id) => $qa['data'][$id]['nps'])->toArray();
                        $avgArr        = collect($orderedIds)->map(fn ($id) => $qa['data'][$id]['avg'])->toArray();
                        $promotersArr  = collect($orderedIds)->map(fn ($id) => $qa['data'][$id]['promoters'])->toArray();
                        $passivesArr   = collect($orderedIds)->map(fn ($id) => $qa['data'][$id]['passives'])->toArray();
                        $detractorsArr = collect($orderedIds)->map(fn ($id) => $qa['data'][$id]['detractors'])->toArray();
                    @endphp

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- NPS --}}
                        <div>
                            <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide mb-2">Evolução do NPS</p>
                            <div wire:ignore id="apex-nps-{{ $qi }}" style="min-height:210px"></div>
                        </div>
                        {{-- Breakdown --}}
                        <div>
                            <p class="text-[11px] text-slate-400 lato-regular uppercase tracking-wide mb-2">Distribuição por edição</p>
                            <div wire:ignore id="apex-bd-{{ $qi }}" style="min-height:210px"></div>
                        </div>
                    </div>

                    {{-- Linha de média + NPS por edição --}}
                    <div class="mt-4 pt-4 border-t border-slate-100 dark:border-slate-700 flex flex-wrap gap-2.5">
                        @foreach ($orderedIds as $i => $sid)
                            @php $d = $qa['data'][$sid]; @endphp
                            <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                                <div class="w-2 h-2 rounded-full" style="background-color: {{ $palette[$i] }}"></div>
                                <span class="text-[11px] text-slate-400 lato-regular">{{ $stats[$sid]['label'] }}</span>
                                <span class="text-sm font-bold text-slate-800 dark:text-white lato-black">
                                    {{ $d['avg'] !== null ? $d['avg'] : '—' }}
                                </span>
                                @if ($d['nps'] !== null)
                                    <span class="text-[10px] font-semibold lato-bold px-1.5 py-0.5 rounded"
                                          style="{{ $d['nps'] >= 50 ? 'background:#d1fae5;color:#065f46' : ($d['nps'] >= 0 ? 'background:#fef3c7;color:#92400e' : 'background:#fee2e2;color:#991b1b') }}">
                                        NPS {{ $d['nps'] > 0 ? '+' : '' }}{{ $d['nps'] }}
                                    </span>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    <script>
                    (function () {
                        var labels      = @json($surveyLabels);
                        var npsData     = @json($npsArr);
                        var palette     = @json($palette);
                        var promoters   = @json($promotersArr);
                        var passives    = @json($passivesArr);
                        var detractors  = @json($detractorsArr);

                        function renderNps() {
                            if (typeof ApexCharts === 'undefined') { setTimeout(renderNps, 80); return; }
                            var el = document.getElementById('apex-nps-{{ $qi }}');
                            if (!el || el.dataset.apex) return;
                            el.dataset.apex = '1';
                            var isDark = document.documentElement.classList.contains('dark');
                            var tc = isDark ? '#94a3b8' : '#64748b';
                            new ApexCharts(el, {
                                chart: { type: 'line', height: 210, toolbar: { show: false }, fontFamily: 'inherit', zoom: { enabled: false } },
                                series: [{ name: 'NPS', data: npsData }],
                                colors: ['#6366f1'],
                                stroke: { curve: 'smooth', width: 2.5 },
                                markers: { size: 7, colors: palette.slice(0, labels.length), strokeColors: '#fff', strokeWidth: 2 },
                                xaxis: { categories: labels, labels: { style: { colors: tc, fontSize: '12px' } } },
                                yaxis: { min: -100, max: 100, tickAmount: 4, labels: { style: { colors: tc }, formatter: function (v) { return v > 0 ? '+' + v : v; } } },
                                grid: { borderColor: isDark ? '#334155' : '#e2e8f0' },
                                annotations: { yaxis: [{ y: 0, borderColor: '#94a3b8', strokeDashArray: 4, label: { text: 'NPS 0', style: { color: '#94a3b8', fontSize: '10px', background: 'transparent' } } }] },
                                dataLabels: { enabled: true, formatter: function (v) { return v !== null ? (v > 0 ? '+' + v : v) : ''; }, style: { fontSize: '11px', fontFamily: 'inherit' }, background: { enabled: false } },
                                tooltip: { y: { formatter: function (v) { return 'NPS ' + (v > 0 ? '+' : '') + v; } } },
                            }).render();
                        }

                        function renderBreakdown() {
                            if (typeof ApexCharts === 'undefined') { setTimeout(renderBreakdown, 80); return; }
                            var el = document.getElementById('apex-bd-{{ $qi }}');
                            if (!el || el.dataset.apex) return;
                            el.dataset.apex = '1';
                            var isDark = document.documentElement.classList.contains('dark');
                            var tc = isDark ? '#94a3b8' : '#64748b';
                            new ApexCharts(el, {
                                chart: { type: 'bar', height: 210, stacked: true, toolbar: { show: false }, fontFamily: 'inherit' },
                                series: [
                                    { name: 'Detratores (0–6)', data: detractors },
                                    { name: 'Neutros (7–8)',    data: passives },
                                    { name: 'Promotores (9–10)',data: promoters },
                                ],
                                colors: ['#ef4444', '#f59e0b', '#10b981'],
                                xaxis: { categories: labels, labels: { style: { colors: tc, fontSize: '12px' } } },
                                yaxis: { max: 100, labels: { formatter: function (v) { return v + '%'; }, style: { colors: tc } } },
                                plotOptions: { bar: { horizontal: false, columnWidth: '55%', borderRadius: 4, borderRadiusApplication: 'end' } },
                                grid: { borderColor: isDark ? '#334155' : '#e2e8f0' },
                                dataLabels: { formatter: function (v) { return v > 6 ? v.toFixed(0) + '%' : ''; }, style: { fontSize: '10px', fontFamily: 'inherit' } },
                                tooltip: { y: { formatter: function (v) { return v.toFixed(1) + '%'; } } },
                                legend: { position: 'bottom', labels: { colors: tc }, fontSize: '11px', fontFamily: 'inherit' },
                            }).render();
                        }

                        renderNps();
                        renderBreakdown();
                    }());
                    </script>

                {{-- ── MÚLTIPLA ESCOLHA ───────────────────────────────── --}}
                @elseif ($qa['type'] === 'multipla_escolha')
                    @php
                        $mcSeries = collect($orderedIds)->map(function ($sid, $i) use ($qa, $stats) {
                            $pcts = collect($qa['options'])->map(fn ($opt) => $qa['data'][$sid]['percentages'][$opt] ?? 0)->values()->toArray();
                            return ['name' => $stats[$sid]['label'], 'data' => $pcts];
                        })->values()->toArray();
                    @endphp

                    <div wire:ignore id="apex-mc-{{ $qi }}" style="min-height:220px"></div>

                    <script>
                    (function () {
                        var options = @json($qa['options']);
                        var series  = @json($mcSeries);
                        var palette = @json($palette);

                        function renderMc() {
                            if (typeof ApexCharts === 'undefined') { setTimeout(renderMc, 80); return; }
                            var el = document.getElementById('apex-mc-{{ $qi }}');
                            if (!el || el.dataset.apex) return;
                            el.dataset.apex = '1';
                            var isDark = document.documentElement.classList.contains('dark');
                            var tc = isDark ? '#94a3b8' : '#64748b';
                            new ApexCharts(el, {
                                chart: { type: 'bar', height: 240, toolbar: { show: false }, fontFamily: 'inherit' },
                                series: series,
                                colors: palette,
                                xaxis: { categories: options, labels: { style: { colors: tc, fontSize: '11px' } } },
                                yaxis: { labels: { formatter: function (v) { return v + '%'; }, style: { colors: tc } } },
                                plotOptions: { bar: { horizontal: false, columnWidth: '65%', borderRadius: 4 } },
                                grid: { borderColor: isDark ? '#334155' : '#e2e8f0' },
                                dataLabels: { formatter: function (v) { return v > 5 ? v.toFixed(0) + '%' : ''; }, style: { fontSize: '10px', fontFamily: 'inherit' } },
                                tooltip: { y: { formatter: function (v) { return v.toFixed(1) + '%'; } } },
                                legend: { position: 'bottom', labels: { colors: tc }, fontSize: '11px', fontFamily: 'inherit' },
                            }).render();
                        }
                        renderMc();
                    }());
                    </script>

                {{-- ── TEXTO LIVRE ─────────────────────────────────────── --}}
                @else
                    <div class="flex flex-wrap gap-3">
                        @foreach ($orderedIds as $i => $sid)
                            <div class="flex items-center gap-3 px-4 py-3.5 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                                <div class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $palette[$i] }}"></div>
                                <div>
                                    <p class="text-[11px] text-slate-400 lato-regular">{{ $stats[$sid]['label'] }}</p>
                                    <p class="text-xl font-bold text-slate-800 dark:text-white lato-black">
                                        {{ $qa['data'][$sid]['total'] }}
                                    </p>
                                    <p class="text-[10px] text-slate-400 lato-regular">
                                        {{ $qa['data'][$sid]['total'] === 1 ? 'resposta' : 'respostas' }}
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        @endforeach

        @if ($comparison['shared_count'] === 0)
            <div class="mt-5 p-5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-2xl text-sm text-amber-700 dark:text-amber-300 flex items-center gap-2">
                <x-lucide-alert-triangle class="w-4 h-4 shrink-0" />
                Nenhuma pergunta em comum encontrada entre todas as pesquisas selecionadas.
            </div>
        @endif

        <p class="mt-4 text-[11px] text-slate-400 dark:text-slate-500 text-center lato-regular">
            {{ $comparison['shared_count'] }}
            {{ $comparison['shared_count'] === 1 ? 'pergunta compartilhada' : 'perguntas compartilhadas' }}
            entre as {{ count($orderedIds) }} pesquisas selecionadas
        </p>

    @endif

</div>
