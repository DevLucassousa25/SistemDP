<div class="p-4 sm:p-6 lg:p-8 space-y-6">

    {{-- ── Cabeçalho ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-xl sm:text-2xl lato-black tracking-tight text-slate-800 dark:text-white">
                Testes Online
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular mt-1">
                Crie e gerencie testes para avaliação de candidatos
            </p>
        </div>
        <button wire:click="openCreate" type="button"
                class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm shrink-0">
            <x-lucide-plus class="w-4 h-4" /> Novo Teste
        </button>
    </div>

    {{-- ── Tabs ────────────────────────────────────────────────────────── --}}
    <div class="flex items-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 w-fit">
        @foreach (['testes' => 'Testes', 'resultados' => 'Resultados'] as $tabKey => $tabLabel)
        <button wire:click="$set('activeTab', '{{ $tabKey }}')" type="button"
                class="flex items-center gap-1.5 px-5 py-1.5 text-sm lato-bold rounded-lg transition
                       {{ $activeTab === $tabKey
                           ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm'
                           : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            @if ($tabKey === 'testes') <x-lucide-clipboard-list class="w-3.5 h-3.5" /> @else <x-lucide-bar-chart-2 class="w-3.5 h-3.5" /> @endif
            {{ $tabLabel }}
        </button>
        @endforeach
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: TESTES                                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($activeTab === 'testes')

    {{-- Busca --}}
    <div class="relative max-w-sm">
        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
        <input type="text" wire:model.live.debounce.400ms="search" placeholder="Buscar teste..."
               class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
    </div>

    {{-- Grid de cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-3 gap-4">
        @forelse ($this->testes as $teste)
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 hover:shadow-md transition flex flex-col gap-3">
            {{-- Topo --}}
            <div class="flex items-start gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                    <x-lucide-clipboard-list class="w-5 h-5 text-indigo-500" />
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-black text-slate-800 dark:text-white truncate">{{ $teste->titulo }}</p>
                    @if ($teste->descricao)
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular mt-0.5 line-clamp-2">{{ $teste->descricao }}</p>
                    @endif
                </div>
                <span class="shrink-0 px-2 py-0.5 rounded-full text-[11px] lato-bold
                             {{ $teste->ativo ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' : 'bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400' }}">
                    {{ $teste->ativo ? 'Ativo' : 'Inativo' }}
                </span>
            </div>

            {{-- Stats --}}
            <div class="grid grid-cols-2 gap-2 text-xs lato-regular text-slate-500 dark:text-slate-400">
                <div class="flex items-center gap-1.5">
                    <x-lucide-help-circle class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ $teste->questoes_count ?? $teste->questoes?->count() ?? 0 }} questões</span>
                </div>
                <div class="flex items-center gap-1.5">
                    <x-lucide-users class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ $teste->tentativas_count ?? $teste->tentativas?->count() ?? 0 }} tentativas</span>
                </div>
                @if ($teste->tempo_limite)
                <div class="flex items-center gap-1.5">
                    <x-lucide-clock class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ $teste->tempo_limite }} min</span>
                </div>
                @endif
                @if ($teste->nota_aprovacao)
                <div class="flex items-center gap-1.5">
                    <x-lucide-award class="w-3.5 h-3.5 text-amber-400" />
                    <span>Aprovação: {{ $teste->nota_aprovacao }}</span>
                </div>
                @endif
            </div>

            {{-- Botões --}}
            <div class="flex items-center gap-2 pt-2 border-t border-slate-100 dark:border-slate-700">
                <button wire:click="openDrawer({{ $teste->id }})" type="button"
                        class="flex-1 flex items-center justify-center gap-1 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                    <x-lucide-eye class="w-3.5 h-3.5" /> Ver
                </button>
                <button wire:click="openEdit({{ $teste->id }})" type="button"
                        class="flex-1 flex items-center justify-center gap-1 px-3 py-2 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                    <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                </button>
                <button wire:click="openEnviar({{ $teste->id }})" type="button"
                        class="flex-1 flex items-center justify-center gap-1 px-3 py-2 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 text-xs lato-bold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                    <x-lucide-send class="w-3.5 h-3.5" /> Enviar
                </button>
                <button wire:click="confirmDelete({{ $teste->id }})" type="button"
                        class="p-2 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition">
                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                </button>
            </div>
        </div>
        @empty
        <div class="col-span-full flex flex-col items-center justify-center py-20 text-center">
            <div class="w-16 h-16 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-4">
                <x-lucide-clipboard-list class="w-8 h-8 text-slate-400" />
            </div>
            <p class="text-slate-500 dark:text-slate-400 lato-bold">Nenhum teste criado</p>
            <p class="text-slate-400 dark:text-slate-500 text-sm lato-regular mt-1">Crie seu primeiro teste de avaliação clicando em "Novo Teste".</p>
        </div>
        @endforelse
    </div>

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- TAB: RESULTADOS                                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @elseif ($activeTab === 'resultados')

    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900/50">
                        <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Candidato</th>
                        <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Teste</th>
                        <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden md:table-cell">Vaga</th>
                        <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-cell">Data</th>
                        <th class="text-left px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider hidden lg:table-cell">Tempo</th>
                        <th class="text-center px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Nota</th>
                        <th class="text-center px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Status</th>
                        <th class="text-center px-4 py-3 text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Ação</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-700">
                    @forelse ($this->resultados as $res)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-900/30 transition">
                        <td class="px-4 py-3">
                            <p class="lato-bold text-slate-800 dark:text-white text-sm">{{ $res->curriculo?->nome ?? '—' }}</p>
                            <p class="text-xs text-slate-500 lato-regular">{{ $res->curriculo?->email ?? '' }}</p>
                        </td>
                        <td class="px-4 py-3">
                            <p class="lato-regular text-slate-700 dark:text-slate-300 text-sm">{{ $res->teste?->titulo ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3 hidden md:table-cell">
                            <p class="lato-regular text-slate-600 dark:text-slate-400 text-sm">{{ $res->candidatura?->vaga?->titulo ?? '—' }}</p>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <p class="lato-regular text-slate-500 dark:text-slate-400 text-xs">{{ $res->created_at?->format('d/m/Y H:i') }}</p>
                        </td>
                        <td class="px-4 py-3 hidden lg:table-cell">
                            <p class="lato-regular text-slate-500 dark:text-slate-400 text-xs">
                                @if ($res->tempo_gasto) {{ gmdate('H:i:s', $res->tempo_gasto * 60) }} @else — @endif
                            </p>
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($res->nota_final !== null)
                            <span class="text-sm lato-black {{ $res->aprovado ? 'text-green-500' : 'text-red-500' }}">
                                {{ number_format($res->nota_final, 1) }}
                            </span>
                            @else
                            <span class="text-xs text-slate-400 lato-regular">—</span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($res->nota_final !== null)
                            <span class="px-2.5 py-1 rounded-full text-[11px] lato-bold
                                         {{ $res->aprovado
                                             ? 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400'
                                             : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                {{ $res->aprovado ? 'Aprovado' : 'Reprovado' }}
                            </span>
                            @elseif ($res->finalizado_em)
                            <span class="px-2.5 py-1 rounded-full text-[11px] lato-bold bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">
                                Corrigir
                            </span>
                            @else
                            <span class="px-2.5 py-1 rounded-full text-[11px] lato-bold bg-slate-100 text-slate-600 dark:bg-slate-700 dark:text-slate-400">
                                Em andamento
                            </span>
                            @endif
                        </td>
                        <td class="px-4 py-3 text-center">
                            @if ($res->finalizado_em && $res->nota_final === null)
                            <button wire:click="corrigirTeste({{ $res->id }})" type="button"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                                <x-lucide-check-circle class="w-3.5 h-3.5" /> Corrigir
                            </button>
                            @else
                            <span class="text-xs text-slate-300 dark:text-slate-600">—</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="py-16 text-center">
                            <div class="flex flex-col items-center gap-3">
                                <x-lucide-bar-chart-2 class="w-8 h-8 text-slate-300 dark:text-slate-600" />
                                <p class="text-sm text-slate-400 dark:text-slate-500 lato-regular">Nenhum resultado registrado ainda.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- DRAWER: Detalhe do teste                                           --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($drawerOpen && $this->drawerTeste)
    @php $dt = $this->drawerTeste; @endphp
    <div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('drawerOpen', false)"></div>
    <div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] lg:w-[560px] bg-white dark:bg-slate-800 shadow-2xl overflow-y-auto">

        {{-- Header --}}
        <div class="sticky top-0 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4 flex items-center gap-3 z-10">
            <div class="w-10 h-10 rounded-xl bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                <x-lucide-clipboard-list class="w-5 h-5 text-indigo-500" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm lato-black text-slate-800 dark:text-white truncate">{{ $dt->titulo }}</p>
                <p class="text-xs text-slate-400 lato-regular mt-0.5">
                    {{ $dt->questoes?->count() ?? 0 }} questões
                    @if ($dt->tempo_limite) · {{ $dt->tempo_limite }} min @endif
                    @if ($dt->nota_aprovacao) · Aprovação: {{ $dt->nota_aprovacao }} @endif
                </p>
            </div>
            <button wire:click="$set('drawerOpen', false)" type="button"
                    class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                <x-lucide-x class="w-5 h-5" />
            </button>
        </div>

        <div class="px-6 py-4 space-y-6">
            {{-- Descrição --}}
            @if ($dt->descricao)
            <div>
                <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Descrição</h4>
                <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed">{{ $dt->descricao }}</p>
            </div>
            @endif

            @if ($dt->instrucoes)
            <div>
                <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Instruções ao candidato</h4>
                <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">{{ $dt->instrucoes }}</p>
            </div>
            @endif

            {{-- Configurações --}}
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

            {{-- Estatísticas --}}
            @php
                $tentativas = $dt->tentativas ?? collect();
                $totalTent = $tentativas->count();
                $aprovadas = $tentativas->where('aprovado', true)->count();
                $mediaFinal = $totalTent > 0 ? $tentativas->whereNotNull('nota_final')->avg('nota_final') : null;
            @endphp
            @if ($totalTent > 0)
            <div>
                <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Desempenho geral</h4>
                <div class="grid grid-cols-3 gap-3">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-center">
                        <p class="text-xl lato-black text-slate-800 dark:text-white">{{ $totalTent }}</p>
                        <p class="text-xs text-slate-400 lato-regular mt-0.5">Tentativas</p>
                    </div>
                    <div class="p-3 rounded-xl bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 text-center">
                        <p class="text-xl lato-black text-green-600 dark:text-green-400">{{ $aprovadas }}</p>
                        <p class="text-xs text-green-600 dark:text-green-400 lato-regular mt-0.5">Aprovados</p>
                    </div>
                    <div class="p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 text-center">
                        <p class="text-xl lato-black text-blue-600 dark:text-blue-400">{{ $mediaFinal !== null ? number_format($mediaFinal, 1) : '—' }}</p>
                        <p class="text-xs text-blue-600 dark:text-blue-400 lato-regular mt-0.5">Média</p>
                    </div>
                </div>
            </div>
            @endif

            {{-- Questões --}}
            @if ($dt->questoes && $dt->questoes->count())
            <div>
                <h4 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">
                    Questões ({{ $dt->questoes->count() }})
                </h4>
                <div class="space-y-4">
                    @foreach ($dt->questoes as $qi => $q)
                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                        <div class="flex items-start gap-3 mb-3">
                            <span class="shrink-0 w-6 h-6 rounded-lg bg-indigo-100 dark:bg-indigo-900/40 text-indigo-600 dark:text-indigo-300 text-xs lato-black flex items-center justify-center">
                                {{ $qi + 1 }}
                            </span>
                            <div class="flex-1">
                                <p class="text-sm lato-bold text-slate-800 dark:text-white leading-relaxed">{{ $q->enunciado }}</p>
                                <div class="flex items-center gap-2 mt-1">
                                    <span class="text-[11px] lato-bold px-2 py-0.5 rounded-full {{ $q->tipo === 'objetiva' ? 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300' : 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-violet-300' }}">
                                        {{ ucfirst($q->tipo) }}
                                    </span>
                                    @if ($q->peso)
                                    <span class="text-[11px] text-slate-400 lato-regular">Peso: {{ $q->peso }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @if ($q->tipo === 'objetiva' && $q->opcoes && $q->opcoes->count())
                        <div class="space-y-1.5 ml-9">
                            @foreach ($q->opcoes as $op)
                            <div class="flex items-center gap-2 p-2 rounded-lg {{ $op->correta ? 'bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700' }}">
                                @if ($op->correta)
                                <x-lucide-check-circle class="w-4 h-4 text-green-500 shrink-0" />
                                @else
                                <div class="w-4 h-4 rounded-full border-2 border-slate-300 dark:border-slate-600 shrink-0"></div>
                                @endif
                                <p class="text-sm {{ $op->correta ? 'text-green-700 dark:text-green-300 lato-bold' : 'text-slate-600 dark:text-slate-400 lato-regular' }}">{{ $op->texto }}</p>
                            </div>
                            @endforeach
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

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Criar / Editar Teste                                        --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($testeModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-3xl max-h-[95vh] flex flex-col">

            {{-- Header --}}
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-700 shrink-0">
                <h2 class="text-base lato-black text-slate-800 dark:text-white">
                    {{ $testeId ? 'Editar Teste' : 'Novo Teste' }}
                </h2>
                <button wire:click="$set('testeModal', false)" type="button"
                        class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            {{-- Corpo rolável --}}
            <div class="flex-1 overflow-y-auto px-6 py-5 space-y-6">

                {{-- Seção: Informações básicas --}}
                <div>
                    <h3 class="text-xs lato-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">Informações básicas</h3>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Título do teste</label>
                            <input type="text" wire:model="testeTitulo" placeholder="Ex: Teste de Raciocínio Lógico"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                            @error('testeTitulo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Descrição</label>
                            <textarea wire:model="testeDescricao" rows="2" placeholder="Breve descrição do teste..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                            @error('testeDescricao') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Instruções ao candidato</label>
                            <textarea wire:model="testeInstrucoes" rows="3" placeholder="Instruções exibidas antes do início do teste..."
                                      class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                            @error('testeInstrucoes') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                    </div>
                </div>

                {{-- Seção: Configurações --}}
                <div>
                    <h3 class="text-xs lato-black text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-4">Configurações</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Tempo limite (minutos)</label>
                            <input type="number" wire:model="testeTempo" min="0" placeholder="Ex: 60"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                            @error('testeTempo') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Nota de aprovação (0–10)</label>
                            <input type="number" wire:model="testeNota" min="0" max="10" step="0.1" placeholder="Ex: 7.0"
                                   class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                            @error('testeNota') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        </div>
                        {{-- Checkboxes --}}
                        <div class="flex flex-col gap-3">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <div class="relative">
                                    <input type="checkbox" wire:model="testeRandomQ" class="sr-only peer" />
                                    <div class="w-10 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-indigo-500 transition"></div>
                                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                                </div>
                                <span class="text-sm lato-regular text-slate-700 dark:text-slate-200">Randomizar questões</span>
                            </label>
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <div class="relative">
                                    <input type="checkbox" wire:model="testeRandomO" class="sr-only peer" />
                                    <div class="w-10 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-indigo-500 transition"></div>
                                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                                </div>
                                <span class="text-sm lato-regular text-slate-700 dark:text-slate-200">Randomizar opções</span>
                            </label>
                        </div>
                        <div class="flex items-center">
                            <label class="flex items-center gap-3 cursor-pointer select-none">
                                <div class="relative">
                                    <input type="checkbox" wire:model="testeAtivo" class="sr-only peer" />
                                    <div class="w-10 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-green-500 transition"></div>
                                    <div class="absolute top-1 left-1 w-4 h-4 bg-white rounded-full shadow transition peer-checked:translate-x-4"></div>
                                </div>
                                <span class="text-sm lato-regular text-slate-700 dark:text-slate-200">Teste ativo</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- Seção: Questões --}}
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xs lato-black text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            Questões ({{ count($questoes) }})
                        </h3>
                        <div class="flex items-center gap-2">
                            <button wire:click="addQuestao('objetiva')" type="button"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-300 text-xs lato-bold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                                <x-lucide-circle-dot class="w-3.5 h-3.5" /> Objetiva
                            </button>
                            <button wire:click="addQuestao('discursiva')" type="button"
                                    class="flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-violet-600 dark:text-violet-300 text-xs lato-bold hover:bg-violet-100 dark:hover:bg-violet-900/50 transition">
                                <x-lucide-pencil-line class="w-3.5 h-3.5" /> Discursiva
                            </button>
                        </div>
                    </div>

                    @if (count($questoes) === 0)
                    <div class="flex flex-col items-center justify-center py-10 text-center border-2 border-dashed border-slate-200 dark:border-slate-700 rounded-2xl">
                        <x-lucide-help-circle class="w-8 h-8 text-slate-300 dark:text-slate-600 mb-2" />
                        <p class="text-sm text-slate-400 lato-regular">Nenhuma questão adicionada ainda.</p>
                        <p class="text-xs text-slate-400 lato-regular mt-0.5">Use os botões acima para adicionar questões objetivas ou discursivas.</p>
                    </div>
                    @else
                    <div class="space-y-4">
                        @foreach ($questoes as $qi => $q)
                        <div class="p-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 space-y-3">
                            {{-- Cabeçalho da questão --}}
                            <div class="flex items-start gap-3">
                                <span class="shrink-0 w-6 h-6 rounded-lg {{ $q['tipo'] === 'objetiva' ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300' : 'bg-violet-100 dark:bg-violet-900/40 text-violet-600 dark:text-violet-300' }} text-xs lato-black flex items-center justify-center mt-0.5">
                                    {{ $qi + 1 }}
                                </span>
                                <div class="flex-1 min-w-0 space-y-2">
                                    <textarea wire:model="questoes.{{ $qi }}.enunciado" rows="2"
                                              placeholder="Enunciado da questão..."
                                              class="w-full px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular resize-none"></textarea>
                                    <div class="flex items-center gap-3">
                                        <select wire:model="questoes.{{ $qi }}.tipo"
                                                class="px-3 py-1.5 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular">
                                            <option value="objetiva">Objetiva</option>
                                            <option value="discursiva">Discursiva</option>
                                        </select>
                                        <div class="flex items-center gap-1.5">
                                            <label class="text-xs text-slate-400 lato-regular">Peso:</label>
                                            <input type="number" wire:model="questoes.{{ $qi }}.peso" min="1" max="10"
                                                   class="w-16 px-2 py-1.5 text-xs border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none lato-regular" />
                                        </div>
                                    </div>
                                </div>
                                <button wire:click="removeQuestao({{ $qi }})" type="button"
                                        class="shrink-0 p-1.5 rounded-lg text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </button>
                            </div>

                            {{-- Opções (objetiva) --}}
                            @if (($q['tipo'] ?? 'objetiva') === 'objetiva')
                            <div class="ml-9 space-y-2">
                                @foreach (($q['opcoes'] ?? []) as $oi => $op)
                                <div class="flex items-center gap-2">
                                    <button wire:click="setCorreta({{ $qi }}, {{ $oi }})" type="button"
                                            class="shrink-0 w-5 h-5 rounded-full border-2 flex items-center justify-center transition
                                                   {{ ($op['correta'] ?? false) ? 'border-green-500 bg-green-500' : 'border-slate-300 dark:border-slate-600 hover:border-green-400' }}">
                                        @if ($op['correta'] ?? false)
                                        <x-lucide-check class="w-3 h-3 text-white" />
                                        @endif
                                    </button>
                                    <input type="text" wire:model="questoes.{{ $qi }}.opcoes.{{ $oi }}.texto"
                                           placeholder="Opção {{ chr(65 + $oi) }}..."
                                           class="flex-1 px-3 py-1.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                                    <button wire:click="removeOpcao({{ $qi }}, {{ $oi }})" type="button"
                                            class="shrink-0 p-1.5 rounded-lg text-slate-400 hover:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                                        <x-lucide-x class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                                @endforeach
                                <button wire:click="addOpcao({{ $qi }})" type="button"
                                        class="flex items-center gap-1.5 text-xs text-blue-500 hover:text-blue-600 lato-bold transition">
                                    <x-lucide-plus class="w-3.5 h-3.5" /> Adicionar opção
                                </button>
                            </div>
                            @else
                            <div class="ml-9">
                                <p class="text-xs text-slate-400 lato-regular italic">Questão discursiva — o candidato digitará a resposta livremente.</p>
                            </div>
                            @endif
                        </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex justify-end gap-3 px-6 py-4 border-t border-slate-200 dark:border-slate-700 shrink-0">
                <button wire:click="$set('testeModal', false)" type="button"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="saveTeste" type="button"
                        wire:loading.attr="disabled" wire:target="saveTeste"
                        class="inline-flex items-center gap-2 px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveTeste"><x-lucide-save class="w-4 h-4 inline" /> Salvar teste</span>
                    <span wire:loading wire:target="saveTeste" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path>
                        </svg>
                        Salvando...
                    </span>
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Enviar teste a candidato                                    --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($enviarModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         wire:click.self="$set('enviarModal', false)">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-lg p-6">
            <div class="flex items-center justify-between mb-5">
                <h2 class="text-base lato-black text-slate-800 dark:text-white">Enviar Teste a Candidato</h2>
                <button wire:click="$set('enviarModal', false)" type="button"
                        class="p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    <x-lucide-x class="w-5 h-5" />
                </button>
            </div>

            {{-- Busca de candidato --}}
            <div class="relative mb-4">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                <input type="text" wire:model.live.debounce.300ms="enviarSearch" placeholder="Buscar candidato por nome ou email..."
                       class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
            </div>

            {{-- Lista --}}
            <div class="space-y-2 max-h-72 overflow-y-auto">
                @forelse ($this->curriculosBusca as $cvBusca)
                @php
                    $initsB  = collect(explode(' ', $cvBusca->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                    $colorsB = ['bg-indigo-500','bg-violet-500','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500'];
                    $bgB     = $colorsB[crc32($cvBusca->nome) % count($colorsB)];
                @endphp
                <div class="flex items-center gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-900 transition">
                    <div class="w-9 h-9 rounded-lg {{ $bgB }} flex items-center justify-center text-white lato-black text-xs shrink-0">{{ $initsB }}</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm lato-bold text-slate-800 dark:text-white truncate">{{ $cvBusca->nome }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">{{ $cvBusca->email ?? $cvBusca->area_interesse ?? '' }}</p>
                    </div>
                    <button wire:click="enviarTesteCandidato({{ $cvBusca->id }})" type="button"
                            wire:loading.attr="disabled" wire:target="enviarTesteCandidato({{ $cvBusca->id }})"
                            class="shrink-0 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-xs lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm">
                        <span wire:loading.remove wire:target="enviarTesteCandidato({{ $cvBusca->id }})">
                            <x-lucide-send class="w-3 h-3 inline" /> Enviar
                        </span>
                        <span wire:loading wire:target="enviarTesteCandidato({{ $cvBusca->id }})">Enviando...</span>
                    </button>
                </div>
                @empty
                <div class="text-center py-10">
                    <x-lucide-user-x class="w-8 h-8 text-slate-300 dark:text-slate-600 mx-auto mb-2" />
                    <p class="text-sm text-slate-400 lato-regular">Nenhum candidato encontrado.</p>
                </div>
                @endforelse
            </div>

            <div class="flex justify-end mt-5">
                <button wire:click="$set('enviarModal', false)" type="button"
                        class="px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Fechar
                </button>
            </div>
        </div>
    </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: Confirmar exclusão                                          --}}
    {{-- ══════════════════════════════════════════════════════════════════ --}}
    @if ($deleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
         wire:click.self="$set('deleteModal', false)">
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-sm p-6">
            <div class="flex flex-col items-center text-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-red-100 dark:bg-red-900/30 flex items-center justify-center">
                    <x-lucide-trash-2 class="w-7 h-7 text-red-500" />
                </div>
                <div>
                    <h3 class="text-base lato-black text-slate-800 dark:text-white">Excluir teste?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-1">
                        Esta ação removerá permanentemente o teste e todos os resultados associados.
                    </p>
                </div>
            </div>
            <div class="flex gap-3 mt-6">
                <button wire:click="$set('deleteModal', false)" type="button"
                        class="flex-1 px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm lato-bold hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button wire:click="deleteTeste" type="button"
                        wire:loading.attr="disabled" wire:target="deleteTeste"
                        class="flex-1 px-5 py-2.5 rounded-xl bg-red-500 text-white text-sm lato-bold hover:bg-red-600 transition disabled:opacity-60">
                    <span wire:loading.remove wire:target="deleteTeste">Confirmar exclusão</span>
                    <span wire:loading wire:target="deleteTeste">Excluindo...</span>
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
