    {{-- ══════════════════════════════════════════════════════════════════
         ABA: MAPA DE COMPETÊNCIAS (Skills Matrix)
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($aba === 'mapa-competencias')
    @php
        $mapa      = $this->mapaCompetencias;
        $depts     = $this->departamentos;
        $nivelAnos = range(now()->year - 2, now()->year);
        $nivelLabels = [1=>'Básico',2=>'Elementar',3=>'Intermediário',4=>'Avançado',5=>'Expert'];
        $gapCores  = [
            0 => 'bg-emerald-500 text-white',
            1 => 'bg-yellow-400 text-slate-800',
            2 => 'bg-orange-400 text-white',
            3 => 'bg-rose-500 text-white',
        ];
        $gapLabel = [0=>'OK',1=>'Gap leve',2=>'Gap médio',3=>'Gap crítico'];
    @endphp
    <div class="flex-1 overflow-y-auto p-4 sm:p-6 space-y-5">

        {{-- Cabeçalho --}}
        <div class="flex flex-wrap items-start justify-between gap-4">
            <div>
                <h2 class="text-lg lato-black text-slate-800 dark:text-white flex items-center gap-2">
                    <x-lucide-layout-grid class="w-5 h-5 text-cyan-500" />
                    Mapa de Competências
                </h2>
                <p class="text-sm text-slate-400 lato-regular mt-0.5">
                    Skills matrix visual — gaps identificados com base nos DPIs · Ano {{ $mapa['ano'] }}
                </p>
            </div>
            {{-- Filtros --}}
            <div class="flex flex-wrap items-center gap-2">

                {{-- Departamento --}}
                @php $selDeptMapa = $mapaDept ? $depts->firstWhere('id', $mapaDept) : null; @endphp
                <div x-data="{
                        open: false,
                        search: '',
                        get filtered() {
                            if (!this.search) return {{ $depts->toJson() }};
                            const q = this.search.toLowerCase();
                            return {{ $depts->toJson() }}.filter(d => d.name.toLowerCase().includes(q));
                        }
                     }"
                     @click.outside="open = false"
                     class="relative">

                    <button type="button" @click="open = !open"
                            class="cursor-pointer flex items-center gap-2 pl-3 pr-2.5 py-2 text-sm lato-regular rounded-xl border bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 transition min-w-[200px]"
                            :class="open ? 'border-cyan-400 ring-2 ring-cyan-400/20' : 'border-slate-200 dark:border-slate-600 hover:border-cyan-300'">
                        <x-lucide-building-2 class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                        <span class="flex-1 text-left truncate">
                            @if($selDeptMapa)
                                <span class="text-slate-800 dark:text-slate-100 lato-bold">{{ $selDeptMapa->name }}</span>
                            @else
                                <span class="text-slate-400">Todos os departamentos</span>
                            @endif
                        </span>
                        @if($mapaDept)
                            <span wire:click.stop="$set('mapaDept', '')" @click.stop="open = false"
                                  class="p-0.5 rounded hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-400 hover:text-slate-600 transition">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M18 6 6 18M6 6l12 12"/></svg>
                            </span>
                        @endif
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>

                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-1.5 w-full min-w-[220px] bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl overflow-hidden"
                         style="display:none">
                        <div class="p-2 border-b border-slate-100 dark:border-slate-700">
                            <div class="flex items-center gap-2 px-2.5 py-1.5 bg-slate-50 dark:bg-slate-900 rounded-lg border border-slate-200 dark:border-slate-600 focus-within:border-cyan-400 focus-within:ring-1 focus-within:ring-cyan-400/20 transition">
                                <svg class="w-3.5 h-3.5 text-slate-400 shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/></svg>
                                <input x-model="search" type="text" placeholder="Buscar departamento..."
                                       x-ref="si" x-init="$watch('open', v => v && $nextTick(() => $refs.si.focus()))"
                                       @keydown.escape="open = false"
                                       class="flex-1 bg-transparent text-xs text-slate-700 dark:text-slate-200 placeholder-slate-400 outline-none" />
                            </div>
                        </div>
                        <ul class="max-h-52 overflow-y-auto py-1">
                            <li wire:click="$set('mapaDept', '')" @click="open = false; search = ''"
                                class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer text-sm lato-regular transition
                                       {{ !$mapaDept ? 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300' : 'text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <x-lucide-building-2 class="w-3.5 h-3.5 shrink-0 {{ !$mapaDept ? 'text-cyan-500' : 'text-slate-400' }}" />
                                <span class="flex-1">Todos os departamentos</span>
                                @if(!$mapaDept)<svg class="w-3.5 h-3.5 text-cyan-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>@endif
                            </li>
                            <template x-for="d in filtered" :key="d.id">
                                <li @click="$wire.set('mapaDept', d.id); open = false; search = ''"
                                    class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer text-sm lato-regular transition"
                                    :class="String({{ $mapaDept ?: 'null' }}) === String(d.id)
                                        ? 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300'
                                        : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700'">
                                    <span class="w-2 h-2 rounded-full bg-cyan-400 shrink-0"></span>
                                    <span class="flex-1 truncate" x-text="d.name"></span>
                                    <template x-if="String({{ $mapaDept ?: 'null' }}) === String(d.id)">
                                        <svg class="w-3.5 h-3.5 text-cyan-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>
                                    </template>
                                </li>
                            </template>
                            <template x-if="filtered.length === 0">
                                <li class="px-3 py-4 text-center text-xs text-slate-400">Nenhum departamento encontrado</li>
                            </template>
                        </ul>
                        <div class="px-3 py-1.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                            <span class="text-xs text-slate-400" x-text="filtered.length + ' departamento(s)'"></span>
                        </div>
                    </div>
                </div>

                {{-- Ano --}}
                @php $anos = range(now()->year - 2, now()->year); @endphp
                <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                    <button type="button" @click="open = !open"
                            class="cursor-pointer flex items-center gap-2 pl-3 pr-2.5 py-2 text-sm lato-regular rounded-xl border bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 transition"
                            :class="open ? 'border-cyan-400 ring-2 ring-cyan-400/20' : 'border-slate-200 dark:border-slate-600 hover:border-cyan-300'">
                        <x-lucide-calendar class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                        <span class="lato-bold text-slate-800 dark:text-slate-100">{{ $mapaAno }}</span>
                        <svg class="w-3.5 h-3.5 text-slate-400 shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6"/></svg>
                    </button>
                    <div x-show="open"
                         x-transition:enter="transition ease-out duration-150"
                         x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                         x-transition:leave="transition ease-in duration-100"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0 scale-95"
                         class="absolute right-0 z-50 mt-1.5 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600 rounded-xl shadow-xl overflow-hidden"
                         style="display:none">
                        <ul class="py-1">
                            @foreach($anos as $y)
                            <li wire:click="$set('mapaAno', {{ $y }})" @click="open = false"
                                class="flex items-center gap-2.5 px-4 py-2.5 cursor-pointer text-sm lato-regular transition
                                       {{ $mapaAno == $y ? 'bg-cyan-50 dark:bg-cyan-900/20 text-cyan-700 dark:text-cyan-300' : 'text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <x-lucide-calendar class="w-3.5 h-3.5 shrink-0 {{ $mapaAno == $y ? 'text-cyan-500' : 'text-slate-400' }}" />
                                <span class="flex-1">{{ $y }}</span>
                                @if($mapaAno == $y)<svg class="w-3.5 h-3.5 text-cyan-500 shrink-0" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="m5 13 4 4L19 7"/></svg>@endif
                            </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <button wire:click="mapaAplicar" type="button"
                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-cyan-500 to-blue-500 text-white text-sm lato-bold hover:from-cyan-600 hover:to-blue-600 transition shadow-sm">
                    <x-lucide-check class="w-3.5 h-3.5" />
                    Aplicar
                </button>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach ([
                ['label'=>'Funcionários',    'value'=>$mapa['kpis']['total_funcionarios'], 'sub'=>'ativos no período',      'bg'=>'bg-slate-100 dark:bg-slate-700',    'color'=>'text-slate-700 dark:text-slate-200',    'icon'=>'users'],
                ['label'=>'Com DPI ativo',   'value'=>$mapa['kpis']['com_dpi'],            'sub'=>$mapa['kpis']['cobertura_pct'].'% cobertura', 'bg'=>'bg-cyan-50 dark:bg-cyan-900/20',    'color'=>'text-cyan-700 dark:text-cyan-300',      'icon'=>'file-check'],
                ['label'=>'Total de Gaps',   'value'=>$mapa['kpis']['total_gaps'],          'sub'=>'pontos abaixo da meta',  'bg'=>'bg-amber-50 dark:bg-amber-900/20',    'color'=>'text-amber-700 dark:text-amber-300',    'icon'=>'trending-down'],
                ['label'=>'Gaps Críticos',   'value'=>$mapa['kpis']['criticos'],            'sub'=>'competências (média ≥3)', 'bg'=>'bg-rose-50 dark:bg-rose-900/20',      'color'=>'text-rose-700 dark:text-rose-300',      'icon'=>'alert-triangle'],
            ] as $kpi)
            <div class="rounded-xl {{ $kpi['bg'] }} p-4 flex items-center gap-3">
                <x-dynamic-component :component="'lucide-'.$kpi['icon']" class="w-5 h-5 {{ $kpi['color'] }} shrink-0" />
                <div>
                    <p class="text-2xl lato-black {{ $kpi['color'] }}">{{ $kpi['value'] }}</p>
                    <p class="text-xs lato-bold text-slate-600 dark:text-slate-300 leading-tight">{{ $kpi['label'] }}</p>
                    <p class="text-[10px] text-slate-400 leading-tight">{{ $kpi['sub'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        @if (empty($mapa['competencias']))
        {{-- Estado vazio --}}
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl">
            <div class="w-16 h-16 rounded-2xl bg-cyan-50 dark:bg-cyan-900/20 flex items-center justify-center mb-4">
                <x-lucide-layout-grid class="w-8 h-8 text-cyan-400" />
            </div>
            <p class="text-base lato-bold text-slate-700 dark:text-slate-200">Nenhum DPI ativo encontrado</p>
            <p class="text-sm text-slate-400 mt-1 max-w-xs">Aprove planos de desenvolvimento para visualizar o mapa de competências</p>
            <a href="{{ route('dpi') }}" class="mt-4 inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-cyan-500 hover:bg-cyan-600 text-white text-sm lato-bold transition shadow-sm">
                <x-lucide-external-link class="w-4 h-4" />
                Ir para DPI
            </a>
        </div>
        @else

        {{-- Layout: matriz + ranking lateral --}}
        <div class="flex gap-4 items-start">

            {{-- ── MATRIZ HEATMAP ───────────────────────────────────── --}}
            <div class="flex-1 min-w-0 space-y-4">

                {{-- Legenda --}}
                <div class="flex flex-wrap items-center gap-3 text-xs lato-bold">
                    <span class="text-slate-400 dark:text-slate-500">Legenda:</span>
                    <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-emerald-500 inline-block"></span> Atingido</span>
                    <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-yellow-400 inline-block"></span> Gap leve (−1)</span>
                    <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-orange-400 inline-block"></span> Gap médio (−2)</span>
                    <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-rose-500 inline-block"></span> Gap crítico (≥−3)</span>
                    <span class="flex items-center gap-1.5"><span class="w-4 h-4 rounded bg-slate-100 dark:bg-slate-700 border border-slate-200 dark:border-slate-600 inline-block"></span> Sem dados</span>
                </div>

                @foreach ($mapa['departamentos'] as $dept)
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                    {{-- Header do departamento --}}
                    <div class="flex items-center gap-3 px-4 py-3 bg-slate-50 dark:bg-slate-700/60 border-b border-slate-200 dark:border-slate-700">
                        <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-cyan-500 to-blue-500 flex items-center justify-center shrink-0">
                            <x-lucide-building-2 class="w-4 h-4 text-white" />
                        </div>
                        <div>
                            <p class="text-sm lato-bold text-slate-800 dark:text-slate-100">{{ $dept['nome'] }}</p>
                            <p class="text-xs text-slate-400">{{ count($dept['funcionarios']) }} funcionário(s) com DPI</p>
                        </div>
                    </div>

                    {{-- Tabela scrollável horizontalmente --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full text-xs">
                            <thead>
                                <tr class="border-b border-slate-100 dark:border-slate-700">
                                    <th class="sticky left-0 z-10 bg-white dark:bg-slate-800 px-4 py-2.5 text-left lato-bold text-slate-500 dark:text-slate-400 min-w-44 whitespace-nowrap">
                                        Funcionário
                                    </th>
                                    @foreach ($mapa['competencias'] as $comp)
                                    <th class="px-2 py-2.5 text-center lato-bold text-slate-500 dark:text-slate-400 min-w-28 max-w-36">
                                        <div class="truncate" title="{{ $comp }}">{{ Str::limit($comp, 18) }}</div>
                                    </th>
                                    @endforeach
                                    <th class="px-3 py-2.5 text-center lato-bold text-slate-500 dark:text-slate-400 min-w-20 whitespace-nowrap">
                                        Gap Total
                                    </th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                                @foreach ($dept['funcionarios'] as $func)
                                @php
                                    $funcGapTotal = array_sum(array_column($func['niveis'], 'gap'));
                                @endphp
                                <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-700/20 transition group">
                                    {{-- Nome --}}
                                    <td class="sticky left-0 z-10 bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-700/30 px-4 py-3 font-medium text-slate-700 dark:text-slate-200">
                                        <div class="flex flex-col">
                                            <span class="truncate max-w-[160px]" title="{{ $func['nome'] ?? '' }}">{{ Str::limit($func['nome'] ?? '', 22) }}</span>
                                            @if (!empty($func['position']))
                                            <span class="text-[10px] text-slate-400 truncate max-w-[160px]">{{ Str::limit($func['position'], 22) }}</span>
                                            @endif
                                        </div>
                                    </td>
                                    {{-- Células por competência --}}
                                    @foreach ($mapa['competencias'] as $comp)
                                    @php $cel = $func['niveis'][$comp] ?? null; @endphp
                                    <td class="px-2 py-3 text-center">
                                        @if ($cel)
                                        @php
                                            $gapN  = min(3, max(0, $cel['gap']));
                                            $cls   = $gapCores[$gapN];
                                            $lbl   = $gapLabel[$gapN];
                                            $title = "Atual: {$nivelLabels[$cel['atual']]} / Meta: {$nivelLabels[$cel['meta']]}";
                                        @endphp
                                        <span title="{{ $title }}"
                                              class="inline-flex items-center justify-center w-8 h-8 rounded-lg text-xs lato-bold {{ $cls }} cursor-default">
                                            {{ $cel['atual'] }}
                                        </span>
                                        @else
                                        <span class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                                        @endif
                                    </td>
                                    @endforeach
                                    {{-- Gap total --}}
                                    <td class="px-3 py-3 text-center">
                                        @if ($funcGapTotal > 0)
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs lato-bold bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300">
                                            -{{ $funcGapTotal }}
                                        </span>
                                        @else
                                        <span class="inline-flex items-center justify-center px-2 py-1 rounded-lg text-xs lato-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300">
                                            OK
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    {{-- Linha de totais --}}
                    @if(!empty($mapa['totalGapsPorCompetencia']))
                    <div class="px-4 py-3 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/30">
                        <div class="flex items-center gap-2 flex-wrap">
                            <span class="text-xs lato-bold text-slate-500 dark:text-slate-400 shrink-0">Gap por competência:</span>
                            @foreach($mapa['totalGapsPorCompetencia'] as $comp => $total)
                            <span class="inline-flex items-center gap-1 text-[10px] lato-bold px-2 py-0.5 rounded-full
                                         {{ $total >= 3 ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
                                          : ($total >= 1 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300'
                                          : 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300') }}">
                                {{ Str::limit($comp, 14) }}: {{ $total > 0 ? '-'.$total : 'OK' }}
                            </span>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
                @endforeach
            @endif

        {{-- ── COMO INTERPRETAR ────────────────────────────────────── --}}
        <details class="group bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
            <summary class="flex items-center justify-between gap-3 px-5 py-4 cursor-pointer select-none list-none">
                <div class="flex items-center gap-2.5">
                    <div class="w-7 h-7 rounded-lg bg-cyan-50 dark:bg-cyan-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-book-open class="w-3.5 h-3.5 text-cyan-600 dark:text-cyan-400" />
                    </div>
                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">Como interpretar o Mapa de Competências</span>
                </div>
                <x-lucide-chevron-down class="w-4 h-4 text-slate-400 transition-transform duration-200 group-open:rotate-180 shrink-0" />
            </summary>

            <div class="px-5 pb-5 pt-1 border-t border-slate-100 dark:border-slate-700 space-y-4">

                <p class="text-xs lato-regular text-slate-500 dark:text-slate-400 leading-relaxed">
                    O Mapa de Competências cruza os <span class="lato-bold text-slate-700 dark:text-slate-200">colaboradores</span> com as <span class="lato-bold text-slate-700 dark:text-slate-200">competências exigidas pelo DPI</span> de cada um, mostrando visualmente onde existem gaps de desenvolvimento.
                </p>

                <div>
                    <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Legenda de cores</p>
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-2">
                        @foreach([
                            ['bg'=>'bg-emerald-500', 'label'=>'OK',         'desc'=>'Competência atingida ou acima da meta'],
                            ['bg'=>'bg-yellow-400',  'label'=>'Gap leve',   'desc'=>'1 ponto abaixo da meta'],
                            ['bg'=>'bg-orange-400',  'label'=>'Gap médio',  'desc'=>'2 pontos abaixo da meta'],
                            ['bg'=>'bg-rose-500',    'label'=>'Gap crítico','desc'=>'3+ pontos abaixo da meta'],
                        ] as $leg)
                        <div class="flex items-start gap-2.5 p-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/50">
                            <span class="w-3 h-3 rounded-sm {{ $leg['bg'] }} shrink-0 mt-0.5"></span>
                            <div>
                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $leg['label'] }}</p>
                                <p class="text-[11px] lato-regular text-slate-400 leading-tight">{{ $leg['desc'] }}</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-3 gap-2">
                    @foreach([
                        ['icon'=>'target',      'clr'=>'text-cyan-500',   'title'=>'Score por colaborador', 'desc'=>'A coluna final mostra a soma total de gaps de cada pessoa. Quanto maior, maior a necessidade de desenvolvimento.'],
                        ['icon'=>'bar-chart-2', 'clr'=>'text-amber-500',  'title'=>'Gaps por competência',  'desc'=>'A linha de totais revela quais competências têm mais deficiências na equipe — ideal para priorizar treinamentos.'],
                        ['icon'=>'filter',      'clr'=>'text-indigo-500', 'title'=>'Use os filtros',         'desc'=>'Filtre por departamento e ano para comparar a evolução da equipe ou identificar padrões por área.'],
                    ] as $tip)
                    <div class="flex items-start gap-2.5 p-3 rounded-xl border border-slate-100 dark:border-slate-700">
                        <div class="w-7 h-7 rounded-lg bg-slate-50 dark:bg-slate-700 flex items-center justify-center shrink-0">
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

    </div>
    @endif
