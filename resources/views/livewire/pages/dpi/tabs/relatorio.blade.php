    {{-- ════════════════════════════════════════════════════════════════
         ABA: RELATÓRIO (RH/DP)
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'relatorio' && auth()->user()->isRhOuDp())
        @php $s = $this->reportStats; @endphp

        <div class="space-y-5">

            {{-- ── KPI Cards ─────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
                    </div>
                    <div class="flex-1">
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">
                            {{ $s['with_plan'] }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $s['total_users'] }}</span>
                        </p>
                        <p class="text-xs text-slate-400 lato-regular">Colaboradores com plano</p>
                        @if ($s['total_users'] > 0)
                            <div class="mt-1.5 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 rounded-full"
                                     style="width: {{ round($s['with_plan'] / $s['total_users'] * 100) }}%"></div>
                            </div>
                        @endif
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-trending-up class="w-4 h-4 text-blue-600 dark:text-blue-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $s['avg_progress'] }}%</p>
                        <p class="text-xs text-slate-400 lato-regular">Progresso médio</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-check-circle class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">
                            {{ $s['done_actions'] }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $s['total_actions'] }}</span>
                        </p>
                        <p class="text-xs text-slate-400 lato-regular">Ações concluídas</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-clock class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $s['enviado'] }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Aguardando aprovação</p>
                    </div>
                </div>

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                        <x-lucide-file-x class="w-4 h-4 text-slate-400" />
                    </div>
                    <div>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $s['without_plan'] }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Sem plano</p>
                    </div>
                </div>

            </div>

            {{-- ── Gráfico de status + Tabela por departamento ────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

                {{-- Donut de status --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold mb-4">
                        Distribuição por status
                    </h3>
                    @if (($s['with_plan'] ?? 0) > 0)
                        {{-- wire:ignore: impede Livewire de destruir o gráfico a cada re-render --}}
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-report-chart');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'donut', height: 200, background: 'transparent', toolbar: { show: false } },
                                        series:      [{{ $s['rascunho'] }}, {{ $s['enviado'] }}, {{ $s['aprovado'] }}, {{ $s['reprovado'] }}, {{ $s['concluido'] }}],
                                        labels:      ['Em preparação','Aguardando','Ativos','Devolvidos','Concluídos'],
                                        colors:      ['#94a3b8','#f59e0b','#10b981','#ef4444','#14b8a6'],
                                        legend:      { position: 'bottom', labels: { colors: dark ? '#94a3b8' : '#64748b' } },
                                        dataLabels:  { style: { fontSize: '11px' } },
                                        plotOptions: { pie: { donut: { size: '65%' } } },
                                        theme:       { mode: dark ? 'dark' : 'light' },
                                        tooltip:     { theme: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })()
                             ">
                            <div id="dpi-report-chart" style="min-height:200px;"></div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-pie-chart class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum dado para {{ $selectedYear }}</p>
                        </div>
                    @endif

                    {{-- Legenda com contagens --}}
                    <div class="mt-4 space-y-1.5">
                        @foreach ([
                            ['Em preparação', 'bg-slate-400',   $s['rascunho']],
                            ['Aguardando',    'bg-amber-400',   $s['enviado']],
                            ['Ativos',        'bg-emerald-500', $s['aprovado']],
                            ['Devolvidos',    'bg-red-400',     $s['reprovado']],
                            ['Concluídos',    'bg-teal-500',    $s['concluido']],
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

                {{-- Tabela por departamento --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Por departamento</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs lato-regular">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-700 text-[10px] lato-bold uppercase tracking-wider text-slate-400">
                                    <th class="px-5 py-2.5 text-left">Departamento</th>
                                    <th class="px-3 py-2.5 text-center">Total</th>
                                    <th class="px-3 py-2.5 text-center">Com plano</th>
                                    <th class="px-3 py-2.5 text-center">Progresso</th>
                                    <th class="px-3 py-2.5 text-center">Ativos</th>
                                    <th class="px-3 py-2.5 text-center">Aguardando</th>
                                    <th class="px-3 py-2.5 text-center">Concluídos</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                @forelse ($this->reportByDepartment as $dept)
                                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                        <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-200">{{ $dept['name'] }}</td>
                                        <td class="px-3 py-3 text-center text-slate-400">{{ $dept['total_users'] }}</td>
                                        <td class="px-3 py-3 text-center {{ $dept['with_plan'] > 0 ? 'text-indigo-600 dark:text-indigo-400 lato-bold' : 'text-slate-300' }}">
                                            {{ $dept['with_plan'] }}
                                        </td>
                                        <td class="px-3 py-3">
                                            <div class="flex items-center gap-1.5 justify-center">
                                                <div class="w-16 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                    <div class="h-full rounded-full {{ $dept['avg_progress'] >= 75 ? 'bg-emerald-500' : ($dept['avg_progress'] >= 40 ? 'bg-amber-400' : 'bg-slate-400') }}"
                                                         style="width:{{ $dept['avg_progress'] }}%"></div>
                                                </div>
                                                <span class="text-slate-400 w-8 text-right">{{ $dept['avg_progress'] }}%</span>
                                            </div>
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($dept['aprovado'] > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 lato-bold">{{ $dept['aprovado'] }}</span>
                                            @else <span class="text-slate-300">—</span> @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($dept['enviado'] > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 lato-bold">{{ $dept['enviado'] }}</span>
                                            @else <span class="text-slate-300">—</span> @endif
                                        </td>
                                        <td class="px-3 py-3 text-center">
                                            @if ($dept['concluido'] > 0)
                                                <span class="px-2 py-0.5 rounded-full bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 lato-bold">{{ $dept['concluido'] }}</span>
                                            @else <span class="text-slate-300">—</span> @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="px-5 py-8 text-center text-slate-400">Nenhum departamento cadastrado.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            {{-- ── Ações por tipo + Ranking de progresso ───────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Gráfico: ações por tipo --}}
                @php $abt = $this->reportActionsByType; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold mb-4">
                        Ações por tipo
                    </h3>
                    @if (array_sum($abt['series'] ?? []) > 0)
                        <div wire:ignore
                             x-data
                             x-init="
                                (function init() {
                                    if (typeof ApexCharts === 'undefined') { setTimeout(init, 80); return; }
                                    var el = $el.querySelector('#dpi-actions-type-chart');
                                    if (!el) return;
                                    if (el._chart) { el._chart.destroy(); }
                                    var dark = document.documentElement.classList.contains('dark');
                                    el._chart = new ApexCharts(el, {
                                        chart:       { type: 'bar', height: 220, background: 'transparent', toolbar: { show: false } },
                                        series: [
                                            { name: 'Total',      data: {{ json_encode($abt['series']) }} },
                                            { name: 'Concluídas', data: {{ json_encode($abt['doneSeries']) }} },
                                        ],
                                        xaxis:       { categories: {{ json_encode($abt['labels']) }}, labels: { style: { colors: dark ? '#94a3b8' : '#64748b', fontSize: '11px' } } },
                                        yaxis:       { labels: { style: { colors: dark ? '#94a3b8' : '#64748b' } } },
                                        colors:      ['#6366f1','#10b981'],
                                        plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                                        dataLabels:  { enabled: false },
                                        grid:        { borderColor: dark ? '#334155' : '#f1f5f9' },
                                        legend:      { labels: { colors: dark ? '#94a3b8' : '#64748b' } },
                                        theme:       { mode: dark ? 'dark' : 'light' },
                                        tooltip:     { theme: dark ? 'dark' : 'light' },
                                    });
                                    el._chart.render();
                                })()
                             ">
                            <div id="dpi-actions-type-chart" style="min-height:220px;"></div>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-bar-chart-2 class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhuma ação registrada em {{ $selectedYear }}</p>
                        </div>
                    @endif
                </div>

                {{-- Ranking de progresso --}}
                @php $ranking = $this->reportProgressRanking; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">Ranking de progresso</h3>
                        <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Planos ativos/concluídos por progresso</p>
                    </div>

                    @if ($ranking['top']->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-trophy class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum plano ativo em {{ $selectedYear }}</p>
                        </div>
                    @else
                        {{-- Top 5 --}}
                        <div class="px-5 pt-4 pb-2">
                            <p class="text-[10px] lato-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1">
                                <x-lucide-trending-up class="w-3 h-3 text-emerald-500" /> Maiores progressos
                            </p>
                            <div class="space-y-2">
                                @foreach ($ranking['top'] as $i => $row)
                                    @php
                                        $rParts = explode(' ', trim($row['plan']->user->name));
                                        $rInit  = strtoupper(substr($rParts[0],0,1).(isset($rParts[1])?substr($rParts[1],0,1):''));
                                        $rPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                        $rBg    = $rPal[abs(crc32($row['plan']->user->name)) % count($rPal)];
                                    @endphp
                                    <div class="flex items-center gap-3">
                                        <span class="text-[11px] lato-bold text-slate-300 dark:text-slate-600 w-4 text-right shrink-0">{{ $i+1 }}</span>
                                        <span class="w-7 h-7 rounded-full {{ $rBg }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0">{{ $rInit }}</span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $row['plan']->user->name }}</p>
                                            <p class="text-[10px] text-slate-400 lato-regular">{{ $row['plan']->user->department?->name ?? '—' }}</p>
                                        </div>
                                        <div class="flex items-center gap-2 shrink-0">
                                            <div class="w-20 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full bg-emerald-500 rounded-full" style="width:{{ $row['progress'] }}%"></div>
                                            </div>
                                            <span class="text-xs lato-bold text-emerald-600 dark:text-emerald-400 w-8 text-right">{{ $row['progress'] }}%</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>

                        @if ($ranking['bottom']->isNotEmpty())
                            <div class="px-5 pt-3 pb-4 border-t border-slate-50 dark:border-slate-700/60 mt-2">
                                <p class="text-[10px] lato-bold uppercase tracking-wider text-slate-400 mb-2 flex items-center gap-1">
                                    <x-lucide-trending-down class="w-3 h-3 text-amber-500" /> Precisam de atenção
                                </p>
                                <div class="space-y-2">
                                    @foreach ($ranking['bottom'] as $i => $row)
                                        @php
                                            $bParts = explode(' ', trim($row['plan']->user->name));
                                            $bInit  = strtoupper(substr($bParts[0],0,1).(isset($bParts[1])?substr($bParts[1],0,1):''));
                                            $bBg    = $rPal[abs(crc32($row['plan']->user->name)) % count($rPal)];
                                        @endphp
                                        <div class="flex items-center gap-3">
                                            <span class="w-7 h-7 rounded-full {{ $bBg }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0">{{ $bInit }}</span>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $row['plan']->user->name }}</p>
                                                <p class="text-[10px] text-slate-400 lato-regular">{{ $row['plan']->user->department?->name ?? '—' }}</p>
                                            </div>
                                            <div class="flex items-center gap-2 shrink-0">
                                                <div class="w-20 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                    <div class="h-full bg-amber-400 rounded-full" style="width:{{ $row['progress'] }}%"></div>
                                                </div>
                                                <span class="text-xs lato-bold text-amber-600 dark:text-amber-400 w-8 text-right">{{ $row['progress'] }}%</span>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    @endif
                </div>

            </div>

            {{-- ── Prazos vencidos + Colaboradores sem plano ────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">

                {{-- Ações com prazo vencido --}}
                @php $overdue = $this->reportOverdueActions; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold flex items-center gap-2">
                                <x-lucide-alarm-clock class="w-4 h-4 text-red-500" />
                                Prazos vencidos
                            </h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Ações não concluídas após a data limite</p>
                        </div>
                        @if ($overdue->isNotEmpty())
                            <span class="px-2.5 py-1 rounded-full bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 text-xs lato-bold">
                                {{ $overdue->count() }}
                            </span>
                        @endif
                    </div>

                    @if ($overdue->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-check-circle class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhum prazo vencido!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 dark:divide-slate-700/50 max-h-64 overflow-y-auto">
                            @foreach ($overdue as $act)
                                @php $daysLate = now()->diffInDays($act->target_date, false) * -1; @endphp
                                <div class="flex items-start gap-3 px-5 py-3">
                                    <div class="w-8 h-8 rounded-lg bg-red-50 dark:bg-red-900/20 flex items-center justify-center shrink-0 mt-0.5">
                                        <x-lucide-clock class="w-3.5 h-3.5 text-red-500" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $act->title }}</p>
                                        <p class="text-[11px] text-slate-400 lato-regular truncate">
                                            {{ $act->goal->plan->user->name }} · {{ $act->goal->plan->user->department?->name ?? '—' }}
                                        </p>
                                        <p class="text-[11px] text-red-500 lato-bold mt-0.5 flex items-center gap-1">
                                            <x-lucide-calendar-x class="w-3 h-3" />
                                            {{ $act->target_date->format('d/m/Y') }} · {{ $daysLate }}d atrasada
                                        </p>
                                    </div>
                                    <span class="text-[10px] lato-bold px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 shrink-0">
                                        {{ $act->type_label }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Colaboradores sem plano --}}
                @php $noPlan = $this->reportUsersWithoutPlan; @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between">
                        <div>
                            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold flex items-center gap-2">
                                <x-lucide-user-x class="w-4 h-4 text-slate-400" />
                                Sem plano em {{ $selectedYear }}
                            </h3>
                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Colaboradores ativos sem DPI cadastrado</p>
                        </div>
                        @if ($noPlan->isNotEmpty())
                            <span class="px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold">
                                {{ $noPlan->count() }}
                            </span>
                        @endif
                    </div>

                    @if ($noPlan->isEmpty())
                        <div class="flex flex-col items-center justify-center py-10 text-center">
                            <x-lucide-check-circle class="w-8 h-8 text-emerald-200 dark:text-emerald-900 mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Todos têm plano em {{ $selectedYear }}!</p>
                        </div>
                    @else
                        <div class="divide-y divide-slate-50 dark:divide-slate-700/50 max-h-64 overflow-y-auto">
                            @foreach ($noPlan as $u)
                                @php
                                    $uParts = explode(' ', trim($u->name));
                                    $uInit  = strtoupper(substr($uParts[0],0,1).(isset($uParts[1])?substr($uParts[1],0,1):''));
                                    $uPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                    $uBg    = $uPal[abs(crc32($u->name)) % count($uPal)];
                                @endphp
                                <div class="flex items-center gap-3 px-5 py-3">
                                    <span class="w-8 h-8 rounded-full {{ $uBg }} text-white text-xs lato-bold
                                                 flex items-center justify-center shrink-0">{{ $uInit }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $u->name }}</p>
                                        <p class="text-[11px] text-slate-400 lato-regular truncate flex items-center gap-1">
                                            <x-lucide-building-2 class="w-3 h-3" />
                                            {{ $u->department?->name ?? 'Sem departamento' }}
                                        </p>
                                    </div>
                                    <span class="text-[10px] lato-regular text-slate-400 shrink-0">
                                        {{ $u->accessProfile?->name ?? '—' }}
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

            </div>

            {{-- ── Listagem individual de planos ───────────────────────── --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                {{-- Filtros --}}
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col gap-3">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold shrink-0">
                            Planos individuais
                            <span class="ml-1 text-xs font-normal text-slate-400 lato-regular">({{ $this->reportPlans->count() }})</span>
                        </h3>
                    </div>

                    {{-- Linha de filtros --}}
                    <div class="flex flex-col gap-2.5">

                        {{-- Busca --}}
                        <div class="relative w-full">
                            <x-lucide-search class="w-3.5 h-3.5 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                            <input type="text" wire:model.live.debounce.300ms="reportSearch"
                                   placeholder="Buscar colaborador..."
                                   class="w-full pl-9 pr-8 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                                          placeholder-slate-400 transition" />
                            @if ($reportSearch)
                                <button wire:click="$set('reportSearch', '')"
                                        class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>

                        {{-- Dropdowns --}}
                        <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">

                            {{-- Filtro Status --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button"
                                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                        {{ $reportFilterStatus ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    <div class="flex items-center gap-1.5 truncate">
                                        @php
                                            $statusDot = match($reportFilterStatus) {
                                                'aprovado'  => 'bg-emerald-400',
                                                'reprovado' => 'bg-red-400',
                                                'enviado'   => 'bg-amber-400',
                                                'concluido' => 'bg-teal-400',
                                                'rascunho'  => 'bg-slate-400',
                                                default     => null,
                                            };
                                        @endphp
                                        @if ($statusDot)
                                            <span class="w-2 h-2 rounded-full {{ $statusDot }} flex-shrink-0"></span>
                                        @else
                                            <x-lucide-circle-check class="w-3.5 h-3.5 flex-shrink-0" />
                                        @endif
                                        <span class="truncate text-xs lato-bold">
                                            {{ match($reportFilterStatus) {
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
                                    <button @click="open=false; $wire.set('reportFilterStatus', '')" type="button"
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
                                        <button @click="open=false; $wire.set('reportFilterStatus', '{{ $val }}')" type="button"
                                                class="w-full text-left px-3 py-2 text-xs lato-regular flex items-center gap-2 transition
                                                {{ $reportFilterStatus === $val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                            <span class="w-2 h-2 rounded-full {{ $dot }} flex-shrink-0"></span>
                                            {{ $label }}
                                            @if ($reportFilterStatus === $val)
                                                <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" />
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </div>

                            {{-- Filtro Departamento --}}
                            <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" type="button"
                                        class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                        {{ $reportFilterDept ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                                    <div class="flex items-center gap-1.5 truncate">
                                        <x-lucide-building-2 class="w-3.5 h-3.5 flex-shrink-0" />
                                        <span class="truncate text-xs lato-bold">
                                            @php $deptName = $reportFilterDept ? ($this->reportDepartments->find($reportFilterDept)?->name ?? 'Depto.') : 'Departamento'; @endphp
                                            {{ $deptName }}
                                        </span>
                                    </div>
                                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                                        x-bind:class="open ? 'rotate-180' : ''" />
                                </button>
                                <div x-show="open" @click.outside="open = false" x-transition
                                     class="absolute mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-20 py-1">
                                    <button @click="open=false; $wire.set('reportFilterDept', '')" type="button"
                                            class="w-full text-left px-3 py-2 text-xs lato-regular hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                        <x-lucide-building-2 class="w-3.5 h-3.5" /> Todos os departamentos
                                    </button>
                                    <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                                    <div class="overflow-y-auto max-h-48">
                                        @foreach ($this->reportDepartments as $dept)
                                            <button @click="open=false; $wire.set('reportFilterDept', '{{ $dept->id }}')" type="button"
                                                    class="w-full text-left px-3 py-2 text-xs lato-regular flex items-center justify-between transition
                                                    {{ $reportFilterDept == $dept->id ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                                {{ $dept->name }}
                                                @if ($reportFilterDept == $dept->id)
                                                    <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" />
                                                @endif
                                            </button>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            {{-- Limpar filtros --}}
                            @if ($reportSearch || $reportFilterStatus || $reportFilterDept)
                                <button wire:click="clearReportFilters" type="button"
                                        class="flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs lato-bold
                                               text-red-400 hover:text-red-600 border border-red-200 dark:border-red-900/50
                                               hover:border-red-400 rounded-xl bg-red-50 dark:bg-red-900/10
                                               hover:bg-red-100 dark:hover:bg-red-900/20 transition cursor-pointer">
                                    <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar filtros
                                </button>
                            @endif
                        </div>

                        {{-- Chips de filtros ativos --}}
                        @if ($reportSearch || $reportFilterStatus || $reportFilterDept)
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-[10px] text-slate-400 lato-regular">Filtros ativos:</span>
                                @if ($reportSearch)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] lato-bold">
                                        <x-lucide-search class="w-2.5 h-2.5" />
                                        "{{ Str::limit($reportSearch, 18) }}"
                                        <button wire:click="$set('reportSearch', '')" class="ml-0.5 hover:text-slate-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                                @if ($reportFilterStatus)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] lato-bold">
                                        {{ match($reportFilterStatus) { 'rascunho' => 'Em preparação', 'enviado' => 'Aguardando', 'aprovado' => 'Ativos', 'reprovado' => 'Devolvidos', 'concluido' => 'Concluídos', default => $reportFilterStatus } }}
                                        <button wire:click="$set('reportFilterStatus', '')" class="ml-0.5 hover:text-indigo-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                                @if ($reportFilterDept)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[10px] lato-bold">
                                        <x-lucide-building-2 class="w-2.5 h-2.5" />
                                        {{ $this->reportDepartments->find($reportFilterDept)?->name }}
                                        <button wire:click="$set('reportFilterDept', '')" class="ml-0.5 hover:text-indigo-900 dark:hover:text-white transition">
                                            <x-lucide-x class="w-2.5 h-2.5" />
                                        </button>
                                    </span>
                                @endif
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Tabela --}}
                <div class="overflow-x-auto">
                    <table class="w-full text-xs lato-regular">
                        <thead>
                            <tr class="border-b border-slate-100 dark:border-slate-700 text-[10px] lato-bold uppercase tracking-wider text-slate-400">
                                <th class="px-5 py-2.5 text-left">Colaborador</th>
                                <th class="px-3 py-2.5 text-left">Departamento</th>
                                <th class="px-3 py-2.5 text-left">Perfil</th>
                                <th class="px-3 py-2.5 text-center">Status</th>
                                <th class="px-3 py-2.5 text-center">Progresso</th>
                                <th class="px-3 py-2.5 text-center">Competências</th>
                                <th class="px-3 py-2.5 text-center">Ações</th>
                                <th class="px-3 py-2.5 text-center">Concluídas</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                            @forelse ($this->reportPlans as $plan)
                                @php
                                    $rSc = match($plan->status) {
                                        'rascunho'  => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                                        'enviado'   => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300',
                                        'aprovado'  => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300',
                                        'reprovado' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                                        'concluido' => 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300',
                                        default     => 'bg-slate-100 text-slate-500',
                                    };
                                    $rTotal = $plan->actions->count();
                                    $rDone  = $plan->actions->where('status','concluido')->count();
                                    $rParts = explode(' ', trim($plan->user->name));
                                    $rInit  = strtoupper(substr($rParts[0],0,1) . (isset($rParts[1]) ? substr($rParts[1],0,1) : ''));
                                    $rPal   = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                    $rBg    = $rPal[abs(crc32($plan->user->name)) % count($rPal)];
                                @endphp
                                <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                    <td class="px-5 py-3">
                                        <div class="flex items-center gap-2.5">
                                            <span class="w-7 h-7 rounded-full {{ $rBg }} text-white text-[10px] lato-bold
                                                         flex items-center justify-center shrink-0">{{ $rInit }}</span>
                                            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $plan->user->name }}</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-slate-400">{{ $plan->user->department?->name ?? '—' }}</td>
                                    <td class="px-3 py-3 text-slate-400">{{ $plan->user->accessProfile?->name ?? '—' }}</td>
                                    <td class="px-3 py-3 text-center">
                                        <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $rSc }}">{{ $plan->status_label }}</span>
                                    </td>
                                    <td class="px-3 py-3">
                                        <div class="flex items-center gap-1.5 justify-center">
                                            <div class="w-14 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                                <div class="h-full rounded-full {{ $plan->progress_percent >= 75 ? 'bg-emerald-500' : ($plan->progress_percent >= 40 ? 'bg-amber-400' : 'bg-slate-400') }}"
                                                     style="width:{{ $plan->progress_percent }}%"></div>
                                            </div>
                                            <span class="text-slate-400 w-8 text-right">{{ $plan->progress_percent }}%</span>
                                        </div>
                                    </td>
                                    <td class="px-3 py-3 text-center text-slate-400">{{ $plan->goals->count() }}</td>
                                    <td class="px-3 py-3 text-center text-slate-400">{{ $rTotal }}</td>
                                    <td class="px-3 py-3 text-center">
                                        @if ($rTotal > 0)
                                            <span class="{{ $rDone === $rTotal ? 'text-emerald-600 dark:text-emerald-400 lato-bold' : 'text-slate-400' }}">
                                                {{ $rDone }}/{{ $rTotal }}
                                            </span>
                                        @else
                                            <span class="text-slate-300 dark:text-slate-600">—</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="px-5 py-10 text-center">
                                        <x-lucide-file-search class="w-7 h-7 text-slate-200 dark:text-slate-600 mx-auto mb-2" />
                                        <p class="text-sm text-slate-400 lato-regular">Nenhum plano encontrado.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    @endif

