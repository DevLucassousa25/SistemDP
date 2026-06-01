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
                        {{ $ocrSearch ? 'text-indigo-500' : 'text-slate-400' }}" />
                    <input type="text" wire:model.live.debounce.400ms="ocrSearch"
                           placeholder="Buscar no currículo: Laravel, Python, inglês..."
                           class="w-full pl-10 pr-9 py-2.5 text-sm lato-regular border rounded-xl transition
                                  bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                  placeholder-slate-400 focus:outline-none
                                  {{ $ocrSearch
                                      ? 'border-indigo-400 ring-2 ring-indigo-400/30 focus:ring-indigo-400/40'
                                      : 'border-slate-200 dark:border-slate-700 focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400' }}" />
                    @if ($ocrSearch)
                    <button wire:click="$set('ocrSearch','')" class="cursor-pointer absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-indigo-600 transition">
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
                    $aColors  = ['bg-indigo-500','bg-indigo-600','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500','bg-rose-500','bg-amber-500'];
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

