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
                                <x-lucide-users class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
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
                        <button wire:click="openEnviar({{ $teste->id }}, {{ $teste->vaga_id ?? 'null' }})" type="button"
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
                                    <span class="shrink-0 w-6 h-6 rounded-lg {{ $isObj ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600 dark:text-blue-300' : 'bg-violet-100 dark:bg-violet-900/40 text-indigo-600 dark:text-violet-300' }} text-xs lato-black flex items-center justify-center">{{ $qi+1 }}</span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-800 dark:text-white leading-relaxed">{{ $q->enunciado }}</p>
                                    </div>
                                    <div class="flex items-center gap-2 shrink-0">
                                        @if ($q->peso > 1)
                                        <span class="text-[10px] lato-bold px-1.5 py-0.5 rounded-md bg-slate-100 dark:bg-slate-700 text-slate-500">peso {{ $q->peso }}</span>
                                        @endif
                                        <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full {{ $isObj ? 'bg-blue-50 text-blue-600 dark:bg-blue-900/30 dark:text-blue-400' : 'bg-violet-50 text-indigo-600 dark:bg-violet-900/30 dark:text-indigo-400' }}">
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
                                        <x-lucide-pencil-line class="w-3.5 h-3.5 text-indigo-400 shrink-0" />
                                        <p class="text-xs text-indigo-600 dark:text-indigo-400 lato-regular">Resposta livre — o candidato digitará a resposta.</p>
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
                            <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
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

                                {{-- Período de disponibilidade --}}
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                                            Data de início
                                            <span class="font-normal text-slate-400 lato-regular ml-1">(opcional)</span>
                                        </label>
                                        <div class="relative">
                                            <x-lucide-calendar-check class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                            <input type="datetime-local" wire:model="tDataInicio"
                                                   class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 transition" />
                                        </div>
                                        @error('tDataInicio') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                    <div>
                                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                                            Data de encerramento
                                            <span class="font-normal text-slate-400 lato-regular ml-1">(opcional)</span>
                                        </label>
                                        <div class="relative">
                                            <x-lucide-calendar-x class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                            <input type="datetime-local" wire:model="tDataFim"
                                                   class="w-full pl-9 pr-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 transition" />
                                        </div>
                                        @error('tDataFim') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                                    </div>
                                </div>
                                @if ($tDataInicio || $tDataFim)
                                <p class="text-[11px] text-amber-600 dark:text-amber-400 lato-regular flex items-center gap-1 -mt-1">
                                    <x-lucide-clock class="w-3 h-3 shrink-0" />
                                    O teste só poderá ser respondido dentro do período definido.
                                </p>
                                @endif

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
                        <div class="border-t border-slate-100 dark:border-slate-700">
                            {{-- Barra sticky: fica visível durante o scroll das questões --}}
                            <div class="sticky top-0 z-10 -mx-6 px-6 py-3 bg-white dark:bg-slate-800 border-b border-slate-100 dark:border-slate-700 flex items-center justify-between mb-4 shadow-sm">
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
                                            class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-violet-50 dark:bg-violet-900/30 text-indigo-600 dark:text-violet-300 text-xs lato-bold hover:bg-violet-100 dark:hover:bg-violet-900/50 transition">
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
                                        <span class="shrink-0 w-6 h-6 rounded-lg {{ ($q['tipo'] ?? 'objetiva') === 'objetiva' ? 'bg-blue-100 dark:bg-blue-900/40 text-blue-600' : 'bg-violet-100 dark:bg-violet-900/40 text-indigo-600' }} text-xs lato-black flex items-center justify-center">{{ $qi+1 }}</span>
                                        <span class="flex-1 text-xs lato-bold {{ ($q['tipo'] ?? 'objetiva') === 'objetiva' ? 'text-blue-600 dark:text-blue-400' : 'text-indigo-600 dark:text-indigo-400' }}">
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
                                            <x-lucide-pencil-line class="w-4 h-4 text-indigo-400 shrink-0" />
                                            <p class="text-xs text-indigo-600 dark:text-indigo-400 lato-regular">Questão discursiva — o candidato digitará a resposta livremente.</p>
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
                    @if ($enviarVagaId)
                    <div class="flex items-center gap-2 mb-3 px-3 py-2 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/40">
                        <x-lucide-filter class="w-3.5 h-3.5 text-blue-500 shrink-0" />
                        <p class="text-xs lato-bold text-blue-700 dark:text-blue-300">Mostrando candidatos da vaga na etapa <span class="lato-black">Teste</span></p>
                    </div>
                    @endif
                    <div class="relative mb-4">
                        <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                        <input type="text" wire:model.live.debounce.300ms="enviarSearch"
                               placeholder="{{ $enviarVagaId ? 'Filtrar por nome ou e-mail...' : 'Buscar candidato...' }}"
                               class="w-full pl-9 pr-4 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                    </div>
                    <div class="space-y-2 max-h-72 overflow-y-auto">
                        @forelse ($this->curriculosBuscaEnvio as $cvE)
                        @php
                            $cvEIn = collect(explode(' ', $cvE->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
                            $cvEBg = ['bg-indigo-500','bg-indigo-600','bg-blue-500','bg-teal-500','bg-emerald-500'][abs(crc32($cvE->nome)) % 5];
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
                            <p class="text-sm text-slate-400 lato-regular">
                                {{ $enviarVagaId
                                    ? 'Nenhum candidato desta vaga está na etapa Teste.'
                                    : (strlen($enviarSearch) < 2 ? 'Digite ao menos 2 caracteres para buscar.' : 'Nenhum candidato encontrado.') }}
                            </p>
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

