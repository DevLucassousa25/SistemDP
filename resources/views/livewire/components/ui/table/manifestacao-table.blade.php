<div>

    {{-- ═══════════════════════════════════════════════
         BARRA DE BUSCA + FILTROS
    ═══════════════════════════════════════════════ --}}
    <div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-col gap-3 mb-5">

        {{-- Busca --}}
        <div class="relative w-full">
            <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
            <input
                type="text"
                wire:model.live.debounce.300ms="search"
                placeholder="Buscar por protocolo, assunto ou descrição..."
                class="w-full pl-10 pr-9 py-2.5 text-sm border border-gray-200 rounded-xl
                    focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent
                    placeholder-gray-400 lato-regular"
            />
            @if ($search)
                <button wire:click="$set('search', '')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 transition cursor-pointer">
                    <x-lucide-x class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>

        {{-- Filtros --}}
        <div class="grid gap-2 {{ ($podeVerTudo && $configGlobal->auto_encerramento_ativo) ? 'grid-cols-2 sm:grid-cols-4' : 'grid-cols-3' }}">

            {{-- Filtro Categoria --}}
            @php
                $categoriasMap = [
                    'sugestao'   => ['label' => 'Sugestão',   'icon' => 'lightbulb',    'color' => 'blue'],
                    'reclamacao' => ['label' => 'Reclamação', 'icon' => 'alert-circle',  'color' => 'orange'],
                    'denuncia'   => ['label' => 'Denúncia',   'icon' => 'shield-alert',  'color' => 'red'],
                    'elogio'     => ['label' => 'Elogio',     'icon' => 'thumbs-up',     'color' => 'emerald'],
                ];
                $statusMap = [
                    'em_analise'   => ['label' => 'Em Análise',   'color' => 'yellow'],
                    'em_andamento' => ['label' => 'Em Andamento', 'color' => 'blue'],
                    'respondido'   => ['label' => 'Respondido',   'color' => 'purple'],
                    'concluido'    => ['label' => 'Concluído',    'color' => 'emerald'],
                ];
            @endphp

            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white transition cursor-pointer
                    {{ $categoria ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        <x-lucide-tag class="w-3.5 h-3.5 flex-shrink-0" />
                        <span class="truncate text-xs font-medium lato-bold">
                            {{ $categoria ? ($categoriasMap[$categoria]['label'] ?? $categoria) : 'Categoria' }}
                        </span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute mt-1 w-52 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('categoria', '')"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2">
                        <x-lucide-list class="w-3.5 h-3.5" /> Todas as categorias
                    </button>
                    <div class="border-t border-gray-100 my-1"></div>
                    @foreach ($categoriasMap as $value => $cat)
                        <button @click="open=false; $wire.set('categoria', '{{ $value }}')"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 flex items-center justify-between cursor-pointer
                            {{ $categoria === $value ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700' }}">
                            <div class="flex items-center gap-2">
                                <x-dynamic-component :component="'lucide-' . $cat['icon']" class="w-3.5 h-3.5" />
                                {{ $cat['label'] }}
                            </div>
                            @if ($categoria === $value)
                                <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" />
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Filtro Status --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white transition cursor-pointer
                    {{ $status ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        <x-lucide-circle-dot class="w-3.5 h-3.5 flex-shrink-0" />
                        <span class="truncate text-xs font-medium lato-bold">
                            {{ $status ? ($statusMap[$status]['label'] ?? $status) : 'Status' }}
                        </span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('status', '')"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2 cursor-pointer">
                        <x-lucide-list class="w-3.5 h-3.5" /> Todos os status
                    </button>
                    <div class="border-t border-gray-100 my-1"></div>
                    @foreach ($statusMap as $value => $st)
                        @php
                            $dotColors = [
                                'yellow'  => 'bg-yellow-400',
                                'blue'    => 'bg-blue-400',
                                'purple'  => 'bg-purple-400',
                                'emerald' => 'bg-emerald-400',
                            ];
                        @endphp
                        <button @click="open=false; $wire.set('status', '{{ $value }}')"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 flex items-center justify-between cursor-pointer
                            {{ $status === $value ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700' }}">
                            <div class="flex items-center gap-2">
                                <span class="w-2 h-2 rounded-full {{ $dotColors[$st['color']] }}"></span>
                                {{ $st['label'] }}
                            </div>
                            @if ($status === $value)
                                <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" />
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Filtro Anônimo --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white transition cursor-pointer cursor-pointer
                    {{ $anonimo !== '' ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        @if ($anonimo === '1')
                            <x-lucide-eye-off class="w-3.5 h-3.5 flex-shrink-0" />
                        @elseif ($anonimo === '0')
                            <x-lucide-user class="w-3.5 h-3.5 flex-shrink-0" />
                        @else
                            <x-lucide-eye class="w-3.5 h-3.5 flex-shrink-0" />
                        @endif
                        <span class="truncate text-xs font-medium lato-bold">
                            {{ match($anonimo) {
                                '1'     => 'Anônimos',
                                '0'     => 'Identificados',
                                default => 'Autor',
                            } }}
                        </span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-1 w-44 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('anonimo', '')"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2 cursor-pointer">
                        <x-lucide-list class="w-3.5 h-3.5" /> Todos
                    </button>
                    <div class="border-t border-gray-100 my-1"></div>
                    <button @click="open=false; $wire.set('anonimo', '0')"
                        class="w-full text-left px-3 py-2 text-sm flex items-center justify-between hover:bg-gray-50 cursor-pointer
                        {{ $anonimo === '0' ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700' }}">
                        <div class="flex items-center gap-2">
                            <x-lucide-user class="w-3.5 h-3.5" />
                            Identificados
                        </div>
                        @if ($anonimo === '0')
                            <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" />
                        @endif
                    </button>
                    <button @click="open=false; $wire.set('anonimo', '1')"
                        class="w-full text-left px-3 py-2 text-sm flex items-center justify-between hover:bg-gray-50
                        {{ $anonimo === '1' ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700' }}">
                        <div class="flex items-center gap-2">
                            <x-lucide-eye-off class="w-3.5 h-3.5" />
                            Anônimos
                        </div>
                        @if ($anonimo === '1')
                            <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" />
                        @endif
                    </button>
                </div>
            </div>

            {{-- Filtro Vencimento (RH/Admin + auto-encerramento ativo) --}}
            @if ($podeVerTudo && $configGlobal->auto_encerramento_ativo)
            @php
                $vencimentoOpcoes = [
                    'proximos' => ['label' => 'Próximos a vencer', 'icon' => 'clock',          'color' => 'blue'],
                    'atencao'  => ['label' => 'Atenção (50–79%)',  'icon' => 'triangle-alert',  'color' => 'amber'],
                    'critico'  => ['label' => 'Crítico (≥ 80%)',   'icon' => 'flame',            'color' => 'red'],
                ];
                $vencimentoAtual = $vencimentoOpcoes[$vencimento] ?? null;
            @endphp
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white transition cursor-pointer cursor-pointer
                    {{ $vencimento ? 'border-red-400 bg-red-50 text-red-700' : 'border-gray-200 text-gray-500' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        <x-lucide-bot class="w-3.5 h-3.5 flex-shrink-0" />
                        <span class="truncate text-xs font-medium lato-bold">
                            {{ $vencimentoAtual ? $vencimentoAtual['label'] : 'Vencimento' }}
                        </span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-1 w-56 bg-white border border-gray-200 rounded-xl shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('vencimento', '')"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2 cursor-pointer">
                        <x-lucide-list class="w-3.5 h-3.5" /> Todos
                    </button>
                    <div class="border-t border-gray-100 my-1"></div>
                    @foreach ($vencimentoOpcoes as $value => $op)
                        @php
                            $iconColors = [
                                'blue'  => 'text-blue-500',
                                'amber' => 'text-amber-500',
                                'red'   => 'text-red-500',
                            ];
                        @endphp
                        <button @click="open=false; $wire.set('vencimento', '{{ $value }}')"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 flex items-center justify-between cursor-pointer
                            {{ $vencimento === $value ? 'bg-red-50 text-red-700 font-medium' : 'text-gray-700' }}">
                            <div class="flex items-center gap-2">
                                <x-dynamic-component
                                    :component="'lucide-' . $op['icon']"
                                    class="w-3.5 h-3.5 {{ $iconColors[$op['color']] }}" />
                                {{ $op['label'] }}
                            </div>
                            @if ($vencimento === $value)
                                <x-lucide-check class="w-3.5 h-3.5 text-red-500" />
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
            @endif

        </div>

        {{-- Chips de filtros ativos --}}
        @php $temFiltros = $search || $categoria || $status || $anonimo !== '' || $vencimento !== ''; @endphp
        @if ($temFiltros)
            <div class="flex items-center gap-2 flex-wrap pt-1">
                <span class="text-xs text-gray-400 lato-regular">Filtros:</span>

                @if ($search)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium lato-bold">
                        <x-lucide-search class="w-3 h-3" />
                        "{{ Str::limit($search, 25) }}"
                        <button wire:click="$set('search', '')" class="ml-0.5 hover:text-slate-900 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($categoria)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium lato-bold">
                        <x-lucide-tag class="w-3 h-3" />
                        {{ $categoriasMap[$categoria]['label'] ?? $categoria }}
                        <button wire:click="$set('categoria', '')" class="ml-0.5 hover:text-emerald-900 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($status)
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-blue-100 text-blue-700 text-xs font-medium lato-bold">
                        <x-lucide-circle-dot class="w-3 h-3" />
                        {{ $statusMap[$status]['label'] ?? $status }}
                        <button wire:click="$set('status', '')" class="ml-0.5 hover:text-blue-900 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($anonimo !== '')
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium lato-bold
                        {{ $anonimo === '1' ? 'bg-purple-100 text-purple-700' : 'bg-gray-100 text-gray-600' }}">
                        @if ($anonimo === '1')
                            <x-lucide-eye-off class="w-3 h-3" />
                            Anônimos
                        @else
                            <x-lucide-user class="w-3 h-3" />
                            Identificados
                        @endif
                        <button wire:click="$set('anonimo', '')" class="ml-0.5 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($vencimento && isset($vencimentoOpcoes[$vencimento]))
                    @php
                        $chipVenc = $vencimentoOpcoes[$vencimento];
                        $chipVencColors = ['blue' => 'bg-blue-100 text-blue-700', 'amber' => 'bg-amber-100 text-amber-700', 'red' => 'bg-red-100 text-red-700'];
                    @endphp
                    <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium lato-bold
                        {{ $chipVencColors[$chipVenc['color']] ?? 'bg-gray-100 text-gray-600' }}">
                        <x-dynamic-component :component="'lucide-' . $chipVenc['icon']" class="w-3 h-3" />
                        {{ $chipVenc['label'] }}
                        <button wire:click="$set('vencimento', '')" class="ml-0.5 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                <button wire:click="limparFiltros"
                    class="ml-auto text-xs text-gray-400 hover:text-red-500 transition flex items-center gap-1 cursor-pointer lato-regular">
                    <x-lucide-filter-x class="w-3.5 h-3.5" />
                    Limpar tudo
                </button>
            </div>
        @endif

    </div>

    {{-- ═══════════════════════════════════════════════
         SKELETON — exibido enquanto o Livewire carrega
    ═══════════════════════════════════════════════ --}}
    <div wire:loading class="w-full">

        {{-- Skeleton Mobile --}}
        <div class="md:hidden flex flex-col gap-3">
            @for ($i = 0; $i < 5; $i++)
                <div class="bg-white border border-gray-100 rounded-xl p-4 space-y-3 animate-pulse shadow-sm">
                    <div class="flex items-center justify-between">
                        <div class="h-4 bg-gray-200 rounded-full w-28"></div>
                        <div class="h-5 bg-gray-200 rounded-full w-20"></div>
                    </div>
                    <div class="h-3.5 bg-gray-200 rounded-full w-3/4"></div>
                    <div class="h-3 bg-gray-200 rounded-full w-full"></div>
                    <div class="h-3 bg-gray-200 rounded-full w-2/3"></div>
                    <div class="flex items-center justify-between pt-1">
                        <div class="h-5 bg-gray-200 rounded-full w-20"></div>
                        <div class="h-3 bg-gray-200 rounded-full w-24"></div>
                    </div>
                </div>
            @endfor
        </div>

        {{-- Skeleton Desktop --}}
        <div class="hidden md:block overflow-hidden border border-gray-100 rounded-xl">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        @foreach (['w-24', 'w-20', 'w-16', 'w-28', 'w-20', 'w-16'] as $w)
                            <th class="px-4 py-3 text-left">
                                <div class="h-3 bg-gray-200 rounded-full {{ $w }} animate-pulse"></div>
                            </th>
                        @endforeach
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @for ($i = 0; $i < 8; $i++)
                        <tr>
                            <td class="px-4 py-3.5"><div class="h-3 bg-gray-200 rounded-full w-28 animate-pulse"></div></td>
                            <td class="px-4 py-3.5"><div class="h-5 bg-gray-200 rounded-full w-20 animate-pulse"></div></td>
                            <td class="px-4 py-3.5"><div class="h-3 bg-gray-200 rounded-full w-36 animate-pulse"></div></td>
                            <td class="px-4 py-3.5"><div class="h-3 bg-gray-200 rounded-full w-48 animate-pulse"></div></td>
                            <td class="px-4 py-3.5"><div class="h-5 bg-gray-200 rounded-full w-24 animate-pulse"></div></td>
                            <td class="px-4 py-3.5"><div class="h-3 bg-gray-200 rounded-full w-20 animate-pulse"></div></td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         CONTEÚDO REAL
    ═══════════════════════════════════════════════ --}}
    <div wire:loading.remove>

        @php
            $categoriaStyles = [
                'sugestao'   => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'ring' => 'ring-blue-200',    'label' => 'Sugestão',   'icon' => 'lightbulb'],
                'reclamacao' => ['bg' => 'bg-orange-50',  'text' => 'text-orange-700',  'ring' => 'ring-orange-200',  'label' => 'Reclamação', 'icon' => 'alert-circle'],
                'denuncia'   => ['bg' => 'bg-red-50',     'text' => 'text-red-700',     'ring' => 'ring-red-200',     'label' => 'Denúncia',   'icon' => 'shield-alert'],
                'elogio'     => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-200', 'label' => 'Elogio',     'icon' => 'thumbs-up'],
            ];

            $statusStyles = [
                'em_analise'   => ['bg' => 'bg-yellow-50',  'text' => 'text-yellow-700',  'dot' => 'bg-yellow-400',  'label' => 'Em Análise'],
                'em_andamento' => ['bg' => 'bg-blue-50',    'text' => 'text-blue-700',    'dot' => 'bg-blue-400',    'label' => 'Em Andamento'],
                'respondido'   => ['bg' => 'bg-purple-50',  'text' => 'text-purple-700',  'dot' => 'bg-purple-400',  'label' => 'Respondido'],
                'concluido'    => ['bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'dot' => 'bg-emerald-400', 'label' => 'Concluído'],
            ];
        @endphp

        {{-- ── MOBILE ── --}}
        <div class="md:hidden flex flex-col gap-3">

            @forelse ($manifestacoes as $item)
                @php
                    $cat = $categoriaStyles[$item->categoria] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'ring' => 'ring-gray-200', 'label' => $item->categoria, 'icon' => 'file'];
                    $st  = $statusStyles[$item->status]       ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400', 'label' => $item->status];

                    // ── Urgência de vencimento ──────────────────────────────────────
                    $itemUrgencia = null;
                    $itemPct      = 0;
                    if (
                        $configGlobal->auto_encerramento_ativo &&
                        ! $item->auto_encerramento_desativado &&
                        in_array($item->status, ['em_analise', 'em_andamento'])
                    ) {
                        $ultimaRespItem = $item->respostas->max('created_at');
                        $ultimaAtivItem = $ultimaRespItem
                            ? max($item->updated_at, \Carbon\Carbon::parse($ultimaRespItem))
                            : $item->updated_at;
                        $minsDecItem    = max(0, (int) floor($ultimaAtivItem->diffInMinutes(now())));
                        $prazoMinsItem  = ($item->prazo_personalizado_horas ?? $configGlobal->prazo_horas) * 60;
                        $itemPct        = $prazoMinsItem > 0 ? max(0, min(100, round($minsDecItem / $prazoMinsItem * 100, 1))) : 0;
                        if ($itemPct >= 80)      $itemUrgencia = 'critico';
                        elseif ($itemPct >= 50)  $itemUrgencia = 'atencao';
                    }
                @endphp

                <div class="bg-white border rounded-xl p-4 shadow-sm space-y-3
                    {{ $itemUrgencia === 'critico' ? 'border-red-200' : ($itemUrgencia === 'atencao' ? 'border-amber-200' : 'border-gray-100') }}">

                    {{-- Protocolo + Status --}}
                    <div class="flex items-center justify-between gap-2">
                        <span class="text-xs font-mono font-semibold text-gray-500 lato-bold">{{ $item->protocolo }}</span>
                        <div class="flex items-center gap-1.5">
                            @if ($itemUrgencia === 'critico')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 text-red-600">
                                    <x-lucide-flame class="w-2.5 h-2.5" /> {{ $itemPct }}%
                                </span>
                            @elseif ($itemUrgencia === 'atencao')
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-semibold bg-amber-100 text-amber-600">
                                    <x-lucide-triangle-alert class="w-2.5 h-2.5" /> {{ $itemPct }}%
                                </span>
                            @endif
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium lato-bold
                                {{ $st['bg'] }} {{ $st['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                {{ $st['label'] }}
                            </span>
                        </div>
                    </div>

                    {{-- Categoria + Assunto --}}
                    <div class="space-y-1">
                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-xs font-medium ring-1 lato-bold
                            {{ $cat['bg'] }} {{ $cat['text'] }} {{ $cat['ring'] }}">
                            <x-dynamic-component :component="'lucide-' . $cat['icon']" class="w-3 h-3" />
                            {{ $cat['label'] }}
                        </span>
                        <p class="text-sm font-semibold text-gray-800 lato-bold leading-snug">{{ $item->assunto }}</p>
                    </div>

                    {{-- Descrição truncada --}}
                    <p class="text-xs text-gray-500 lato-regular leading-relaxed">
                        {{ \Illuminate\Support\Str::limit($item->descricao, 30, '...') }}
                    </p>
                    {{-- Autor + Data + Ação --}}
                    <div class="flex items-center justify-between pt-1 border-t border-gray-100">
                        <div class="flex items-center gap-1.5 text-xs text-gray-400 lato-regular">
                            @if ($item->is_anonimo)
                                <x-lucide-eye-off class="w-3.5 h-3.5" />
                                <span>Anônimo</span>
                            @else
                                <x-lucide-user class="w-3.5 h-3.5" />
                                <span>{{ $item->user?->name ?? '—' }}</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-gray-400 lato-regular">
                                {{ $item->created_at->format('d/m/Y') }}
                            </span>
                            <button wire:click="ver('{{ $item->id }}')"
                                class="flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-medium lato-bold
                                    bg-gray-100 text-gray-600 hover:bg-emerald-50 hover:text-emerald-700 transition cursor-pointer">
                                <x-lucide-eye class="w-3.5 h-3.5" />
                                Ver
                            </button>
                        </div>
                    </div>

                </div>
            @empty
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                        <x-lucide-inbox class="w-7 h-7 text-gray-400" />
                    </div>
                    <p class="text-sm font-semibold text-gray-500 lato-bold">Nenhuma manifestação encontrada</p>
                    <p class="text-xs text-gray-400 lato-regular mt-1">
                        {{ $search || $categoria || $status ? 'Tente ajustar os filtros.' : 'As manifestações cadastradas aparecerão aqui.' }}
                    </p>
                </div>
            @endforelse

            {{ $manifestacoes->links('partials.pagination') }}

        </div>

        {{-- ── DESKTOP ── --}}
        <div class="hidden md:block overflow-hidden border border-gray-100 rounded-xl">
            <table class="w-full text-sm">

                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3 text-left lato-bold">Protocolo</th>
                        <th class="px-4 py-3 text-left lato-bold">Categoria</th>
                        <th class="px-4 py-3 text-left lato-bold">Assunto</th>
                        <th class="px-4 py-3 text-left lato-bold">Descrição</th>
                        <th class="px-4 py-3 text-left lato-bold">Status</th>
                        <th class="px-4 py-3 text-left lato-bold">Autor</th>
                        <th class="px-4 py-3 text-left lato-bold">Data</th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse ($manifestacoes as $item)
                        @php
                            $cat = $categoriaStyles[$item->categoria] ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'ring' => 'ring-gray-200', 'label' => $item->categoria, 'icon' => 'file'];
                            $st  = $statusStyles[$item->status]       ?? ['bg' => 'bg-gray-50', 'text' => 'text-gray-600', 'dot' => 'bg-gray-400', 'label' => $item->status];

                            // ── Urgência de vencimento ─────────────────────────────────
                            $itemUrgencia = null;
                            $itemPct      = 0;
                            if (
                                $configGlobal->auto_encerramento_ativo &&
                                ! $item->auto_encerramento_desativado &&
                                in_array($item->status, ['em_analise', 'em_andamento'])
                            ) {
                                $ultimaRespItem = $item->respostas->max('created_at');
                                $ultimaAtivItem = $ultimaRespItem
                                    ? max($item->updated_at, \Carbon\Carbon::parse($ultimaRespItem))
                                    : $item->updated_at;
                                $minsDecItem    = max(0, (int) floor($ultimaAtivItem->diffInMinutes(now())));
                                $prazoMinsItem  = ($item->prazo_personalizado_horas ?? $configGlobal->prazo_horas) * 60;
                                $itemPct        = $prazoMinsItem > 0 ? max(0, min(100, round($minsDecItem / $prazoMinsItem * 100, 1))) : 0;
                                if ($itemPct >= 80)     $itemUrgencia = 'critico';
                                elseif ($itemPct >= 50) $itemUrgencia = 'atencao';
                            }
                        @endphp

                        <tr class="hover:bg-gray-50/70 transition
                            {{ $itemUrgencia === 'critico' ? 'border-l-2 border-l-red-400' : ($itemUrgencia === 'atencao' ? 'border-l-2 border-l-amber-400' : '') }}">

                            {{-- Protocolo --}}
                            <td class="px-4 py-3.5">
                                <span class="font-mono text-xs font-semibold text-gray-500 lato-bold whitespace-nowrap">
                                    {{ $item->protocolo }}
                                </span>
                            </td>

                            {{-- Categoria --}}
                            <td class="px-4 py-3.5">
                                <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium ring-1 lato-bold whitespace-nowrap
                                    {{ $cat['bg'] }} {{ $cat['text'] }} {{ $cat['ring'] }}">
                                    <x-dynamic-component :component="'lucide-' . $cat['icon']" class="w-3 h-3" />
                                    {{ $cat['label'] }}
                                </span>
                            </td>

                            {{-- Assunto --}}
                            <td class="px-4 py-3.5 max-w-[180px]">
                                <p class="text-sm font-medium text-gray-800 lato-bold truncate" title="{{ $item->assunto }}">
                                    {{ $item->assunto }}
                                </p>
                            </td>

                            {{-- Descrição --}}
                            <td class="px-4 py-3.5 max-w-[260px]">
                                <p class="text-xs text-gray-500 lato-regular leading-relaxed line-clamp-2" title="{{ $item->descricao }}">
                                     {{ \Illuminate\Support\Str::limit($item->descricao, 80, '...') }}
                                </p>
                            </td>

                            {{-- Status + urgência --}}
                            <td class="px-4 py-3.5">
                                <div class="flex flex-col gap-1">
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-xs font-medium lato-bold whitespace-nowrap
                                        {{ $st['bg'] }} {{ $st['text'] }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $st['dot'] }}"></span>
                                        {{ $st['label'] }}
                                    </span>
                                    @if ($itemUrgencia === 'critico')
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-red-500 whitespace-nowrap">
                                            <x-lucide-flame class="w-3 h-3" />
                                            Crítico · {{ $itemPct }}%
                                        </span>
                                    @elseif ($itemUrgencia === 'atencao')
                                        <span class="inline-flex items-center gap-1 text-[10px] font-semibold text-amber-500 whitespace-nowrap">
                                            <x-lucide-triangle-alert class="w-3 h-3" />
                                            Atenção · {{ $itemPct }}%
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Autor --}}
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-1.5 text-xs text-gray-500 lato-regular whitespace-nowrap">
                                    @if ($item->is_anonimo)
                                        <x-lucide-eye-off class="w-3.5 h-3.5 text-gray-400 flex-shrink-0" />
                                        <span class="italic text-gray-400">Anônimo</span>
                                    @else
                                        <div class="w-6 h-6 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold text-[10px] flex-shrink-0">
                                            {{ strtoupper(substr($item->user?->name ?? '?', 0, 1)) }}
                                        </div>
                                        <span class="truncate max-w-[110px]" title="{{ $item->user?->name }}">
                                            {{ $item->user?->name ?? '—' }}
                                        </span>
                                    @endif
                                </div>
                            </td>

                            {{-- Data --}}
                            <td class="px-4 py-3.5">
                                <span class="text-xs text-gray-400 lato-regular whitespace-nowrap">
                                    {{ $item->created_at->format('d/m/Y') }}
                                </span>
                                <span class="block text-[10px] text-gray-300 lato-regular">
                                    {{ $item->created_at->format('H:i') }}
                                </span>
                            </td>

                            {{-- Ação --}}
                            <td class="px-4 py-3.5 text-right">
                                <a href="{{ route('ouvidoria.details', $item->id) }}" wire:navigate
                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium lato-bold
                                        text-gray-500 hover:text-emerald-700 hover:bg-emerald-50
                                        border border-gray-200 hover:border-emerald-200
                                        transition cursor-pointer whitespace-nowrap">
                                    <x-lucide-eye class="w-3.5 h-3.5" />
                                    Ver
                                </a>
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="8">
                                <div class="flex flex-col items-center justify-center py-16 text-center">
                                    <div class="w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center mb-4">
                                        <x-lucide-inbox class="w-7 h-7 text-gray-400" />
                                    </div>
                                    <p class="text-sm font-semibold text-gray-500 lato-bold">Nenhuma manifestação encontrada</p>
                                    <p class="text-xs text-gray-400 lato-regular mt-1">
                                        {{ $search || $categoria || $status ? 'Tente ajustar os filtros.' : 'As manifestações cadastradas aparecerão aqui.' }}
                                    </p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="bg-white dark:bg-gray-800 border-t border-gray-100 dark:border-gray-700 px-4">
                {{ $manifestacoes->links('partials.pagination') }}
            </div>

        </div>

    </div>{{-- fim wire:loading.remove --}}

</div>
