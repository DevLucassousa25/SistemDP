    {{-- ════════════════════════════════════════════════════════════════
         ABA: ANÁLISES AVANÇADAS
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'analises')
        @php $ap = $this->analyticsPersonal; @endphp
        <div class="space-y-6">

            {{-- ── Cabeçalho Análise Pessoal ─────────────────────────────── --}}
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600
                            flex items-center justify-center shadow shadow-indigo-400/30">
                    <x-lucide-chart-no-axes-combined class="w-4.5 h-4.5 text-white" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white lato-bold">
                        Análise do Meu Plano — {{ $selectedYear }}
                    </h2>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Visão analítica detalhada do seu desenvolvimento</p>
                </div>
            </div>

            @if (empty($ap))
                {{-- Estado vazio --}}
                <div class="flex flex-col items-center justify-center py-20 text-center
                            bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                    <x-lucide-line-chart class="w-12 h-12 text-slate-200 dark:text-slate-600 mb-3" />
                    <p class="text-sm lato-bold text-slate-500">Nenhum plano DPI em {{ $selectedYear }}</p>
                    <p class="text-xs text-slate-400 lato-regular mt-1">Crie um plano para visualizar análises detalhadas</p>
                </div>
            @else

                {{-- ── KPIs pessoais ──────────────────────────────────── --}}
                @php
                    $apKpis = [
                        ['label' => 'Total de ações',  'value' => $ap['totalActions'], 'icon' => 'layers',          'ring' => 'ring-indigo-400/20',  'bg' => 'bg-indigo-50 dark:bg-indigo-900/20',   'text' => 'text-indigo-600 dark:text-indigo-400'],
                        ['label' => 'Concluídas',       'value' => $ap['doneActions'],  'icon' => 'check-circle-2',  'ring' => 'ring-emerald-400/20', 'bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => 'text-emerald-600 dark:text-emerald-400'],
                        ['label' => 'Em andamento',     'value' => $ap['inProgress'],   'icon' => 'zap',             'ring' => 'ring-blue-400/20',    'bg' => 'bg-blue-50 dark:bg-blue-900/20',       'text' => 'text-blue-600 dark:text-blue-400'],
                        ['label' => 'Pendentes',        'value' => $ap['pending'],      'icon' => 'clock',           'ring' => 'ring-slate-300/30',   'bg' => 'bg-slate-100 dark:bg-slate-700',        'text' => 'text-slate-500 dark:text-slate-400'],
                        ['label' => 'Atrasadas',        'value' => $ap['overdue'],      'icon' => 'alarm-clock',     'ring' => $ap['overdue'] > 0 ? 'ring-red-400/20' : 'ring-emerald-400/20', 'bg' => $ap['overdue'] > 0 ? 'bg-red-50 dark:bg-red-900/20' : 'bg-emerald-50 dark:bg-emerald-900/20', 'text' => $ap['overdue'] > 0 ? 'text-red-600 dark:text-red-400' : 'text-emerald-600 dark:text-emerald-400'],
                    ];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
                    @foreach ($apKpis as $k)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 ring-1 {{ $k['ring'] }}">
                            <div class="flex items-center gap-2 mb-2">
                                <div class="w-6 h-6 rounded-lg {{ $k['bg'] }} flex items-center justify-center">
                                    <x-dynamic-component :component="'lucide-' . $k['icon']" class="w-3.5 h-3.5 {{ $k['text'] }}" />
                                </div>
                                <p class="text-[11px] text-slate-400 lato-regular leading-tight">{{ $k['label'] }}</p>
                            </div>
                            <p class="text-2xl lato-black {{ $k['text'] }}">{{ $k['value'] }}</p>
                        </div>
                    @endforeach
                </div>

                {{-- ── Gauge + Status Donut + Ações em Risco ─────────────── --}}
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                    {{-- Gauge de progresso geral --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 flex flex-col">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-1">Progresso geral</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-3">{{ $ap['doneActions'] }} de {{ $ap['totalActions'] }} ações concluídas</p>
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-an-gauge');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:   { type: 'radialBar', height: 200, toolbar: { show: false }, background: 'transparent' },
                                        series:  [{{ $ap['progress'] }}],
                                        plotOptions: {
                                            radialBar: {
                                                startAngle: -90, endAngle: 90,
                                                hollow: { size: '60%' },
                                                dataLabels: {
                                                    name:  { show: true,  offsetY: -8,  color: dark?'#94a3b8':'#64748b', fontSize: '11px' },
                                                    value: { show: true,  offsetY: -40, color: dark?'#f1f5f9':'#1e293b', fontSize: '30px', fontWeight: 900,
                                                             formatter: function(v){ return v+'%'; } }
                                                }
                                            }
                                        },
                                        labels:  ['Concluído'],
                                        colors:  ['#6366f1'],
                                        fill:    { type: 'gradient', gradient: { shade: 'light', type: 'horizontal', gradientToColors: ['#8b5cf6'] } },
                                        theme:   { mode: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })();
                             ">
                            <div id="dpi-an-gauge"></div>
                        </div>
                        {{-- Barra de velocidade / projeção --}}
                        <div class="mt-auto pt-3 border-t border-slate-100 dark:border-slate-700 space-y-2 text-[11px] lato-regular text-slate-400">
                            <div class="flex justify-between">
                                <span>Concluídas nos últimos 30 dias</span>
                                <span class="lato-bold text-slate-600 dark:text-slate-300">{{ $ap['recentDone'] }}</span>
                            </div>
                            @if ($ap['projectionDays'] !== null)
                                <div class="flex justify-between">
                                    <span>Projeção de conclusão</span>
                                    <span class="lato-bold text-indigo-500">~{{ $ap['projectionDays'] }} dias</span>
                                </div>
                            @elseif ($ap['doneActions'] === $ap['totalActions'] && $ap['totalActions'] > 0)
                                <div class="flex items-center gap-1 text-emerald-500 lato-bold">
                                    <x-lucide-check-circle class="w-3 h-3" /> Plano concluído!
                                </div>
                            @else
                                <div class="flex justify-between">
                                    <span>Projeção</span>
                                    <span class="text-slate-300 dark:text-slate-600">Sem dados suficientes</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Donut de distribuição por status --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-1">Distribuição por status</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-3">Como estão suas ações agora</p>
                        @if ($ap['totalActions'] > 0)
                            <div wire:ignore
                                 x-data
                                 x-init="
                                    (function init() {
                                        if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                        var el = $el.querySelector('#dpi-an-status');
                                        if (!el) return;
                                        if (el._chart) { el._chart.destroy(); }
                                        var dark = document.documentElement.classList.contains('dark');
                                        el._chart = new ApexCharts(el, {
                                            chart:       { type: 'donut', height: 210, background: 'transparent', toolbar: { show: false } },
                                            series:      [{{ $ap['doneActions'] }}, {{ $ap['inProgress'] }}, {{ $ap['pending'] }}, {{ $ap['overdue'] }}],
                                            labels:      ['Concluídas','Em andamento','Pendentes','Atrasadas'],
                                            colors:      ['#10b981','#3b82f6','#94a3b8','#ef4444'],
                                            legend:      { position: 'bottom', labels: { colors: dark?'#94a3b8':'#64748b' }, fontSize: '11px' },
                                            dataLabels:  { style: { fontSize: '11px' } },
                                            plotOptions: { pie: { donut: { size: '60%' } } },
                                            theme:       { mode: dark?'dark':'light' },
                                            tooltip:     { theme: dark?'dark':'light' },
                                        });
                                        el._chart.render();
                                    })();
                                 ">
                                <div id="dpi-an-status"></div>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-40 text-slate-300 dark:text-slate-600">
                                <x-lucide-pie-chart class="w-10 h-10" />
                            </div>
                        @endif
                    </div>

                    {{-- Ações em risco --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <div>
                                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-1.5">
                                    <x-lucide-shield-alert class="w-3.5 h-3.5 text-amber-500" />
                                    Ações em risco
                                </h3>
                                <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Atrasadas ou prazo em 14 dias</p>
                            </div>
                            @if ($ap['atRisk']->isNotEmpty())
                                <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400">
                                    {{ $ap['atRisk']->count() }}
                                </span>
                            @endif
                        </div>
                        @if ($ap['atRisk']->isEmpty())
                            <div class="flex flex-col items-center justify-center py-10 text-center">
                                <x-lucide-shield-check class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                                <p class="text-xs text-slate-400 lato-regular">Nenhuma ação em risco!</p>
                            </div>
                        @else
                            <div class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                @foreach ($ap['atRisk'] as $rAction)
                                    @php
                                        $daysLeft  = now()->diffInDays($rAction->target_date, false);
                                        $isOverdue = $daysLeft < 0;
                                        $rColor    = $isOverdue ? 'red' : ($daysLeft <= 7 ? 'amber' : 'yellow');
                                        $rBgMap    = ['red'=>'bg-red-50 dark:bg-red-900/20','amber'=>'bg-amber-50 dark:bg-amber-900/20','yellow'=>'bg-yellow-50 dark:bg-yellow-900/20'];
                                        $rTextMap  = ['red'=>'text-red-600 dark:text-red-400','amber'=>'text-amber-600 dark:text-amber-400','yellow'=>'text-yellow-600 dark:text-yellow-400'];
                                    @endphp
                                    <div class="flex items-start gap-3 px-4 py-3">
                                        <div class="w-7 h-7 rounded-lg {{ $rBgMap[$rColor] }} flex items-center justify-center shrink-0 mt-0.5">
                                            <x-lucide-clock class="w-3.5 h-3.5 {{ $rTextMap[$rColor] }}" />
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $rAction->title }}</p>
                                            <p class="text-[11px] text-slate-400 lato-regular truncate">{{ $rAction->goal->name }}</p>
                                            <p class="text-[10px] {{ $rTextMap[$rColor] }} lato-bold mt-0.5">
                                                {{ $isOverdue ? abs($daysLeft).'d atrasada' : ($daysLeft === 0 ? 'Vence hoje' : 'Vence em '.$daysLeft.'d') }}
                                                · {{ $rAction->target_date->format('d/m/Y') }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                </div>{{-- /grid gauge+donut+risk --}}

                {{-- ── Radar de Competências + Evolução Mensal ───────────── --}}
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                    {{-- Radar: nível atual vs meta por competência --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-0.5">Radar de competências</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-3">Nível atual vs meta definida</p>
                        @if (count($ap['radarLabels']) >= 2)
                            <div wire:ignore
                                 x-data
                                 x-init="
                                    (function init() {
                                        if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                        var el = $el.querySelector('#dpi-an-radar');
                                        if (!el) return;
                                        if (el._chart) { el._chart.destroy(); }
                                        var dark = document.documentElement.classList.contains('dark');
                                        el._chart = new ApexCharts(el, {
                                            chart:   { type: 'radar', height: 280, toolbar: { show: false }, background: 'transparent' },
                                            series:  [
                                                { name: 'Nível Atual', data: {{ json_encode($ap['radarCurrent']) }} },
                                                { name: 'Meta',        data: {{ json_encode($ap['radarTarget'])  }} },
                                            ],
                                            labels:  {{ json_encode($ap['radarLabels']) }},
                                            colors:  ['#6366f1','#a78bfa'],
                                            fill:    { opacity: [0.3, 0.1] },
                                            stroke:  { width: [2, 2], dashArray: [0, 5] },
                                            markers: { size: 4 },
                                            yaxis:   { min: 0, max: 5, tickAmount: 5, labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '10px' } } },
                                            xaxis:   { labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '11px' } } },
                                            legend:  { labels: { colors: dark?'#94a3b8':'#64748b' } },
                                            theme:   { mode: dark?'dark':'light' },
                                            tooltip: { theme: dark?'dark':'light' },
                                        });
                                        el._chart.render();
                                    })();
                                 ">
                                <div id="dpi-an-radar"></div>
                            </div>
                        @elseif (count($ap['radarLabels']) === 1)
                            {{-- Single goal: show a simple level bar instead --}}
                            @php $g0 = $ap['radarLabels'][0]; @endphp
                            <div class="flex flex-col gap-3 py-4">
                                <p class="text-xs lato-bold text-slate-600 dark:text-slate-300">{{ $g0 }}</p>
                                <div class="flex items-center gap-3">
                                    <span class="text-[11px] text-slate-400 w-20 shrink-0">Atual: <strong>{{ $ap['radarCurrent'][0] }}/5</strong></span>
                                    <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-indigo-500 rounded-full" style="width:{{ $ap['radarCurrent'][0] / 5 * 100 }}%"></div>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span class="text-[11px] text-slate-400 w-20 shrink-0">Meta: <strong>{{ $ap['radarTarget'][0] }}/5</strong></span>
                                    <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-violet-400 rounded-full" style="width:{{ $ap['radarTarget'][0] / 5 * 100 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="flex items-center justify-center h-40 text-slate-300 dark:text-slate-600">
                                <p class="text-xs text-slate-400 lato-regular">Adicione competências para ver o radar</p>
                            </div>
                        @endif

                        {{-- Prioridades footer --}}
                        @if (array_sum($ap['goalsByPriority']) > 0)
                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center gap-4 text-[11px] lato-regular text-slate-400">
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-red-400"></span>Alta: {{ $ap['goalsByPriority']['alta'] }}</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-amber-400"></span>Média: {{ $ap['goalsByPriority']['media'] }}</span>
                                <span class="flex items-center gap-1"><span class="w-2 h-2 rounded-full bg-slate-300 dark:bg-slate-600"></span>Baixa: {{ $ap['goalsByPriority']['baixa'] }}</span>
                            </div>
                        @endif
                    </div>

                    {{-- Evolução mensal de conclusões --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-0.5">Evolução de conclusões</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-3">Ações concluídas por mês nos últimos 6 meses</p>
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-an-monthly');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'area', height: 200, toolbar: { show: false }, background: 'transparent' },
                                        series:      [{ name: 'Concluídas', data: {{ json_encode($ap['monthCounts']) }} }],
                                        xaxis:       { categories: {{ json_encode($ap['monthLabels']) }}, labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '11px' } } },
                                        yaxis:       { min: 0, tickAmount: 4, labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '10px' } } },
                                        colors:      ['#10b981'],
                                        fill:        { type: 'gradient', gradient: { opacityFrom: 0.35, opacityTo: 0.02 } },
                                        stroke:      { width: 2, curve: 'smooth' },
                                        dataLabels:  { enabled: true, style: { fontSize: '10px', colors: ['#10b981'] }, background: { enabled: false } },
                                        grid:        { borderColor: dark?'#334155':'#f1f5f9', strokeDashArray: 4 },
                                        theme:       { mode: dark?'dark':'light' },
                                        tooltip:     { theme: dark?'dark':'light' },
                                    });
                                    el._chart.render();
                                })();
                             ">
                            <div id="dpi-an-monthly"></div>
                        </div>

                        {{-- Velocidade + projeção textual --}}
                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 grid grid-cols-2 gap-3 text-[11px]">
                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                                <p class="text-slate-400 lato-regular">Ritmo (30 dias)</p>
                                <p class="text-base lato-black text-indigo-600 dark:text-indigo-400 mt-0.5">{{ $ap['recentDone'] }}
                                    <span class="text-xs font-normal text-slate-400 lato-regular">ações</span>
                                </p>
                            </div>
                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                                <p class="text-slate-400 lato-regular">Projeção final</p>
                                @if ($ap['projectionDays'] !== null)
                                    <p class="text-base lato-black text-indigo-600 dark:text-indigo-400 mt-0.5">~{{ $ap['projectionDays'] }}d</p>
                                @elseif ($ap['doneActions'] === $ap['totalActions'] && $ap['totalActions'] > 0)
                                    <p class="text-base lato-black text-emerald-600 mt-0.5">Concluído ✓</p>
                                @else
                                    <p class="text-slate-300 dark:text-slate-600 lato-regular mt-0.5">—</p>
                                @endif
                            </div>
                        </div>
                    </div>

                </div>{{-- /grid radar+monthly --}}

                {{-- ── Ações por tipo ──────────────────────────────────── --}}
                @if (! empty($ap['typeData']))
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-0.5">Ações por tipo</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mb-4">Total e concluídas por categoria de desenvolvimento</p>
                        <div class="space-y-3">
                            @php
                                $typeColors = ['Curso'=>'indigo','Certificação'=>'violet','Leitura'=>'teal','Mentoria'=>'purple','Projeto'=>'orange','Workshop'=>'pink','Outro'=>'slate'];
                                $typeHex    = ['Curso'=>'#6366f1','Certificação'=>'#8b5cf6','Leitura'=>'#14b8a6','Mentoria'=>'#a855f7','Projeto'=>'#f97316','Workshop'=>'#ec4899','Outro'=>'#94a3b8'];
                            @endphp
                            @foreach ($ap['typeData'] as $td)
                                @php
                                    $tdPct = $td['total'] > 0 ? round($td['done'] / $td['total'] * 100) : 0;
                                    $tdHex = $typeHex[$td['label']] ?? '#94a3b8';
                                @endphp
                                <div class="flex items-center gap-3">
                                    <span class="text-xs lato-bold text-slate-600 dark:text-slate-300 w-24 shrink-0 truncate">{{ $td['label'] }}</span>
                                    <div class="flex-1 h-2.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all" style="width:{{ $tdPct }}%; background-color:{{ $tdHex }};"></div>
                                    </div>
                                    <span class="text-[11px] lato-bold text-slate-500 dark:text-slate-400 w-16 text-right shrink-0">
                                        {{ $td['done'] }}/{{ $td['total'] }} ({{ $tdPct }}%)
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

            @endif{{-- /!empty($ap) --}}

            {{-- ════════════════════════════════════════════════════════
                 ANÁLISE DA EQUIPE (Gerente + RH)
            ════════════════════════════════════════════════════════ --}}
            @if (auth()->user()->isGerente() || auth()->user()->isRhOuDp())
                @php $at = $this->analyticsTeam; @endphp
                @if (! empty($at))

                    {{-- Divisória --}}
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-6 flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-emerald-600
                                    flex items-center justify-center shadow shadow-emerald-400/30">
                            <x-lucide-users class="w-4.5 h-4.5 text-white" />
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white lato-bold">
                                Análise da Equipe — {{ $selectedYear }}
                            </h2>
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">Visão consolidada do desenvolvimento da sua equipe</p>
                        </div>
                    </div>

                    {{-- KPIs da equipe --}}
                    @php
                        $teamKpis = [
                            ['label'=>'Colaboradores', 'value'=> $at['totalMembers'], 'sub'=> $at['withPlan'].' com plano',     'icon'=>'users',       'bg'=>'bg-indigo-50 dark:bg-indigo-900/20',   'text'=>'text-indigo-600 dark:text-indigo-400'],
                            ['label'=>'Progresso médio','value'=> $at['avgProgress'].'%','sub'=> 'média do time',              'icon'=>'trending-up', 'bg'=>'bg-blue-50 dark:bg-blue-900/20',       'text'=>'text-blue-600 dark:text-blue-400'],
                            ['label'=>'Em risco',       'value'=> $at['atRiskCount'], 'sub'=> 'com ações atrasadas',           'icon'=>'alert-circle','bg'=> $at['atRiskCount']>0?'bg-red-50 dark:bg-red-900/20':'bg-emerald-50 dark:bg-emerald-900/20','text'=> $at['atRiskCount']>0?'text-red-600 dark:text-red-400':'text-emerald-600 dark:text-emerald-400'],
                            ['label'=>'Health Score',   'value'=> $at['healthScore'].'%','sub'=> 'índice de saúde do time',   'icon'=>'heart-pulse', 'bg'=> $at['healthScore']>=80?'bg-emerald-50 dark:bg-emerald-900/20':($at['healthScore']>=60?'bg-amber-50 dark:bg-amber-900/20':'bg-red-50 dark:bg-red-900/20'),'text'=> $at['healthScore']>=80?'text-emerald-600 dark:text-emerald-400':($at['healthScore']>=60?'text-amber-600 dark:text-amber-400':'text-red-600 dark:text-red-400')],
                        ];
                    @endphp
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                        @foreach ($teamKpis as $tk)
                            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                                <div class="flex items-center gap-2 mb-2">
                                    <div class="w-6 h-6 rounded-lg {{ $tk['bg'] }} flex items-center justify-center">
                                        <x-dynamic-component :component="'lucide-' . $tk['icon']" class="w-3.5 h-3.5 {{ $tk['text'] }}" />
                                    </div>
                                    <p class="text-[11px] text-slate-400 lato-regular">{{ $tk['label'] }}</p>
                                </div>
                                <p class="text-xl lato-black {{ $tk['text'] }}">{{ $tk['value'] }}</p>
                                <p class="text-[10px] text-slate-400 lato-regular mt-0.5">{{ $tk['sub'] }}</p>
                            </div>
                        @endforeach
                    </div>

                    {{-- Gráfico de progresso por colaborador --}}
                    @if (count($at['chartNames']) > 0)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                            <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-0.5">Progresso por colaborador</h3>
                            <p class="text-[11px] text-slate-400 lato-regular mb-3">Ordenado do mais avançado para o menos avançado</p>
                            <div wire:ignore
                                 x-data
                                 x-init="
                                    (function init() {
                                        if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                        var el = $el.querySelector('#dpi-an-team-progress');
                                        if (!el) return;
                                        if (el._chart) { el._chart.destroy(); }
                                        var dark = document.documentElement.classList.contains('dark');
                                        var h = Math.max(180, {{ count($at['chartNames']) }} * 44 + 60);
                                        el._chart = new ApexCharts(el, {
                                            chart:       { type: 'bar', height: h, toolbar: { show: false }, background: 'transparent' },
                                            series:      [{ name: 'Progresso (%)', data: {{ json_encode($at['chartProgress']) }} }],
                                            plotOptions: { bar: { horizontal: true, borderRadius: 5, barHeight: '55%',
                                                           dataLabels: { position: 'right' } } },
                                            dataLabels:  { enabled: true, formatter: function(val){ return val+'%'; },
                                                           style: { fontSize: '11px', colors: [dark?'#94a3b8':'#475569'] },
                                                           offsetX: 8 },
                                            xaxis:       { categories: {{ json_encode($at['chartNames']) }},
                                                           max: 100,
                                                           labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '11px' } } },
                                            yaxis:       { labels: { style: { colors: dark?'#94a3b8':'#64748b', fontSize: '11px' } } },
                                            colors:      ['#6366f1'],
                                            fill:        { type: 'gradient', gradient: { gradientToColors: ['#8b5cf6'], type: 'horizontal' } },
                                            grid:        { borderColor: dark?'#334155':'#f1f5f9', strokeDashArray: 4 },
                                            theme:       { mode: dark?'dark':'light' },
                                            tooltip:     { theme: dark?'dark':'light' },
                                        });
                                        el._chart.render();
                                    })();
                                 ">
                                <div id="dpi-an-team-progress"></div>
                            </div>
                        </div>
                    @endif

                    {{-- Tabela de membros com health score --}}
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                            <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200">Detalhamento por colaborador</h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Progresso, conclusões e saúde do plano individual</p>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="w-full text-xs lato-regular">
                                <thead>
                                    <tr class="border-b border-slate-100 dark:border-slate-700">
                                        <th class="text-left px-5 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Colaborador</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Status plano</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Progresso</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Concluídas</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Em andamento</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Atrasadas</th>
                                        <th class="text-center px-3 py-3 text-[11px] lato-bold text-slate-400 uppercase tracking-wider">Health</th>
                                        <th class="px-3 py-3"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                    @foreach ($at['rows'] as $tr)
                                        @php
                                            $trPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                            $trBg    = $trPal[abs(crc32($tr['name'])) % count($trPal)];
                                            $trParts = explode(' ', trim($tr['name']));
                                            $trInit  = strtoupper(substr($trParts[0],0,1).(isset($trParts[1])?substr($trParts[1],0,1):''));
                                            $trBar   = $tr['progress'] >= 75 ? 'bg-emerald-500' : ($tr['progress'] >= 40 ? 'bg-amber-400' : 'bg-slate-300 dark:bg-slate-600');
                                            $trSc    = match($tr['status']) {
                                                'aprovado'  => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400',
                                                'enviado'   => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400',
                                                'concluido' => 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400',
                                                'reprovado' => 'bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-400',
                                                default     => 'bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400',
                                            };
                                            $trStLbl = match($tr['status']) {
                                                'aprovado'  => 'Ativo',
                                                'enviado'   => 'Em revisão',
                                                'concluido' => 'Concluído',
                                                'reprovado' => 'Devolvido',
                                                default     => 'Rascunho',
                                            };
                                            $trHealth     = $tr['health'];
                                            $trHealthText = $trHealth >= 80 ? 'text-emerald-600 dark:text-emerald-400' : ($trHealth >= 60 ? 'text-amber-600 dark:text-amber-400' : 'text-red-600 dark:text-red-400');
                                            $trHealthBg   = $trHealth >= 80 ? 'bg-emerald-50 dark:bg-emerald-900/20' : ($trHealth >= 60 ? 'bg-amber-50 dark:bg-amber-900/20' : 'bg-red-50 dark:bg-red-900/20');
                                        @endphp
                                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                            <td class="px-5 py-3">
                                                <div class="flex items-center gap-2.5">
                                                    <span class="w-7 h-7 rounded-full {{ $trBg }} text-white text-[10px] lato-bold
                                                                 flex items-center justify-center shrink-0">{{ $trInit }}</span>
                                                    <span class="lato-bold text-slate-700 dark:text-slate-200 truncate max-w-[140px]">{{ $tr['name'] }}</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $trSc }}">{{ $trStLbl }}</span>
                                            </td>
                                            <td class="px-3 py-3">
                                                <div class="flex items-center gap-2 justify-center">
                                                    <div class="w-16 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                        <div class="h-full {{ $trBar }} rounded-full" style="width:{{ $tr['progress'] }}%"></div>
                                                    </div>
                                                    <span class="text-[11px] lato-bold text-slate-500 dark:text-slate-400 w-8">{{ $tr['progress'] }}%</span>
                                                </div>
                                            </td>
                                            <td class="px-3 py-3 text-center lato-bold text-emerald-600 dark:text-emerald-400">{{ $tr['done'] }}</td>
                                            <td class="px-3 py-3 text-center lato-bold text-blue-500 dark:text-blue-400">{{ $tr['in_prog'] }}</td>
                                            <td class="px-3 py-3 text-center">
                                                @if ($tr['overdue'] > 0)
                                                    <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400">
                                                        {{ $tr['overdue'] }}
                                                    </span>
                                                @else
                                                    <x-lucide-check class="w-3.5 h-3.5 text-emerald-400 mx-auto" />
                                                @endif
                                            </td>
                                            <td class="px-3 py-3 text-center">
                                                <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $trHealthBg }} {{ $trHealthText }}">
                                                    {{ $trHealth }}%
                                                </span>
                                            </td>
                                            <td class="px-3 py-3">
                                                <button wire:click="goToTeamUser({{ $tr['user_id'] }})"
                                                        type="button"
                                                        class="text-[11px] lato-bold text-indigo-500 hover:text-indigo-700
                                                               dark:text-indigo-400 dark:hover:text-indigo-300 transition cursor-pointer
                                                               flex items-center gap-1">
                                                    <x-lucide-arrow-right class="w-3 h-3" /> Ver
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        {{-- Sem planos --}}
                        @php $noPlan = $at['totalMembers'] - $at['withPlan']; @endphp
                        @if ($noPlan > 0)
                            <div class="px-5 py-3 border-t border-slate-100 dark:border-slate-700
                                        bg-amber-50/50 dark:bg-amber-900/10
                                        flex items-center gap-2 text-[11px] text-amber-700 dark:text-amber-400 lato-regular">
                                <x-lucide-alert-triangle class="w-3.5 h-3.5 shrink-0" />
                                {{ $noPlan }} {{ $noPlan === 1 ? 'colaborador não tem' : 'colaboradores não têm' }} plano em {{ $selectedYear }}
                                — acesse a aba <strong class="lato-bold ml-0.5">Equipe</strong> para criar.
                            </div>
                        @endif

                    </div>{{-- /tabela membros --}}

                @endif{{-- /!empty($at) --}}
            @endif{{-- /isGerente or isRhOuDp --}}

        </div>{{-- /space-y-6 --}}
    @endif{{-- /activeTab === analises --}}

