@if ($demDrawer && $this->demDrawerData)
@php $dd = $this->demDrawerData; @endphp
<div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('demDrawer', false)"></div>
<div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[520px] bg-white dark:bg-slate-800 shadow-2xl flex flex-col"
     x-data="{ confirmConcluir: false }">

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
        @foreach (['processo' => 'Processo', 'checklist' => 'Checklist', 'equipamentos' => 'Equipamentos', 'verbas' => 'Verbas', 'impacto' => 'Impacto'] as $dtk => $dtl)
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
        <button @click="confirmConcluir = true" type="button"
                class="cursor-pointer w-full py-2.5 rounded-xl bg-gradient-to-r from-emerald-500 to-teal-500 text-white text-sm lato-bold hover:from-emerald-600 hover:to-teal-600 transition shadow-sm flex items-center justify-center gap-2">
            <x-lucide-check-circle class="w-4 h-4" />
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

        @elseif ($demDrawerTab === 'equipamentos')

        {{-- EQUIPAMENTOS TAB --}}
        @php $eqs = $this->demEquipamentos; @endphp
        <div class="flex items-center justify-between mb-3">
            <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider">Equipamentos em posse</p>
            <span class="text-xs px-2 py-0.5 rounded-full
                {{ $eqs->count() > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400' : 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400' }}">
                {{ $eqs->count() }} {{ $eqs->count() === 1 ? 'item' : 'itens' }}
            </span>
        </div>

        @if ($eqs->isEmpty())
            <div class="flex flex-col items-center justify-center py-10 text-center">
                <div class="w-12 h-12 rounded-2xl bg-green-50 dark:bg-green-900/20 flex items-center justify-center mb-3">
                    <x-lucide-package-check class="w-6 h-6 text-green-500" />
                </div>
                <p class="text-sm lato-bold text-slate-600 dark:text-slate-300">Nenhum equipamento em posse</p>
                <p class="text-xs text-slate-400 mt-1">Todos os ativos já foram devolvidos.</p>
            </div>
        @else
            @php
                $catIcons = [
                    'notebook'  => 'laptop-2',
                    'desktop'   => 'monitor',
                    'monitor'   => 'monitor-dot',
                    'teclado'   => 'keyboard',
                    'mouse'     => 'mouse-pointer-2',
                    'headset'   => 'headphones',
                    'cracha'    => 'id-card',
                    'epi'       => 'hard-hat',
                    'celular'   => 'smartphone',
                    'cadeira'   => 'sofa',
                    'outros'    => 'package',
                ];
                $condOpts = ['novo' => 'Novo', 'bom' => 'Bom', 'regular' => 'Regular', 'danificado' => 'Danificado'];
            @endphp
            <div class="space-y-3">
                @foreach ($eqs as $atrib)
                @php $eq = $atrib->equipamento; @endphp
                <div class="bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-xl p-3"
                     x-data="{ open: false, condicao: 'bom' }">
                    <div class="flex items-center gap-3">
                        {{-- Ícone de categoria --}}
                        <div class="w-9 h-9 rounded-xl bg-violet-50 dark:bg-violet-900/20 flex items-center justify-center shrink-0">
                            <x-dynamic-component :component="'lucide-'.($catIcons[$eq?->categoria] ?? 'package')"
                                class="w-4 h-4 text-indigo-500" />
                        </div>
                        {{-- Info --}}
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $eq?->nome ?? '—' }}</p>
                            <p class="text-xs text-slate-400 lato-regular">
                                {{ ucfirst($eq?->categoria ?? '') }}
                                @if ($eq?->numero_serie) · {{ $eq->numero_serie }} @endif
                            </p>
                            <p class="text-xs text-slate-400 lato-regular mt-0.5">
                                Entregue em {{ $atrib->data_entrega?->format('d/m/Y') ?? '—' }}
                            </p>
                        </div>
                        {{-- Botão devolver --}}
                        <button @click="open = !open" type="button"
                                class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs lato-bold
                                       bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-400
                                       border border-amber-200 dark:border-amber-700/40
                                       hover:bg-amber-100 dark:hover:bg-amber-900/40 transition shrink-0">
                            <x-lucide-rotate-ccw class="w-3 h-3" />
                            Devolver
                        </button>
                    </div>

                    {{-- Painel inline de devolução --}}
                    <div x-show="open" x-transition style="display:none"
                         class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700">
                        <p class="text-xs lato-bold text-slate-600 dark:text-slate-300 mb-2">Condição na devolução</p>
                        <div class="grid grid-cols-4 gap-1.5 mb-3">
                            @foreach ($condOpts as $cv => $cl)
                            @php
                                $condCls = match($cv) {
                                    'novo'       => 'peer-checked:border-green-400 peer-checked:bg-green-50 dark:peer-checked:bg-green-900/20 peer-checked:text-green-700 dark:peer-checked:text-green-300',
                                    'bom'        => 'peer-checked:border-blue-400 peer-checked:bg-blue-50 dark:peer-checked:bg-blue-900/20 peer-checked:text-blue-700 dark:peer-checked:text-blue-300',
                                    'regular'    => 'peer-checked:border-amber-400 peer-checked:bg-amber-50 dark:peer-checked:bg-amber-900/20 peer-checked:text-amber-700 dark:peer-checked:text-amber-300',
                                    'danificado' => 'peer-checked:border-red-400 peer-checked:bg-red-50 dark:peer-checked:bg-red-900/20 peer-checked:text-red-700 dark:peer-checked:text-red-300',
                                };
                            @endphp
                            <label class="cursor-pointer">
                                <input type="radio" name="cond_{{ $atrib->id }}" value="{{ $cv }}"
                                       x-model="condicao"
                                       class="sr-only peer" />
                                <div class="text-center py-1.5 rounded-lg border text-xs lato-bold transition
                                    {{ $condCls }}
                                    border-slate-200 dark:border-slate-600 text-slate-500 hover:border-slate-300">
                                    {{ $cl }}
                                </div>
                            </label>
                            @endforeach
                        </div>
                        <div class="flex gap-2">
                            <button @click="open = false" type="button"
                                    class="cursor-pointer flex-1 py-1.5 rounded-lg text-xs lato-bold text-slate-500 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                                Cancelar
                            </button>
                            <button @click="$wire.devolverEquipamento({{ $atrib->id }}, condicao); open = false;" type="button"
                                    class="cursor-pointer flex-1 py-1.5 rounded-lg text-xs lato-bold text-white bg-gradient-to-r from-amber-500 to-orange-500 hover:opacity-90 transition flex items-center justify-center gap-1">
                                <x-lucide-check class="w-3 h-3" />
                                Confirmar devolução
                            </button>
                        </div>
                    </div>
                </div>
                @endforeach

                <div class="mt-2 p-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40">
                    <p class="text-xs text-amber-700 dark:text-amber-400 lato-regular flex items-start gap-2">
                        <x-lucide-alert-triangle class="w-3.5 h-3.5 shrink-0 mt-0.5" />
                        Ao confirmar a devolução, o equipamento volta para o status <strong>Disponível</strong> no controle de ativos.
                    </p>
                </div>
            </div>
        @endif

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

    {{-- ── Dialog: Confirmar Conclusão do Desligamento ── --}}
    <div x-show="confirmConcluir"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[70] flex items-center justify-center p-5 bg-black/50 backdrop-blur-sm"
         style="display:none"
         @keydown.escape.window="confirmConcluir = false">

        <div x-show="confirmConcluir"
             x-transition:enter="transition ease-out duration-200"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm overflow-hidden"
             @click.stop>

            {{-- Header colorido --}}
            <div class="bg-gradient-to-br from-emerald-500 to-teal-600 p-6 text-center">
                <div class="w-14 h-14 rounded-2xl bg-white/20 flex items-center justify-center mx-auto mb-3">
                    <x-lucide-check-circle class="w-7 h-7 text-white" />
                </div>
                <h3 class="text-base lato-black text-white">Concluir Desligamento?</h3>
                <p class="text-xs text-emerald-100 mt-1 lato-regular">Esta ação não pode ser desfeita</p>
            </div>

            {{-- Body --}}
            <div class="p-5 space-y-3">
                <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular text-center leading-relaxed">
                    Ao confirmar, o processo de desligamento de
                    <strong class="text-slate-800 dark:text-white lato-bold">{{ $dd->funcionario?->name }}</strong>
                    será encerrado e todos os registros serão finalizados.
                </p>

                {{-- Checklist de pendências --}}
                @php
                    $cl       = $dd->checklist ?? collect();
                    $pendCl   = $cl->where('status', 'pendente')->count();
                    $pendEq   = \App\Models\EquipamentoAtribuicao::where('user_id', $dd->user_id)->where('tipo','entrega')->whereNull('data_devolucao')->count();
                    $temVerbas = (bool) $dd->verbas;
                @endphp
                <div class="space-y-1.5 bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3">
                    <div class="flex items-center gap-2 text-xs">
                        <x-lucide-{{ $pendCl === 0 ? 'check-circle' : 'alert-circle' }}
                            class="w-3.5 h-3.5 shrink-0 {{ $pendCl === 0 ? 'text-emerald-500' : 'text-amber-500' }}" />
                        <span class="{{ $pendCl === 0 ? 'text-slate-500 dark:text-slate-400' : 'text-amber-700 dark:text-amber-400 lato-bold' }}">
                            Checklist: {{ $cl->where('status','concluido')->count() }}/{{ $cl->count() }} itens
                            {{ $pendCl > 0 ? "($pendCl pendentes)" : 'concluídos' }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <x-lucide-{{ $pendEq === 0 ? 'check-circle' : 'alert-circle' }}
                            class="w-3.5 h-3.5 shrink-0 {{ $pendEq === 0 ? 'text-emerald-500' : 'text-amber-500' }}" />
                        <span class="{{ $pendEq === 0 ? 'text-slate-500 dark:text-slate-400' : 'text-amber-700 dark:text-amber-400 lato-bold' }}">
                            Equipamentos: {{ $pendEq === 0 ? 'todos devolvidos' : "$pendEq ainda em posse" }}
                        </span>
                    </div>
                    <div class="flex items-center gap-2 text-xs">
                        <x-lucide-{{ $temVerbas ? 'check-circle' : 'alert-circle' }}
                            class="w-3.5 h-3.5 shrink-0 {{ $temVerbas ? 'text-emerald-500' : 'text-amber-500' }}" />
                        <span class="{{ $temVerbas ? 'text-slate-500 dark:text-slate-400' : 'text-amber-700 dark:text-amber-400 lato-bold' }}">
                            Verbas: {{ $temVerbas ? 'calculadas' : 'não calculadas' }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Footer --}}
            <div class="flex gap-3 px-5 pb-5">
                <button @click="confirmConcluir = false" type="button"
                        class="cursor-pointer flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                               text-sm lato-bold text-slate-600 dark:text-slate-300
                               hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                    Cancelar
                </button>
                <button @click="confirmConcluir = false" wire:click="concluirDesligamento" type="button"
                        class="cursor-pointer flex-1 py-2.5 rounded-xl
                               bg-gradient-to-r from-emerald-500 to-teal-500
                               hover:from-emerald-600 hover:to-teal-600
                               text-sm lato-bold text-white transition shadow-sm flex items-center justify-center gap-2">
                    <x-lucide-check class="w-4 h-4" />
                    Confirmar
                </button>
            </div>
        </div>
    </div>

</div>{{-- fim drawer panel --}}
@endif{{-- fim demDrawer --}}
