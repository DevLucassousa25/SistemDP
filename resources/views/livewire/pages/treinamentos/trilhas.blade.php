<div class="p-4 sm:p-6 lg:p-8"
     x-data
     x-effect="document.body.style.overflow = ($wire.modalTrilha || $wire.modalAddCurso) ? 'hidden' : ''">

    {{-- ───── Cabeçalho ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            @if($trilhaSelecionadaId && $this->trilhaSelecionada)
                <button wire:click="voltarLista"
                    class="cursor-pointer flex items-center gap-1.5 text-xs text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 mb-2 transition lato-regular">
                    <x-lucide-arrow-left class="w-3.5 h-3.5" /> Voltar
                </button>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-white lato-black">
                    {{ $this->trilhaSelecionada->titulo }}
                </h1>
                <p class="text-sm text-slate-400 mt-1 lato-regular">Trilha de aprendizado</p>
            @else
                <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white lato-black">
                    Trilhas de Aprendizado
                </h1>
                <p class="text-sm text-slate-400 mt-1 lato-regular">
                    Percursos de desenvolvimento estruturados para você evoluir
                </p>
            @endif
        </div>
        <div class="flex gap-2">
            @if(auth()->user()?->isRhOuDp() && !$trilhaSelecionadaId)
                <button wire:click="abrirModalTrilha('criar')"
                    class="cursor-pointer flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-indigo-500/20 text-white text-sm px-4 py-2.5 rounded-lg transition lato-bold">
                    <x-lucide-plus class="w-4 h-4" /> Nova trilha
                </button>
            @endif
            @if(auth()->user()?->isRhOuDp() && $trilhaSelecionadaId)
                <button wire:click="abrirModalAddCurso"
                    class="cursor-pointer flex items-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm px-4 py-2.5 rounded-lg transition lato-bold shadow-sm">
                    <x-lucide-plus class="w-4 h-4" /> Adicionar curso
                </button>
                <button wire:click="abrirModalTrilha('editar', {{ $trilhaSelecionadaId }})"
                    class="cursor-pointer flex items-center gap-2 border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 text-sm px-4 py-2.5 rounded-lg transition lato-regular">
                    <x-lucide-pencil class="w-4 h-4" /> Editar
                </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         DETALHE DA TRILHA SELECIONADA
    ══════════════════════════════════════════════════════════════════ --}}
    @if($trilhaSelecionadaId && $this->trilhaSelecionada)
        @php $trilha = $this->trilhaSelecionada; @endphp

        {{-- Info da trilha --}}
        <div class="mb-6 bg-gradient-to-r from-blue-500 to-indigo-700 rounded-2xl p-6 text-white">
            <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                <div class="flex-1">
                    <div class="flex flex-wrap items-center gap-2 mb-2">
                        @if($trilha->categoria)
                            <span class="px-2 py-0.5 text-xs rounded-full bg-white/20 text-white lato-bold">{{ $trilha->categoria }}</span>
                        @endif
                        <span class="px-2 py-0.5 text-xs rounded-full bg-white/20 text-white lato-regular">
                            {{ $trilha->trilhaCursos->count() }} curso(s)
                        </span>
                        <span class="px-2 py-0.5 text-xs rounded-full bg-white/20 text-white lato-regular">
                            {{ $trilha->carga_horaria_formatada }}
                        </span>
                    </div>
                    @if($trilha->descricao)
                        <p class="text-sm text-white/80 lato-regular">{{ $trilha->descricao }}</p>
                    @endif
                </div>
                {{-- Progresso do usuário na trilha --}}
                @if($this->inscricaoDoUsuario)
                    <div class="shrink-0 text-center bg-white/10 rounded-xl p-4 min-w-[120px]">
                        <p class="text-3xl lato-black">{{ $this->inscricaoDoUsuario->progresso }}%</p>
                        <p class="text-xs text-white/70 lato-regular mt-1">concluído</p>
                    </div>
                @else
                    <button wire:click="inscreverTrilha({{ $trilha->id }})"
                        class="cursor-pointer shrink-0 flex items-center gap-2 bg-white text-violet-700 hover:bg-violet-50 text-sm font-semibold px-5 py-2.5 rounded-xl transition lato-bold">
                        <x-lucide-play class="w-4 h-4" /> Começar trilha
                    </button>
                @endif
            </div>
        </div>

        {{-- Lista de cursos da trilha --}}
        <div class="space-y-3">
            @forelse($trilha->trilhaCursos->sortBy('ordem') as $idx => $tc)
                @php
                    $trein     = $tc->treinamento;
                    $inscricao = $trein->inscricaoDoUsuario(auth()->id());
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
                    {{-- Número --}}
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 text-sm lato-black
                        {{ $inscricao?->status === 'concluido' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-600 dark:text-emerald-400' : 'bg-violet-50 dark:bg-violet-900/20 text-indigo-600 dark:text-indigo-400' }}">
                        @if($inscricao?->status === 'concluido')
                            <x-lucide-check class="w-5 h-5" />
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </div>

                    {{-- Info do curso --}}
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2">
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $trein->titulo }}</p>
                            @if($tc->obrigatorio)
                                <span class="text-xs bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400 px-2 py-0.5 rounded-full lato-bold">Obrigatório</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 mt-1 text-xs text-slate-400 lato-regular">
                            <span>{{ ucfirst($trein->nivel) }}</span>
                            <span>{{ $trein->carga_horaria_formatada }}</span>
                            @if($inscricao)
                                <span class="text-indigo-500 lato-bold">{{ $inscricao->progresso }}% concluído</span>
                            @endif
                        </div>
                        @if($inscricao)
                            <div class="mt-1.5 w-40 h-1 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: {{ $inscricao->progresso }}%"></div>
                            </div>
                        @endif
                    </div>

                    {{-- Ações --}}
                    <div class="flex items-center gap-2 shrink-0">
                        <a href="{{ route('treinamentos.curso', $trein->id) }}" wire:navigate
                            class="flex items-center gap-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-xs px-3 py-2 rounded-lg transition lato-bold">
                            <x-lucide-play class="w-3.5 h-3.5" />
                            {{ $inscricao?->status === 'concluido' ? 'Rever' : ($inscricao ? 'Continuar' : 'Iniciar') }}
                        </a>
                        @if(auth()->user()?->isRhOuDp())
                            <button wire:click="removerCursoDaTrilha({{ $tc->id }})"
                                wire:confirm="Remover este curso da trilha?"
                                class="cursor-pointer p-2 text-slate-300 dark:text-slate-600 hover:text-red-500 transition">
                                <x-lucide-trash-2 class="w-4 h-4" />
                            </button>
                        @endif
                    </div>
                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-slate-400">
                    <x-lucide-layers class="w-10 h-10 mb-3 opacity-40" />
                    <p class="text-sm lato-bold">Nenhum curso adicionado à trilha</p>
                    @if(auth()->user()?->isRhOuDp())
                        <button wire:click="abrirModalAddCurso"
                            class="cursor-pointer mt-3 text-sm text-indigo-600 dark:text-indigo-400 hover:underline lato-bold">
                            Adicionar primeiro curso →
                        </button>
                    @endif
                </div>
            @endforelse
        </div>

        {{-- Estatísticas da trilha (RH) --}}
        @if(auth()->user()?->isRhOuDp() && $trilha->inscricoes->isNotEmpty())
            <div class="mt-8">
                <h3 class="text-sm lato-bold text-slate-700 dark:text-slate-300 mb-3">Inscritos na trilha ({{ $trilha->inscricoes->count() }})</h3>
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                    <table class="w-full text-sm">
                        <thead class="bg-slate-50 dark:bg-slate-900/50 border-b border-slate-200 dark:border-slate-700">
                            <tr>
                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500">Colaborador</th>
                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 hidden sm:table-cell">Status</th>
                                <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500">Progresso</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                            @foreach($trilha->inscricoes as $ins)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                                <td class="px-4 py-3">
                                    <p class="lato-bold text-slate-700 dark:text-slate-200 text-xs">{{ $ins->usuario->name }}</p>
                                </td>
                                <td class="px-4 py-3 hidden sm:table-cell">
                                    <span class="px-2 py-0.5 text-xs rounded-full lato-bold
                                        {{ $ins->status === 'concluida' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700' }}">
                                        {{ $ins->status === 'concluida' ? 'Concluída' : 'Em andamento' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <div class="w-20 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full bg-indigo-600 rounded-full" style="width: {{ $ins->progresso }}%"></div>
                                        </div>
                                        <span class="text-xs text-slate-400 lato-regular">{{ $ins->progresso }}%</span>
                                    </div>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    {{-- ══════════════════════════════════════════════════════════════════
         LISTA DE TRILHAS
    ══════════════════════════════════════════════════════════════════ --}}
    @else

        {{-- Abas --}}
        <div class="flex gap-1 bg-slate-100 dark:bg-slate-800 rounded-xl p-1 w-fit mb-6">
            <button wire:click="$set('aba','catalogo')"
                class="cursor-pointer px-4 py-2 text-sm rounded-lg transition lato-bold
                    {{ $aba === 'catalogo' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700' }}">
                <x-lucide-layers class="w-4 h-4 inline -mt-0.5 mr-1" /> Trilhas disponíveis
            </button>
            <button wire:click="$set('aba','minhas_trilhas')"
                class="cursor-pointer px-4 py-2 text-sm rounded-lg transition lato-bold
                    {{ $aba === 'minhas_trilhas' ? 'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700' }}">
                <x-lucide-bookmark class="w-4 h-4 inline -mt-0.5 mr-1" /> Minhas Trilhas
                @if($this->minhasTrilhas->isNotEmpty())
                    <span class="ml-1 text-xs bg-violet-100 dark:bg-violet-900/30 text-indigo-600 dark:text-indigo-400 px-1.5 rounded-full">{{ $this->minhasTrilhas->count() }}</span>
                @endif
            </button>
        </div>

        {{-- Busca --}}
        <div class="mb-5 relative max-w-sm">
            <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400" />
            <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar trilha…"
                class="w-full pl-9 pr-4 py-2.5 text-sm rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-500 lato-regular" />
        </div>

        @if($aba === 'catalogo')
            {{-- Grid de trilhas --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5">
                @forelse(auth()->user()?->isRhOuDp() ? $this->todasTrilhas : $this->trilhas as $trilha)
                    @php
                        $minha = $this->minhasTrilhas->firstWhere('trilha_id', $trilha->id);
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden hover:shadow-lg hover:shadow-slate-200/60 dark:hover:shadow-slate-900/40 transition-all duration-200">
                        {{-- Banner --}}
                        <div class="h-28 bg-gradient-to-br from-blue-500 to-indigo-700 relative flex items-center justify-center">
                            <x-lucide-layers class="w-10 h-10 text-white/30" />
                            {{-- Status badge (RH) --}}
                            @if(auth()->user()?->isRhOuDp() && $trilha->status !== 'ativa')
                                <span class="absolute top-2 right-2 px-2 py-0.5 text-xs rounded-full bg-black/30 text-white lato-bold">
                                    {{ ucfirst($trilha->status) }}
                                </span>
                            @endif
                            @if($minha)
                                <div class="absolute bottom-2 right-2 w-8 h-8 rounded-full bg-white/20 flex items-center justify-center">
                                    <x-lucide-check class="w-4 h-4 text-white" />
                                </div>
                            @endif
                        </div>

                        {{-- Corpo --}}
                        <div class="p-4">
                            @if($trilha->categoria)
                                <span class="text-xs bg-violet-100 dark:bg-violet-900/30 text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded-full lato-bold">{{ $trilha->categoria }}</span>
                            @endif
                            <h3 class="mt-2 text-sm font-semibold text-slate-800 dark:text-white lato-bold">{{ $trilha->titulo }}</h3>
                            @if($trilha->descricao)
                                <p class="text-xs text-slate-400 lato-regular mt-1 line-clamp-2">{{ $trilha->descricao }}</p>
                            @endif
                            <div class="flex items-center gap-3 mt-2 text-xs text-slate-400 lato-regular">
                                <span><x-lucide-layers class="w-3 h-3 inline -mt-0.5" /> {{ $trilha->trilha_cursos_count }} curso(s)</span>
                                @if(auth()->user()?->isRhOuDp())
                                    <span><x-lucide-users class="w-3 h-3 inline -mt-0.5" /> {{ $trilha->inscricoes->count() }} inscrito(s)</span>
                                @endif
                            </div>

                            @if($minha)
                                <div class="mt-3">
                                    <div class="flex justify-between text-xs text-slate-400 mb-1">
                                        <span class="lato-regular">Progresso</span>
                                        <span class="lato-bold">{{ $minha->progresso }}%</span>
                                    </div>
                                    <div class="w-full h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: {{ $minha->progresso }}%"></div>
                                    </div>
                                </div>
                            @endif

                            <div class="mt-4 flex gap-2">
                                <button wire:click="selecionarTrilha({{ $trilha->id }})"
                                    class="cursor-pointer flex-1 flex items-center justify-center gap-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-xs px-3 py-2 rounded-lg transition lato-bold">
                                    <x-lucide-eye class="w-3.5 h-3.5" /> Ver trilha
                                </button>
                                @if(auth()->user()?->isRhOuDp())
                                    <button wire:click="excluirTrilha({{ $trilha->id }})"
                                        wire:confirm="Excluir esta trilha permanentemente?"
                                        class="cursor-pointer p-2 text-slate-300 dark:text-slate-600 hover:text-red-500 transition rounded-lg">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full flex flex-col items-center justify-center py-20 text-slate-400">
                        <x-lucide-layers class="w-12 h-12 mb-3 opacity-40" />
                        <p class="text-sm lato-bold">Nenhuma trilha disponível</p>
                        @if(auth()->user()?->isRhOuDp())
                            <button wire:click="abrirModalTrilha('criar')"
                                class="cursor-pointer mt-4 text-sm text-indigo-600 dark:text-indigo-400 hover:underline lato-bold">
                                Criar primeira trilha →
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>
        @endif

        @if($aba === 'minhas_trilhas')
            <div class="space-y-4">
                @forelse($this->minhasTrilhas as $ins)
                    @php $t = $ins->trilha; @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-5">
                        <div class="flex flex-col sm:flex-row sm:items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-700 flex items-center justify-center shrink-0">
                                <x-lucide-layers class="w-6 h-6 text-white" />
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex flex-wrap items-center gap-2">
                                    <h3 class="text-sm lato-bold text-slate-800 dark:text-white">{{ $t->titulo }}</h3>
                                    <span class="px-2 py-0.5 text-xs rounded-full lato-bold
                                        {{ $ins->status === 'concluida' ? 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300' : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300' }}">
                                        {{ $ins->status === 'concluida' ? 'Concluída' : 'Em andamento' }}
                                    </span>
                                </div>
                                <p class="text-xs text-slate-400 lato-regular mt-1">
                                    {{ $t->trilhaCursos->count() }} curso(s) · Iniciada em {{ $ins->created_at->format('d/m/Y') }}
                                </p>
                                <div class="mt-2 flex items-center gap-3">
                                    <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="h-full bg-gradient-to-r from-blue-500 to-indigo-600 rounded-full" style="width: {{ $ins->progresso }}%"></div>
                                    </div>
                                    <span class="text-xs lato-bold text-slate-500 shrink-0">{{ $ins->progresso }}%</span>
                                </div>
                            </div>
                            <button wire:click="selecionarTrilha({{ $t->id }})"
                                class="cursor-pointer shrink-0 flex items-center gap-1.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-xs px-3 py-2 rounded-lg transition lato-bold">
                                <x-lucide-play class="w-3.5 h-3.5" />
                                Continuar
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center py-20 text-slate-400">
                        <x-lucide-bookmark class="w-12 h-12 mb-3 opacity-40" />
                        <p class="text-sm lato-bold">Você ainda não iniciou nenhuma trilha</p>
                        <button wire:click="$set('aba','catalogo')"
                            class="cursor-pointer mt-4 text-sm text-indigo-600 dark:text-indigo-400 hover:underline lato-bold">
                            Ver trilhas disponíveis →
                        </button>
                    </div>
                @endforelse
            </div>
        @endif
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAIS
    ══════════════════════════════════════════════════════════════════ --}}

    {{-- Modal: Criar/Editar Trilha --}}
    @if($modalTrilha)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
         x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.set('modalTrilha', false), 300); } }"
         x-init="requestAnimationFrame(() => open = true)"
         @click.self="close()">
        <div class="bg-white dark:bg-slate-800 shadow-2xl w-full sm:max-w-md sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
             :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
             @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shadow-sm shadow-indigo-500/20">
                        <x-lucide-layers class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">
                            {{ $modalTrilhaModo === 'criar' ? 'Nova Trilha' : 'Editar Trilha' }}
                        </h3>
                        <p class="text-[10px] text-slate-400 lato-regular">
                            {{ $modalTrilhaModo === 'criar' ? 'Crie um percurso de aprendizado' : 'Atualize as informações' }}
                        </p>
                    </div>
                </div>
                <button @click="close()"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="px-5 py-5 space-y-4">
                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Título *</label>
                    <input wire:model="trilhaTitulo" type="text" placeholder="Ex: Liderança para Gestores"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400 transition" />
                    @error('trilhaTitulo') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Descrição</label>
                    <textarea wire:model="trilhaDescricao" rows="3" placeholder="Descreva o objetivo desta trilha…"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400 resize-none transition"></textarea>
                </div>

                <div class="grid grid-cols-2 gap-3">
                    {{-- Categoria --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Categoria</label>
                        <div class="relative">
                            <x-lucide-tag class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                            <input wire:model="trilhaCategoria" type="text" placeholder="Ex: Gestão…"
                                class="w-full pl-8 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-800 dark:text-slate-100 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400 transition" />
                        </div>
                    </div>

                    {{-- Status Alpine dropdown --}}
                    <div x-data="{ open: false }" @click.outside="open = false" class="relative">
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Status *</label>
                        @php $statusMapT = ['rascunho'=>['Rascunho','bg-amber-400'],'ativa'=>['Ativa','bg-emerald-400'],'arquivada'=>['Arquivada','bg-slate-400']]; @endphp
                        <button @click="open = !open" type="button"
                            class="cursor-pointer w-full flex items-center justify-between px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900/50 text-slate-700 dark:text-slate-200 lato-regular transition hover:border-indigo-400/50">
                            <span class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full shrink-0 {{ $statusMapT[$trilhaStatus][1] ?? 'bg-slate-400' }}"></span>
                                {{ $statusMapT[$trilhaStatus][0] ?? ucfirst($trilhaStatus) }}
                            </span>
                            <x-lucide-chevron-down class="w-3.5 h-3.5 text-slate-400 transition-transform" ::class="{'rotate-180':open}" />
                        </button>
                        <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                             class="absolute top-full left-0 mt-1 z-50 w-full bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 shadow-lg py-1" style="display:none">
                            @foreach([['rascunho','Rascunho','bg-amber-400'],['ativa','Ativa','bg-emerald-400'],['arquivada','Arquivada','bg-slate-400']] as [$v,$l,$d])
                            <button type="button" @click="$wire.set('trilhaStatus','{{ $v }}'); open=false"
                                class="cursor-pointer w-full flex items-center gap-2.5 px-3 py-2 text-sm lato-regular transition {{ $trilhaStatus===$v ? 'bg-violet-50 dark:bg-violet-900/20 text-violet-700 dark:text-violet-300' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <span class="w-2 h-2 rounded-full {{ $d }} shrink-0"></span> {{ $l }}
                                @if($trilhaStatus===$v) <x-lucide-check class="w-3 h-3 ml-auto text-indigo-500" /> @endif
                            </button>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button @click="close()"
                    class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                           text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Cancelar</button>
                <button wire:click="salvarTrilha"
                    class="cursor-pointer flex-1 px-5 py-2.5 text-xs lato-bold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600
                           text-white hover:from-blue-600 hover:to-indigo-700 transition shadow-sm shadow-indigo-500/20">
                    {{ $modalTrilhaModo === 'criar' ? 'Criar trilha' : 'Salvar alterações' }}
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- Modal: Adicionar curso à trilha --}}
    @if($modalAddCurso)
    <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
         x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.set('modalAddCurso', false), 300); } }"
         x-init="requestAnimationFrame(() => open = true)"
         @click.self="close()">
        <div class="bg-white dark:bg-slate-800 shadow-2xl w-full sm:max-w-md sm:mx-4 flex flex-col
                    rounded-t-3xl sm:rounded-2xl
                    border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                    transition-transform duration-300 ease-out will-change-transform"
             :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
             @click.stop>

            {{-- Drag handle --}}
            <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <x-lucide-plus-circle class="w-4 h-4 text-indigo-500" />
                    Adicionar Curso à Trilha
                </h3>
                <button @click="close()"
                    class="cursor-pointer p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="px-5 py-5 space-y-4">
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Curso *</label>
                    <select wire:model="addCursoId"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular">
                        <option value="">Selecione um curso…</option>
                        @foreach($this->cursosDisponiveis as $c)
                            <option value="{{ $c->id }}">{{ $c->titulo }} ({{ ucfirst($c->nivel) }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Ordem na trilha</label>
                    <input wire:model="addCursoOrdem" type="number" min="0"
                        class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular" />
                </div>
                <label class="flex items-center gap-2.5 cursor-pointer">
                    <input type="checkbox" wire:model="addCursoObrig" class="accent-indigo-500 w-4 h-4 rounded" />
                    <span class="text-sm lato-regular text-slate-700 dark:text-slate-300">Curso obrigatório na trilha</span>
                </label>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0
                        pb-[max(1rem,env(safe-area-inset-bottom))]">
                <button @click="close()"
                    class="cursor-pointer flex-1 sm:flex-none px-4 py-2.5 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                           text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition">Cancelar</button>
                <button wire:click="salvarCursoNaTrilha"
                    class="cursor-pointer flex-1 px-5 py-2.5 text-xs lato-bold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600
                           text-white hover:from-blue-600 hover:to-indigo-700 disabled:opacity-50 transition shadow-sm">
                    Adicionar
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
