<div class="p-4 sm:p-6 lg:p-8"
     x-data="{ activeTab: 'lista' }"
     x-effect="document.body.style.overflow = ($wire.modalOpen || $wire.viewModal || $wire.confirmDelete) ? 'hidden' : ''">

    @once
        <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endonce

    {{-- ───── Cabeçalho ─────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white lato-black tracking-tight">
                Feedbacks
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5 lato-regular">
                Reconhecimentos, sugestões e alertas sobre colaboradores
            </p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            {{-- Exportar --}}
            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                <button type="button" @click="open = !open"
                        class="flex items-center gap-1.5 px-4 py-2.5 text-sm text-slate-600 dark:text-slate-300
                               bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700
                               rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer lato-bold">
                    <x-lucide-download class="w-4 h-4" />
                    Exportar
                    <x-lucide-chevron-down class="w-3.5 h-3.5 transition-transform" x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" x-cloak
                     class="absolute right-0 mt-1.5 z-30 w-48 bg-white dark:bg-slate-800 border border-slate-200
                            dark:border-slate-700 rounded-xl shadow-lg overflow-hidden">
                    <a href="{{ route('feedback.export.pdf') }}" target="_blank"
                       class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 dark:text-slate-200
                              hover:bg-slate-50 dark:hover:bg-slate-700 transition lato-regular">
                        <x-lucide-file-text class="w-4 h-4 text-rose-500" />
                        Exportar PDF
                    </a>
                    <a href="{{ route('feedback.export.excel') }}"
                       class="flex items-center gap-2 px-4 py-3 text-sm text-slate-700 dark:text-slate-200
                              hover:bg-slate-50 dark:hover:bg-slate-700 transition lato-regular
                              border-t border-slate-100 dark:border-slate-700">
                        <x-lucide-table-2 class="w-4 h-4 text-emerald-500" />
                        Exportar Excel
                    </a>
                </div>
            </div>

            <button type="button" wire:click="abrirModal"
                    class="flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                           text-sm font-medium px-4 py-2.5 rounded-lg shadow-sm shadow-blue-500/20 transition cursor-pointer lato-bold">
                <x-lucide-plus class="w-4 h-4" />
                Novo feedback
            </button>
        </div>
    </div>

    {{-- ───── Cards de estatísticas ─────────────────────────────────── --}}
    @php $s = $this->stats; @endphp

    <div class="mt-8 grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-3 sm:gap-4">
        <div class="bg-[#F1F5F9] dark:bg-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600 dark:text-slate-400 lato-regular">Total</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $s['total'] }}</p>
            </div>
            <x-lucide-message-square class="w-9 h-9 text-slate-400" />
        </div>
        <div class="bg-[#D1FAE5] dark:bg-emerald-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600 dark:text-emerald-200 lato-regular">Reconhecimentos</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $s['reconhecimentos'] }}</p>
            </div>
            <x-lucide-star class="w-9 h-9 text-emerald-500" />
        </div>
        <div class="bg-[#FEE2E2] dark:bg-red-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600 dark:text-red-200 lato-regular">Alertas</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $s['alertas'] }}</p>
            </div>
            <x-lucide-alert-triangle class="w-9 h-9 text-red-500" />
        </div>
        <div class="bg-[#FEF2F2] dark:bg-red-950/30 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-gray-600 dark:text-red-200 lato-regular">Críticos</p>
                <p class="text-2xl sm:text-3xl font-bold text-red-600 dark:text-red-400 mt-1 lato-black">{{ $s['criticos'] }}</p>
            </div>
            <x-lucide-siren class="w-9 h-9 text-red-600" />
        </div>
        <div class="bg-[#FEF3C7] dark:bg-amber-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between col-span-2 sm:col-span-1">
            <div>
                <p class="text-xs text-gray-600 dark:text-amber-200 lato-regular">Pendentes</p>
                <p class="text-2xl sm:text-3xl font-bold text-gray-900 dark:text-white mt-1 lato-black">{{ $s['pendentes'] }}</p>
            </div>
            <x-lucide-clock class="w-9 h-9 text-amber-500" />
        </div>
    </div>

    {{-- ───── Tabs ────────────────────────────────────────────────────── --}}
    <div class="mt-6 flex items-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 w-fit">
        <button type="button" @click="activeTab = 'lista'"
                :class="activeTab === 'lista' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                class="px-4 py-2 text-xs lato-bold rounded-lg transition cursor-pointer">
            Lista
        </button>
        <button type="button" @click="activeTab = 'analytics'"
                :class="activeTab === 'analytics' ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200'"
                class="px-4 py-2 text-xs lato-bold rounded-lg transition cursor-pointer">
            Analytics
        </button>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: LISTA
    ═══════════════════════════════════════════════════════════════════ --}}
    <div x-show="activeTab === 'lista'">

        {{-- Filtros --}}
        <div class="mt-4 bg-white dark:bg-slate-800 border border-gray-100 dark:border-slate-700 rounded-xl p-4 flex flex-col gap-3">

            {{-- Busca --}}
            <div class="relative w-full">
                <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 pointer-events-none" />
                <input type="text" wire:model.live.debounce.300ms="search"
                       placeholder="Buscar por funcionário ou avaliador..."
                       class="w-full pl-10 pr-9 py-2.5 text-sm border border-gray-200 dark:border-slate-600
                              rounded-xl bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                              placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-emerald-500
                              focus:border-transparent lato-regular transition" />
                @if ($search)
                    <button wire:click="$set('search', '')"
                            class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 cursor-pointer">
                        <x-lucide-x class="w-3.5 h-3.5" />
                    </button>
                @endif
            </div>

            {{-- Linha de filtros --}}
            <div x-data="{
                typeMap:          { reconhecimento: 'Reconhecimento', sugestao: 'Sugestão', alerta: 'Alerta' },
                categoryMap:      { comportamento: 'Comportamento', desempenho: 'Desempenho', pontualidade: 'Pontualidade', trabalho_em_equipe: 'Trab. em equipe', comunicacao: 'Comunicação', lideranca: 'Liderança', outros: 'Outros' },
                statusMap:        { aberto: 'Aberto', em_analise: 'Em análise', aguardando_plano: 'Aguard. plano', plano_em_andamento: 'Plano andamento', resolvido: 'Resolvido', arquivado: 'Arquivado' },
                severityMap:      { baixo: 'Baixo', medio: 'Médio', alto: 'Alto', critico: 'Crítico' },
                periodMap:        { este_mes: 'Este mês', ultimo_mes: 'Último mês', ultimos_3_meses: 'Últimos 3 meses', este_ano: 'Este ano' },
                actionPlanMap:    { com_plano: 'Com plano', sem_plano: 'Sem plano', plano_vencido: 'Plano vencido' },
                openType: false, openCategory: false, openStatus: false,
                openSeverity: false, openPeriod: false, openActionPlan: false,
            }"
                 class="grid grid-cols-2 sm:grid-cols-4 gap-2">

                {{-- Tipo --}}
                <div class="relative" @click.away="openType = false">
                    <button type="button" @click="openType = !openType"
                            :class="$wire.typeFilter ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500'"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer dark:text-slate-300">
                        <div class="flex items-center gap-1.5 truncate">
                            <x-lucide-layout-list class="w-3.5 h-3.5 flex-shrink-0" />
                            <span class="truncate text-xs lato-bold" x-text="$wire.typeFilter ? typeMap[$wire.typeFilter] : 'Tipo'"></span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="openType ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openType" x-cloak x-transition
                         class="absolute mt-1 w-48 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                        <button type="button" @click="openType=false; $wire.set('typeFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 flex items-center gap-2 lato-regular">
                            <x-lucide-layout-list class="w-3.5 h-3.5" /> Todos os tipos
                        </button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        <button type="button" @click="openType=false; $wire.set('typeFilter', 'reconhecimento')"
                                :class="$wire.typeFilter === 'reconhecimento' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-star class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" /> Reconhecimento</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" x-show="$wire.typeFilter === 'reconhecimento'" />
                        </button>
                        <button type="button" @click="openType=false; $wire.set('typeFilter', 'sugestao')"
                                :class="$wire.typeFilter === 'sugestao' ? 'bg-blue-50 text-blue-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-lightbulb class="w-3.5 h-3.5 text-blue-500 flex-shrink-0" /> Sugestão</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-blue-500" x-show="$wire.typeFilter === 'sugestao'" />
                        </button>
                        <button type="button" @click="openType=false; $wire.set('typeFilter', 'alerta')"
                                :class="$wire.typeFilter === 'alerta' ? 'bg-red-50 text-red-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-alert-triangle class="w-3.5 h-3.5 text-red-500 flex-shrink-0" /> Alerta</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-red-500" x-show="$wire.typeFilter === 'alerta'" />
                        </button>
                    </div>
                </div>

                {{-- Categoria --}}
                <div class="relative" @click.away="openCategory = false">
                    <button type="button" @click="openCategory = !openCategory"
                            :class="$wire.categoryFilter ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500'"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer dark:text-slate-300">
                        <div class="flex items-center gap-1.5 truncate">
                            <x-lucide-tag class="w-3.5 h-3.5 flex-shrink-0" />
                            <span class="truncate text-xs lato-bold" x-text="$wire.categoryFilter ? categoryMap[$wire.categoryFilter] : 'Categoria'"></span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="openCategory ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openCategory" x-cloak x-transition
                         class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                        <button type="button" @click="openCategory=false; $wire.set('categoryFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 flex items-center gap-2 lato-regular">
                            <x-lucide-tag class="w-3.5 h-3.5" /> Todas as categorias
                        </button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        @foreach (['comportamento'=>'Comportamento','desempenho'=>'Desempenho','pontualidade'=>'Pontualidade','trabalho_em_equipe'=>'Trabalho em equipe','comunicacao'=>'Comunicação','lideranca'=>'Liderança','outros'=>'Outros'] as $catVal => $catLabel)
                        <button type="button" @click="openCategory=false; $wire.set('categoryFilter', '{{ $catVal }}')"
                                :class="$wire.categoryFilter === '{{ $catVal }}' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            {{ $catLabel }}
                            <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" x-show="$wire.categoryFilter === '{{ $catVal }}'" />
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Status --}}
                <div class="relative" @click.away="openStatus = false">
                    <button type="button" @click="openStatus = !openStatus"
                            :class="$wire.statusFilter ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500'"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer dark:text-slate-300">
                        <div class="flex items-center gap-1.5 truncate">
                            <x-lucide-activity class="w-3.5 h-3.5 flex-shrink-0" />
                            <span class="truncate text-xs lato-bold" x-text="$wire.statusFilter ? statusMap[$wire.statusFilter] : 'Status'"></span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="openStatus ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openStatus" x-cloak x-transition
                         class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                        <button type="button" @click="openStatus=false; $wire.set('statusFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 flex items-center gap-2 lato-regular">
                            <x-lucide-activity class="w-3.5 h-3.5" /> Todos os status
                        </button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        @foreach ([
                            'aberto'             => 'Aberto',
                            'em_analise'         => 'Em análise',
                            'aguardando_plano'   => 'Aguard. plano',
                            'plano_em_andamento' => 'Plano andamento',
                            'resolvido'          => 'Resolvido',
                            'arquivado'          => 'Arquivado',
                        ] as $stVal => $stLabel)
                        <button type="button" @click="openStatus=false; $wire.set('statusFilter', '{{ $stVal }}')"
                                :class="$wire.statusFilter === '{{ $stVal }}' ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            {{ $stLabel }}
                            <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" x-show="$wire.statusFilter === '{{ $stVal }}'" />
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Severidade --}}
                <div class="relative" @click.away="openSeverity = false">
                    <button type="button" @click="openSeverity = !openSeverity"
                            :class="$wire.severityFilter ? 'border-orange-400 bg-orange-50 text-orange-700' : 'border-gray-200 text-gray-500'"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer dark:text-slate-300">
                        <div class="flex items-center gap-1.5 truncate">
                            <x-lucide-flame class="w-3.5 h-3.5 flex-shrink-0" />
                            <span class="truncate text-xs lato-bold" x-text="$wire.severityFilter ? severityMap[$wire.severityFilter] : 'Severidade'"></span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="openSeverity ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openSeverity" x-cloak x-transition
                         class="absolute mt-1 w-44 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                        <button type="button" @click="openSeverity=false; $wire.set('severityFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 flex items-center gap-2 lato-regular">
                            <x-lucide-flame class="w-3.5 h-3.5" /> Todas
                        </button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        <button type="button" @click="openSeverity=false; $wire.set('severityFilter', 'baixo')"
                                :class="$wire.severityFilter === 'baixo' ? 'bg-orange-50 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-shield-check class="w-3.5 h-3.5 text-green-500 flex-shrink-0" /> Baixo</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-orange-500" x-show="$wire.severityFilter === 'baixo'" />
                        </button>
                        <button type="button" @click="openSeverity=false; $wire.set('severityFilter', 'medio')"
                                :class="$wire.severityFilter === 'medio' ? 'bg-orange-50 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-shield class="w-3.5 h-3.5 text-yellow-500 flex-shrink-0" /> Médio</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-orange-500" x-show="$wire.severityFilter === 'medio'" />
                        </button>
                        <button type="button" @click="openSeverity=false; $wire.set('severityFilter', 'alto')"
                                :class="$wire.severityFilter === 'alto' ? 'bg-orange-50 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-shield-alert class="w-3.5 h-3.5 text-orange-500 flex-shrink-0" /> Alto</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-orange-500" x-show="$wire.severityFilter === 'alto'" />
                        </button>
                        <button type="button" @click="openSeverity=false; $wire.set('severityFilter', 'critico')"
                                :class="$wire.severityFilter === 'critico' ? 'bg-orange-50 text-orange-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-siren class="w-3.5 h-3.5 text-red-600 flex-shrink-0" /> Crítico</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-orange-500" x-show="$wire.severityFilter === 'critico'" />
                        </button>
                    </div>
                </div>

                {{-- Período --}}
                <div class="relative" @click.away="openPeriod = false">
                    <button type="button" @click="openPeriod = !openPeriod"
                            :class="$wire.periodFilter ? 'border-sky-400 bg-sky-50 text-sky-700' : 'border-gray-200 text-gray-500'"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer dark:text-slate-300">
                        <div class="flex items-center gap-1.5 truncate">
                            <x-lucide-calendar-range class="w-3.5 h-3.5 flex-shrink-0" />
                            <span class="truncate text-xs lato-bold" x-text="$wire.periodFilter ? periodMap[$wire.periodFilter] : 'Período'"></span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="openPeriod ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openPeriod" x-cloak x-transition
                         class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                        <button type="button" @click="openPeriod=false; $wire.set('periodFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 flex items-center gap-2 lato-regular">
                            <x-lucide-calendar-range class="w-3.5 h-3.5" /> Qualquer período
                        </button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        @foreach ([
                            'este_mes'        => 'Este mês',
                            'ultimo_mes'      => 'Último mês',
                            'ultimos_3_meses' => 'Últimos 3 meses',
                            'este_ano'        => 'Este ano',
                        ] as $perVal => $perLabel)
                        <button type="button" @click="openPeriod=false; $wire.set('periodFilter', '{{ $perVal }}')"
                                :class="$wire.periodFilter === '{{ $perVal }}' ? 'bg-sky-50 text-sky-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            {{ $perLabel }}
                            <x-lucide-check class="w-3.5 h-3.5 text-sky-500" x-show="$wire.periodFilter === '{{ $perVal }}'" />
                        </button>
                        @endforeach
                    </div>
                </div>

                {{-- Plano de ação --}}
                <div class="relative" @click.away="openActionPlan = false">
                    <button type="button" @click="openActionPlan = !openActionPlan"
                            :class="$wire.actionPlanFilter ? 'border-rose-400 bg-rose-50 text-rose-700' : 'border-gray-200 text-gray-500'"
                            class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white dark:bg-slate-700 transition cursor-pointer dark:text-slate-300">
                        <div class="flex items-center gap-1.5 truncate">
                            <x-lucide-clipboard-check class="w-3.5 h-3.5 flex-shrink-0" />
                            <span class="truncate text-xs lato-bold" x-text="$wire.actionPlanFilter ? actionPlanMap[$wire.actionPlanFilter] : 'Plano de ação'"></span>
                        </div>
                        <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform" x-bind:class="openActionPlan ? 'rotate-180' : ''" />
                    </button>
                    <div x-show="openActionPlan" x-cloak x-transition
                         class="absolute mt-1 w-52 bg-white dark:bg-slate-800 border border-gray-200 dark:border-slate-700 rounded-xl shadow-lg z-30 py-1 overflow-hidden">
                        <button type="button" @click="openActionPlan=false; $wire.set('actionPlanFilter', '')"
                                class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 dark:hover:bg-slate-700 text-gray-500 flex items-center gap-2 lato-regular">
                            <x-lucide-clipboard-check class="w-3.5 h-3.5" /> Todos
                        </button>
                        <div class="border-t border-gray-100 dark:border-slate-700 my-1"></div>
                        <button type="button" @click="openActionPlan=false; $wire.set('actionPlanFilter', 'com_plano')"
                                :class="$wire.actionPlanFilter === 'com_plano' ? 'bg-rose-50 text-rose-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-circle-check-big class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" /> Com plano</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-rose-500" x-show="$wire.actionPlanFilter === 'com_plano'" />
                        </button>
                        <button type="button" @click="openActionPlan=false; $wire.set('actionPlanFilter', 'sem_plano')"
                                :class="$wire.actionPlanFilter === 'sem_plano' ? 'bg-rose-50 text-rose-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-circle-minus class="w-3.5 h-3.5 text-slate-400 flex-shrink-0" /> Sem plano</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-rose-500" x-show="$wire.actionPlanFilter === 'sem_plano'" />
                        </button>
                        <button type="button" @click="openActionPlan=false; $wire.set('actionPlanFilter', 'plano_vencido')"
                                :class="$wire.actionPlanFilter === 'plano_vencido' ? 'bg-rose-50 text-rose-700 font-semibold' : 'text-gray-700 hover:bg-gray-50'"
                                class="w-full text-left px-3 py-2 text-sm flex items-center justify-between lato-regular dark:text-slate-200 dark:hover:bg-slate-700">
                            <span class="flex items-center gap-1.5"><x-lucide-circle-alert class="w-3.5 h-3.5 text-amber-500 flex-shrink-0" /> Plano vencido</span>
                            <x-lucide-check class="w-3.5 h-3.5 text-rose-500" x-show="$wire.actionPlanFilter === 'plano_vencido'" />
                        </button>
                    </div>
                </div>

                {{-- Limpar --}}
                <button type="button" wire:click="limparFiltros"
                        x-show="$wire.search || $wire.typeFilter || $wire.statusFilter || $wire.categoryFilter || $wire.severityFilter || $wire.periodFilter || $wire.actionPlanFilter"
                        class="w-full flex items-center justify-center gap-1.5 px-3 py-2.5 text-xs lato-bold rounded-xl
                               border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700
                               text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-600
                               hover:text-slate-700 transition cursor-pointer">
                    <x-lucide-x class="w-3.5 h-3.5" />
                    Limpar filtros
                </button>

            </div>

            {{-- Chips de filtros ativos --}}
            @if ($search || $typeFilter || $statusFilter || $categoryFilter || $severityFilter || $periodFilter || $actionPlanFilter)
            <div class="flex items-center gap-2 flex-wrap pt-1">
                <span class="text-xs text-gray-400 lato-regular">Filtros:</span>

                @if ($search)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold">
                        <x-lucide-search class="w-3 h-3" />
                        "{{ Str::limit($search, 20) }}"
                        <button wire:click="$set('search', '')" class="ml-0.5 hover:text-slate-900 dark:hover:text-white transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($typeFilter)
                    @php
                        $typeChipStyle = match($typeFilter) {
                            'reconhecimento' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                            'sugestao'       => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'alerta'         => 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                            default          => 'bg-slate-100 text-slate-600',
                        };
                        $typeChipLabel = match($typeFilter) {
                            'reconhecimento' => 'Reconhecimento',
                            'sugestao'       => 'Sugestão',
                            'alerta'         => 'Alerta',
                            default          => $typeFilter,
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs lato-bold {{ $typeChipStyle }}">
                        @if ($typeFilter === 'reconhecimento')
                            <x-lucide-star class="w-3 h-3" />
                        @elseif ($typeFilter === 'sugestao')
                            <x-lucide-lightbulb class="w-3 h-3" />
                        @elseif ($typeFilter === 'alerta')
                            <x-lucide-alert-triangle class="w-3 h-3" />
                        @endif
                        {{ $typeChipLabel }}
                        <button wire:click="$set('typeFilter', '')" class="ml-0.5 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($categoryFilter)
                    @php
                        $catChipLabel = match($categoryFilter) {
                            'comportamento'      => 'Comportamento',
                            'desempenho'         => 'Desempenho',
                            'pontualidade'       => 'Pontualidade',
                            'trabalho_em_equipe' => 'Trabalho em equipe',
                            'comunicacao'        => 'Comunicação',
                            'lideranca'          => 'Liderança',
                            'outros'             => 'Outros',
                            default              => $categoryFilter,
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 dark:bg-indigo-900/30 dark:text-indigo-300 text-xs lato-bold">
                        <x-lucide-tag class="w-3 h-3" />
                        {{ $catChipLabel }}
                        <button wire:click="$set('categoryFilter', '')" class="ml-0.5 hover:text-indigo-900 dark:hover:text-indigo-100 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($statusFilter)
                    @php
                        $stChipStyle = match($statusFilter) {
                            'aberto'             => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',
                            'em_analise'         => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',
                            'aguardando_plano'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                            'plano_em_andamento' => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300',
                            'resolvido'          => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300',
                            'arquivado'          => 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-300',
                            default              => 'bg-slate-100 text-slate-600',
                        };
                        $stChipLabel = match($statusFilter) {
                            'aberto'             => 'Aberto',
                            'em_analise'         => 'Em análise',
                            'aguardando_plano'   => 'Aguard. plano',
                            'plano_em_andamento' => 'Plano andamento',
                            'resolvido'          => 'Resolvido',
                            'arquivado'          => 'Arquivado',
                            default              => $statusFilter,
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs lato-bold {{ $stChipStyle }}">
                        <x-lucide-activity class="w-3 h-3" />
                        {{ $stChipLabel }}
                        <button wire:click="$set('statusFilter', '')" class="ml-0.5 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($severityFilter)
                    @php
                        $sevChipStyle = match($severityFilter) {
                            'baixo'  => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-300',
                            'medio'  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-900/30 dark:text-yellow-300',
                            'alto'   => 'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-300',
                            'critico'=> 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300',
                            default  => 'bg-slate-100 text-slate-600',
                        };
                        $sevChipLabel = match($severityFilter) {
                            'baixo'  => 'Baixo',
                            'medio'  => 'Médio',
                            'alto'   => 'Alto',
                            'critico'=> 'Crítico',
                            default  => $severityFilter,
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs lato-bold {{ $sevChipStyle }}">
                        @if ($severityFilter === 'baixo')
                            <x-lucide-shield-check class="w-3 h-3" />
                        @elseif ($severityFilter === 'medio')
                            <x-lucide-shield class="w-3 h-3" />
                        @elseif ($severityFilter === 'alto')
                            <x-lucide-shield-alert class="w-3 h-3" />
                        @elseif ($severityFilter === 'critico')
                            <x-lucide-siren class="w-3 h-3" />
                        @endif
                        {{ $sevChipLabel }}
                        <button wire:click="$set('severityFilter', '')" class="ml-0.5 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($periodFilter)
                    @php
                        $perChipLabel = match($periodFilter) {
                            'este_mes'        => 'Este mês',
                            'ultimo_mes'      => 'Último mês',
                            'ultimos_3_meses' => 'Últimos 3 meses',
                            'este_ano'        => 'Este ano',
                            default           => $periodFilter,
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-sky-100 text-sky-700 dark:bg-sky-900/30 dark:text-sky-300 text-xs lato-bold">
                        <x-lucide-calendar-range class="w-3 h-3" />
                        {{ $perChipLabel }}
                        <button wire:click="$set('periodFilter', '')" class="ml-0.5 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($actionPlanFilter)
                    @php
                        $apChipLabel = match($actionPlanFilter) {
                            'com_plano'    => 'Com plano',
                            'sem_plano'    => 'Sem plano',
                            'plano_vencido'=> 'Plano vencido',
                            default        => $actionPlanFilter,
                        };
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-rose-100 text-rose-700 dark:bg-rose-900/30 dark:text-rose-300 text-xs lato-bold">
                        @if ($actionPlanFilter === 'com_plano')
                            <x-lucide-circle-check-big class="w-3 h-3" />
                        @elseif ($actionPlanFilter === 'sem_plano')
                            <x-lucide-circle-minus class="w-3 h-3" />
                        @elseif ($actionPlanFilter === 'plano_vencido')
                            <x-lucide-circle-alert class="w-3 h-3" />
                        @endif
                        {{ $apChipLabel }}
                        <button wire:click="$set('actionPlanFilter', '')" class="ml-0.5 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif
            </div>
            @endif

        </div>

        {{-- Lista de feedbacks --}}
        <div class="mt-4 space-y-3">
            @forelse ($this->feedbacks as $fb)
                @php
                    $isRecurrent = in_array($fb->employee_id . '-' . $fb->category, $this->recurrentAlerts);

                    $typeStyle = match($fb->type) {
                        'reconhecimento' => 'background:#ecfdf5;color:#065f46;border-color:#6ee7b7',
                        'sugestao'       => 'background:#eff6ff;color:#1e40af;border-color:#93c5fd',
                        default          => 'background:#fef2f2;color:#991b1b;border-color:#fca5a5',
                    };
                    $typeIcon = match($fb->type) {
                        'reconhecimento' => 'star',
                        'sugestao'       => 'lightbulb',
                        default          => 'alert-triangle',
                    };

                    $sevStyle = match($fb->severity) {
                        'baixo'  => 'background:#ecfdf5;color:#166534',
                        'medio'  => 'background:#fefce8;color:#854d0e',
                        'alto'   => 'background:#fff7ed;color:#9a3412',
                        'critico'=> 'background:#fef2f2;color:#7f1d1d',
                        default  => '',
                    };
                    $stStyle = match($fb->status) {
                        'aberto'             => 'background:#eff6ff;color:#1e40af',
                        'em_analise'         => 'background:#fefce8;color:#854d0e',
                        'aguardando_plano'   => 'background:#fff7ed;color:#9a3412',
                        'plano_em_andamento' => 'background:#f5f3ff;color:#5b21b6',
                        'resolvido'          => 'background:#ecfdf5;color:#166534',
                        'arquivado'          => 'background:#f1f5f9;color:#475569',
                        default              => 'background:#f1f5f9;color:#475569',
                    };
                @endphp

                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700
                            p-4 sm:p-5 hover:shadow-md transition-shadow
                            {{ $isRecurrent ? 'border-l-4 border-l-red-500' : '' }}">
                    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">

                        <div class="flex-1 min-w-0">

                            {{-- Badges --}}
                            <div class="flex flex-wrap items-center gap-1.5 mb-2.5">
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold lato-bold px-2 py-0.5 rounded-full border"
                                      style="{{ $typeStyle }}">
                                    <x-dynamic-component :component="'lucide-' . $typeIcon" class="w-3 h-3" />
                                    {{ $fb->type_label }}
                                </span>

                                @if ($fb->type === 'alerta' && $fb->severity)
                                    <span class="text-[11px] font-bold lato-bold px-2 py-0.5 rounded-full"
                                          style="{{ $sevStyle }}">
                                        {{ strtoupper($fb->severity_label) }}
                                    </span>
                                @endif

                                <span class="text-[11px] text-slate-500 dark:text-slate-400 lato-regular px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700">
                                    {{ $fb->category_label }}
                                </span>

                                <span class="text-[11px] font-semibold lato-bold px-2 py-0.5 rounded-full"
                                      style="{{ $stStyle }}">
                                    {{ $fb->status_label }}
                                </span>

                                @if ($isRecurrent)
                                    <span class="inline-flex items-center gap-0.5 text-[10px] font-bold lato-bold px-1.5 py-0.5 rounded bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400">
                                        <x-lucide-repeat class="w-2.5 h-2.5" />
                                        Recorrente
                                    </span>
                                @endif
                            </div>

                            {{-- Funcionário --}}
                            <div class="flex items-center gap-2 mb-1.5">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-400 to-slate-600
                                            flex items-center justify-center text-white text-[10px] font-bold lato-black shrink-0">
                                    {{ mb_strtoupper(mb_substr($fb->employee?->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white lato-bold leading-tight">
                                        {{ $fb->employee?->name ?? '—' }}
                                    </p>
                                    <p class="text-[11px] text-slate-400 lato-regular">
                                        {{ $fb->employee?->department?->name ?? '—' }}
                                        @if ($fb->employee?->position) · {{ $fb->employee->position }} @endif
                                    </p>
                                </div>
                            </div>

                            {{-- Mensagem preview --}}
                            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular line-clamp-2 mt-2">
                                {{ $fb->message }}
                            </p>

                            {{-- Rodapé --}}
                            <div class="flex flex-wrap items-center gap-3 mt-3 text-[11px] text-slate-400 lato-regular">
                                <span class="flex items-center gap-1">
                                    <x-lucide-user class="w-3 h-3" />
                                    {{ $fb->evaluator_name }}
                                </span>
                                <span class="flex items-center gap-1">
                                    <x-lucide-calendar class="w-3 h-3" />
                                    {{ $fb->occurred_at->format('d/m/Y') }}
                                </span>
                                @if ($fb->rating)
                                    <span>
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span style="color: {{ $i <= $fb->rating ? '#f59e0b' : '#e2e8f0' }}">★</span>
                                        @endfor
                                    </span>
                                @endif
                                @if ($fb->action_plan_deadline)
                                    <span class="flex items-center gap-1 {{ $fb->action_plan_deadline->isPast() && $fb->status !== 'resolvido' ? 'text-red-500' : '' }}">
                                        <x-lucide-clock class="w-3 h-3" />
                                        Prazo: {{ $fb->action_plan_deadline->format('d/m/Y') }}
                                    </span>
                                @endif
                                @if (!empty($fb->attachments))
                                    <span class="flex items-center gap-1">
                                        <x-lucide-paperclip class="w-3 h-3" />
                                        {{ count($fb->attachments) }}
                                    </span>
                                @endif
                            </div>
                        </div>

                        {{-- Ações --}}
                        <div class="flex items-center gap-1.5 shrink-0">
                            {{-- Status dropdown --}}
                            <div class="relative" x-data="{ open: false }" @click.away="open = false">
                                <button type="button" @click="open = !open"
                                        class="flex items-center gap-1.5 px-2.5 py-1.5 text-xs lato-bold rounded-lg border
                                               border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800
                                               text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700
                                               hover:border-slate-300 transition cursor-pointer">
                                    <x-lucide-circle-dot class="w-3 h-3" />
                                    Mover status
                                    <x-lucide-chevron-down class="w-3 h-3 opacity-50" x-bind:class="open ? 'rotate-180' : ''" />
                                </button>
                                <div x-show="open" x-cloak x-transition
                                     class="absolute right-0 mt-1.5 z-20 w-52 bg-white dark:bg-slate-800 border border-slate-200
                                            dark:border-slate-700 rounded-xl shadow-xl overflow-hidden">
                                    <div class="px-3 py-2 border-b border-slate-100 dark:border-slate-700">
                                        <p class="text-[10px] font-bold lato-bold text-slate-400 uppercase tracking-wider">Alterar status</p>
                                    </div>
                                    @foreach ([
                                        'aberto'             => ['Aberto',              'w-1.5 h-1.5 rounded-full bg-blue-400',    'hover:bg-blue-50 dark:hover:bg-blue-900/20',   'text-blue-700 dark:text-blue-300'],
                                        'em_analise'         => ['Em análise',          'w-1.5 h-1.5 rounded-full bg-amber-400',   'hover:bg-amber-50 dark:hover:bg-amber-900/20', 'text-amber-700 dark:text-amber-300'],
                                        'aguardando_plano'   => ['Aguard. plano',       'w-1.5 h-1.5 rounded-full bg-orange-400',  'hover:bg-orange-50 dark:hover:bg-orange-900/20','text-orange-700 dark:text-orange-300'],
                                        'plano_em_andamento' => ['Plano em andamento',  'w-1.5 h-1.5 rounded-full bg-violet-400',  'hover:bg-violet-50 dark:hover:bg-violet-900/20','text-violet-700 dark:text-violet-300'],
                                        'resolvido'          => ['Resolvido',           'w-1.5 h-1.5 rounded-full bg-emerald-400', 'hover:bg-emerald-50 dark:hover:bg-emerald-900/20','text-emerald-700 dark:text-emerald-300'],
                                        'arquivado'          => ['Arquivado',           'w-1.5 h-1.5 rounded-full bg-slate-400',   'hover:bg-slate-50 dark:hover:bg-slate-700',    'text-slate-500 dark:text-slate-400'],
                                    ] as $val => [$label, $dot, $hoverBg, $activeText])
                                    <button type="button"
                                            wire:click="atualizarStatus({{ $fb->id }}, '{{ $val }}')"
                                            @click="open = false"
                                            class="w-full flex items-center justify-between px-3 py-2.5 text-xs lato-regular transition cursor-pointer
                                                   {{ $fb->status === $val
                                                       ? 'font-bold lato-bold '.$activeText.' bg-slate-50 dark:bg-slate-700/60'
                                                       : 'text-slate-600 dark:text-slate-300 '.$hoverBg }}">
                                        <span class="flex items-center gap-2">
                                            <span class="{{ $dot }}"></span>
                                            {{ $label }}
                                        </span>
                                        @if ($fb->status === $val)
                                            <x-lucide-check class="w-3.5 h-3.5 shrink-0" />
                                        @endif
                                    </button>
                                    @endforeach
                                </div>
                            </div>

                            <button type="button" wire:click="ver({{ $fb->id }})" title="Ver detalhes"
                                    class="p-2 text-slate-400 hover:text-slate-700 dark:hover:text-white
                                           hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition cursor-pointer">
                                <x-lucide-eye class="w-4 h-4" />
                            </button>
                            <button type="button" wire:click="editar({{ $fb->id }})" title="Editar"
                                    class="p-2 text-slate-400 hover:text-blue-600 hover:bg-blue-50
                                           dark:hover:bg-blue-900/20 rounded-lg transition cursor-pointer">
                                <x-lucide-pencil class="w-4 h-4" />
                            </button>
                            <button type="button" wire:click="confirmarExclusao({{ $fb->id }})" title="Excluir"
                                    class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50
                                           dark:hover:bg-red-900/20 rounded-lg transition cursor-pointer">
                                <x-lucide-trash-2 class="w-4 h-4" />
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-center bg-white dark:bg-slate-800
                            rounded-2xl border border-slate-100 dark:border-slate-700">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                        <x-lucide-message-square class="w-7 h-7 text-slate-400" />
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400 lato-bold">Nenhum feedback encontrado</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular mt-1 max-w-xs">
                        Ajuste os filtros ou registre um novo feedback
                    </p>
                    @if (trim($search) === '' && ! $typeFilter && ! $statusFilter && ! $categoryFilter)
                        <button type="button" wire:click="abrirModal"
                                class="mt-4 flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                                       text-xs lato-bold px-4 py-2 rounded-lg shadow-sm shadow-blue-500/20 transition cursor-pointer">
                            <x-lucide-plus class="w-3.5 h-3.5" />
                            Novo feedback
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

    </div> {{-- fim lista --}}


    {{-- ═══════════════════════════════════════════════════════════════
         ABA: ANALYTICS
    ═══════════════════════════════════════════════════════════════════ --}}
    @php $ad = $this->analyticsData; @endphp
    <div x-show="activeTab === 'analytics'" x-cloak>

        {{-- ── Linha 0: KPIs de tendência ─────────────────────────── --}}
        <div class="mt-4 grid grid-cols-2 lg:grid-cols-4 gap-3">
            @php
                $trendCards = [
                    ['key'=>'total',          'label'=>'Total este mês',     'icon'=>'message-square',  'color'=>'slate'],
                    ['key'=>'alertas',        'label'=>'Alertas este mês',   'icon'=>'alert-triangle',  'color'=>'red'],
                    ['key'=>'reconhecimentos','label'=>'Reconhecimentos',     'icon'=>'star',            'color'=>'emerald'],
                    ['key'=>'resolvidos',     'label'=>'Resolvidos este mês', 'icon'=>'check-circle-2',  'color'=>'blue'],
                ];
                $colorMap = [
                    'slate'   => ['bg'=>'bg-slate-50 dark:bg-slate-700/50',   'icon'=>'text-slate-400',   'num'=>'text-slate-800 dark:text-white'],
                    'red'     => ['bg'=>'bg-red-50 dark:bg-red-900/20',       'icon'=>'text-red-400',     'num'=>'text-red-600 dark:text-red-400'],
                    'emerald' => ['bg'=>'bg-emerald-50 dark:bg-emerald-900/20','icon'=>'text-emerald-500','num'=>'text-emerald-700 dark:text-emerald-300'],
                    'blue'    => ['bg'=>'bg-blue-50 dark:bg-blue-900/20',     'icon'=>'text-blue-400',    'num'=>'text-blue-600 dark:text-blue-400'],
                ];
            @endphp
            @foreach ($trendCards as $tc)
            @php
                $t   = $ad['trend'][$tc['key']];
                $c   = $colorMap[$tc['color']];
                $up  = $t['diff'] >= 0;
                $pos = $tc['key'] === 'reconhecimentos' || $tc['key'] === 'resolvidos';
                $good = ($pos && $up) || (!$pos && !$up);
            @endphp
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-4 flex flex-col gap-3">
                <div class="flex items-center justify-between">
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-bold">{{ $tc['label'] }}</p>
                    <div class="w-8 h-8 rounded-xl {{ $c['bg'] }} flex items-center justify-center">
                        <x-dynamic-component :component="'lucide-'.$tc['icon']" class="w-4 h-4 {{ $c['icon'] }}" />
                    </div>
                </div>
                <div>
                    <p class="text-3xl font-bold lato-black {{ $c['num'] }}">{{ $t['current'] }}</p>
                    <div class="flex items-center gap-1 mt-1">
                        @if ($t['diff'] !== 0)
                            <span class="inline-flex items-center gap-0.5 text-[10px] font-bold lato-bold px-1.5 py-0.5 rounded-full
                                         {{ $good ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400' }}">
                                @if ($up) ↑ @else ↓ @endif
                                {{ abs($t['diff']) }}%
                            </span>
                        @else
                            <span class="text-[10px] text-slate-400 lato-regular px-1.5">—</span>
                        @endif
                        <span class="text-[10px] text-slate-400 lato-regular">vs mês anterior ({{ $t['previous'] }})</span>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        {{-- ── Linha 1: Tipo + Gravidade ───────────────────────────── --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Distribuição por Tipo</p>
                <div wire:ignore id="chart-by-type" style="min-height:220px"></div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Alertas por Gravidade</p>
                <div wire:ignore id="chart-by-severity" style="min-height:220px"></div>
            </div>
        </div>

        {{-- ── Linha 2: Evolução mensal ────────────────────────────── --}}
        <div class="mt-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Evolução Mensal (últimos 12 meses)</p>
            <div wire:ignore id="chart-monthly" style="min-height:220px"></div>
        </div>

        {{-- ── Linha 3: Proporção por departamento ─────────────────── --}}
        @if (!empty($ad['dept_ratio']))
        <div class="mt-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Reconhecimento vs Alerta por Departamento</p>
            <div wire:ignore id="chart-dept-ratio" style="min-height:260px"></div>
        </div>
        @endif

        {{-- ── Linha 4: Categoria + Status ────────────────────────── --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Por Categoria</p>
                <div wire:ignore id="chart-by-category" style="min-height:240px"></div>
            </div>
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Por Status</p>
                <div wire:ignore id="chart-by-status" style="min-height:240px"></div>
            </div>
        </div>

        {{-- ── Linha 5: Radar de risco + Planos de ação ───────────── --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Radar de risco --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
                <div class="flex items-center gap-2 mb-4">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide flex-1">Radar de Risco Individual</p>
                    <span class="text-[10px] text-slate-400 lato-regular">Score = alertas × severidade</span>
                </div>
                @forelse ($ad['risk_employees'] as $i => $emp)
                @php
                    $maxScore = $ad['risk_employees'][0]['risk_score'] ?? 1;
                    $pct      = $maxScore > 0 ? round(($emp['risk_score'] / $maxScore) * 100) : 0;
                    $riskColor = match(true) {
                        $emp['risk_score'] >= 12 => ['bar'=>'bg-red-500',    'badge'=>'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400',    'label'=>'Crítico'],
                        $emp['risk_score'] >= 6  => ['bar'=>'bg-orange-400', 'badge'=>'bg-orange-100 text-orange-700 dark:bg-orange-900/30 dark:text-orange-400','label'=>'Alto'],
                        $emp['risk_score'] >= 3  => ['bar'=>'bg-amber-400',  'badge'=>'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',  'label'=>'Médio'],
                        default                  => ['bar'=>'bg-emerald-400','badge'=>'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400','label'=>'Baixo'],
                    };
                @endphp
                <div class="flex items-center gap-3 mb-3">
                    <span class="text-xs font-bold text-slate-400 lato-black w-4 shrink-0">{{ $i + 1 }}</span>
                    <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-400 to-slate-600 flex items-center justify-center text-white text-[10px] lato-black shrink-0">
                        {{ mb_strtoupper(mb_substr($emp['name'], 0, 1)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between mb-1">
                            <p class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $emp['name'] }}</p>
                            <span class="text-[10px] lato-bold px-1.5 py-0.5 rounded-full shrink-0 ml-2 {{ $riskColor['badge'] }}">{{ $riskColor['label'] }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <div class="flex-1 h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                <div class="h-full rounded-full {{ $riskColor['bar'] }} transition-all" style="width:{{ $pct }}%"></div>
                            </div>
                            <span class="text-[10px] text-slate-400 lato-regular shrink-0">{{ $emp['total'] }} alertas</span>
                        </div>
                    </div>
                </div>
                @empty
                    <p class="text-sm text-slate-400 lato-regular text-center py-8">Sem alertas registrados</p>
                @endforelse
            </div>

            {{-- Planos de ação + Templates --}}
            <div class="flex flex-col gap-4">

                {{-- Planos de ação --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5 flex-1">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Planos de Ação</p>
                    @php $ap = $ad['action_plans']; @endphp
                    @if ($ap['total'] > 0)
                        <div class="flex items-center gap-4 mb-4">
                            <div class="text-center">
                                <p class="text-3xl font-bold lato-black text-slate-800 dark:text-white">{{ $ap['total'] }}</p>
                                <p class="text-[10px] text-slate-400 lato-regular mt-0.5">Com prazo definido</p>
                            </div>
                            <div class="flex-1 space-y-2.5">
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11px] text-emerald-600 lato-bold">No prazo</span>
                                        <span class="text-[11px] font-bold lato-black text-emerald-600">{{ $ap['on_time'] }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                        <div class="h-full rounded-full bg-emerald-400" style="width:{{ $ap['total'] > 0 ? round(($ap['on_time']/$ap['total'])*100) : 0 }}%"></div>
                                    </div>
                                </div>
                                <div>
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[11px] text-red-500 lato-bold">Vencidos</span>
                                        <span class="text-[11px] font-bold lato-black text-red-500">{{ $ap['overdue'] }}</span>
                                    </div>
                                    <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                        <div class="h-full rounded-full bg-red-400" style="width:{{ $ap['total'] > 0 ? round(($ap['overdue']/$ap['total'])*100) : 0 }}%"></div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <p class="text-sm text-slate-400 lato-regular text-center py-4">Nenhum plano com prazo definido</p>
                    @endif
                </div>

                {{-- Template usage --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5 flex-1">
                    <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-3">Uso de Templates</p>
                    <div wire:ignore id="chart-templates" style="min-height:120px"></div>
                </div>

            </div>
        </div>

        {{-- ── Linha 6: Heatmap por departamento ──────────────────── --}}
        @if (!empty($ad['heatmap']))
        <div class="mt-4 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Heatmap por Departamento</p>
            <div class="overflow-x-auto">
                <table class="w-full text-xs lato-regular">
                    <thead>
                        <tr>
                            <th class="text-left py-2 pr-4 text-slate-400 font-semibold lato-bold w-40">Departamento</th>
                            <th class="text-center py-2 px-3 text-emerald-600 dark:text-emerald-400 font-semibold lato-bold">
                                <span class="inline-flex items-center gap-1 justify-center">
                                    <x-lucide-star class="w-3.5 h-3.5" /> Reconhecimento
                                </span>
                            </th>
                            <th class="text-center py-2 px-3 text-blue-600 dark:text-blue-400 font-semibold lato-bold">
                                <span class="inline-flex items-center gap-1 justify-center">
                                    <x-lucide-lightbulb class="w-3.5 h-3.5" /> Sugestão
                                </span>
                            </th>
                            <th class="text-center py-2 px-3 text-red-600 dark:text-red-400 font-semibold lato-bold">
                                <span class="inline-flex items-center gap-1 justify-center">
                                    <x-lucide-alert-triangle class="w-3.5 h-3.5" /> Alerta
                                </span>
                            </th>
                            <th class="text-center py-2 px-3 text-slate-400 font-semibold lato-bold">Total</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-50 dark:divide-slate-700/50">
                        @php
                            $maxVal = collect($ad['heatmap'])->map(fn($t) => array_sum($t))->max() ?: 1;
                        @endphp
                        @foreach ($ad['heatmap'] as $dept => $types)
                        @php
                            $rec   = $types['reconhecimento'] ?? 0;
                            $sug   = $types['sugestao']       ?? 0;
                            $ale   = $types['alerta']         ?? 0;
                            $total = $rec + $sug + $ale;
                            $intensity = $maxVal > 0 ? round(($total / $maxVal) * 100) : 0;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                            <td class="py-2.5 pr-4 font-semibold text-slate-700 dark:text-slate-200 truncate max-w-[140px]">{{ $dept }}</td>
                            <td class="py-2.5 px-3 text-center">
                                @if ($rec > 0)
                                <span class="inline-flex items-center justify-center w-8 h-6 rounded-lg text-xs font-bold lato-black"
                                      style="background:rgba(16,185,129,{{ $rec / max($maxVal,1) }});color:{{ $rec > $maxVal*0.5 ? '#fff' : '#065f46' }}">
                                    {{ $rec }}
                                </span>
                                @else
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if ($sug > 0)
                                <span class="inline-flex items-center justify-center w-8 h-6 rounded-lg text-xs font-bold lato-black"
                                      style="background:rgba(59,130,246,{{ $sug / max($maxVal,1) }});color:{{ $sug > $maxVal*0.5 ? '#fff' : '#1e40af' }}">
                                    {{ $sug }}
                                </span>
                                @else
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if ($ale > 0)
                                <span class="inline-flex items-center justify-center w-8 h-6 rounded-lg text-xs font-bold lato-black"
                                      style="background:rgba(239,68,68,{{ $ale / max($maxVal,1) }});color:{{ $ale > $maxVal*0.5 ? '#fff' : '#991b1b' }}">
                                    {{ $ale }}
                                </span>
                                @else
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center font-bold text-slate-600 dark:text-slate-300 lato-black">{{ $total }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- ── Linha 7: Funil de resolução + Top funcionários ─────── --}}
        <div class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Funil --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Funil de Resolução</p>
                @php $funnelMax = collect($ad['funnel'])->max('value') ?: 1; @endphp
                <div class="space-y-2.5">
                    @foreach ($ad['funnel'] as $step)
                    @php $fpct = round(($step['value'] / $funnelMax) * 100); @endphp
                    <div>
                        <div class="flex items-center justify-between mb-1">
                            <span class="text-xs lato-bold text-slate-600 dark:text-slate-300">{{ $step['label'] }}</span>
                            <span class="text-xs font-bold lato-black text-slate-700 dark:text-slate-200">{{ $step['value'] }}</span>
                        </div>
                        <div class="h-3 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                            <div class="h-full rounded-full transition-all" style="width:{{ $fpct }}%;background:{{ $step['color'] }}"></div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            {{-- Top funcionários --}}
            @if (!empty($ad['top_employees']))
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 p-5">
                <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-4">Top Colaboradores com Alertas</p>
                <div class="space-y-3">
                    @foreach ($ad['top_employees'] as $i => $emp)
                    <div class="flex items-center gap-3">
                        <span class="text-xs font-bold text-slate-400 lato-black w-4">{{ $i + 1 }}</span>
                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-red-400 to-red-600 flex items-center justify-center text-white text-[10px] font-bold lato-black shrink-0">
                            {{ mb_strtoupper(mb_substr($emp['name'], 0, 1)) }}
                        </div>
                        <p class="text-sm text-slate-700 dark:text-slate-200 lato-regular flex-1">{{ $emp['name'] }}</p>
                        <div class="flex items-center gap-2">
                            <div class="h-2 rounded-full bg-red-400" style="width: {{ min($emp['total'] * 20, 120) }}px"></div>
                            <span class="text-sm font-bold text-slate-700 dark:text-slate-200 lato-black w-6 text-right">{{ $emp['total'] }}</span>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        <script>
        (function () {
            var byType       = @json($ad['by_type']);
            var bySeverity   = @json($ad['by_severity']);
            var monthly      = @json($ad['monthly']);
            var byCategory   = @json($ad['by_category']);
            var byStatus     = @json($ad['by_status']);
            var deptRatio    = @json($ad['dept_ratio']);
            var templateData = @json($ad['template_usage']);

            function initCharts() {
                if (typeof ApexCharts === 'undefined') { setTimeout(initCharts, 80); return; }
                var isDark = document.documentElement.classList.contains('dark');
                var tc = isDark ? '#94a3b8' : '#64748b';
                var gc = isDark ? '#334155' : '#e2e8f0';

                function render(id, opts) {
                    var el = document.getElementById(id);
                    if (!el || el.dataset.apex) return;
                    el.dataset.apex = '1';
                    new ApexCharts(el, Object.assign({ chart: { fontFamily: 'inherit', toolbar: { show: false } } }, opts)).render();
                }

                render('chart-by-type', {
                    chart: { type: 'donut', height: 220 },
                    series: byType.data, labels: byType.labels,
                    colors: ['#10b981', '#3b82f6', '#ef4444'],
                    legend: { position: 'bottom', labels: { colors: tc } },
                    dataLabels: { style: { fontSize: '11px' } },
                    plotOptions: { pie: { donut: { size: '65%' } } },
                });

                render('chart-by-severity', {
                    chart: { type: 'bar', height: 220 },
                    series: [{ name: 'Alertas', data: bySeverity.data }],
                    colors: ['#10b981', '#f59e0b', '#f97316', '#dc2626'],
                    plotOptions: { bar: { distributed: true, borderRadius: 5, columnWidth: '55%' } },
                    xaxis: { categories: bySeverity.labels, labels: { style: { colors: tc } } },
                    yaxis: { labels: { style: { colors: tc } } },
                    legend: { show: false }, grid: { borderColor: gc }, dataLabels: { enabled: true },
                });

                render('chart-monthly', {
                    chart: { type: 'area', height: 220 },
                    series: [{ name: 'Feedbacks', data: monthly.data }],
                    colors: ['#6366f1'], stroke: { curve: 'smooth', width: 2.5 },
                    fill: { type: 'gradient', gradient: { opacityFrom: 0.3, opacityTo: 0.05 } },
                    xaxis: { categories: monthly.labels, labels: { style: { colors: tc, fontSize: '11px' } } },
                    yaxis: { labels: { style: { colors: tc } } },
                    grid: { borderColor: gc }, markers: { size: 4 }, dataLabels: { enabled: false },
                });

                if (deptRatio.length > 0) {
                    render('chart-dept-ratio', {
                        chart: { type: 'bar', height: 260, stacked: true },
                        series: [
                            { name: 'Reconhecimento', data: deptRatio.map(function(d){ return d.reconhecimento; }) },
                            { name: 'Sugestão',       data: deptRatio.map(function(d){ return d.sugestao; }) },
                            { name: 'Alerta',         data: deptRatio.map(function(d){ return d.alerta; }) },
                        ],
                        colors: ['#10b981', '#3b82f6', '#ef4444'],
                        plotOptions: { bar: { horizontal: true, borderRadius: 3, barHeight: '65%' } },
                        xaxis: { categories: deptRatio.map(function(d){ return d.dept; }), labels: { style: { colors: tc, fontSize: '11px' } } },
                        yaxis: { labels: { style: { colors: tc, fontSize: '11px' } } },
                        legend: { position: 'top', labels: { colors: tc }, fontSize: '12px' },
                        grid: { borderColor: gc }, dataLabels: { enabled: false },
                        tooltip: { shared: true, intersect: false },
                    });
                }

                render('chart-by-category', {
                    chart: { type: 'bar', height: 240 },
                    series: [{ name: 'Feedbacks', data: byCategory.data }],
                    colors: ['#6366f1'],
                    plotOptions: { bar: { horizontal: true, borderRadius: 4, barHeight: '60%' } },
                    xaxis: { categories: byCategory.labels, labels: { style: { colors: tc, fontSize: '11px' } } },
                    yaxis: { labels: { style: { colors: tc, fontSize: '11px' } } },
                    grid: { borderColor: gc }, dataLabels: { enabled: true, style: { fontSize: '11px' } },
                });

                render('chart-by-status', {
                    chart: { type: 'donut', height: 240 },
                    series: byStatus.data, labels: byStatus.labels,
                    colors: ['#3b82f6', '#f59e0b', '#f97316', '#8b5cf6', '#10b981', '#94a3b8'],
                    legend: { position: 'bottom', labels: { colors: tc }, fontSize: '11px' },
                    dataLabels: { style: { fontSize: '10px' } },
                    plotOptions: { pie: { donut: { size: '60%' } } },
                });

                if (templateData.data && templateData.data.length > 0) {
                    render('chart-templates', {
                        chart: { type: 'donut', height: 120 },
                        series: templateData.data, labels: templateData.labels,
                        colors: ['#64748b', '#8b5cf6', '#3b82f6', '#10b981', '#f59e0b', '#f43f5e'],
                        legend: { position: 'right', labels: { colors: tc }, fontSize: '10px' },
                        dataLabels: { enabled: false },
                        plotOptions: { pie: { donut: { size: '70%' } } },
                    });
                }
            }

            initCharts();
        }());
        </script>

        <div class="pb-4"></div>
    </div> {{-- fim analytics --}}

    {{-- ═══════════════════════════════════════════════════════════════
         MODAL — Criar / Editar  (wizard 3 passos)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($modalOpen)
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
         x-data="{
             step: 1,
             totalSteps: 3,
             stepLabels: ['Sobre', 'Conteúdo', 'Detalhes'],
             next() { if (this.step < this.totalSteps) this.step++; },
             prev() { if (this.step > 1) this.step--; },
         }"
         x-init="step = {{ $errors->any() ? ($errors->hasAny(['templateData.*','privateNote']) ? 2 : ($errors->hasAny(['occurred_at','rating','action_plan','status']) ? 3 : 1)) : 1 }}">

        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="fecharModal"></div>

        <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-xl max-h-[96vh] sm:max-h-[88vh]
                    flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden">

            {{-- ── Header ──────────────────────────────────────────────── --}}
            <div class="flex-shrink-0 px-5 pt-5 pb-0">
                <div class="flex items-start justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-emerald-500 to-emerald-700
                                    flex items-center justify-center shadow-sm shrink-0">
                            <x-lucide-message-square-plus class="w-4.5 h-4.5 text-white" style="width:1.1rem;height:1.1rem" />
                        </div>
                        <div>
                            <h2 class="text-base font-bold text-slate-800 dark:text-white lato-black leading-tight">
                                {{ $editingId ? 'Editar Feedback' : 'Novo Feedback' }}
                            </h2>
                            <p class="text-[11px] text-slate-400 lato-regular" x-text="stepLabels[step-1] + ' · Passo ' + step + ' de ' + totalSteps"></p>
                        </div>
                    </div>
                    <button wire:click="fecharModal"
                            class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                                   hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer shrink-0">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Barra de progresso --}}
                <div class="flex items-center gap-1.5 mb-4">
                    @for ($s = 1; $s <= 3; $s++)
                    <div class="flex-1 h-1.5 rounded-full overflow-hidden bg-slate-100 dark:bg-slate-700">
                        <div class="h-full rounded-full bg-emerald-500 transition-all duration-300"
                             :style="step >= {{ $s }} ? 'width:100%' : 'width:0%'"></div>
                    </div>
                    @endfor
                </div>
            </div>

            {{-- ── Body ─────────────────────────────────────────────────── --}}
            <div class="flex-1 overflow-y-auto px-5 pb-4 space-y-4">

                {{-- ════ PASSO 1: Sobre ════ --}}
                <div x-show="step === 1" x-cloak class="space-y-4 pt-1">

                    {{-- Funcionário --}}
                    <div class="space-y-1.5"
                         x-data="{
                             open: false,
                             search: '',
                             selectedId: @entangle('employee_id'),
                             selectedLabel: '',
                             employees: @js($this->employees->map(fn($e) => ['id' => $e->id, 'name' => $e->name, 'dept' => $e->department?->name ?? '', 'dept_id' => $e->department_id])),
                             managers: @js($departmentManagers),
                             get filtered() {
                                 if (!this.search) return this.employees;
                                 const q = this.search.toLowerCase();
                                 return this.employees.filter(e => e.name.toLowerCase().includes(q) || e.dept.toLowerCase().includes(q));
                             },
                             select(emp) {
                                 this.selectedId = emp.id;
                                 this.selectedLabel = emp.dept ? emp.name+' — '+emp.dept : emp.name;
                                 this.search=''; this.open=false;
                                 // Auto-preenche responsável com o gerente do departamento
                                 const mgr = emp.dept_id ? this.managers[emp.dept_id] : null;
                                 if (mgr) $wire.set('action_plan_responsible_id', mgr.id);
                             },
                             clear() { this.selectedId=''; this.selectedLabel=''; this.search=''; this.open=false; },
                             init() { this.$watch('selectedId', id => { if(!id){this.selectedLabel='';return;} const e=this.employees.find(e=>e.id==id); if(e) this.selectedLabel=e.dept?e.name+' — '+e.dept:e.name; }); }
                         }"
                         @click.away="open = false">

                        <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            Funcionário <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <x-lucide-user class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none z-10" />
                            <div x-show="selectedId && !open"
                                 class="flex items-center w-full pl-9 pr-9 py-2.5 text-sm lato-regular rounded-xl
                                        bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                                        text-slate-800 dark:text-white cursor-pointer"
                                 @click="open=true; $nextTick(()=>$refs.empInput.focus())">
                                <span x-text="selectedLabel" class="truncate flex-1"></span>
                            </div>
                            <input x-show="!selectedId || open" x-ref="empInput"
                                   type="text" x-model="search" @focus="open=true" @input="open=true"
                                   placeholder="Buscar por nome ou departamento..."
                                   class="w-full pl-9 pr-9 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                          text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none
                                          focus:ring-2 focus:ring-emerald-500/40 transition
                                          {{ $errors->has('employee_id') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}" />
                            <button type="button" x-show="selectedId" @click="clear()"
                                    class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                <x-lucide-x class="w-3.5 h-3.5" />
                            </button>
                            <div x-show="open && filtered.length > 0" x-cloak
                                 class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200
                                        dark:border-slate-600 rounded-xl shadow-xl overflow-hidden max-h-48 overflow-y-auto">
                                <template x-for="emp in filtered" :key="emp.id">
                                    <button type="button" @click="select(emp)"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-left
                                                   hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer"
                                            :class="{'bg-emerald-50 dark:bg-emerald-900/20': selectedId == emp.id}">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br from-slate-400 to-slate-500
                                                    flex items-center justify-center text-white text-[10px] lato-black shrink-0"
                                             x-text="emp.name.charAt(0).toUpperCase()"></div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 dark:text-white lato-bold truncate" x-text="emp.name"></p>
                                            <p class="text-xs text-slate-400 truncate" x-text="emp.dept || 'Sem departamento'"></p>
                                        </div>
                                        <x-lucide-check class="w-4 h-4 text-emerald-500 shrink-0" x-show="selectedId == emp.id" />
                                    </button>
                                </template>
                            </div>
                            <div x-show="open && filtered.length===0 && search.length>0" x-cloak
                                 class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200
                                        dark:border-slate-600 rounded-xl shadow-lg px-4 py-3 text-sm text-slate-400">
                                Nenhum resultado para "<span x-text="search" class="font-medium text-slate-600"></span>"
                            </div>
                        </div>
                        @error('employee_id')
                            <p class="flex items-center gap-1 text-xs text-red-500"><x-lucide-alert-circle class="w-3 h-3"/>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Tipo --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            Tipo <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ([
                                'reconhecimento' => ['★',  'Reconhecimento', 'Valorize um comportamento positivo', '#10b981', '#ecfdf5', '#d1fae5'],
                                'sugestao'       => ['💡', 'Sugestão',       'Proponha uma melhoria ou ideia',    '#3b82f6', '#eff6ff', '#dbeafe'],
                                'alerta'         => ['⚠',  'Alerta',         'Sinalize um comportamento crítico', '#ef4444', '#fef2f2', '#fee2e2'],
                            ] as $val => [$icon, $label, $desc, $color, $bg, $border])
                            <button type="button" wire:click="$set('type', '{{ $val }}')"
                                    class="flex flex-col items-start gap-1.5 p-3 rounded-xl border-2 text-left transition cursor-pointer"
                                    style="{{ $type === $val
                                        ? 'border-color:'.$color.';background:'.$bg
                                        : 'border-color:#e2e8f0;background:transparent' }}">
                                <span class="text-xl leading-none">{{ $icon }}</span>
                                <span class="text-xs font-bold lato-bold" style="color:{{ $type === $val ? $color : '#64748b' }}">{{ $label }}</span>
                                <span class="text-[10px] lato-regular leading-tight" style="color:{{ $type === $val ? $color : '#94a3b8' }}">{{ $desc }}</span>
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Categoria --}}
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            Categoria <span class="text-red-500">*</span>
                        </label>
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ([
                                'comportamento'      => 'Comportamento',
                                'desempenho'         => 'Desempenho',
                                'pontualidade'       => 'Pontualidade',
                                'trabalho_em_equipe' => 'Trabalho em equipe',
                                'comunicacao'        => 'Comunicação',
                                'lideranca'          => 'Liderança',
                                'outros'             => 'Outros',
                            ] as $val => $label)
                            <button type="button" wire:click="$set('category', '{{ $val }}')"
                                    class="px-3 py-1.5 rounded-full text-xs lato-bold border transition cursor-pointer
                                           {{ $category === $val
                                               ? 'bg-slate-800 dark:bg-slate-100 border-slate-800 dark:border-slate-100 text-white dark:text-slate-900'
                                               : 'bg-white dark:bg-slate-700 border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-slate-400' }}">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                        @error('category')
                            <p class="flex items-center gap-1 text-xs text-red-500"><x-lucide-alert-circle class="w-3 h-3"/>{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Gravidade (só para alerta) --}}
                    @if ($type === 'alerta')
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            Gravidade <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-4 gap-1.5">
                            @foreach ([
                                'baixo'  => ['Baixo',   '#dcfce7', '#166534', '#22c55e'],
                                'medio'  => ['Médio',   '#fefce8', '#854d0e', '#f59e0b'],
                                'alto'   => ['Alto',    '#ffedd5', '#9a3412', '#f97316'],
                                'critico'=> ['Crítico', '#fee2e2', '#991b1b', '#ef4444'],
                            ] as $val => [$label, $bg, $col, $border])
                            <button type="button" wire:click="$set('severity', '{{ $val }}')"
                                    class="py-2 rounded-xl border-2 text-[11px] font-bold lato-bold text-center transition cursor-pointer"
                                    style="{{ $severity === $val
                                        ? 'background:'.$bg.';color:'.$col.';border-color:'.$border
                                        : 'border-color:#e2e8f0;color:#94a3b8' }}">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                        @error('severity')
                            <p class="flex items-center gap-1 text-xs text-red-500"><x-lucide-alert-circle class="w-3 h-3"/>{{ $message }}</p>
                        @enderror
                    </div>
                    @endif

                    {{-- Identificação (toggle anônimo) --}}
                    <div class="flex items-center justify-between gap-3 px-4 py-3 rounded-xl
                                {{ $is_anonymous ? 'bg-slate-50 dark:bg-slate-700/50 border border-slate-200 dark:border-slate-600' : 'bg-emerald-50 dark:bg-emerald-900/10 border border-emerald-200 dark:border-emerald-800' }}">
                        <div class="flex items-center gap-2.5">
                            @if ($is_anonymous)
                                <x-lucide-eye-off class="w-4 h-4 text-slate-400 shrink-0" />
                                <div>
                                    <p class="text-xs font-bold lato-bold text-slate-500">Feedback anônimo</p>
                                    <p class="text-[10px] text-slate-400 lato-regular">Seu nome não aparecerá no registro</p>
                                </div>
                            @else
                                <x-lucide-user-check class="w-4 h-4 text-emerald-600 shrink-0" />
                                <div>
                                    <p class="text-xs font-bold lato-bold text-emerald-700 dark:text-emerald-300">Feedback identificado</p>
                                    <p class="text-[10px] text-emerald-600 dark:text-emerald-400 lato-regular">Seu nome ficará registrado</p>
                                </div>
                            @endif
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" wire:model.live="is_anonymous" class="sr-only peer">
                            <div class="w-10 h-[22px] rounded-full bg-slate-300 peer-checked:bg-slate-500 transition-colors relative">
                                <div class="absolute top-[3px] left-[3px] w-4 h-4 bg-white rounded-full shadow transition-all peer-checked:translate-x-[18px]"></div>
                            </div>
                        </label>
                    </div>

                </div>{{-- /passo 1 --}}


                {{-- ════ PASSO 2: Conteúdo ════ --}}
                <div x-show="step === 2" x-cloak class="space-y-4 pt-1">

                    {{-- Seletor de template --}}
                    <div class="space-y-2">
                        <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block">
                            Escolha o formato
                        </label>

                        {{-- Grid de cards de template --}}
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ($templateDefs as $key => $tpl)
                            @php
                                $cardBg = match($tpl['color']) {
                                    'violet'  => ['active' => 'bg-violet-600 border-violet-600', 'dot' => 'bg-violet-400', 'text' => 'text-white'],
                                    'blue'    => ['active' => 'bg-blue-600 border-blue-600',     'dot' => 'bg-blue-400',   'text' => 'text-white'],
                                    'emerald' => ['active' => 'bg-emerald-600 border-emerald-600','dot' => 'bg-emerald-400','text' => 'text-white'],
                                    'amber'   => ['active' => 'bg-amber-500 border-amber-500',   'dot' => 'bg-amber-300',  'text' => 'text-white'],
                                    'rose'    => ['active' => 'bg-rose-600 border-rose-600',     'dot' => 'bg-rose-400',   'text' => 'text-white'],
                                    default   => ['active' => 'bg-slate-800 border-slate-800 dark:bg-slate-100 dark:border-slate-200', 'dot' => 'bg-slate-500', 'text' => 'text-white dark:text-slate-900'],
                                };
                                $isActive = $template === $key;
                            @endphp
                            <button type="button"
                                    wire:click="$set('template', '{{ $key }}')"
                                    class="relative flex flex-col items-start gap-1.5 p-3 rounded-xl border-2 text-left transition-all duration-150 cursor-pointer
                                           {{ $isActive
                                               ? $cardBg['active'] . ' shadow-md'
                                               : 'bg-white dark:bg-slate-700/60 border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500' }}">

                                {{-- Bolinhas de campo --}}
                                <div class="flex items-center gap-1 flex-wrap">
                                    @foreach ($tpl['fields'] as $fKey => $fDef)
                                    <span class="w-1.5 h-1.5 rounded-full {{ $isActive ? $cardBg['dot'] : 'bg-slate-300 dark:bg-slate-500' }}"></span>
                                    @endforeach
                                </div>

                                <div>
                                    <span class="text-[11px] font-bold lato-bold leading-tight block
                                                 {{ $isActive ? $cardBg['text'] : 'text-slate-700 dark:text-slate-200' }}">{{ $tpl['label'] }}</span>
                                    <span class="text-[9px] lato-regular leading-tight block mt-0.5
                                                 {{ $isActive ? 'text-white/70' : 'text-slate-400' }}">{{ count($tpl['fields']) }} campos</span>
                                </div>

                                @if ($isActive)
                                <div class="absolute top-2 right-2">
                                    <x-lucide-check class="w-3 h-3 text-white/80" />
                                </div>
                                @endif
                            </button>
                            @endforeach
                        </div>

                        {{-- Preview dos campos do template ativo --}}
                        @foreach ($templateDefs as $key => $tpl)
                        @if ($template === $key)
                        @php
                            $previewColor = match($tpl['color']) {
                                'violet'  => 'text-violet-600 dark:text-violet-400 bg-violet-50 dark:bg-violet-900/20 border-violet-100 dark:border-violet-800',
                                'blue'    => 'text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-900/20 border-blue-100 dark:border-blue-800',
                                'emerald' => 'text-emerald-600 dark:text-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800',
                                'amber'   => 'text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20 border-amber-100 dark:border-amber-800',
                                'rose'    => 'text-rose-600 dark:text-rose-400 bg-rose-50 dark:bg-rose-900/20 border-rose-100 dark:border-rose-800',
                                default   => 'text-slate-600 dark:text-slate-400 bg-slate-50 dark:bg-slate-700/50 border-slate-200 dark:border-slate-600',
                            };
                        @endphp
                        <div class="flex items-center gap-1.5 flex-wrap px-2.5 py-1.5 rounded-lg border {{ $previewColor }}">
                            <span class="text-[10px] font-semibold lato-bold opacity-60 uppercase tracking-wide">Campos:</span>
                            @foreach ($tpl['fields'] as $fKey => $fDef)
                                <span class="text-[10px] lato-bold">{{ $fDef['label'] }}</span>
                                @if (!$loop->last)<span class="opacity-40 text-[10px]">→</span>@endif
                            @endforeach
                            <span class="opacity-40 text-[10px]">→</span>
                            <span class="text-[10px] lato-bold opacity-70">🔒 Anotação privada</span>
                        </div>
                        @endif
                        @endforeach
                    </div>

                    {{-- Campos dinâmicos do template --}}
                    <div class="space-y-3">
                        @foreach ($templateDefs as $key => $tpl)
                        @if ($template === $key)
                        @php
                            $fieldStyle = match($tpl['color']) {
                                'violet'  => ['ring' => 'focus:ring-violet-400/50',  'border' => 'border-violet-200 dark:border-violet-700/40',  'label' => 'text-violet-700 dark:text-violet-300',  'num' => 'bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-300'],
                                'blue'    => ['ring' => 'focus:ring-blue-400/50',    'border' => 'border-blue-200 dark:border-blue-700/40',    'label' => 'text-blue-700 dark:text-blue-300',    'num' => 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300'],
                                'emerald' => ['ring' => 'focus:ring-emerald-400/50', 'border' => 'border-emerald-200 dark:border-emerald-700/40','label' => 'text-emerald-700 dark:text-emerald-300','num' => 'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-300'],
                                'amber'   => ['ring' => 'focus:ring-amber-400/50',   'border' => 'border-amber-200 dark:border-amber-700/40',   'label' => 'text-amber-700 dark:text-amber-300',   'num' => 'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300'],
                                'rose'    => ['ring' => 'focus:ring-rose-400/50',    'border' => 'border-rose-200 dark:border-rose-700/40',    'label' => 'text-rose-700 dark:text-rose-300',    'num' => 'bg-rose-100 dark:bg-rose-900/40 text-rose-600 dark:text-rose-300'],
                                default   => ['ring' => 'focus:ring-emerald-500/40', 'border' => 'border-slate-200 dark:border-slate-600',        'label' => 'text-slate-600 dark:text-slate-300',   'num' => 'bg-slate-100 dark:bg-slate-700 text-slate-500'],
                            };
                        @endphp
                        @foreach ($tpl['fields'] as $fieldKey => $field)
                        @php $fieldNum = $loop->index + 1; @endphp
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-2 text-xs font-bold lato-bold {{ $fieldStyle['label'] }}">
                                <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[9px] lato-black {{ $fieldStyle['num'] }}">{{ $fieldNum }}</span>
                                {{ $field['label'] }}
                                @if($field['required']) <span class="text-red-400 font-normal">obrigatório</span> @endif
                            </label>
                            <textarea wire:model.defer="templateData.{{ $fieldKey }}"
                                      rows="{{ $loop->first ? 4 : 3 }}"
                                      placeholder="{{ $field['placeholder'] }}"
                                      class="w-full px-3 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                             {{ $fieldStyle['border'] }} {{ $fieldStyle['ring'] }}
                                             text-slate-800 dark:text-white placeholder-slate-400 focus:outline-none
                                             focus:ring-2 transition resize-none
                                             {{ $errors->has('templateData.'.$fieldKey) ? 'border-2 border-red-400' : 'border' }}"></textarea>
                            @error('templateData.'.$fieldKey)
                                <p class="flex items-center gap-1 text-xs text-red-500">
                                    <x-lucide-alert-circle class="w-3 h-3"/>{{ $message }}
                                </p>
                            @enderror
                        </div>
                        @endforeach
                        @endif
                        @endforeach
                    </div>

                    {{-- Anotação privada --}}
                    <div class="space-y-1.5">
                        <label class="flex items-center gap-2 text-xs font-bold lato-bold text-amber-600 dark:text-amber-400">
                            <span class="inline-flex items-center justify-center w-4 h-4 rounded-full text-[9px] lato-black bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-300">
                                <x-lucide-lock class="w-2.5 h-2.5" />
                            </span>
                            Anotação privada
                            <span class="text-[9px] font-normal lato-regular text-amber-400">— visível somente para você</span>
                        </label>
                        <textarea wire:model.defer="privateNote" rows="2"
                                  placeholder="Notas internas, observações confidenciais, contexto adicional..."
                                  class="w-full px-3 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                         border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                         placeholder-slate-400 focus:outline-none
                                         focus:ring-2 focus:ring-emerald-500/40 transition resize-none"></textarea>
                    </div>

                </div>{{-- /passo 2 --}}


                {{-- ════ PASSO 3: Detalhes ════ --}}
                <div x-show="step === 3" x-cloak class="space-y-4 pt-1">

                    {{-- Data + Nota lado a lado --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-1.5">
                                Data do ocorrido <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="date" wire:model="occurred_at"
                                       class="w-full pl-9 pr-3 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                              text-slate-800 dark:text-white focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition
                                              {{ $errors->has('occurred_at') ? 'border border-red-400' : 'border border-slate-200 dark:border-slate-600' }}">
                            </div>
                            @error('occurred_at')
                                <p class="flex items-center gap-1 text-xs text-red-500 mt-0.5"><x-lucide-alert-circle class="w-3 h-3"/>{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-1.5">
                                Nota
                            </label>
                            <div x-data="{ hovered: 0, selected: $wire.entangle('rating') }"
                                 class="flex items-center gap-0.5 pt-1">
                                @for ($i = 1; $i <= 5; $i++)
                                <button type="button"
                                        @click="selected = (selected === {{ $i }}) ? null : {{ $i }}"
                                        @mouseenter="hovered = {{ $i }}" @mouseleave="hovered = 0"
                                        class="text-2xl leading-none transition cursor-pointer"
                                        :style="(hovered || selected) >= {{ $i }} ? 'color:#f59e0b' : 'color:#e2e8f0'">★</button>
                                @endfor
                                <button type="button" x-show="selected" @click="selected = null"
                                        class="ml-1 text-[10px] text-slate-400 hover:text-slate-600 cursor-pointer">limpar</button>
                            </div>
                        </div>
                    </div>

                    {{-- Status --}}
                    <div>
                        <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-1.5">
                            Status
                        </label>
                        <div class="grid grid-cols-3 gap-1.5">
                            @foreach ([
                                'aberto'             => ['Aberto',              '#f1f5f9', '#475569'],
                                'em_analise'         => ['Em análise',          '#dbeafe', '#1e40af'],
                                'aguardando_plano'   => ['Aguard. plano',       '#fef3c7', '#92400e'],
                                'plano_em_andamento' => ['Plano andamento',     '#ffedd5', '#9a3412'],
                                'resolvido'          => ['Resolvido',           '#d1fae5', '#065f46'],
                                'arquivado'          => ['Arquivado',           '#f8fafc', '#94a3b8'],
                            ] as $val => [$label, $bg, $col])
                            <button type="button" wire:click="$set('status', '{{ $val }}')"
                                    class="py-2 px-2 rounded-lg border text-[10px] font-bold lato-bold text-center transition cursor-pointer leading-tight"
                                    style="{{ $status === $val
                                        ? 'background:'.$bg.';color:'.$col.';border-color:'.$col.'66'
                                        : 'border-color:#e2e8f0;color:#94a3b8' }}">
                                {{ $label }}
                            </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Plano de ação --}}
                    <div>
                        <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-1.5">
                            Plano de ação <span class="text-[10px] font-normal normal-case text-slate-400">— descrição geral</span>
                        </label>
                        <textarea wire:model="action_plan" rows="2"
                                  placeholder="Contexto ou objetivo geral do plano..."
                                  class="w-full px-3 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                         border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                         placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition resize-none"></textarea>
                    </div>

                    {{-- Prazo global + Responsável global --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-1.5">
                                Prazo
                            </label>
                            <div class="relative">
                                <x-lucide-calendar-clock class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="date" wire:model="action_plan_deadline"
                                       class="w-full pl-9 pr-2 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                              border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                              focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition">
                            </div>
                        </div>

                        {{-- Responsável com combobox e sugestão de gerente --}}
                        <div
                             x-data="{
                                 open: false,
                                 search: '',
                                 selectedId: @entangle('action_plan_responsible_id'),
                                 selectedLabel: '',
                                 suggestedMgrId: null,
                                 suggestedMgrName: '',
                                 managers: @js($departmentManagers),
                                 employees: @js($this->employees->map(fn($e) => ['id' => $e->id, 'name' => $e->name, 'dept' => $e->department?->name ?? '', 'dept_id' => $e->department_id])),
                                 get filtered() {
                                     const list = this.employees;
                                     if (!this.search) return list;
                                     const q = this.search.toLowerCase();
                                     return list.filter(e => e.name.toLowerCase().includes(q) || e.dept.toLowerCase().includes(q));
                                 },
                                 select(emp) {
                                     this.selectedId = emp.id;
                                     this.selectedLabel = emp.dept ? emp.name+' — '+emp.dept : emp.name;
                                     this.search = ''; this.open = false;
                                 },
                                 clear() { this.selectedId=''; this.selectedLabel=''; this.search=''; this.open=false; },
                                 init() {
                                     // Sincroniza label quando selectedId muda (vindo do funcionário)
                                     this.$watch('selectedId', id => {
                                         if (!id) { this.selectedLabel = ''; return; }
                                         const e = this.employees.find(e => e.id == id);
                                         if (e) this.selectedLabel = e.dept ? e.name+' — '+e.dept : e.name;
                                     });
                                     // Observa employee_id do Wire para sugerir gerente
                                     this.$watch(() => $wire.employee_id, empId => {
                                         if (!empId) { this.suggestedMgrId=null; this.suggestedMgrName=''; return; }
                                         const emp = this.employees.find(e => e.id == empId);
                                         const mgr = emp?.dept_id ? this.managers[emp.dept_id] : null;
                                         this.suggestedMgrId   = mgr?.id ?? null;
                                         this.suggestedMgrName = mgr?.name ?? '';
                                     });
                                 }
                             }"
                             @click.away="open = false">

                            <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-1.5">
                                Responsável
                            </label>

                            {{-- Sugestão de gerente --}}
                            <template x-if="suggestedMgrId && !selectedId">
                                <button type="button"
                                        @click="select(employees.find(e => e.id == suggestedMgrId) || {id: suggestedMgrId, name: suggestedMgrName, dept: '', dept_id: null})"
                                        class="w-full flex items-center gap-2 px-3 py-2 mb-1.5 rounded-xl border
                                               border-emerald-200 dark:border-emerald-700/50 bg-emerald-50 dark:bg-emerald-900/10
                                               text-emerald-700 dark:text-emerald-300 text-xs lato-bold cursor-pointer
                                               hover:bg-emerald-100 dark:hover:bg-emerald-900/20 transition">
                                    <x-lucide-shield-check class="w-3.5 h-3.5 shrink-0" />
                                    <span class="truncate" x-text="'Gerente: ' + suggestedMgrName"></span>
                                    <span class="ml-auto shrink-0 text-[9px] text-emerald-500 lato-regular">sugerido</span>
                                </button>
                            </template>

                            {{-- Combobox principal --}}
                            <div class="relative">
                                <x-lucide-user-check class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none z-10" />

                                {{-- Selecionado --}}
                                <div x-show="selectedId && !open"
                                     class="flex items-center w-full pl-9 pr-8 py-2.5 text-sm lato-regular rounded-xl
                                            bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                                            text-slate-800 dark:text-white cursor-pointer"
                                     @click="open=true; $nextTick(()=>$refs.respInput.focus())">
                                    <span x-text="selectedLabel" class="truncate flex-1 text-xs"></span>
                                </div>

                                {{-- Input busca --}}
                                <input x-show="!selectedId || open"
                                       x-ref="respInput"
                                       type="text" x-model="search"
                                       @focus="open=true" @input="open=true"
                                       placeholder="Buscar responsável..."
                                       class="w-full pl-9 pr-8 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700/60
                                              border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                              placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition" />

                                {{-- Limpar --}}
                                <button type="button" x-show="selectedId" @click="clear()"
                                        class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>

                                {{-- Dropdown --}}
                                <div x-show="open && filtered.length > 0" x-cloak
                                     class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200
                                            dark:border-slate-600 rounded-xl shadow-xl overflow-hidden max-h-44 overflow-y-auto">

                                    {{-- Gerente destacado no topo --}}
                                    <template x-if="suggestedMgrId && !search">
                                        <div>
                                            <div class="px-3 py-1 text-[9px] font-bold lato-bold text-emerald-600 dark:text-emerald-400 uppercase tracking-wider bg-emerald-50 dark:bg-emerald-900/20">
                                                Gerente do departamento
                                            </div>
                                            <button type="button"
                                                    @click="select(employees.find(e=>e.id==suggestedMgrId)||{id:suggestedMgrId,name:suggestedMgrName,dept:'',dept_id:null})"
                                                    class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-left
                                                           bg-emerald-50/60 dark:bg-emerald-900/10 hover:bg-emerald-100 dark:hover:bg-emerald-900/20
                                                           transition cursor-pointer border-b border-slate-100 dark:border-slate-700">
                                                <div class="w-6 h-6 rounded-full bg-emerald-500 flex items-center justify-center text-white text-[9px] lato-black shrink-0"
                                                     x-text="suggestedMgrName.charAt(0).toUpperCase()"></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="text-xs font-semibold lato-bold text-slate-800 dark:text-white truncate" x-text="suggestedMgrName"></p>
                                                    <p class="text-[9px] text-emerald-600 dark:text-emerald-400 lato-regular">Gerente</p>
                                                </div>
                                                <x-lucide-check class="w-3.5 h-3.5 text-emerald-500 shrink-0" x-show="selectedId == suggestedMgrId" />
                                            </button>
                                            <div class="px-3 py-1 text-[9px] font-bold lato-bold text-slate-400 uppercase tracking-wider">
                                                Outros
                                            </div>
                                        </div>
                                    </template>

                                    <template x-for="emp in filtered.filter(e => e.id != suggestedMgrId || !!search)" :key="emp.id">
                                        <button type="button" @click="select(emp)"
                                                class="w-full flex items-center gap-2.5 px-3 py-2 text-sm text-left
                                                       hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer"
                                                :class="{'bg-emerald-50 dark:bg-emerald-900/20': selectedId == emp.id}">
                                            <div class="w-6 h-6 rounded-full bg-gradient-to-br from-slate-400 to-slate-500
                                                        flex items-center justify-center text-white text-[9px] lato-black shrink-0"
                                                 x-text="emp.name.charAt(0).toUpperCase()"></div>
                                            <div class="flex-1 min-w-0">
                                                <p class="text-xs font-semibold lato-bold text-slate-800 dark:text-white truncate" x-text="emp.name"></p>
                                                <p class="text-[9px] text-slate-400 truncate" x-text="emp.dept || '—'"></p>
                                            </div>
                                            <x-lucide-check class="w-3.5 h-3.5 text-emerald-500 shrink-0" x-show="selectedId == emp.id" />
                                        </button>
                                    </template>
                                </div>

                                <div x-show="open && filtered.length===0 && search.length>0" x-cloak
                                     class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200
                                            dark:border-slate-600 rounded-xl shadow-lg px-3 py-2.5 text-xs text-slate-400 lato-regular">
                                    Nenhum resultado para "<span x-text="search" class="font-medium text-slate-600"></span>"
                                </div>
                            </div>

                        </div>
                    </div>

                    {{-- Tarefas do plano de ação (checklist) --}}
                    <div x-data="{
                            taskRespOpen: false,
                            taskRespSearch: '',
                            taskRespId: @entangle('newTaskResponsibleId'),
                            taskRespLabel: '',
                            employees: @js($this->employees->map(fn($e) => ['id' => $e->id, 'name' => $e->name, 'dept' => $e->department?->name ?? ''])),
                            get filteredEmps() {
                                if (!this.taskRespSearch) return this.employees;
                                const q = this.taskRespSearch.toLowerCase();
                                return this.employees.filter(e => e.name.toLowerCase().includes(q) || e.dept.toLowerCase().includes(q));
                            },
                            selectResp(emp) {
                                this.taskRespId = emp.id;
                                this.taskRespLabel = emp.dept ? emp.name + ' — ' + emp.dept : emp.name;
                                this.taskRespSearch = ''; this.taskRespOpen = false;
                            },
                            clearResp() { this.taskRespId = null; this.taskRespLabel = ''; this.taskRespSearch = ''; this.taskRespOpen = false; },
                            init() {
                                this.$watch('taskRespId', id => {
                                    if (!id) { this.taskRespLabel = ''; return; }
                                    const e = this.employees.find(e => e.id == id);
                                    if (e) this.taskRespLabel = e.dept ? e.name + ' — ' + e.dept : e.name;
                                });
                            }
                         }"
                         @click.away="taskRespOpen = false">

                        <div class="flex items-center justify-between mb-2">
                            <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                                Tarefas do plano
                            </label>
                            @if(count($actionTasks) > 0)
                                <span class="text-[10px] lato-regular text-slate-400">
                                    {{ collect($actionTasks)->where('completed', true)->count() }}/{{ count($actionTasks) }} concluídas
                                </span>
                            @endif
                        </div>

                        {{-- Lista de tarefas --}}
                        @if(count($actionTasks) > 0)
                            <div class="space-y-1.5 mb-3">
                                @foreach($actionTasks as $idx => $task)
                                    <div class="flex items-start gap-2.5 px-3 py-2.5 rounded-xl border
                                                {{ $task['completed']
                                                    ? 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-100 dark:border-emerald-800/40'
                                                    : 'bg-white dark:bg-slate-700/40 border-slate-200 dark:border-slate-600' }}">

                                        {{-- Ícone concluído / pendente --}}
                                        <div class="mt-0.5 w-4 h-4 rounded-full shrink-0 flex items-center justify-center
                                                    {{ $task['completed']
                                                        ? 'bg-emerald-500'
                                                        : 'border-2 border-slate-300 dark:border-slate-500' }}">
                                            @if($task['completed'])
                                                <x-lucide-check class="w-2.5 h-2.5 text-white" />
                                            @endif
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 leading-snug
                                                       {{ $task['completed'] ? 'line-through text-slate-400 dark:text-slate-500' : '' }}">
                                                {{ $task['description'] }}
                                            </p>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                @if(!empty($task['responsible_name']))
                                                    <span class="flex items-center gap-1 text-[10px] text-slate-400 lato-regular">
                                                        <x-lucide-user class="w-3 h-3" />
                                                        {{ $task['responsible_name'] }}
                                                    </span>
                                                @endif
                                                @if(!empty($task['due_date']))
                                                    <span class="flex items-center gap-1 text-[10px] lato-regular
                                                                 {{ !$task['completed'] && \Carbon\Carbon::parse($task['due_date'])->isPast()
                                                                    ? 'text-red-500' : 'text-slate-400' }}">
                                                        <x-lucide-calendar class="w-3 h-3" />
                                                        {{ \Carbon\Carbon::parse($task['due_date'])->format('d/m/Y') }}
                                                        @if(!$task['completed'] && \Carbon\Carbon::parse($task['due_date'])->isPast())
                                                            <span class="font-semibold">(vencida)</span>
                                                        @endif
                                                    </span>
                                                @endif
                                            </div>
                                        </div>

                                        <button type="button"
                                                wire:click="removerTarefa({{ $idx }})"
                                                class="shrink-0 text-slate-300 hover:text-red-400 dark:text-slate-600
                                                       dark:hover:text-red-400 transition cursor-pointer">
                                            <x-lucide-x class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="flex items-center gap-2 px-3 py-2.5 mb-3 rounded-xl border border-dashed
                                        border-slate-200 dark:border-slate-600 text-slate-400 text-xs lato-regular">
                                <x-lucide-clipboard-list class="w-3.5 h-3.5 shrink-0" />
                                Nenhuma tarefa adicionada ainda.
                            </div>
                        @endif

                        {{-- Formulário para nova tarefa --}}
                        <div class="bg-slate-50 dark:bg-slate-700/30 rounded-xl border border-slate-200 dark:border-slate-600 p-3 space-y-2">
                            <p class="text-[10px] font-semibold lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wide mb-1">
                                Adicionar tarefa
                            </p>

                            {{-- Descrição --}}
                            <input type="text"
                                   wire:model="newTaskDesc"
                                   placeholder="Descreva a tarefa..."
                                   class="w-full px-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700/60
                                          border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                          placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition" />
                            @error('newTaskDesc')
                                <span class="text-xs text-red-500 lato-regular">{{ $message }}</span>
                            @enderror

                            {{-- Fase + Prazo --}}
                            <div class="grid grid-cols-2 gap-2">

                                {{-- Fase do plano --}}
                                <select wire:model="newTaskPhase"
                                        class="w-full px-3 py-2 text-xs lato-regular rounded-lg bg-white dark:bg-slate-700/60
                                               border border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300
                                               focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition">
                                    <option value="">Fase (opcional)</option>
                                    <option value="30_dias">1ª fase — 30 dias</option>
                                    <option value="60_dias">2ª fase — 60 dias</option>
                                    <option value="90_dias">3ª fase — 90 dias</option>
                                </select>

                                {{-- Prazo da tarefa --}}
                                <div class="relative">
                                    <x-lucide-calendar class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                                    <input type="date" wire:model="newTaskDue"
                                           class="w-full pl-8 pr-2 py-2 text-xs lato-regular rounded-lg bg-white dark:bg-slate-700/60
                                                  border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                                  focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition" />
                                </div>
                            </div>

                            {{-- Recursos da empresa --}}
                            <input type="text"
                                   wire:model="newTaskResources"
                                   placeholder="Recursos oferecidos (ex: treinamento, mentor, curso...)  — opcional"
                                   class="w-full px-3 py-2 text-xs lato-regular rounded-lg bg-white dark:bg-slate-700/60
                                          border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                          placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-400/40 transition" />

                            {{-- Responsável da tarefa --}}
                            <div class="relative" @click.away="taskRespOpen = false">
                                    <x-lucide-user class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none z-10" />

                                    <div x-show="taskRespId && !taskRespOpen"
                                         class="flex items-center w-full pl-8 pr-7 py-2 text-xs lato-regular rounded-lg
                                                bg-white dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600
                                                text-slate-800 dark:text-white cursor-pointer truncate"
                                         @click="taskRespOpen=true; $nextTick(()=>$refs.taskRespInput.focus())">
                                        <span x-text="taskRespLabel" class="truncate"></span>
                                    </div>

                                    <input x-show="!taskRespId || taskRespOpen"
                                           x-ref="taskRespInput"
                                           type="text" x-model="taskRespSearch"
                                           @focus="taskRespOpen=true" @input="taskRespOpen=true"
                                           placeholder="Responsável..."
                                           class="w-full pl-8 pr-7 py-2 text-xs lato-regular rounded-lg bg-white dark:bg-slate-700/60
                                                  border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                                  placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 transition" />

                                    <button type="button" x-show="taskRespId" @click="clearResp()"
                                            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                                        <x-lucide-x class="w-3 h-3" />
                                    </button>

                                    <div x-show="taskRespOpen && filteredEmps.length > 0" x-cloak
                                         class="absolute z-50 mt-1 w-full bg-white dark:bg-slate-800 border border-slate-200
                                                dark:border-slate-600 rounded-xl shadow-xl overflow-hidden max-h-36 overflow-y-auto">
                                        <template x-for="emp in filteredEmps" :key="emp.id">
                                            <button type="button" @click="selectResp(emp)"
                                                    class="w-full flex items-center gap-2 px-2.5 py-1.5 text-xs text-left
                                                           hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer"
                                                    :class="{'bg-emerald-50 dark:bg-emerald-900/20': taskRespId == emp.id}">
                                                <div class="w-5 h-5 rounded-full bg-slate-300 dark:bg-slate-600 flex items-center
                                                            justify-center text-white text-[8px] lato-black shrink-0"
                                                     x-text="emp.name.charAt(0).toUpperCase()"></div>
                                                <div class="flex-1 min-w-0">
                                                    <p class="font-semibold lato-bold text-slate-800 dark:text-white truncate" x-text="emp.name"></p>
                                                    <p class="text-[9px] text-slate-400 truncate" x-text="emp.dept || '—'"></p>
                                                </div>
                                                <x-lucide-check class="w-3 h-3 text-emerald-500 shrink-0" x-show="taskRespId == emp.id" />
                                            </button>
                                        </template>
                                    </div>
                            </div>

                            <button type="button"
                                    wire:click="adicionarTarefa"
                                    class="w-full flex items-center justify-center gap-1.5 py-2 rounded-lg text-xs font-semibold lato-bold
                                           bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-sm shadow-blue-500/20 transition cursor-pointer">
                                <x-lucide-plus class="w-3.5 h-3.5" />
                                Adicionar tarefa
                            </button>
                        </div>
                    </div>

                    {{-- Anexo --}}
                    <div>
                        <label class="text-xs font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide block mb-1.5">
                            Anexo <span class="text-[10px] font-normal normal-case text-slate-400">(máx 10MB)</span>
                        </label>
                        <input type="file" wire:model="attachment"
                               class="block w-full text-xs text-slate-500 lato-regular cursor-pointer rounded-xl
                                      border border-slate-200 dark:border-slate-600 px-3 py-2
                                      file:mr-3 file:py-1 file:px-3 file:rounded-lg file:border-0
                                      file:text-xs file:font-semibold file:bg-emerald-50 file:text-emerald-700
                                      hover:file:bg-emerald-100 bg-white dark:bg-slate-700/60 transition">
                        @error('attachment')
                            <p class="flex items-center gap-1 text-xs text-red-500 mt-0.5"><x-lucide-alert-circle class="w-3 h-3"/>{{ $message }}</p>
                        @enderror
                    </div>

                </div>{{-- /passo 3 --}}

            </div>{{-- /body --}}

            {{-- ── Footer ───────────────────────────────────────────────── --}}
            <div class="flex-shrink-0 px-5 py-3.5 border-t border-slate-100 dark:border-slate-700
                        bg-white dark:bg-slate-800 flex items-center justify-between gap-3">

                {{-- Voltar / Cancelar --}}
                <button type="button"
                        @click="step > 1 ? prev() : $wire.fecharModal()"
                        class="flex items-center gap-1.5 px-4 py-2.5 text-sm lato-bold rounded-xl text-slate-500
                               bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                    <x-lucide-chevron-left class="w-4 h-4" x-show="step > 1" />
                    <span x-text="step > 1 ? 'Voltar' : 'Cancelar'"></span>
                </button>

                {{-- Indicador de passo (pontos) --}}
                <div class="flex items-center gap-1.5">
                    @for ($s = 1; $s <= 3; $s++)
                    <div class="rounded-full transition-all duration-200"
                         :class="step === {{ $s }} ? 'w-4 h-1.5 bg-emerald-500' : 'w-1.5 h-1.5 bg-slate-300'"></div>
                    @endfor
                </div>

                {{-- Próximo / Salvar --}}
                <template x-if="step < totalSteps">
                    <button type="button" @click="next()"
                            class="flex items-center gap-1.5 px-4 py-2.5 text-sm lato-bold rounded-xl
                                   bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer shadow-sm">
                        Próximo
                        <x-lucide-chevron-right class="w-4 h-4" />
                    </button>
                </template>
                <template x-if="step === totalSteps">
                    <button wire:click="salvar" wire:loading.attr="disabled" wire:target="salvar"
                            class="flex items-center gap-1.5 px-5 py-2.5 text-sm lato-bold rounded-xl
                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white shadow-md shadow-blue-500/20 transition cursor-pointer
                                   disabled:opacity-60">
                        <span wire:loading.remove wire:target="salvar" class="flex items-center gap-1">
                            @if ($editingId)
                                <x-lucide-square-pen class="w-4 h-4" />
                            @else
                                <x-lucide-circle-check class="w-4 h-4" />
                            @endif
                                {{ $editingId ? 'Salvar Alterações' : 'Confirmar' }}
                        </span>

                        <span wire:loading wire:target="salvar" class="flex items-center gap-2">
                                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        </span>
                    </button>
                </template>

            </div>

        </div>
    </div>
    @endif


    {{-- ═══════════════════════════════════════════════════════════════
         MODAL — Detalhes (com histórico + comentários)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($viewModal && $fb = $this->viewing)
        @php
            $typeStyle2 = match($fb->type) {
                'reconhecimento' => 'background:#ecfdf5;color:#065f46;border-color:#6ee7b7',
                'sugestao'       => 'background:#eff6ff;color:#1e40af;border-color:#93c5fd',
                default          => 'background:#fef2f2;color:#991b1b;border-color:#fca5a5',
            };
            $stStyle2 = match($fb->status) {
                'aberto'             => 'background:#eff6ff;color:#1e40af',
                'em_analise'         => 'background:#fefce8;color:#854d0e',
                'aguardando_plano'   => 'background:#fff7ed;color:#9a3412',
                'plano_em_andamento' => 'background:#f5f3ff;color:#5b21b6',
                'resolvido'          => 'background:#ecfdf5;color:#166534',
                'arquivado'          => 'background:#f1f5f9;color:#475569',
                default              => 'background:#f1f5f9;color:#475569',
            };
        @endphp

        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="fecharView"></div>

            <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh]
                        flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden">

                {{-- Header --}}
                <div class="flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-3 px-4 sm:px-6 py-3.5 sm:py-5">
                        <div class="flex flex-wrap items-center gap-1.5 flex-1">
                            <span class="text-xs font-semibold lato-bold px-2.5 py-1 rounded-full border"
                                  style="{{ $typeStyle2 }}">
                                {{ $fb->type_label }}
                            </span>
                            @if ($fb->type === 'alerta' && $fb->severity)
                                <span class="text-xs font-bold lato-bold px-2.5 py-1 rounded-full"
                                      style="{{ match($fb->severity) {
                                          'baixo'  => 'background:#dcfce7;color:#166534',
                                          'medio'  => 'background:#fefce8;color:#854d0e',
                                          'alto'   => 'background:#ffedd5;color:#9a3412',
                                          default  => 'background:#fee2e2;color:#991b1b',
                                      } }}">
                                    {{ strtoupper($fb->severity_label) }}
                                </span>
                            @endif
                            <span class="text-xs font-semibold lato-bold px-2.5 py-1 rounded-full"
                                  style="{{ $stStyle2 }}">
                                {{ $fb->status_label }}
                            </span>
                        </div>
                        <button type="button" wire:click="fecharView"
                                class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                                       hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 sm:py-5 space-y-5">

                    {{-- Funcionário + Avaliador --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Funcionário avaliado</p>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-600
                                            flex items-center justify-center text-white text-xs font-bold lato-black shrink-0">
                                    {{ mb_strtoupper(mb_substr($fb->employee?->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white lato-bold leading-tight">{{ $fb->employee?->name ?? '—' }}</p>
                                    <p class="text-[11px] text-slate-400 lato-regular">{{ $fb->employee?->department?->name ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Avaliador</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white lato-bold">{{ $fb->evaluator_name }}</p>
                            @if ($fb->is_anonymous)
                                <p class="text-[11px] text-slate-400 lato-regular">Identidade ocultada</p>
                            @endif
                        </div>
                    </div>

                    {{-- Metadados --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Categoria</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold mt-0.5">{{ $fb->category_label }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Data</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold mt-0.5">{{ $fb->occurred_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Nota</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold mt-0.5">
                                @if ($fb->rating)
                                    @for ($i = 1; $i <= 5; $i++)<span style="color: {{ $i <= $fb->rating ? '#f59e0b' : '#e2e8f0' }}">★</span>@endfor
                                @else —
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Criado em</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold mt-0.5">{{ $fb->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- Conteúdo do feedback (estruturado por template) --}}
                    @php
                        $fbTemplate  = $fb->template ?? 'livre';
                        $fbTplDef    = \App\Livewire\Pages\Feedback\Index::templateDefinitions()[$fbTemplate] ?? null;
                        $fbTplData   = $fb->template_data ?? [];
                        $colorBorder = match($fbTplDef['color'] ?? 'slate') {
                            'violet'  => 'border-violet-200 dark:border-violet-700/50',
                            'blue'    => 'border-blue-200 dark:border-blue-700/50',
                            'emerald' => 'border-emerald-200 dark:border-emerald-700/50',
                            'amber'   => 'border-amber-200 dark:border-amber-700/50',
                            'rose'    => 'border-rose-200 dark:border-rose-700/50',
                            default   => 'border-slate-200 dark:border-slate-700/50',
                        };
                        $colorDotView = match($fbTplDef['color'] ?? 'slate') {
                            'violet'  => 'bg-violet-500',
                            'blue'    => 'bg-blue-500',
                            'emerald' => 'bg-emerald-500',
                            'amber'   => 'bg-amber-500',
                            'rose'    => 'bg-rose-500',
                            default   => 'bg-slate-400',
                        };
                    @endphp

                    <div class="space-y-2">
                        {{-- Badge do template --}}
                        @if ($fbTplDef)
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Formato</span>
                            <span class="text-[10px] font-semibold lato-bold px-2 py-0.5 rounded-full
                                         {{ match($fbTplDef['color'] ?? 'slate') {
                                             'violet'  => 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300',
                                             'blue'    => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                                             'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                             'amber'   => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                             'rose'    => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300',
                                             default   => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                                         } }}">
                                {{ $fbTplDef['label'] }}
                            </span>
                        </div>
                        @endif

                        {{-- Campos do template --}}
                        @if ($fbTplDef && !empty($fbTplData))
                            @foreach ($fbTplDef['fields'] as $fieldKey => $fieldDef)
                                @if (!empty($fbTplData[$fieldKey]))
                                <div class="rounded-xl border {{ $colorBorder }} bg-white dark:bg-slate-800/50 overflow-hidden">
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 dark:bg-slate-700/50 border-b {{ $colorBorder }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $colorDotView }} shrink-0"></span>
                                        <span class="text-[10px] font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ $fieldDef['label'] }}</span>
                                    </div>
                                    <p class="px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed whitespace-pre-wrap">{{ $fbTplData[$fieldKey] }}</p>
                                </div>
                                @endif
                            @endforeach
                        @else
                            {{-- Fallback para feedbacks sem template_data (registros antigos) --}}
                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed whitespace-pre-wrap">
                                {{ $fb->message }}
                            </div>
                        @endif
                    </div>

                    {{-- Anotação privada (só para o avaliador ou RH/DP) --}}
                    @if ($fb->private_note && (auth()->id() === $fb->evaluator_id || auth()->user()->isRhOuDp()))
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 overflow-hidden">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 border-b border-slate-200 dark:border-slate-700">
                            <x-lucide-lock class="w-3 h-3 text-slate-400 shrink-0" />
                            <span class="text-[10px] font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Anotação privada</span>
                            <span class="text-[9px] text-slate-400 lato-regular ml-1">— visível só para você</span>
                        </div>
                        <p class="px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed whitespace-pre-wrap">{{ $fb->private_note }}</p>
                    </div>
                    @endif

                    {{-- Plano de ação --}}
                    @if ($fb->action_plan)
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Plano de ação</p>
                            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl p-4">
                                <p class="text-sm text-indigo-900 dark:text-indigo-200 lato-regular whitespace-pre-wrap leading-relaxed">{{ $fb->action_plan }}</p>
                                @if ($fb->action_plan_deadline || $fb->actionPlanResponsible)
                                    <div class="flex flex-wrap gap-3 mt-3 pt-3 border-t border-indigo-100 dark:border-indigo-800">
                                        @if ($fb->action_plan_deadline)
                                            <span class="flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-300 lato-regular">
                                                <x-lucide-calendar-clock class="w-3.5 h-3.5" />
                                                Prazo: {{ $fb->action_plan_deadline->format('d/m/Y') }}
                                                @if ($fb->action_plan_deadline->isPast() && $fb->status !== 'resolvido')
                                                    ss="text-red-500 font-semibold">(vencido)</span>
                                                @endif
                                            </span>
                                        @endif
                                        @if ($fb->actionPlanResponsible)
                                            <span class="flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-300 lato-regular">
                                                <x-lucide-user-check class="w-3.5 h-3.5" />
                                                Responsável: {{ $fb->actionPlanResponsible->name }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Tarefas do plano de ação (checklist — view) --}}
                    @if ($fb->actionTasks->isNotEmpty())
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Tarefas do plano</p>
                                @php
                                    $doneCount  = $fb->actionTasks->where('completed', true)->count();
                                    $totalCount = $fb->actionTasks->count();
                                    $pct        = $totalCount > 0 ? round(($doneCount / $totalCount) * 100) : 0;
                                @endphp
                                <span class="text-[10px] lato-regular text-slate-400">{{ $doneCount }}/{{ $totalCount }} concluídas</span>
                            </div>

                            {{-- Barra de progresso --}}
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 mb-3">
                                <div class="h-1.5 rounded-full transition-all duration-500
                                            {{ $pct === 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>

                            <div class="space-y-1.5">
                                @foreach ($fb->actionTasks as $task)
                                    <div class="flex items-start gap-2.5 px-3 py-2.5 rounded-xl border
                                                {{ $task->completed
                                                    ? 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-100 dark:border-emerald-800/40'
                                                    : ($task->is_overdue
                                                        ? 'bg-red-50 dark:bg-red-900/10 border-red-100 dark:border-red-800/40'
                                                        : 'bg-white dark:bg-slate-700/40 border-slate-200 dark:border-slate-600') }}">

                                        {{-- Checkbox --}}
                                        <button type="button"
                                                wire:click="toggleTask({{ $task->id }})"
                                                class="mt-0.5 w-4 h-4 rounded-full shrink-0 flex items-center justify-center
                                                       transition cursor-pointer
                                                       {{ $task->completed
                                                           ? 'bg-emerald-500 hover:bg-emerald-600'
                                                           : 'border-2 border-slate-300 dark:border-slate-500 hover:border-emerald-400' }}">
                                            @if ($task->completed)
                                                <x-lucide-check class="w-2.5 h-2.5 text-white" />
                                            @endif
                                        </button>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm lato-regular leading-snug
                                                       {{ $task->completed
                                                           ? 'line-through text-slate-400 dark:text-slate-500'
                                                           : 'text-slate-700 dark:text-slate-200' }}">
                                                {{ $task->description }}
                                            </p>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                @if ($task->responsible)
                                                    <span class="flex items-center gap-1 text-[10px] text-slate-400 lato-regular">
                                                        <x-lucide-user class="w-3 h-3" />
                                                        {{ $task->responsible->name }}
                                                    </span>
                                                @endif
                                                @if ($task->due_date)
                                                    <span class="flex items-center gap-1 text-[10px] lato-regular
                                                                 {{ $task->is_overdue ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                                                        <x-lucide-calendar class="w-3 h-3" />
                                                        {{ $task->due_date->format('d/m/Y') }}
                                                        @if ($task->is_overdue)
                                                            (vencida)
                                                        @endif
                                                    </span>
                                                @endif
                                                @if ($task->completed && $task->completedBy)
                                                    <span class="flex items-center gap-1 text-[10px] text-emerald-500 lato-regular">
                                                        <x-lucide-check-circle class="w-3 h-3" />
                                                        {{ $task->completedBy->name }} · {{ $task->completed_at?->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Anexos --}}
                    @if (!empty($fb->attachments))
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Anexos</p>
                            <div class="space-y-2">
                                @foreach ($fb->attachments as $att)
                                    <a href="{{ asset('storage/' . $att['path']) }}" target="_blank"
                                       class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl
                                              hover:bg-slate-100 dark:hover:bg-slate-700 transition text-sm
                                              text-slate-700 dark:text-slate-200 lato-regular">
                                        <x-lucide-paperclip class="w-4 h-4 text-slate-400 shrink-0" />
                                        {{ $att['name'] }}
                                        <span class="ml-auto text-[11px] text-slate-400">{{ number_format($att['size'] / 1024, 1) }} KB</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Atualizar status --}}
                    <div class="pt-2">
                        <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Atualizar status</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ([
                                'aberto'             => ['Aberto',            '#eff6ff', '#1e40af'],
                                'em_analise'         => ['Em análise',         '#fefce8', '#854d0e'],
                                'aguardando_plano'   => ['Aguard. plano',      '#fff7ed', '#9a3412'],
                                'plano_em_andamento' => ['Plano em andamento', '#f5f3ff', '#5b21b6'],
                                'resolvido'          => ['Resolvido',          '#ecfdf5', '#166534'],
                                'arquivado'          => ['Arquivado',          '#f1f5f9', '#475569'],
                            ] as $val => [$label, $bg, $col])
                                <button type="button"
                                        wire:click="atualizarStatus({{ $fb->id }}, '{{ $val }}')"
                                        class="text-xs px-3 py-1.5 rounded-lg font-semibold lato-bold transition cursor-pointer border"
                                        style="background:{{ $bg }};color:{{ $col }};border-color:{{ $col }}44;
                                               {{ $fb->status === $val ? 'box-shadow:0 0 0 2px ' . $col . '66' : '' }}">
                                    {{ $label }} @if ($fb->status === $val) ✓ @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Histórico de status --}}
                    @if ($fb->statusHistory->isNotEmpty())
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-3">Histórico</p>
                            <div class="space-y-0">
                                @foreach ($fb->statusHistory->sortByDesc('created_at') as $h)
                                    <div class="flex gap-3">
                                        <div class="flex flex-col items-center shrink-0">
                                            <div class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0
                                                        {{ $h->old_status === null ? 'bg-violet-500' : 'bg-emerald-500' }}"></div>
                                            @if (! $loop->last)
                                                <div class="w-px flex-1 bg-slate-200 dark:bg-slate-700 mt-1 mb-1" style="min-height: 20px"></div>
                                            @endif
                                        </div>
                                        <div class="pb-3 min-w-0">
                                            <p class="text-xs text-slate-700 dark:text-slate-200 lato-regular leading-snug">
                                                <span class="font-semibold lato-bold">{{ $h->changedBy?->name ?? 'Sistema' }}</span>
                                                @if ($h->old_status === null)
                                                    registrou como <span class="font-semibold">{{ $h->new_status_label }}</span>
                                                @else
                                                    alterou de <span class="text-slate-500">{{ $h->old_status_label }}</span>
                                                    para <span class="font-semibold">{{ $h->new_status_label }}</span>
                                                @endif
                                            </p>
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">
                                                {{ $h->created_at->format('d/m/Y H:i') }} · {{ $h->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Notas internas / comentários --}}
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-3">
                            Notas internas
                            @if ($fb->comments->isNotEmpty())
                                <span class="ml-1 text-slate-300">({{ $fb->comments->count() }})</span>
                            @endif
                        </p>

                        @forelse ($fb->comments->sortByDesc('created_at') as $comment)
                            <div class="flex items-start gap-2.5 mb-3">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600
                                            flex items-center justify-center text-white text-[10px] font-bold lato-black shrink-0">
                                    {{ mb_strtoupper(mb_substr($comment->user?->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="flex-1 bg-slate-50 dark:bg-slate-700/50 rounded-xl px-3 py-2.5">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 lato-bold">
                                            {{ $comment->user?->name ?? '—' }}
                                        </p>
                                        <div class="flex items-center gap-1">
                                            <p class="text-[11px] text-slate-400 lato-regular">{{ $comment->created_at->diffForHumans() }}</p>
                                            @if ($comment->user_id === auth()->id() || auth()->user()?->isRhOuDp())
                                                <button type="button" wire:click="deleteComment({{ $comment->id }})"
                                                        class="p-0.5 text-slate-300 hover:text-red-500 transition cursor-pointer rounded">
                                                    <x-lucide-x class="w-3 h-3" />
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular leading-relaxed">{{ $comment->comment }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 lato-regular italic mb-3">Nenhuma nota interna ainda.</p>
                        @endforelse

                        <div class="flex gap-2 mt-2">
                            <input type="text" wire:model="newComment"
                                   wire:keydown.enter="addComment({{ $fb->id }})"
                                   placeholder="Adicionar nota interna..."
                                   class="flex-1 px-3 py-2 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700
                                          border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200
                                          placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                            <button type="button" wire:click="addComment({{ $fb->id }})"
                                    class="px-4 py-2 text-xs lato-bold bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                                           shadow-sm shadow-blue-500/20 rounded-xl transition cursor-pointer shrink-0">
                                Enviar
                            </button>
                        </div>
                        @error('newComment')
                            <p class="flex items-center gap-1 text-xs text-red-500 lato-regular mt-1">
                                <x-lucide-alert-circle class="w-3 h-3" />{{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700
                            bg-white dark:bg-slate-800 flex justify-between items-center">
                    <button type="button" wire:click="editar({{ $fb->id }})" @click="$wire.fecharView()"
                            class="flex items-center gap-2 px-4 py-2 text-sm lato-bold text-blue-600 border border-blue-200
                                   rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition cursor-pointer">
                        <x-lucide-pencil class="w-4 h-4" />
                        Editar
                    </button>
                    <button type="button" wire:click="fecharView"
                            class="px-4 py-2 text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200
                                   dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif


    {{-- Modal de confirmação de exclusão --}}
    @if ($confirmDelete)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="cancelarExclusao"></div>
            <div class="relative bg-white dark:bg-slate-800 w-full max-w-sm rounded-2xl shadow-2xl z-10 overflow-hidden p-6 text-center">
                <div class="w-12 h-12 bg-red-100 dark:bg-red-900/30 rounded-full flex items-center justify-center mx-auto mb-4">
                    <x-lucide-trash-2 class="w-6 h-6 text-red-600" />
                </div>
                <h3 class="text-base font-semibold text-slate-800 dark:text-white lato-bold mb-1">Excluir feedback?</h3>
                <p class="text-sm text-slate-400 lato-regular mb-6">Esta ação é permanente e não pode ser desfeita.</p>
                <div class="flex gap-3">
                    <button type="button" wire:click="cancelarExclusao"
                            class="flex-1 px-4 py-2.5 text-sm lato-bold text-slate-600 border border-slate-200 rounded-xl
                                   hover:bg-slate-50 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button type="button" wire:click="excluir"
                            class="flex-1 px-4 py-2.5 text-sm lato-bold bg-red-600 hover:bg-red-700 text-white
                                   rounded-xl shadow-sm transition cursor-pointer">
                        Excluir
                    </button>
                </div>
            </div>
        </div>
    @endif

    <livewire:components.ui.modal.alert-modal />

</div>
