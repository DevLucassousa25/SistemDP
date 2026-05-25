@php
    use Illuminate\Support\Facades\Storage;
    if (!function_exists('portalVagaBg')) {
        function portalVagaBg(string $n): string {
            $pal = ['bg-indigo-500','bg-violet-500','bg-teal-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
            return $pal[abs(crc32($n)) % count($pal)];
        }
    }
    $escLabels = ['fundamental'=>'Fundamental','medio'=>'Médio','tecnico'=>'Técnico','graduacao'=>'Graduação','pos_graduacao'=>'Pós-Graduação','mestrado'=>'Mestrado','doutorado'=>'Doutorado'];
    $statusCurrMap = [
        'ativo'          => ['bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',  'Ativo'],
        'favorito'       => ['bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',  'Favorito'],
        'banco_talentos' => ['bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',      'Banco Talentos'],
        'inativo'        => ['bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400',     'Inativo'],
    ];
    $vagaStatusCls = ['rascunho'=>'bg-slate-100 text-slate-600','publicada'=>'bg-green-100 text-green-700','pausada'=>'bg-amber-100 text-amber-700','encerrada'=>'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400'];
    $vagaStatusLbl = ['rascunho'=>'Rascunho','publicada'=>'Publicada','pausada'=>'Pausada','encerrada'=>'Encerrada'];
    $vagaModalLbl  = ['presencial'=>'Presencial','remoto'=>'Remoto','hibrido'=>'Híbrido'];
    $etapaCores    = ['bg-slate-400','bg-blue-400','bg-indigo-400','bg-violet-400','bg-purple-400','bg-green-400','bg-red-400','bg-amber-400','bg-teal-400','bg-orange-400'];
@endphp

{{-- ═══════════════════════════════════════════════════════════════════════
     PORTAL RH  –  Layout: sidebar esquerda + área de conteúdo
═══════════════════════════════════════════════════════════════════════ --}}
<div class="flex min-h-full bg-slate-50 dark:bg-slate-900" x-data="{ nav: false }">

    {{-- ── OVERLAY MOBILE ──────────────────────────────────────────── --}}
    <div x-show="nav" @click="nav=false" style="cursor:pointer"
         class="fixed inset-0 bg-black/40 z-30 lg:hidden"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display:none"></div>

    {{-- ── SIDEBAR DE NAVEGAÇÃO DO PORTAL ──────────────────────────── --}}
    <aside :class="nav ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
           class="fixed lg:relative inset-y-0 left-0 z-40 w-60 shrink-0
                  bg-white dark:bg-slate-800 border-r border-slate-200 dark:border-slate-700
                  flex flex-col transition-transform duration-200 ease-in-out">

        {{-- Logo --}}
        <div class="px-5 py-5 border-b border-slate-200 dark:border-slate-700">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                    <x-lucide-users class="w-4 h-4 text-white" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-black text-slate-800 dark:text-white leading-tight">Portal RH</p>
                    <p class="text-[11px] text-slate-400 lato-regular">Recrutamento & Seleção</p>
                </div>
                {{-- Fechar no mobile --}}
                <button @click="nav=false" type="button"
                        class="cursor-pointer lg:hidden p-1 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Navegação --}}
        <nav class="flex-1 p-3 space-y-0.5 overflow-y-auto">
            @php
                $navItems = [
                    ['id'=>'dashboard',      'label'=>'Dashboard',      'icon'=>'layout-dashboard', 'dot'=>'bg-blue-500'],
                    ['id'=>'curriculos',     'label'=>'Currículos',     'icon'=>'file-text',        'dot'=>'bg-indigo-500'],
                    ['id'=>'vagas',          'label'=>'Vagas',          'icon'=>'briefcase',        'dot'=>'bg-violet-500'],
                    ['id'=>'pipeline',       'label'=>'Pipeline',       'icon'=>'git-branch',       'dot'=>'bg-teal-500'],
                    ['id'=>'testes',         'label'=>'Testes Online',  'icon'=>'clipboard-list',   'dot'=>'bg-orange-500'],
                    ['id'=>'desligamentos',       'label'=>'Desligamentos',  'icon'=>'user-minus',       'dot'=>'bg-rose-500'],
                    ['id'=>'onboarding',          'label'=>'Onboarding',     'icon'=>'user-check',       'dot'=>'bg-emerald-500'],
                    ['id'=>'relatorio-turnover',  'label'=>'Turnover',         'icon'=>'bar-chart-2',      'dot'=>'bg-pink-500'],
                    ['id'=>'clima-unificado',     'label'=>'Clima Unificado',  'icon'=>'sun-medium',       'dot'=>'bg-violet-500'],
                    ['id'=>'solicitacoes',         'label'=>'Solicitações RH',  'icon'=>'inbox',            'dot'=>'bg-amber-500'],
                    ['id'=>'mapa-competencias',    'label'=>'Mapa Competências', 'icon'=>'layout-grid',      'dot'=>'bg-cyan-500'],
                    ['id'=>'people-analytics',     'label'=>'People Analytics',  'icon'=>'users-round',      'dot'=>'bg-purple-500'],
                ];
            @endphp
            @foreach ($navItems as $item)
            <button wire:click="setAba('{{ $item['id'] }}')" @click="nav=false" type="button"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition lato-regular cursor-pointer
                           {{ $aba === $item['id']
                              ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 lato-bold'
                              : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                <x-dynamic-component :component="'lucide-'.$item['icon']"
                    class="w-4 h-4 shrink-0 {{ $aba === $item['id'] ? 'text-blue-500' : 'text-slate-400' }}" />
                {{ $item['label'] }}
                @if ($aba === $item['id'])
                <div class="ml-auto w-1.5 h-1.5 rounded-full {{ $item['dot'] }}"></div>
                @endif
            </button>
            @endforeach

            {{-- Humor das Equipes --}}
            <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-700">
                <a href="{{ route('rh.humor-equipes') }}" wire:navigate
                   class="w-full flex items-center gap-3 px-3 py-2.5 rounded-xl text-sm transition lato-regular cursor-pointer
                          {{ request()->routeIs('rh.humor-equipes')
                             ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 lato-bold'
                             : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700/60' }}">
                    <x-lucide-heart-pulse class="w-4 h-4 shrink-0 {{ request()->routeIs('rh.humor-equipes') ? 'text-rose-500' : 'text-slate-400' }}" />
                    Humor das Equipes
                    @if(request()->routeIs('rh.humor-equipes'))
                        <div class="ml-auto w-1.5 h-1.5 rounded-full bg-rose-500"></div>
                    @endif
                </a>
            </div>

            <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-700">
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-wider px-3 mb-2">Links rápidos</p>
                <a href="{{ route('dashboard') }}"
                   class="cursor-pointer flex items-center gap-3 px-3 py-2 rounded-xl text-xs text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition lato-regular">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" /> Voltar
                </a>
            </div>
        </nav>

        {{-- Stats rápidos --}}
        @php $st = $this->stats; @endphp
        <div class="p-3 border-t border-slate-200 dark:border-slate-700 space-y-1">
            <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900">
                <span class="text-xs text-slate-500 lato-regular">Candidatos</span>
                <span class="text-xs lato-black text-slate-800 dark:text-white">{{ $st['total_candidatos'] }}</span>
            </div>
            <div class="flex items-center justify-between px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900">
                <span class="text-xs text-slate-500 lato-regular">Vagas abertas</span>
                <span class="text-xs lato-black text-green-600">{{ $st['vagas_abertas'] }}</span>
            </div>
        </div>
    </aside>

    {{-- ── ÁREA DE CONTEÚDO ─────────────────────────────────────────── --}}
    <div class="flex-1 flex flex-col min-w-0 min-h-0">

        {{-- Barra mobile (hambúrguer + aba atual) --}}
        @php
            $abaLabels = ['dashboard'=>'Dashboard','curriculos'=>'Currículos','vagas'=>'Vagas','pipeline'=>'Pipeline','testes'=>'Testes Online','desligamentos'=>'Desligamentos','onboarding'=>'Onboarding','relatorio-turnover'=>'Turnover','clima-unificado'=>'Clima Unificado','solicitacoes'=>'Solicitações RH','mapa-competencias'=>'Mapa de Competências','people-analytics'=>'People Analytics'];
            $abaIcons  = ['dashboard'=>'layout-dashboard','curriculos'=>'file-text','vagas'=>'briefcase','pipeline'=>'git-branch','testes'=>'clipboard-list','desligamentos'=>'user-minus','onboarding'=>'user-check','relatorio-turnover'=>'bar-chart-2','clima-unificado'=>'sun-medium','solicitacoes'=>'inbox','mapa-competencias'=>'layout-grid','people-analytics'=>'users-round'];
        @endphp
        <header class="lg:hidden sticky top-0 z-20 flex items-center gap-3 px-4 py-3
                        bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 shrink-0">
            <button @click="nav=true" type="button"
                    class="cursor-pointer p-2 rounded-xl text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-menu class="w-5 h-5" />
            </button>
            <div class="flex items-center gap-2 flex-1 min-w-0">
                <x-dynamic-component :component="'lucide-'.$abaIcons[$aba]" class="w-4 h-4 text-blue-500 shrink-0" />
                <span class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $abaLabels[$aba] }}</span>
            </div>
            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                <x-lucide-users class="w-4 h-4 text-white" />
            </div>
        </header>

        <div class="flex-1">

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
                        ['label'=>'Vagas abertas',   'value'=>$st['vagas_abertas'],      'icon'=>'briefcase',      'color'=>'from-violet-500 to-purple-600'],
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
                                $rColors= ['bg-indigo-500','bg-violet-500','bg-teal-500','bg-rose-500','bg-amber-500'];
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
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shrink-0">
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

        {{-- ═══════════════════════════════════════════════════════════
             ABA: CURRÍCULOS
        ═══════════════════════════════════════════════════════════ --}}
        @if ($aba === 'curriculos')
        <div class="p-3 md:p-6 space-y-4 md:space-y-5">
            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white">Currículos</h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">Banco de candidatos e talentos</p>
                </div>
                <button wire:click="$set('uploadModal', true)" type="button"
                        class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm shrink-0">
                    <x-lucide-upload class="w-4 h-4" /> Novo Currículo
                </button>
            </div>

            {{-- Filtros --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3">

                {{-- Busca por nome/e-mail --}}
                <div class="relative w-full">
                    <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    <input type="text" wire:model.live.debounce.300ms="search"
                           placeholder="Nome, e-mail, área de interesse..."
                           class="w-full pl-10 pr-9 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                  bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                  focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                                  placeholder-slate-400 transition" />
                    @if ($search)
                    <button wire:click="$set('search','')" class="cursor-pointer absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                    @endif
                </div>

                {{-- Busca OCR (no conteúdo do PDF) --}}
                <div class="relative w-full">
                    <x-lucide-file-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 pointer-events-none
                        {{ $ocrSearch ? 'text-violet-500' : 'text-slate-400' }}" />
                    <input type="text" wire:model.live.debounce.400ms="ocrSearch"
                           placeholder="Buscar no currículo: Laravel, Python, inglês..."
                           class="w-full pl-10 pr-9 py-2.5 text-sm lato-regular border rounded-xl transition
                                  bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                  placeholder-slate-400 focus:outline-none
                                  {{ $ocrSearch
                                      ? 'border-violet-400 ring-2 ring-violet-400/30 focus:ring-violet-400/40'
                                      : 'border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-violet-400/40 focus:border-violet-400' }}" />
                    @if ($ocrSearch)
                    <button wire:click="$set('ocrSearch','')" class="cursor-pointer absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-violet-600 transition">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                    @endif
                </div>

                {{-- Dropdowns --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-2">

                    {{-- Status --}}
                    @php
                        $statusOpts = ['ativo'=>['Ativo','bg-green-400'],'favorito'=>['Favorito','bg-amber-400'],'banco_talentos'=>['Banco Talentos','bg-blue-400'],'inativo'=>['Inativo','bg-slate-400']];
                        $statusDot  = $filterStatus ? ($statusOpts[$filterStatus][1] ?? null) : null;
                        $statusLbl  = $filterStatus ? ($statusOpts[$filterStatus][0] ?? 'Status') : 'Status';
                    @endphp
                    <div wire:key="dropdown-1" x-data="{ isOpen: false }" class="relative">
                        <button @click="isOpen = !isOpen" type="button"
                                class="cursor-pointer w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                {{ $filterStatus ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate min-w-0">
                                @if ($statusDot)
                                    <span class="w-2 h-2 rounded-full {{ $statusDot }} shrink-0"></span>
                                @else
                                    <x-lucide-circle-dot class="w-3.5 h-3.5 shrink-0" />
                                @endif
                                <span class="truncate text-xs lato-bold">{{ $statusLbl }}</span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform ml-1" x-bind:class="isOpen ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="isOpen" @click.outside="isOpen = false" x-transition
                             class="absolute mt-1 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1">
                            <button @click="isOpen=false; $wire.set('filterStatus','')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <x-lucide-circle-dot class="w-3.5 h-3.5" /> Todos os status
                            </button>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                            @foreach ($statusOpts as $val => [$lbl, $dot])
                            <button @click="isOpen=false; $wire.set('filterStatus','{{ $val }}')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs flex items-center gap-2 transition
                                    {{ $filterStatus === $val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span> {{ $lbl }}
                                @if ($filterStatus === $val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Escolaridade --}}
                    @php
                        $escOpts = ['fundamental'=>'Fundamental','medio'=>'Médio','tecnico'=>'Técnico','graduacao'=>'Graduação','pos_graduacao'=>'Pós-Grad.','mestrado'=>'Mestrado','doutorado'=>'Doutorado'];
                        $escLbl  = $filterEscolaridade ? ($escOpts[$filterEscolaridade] ?? 'Escolaridade') : 'Escolaridade';
                    @endphp
                    <div wire:key="dropdown-2" x-data="{ isOpen: false }" class="relative">
                        <button @click="isOpen = !isOpen" type="button"
                                class="cursor-pointer w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                {{ $filterEscolaridade ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate min-w-0">
                                <x-lucide-graduation-cap class="w-3.5 h-3.5 shrink-0" />
                                <span class="truncate text-xs lato-bold">{{ $escLbl }}</span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform ml-1" x-bind:class="isOpen ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="isOpen" @click.outside="isOpen = false" x-transition
                             class="absolute mt-1 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1">
                            <button @click="isOpen=false; $wire.set('filterEscolaridade','')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <x-lucide-graduation-cap class="w-3.5 h-3.5" /> Qualquer nível
                            </button>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                            @foreach ($escOpts as $val => $lbl)
                            <button @click="isOpen=false; $wire.set('filterEscolaridade','{{ $val }}')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs flex items-center justify-between transition
                                    {{ $filterEscolaridade === $val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                {{ $lbl }}
                                @if ($filterEscolaridade === $val) <x-lucide-check class="w-3.5 h-3.5 text-indigo-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Estado --}}
                    <div class="relative">
                        <x-lucide-map-pin class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                        <input type="text" wire:model.live.debounce.400ms="filterEstado" placeholder="Estado (UF)"
                               class="w-full pl-9 pr-8 py-2.5 text-xs lato-bold border rounded-xl transition
                                      bg-white dark:bg-slate-900 placeholder-slate-400
                                      {{ $filterEstado ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 focus:ring-indigo-400/40' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 focus:ring-indigo-400/40' }}
                                      focus:outline-none focus:ring-2 focus:border-indigo-400" />
                        @if ($filterEstado)
                        <button wire:click="$set('filterEstado','')" class="cursor-pointer absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                        @endif
                    </div>

                    {{-- Ordenar --}}
                    @php $sortLbl = match($sortBy ?? 'recente') { 'nome' => 'Nome A–Z', 'pretensao' => 'Pretensão', default => 'Mais recentes' }; @endphp
                    <div wire:key="dropdown-3" x-data="{ isOpen: false }" class="relative">
                        <button @click="isOpen = !isOpen" type="button"
                                class="cursor-pointer w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                {{ ($sortBy ?? 'recente') !== 'recente' ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate min-w-0">
                                <x-lucide-arrow-up-down class="w-3.5 h-3.5 shrink-0" />
                                <span class="truncate text-xs lato-bold">{{ $sortLbl }}</span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform ml-1" x-bind:class="isOpen ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="isOpen" @click.outside="isOpen = false" x-transition
                             class="absolute mt-1 w-44 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 right-0">
                            @foreach (['recente'=>['Mais recentes','clock'],'nome'=>['Nome A–Z','a-arrow-down'],'pretensao'=>['Pretensão','banknote']] as $val=>[$lbl,$ico])
                            <button @click="isOpen=false; $wire.set('sortBy','{{ $val }}')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs flex items-center gap-2 transition
                                    {{ ($sortBy ?? 'recente') === $val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                <x-dynamic-component :component="'lucide-'.$ico" class="w-3.5 h-3.5" /> {{ $lbl }}
                                @if (($sortBy ?? 'recente') === $val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Limpar --}}
                    @if ($search || $filterStatus || $filterEscolaridade || $filterEstado)
                    <button wire:click="$set('search',''); $set('filterStatus',''); $set('filterEscolaridade',''); $set('filterEstado','')" type="button"
                            class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs lato-bold
                                   text-red-400 hover:text-red-600 border border-red-200 dark:border-red-900/50
                                   hover:border-red-400 rounded-xl bg-red-50 dark:bg-red-900/10 transition">
                        <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar
                    </button>
                    @endif
                </div>

                {{-- Chips de filtros ativos --}}
                @if ($search || $filterStatus || $filterEscolaridade || $filterEstado)
                <div class="flex items-center gap-2 flex-wrap pt-1">
                    <span class="text-[10px] text-slate-400 lato-regular uppercase tracking-wide">Filtros:</span>
                    @if ($search)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[11px] lato-bold border border-indigo-200 dark:border-indigo-700">
                        <x-lucide-search class="w-2.5 h-2.5" /> "{{ Str::limit($search, 18) }}"
                        <button wire:click="$set('search','')" class="cursor-pointer ml-0.5 hover:text-indigo-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                    @endif
                    @if ($filterStatus)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[11px] lato-bold border border-indigo-200 dark:border-indigo-700">
                        <span class="w-1.5 h-1.5 rounded-full {{ $statusOpts[$filterStatus][1] ?? 'bg-slate-400' }}"></span>
                        {{ $statusOpts[$filterStatus][0] ?? $filterStatus }}
                        <button wire:click="$set('filterStatus','')" class="cursor-pointer ml-0.5 hover:text-indigo-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                    @endif
                    @if ($filterEscolaridade)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[11px] lato-bold border border-indigo-200 dark:border-indigo-700">
                        <x-lucide-graduation-cap class="w-2.5 h-2.5" /> {{ $escOpts[$filterEscolaridade] ?? $filterEscolaridade }}
                        <button wire:click="$set('filterEscolaridade','')" class="cursor-pointer ml-0.5 hover:text-indigo-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                    @endif
                    @if ($filterEstado)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[11px] lato-bold border border-indigo-200 dark:border-indigo-700">
                        <x-lucide-map-pin class="w-2.5 h-2.5" /> {{ strtoupper($filterEstado) }}
                        <button wire:click="$set('filterEstado','')" class="cursor-pointer ml-0.5 hover:text-indigo-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                    @endif
                </div>
                @endif
            </div>

            {{-- Grid de currículos --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($this->curriculos as $curr)
                @php
                    $initials = collect(explode(' ', $curr->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                    $aColors  = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500','bg-rose-500','bg-amber-500'];
                    $avatarBg = $aColors[abs(crc32($curr->nome)) % count($aColors)];
                    [$sCls, $sLbl] = $statusCurrMap[$curr->status] ?? $statusCurrMap['inativo'];
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 hover:shadow-md transition flex flex-col gap-3"
                     x-data="{ menu: false }">
                    <div class="flex items-start gap-3">
                        <div class="w-11 h-11 rounded-xl {{ $avatarBg }} flex items-center justify-center text-white lato-black text-sm shrink-0">{{ $initials }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $curr->nome }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">{{ $curr->area_interesse ?? '—' }}</p>
                            @if ($curr->cidade || $curr->estado)
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">
                                <x-lucide-map-pin class="w-3 h-3 inline -mt-0.5" />
                                {{ trim(($curr->cidade ?? '') . ', ' . ($curr->estado ?? ''), ', ') }}
                            </p>
                            @endif
                        </div>
                        <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] lato-bold {{ $sCls }}">{{ $sLbl }}</span>
                    </div>

                    @if ($curr->tags && $curr->tags->count())
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($curr->tags->take(3) as $tag)
                        <span class="px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-[11px] lato-bold">
                            {{ $tag->tag }}
                        </span>
                        @endforeach
                        @if ($curr->tags->count() > 3)
                        <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 text-[11px] lato-regular">+{{ $curr->tags->count() - 3 }}</span>
                        @endif
                    </div>
                    @endif

                    <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 lato-regular">
                        <span class="flex items-center gap-1">
                            <x-lucide-graduation-cap class="w-3.5 h-3.5" />
                            {{ $escLabels[$curr->escolaridade] ?? ucfirst($curr->escolaridade ?? '—') }}
                        </span>
                        @if ($curr->pretensao_salarial)
                        <span class="flex items-center gap-1 text-emerald-600 dark:text-emerald-400 lato-bold">
                            <x-lucide-banknote class="w-3.5 h-3.5" />
                            R$ {{ number_format($curr->pretensao_salarial, 0, ',', '.') }}
                        </span>
                        @endif
                    </div>

                    @if ($curr->experiencias && $curr->experiencias->count())
                    @php $exp = $curr->experiencias->sortByDesc('data_inicio')->first(); @endphp
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">
                        <x-lucide-building-2 class="w-3 h-3 inline -mt-0.5 mr-0.5" />
                        {{ $exp->cargo ?? '' }}@if ($exp->empresa) · {{ $exp->empresa }}@endif
                    </p>
                    @endif

                    <div class="flex items-center gap-2 pt-1 border-t border-slate-100 dark:border-slate-700 mt-auto">
                        <button wire:click="openCurrDrawer({{ $curr->id }})" type="button"
                                class="cursor-pointer flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                            <x-lucide-eye class="w-3.5 h-3.5" /> Ver
                        </button>
                        <button wire:click="openCurrEdit({{ $curr->id }})" type="button"
                                class="cursor-pointer flex-1 flex items-center justify-center gap-1.5 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                            <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                        </button>
                        <button wire:click="toggleFavorito({{ $curr->id }})" type="button"
                                class="cursor-pointer p-2 rounded-xl {{ $curr->status === 'favorito' ? 'bg-amber-100 text-amber-500 dark:bg-amber-900/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-400' }} hover:bg-amber-100 hover:text-amber-500 dark:hover:bg-amber-900/30 transition">
                            <x-lucide-star class="w-3.5 h-3.5" />
                        </button>
                        <div class="relative" @click.outside="menu = false">
                            <button @click="menu = !menu" type="button"
                                    class="cursor-pointer p-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                <x-lucide-ellipsis-vertical class="w-3.5 h-3.5" />
                            </button>
                            <div x-show="menu" x-transition
                                 class="absolute right-0 bottom-full mb-2 w-44 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg z-20 py-1">
                                <button wire:click="moverBancoTalentos({{ $curr->id }})" @click="menu=false" type="button"
                                        class="cursor-pointer w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                    <x-lucide-database class="w-3.5 h-3.5 text-blue-500" /> Banco de Talentos
                                </button>
                                <button wire:click="confirmDeleteCurr({{ $curr->id }})" @click="menu=false" type="button"
                                        class="cursor-pointer w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" /> Excluir
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                        <x-lucide-file-search class="w-8 h-8 text-slate-400" />
                    </div>
                    <p class="text-slate-500 dark:text-slate-400 lato-bold">Nenhum currículo encontrado</p>
                    <p class="text-slate-400 text-sm lato-regular mt-1">Ajuste os filtros ou faça upload de um novo currículo.</p>
                </div>
                @endforelse
            </div>

            @if ($this->curriculos->hasPages())
            <div class="mt-2">{{ $this->curriculos->links() }}</div>
            @endif
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             ABA: VAGAS
        ═══════════════════════════════════════════════════════════ --}}
        @if ($aba === 'vagas')
        <div class="p-3 md:p-6 space-y-4 md:space-y-5" x-data="{}">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white">Gestão de Vagas</h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">Crie, publique e gerencie as vagas</p>
                </div>
                <button wire:click="openVagaCreate" type="button"
                        class="cursor-pointer flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                    <x-lucide-plus class="w-4 h-4" /> Nova Vaga
                </button>
            </div>

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3">

                {{-- Busca + Filtros --}}
                <div class="flex flex-col sm:flex-row gap-2">

                    {{-- Busca --}}
                    <div class="relative flex-1">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                        <input wire:model.live.debounce.300ms="vagaSearch" type="text" placeholder="Buscar vaga por título, cargo..."
                               class="w-full pl-10 pr-9 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400
                                      placeholder-slate-400 transition" />
                        @if ($vagaSearch)
                        <button wire:click="$set('vagaSearch','')" class="cursor-pointer absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                            <x-lucide-x class="w-3.5 h-3.5" />
                        </button>
                        @endif
                    </div>

                    {{-- Status Dropdown --}}
                    @php
                        $vsOpts = ['rascunho'=>['Rascunho','bg-slate-400'],'publicada'=>['Publicada','bg-green-400'],'pausada'=>['Pausada','bg-amber-400'],'encerrada'=>['Encerrada','bg-red-400']];
                        $vsLbl  = $vagaStatus ? ($vsOpts[$vagaStatus][0] ?? 'Status') : 'Status';
                        $vsDot  = $vagaStatus ? ($vsOpts[$vagaStatus][1] ?? null) : null;
                    @endphp
                    <div wire:key="dropdown-4" x-data="{ isOpen: false }" class="relative sm:w-48 shrink-0">
                        <button @click="isOpen = !isOpen" type="button"
                                class="cursor-pointer w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-900 transition
                                {{ $vagaStatus ? 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300' : 'border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate min-w-0">
                                @if ($vsDot)
                                    <span class="w-2 h-2 rounded-full {{ $vsDot }} shrink-0"></span>
                                @else
                                    <x-lucide-circle-dot class="w-3.5 h-3.5 shrink-0" />
                                @endif
                                <span class="truncate text-xs lato-bold">{{ $vsLbl }}</span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 ml-1 transition-transform" x-bind:class="isOpen ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="isOpen" @click.outside="isOpen = false" x-transition
                             class="absolute mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1">
                            <button @click="isOpen=false; $wire.set('vagaStatus','')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400">
                                <x-lucide-circle-dot class="w-3.5 h-3.5" /> Todos os status
                            </button>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                            @foreach ($vsOpts as $val => [$lbl, $dot])
                            <button @click="isOpen=false; $wire.set('vagaStatus','{{ $val }}')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs flex items-center gap-2 transition
                                    {{ $vagaStatus === $val ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-700 dark:text-indigo-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span> {{ $lbl }}
                                @if ($vagaStatus === $val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-indigo-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Limpar --}}
                    @if ($vagaSearch || $vagaStatus)
                    <button wire:click="$set('vagaSearch',''); $set('vagaStatus','')" type="button"
                            class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs lato-bold shrink-0
                                   text-red-400 hover:text-red-600 border border-red-200 dark:border-red-900/50
                                   hover:border-red-400 rounded-xl bg-red-50 dark:bg-red-900/10 transition">
                        <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar
                    </button>
                    @endif
                </div>

                {{-- Chips ativos --}}
                @if ($vagaSearch || $vagaStatus)
                <div class="flex items-center gap-2 flex-wrap">
                    <span class="text-[10px] text-slate-400 lato-regular uppercase tracking-wide">Filtros:</span>
                    @if ($vagaSearch)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[11px] lato-bold border border-indigo-200 dark:border-indigo-700">
                        <x-lucide-search class="w-2.5 h-2.5" /> "{{ Str::limit($vagaSearch, 20) }}"
                        <button wire:click="$set('vagaSearch','')" class="cursor-pointer ml-0.5 hover:text-indigo-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                    @endif
                    @if ($vagaStatus)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-[11px] lato-bold border border-indigo-200 dark:border-indigo-700">
                        <span class="w-1.5 h-1.5 rounded-full {{ $vsOpts[$vagaStatus][1] ?? 'bg-slate-400' }}"></span>
                        {{ $vsOpts[$vagaStatus][0] ?? $vagaStatus }}
                        <button wire:click="$set('vagaStatus','')" class="cursor-pointer ml-0.5 hover:text-indigo-900"><x-lucide-x class="w-2.5 h-2.5" /></button>
                    </span>
                    @endif
                </div>
                @endif
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($this->vagas as $vaga)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5 hover:shadow-md transition flex flex-col gap-3"
                     x-data="{ menu: false }" @click.away="menu = false">
                    <div class="flex items-start justify-between gap-2">
                        <div class="w-10 h-10 rounded-xl {{ portalVagaBg($vaga->titulo) }} flex items-center justify-center text-white lato-bold text-sm shrink-0">
                            {{ strtoupper(substr($vaga->titulo, 0, 1)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $vaga->titulo }}</h3>
                            <p class="text-xs text-slate-500 lato-regular">{{ $vaga->cargo }}</p>
                        </div>
                        <div class="relative shrink-0">
                            <button @click="menu = !menu" type="button"
                                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                <x-lucide-more-vertical class="w-4 h-4" />
                            </button>

                            <div x-show="menu" x-transition:enter="transition ease-out duration-100"
                                 x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                                 x-transition:leave="transition ease-in duration-75"
                                 x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
                                 class="absolute right-0 mt-1.5 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl shadow-xl z-20 overflow-hidden">

                                {{-- Grupo: visualizar --}}
                                <div class="px-1.5 pt-1.5 pb-1">
                                    <button wire:click="openVagaDrawer({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="cursor-pointer w-full text-left px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl flex items-center gap-2.5 transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                            <x-lucide-eye class="w-3.5 h-3.5 text-slate-500" />
                                        </span>
                                        Ver detalhes
                                    </button>
                                    <button wire:click="setAba('pipeline')" @click="menu=false" type="button"
                                            class="cursor-pointer w-full text-left px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl flex items-center gap-2.5 transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                            <x-lucide-git-branch class="w-3.5 h-3.5 text-slate-500" />
                                        </span>
                                        Ver Pipeline
                                    </button>
                                </div>

                                {{-- Divider --}}
                                <div class="border-t border-slate-100 dark:border-slate-700 mx-1.5"></div>

                                {{-- Grupo: editar / duplicar --}}
                                <div class="px-1.5 py-1">
                                    <button wire:click="openVagaEdit({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="cursor-pointer w-full text-left px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 rounded-xl flex items-center gap-2.5 transition">
                                        <span class="w-6 h-6 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-pencil class="w-3.5 h-3.5 text-indigo-500" />
                                        </span>
                                        Editar
                                    </button>
                                    <button wire:click="duplicarVaga({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="cursor-pointer w-full text-left px-3 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl flex items-center gap-2.5 transition">
                                        <span class="w-6 h-6 rounded-lg bg-slate-100 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                            <x-lucide-copy class="w-3.5 h-3.5 text-slate-500" />
                                        </span>
                                        Duplicar
                                    </button>
                                </div>

                                {{-- Grupo: status (condicional) --}}
                                @if ($vaga->status !== 'publicada' || $vaga->status !== 'encerrada')
                                <div class="border-t border-slate-100 dark:border-slate-700 mx-1.5"></div>
                                <div class="px-1.5 py-1">
                                    @if ($vaga->status !== 'publicada')
                                    <button wire:click="publicarVaga({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="cursor-pointer w-full text-left px-3 py-2 text-xs text-green-700 dark:text-green-400 hover:bg-green-50 dark:hover:bg-green-900/20 rounded-xl flex items-center gap-2.5 transition">
                                        <span class="w-6 h-6 rounded-lg bg-green-50 dark:bg-green-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-send class="w-3.5 h-3.5 text-green-500" />
                                        </span>
                                        Publicar
                                    </button>
                                    @endif
                                    @if ($vaga->status !== 'encerrada')
                                    <button wire:click="encerrarVaga({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="cursor-pointer w-full text-left px-3 py-2 text-xs text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-xl flex items-center gap-2.5 transition">
                                        <span class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-x-circle class="w-3.5 h-3.5 text-amber-500" />
                                        </span>
                                        Encerrar
                                    </button>
                                    @endif
                                </div>
                                @endif

                                {{-- Divider + Excluir --}}
                                <div class="border-t border-slate-100 dark:border-slate-700 mx-1.5"></div>
                                <div class="px-1.5 pb-1.5 pt-1">
                                    <button wire:click="confirmDeleteVaga({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="cursor-pointer w-full text-left px-3 py-2 text-xs text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 rounded-xl flex items-center gap-2.5 transition">
                                        <span class="w-6 h-6 rounded-lg bg-red-50 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5 text-red-500" />
                                        </span>
                                        Excluir
                                    </button>
                                </div>

                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1.5 flex-wrap">
                        <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $vagaStatusCls[$vaga->status] ?? 'bg-slate-100 text-slate-600' }}">
                            {{ $vagaStatusLbl[$vaga->status] ?? $vaga->status }}
                        </span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] lato-regular bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
                            {{ $vagaModalLbl[$vaga->modalidade] ?? $vaga->modalidade }}
                        </span>
                        @if ($vaga->cidade)
                        <span class="px-2 py-0.5 rounded-full text-[10px] lato-regular bg-slate-100 dark:bg-slate-700 text-slate-500 flex items-center gap-1">
                            <x-lucide-map-pin class="w-2.5 h-2.5" /> {{ $vaga->cidade }}{{ $vaga->estado ? '/'.$vaga->estado : '' }}
                        </span>
                        @endif
                    </div>

                    @if ($vaga->salario_min || $vaga->salario_max)
                    <p class="text-xs text-slate-600 dark:text-slate-300 lato-regular flex items-center gap-1">
                        <x-lucide-dollar-sign class="w-3.5 h-3.5 text-green-500" />
                        @if ($vaga->salario_min && $vaga->salario_max)
                            R$ {{ number_format($vaga->salario_min, 0, ',', '.') }} – {{ number_format($vaga->salario_max, 0, ',', '.') }}
                        @elseif ($vaga->salario_min)
                            A partir de R$ {{ number_format($vaga->salario_min, 0, ',', '.') }}
                        @else
                            Até R$ {{ number_format($vaga->salario_max, 0, ',', '.') }}
                        @endif
                    </p>
                    @endif

                    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                        <span class="text-xs text-slate-500 flex items-center gap-1">
                            <x-lucide-users class="w-3.5 h-3.5" /> {{ $vaga->candidaturas_count }} candidato{{ $vaga->candidaturas_count !== 1 ? 's' : '' }}
                        </span>
                        <button wire:click="setAba('pipeline')" type="button"
                                class="cursor-pointer text-xs text-blue-500 hover:text-blue-700 lato-bold flex items-center gap-1 transition">
                            Pipeline <x-lucide-arrow-right class="w-3 h-3" />
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full py-16 text-center">
                    <div class="w-16 h-16 mx-auto mb-4 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center">
                        <x-lucide-briefcase class="w-8 h-8 text-slate-400" />
                    </div>
                    <p class="text-slate-500 lato-bold text-sm">Nenhuma vaga encontrada.</p>
                    <p class="text-slate-400 lato-regular text-xs mt-1">Crie sua primeira vaga clicando em "Nova Vaga".</p>
                </div>
                @endforelse
            </div>
            {{ $this->vagas->links() }}
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             ABA: PIPELINE
        ═══════════════════════════════════════════════════════════ --}}
        @if ($aba === 'pipeline')
        <div class="p-3 md:p-6 space-y-4 md:space-y-5" x-data="{ drawerTab: 'perfil' }">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white">Pipeline de Seleção</h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">Gerencie candidatos por etapa</p>
                </div>
                <div class="flex items-center gap-3 flex-wrap">
                    <div class="relative group">
                        <x-lucide-briefcase class="absolute left-3.5 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none transition-colors
                            {{ $pipelineVagaId ? 'text-blue-500' : 'text-slate-400 group-focus-within:text-blue-500' }}" />
                        <select wire:model.live="pipelineVagaId"
                                class="appearance-none pl-10 pr-10 py-2.5 text-sm rounded-xl border shadow-sm transition-all duration-200 focus:outline-none cursor-pointer
                                       lato-bold min-w-[220px]
                                       {{ $pipelineVagaId
                                           ? 'bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 border-blue-300 dark:border-blue-600 ring-2 ring-blue-400/20'
                                           : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400' }}">
                            <option value="">Selecionar vaga...</option>
                            @foreach ($this->vagasParaPipeline as $vp)
                            <option value="{{ $vp->id }}">{{ $vp->titulo }}</option>
                            @endforeach
                        </select>
                        <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 pointer-events-none transition-colors
                            {{ $pipelineVagaId ? 'text-blue-400' : 'text-slate-400' }}" />
                    </div>
                    <div class="relative">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                        <input type="text" wire:model.live.debounce.300ms="pipelineSearch" placeholder="Buscar candidato..."
                               class="pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular w-44" />
                    </div>
                </div>
            </div>

            @if ($pipelineVagaId && $this->vagaAtualPipeline)
            @php $va = $this->vagaAtualPipeline; @endphp

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-wrap items-center gap-4 justify-between">
                <div class="flex items-center gap-4 flex-wrap">
                    <div>
                        <p class="text-xs text-slate-400 lato-regular">Vaga selecionada</p>
                        <p class="text-sm lato-black text-slate-800 dark:text-white">{{ $va->titulo }}</p>
                    </div>
                    <div class="hidden sm:block w-px h-8 bg-slate-200 dark:bg-slate-700"></div>
                    <div>
                        <p class="text-xs text-slate-400 lato-regular">Candidatos</p>
                        <p class="text-sm lato-black text-slate-800 dark:text-white">
                            {{ collect($this->kanban)->sum(fn($e) => $e['candidaturas']->count()) }}
                        </p>
                    </div>
                </div>
                <button wire:click="openVincular" type="button"
                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm shrink-0">
                    <x-lucide-user-plus class="w-4 h-4" /> Vincular Candidato
                </button>
            </div>

            @if (count($this->kanban) > 0)
            <div class="overflow-x-auto pb-4">
                <div class="flex gap-4 min-w-max">
                    @foreach ($this->kanban as $coluna)
                    @php $colEtapa = $coluna['etapa']; $colCands = $coluna['candidaturas']; @endphp
                    <div class="w-72 shrink-0 flex flex-col gap-3">
                        <div class="flex items-center justify-between px-3 py-2.5 rounded-xl bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center gap-2">
                                <div class="w-2.5 h-2.5 rounded-full {{ $colEtapa->cor }}"></div>
                                <span class="text-sm lato-black text-slate-800 dark:text-white">{{ $colEtapa->nome }}</span>
                            </div>
                            <span class="min-w-[22px] h-[22px] px-1.5 rounded-full text-[11px] lato-black text-white flex items-center justify-center {{ $colEtapa->cor }}">
                                {{ $colCands->count() }}
                            </span>
                        </div>

                        <div class="space-y-2.5 min-h-[120px]">
                            @forelse ($colCands as $cand)
                            @php
                                $cCurr    = $cand->curriculo;
                                $cInit    = $cCurr ? collect(explode(' ', $cCurr->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('') : '??';
                                $cColors  = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500'];
                                $cBg      = $cColors[abs(crc32($cCurr->nome ?? 'x')) % count($cColors)];
                                // Teste mais relevante: prioriza concluído > em_andamento > pendente
                                $cTeste = $cCurr?->testes?->sortBy(fn($t) => match($t->status) {
                                    'concluido'    => 0,
                                    'em_andamento' => 1,
                                    'pendente'     => 2,
                                    default        => 3,
                                })->first();
                            @endphp
                            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-3 hover:shadow-md transition cursor-pointer"
                                 x-data="{ moveMenu: false }"
                                 wire:click="openPipeDrawer({{ $cand->id }})" class="cursor-pointer">
                                <div class="flex items-start gap-2.5">
                                    <div class="w-8 h-8 rounded-lg {{ $cBg }} flex items-center justify-center text-white lato-black text-xs shrink-0">{{ $cInit }}</div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $cCurr->nome ?? '—' }}</p>
                                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">
                                            {{ $cCurr->area_interesse ?? '' }}@if ($cCurr->cidade ?? null) · {{ $cCurr->cidade }}@endif
                                        </p>
                                    </div>
                                    @if ($cand->nota_final !== null)
                                    <span class="shrink-0 text-xs lato-black {{ $cand->status === 'aprovado' ? 'text-green-500' : 'text-red-500' }}">
                                        {{ number_format($cand->nota_final, 1) }}
                                    </span>
                                    @endif
                                </div>

                                {{-- Badge resultado do teste --}}
                                @if ($cTeste)
                                <div class="mt-2 flex items-center gap-1.5 px-2 py-1 rounded-lg
                                    @if ($cTeste->status === 'concluido')
                                        {{ $cTeste->aprovado ? 'bg-emerald-50 dark:bg-emerald-900/20' : 'bg-rose-50 dark:bg-rose-900/20' }}
                                    @elseif ($cTeste->status === 'em_andamento')
                                        bg-blue-50 dark:bg-blue-900/20
                                    @else
                                        bg-amber-50 dark:bg-amber-900/20
                                    @endif
                                    " @click.stop>
                                    @if ($cTeste->status === 'concluido')
                                        @if ($cTeste->aprovado)
                                            <x-lucide-circle-check-big class="w-3 h-3 text-emerald-500 shrink-0" />
                                            <span class="text-[11px] lato-bold text-emerald-700 dark:text-emerald-400">Aprovado</span>
                                        @elseif ($cTeste->aprovado === false)
                                            <x-lucide-circle-x class="w-3 h-3 text-rose-500 shrink-0" />
                                            <span class="text-[11px] lato-bold text-rose-700 dark:text-rose-400">Reprovado</span>
                                        @else
                                            <x-lucide-clock class="w-3 h-3 text-amber-500 shrink-0" />
                                            <span class="text-[11px] lato-bold text-amber-700 dark:text-amber-400">Aguard. correção</span>
                                        @endif
                                        @if ($cTeste->nota !== null)
                                            <span class="ml-auto text-[11px] lato-black
                                                {{ $cTeste->aprovado ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                                {{ number_format($cTeste->nota, 1) }}
                                            </span>
                                        @endif
                                    @elseif ($cTeste->status === 'em_andamento')
                                        <x-lucide-loader class="w-3 h-3 text-blue-500 shrink-0" />
                                        <span class="text-[11px] lato-bold text-blue-700 dark:text-blue-400">Teste em andamento</span>
                                    @else
                                        <x-lucide-hourglass class="w-3 h-3 text-amber-500 shrink-0" />
                                        <span class="text-[11px] lato-bold text-amber-700 dark:text-amber-400">Teste enviado</span>
                                    @endif
                                </div>
                                @endif

                                @if ($cCurr && $cCurr->tags && $cCurr->tags->count())
                                <div class="flex flex-wrap gap-1 mt-2">
                                    @foreach ($cCurr->tags->take(2) as $cTag)
                                    <span class="px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-[10px] lato-bold">{{ $cTag->tag }}</span>
                                    @endforeach
                                    @if ($cCurr->tags->count() > 2)
                                    <span class="px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 text-[10px]">+{{ $cCurr->tags->count() - 2 }}</span>
                                    @endif
                                </div>
                                @endif

                                <div class="mt-2.5 pt-2.5 border-t border-slate-100 dark:border-slate-700" @click.stop>
                                    <div class="relative" @click.outside="moveMenu = false">
                                        <button @click="moveMenu = !moveMenu" type="button"
                                                class="cursor-pointer w-full flex items-center justify-between px-2.5 py-1.5 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs text-slate-500 lato-regular hover:bg-slate-100 dark:hover:bg-slate-800 transition">
                                            <span class="flex items-center gap-1"><x-lucide-move-right class="w-3 h-3" /> Mover para...</span>
                                            <x-lucide-chevron-down class="w-3 h-3" />
                                        </button>
                                        <div x-show="moveMenu" x-transition
                                             class="absolute bottom-full mb-1 left-0 right-0 bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg z-20 py-1 max-h-48 overflow-y-auto">
                                            @foreach ($this->kanban as $moverPara)
                                            @if ($moverPara['etapa']->id !== $colEtapa->id)
                                            <button wire:click="moverCandidato({{ $cand->id }}, {{ $moverPara['etapa']->id }})"
                                                    @click="moveMenu = false" type="button"
                                                    class="cursor-pointer w-full flex items-center gap-2 px-3 py-2 text-xs lato-regular text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                                                <div class="w-2 h-2 rounded-full {{ $moverPara['etapa']->cor }}"></div>
                                                {{ $moverPara['etapa']->nome }}
                                            </button>
                                            @endif
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @empty
                            <div class="flex flex-col items-center justify-center py-8 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-xl">
                                <x-lucide-users class="w-6 h-6 text-slate-300 dark:text-slate-600 mb-1" />
                                <p class="text-xs text-slate-400 lato-regular">Sem candidatos</p>
                            </div>
                            @endforelse
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @else
            <div class="flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                <x-lucide-layout-dashboard class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-3" />
                <p class="text-slate-500 dark:text-slate-400 lato-bold">Nenhuma etapa configurada</p>
                <p class="text-slate-400 text-sm lato-regular mt-1">Configure as etapas do processo seletivo para esta vaga.</p>
            </div>
            @endif

            @else
            <div class="flex flex-col items-center justify-center py-24 text-center bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700">
                <div class="w-20 h-20 rounded-2xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center mb-5">
                    <x-lucide-kanban class="w-10 h-10 text-indigo-400" />
                </div>
                <p class="text-slate-700 dark:text-slate-200 lato-black text-lg">Selecione uma vaga</p>
                <p class="text-slate-400 text-sm lato-regular mt-2 max-w-xs">Escolha uma vaga no seletor acima para visualizar o pipeline de candidatos.</p>
            </div>
            @endif



            {{-- Drawer: detalhe da candidatura --}}
            @if ($pipeDrawer && $this->pipeDrawerCand)
            @php
                $pd   = $this->pipeDrawerCand;
                $pdC  = $pd->curriculo;
                $pdIn = $pdC ? collect(explode(' ', $pdC->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('') : '??';
                $pdColors = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500'];
                $pdBg = $pdColors[abs(crc32($pdC->nome ?? 'x')) % count($pdColors)];
            @endphp
            <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('pipeDrawer', false)"></div>
            <div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] lg:w-[540px] bg-white dark:bg-slate-800 shadow-2xl overflow-y-auto flex flex-col">
                <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 z-10">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl {{ $pdBg }} flex items-center justify-center text-white lato-black text-sm shrink-0">{{ $pdIn }}</div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-black text-slate-800 dark:text-white truncate">{{ $pdC->nome ?? '—' }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">{{ $pdC->email ?? '' }}</p>
                        </div>
                        <button wire:click="$set('pipeDrawer', false)" type="button"
                                class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                            <x-lucide-x class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="flex items-center gap-1 mt-3 bg-slate-100 dark:bg-slate-700 rounded-xl p-1">
                        @foreach (['perfil' => 'Perfil', 'pipeline' => 'Pipeline', 'comentarios' => 'Comentários'] as $tKey => $tLbl)
                        <button @click="drawerTab = '{{ $tKey }}'" type="button"
                                :class="cursor-pointer drawerTab === '{{ $tKey }}' ? 'bg-white dark:bg-slate-600 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700'"
                                class="flex-1 py-1.5 text-xs lato-bold rounded-lg transition">
                            {{ $tLbl }}
                        </button>
                        @endforeach
                    </div>
                </div>

                <div class="flex-1 px-6 py-4">
                    <div x-show="drawerTab === 'perfil'" x-transition class="space-y-5">
                        <div class="grid grid-cols-2 gap-3">
                            @if ($pdC->telefone)
                            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                <x-lucide-phone class="w-4 h-4 text-slate-400" /> {{ $pdC->telefone }}
                            </div>
                            @endif
                            @if ($pdC->cidade || $pdC->estado)
                            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                <x-lucide-map-pin class="w-4 h-4 text-slate-400" /> {{ trim(($pdC->cidade ?? '').', '.($pdC->estado ?? ''), ', ') }}
                            </div>
                            @endif
                            @if ($pdC->area_interesse)
                            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                <x-lucide-briefcase class="w-4 h-4 text-slate-400" /> {{ $pdC->area_interesse }}
                            </div>
                            @endif
                            @if ($pdC->escolaridade)
                            <div class="flex items-center gap-2 text-xs text-slate-600 dark:text-slate-300">
                                <x-lucide-graduation-cap class="w-4 h-4 text-slate-400" /> {{ $escLabels[$pdC->escolaridade] ?? ucfirst($pdC->escolaridade) }}
                            </div>
                            @endif
                        </div>
                        @if ($pdC->resumo_profissional)
                        <div>
                            <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Resumo</h4>
                            <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed">{{ $pdC->resumo_profissional }}</p>
                        </div>
                        @endif
                        @if ($pdC->experiencias && $pdC->experiencias->count())
                        <div>
                            <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Experiências</h4>
                            <div class="relative pl-4 border-l-2 border-slate-200 dark:border-slate-700 space-y-3">
                                @foreach ($pdC->experiencias->sortByDesc('data_inicio') as $pdExp)
                                <div class="relative">
                                    <div class="absolute -left-[18px] top-1 w-3 h-3 rounded-full bg-blue-500 border-2 border-white dark:border-slate-800"></div>
                                    <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $pdExp->cargo }}</p>
                                    <p class="text-xs text-slate-500 lato-regular">{{ $pdExp->empresa }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                        @if ($pdC->habilidades && $pdC->habilidades->count())
                        <div>
                            <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Habilidades</h4>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach ($pdC->habilidades as $pdHab)
                                <span class="px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300 text-xs lato-bold">{{ $pdHab->nome }}</span>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        {{-- Testes online realizados --}}
                        @php $pdTestesP = $pd->curriculo->testes->where('vaga_id', $pd->vaga_id)->merge($pd->curriculo->testes->whereNull('vaga_id'))->sortByDesc('created_at'); @endphp
                        @if ($pdTestesP->count())
                        <div>
                            <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Testes online</h4>
                            <div class="space-y-2">
                                @foreach ($pdTestesP as $pdTP)
                                @php
                                    $tpAprovado = $pdTP->aprovado;
                                    $tpNota     = $pdTP->nota;
                                    $tpStatus   = $pdTP->status;
                                @endphp
                                <div class="rounded-xl border p-3 flex items-center gap-3
                                    {{ $tpStatus === 'concluido'
                                        ? ($tpAprovado === true  ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20'
                                        : ($tpAprovado === false ? 'border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20'
                                                                 : 'border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20'))
                                        : ($tpStatus === 'em_andamento'
                                            ? 'border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20'
                                            : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900') }}">

                                    {{-- Ícone de status --}}
                                    <div class="shrink-0">
                                        @if ($tpStatus === 'concluido' && $tpAprovado === true)
                                            <x-lucide-circle-check-big class="w-5 h-5 text-emerald-500" />
                                        @elseif ($tpStatus === 'concluido' && $tpAprovado === false)
                                            <x-lucide-circle-x class="w-5 h-5 text-rose-500" />
                                        @elseif ($tpStatus === 'concluido')
                                            <x-lucide-clock class="w-5 h-5 text-amber-500" />
                                        @elseif ($tpStatus === 'em_andamento')
                                            <x-lucide-loader class="w-5 h-5 text-blue-500" />
                                        @else
                                            <x-lucide-hourglass class="w-5 h-5 text-slate-400" />
                                        @endif
                                    </div>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-800 dark:text-white truncate">{{ $pdTP->teste?->titulo ?? '—' }}</p>
                                        <p class="text-[11px] text-slate-400 lato-regular">
                                            @if ($tpStatus === 'concluido')
                                                Concluído {{ $pdTP->concluido_at?->format('d/m/Y H:i') }}
                                            @elseif ($tpStatus === 'em_andamento')
                                                Iniciado {{ $pdTP->iniciado_at?->format('d/m/Y H:i') }}
                                            @else
                                                Enviado {{ $pdTP->created_at->format('d/m/Y H:i') }}
                                            @endif
                                        </p>
                                    </div>

                                    {{-- Nota + status --}}
                                    @if ($tpStatus === 'concluido')
                                    <div class="shrink-0 text-right">
                                        @if ($tpNota !== null)
                                        <p class="text-base lato-black leading-none
                                            {{ $tpAprovado === true ? 'text-emerald-600 dark:text-emerald-400' : ($tpAprovado === false ? 'text-rose-500' : 'text-amber-500') }}">
                                            {{ number_format($tpNota, 1) }}
                                        </p>
                                        <p class="text-[10px] text-slate-400">/ 100</p>
                                        @endif
                                        <p class="text-[11px] lato-bold mt-0.5
                                            {{ $tpAprovado === true ? 'text-emerald-600 dark:text-emerald-400' : ($tpAprovado === false ? 'text-rose-500' : 'text-amber-500') }}">
                                            {{ $tpAprovado === true ? 'Aprovado' : ($tpAprovado === false ? 'Reprovado' : 'Aguard. correção') }}
                                        </p>
                                    </div>
                                    @elseif ($tpStatus === 'em_andamento')
                                    <span class="shrink-0 text-[11px] lato-bold text-blue-600 dark:text-blue-400">Em andamento</span>
                                    @else
                                    <span class="shrink-0 text-[11px] lato-bold text-slate-400">Aguardando</span>
                                    @endif
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                    </div>

                    <div x-show="drawerTab === 'pipeline'" x-transition class="space-y-5">
                        <div class="p-4 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800">
                            <p class="text-xs text-blue-600 lato-regular">Etapa atual</p>
                            <p class="text-sm lato-black text-blue-800 dark:text-blue-300 mt-0.5">{{ $pd->etapa->nome ?? '—' }}</p>
                        </div>

                        {{-- Resultado dos testes online --}}
                        @php
                            $pdTestes = $pd->curriculo->testes->where('vaga_id', $pd->vaga_id)->sortByDesc('created_at');
                        @endphp
                        @if ($pdTestes->count())
                        <div>
                            <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Testes online</h4>
                            <div class="space-y-2">
                                @foreach ($pdTestes as $pdT)
                                @php
                                    $pdTTeste = $pdT->teste;
                                @endphp
                                <div class="rounded-xl border p-3
                                    {{ $pdT->status === 'concluido'
                                        ? ($pdT->aprovado ? 'border-emerald-200 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-900/20'
                                                          : ($pdT->aprovado === false ? 'border-rose-200 dark:border-rose-800 bg-rose-50 dark:bg-rose-900/20'
                                                                                      : 'border-amber-200 dark:border-amber-800 bg-amber-50 dark:bg-amber-900/20'))
                                        : ($pdT->status === 'em_andamento' ? 'border-blue-200 dark:border-blue-800 bg-blue-50 dark:bg-blue-900/20'
                                                                           : 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900') }}">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $pdTTeste->titulo ?? '—' }}</p>
                                            @if ($pdT->concluido_at)
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Concluído {{ $pdT->concluido_at->format('d/m/Y H:i') }}</p>
                                            @elseif ($pdT->iniciado_at)
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Iniciado {{ $pdT->iniciado_at->format('d/m/Y H:i') }}</p>
                                            @else
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">Enviado {{ $pdT->created_at->format('d/m/Y H:i') }}</p>
                                            @endif
                                        </div>
                                        <div class="shrink-0 text-right">
                                            @if ($pdT->status === 'concluido')
                                                @if ($pdT->nota !== null)
                                                <p class="text-base lato-black leading-none
                                                    {{ $pdT->aprovado ? 'text-emerald-600 dark:text-emerald-400' : ($pdT->aprovado === false ? 'text-rose-600 dark:text-rose-400' : 'text-amber-600 dark:text-amber-400') }}">
                                                    {{ number_format($pdT->nota, 1) }}
                                                </p>
                                                <p class="text-[10px] text-slate-400">/ 100</p>
                                                @endif
                                                <p class="text-[11px] lato-bold mt-1
                                                    {{ $pdT->aprovado ? 'text-emerald-600 dark:text-emerald-400' : ($pdT->aprovado === false ? 'text-rose-500' : 'text-amber-600') }}">
                                                    {{ $pdT->aprovado ? 'Aprovado' : ($pdT->aprovado === false ? 'Reprovado' : 'Corrigir') }}
                                                </p>
                                            @elseif ($pdT->status === 'em_andamento')
                                                <span class="text-[11px] lato-bold text-blue-600 dark:text-blue-400">Em andamento</span>
                                            @else
                                                <span class="text-[11px] lato-bold text-slate-400">Aguardando</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif

                        <div>
                            <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Nota final</h4>
                            <div class="flex items-center gap-3">
                                <input type="number" min="0" max="100" step="0.1"
                                       wire:model="pipeNotaInput" placeholder="0 – 100"
                                       class="flex-1 px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                                <button wire:click="salvarNotaCand({{ $pd->id }})" type="button"
                                        class="cursor-pointer px-4 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                                    Salvar
                                </button>
                            </div>
                        </div>
                        <div>
                            <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Mover para etapa</h4>
                            <div class="space-y-2">
                                @foreach ($this->kanban as $kOpt)
                                @if ($kOpt['etapa']->id !== $pd->etapa_id)
                                <button wire:click="moverCandidato({{ $pd->id }}, {{ $kOpt['etapa']->id }})" type="button"
                                        class="cursor-pointer w-full flex items-center gap-3 px-4 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-sm lato-regular text-slate-700 dark:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-800 transition text-left">
                                    <div class="w-2.5 h-2.5 rounded-full {{ $kOpt['etapa']->cor }} shrink-0"></div>
                                    {{ $kOpt['etapa']->nome }}
                                    <x-lucide-arrow-right class="w-3.5 h-3.5 text-slate-400 ml-auto" />
                                </button>
                                @endif
                                @endforeach
                            </div>
                        </div>
                        @if ($pd->historicos && $pd->historicos->count())
                        <div>
                            <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Histórico</h4>
                            <div class="relative pl-4 border-l-2 border-slate-200 dark:border-slate-700 space-y-3">
                                @foreach ($pd->historicos->sortByDesc('created_at') as $hist)
                                <div class="relative">
                                    <div class="absolute -left-[18px] top-1 w-3 h-3 rounded-full bg-slate-400 border-2 border-white dark:border-slate-800"></div>
                                    <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $hist->etapa_nova ?? $hist->acao }}</p>
                                    <p class="text-xs text-slate-400 lato-regular">{{ $hist->created_at->format('d/m/Y H:i') }} · {{ $hist->user->name ?? '' }}</p>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>

                    <div x-show="drawerTab === 'comentarios'" x-transition class="space-y-4">
                        @forelse ($pd->comentarios as $coment)
                        <div class="flex items-start gap-3">
                            <div class="w-8 h-8 rounded-full bg-slate-200 dark:bg-slate-700 flex items-center justify-center shrink-0">
                                <x-lucide-user class="w-4 h-4 text-slate-500" />
                            </div>
                            <div class="flex-1 bg-slate-50 dark:bg-slate-900 rounded-xl p-3 border border-slate-200 dark:border-slate-700">
                                <div class="flex items-center justify-between mb-1">
                                    <p class="text-xs lato-bold text-slate-800 dark:text-white">{{ $coment->user->name ?? 'Usuário' }}</p>
                                    <p class="text-[11px] text-slate-400 lato-regular">{{ $coment->created_at->format('d/m/Y H:i') }}</p>
                                </div>
                                <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed">{{ $coment->comentario }}</p>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-8">
                            <x-lucide-message-circle class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                            <p class="text-sm text-slate-400 lato-regular">Nenhum comentário ainda.</p>
                        </div>
                        @endforelse
                        <div class="pt-4 border-t border-slate-200 dark:border-slate-700">
                            <textarea wire:model="pipeComentario" rows="3" placeholder="Adicionar comentário..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                            <div class="flex justify-end mt-2">
                                <button wire:click="addPipeComentario" type="button"
                                        class="cursor-pointer inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                                    <x-lucide-send class="w-3.5 h-3.5" /> Comentar
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Modal: Vincular candidato --}}
            @if ($vincularModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
                 wire:click.self="$set('vincularModal', false)">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-lg p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base lato-black text-slate-800 dark:text-white">Vincular Candidato</h2>
                        <button wire:click="$set('vincularModal', false)" type="button" class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            <x-lucide-x class="w-5 h-5" />
                        </button>
                    </div>
                    <div class="relative mb-4">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                        <input type="text" wire:model.live.debounce.300ms="vincularSearch" placeholder="Buscar por nome ou email..."
                               class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                    </div>
                    <div class="space-y-2 max-h-64 overflow-y-auto">
                        @forelse ($this->curriculosParaVincular as $cvV)
                        @php
                            $cvVIn = collect(explode(' ', $cvV->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                            $cvVColors = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500'];
                            $cvVBg = $cvVColors[abs(crc32($cvV->nome)) % count($cvVColors)];
                        @endphp
                        <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-900 transition">
                            <div class="w-9 h-9 rounded-lg {{ $cvVBg }} flex items-center justify-center text-white lato-black text-xs shrink-0">{{ $cvVIn }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $cvV->nome }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">{{ $cvV->email ?? $cvV->area_interesse ?? '' }}</p>
                            </div>
                            <button wire:click="vincularCandidato({{ $cvV->id }})" type="button"
                                    class="cursor-pointer shrink-0 px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                                Vincular
                            </button>
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <x-lucide-user-x class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                            <p class="text-sm text-slate-400 lato-regular">Nenhum candidato disponível.</p>
                        </div>
                        @endforelse
                    </div>
                    <div class="flex justify-end mt-5">
                        <button wire:click="$set('vincularModal', false)" type="button"
                                class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            Fechar
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             ABA: TESTES
        ═══════════════════════════════════════════════════════════ --}}
        @if ($aba === 'testes')
        <div class="p-3 md:p-6 space-y-4 md:space-y-5">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white">Testes Online</h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">Crie e gerencie testes para candidatos</p>
                </div>
                <button wire:click="openTesteCreate" type="button"
                        class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm shrink-0">
                    <x-lucide-plus class="w-4 h-4" /> Novo Teste
                </button>
            </div>

            {{-- Sub-tabs --}}
            <div class="flex items-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 w-fit">
                @foreach (['lista' => 'Testes', 'resultados' => 'Resultados'] as $tKey => $tLabel)
                <button wire:click="$set('testeAba', '{{ $tKey }}')" type="button"
                        class="cursor-pointer flex items-center gap-1.5 px-5 py-1.5 text-sm lato-bold rounded-lg transition
                               {{ $testeAba === $tKey
                                   ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                                   : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                    @if ($tKey === 'lista') <x-lucide-clipboard-list class="w-3.5 h-3.5" />
                    @else <x-lucide-bar-chart-2 class="w-3.5 h-3.5" /> @endif
                    {{ $tLabel }}
                </button>
                @endforeach
            </div>

            @if ($testeAba === 'lista')
            <div class="relative max-w-sm">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input type="text" wire:model.live.debounce.400ms="testeSearch" placeholder="Buscar teste..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
                @forelse ($this->testes as $teste)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 hover:shadow-lg hover:border-indigo-200 dark:hover:border-indigo-700 transition-all duration-200 flex flex-col overflow-hidden">

                    {{-- Card top --}}
                    <div class="p-5 flex-1 flex flex-col gap-4">

                        {{-- Header --}}
                        <div class="flex items-start gap-3">
                            <div class="w-11 h-11 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 flex items-center justify-center shrink-0 shadow-sm">
                                <x-lucide-clipboard-list class="w-5 h-5 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm lato-black text-slate-800 dark:text-white leading-snug">{{ $teste->titulo }}</p>
                                @if ($teste->vaga)
                                <p class="text-[10px] text-indigo-500 dark:text-indigo-400 lato-bold mt-0.5 flex items-center gap-1 truncate">
                                    <x-lucide-briefcase class="w-3 h-3 shrink-0" /> {{ $teste->vaga->titulo }}
                                </p>
                                @elseif ($teste->descricao)
                                <p class="text-[11px] text-slate-500 dark:text-slate-400 lato-regular mt-0.5 line-clamp-1">{{ $teste->descricao }}</p>
                                @endif
                            </div>
                            <span class="shrink-0 px-2 py-0.5 rounded-full text-[10px] lato-bold
                                {{ $teste->ativo
                                    ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                    : 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400' }}">
                                {{ $teste->ativo ? 'Ativo' : 'Inativo' }}
                            </span>
                        </div>

                        {{-- Stats --}}
                        <div class="grid grid-cols-2 gap-2">
                            <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                <x-lucide-help-circle class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                                <div>
                                    <p class="text-xs lato-black text-slate-800 dark:text-white">{{ $teste->questoes_count ?? 0 }}</p>
                                    <p class="text-[10px] text-slate-400 lato-regular">questões</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                <x-lucide-users class="w-3.5 h-3.5 text-violet-400 shrink-0" />
                                <div>
                                    <p class="text-xs lato-black text-slate-800 dark:text-white">{{ $teste->tentativas_count ?? 0 }}</p>
                                    <p class="text-[10px] text-slate-400 lato-regular">tentativas</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                <x-lucide-clock class="w-3.5 h-3.5 text-blue-400 shrink-0" />
                                <div>
                                    <p class="text-xs lato-black text-slate-800 dark:text-white">{{ $teste->tempo_limite_minutos ?? '—' }} min</p>
                                    <p class="text-[10px] text-slate-400 lato-regular">duração</p>
                                </div>
                            </div>
                            <div class="flex items-center gap-2 px-3 py-2 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                                <x-lucide-award class="w-3.5 h-3.5 text-amber-400 shrink-0" />
                                <div>
                                    <p class="text-xs lato-black text-slate-800 dark:text-white">{{ $teste->nota_aprovacao ?? '—' }}%</p>
                                    <p class="text-[10px] text-slate-400 lato-regular">aprovação</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Footer com ações --}}
                    <div class="border-t border-slate-100 dark:border-slate-700 px-3 py-2.5 flex items-center gap-1.5 bg-slate-50/50 dark:bg-slate-800/80">
                        <button wire:click="openTesteDrawer({{ $teste->id }})" type="button"
                                class="cursor-pointer flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl text-xs lato-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 hover:shadow-sm transition">
                            <x-lucide-eye class="w-3.5 h-3.5" /> Ver
                        </button>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700"></div>
                        <button wire:click="openTesteEdit({{ $teste->id }})" type="button"
                                class="cursor-pointer flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl text-xs lato-bold text-indigo-600 dark:text-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition">
                            <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                        </button>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700"></div>
                        <button wire:click="openEnviar({{ $teste->id }})" type="button"
                                class="cursor-pointer flex-1 flex items-center justify-center gap-1.5 py-2 rounded-xl text-xs lato-bold text-blue-600 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition">
                            <x-lucide-send class="w-3.5 h-3.5" /> Enviar
                        </button>
                        <div class="w-px h-5 bg-slate-200 dark:bg-slate-700"></div>
                        <button wire:click="confirmDeleteTeste({{ $teste->id }})" type="button"
                                class="cursor-pointer p-2 rounded-xl text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        </button>
                    </div>

                </div>
                @empty
                <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
                    <div class="w-16 h-16 rounded-2xl bg-gradient-to-br from-indigo-50 to-violet-50 dark:from-indigo-900/20 dark:to-violet-900/20 flex items-center justify-center mb-4">
                        <x-lucide-clipboard-list class="w-8 h-8 text-indigo-300 dark:text-indigo-600" />
                    </div>
                    <p class="text-slate-600 dark:text-slate-400 lato-black">Nenhum teste criado</p>
                    <p class="text-slate-400 text-xs lato-regular mt-1">Clique em "Novo Teste" para criar o primeiro</p>
                </div>
                @endforelse
            </div>

            @elseif ($testeAba === 'resultados')
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wider">Candidato</th>
                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wider">Teste</th>
                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wider hidden lg:table-cell">Data</th>
                                <th class="text-center px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wider">Nota</th>
                                <th class="text-center px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="text-center px-4 py-3 text-xs lato-bold text-slate-500 uppercase tracking-wider">Ação</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @forelse ($this->resultados as $res)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 transition">
                                <td class="px-4 py-3">
                                    <p class="lato-bold text-slate-800 dark:text-white">{{ $res->curriculo?->nome ?? '—' }}</p>
                                    <p class="text-xs text-slate-500 lato-regular">{{ $res->curriculo?->email ?? '' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="lato-regular text-slate-700 dark:text-slate-300">{{ $res->teste?->titulo ?? '—' }}</p>
                                </td>
                                <td class="px-4 py-3 hidden lg:table-cell">
                                    <p class="lato-regular text-slate-500 dark:text-slate-400 text-xs">{{ $res->concluido_at?->format('d/m/Y H:i') ?? $res->created_at->format('d/m/Y') }}</p>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($res->nota !== null)
                                    <span class="text-sm lato-black {{ $res->aprovado ? 'text-green-500' : 'text-red-500' }}">{{ number_format($res->nota, 1) }}</span>
                                    @else
                                    <span class="text-xs text-slate-400 lato-regular">—</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($res->nota !== null)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] lato-bold {{ $res->aprovado ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                        {{ $res->aprovado ? 'Aprovado' : 'Reprovado' }}
                                    </span>
                                    @elseif ($res->concluido_at)
                                    <span class="px-2.5 py-1 rounded-full text-[11px] lato-bold bg-amber-100 text-amber-700">Corrigir</span>
                                    @else
                                    <span class="px-2.5 py-1 rounded-full text-[11px] lato-bold bg-slate-100 text-slate-600">Em andamento</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-2">
                                        @if ($res->concluido_at && $res->nota === null)
                                        <button wire:click="corrigirTeste({{ $res->id }})" type="button"
                                                class="cursor-pointer inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                                            <x-lucide-check-circle class="w-3.5 h-3.5" /> Corrigir
                                        </button>
                                        @endif
                                        @if ($res->status === 'concluido')
                                        <button wire:click="autorizarRefazer({{ $res->id }})" type="button"
                                                wire:confirm="Autorizar {{ $res->curriculo?->nome }} a refazer este teste? As respostas anteriores serão apagadas e um novo link será gerado."
                                                class="cursor-pointer inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700 text-amber-700 dark:text-amber-400 text-xs lato-bold hover:bg-amber-100 dark:hover:bg-amber-900/40 transition">
                                            <x-lucide-refresh-cw class="w-3.5 h-3.5" /> Refazer
                                        </button>
                                        @elseif ($res->status !== 'concluido' && $res->nota === null)
                                        <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-16 text-center">
                                    <x-lucide-bar-chart-2 class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                                    <p class="text-sm text-slate-400 lato-regular">Nenhum resultado registrado ainda.</p>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
            {{ $this->resultados->links() }}
            @endif

            {{-- Drawer: detalhe do teste --}}
            @if ($testeDrawer && $this->testeDrawerData)
            @php $dt = $this->testeDrawerData; @endphp
            <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('testeDrawer', false)"></div>
            <div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] lg:w-[560px] bg-white dark:bg-slate-800 shadow-2xl overflow-y-auto">
                <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center gap-3 z-10">
                    <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-clipboard-list class="w-5 h-5 text-indigo-500" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm lato-black text-slate-800 dark:text-white truncate">{{ $dt->titulo }}</p>
                        <p class="text-xs text-slate-400 lato-regular mt-0.5">
                            {{ $dt->questoes?->count() ?? 0 }} questões
                            @if ($dt->tempo_limite_minutos) · {{ $dt->tempo_limite_minutos }} min @endif
                            @if ($dt->nota_aprovacao) · Aprovação: {{ $dt->nota_aprovacao }}% @endif
                        </p>
                    </div>
                    <button wire:click="$set('testeDrawer', false)" type="button"
                            class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
                <div class="px-6 py-4 space-y-6">
                    @if ($dt->descricao)
                    <div>
                        <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Descrição</h4>
                        <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed">{{ $dt->descricao }}</p>
                    </div>
                    @endif
                    @if ($dt->instrucoes)
                    <div>
                        <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Instruções</h4>
                        <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dt->instrucoes }}</p>
                    </div>
                    @endif
                    <div class="grid grid-cols-2 gap-3">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-400 lato-regular">Randomizar questões</p>
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 mt-0.5">{{ $dt->randomizar_questoes ? 'Sim' : 'Não' }}</p>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                            <p class="text-xs text-slate-400 lato-regular">Randomizar opções</p>
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 mt-0.5">{{ $dt->randomizar_opcoes ? 'Sim' : 'Não' }}</p>
                        </div>
                    </div>
                    @if ($dt->questoes && $dt->questoes->count())
                    <div>
                        <div class="flex items-center justify-between mb-3">
                            <h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest">Questões</h4>
                            <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                {{ $dt->questoes->count() }} questão(ões)
                            </span>
                        </div>
                        <div class="space-y-3">
                            @foreach ($dt->questoes as $qi => $q)
                            @php
                                $isObj = $q->tipo === 'objetiva';
                                $correta = $isObj ? $q->opcoes->firstWhere('correta', true) : null;
                            @endphp
                            <div class="rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden">

                                {{-- Questão header --}}
                                <div class="flex items-center gap-3 px-4 py-2.5 bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700">
                                    <span class="shrink-0 w-6 h-6 rounded-lg {{ $isObj ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300' : 'bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-300' }} text-xs lato-black flex items-center justify-center">{{ $qi+1 }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-800 dark:text-white leading-relaxed">{{ $q->enunciado }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if ($q->peso > 1)
                                        <span class="text-[10px] lato-bold px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-500">peso {{ $q->peso }}</span>
                                        @endif
                                        <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full {{ $isObj ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-violet-50 text-violet-600 dark:bg-violet-900/30 dark:text-violet-400' }}">
                                            {{ $isObj ? 'Objetiva' : 'Discursiva' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Opções (objetiva) --}}
                                @if ($isObj && $q->opcoes && $q->opcoes->count())
                                <div class="px-4 py-3 bg-slate-50 dark:bg-slate-900 space-y-2">
                                    @foreach ($q->opcoes->sortBy('ordem') as $oi => $op)
                                    <div class="flex items-center gap-2.5 px-3 py-2 rounded-xl border transition
                                        {{ $op->correta
                                            ? 'bg-green-50 dark:bg-green-900/20 border-green-300 dark:border-green-700'
                                            : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-700' }}">
                                        {{-- Letra --}}
                                        <span class="shrink-0 w-5 h-5 rounded-md text-[10px] lato-black flex items-center justify-center
                                            {{ $op->correta
                                                ? 'bg-green-500 text-white'
                                                : 'bg-slate-200 dark:bg-slate-700 text-slate-500 dark:text-slate-400' }}">
                                            {{ chr(65 + $oi) }}
                                        </span>
                                        {{-- Texto --}}
                                        <p class="flex-1 text-sm lato-regular {{ $op->correta ? 'text-green-700 dark:text-green-300 lato-bold' : 'text-slate-600 dark:text-slate-400' }}">
                                            {{ $op->texto }}
                                        </p>
                                        {{-- Badge correta --}}
                                        @if ($op->correta)
                                        <span class="shrink-0 flex items-center gap-1 text-[10px] lato-bold text-green-600 dark:text-green-400 bg-green-100 dark:bg-green-900/40 px-2 py-0.5 rounded-full">
                                            <x-lucide-check class="w-3 h-3" /> Correta
                                        </span>
                                        @endif
                                    </div>
                                    @endforeach
                                </div>
                                @elseif (!$isObj)
                                <div class="px-4 py-3 bg-slate-50 dark:bg-slate-900">
                                    <div class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800">
                                        <x-lucide-pencil-line class="w-3.5 h-3.5 text-violet-400 shrink-0" />
                                        <p class="text-xs text-violet-600 dark:text-violet-400 lato-regular">Resposta livre — o candidato digitará a resposta.</p>
                                    </div>
                                </div>
                                @endif

                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            {{-- Modal: Criar/Editar Teste --}}
            @if ($testeModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
                <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[92vh] flex flex-col" @click.stop>

                    {{-- Header --}}
                    <div class="shrink-0 flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-violet-500 to-indigo-600 flex items-center justify-center shrink-0">
                                <x-lucide-clipboard-list class="w-4.5 h-4.5 text-white w-5 h-5" />
                            </div>
                            <div>
                                <h2 class="text-sm lato-black text-slate-800 dark:text-white">{{ $testeEditId ? 'Editar Teste' : 'Novo Teste' }}</h2>
                                <p class="text-[11px] text-slate-400 lato-regular">Preencha as informações e adicione as questões</p>
                            </div>
                        </div>
                        <button wire:click="$set('testeModal', false)" type="button"
                                class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            <x-lucide-x class="w-5 h-5" />
                        </button>
                    </div>

                    {{-- Body (scrollável) --}}
                    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

                        {{-- ── Seção 1: Identificação ── --}}
                        <div>
                            <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <x-lucide-info class="w-3.5 h-3.5" /> Identificação
                            </p>
                            <div class="space-y-3">
                                <div>
                                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Título do teste <span class="text-red-400">*</span></label>
                                    <input type="text" wire:model="tTitulo" placeholder="Ex: Teste de Raciocínio Lógico"
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular transition" />
                                    @error('tTitulo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>

                                {{-- Vaga vinculada --}}
                                <div>
                                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                                        Vincular à vaga
                                        <span class="ml-1 text-[10px] font-normal text-slate-400">(opcional)</span>
                                    </label>
                                    <div class="relative">
                                        <x-lucide-briefcase class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                        <select wire:model="tVagaId"
                                                class="cursor-pointer w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular transition appearance-none">
                                            <option value="">Nenhuma — teste genérico</option>
                                            @foreach ($this->vagasParaTeste as $vpt)
                                            <option value="{{ $vpt->id }}">{{ $vpt->titulo }}
                                                @php $stLabel = ['rascunho'=>'(Rascunho)','publicada'=>'(Publicada)','pausada'=>'(Pausada)','encerrada'=>'(Encerrada)']; @endphp
                                                {{ $stLabel[$vpt->status] ?? '' }}
                                            </option>
                                            @endforeach
                                        </select>
                                        <x-lucide-chevron-down class="absolute right-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                    </div>
                                    @if ($tVagaId)
                                    <p class="mt-1.5 text-[11px] text-indigo-600 dark:text-indigo-400 lato-regular flex items-center gap-1">
                                        <x-lucide-link class="w-3 h-3" /> Este teste ficará associado à vaga selecionada
                                    </p>
                                    @endif
                                </div>

                                <div>
                                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição</label>
                                    <textarea wire:model="tDescricao" rows="2" placeholder="Breve descrição do objetivo do teste..."
                                              class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular resize-none transition"></textarea>
                                </div>
                                <div>
                                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Instruções ao candidato</label>
                                    <textarea wire:model="tInstrucoes" rows="3" placeholder="Texto exibido antes do candidato iniciar o teste..."
                                              class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular resize-none transition"></textarea>
                                </div>
                            </div>
                        </div>

                        {{-- ── Seção 2: Configurações ── --}}
                        <div class="border-t border-slate-100 dark:border-slate-700 pt-5">
                            <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                                <x-lucide-settings class="w-3.5 h-3.5" /> Configurações
                            </p>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                                        <x-lucide-clock class="w-3.5 h-3.5 inline mr-1" />Tempo limite (minutos)
                                    </label>
                                    <input type="number" wire:model="tTempo" min="1" max="480"
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular transition" />
                                    @error('tTempo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                                </div>
                                <div>
                                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                                        <x-lucide-percent class="w-3.5 h-3.5 inline mr-1" />Nota de aprovação (%)
                                    </label>
                                    <input type="number" wire:model="tNota" min="0" max="100"
                                           class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular transition" />
                                </div>
                            </div>
                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
                                <label class="cursor-pointer flex items-center gap-3 px-3 py-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition select-none">
                                    <div class="relative shrink-0">
                                        <input type="checkbox" wire:model="tRandomQ" class="sr-only peer" />
                                        <div class="w-10 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-indigo-500 transition"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                                    </div>
                                    <div>
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">Randomizar questões</p>
                                        <p class="text-[10px] text-slate-400 lato-regular">Ordem aleatória</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer flex items-center gap-3 px-3 py-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-indigo-300 dark:hover:border-indigo-600 transition select-none">
                                    <div class="relative shrink-0">
                                        <input type="checkbox" wire:model="tRandomO" class="sr-only peer" />
                                        <div class="w-10 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-indigo-500 transition"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                                    </div>
                                    <div>
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">Randomizar opções</p>
                                        <p class="text-[10px] text-slate-400 lato-regular">Embaralha alternativas</p>
                                    </div>
                                </label>
                                <label class="cursor-pointer flex items-center gap-3 px-3 py-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:border-green-300 dark:hover:border-green-600 transition select-none">
                                    <div class="relative shrink-0">
                                        <input type="checkbox" wire:model="tAtivo" class="sr-only peer" />
                                        <div class="w-10 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-green-500 transition"></div>
                                        <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                                    </div>
                                    <div>
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">Teste ativo</p>
                                        <p class="text-[10px] text-slate-400 lato-regular">Disponível para envio</p>
                                    </div>
                                </label>
                            </div>
                        </div>

                        {{-- ── Seção 3: Questões ── --}}
                        <div class="border-t border-slate-100 dark:border-slate-700 pt-5">
                            <div class="flex items-center justify-between mb-4">
                                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                                    <x-lucide-help-circle class="w-3.5 h-3.5" /> Questões
                                    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 normal-case text-[9px]">{{ count($questoes) }}</span>
                                </p>
                                <div class="flex items-center gap-2">
                                    <button wire:click="addQuestao('objetiva')" type="button"
                                            class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 text-xs lato-bold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                                        <x-lucide-circle-dot class="w-3.5 h-3.5" /> Objetiva
                                    </button>
                                    <button wire:click="addQuestao('discursiva')" type="button"
                                            class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-300 text-xs lato-bold hover:bg-violet-100 dark:hover:bg-violet-900/50 transition">
                                        <x-lucide-pencil-line class="w-3.5 h-3.5" /> Discursiva
                                    </button>
                                </div>
                            </div>

                            @if (count($questoes) === 0)
                            <div class="flex flex-col items-center justify-center py-10 border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl text-center">
                                <x-lucide-help-circle class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2" />
                                <p class="text-sm lato-bold text-slate-500 dark:text-slate-400">Nenhuma questão ainda</p>
                                <p class="text-xs text-slate-400 lato-regular mt-1">Use os botões acima para adicionar questões objetivas ou discursivas</p>
                            </div>
                            @else
                            <div class="space-y-3">
                                @foreach ($questoes as $qi => $q)
                                <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 overflow-hidden">
                                    {{-- Questão header --}}
                                    <div class="flex items-center gap-3 px-4 py-2.5 bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700">
                                        <span class="shrink-0 w-6 h-6 rounded-lg {{ ($q['tipo'] ?? 'objetiva') === 'objetiva' ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600' : 'bg-violet-100 dark:bg-violet-900/40 text-violet-600' }} text-xs lato-black flex items-center justify-center">{{ $qi+1 }}</span>
                                        <span class="flex-1 text-xs lato-bold {{ ($q['tipo'] ?? 'objetiva') === 'objetiva' ? 'text-blue-600 dark:text-blue-400' : 'text-violet-600 dark:text-violet-400' }}">
                                            {{ ($q['tipo'] ?? 'objetiva') === 'objetiva' ? 'Objetiva' : 'Discursiva' }}
                                        </span>
                                        <div class="flex items-center gap-2">
                                            <select wire:model="questoes.{{ $qi }}.tipo"
                                                    class="cursor-pointer px-2 py-1 text-[10px] border border-slate-200 dark:border-slate-700 rounded-lg bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-300 focus:outline-none lato-regular">
                                                <option value="objetiva">Objetiva</option>
                                                <option value="discursiva">Discursiva</option>
                                            </select>
                                            <div class="flex items-center gap-1 text-[10px] text-slate-400 lato-regular">
                                                <span>Peso</span>
                                                <input type="number" wire:model="questoes.{{ $qi }}.peso" min="1" max="10"
                                                       class="w-12 px-2 py-1 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular" />
                                            </div>
                                            <button wire:click="removeQuestao({{ $qi }})" type="button"
                                                    class="cursor-pointer p-1.5 rounded-lg text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                    {{-- Questão body --}}
                                    <div class="p-4 space-y-3">
                                        <textarea wire:model="questoes.{{ $qi }}.enunciado" rows="2" placeholder="Enunciado da questão..."
                                                  class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular resize-none"></textarea>
                                        @if (($q['tipo'] ?? 'objetiva') === 'objetiva')
                                        <div class="space-y-2">
                                            @foreach (($q['opcoes'] ?? []) as $oi => $op)
                                            @php $isCorreta = $op['correta'] ?? false; @endphp
                                            <div class="flex items-center gap-2 rounded-xl border px-2 py-1.5 transition
                                                        {{ $isCorreta ? 'border-green-300 dark:border-green-700 bg-green-50 dark:bg-green-900/20' : 'border-transparent' }}">
                                                {{-- Botão: marcar como correta --}}
                                                <button wire:click="setCorreta({{ $qi }}, {{ $oi }})" type="button"
                                                        title="{{ $isCorreta ? 'Resposta correta' : 'Marcar como correta' }}"
                                                        class="cursor-pointer shrink-0 flex items-center gap-1.5 px-2 py-1 rounded-lg text-xs lato-bold transition
                                                               {{ $isCorreta
                                                                    ? 'bg-green-500 text-white'
                                                                    : 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 hover:bg-green-100 dark:hover:bg-green-900/30 hover:text-green-600 dark:hover:text-green-400' }}">
                                                    @if ($isCorreta)
                                                        <x-lucide-check class="w-3 h-3" />
                                                        Correta
                                                    @else
                                                        {{ chr(65+$oi) }}
                                                    @endif
                                                </button>
                                                <input type="text" wire:model="questoes.{{ $qi }}.opcoes.{{ $oi }}.texto"
                                                       placeholder="Texto da opção {{ chr(65+$oi) }}..."
                                                       class="flex-1 px-3 py-1.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular" />
                                                <button wire:click="removeOpcao({{ $qi }}, {{ $oi }})" type="button"
                                                        class="cursor-pointer shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                                    <x-lucide-x class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                            @endforeach
                                            <button wire:click="addOpcao({{ $qi }})" type="button"
                                                    class="cursor-pointer flex items-center gap-1.5 text-xs text-indigo-500 hover:text-indigo-600 lato-bold transition mt-1">
                                                <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar opção
                                            </button>
                                        </div>
                                        @else
                                        <div class="flex items-center gap-2 px-3 py-2.5 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-100 dark:border-violet-800">
                                            <x-lucide-pencil-line class="w-4 h-4 text-violet-400 shrink-0" />
                                            <p class="text-xs text-violet-600 dark:text-violet-400 lato-regular">Questão discursiva — o candidato digitará a resposta livremente.</p>
                                        </div>
                                        @endif
                                    </div>
                                </div>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>

                    {{-- Footer --}}
                    <div class="shrink-0 flex justify-between items-center px-6 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80 rounded-b-2xl">
                        <p class="text-xs text-slate-400 lato-regular">
                            {{ count($questoes) }} questão(ões) · {{ $tTempo }}min · aprovação {{ $tNota }}%
                        </p>
                        <div class="flex items-center gap-3">
                            <button wire:click="$set('testeModal', false)" type="button"
                                    class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
                            <button wire:click="saveTeste" type="button" wire:loading.attr="disabled" wire:target="saveTeste"
                                    class="cursor-pointer inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                                <span wire:loading.remove wire:target="saveTeste" class="flex items-center gap-2">
                                     <x-lucide-circle-check class="w-4 h-4" />
                                    Confirmar</span>
                                <span wire:loading wire:target="saveTeste">
                                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                </span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endif

            {{-- Modal: Enviar teste --}}
            @if ($enviarModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="$set('enviarModal', false)">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-lg p-6">
                    <div class="flex items-center justify-between mb-5">
                        <h2 class="text-base lato-black text-slate-800 dark:text-white">Enviar Teste a Candidato</h2>
                        <button wire:click="$set('enviarModal', false)" type="button" class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition"><x-lucide-x class="w-5 h-5" /></button>
                    </div>
                    <div class="relative mb-4">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                        <input type="text" wire:model.live.debounce.300ms="enviarSearch" placeholder="Buscar candidato..."
                               class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                    </div>
                    <div class="space-y-2 max-h-72 overflow-y-auto">
                        @forelse ($this->curriculosBuscaEnvio as $cvE)
                        @php
                            $cvEIn = collect(explode(' ', $cvE->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                            $cvEBg = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500'][abs(crc32($cvE->nome)) % 5];
                            $testeStatus = $cvE->teste_status ?? null;
                        @endphp
                        <div class="flex items-center gap-3 p-3 rounded-xl border transition
                                    {{ $testeStatus ? 'border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50 opacity-80' : 'border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-900' }}">
                            <div class="w-9 h-9 rounded-lg {{ $cvEBg }} flex items-center justify-center text-white lato-black text-xs shrink-0">{{ $cvEIn }}</div>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $cvE->nome }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">{{ $cvE->email ?? $cvE->area_interesse ?? '' }}</p>
                            </div>
                            @if (!$testeStatus)
                                <button wire:click="enviarTesteCand({{ $cvE->id }})" type="button"
                                        class="cursor-pointer shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                                    <x-lucide-send class="w-3 h-3" /> Enviar
                                </button>
                            @elseif ($testeStatus === 'concluido')
                                <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-xs lato-bold cursor-not-allowed">
                                    <x-lucide-circle-check-big class="w-3 h-3 text-emerald-500" /> Concluído
                                </span>
                            @elseif ($testeStatus === 'em_andamento')
                                <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 text-xs lato-bold cursor-not-allowed">
                                    <x-lucide-clock class="w-3 h-3" /> Em andamento
                                </span>
                            @elseif ($testeStatus === 'pendente')
                                <span class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 dark:bg-amber-900/20 text-amber-600 dark:text-amber-400 text-xs lato-bold cursor-not-allowed">
                                    <x-lucide-hourglass class="w-3 h-3" /> Aguardando
                                </span>
                            @endif
                        </div>
                        @empty
                        <div class="text-center py-10">
                            <x-lucide-user-x class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                            <p class="text-sm text-slate-400 lato-regular">{{ strlen($enviarSearch) < 2 ? 'Digite ao menos 2 caracteres para buscar.' : 'Nenhum candidato encontrado.' }}</p>
                        </div>
                        @endforelse
                    </div>
                    <div class="flex justify-end mt-5">
                        <button wire:click="$set('enviarModal', false)" type="button"
                                class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Fechar</button>
                    </div>
                </div>
            </div>
            @endif

            {{-- Modal: Excluir teste --}}
            @if ($testeDeleteModal)
            <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="$set('testeDeleteModal', false)">
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-sm p-6">
                    <div class="flex flex-col items-center text-center gap-4">
                        <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                            <x-lucide-trash-2 class="w-7 h-7 text-red-500" />
                        </div>
                        <div>
                            <h3 class="text-base lato-black text-slate-800 dark:text-white">Excluir teste?</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-1">Todos os resultados associados serão removidos permanentemente.</p>
                        </div>
                    </div>
                    <div class="flex gap-3 mt-6">
                        <button wire:click="$set('testeDeleteModal', false)" type="button"
                                class="cursor-pointer flex-1 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
                        <button wire:click="deleteTeste" type="button" wire:loading.attr="disabled" wire:target="deleteTeste"
                                class="cursor-pointer flex-1 px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm lato-bold hover:bg-red-600 transition disabled:opacity-60">
                            <span wire:loading.remove wire:target="deleteTeste">Confirmar</span>
                            <span wire:loading wire:target="deleteTeste">Excluindo...</span>
                        </button>
                    </div>
                </div>
            </div>
            @endif
        </div>
        @endif

        {{-- ═══════════════════════════════════════════════════════════
             ABA: DESLIGAMENTOS
        ═══════════════════════════════════════════════════════════ --}}
        @if ($aba === 'desligamentos')
        @php
            $ds = $this->demStats;
            $tipoLabels = \App\Models\RhDesligamento::$tipoLabels;
            $tipoCores  = \App\Models\RhDesligamento::$tipoCores;
            $respLabels = \App\Models\RhDesligamentoChecklist::$responsavelLabels;
            $respCores  = \App\Models\RhDesligamentoChecklist::$responsavelCores;
        @endphp
        <div class="p-3 md:p-6 space-y-4 md:space-y-5">

            {{-- Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white">Desligamentos</h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">Gerencie processos de desligamento de colaboradores</p>
                </div>
                @if ($demSubAba === 'processos')
                <button wire:click="openDemModal" type="button"
                        class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-rose-500 to-red-600 text-white text-sm lato-bold hover:from-rose-600 hover:to-red-700 transition shadow-sm shrink-0">
                    <x-lucide-user-minus class="w-4 h-4" /> Novo Desligamento
                </button>
                @endif
            </div>

            {{-- Sub-abas --}}
            <div class="flex gap-1 bg-slate-100 dark:bg-slate-800/80 border border-slate-200 dark:border-slate-700 p-1 rounded-xl w-fit">
                <button wire:click="$set('demSubAba','processos')" type="button"
                        class="cursor-pointer flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm lato-bold transition
                        {{ $demSubAba === 'processos' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300' }}">
                    <x-lucide-list class="w-4 h-4" /> Processos
                </button>
                <button wire:click="$set('demSubAba','dashboard')" type="button"
                        class="cursor-pointer flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm lato-bold transition
                        {{ $demSubAba === 'dashboard' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-300' }}">
                    <x-lucide-bar-chart-2 class="w-4 h-4" /> Dashboard de Turnover
                </button>
            </div>

            {{-- Stats cards --}}
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <p class="text-xs text-slate-400 lato-regular mb-1">Em processo</p>
                    <p class="text-2xl lato-black text-rose-600 dark:text-rose-400">{{ $ds['emProcesso'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <p class="text-xs text-slate-400 lato-regular mb-1">Concluídos este mês</p>
                    <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $ds['esteMes'] }}</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <p class="text-xs text-slate-400 lato-regular mb-1">Turnover mensal</p>
                    <p class="text-2xl lato-black text-amber-600 dark:text-amber-400">{{ $ds['turnover'] }}%</p>
                </div>
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                    <p class="text-xs text-slate-400 lato-regular mb-1">Motivo mais frequente</p>
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">
                        {{ $ds['motivoTop'] ? (\App\Models\RhEntrevistaDesligamento::$motivosPrincipais[$ds['motivoTop']->motivo_principal] ?? $ds['motivoTop']->motivo_principal) : '—' }}
                    </p>
                </div>
            </div>

            @if ($demSubAba === 'processos')
            {{-- Filtros --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex flex-col gap-3">

                {{-- Busca --}}
                <div class="relative w-full">
                    <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    <input type="text" wire:model.live.debounce.300ms="demSearch"
                           placeholder="Buscar colaborador por nome..."
                           class="w-full pl-10 pr-9 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                  bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                  focus:outline-none focus:ring-2 focus:ring-rose-400/40 focus:border-rose-400
                                  placeholder-slate-400 transition" />
                    @if ($demSearch)
                    <button wire:click="$set('demSearch','')" class="cursor-pointer absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                    @endif
                </div>

                {{-- Dropdowns --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">

                    {{-- Status --}}
                    @php
                        $statusDemOpts = [
                            'em_processo' => ['Em Processo',  'bg-amber-400'],
                            'concluido'   => ['Concluído',    'bg-emerald-400'],
                            'cancelado'   => ['Cancelado',    'bg-slate-400'],
                        ];
                        $statusDemLbl = $demStatusFiltro ? ($statusDemOpts[$demStatusFiltro][0] ?? 'Status') : 'Status';
                        $statusDemDot = $demStatusFiltro ? ($statusDemOpts[$demStatusFiltro][1] ?? null) : null;
                    @endphp
                    <div wire:key="dropdown-5" x-data="{ isOpen: false }" class="relative">
                        <button @click="isOpen = !isOpen" type="button"
                                class="cursor-pointer w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 transition
                                {{ $demStatusFiltro ? 'border-rose-400 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate min-w-0">
                                @if ($statusDemDot)
                                    <span class="w-2 h-2 rounded-full {{ $statusDemDot }} shrink-0"></span>
                                @else
                                    <x-lucide-circle-dot class="w-3.5 h-3.5 shrink-0" />
                                @endif
                                <span class="truncate text-xs lato-bold">{{ $statusDemLbl }}</span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform ml-1" x-bind:class="isOpen ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="isOpen" @click.outside="isOpen = false" x-transition
                             class="absolute mt-1 w-48 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1">
                            <button @click="isOpen=false; $wire.set('demStatusFiltro','')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <x-lucide-circle-dot class="w-3.5 h-3.5" /> Todos os status
                            </button>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                            @foreach ($statusDemOpts as $val => [$lbl, $dot])
                            <button @click="isOpen=false; $wire.set('demStatusFiltro','{{ $val }}')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs flex items-center gap-2 transition
                                    {{ $demStatusFiltro === $val ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                <span class="w-2 h-2 rounded-full {{ $dot }} shrink-0"></span> {{ $lbl }}
                                @if ($demStatusFiltro === $val) <x-lucide-check class="w-3.5 h-3.5 ml-auto text-rose-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Tipo --}}
                    @php
                        $tipoDemLbl = $demTipoFiltro ? ($tipoLabels[$demTipoFiltro] ?? 'Tipo') : 'Tipo';
                    @endphp
                    <div wire:key="dropdown-6" x-data="{ isOpen: false }" class="relative">
                        <button @click="isOpen = !isOpen" type="button"
                                class="cursor-pointer w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 transition
                                {{ $demTipoFiltro ? 'border-rose-400 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300' : 'bg-white dark:bg-slate-900 border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400' }}">
                            <div class="flex items-center gap-1.5 truncate min-w-0">
                                <x-lucide-tag class="w-3.5 h-3.5 shrink-0" />
                                <span class="truncate text-xs lato-bold">{{ $tipoDemLbl }}</span>
                            </div>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 shrink-0 transition-transform ml-1" x-bind:class="isOpen ? 'rotate-180' : ''" />
                        </button>
                        <div x-show="isOpen" @click.outside="isOpen = false" x-transition
                             class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1">
                            <button @click="isOpen=false; $wire.set('demTipoFiltro','')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-500 dark:text-slate-400 flex items-center gap-2">
                                <x-lucide-tag class="w-3.5 h-3.5" /> Todos os tipos
                            </button>
                            <div class="border-t border-slate-100 dark:border-slate-700 my-1"></div>
                            @foreach ($tipoLabels as $val => $lbl)
                            <button @click="isOpen=false; $wire.set('demTipoFiltro','{{ $val }}')" type="button"
                                    class="cursor-pointer w-full text-left px-3 py-2 text-xs flex items-center justify-between gap-2 transition
                                    {{ $demTipoFiltro === $val ? 'bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300 lato-bold' : 'hover:bg-slate-50 dark:hover:bg-slate-700/50 text-slate-700 dark:text-slate-300' }}">
                                {{ $lbl }}
                                @if ($demTipoFiltro === $val) <x-lucide-check class="w-3.5 h-3.5 text-rose-500 shrink-0" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Limpar filtros --}}
                    @if ($demSearch || $demStatusFiltro || $demTipoFiltro)
                    <button wire:click="$set('demSearch',''); $set('demStatusFiltro',''); $set('demTipoFiltro','')" type="button"
                            class="cursor-pointer flex items-center justify-center gap-1.5 px-3 py-2.5 rounded-xl border border-rose-200 dark:border-rose-700 text-xs lato-bold text-rose-500 dark:text-rose-400 hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
                        <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar
                    </button>
                    @endif
                </div>
            </div>

            {{-- Lista --}}
            <div class="space-y-3">
                @forelse ($this->desligamentos as $des)
                @php
                    $prog = $des->checklist_progresso;
                @endphp
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4 hover:border-rose-300 dark:hover:border-rose-600 transition cursor-pointer"
                     wire:click="openDemDrawer({{ $des->id }})">
                    <div class="flex items-start gap-4">
                        {{-- Avatar --}}
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-red-600 flex items-center justify-center text-white lato-bold text-sm shrink-0">
                            {{ strtoupper(substr($des->funcionario?->name ?? '?', 0, 1)) }}
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 flex-wrap">
                                <span class="text-sm lato-bold text-slate-800 dark:text-white">{{ $des->funcionario?->name ?? '—' }}</span>
                                <span class="text-xs px-2 py-0.5 rounded-full {{ $tipoCores[$des->tipo] ?? 'bg-slate-100 text-slate-600' }}">
                                    {{ $tipoLabels[$des->tipo] ?? $des->tipo }}
                                </span>
                                @if ($des->status === 'concluido')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">Concluído</span>
                                @elseif ($des->status === 'cancelado')
                                <span class="text-xs px-2 py-0.5 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">Cancelado</span>
                                @else
                                <span class="text-xs px-2 py-0.5 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Em processo</span>
                                @endif
                            </div>
                            <p class="text-xs text-slate-400 mt-0.5">
                                {{ $des->funcionario?->department?->name ?? '' }}
                                @if ($des->funcionario?->position) · {{ $des->funcionario->position }} @endif
                            </p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
                                Último dia: <span class="lato-bold">{{ $des->data_ultimo_dia?->format('d/m/Y') ?? '—' }}</span>
                                @if ($des->data_aviso)
                                · Aviso: {{ $des->data_aviso->format('d/m/Y') }}
                                @endif
                            </p>
                            {{-- Checklist progress --}}
                            @if ($prog['total'] > 0)
                            <div class="mt-2">
                                <div class="flex items-center justify-between mb-0.5">
                                    <span class="text-xs text-slate-400">Checklist</span>
                                    <span class="text-xs text-slate-500">{{ $prog['concluidos'] }}/{{ $prog['total'] }}</span>
                                </div>
                                <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full transition-all
                                        {{ $prog['pct'] === 100 ? 'bg-emerald-500' : 'bg-rose-500' }}"
                                         style="width: {{ $prog['pct'] }}%"></div>
                                </div>
                            </div>
                            @endif
                        </div>
                        {{-- Arrow --}}
                        <x-lucide-chevron-right class="w-4 h-4 text-slate-400 shrink-0 mt-1" />
                    </div>
                </div>
                @empty
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <x-lucide-user-minus class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-3" />
                    <p class="text-sm text-slate-400 lato-regular">Nenhum desligamento encontrado</p>
                </div>
                @endforelse

                {{ $this->desligamentos->links() }}
            </div>
            @elseif ($demSubAba === 'dashboard')
            {{-- ═══ DASHBOARD DE TURNOVER ═══ --}}
            @php
                $td = $this->demTurnoverData;
                $maxMes = max(array_column($td['meses'], 'total') ?: [1]);
                $tipoLabelsD = \App\Models\RhDesligamento::$tipoLabels;
                $tipoCoresD  = \App\Models\RhDesligamento::$tipoCores;
                $totalPorTipo = array_sum($td['porTipo'] ?: [1]);
                $motivoLabels = \App\Models\RhEntrevistaDesligamento::$motivosPrincipais;
            @endphp

            <div class="space-y-4">
                {{-- KPIs --}}
                <div class="grid grid-cols-2 lg:grid-cols-4 gap-3">
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <p class="text-xs text-slate-400 lato-regular mb-1">Total desligados</p>
                        <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $td['totalConc'] }}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <p class="text-xs text-slate-400 lato-regular mb-1">Em processo</p>
                        <p class="text-2xl lato-black text-amber-600 dark:text-amber-400">{{ $ds['emProcesso'] }}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <p class="text-xs text-slate-400 lato-regular mb-1">Recontratáveis</p>
                        <p class="text-2xl lato-black text-emerald-600 dark:text-emerald-400">{{ $td['recontratavel'] }}</p>
                    </div>
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-4">
                        <p class="text-xs text-slate-400 lato-regular mb-1">Não recontratáveis</p>
                        <p class="text-2xl lato-black text-rose-600 dark:text-rose-400">{{ $td['naoRecontr'] }}</p>
                    </div>
                </div>

                {{-- Gráfico: Desligamentos por mês (últimos 12 meses) --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                    <h3 class="text-sm lato-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                        <x-lucide-trending-up class="w-4 h-4 text-rose-500" />
                        Desligamentos por mês (últimos 12 meses)
                    </h3>
                    <div class="flex items-end gap-2 h-40">
                        @foreach ($td['meses'] as $m)
                        @php $pct = $maxMes > 0 ? ($m['total'] / $maxMes * 100) : 0; @endphp
                        <div class="flex-1 flex flex-col items-center gap-1 min-w-0">
                            <span class="text-[10px] lato-bold text-slate-600 dark:text-slate-300">{{ $m['total'] > 0 ? $m['total'] : '' }}</span>
                            <div class="w-full rounded-t-md transition-all"
                                 style="height: {{ max($pct, $m['total'] > 0 ? 4 : 1) }}%; background: {{ $m['total'] > 0 ? 'linear-gradient(to top, #f43f5e, #fb7185)' : '#e2e8f0' }}; min-height: 4px;"></div>
                            <span class="text-[9px] text-slate-400 truncate w-full text-center">{{ $m['mes'] }}</span>
                        </div>
                        @endforeach
                    </div>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    {{-- Por tipo --}}
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                        <h3 class="text-sm lato-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                            <x-lucide-pie-chart class="w-4 h-4 text-rose-500" />
                            Distribuição por tipo
                        </h3>
                        @if (empty($td['porTipo']))
                        <p class="text-xs text-slate-400 text-center py-8">Sem dados ainda</p>
                        @else
                        <div class="space-y-2.5">
                            @foreach ($td['porTipo'] as $tipo => $qtd)
                            @php $pctTipo = $totalPorTipo > 0 ? round($qtd / $totalPorTipo * 100) : 0; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $tipoLabelsD[$tipo] ?? $tipo }}</span>
                                    <span class="text-xs text-slate-400">{{ $qtd }} ({{ $pctTipo }}%)</span>
                                </div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full"
                                         style="width: {{ $pctTipo }}%; background: linear-gradient(to right, #f43f5e, #fb923c)"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>

                    {{-- Motivos mais citados --}}
                    <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-5">
                        <h3 class="text-sm lato-black text-slate-700 dark:text-white mb-4 flex items-center gap-2">
                            <x-lucide-message-square class="w-4 h-4 text-rose-500" />
                            Motivos mais citados nas entrevistas
                        </h3>
                        @if (empty($td['motivos']))
                        <p class="text-xs text-slate-400 text-center py-8">Nenhuma entrevista respondida ainda</p>
                        @else
                        @php $maxMotivo = max(array_column($td['motivos'], 'total') ?: [1]); @endphp
                        <div class="space-y-2.5">
                            @foreach ($td['motivos'] as $m)
                            @php $pctM = $maxMotivo > 0 ? round($m['total'] / $maxMotivo * 100) : 0; @endphp
                            <div>
                                <div class="flex items-center justify-between mb-1">
                                    <span class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate max-w-[70%]">{{ $m['motivo'] }}</span>
                                    <span class="text-xs text-slate-400">{{ $m['total'] }}</span>
                                </div>
                                <div class="h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full bg-rose-400"
                                         style="width: {{ $pctM }}%"></div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        @endif
                    </div>
                </div>
            </div>
            @endif {{-- fim sub-abas processos/dashboard --}}
        </div>
        @endif


        @if ($aba === 'onboarding')
        <div class="flex-1">
            @livewire('pages.rh.onboarding')
        </div>
        @endif

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
                        <x-lucide-briefcase class="w-4 h-4 text-violet-500" />
                        Por Cargo (Top 10)
                    </h3>
                    @if(count($tv['porCargo']) > 0)
                    <div class="space-y-2">
                        @foreach ($tv['porCargo'] as $row)
                        @php $pctC = $tv['total'] > 0 ? round(($row['total'] / $tv['total']) * 100) : 0; @endphp
                        <div class="flex items-center gap-2">
                            <span class="text-xs lato-regular text-slate-600 dark:text-slate-300 truncate flex-1 min-w-0">{{ $row['nome'] }}</span>
                            <div class="w-24 bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 shrink-0">
                                <div class="bg-gradient-to-r from-violet-500 to-purple-500 h-1.5 rounded-full" style="width: {{ $pctC }}%"></div>
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
        @endif

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
                                   class="w-full pl-9 pr-2 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent lato-regular
                                          {{ $climaInicio ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/10 text-violet-700 dark:text-violet-300' : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200' }}" />
                        </div>
                        <span class="text-gray-400 text-sm shrink-0">→</span>
                        <div class="relative flex-1 min-w-0">
                            <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400 pointer-events-none" />
                            <input wire:model="climaFim" type="date"
                                   class="w-full pl-9 pr-2 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400 focus:border-transparent lato-regular
                                          {{ $climaFim ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/10 text-violet-700 dark:text-violet-300' : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200' }}" />
                        </div>
                    </div>
                    <button wire:click="climaAplicar" type="button"
                            class="cursor-pointer flex items-center justify-center gap-1.5 px-4 py-2 text-sm lato-bold text-white bg-gradient-to-r from-violet-500 to-purple-600 hover:from-violet-600 hover:to-purple-700 rounded-lg transition shrink-0 w-full sm:w-auto">
                        <x-lucide-filter class="w-3.5 h-3.5" /> Aplicar
                    </button>
                </div>

                {{-- Linha inferior: selects --}}
                <div class="flex flex-col sm:flex-row gap-2">
                    {{-- Departamento --}}
                    <select wire:model="climaDepartamento" wire:change="climaAplicar"
                            class="flex-1 px-3 py-2 text-sm border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400 lato-regular cursor-pointer
                                   {{ $climaDepartamento ? 'border-violet-400 bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-gray-500 dark:text-slate-400' }}">
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
                        <x-lucide-trending-up class="w-4 h-4 text-violet-500" />
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
                        <x-lucide-activity class="w-4 h-4 text-violet-500" />
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
                ['label'=>'Total',        'value'=>$solKpis['total'],        'color'=>'text-slate-700 dark:text-slate-200',   'bg'=>'bg-slate-50 dark:bg-slate-700/50',   'icon'=>'inbox'],
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
                        class="cursor-pointer px-4 py-2 rounded-lg bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm lato-bold hover:from-amber-600 hover:to-orange-600 transition shadow-sm">
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
                <select wire:model="mapaDept"
                        class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-cyan-400 lato-regular cursor-pointer">
                    <option value="">Todos os departamentos</option>
                    @foreach ($depts as $dept)
                        <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                    @endforeach
                </select>
                <select wire:model="mapaAno"
                        class="px-3 py-2 text-sm border border-slate-200 dark:border-slate-600 rounded-lg bg-white dark:bg-slate-700 text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-1 focus:ring-cyan-400 lato-regular cursor-pointer">
                    @foreach ($nivelAnos as $y)
                        <option value="{{ $y }}" {{ $mapa['ano'] == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button wire:click="mapaAplicar" type="button"
                        class="cursor-pointer px-4 py-2 rounded-lg bg-gradient-to-r from-cyan-500 to-blue-500 text-white text-sm lato-bold hover:from-cyan-600 hover:to-blue-600 transition shadow-sm">
                    Aplicar
                </button>
            </div>
        </div>

        {{-- KPIs --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @foreach ([
                ['label'=>'Funcionários',    'value'=>$mapa['kpis']['total_funcionarios'], 'sub'=>'ativos no período',      'bg'=>'bg-slate-50 dark:bg-slate-700/50',    'color'=>'text-slate-700 dark:text-slate-200',    'icon'=>'users'],
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
                                    <td class="sticky left-0 z-10 bg-white dark:bg-slate-800 group-hover:bg-slate-50 dark:group-hover:bg-slate-700/30 px-4 py-3 whitespace-nowrap transition">
                                        <a href="{{ route('dpi') }}" target="_blank" class="flex items-center gap-2 hover:text-cyan-600 transition">
                                            <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-[10px] lato-bold shrink-0">
                                                {{ strtoupper(substr($func['nome'], 0, 1)) }}
                                            </div>
                                            <div>
                                                <p class="lato-bold text-slate-700 dark:text-slate-200 truncate max-w-[9rem]">{{ $func['nome'] }}</p>
                                                @if ($func['position'])
                                                <p class="text-[10px] text-slate-400 truncate max-w-[9rem]">{{ $func['position'] }}</p>
                                                @endif
                                            </div>
                                        </a>
                                    </td>
                                    {{-- Células de competência --}}
                                    @foreach ($mapa['competencias'] as $comp)
                                    @php
                                        $cel = $func['niveis'][$comp] ?? null;
                                        $gap = $cel ? min($cel['gap'], 3) : null;
                                        $cls = $gap !== null ? ($gapCores[$gap] ?? $gapCores[3]) : 'bg-slate-100 dark:bg-slate-700 text-slate-400';
                                    @endphp
                                    <td class="px-2 py-3 text-center">
                                        @if ($cel)
                                        <div class="inline-flex flex-col items-center gap-0.5 group/cell relative cursor-default">
                                            <span class="w-9 h-9 rounded-lg {{ $cls }} flex items-center justify-center lato-black text-sm font-bold transition hover:scale-110 hover:shadow-md"
                                                  title="{{ $comp }}: Atual {{ $nivelLabels[$cel['atual']] ?? $cel['atual'] }} → Meta {{ $nivelLabels[$cel['meta']] ?? $cel['meta'] }}">
                                                {{ $cel['atual'] }}
                                            </span>
                                            <span class="text-[9px] text-slate-400">/ {{ $cel['meta'] }}</span>
                                        </div>
                                        @else
                                        <span class="w-9 h-9 rounded-lg bg-slate-100 dark:bg-slate-700 border border-dashed border-slate-200 dark:border-slate-600 flex items-center justify-center text-slate-300 text-xs mx-auto">—</span>
                                        @endif
                                    </td>
                                    @endforeach
                                    {{-- Gap total --}}
                                    <td class="px-3 py-3 text-center">
                                        @if ($funcGapTotal > 0)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full lato-black text-sm
                                                     {{ $funcGapTotal >= 6 ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300'
                                                      : ($funcGapTotal >= 3 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300'
                                                      : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300') }}">
                                            {{ $funcGapTotal }}
                                        </span>
                                        @else
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400">
                                            <x-lucide-check class="w-4 h-4" />
                                        </span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach

                                {{-- Linha de média do departamento --}}
                                <tr class="bg-slate-50 dark:bg-slate-700/30 border-t-2 border-slate-200 dark:border-slate-600">
                                    <td class="sticky left-0 z-10 bg-slate-50 dark:bg-slate-700/30 px-4 py-2.5 lato-bold text-slate-500 dark:text-slate-400 text-[10px] uppercase tracking-wider whitespace-nowrap">
                                        Média Dept.
                                    </td>
                                    @foreach ($mapa['competencias'] as $comp)
                                    @php
                                        $vals = array_filter(array_map(fn($f) => $f['niveis'][$comp]['gap'] ?? null, $dept['funcionarios']), fn($v) => $v !== null);
                                        $mediaGap = count($vals) > 0 ? round(array_sum($vals) / count($vals), 1) : null;
                                        $mediaAtual = count($vals) > 0
                                            ? round(array_sum(array_map(fn($f) => $f['niveis'][$comp]['atual'] ?? 0, $dept['funcionarios'])) / count($dept['funcionarios']), 1)
                                            : null;
                                    @endphp
                                    <td class="px-2 py-2.5 text-center">
                                        @if ($mediaGap !== null)
                                        @php $mg = min((int)ceil($mediaGap), 3); @endphp
                                        <span class="inline-block px-2 py-0.5 rounded lato-bold text-[10px]
                                            {{ $mg === 0 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                             : ($mg === 1 ? 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-600'
                                             : ($mg === 2 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'
                                             : 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400')) }}">
                                            {{ $mediaGap }}
                                        </span>
                                        @else
                                        <span class="text-slate-300 text-[10px]">—</span>
                                        @endif
                                    </td>
                                    @endforeach
                                    <td></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>

            {{-- ── PAINEL LATERAL: TOP GAPS + AÇÃO DPI ─────────────── --}}
            <div class="w-72 shrink-0 space-y-4">

                {{-- Top gaps --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl overflow-hidden">
                    <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700 flex items-center gap-2">
                        <x-lucide-trending-down class="w-4 h-4 text-rose-500" />
                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">Maiores Gaps</p>
                    </div>
                    <div class="divide-y divide-slate-50 dark:divide-slate-700/50">
                        @forelse ($mapa['topGaps'] as $i => $tg)
                        <div class="px-4 py-3">
                            <div class="flex items-start justify-between gap-2 mb-1.5">
                                <div class="flex items-center gap-2 min-w-0">
                                    <span class="w-5 h-5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 text-[10px] lato-bold flex items-center justify-center shrink-0">{{ $i+1 }}</span>
                                    <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate" title="{{ $tg['name'] }}">{{ Str::limit($tg['name'], 22) }}</p>
                                </div>
                                <span class="shrink-0 text-[10px] lato-bold px-1.5 py-0.5 rounded
                                    {{ $tg['media'] >= 3 ? 'bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400'
                                     : ($tg['media'] >= 2 ? 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400'
                                     : 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-600') }}">
                                    −{{ $tg['media'] }}
                                </span>
                            </div>
                            {{-- Barra de gap --}}
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5">
                                <div class="h-1.5 rounded-full {{ $tg['media'] >= 3 ? 'bg-rose-500' : ($tg['media'] >= 2 ? 'bg-orange-400' : 'bg-yellow-400') }}"
                                     style="width: {{ min(100, $tg['media'] / 4 * 100) }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">{{ $tg['afetados'] }} funcionário(s) afetado(s)</p>
                        </div>
                        @empty
                        <div class="px-4 py-6 text-center">
                            <x-lucide-check-circle class="w-6 h-6 text-emerald-400 mx-auto mb-2" />
                            <p class="text-xs text-slate-400">Nenhum gap identificado</p>
                        </div>
                        @endforelse
                    </div>
                </div>

                {{-- Ação recomendada --}}
                <div class="bg-gradient-to-br from-cyan-50 to-blue-50 dark:from-cyan-900/20 dark:to-blue-900/20 border border-cyan-200 dark:border-cyan-700/40 rounded-2xl p-4 space-y-3">
                    <div class="flex items-center gap-2">
                        <x-lucide-zap class="w-4 h-4 text-cyan-600 dark:text-cyan-400" />
                        <p class="text-sm lato-bold text-cyan-700 dark:text-cyan-300">Próximas Ações</p>
                    </div>
                    <p class="text-xs text-cyan-600 dark:text-cyan-400 leading-relaxed">
                        Direcione os gaps identificados para ações de desenvolvimento no DPI de cada funcionário.
                    </p>
                    <ul class="space-y-1.5">
                        @foreach (array_slice($mapa['topGaps'], 0, 3) as $tg)
                        <li class="flex items-center gap-2 text-xs text-cyan-700 dark:text-cyan-300">
                            <span class="w-1.5 h-1.5 rounded-full bg-cyan-400 shrink-0"></span>
                            {{ Str::limit($tg['name'], 28) }} ({{ $tg['afetados'] }} pessoas)
                        </li>
                        @endforeach
                    </ul>
                    <a href="{{ route('dpi') }}" target="_blank"
                       class="inline-flex items-center gap-1.5 text-xs lato-bold text-cyan-700 dark:text-cyan-300 hover:text-cyan-900 dark:hover:text-cyan-100 transition">
                        <x-lucide-external-link class="w-3.5 h-3.5" />
                        Abrir módulo DPI
                    </a>
                </div>

                {{-- Cobertura DPI --}}
                <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-4">
                    <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 mb-3 uppercase tracking-wider">Cobertura DPI</p>
                    <div class="flex items-end gap-3">
                        <div>
                            <p class="text-3xl lato-black text-slate-800 dark:text-white">{{ $mapa['kpis']['cobertura_pct'] }}<span class="text-lg text-slate-400">%</span></p>
                            <p class="text-xs text-slate-400 mt-0.5">dos funcionários com DPI ativo</p>
                        </div>
                        <div class="flex-1">
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-2.5">
                                <div class="h-2.5 rounded-full bg-gradient-to-r from-cyan-500 to-blue-500"
                                     style="width: {{ $mapa['kpis']['cobertura_pct'] }}%"></div>
                            </div>
                            <p class="text-[10px] text-slate-400 mt-1">{{ $mapa['kpis']['com_dpi'] }} / {{ $mapa['kpis']['total_funcionarios'] }} funcionários</p>
                        </div>
                    </div>
                    @if ($mapa['kpis']['cobertura_pct'] < 80)
                    <div class="mt-3 flex items-start gap-2 p-2.5 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 rounded-xl">
                        <x-lucide-alert-triangle class="w-3.5 h-3.5 text-amber-500 shrink-0 mt-0.5" />
                        <p class="text-[10px] text-amber-600 dark:text-amber-400">Cobertura baixa. Incentive a criação de DPIs para visibilidade completa.</p>
                    </div>
                    @endif
                </div>

            </div>
        </div>
        @endif

    </div>
    @endif

    </div>{{-- fim área de conteúdo --}}
</div>{{-- fim portal --}}

{{-- ═══════════════════════════════════════════════════════════════════════
     MODAIS GLOBAIS (currículos + vagas) — fixed position, sempre no DOM
═══════════════════════════════════════════════════════════════════════ --}}

{{-- ── Modal: Upload de currículo ──────────────────────────────────── --}}

{{-- Drawer: detalhe da vaga --}}
@if ($vagaDrawer && $this->vagaDrawerData)
@php
    $dv  = $this->vagaDrawerData;
    $dvBg = portalVagaBg($dv->titulo);
    $dvIni = strtoupper(substr($dv->titulo, 0, 1));
@endphp
<div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('vagaDrawer', false)"></div>
<div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] lg:w-[540px] bg-white dark:bg-slate-800 shadow-2xl flex flex-col">

    {{-- Header --}}
    <div class="shrink-0 border-b border-slate-200 dark:border-slate-700 px-6 py-4">
        <div class="flex items-start gap-3">
<div class="w-12 h-12 rounded-xl {{ $dvBg }} flex items-center justify-center text-white lato-black text-lg shrink-0">
    {{ $dvIni }}
</div>
<div class="flex-1 min-w-0">
    <h2 class="text-sm lato-black text-slate-800 dark:text-white leading-snug">{{ $dv->titulo }}</h2>
    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-0.5">{{ $dv->cargo }}</p>
    <div class="flex items-center gap-2 mt-2 flex-wrap">
        <span class="px-2 py-0.5 rounded-full text-[10px] lato-bold {{ $vagaStatusCls[$dv->status] ?? 'bg-slate-100 text-slate-600' }}">
{{ $vagaStatusLbl[$dv->status] ?? $dv->status }}
        </span>
        <span class="px-2 py-0.5 rounded-full text-[10px] lato-regular bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300">
{{ $vagaModalLbl[$dv->modalidade] ?? $dv->modalidade }}
        </span>
        @if ($dv->cidade || $dv->estado)
        <span class="flex items-center gap-1 text-[10px] text-slate-500 lato-regular">
<x-lucide-map-pin class="w-3 h-3" />
{{ trim(($dv->cidade ?? '').'/'.($dv->estado ?? ''), '/') }}
        </span>
        @endif
    </div>
</div>
<button wire:click="$set('vagaDrawer', false)" type="button"
        class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
    <x-lucide-x class="w-5 h-5" />
</button>
        </div>
    </div>

    {{-- Body (scrollável) --}}
    <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

        {{-- Números rápidos --}}
        <div class="grid grid-cols-3 gap-3">
<div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
    <p class="text-xl lato-black text-indigo-600 dark:text-indigo-400">{{ $dv->candidaturas_count }}</p>
    <p class="text-[10px] text-slate-500 dark:text-slate-400 lato-regular mt-0.5">Candidatos</p>
</div>
<div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
    <p class="text-xl lato-black text-slate-800 dark:text-white">{{ $dv->vagas_disponiveis ?? '—' }}</p>
    <p class="text-[10px] text-slate-500 dark:text-slate-400 lato-regular mt-0.5">Vagas</p>
</div>
<div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3 text-center">
    <p class="text-xl lato-black text-slate-800 dark:text-white">{{ $dv->sla_dias ?? '—' }}</p>
    <p class="text-[10px] text-slate-500 dark:text-slate-400 lato-regular mt-0.5">SLA (dias)</p>
</div>
        </div>

        {{-- Remuneração --}}
        @if ($dv->salario_min || $dv->salario_max)
        <div class="flex items-center gap-2 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 rounded-xl border border-emerald-100 dark:border-emerald-800">
<x-lucide-dollar-sign class="w-4 h-4 text-emerald-500 shrink-0" />
<span class="text-sm lato-bold text-emerald-700 dark:text-emerald-400">
    @if ($dv->salario_min && $dv->salario_max)
        R$ {{ number_format($dv->salario_min, 0, ',', '.') }} – {{ number_format($dv->salario_max, 0, ',', '.') }}
    @elseif ($dv->salario_min)
        A partir de R$ {{ number_format($dv->salario_min, 0, ',', '.') }}
    @else
        Até R$ {{ number_format($dv->salario_max, 0, ',', '.') }}
    @endif
</span>
        </div>
        @endif

        {{-- Descrição --}}
        @if ($dv->descricao)
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-2">Descrição</h4>
<p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dv->descricao }}</p>
        </div>
        @endif

        {{-- Requisitos --}}
        @if ($dv->requisitos)
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-2">Requisitos</h4>
<p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dv->requisitos }}</p>
        </div>
        @endif

        {{-- Competências --}}
        @if ($dv->competencias)
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-2">Competências desejáveis</h4>
<p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dv->competencias }}</p>
        </div>
        @endif

        {{-- Benefícios --}}
        @if ($dv->beneficios)
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-2">Benefícios</h4>
<p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dv->beneficios }}</p>
        </div>
        @endif

        {{-- Etapas do processo --}}
        @if ($dv->etapas && $dv->etapas->count())
        <div>
            <div class="flex items-center justify-between mb-3">
                <h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest">Etapas do processo</h4>
                <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                    {{ $dv->etapas->count() }} etapas
                </span>
            </div>
            @php $etapasOrdenadas = $dv->etapas->sortBy('ordem')->values(); @endphp
            <div class="space-y-2">
                @foreach ($etapasOrdenadas as $idx => $etapa)
                @php
                    $etapaColors = [
                        'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',
                        'bg-blue-50 text-blue-500 dark:bg-blue-900/30 dark:text-blue-400',
                        'bg-indigo-50 text-indigo-500 dark:bg-indigo-900/30 dark:text-indigo-400',
                        'bg-violet-50 text-violet-500 dark:bg-violet-900/30 dark:text-violet-400',
                        'bg-purple-50 text-purple-500 dark:bg-purple-900/30 dark:text-purple-400',
                        'bg-amber-50 text-amber-500 dark:bg-amber-900/30 dark:text-amber-400',
                        'bg-green-50 text-green-600 dark:bg-green-900/30 dark:text-green-400',
                        'bg-teal-50 text-teal-500 dark:bg-teal-900/30 dark:text-teal-400',
                    ];
                    $etapaDot = [
                        'bg-slate-400','bg-blue-500','bg-indigo-500','bg-violet-500',
                        'bg-purple-500','bg-amber-500','bg-green-500','bg-teal-500',
                    ];
                    $ci = $idx % count($etapaColors);
                @endphp
                <div class="flex items-center gap-3 px-3 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-slate-100 dark:border-slate-700">
                    <span class="w-6 h-6 rounded-lg {{ $etapaColors[$ci] }} flex items-center justify-center text-[10px] lato-black shrink-0">
                        {{ $idx + 1 }}
                    </span>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $etapa->nome }}</p>
                        @if ($etapa->descricao)
                        <p class="text-[10px] text-slate-400 lato-regular truncate mt-0.5">{{ $etapa->descricao }}</p>
                        @endif
                    </div>
                    @if (!$loop->last)
                    <x-lucide-chevron-right class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0" />
                    @else
                    <x-lucide-flag class="w-3.5 h-3.5 text-slate-300 dark:text-slate-600 shrink-0" />
                    @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Candidatos recentes --}}
        @if ($dv->candidaturas && $dv->candidaturas->count())
        <div>
<h4 class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3">
    Candidatos recentes
    <span class="ml-1 px-1.5 py-0.5 rounded-full bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-400 normal-case text-[9px]">{{ $dv->candidaturas_count }}</span>
</h4>
<div class="space-y-2">
    @foreach ($dv->candidaturas->take(6) as $cand)
    @php $cCurr = $cand->curriculo; @endphp
    @if ($cCurr)
    @php
        $cIni = collect(explode(' ', $cCurr->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
        $candColors = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-pink-500','bg-emerald-500'];
        $cBg = $candColors[abs(crc32($cCurr->nome)) % count($candColors)];
    @endphp
    <div class="flex items-center gap-3 py-2 px-3 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700/50 transition">
        <div class="w-8 h-8 rounded-full {{ $cBg }} flex items-center justify-center text-white text-xs lato-bold shrink-0">{{ $cIni }}</div>
        <div class="flex-1 min-w-0">
<p class="text-xs lato-bold text-slate-800 dark:text-white truncate">{{ $cCurr->nome }}</p>
<p class="text-[10px] text-slate-500 lato-regular truncate">{{ $cand->etapa->nome ?? 'Sem etapa' }}</p>
        </div>
        @php
$stColors = ['em_analise'=>'bg-blue-100 text-blue-700','aprovado'=>'bg-green-100 text-green-700','reprovado'=>'bg-red-100 text-red-600','em_espera'=>'bg-amber-100 text-amber-700'];
$stLabels = ['em_analise'=>'Em análise','aprovado'=>'Aprovado','reprovado'=>'Reprovado','em_espera'=>'Em espera'];
        @endphp
        <span class="shrink-0 px-1.5 py-0.5 rounded-full text-[9px] lato-bold {{ $stColors[$cand->status] ?? 'bg-slate-100 text-slate-600' }}">
{{ $stLabels[$cand->status] ?? $cand->status }}
        </span>
    </div>
    @endif
    @endforeach
</div>
        </div>
        @endif

        {{-- Datas --}}
        <div class="grid grid-cols-2 gap-3 text-xs text-slate-500 dark:text-slate-400 lato-regular">
@if ($dv->data_encerramento)
<div class="flex items-center gap-1.5">
    <x-lucide-calendar class="w-3.5 h-3.5 text-slate-400" />
    Encerra: {{ \Carbon\Carbon::parse($dv->data_encerramento)->format('d/m/Y') }}
</div>
@endif
@if ($dv->nota_minima)
<div class="flex items-center gap-1.5">
    <x-lucide-check-circle class="w-3.5 h-3.5 text-slate-400" />
    Nota mínima: {{ $dv->nota_minima }}%
</div>
@endif
@if ($dv->creator)
<div class="flex items-center gap-1.5 col-span-2">
    <x-lucide-user class="w-3.5 h-3.5 text-slate-400" />
    Criado por {{ $dv->creator->name }}
</div>
@endif
        </div>

    </div>

    {{-- Footer --}}
    <div class="shrink-0 border-t border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center gap-3">
        <button wire:click="openVagaEdit({{ $dv->id }})" @click="$wire.set('vagaDrawer', false)" type="button"
    class="cursor-pointer flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs lato-bold border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
<x-lucide-pencil class="w-4 h-4" /> Editar vaga
        </button>
        <button wire:click="setAba('pipeline')" @click="$wire.set('vagaDrawer', false)" type="button"
    class="cursor-pointer flex-1 flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl text-xs lato-bold bg-indigo-600 hover:bg-indigo-700 text-white transition">
<x-lucide-git-branch class="w-4 h-4" /> Ver Pipeline
        </button>
    </div>

</div>
@endif

@if ($uploadModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('uploadModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-md p-6"
         x-data="{ dragging: false }">
        <div class="flex items-center justify-between mb-5">
            <h2 class="text-base lato-black text-slate-800 dark:text-white">Enviar Currículo</h2>
            <button wire:click="$set('uploadModal', false)" type="button" class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition"><x-lucide-x class="w-5 h-5" /></button>
        </div>
        <label for="currUpload" class="block w-full cursor-pointer"
               @dragover.prevent="dragging=true" @dragleave.prevent="dragging=false" @drop.prevent="dragging=false">
            <div :class="dragging ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-slate-300 dark:border-slate-600 bg-slate-50 dark:bg-slate-900'"
                 class="border-2 border-dashed rounded-2xl p-10 flex flex-col items-center gap-3 transition">
                <div class="w-14 h-14 rounded-2xl bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                    <x-lucide-file-up class="w-7 h-7 text-blue-500" />
                </div>
                <div class="text-center">
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">Arraste o arquivo ou clique para selecionar</p>
                    <p class="text-xs text-slate-400 lato-regular mt-1">PDF, DOC ou DOCX — máx. 5 MB</p>
                </div>
                <input id="currUpload" type="file" wire:model="arquivo" accept=".pdf,.doc,.docx" class="hidden" />
            </div>
        </label>
        @if ($arquivo)
        <div class="mt-3 flex items-center gap-2 p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800">
            <x-lucide-file-check class="w-4 h-4 text-green-500 shrink-0" />
            <span class="text-xs text-green-700 dark:text-green-300 lato-regular truncate">
                {{ is_object($arquivo) ? $arquivo->getClientOriginalName() : 'Arquivo selecionado' }}
            </span>
        </div>
        @endif
        @error('arquivo') <p class="mt-2 text-xs text-red-500 lato-regular">{{ $message }}</p> @enderror
        <div class="flex justify-end gap-3 mt-5">
            <button wire:click="$set('uploadModal', false)" type="button"
                    class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
            <button wire:click="uploadCurriculo" type="button" wire:loading.attr="disabled" wire:target="uploadCurriculo"
                    class="cursor-pointer relative inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                <span wire:loading.remove wire:target="uploadCurriculo"><x-lucide-sparkles class="w-4 h-4 inline" /> Enviar e Analisar</span>
                <span wire:loading wire:target="uploadCurriculo" class="flex items-center gap-2">
                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                    </svg>
                    Analisando...
                </span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Drawer: detalhe do currículo ───────────────────────────────── --}}
@if ($currDrawer && $this->curriculoDrawer)
@php
    $dc = $this->curriculoDrawer;
    $dcIn = collect(explode(' ', $dc->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
    $dcColors = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500','bg-rose-500','bg-amber-500'];
    $dcBg = $dcColors[abs(crc32($dc->nome)) % count($dcColors)];
    [$dcSCls, $dcSLbl] = $statusCurrMap[$dc->status] ?? $statusCurrMap['inativo'];
@endphp
<div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('currDrawer', false)"></div>
<div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] lg:w-[560px] bg-white dark:bg-slate-800 shadow-2xl overflow-y-auto">
    <div class="sticky top-0 z-10 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl {{ $dcBg }} flex items-center justify-center text-white lato-black text-base shrink-0">{{ $dcIn }}</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base lato-black text-slate-800 dark:text-white">{{ $dc->nome }}</h2>
                    <span class="px-2 py-0.5 rounded-full text-[11px] lato-bold {{ $dcSCls }}">{{ $dcSLbl }}</span>
                </div>
                <div class="flex flex-wrap gap-3 mt-1 text-xs text-slate-500 dark:text-slate-400 lato-regular">
                    @if ($dc->email) <span><x-lucide-mail class="w-3 h-3 inline" /> {{ $dc->email }}</span> @endif
                    @if ($dc->telefone) <span><x-lucide-phone class="w-3 h-3 inline" /> {{ $dc->telefone }}</span> @endif
                    @if ($dc->cidade || $dc->estado) <span><x-lucide-map-pin class="w-3 h-3 inline" /> {{ trim(($dc->cidade ?? '').', '.($dc->estado ?? ''), ', ') }}</span> @endif
                </div>
            </div>
            <button wire:click="$set('currDrawer', false)" type="button"
                    class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                <x-lucide-x class="w-5 h-5" />
            </button>
        </div>
        <div class="flex items-center gap-2 mt-3">
            <button wire:click="openCurrEdit({{ $dc->id }})" type="button"
                    class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
            </button>
            <button wire:click="toggleFavorito({{ $dc->id }})" type="button"
                    class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-xl {{ $dc->status === 'favorito' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }} text-xs lato-bold transition">
                <x-lucide-star class="w-3.5 h-3.5" /> {{ $dc->status === 'favorito' ? 'Favoritado' : 'Favoritar' }}
            </button>
            @if ($dc->arquivo_path)
            <a href="{{ Storage::url($dc->arquivo_path) }}" target="_blank"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs lato-bold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                <x-lucide-download class="w-3.5 h-3.5" /> Baixar arquivo
            </a>
            @endif
        </div>
    </div>
    <div class="px-6 py-4 space-y-6">
        @if ($dc->resumo_profissional)
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Resumo Profissional</h3>
            <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed">{{ $dc->resumo_profissional }}</p>
        </div>
        @endif
        @if ($dc->experiencias && $dc->experiencias->count())
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Experiências</h3>
            <div class="relative pl-5 border-l-2 border-slate-200 dark:border-slate-700 space-y-4">
                @foreach ($dc->experiencias->sortByDesc('data_inicio') as $exp)
                <div class="relative">
                    <div class="absolute -left-[22px] top-1 w-3.5 h-3.5 rounded-full bg-blue-500 border-2 border-white dark:border-slate-800"></div>
                    <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $exp->cargo }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $exp->empresa }}</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">
                        {{ \Carbon\Carbon::parse($exp->data_inicio)->format('M/Y') }}
                        — {{ $exp->data_fim ? \Carbon\Carbon::parse($exp->data_fim)->format('M/Y') : 'Atual' }}
                    </p>
                    @if ($exp->descricao) <p class="text-xs text-slate-600 dark:text-slate-400 lato-regular mt-1 leading-relaxed">{{ $exp->descricao }}</p> @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if ($dc->formacoes && $dc->formacoes->count())
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Formação Acadêmica</h3>
            <div class="space-y-3">
                @foreach ($dc->formacoes->sortByDesc('data_conclusao') as $form)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-graduation-cap class="w-4 h-4 text-indigo-500" />
                    </div>
                    <div>
                        <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $form->curso }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $form->instituicao }}</p>
                        <p class="text-xs text-slate-400 lato-regular">{{ $escLabels[$form->nivel] ?? ucfirst($form->nivel ?? '') }}@if ($form->data_conclusao) · {{ \Carbon\Carbon::parse($form->data_conclusao)->format('Y') }}@endif</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if ($dc->habilidades && $dc->habilidades->count())
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Habilidades</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($dc->habilidades as $hab)
                <span class="px-2.5 py-1 rounded-full text-xs lato-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                    {{ $hab->nome }}@if ($hab->nivel) <span class="opacity-60 font-normal">({{ ucfirst($hab->nivel) }})</span>@endif
                </span>
                @endforeach
            </div>
        </div>
        @endif
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Tags</h3>
            <div class="flex flex-wrap gap-2 mb-3">
                @forelse ($dc->tags ?? [] as $tag)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-xs lato-bold">
                    {{ $tag->tag }}
                    <button wire:click="removeTag({{ $tag->id }})" type="button" class="cursor-pointer ml-0.5 text-indigo-400 hover:text-red-500 transition">
                        <x-lucide-x class="w-3 h-3" />
                    </button>
                </span>
                @empty
                <p class="text-xs text-slate-400 lato-regular">Nenhuma tag adicionada.</p>
                @endforelse
            </div>
            <div class="flex gap-2">
                <input type="text" wire:model="newTag" placeholder="Nova tag..."
                       wire:keydown.enter="addTag({{ $dc->id }})"
                       class="flex-1 px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                <button wire:click="addTag({{ $dc->id }})" type="button"
                        class="cursor-pointer px-3 py-2 rounded-xl bg-indigo-500 text-white text-sm lato-bold hover:bg-indigo-600 transition">
                    <x-lucide-plus class="w-4 h-4" />
                </button>
            </div>
        </div>
        @if ($dc->candidaturas && $dc->candidaturas->count())
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Candidaturas</h3>
            <div class="space-y-2">
                @foreach ($dc->candidaturas as $cand)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                    <div>
                        <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $cand->vaga?->titulo ?? '—' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $cand->etapa?->nome ?? '—' }}</p>
                    </div>
                    <span class="text-xs text-slate-400 lato-regular">{{ $cand->created_at?->format('d/m/Y') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        {{-- Conteúdo extraído do PDF (OCR) --}}
        @if ($dc->texto_ocr || $dc->arquivo_path)
        <div x-data="{ expandido: false }">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <x-lucide-scan-text class="w-3.5 h-3.5" />
                    Conteúdo extraído do PDF
                </h3>
                <div class="flex items-center gap-2">
                    @if ($dc->ocr_processado_at)
                        <span class="text-[10px] text-slate-400 lato-regular">
                            Processado {{ $dc->ocr_processado_at->format('d/m/Y H:i') }}
                        </span>
                    @endif
                    <button wire:click="reprocessarOcr({{ $dc->id }})" type="button"
                            title="Reprocessar OCR"
                            class="cursor-pointer p-1 rounded-lg text-slate-400 hover:text-violet-500 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition">
                        <x-lucide-refresh-cw class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            @if ($dc->texto_ocr)
                {{-- Preview compacto com expandir --}}
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 overflow-hidden">
                    <div class="relative">
                        <pre :class="expandido ? 'max-h-96 overflow-y-auto' : 'max-h-40 overflow-hidden'"
                             class="text-xs text-slate-600 dark:text-slate-400 lato-regular leading-relaxed p-4 whitespace-pre-wrap transition-all duration-300">{{ $dc->texto_ocr }}</pre>
                        {{-- Fade overlay quando compacto --}}
                        <div x-show="!expandido"
                             class="absolute bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-slate-50 dark:from-slate-900 to-transparent pointer-events-none"></div>
                    </div>
                    <button @click="expandido = !expandido" type="button"
                            class="cursor-pointer w-full flex items-center justify-center gap-1.5 py-2 border-t border-slate-200 dark:border-slate-700 text-xs text-violet-600 dark:text-violet-400 lato-bold hover:bg-violet-50 dark:hover:bg-violet-900/20 transition">
                        <span class="transition-transform duration-200" :class="expandido ? 'rotate-180' : ''"><x-lucide-chevron-down class="w-3.5 h-3.5" /></span>
                        <span x-text="expandido ? 'Mostrar menos' : 'Ver texto completo'"></span>
                    </button>
                </div>

                {{-- Palavras-chave detectadas automaticamente --}}
                @php
                    $techs = ['Laravel','PHP','Python','JavaScript','TypeScript','React','Vue','Angular','Node','Java','C#','C\+\+','Docker','AWS','Azure','GCP','SQL','PostgreSQL','MySQL','MongoDB','Redis','Git','Linux','Scrum','Agile','inglês','espanhol','inglês fluente','bilíngue'];
                    $encontradas = array_filter($techs, fn($t) => preg_match('/\b'.$t.'\b/i', $dc->texto_ocr));
                @endphp
                @if ($encontradas)
                <div class="mt-3">
                    <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wider mb-1.5">Detectado no currículo</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($encontradas as $tech)
                        <span class="px-2 py-0.5 rounded-full bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 text-[11px] lato-bold">
                            {{ $tech }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

            @else
                <div class="flex items-center gap-2 px-4 py-3 rounded-xl border border-dashed border-slate-300 dark:border-slate-600 text-slate-400 text-sm">
                    <x-lucide-clock class="w-4 h-4 shrink-0" />
                    <span class="lato-regular text-xs">
                        @if ($dc->arquivo_path)
                            Texto ainda sendo extraído. Clique em <x-lucide-refresh-cw class="w-3 h-3 inline" /> para reprocessar.
                        @else
                            Nenhum arquivo anexado a este currículo.
                        @endif
                    </span>
                </div>
            @endif
        </div>
        @endif

        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Notas Internas</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 lato-regular leading-relaxed whitespace-pre-line">{{ $dc->notas_internas ?? 'Nenhuma nota registrada.' }}</p>
        </div>
    </div>
</div>
@endif

{{-- ── Modal: Editar currículo ─────────────────────────────────────── --}}
@if ($currEditModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('currEditModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center justify-between z-10">
            <h2 class="text-base lato-black text-slate-800 dark:text-white">Editar Currículo</h2>
            <button wire:click="$set('currEditModal', false)" type="button" class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition"><x-lucide-x class="w-5 h-5" /></button>
        </div>
        <div class="p-3 md:p-6 space-y-3 md:space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Nome completo</label>
                    <input type="text" wire:model="editNome" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                    @error('editNome') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Email</label>
                    <input type="email" wire:model="editEmail" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                    @error('editEmail') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Telefone</label>
                    <input type="text" wire:model="editTelefone" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Cidade</label>
                    <input type="text" wire:model="editCidade" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Estado (UF)</label>
                    <input type="text" wire:model="editEstado" maxlength="2" placeholder="SP" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Área de interesse</label>
                    <input type="text" wire:model="editArea" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Escolaridade</label>
                    <select wire:model="editEscolaridade" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular">
                        <option value="">Selecione</option>
                        @foreach ($escLabels as $val => $lbl) <option value="{{ $val }}">{{ $lbl }}</option> @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Pretensão salarial (R$)</label>
                    <input type="number" wire:model="editPretensao" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Status</label>
                    <select wire:model="editStatus" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular">
                        <option value="ativo">Ativo</option>
                        <option value="favorito">Favorito</option>
                        <option value="banco_talentos">Banco de Talentos</option>
                        <option value="inativo">Inativo</option>
                    </select>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Resumo profissional</label>
                    <textarea wire:model="editResumo" rows="3" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Notas internas</label>
                    <textarea wire:model="editNotas" rows="3" class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                </div>
            </div>
        </div>
        <div class="sticky bottom-0 bg-white dark:bg-slate-800 border-t border-slate-200 dark:border-slate-700 px-6 py-4 flex justify-end gap-3">
            <button wire:click="$set('currEditModal', false)" type="button"
                    class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
            <button wire:click="saveCurrEdit" type="button" wire:loading.attr="disabled" wire:target="saveCurrEdit"
                    class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                <span wire:loading.remove wire:target="saveCurrEdit"><x-lucide-save class="w-4 h-4 inline" /> Salvar</span>
                <span wire:loading wire:target="saveCurrEdit">Salvando...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Modal: Excluir currículo ────────────────────────────────────── --}}
@if ($currDeleteModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm" wire:click.self="$set('currDeleteModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-sm p-6">
        <div class="flex flex-col items-center text-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center"><x-lucide-trash-2 class="w-7 h-7 text-red-500" /></div>
            <div>
                <h3 class="text-base lato-black text-slate-800 dark:text-white">Excluir currículo?</h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-1">Esta ação não pode ser desfeita.</p>
            </div>
        </div>
        <div class="flex gap-3 mt-6">
            <button wire:click="$set('currDeleteModal', false)" type="button"
                    class="cursor-pointer flex-1 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
            <button wire:click="deleteCurriculo" type="button" wire:loading.attr="disabled" wire:target="deleteCurriculo"
                    class="cursor-pointer flex-1 px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm lato-bold hover:bg-red-600 transition disabled:opacity-60">
                <span wire:loading.remove wire:target="deleteCurriculo">Confirmar</span>
                <span wire:loading wire:target="deleteCurriculo">Excluindo...</span>
            </button>
        </div>
    </div>
</div>
@endif

{{-- ── Modal: Criar/Editar Vaga ────────────────────────────────────── --}}
@if ($vagaModal2)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('vagaModal2', '')">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-3xl max-h-[90vh] flex flex-col" @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 shrink-0 rounded-t-2xl">
            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                    <x-lucide-briefcase class="w-3.5 h-3.5 text-white" />
                </span>
                {{ $vagaModal2 === 'edit' ? 'Editar Vaga' : 'Nova Vaga' }}
            </h3>
            <button wire:click="$set('vagaModal2', '')" type="button"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        <div class="flex-1 overflow-y-auto p-5 space-y-6">

            {{-- ── Seção 1: Identificação ─────────────────────────── --}}
            <div>
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <x-lucide-file-text class="w-3.5 h-3.5" /> Identificação
                </p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Título da vaga <span class="text-red-400">*</span></label>
                        <input wire:model="vTitulo" type="text" placeholder="Ex: Desenvolvedor Full Stack Sênior"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                        @error('vTitulo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Cargo <span class="text-red-400">*</span></label>
                        <input wire:model="vCargo" type="text" placeholder="Ex: Analista de TI Pleno"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                        @error('vCargo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Modalidade</label>
                        <div class="flex gap-2">
                            @foreach (['presencial'=>['Presencial','building-2'],'remoto'=>['Remoto','wifi'],'hibrido'=>['Híbrido','split']] as $mv=>[$ml,$mi])
                            <label class="flex-1 cursor-pointer">
                                <input type="radio" wire:model="vModalidade" value="{{ $mv }}" class="sr-only peer" />
                                <div class="flex flex-col items-center gap-1 p-2.5 rounded-xl border text-center transition
                                            peer-checked:border-indigo-400 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300
                                            border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600">
                                    <x-dynamic-component :component="'lucide-'.$mi" class="w-4 h-4" />
                                    <span class="text-[10px] lato-bold leading-none">{{ $ml }}</span>
                                </div>
                            </label>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

            {{-- ── Seção 2: Localização & Remuneração ─────────────── --}}
            <div x-data="vagaGeo($wire)">
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <x-lucide-map-pin class="w-3.5 h-3.5" /> Localização & Remuneração
                    </p>
                    <button @click="getLocation" type="button"
                            :disabled="geoLoading"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold transition
                                   border border-indigo-200 dark:border-indigo-700
                                   text-indigo-600 dark:text-indigo-300
                                   bg-indigo-50 dark:bg-indigo-900/20
                                   hover:bg-indigo-100 dark:hover:bg-indigo-900/40
                                   disabled:opacity-50 disabled:cursor-not-allowed">
                        <template x-if="!geoLoading">
                            <x-lucide-locate class="w-3.5 h-3.5" />
                        </template>
                        <template x-if="geoLoading">
                            <svg class="animate-spin w-3.5 h-3.5" viewBox="0 0 24 24" fill="none">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8z"/>
                            </svg>
                        </template>
                        <span x-text="geoLoading ? 'Detectando...' : 'Usar minha localização'"></span>
                    </button>
                </div>

                {{-- Erro: permissão bloqueada --}}
                <template x-if="geoError === 'blocked'">
                    <div class="flex items-start gap-2.5 px-3 py-3 mb-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700">
                        <x-lucide-lock class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                        <div class="flex-1 min-w-0">
                            <p class="text-xs lato-bold text-amber-700 dark:text-amber-400 mb-1">Localização bloqueada pelo browser</p>
                            <p class="text-[11px] text-amber-600 dark:text-amber-500 lato-regular leading-relaxed" x-html="geoHint"></p>
                        </div>
                    </div>
                </template>

                {{-- Erro: falha de rede --}}
                <template x-if="geoError === 'fetch_error'">
                    <div class="flex items-center gap-2 px-3 py-2 mb-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <x-lucide-wifi-off class="w-3.5 h-3.5 text-red-500 shrink-0" />
                        <p class="text-xs text-red-600 dark:text-red-400 lato-regular">Não foi possível obter o endereço. Verifique sua conexão.</p>
                    </div>
                </template>

                {{-- Erro: timeout --}}
                <template x-if="geoError === 'timeout'">
                    <div class="flex items-center gap-2 px-3 py-2 mb-3 rounded-xl bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800">
                        <x-lucide-clock class="w-3.5 h-3.5 text-red-500 shrink-0" />
                        <p class="text-xs text-red-600 dark:text-red-400 lato-regular">Tempo esgotado. Tente novamente.</p>
                    </div>
                </template>

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div class="col-span-2 sm:col-span-1">
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Cidade</label>
                        <input wire:model="vCidade" type="text" placeholder="São Paulo"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">UF</label>
                        <input wire:model="vEstado" type="text" maxlength="2" placeholder="SP"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                      lato-regular placeholder-slate-400 transition uppercase" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Sal. mínimo (R$)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 lato-bold pointer-events-none">R$</span>
                            <input wire:model="vSalMin" type="number" step="100" placeholder="0"
                                   class="w-full pl-8 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Sal. máximo (R$)</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-xs text-slate-400 lato-bold pointer-events-none">R$</span>
                            <input wire:model="vSalMax" type="number" step="100" placeholder="0"
                                   class="w-full pl-8 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

            {{-- ── Seção 3: Descrição ──────────────────────────────── --}}
            <div>
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <x-lucide-align-left class="w-3.5 h-3.5" /> Descrição da Vaga
                </p>
                <div class="space-y-4">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição geral</label>
                        <textarea wire:model="vDescricao" rows="3" placeholder="Descreva as responsabilidades e o contexto da vaga..."
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                         bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                         focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                         lato-regular placeholder-slate-400 resize-none transition"></textarea>
                    </div>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Requisitos</label>
                            <textarea wire:model="vRequisitos" rows="3" placeholder="Experiências e conhecimentos necessários..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                             bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                             focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                             lato-regular placeholder-slate-400 resize-none transition"></textarea>
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Competências desejáveis</label>
                            <textarea wire:model="vCompetencias" rows="3" placeholder="Soft skills e diferenciais valorizados..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                             bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                             focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                             lato-regular placeholder-slate-400 resize-none transition"></textarea>
                        </div>
                        <div class="sm:col-span-2">
                            <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Benefícios</label>
                            <textarea wire:model="vBeneficios" rows="2" placeholder="VR, VT, plano de saúde, home office..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                             bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                             focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                             lato-regular placeholder-slate-400 resize-none transition"></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

            {{-- ── Seção 4: Configurações ──────────────────────────── --}}
            <div>
                <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest mb-3 flex items-center gap-2">
                    <x-lucide-settings-2 class="w-3.5 h-3.5" /> Configurações
                </p>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Vagas</label>
                        <input wire:model="vQtd" type="number" min="1" placeholder="1"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">SLA (dias)</label>
                        <input wire:model="vSla" type="number" min="1" placeholder="30"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nota mínima (%)</label>
                        <input wire:model="vNota" type="number" min="0" max="100" placeholder="70"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Encerramento</label>
                        <input wire:model="vDataEnc" type="date"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular transition" />
                    </div>
                </div>

                {{-- Status da vaga --}}
                <div class="mt-4">
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Status da vaga</label>
                    <div class="flex flex-wrap gap-2">
                        @foreach (['rascunho'=>['Rascunho','bg-slate-400','slate'],'publicada'=>['Publicada','bg-green-400','green'],'pausada'=>['Pausada','bg-amber-400','amber'],'encerrada'=>['Encerrada','bg-red-400','red']] as $sv=>[$sl,$sd,$sc])
                        <label class="cursor-pointer">
                            <input type="radio" wire:model="vStatus" value="{{ $sv }}" class="sr-only peer" />
                            <div class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl border text-xs lato-bold transition
                                        peer-checked:border-indigo-400 peer-checked:bg-indigo-50 dark:peer-checked:bg-indigo-900/20 peer-checked:text-indigo-700 dark:peer-checked:text-indigo-300
                                        border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 hover:border-slate-300 dark:hover:border-slate-600">
                                <span class="w-2 h-2 rounded-full {{ $sd }}"></span> {{ $sl }}
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700/60"></div>

            {{-- ── Seção 5: Etapas do Processo Seletivo ───────────── --}}
            <div>
                <div class="flex items-center justify-between mb-3">
                    <p class="text-[10px] lato-bold text-slate-400 uppercase tracking-widest flex items-center gap-2">
                        <x-lucide-git-branch class="w-3.5 h-3.5" /> Etapas do Processo Seletivo
                    </p>
                    <button wire:click="addEtapa" type="button"
                            class="cursor-pointer flex items-center gap-1 text-xs lato-bold text-indigo-500 hover:text-indigo-700 transition">
                        <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar etapa
                    </button>
                </div>
                <div class="space-y-2">
                    @foreach ($etapas as $i => $etapa)
                    <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-900/50 border border-slate-200 dark:border-slate-700 group">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0 {{ $etapa['cor'] }}"></span>
                        <input wire:model="etapas.{{ $i }}.nome" type="text" placeholder="Nome da etapa"
                               class="flex-1 min-w-0 text-sm bg-transparent border-none outline-none text-slate-700 dark:text-slate-200 lato-regular placeholder-slate-400" />
                        <select wire:model="etapas.{{ $i }}.cor"
                                class="text-xs border border-slate-200 dark:border-slate-600 rounded-lg px-2 py-1
                                       bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300
                                       focus:outline-none focus:ring-1 focus:ring-indigo-400/30 shrink-0">
                            @foreach ($etapaCores as $cor)
                            <option value="{{ $cor }}">{{ ucfirst(str_replace(['bg-','-400','-500','-'], ['','','',' '], $cor)) }}</option>
                            @endforeach
                        </select>
                        <label class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400 cursor-pointer shrink-0">
                            <input type="checkbox" wire:model="etapas.{{ $i }}.is_aprovado" class="w-3 h-3 rounded accent-green-500 cursor-pointer" />
                            <span class="text-green-600 dark:text-green-400 lato-bold">Aprova</span>
                        </label>
                        <label class="flex items-center gap-1 text-[10px] text-slate-500 dark:text-slate-400 cursor-pointer shrink-0">
                            <input type="checkbox" wire:model="etapas.{{ $i }}.is_reprovado" class="w-3 h-3 rounded accent-red-500 cursor-pointer" />
                            <span class="text-red-500 dark:text-red-400 lato-bold">Reprova</span>
                        </label>
                        @if (count($etapas) > 1)
                        <button wire:click="removeEtapa({{ $i }})" type="button"
                                class="cursor-pointer p-1 rounded-lg text-slate-300 dark:text-slate-600 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition shrink-0 opacity-0 group-hover:opacity-100">
                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        </button>
                        @endif
                    </div>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between px-5 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50/70 dark:bg-slate-900/30 rounded-b-2xl shrink-0">
            <p class="text-xs text-slate-400 lato-regular">
                <span class="text-red-400">*</span> campos obrigatórios
            </p>
            <div class="flex items-center gap-2">
                <button wire:click="$set('vagaModal2', '')" type="button"
                        class="cursor-pointer px-4 py-2 rounded-xl border border-slate-200 dark:border-slate-700 text-sm text-slate-600 dark:text-slate-300 lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="saveVaga" type="button" wire:loading.attr="disabled" wire:target="saveVaga"
                        class="cursor-pointer inline-flex items-center gap-2 px-5 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                    <span wire:loading wire:target="saveVaga">
                        <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                    </span>

                    <span wire:loading.remove wire:target="saveVaga" class="flex items-center gap-1.5">
                        @if ($vagaModal2 === 'edit')
                            <x-lucide-square-pen class="w-4 h-4" />
                            Salvar Alterações
                        @else
                            <x-lucide-circle-check class="w-4 h-4" />
                            Confirmar
                        @endif
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
@endif


{{-- ── Modal: Excluir vaga ─────────────────────────────────────────── --}}
@if ($vagaDeleteModal)
<div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6 text-center">
        <div class="w-12 h-12 mx-auto mb-4 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
            <x-lucide-trash-2 class="w-6 h-6 text-red-500" />
        </div>
        <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 mb-1">Excluir vaga?</h3>
        <p class="text-xs text-slate-500 lato-regular mb-5">Todos os dados e candidaturas serão apagados permanentemente.</p>
        <div class="flex gap-2 justify-center">
            <button wire:click="$set('vagaDeleteModal', false)" type="button"
                    class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">Cancelar</button>
            <button wire:click="deleteVaga" type="button" wire:loading.attr="disabled" wire:target="deleteVaga"
                    class="cursor-pointer px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm lato-bold hover:bg-red-600 disabled:opacity-50 transition">Excluir</button>
        </div>
    </div>
</div>
@endif

    {{-- ── Modal: Teste enviado (Alpine — disparado via Livewire dispatch) ── --}}
<div x-data="{ show: false, link: '', nome: '', copied: false }"
     x-on:teste-enviado.window="
         link = $event.detail.link;
         nome = $event.detail.nome;
         copied = false;
         show = true;
     "
     x-show="show"
     x-cloak
     class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     x-on:click.self="show = false">

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-md"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-end px-6 pt-5">
            <button type="button" x-on:click="show = false"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 hover:text-slate-600 dark:hover:text-slate-300 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        {{-- Conteúdo --}}
        <div class="px-8 pt-2 pb-8 flex flex-col items-center text-center">

            {{-- Ícone --}}
            <div class="w-16 h-16 rounded-full bg-emerald-100 dark:bg-emerald-900/30 flex items-center justify-center mb-4">
                <x-lucide-send class="w-7 h-7 text-emerald-600 dark:text-emerald-400" />
            </div>

            <h2 class="text-lg lato-black text-slate-800 dark:text-white mb-1">Teste enviado!</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mb-6">
                Compartilhe o link abaixo com
                <strong class="text-slate-700 dark:text-slate-300" x-text="nome"></strong>.
                O link expira em <strong>7 dias</strong>.
            </p>

            {{-- Link copiável --}}
            <div class="w-full mb-4">
                <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                    <x-lucide-link class="w-4 h-4 text-slate-400 shrink-0" />
                    <span class="flex-1 text-xs text-slate-600 dark:text-slate-300 lato-regular truncate text-left" x-text="link"></span>
                    <button type="button"
                            x-on:click="
                                navigator.clipboard.writeText(link);
                                copied = true;
                                setTimeout(() => copied = false, 2500);
                            "
                            class="cursor-pointer shrink-0 flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs lato-bold transition"
                            :class="copied
                                ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400'
                                : 'bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400 hover:bg-indigo-100 dark:hover:bg-indigo-900/50'">
                        <x-lucide-copy class="w-3.5 h-3.5" x-show="!copied" />
                        <x-lucide-check class="w-3.5 h-3.5" x-show="copied" />
                        <span x-text="copied ? 'Copiado!' : 'Copiar'"></span>
                    </button>
                </div>
            </div>

            {{-- Info expiração --}}
            <div class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-100 dark:border-amber-800 mb-6">
                <x-lucide-clock class="w-3.5 h-3.5 text-amber-500 shrink-0" />
                <p class="text-xs text-amber-700 dark:text-amber-400 lato-regular text-left">
                    Este link expira em <strong>7 dias</strong>. Após isso, o candidato não conseguirá acessar o teste.
                </p>
            </div>

            <button type="button" x-on:click="show = false"
                    class="cursor-pointer w-full px-6 py-2.5 rounded-xl bg-slate-800 dark:bg-slate-700 hover:bg-slate-700 dark:hover:bg-slate-600 text-white text-sm lato-bold transition">
                Fechar
            </button>
        </div>
    </div>

    @once
    <script src="{{ asset('js/vaga-geo.js') }}" defer></script>
    @endonce

</div>{{-- /modal: teste enviado --}}


{{-- ═══════════════════════════════════════════════════════════════════════
     MÓDULO DESLIGAMENTOS — Modais + Drawer
═══════════════════════════════════════════════════════════════════════ --}}

{{-- ── Modal: Criar / Editar Desligamento ─────────────────────────── --}}
<div x-data x-show="$wire.demModal" x-cloak style="display:none"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('demModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-xl flex flex-col" @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 shrink-0 rounded-t-2xl">
            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-rose-500 to-red-600 flex items-center justify-center shrink-0">
                    <x-lucide-user-minus class="w-3.5 h-3.5 text-white" />
                </span>
                {{ $demEditId ? 'Editar Desligamento' : 'Iniciar Desligamento' }}
            </h3>
            <button wire:click="$set('demModal', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-5 overflow-y-auto max-h-[75vh]">

            {{-- Card de alerta --}}
            @if (!$demEditId)
            <div class="flex items-start gap-3 p-3.5 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50">
                <x-lucide-triangle-alert class="w-4 h-4 text-amber-500 shrink-0 mt-0.5" />
                <p class="text-xs text-amber-700 dark:text-amber-400 lato-regular leading-relaxed">
                    Ao iniciar, será criado automaticamente um checklist padrão de offboarding com 12 itens.
                </p>
            </div>
            @endif

            {{-- Colaborador --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">
                    Colaborador <span class="text-rose-500">*</span>
                </label>
                <div class="relative">
                    <x-lucide-user class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 pointer-events-none" />
                    <select wire:model="demUserId"
                            class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-400/30 focus:border-rose-400 transition appearance-none"
                            @if ($demEditId) disabled @endif>
                        <option value="">Selecione o colaborador...</option>
                        @foreach ($this->funcionariosAtivos as $func)
                        <option value="{{ $func->id }}">{{ $func->name }}{{ $func->position ? ' · ' . $func->position : '' }}</option>
                        @endforeach
                    </select>
                </div>
                @error('demUserId') <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1"><x-lucide-circle-alert class="w-3 h-3" />{{ $message }}</p> @enderror
            </div>

            {{-- Tipo — cards clicáveis --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">
                    Tipo de Desligamento <span class="text-rose-500">*</span>
                </label>
                @php
                    $tipoIcons = [
                        'voluntario'      => 'log-out',
                        'sem_justa_causa' => 'briefcase',
                        'com_justa_causa' => 'shield-x',
                        'acordo_mutuo'    => 'handshake',
                        'aposentadoria'   => 'sun',
                    ];
                @endphp
                <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                    @foreach (\App\Models\RhDesligamento::$tipoLabels as $tk => $tv)
                    <button wire:click="$set('demTipo', '{{ $tk }}')" type="button"
                            class="cursor-pointer flex flex-col items-center gap-1.5 px-3 py-3 rounded-xl border-2 text-center transition
                                   {{ $demTipo === $tk
                                      ? 'border-rose-500 bg-rose-50 dark:bg-rose-900/20'
                                      : 'border-slate-200 dark:border-slate-600 hover:border-rose-300 dark:hover:border-rose-700 bg-white dark:bg-slate-900' }}">
                        <x-dynamic-component :component="'lucide-' . ($tipoIcons[$tk] ?? 'circle')"
                                             class="w-4 h-4 {{ $demTipo === $tk ? 'text-rose-600 dark:text-rose-400' : 'text-slate-400' }}" />
                        <span class="text-xs lato-bold leading-tight {{ $demTipo === $tk ? 'text-rose-700 dark:text-rose-300' : 'text-slate-600 dark:text-slate-400' }}">{{ $tv }}</span>
                    </button>
                    @endforeach
                </div>
                @error('demTipo') <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p> @enderror
            </div>

            {{-- Datas --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Datas</label>
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Data do Aviso</label>
                        <input wire:model="demDataAviso" type="date"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-400/30 focus:border-rose-400 transition" />
                    </div>
                    <div>
                        <label class="block text-xs text-slate-500 dark:text-slate-400 mb-1">Último Dia <span class="text-rose-500">*</span></label>
                        <input wire:model="demDataUltimoDia" type="date"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-400/30 focus:border-rose-400 transition" />
                        @error('demDataUltimoDia') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>

            {{-- Aviso Prévio --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Aviso Prévio</label>
                <div class="grid grid-cols-2 gap-3">
                    <div class="flex gap-2">
                        @foreach (['trabalhado' => 'Trabalhado', 'indenizado' => 'Indenizado'] as $apv => $apl)
                        <button wire:click="$set('demAvisoPrevioTipo', '{{ $apv }}')" type="button"
                                class="cursor-pointer flex-1 py-2.5 rounded-xl border-2 text-xs lato-bold transition
                                       {{ $demAvisoPrevioTipo === $apv
                                          ? 'border-rose-500 bg-rose-50 dark:bg-rose-900/20 text-rose-700 dark:text-rose-300'
                                          : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-rose-300' }}">
                            {{ $apl }}
                        </button>
                        @endforeach
                    </div>
                    <div>
                        <div class="relative">
                            <input wire:model="demAvisoPrevioDias" type="number" min="0" max="90"
                                   class="w-full px-3 py-2.5 pr-10 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-400/30 transition" />
                            <span class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-400">dias</span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Observações --}}
            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-2">Observações internas</label>
                <textarea wire:model="demObservacoes" rows="2" placeholder="Contexto, acordos, motivos internos..."
                          class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-rose-400/30 focus:border-rose-400 transition resize-none placeholder-slate-400"></textarea>
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/30 shrink-0">
            <button wire:click="$set('demModal', false)" type="button"
                    class="cursor-pointer px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition">
                Cancelar
            </button>
            <button wire:click="saveDem" type="button" wire:loading.attr="disabled" wire:target="saveDem"
                    class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-rose-600 to-red-500 text-white text-sm lato-bold hover:from-rose-700 hover:to-red-600 transition shadow-sm shadow-rose-200 dark:shadow-rose-900/30 disabled:opacity-60">
                <span wire:loading.remove wire:target="saveDem" class="flex items-center gap-2">
                    <x-lucide-user-minus class="w-4 h-4" />
                    {{ $demEditId ? 'Salvar Alterações' : 'Iniciar Processo' }}
                </span>
                <span wire:loading wire:target="saveDem" class="flex items-center gap-2">
                    <x-lucide-loader-circle class="w-4 h-4 animate-spin" /> Salvando...
                </span>
            </button>
        </div>
    </div>
</div>

{{-- ── Drawer: Detalhe do Desligamento ─────────────────────────────── --}}
@if ($demDrawer && $this->demDrawerData)
@php $dd = $this->demDrawerData; @endphp
<div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('demDrawer', false)"></div>
<div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[520px] bg-white dark:bg-slate-800 shadow-2xl flex flex-col">

    {{-- Header do drawer --}}
    <div class="flex items-center gap-3 p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-rose-400 to-red-600 flex items-center justify-center text-white lato-bold text-sm shrink-0">
            {{ strtoupper(substr($dd->funcionario?->name ?? '?', 0, 1)) }}
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $dd->funcionario?->name ?? '—' }}</p>
            <p class="text-xs text-slate-400">{{ $dd->funcionario?->department?->name ?? '' }}{{ $dd->funcionario?->position ? ' · '.$dd->funcionario->position : '' }}</p>
        </div>
        <div class="flex items-center gap-2">
            <button wire:click="openDemModal({{ $dd->id }})" type="button"
                    class="cursor-pointer p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-pencil class="w-4 h-4" />
            </button>
            <button wire:click="$set('demDrawer', false)" type="button"
                    class="cursor-pointer p-2 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-5 h-5" />
            </button>
        </div>
    </div>

    {{-- Sub-tabs --}}
    <div class="flex items-center gap-1 p-3 border-b border-slate-100 dark:border-slate-700 shrink-0">
        @foreach (['processo' => 'Processo', 'checklist' => 'Checklist', 'verbas' => 'Verbas', 'impacto' => 'Impacto'] as $dtk => $dtl)
        <button wire:click="$set('demDrawerTab', '{{ $dtk }}')" type="button"
                class="cursor-pointer flex-1 py-1.5 text-xs lato-bold rounded-lg transition text-center
                       {{ $demDrawerTab === $dtk ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            {{ $dtl }}
        </button>
        @endforeach
    </div>

    {{-- Conteúdo do sub-tab --}}
    <div class="flex-1 overflow-y-auto p-5 space-y-4">

        {{-- ── TAB: PROCESSO ── --}}
        @if ($demDrawerTab === 'processo')

        {{-- Tipo + Status badges --}}
        <div class="flex flex-wrap gap-2">
            <span class="text-xs px-2.5 py-1 rounded-full {{ \App\Models\RhDesligamento::$tipoCores[$dd->tipo] ?? 'bg-slate-100 text-slate-600' }}">
                {{ \App\Models\RhDesligamento::$tipoLabels[$dd->tipo] ?? $dd->tipo }}
            </span>
            @if ($dd->status === 'concluido')
            <span class="text-xs px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300">✓ Concluído</span>
            @elseif ($dd->status === 'cancelado')
            <span class="text-xs px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">Cancelado</span>
            @else
            <span class="text-xs px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300">Em processo</span>
            @endif
        </div>

        {{-- Datas --}}
        <div class="grid grid-cols-2 gap-3">
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                <p class="text-xs text-slate-400 mb-1">Data do Aviso</p>
                <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $dd->data_aviso?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                <p class="text-xs text-slate-400 mb-1">Último Dia</p>
                <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $dd->data_ultimo_dia?->format('d/m/Y') ?? '—' }}</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                <p class="text-xs text-slate-400 mb-1">Aviso Prévio</p>
                <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 capitalize">{{ $dd->aviso_previo_tipo }} · {{ $dd->aviso_previo_dias }} dias</p>
            </div>
            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                <p class="text-xs text-slate-400 mb-1">Responsável RH</p>
                <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $dd->rhUser?->name ?? '—' }}</p>
            </div>
        </div>

        @if ($dd->observacoes)
        <div class="bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/50 rounded-xl p-3">
            <p class="text-xs text-amber-600 dark:text-amber-400 lato-bold mb-1">Observações</p>
            <p class="text-sm text-slate-700 dark:text-slate-300">{{ $dd->observacoes }}</p>
        </div>
        @endif

        {{-- Recontratável --}}
        <div class="bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl p-4">
            <p class="text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Recontratável?</p>
            <div class="flex gap-2">
                <button wire:click="atualizarRecontratavel(true)" type="button"
                        class="cursor-pointer flex-1 py-2 rounded-lg text-sm lato-bold transition
                               {{ $dd->recontratavel === true ? 'bg-emerald-500 text-white' : 'bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300 hover:bg-emerald-50' }}">
                    Sim
                </button>
                <button wire:click="atualizarRecontratavel(false)" type="button"
                        class="cursor-pointer flex-1 py-2 rounded-lg text-sm lato-bold transition
                               {{ $dd->recontratavel === false ? 'bg-red-500 text-white' : 'bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300 hover:bg-red-50' }}">
                    Não
                </button>
                <button wire:click="limparRecontratavel" type="button"
                        class="cursor-pointer flex-1 py-2 rounded-lg text-sm lato-bold transition
                               {{ is_null($dd->recontratavel) ? 'bg-slate-400 text-white' : 'bg-slate-100 dark:bg-slate-600 text-slate-600 dark:text-slate-300 hover:bg-slate-200' }}">
                    Indefinido
                </button>
            </div>
        </div>

        {{-- Entrevista de desligamento --}}
        <div class="bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl p-4">
            <div class="flex items-center justify-between mb-3">
                <p class="text-xs lato-bold text-slate-600 dark:text-slate-400">Entrevista de Desligamento</p>
                <button wire:click="enviarEntrevista" type="button"
                        class="cursor-pointer text-xs px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 hover:bg-blue-100 transition lato-bold">
                    {{ $dd->entrevista ? 'Reenviar Link' : 'Gerar Link' }}
                </button>
            </div>
            @if ($dd->entrevista)
            <div class="space-y-1.5">
                <div class="flex items-center gap-2">
                    @if ($dd->entrevista->respondido_at)
                    <x-lucide-check-circle class="w-4 h-4 text-emerald-500 shrink-0" />
                    <span class="text-xs text-emerald-600 dark:text-emerald-400">Respondida em {{ $dd->entrevista->respondido_at->format('d/m/Y H:i') }}</span>
                    @else
                    <x-lucide-clock class="w-4 h-4 text-amber-500 shrink-0" />
                    <span class="text-xs text-slate-500 dark:text-slate-400">Aguardando resposta</span>
                    @endif
                </div>
                @if ($dd->entrevista->respondido_at)
                <p class="text-xs text-slate-500 dark:text-slate-400">NPS médio: <span class="lato-bold text-slate-700 dark:text-slate-200">{{ $dd->entrevista->nps_media }}/5</span></p>
                @endif
                <div class="flex items-center gap-2 mt-2">
                    <input readonly type="text"
                           value="{{ url('/funcionario/entrevista-desligamento/' . $dd->entrevista->token) }}"
                           class="flex-1 text-xs px-2 py-1.5 border border-slate-200 dark:border-slate-600 rounded-lg bg-slate-50 dark:bg-slate-600 text-slate-600 dark:text-slate-300" />
                    <button onclick="navigator.clipboard.writeText('{{ url('/funcionario/entrevista-desligamento/' . $dd->entrevista->token) }}')" type="button"
                            class="cursor-pointer p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-600 text-slate-400 hover:text-slate-600 transition">
                        <x-lucide-copy class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>
            @else
            <p class="text-xs text-slate-400">Nenhum link gerado ainda.</p>
            @endif
        </div>

        {{-- Ação: Concluir desligamento --}}
        @if ($dd->status !== 'concluido')
        <button wire:click="concluirDesligamento" type="button"
                wire:confirm="Confirmar conclusão do processo de desligamento?"
                class="cursor-pointer w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm lato-bold hover:from-emerald-600 hover:to-teal-600 transition shadow-sm">
            Concluir Desligamento
        </button>
        @endif

        @elseif ($demDrawerTab === 'checklist')

        {{-- CHECKLIST TAB --}}
        @php $cl = $dd->checklist ?? collect(); @endphp
        <div class="flex items-center justify-between mb-1">
            <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Itens do Checklist</p>
            <span class="text-xs text-slate-400">{{ $cl->where('status','concluido')->count() }}/{{ $cl->count() }}</span>
        </div>
        {{-- Progress bar --}}
        @if ($cl->count() > 0)
        <div class="w-full h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full mb-3">
            <div class="h-1.5 rounded-full bg-gradient-to-r from-emerald-400 to-teal-400 transition-all"
                 style="width: {{ round($cl->where('status','concluido')->count() / $cl->count() * 100) }}%"></div>
        </div>
        @endif
        <div class="space-y-2">
            @forelse ($cl as $item)
            <button wire:click="toggleChecklist({{ $item->id }})" type="button"
                    class="cursor-pointer w-full flex items-start gap-3 p-3 rounded-xl border transition text-left
                           {{ $item->status === 'concluido'
                               ? 'bg-emerald-50 dark:bg-emerald-900/20 border-emerald-200 dark:border-emerald-700/40'
                               : 'bg-white dark:bg-slate-700 border-slate-200 dark:border-slate-600 hover:border-emerald-300' }}">
                <div class="w-5 h-5 rounded-full shrink-0 flex items-center justify-center mt-0.5
                            {{ $item->status === 'concluido' ? 'bg-emerald-500' : 'border-2 border-slate-300 dark:border-slate-500' }}">
                    @if ($item->status === 'concluido')
                    <x-lucide-check class="w-3 h-3 text-white" />
                    @endif
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-bold {{ $item->status === 'concluido' ? 'text-emerald-700 dark:text-emerald-300 line-through' : 'text-slate-700 dark:text-slate-200' }}">
                        {{ $item->titulo }}
                    </p>
                    @if ($item->descricao)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $item->descricao }}</p>
                    @endif
                    @if ($item->status === 'concluido' && $item->concluido_at)
                    <p class="text-xs text-emerald-500 mt-1">Concluído {{ $item->concluido_at->diffForHumans() }}</p>
                    @endif
                </div>
            </button>
            @empty
            <p class="text-sm text-slate-400 text-center py-6">Sem itens no checklist.</p>
            @endforelse
        </div>

        @elseif ($demDrawerTab === 'verbas')

        {{-- VERBAS TAB --}}
        @if ($dd->verbas)
        @php $vb = $dd->verbas; @endphp
        <div class="space-y-3">
            {{-- Total líquido destaque --}}
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 rounded-xl p-4 text-white text-center">
                <p class="text-xs opacity-80 mb-1">Total Líquido a Receber</p>
                <p class="text-2xl lato-black">R$ {{ number_format($vb->total_liquido, 2, ',', '.') }}</p>
            </div>
            {{-- Créditos --}}
            <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-4 space-y-2">
                <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Créditos</p>
                @foreach ([
                    ['Saldo de Salário', $vb->saldo_salario],
                    ['Férias Proporcionais', $vb->ferias_proporcionais],
                    ['Férias Vencidas', $vb->ferias_vencidas],
                    ['1/3 de Férias', $vb->um_terco_ferias],
                    ['13° Proporcional', $vb->decimo_terceiro],
                    ['Aviso Prévio', $vb->aviso_previo_valor],
                    ['Outros Créditos', $vb->outros_creditos ?? 0],
                ] as [$label, $val])
                @if ($val > 0)
                <div class="flex justify-between items-center">
                    <span class="text-xs text-slate-500 dark:text-slate-400">{{ $label }}</span>
                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">R$ {{ number_format($val, 2, ',', '.') }}</span>
                </div>
                @endif
                @endforeach
                <div class="border-t border-slate-100 dark:border-slate-600 pt-2 mt-2 flex justify-between">
                    <span class="text-xs lato-bold text-slate-600 dark:text-slate-300">Total Bruto</span>
                    <span class="text-sm lato-black text-slate-800 dark:text-white">R$ {{ number_format($vb->total_bruto, 2, ',', '.') }}</span>
                </div>
            </div>
            {{-- Descontos --}}
            <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-4 space-y-2">
                <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">Descontos</p>
                @foreach ([['INSS', $vb->inss], ['IRRF', $vb->irrf], ['Outros', $vb->descontos ?? 0]] as [$label, $val])
                @if ($val > 0)
                <div class="flex justify-between">
                    <span class="text-xs text-slate-500">{{ $label }}</span>
                    <span class="text-sm lato-bold text-rose-600">- R$ {{ number_format($val, 2, ',', '.') }}</span>
                </div>
                @endif
                @endforeach
            </div>
            {{-- FGTS --}}
            <div class="bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-200 dark:border-blue-700/40 p-4">
                <p class="text-xs lato-bold text-blue-600 dark:text-blue-400 mb-2">FGTS</p>
                <div class="flex justify-between mb-1">
                    <span class="text-xs text-slate-500">Saldo FGTS</span>
                    <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">R$ {{ number_format($vb->saldo_fgts, 2, ',', '.') }}</span>
                </div>
                @if ($vb->multa_fgts > 0)
                <div class="flex justify-between">
                    <span class="text-xs text-slate-500">Multa ({{ $dd->tipo === 'acordo_mutuo' ? '20%' : '40%' }})</span>
                    <span class="text-sm lato-bold text-emerald-600">+ R$ {{ number_format($vb->multa_fgts, 2, ',', '.') }}</span>
                </div>
                @endif
                <div class="border-t border-blue-200 dark:border-blue-700/40 mt-2 pt-2 flex justify-between">
                    <span class="text-xs lato-bold text-blue-700 dark:text-blue-300">Total disponível FGTS</span>
                    <span class="text-sm lato-black text-blue-700 dark:text-blue-300">R$ {{ number_format($vb->fgts_disponivel, 2, ',', '.') }}</span>
                </div>
            </div>
            {{-- Botão recalcular --}}
            <button wire:click="openVerbasModal({{ $dd->id }})" type="button"
                    class="cursor-pointer w-full py-2 rounded-xl border border-slate-200 dark:border-slate-600 text-sm text-slate-600 dark:text-slate-300 lato-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                Recalcular Verbas
            </button>
        </div>
        @else
        <div class="text-center py-10">
            <x-lucide-calculator class="w-10 h-10 text-slate-300 dark:text-slate-600 mx-auto mb-3" />
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-4">Verbas rescisórias não calculadas.</p>
            <button wire:click="openVerbasModal({{ $dd->id }})" type="button"
                    class="cursor-pointer px-5 py-2 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-500 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-600 transition shadow-sm">
                Calcular Verbas
            </button>
        </div>
        @endif

        @elseif ($demDrawerTab === 'impacto')

        {{-- IMPACTO TAB --}}
        <div class="space-y-3">
            <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-4 space-y-3">
                <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Impacto Estimado</p>
                @php
                    $salBase = $dd->verbas?->salario_base ?? 0;
                    $custoTotal = $dd->verbas?->total_liquido ?? 0;
                    $custoRep   = $salBase * 1.5;
                @endphp
                <div class="grid grid-cols-2 gap-3">
                    <div class="bg-slate-50 dark:bg-slate-600/50 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-400 mb-1">Custo Rescisão</p>
                        <p class="text-base lato-black text-slate-800 dark:text-white">R$ {{ number_format($custoTotal, 0, ',', '.') }}</p>
                    </div>
                    <div class="bg-slate-50 dark:bg-slate-600/50 rounded-xl p-3 text-center">
                        <p class="text-xs text-slate-400 mb-1">Reposição (1,5× sal.)</p>
                        <p class="text-base lato-black text-slate-800 dark:text-white">R$ {{ number_format($custoRep, 0, ',', '.') }}</p>
                    </div>
                </div>
                <div class="bg-rose-50 dark:bg-rose-900/20 rounded-xl p-3 text-center border border-rose-200 dark:border-rose-700/40">
                    <p class="text-xs text-rose-500 mb-1">Custo Total Estimado</p>
                    <p class="text-xl lato-black text-rose-700 dark:text-rose-300">R$ {{ number_format($custoTotal + $custoRep, 0, ',', '.') }}</p>
                </div>
            </div>
            <div class="bg-white dark:bg-slate-700 rounded-xl border border-slate-200 dark:border-slate-600 p-4">
                <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">Tempo de Casa</p>
                <div class="flex items-center gap-3">
                    <x-lucide-calendar class="w-5 h-5 text-slate-400 shrink-0" />
                    <div>
                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">
                            {{ $dd->verbas?->meses_trabalhados ? intdiv($dd->verbas->meses_trabalhados, 12).'a '.($dd->verbas->meses_trabalhados % 12).'m' : '—' }}
                        </p>
                        <p class="text-xs text-slate-400">{{ $dd->verbas?->data_admissao?->format('d/m/Y') ?? '—' }} → {{ $dd->data_ultimo_dia?->format('d/m/Y') ?? '—' }}</p>
                    </div>
                </div>
            </div>
        </div>
        @endif

    </div>{{-- fim scroll --}}
</div>{{-- fim drawer panel --}}
@endif{{-- fim demDrawer --}}


{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL: ATENDER SOLICITAÇÃO (RH)
══════════════════════════════════════════════════════════════════════════ --}}
<div x-data x-show="$wire.solModal" x-cloak style="display:none"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('solModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg flex flex-col" @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center">
                    <x-lucide-inbox class="w-3.5 h-3.5 text-white" />
                </span>
                Atender Solicitação
            </h3>
            <button wire:click="$set('solModal', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-4 overflow-y-auto">
            @if ($solEditId)
            @php $solRec = \App\Models\RhSolicitacao::with('user.department')->find($solEditId); @endphp
            @if ($solRec)
            {{-- Info do solicitante --}}
            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-sm lato-bold shrink-0">
                    {{ strtoupper(substr($solRec->user?->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $solRec->user?->name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $solRec->user?->department?->name ?? '' }} · {{ \App\Models\RhSolicitacao::$tipoLabels[$solRec->tipo] ?? $solRec->tipo }}</p>
                </div>
                <span class="text-xs text-slate-400">{{ $solRec->created_at->format('d/m/Y') }}</span>
            </div>
            @if ($solRec->descricao)
            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 rounded-xl">
                <p class="text-xs lato-bold text-amber-600 dark:text-amber-400 mb-1">Descrição do funcionário</p>
                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $solRec->descricao }}</p>
            </div>
            @endif
            @endif
            @endif

            {{-- Status --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Status</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (\App\Models\RhSolicitacao::$statusLabels as $k => $v)
                    <button wire:click="$set('solStatusEdit', '{{ $k }}')" type="button"
                            class="cursor-pointer flex items-center gap-2 px-3 py-2.5 rounded-xl border text-sm lato-bold transition
                                   {{ $solStatusEdit === $k
                                       ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300'
                                       : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-amber-300' }}">
                        <span class="w-2 h-2 rounded-full
                            {{ $k === 'pendente' ? 'bg-amber-400' : ($k === 'em_andamento' ? 'bg-blue-400' : ($k === 'concluida' ? 'bg-emerald-400' : 'bg-slate-400')) }}"></span>
                        {{ $v }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Prazo --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Prazo de Entrega</label>
                <input wire:model="solPrazo" type="date"
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400" />
            </div>

            {{-- Observação do RH --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Observação / Resposta ao Funcionário</label>
                <textarea wire:model="solObsRh" rows="4" placeholder="Ex: Documento gerado e disponível para retirada na recepção…"
                          class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none"></textarea>
            </div>

            {{-- Upload de arquivo --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Anexar Documento (opcional)</label>
                <label class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-amber-400 transition bg-slate-50 dark:bg-slate-700/50">
                    <x-lucide-paperclip class="w-4 h-4 text-slate-400 shrink-0" />
                    <span class="text-sm text-slate-500 dark:text-slate-400">
                        @if ($solArquivo)
                            {{ $solArquivo->getClientOriginalName() }}
                        @else
                            Clique para selecionar arquivo (PDF, DOC, DOCX — máx. 10 MB)
                        @endif
                    </span>
                    <input type="file" wire:model="solArquivo" class="hidden" accept=".pdf,.doc,.docx,.png,.jpg" />
                </label>
                @error('solArquivo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0">
            <button wire:click="$set('solModal', false)" type="button"
                    class="cursor-pointer px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition">
                Cancelar
            </button>
            <button wire:click="saveSol" type="button" wire:loading.attr="disabled" wire:target="saveSol"
                    class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-500 text-white text-sm lato-bold hover:from-amber-600 hover:to-orange-600 transition shadow-sm disabled:opacity-60">
                <span wire:loading.remove wire:target="saveSol" class="flex items-center gap-2">
                    <x-lucide-check class="w-4 h-4" />
                    Salvar Atendimento
                </span>
                <span wire:loading wire:target="saveSol" class="flex items-center gap-2">
                    <x-lucide-loader-circle class="w-4 h-4 animate-spin" /> Salvando...
                </span>
            </button>
        </div>
    </div>
</div>

        @if ($aba === 'people-analytics')
        @php $pa = $this->peopleAnalytics; $depListPa = $this->departamentos; @endphp
        <div class="p-4 md:p-6 space-y-6">

            {{-- ── CABEÇALHO ─────────────────────────────────────────── --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h1 class="text-xl lato-black text-slate-800 dark:text-white flex items-center gap-2">
                        <x-lucide-users-round class="w-5 h-5 text-purple-500" />
                        People Analytics
                    </h1>
                    <p class="text-sm text-slate-400 lato-regular mt-0.5">Visão unificada · headcount, turnover, humor, eNPS e performance</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    @if($paAno || $paDept)
                    <button wire:click="paLimpar" type="button"
                            class="cursor-pointer flex items-center gap-1 text-xs text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 lato-regular transition">
                        <x-lucide-filter-x class="w-3.5 h-3.5" /> Limpar
                    </button>
                    @endif
                    <select wire:model="paDept" wire:change="paAplicar"
                            class="text-sm border border-gray-200 dark:border-slate-600 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 rounded-lg px-3 py-2 lato-regular cursor-pointer focus:outline-none focus:ring-2 focus:ring-purple-400">
                        <option value="">Todos os departamentos</option>
                        @foreach($depListPa as $dep)
                            <option value="{{ $dep->id }}" {{ $paDept == $dep->id ? 'selected' : '' }}>{{ $dep->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- ── KPI CARDS ──────────────────────────────────────────── --}}
            <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-6 gap-3">
                @php
                    $paKpis = [
                        [
                            'label' => 'Headcount',
                            'value' => $pa['headcount'],
                            'sub'   => 'ativos',
                            'icon'  => 'users',
                            'grad'  => 'from-blue-500 to-indigo-600',
                            'text'  => 'text-blue-600 dark:text-blue-400',
                        ],
                        [
                            'label' => 'Turnover 12m',
                            'value' => $pa['turnoverAnual'] . '%',
                            'sub'   => $pa['totalSaidas'] . ' saídas',
                            'icon'  => 'log-out',
                            'grad'  => $pa['turnoverAnual'] > 10 ? 'from-rose-500 to-red-600' : ($pa['turnoverAnual'] > 5 ? 'from-amber-500 to-orange-500' : 'from-emerald-500 to-teal-600'),
                            'text'  => $pa['turnoverAnual'] > 10 ? 'text-rose-600 dark:text-rose-400' : ($pa['turnoverAnual'] > 5 ? 'text-amber-600 dark:text-amber-400' : 'text-emerald-600 dark:text-emerald-400'),
                        ],
                        [
                            'label' => 'Humor',
                            'value' => ($pa['humorAtual'] ?: '—') . ($pa['humorAtual'] ? '/100' : ''),
                            'sub'   => $pa['humorAtual'] >= 75 ? 'Ótimo' : ($pa['humorAtual'] >= 60 ? 'Bom' : ($pa['humorAtual'] >= 45 ? 'Regular' : 'Atenção')),
                            'icon'  => $pa['humorAtual'] >= 70 ? 'smile' : ($pa['humorAtual'] >= 50 ? 'meh' : 'frown'),
                            'grad'  => $pa['humorAtual'] >= 70 ? 'from-emerald-500 to-teal-500' : ($pa['humorAtual'] >= 50 ? 'from-amber-500 to-yellow-500' : 'from-rose-500 to-red-500'),
                            'text'  => $pa['humorAtual'] >= 70 ? 'text-emerald-600 dark:text-emerald-400' : ($pa['humorAtual'] >= 50 ? 'text-amber-600 dark:text-amber-400' : 'text-rose-600 dark:text-rose-400'),
                        ],
                        [
                            'label' => 'eNPS',
                            'value' => $pa['npsAtual'] !== null ? ($pa['npsAtual'] >= 0 ? '+' : '') . $pa['npsAtual'] : '—',
                            'sub'   => $pa['npsAtual'] !== null ? ($pa['npsAtual'] >= 50 ? 'Excelente' : ($pa['npsAtual'] >= 30 ? 'Bom' : ($pa['npsAtual'] >= 0 ? 'Neutro' : 'Crítico'))) : 'Sem dados',
                            'icon'  => 'trending-up',
                            'grad'  => $pa['npsAtual'] !== null && $pa['npsAtual'] >= 30 ? 'from-violet-500 to-purple-600' : ($pa['npsAtual'] !== null && $pa['npsAtual'] >= 0 ? 'from-amber-500 to-orange-500' : 'from-rose-500 to-red-600'),
                            'text'  => 'text-violet-600 dark:text-violet-400',
                        ],
                        [
                            'label' => 'Performance DPI',
                            'value' => $pa['dpiScore'] ? $pa['dpiScore'] . '%' : '—',
                            'sub'   => 'nível atual/meta',
                            'icon'  => 'bar-chart-3',
                            'grad'  => 'from-teal-500 to-cyan-600',
                            'text'  => 'text-teal-600 dark:text-teal-400',
                        ],
                        [
                            'label' => 'OKR Progress',
                            'value' => $pa['okrProgress'] !== null ? $pa['okrProgress'] . '%' : '—',
                            'sub'   => 'key results ativos',
                            'icon'  => 'target',
                            'grad'  => 'from-orange-500 to-amber-600',
                            'text'  => 'text-orange-600 dark:text-orange-400',
                        ],
                    ];
                @endphp
                @foreach($paKpis as $kpi)
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br {{ $kpi['grad'] }} flex items-center justify-center mb-3">
                        <x-dynamic-component :component="'lucide-'.$kpi['icon']" class="w-4 h-4 text-white" />
                    </div>
                    <p class="text-xl lato-black {{ $kpi['text'] }}">{{ $kpi['value'] }}</p>
                    <p class="text-xs text-slate-500 lato-regular mt-0.5">{{ $kpi['label'] }}</p>
                    <p class="text-[10px] text-slate-400 lato-regular">{{ $kpi['sub'] }}</p>
                </div>
                @endforeach
            </div>

            {{-- ── LINHA 1: Headcount + Turnover ──────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Headcount trend --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <x-lucide-users class="w-4 h-4 text-blue-500" /> Evolução de Headcount
                        </h3>
                        @if(count($pa['projMonths']) > 0)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-purple-100 dark:bg-purple-900/30 text-purple-700 dark:text-purple-300 lato-bold">
                            + projeção 3m
                        </span>
                        @endif
                    </div>
                    <div id="pa-hc-chart" wire:ignore
                         x-data
                         x-init="
                            const labels  = @js(array_keys($pa['headcountTrend']));
                            const hcData  = @js(array_values($pa['headcountTrend']));
                            @if(count($pa['projMonths']) > 0)
                            const projLbl = @js(array_column($pa['projMonths'], 'label'));
                            const projVal = @js(array_column($pa['projMonths'], 'value'));
                            const allLbl  = [...labels, ...projLbl];
                            const series  = [
                                { name: 'Headcount real', data: [...hcData, ...Array(projLbl.length).fill(null)] },
                                { name: 'Projeção', data: [...Array(hcData.length - 1).fill(null), hcData[hcData.length-1], ...projVal] },
                            ];
                            @else
                            const allLbl  = labels;
                            const series  = [{ name: 'Headcount', data: hcData }];
                            @endif
                            new ApexCharts($el, {
                                chart: { type: 'area', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series,
                                xaxis: { categories: allLbl, labels: { style: { fontSize: '10px' } } },
                                yaxis: { labels: { style: { fontSize: '10px' }, formatter: v => Math.round(v) } },
                                colors: ['#6366f1', '#a78bfa'],
                                fill: { type: ['gradient', 'pattern'], gradient: { shadeIntensity: 1, opacityFrom: 0.35, opacityTo: 0.05 }, pattern: { style: 'slantedLines', width: 4, height: 4, strokeWidth: 1 } },
                                stroke: { curve: 'smooth', width: [2, 2], dashArray: [0, 5] },
                                dataLabels: { enabled: false },
                                grid: { borderColor: 'rgba(148,163,184,0.12)' },
                                legend: { position: 'top', fontSize: '11px' },
                                tooltip: { y: { formatter: v => v !== null ? Math.round(v) + ' pessoas' : '' } },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                </div>

                {{-- Turnover trend --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                            <x-lucide-log-out class="w-4 h-4 text-rose-500" /> Turnover Mensal (%)
                        </h3>
                        <span class="text-xs lato-regular text-slate-400">Meta ≤ 1% / mês</span>
                    </div>
                    <div id="pa-tv-chart" wire:ignore
                         x-data
                         x-init="
                            const labels = @js(array_keys($pa['turnoverTrend']));
                            const data   = @js(array_values($pa['turnoverTrend']));
                            new ApexCharts($el, {
                                chart: { type: 'bar', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series: [{ name: 'Turnover %', data }],
                                xaxis: { categories: labels, labels: { style: { fontSize: '10px' } } },
                                yaxis: { labels: { style: { fontSize: '10px' }, formatter: v => v + '%' } },
                                colors: data.map(v => v > 1 ? '#f43f5e' : v > 0.5 ? '#f59e0b' : '#10b981'),
                                plotOptions: { bar: { borderRadius: 4, columnWidth: '60%' } },
                                annotations: { yaxis: [{ y: 1, borderColor: '#f59e0b', borderWidth: 1, strokeDashArray: 4, label: { text: 'Meta 1%', style: { fontSize: '10px', color: '#f59e0b', background: 'transparent' } } }] },
                                dataLabels: { enabled: false },
                                grid: { borderColor: 'rgba(148,163,184,0.12)' },
                                tooltip: { y: { formatter: v => v + '%' } },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                    {{-- breakdown por tipo --}}
                    @if(!empty($pa['turnoverByTipo']))
                    <div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 flex flex-wrap gap-2">
                        @foreach($pa['turnoverByTipo'] as $tipo => $qtd)
                        @php $tipoLabel = \App\Models\RhDesligamento::$tipoLabels[$tipo] ?? $tipo; @endphp
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-[10px] lato-regular">
                            {{ $tipoLabel }}: <strong>{{ $qtd }}</strong>
                        </span>
                        @endforeach
                    </div>
                    @endif
                </div>

            </div>

            {{-- ── LINHA 2: Humor + eNPS ───────────────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Humor trend --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 mb-4">
                        <x-lucide-smile class="w-4 h-4 text-amber-500" /> Score de Humor Mensal
                        <span class="ml-auto text-xs text-slate-400 lato-regular font-normal">0–100</span>
                    </h3>
                    @php
                        $humorHasData = collect($pa['humorTrend'])->whereNotNull()->isNotEmpty();
                    @endphp
                    @if($humorHasData)
                    <div id="pa-humor-chart" wire:ignore
                         x-data
                         x-init="
                            const labels = @js(array_keys($pa['humorTrend']));
                            const raw    = @js(array_values($pa['humorTrend']));
                            new ApexCharts($el, {
                                chart: { type: 'line', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series: [{ name: 'Score Humor', data: raw }],
                                xaxis: { categories: labels, labels: { style: { fontSize: '10px' } } },
                                yaxis: { min: 0, max: 100, tickAmount: 5, labels: { style: { fontSize: '10px' }, formatter: v => v + '/100' } },
                                colors: ['#f59e0b'],
                                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02 } },
                                stroke: { curve: 'smooth', width: 2 },
                                markers: { size: 4 },
                                dataLabels: { enabled: false },
                                annotations: {
                                    yaxis: [
                                        { y: 70, borderColor: '#10b981', strokeDashArray: 4, label: { text: 'Bom (70)', style: { fontSize: '10px', color: '#10b981', background: 'transparent' } } },
                                        { y: 50, borderColor: '#f59e0b', strokeDashArray: 4, label: { text: 'Regular (50)', style: { fontSize: '10px', color: '#f59e0b', background: 'transparent' } } },
                                    ]
                                },
                                grid: { borderColor: 'rgba(148,163,184,0.12)' },
                                tooltip: { y: { formatter: v => v !== null ? v + '/100' : 'Sem dados' } },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <x-lucide-meh class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Nenhum check-in de humor registrado no período</p>
                    </div>
                    @endif
                </div>

                {{-- eNPS trend --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 mb-4">
                        <x-lucide-trending-up class="w-4 h-4 text-violet-500" /> eNPS Histórico
                        <span class="ml-auto text-xs text-slate-400 lato-regular font-normal">Employee Net Promoter Score</span>
                    </h3>
                    @php $npsHasData = collect($pa['npsTrend'])->whereNotNull()->isNotEmpty(); @endphp
                    @if($npsHasData)
                    <div id="pa-nps-chart" wire:ignore
                         x-data
                         x-init="
                            const labels = @js(array_keys($pa['npsTrend']));
                            const raw    = @js(array_values($pa['npsTrend']));
                            new ApexCharts($el, {
                                chart: { type: 'line', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series: [{ name: 'eNPS', data: raw }],
                                xaxis: { categories: labels, labels: { style: { fontSize: '10px' } } },
                                yaxis: { min: -100, max: 100, tickAmount: 5, labels: { style: { fontSize: '10px' }, formatter: v => (v >= 0 ? '+' : '') + v } },
                                colors: ['#8b5cf6'],
                                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0.02 } },
                                stroke: { curve: 'smooth', width: 2 },
                                markers: { size: 4 },
                                dataLabels: { enabled: false },
                                annotations: {
                                    yaxis: [
                                        { y: 50,  borderColor: '#10b981', strokeDashArray: 4, label: { text: 'Excelente (50)', style: { fontSize: '10px', color: '#10b981', background: 'transparent' } } },
                                        { y: 0,   borderColor: '#f43f5e', strokeDashArray: 3, label: { text: 'Zero', style: { fontSize: '10px', color: '#f43f5e', background: 'transparent' } } },
                                    ]
                                },
                                grid: { borderColor: 'rgba(148,163,184,0.12)' },
                                tooltip: { y: { formatter: v => v !== null ? (v >= 0 ? '+' : '') + v : 'Sem dados' } },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <x-lucide-bar-chart-2 class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Nenhuma resposta de pesquisa com escala 0-10 no período</p>
                    </div>
                    @endif
                </div>

            </div>

            {{-- ── LINHA 3: Performance + Departamentos ───────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">

                {{-- Performance DPI trend --}}
                <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 mb-4">
                        <x-lucide-bar-chart-3 class="w-4 h-4 text-teal-500" /> Performance Mensal
                        <span class="ml-auto text-xs text-slate-400 lato-regular font-normal">DPI (% nível atual/meta)</span>
                    </h3>
                    @php $perfHasData = collect($pa['perfTrend'])->whereNotNull()->isNotEmpty(); @endphp
                    @if($perfHasData)
                    <div id="pa-perf-chart" wire:ignore
                         x-data
                         x-init="
                            const labels = @js(array_keys($pa['perfTrend']));
                            const raw    = @js(array_values($pa['perfTrend']));
                            const okrPct = @js($pa['okrProgress']);
                            const evalPct= @js($pa['evalScore'] ? min($pa['evalScore'] / 5 * 100, 100) : null);
                            const series = [{ name: 'DPI Achievement %', data: raw }];
                            new ApexCharts($el, {
                                chart: { type: 'area', height: 200, toolbar: { show: false }, background: 'transparent' },
                                series,
                                xaxis: { categories: labels, labels: { style: { fontSize: '10px' } } },
                                yaxis: { min: 0, max: 100, tickAmount: 5, labels: { style: { fontSize: '10px' }, formatter: v => v + '%' } },
                                colors: ['#14b8a6'],
                                fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
                                stroke: { curve: 'smooth', width: 2 },
                                markers: { size: 4 },
                                dataLabels: { enabled: false },
                                annotations: { yaxis: [{ y: 80, borderColor: '#10b981', strokeDashArray: 4, label: { text: 'Meta 80%', style: { fontSize: '10px', color: '#10b981', background: 'transparent' } } }] },
                                grid: { borderColor: 'rgba(148,163,184,0.12)' },
                                tooltip: { y: { formatter: v => v !== null ? v + '%' : 'Sem dados' } },
                                theme: { mode: document.documentElement.classList.contains('dark') ? 'dark' : 'light' },
                            }).render();
                         ">
                    </div>
                    {{-- OKR + Avaliação pills --}}
                    <div class="mt-4 flex flex-wrap gap-3">
                        @if($pa['okrProgress'] !== null)
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-orange-50 dark:bg-orange-900/20 border border-orange-200 dark:border-orange-800">
                            <x-lucide-target class="w-4 h-4 text-orange-500" />
                            <div>
                                <p class="text-xs lato-bold text-orange-700 dark:text-orange-300">{{ $pa['okrProgress'] }}%</p>
                                <p class="text-[10px] text-orange-500 lato-regular">OKR médio</p>
                            </div>
                        </div>
                        @endif
                        @if($pa['evalScore'] !== null)
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-violet-50 dark:bg-violet-900/20 border border-violet-200 dark:border-violet-800">
                            <x-lucide-award class="w-4 h-4 text-violet-500" />
                            <div>
                                <p class="text-xs lato-bold text-violet-700 dark:text-violet-300">{{ $pa['evalScore'] }}/5</p>
                                <p class="text-[10px] text-violet-500 lato-regular">Avaliação média</p>
                            </div>
                        </div>
                        @endif
                        <div class="flex items-center gap-2 px-3 py-2 rounded-xl bg-teal-50 dark:bg-teal-900/20 border border-teal-200 dark:border-teal-800">
                            <x-lucide-activity class="w-4 h-4 text-teal-500" />
                            <div>
                                <p class="text-xs lato-bold text-teal-700 dark:text-teal-300">{{ $pa['perfIndex'] }}%</p>
                                <p class="text-[10px] text-teal-500 lato-regular">Índice consolidado</p>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="flex flex-col items-center justify-center py-10 text-slate-400">
                        <x-lucide-bar-chart-3 class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Nenhum dado de DPI disponível no período</p>
                    </div>
                    @endif
                </div>

                {{-- Headcount por Departamento --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 mb-4">
                        <x-lucide-building-2 class="w-4 h-4 text-blue-500" /> Por Departamento
                    </h3>
                    @if(count($pa['deptBreakdown']) > 0)
                    @php $maxHc = max(array_column($pa['deptBreakdown'], 'headcount') ?: [1]); @endphp
                    <div class="space-y-2.5 overflow-y-auto max-h-64">
                        @foreach($pa['deptBreakdown'] as $dept)
                        <div>
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-xs text-slate-600 dark:text-slate-300 lato-regular truncate flex-1 min-w-0 mr-2">{{ $dept['name'] }}</span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-white shrink-0">{{ $dept['headcount'] }}</span>
                            </div>
                            <div class="h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-400 to-indigo-500 rounded-full transition-all"
                                     style="width: {{ $maxHc > 0 ? round($dept['headcount'] / $maxHc * 100) : 0 }}%"></div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <p class="text-xs text-slate-400 lato-regular text-center py-8">Nenhum departamento cadastrado</p>
                    @endif
                </div>

            </div>

            {{-- ── LINHA 4: Insights + Projeção ───────────────────────── --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">

                {{-- Insights --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 mb-4">
                        <x-lucide-zap class="w-4 h-4 text-amber-500" /> Insights Automáticos
                    </h3>
                    <div class="space-y-3">
                        @foreach($pa['insights'] as $ins)
                        @php
                            $insColors = [
                                'success' => ['bg' => 'bg-emerald-50 dark:bg-emerald-900/20', 'border' => 'border-emerald-200 dark:border-emerald-800', 'icon' => 'text-emerald-500', 'text' => 'text-emerald-700 dark:text-emerald-300'],
                                'warning' => ['bg' => 'bg-amber-50 dark:bg-amber-900/20',   'border' => 'border-amber-200 dark:border-amber-800',   'icon' => 'text-amber-500',   'text' => 'text-amber-700 dark:text-amber-300'],
                                'danger'  => ['bg' => 'bg-rose-50 dark:bg-rose-900/20',     'border' => 'border-rose-200 dark:border-rose-800',     'icon' => 'text-rose-500',    'text' => 'text-rose-700 dark:text-rose-300'],
                            ];
                            $ic = $insColors[$ins['type']] ?? $insColors['warning'];
                        @endphp
                        <div class="flex items-start gap-3 p-3 rounded-xl {{ $ic['bg'] }} border {{ $ic['border'] }}">
                            <x-dynamic-component :component="'lucide-'.$ins['icon']" class="w-4 h-4 {{ $ic['icon'] }} shrink-0 mt-0.5" />
                            <p class="text-xs {{ $ic['text'] }} lato-regular leading-relaxed">{{ $ins['text'] }}</p>
                        </div>
                        @endforeach
                        @if(empty($pa['insights']))
                        <p class="text-xs text-slate-400 lato-regular text-center py-4">Nenhum insight disponível</p>
                        @endif
                    </div>
                </div>

                {{-- Projeção headcount --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                    <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2 mb-4">
                        <x-lucide-telescope class="w-4 h-4 text-purple-500" /> Projeção Headcount
                        <span class="ml-auto text-[10px] text-slate-400 lato-regular font-normal">Próximos 3 meses</span>
                    </h3>
                    @if(count($pa['projMonths']) > 0)
                    <div class="space-y-3">
                        @foreach($pa['projMonths'] as $i => $proj)
                        @php $delta = $i === 0 ? ($proj['value'] - $pa['headcount']) : ($proj['value'] - $pa['projMonths'][$i-1]['value']); @endphp
                        <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50 dark:bg-slate-900">
                            <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-purple-500 to-indigo-600 flex items-center justify-center shrink-0">
                                <span class="text-[10px] lato-black text-white">{{ $proj['label'] }}</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm lato-black text-slate-800 dark:text-white">{{ $proj['value'] }} pessoas</p>
                                <p class="text-[10px] lato-regular {{ $delta > 0 ? 'text-emerald-500' : ($delta < 0 ? 'text-rose-500' : 'text-slate-400') }}">
                                    {{ $delta > 0 ? '+' : '' }}{{ $delta }} vs mês anterior
                                </p>
                            </div>
                            @if($delta > 0)
                                <x-lucide-trending-up class="w-4 h-4 text-emerald-500" />
                            @elseif($delta < 0)
                                <x-lucide-trending-down class="w-4 h-4 text-rose-500" />
                            @else
                                <x-lucide-minus class="w-4 h-4 text-slate-400" />
                            @endif
                        </div>
                        @endforeach
                    </div>
                    <p class="text-[10px] text-slate-400 lato-regular mt-3 flex items-center gap-1">
                        <x-lucide-info class="w-3 h-3" />
                        Projeção baseada na média de variação dos últimos 3 meses
                    </p>
                    @else
                    <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                        <x-lucide-telescope class="w-10 h-10 mb-2 opacity-30" />
                        <p class="text-xs lato-regular">Dados insuficientes para projeção</p>
                    </div>
                    @endif
                </div>

            </div>

        </div>
        @endif

</div>{{-- fim root Livewire --}}
