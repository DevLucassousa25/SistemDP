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
                        $vsOpts = ['rascunho'=>['Rascunho','bg-slate-400'],'publicada'=>['Publicada','bg-green-400'],'pausada'=>['Pausada','bg-amber-400'],'encerrada'=>['Encerrada','bg-red-400'],'preenchida'=>['Preenchida','bg-blue-500']];
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

                                {{-- Grupo: mudança de status --}}
                                @if (!in_array($vaga->status, ['preenchida', 'encerrada']) || $vaga->status === 'encerrada')
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
                                    @if (!in_array($vaga->status, ['preenchida', 'encerrada']))
                                    <button wire:click="preencherVaga({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="cursor-pointer w-full text-left px-3 py-2 text-xs text-blue-700 dark:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 rounded-xl flex items-center gap-2.5 transition">
                                        <span class="w-6 h-6 rounded-lg bg-blue-50 dark:bg-blue-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-user-check class="w-3.5 h-3.5 text-blue-500" />
                                        </span>
                                        Vaga preenchida
                                    </button>
                                    <button wire:click="encerrarVaga({{ $vaga->id }})" @click="menu=false" type="button"
                                            class="cursor-pointer w-full text-left px-3 py-2 text-xs text-amber-700 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 rounded-xl flex items-center gap-2.5 transition">
                                        <span class="w-6 h-6 rounded-lg bg-amber-50 dark:bg-amber-900/30 flex items-center justify-center shrink-0">
                                            <x-lucide-x-circle class="w-3.5 h-3.5 text-amber-500" />
                                        </span>
                                        Encerrar sem preencher
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

