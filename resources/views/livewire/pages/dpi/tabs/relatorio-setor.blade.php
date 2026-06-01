    {{-- ════════════════════════════════════════════════════════════════
         ABA: RELATÓRIO DO SETOR (Gerente)
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'relatorio_setor' && auth()->user()->isGerente())
        @php
            $gs = $this->reportSetorStats;
            $gUser = auth()->user();
        @endphp

        <div class="space-y-5">

            {{-- ── Cabeçalho do setor ─────────────────────────────────── --}}
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-100 to-violet-100
                            dark:from-indigo-900/30 dark:to-violet-900/30 flex items-center justify-center shrink-0">
                    <x-lucide-building-2 class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                </div>
                <div>
                    <h2 class="text-base font-bold text-slate-800 dark:text-white lato-bold">
                        Relatório do Setor — {{ $selectedYear }}
                    </h2>
                    <p class="text-xs text-slate-400 lato-regular">
                        {{ $gUser->department?->name ?? 'Seu departamento' }} · {{ $gs['total_members'] ?? 0 }} colaborador(es) direto(s)
                    </p>
                </div>
            </div>

            {{-- ── KPI Cards ─────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

                {{-- Colaboradores com plano --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div class="flex-1">
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">
                            {{ $gs['with_plan'] ?? 0 }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $gs['total_members'] ?? 0 }}</span>
                        </p>
                        <p class="text-xs text-slate-400 lato-regular">Com plano criado</p>
                        @if (($gs['total_members'] ?? 0) > 0)
                            <div class="mt-1.5 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full"
                                     style="width: {{ round(($gs['with_plan'] / $gs['total_members']) * 100) }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Progresso médio --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-trending-up class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $gs['avg_progress'] ?? 0 }}%</p>
                        <p class="text-xs text-slate-400 lato-regular">Progresso médio</p>
                    </div>
                </div>

                {{-- Ações concluídas --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-check-circle class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">
                            {{ $gs['done_actions'] ?? 0 }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $gs['total_actions'] ?? 0 }}</span>
                        </p>
                        <p class="text-xs text-slate-400 lato-regular">Ações concluídas</p>
                    </div>
                </div>

                {{-- Planos ativos --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-teal-100 dark:bg-teal-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-shield-check class="w-4 h-4 text-teal-600 dark:text-teal-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $gs['aprovado'] ?? 0 }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Planos ativos</p>
                    </div>
                </div>

                {{-- Sem plano --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl {{ ($gs['without_plan'] ?? 0) > 0 ? 'bg-red-100 dark:bg-red-900/30' : 'bg-slate-100 dark:bg-slate-700' }} flex items-center justify-center shrink-0">
                        <x-lucide-user-x class="w-4 h-4 {{ ($gs['without_plan'] ?? 0) > 0 ? 'text-red-500' : 'text-slate-400' }}" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black {{ ($gs['without_plan'] ?? 0) > 0 ? 'text-red-500' : 'text-slate-800 dark:text-white' }}">{{ $gs['without_plan'] ?? 0 }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Sem plano</p>
                    </div>
                </div>

            </div>

            {{-- ── Gráfico de status + Ações por status ───────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Donut: distribuição de planos por status --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold mb-4">
                        Status dos planos
                    </h3>
                    @if (($gs['with_plan'] ?? 0) > 0)
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-setor-status-chart');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'donut', height: 180, background: 'transparent', toolbar: { show: false } },
                                        series:      [{{ $gs['rascunho'] }}, {{ $gs['enviado'] }}, {{ $gs['aprovado'] }}, {{ $gs['reprovado'] }}, {{ $gs['concluido'] }}],
                                        labels:      ['Em preparação','Aguardando','Ativos','Devolvidos','Concluídos'],
                                        colors:      ['#94a3b8','#f59e0b','#10b981','#ef4444','#14b8a6'],
                                        legend:      { position: 'bottom', labels: { colors: dark ? '#94a3b8' : '#64748b' }, fontSize: '11px' },
                                        dataLabels:  { style: { fontSize: '10px' } },
                                        plotOptions: { pie: { donut: { size: '60%' } } },
                                        theme:       { mode: dark ? 'dark' : 'light' },
                                        tooltip:     { theme: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })()
                             ">
                            <div id="dpi-setor-status-chart" style="min-height:180px;"></div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-pie-chart class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum plano em {{ $selectedYear }}</p>
                        </div>
                    @endif

                    {{-- Mini-legenda com contagens --}}
                    <div class="mt-3 space-y-1.5">
                        @foreach ([
                            ['Em preparação', 'bg-slate-400',   $gs['rascunho']  ?? 0],
                            ['Aguardando',    'bg-amber-400',   $gs['enviado']   ?? 0],
                            ['Ativos',        'bg-emerald-500', $gs['aprovado']  ?? 0],
                            ['Devolvidos',    'bg-red-400',     $gs['reprovado'] ?? 0],
                            ['Concluídos',    'bg-teal-500',    $gs['concluido'] ?? 0],
                        ] as [$lbl, $dot, $cnt])
                            <div class="flex items-center justify-between text-xs lato-regular">
                                <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                    <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span>{{ $lbl }}
                                </span>
                                <span class="lato-bold text-slate-700 dark:text-slate-200">{{ $cnt }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Donut: distribuição de ações por status --}}
                @php $gas = $this->reportSetorActionStatus; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold mb-4">
                        Status das ações
                    </h3>
                    @if (($gas['total'] ?? 0) > 0)
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-setor-actions-chart');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'donut', height: 180, background: 'transparent', toolbar: { show: false } },
                                        series:      [{{ $gas['pendente'] }}, {{ $gas['em_andamento'] }}, {{ $gas['concluido'] }}],
                                        labels:      ['Pendente','Em andamento','Concluída'],
                                        colors:      ['#cbd5e1','#6366f1','#10b981'],
                                        legend:      { position: 'bottom', labels: { colors: dark ? '#94a3b8' : '#64748b' }, fontSize: '11px' },
                                        dataLabels:  { style: { fontSize: '10px' } },
                                        plotOptions: { pie: { donut: { size: '60%' } } },
                                        theme:       { mode: dark ? 'dark' : 'light' },
                                        tooltip:     { theme: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })()
                             ">
                            <div id="dpi-setor-actions-chart" style="min-height:180px;"></div>
                        </div>

                        {{-- Totalizadores --}}
                        <div class="mt-3 space-y-1.5">
                            @foreach ([
                                ['Pendentes',    'bg-slate-300',   $gas['pendente']],
                                ['Em andamento', 'bg-indigo-500',  $gas['em_andamento']],
                                ['Concluídas',   'bg-emerald-500', $gas['concluido']],
                            ] as [$lbl, $dot, $cnt])
                                <div class="flex items-center justify-between text-xs lato-regular">
                                    <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                                        <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span>{{ $lbl }}
                                    </span>
                                    <span class="lato-bold text-slate-700 dark:text-slate-200">{{ $cnt }}</span>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-bar-chart-2 class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhuma ação registrada</p>
                        </div>
                    @endif
                </div>

                {{-- Ranking de progresso do setor --}}
                @php $grank = $this->reportSetorRanking; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Ranking do setor</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Progresso por colaborador</p>
                    </div>

                    @if ($grank['top']->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-trophy class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum plano ativo em {{ $selectedYear }}</p>
                        </div>
                    @else
                        <div class="px-5 py-4 space-y-3">
                            @foreach ($grank['top'] as $i => $row)
                                @php
                                    $grParts = explode(' ', trim($row['plan']->user->name));
                                    $grInit  = strtoupper(substr($grParts[0],0,1).(isset($grParts[1])?substr($grParts[1],0,1):''));
                                    $grPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                    $grBg    = $grPal[abs(crc32($row['plan']->user->name)) % count($grPal)];
                                    $grPct   = $row['progress'];
                                    $grBar   = $grPct >= 75 ? 'bg-emerald-500' : ($grPct >= 40 ? 'bg-amber-400' : 'bg-slate-400');
                                @endphp
                                <div class="flex items-center gap-3">
                                    <span class="text-[11px] lato-bold text-slate-300 dark:text-slate-600 w-4 text-right shrink-0">{{ $i+1 }}</span>
                                    <span class="w-7 h-7 rounded-full {{ $grBg }} text-white text-[10px] lato-bold
                                                 flex items-center justify-center shrink-0">{{ $grInit }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $row['plan']->user->name }}</p>
                                        <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden mt-1">
                                            <div class="h-full {{ $grBar }} rounded-full" style="width:{{ $grPct }}%"></div>
                                        </div>
                                    </div>
                                    <span class="text-xs lato-bold w-9 text-right shrink-0
                                                 {{ $grPct >= 75 ? 'text-emerald-600 dark:text-emerald-400' : ($grPct >= 40 ? 'text-amber-600 dark:text-amber-400' : 'text-slate-400') }}">
                                        {{ $grPct }}%
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- ── Prazos vencidos + Sem plano ─────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Ações com prazo vencido --}}
                @php $govd = $this->reportSetorOverdue; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold flex items-center gap-2">
                                <x-lucide-alarm-clock class="w-4 h-4 text-red-500" />
                                Prazos vencidos no setor
                            </h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Ações não concluídas após a data limite</p>
                        </div>
                        @if ($govd->isNotEmpty())
                            <span class="px-2.5 py-1 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs lato-bold">
                                {{ $govd->count() }}
                            </span>
                        @endif
                    </div>

                    @if ($govd->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-check-circle class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum prazo vencido!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 dark:divide-slate-700/50 max-h-72 overflow-y-auto">
                            @foreach ($govd as $gact)
                                @php $gdLate = now()->diffInDays($gact->target_date, false) * -1; @endphp
                                <div class="flex items-start gap-3 px-5 py-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0 mt-0.5">
                                        <x-lucide-clock class="w-3.5 h-3.5 text-red-500" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $gact->title }}</p>
                                        <p class="text-[11px] text-slate-400 lato-regular truncate">
                                            {{ $gact->goal->plan->user->name }}
                                        </p>
                                        <p class="text-[11px] text-red-500 lato-bold mt-0.5 flex items-center gap-1">
                                            <x-lucide-calendar-x class="w-3 h-3" />
                                            {{ $gact->target_date->format('d/m/Y') }} · {{ $gdLate }}d atrasada
                                        </p>
                                    </div>
                                    <span class="text-[10px] lato-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 shrink-0">
                                        {{ $gact->type_label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Colaboradores sem plano --}}
                @php $gnp = $this->reportSetorWithoutPlan; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold flex items-center gap-2">
                                <x-lucide-user-x class="w-4 h-4 text-slate-400" />
                                Sem plano em {{ $selectedYear }}
                            </h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Colaboradores do setor sem DPI cadastrado</p>
                        </div>
                        @if ($gnp->isNotEmpty())
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold">
                                {{ $gnp->count() }}
                            </span>
                        @endif
                    </div>

                    @if ($gnp->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-check-circle class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Todos têm plano em {{ $selectedYear }}!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 dark:divide-slate-700/50 max-h-72 overflow-y-auto">
                            @foreach ($gnp as $gnu)
                                @php
                                    $gnParts = explode(' ', trim($gnu->name));
                                    $gnInit  = strtoupper(substr($gnParts[0],0,1).(isset($gnParts[1])?substr($gnParts[1],0,1):''));
                                    $gnPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                    $gnBg    = $gnPal[abs(crc32($gnu->name)) % count($gnPal)];
                                @endphp
                                <div class="flex items-center gap-3 px-5 py-3">
                                    <span class="w-8 h-8 rounded-full {{ $gnBg }} text-white text-xs lato-bold
                                                 flex items-center justify-center shrink-0">{{ $gnInit }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $gnu->name }}</p>
                                        <p class="text-[11px] text-slate-400 lato-regular">{{ $gnu->accessProfile?->name ?? '—' }}</p>
                                    </div>
                                    <button wire:click="createPlanForEmployee({{ $gnu->id }})" type="button"
                                            title="Criar plano para este colaborador"
                                            class="flex items-center gap-1 text-[11px] lato-bold text-indigo-500 hover:text-indigo-700
                                                   dark:text-indigo-400 dark:hover:text-indigo-300 transition cursor-pointer">
                                        <x-lucide-plus class="w-3 h-3" /> Criar plano
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- ── Tabela individual de planos do setor ─────────────────── --}}
            @php $gplans = $this->reportSetorPlans; @endphp
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col gap-3">
                    <div class="flex items-center justify-between">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">
                            Planos individuais
                            <span class="ml-1 text-xs font-normal text-slate-400 lato-regular">({{ $gplans->count() }})</span>
                        </h3>
                    </div>

                    {{-- Linha de filtros --}}
                    <div class="flex flex-col gap-2.5">

                        {{-- Busca --}}
                        <div class="relative w-full">
                            <x-lucide-search class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                            <input type="text" wire:model.live.debounce.300ms="gerenteReportSearch"
                                   placeholder="Buscar colaborador..."
                                   class="w-full pl-9 pr-8 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                                          placeholder-slate-400 transition" />
                            @if ($gerenteReportSearch)
                                <button wire:click="$set('gerenteReportSearch', '')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>

                        {{-- Dropdowns --}}
                        <div class="grid grid-cols-2 gap-2">

                            {{-- Filtro Status --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button"
                                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                        {{ $gerenteReportStatus ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    <div class="flex items-center gap-1.5 truncate">
                                        @php
                                            $gStatusDot = match($gerenteReportStatus) {
                                                'aprovado'  => 'bg-emerald-400',
                                                'reprovado' => 'bg-red-400',
                                                'enviado'   => 'bg-amber-400',
                                                'concluido' => 'bg-teal-400',
                                                'rascunho'  => 'bg-slate-400',
                                                default     => null,
                                            };
                                        @endphp
                                        @if ($gStatusDot)
                                            <span class="w-2 h-2 rounded-full {{ $gStatusDot }} flex-shrink-0"></span>
                                        @else
                                            <x-lucide-circle-check class="w-3.5 h-3.5 flex-shrink-0" />
                                        @endif
                                        <span class="truncate text-xs lato-bold">
                                            {{ match($gerenteReportStatus) {
                                                'rascunho'  => 'Em preparação',
                                                'enviado'   => 'Aguardando',
                                                'aprovado'  => 'Ativos',
                                                'reprovado' => 'Devolvidos',
                                                'concluido' => 'Concluídos',
                                                default     => 'Status',
                                            } }}
                                        </span>
                                    </div>
                                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                                        x-bind:class="open ? 'rotate-180' : ''" />
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                     class="absolute mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                                    <button @click="open=false; $wire.set('gerenteReportStatus', '')" type="button"
                                            class="w-full text-left px-3 py-2 text-xs lato-regular hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                        <x-lucide-circle-check class="w-3.5 h-3.5" /> Todos os status
                                    </button>
                                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                    @foreach ([
                                        'rascunho'  => ['Em preparação', 'bg-slate-400'],
                                        'enviado'   => ['Aguardando',    'bg-amber-400'],
                                        'aprovado'  => ['Ativos',        'bg-emerald-400'],
                                        'reprovado' => ['Devolvidos',    'bg-red-400'],
                                        'concluido' => ['Concluídos',    'bg-teal-400'],
                                    ] as $val => [$label, $dot])
                                        <button @click="open=false; $wire.set('gerenteReportStatus', '{{ $val }}')" type="button"
                                                class="w-full text-left px-3 py-2 text-xs lato-regular flex items-center gap-2 transition
                                                {{ $gerenteReportStatus === $val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                            <span class="w-2 h-2 rounded-full {{ $dot }} flex-shrink-0"></span>
                                            {{ $label }}
                                            @if ($gerenteReportStatus === $val)
                                                <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" />
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Limpar filtros --}}
                            @if ($gerenteReportSearch || $gerenteReportStatus)
                                <button wire:click="clearGerenteReportFilters" type="button"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs lato-bold
                                               text-red-400 hover:text-red-600 border border-red-200 dark:border-red-900/50
                                               hover:border-red-400 rounded-xl bg-red-50 dark:bg-red-900/10
                                               hover:bg-red-100 dark:hover:bg-red-900/20 transition cursor-pointer">
                                    <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar filtros
                                </button>
                            @endif
                        </div>

                        {{-- Chips de filtros ativos --}}
                        @if ($gerenteReportSearch || $gerenteReportStatus)
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] text-slate-400 lato-regular">Filtros ativos:</span>
                                @if ($gerenteReportSearch)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] lato-bold">
                                        <x-lucide-search class="w-2.5 h-2.5" />
                                        "{{ Str::limit($gerenteReportSearch, 18) }}"
                                        <button wire:click="$set('gerenteReportSearch', '')" class="ml-0.5 hover:text-slate-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                                @if ($gerenteReportStatus)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] lato-bold">
                                        {{ match($gerenteReportStatus) { 'rascunho' => 'Em preparação', 'enviado' => 'Aguardando', 'aprovado' => 'Ativos', 'reprovado' => 'Devolvidos', 'concluido' => 'Concluídos', default => $gerenteReportStatus } }}
                                        <button wire:click="$set('gerenteReportStatus', '')" class="ml-0.5 hover:text-indigo-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                @if ($gplans->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 text-center">
                        <x-lucide-file-search class="w-7 h-7 text-slate-200 dark:text-slate-600 mx-auto mb-2" />
                        <p class="text-sm text-slate-400 lato-regular">Nenhum plano criado para o setor em {{ $selectedYear }}.</p>
                    </div>
                @else
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs lato-regular">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-700 text-[10px] lato-bold uppercase tracking-wider text-slate-400">
                                    <th class="px-5 py-2.5 text-left">Colaborador</th>
                                    <th class="px-3 py-2.5 text-center">Status</th>
                                    <th class="px-3 py-2.5 text-center">Progresso</th>
                                    <th class="px-3 py-2.5 text-center">Competências</th>
                                    <th class="px-3 py-2.5 text-center">Ações</th>
                                    <th class="px-3 py-2.5 text-center">Concluídas</th>
                                    <th class="px-3 py-2.5 text-center">Prazos vencidos</th>
                                    <th class="px-3 py-2.5 text-left">Ação rápida</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                @foreach ($gplans as $gp)
                                    @php
                                        $gpSc = match($gp->status) {
                                            'rascunho'  => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                                            'enviado'   => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300',
                                            'aprovado'  => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300',
                                            'reprovado' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                                            'concluido' => 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300',
                                            default     => 'bg-slate-100 text-slate-500',
                                        };
                                        $gpTotal    = $gp->actions->count();
                                        $gpDone     = $gp->actions->where('status','concluido')->count();
                                        $gpOverdue  = $gp->actions->filter(fn ($a) =>
                                            $a->status !== 'concluido' &&
                                            $a->target_date && $a->target_date->isPast()
                                        )->count();
                                        $gpParts = explode(' ', trim($gp->user->name));
                                        $gpInit  = strtoupper(substr($gpParts[0],0,1).(isset($gpParts[1])?substr($gpParts[1],0,1):''));
                                        $gpPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                        $gpBg    = $gpPal[abs(crc32($gp->user->name)) % count($gpPal)];
                                    @endphp
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition cursor-pointer"
                                        wire:click="goToTeamUser({{ $gp->user_id }})">
                                        <td class="px-5 py-3">
                                            <div class="flex items-center gap-2.5">
                                                <span class="w-7 h-7 rounded-full {{ $gpBg }} text-white text-[10px] lato-bold
                                                             flex items-center justify-center shrink-0">{{ $gpInit }}</span>
                                                <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $gp->user->name }}</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center" wire:click.stop>
                                            <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $gpSc }}">{{ $gp->status_label }}</span>
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-1.5 justify-center">
                                                <div class="w-14 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $gp->progress_percent >= 75 ? 'bg-emerald-500' : ($gp->progress_percent >= 40 ? 'bg-amber-400' : 'bg-slate-400') }}"
                                                         style="width:{{ $gp->progress_percent }}%"></div>
                                                </div>
                                                <span class="text-slate-400 w-8 text-right">{{ $gp->progress_percent }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center text-slate-400">{{ $gp->goals->count() }}</td>
                                        <td class="px-3 py-3 text-center text-slate-400">{{ $gpTotal }}</td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($gpTotal > 0)
                                                <span class="{{ $gpDone === $gpTotal && $gpTotal > 0 ? 'text-emerald-600 dark:text-emerald-400 lato-bold' : 'text-slate-400' }}">
                                                    {{ $gpDone }}/{{ $gpTotal }}
                                                </span>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($gpOverdue > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 lato-bold">
                                                    {{ $gpOverdue }}
                                                </span>
                                            @else
                                                <span class="text-slate-300 dark:text-slate-600">—</span>
                                            @endif
                                        </td>
                                        <td class="px-3 py-3" wire:click.stop>
                                            <button wire:click="goToTeamUser({{ $gp->user_id }})"
                                                    type="button"
                                                    class="text-[11px] lato-bold text-indigo-500 hover:text-indigo-700
                                                           dark:text-indigo-400 dark:hover:text-indigo-300 transition cursor-pointer
                                                           flex items-center gap-1">
                                                <x-lucide-arrow-right class="w-3 h-3" /> Ver plano
                                            </button>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>

        </div>
    @endif

