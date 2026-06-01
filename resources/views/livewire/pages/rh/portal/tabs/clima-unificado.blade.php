        {{-- ═══════════════════════════════════════════════════════════
             ABA: CLIMA UNIFICADO
        ═══════════════════════════════════════════════════════════ --}}
        @if ($aba === 'clima-unificado')
        @php
            $cl = $this->climaUnificado;
            $depListClima = \App\Models\Department::orderBy('name')->get();
            $corMap = [
                'emerald' => ['bg' => 'from-emerald-500 to-teal-500',    'text' => 'text-emerald-600 dark:text-emerald-400',  'ring' => 'ring-emerald-400', 'light' => 'bg-emerald-50 dark:bg-emerald-900/20'],
                'blue'    => ['bg' => 'from-blue-500 to-indigo-500',      'text' => 'text-blue-600 dark:text-blue-400',        'ring' => 'ring-blue-400',    'light' => 'bg-blue-50 dark:bg-blue-900/20'],
                'amber'   => ['bg' => 'from-amber-500 to-yellow-500',     'text' => 'text-amber-600 dark:text-amber-400',      'ring' => 'ring-amber-400',   'light' => 'bg-amber-50 dark:bg-amber-900/20'],
                'orange'  => ['bg' => 'from-orange-500 to-red-400',       'text' => 'text-orange-600 dark:text-orange-400',    'ring' => 'ring-orange-400',  'light' => 'bg-orange-50 dark:bg-orange-900/20'],
                'rose'    => ['bg' => 'from-rose-500 to-red-600',         'text' => 'text-rose-600 dark:text-rose-400',        'ring' => 'ring-rose-400',    'light' => 'bg-rose-50 dark:bg-rose-900/20'],
                'slate'   => ['bg' => 'from-slate-400 to-slate-500',      'text' => 'text-slate-500 dark:text-slate-400',      'ring' => 'ring-slate-400',   'light' => 'bg-slate-50 dark:bg-slate-800'],
            ];
            $cor = $corMap[$cl['climaCor']] ?? $corMap['slate'];
        @endphp
        <div class="p-4 md:p-6 space-y-6">

            {{-- Cabeçalho --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white">Relatório de Clima Unificado</h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">
                        {{ \Carbon\Carbon::parse($cl['inicio'])->format('d/m/Y') }}
                        –
                        {{ \Carbon\Carbon::parse($cl['fim'])->format('d/m/Y') }}
                    </p>
                </div>
            </div>

            {{-- Filtros --}}
            <div class="flex flex-col gap-3 p-4 bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700">

                {{-- Linha superior: período --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    <div class="flex items-center gap-2 flex-1 min-w-0">
                        <div class="relative flex-1 min-w-0">
                            <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                            <input wire:model="climaInicio" type="date"
                                   class="w-full pl-9 pr-2 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent lato-regular
                                          {{ $climaInicio ? 'border-indigo-400 bg-violet-50 dark:bg-violet-900/10 text-violet-700 dark:text-violet-300' : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200' }}" />
                        </div>
                        <span class="text-gray-400 text-sm shrink-0">→</span>
                        <div class="relative flex-1 min-w-0">
                            <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                            <input wire:model="climaFim" type="date"
                                   class="w-full pl-9 pr-2 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 focus:border-transparent lato-regular
                                          {{ $climaFim ? 'border-indigo-400 bg-violet-50 dark:bg-violet-900/10 text-violet-700 dark:text-violet-300' : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200' }}" />
                        </div>
                    </div>
                    <button wire:click="climaAplicar" type="button"
                            class="cursor-pointer flex items-center justify-center gap-1.5 px-4 py-2 text-sm lato-bold text-white bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 rounded-lg transition shrink-0 w-full sm:w-auto">
                        <x-lucide-filter class="w-3.5 h-3.5" /> Aplicar
                    </button>
                </div>

                {{-- Linha inferior: selects --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    {{-- Departamento --}}
                    <select wire:model="climaDepartamento" wire:change="climaAplicar"
                            class="flex-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-indigo-400 lato-regular cursor-pointer
                                   {{ $climaDepartamento ? 'border-indigo-400 bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-500 dark:text-slate-400' }}">
                        <option value="">Todos os departamentos</option>
                        @foreach ($depListClima as $dep)
                            <option value="{{ $dep->id }}" {{ $climaDepartamento == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                    {{-- Limpar (quando há filtros ativos) --}}
                    @if($climaInicio || $climaFim || $climaDepartamento)
                    <button wire:click="climaLimpar" type="button"
                            class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2 text-xs lato-bold
                                   text-slate-500 dark:text-slate-400 border border-dashed border-slate-300 dark:border-slate-600
                                   rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 hover:text-slate-700 dark:hover:text-slate-200 transition w-full sm:w-auto">
                        <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar filtros
                    </button>
                    @endif
                </div>

                {{-- Chips filtros ativos --}}
                @if($climaInicio || $climaFim || $climaDepartamento)
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-gray-400 lato-regular">Filtros:</span>
                    @if($climaInicio)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 text-xs lato-bold">
                        <x-lucide-calendar class="w-3 h-3" /> De {{ \Carbon\Carbon::parse($climaInicio)->format('d/m/Y') }}
                        <button wire:click="$set('climaInicio','')" class="cursor-pointer ml-0.5 hover:text-violet-900 transition"><x-lucide-x class="w-3 h-3" /></button>
                    </span>
                    @endif
                    @if($climaFim)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 text-xs lato-bold">
                        <x-lucide-calendar class="w-3 h-3" /> Até {{ \Carbon\Carbon::parse($climaFim)->format('d/m/Y') }}
                        <button wire:click="$set('climaFim','')" class="cursor-pointer ml-0.5 hover:text-violet-900 transition"><x-lucide-x class="w-3 h-3" /></button>
                    </span>
                    @endif
                    @if($climaDepartamento)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 text-xs lato-bold">
                        <x-lucide-building-2 class="w-3 h-3" /> {{ $depListClima->firstWhere('id', $climaDepartamento)?->name }}
                        <button wire:click="$set('climaDepartamento','')" class="cursor-pointer ml-0.5 hover:text-violet-900 transition"><x-lucide-x class="w-3 h-3" /></button>
                    </span>
                    @endif
                    <button wire:click="climaLimpar" type="button"
                            class="cursor-pointer ml-auto flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 transition lato-regular">
                        <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar tudo
                    </button>
                </div>
                @endif

            </div>

            {{-- ÍNDICE GERAL DE CLIMA --}}
            <div class="bg-gradient-to-br {{ $cor['bg'] }} rounded-2xl p-6 text-white relative overflow-hidden">
                <div class="absolute inset-0 opacity-10">
                    <svg viewBox="0 0 400 200" class="w-full h-full"><circle cx="350" cy="50" r="120" fill="white"/><circle cx="50" cy="180" r="80" fill="white"/></svg>
                </div>
                <div class="relative flex flex-col sm:flex-row sm:items-center gap-6">
                    <div class="flex-1">
                        <p class="text-white/70 text-sm lato-regular">Índice de Clima Organizacional</p>
                        <div class="flex items-baseline gap-3 mt-1">
                            <p class="text-5xl sm:text-6xl lato-black">{{ $cl['climaGeral'] ?? '—' }}</p>
                            @if($cl['climaGeral'] !== null)<p class="text-3xl lato-bold text-white/60">/100</p>@endif
                        </div>
                        <div class="flex items-center gap-2 mt-2">
                            <span class="px-3 py-1 rounded-full bg-white/20 text-xs lato-bold">{{ $cl['climaLabel'] }}</span>
                            <span class="text-white/60 text-xs lato-regular">
                                Humor 40% · Pesquisas 40% · Feedbacks 20%
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-3 gap-2 sm:gap-4 shrink-0 w-full sm:w-auto">
                        <div class="text-center">
                            <p class="text-2xl sm:text-3xl lato-black">{{ $cl['moodScore'] ?? '—' }}</p>
                            <p class="text-white/60 text-xs lato-regular mt-0.5">Humor</p>
                        </div>
                        <div class="text-center border-x border-white/20 px-2 sm:px-4">
                            <p class="text-2xl sm:text-3xl lato-black">{{ $cl['pesquisaScore'] ?? '—' }}</p>
                            <p class="text-white/60 text-xs lato-regular mt-0.5">Pesquisas</p>
                        </div>
                        <div class="text-center">
                            <p class="text-2xl sm:text-3xl lato-black">{{ $cl['feedbackScore'] ?? '—' }}</p>
                            <p class="text-white/60 text-xs lato-regular mt-0.5">Feedbacks</p>
                        </div>
                    </div>
                </div>
                {{-- Barra de progresso --}}
                @if($cl['climaGeral'] !== null)
                <div class="relative mt-5 bg-white/20 rounded-full h-2">
                    <div class="bg-white h-2 rounded-full transition-all" style="width: {{ $cl['climaGeral'] }}%"></div>
                </div>
                @endif
            </div>

            {{-- eNPS + Humor Distribuição --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- eNPS --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-1 flex items-center gap-2">
                        <x-lucide-trending-up class="w-4 h-4 text-indigo-500" />
                        eNPS
                        <span class="ml-auto text-xs text-slate-400 lato-regular font-normal">Employee Net Promoter Score</span>
                    </h3>

                    @if($cl['totalRespostas'] > 0)
                    <div class="flex items-center justify-between mt-4">
                        <div class="text-center flex-1">
                            <p class="text-4xl lato-black {{ $cl['eNPS'] >= 50 ? 'text-emerald-600 dark:text-emerald-400' : ($cl['eNPS'] >= 0 ? 'text-amber-500' : 'text-rose-500') }}">
                                {{ $cl['eNPS'] >= 0 ? '+' : '' }}{{ $cl['eNPS'] }}
                            </p>
                            <p class="text-xs text-slate-400 lato-regular mt-1">Score eNPS</p>
                        </div>
                        <div class="flex-1 space-y-2 border-l border-slate-100 dark:border-slate-700 pl-4 ml-4">
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 lato-regular text-emerald-600 dark:text-emerald-400">
                                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Promotores (9-10)
                                </span>
                                <span class="lato-bold text-slate-700 dark:text-white">{{ $cl['promotores'] }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 lato-regular text-amber-500">
                                    <span class="w-2 h-2 rounded-full bg-amber-400"></span> Neutros (7-8)
                                </span>
                                <span class="lato-bold text-slate-700 dark:text-white">{{ $cl['neutros'] }}</span>
                            </div>
                            <div class="flex items-center justify-between text-xs">
                                <span class="flex items-center gap-1.5 lato-regular text-rose-500">
                                    <span class="w-2 h-2 rounded-full bg-rose-400"></span> Detratores (0-6)
                                </span>
                                <span class="lato-bold text-slate-700 dark:text-white">{{ $cl['detratores'] }}</span>
                            </div>
                            <div class="pt-1 border-t border-slate-100 dark:border-slate-700">
                                <div class="flex h-2 rounded-full overflow-hidden">
                                    @php $total = $cl['totalRespostas']; @endphp
                                    <div class="bg-emerald-400 transition-all" style="width:{{ $total > 0 ? round(($cl['promotores']/$total)*100) : 0 }}%"></div>
                                    <div class="bg-amber-400 transition-all" style="width:{{ $total > 0 ? round(($cl['neutros']/$total)*100) : 0 }}%"></div>
                                    <div class="bg-rose-400 transition-all" style="width:{{ $total > 0 ? round(($cl['detratores']/$total)*100) : 0 }}%"></div>
                                </div>
                                <p class="text-[10px] text-slate-400 lato-regular mt-1">{{ $total }} respostas de pesquisas</p>
                            </div>
                        </div>
                    </div>
                    @if($cl['exitTotal'] > 0)
                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between text-xs">
                        <span class="text-slate-400 lato-regular flex items-center gap-1.5"><x-lucide-log-out class="w-3.5 h-3.5" /> Recomendariam (entrev. saída)</span>
                        <span class="lato-bold text-slate-700 dark:text-white">{{ $cl['exitRecomenda'] }}/{{ $cl['exitTotal'] }} ({{ round(($cl['exitRecomenda']/$cl['exitTotal'])*100) }}%)</span>
                    </div>
                    @endif
                    @else
                    <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                        <x-lucide-trending-up class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Nenhuma pesquisa com escala 0-10 no período</p>
                    </div>
                    @endif
                </div>

                {{-- Humor: distribuição --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-smile class="w-4 h-4 text-amber-500" />
                        Distribuição de Humor
                        <span class="ml-auto text-xs text-slate-400 lato-regular font-normal">{{ $cl['totalCheckins'] }} check-ins</span>
                    </h3>
                    @if($cl['totalCheckins'] > 0)
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-4">
                        @foreach($cl['moodDistrib'] as $key => $m)
                        <div class="rounded-xl p-3 text-center" style="background: {{ $m['hex'] }}18; border: 1px solid {{ $m['hex'] }}40">
                            <x-dynamic-component :component="'lucide-' . $m['icon']" class="w-6 h-6 mx-auto mb-1" style="color: {{ $m['hex'] }}" />
                            <p class="text-xl lato-black text-slate-800 dark:text-white">{{ $m['count'] }}</p>
                            <p class="text-xs lato-regular text-slate-500 dark:text-slate-400">{{ $m['label'] }}</p>
                            <p class="text-xs lato-bold mt-0.5" style="color: {{ $m['hex'] }}">{{ $m['pct'] }}%</p>
                        </div>
                        @endforeach
                    </div>
                    {{-- Barra proporcional --}}
                    <div class="flex h-3 rounded-full overflow-hidden">
                        @foreach($cl['moodDistrib'] as $m)
                        <div class="transition-all" style="width:{{ $m['pct'] }}%; background:{{ $m['hex'] }}"></div>
                        @endforeach
                    </div>
                    <div class="mt-2 flex items-center justify-between">
                        <p class="text-xs text-slate-400 lato-regular">Score de bem-estar</p>
                        <p class="text-sm lato-black {{ $cl['moodScore'] >= 70 ? 'text-emerald-600 dark:text-emerald-400' : ($cl['moodScore'] >= 50 ? 'text-amber-500' : 'text-rose-500') }}">
                            {{ $cl['moodScore'] }}/100
                        </p>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                        <x-lucide-smile class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Nenhum check-in de humor no período</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Tendência Humor + Feedbacks --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Tendência diária de humor --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-activity class="w-4 h-4 text-indigo-500" />
                        Tendência de Humor Diário
                    </h3>
                    @if(count($cl['moodTrend']) > 0)
                    <div id="clima-mood-chart" wire:ignore
                         x-data
                         x-init="
                            const labels = @js(array_keys($cl['moodTrend']));
                            const data   = @js(array_column(array_values($cl['moodTrend']), 'score'));
                            new ApexCharts(document.getElementById('clima-mood-chart'), {
                                chart:  { type: 'area', height: 180, toolbar: { show: false }, background: 'transparent', sparkline: { enabled: false } },
                                series: [{ name: 'Score humor', data }],
                                xaxis:  { categories: labels, labels: { style: { fontSize: '10px' } } },
                                yaxis:  { min: 0, max: 100, tickAmount: 4, labels: { style: { fontSize: '10px' }, formatter: v => v + '%' } },
                                colors: ['#8b5cf6'],
                                fill:   { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
                                stroke: { curve: 'smooth', width: 2 },
                                dataLabels: { enabled: false },
                                grid:   { borderColor: 'rgba(148,163,184,0.15)' },
                                tooltip: { y: { formatter: v => v + '/100' } },
                                theme:  { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center h-40 text-slate-400">
                        <x-lucide-activity class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Sem dados no período</p>
                    </div>
                    @endif
                </div>

                {{-- Feedbacks breakdown --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-message-square class="w-4 h-4 text-blue-500" />
                        Feedbacks no Período
                        <span class="ml-auto text-xs text-slate-400 lato-regular font-normal">{{ $cl['totalFeedbacks'] }} total</span>
                    </h3>
                    @if($cl['totalFeedbacks'] > 0)
                    <div class="space-y-3">
                        @php
                        $fbItems = [
                            ['label' => 'Reconhecimentos', 'count' => $cl['reconhecimentos'], 'color' => 'emerald', 'icon' => 'star'],
                            ['label' => 'Sugestões',       'count' => $cl['sugestoes'],       'color' => 'blue',    'icon' => 'lightbulb'],
                            ['label' => 'Alertas',         'count' => $cl['alertas'],         'color' => 'amber',   'icon' => 'alert-triangle'],
                            ['label' => 'Críticos',        'count' => $cl['criticos'],        'color' => 'rose',    'icon' => 'siren'],
                        ];
                        @endphp
                        @foreach($fbItems as $fb)
                        @php $pct = $cl['totalFeedbacks'] > 0 ? round(($fb['count']/$cl['totalFeedbacks'])*100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="flex items-center gap-1.5 text-xs lato-regular text-slate-600 dark:text-slate-300">
                                    <x-dynamic-component :component="'lucide-'.$fb['icon']" class="w-3.5 h-3.5 text-{{ $fb['color'] }}-500" />
                                    {{ $fb['label'] }}
                                </span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-white">{{ $fb['count'] }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                                <div class="bg-{{ $fb['color'] }}-400 h-1.5 rounded-full transition-all" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                        @endforeach

                        <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between">
                            <span class="text-xs text-slate-400 lato-regular">Índice de positividade</span>
                            <span class="text-sm lato-black {{ $cl['feedbackScore'] >= 70 ? 'text-emerald-600 dark:text-emerald-400' : ($cl['feedbackScore'] >= 50 ? 'text-amber-500' : 'text-rose-500') }}">
                                {{ $cl['feedbackScore'] }}/100
                            </span>
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center h-40 text-slate-400">
                        <x-lucide-message-square class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Nenhum feedback no período</p>
                    </div>
                    @endif
                </div>
            </div>

            {{-- Painel interpretativo --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <x-lucide-info class="w-4 h-4 text-slate-400" />
                    Como interpretar o eNPS
                </h3>
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-3 text-center text-xs">
                    @foreach([
                        ['range'=>'+75 a +100','label'=>'Excelente','cor'=>'emerald','desc'=>'Cultura forte, baixíssima rotatividade'],
                        ['range'=>'+50 a +74', 'label'=>'Muito bom', 'cor'=>'blue',   'desc'=>'Colaboradores majoritariamente engajados'],
                        ['range'=>'+0 a +49',  'label'=>'Bom',       'cor'=>'amber',  'desc'=>'Espaço para melhorias focadas'],
                        ['range'=>'Negativo',  'label'=>'Crítico',   'cor'=>'rose',   'desc'=>'Atenção urgente ao clima organizacional'],
                    ] as $faixa)
                    <div class="rounded-xl p-3 bg-{{ $faixa['cor'] }}-50 dark:bg-{{ $faixa['cor'] }}-900/20 border border-{{ $faixa['cor'] }}-200 dark:border-{{ $faixa['cor'] }}-800/40">
                        <p class="lato-black text-{{ $faixa['cor'] }}-700 dark:text-{{ $faixa['cor'] }}-400 text-sm">{{ $faixa['range'] }}</p>
                        <p class="lato-bold text-{{ $faixa['cor'] }}-600 dark:text-{{ $faixa['cor'] }}-400 mt-0.5">{{ $faixa['label'] }}</p>
                        <p class="text-slate-500 dark:text-slate-400 lato-regular mt-1">{{ $faixa['desc'] }}</p>
                    </div>
                    @endforeach
                </div>
            </div>

        </div>
        @endif


