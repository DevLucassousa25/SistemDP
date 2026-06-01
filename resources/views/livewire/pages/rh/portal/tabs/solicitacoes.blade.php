    {{-- ══════════════════════════════════════════════════════════════════
         ABA: SOLICITAÇÕES DE RH (Self-service)
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($aba === 'solicitacoes')
    @php
        $solTipoLabels = \App\Models\RhSolicitacao::$tipoLabels;
        $solStatusCls  = [
            'pendente'    => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
            'em_andamento'=> 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
            'concluida'   => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
            'cancelada'   => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
        ];
        $solStatusDot = [
            'pendente'    => 'bg-amber-400',
            'em_andamento'=> 'bg-blue-400',
            'concluida'   => 'bg-emerald-400',
            'cancelada'   => 'bg-slate-400',
        ];
        $solKpis = $this->solKpis;
    @endphp
    <div class="flex-1 overflow-y-auto p-6 space-y-5">

        {{-- Cabeçalho --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-lg lato-black text-slate-800 dark:text-white">Solicitações de RH</h2>
                <p class="text-sm text-slate-400 lato-regular mt-0.5">Self-service — acompanhe e atenda as solicitações dos funcionários</p>
            </div>
            <a href="{{ route('solicitacoes') }}" target="_blank"
               class="inline-flex items-center gap-2 px-4 py-2 rounded-xl text-sm lato-bold text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition">
                <x-lucide-external-link class="w-3.5 h-3.5" />
                Portal Funcionário
            </a>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 sm:grid-cols-5 gap-3">
            @foreach ([
                ['label'=>'Total',        'value'=>$solKpis['total'],        'color'=>'text-slate-700 dark:text-slate-200',   'bg'=>'bg-slate-100 dark:bg-slate-700',   'icon'=>'inbox'],
                ['label'=>'Pendentes',    'value'=>$solKpis['pendentes'],    'color'=>'text-amber-700 dark:text-amber-300',   'bg'=>'bg-amber-50 dark:bg-amber-900/20',   'icon'=>'clock'],
                ['label'=>'Em Andamento', 'value'=>$solKpis['em_andamento'], 'color'=>'text-blue-700 dark:text-blue-300',     'bg'=>'bg-blue-50 dark:bg-blue-900/20',     'icon'=>'loader'],
                ['label'=>'Concluídas',   'value'=>$solKpis['concluidas'],   'color'=>'text-emerald-700 dark:text-emerald-300','bg'=>'bg-emerald-50 dark:bg-emerald-900/20','icon'=>'check-circle'],
                ['label'=>'Vencidas',     'value'=>$solKpis['vencidas'],     'color'=>'text-rose-700 dark:text-rose-300',     'bg'=>'bg-rose-50 dark:bg-rose-900/20',     'icon'=>'alert-circle'],
            ] as $kpi)
            <div class="rounded-xl {{ $kpi['bg'] }} p-4 flex items-center gap-3">
                <x-dynamic-component :component="'lucide-'.$kpi['icon']" class="w-5 h-5 {{ $kpi['color'] }} shrink-0" />
                <div>
                    <p class="text-xl lato-black {{ $kpi['color'] }}">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-slate-400 lato-regular leading-tight">{{ $kpi['label'] }}</p>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Filtros (room-table style) --}}
        <div class="flex flex-col gap-3 p-4 bg-white dark:bg-slate-800 rounded-xl border border-gray-100 dark:border-slate-700">
            {{-- Linha 1: busca + datas + aplicar --}}
            <div class="flex flex-wrap gap-2 items-center">
                <div class="relative flex-1 min-w-40">
                    <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400" />
                    <input wire:model="solBusca" type="text" placeholder="Buscar funcionário…"
                           class="w-full pl-8 pr-3 py-2 text-sm border border-slate-200 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-1 focus:ring-amber-400" />
                </div>
                <input wire:model="solInicio" type="date"
                       class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-amber-400" />
                <span class="text-slate-400 text-sm">→</span>
                <input wire:model="solFim" type="date"
                       class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-amber-400" />
                <button wire:click="solAplicar" type="button"
                        class="cursor-pointer px-4 py-2 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                    Aplicar
                </button>
            </div>
            {{-- Linha 2: selects nativos (evita clipping do overflow-y-auto pai) --}}
            <div class="grid grid-cols-2 gap-2">
                {{-- Tipo --}}
                <select wire:model="solTipo"
                        class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-amber-400 lato-regular cursor-pointer">
                    <option value="">Todos os tipos</option>
                    @foreach ($solTipoLabels as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
                {{-- Status --}}
                <select wire:model="solStatus"
                        class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-700 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-amber-400 lato-regular cursor-pointer">
                    <option value="">Todos os status</option>
                    @foreach (\App\Models\RhSolicitacao::$statusLabels as $k => $v)
                        <option value="{{ $k }}">{{ $v }}</option>
                    @endforeach
                </select>
            </div>
            {{-- Chips de filtros ativos --}}
            @if ($this->temFiltrosSol())
            <div class="flex flex-wrap items-center gap-2">
                <span class="text-xs text-slate-400 lato-bold">Filtros:</span>
                @if ($solBusca)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 text-xs lato-bold border border-amber-200 dark:border-amber-700/40">
                    "{{ $solBusca }}"
                    <button wire:click="$set('solBusca', '')" class="cursor-pointer hover:text-amber-900 dark:hover:text-amber-100 transition"><x-lucide-x class="w-3 h-3" /></button>
                </span>
                @endif
                @if ($solTipo)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 text-xs lato-bold border border-amber-200 dark:border-amber-700/40">
                    {{ $solTipoLabels[$solTipo] ?? $solTipo }}
                    <button wire:click="$set('solTipo', '')" class="cursor-pointer hover:text-amber-900 dark:hover:text-amber-100 transition"><x-lucide-x class="w-3 h-3" /></button>
                </span>
                @endif
                @if ($solStatus)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 text-xs lato-bold border border-amber-200 dark:border-amber-700/40">
                    {{ \App\Models\RhSolicitacao::$statusLabels[$solStatus] ?? $solStatus }}
                    <button wire:click="$set('solStatus', '')" class="cursor-pointer hover:text-amber-900 dark:hover:text-amber-100 transition"><x-lucide-x class="w-3 h-3" /></button>
                </span>
                @endif
                @if ($solInicio || $solFim)
                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300 text-xs lato-bold border border-amber-200 dark:border-amber-700/40">
                    {{ $solInicio ? \Carbon\Carbon::parse($solInicio)->format('d/m/Y') : '…' }} → {{ $solFim ? \Carbon\Carbon::parse($solFim)->format('d/m/Y') : '…' }}
                    <button wire:click="$set('solInicio', ''); $set('solFim', '')" class="cursor-pointer hover:text-amber-900 dark:hover:text-amber-100 transition"><x-lucide-x class="w-3 h-3" /></button>
                </span>
                @endif
                <button wire:click="solLimpar" class="cursor-pointer text-xs text-slate-400 hover:text-rose-500 transition lato-bold ml-1">Limpar tudo</button>
            </div>
            @endif
        </div>

        {{-- Lista de solicitações --}}
        @php $sls = $this->solicitacoes; @endphp
        @if ($sls->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-center">
            <div class="w-14 h-14 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mb-4">
                <x-lucide-inbox class="w-7 h-7 text-amber-400" />
            </div>
            <p class="text-sm lato-bold text-slate-600 dark:text-slate-300">Nenhuma solicitação encontrada</p>
            <p class="text-xs text-slate-400 mt-1">As solicitações dos funcionários aparecerão aqui</p>
        </div>
        @else
        <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-100 dark:border-slate-700 overflow-hidden">
            {{-- Cabeçalho da tabela --}}
            <div class="grid grid-cols-12 gap-4 px-4 py-3 bg-slate-50 dark:bg-slate-700/50 border-b border-slate-100 dark:border-slate-700">
                <div class="col-span-3 text-xs lato-bold text-slate-400 uppercase tracking-wider">Funcionário</div>
                <div class="col-span-3 text-xs lato-bold text-slate-400 uppercase tracking-wider">Tipo</div>
                <div class="col-span-2 text-xs lato-bold text-slate-400 uppercase tracking-wider">Status</div>
                <div class="col-span-2 text-xs lato-bold text-slate-400 uppercase tracking-wider">Prazo</div>
                <div class="col-span-1 text-xs lato-bold text-slate-400 uppercase tracking-wider">Resp.</div>
                <div class="col-span-1"></div>
            </div>
            {{-- Linhas --}}
            @foreach ($sls as $sol)
            @php
                $vencida = $sol->vencida;
                $rowBg = $vencida ? 'bg-rose-50/40 dark:bg-rose-900/10' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50';
            @endphp
            <div class="grid grid-cols-12 gap-4 px-4 py-3.5 border-b border-slate-100 dark:border-slate-700/50 last:border-0 transition {{ $rowBg }} items-center">
                {{-- Funcionário --}}
                <div class="col-span-3 flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-xs lato-bold shrink-0">
                        {{ strtoupper(substr($sol->user?->name ?? '?', 0, 1)) }}
                    </div>
                    <div class="min-w-0">
                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $sol->user?->name ?? '—' }}</p>
                        <p class="text-xs text-slate-400 truncate">{{ $sol->user?->department?->name ?? '' }}</p>
                    </div>
                </div>
                {{-- Tipo --}}
                <div class="col-span-3 min-w-0">
                    <p class="text-sm text-slate-700 dark:text-slate-200 truncate">{{ $solTipoLabels[$sol->tipo] ?? $sol->tipo }}</p>
                    <p class="text-xs text-slate-400 truncate">{{ $sol->created_at->format('d/m/Y H:i') }}</p>
                </div>
                {{-- Status --}}
                <div class="col-span-2">
                    <span class="inline-flex items-center gap-1.5 text-xs lato-bold px-2.5 py-1 rounded-full {{ $solStatusCls[$sol->status] ?? '' }}">
                        <span class="w-1.5 h-1.5 rounded-full {{ $solStatusDot[$sol->status] ?? 'bg-slate-400' }}"></span>
                        {{ \App\Models\RhSolicitacao::$statusLabels[$sol->status] ?? $sol->status }}
                    </span>
                </div>
                {{-- Prazo --}}
                <div class="col-span-2">
                    @if ($sol->prazo)
                    <p class="text-sm {{ $vencida ? 'text-rose-600 dark:text-rose-400 lato-bold' : 'text-slate-600 dark:text-slate-300' }}">
                        {{ $sol->prazo->format('d/m/Y') }}
                        @if ($vencida) <span class="text-xs">(vencido)</span> @endif
                    </p>
                    @else
                    <p class="text-sm text-slate-300 dark:text-slate-500">—</p>
                    @endif
                </div>
                {{-- Responsável RH --}}
                <div class="col-span-1">
                    @if ($sol->rhUser)
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center text-white text-xs lato-bold"
                         title="{{ $sol->rhUser->name }}">
                        {{ strtoupper(substr($sol->rhUser->name, 0, 1)) }}
                    </div>
                    @else
                    <span class="text-slate-300 dark:text-slate-600 text-xs">—</span>
                    @endif
                </div>
                {{-- Ação --}}
                <div class="col-span-1 flex justify-end">
                    <button wire:click="openSolModal({{ $sol->id }})" type="button"
                            class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition">
                        <x-lucide-pencil class="w-4 h-4" />
                    </button>
                </div>
            </div>
            @endforeach
        </div>
        @endif

    </div>
    @endif

  