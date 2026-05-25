@php
    use Illuminate\Support\Facades\Auth;
    $stats = $this->stats;
@endphp

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 lg:p-8">
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Header --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl lato-bold text-slate-800 dark:text-slate-100">Recrutamento & Seleção</h1>
                <p class="text-sm text-slate-500 lato-regular mt-0.5">Dashboard de indicadores e métricas</p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('rh.curriculos') }}"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                    <x-lucide-file-text class="w-4 h-4" /> Currículos
                </a>
                <a href="{{ route('rh.vagas') }}"
                   class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-briefcase class="w-4 h-4" /> Vagas
                </a>
            </div>
        </div>

        {{-- Cards de indicadores --}}
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            @php
                $cards = [
                    ['label' => 'Candidatos',      'value' => $stats['total_candidatos'],  'icon' => 'users',         'color' => 'from-blue-500 to-indigo-600'],
                    ['label' => 'Vagas abertas',   'value' => $stats['vagas_abertas'],     'icon' => 'briefcase',     'color' => 'from-violet-500 to-purple-600'],
                    ['label' => 'Processos ativos','value' => $stats['processos_ativos'],  'icon' => 'git-branch',    'color' => 'from-teal-500 to-cyan-600'],
                    ['label' => 'Testes feitos',   'value' => $stats['testes_realizados'], 'icon' => 'clipboard-list','color' => 'from-orange-500 to-amber-600'],
                    ['label' => 'Taxa aprovação',  'value' => $stats['taxa_aprovacao'].'%','icon' => 'check-circle',  'color' => 'from-green-500 to-emerald-600'],
                    ['label' => 'Tempo médio (d)', 'value' => $stats['tempo_medio_dias'],  'icon' => 'clock',         'color' => 'from-rose-500 to-pink-600'],
                ];
            @endphp
            @foreach ($cards as $card)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $card['color'] }} flex items-center justify-center">
                            <x-dynamic-component :component="'lucide-'.$card['icon']" class="w-4 h-4 text-white" />
                        </div>
                    </div>
                    <p class="text-2xl lato-bold text-slate-800 dark:text-slate-100">{{ $card['value'] }}</p>
                    <p class="text-xs text-slate-500 lato-regular mt-0.5">{{ $card['label'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- Gráficos e tabelas --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Candidatos por vaga --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <x-lucide-bar-chart-2 class="w-4 h-4 text-blue-500" /> Candidatos por vaga
                </h3>
                @php $cpv = $this->candidatosPorVaga; @endphp
                @if (empty($cpv))
                    <p class="text-sm text-slate-400 lato-regular py-6 text-center">Nenhum dado disponível.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($cpv as $item)
                            @php $max = max(array_column($cpv, 'value')); $pct = $max > 0 ? round($item['value'] / $max * 100) : 0; @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="lato-regular text-slate-600 dark:text-slate-300 truncate max-w-[200px]">{{ $item['label'] }}</span>
                                    <span class="lato-bold text-slate-700 dark:text-slate-200 shrink-0 ml-2">{{ $item['value'] }}</span>
                                </div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full transition-all" style="width: {{ $pct }}%"></div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Contratações por mês --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <x-lucide-trending-up class="w-4 h-4 text-green-500" /> Contratações por mês
                </h3>
                @php $cpm = $this->contratacoesPorMes; @endphp
                @if (empty($cpm))
                    <p class="text-sm text-slate-400 lato-regular py-6 text-center">Nenhuma contratação registrada.</p>
                @else
                    <div class="flex items-end gap-2 h-36">
                        @php $maxC = max(array_column($cpm, 'value') ?: [1]); @endphp
                        @foreach ($cpm as $c)
                            @php $h = $maxC > 0 ? round($c['value'] / $maxC * 100) : 0; @endphp
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <span class="text-[10px] lato-bold text-slate-600 dark:text-slate-300">{{ $c['value'] }}</span>
                                <div class="w-full bg-gradient-to-t from-green-500 to-emerald-400 rounded-t-lg" style="height: {{ max($h, 4) }}%"></div>
                                <span class="text-[9px] text-slate-400 lato-regular">{{ $c['label'] }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Ranking de recrutadores --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <x-lucide-trophy class="w-4 h-4 text-amber-500" /> Ranking de Recrutadores
                </h3>
                @php $ranking = $this->rankingRecrutadores; @endphp
                @if (empty($ranking))
                    <p class="text-sm text-slate-400 lato-regular py-6 text-center">Nenhuma contratação ainda.</p>
                @else
                    <div class="space-y-3">
                        @foreach ($ranking as $pos => $r)
                            @php
                                $medals = ['🥇','🥈','🥉'];
                                $medal  = $medals[$pos] ?? ($pos + 1) . 'º';
                                $parts  = explode(' ', trim($r->name));
                                $init   = strtoupper(substr($parts[0],0,1) . (isset($parts[1]) ? substr($parts[1],0,1) : ''));
                                $colors = ['bg-indigo-500','bg-violet-500','bg-teal-500','bg-rose-500','bg-amber-500'];
                                $bg     = $colors[abs(crc32($r->name)) % count($colors)];
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="text-lg">{{ $medal }}</span>
                                <span class="w-8 h-8 rounded-full {{ $bg }} text-white text-xs lato-bold flex items-center justify-center shrink-0">{{ $init }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $r->name }}</p>
                                </div>
                                <span class="text-sm lato-bold text-green-600 dark:text-green-400">{{ $r->total }}</span>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            {{-- Candidatos e vagas recentes --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <x-lucide-clock class="w-4 h-4 text-slate-400" /> Vagas recentes
                </h3>
                <div class="space-y-2">
                    @forelse ($this->vagasRecentes as $vaga)
                        @php
                            $sc = ['publicada' => 'bg-green-100 text-green-700', 'rascunho' => 'bg-slate-100 text-slate-600', 'pausada' => 'bg-amber-100 text-amber-700', 'encerrada' => 'bg-red-100 text-red-600'];
                            $sl = ['publicada' => 'Publicada', 'rascunho' => 'Rascunho', 'pausada' => 'Pausada', 'encerrada' => 'Encerrada'];
                        @endphp
                        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shrink-0">
                                <x-lucide-briefcase class="w-4 h-4 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $vaga->titulo }}</p>
                                <p class="text-[10px] text-slate-400 lato-regular">{{ $vaga->candidaturas_count }} candidatos · {{ $vaga->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $sc[$vaga->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $sl[$vaga->status] ?? $vaga->status }}
                            </span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400 lato-regular py-4 text-center">Nenhuma vaga cadastrada.</p>
                    @endforelse
                </div>
                <a href="{{ route('rh.vagas') }}" class="block mt-3 text-center text-xs text-blue-500 hover:underline lato-bold">Ver todas →</a>
            </div>

        </div>

    </div>
</div>
