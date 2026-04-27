<div class="mt-10">

    {{-- ─── Filtros ─────────────────────────────────────────── --}}
    <div class="flex flex-col gap-3 p-4 bg-white rounded-xl border border-gray-100 mb-4">

        {{-- Busca --}}
        <div class="relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-gray-400" fill="none"
                stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35M17 11A6 6 0 1 1 5 11a6 6 0 0 1 12 0z" />
            </svg>
            <input wire:model.live.debounce.300ms="busca" type="text" placeholder="Buscar por nome ou localização..."
                class="w-full pl-9 pr-9 py-2 text-sm border border-gray-200 rounded-lg focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent" />
            @if ($busca)
                <button wire:click="$set('busca', '')"
                    class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                    <x-lucide-x class="w-3.5 h-3.5" />
                </button>
            @endif
        </div>

        {{-- Linha: Andar + Status + Capacidade --}}
        <div class="grid grid-cols-3 gap-2">

            {{-- Filtro Andar --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between text-sm border rounded-lg px-3 py-2 bg-white transition cursor-pointer
                {{ $filtroAndar ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        <x-lucide-building class="w-3.5 h-3.5 flex-shrink-0" />
                        <span class="truncate text-xs font-medium">
                            {{ $filtroAndar ?: 'Andar' }}
                        </span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('filtroAndar','')"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2 cursor-pointer">
                        <x-lucide-layers class="w-3.5 h-3.5" /> Todos os andares
                    </button>
                    <div class="border-t border-gray-100 my-1"></div>
                    @foreach ($andares ?? [] as $andar)
                        <button @click="open=false; $wire.set('filtroAndar','{{ $andar }}')"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700 flex items-center justify-between cursor-pointer
                        {{ $filtroAndar === $andar ? 'bg-emerald-50 text-emerald-700 font-medium' : '' }}">
                            {{ $andar }}
                            @if ($filtroAndar === $andar)
                                <x-lucide-check class="w-3.5 h-3.5 text-emerald-500" />
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

            {{-- Filtro Status --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between text-sm border rounded-lg px-3 py-2 bg-white transition cursor-pointer
                {{ $filtroStatus ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        @if ($filtroStatus === 'disponivel')
                            <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                        @elseif($filtroStatus === 'ocupada')
                            <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>
                        @elseif($filtroStatus === 'reservada')
                            <span class="w-2 h-2 rounded-full bg-blue-400 flex-shrink-0"></span>
                        @elseif($filtroStatus === 'manutencao')
                            <span class="w-2 h-2 rounded-full bg-yellow-400 flex-shrink-0"></span>
                        @else
                            <x-lucide-circle class="w-3.5 h-3.5 flex-shrink-0" />
                        @endif
                        <span class="truncate text-xs font-medium">
                            {{ match ($filtroStatus) {
                                'disponivel' => 'Disponível',
                                'ocupada' => 'Ocupada',
                                'reservada' => 'Reservada',
                                'manutencao' => 'Manutenção',
                                default => 'Status',
                            } }}
                        </span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute mt-1 w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('filtroStatus','')"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2 cursor-pointer">
                        <x-lucide-circle class="w-3.5 h-3.5" /> Todos
                    </button>
                    <div class="border-t border-gray-100 my-1"></div>
                    <button @click="open=false; $wire.set('filtroStatus','disponivel')"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm cursor-pointer hover:bg-emerald-50 {{ $filtroStatus === 'disponivel' ? 'bg-emerald-50 font-medium text-emerald-700' : 'text-gray-700' }}">
                        <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Disponível
                        @if ($filtroStatus === 'disponivel')
                            <x-lucide-check class="w-3.5 h-3.5 ml-auto text-emerald-500" />
                        @endif
                    </button>
                    <button @click="open=false; $wire.set('filtroStatus','ocupada')"
                        class="w-full flex items-center gap-2 px-3 py-2 cursor-pointer text-sm hover:bg-red-50 {{ $filtroStatus === 'ocupada' ? 'bg-red-50 font-medium text-red-600' : 'text-gray-700' }}">
                        <span class="w-2 h-2 rounded-full bg-red-400"></span> Ocupada
                        @if ($filtroStatus === 'ocupada')
                            <x-lucide-check class="w-3.5 h-3.5 ml-auto text-red-500" />
                        @endif
                    </button>
                    <button @click="open=false; $wire.set('filtroStatus','reservada')"
                        class="w-full flex items-center gap-2 px-3 py-2 cursor-pointer text-sm hover:bg-blue-50 {{ $filtroStatus === 'reservada' ? 'bg-blue-50 font-medium text-blue-700' : 'text-gray-700' }}">
                        <span class="w-2 h-2 rounded-full bg-blue-400"></span> Reservada
                        @if ($filtroStatus === 'reservada')
                            <x-lucide-check class="w-3.5 h-3.5 ml-auto text-blue-500" />
                        @endif
                    </button>
                    <button @click="open=false; $wire.set('filtroStatus','manutencao')"
                        class="w-full flex items-center gap-2 px-3 py-2 text-sm cursor-pointer hover:bg-yellow-50 {{ $filtroStatus === 'manutencao' ? 'bg-yellow-50 font-medium text-yellow-700' : 'text-gray-700' }}">
                        <span class="w-2 h-2 rounded-full bg-yellow-400"></span> Manutenção
                        @if ($filtroStatus === 'manutencao')
                            <x-lucide-check class="w-3.5 h-3.5 ml-auto text-yellow-500" />
                        @endif
                    </button>
                </div>
            </div>

            {{-- Filtro Capacidade --}}
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="w-full flex items-center justify-between text-sm border rounded-lg px-3 py-2 bg-white transition cursor-pointer
                {{ $filtroCapacidade ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-200 text-gray-500' }}">
                    <div class="flex items-center gap-1.5 truncate">
                        <x-lucide-users class="w-3.5 h-3.5 flex-shrink-0" />
                        <span class="truncate text-xs font-medium">
                            {{ match ($filtroCapacidade) {
                                'pequena' => 'Até 6',
                                'media' => '7 a 15',
                                'grande' => '16+',
                                default => 'Tamanho',
                            } }}
                        </span>
                    </div>
                    <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                        x-bind:class="open ? 'rotate-180' : ''" />
                </button>
                <div x-show="open" @click.outside="open = false" x-transition
                    class="absolute right-0 mt-1 w-48 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-1">
                    <button @click="open=false; $wire.set('filtroCapacidade','')"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2 cursor-pointer">
                        <x-lucide-users class="w-3.5 h-3.5" /> Todos os tamanhos
                    </button>
                    <div class="border-t border-gray-100 my-1"></div>
                    @foreach ([['value' => 'pequena', 'label' => 'Pequena', 'desc' => 'Até 6 pessoas', 'icon' => '👤'], ['value' => 'media', 'label' => 'Média', 'desc' => '7 a 15 pessoas', 'icon' => '👥'], ['value' => 'grande', 'label' => 'Grande', 'desc' => '16+ pessoas', 'icon' => '🏢']] as $cap)
                        <button @click="open=false; $wire.set('filtroCapacidade','{{ $cap['value'] }}')"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 flex items-center justify-between cursor-pointer
                        {{ $filtroCapacidade === $cap['value'] ? 'bg-emerald-50 text-emerald-700 font-medium' : 'text-gray-700' }}">
                            <div>
                                <p class="text-xs font-medium">{{ $cap['label'] }}</p>
                                <p class="text-xs text-gray-400">{{ $cap['desc'] }}</p>
                            </div>
                            @if ($filtroCapacidade === $cap['value'])
                                <x-lucide-check class="w-3.5 h-3.5 text-emerald-500 flex-shrink-0" />
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>

        </div>

        {{-- Chips de filtros ativos + botão limpar tudo --}}
        @php
            $temFiltros = $busca || $filtroAndar || $filtroStatus || $filtroCapacidade;
        @endphp

        @if ($temFiltros)
            <div class="flex items-center gap-2 flex-wrap">
                <span class="text-xs text-gray-400">Filtros:</span>

                @if ($busca)
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                        <x-lucide-search class="w-3 h-3" />
                        "{{ Str::limit($busca, 20) }}"
                        <button wire:click="$set('busca', '')" class="ml-0.5 hover:text-slate-900 transition">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($filtroAndar)
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                        <x-lucide-building class="w-3 h-3" />
                        {{ $filtroAndar }}
                        <button wire:click="$set('filtroAndar', '')" class="ml-0.5 hover:text-emerald-900 transition">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($filtroStatus)
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                    {{ match ($filtroStatus) {
                        'disponivel' => 'bg-emerald-100 text-emerald-700',
                        'ocupada' => 'bg-red-100 text-red-600',
                        'reservada' => 'bg-blue-100 text-blue-700',
                        'manutencao' => 'bg-yellow-100 text-yellow-700',
                        default => 'bg-gray-100 text-gray-600',
                    } }}">
                        <span
                            class="w-1.5 h-1.5 rounded-full
                        {{ match ($filtroStatus) {
                            'disponivel' => 'bg-emerald-400',
                            'ocupada' => 'bg-red-400',
                            'reservada' => 'bg-blue-400',
                            'manutencao' => 'bg-yellow-400',
                            default => 'bg-gray-400',
                        } }}">
                        </span>
                        {{ match ($filtroStatus) {
                            'disponivel' => 'Disponível',
                            'ocupada' => 'Ocupada',
                            'reservada' => 'Reservada',
                            'manutencao' => 'Manutenção',
                            default => $filtroStatus,
                        } }}
                        <button wire:click="$set('filtroStatus', '')" class="ml-0.5 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                @if ($filtroCapacidade)
                    <span
                        class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 text-xs font-medium">
                        <x-lucide-users class="w-3 h-3" />
                        {{ match ($filtroCapacidade) {'pequena' => 'Até 6 pessoas','media' => '7 a 15 pessoas','grande' => '16+ pessoas',default => $filtroCapacidade} }}
                        <button wire:click="$set('filtroCapacidade', '')"
                            class="ml-0.5 hover:text-emerald-900 transition cursor-pointer">
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </span>
                @endif

                <button wire:click="limparFiltros"
                    class="ml-auto text-xs text-gray-400 hover:text-red-500 transition flex items-center gap-1 cursor-pointer">
                    <x-lucide-filter-x class="w-3.5 h-3.5" />
                    Limpar tudo
                </button>
            </div>
        @endif

    </div>


    <div class="bg-white rounded-xl overflow-hidden">



        {{-- ─── DESKTOP: Tabela (sm+) ────────────────────────────── --}}
        <div class="hidden sm:block overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="text-xs uppercase tracking-wider text-gray-400 border-b border-gray-100">
                        <th class="text-left px-5 py-3 font-semibold">Nome da Sala</th>
                        <th class="text-left px-5 py-3 font-semibold">Capacidade</th>
                        <th class="text-left px-5 py-3 font-semibold">Recursos</th>
                        <th class="text-left px-5 py-3 font-semibold">Status</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse ($salas as $sala)
                        @php
                            $s = match ($sala->status) {
                                'disponivel' => [
                                    'label' => 'Disponível',
                                    'dot' => 'bg-emerald-400',
                                    'bg' => 'bg-emerald-50',
                                    'text' => 'text-emerald-700',
                                ],
                                'ocupada' => [
                                    'label' => 'Ocupada',
                                    'dot' => 'bg-red-400',
                                    'bg' => 'bg-red-50',
                                    'text' => 'text-red-600',
                                ],
                                'reservada' => [
                                    'label' => 'Reservada',
                                    'dot' => 'bg-blue-400',
                                    'bg' => 'bg-blue-50',
                                    'text' => 'text-blue-700',
                                ],
                                'manutencao' => [
                                    'label' => 'Manutenção',
                                    'dot' => 'bg-yellow-400',
                                    'bg' => 'bg-yellow-50',
                                    'text' => 'text-yellow-700',
                                ],
                                default => [
                                    'label' => $sala->status,
                                    'dot' => 'bg-gray-400',
                                    'bg' => 'bg-gray-50',
                                    'text' => 'text-gray-600',
                                ],
                            };
                        @endphp
                        <tr class="hover:bg-gray-50 transition-colors">
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-9 h-9 rounded-xl bg-emerald-100 flex items-center justify-center flex-shrink-0">
                                        <x-lucide-building-2 class="w-5 h-5 text-emerald-500" />
                                    </div>
                                    <div>
                                        <p class="font-semibold text-gray-900">{{ $sala->name }}</p>
                                        <p class="text-xs text-gray-400">{{ $sala->location }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-4 text-gray-600">
                                <div class="flex items-center gap-1.5">
                                    <x-lucide-users class="w-4 h-4 text-gray-400" />
                                    {{ $sala->capacity }} pessoas
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <div class="flex items-center gap-2 text-gray-600">
                                    @if ($sala->has_tv)
                                        <div class="bg-[#F1F5F9] p-2 rounded-lg"><x-lucide-tv class="w-4 h-4" /></div>
                                    @endif
                                    @if ($sala->has_wifi)
                                        <div class="bg-[#F1F5F9] p-2 rounded-lg"><x-lucide-wifi class="w-4 h-4" />
                                        </div>
                                    @endif
                                    @if ($sala->has_video_conference)
                                        <div class="bg-[#F1F5F9] p-2 rounded-lg"><x-lucide-video class="w-4 h-4" />
                                        </div>
                                    @endif
                                    @if ($sala->has_projetor)
                                        <div class="bg-[#F1F5F9] p-2 rounded-lg"><x-lucide-projector
                                                class="w-4 h-4" />
                                        </div>
                                    @endif
                                    @if ($sala->has_coffee)
                                        <div class="bg-[#F1F5F9] p-2 rounded-lg"><x-lucide-coffee class="w-4 h-4" />
                                        </div>
                                    @endif
                                    @if ($sala->has_whiteboard)
                                        <div class="bg-[#F1F5F9] p-2 rounded-lg"><x-lucide-presentation
                                                class="w-4 h-4" />
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-5 py-4">
                                <span
                                    class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $s['bg'] }} {{ $s['text'] }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                                    {{ $s['label'] }}
                                </span>
                            </td>
                            <td class="px-5 py-4">
                                <div x-data="{ open: false }" class="relative">
                                    <button @click="open = !open"
                                        class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 cursor-pointer hover:text-gray-600 transition-colors">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                            <circle cx="5" cy="12" r="1.5" />
                                            <circle cx="12" cy="12" r="1.5" />
                                            <circle cx="19" cy="12" r="1.5" />
                                        </svg>
                                    </button>
                                    <div x-show="open" @click.outside="open = false" x-transition
                                        class="absolute right-0 mt-1 w-44 bg-white border border-gray-100 rounded-xl shadow-lg z-10 py-1">
                                        <button wire:click="verDetalhes({{ $sala->id }})" @click="open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 cursor-pointer hover:bg-gray-50 flex items-center gap-2">
                                            <x-lucide-eye class="w-4 h-4 text-gray-500" /> Ver detalhes
                                        </button>

                                        @if (auth()->user()?->isRhOuDp())
                                        <button wire:click="editar({{ $sala->id }})" @click="open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-gray-700 cursor-pointer hover:bg-gray-50 flex items-center gap-2">
                                            <x-lucide-pencil class="w-4 h-4 text-gray-500" /> Editar
                                        </button>
                                        <button
                                            wire:click="$dispatch('confirmarExclusao', { id: {{ $sala->id }} })"
                                            @click="open = false"
                                            class="w-full text-left px-4 py-2 text-sm text-red-500 cursor-pointer hover:bg-red-50 flex items-center gap-2">
                                            <x-lucide-trash-2 class="w-4 h-4" /> Excluir
                                        </button>
                                        @endif
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-5 py-12 text-center text-gray-400">
                                <x-lucide-search-x class="w-10 h-10 mx-auto mb-3 text-gray-300" />
                                Nenhuma sala encontrada.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ─── MOBILE: Cards idênticos ao padrão das imagens ──── --}}
        <div class="sm:hidden divide-y divide-gray-100">
            @forelse ($salas as $sala)
                @php
                    $s = match ($sala->status) {
                        'disponivel' => [
                            'label' => 'Disponível',
                            'dot' => 'bg-emerald-400',
                            'bg' => 'bg-emerald-50',
                            'text' => 'text-emerald-700',
                        ],
                        'ocupada' => [
                            'label' => 'Ocupada',
                            'dot' => 'bg-red-400',
                            'bg' => 'bg-red-50',
                            'text' => 'text-red-600',
                        ],
                        'manutencao' => [
                            'label' => 'Manutenção',
                            'dot' => 'bg-yellow-400',
                            'bg' => 'bg-yellow-50',
                            'text' => 'text-yellow-700',
                        ],
                        default => [
                            'label' => $sala->status,
                            'dot' => 'bg-gray-400',
                            'bg' => 'bg-gray-50',
                            'text' => 'text-gray-600',
                        ],
                    };

                    $words = explode(' ', $sala->name);
                    $initials = strtoupper(substr($words[0], 0, 1) . (isset($words[1]) ? substr($words[1], 0, 1) : ''));
                @endphp

                <div class="flex items-center gap-3 px-4 py-4 hover:bg-gray-50 transition-colors">

                    {{-- Avatar com iniciais (igual ao "AD" das imagens) --}}
                    <div
                        class="w-11 h-11 rounded-full bg-emerald-100 flex items-center justify-center flex-shrink-0 self-start mt-0.5">
                        <span class="text-sm font-bold text-emerald-600 leading-none">{{ $initials }}</span>
                    </div>

                    {{-- Bloco central --}}
                    <div class="flex-1 min-w-0">

                        {{-- Nome --}}
                        <p class="font-semibold text-gray-900 text-sm leading-tight">{{ $sala->name }}</p>

                        {{-- Andar (igual ao email nas imagens) --}}
                        <div class="flex items-center gap-1 text-xs text-gray-400 mt-0.5">
                            <x-lucide-building class="w-3 h-3 flex-shrink-0" />
                            <span>{{ $sala->location }}</span>
                        </div>

                        {{-- Capacidade (igual ao departamento nas imagens) --}}
                        <div class="flex items-center gap-1 text-xs text-gray-400 mt-0.5">
                            <x-lucide-users class="w-3 h-3 flex-shrink-0" />
                            <span>{{ $sala->capacity }} pessoas</span>
                        </div>

                        {{-- Badges: recursos (pill cinza) + status (igual role + status das imagens) --}}
                        <div class="flex items-start gap-2 mt-2 flex-wrap">

                            {{-- Recursos: máximo 3 por linha --}}
                            @php
                                $recursos = collect([
                                    $sala->has_tv ? ['icon' => 'lucide-tv', 'label' => 'TV'] : null,
                                    $sala->has_wifi ? ['icon' => 'lucide-wifi', 'label' => 'Wi-Fi'] : null,
                                    $sala->has_video_conference ? ['icon' => 'lucide-video', 'label' => 'Vídeo'] : null,
                                    $sala->has_projector ? ['icon' => 'lucide-projector', 'label' => 'Proj.'] : null,
                                    $sala->has_coffee ? ['icon' => 'lucide-coffee', 'label' => 'Café'] : null,
                                    $sala->has_whiteboard
                                        ? ['icon' => 'lucide-presentation', 'label' => 'Quadro']
                                        : null,
                                ])
                                    ->filter()
                                    ->values();

                                $chunks = $recursos->chunk(3);
                            @endphp

                            <div class="flex flex-col gap-1">
                                @foreach ($chunks as $grupo)
                                    <div class="flex items-center gap-1">
                                        @foreach ($grupo as $recurso)
                                            <span
                                                class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md bg-gray-100 text-gray-600 text-xs font-medium">
                                                <x-dynamic-component :component="$recurso['icon']" class="w-3 h-3" />
                                                {{ $recurso['label'] }}
                                            </span>
                                        @endforeach
                                    </div>
                                @endforeach
                            </div>

                            {{-- Status badge --}}
                            <span
                                class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $s['bg'] }} {{ $s['text'] }}">
                                <span class="w-1.5 h-1.5 rounded-full {{ $s['dot'] }}"></span>
                                {{ $s['label'] }}
                            </span>

                        </div>

                    </div>

                    {{-- Menu ... (igual nas imagens) --}}
                    <div x-data="{ open: false }" class="relative flex-shrink-0 self-start mt-1">
                        <button @click="open = !open"
                            class="p-1.5 rounded-lg hover:bg-gray-100 text-gray-400 cursor-pointer hover:text-gray-600 transition-colors">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                <circle cx="5" cy="12" r="1.5" />
                                <circle cx="12" cy="12" r="1.5" />
                                <circle cx="19" cy="12" r="1.5" />
                            </svg>
                        </button>

                        {{-- Dropdown idêntico ao das imagens: Ver perfil / Editar / Desativar --}}
                        <div x-show="open" @click.outside="open = false" x-transition
                            class="absolute right-0 mt-1 w-44 bg-white border border-gray-100 rounded-xl shadow-lg z-20 py-1">
                            <button wire:click="verDetalhes({{ $sala->id }})" @click="open = false"
                                class="w-full text-left px-4 py-2 text-sm text-gray-700 cursor-pointer hover:bg-gray-50 flex items-center gap-2">
                                <x-lucide-eye class="w-4 h-4 text-gray-500" />
                                Ver detalhes
                            </button>

                            @if (auth()->user()?->isRhOuDp())
                                <button wire:click="editar({{ $sala->id }})" @click="open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-gray-700 cursor-pointer hover:bg-gray-50 flex items-center gap-2">
                                    <x-lucide-pencil class="w-4 h-4 text-gray-500" />
                                    Editar
                                </button>
                                <button wire:click="$dispatch('confirmarExclusao', { id: {{ $sala->id }} })"
                                    @click="open = false"
                                    class="w-full text-left px-4 py-2 text-sm text-red-500 cursor-pointer hover:bg-red-50 flex items-center gap-2">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                    Excluir
                                </button>
                            @endif
                        </div>
                    </div>

                </div>
            @empty
                <div class="px-5 py-12 text-center text-gray-400">
                    <x-lucide-search-x class="w-10 h-10 mx-auto mb-3 text-gray-300" />
                    Nenhuma sala encontrada.
                </div>
            @endforelse
        </div>

        {{-- ─── Paginação ────────────────────────────────────────── --}}
        <div class="border-t border-gray-100 px-4">
            {{ $salas->links('partials.pagination') }}
        </div>

    </div>
</div>
