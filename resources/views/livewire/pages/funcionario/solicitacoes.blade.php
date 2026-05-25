@php
    use App\Models\RhSolicitacao;
    use Illuminate\Support\Facades\Storage;

    $tipoLabels = RhSolicitacao::$tipoLabels;

    $statusCfg = [
        'pendente'     => ['cls' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',  'dot' => 'bg-amber-400',   'label' => 'Pendente'],
        'em_andamento' => ['cls' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-300',     'dot' => 'bg-blue-400',    'label' => 'Em Andamento'],
        'concluida'    => ['cls' => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', 'dot' => 'bg-emerald-400', 'label' => 'Concluída'],
        'cancelada'    => ['cls' => 'bg-slate-100 text-slate-500 dark:bg-slate-700 dark:text-slate-400',    'dot' => 'bg-slate-400',   'label' => 'Cancelada'],
    ];

    $tipoIcons = [
        'declaracao_emprego'    => 'file-badge',
        'declaracao_salario'    => 'banknote',
        'declaracao_ferias'     => 'umbrella',
        'declaracao_quitacao'   => 'file-check-2',
        'comprovante_pagamento' => 'receipt',
        'comprovante_fgts'      => 'landmark',
        'informe_rendimentos'   => 'trending-up',
        'segunda_via_cracha'    => 'badge-check',
        'outros'                => 'help-circle',
    ];

    $tipoDesc = [
        'declaracao_emprego'    => 'Para fins de comprovação de vínculo',
        'declaracao_salario'    => 'Com informações de remuneração',
        'declaracao_ferias'     => 'Período de gozo ou programação',
        'declaracao_quitacao'   => 'Encerramento de vínculo',
        'comprovante_pagamento' => 'Holerite / recibo de salário',
        'comprovante_fgts'      => 'Extrato ou saldo do FGTS',
        'informe_rendimentos'   => 'Para declaração de IR',
        'segunda_via_cracha'    => 'Substituição de crachá perdido ou danificado',
        'outros'                => 'Outro documento não listado',
    ];

    $stats = $this->stats;
    $sls   = $this->solicitacoes;
@endphp

{{-- ╔══════════════════════════════════════════════════════════════╗
     ÚNICO ROOT ELEMENT — Livewire v3 exige apenas um elemento raiz
     ╚══════════════════════════════════════════════════════════════╝ --}}
<div x-data="{ novaModal: false }">

{{-- ── Conteúdo principal ───────────────────────────────────────── --}}
<div class="p-4 sm:p-6 lg:p-8">

    {{-- Cabeçalho --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl lato-black text-slate-800 dark:text-white tracking-tight">Solicitações ao RH</h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1 lato-regular">
                Solicite documentos e acompanhe o andamento em tempo real
            </p>
        </div>
        <button @click="novaModal = true" type="button"
                class="cursor-pointer inline-flex items-center gap-2 px-5 py-2.5 rounded-xl
                       bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600
                       text-white text-sm lato-bold shadow-sm shadow-amber-200 dark:shadow-amber-900/30 transition">
            <x-lucide-plus class="w-4 h-4" />
            Nova Solicitação
        </button>
    </div>

    {{-- Cards de estatísticas --}}
    <div class="mt-6 grid grid-cols-3 gap-3 sm:gap-4">
        <div class="bg-[#F1F5F9] dark:bg-slate-800 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">Total</p>
                <p class="text-3xl lato-black text-slate-800 dark:text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <x-lucide-inbox class="w-9 h-9 text-slate-400 shrink-0" />
        </div>
        <div class="bg-[#FEF3C7] dark:bg-amber-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-amber-700 dark:text-amber-300 lato-regular">Em aberto</p>
                <p class="text-3xl lato-black text-slate-800 dark:text-white mt-1">{{ $stats['abertas'] }}</p>
            </div>
            <x-lucide-clock class="w-9 h-9 text-amber-400 shrink-0" />
        </div>
        <div class="bg-[#D1FAE5] dark:bg-emerald-900/20 rounded-2xl p-4 sm:p-5 flex items-center justify-between">
            <div>
                <p class="text-xs text-emerald-700 dark:text-emerald-300 lato-regular">Concluídas</p>
                <p class="text-3xl lato-black text-slate-800 dark:text-white mt-1">{{ $stats['concluidas'] }}</p>
            </div>
            <x-lucide-check-circle class="w-9 h-9 text-emerald-500 shrink-0" />
        </div>
    </div>

    {{-- Filtro de status --}}
    <div class="mt-6 flex items-center gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl p-1 w-fit">
        @foreach (array_merge(['' => 'Todas'], array_map(fn($c) => $c['label'], $statusCfg)) as $k => $v)
        <button wire:click="$set('filtroStatus', '{{ $k }}')" type="button"
                class="cursor-pointer px-4 py-1.5 text-xs lato-bold rounded-lg transition
                       {{ $filtroStatus === $k
                           ? 'bg-slate-900 dark:bg-white text-white dark:text-slate-900'
                           : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            {{ $v }}
        </button>
        @endforeach
    </div>

    {{-- Lista de solicitações — wire:poll atualiza status sem precisar de WebSocket --}}
    <div class="mt-5" wire:poll.25s>
        @if ($sls->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-slate-800
                    border border-slate-200 dark:border-slate-700 rounded-2xl">
            <div class="w-16 h-16 rounded-2xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center mb-4">
                <x-lucide-inbox class="w-8 h-8 text-amber-400" />
            </div>
            <p class="text-base lato-bold text-slate-700 dark:text-slate-200">Nenhuma solicitação ainda</p>
            <p class="text-sm text-slate-400 mt-1 mb-5">Clique em "Nova Solicitação" para começar</p>
            <button @click="novaModal = true" type="button"
                    class="cursor-pointer px-6 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-sm lato-bold transition shadow-sm">
                Fazer primeira solicitação
            </button>
        </div>
        @else
        <div class="space-y-3">
            @foreach ($sls as $sol)
            @php $sc = $statusCfg[$sol->status] ?? $statusCfg['cancelada']; $vencida = $sol->vencida; @endphp
            <button wire:click="openDrawer({{ $sol->id }})" type="button"
                    class="cursor-pointer w-full text-left bg-white dark:bg-slate-800
                           border {{ $vencida ? 'border-rose-200 dark:border-rose-700/40' : 'border-slate-200 dark:border-slate-700' }}
                           rounded-2xl p-4 hover:shadow-md hover:border-amber-300 dark:hover:border-amber-600/50 transition group">
                <div class="flex items-center gap-4">
                    <div class="w-11 h-11 rounded-xl bg-amber-50 dark:bg-amber-900/20
                                flex items-center justify-center shrink-0
                                group-hover:bg-amber-100 dark:group-hover:bg-amber-900/30 transition">
                        <x-dynamic-component :component="'lucide-'.($tipoIcons[$sol->tipo] ?? 'file-text')"
                            class="w-5 h-5 text-amber-500" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex items-start justify-between gap-2 flex-wrap">
                            <p class="text-sm lato-bold text-slate-800 dark:text-white">
                                {{ $tipoLabels[$sol->tipo] ?? $sol->tipo }}
                            </p>
                            <span class="inline-flex items-center gap-1.5 text-xs lato-bold px-2.5 py-1 rounded-full shrink-0 {{ $sc['cls'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $sc['dot'] }}"></span>
                                {{ $sc['label'] }}
                            </span>
                        </div>
                        <div class="flex items-center flex-wrap gap-x-4 gap-y-1 mt-1.5">
                            <span class="text-xs text-slate-400 flex items-center gap-1">
                                <x-lucide-calendar class="w-3 h-3" />
                                {{ $sol->created_at->format('d/m/Y') }} · {{ $sol->created_at->diffForHumans() }}
                            </span>
                            @if ($sol->prazo)
                            <span class="text-xs flex items-center gap-1 {{ $vencida ? 'text-rose-500 lato-bold' : 'text-slate-400' }}">
                                <x-lucide-alarm-clock class="w-3 h-3" />
                                Prazo: {{ $sol->prazo->format('d/m/Y') }}@if ($vencida) · vencido @endif
                            </span>
                            @endif
                            @if ($sol->rhUser)
                            <span class="text-xs text-slate-400 flex items-center gap-1">
                                <x-lucide-user class="w-3 h-3" />{{ $sol->rhUser->name }}
                            </span>
                            @endif
                        </div>
                        @if ($sol->observacao_rh)
                        <div class="mt-2 px-3 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-100 dark:border-blue-800/40 rounded-lg">
                            <p class="text-xs text-blue-700 dark:text-blue-300 truncate">
                                <span class="lato-bold">RH:</span> {{ $sol->observacao_rh }}
                            </p>
                        </div>
                        @endif
                    </div>
                    <x-lucide-chevron-right class="w-4 h-4 text-slate-300 dark:text-slate-600 shrink-0
                                                    group-hover:text-amber-500 group-hover:translate-x-0.5 transition" />
                </div>
            </button>
            @endforeach
        </div>
        @endif
    </div>

</div>{{-- /p-4 sm:p-6 lg:p-8 --}}


{{-- ════════════════════════════════════════════════════════════════
     MODAL: NOVA SOLICITAÇÃO
════════════════════════════════════════════════════════════════ --}}
<div x-show="novaModal" x-cloak style="display:none"
     class="fixed inset-0 z-[9999] flex items-end sm:items-center justify-center p-0 sm:p-4 bg-black/50 backdrop-blur-sm"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @click.self="novaModal = false">

    <div class="bg-white dark:bg-slate-800 w-full sm:max-w-lg rounded-t-3xl sm:rounded-2xl shadow-2xl flex flex-col max-h-[92vh]"
         @click.stop
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="translate-y-4 opacity-0 sm:scale-95"
         x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100">

        <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
            <div class="w-10 h-1 rounded-full bg-slate-200 dark:bg-slate-600"></div>
        </div>

        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center">
                    <x-lucide-plus class="w-4 h-4 text-white" />
                </div>
                <div>
                    <h2 class="text-sm lato-bold text-slate-800 dark:text-white">Nova Solicitação</h2>
                    <p class="text-xs text-slate-400">Escolha o documento e envie para o RH</p>
                </div>
            </div>
            <button @click="novaModal = false" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        <div class="overflow-y-auto flex-1 p-5 space-y-5">
            <div>
                <p class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-3">
                    Tipo de documento <span class="text-rose-400">*</span>
                </p>
                <div class="grid grid-cols-1 gap-2">
                    @foreach ($tipoLabels as $k => $v)
                    <button wire:click="$set('tipo', '{{ $k }}')" type="button"
                            class="cursor-pointer flex items-center gap-3 p-3 rounded-xl border-2 text-left transition
                                   {{ $tipo === $k
                                       ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/20'
                                       : 'border-slate-100 dark:border-slate-700 hover:border-amber-200 dark:hover:border-amber-700/50 hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                        <div class="w-9 h-9 rounded-xl shrink-0 flex items-center justify-center
                                    {{ $tipo === $k ? 'bg-amber-100 dark:bg-amber-900/40' : 'bg-slate-100 dark:bg-slate-700' }}">
                            <x-dynamic-component :component="'lucide-'.($tipoIcons[$k] ?? 'file-text')"
                                class="w-4 h-4 {{ $tipo === $k ? 'text-amber-600' : 'text-slate-400' }}" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-bold {{ $tipo === $k ? 'text-amber-700 dark:text-amber-300' : 'text-slate-700 dark:text-slate-200' }}">
                                {{ $v }}
                            </p>
                            <p class="text-xs text-slate-400 leading-tight mt-0.5">{{ $tipoDesc[$k] ?? '' }}</p>
                        </div>
                        <div class="w-5 h-5 rounded-full shrink-0 border-2 flex items-center justify-center transition
                                    {{ $tipo === $k ? 'border-amber-500 bg-amber-500' : 'border-slate-300 dark:border-slate-600' }}">
                            @if ($tipo === $k)<x-lucide-check class="w-3 h-3 text-white" />@endif
                        </div>
                    </button>
                    @endforeach
                </div>
                @error('tipo') <p class="text-xs text-rose-500 mt-2">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                    Detalhes adicionais <span class="text-slate-300 lato-regular normal-case">(opcional)</span>
                </label>
                <textarea wire:model="descricao" rows="3"
                          placeholder="Informe o período, finalidade ou qualquer detalhe que ajude o RH…"
                          class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-600 rounded-xl
                                 bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200
                                 focus:outline-none focus:ring-2 focus:ring-amber-400/50 focus:border-amber-400
                                 resize-none placeholder:text-slate-300 dark:placeholder:text-slate-500"></textarea>
                @error('descricao') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex items-start gap-3 p-3.5 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/40">
                <x-lucide-info class="w-4 h-4 text-blue-500 shrink-0 mt-0.5" />
                <p class="text-xs text-blue-600 dark:text-blue-400 leading-relaxed">
                    Após o envio, o RH definirá um prazo de atendimento. Você pode acompanhar o status nesta tela.
                </p>
            </div>
        </div>

        <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0">
            <button @click="novaModal = false" type="button"
                    class="cursor-pointer px-5 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600
                           text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                Cancelar
            </button>
            <button wire:click="novaSolicitacao" type="button"
                    x-on:click="$wire.novaSolicitacao().then(r => { if($wire.aba !== 'nova') novaModal = false })"
                    wire:loading.attr="disabled" wire:target="novaSolicitacao"
                    class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 rounded-xl
                           bg-gradient-to-r from-amber-500 to-orange-500 hover:from-amber-600 hover:to-orange-600
                           text-white text-sm lato-bold shadow-sm transition disabled:opacity-60">
                <span wire:loading.remove wire:target="novaSolicitacao" class="flex items-center gap-2">
                    <x-lucide-send class="w-4 h-4" />Enviar Solicitação
                </span>
                <span wire:loading wire:target="novaSolicitacao" class="flex items-center gap-2">
                    <x-lucide-loader-circle class="w-4 h-4 animate-spin" /> Enviando…
                </span>
            </button>
        </div>
    </div>
</div>{{-- /modal nova solicitação --}}


{{-- ════════════════════════════════════════════════════════════════
     DRAWER: DETALHE DA SOLICITAÇÃO
════════════════════════════════════════════════════════════════ --}}
@if ($drawer && $this->detalhe)
@php $d = $this->detalhe; $sc = $statusCfg[$d->status] ?? $statusCfg['cancelada']; @endphp

<div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('drawer', false)"></div>

<div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[460px] bg-white dark:bg-slate-800 shadow-2xl flex flex-col">

    <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-900/20 flex items-center justify-center shrink-0">
            <x-dynamic-component :component="'lucide-'.($tipoIcons[$d->tipo] ?? 'file-text')" class="w-5 h-5 text-amber-500" />
        </div>
        <div class="flex-1 min-w-0">
            <p class="text-sm lato-bold text-slate-800 dark:text-white leading-tight truncate">
                {{ $tipoLabels[$d->tipo] ?? $d->tipo }}
            </p>
            <p class="text-xs text-slate-400 mt-0.5">Protocolo #{{ str_pad($d->id, 5, '0', STR_PAD_LEFT) }}</p>
        </div>
        <button wire:click="$set('drawer', false)" type="button"
                class="cursor-pointer p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
            <x-lucide-x class="w-5 h-5" />
        </button>
    </div>

    <div class="flex-1 overflow-y-auto p-5 space-y-5">

        <div class="flex items-center justify-between flex-wrap gap-2">
            <span class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full text-sm lato-bold {{ $sc['cls'] }}">
                <span class="w-2 h-2 rounded-full {{ $sc['dot'] }}"></span>
                {{ $sc['label'] }}
            </span>
            @if ($d->prazo)
            <div class="flex items-center gap-1.5 text-xs {{ $d->vencida ? 'text-rose-500 lato-bold' : 'text-slate-400' }}">
                <x-lucide-alarm-clock class="w-3.5 h-3.5 shrink-0" />
                Prazo: {{ $d->prazo->format('d/m/Y') }}@if ($d->vencida) · vencido @endif
            </div>
            @else
            <span class="text-xs text-slate-300 dark:text-slate-600">Prazo não definido</span>
            @endif
        </div>

        <div>
            <p class="text-xs lato-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-3">Histórico</p>
            <ol class="relative border-l-2 border-slate-100 dark:border-slate-700 ml-3 space-y-5">

                <li class="pl-5 relative">
                    <span class="absolute -left-[11px] top-0.5 w-5 h-5 rounded-full bg-amber-100 dark:bg-amber-900/30
                                 flex items-center justify-center ring-4 ring-white dark:ring-slate-800">
                        <x-lucide-send class="w-2.5 h-2.5 text-amber-600" />
                    </span>
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">Solicitação enviada</p>
                    <p class="text-xs text-slate-400 mt-0.5">{{ $d->created_at->format('d/m/Y \à\s H:i') }}</p>
                    @if ($d->descricao)
                    <div class="mt-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl border border-slate-100 dark:border-slate-700">
                        <p class="text-xs text-slate-500 dark:text-slate-400 italic leading-relaxed">"{{ $d->descricao }}"</p>
                    </div>
                    @endif
                </li>

                @if (in_array($d->status, ['em_andamento', 'concluida', 'cancelada']))
                <li class="pl-5 relative">
                    <span class="absolute -left-[11px] top-0.5 w-5 h-5 rounded-full
                                 {{ $d->status === 'cancelada' ? 'bg-slate-100 dark:bg-slate-700' : 'bg-blue-100 dark:bg-blue-900/30' }}
                                 flex items-center justify-center ring-4 ring-white dark:ring-slate-800">
                        <x-lucide-user class="w-2.5 h-2.5 {{ $d->status === 'cancelada' ? 'text-slate-400' : 'text-blue-600' }}" />
                    </span>
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">
                        @if ($d->status === 'cancelada') Cancelada
                        @elseif ($d->status === 'em_andamento') RH em atendimento
                        @else RH atualizou o status @endif
                    </p>
                    @if ($d->rhUser)
                    <p class="text-xs text-slate-400 mt-0.5">Responsável: <span class="lato-bold">{{ $d->rhUser->name }}</span></p>
                    @endif
                    @if ($d->prazo)
                    <p class="text-xs text-slate-400">Prazo definido: {{ $d->prazo->format('d/m/Y') }}</p>
                    @endif
                    @if ($d->observacao_rh)
                    <div class="mt-2 p-3 bg-blue-50 dark:bg-blue-900/20 rounded-xl border border-blue-100 dark:border-blue-800/40">
                        <p class="text-xs lato-bold text-blue-600 dark:text-blue-400 mb-1">Mensagem do RH</p>
                        <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $d->observacao_rh }}</p>
                    </div>
                    @endif
                </li>
                @endif

                @if ($d->status === 'concluida')
                <li class="pl-5 relative">
                    <span class="absolute -left-[11px] top-0.5 w-5 h-5 rounded-full bg-emerald-100 dark:bg-emerald-900/30
                                 flex items-center justify-center ring-4 ring-white dark:ring-slate-800">
                        <x-lucide-check class="w-2.5 h-2.5 text-emerald-600" />
                    </span>
                    <p class="text-sm lato-bold text-emerald-700 dark:text-emerald-300">Solicitação concluída!</p>
                    @if ($d->concluida_em)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $d->concluida_em->format('d/m/Y \à\s H:i') }}</p>
                    @endif
                </li>
                @endif

            </ol>
        </div>

        @if ($d->arquivo_url)
        <div class="bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/40 rounded-2xl p-4">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-9 h-9 rounded-xl bg-emerald-100 dark:bg-emerald-900/40 flex items-center justify-center">
                    <x-lucide-file-check-2 class="w-4 h-4 text-emerald-600" />
                </div>
                <div>
                    <p class="text-sm lato-bold text-emerald-700 dark:text-emerald-300">Documento disponível</p>
                    <p class="text-xs text-emerald-500">Pronto para download</p>
                </div>
            </div>
            <a href="{{ Storage::url($d->arquivo_url) }}" target="_blank"
               class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl
                      bg-emerald-500 hover:bg-emerald-600 text-white text-sm lato-bold transition">
                <x-lucide-download class="w-4 h-4" />Baixar documento
            </a>
        </div>
        @endif

    </div>{{-- /body --}}

    @if ($d->status === 'pendente')
    <div class="px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0">
        <button wire:click="cancelarSolicitacao({{ $d->id }})" type="button"
                wire:confirm="Tem certeza que deseja cancelar esta solicitação?"
                class="cursor-pointer w-full py-2.5 rounded-xl border border-rose-200 dark:border-rose-700/40
                       text-rose-500 dark:text-rose-400 text-sm lato-bold
                       hover:bg-rose-50 dark:hover:bg-rose-900/20 transition">
            Cancelar solicitação
        </button>
    </div>
    @endif

</div>{{-- /drawer panel --}}
@endif{{-- /drawer --}}

</div>{{-- /root único --}}
