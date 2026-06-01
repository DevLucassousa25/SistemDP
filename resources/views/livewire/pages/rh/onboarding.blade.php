<div class="bg-slate-50 dark:bg-slate-900">

    {{-- ═══════════════════════════════════════════════════════════════
         HEADER
    ═══════════════════════════════════════════════════════════════════ --}}
    <div class="bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-4 sm:px-6 py-4">
        <div class="flex items-center justify-between gap-4 max-w-7xl mx-auto">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-green-500 to-emerald-600 flex items-center justify-center shrink-0 shadow-sm shadow-green-500/20">
                    <x-lucide-user-check class="w-5 h-5 text-white" />
                </div>
                <div>
                    <h1 class="text-base sm:text-lg font-semibold text-slate-800 dark:text-white lato-bold leading-tight">Onboarding</h1>
                    <p class="text-xs text-slate-400 lato-regular hidden sm:block">Acompanhe a integração dos novos colaboradores</p>
                </div>
            </div>

            {{-- Busca --}}
            <div class="relative flex-1 max-w-xs hidden sm:block">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar colaborador..."
                    class="w-full pl-9 pr-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                           bg-slate-50 dark:bg-slate-700 text-slate-800 dark:text-white
                           placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                           transition lato-regular" />
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-6 space-y-6">

        {{-- ═══════════════════════════════════════════════════════════
             STATS
        ═══════════════════════════════════════════════════════════════ --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
            @php
                $statsCards = [
                    ['label' => 'Em andamento', 'value' => $this->stats['em_andamento'], 'color' => 'text-blue-600',    'bg' => 'bg-blue-50 dark:bg-blue-900/20',    'icon' => 'loader'],
                    ['label' => 'Concluídos',   'value' => $this->stats['concluido'],    'color' => 'text-green-600',   'bg' => 'bg-green-50 dark:bg-green-900/20',  'icon' => 'check-circle'],
                    ['label' => 'Cancelados',   'value' => $this->stats['cancelado'],    'color' => 'text-red-500',     'bg' => 'bg-red-50 dark:bg-red-900/20',      'icon' => 'x-circle'],
                    ['label' => 'Total',        'value' => $this->stats['total'],        'color' => 'text-slate-700 dark:text-slate-200', 'bg' => 'bg-slate-100 dark:bg-slate-700', 'icon' => 'users'],
                ];
            @endphp
            @foreach ($statsCards as $s)
                <div class="{{ $s['bg'] }} rounded-xl px-4 py-3 border border-white/60 dark:border-slate-600">
                    <div class="flex items-center gap-2 mb-1">
                        <x-dynamic-component :component="'lucide-' . $s['icon']" class="w-4 h-4 {{ $s['color'] }}" />
                        <span class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $s['label'] }}</span>
                    </div>
                    <p class="text-2xl font-bold lato-black {{ $s['color'] }}">{{ $s['value'] }}</p>
                </div>
            @endforeach
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             FILTROS DE STATUS
        ═══════════════════════════════════════════════════════════════ --}}
        <div class="flex items-center gap-2 flex-wrap">
            @foreach ([
                'em_andamento' => ['Em andamento', 'bg-blue-500'],
                'concluido'    => ['Concluídos',   'bg-green-500'],
                'cancelado'    => ['Cancelados',   'bg-red-500'],
                'todos'        => ['Todos',        'bg-slate-500'],
            ] as $val => [$lbl, $activeClass])
                <button wire:click="$set('statusFiltro', '{{ $val }}')"
                    class="px-3 py-1.5 rounded-lg text-xs lato-bold transition cursor-pointer border
                           {{ $statusFiltro === $val
                               ? $activeClass . ' text-white border-transparent shadow-sm'
                               : 'bg-white dark:bg-slate-800 border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-slate-300' }}">
                    {{ $lbl }}
                </button>
            @endforeach
        </div>

        {{-- ═══════════════════════════════════════════════════════════
             LISTA DE ONBOARDINGS
        ═══════════════════════════════════════════════════════════════ --}}
        @forelse ($this->onboardings as $onb)
            @php
                $total     = $onb->tarefas->count();
                $concluidas = $onb->tarefas->where('status', 'concluido')->count();
                $pct       = $total > 0 ? (int) round($concluidas / $total * 100) : 0;
                $statusCls = match ($onb->status) {
                    'em_andamento' => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                    'concluido'    => 'bg-green-100 text-green-700 dark:bg-green-900/30 dark:text-green-400',
                    'cancelado'    => 'bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400',
                    default        => 'bg-slate-100 text-slate-600',
                };
                $statusLbl = match ($onb->status) {
                    'em_andamento' => 'Em andamento',
                    'concluido'    => 'Concluído',
                    'cancelado'    => 'Cancelado',
                    default        => $onb->status,
                };
            @endphp
            <div class="bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700 p-4 sm:p-5
                        hover:border-slate-300 dark:hover:border-slate-600 transition cursor-pointer"
                 wire:click="openDrawer({{ $onb->id }})">

                <div class="flex items-start gap-4">
                    {{-- Avatar --}}
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-sm font-bold shrink-0 lato-black">
                        {{ $onb->user?->initials() ?? '?' }}
                    </div>

                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 mb-0.5">
                            <p class="text-sm font-semibold text-slate-800 dark:text-white lato-bold truncate">
                                {{ $onb->user?->name ?? '—' }}
                            </p>
                            <span class="text-[10px] px-2 py-0.5 rounded-full lato-bold {{ $statusCls }}">
                                {{ $statusLbl }}
                            </span>
                        </div>

                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular truncate">
                            {{ $onb->cargo ?? '—' }}
                            @if ($onb->department)
                                · {{ $onb->department->name }}
                            @endif
                        </p>

                        @if ($onb->data_inicio)
                            <p class="text-[11px] text-slate-400 mt-0.5 lato-regular">
                                Início: {{ $onb->data_inicio->format('d/m/Y') }}
                            </p>
                        @endif

                        {{-- Progresso --}}
                        <div class="mt-3">
                            <div class="flex items-center justify-between mb-1">
                                <span class="text-[10px] text-slate-400 lato-regular">{{ $concluidas }}/{{ $total }} tarefas</span>
                                <span class="text-[10px] font-semibold lato-bold {{ $pct === 100 ? 'text-green-600' : 'text-slate-500' }}">{{ $pct }}%</span>
                            </div>
                            <div class="h-1.5 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden">
                                <div class="h-full rounded-full transition-all duration-500
                                            {{ $pct === 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>
                        </div>
                    </div>

                    {{-- Vaga --}}
                    <div class="hidden sm:block shrink-0 text-right">
                        <p class="text-[11px] text-slate-400 lato-regular">Vaga</p>
                        <p class="text-xs text-slate-600 dark:text-slate-300 lato-regular max-w-[140px] truncate">
                            {{ $onb->candidatura?->vaga?->titulo ?? '—' }}
                        </p>
                    </div>
                </div>
            </div>
        @empty
            <div class="flex flex-col items-center justify-center py-16 text-center bg-white dark:bg-slate-800 rounded-xl border border-slate-200 dark:border-slate-700">
                <x-lucide-user-check class="w-10 h-10 text-slate-300 dark:text-slate-600 mb-3" />
                <p class="text-sm font-medium text-slate-500 dark:text-slate-400 lato-bold">Nenhum onboarding encontrado</p>
                <p class="text-xs text-slate-400 mt-1 lato-regular">
                    Os onboardings são criados automaticamente ao aprovar candidatos no pipeline.
                </p>
            </div>
        @endforelse
    </div>

    {{-- ═══════════════════════════════════════════════════════════════
         DRAWER DE DETALHE
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($drawerOpen && $this->drawerOnboarding)
        @php $onb = $this->drawerOnboarding; @endphp
        <div class="fixed inset-0 z-50 flex justify-end">
            {{-- Backdrop --}}
            <div wire:click="closeDrawer" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

            {{-- Panel --}}
            <div class="relative bg-white dark:bg-slate-800 w-full max-w-md h-full flex flex-col overflow-hidden shadow-2xl">

                {{-- Header --}}
                <div class="flex items-start justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-700 shrink-0">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-sm font-bold lato-black">
                            {{ $onb->user?->initials() ?? '?' }}
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white lato-bold">{{ $onb->user?->name ?? '—' }}</p>
                            <p class="text-xs text-slate-400 lato-regular">{{ $onb->cargo ?? '—' }}</p>
                        </div>
                    </div>
                    <button wire:click="closeDrawer" class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto px-5 py-4 space-y-5">

                    {{-- Infos gerais --}}
                    <div class="bg-slate-50 dark:bg-slate-700/40 rounded-xl p-4 grid grid-cols-2 gap-3 text-xs">
                        <div>
                            <p class="text-slate-400 lato-regular mb-0.5">E-mail</p>
                            <p class="text-slate-700 dark:text-slate-200 lato-regular truncate">{{ $onb->user?->email ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 lato-regular mb-0.5">Departamento</p>
                            <p class="text-slate-700 dark:text-slate-200 lato-regular">{{ $onb->department?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 lato-regular mb-0.5">Data de início</p>
                            <p class="text-slate-700 dark:text-slate-200 lato-regular">{{ $onb->data_inicio?->format('d/m/Y') ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 lato-regular mb-0.5">Vaga</p>
                            <p class="text-slate-700 dark:text-slate-200 lato-regular truncate">{{ $onb->candidatura?->vaga?->titulo ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 lato-regular mb-0.5">Criado por</p>
                            <p class="text-slate-700 dark:text-slate-200 lato-regular">{{ $onb->criadoPor?->name ?? '—' }}</p>
                        </div>
                        <div>
                            <p class="text-slate-400 lato-regular mb-0.5">Senha padrão</p>
                            <p class="text-slate-700 dark:text-slate-200 font-mono text-[11px] bg-slate-200 dark:bg-slate-600 px-2 py-0.5 rounded select-all">lu753951</p>
                        </div>
                    </div>

                    {{-- Progresso --}}
                    @php
                        $total      = $onb->tarefas->count();
                        $concluidas = $onb->tarefas->where('status', 'concluido')->count();
                        $pct        = $total > 0 ? (int) round($concluidas / $total * 100) : 0;
                    @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-slate-600 dark:text-slate-300 lato-bold uppercase tracking-wide">Checklist de Integração</p>
                            <span class="text-xs font-semibold lato-bold {{ $pct === 100 ? 'text-green-600' : 'text-slate-500' }}">{{ $pct }}%</span>
                        </div>
                        <div class="h-2 rounded-full bg-slate-100 dark:bg-slate-700 overflow-hidden mb-4">
                            <div class="h-full rounded-full transition-all duration-500 {{ $pct === 100 ? 'bg-green-500' : 'bg-blue-500' }}"
                                 style="width: {{ $pct }}%"></div>
                        </div>

                        {{-- Lista de tarefas --}}
                        <div class="space-y-2">
                            @foreach ($onb->tarefas as $tarefa)
                                @php
                                    $respCls = match ($tarefa->responsavel) {
                                        'rh'         => 'bg-blue-100 text-blue-700 dark:bg-blue-900/30 dark:text-blue-400',
                                        'ti'         => 'bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-indigo-400',
                                        'gestao'     => 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400',
                                        'financeiro' => 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-400',
                                        default      => 'bg-slate-100 text-slate-600',
                                    };
                                    $respLbl = match ($tarefa->responsavel) {
                                        'rh' => 'RH', 'ti' => 'TI', 'gestao' => 'Gestão', 'financeiro' => 'Financeiro',
                                        default => ucfirst($tarefa->responsavel),
                                    };
                                @endphp
                                <div class="flex items-start gap-3 p-3 rounded-xl border transition
                                            {{ $tarefa->status === 'concluido'
                                                ? 'border-green-200 dark:border-green-800 bg-green-50/40 dark:bg-green-900/10'
                                                : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700/30' }}">
                                    <button wire:click="toggleTarefa({{ $tarefa->id }})"
                                        class="w-5 h-5 rounded-full border-2 flex items-center justify-center shrink-0 mt-0.5 transition cursor-pointer
                                               {{ $tarefa->status === 'concluido'
                                                   ? 'bg-green-500 border-green-500 text-white'
                                                   : 'border-slate-300 dark:border-slate-500 hover:border-green-400' }}">
                                        @if ($tarefa->status === 'concluido')
                                            <x-lucide-check class="w-3 h-3" />
                                        @endif
                                    </button>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm lato-regular {{ $tarefa->status === 'concluido' ? 'line-through text-slate-400' : 'text-slate-700 dark:text-slate-200' }}">
                                            {{ $tarefa->titulo }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-1">
                                            <span class="text-[10px] px-1.5 py-0.5 rounded-full lato-bold {{ $respCls }}">{{ $respLbl }}</span>
                                            @if ($tarefa->status === 'concluido' && $tarefa->concluidoPor)
                                                <span class="text-[10px] text-slate-400 lato-regular">por {{ $tarefa->concluidoPor->name }}</span>
                                            @endif
                                        </div>
                                    </div>
                                    <button wire:click="removerTarefa({{ $tarefa->id }})"
                                        class="p-1 rounded text-slate-300 hover:text-red-400 transition cursor-pointer shrink-0">
                                        <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- Adicionar tarefa --}}
                    <div class="border border-dashed border-slate-200 dark:border-slate-600 rounded-xl p-3">
                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold mb-2">+ Adicionar tarefa</p>
                        <div class="flex flex-col gap-2">
                            <input wire:model="novaTarefaTitulo" type="text" placeholder="Título da tarefa..."
                                class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                       bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                       placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                       transition lato-regular" />
                            <div class="flex gap-2">
                                <select wire:model="novaTarefaResp"
                                    class="flex-1 px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                           bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                           transition lato-regular">
                                    <option value="rh">RH</option>
                                    <option value="ti">TI</option>
                                    <option value="gestao">Gestão</option>
                                    <option value="financeiro">Financeiro</option>
                                </select>
                                <button wire:click="adicionarTarefa"
                                    class="px-4 py-2 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-sm lato-bold transition cursor-pointer">
                                    Adicionar
                                </button>
                            </div>
                        </div>
                        @error('novaTarefaTitulo')
                            <p class="text-xs text-red-500 mt-1 lato-regular">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ── Equipamentos vinculados ── --}}
                    @php $eqsOnb = $this->drawerEquipamentos; @endphp
                    <div>
                        <div class="flex items-center justify-between mb-2">
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide">Equipamentos Entregues</p>
                            @if ($eqsOnb->count() > 0)
                            <span class="text-[10px] px-2 py-0.5 rounded-full bg-violet-100 text-violet-700 dark:bg-violet-900/30 dark:text-indigo-400 lato-bold">
                                {{ $eqsOnb->count() }} {{ $eqsOnb->count() === 1 ? 'item' : 'itens' }}
                            </span>
                            @endif
                        </div>
                        @if ($eqsOnb->isEmpty())
                            <div class="flex items-center gap-2 p-3 rounded-xl bg-slate-50 dark:bg-slate-700/40 border border-dashed border-slate-200 dark:border-slate-600">
                                <x-lucide-package class="w-4 h-4 text-slate-300 shrink-0" />
                                <p class="text-xs text-slate-400 lato-regular">Nenhum equipamento entregue ainda. Faça o registro no módulo <strong>Equipamentos</strong>.</p>
                            </div>
                        @else
                            @php
                                $catIconsOnb = ['notebook'=>'laptop-2','desktop'=>'monitor','monitor'=>'monitor-dot','teclado'=>'keyboard','mouse'=>'mouse-pointer-2','headset'=>'headphones','cracha'=>'id-card','epi'=>'hard-hat','celular'=>'smartphone','cadeira'=>'sofa','outros'=>'package'];
                                $condLblOnb  = ['novo'=>'Novo','bom'=>'Bom','regular'=>'Regular','danificado'=>'Danificado'];
                            @endphp
                            <div class="space-y-2">
                                @foreach ($eqsOnb as $atrib)
                                @php $eq = $atrib->equipamento; @endphp
                                <div class="flex items-center gap-3 p-3 rounded-xl bg-violet-50 dark:bg-violet-900/10 border border-violet-100 dark:border-violet-800/30">
                                    <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-900/30 flex items-center justify-center shrink-0">
                                        <x-dynamic-component :component="'lucide-'.($catIconsOnb[$eq?->categoria] ?? 'package')"
                                            class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $eq?->nome ?? '—' }}</p>
                                        <p class="text-[10px] text-slate-400 lato-regular">
                                            {{ ucfirst($eq?->categoria ?? '') }}
                                            · {{ $condLblOnb[$atrib->condicao_entrega] ?? $atrib->condicao_entrega }}
                                            · {{ $atrib->data_entrega?->format('d/m/Y') ?? '—' }}
                                        </p>
                                    </div>
                                    <x-lucide-check class="w-4 h-4 text-indigo-500 shrink-0" />
                                </div>
                                @endforeach
                            </div>
                        @endif
                    </div>

                    {{-- Observações --}}
                    @if ($onb->observacoes)
                        <div>
                            <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 lato-bold uppercase tracking-wide mb-1">Observações</p>
                            <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular whitespace-pre-line">{{ $onb->observacoes }}</p>
                        </div>
                    @endif
                </div>

                {{-- Footer --}}
                @if ($onb->status === 'em_andamento')
                    <div class="shrink-0 px-5 py-4 border-t border-slate-200 dark:border-slate-700 bg-slate-50/50 dark:bg-slate-800 flex gap-2">
                        <button wire:click="cancelarOnboarding({{ $onb->id }})"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-500 dark:text-slate-400
                                   border border-slate-200 dark:border-slate-600 hover:border-red-300 hover:text-red-500
                                   transition cursor-pointer lato-bold">
                            Cancelar
                        </button>
                        <button wire:click="abrirConcluir({{ $onb->id }})"
                            class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-white
                                   bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700
                                   shadow-md shadow-green-500/20 transition cursor-pointer lato-bold flex items-center justify-center gap-2">
                            <x-lucide-check-circle class="w-4 h-4" />
                            Concluir Onboarding
                        </button>
                    </div>
                @endif
            </div>
        </div>
    @endif

    {{-- ═══════════════════════════════════════════════════════════════
         MODAL CONFIRMAR CONCLUSÃO
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($concluirModal)
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div wire:click="$set('concluirModal', false)" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div class="relative bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-sm p-6">
                <div class="flex flex-col items-center text-center gap-3">
                    <div class="w-12 h-12 rounded-full bg-green-100 dark:bg-green-900/30 flex items-center justify-center">
                        <x-lucide-check-circle class="w-6 h-6 text-green-600 dark:text-green-400" />
                    </div>
                    <h3 class="text-base font-semibold text-slate-800 dark:text-white lato-bold">Concluir Onboarding?</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">
                        Todas as tarefas pendentes serão marcadas como concluídas e o processo de integração será finalizado.
                    </p>
                </div>
                <div class="flex gap-3 mt-6">
                    <button wire:click="$set('concluirModal', false)"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-slate-600 border border-slate-200 dark:border-slate-600 hover:border-slate-300 transition cursor-pointer lato-bold bg-white dark:bg-slate-700 dark:text-slate-300">
                        Cancelar
                    </button>
                    <button wire:click="concluirOnboarding"
                        class="flex-1 px-4 py-2.5 rounded-xl text-sm font-medium text-white
                               bg-gradient-to-r from-green-500 to-emerald-600 hover:from-green-600 hover:to-emerald-700
                               shadow-sm transition cursor-pointer lato-bold">
                        Confirmar
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
