        {{-- ═══════════════════════════════════════════════════════════
             ABA: RELATÓRIO DE TURNOVER
        ═══════════════════════════════════════════════════════════ --}}
        @if ($aba === 'relatorio-turnover')
        @php $tv = $this->turnoverRelatorio; @endphp
        <div class="p-4 md:p-6 space-y-6">

            {{-- Cabeçalho --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white">Relatório de Turnover</h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">
                        {{ \Carbon\Carbon::parse($tv['inicio'])->format('d/m/Y') }}
                        –
                        {{ \Carbon\Carbon::parse($tv['fim'])->format('d/m/Y') }}
                        · {{ (int) $tv['meses'] }} {{ (int) $tv['meses'] == 1 ? 'mês' : 'meses' }}
                    </p>
                </div>
            </div>

            {{-- Filtros --}}
            @php $depList = \App\Models\Department::orderBy('name')->get(); @endphp
            <div class="flex flex-col gap-3 p-4 bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700">

                {{-- Busca: período como campo de texto top --}}
                <div class="relative flex gap-2">
                    <div class="relative flex-1">
                        <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                        <input wire:model="tvInicio" type="date"
                               class="w-full pl-9 pr-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent lato-regular
                                      {{ $tvInicio ? 'border-pink-400 bg-pink-50 dark:bg-pink-900/10 text-pink-700 dark:text-pink-300' : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200' }}" />
                    </div>
                    <span class="flex items-center text-gray-400 text-sm">→</span>
                    <div class="relative flex-1">
                        <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                        <input wire:model="tvFim" type="date"
                               class="w-full pl-9 pr-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-pink-400 focus:border-transparent lato-regular
                                      {{ $tvFim ? 'border-pink-400 bg-pink-50 dark:bg-pink-900/10 text-pink-700 dark:text-pink-300' : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200' }}" />
                    </div>
                    <button wire:click="tvAplicar" type="button"
                            class="cursor-pointer flex items-center gap-1.5 px-4 py-2 text-sm lato-bold text-white bg-gradient-to-r from-pink-500 to-rose-600 hover:from-pink-600 hover:to-rose-700 rounded-lg transition shrink-0">
                        <x-lucide-filter class="w-3.5 h-3.5" /> Aplicar
                    </button>
                </div>

                {{-- Linha: Departamento + Tipo --}}
                <div class="grid grid-cols-2 gap-2">

                    {{-- Departamento --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                                class="w-full flex items-center justify-between text-sm border rounded-lg px-3 py-2 bg-white dark:bg-slate-700 transition cursor-pointer
                                       {{ $tvDepartamento ? 'border-pink-400 bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-300' : 'border-gray-200 dark:border-slate-600 text-gray-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate">
                                <x-lucide-building-2 class="w-3.5 h-3.5 flex-shrink-0" />
                                <span class="truncate text-xs font-medium">
                                    {{ $tvDepartamento ? ($depList->firstWhere('id', $tvDepartamento)?->name ?? 'Departamento') : 'Departamento' }}
                                </span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                             class="absolute mt-1 w-56 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-lg z-20 py-1 max-h-60 overflow-y-auto">
                            <button @click="open=false; $wire.set('tvDepartamento',''); $wire.call('tvAplicar')"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 dark:text-slate-400 flex items-center gap-2 cursor-pointer lato-regular">
                                <x-lucide-building-2 class="w-3.5 h-3.5" /> Todos os departamentos
                            </button>
                            <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                            @foreach ($depList as $dep)
                            <button @click="open=false; $wire.set('tvDepartamento','{{ $dep->id }}'); $wire.call('tvAplicar')"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 flex items-center justify-between cursor-pointer lato-regular
                                           {{ $tvDepartamento == $dep->id ? 'bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-300 font-medium' : 'text-gray-700 dark:text-slate-200' }}">
                                {{ $dep->name }}
                                @if($tvDepartamento == $dep->id)
                                    <x-lucide-check class="w-3.5 h-3.5 text-pink-500" />
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tipo --}}
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" type="button"
                                class="w-full flex items-center justify-between text-sm border rounded-lg px-3 py-2 bg-white dark:bg-slate-700 transition cursor-pointer
                                       {{ $tvTipo ? 'border-pink-400 bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-300' : 'border-gray-200 dark:border-slate-600 text-gray-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate">
                                <x-lucide-tag class="w-3.5 h-3.5 flex-shrink-0" />
                                <span class="truncate text-xs font-medium">
                                    {{ $tvTipo ? (\App\Models\RhDesligamento::$tipoLabels[$tvTipo] ?? 'Tipo') : 'Tipo de desligamento' }}
                                </span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="open" @click.outside="open = false" x-transition
                             class="absolute right-0 mt-1 w-56 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-lg shadow-lg z-20 py-1">
                            <button @click="open=false; $wire.set('tvTipo',''); $wire.call('tvAplicar')"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 dark:text-slate-400 flex items-center gap-2 cursor-pointer lato-regular">
                                <x-lucide-tag class="w-3.5 h-3.5" /> Todos os tipos
                            </button>
                            <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                            @foreach (\App\Models\RhDesligamento::$tipoLabels as $key => $label)
                            <button @click="open=false; $wire.set('tvTipo','{{ $key }}'); $wire.call('tvAplicar')"
                                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 flex items-center justify-between cursor-pointer lato-regular
                                           {{ $tvTipo === $key ? 'bg-pink-50 dark:bg-pink-900/20 text-pink-700 dark:text-pink-300 font-medium' : 'text-gray-700 dark:text-slate-200' }}">
                                {{ $label }}
                                @if($tvTipo === $key)
                                    <x-lucide-check class="w-3.5 h-3.5 text-pink-500" />
                                @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- Chips filtros ativos + Limpar --}}
                @php $temFiltrosTv = $tvInicio || $tvFim || $tvDepartamento || $tvTipo; @endphp
                @if($temFiltrosTv)
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-xs text-gray-400 lato-regular">Filtros:</span>
                    @if($tvInicio)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 text-xs lato-bold">
                        <x-lucide-calendar class="w-3 h-3" /> De {{ \Carbon\Carbon::parse($tvInicio)->format('d/m/Y') }}
                        <button wire:click="$set('tvInicio',''); tvAplicar()" class="cursor-pointer ml-0.5 hover:text-pink-900 transition"><x-lucide-x class="w-3 h-3" /></button>
                    </span>
                    @endif
                    @if($tvFim)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 text-xs lato-bold">
                        <x-lucide-calendar class="w-3 h-3" /> Até {{ \Carbon\Carbon::parse($tvFim)->format('d/m/Y') }}
                        <button wire:click="$set('tvFim',''); tvAplicar()" class="cursor-pointer ml-0.5 hover:text-pink-900 transition"><x-lucide-x class="w-3 h-3" /></button>
                    </span>
                    @endif
                    @if($tvDepartamento)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 text-xs lato-bold">
                        <x-lucide-building-2 class="w-3 h-3" /> {{ $depList->firstWhere('id', $tvDepartamento)?->name }}
                        <button wire:click="$set('tvDepartamento',''); tvAplicar()" class="cursor-pointer ml-0.5 hover:text-pink-900 transition"><x-lucide-x class="w-3 h-3" /></button>
                    </span>
                    @endif
                    @if($tvTipo)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-pink-100 dark:bg-pink-900/30 text-pink-700 dark:text-pink-300 text-xs lato-bold">
                        <x-lucide-tag class="w-3 h-3" /> {{ \App\Models\RhDesligamento::$tipoLabels[$tvTipo] ?? $tvTipo }}
                        <button wire:click="$set('tvTipo',''); tvAplicar()" class="cursor-pointer ml-0.5 hover:text-pink-900 transition"><x-lucide-x class="w-3 h-3" /></button>
                    </span>
                    @endif
                    <button wire:click="tvLimpar" type="button"
                            class="cursor-pointer ml-auto flex items-center gap-1 text-xs text-gray-400 hover:text-gray-600 dark:hover:text-slate-200 transition lato-regular">
                        <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar tudo
                    </button>
                </div>
                @endif

            </div>

            {{-- KPI Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center mb-3">
                        <x-lucide-user-minus class="w-4 h-4 text-white" />
                    </div>
                    <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $tv['total'] }}</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Desligamentos</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-amber-500 to-orange-600 flex items-center justify-center mb-3">
                        <x-lucide-percent class="w-4 h-4 text-white" />
                    </div>
                    <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $tv['taxa'] }}%</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Taxa no período · {{ $tv['taxaMensal'] }}%/mês</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center mb-3">
                        <x-lucide-banknote class="w-4 h-4 text-white" />
                    </div>
                    <p class="text-2xl lato-black text-slate-800 dark:text-white">R$ {{ number_format($tv['custoRescisao'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Custo rescisório total</p>
                </div>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-teal-500 to-cyan-600 flex items-center justify-center mb-3">
                        <x-lucide-trending-up class="w-4 h-4 text-white" />
                    </div>
                    <p class="text-2xl lato-black text-slate-800 dark:text-white">R$ {{ number_format($tv['custoTotal'], 0, ',', '.') }}</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">Custo total (rescisão + reposição)</p>
                </div>
            </div>

            {{-- Gráfico de tendência + Tipos --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Tendência mensal --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-bar-chart-2 class="w-4 h-4 text-pink-500" />
                        Tendência Mensal
                    </h3>
                    @if(count($tv['tendencia']) > 0)
                    <div id="tv-chart" wire:ignore
                         x-data
                         x-init="
                            const series = @js(array_column($tv['tendencia'], 'total'));
                            const cats   = @js(array_column($tv['tendencia'], 'mes'));
                            new ApexCharts(document.getElementById('tv-chart'), {
                                chart: { type: 'bar', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series: [{ name: 'Desligamentos', data: series }],
                                xaxis: { categories: cats, labels: { style: { fontSize: '10px' } } },
                                yaxis: { tickAmount: 4, labels: { style: { fontSize: '10px' } } },
                                colors: ['#f43f5e'],
                                plotOptions: { bar: { borderRadius: 4, columnWidth: '55%' } },
                                dataLabels: { enabled: false },
                                grid: { borderColor: 'rgba(148,163,184,0.15)' },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center h-48 text-slate-400">
                        <x-lucide-bar-chart-2 class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-sm lato-regular">Nenhum dado no período</p>
                    </div>
                    @endif
                </div>

                {{-- Por tipo --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-pie-chart class="w-4 h-4 text-pink-500" />
                        Por Tipo
                    </h3>
                    @if(count($tv['porTipo']) > 0)
                    <div class="space-y-2.5">
                        @foreach ($tv['porTipo'] as $item)
                        @php $pct = $tv['total'] > 0 ? round(($item['total'] / $tv['total']) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs lato-regular text-slate-600 dark:text-slate-300 truncate max-w-[70%]">{{ $item['label'] }}</span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $item['total'] }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-pink-500 to-rose-500 h-1.5 rounded-full" style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-slate-400 lato-regular text-center mt-8">Sem dados</p>
                    @endif
                </div>
            </div>

            {{-- Por Departamento + Por Cargo --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Por departamento --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-building-2 class="w-4 h-4 text-indigo-500" />
                        Por Departamento
                    </h3>
                    @if(count($tv['porDepto']) > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-xs lato-regular">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-100 dark:border-slate-700">
                                    <th class="pb-2 text-left font-medium">Departamento</th>
                                    <th class="pb-2 text-center font-medium">Deslig.</th>
                                    <th class="pb-2 text-right font-medium">Custo rescisório</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                                @foreach ($tv['porDepto'] as $row)
                                <tr class="text-slate-700 dark:text-slate-300">
                                    <td class="py-2 truncate max-w-[160px]">{{ $row['nome'] }}</td>
                                    <td class="py-2 text-center lato-bold text-slate-800 dark:text-white">{{ $row['total'] }}</td>
                                    <td class="py-2 text-right text-slate-500">R$ {{ number_format($row['custo'], 0, ',', '.') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-xs text-slate-400 lato-regular text-center mt-8">Sem dados</p>
                    @endif
                </div>

                {{-- Por cargo --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-briefcase class="w-4 h-4 text-indigo-500" />
                        Por Cargo (Top 10)
                    </h3>
                    @if(count($tv['porCargo']) > 0)
                    <div class="space-y-2">
                        @foreach ($tv['porCargo'] as $row)
                        @php $pctC = $tv['total'] > 0 ? round(($row['total'] / $tv['total']) * 100) : 0; @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-xs lato-regular text-slate-600 dark:text-slate-300 truncate flex-1 min-w-0">{{ $row['nome'] }}</span>
                            <div class="w-24 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 shrink-0">
                                <div class="bg-gradient-to-r from-blue-500 to-indigo-600 h-1.5 rounded-full" style="width: {{ $pctC }}%"></div>
                            </div>
                            <span class="text-xs lato-bold text-slate-700 dark:text-white w-5 text-right">{{ $row['total'] }}</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-slate-400 lato-regular text-center mt-8">Sem dados</p>
                    @endif
                </div>
            </div>

            {{-- Motivos de desligamento + Satisfação --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Motivos --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-message-circle class="w-4 h-4 text-amber-500" />
                        Motivos de Desligamento (Entrevistas)
                    </h3>
                    @if(count($tv['motivos']) > 0)
                    <div class="space-y-3">
                        @foreach ($tv['motivos'] as $m)
                        @php $pctM = ($tv['total'] > 0) ? round(($m['total'] / $tv['total']) * 100) : 0; @endphp
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs lato-regular text-slate-600 dark:text-slate-300">{{ $m['label'] }}</span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-white">{{ $m['total'] }} <span class="text-slate-400 font-normal">({{ $pctM }}%)</span></span>
                            </div>
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                                <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-1.5 rounded-full" style="width: {{ $pctM }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <x-lucide-message-circle class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-sm lato-regular">Nenhuma entrevista de desligamento encontrada</p>
                    </div>
                    @endif
                </div>

                {{-- Satisfação e Recomendaria --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-5">
                    <div>
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-2">
                            <x-lucide-star class="w-4 h-4 text-yellow-500" />
                            Satisfação Média
                        </h3>
                        @if($tv['satisfacaoMedia'])
                        <div class="text-center py-4">
                            <p class="text-4xl lato-black text-slate-800 dark:text-white">{{ number_format($tv['satisfacaoMedia'], 1) }}</p>
                            <p class="text-xs text-slate-400 lato-regular mt-1">de 5 pontos</p>
                            <div class="flex justify-center gap-1 mt-3">
                                @for($s = 1; $s <= 5; $s++)
                                <div class="w-3 h-3 rounded-full {{ $s <= round($tv['satisfacaoMedia']) ? 'bg-yellow-400' : 'bg-slate-200 dark:bg-slate-600' }}"></div>
                                @endfor
                            </div>
                        </div>
                        @else
                        <p class="text-xs text-slate-400 lato-regular text-center py-4">Sem dados</p>
                        @endif
                    </div>
                    <div class="border-t border-slate-100 dark:border-slate-700 pt-4">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-2">
                            <x-lucide-thumbs-up class="w-4 h-4 text-green-500" />
                            Recomendaria a empresa
                        </h3>
                        @if($tv['recomendariaPct'] !== null)
                        <div class="text-center py-2">
                            <p class="text-4xl lato-black {{ $tv['recomendariaPct'] >= 70 ? 'text-green-600' : ($tv['recomendariaPct'] >= 40 ? 'text-amber-500' : 'text-rose-500') }}">{{ $tv['recomendariaPct'] }}%</p>
                            <p class="text-xs text-slate-400 lato-regular mt-1">dos colaboradores</p>
                        </div>
                        @else
                        <p class="text-xs text-slate-400 lato-regular text-center py-2">Sem dados</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Custo estimado de reposição --}}
            <div class="bg-gradient-to-r from-pink-50 to-rose-50 dark:from-pink-900/20 dark:to-rose-900/20 border border-pink-200 dark:border-pink-800/40 rounded-2xl p-5">
                <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                    <div class="w-12 h-12 rounded-2xl bg-gradient-to-br from-pink-500 to-rose-600 flex items-center justify-center shrink-0 shadow-lg shadow-pink-500/20">
                        <x-lucide-calculator class="w-6 h-6 text-white" />
                    </div>
                    <div class="flex-1">
                        <h3 class="text-sm lato-bold text-slate-800 dark:text-white">Custo Estimado de Reposição</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-0.5">
                            Calculado com base no salário médio de R$ {{ number_format($tv['salarioMedio'], 2, ',', '.') }} × 12 meses × 1,5 (benchmark de mercado) × {{ $tv['total'] }} colaboradores
                        </p>
                    </div>
                    <div class="text-right shrink-0">
                        <p class="text-2xl lato-black text-rose-600 dark:text-rose-400">R$ {{ number_format($tv['custoReposicao'], 0, ',', '.') }}</p>
                        <p class="text-xs text-slate-400 lato-regular">custo de reposição</p>
                    </div>
                </div>
                <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="bg-white/60 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-500 lato-regular">Rescisório</p>
                        <p class="text-lg lato-black text-slate-800 dark:text-white">R$ {{ number_format($tv['custoRescisao'], 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/60 dark:bg-slate-800/60 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-500 lato-regular">Reposição</p>
                        <p class="text-lg lato-black text-slate-800 dark:text-white">R$ {{ number_format($tv['custoReposicao'], 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-white/60 dark:bg-slate-800/60 rounded-xl p-3 text-center border-2 border-rose-200 dark:border-rose-800/50">
                        <p class="text-xs text-rose-500 lato-bold">Total</p>
                        <p class="text-lg lato-black text-rose-600 dark:text-rose-400">R$ {{ number_format($tv['custoTotal'], 0, ',', '.') }}</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- ── COMO INTERPRETAR ────────────────────────────────────── --}}
        <details class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
            <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none list-none">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-pink-50 dark:bg-pink-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-book-open class="w-3.5 h-3.5 text-pink-600 dark:text-pink-400" />
                    </div>
                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Como interpretar o Relatório de Turnover</span>
                </div>
                <x-lucide-chevron-down class="w-4 h-4 text-slate-400 transition-transform duration-200 group-open:rotate-180 shrink-0" />
            </summary>

            <div class="px-5 pb-5 pt-1 border-t border-slate-100 dark:border-slate-700 space-y-4">

                <p class="text-xs lato-regular text-slate-500 dark:text-slate-400 leading-relaxed">
                    O Relatório de Turnover analisa a rotatividade de colaboradores no período selecionado, comparando com a meta definida e estimando o impacto financeiro das saídas.
                </p>

                {{-- Indicadores --}}
                <div>
                    <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Indicadores</p>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-2">
                        @foreach([
                            ['icon'=>'log-out',      'clr'=>'text-rose-500',    'title'=>'Taxa de Turnover',      'desc'=>'% de saídas em relação ao headcount médio do período. Meta padrão: abaixo de 1% ao mês ou 12% ao ano.'],
                            ['icon'=>'users',        'clr'=>'text-blue-500',    'title'=>'Headcount Médio',        'desc'=>'Média de colaboradores ativos no período — base de cálculo da taxa de turnover.'],
                            ['icon'=>'bar-chart-2',  'clr'=>'text-indigo-500',  'title'=>'Turnover Mensal',        'desc'=>'Gráfico de barras mostrando a evolução mês a mês das saídas e a linha de meta.'],
                            ['icon'=>'pie-chart',    'clr'=>'text-violet-500',  'title'=>'Motivos de Saída',       'desc'=>'Distribuição dos desligamentos por tipo: voluntário, sem justa causa, com justa causa, acordo e aposentadoria.'],
                            ['icon'=>'building-2',   'clr'=>'text-cyan-500',    'title'=>'Por Departamento',       'desc'=>'Ranking de departamentos com maior rotatividade — útil para identificar áreas críticas.'],
                            ['icon'=>'banknote',     'clr'=>'text-emerald-500', 'title'=>'Custo de Reposição',     'desc'=>'Estimativa financeira baseada em: salário médio × 12 meses × 1,5 (benchmark) × nº de saídas.'],
                        ] as $item)
                        <div class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                            <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                <x-dynamic-component :component="'lucide-'.$item['icon']" class="w-3.5 h-3.5 {{ $item['clr'] }}" />
                            </div>
                            <div>
                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $item['title'] }}</p>
                                <p class="text-[11px] lato-regular text-slate-400 leading-snug mt-0.5">{{ $item['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Referências --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    @foreach([
                        ['icon'=>'target',        'clr'=>'text-rose-500',    'title'=>'Meta de referência',   'desc'=>'Turnover saudável: até 1% ao mês (12% ao ano). Acima disso indica problema de retenção.'],
                        ['icon'=>'trending-down', 'clr'=>'text-amber-500',   'title'=>'Turnover involuntário', 'desc'=>'Demissões por iniciativa da empresa (sem justa causa, com justa causa). Sinaliza custos de gestão.'],
                        ['icon'=>'trending-up',   'clr'=>'text-indigo-500',  'title'=>'Turnover voluntário',  'desc'=>'Pedidos de demissão. Alta taxa pode indicar problemas de clima, liderança ou remuneração.'],
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

        @endif

