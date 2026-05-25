<div class="p-4 sm:p-6 lg:p-8 space-y-6">

    {{-- ── Cabeçalho ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2 mb-0.5">
                <a href="{{ route('dpi') }}"
                   class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition cursor-pointer">
                    <x-lucide-arrow-left class="w-4 h-4" />
                </a>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white lato-black tracking-tight">
                    Relatório DPI
                </h1>
            </div>
            <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular">
                Visão consolidada dos planos de desenvolvimento — {{ $selectedYear }}
            </p>
        </div>

        {{-- Seletor de ano --}}
        <div class="relative shrink-0">
            <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
            <select wire:model.live="selectedYear"
                    class="appearance-none pl-9 pr-8 py-2.5 text-sm lato-bold rounded-xl
                           bg-white dark:bg-slate-800
                           text-slate-700 dark:text-slate-200
                           border border-slate-200 dark:border-slate-700
                           focus:outline-none focus:ring-2 focus:ring-indigo-400/40
                           cursor-pointer transition">
                @foreach ($this->years as $y)
                    <option value="{{ $y }}">{{ $y }}</option>
                @endforeach
            </select>
            <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
        </div>
    </div>

    @php $s = $this->stats; @endphp

    {{-- ── KPI Cards ──────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">

        {{-- Cobertura --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
            <div class="w-12 h-12 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                <x-lucide-users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
            </div>
            <div>
                <p class="text-2xl lato-black text-slate-800 dark:text-white">
                    {{ $s['with_plan'] }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $s['total_users'] }}</span>
                </p>
                <p class="text-xs text-slate-400 lato-regular">Colaboradores com plano</p>
                @if ($s['total_users'] > 0)
                    <div class="mt-1.5 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden w-28">
                        <div class="h-full bg-indigo-500 rounded-full transition-all"
                             style="width: {{ round($s['with_plan'] / $s['total_users'] * 100) }}%"></div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Progresso médio --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                <x-lucide-trending-up class="w-4.5 h-4.5 text-blue-600 dark:text-blue-400" />
            </div>
            <div>
                <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $s['avg_progress'] }}%</p>
                <p class="text-xs text-slate-400 lato-regular">Progresso médio</p>
            </div>
        </div>

        {{-- Ações concluídas --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center shrink-0">
                <x-lucide-check-circle class="w-4.5 h-4.5 text-emerald-600 dark:text-emerald-400" />
            </div>
            <div>
                <p class="text-2xl lato-black text-slate-800 dark:text-white">
                    {{ $s['done_actions'] }}<span class="text-slate-300 dark:text-slate-600 text-lg">/{{ $s['total_actions'] }}</span>
                </p>
                <p class="text-xs text-slate-400 lato-regular">Ações concluídas</p>
            </div>
        </div>

        {{-- Aguardando --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-amber-100 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                <x-lucide-clock class="w-4.5 h-4.5 text-amber-600 dark:text-amber-400" />
            </div>
            <div>
                <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $s['enviado'] }}</p>
                <p class="text-xs text-slate-400 lato-regular">Aguardando aprovação</p>
            </div>
        </div>

        {{-- Sem plano --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                <x-lucide-file-x class="w-4.5 h-4.5 text-slate-400" />
            </div>
            <div>
                <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $s['without_plan'] }}</p>
                <p class="text-xs text-slate-400 lato-regular">Sem plano</p>
            </div>
        </div>

    </div>

    {{-- ── Gráfico + Breakdown por departamento ───────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">

        {{-- Donut de status --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold mb-4">
                Distribuição por status
            </h3>

            @php $cd = $this->chartStatusData; @endphp

            @if (array_sum($cd['series']) > 0)
                <div id="dpi-status-chart" class="w-full" style="min-height: 220px;"></div>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        renderDpiStatusChart();
                    });
                    document.addEventListener('livewire:navigated', function () {
                        renderDpiStatusChart();
                    });
                    function renderDpiStatusChart() {
                        var el = document.getElementById('dpi-status-chart');
                        if (!el || typeof ApexCharts === 'undefined') return;
                        if (el._apexChart) { el._apexChart.destroy(); }
                        var isDark = document.documentElement.classList.contains('dark');
                        var chart = new ApexCharts(el, {
                            chart:   { type: 'donut', height: 220, background: 'transparent' },
                            series:  @json($cd['series']),
                            labels:  @json($cd['labels']),
                            colors:  @json($cd['colors']),
                            legend:  { position: 'bottom', labels: { colors: isDark ? '#94a3b8' : '#64748b' } },
                            dataLabels: { style: { fontSize: '11px' } },
                            plotOptions: { pie: { donut: { size: '65%' } } },
                            theme:   { mode: isDark ? 'dark' : 'light' },
                            tooltip: { theme: isDark ? 'dark' : 'light' },
                        });
                        chart.render();
                        el._apexChart = chart;
                    }
                </script>
            @else
                <div class="flex flex-col items-center justify-center py-12 text-center">
                    <x-lucide-pie-chart class="w-10 h-10 text-slate-200 dark:text-slate-600 mb-2" />
                    <p class="text-sm text-slate-400 lato-regular">Nenhum dado para {{ $selectedYear }}</p>
                </div>
            @endif

            {{-- Legenda extra com contagens --}}
            <div class="mt-4 space-y-1.5">
                @foreach ([
                    ['rascunho', 'Em preparação', 'bg-slate-400', $s['rascunho']],
                    ['enviado',  'Aguardando',    'bg-amber-400', $s['enviado']],
                    ['aprovado', 'Ativos',         'bg-emerald-500', $s['aprovado']],
                    ['reprovado','Devolvidos',     'bg-red-400',   $s['reprovado']],
                    ['concluido','Concluídos',     'bg-teal-500',  $s['concluido']],
                ] as [$key, $label, $dot, $count])
                    <div class="flex items-center justify-between text-xs lato-regular">
                        <span class="flex items-center gap-1.5 text-slate-500 dark:text-slate-400">
                            <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span>
                            {{ $label }}
                        </span>
                        <span class="lato-bold text-slate-700 dark:text-slate-200">{{ $count }}</span>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Tabela por departamento --}}
        <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700">
                <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold">
                    Por departamento
                </h3>
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
                        @forelse ($this->byDepartment as $dept)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                <td class="px-5 py-3 font-semibold text-slate-700 dark:text-slate-200">
                                    {{ $dept['name'] }}
                                </td>
                                <td class="px-3 py-3 text-center text-slate-500 dark:text-slate-400">
                                    {{ $dept['total_users'] }}
                                </td>
                                <td class="px-3 py-3 text-center">
                                    <span class="{{ $dept['with_plan'] > 0 ? 'text-indigo-600 dark:text-indigo-400 lato-bold' : 'text-slate-300 dark:text-slate-600' }}">
                                        {{ $dept['with_plan'] }}
                                    </span>
                                </td>
                                <td class="px-3 py-3">
                                    <div class="flex items-center gap-1.5 justify-center">
                                        <div class="w-16 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full transition-all
                                                        {{ $dept['avg_progress'] >= 75 ? 'bg-emerald-500' : ($dept['avg_progress'] >= 40 ? 'bg-amber-400' : 'bg-slate-400') }}"
                                                 style="width: {{ $dept['avg_progress'] }}%"></div>
                                        </div>
                                        <span class="text-slate-500 dark:text-slate-400 w-8 text-right">{{ $dept['avg_progress'] }}%</span>
                                    </div>
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if ($dept['aprovado'] > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 lato-bold">
                                            {{ $dept['aprovado'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if ($dept['enviado'] > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 lato-bold">
                                            {{ $dept['enviado'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-3 text-center">
                                    @if ($dept['concluido'] > 0)
                                        <span class="px-2 py-0.5 rounded-full bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-400 lato-bold">
                                            {{ $dept['concluido'] }}
                                        </span>
                                    @else
                                        <span class="text-slate-300 dark:text-slate-600">—</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-5 py-8 text-center text-slate-400 lato-regular">
                                    Nenhum departamento cadastrado.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

    {{-- ── Listagem de planos ──────────────────────────────────────────── --}}
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">

        {{-- Filtros --}}
        <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 flex flex-col sm:flex-row gap-3 items-start sm:items-center justify-between">
            <h3 class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-bold shrink-0">
                Planos individuais
                <span class="ml-1.5 text-xs font-normal text-slate-400 lato-regular">({{ $this->plans->count() }})</span>
            </h3>
            <div class="flex flex-wrap items-center gap-2 w-full sm:w-auto">

                {{-- Busca --}}
                <div class="relative flex-1 min-w-[160px]">
                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                    <input wire:model.live.debounce.300ms="search"
                           type="text" placeholder="Buscar colaborador..."
                           class="w-full pl-8 pr-3 py-1.5 text-xs lato-regular rounded-xl border
                                  bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                  border-slate-200 dark:border-slate-700
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400/40 transition" />
                </div>

                {{-- Filtro status --}}
                <select wire:model.live="filterStatus"
                        class="appearance-none pl-3 pr-7 py-1.5 text-xs lato-bold rounded-xl border
                               bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                               border-slate-200 dark:border-slate-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition">
                    <option value="">Todos os status</option>
                    <option value="rascunho">Em preparação</option>
                    <option value="enviado">Aguardando</option>
                    <option value="aprovado">Ativos</option>
                    <option value="reprovado">Devolvidos</option>
                    <option value="concluido">Concluídos</option>
                </select>

                {{-- Filtro departamento --}}
                <select wire:model.live="filterDept"
                        class="appearance-none pl-3 pr-7 py-1.5 text-xs lato-bold rounded-xl border
                               bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                               border-slate-200 dark:border-slate-700
                               focus:outline-none focus:ring-2 focus:ring-indigo-400/40 cursor-pointer transition">
                    <option value="">Todos os departamentos</option>
                    @foreach ($this->departments as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>

                {{-- Limpar filtros --}}
                @if ($filterStatus || $filterDept || $search)
                    <button wire:click="$set('filterStatus',''); $set('filterDept',''); $set('search','')" type="button"
                            class="text-xs text-slate-400 hover:text-red-500 lato-bold transition cursor-pointer flex items-center gap-1">
                        <x-lucide-x class="w-3 h-3" /> Limpar
                    </button>
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
                    @forelse ($this->plans as $plan)
                        @php
                            $statusColors = [
                                'rascunho'  => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                                'enviado'   => 'bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300',
                                'aprovado'  => 'bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300',
                                'reprovado' => 'bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400',
                                'concluido' => 'bg-teal-50 dark:bg-teal-900/20 text-teal-700 dark:text-teal-300',
                            ];
                            $sc = $statusColors[$plan->status] ?? 'bg-slate-100 text-slate-500';
                            $totalAct = $plan->actions->count();
                            $doneAct  = $plan->actions->where('status','concluido')->count();
                            $palette  = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                            $avatarBg = $palette[abs(crc32($plan->user->name)) % count($palette)];
                            $parts    = explode(' ', trim($plan->user->name));
                            $initials = strtoupper(substr($parts[0],0,1) . (isset($parts[1]) ? substr($parts[1],0,1) : ''));
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-2.5">
                                    <span class="w-7 h-7 rounded-full {{ $avatarBg }} text-white text-[10px] lato-bold
                                                 flex items-center justify-center shrink-0">{{ $initials }}</span>
                                    <span class="font-semibold text-slate-700 dark:text-slate-200">{{ $plan->user->name }}</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-slate-500 dark:text-slate-400">
                                {{ $plan->user->department?->name ?? '—' }}
                            </td>
                            <td class="px-3 py-3 text-slate-500 dark:text-slate-400">
                                {{ $plan->user->accessProfile?->name ?? '—' }}
                            </td>
                            <td class="px-3 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $sc }}">
                                    {{ $plan->status_label }}
                                </span>
                            </td>
                            <td class="px-3 py-3">
                                <div class="flex items-center gap-1.5 justify-center">
                                    <div class="w-14 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full rounded-full transition-all
                                                    {{ $plan->progress_percent >= 75 ? 'bg-emerald-500' : ($plan->progress_percent >= 40 ? 'bg-amber-400' : 'bg-slate-400') }}"
                                             style="width: {{ $plan->progress_percent }}%"></div>
                                    </div>
                                    <span class="text-slate-500 dark:text-slate-400 w-8 text-right">{{ $plan->progress_percent }}%</span>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-center text-slate-500 dark:text-slate-400">
                                {{ $plan->goals->count() }}
                            </td>
                            <td class="px-3 py-3 text-center text-slate-500 dark:text-slate-400">
                                {{ $totalAct }}
                            </td>
                            <td class="px-3 py-3 text-center">
                                @if ($totalAct > 0)
                                    <span class="{{ $doneAct === $totalAct ? 'text-emerald-600 dark:text-emerald-400 lato-bold' : 'text-slate-500 dark:text-slate-400' }}">
                                        {{ $doneAct }}/{{ $totalAct }}
                                    </span>
                                @else
                                    <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-10 text-center">
                                <x-lucide-file-search class="w-8 h-8 text-slate-200 dark:text-slate-600 mx-auto mb-2" />
                                <p class="text-sm text-slate-400 lato-regular">Nenhum plano encontrado com os filtros aplicados.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

</div>
