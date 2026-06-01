<div class="min-h-screen bg-slate-50 dark:bg-slate-950 pb-12">

    {{-- ════════════════════════════════════════════════════════════════
         HEADER
         ════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-900 border-b border-slate-200 dark:border-slate-800 px-4 sm:px-6 lg:px-8 py-6">
        <div class="max-w-4xl mx-auto flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-pink-500 flex items-center justify-center shadow-sm">
                    <x-lucide-heart-pulse class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h1 class="text-xl font-bold text-slate-800 dark:text-slate-100">Meu Histórico de Humor</h1>
                    <p class="text-sm text-slate-500 dark:text-slate-400">Apenas você vê estas informações</p>
                </div>
            </div>

            {{-- Filtros de período --}}
            <div class="flex items-center gap-1.5 bg-slate-100 dark:bg-slate-800 rounded-xl p-1">
                @foreach(['7' => '7 dias', '30' => '30 dias', '90' => '3 meses', 'custom' => 'Personalizado'] as $key => $label)
                    <button wire:click="setPeriodo('{{ $key }}')"
                            class="px-3 py-1.5 text-xs font-semibold rounded-lg transition cursor-pointer
                                   {{ $periodo === $key
                                       ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-slate-100 shadow-sm'
                                       : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300' }}">
                        {{ $label }}
                    </button>
                @endforeach
            </div>
        </div>

        {{-- Filtro de datas customizadas --}}
        @if($periodo === 'custom')
            <div class="max-w-4xl mx-auto mt-4 flex flex-wrap items-end gap-3">
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">De</label>
                    <input type="date" wire:model="dataInicio"
                           class="px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700
                                  bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Até</label>
                    <input type="date" wire:model="dataFim"
                           class="px-3 py-2 text-sm rounded-xl border border-slate-200 dark:border-slate-700
                                  bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400">
                </div>
                <button wire:click="aplicarCustom"
                        class="px-4 py-2 text-sm font-semibold rounded-xl bg-gradient-to-r from-blue-500 to-pink-500
                               hover:from-blue-500 hover:to-pink-600 text-white shadow-sm transition cursor-pointer">
                    Aplicar
                </button>
                @error('dataInicio') <p class="text-xs text-rose-500 self-end">{{ $message }}</p> @enderror
            </div>
        @endif
    </div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-6 space-y-6">

        {{-- ════════════════════════════════════════════════════════════
             STATS CARDS
             ════════════════════════════════════════════════════════════ --}}
        @php $s = $this->stats; $moods = \App\Models\MoodCheckin::MOODS; @endphp

        <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">

            {{-- Total check-ins --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-violet-50 dark:bg-violet-900/30 flex items-center justify-center">
                        <x-lucide-calendar-check class="w-4 h-4 text-indigo-500" />
                    </div>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Check-ins</span>
                </div>
                <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $s['total'] }}</p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">no período</p>
            </div>

            {{-- Score de bem-estar --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                        <x-lucide-heart-pulse class="w-4 h-4 text-emerald-500" />
                    </div>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Bem-estar</span>
                </div>
                @if($s['score'] !== null)
                    <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $s['score'] }}<span class="text-lg text-slate-400">%</span></p>
                    <div class="mt-2 w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full bg-gradient-to-r
                            {{ $s['score'] >= 70 ? 'from-emerald-400 to-green-500' : ($s['score'] >= 40 ? 'from-amber-400 to-yellow-500' : 'from-rose-400 to-red-500') }}"
                            style="width: {{ $s['score'] }}%"></div>
                    </div>
                @else
                    <p class="text-3xl font-bold text-slate-400">—</p>
                    <p class="text-xs text-slate-400 mt-0.5">sem dados</p>
                @endif
            </div>

            {{-- Streak atual --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center">
                        <x-lucide-flame class="w-4 h-4 text-amber-500" />
                    </div>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Sequência</span>
                </div>
                <p class="text-3xl font-bold text-slate-800 dark:text-slate-100">{{ $s['streak'] }}<span class="text-lg text-slate-400">d</span></p>
                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">recorde: {{ $s['melhor_streak'] }}d</p>
            </div>

            {{-- Humor mais frequente --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                <div class="flex items-center gap-2 mb-2">
                    <div class="w-8 h-8 rounded-lg bg-pink-50 dark:bg-pink-900/30 flex items-center justify-center">
                        <x-lucide-smile class="w-4 h-4 text-pink-500" />
                    </div>
                    <span class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Mais freq.</span>
                </div>
                @if($s['mais_frequente'])
                    @php $mf = $moods[$s['mais_frequente']]; @endphp
                    <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $mf['color'] }} flex items-center justify-center mb-1">
                        <x-dynamic-component :component="'lucide-' . $mf['icon']" class="w-5 h-5 text-white" />
                    </div>
                    <p class="text-xs font-semibold {{ $mf['text'] }}">{{ $mf['label'] }}</p>
                @else
                    <p class="text-3xl font-bold text-slate-400">—</p>
                    <p class="text-xs text-slate-400 mt-0.5">sem dados</p>
                @endif
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════
             GRÁFICO DE EVOLUÇÃO
             ════════════════════════════════════════════════════════════ --}}
        @if($this->stats['total'] > 0)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <x-lucide-trending-up class="w-4 h-4 text-indigo-500" />
                <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200">Evolução do Humor</h2>
                <span class="ml-auto text-xs text-slate-400">
                    {{ \Carbon\Carbon::parse($dataInicio)->format('d/m/Y') }} — {{ \Carbon\Carbon::parse($dataFim)->format('d/m/Y') }}
                </span>
            </div>

            <div class="p-4"
                 wire:key="chart-humor-{{ $periodo }}-{{ $dataInicio }}-{{ $dataFim }}"
                 x-data="{
                    chart: null,
                    init() {
                        this.$nextTick(() => this.renderChart());
                    },
                    renderChart() {
                        const el = this.$refs.chartEl;
                        if (!el || typeof ApexCharts === 'undefined') return;
                        if (this.chart) { this.chart.destroy(); this.chart = null; }

                        const isDark = document.documentElement.classList.contains('dark');
                        const labels  = @js($this->chartData['labels']);
                        const scores  = @js($this->chartData['scores']);
                        const moods   = @js($this->chartData['moods']);
                        const moodMap = {
                            'otimo':   { label: 'Ótimo',   color: '#10b981' },
                            'bem':     { label: 'Bem',     color: '#6366f1' },
                            'normal':  { label: 'Normal',  color: '#f59e0b' },
                            'pessimo': { label: 'Péssimo', color: '#f43f5e' },
                        };
                        const yLabels = { 1: 'Péssimo', 2: 'Normal', 3: 'Bem', 4: 'Ótimo' };

                        // Filtra apenas os dias com registro — ApexCharts não renderiza
                        // pontos isolados em area/line quando cercados de nulls.
                        // Formato {x, y} permite dados esparsos sem categorias fixas.
                        const dataPoints = scores
                            .map((s, i) => s !== null ? { x: labels[i], y: s, mood: moods[i] } : null)
                            .filter(Boolean);

                        const markerColors = dataPoints.map(d => moodMap[d.mood]?.color ?? '#8b5cf6');

                        this.chart = new ApexCharts(el, {
                            chart: {
                                type: 'line',
                                height: 240,
                                toolbar: { show: false },
                                background: 'transparent',
                                fontFamily: 'Inter, sans-serif',
                                animations: { enabled: true, easing: 'easeinout', speed: 600 },
                            },
                            series: [{ name: 'Humor', data: dataPoints.map(d => ({ x: d.x, y: d.y })) }],
                            colors: ['#8b5cf6'],
                            stroke: { curve: 'smooth', width: 2.5 },
                            markers: {
                                size: 7,
                                colors: markerColors,
                                strokeColors: isDark ? '#1e293b' : '#ffffff',
                                strokeWidth: 2.5,
                                hover: { size: 9 },
                                discrete: dataPoints.map((d, i) => ({
                                    seriesIndex: 0,
                                    dataPointIndex: i,
                                    fillColor: moodMap[d.mood]?.color ?? '#8b5cf6',
                                    strokeColor: isDark ? '#1e293b' : '#ffffff',
                                    size: 7,
                                })),
                            },
                            xaxis: {
                                type: 'category',
                                labels: {
                                    style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' },
                                    rotate: -30,
                                    rotateAlways: labels.length > 20,
                                },
                                axisBorder: { show: false },
                                axisTicks: { show: false },
                                tooltip: { enabled: false },
                            },
                            yaxis: {
                                min: 0.5, max: 4.5,
                                tickAmount: 3,
                                labels: {
                                    style: { colors: isDark ? '#94a3b8' : '#64748b', fontSize: '11px' },
                                    formatter: (val) => yLabels[Math.round(val)] ?? '',
                                },
                            },
                            grid: {
                                borderColor: isDark ? '#334155' : '#f1f5f9',
                                strokeDashArray: 4,
                                yaxis: { lines: { show: true } },
                                xaxis: { lines: { show: false } },
                            },
                            tooltip: {
                                theme: isDark ? 'dark' : 'light',
                                custom({ series, seriesIndex, dataPointIndex, w }) {
                                    const d = dataPoints[dataPointIndex];
                                    if (!d) return '';
                                    const m = moodMap[d.mood];
                                    return `<div style='padding:8px 12px;font-size:13px;display:flex;align-items:center;gap:8px;'>
                                        <span style='display:inline-block;width:10px;height:10px;border-radius:50%;background:${m.color};flex-shrink:0;'></span>
                                        <strong>${m.label}</strong>
                                        <span style='font-size:11px;opacity:.6;margin-left:4px;'>${d.x}</span>
                                    </div>`;
                                },
                            },
                            dataLabels: { enabled: false },
                        });
                        this.chart.render();
                    },
                    destroy() {
                        if (this.chart) { this.chart.destroy(); this.chart = null; }
                    },
                 }">
                <div x-ref="chartEl" style="min-height:240px;"></div>
            </div>

            {{-- Legenda com ícones Lucide --}}
            <div class="px-5 pb-4 flex flex-wrap gap-4">
                @foreach(\App\Models\MoodCheckin::MOODS as $key => $mood)
                    <div class="flex items-center gap-1.5 text-xs">
                        <div class="w-5 h-5 rounded-md bg-gradient-to-br {{ $mood['color'] }} flex items-center justify-center">
                            <x-dynamic-component :component="'lucide-' . $mood['icon']" class="w-3 h-3 text-white" />
                        </div>
                        <span class="font-medium {{ $mood['text'] }}">{{ $mood['label'] }}</span>
                        <span class="text-slate-400">({{ $this->stats['distribuicao'][$key] }})</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ════════════════════════════════════════════════════════════
             DISTRIBUIÇÃO EM BARRAS
             ════════════════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <div class="flex items-center gap-2 mb-4">
                <x-lucide-bar-chart-2 class="w-4 h-4 text-slate-400" />
                <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200">Distribuição</h2>
            </div>
            <div class="space-y-3">
                @foreach(\App\Models\MoodCheckin::MOODS as $key => $mood)
                    @php
                        $count = $this->stats['distribuicao'][$key];
                        $pct   = $this->stats['total'] > 0 ? round($count / $this->stats['total'] * 100) : 0;
                    @endphp
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $mood['color'] }} flex items-center justify-center shrink-0">
                            <x-dynamic-component :component="'lucide-' . $mood['icon']" class="w-4 h-4 text-white" />
                        </div>
                        <div class="flex-1">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs font-semibold {{ $mood['text'] }}">{{ $mood['label'] }}</span>
                                <span class="text-xs text-slate-400">{{ $count }} ({{ $pct }}%)</span>
                            </div>
                            <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                <div class="h-full rounded-full bg-gradient-to-r {{ $mood['color'] }} transition-all duration-700"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ════════════════════════════════════════════════════════════
             TIMELINE DE CHECK-INS
             ════════════════════════════════════════════════════════════ --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                <x-lucide-clock class="w-4 h-4 text-slate-400" />
                <h2 class="text-sm font-bold text-slate-700 dark:text-slate-200">Histórico</h2>
                <span class="ml-auto text-xs text-slate-400">{{ $this->stats['total'] }} registro{{ $this->stats['total'] !== 1 ? 's' : '' }}</span>
            </div>

            @if($this->checkins->isEmpty())
                <div class="py-16 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                        <x-lucide-calendar-x class="w-7 h-7 text-slate-400" />
                    </div>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300 mb-1">Nenhum check-in no período</p>
                    <p class="text-xs text-slate-400">Responda ao popup diário para construir seu histórico.</p>
                </div>
            @else
                <div class="divide-y divide-slate-100 dark:divide-slate-700/60">
                    @foreach($this->checkins as $checkin)
                        @php $mood = \App\Models\MoodCheckin::MOODS[$checkin->mood]; @endphp
                        <div class="flex items-start gap-4 px-5 py-4 hover:bg-slate-50/60 dark:hover:bg-slate-700/20 transition">

                            {{-- Ícone Lucide com gradiente --}}
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br {{ $mood['color'] }}
                                        flex items-center justify-center shadow-sm shrink-0">
                                <x-dynamic-component :component="'lucide-' . $mood['icon']" class="w-5 h-5 text-white" />
                            </div>

                            {{-- Dados --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-bold {{ $mood['text'] }}">{{ $mood['label'] }}</span>
                                    <span class="text-xs text-slate-400 shrink-0">
                                        {{ $checkin->checkin_date->translatedFormat('d \d\e M\. Y') }}
                                    </span>
                                </div>
                                @if($checkin->note)
                                    <p class="text-sm text-slate-600 dark:text-slate-300 mt-0.5 leading-relaxed">
                                        "{{ $checkin->note }}"
                                    </p>
                                @else
                                    <p class="text-xs text-slate-400 mt-0.5">Sem observação</p>
                                @endif
                            </div>

                        </div>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Nota de privacidade --}}
        <div class="flex items-center gap-2 px-1">
            <x-lucide-shield class="w-3.5 h-3.5 text-slate-400 shrink-0" />
            <p class="text-xs text-slate-400">
                Seu histórico de humor é <strong class="font-semibold">completamente privado</strong>.
                Gerentes e RH veem apenas dados agregados por equipe, sem identificação individual.
            </p>
        </div>

    </div>
</div>
