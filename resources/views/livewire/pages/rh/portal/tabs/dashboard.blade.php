        {{-- ═══════════════════════════════════════════════════════════
             ABA: DASHBOARD
        ═══════════════════════════════════════════════════════════ --}}
        @if ($aba === 'dashboard')
        <div class="p-3 md:p-6 space-y-5 md:space-y-6">
            <div>
                <h1 class="text-xl lato-black text-slate-800 dark:text-white">Dashboard R&S</h1>
                <p class="text-sm text-slate-400 lato-regular mt-0.5">Indicadores e métricas do recrutamento</p>
            </div>

            {{-- KPI Cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
                @php
                    $kpis = [
                        ['label'=>'Candidatos',      'value'=>$st['total_candidatos'],   'icon'=>'users',          'color'=>'from-blue-500 to-indigo-600'],
                        ['label'=>'Vagas abertas',   'value'=>$st['vagas_abertas'],      'icon'=>'briefcase',      'color'=>'from-blue-500 to-indigo-600'],
                        ['label'=>'Proc. ativos',    'value'=>$st['processos_ativos'],   'icon'=>'git-branch',     'color'=>'from-teal-500 to-cyan-600'],
                        ['label'=>'Testes feitos',   'value'=>$st['testes_realizados'],  'icon'=>'clipboard-list', 'color'=>'from-orange-500 to-amber-600'],
                        ['label'=>'Taxa aprovação',  'value'=>$st['taxa_aprovacao'].'%','icon'=>'check-circle',   'color'=>'from-green-500 to-emerald-600'],
                        ['label'=>'Tempo médio (d)', 'value'=>$st['tempo_medio_dias'],   'icon'=>'clock',          'color'=>'from-rose-500 to-pink-600'],
                    ];
                @endphp
                @foreach ($kpis as $kpi)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $kpi['color'] }} flex items-center justify-center mb-3">
                        <x-dynamic-component :component="'lucide-'.$kpi['icon']" class="w-4 h-4 text-white" />
                    </div>
                    <p class="text-2xl lato-bold text-slate-800 dark:text-slate-100">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-slate-500 lato-regular mt-0.5">{{ $kpi['label'] }}</p>
                </div>
                @endforeach
            </div>

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
                            @php $maxV = max(array_column($cpv,'value')); $pctV = $maxV > 0 ? round($item['value']/$maxV*100) : 0; @endphp
                            <div>
                                <div class="flex items-center justify-between text-xs mb-1">
                                    <span class="lato-regular text-slate-600 dark:text-slate-300 truncate max-w-[180px]">{{ $item['label'] }}</span>
                                    <span class="lato-bold text-slate-700 dark:text-slate-200 shrink-0 ml-2">{{ $item['value'] }}</span>
                                </div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full" style="width:{{ $pctV }}%"></div>
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
                            @php $maxC = max(array_column($cpm,'value') ?: [1]); @endphp
                            @foreach ($cpm as $c)
                            @php $hC = $maxC > 0 ? round($c['value']/$maxC*100) : 0; @endphp
                            <div class="flex-1 flex flex-col items-center gap-1">
                                <span class="text-[10px] lato-bold text-slate-600 dark:text-slate-300">{{ $c['value'] }}</span>
                                <div class="w-full bg-gradient-to-t from-green-500 to-emerald-400 rounded-t-lg" style="height:{{ max($hC,4) }}%"></div>
                                <span class="text-[9px] text-slate-400 lato-regular">{{ $c['label'] }}</span>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Ranking recrutadores --}}
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
                                $medal  = $medals[$pos] ?? ($pos+1).'º';
                                $parts  = explode(' ', trim($r->name));
                                $init   = strtoupper(substr($parts[0],0,1).(isset($parts[1])?substr($parts[1],0,1):''));
                                $rColors= ['bg-indigo-500','bg-indigo-600','bg-teal-500','bg-rose-500','bg-amber-500'];
                                $rBg    = $rColors[abs(crc32($r->name))%count($rColors)];
                            @endphp
                            <div class="flex items-center gap-3">
                                <span class="text-lg">{{ $medal }}</span>
                                <span class="w-8 h-8 rounded-full {{ $rBg }} text-white text-xs lato-bold flex items-center justify-center shrink-0">{{ $init }}</span>
                                <p class="flex-1 text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $r->name }}</p>
                                <span class="text-sm lato-bold text-green-600 dark:text-green-400">{{ $r->total }}</span>
                            </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                {{-- Vagas recentes --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                        <x-lucide-clock class="w-4 h-4 text-slate-400" /> Vagas recentes
                    </h3>
                    <div class="space-y-2">
                        @forelse ($this->vagasRecentes as $vr)
                        <div class="flex items-center gap-3 p-2 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                                <x-lucide-briefcase class="w-4 h-4 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $vr->titulo }}</p>
                                <p class="text-[10px] text-slate-400 lato-regular">{{ $vr->candidaturas_count }} candidatos · {{ $vr->created_at->diffForHumans() }}</p>
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $vagaStatusCls[$vr->status] ?? 'bg-slate-100 text-slate-600' }}">
                                {{ $vagaStatusLbl[$vr->status] ?? $vr->status }}
                            </span>
                        </div>
                        @empty
                        <p class="text-sm text-slate-400 lato-regular py-4 text-center">Nenhuma vaga cadastrada.</p>
                        @endforelse
                    </div>
                    <button wire:click="setAba('vagas')" type="button"
                            class="cursor-pointer mt-3 w-full text-center text-xs text-blue-500 hover:underline lato-bold">Ver todas →</button>
                </div>
            </div>
        </div>
        @endif

