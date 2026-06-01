    {{-- ════════════════════════════════════════════════════════════════
         ABA: VISUALIZAÇÕES ALTERNATIVAS (Gantt + Radar)
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'visualizacoes')
        @php
            $vp      = $this->vizPlan;
            $gd      = $this->ganttData;
            $rd      = $this->radarCompetenciasData;
            $canPick = auth()->user()->isRhOuDp() || auth()->user()->isGerente();
        @endphp

        <div class="space-y-6">

            {{-- ── Cabeçalho + seletor de usuário + toggle Gantt/Radar ── --}}
            <div class="flex flex-wrap items-center justify-between gap-3">
                <div class="flex items-center gap-2">
                    <x-lucide-calendar-days class="w-5 h-5 text-indigo-500" />
                    <h2 class="text-lg lato-bold text-slate-800 dark:text-slate-100">Visualizações Alternativas</h2>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    {{-- Seletor de colaborador (RH / Gerente) — dropdown customizado --}}
                    @if ($canPick)
                        @php
                            $tmOptions = $this->teamMembers->map(fn($tm) => ['id' => $tm->id, 'name' => $tm->name])->values()->toArray();
                        @endphp
                        <div class="relative"
                             x-data="{
                                open: false,
                                selectedId: @entangle('vizUserId').live,
                                options: @js($tmOptions),
                                get selectedLabel() {
                                    if (!this.selectedId) return '— Meu plano —';
                                    const opt = this.options.find(o => String(o.id) === String(this.selectedId));
                                    return opt ? opt.name : '— Meu plano —';
                                },
                                select(id) {
                                    this.selectedId = id;
                                    this.open = false;
                                }
                             }"
                             x-on:keydown.escape="open = false"
                             x-on:click.outside="open = false">

                            {{-- Botão trigger --}}
                            <button type="button"
                                    x-on:click="open = !open"
                                    class="flex items-center gap-2 px-3 py-1.5 rounded-lg border text-sm font-medium
                                           shadow-sm cursor-pointer select-none transition-all duration-150
                                           border-slate-200 dark:border-slate-600
                                           bg-white dark:bg-slate-800
                                           text-slate-700 dark:text-slate-200
                                           hover:border-indigo-400 dark:hover:border-indigo-500
                                           hover:bg-slate-50 dark:hover:bg-slate-700"
                                    x-bind:class="open ? 'border-indigo-400 ring-2 ring-indigo-300 dark:ring-indigo-700' : ''">
                                <x-lucide-user class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                                <span x-text="selectedLabel" class="max-w-[160px] truncate"></span>
                                <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 dark:text-slate-500 shrink-0 transition-transform duration-200"
                                                       x-bind:class="open ? 'rotate-180' : ''" />
                            </button>

                            {{-- Lista de opções --}}
                            <div x-show="open"
                                 x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 -translate-y-1 scale-95"
                                 x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                                 x-transition:leave-end="opacity-0 -translate-y-1 scale-95"
                                 class="absolute left-0 top-full mt-1.5 z-50 min-w-full w-max max-w-xs
                                        rounded-xl border border-slate-200 dark:border-slate-600
                                        bg-white dark:bg-slate-800
                                        shadow-lg dark:shadow-slate-900/60
                                        overflow-hidden"
                                 style="display:none">

                                {{-- Meu plano --}}
                                <button type="button"
                                        x-on:click="select('')"
                                        class="flex items-center gap-2.5 w-full px-3.5 py-2.5 text-sm text-left
                                               transition-colors duration-100 cursor-pointer group"
                                        x-bind:class="!selectedId
                                            ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-semibold'
                                            : 'text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700/60 font-normal'">
                                    <x-lucide-star class="w-3.5 h-3.5 shrink-0 text-indigo-400" />
                                    <span>— Meu plano —</span>
                                    <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500 transition-opacity"
                                                    x-bind:class="!selectedId ? 'opacity-100' : 'opacity-0'" />
                                </button>

                                {{-- Divider --}}
                                <div class="h-px bg-slate-100 dark:bg-slate-700 mx-2"></div>

                                {{-- Membros --}}
                                <template x-for="opt in options" x-bind:key="opt.id">
                                    <button type="button"
                                            x-on:click="select(opt.id)"
                                            class="flex items-center gap-2.5 w-full px-3.5 py-2.5 text-sm text-left
                                                   transition-colors duration-100 cursor-pointer"
                                            x-bind:class="String(selectedId) === String(opt.id)
                                                ? 'bg-indigo-50 dark:bg-indigo-900/40 text-indigo-700 dark:text-indigo-300 font-semibold'
                                                : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700/60 font-normal'">
                                        <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-900/50
                                                    flex items-center justify-center shrink-0">
                                            <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase leading-none"
                                                  x-text="opt.name.charAt(0)"></span>
                                        </div>
                                        <span x-text="opt.name" class="truncate"></span>
                                        <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500 transition-opacity shrink-0"
                                                        x-bind:class="String(selectedId) === String(opt.id) ? 'opacity-100' : 'opacity-0'" />
                                    </button>
                                </template>
                            </div>
                        </div>
                    @endif

                    {{-- Toggle Gantt / Radar --}}
                    <div class="inline-flex rounded-lg border border-slate-200 dark:border-slate-600 overflow-hidden text-sm">
                        <button wire:click="$set('vizView','gantt')" type="button"
                                class="flex items-center gap-1.5 px-3 py-1.5 transition cursor-pointer
                                       {{ $vizView === 'gantt'
                                           ? 'bg-indigo-600 text-white'
                                           : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <x-lucide-gantt-chart class="w-3.5 h-3.5" /> Linha do Tempo
                        </button>
                        <button wire:click="$set('vizView','radar')" type="button"
                                class="flex items-center gap-1.5 px-3 py-1.5 transition cursor-pointer
                                       {{ $vizView === 'radar'
                                           ? 'bg-indigo-600 text-white'
                                           : 'bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                            <x-lucide-radar class="w-3.5 h-3.5" /> Radar
                        </button>
                    </div>
                </div>
            </div>

            {{-- Sem plano --}}
            @if (! $vp)
                <div class="flex flex-col items-center justify-center py-16 text-slate-400 dark:text-slate-500 gap-3">
                    <x-lucide-file-search class="w-12 h-12 opacity-40" />
                    <p class="text-sm">Nenhum plano DPI encontrado para {{ $selectedYear }}.</p>
                </div>

            {{-- ──────────── GANTT (ApexGantt) ──────────── --}}
            @elseif ($vizView === 'gantt')
                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm overflow-hidden">

                    {{-- Cabeçalho --}}
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">Linha do Tempo — {{ $selectedYear }}</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                {{ count($gd['series']) }} tarefas
                                @if ($gd['no_date_count'] > 0)
                                    · <span class="text-amber-500">{{ $gd['no_date_count'] }} ações sem prazo (não exibidas)</span>
                                @endif
                            </p>
                        </div>
                        {{-- Legenda de cores --}}
                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500 dark:text-slate-400">
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#6366f1] inline-block"></span> Objetivo</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#10b981] inline-block"></span> Concluída</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#3b82f6] inline-block"></span> Em andamento</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#94a3b8] inline-block"></span> Pendente</span>
                            <span class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-sm bg-[#ef4444] inline-block"></span> Atrasada</span>
                        </div>
                    </div>

                    {{-- Gráfico --}}
                    @if (empty($gd['series']))
                        <div class="flex flex-col items-center justify-center py-14 text-slate-400 dark:text-slate-500 gap-3">
                            <x-lucide-calendar-x-2 class="w-10 h-10 opacity-40" />
                            <p class="text-sm">Nenhuma ação com prazo definido encontrada.</p>
                        </div>
                    @else
                        @php
                            $ganttRowCount = count($gd['series']);
                            $ganttH        = max(500, $ganttRowCount * 42 + 100);
                        @endphp
                        <div wire:ignore
                             x-data="{ ganttSeries: @js($gd['series']) }"
                             x-init="
                                (function init() {
                                    if (typeof ApexGantt === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#gantt-viz-dpi');
                                    if (!el) return;
                                    if (el._gantt && !el._gantt.isDestroyed()) { el._gantt.destroy(); }
                                    var _stored = localStorage.getItem('theme');
                                    var dark = _stored === 'dark' ||
                                               (!_stored && window.matchMedia('(prefers-color-scheme: dark)').matches) ||
                                               document.documentElement.classList.contains('dark');

                                    var lightTheme = {
                                        backgroundColor:   '#ffffff',
                                        headerBackground:  '#f1f5f9',
                                        fontColor:         '#334155',
                                        borderColor:       '#e2e8f0',
                                        cellBorderColor:   '#e2e8f0',
                                        barTextColor:      '#ffffff',
                                        arrowColor:        '#6366f1',
                                        tooltipBGColor:    '#ffffff',
                                        tooltipBorderColor:'#cbd5e1',
                                        rowBackgroundColors: ['#ffffff', '#f8fafc'],
                                        annotationBgColor:   '#e0e7ff',
                                        annotationBorderColor: '#6366f1',
                                    };
                                    var darkTheme = {
                                        backgroundColor:   '#1e293b',
                                        headerBackground:  '#0f172a',
                                        fontColor:         '#cbd5e1',
                                        borderColor:       '#334155',
                                        cellBorderColor:   '#334155',
                                        barTextColor:      '#ffffff',
                                        arrowColor:        '#818cf8',
                                        tooltipBGColor:    '#1e293b',
                                        tooltipBorderColor:'#475569',
                                        rowBackgroundColors: ['#1e293b', '#172033'],
                                        annotationBgColor:   '#312e81',
                                        annotationBorderColor: '#818cf8',
                                    };
                                    var t = dark ? darkTheme : lightTheme;

                                    el._gantt = new ApexGantt(el, {
                                        series: $data.ganttSeries,
                                        inputDateFormat: 'YYYY-MM-DD',
                                        theme: dark ? 'dark' : 'light',
                                        height: {{ $ganttH }},
                                        fontFamily: 'Lato, sans-serif',
                                        fontSize: '13px',
                                        rowHeight: 40,
                                        tasksContainerWidth: 320,
                                        barMargin: 5,
                                        barBorderRadius: '5px',
                                        enableTaskDrag: false,
                                        enableTaskResize: false,
                                        enableTaskEdit: false,
                                        enableExport: false,
                                        enableTooltip: true,
                                        toolbarItems: [],
                                        backgroundColor:       t.backgroundColor,
                                        headerBackground:      t.headerBackground,
                                        fontColor:             t.fontColor,
                                        borderColor:           t.borderColor,
                                        cellBorderColor:       t.cellBorderColor,
                                        cellBorderWidth:       '1px',
                                        barTextColor:          t.barTextColor,
                                        arrowColor:            t.arrowColor,
                                        tooltipBGColor:        t.tooltipBGColor,
                                        tooltipBorderColor:    t.tooltipBorderColor,
                                        rowBackgroundColors:   t.rowBackgroundColors,
                                        annotationBgColor:     t.annotationBgColor,
                                        annotationBorderColor: t.annotationBorderColor,
                                        annotations: [{
                                            x1: new Date().toISOString().slice(0, 10),
                                            label: { text: 'Hoje', fontColor: dark ? '#f1f5f9' : '#1e293b', fontSize: '11px', fontWeight: 'bold' },
                                        }],
                                    });
                                    el._gantt.render();
                                })()
                             ">
                            <div id="gantt-viz-dpi" style="min-height:{{ $ganttH }}px;"></div>
                            <style>
                                /* Oculta toolbar de zoom nativo do ApexGantt */
                                #gantt-viz-dpi .apexgantt-toolbar { display: none !important; }
                            </style>
                        </div>
                    @endif

                </div>

            {{-- ──────────── RADAR DE COMPETÊNCIAS ──────────── --}}
            @elseif ($vizView === 'radar')
                @if (empty($rd))
                    <div class="flex flex-col items-center justify-center py-16 text-slate-400 dark:text-slate-500 gap-3">
                        <x-lucide-target class="w-12 h-12 opacity-40" />
                        <p class="text-sm">Nenhuma competência cadastrada no plano.</p>
                    </div>
                @else
                    <div class="grid grid-cols-1 xl:grid-cols-5 gap-6">

                        {{-- Radar chart (col. esquerda, ocupa 2 de 5) --}}
                        <div class="xl:col-span-2 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-5 flex flex-col">
                            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 mb-1">Radar de Competências</h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">Nível atual vs. meta por competência</p>

                            @php
                                $radarVizLabels  = array_values(array_column($rd, 'short'));
                                $radarVizCurrent = array_values(array_map(fn($v) => (float)($v ?? 0), array_column($rd, 'nivel_atual')));
                                $radarVizTarget  = array_values(array_map(fn($v) => (float)($v ?? 0), array_column($rd, 'nivel_meta')));
                                $radarCanRender  = count($radarVizLabels) >= 3;
                            @endphp

                            @if (! $radarCanRender)
                                <div class="flex flex-col items-center justify-center flex-1 py-10 text-slate-400 dark:text-slate-500 gap-2">
                                    <x-lucide-triangle-alert class="w-8 h-8 opacity-40" />
                                    <p class="text-xs text-center">O radar requer pelo menos 3 competências.<br>Adicione mais objetivos ao plano.</p>
                                </div>
                            @else
                            <div wire:ignore
                                 x-data="{ radarCurrent: @js($radarVizCurrent), radarTarget: @js($radarVizTarget), radarLabels: @js($radarVizLabels) }"
                                 x-init="
                                    (function init() {
                                        if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                        var el = $el.querySelector('#radar-viz-dpi');
                                        if (!el) return;
                                        if (el._chart) { el._chart.destroy(); }
                                        var dark = document.documentElement.classList.contains('dark');
                                        var cur = $data.radarCurrent.map(function(v) { return isNaN(v) || v === null ? 0 : Number(v); });
                                        var tgt = $data.radarTarget.map(function(v)  { return isNaN(v) || v === null ? 0 : Number(v); });
                                        el._chart = new ApexCharts(el, {
                                            series: [
                                                { name: 'Nível Atual', data: cur },
                                                { name: 'Meta',        data: tgt },
                                            ],
                                            chart: {
                                                type: 'radar',
                                                height: 380,
                                                toolbar: { show: false },
                                                background: 'transparent',
                                                fontFamily: 'Lato, sans-serif',
                                            },
                                            xaxis: { categories: $data.radarLabels },
                                            yaxis: { min: 0, max: 5, tickAmount: 5, show: false },
                                            stroke: { width: [2, 2], dashArray: [0, 5] },
                                            fill:   { opacity: [0.25, 0.08] },
                                            colors: ['#6366f1', '#f59e0b'],
                                            markers: { size: [4, 4] },
                                            legend: {
                                                show: true,
                                                position: 'bottom',
                                                labels: { colors: dark ? '#cbd5e1' : '#475569' },
                                            },
                                            tooltip: {
                                                y: { formatter: function(v) { return 'Nível ' + v; } }
                                            },
                                            plotOptions: {
                                                radar: {
                                                    polygons: {
                                                        strokeColors: dark ? '#334155' : '#e2e8f0',
                                                        connectorColors: dark ? '#334155' : '#e2e8f0',
                                                        fill: { colors: [dark ? '#1e293b' : '#f8fafc', dark ? '#0f172a' : '#f1f5f9'] },
                                                    }
                                                }
                                            },
                                            theme: { mode: dark ? 'dark' : 'light' },
                                        });
                                        el._chart.render();
                                    })()
                                 "
                                 class="flex-1">
                                <div id="radar-viz-dpi" style="min-height:380px;"></div>
                            </div>
                            @endif
                        </div>

                        {{-- Cards de competência (col. direita, ocupa 3 de 5) --}}
                        <div class="xl:col-span-3 space-y-4">
                            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">Detalhamento por Competência</h3>

                            @foreach ($rd as $comp)
                                @php
                                    $prioColor = match($comp['priority']) {
                                        'alta'  => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',
                                        'media' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        default => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                                    };
                                    $prioLabel = ['alta' => 'Alta', 'media' => 'Média', 'baixa' => 'Baixa'][$comp['priority']] ?? $comp['priority'];
                                @endphp
                                <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-sm p-4">

                                    {{-- Nome + prioridade + gap --}}
                                    <div class="flex items-start justify-between gap-3 mb-3">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $comp['name'] }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <span class="text-xs px-2 py-0.5 rounded-full {{ $prioColor }}">{{ $prioLabel }}</span>
                                            @if ($comp['gap'] > 0)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-400">
                                                    Gap +{{ $comp['gap'] }}
                                                </span>
                                            @else
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">
                                                    Meta atingida
                                                </span>
                                            @endif
                                        </div>
                                    </div>

                                    {{-- Nível: 5 segmentos preenchidos --}}
                                    <div class="flex items-center gap-2 mb-3">
                                        <span class="text-xs text-slate-500 dark:text-slate-400 w-16 shrink-0">Nível atual</span>
                                        <div class="flex gap-1">
                                            @for ($dot = 1; $dot <= 5; $dot++)
                                                <div class="w-6 h-2.5 rounded-sm transition-colors
                                                    {{ $dot <= $comp['nivel_atual']
                                                        ? 'bg-indigo-500'
                                                        : ($dot <= $comp['nivel_meta']
                                                            ? 'bg-indigo-200 dark:bg-indigo-900/50'
                                                            : 'bg-slate-100 dark:bg-slate-700') }}">
                                                </div>
                                            @endfor
                                        </div>
                                        <span class="text-xs text-slate-500 dark:text-slate-400">
                                            {{ $comp['nivel_atual'] }}/{{ $comp['nivel_meta'] }}
                                        </span>
                                    </div>

                                    {{-- Progresso das ações --}}
                                    @if ($comp['total'] > 0)
                                        <div class="mb-2">
                                            <div class="flex justify-between text-xs text-slate-500 dark:text-slate-400 mb-1">
                                                <span>Ações: {{ $comp['done'] }}/{{ $comp['total'] }} concluídas</span>
                                                <span class="lato-bold {{ $comp['progress'] >= 75 ? 'text-emerald-600' : ($comp['progress'] >= 40 ? 'text-indigo-600' : 'text-slate-500') }}">
                                                    {{ $comp['progress'] }}%
                                                </span>
                                            </div>
                                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2">
                                                <div class="h-2 rounded-full transition-all
                                                    {{ $comp['progress'] >= 75 ? 'bg-emerald-500' : ($comp['progress'] >= 40 ? 'bg-indigo-500' : 'bg-slate-400') }}"
                                                     style="width:{{ $comp['progress'] }}%">
                                                </div>
                                            </div>
                                        </div>

                                        {{-- Mini contadores --}}
                                        <div class="flex flex-wrap gap-2 mt-2">
                                            @if ($comp['in_prog'] > 0)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400">
                                                    {{ $comp['in_prog'] }} em andamento
                                                </span>
                                            @endif
                                            @if ($comp['pending'] > 0)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                                    {{ $comp['pending'] }} pendente{{ $comp['pending'] > 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                            @if ($comp['overdue'] > 0)
                                                <span class="text-xs px-2 py-0.5 rounded-full bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                                    ⚠ {{ $comp['overdue'] }} atrasada{{ $comp['overdue'] > 1 ? 's' : '' }}
                                                </span>
                                            @endif
                                        </div>
                                    @else
                                        <p class="text-xs text-slate-400 dark:text-slate-500 italic">Nenhuma ação cadastrada.</p>
                                    @endif

                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endif

        </div>{{-- /space-y-6 --}}
    @endif{{-- /activeTab === visualizacoes --}}
