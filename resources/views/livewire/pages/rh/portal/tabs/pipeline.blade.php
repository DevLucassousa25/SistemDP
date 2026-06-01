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
                                $cColors  = ['bg-indigo-500','bg-indigo-600','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500'];
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
                $pdColors = ['bg-indigo-500','bg-indigo-600','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500'];
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
                            $cvVColors = ['bg-indigo-500','bg-indigo-600','bg-blue-500','bg-teal-500','bg-emerald-500'];
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

