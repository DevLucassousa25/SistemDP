<div>
    <div class="max-w-7xl mx-auto p-4 lg:px-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">

            {{-- Esquerda --}}
            <div class="flex items-start sm:items-center gap-3">
                <a href="{{ route('rooms') }}"
                    class="p-2 text-gray-400 hover:text-gray-600 hover:bg-white rounded-lg transition border border-transparent hover:border-gray-200">
                    <x-lucide-arrow-left class="w-4 h-4" />
                </a>

                <div>
                    <h1 class="text-lg sm:text-xl font-bold text-gray-900">Detalhes da Sala</h1>
                    <p class="text-xs sm:text-sm text-gray-400">
                        Informações, disponibilidade e reservas
                    </p>
                </div>
            </div>

            {{-- Botões --}}

            @if (auth()->user()?->isRhOuDp())
                <div class="flex flex-wrap items-center gap-2">

                    <button wire:click="$dispatch('abrir-modal-edicao', { salaId: {{ $sala->id }} })"
                        class="flex-1 sm:flex-none inline-flex justify-center cursor-pointer items-center gap-2 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200 rounded-xl hover:border-gray-300 hover:bg-gray-50 transition">
                        <x-lucide-pencil class="w-4 h-4" />
                        Editar
                    </button>

                    <button wire:click="$dispatch('confirmarExclusao', { id: {{ $sala->id }} })"
                        class="flex-1 sm:flex-none inline-flex justify-center cursor-pointer items-center gap-2 px-4 py-2.5 text-sm font-medium text-red-600 bg-white border border-red-200 rounded-xl hover:bg-red-50 hover:border-red-300 transition">
                        <x-lucide-trash-2 class="w-4 h-4" />
                        Excluir
                    </button>
                </div>
            @endif
        </div>

        {{-- Hero Image --}}
        <div class="relative rounded-2xl overflow-hidden h-72 bg-gray-800 mb-4">
            @if ($sala->images->isNotEmpty())
                <img src="{{ asset('storage/' . $sala->images->first()->path) }}"
                    alt="Imagem da sala {{ $sala->name }}" class="w-full h-full object-cover" />
            @else
                <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-gray-700 to-gray-900">
                    <x-lucide-image class="w-16 h-16 text-gray-500" />
                </div>
            @endif

            {{-- Overlay gradiente --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/70 via-black/10 to-transparent"></div>

            {{-- Badge status --}}
            <div class="absolute top-4 right-4">
                <span
                    class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold bg-white shadow
                    {{ match ($sala->status) {
                        'disponivel' => 'text-emerald-600',
                        'ocupada'    => 'text-red-600',
                        'reservada'  => 'text-blue-600',
                        'manutencao' => 'text-yellow-600',
                        default      => 'text-gray-600',
                    } }}">
                    <span
                        class="w-1.5 h-1.5 rounded-full
                        {{ match ($sala->status) {
                            'disponivel' => 'bg-emerald-500',
                            'ocupada'    => 'bg-red-500',
                            'reservada'  => 'bg-blue-500',
                            'manutencao' => 'bg-yellow-500',
                            default      => 'bg-gray-500',
                        } }}">
                    </span>
                    {{ match ($sala->status) {
                        'disponivel' => 'Disponível',
                        'ocupada'    => 'Ocupada',
                        'reservada'  => 'Reservada',
                        'manutencao' => 'Manutenção',
                        default      => $sala->status,
                    } }}
                </span>
            </div>

            {{-- Nome e localização --}}
            <div class="absolute bottom-5 left-6">
                <h2 class="text-2xl font-bold text-white">{{ $sala->name }}</h2>
                <p class="text-sm text-white/70 mt-0.5">{{ $sala->location }}</p>
            </div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3 mb-6">

            <div class="bg-white rounded-xl border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <x-lucide-users class="w-4 h-4 text-emerald-500" />
                </div>
                <div>
                    <p class="text-xs text-gray-400">Capacidade</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $sala->capacity }} pessoas</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <x-lucide-map-pin class="w-4 h-4 text-emerald-500" />
                </div>
                <div>
                    <p class="text-xs text-gray-400">Localização</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $sala->location }}</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div class="p-2 bg-emerald-50 rounded-lg">
                    <x-lucide-calendar class="w-4 h-4 text-emerald-500" />
                </div>
                <div>
                    <p class="text-xs text-gray-400">Reservas hoje</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $reservasHoje }} reuniões</p>
                </div>
            </div>

            <div class="bg-white rounded-xl border border-gray-100 px-4 py-3.5 flex items-center gap-3">
                <div
                    class="p-2 {{ str_contains($sala->proximoHorarioLivre(), 'Ocupada') ? 'bg-yellow-50' : 'bg-emerald-50' }} rounded-lg">

                    <x-lucide-clock
                        class="w-4 h-4 {{ str_contains($sala->proximoHorarioLivre(), 'Ocupada') ? 'text-yellow-500' : 'text-emerald-500' }}" />

                </div>
                <div>
                    <p class="text-xs text-gray-400">Disponível</p>
                    <p class="text-sm font-semibold text-gray-800">{{ $proximoHorario ?? 'Livre agora' }}</p>
                </div>
            </div>

        </div>

        {{-- Tabs --}}
        <div x-data="{ tab: 'visao' }" class="flex flex-col gap-6">
            <div class="flex gap-1 border-b border-gray-200">
                @foreach ([
        ['key' => 'visao', 'label' => 'Visão Geral'],
        [
            'key' => 'hoje',
            'label' => 'Reservas de
                Hoje',
        ],
        ['key' => 'calendario', 'label' => 'Calendário'],
        ['key' => 'historico', 'label' => 'Histórico'],
    ] as $t)
                    <button @click="tab = '{{ $t['key'] }}'"
                        class="px-4 py-2.5 text-sm font-medium transition border-b-2 -mb-px cursor-pointer"
                        :class="tab === '{{ $t['key'] }}' ? 'border-gray-900 text-gray-900' :
                            'border-transparent text-gray-400 hover:text-gray-600'">
                        {{ $t['label'] }}
                    </button>
                @endforeach
            </div>

            {{-- Tab: Visão Geral --}}
            <div x-show="tab === 'visao'" class="grid grid-cols-1 md:grid-cols-3 gap-4">

                {{-- Coluna esquerda --}}
                <div class="md:col-span-2 flex flex-col gap-4">

                    {{-- Descrição --}}
                    <div class="bg-white rounded-xl border border-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-2">Descrição</h3>
                        <p class="text-sm text-gray-500 leading-relaxed">
                            {{ $sala->description ?? 'Nenhuma descrição cadastrada.' }}
                        </p>
                    </div>

                    {{-- Recursos --}}
                    <div class="bg-white rounded-xl border border-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-4">Recursos Disponíveis</h3>

                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-3">
                            @php
                                $recursos = [
                                    ['key' => 'has_tv', 'label' => 'TV', 'icon' => 'tv-2'],
                                    ['key' => 'has_wifi', 'label' => 'Wi-Fi', 'icon' => 'wifi'],
                                    [
                                        'key' => 'has_video_conference',
                                        'label' => 'Videoconferência',
                                        'icon' => 'monitor',
                                    ],
                                    ['key' => 'has_projector', 'label' => 'Projetor', 'icon' => 'projector'],
                                    ['key' => 'has_coffee', 'label' => 'Café', 'icon' => 'coffee'],
                                    ['key' => 'has_whiteboard', 'label' => 'Quadro Branco', 'icon' => 'presentation'],
                                ];
                            @endphp

                            @foreach ($recursos as $recurso)
                                @if ($sala->{$recurso['key']})
                                    <div
                                        class="flex flex-col items-center gap-2 px-4 py-3 bg-gray-50 rounded-xl border border-gray-100">
                                        <div class="p-2.5 bg-emerald-100 rounded-lg">
                                            <x-dynamic-component :component="'lucide-' . $recurso['icon']" class="w-5 h-5 text-emerald-600" />
                                        </div>
                                        <span class="text-xs text-gray-600 font-medium text-center">
                                            {{ $recurso['label'] }}
                                        </span>
                                    </div>
                                @endif
                            @endforeach

                            @if (!collect($recursos)->contains(fn($r) => $sala->{$r['key']}))
                                <p class="text-sm text-gray-400 col-span-full">Nenhum recurso cadastrado.</p>
                            @endif
                        </div>
                    </div>

                    {{-- Galeria --}}
                    @if ($sala->images->count() > 1)
                        <div class="bg-white rounded-xl border border-gray-100 p-5">
                            <h3 class="text-sm font-semibold text-gray-800 mb-3">Fotos da Sala</h3>

                            <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                @foreach ($sala->images->skip(1) as $image)
                                    <div class="aspect-video rounded-lg overflow-hidden bg-gray-100">
                                        <img src="{{ asset('storage/' . $image->path) }}"
                                            alt="Imagem da sala {{ $sala->name }}"
                                            class="w-full h-full object-cover" />
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>

                {{-- Coluna direita --}}
                <div class="flex flex-col gap-4">

                    {{-- Responsável --}}
                    <div class="bg-white rounded-xl border border-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-3">Responsável</h3>

                        @if ($sala->responsavel)
                            <div class="flex items-center gap-3">
                                <img src="{{ $sala->responsavel->profile_photo_url }}"
                                    class="w-10 h-10 rounded-full object-cover" />
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">
                                        {{ $sala->responsavel->name }}
                                    </p>
                                    <p class="text-xs text-gray-400">
                                        {{ $sala->responsavel->department }}
                                    </p>
                                </div>
                            </div>
                        @else
                            <p class="text-sm text-gray-400">Não atribuído.</p>
                        @endif
                    </div>

                    {{-- Card: manutenção ativa --}}
                    @if ($sala->status === 'manutencao' && $sala->maintenance_start && $sala->maintenance_end)
                        <div class="bg-yellow-50 rounded-xl border border-yellow-200 p-5">
                            <div class="flex items-center gap-2 mb-3">
                                <x-lucide-triangle-alert class="w-4 h-4 text-yellow-600" />
                                <h3 class="text-sm font-semibold text-yellow-800">Manutenção em andamento</h3>
                            </div>
                            <div class="flex flex-col gap-1.5 text-xs text-yellow-700">
                                <div class="flex items-center gap-2">
                                    <x-lucide-calendar class="w-3.5 h-3.5 shrink-0" />
                                    <span>
                                        Início:
                                        <strong>{{ $sala->maintenance_start->format('d/m/Y H:i') }}</strong>
                                    </span>
                                </div>
                                <div class="flex items-center gap-2">
                                    <x-lucide-calendar-check class="w-3.5 h-3.5 shrink-0" />
                                    <span>
                                        Término:
                                        <strong>{{ $sala->maintenance_end->format('d/m/Y H:i') }}</strong>
                                    </span>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- Alterar Status --}}
                    <div class="bg-white rounded-xl border border-gray-100 p-5">
                        <h3 class="text-sm font-semibold text-gray-800 mb-1">Status</h3>
                        <p class="text-xs text-gray-400 mb-3">Gerenciado automaticamente pelo sistema</p>

                        <div class="flex flex-col gap-2">

                            {{-- Disponível — indicador apenas --}}
                            @php $ativo = $sala->status === 'disponivel'; @endphp
                            <div
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl border text-sm font-medium
                                {{ $ativo ? 'border-emerald-400 bg-emerald-50 text-emerald-700' : 'border-gray-100 bg-gray-50 text-gray-400' }}">
                                <div class="flex items-center gap-2.5">
                                    <x-lucide-circle-check-big
                                        class="w-4 h-4 {{ $ativo ? 'text-emerald-500' : 'text-gray-300' }}" />
                                    Disponível
                                </div>
                                @if ($ativo)
                                    <span
                                        class="text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-500 text-white">Atual</span>
                                @endif
                            </div>

                            {{-- Ocupada — indicador apenas --}}
                            @php $ativo = $sala->status === 'ocupada'; @endphp
                            <div
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl border text-sm font-medium
                                {{ $ativo ? 'border-red-400 bg-red-50 text-red-700' : 'border-gray-100 bg-gray-50 text-gray-400' }}">
                                <div class="flex items-center gap-2.5">
                                    <x-lucide-circle-x
                                        class="w-4 h-4 {{ $ativo ? 'text-red-500' : 'text-gray-300' }}" />
                                    Ocupada
                                </div>
                                @if ($ativo)
                                    <span
                                        class="text-xs font-semibold px-2 py-0.5 rounded-full bg-red-500 text-white">Atual</span>
                                @endif
                            </div>

                            {{-- Reservada — indicador apenas (reservas futuras) --}}
                            @php $ativo = $sala->status === 'reservada'; @endphp
                            <div
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl border text-sm font-medium
                                {{ $ativo ? 'border-blue-400 bg-blue-50 text-blue-700' : 'border-gray-100 bg-gray-50 text-gray-400' }}">
                                <div class="flex items-center gap-2.5">
                                    <x-lucide-calendar-clock
                                        class="w-4 h-4 {{ $ativo ? 'text-blue-500' : 'text-gray-300' }}" />
                                    Reservada
                                </div>
                                @if ($ativo)
                                    <span
                                        class="text-xs font-semibold px-2 py-0.5 rounded-full bg-blue-500 text-white">Atual</span>
                                @endif
                            </div>

                            {{-- Manutenção — único botão clicável --}}
                            @php
                                $ativo = $sala->status === 'manutencao';
                                $semPermissao = !auth()->user()?->isRhOuDp();
                            @endphp

                            <button wire:click="alterarStatus('manutencao')"
                                @if ($ativo || $semPermissao) disabled @endif
                                class="w-full flex items-center justify-between px-4 py-3 rounded-xl border text-sm font-medium transition
        {{ $semPermissao
            ? 'border-gray-200 bg-gray-50 text-gray-400 cursor-not-allowed'
            : ($ativo
                ? 'border-yellow-400 bg-yellow-50 text-yellow-700 cursor-default'
                : 'border-gray-200 text-gray-600 hover:border-yellow-300 hover:bg-yellow-50 hover:text-yellow-700 cursor-pointer') }}">

                                <div class="flex items-center gap-2.5">
                                    <x-lucide-triangle-alert
                                        class="w-4 h-4
            {{ $semPermissao ? 'text-gray-300' : ($ativo ? 'text-yellow-500' : 'text-gray-400') }}" />
                                    Manutenção
                                </div>

                                @if ($ativo && !$semPermissao)
                                    <span
                                        class="text-xs font-semibold px-2 py-0.5 rounded-full bg-yellow-500 text-white">
                                        Atual
                                    </span>
                                @else
                                    <x-lucide-chevron-right class="w-3.5 h-3.5 text-gray-300" />
                                @endif

                            </button>
                        </div>
                    </div>

                </div>

            </div>

            {{-- Tab: Reservas de Hoje --}}
            <div x-show="tab === 'hoje'" x-cloak class="flex flex-col gap-3 max-h-[420px] overflow-y-auto pr-1">

                <div class="flex items-center justify-between">
                    <p class="text-sm text-gray-500">{{ $reservasHojeList->count() }} reserva(s) para hoje</p>

                    @if ($sala->status === 'manutencao')
                        <div
                            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-medium text-yellow-600 bg-yellow-50 border border-yellow-200 rounded-xl cursor-not-allowed select-none">
                            <x-lucide-triangle-alert class="w-4 h-4" />
                            Em manutenção
                        </div>
                    @else
                        <button wire:click="abrirModalReserva"
                            class="inline-flex items-center gap-2 cursor-pointer px-4 py-2 text-sm font-medium text-white bg-emerald-500 hover:bg-emerald-600 rounded-xl transition">
                            <x-lucide-plus class="w-4 h-4" />
                            Nova Reserva
                        </button>
                    @endif
                </div>

                @forelse ($reservasHojeList as $reserva)
                    @if ($reserva->is_maintenance)
                        {{-- Reserva de manutenção --}}
                        <div
                            class="bg-yellow-50 rounded-xl border border-yellow-200 px-5 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="p-2.5 bg-yellow-100 rounded-lg">
                                    <x-lucide-triangle-alert class="w-4 h-4 text-yellow-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-yellow-800">Manutenção Programada</p>
                                    <p class="text-xs text-yellow-600 mt-0.5">
                                        {{ $reserva->horario }} · Sala indisponível para reservas
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-yellow-200 text-yellow-800">
                                Manutenção
                            </span>
                        </div>
                    @else
                        {{-- Reserva normal --}}
                        <div
                            class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="p-2.5 bg-emerald-50 rounded-lg">
                                    <x-lucide-calendar-check class="w-4 h-4 text-emerald-500" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $reserva->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $reserva->horario }} · {{ $reserva->user->name }} ·
                                        {{ $reserva->attendees_count }} participante(s)
                                    </p>
                                </div>
                            </div>

                            @if (auth()->id() === $reserva->user_id || auth()->user()->isAdmin())
                                @if ($reserva->end_time->isFuture() || $reserva->start_time->isFuture())
                                    <button wire:click="confirmarCancelamento({{ $reserva->id }})"
                                        class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-lg transition cursor-pointer"
                                        title="Cancelar reserva">
                                        <x-lucide-x class="w-4 h-4" />
                                    </button>
                                @else
                                    <span class="text-xs text-gray-400 px-2 py-1 rounded-lg bg-gray-50"
                                        title="Reserva já realizada">
                                        Concluída
                                    </span>
                                @endif
                            @else
                                <span class="text-xs text-gray-300 px-2"
                                    title="Apenas o responsável ou um administrador pode cancelar">
                                    <x-lucide-lock class="w-3.5 h-3.5" />
                                </span>
                            @endif
                        </div>
                    @endif
                @empty
                    <div
                        class="bg-white rounded-xl border border-gray-100 p-8 flex flex-col items-center gap-2 text-center">
                        <x-lucide-calendar-x class="w-8 h-8 text-gray-300" />
                        <p class="text-sm font-medium text-gray-500">Nenhuma reserva para hoje</p>
                        <p class="text-xs text-gray-400">Clique em "Nova Reserva" para agendar</p>
                    </div>
                @endforelse

            </div>

            {{-- Tab: Calendário --}}
            <div x-show="tab === 'calendario'" x-cloak class="flex flex-col gap-3">
                @livewire('components.ui.calendar.reservetion-room', ['id' => $sala->id], key($sala->id))

                {{-- <div
                    class="bg-white rounded-xl border border-gray-100 p-8 flex flex-col items-center gap-2 text-center">
                    <x-lucide-calendar class="w-8 h-8 text-gray-300" />
                    <p class="text-sm font-medium text-gray-500">Nenhum calendario disponível</p>
                </div> --}}
            </div>

            {{-- Tab: historico --}}
            <div x-show="tab === 'historico'" x-cloak class="flex flex-col gap-3 max-h-[420px] overflow-y-auto pr-1">

                @forelse ($historico as $reserva)
                    @if ($reserva->is_maintenance)
                        {{-- Histórico de manutenção --}}
                        <div
                            class="bg-yellow-50 rounded-xl border border-yellow-200 px-5 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="p-2.5 bg-yellow-100 rounded-lg">
                                    <x-lucide-triangle-alert class="w-4 h-4 text-yellow-600" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-yellow-800">Manutenção Programada</p>
                                    <p class="text-xs text-yellow-600 mt-0.5">
                                        {{ $reserva->start_time->format('d/m/Y') }} · {{ $reserva->horario }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-xs font-medium px-2.5 py-1 rounded-full
                                {{ $reserva->status === 'cancelada' ? 'bg-red-100 text-red-600' : 'bg-yellow-200 text-yellow-800' }}">
                                {{ $reserva->status === 'cancelada' ? 'Cancelada' : 'Manutenção' }}
                            </span>
                        </div>
                    @else
                        {{-- Histórico normal --}}
                        <div
                            class="bg-white rounded-xl border border-gray-100 px-5 py-4 flex items-center justify-between">
                            <div class="flex items-center gap-4">
                                <div class="p-2.5 bg-gray-50 rounded-lg">
                                    <x-lucide-history class="w-4 h-4 text-gray-400" />
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-gray-800">{{ $reserva->title }}</p>
                                    <p class="text-xs text-gray-400 mt-0.5">
                                        {{ $reserva->start_time->format('d/m/Y') }} · {{ $reserva->horario }} ·
                                        {{ $reserva->user->name }}
                                    </p>
                                </div>
                            </div>
                            <span
                                class="text-xs font-medium px-2.5 py-1 rounded-full
                                {{ match ($reserva->status) {
                                    'confirmada' => 'bg-emerald-100 text-emerald-700',
                                    'cancelada' => 'bg-red-100 text-red-600',
                                    'concluida' => 'bg-gray-100 text-gray-600',
                                } }}">
                                {{ ucfirst($reserva->status) }}
                            </span>
                        </div>
                    @endif
                @empty
                    <div
                        class="bg-white rounded-xl border border-gray-100 p-8 flex flex-col items-center gap-2 text-center">
                        <x-lucide-inbox class="w-8 h-8 text-gray-300" />
                        <p class="text-sm font-medium text-gray-500">Nenhum histórico disponível</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>

    <!-- Modal de Nova Reserva -->

    @if ($modalReserva)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="fecharModalReserva" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col z-10">
                <div class="flex items-start justify-between p-6 pb-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Nova Reserva</h2>
                        <p class="text-sm text-gray-400 mt-0.5">{{ $sala->name }}</p>
                    </div>
                    <button wire:click="fecharModalReserva"
                        class="text-gray-400 hover:text-gray-600 transition crursor-pointer">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
                <div class="p-6 flex flex-col gap-4">
                    <div> <label class="block text-sm font-semibold text-gray-700 mb-1.5">Título da Reunião</label>
                        <input type="text" wire:model="titulo" placeholder="Ex: Reunião de planejamento"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent placeholder-gray-300 @error('titulo') border-red-400 @enderror" />
                        @error('titulo')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div> <label class="block text-sm font-semibold text-gray-700 mb-1.5">Data</label> <input
                            type="date" wire:model="dataReserva"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent @error('dataReserva') border-red-400 @enderror" />
                        @error('dataReserva')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div> <label class="block text-sm font-semibold text-gray-700 mb-1.5">Início</label> <input
                                type="time" wire:model="horaInicio"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent @error('horaInicio') border-red-400 @enderror" />
                            @error('horaInicio')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div> <label class="block text-sm font-semibold text-gray-700 mb-1.5">Término</label> <input
                                type="time" wire:model="horaFim"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent @error('horaFim') border-red-400 @enderror" />
                            @error('horaFim')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    <div> <label class="block text-sm font-semibold text-gray-700 mb-1.5"> Participantes <span
                                class="font-normal text-gray-400">(máx. {{ $sala->capacity }})</span> </label> <input
                            type="number" wire:model="participantes" min="1" max="{{ $sala->capacity }}"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent @error('participantes') border-red-400 @enderror" />
                        @error('participantes')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                    <div> <label class="block text-sm font-semibold text-gray-700 mb-1.5">Descrição <span
                                class="font-normal text-gray-400">(opcional)</span></label>
                        <textarea wire:model="descricao" rows="2" placeholder="Detalhes da reunião..."
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent placeholder-gray-300 resize-none"></textarea>
                    </div>
                </div>
                <div
                    class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <button wire:click="fecharModalReserva"
                        class="px-5 py-2.5 text-sm cursor-pointer font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition bg-white">
                        Cancelar </button>

                    <button wire:click="salvarReserva" wire:loading.attr="disabled"
                        class="px-5 py-2.5 text-sm font-medium cursor-pointer text-white bg-emerald-500 hover:bg-emerald-600 rounded-xl transition flex items-center gap-2 disabled:opacity-60">
                        <span wire:loading.remove wire:target="salvarReserva">Confirmar Reserva</span> <span
                            wire:loading wire:target="salvarReserva" class="flex items-center gap-2">
                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- Modal Cancelar Reserva --}}
    @if ($modalCancelar && !empty($cancelarReservaDados))
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="fecharModalCancelar" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col z-10">

                {{-- Header --}}
                <div class="flex items-start justify-between p-6 pb-4 border-b border-gray-100">
                    <div class="flex items-center gap-3">
                        <div class="p-2 bg-red-100 rounded-xl">
                            <x-lucide-calendar-x class="w-5 h-5 text-red-600" />
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-gray-900">Cancelar Reserva</h2>
                            <p class="text-xs text-gray-400 mt-0.5">Esta ação não poderá ser desfeita</p>
                        </div>
                    </div>
                    <button wire:click="fecharModalCancelar"
                        class="text-gray-400 hover:text-gray-600 transition cursor-pointer mt-0.5">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                {{-- Detalhes da reserva --}}
                <div class="p-6 flex flex-col gap-4">

                    <p class="text-sm text-gray-600">Você está prestes a cancelar a seguinte reserva:</p>

                    <div class="bg-gray-50 rounded-xl border border-gray-200 p-4 flex flex-col gap-3">
                        <p class="text-sm font-semibold text-gray-800">{{ $cancelarReservaDados['titulo'] }}</p>
                        <div class="flex flex-col gap-1.5">
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <x-lucide-calendar class="w-3.5 h-3.5 shrink-0 text-gray-400" />
                                {{ $cancelarReservaDados['data'] }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <x-lucide-clock class="w-3.5 h-3.5 shrink-0 text-gray-400" />
                                {{ $cancelarReservaDados['horario'] }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <x-lucide-user class="w-3.5 h-3.5 shrink-0 text-gray-400" />
                                {{ $cancelarReservaDados['responsavel'] }}
                            </div>
                            <div class="flex items-center gap-2 text-xs text-gray-500">
                                <x-lucide-users class="w-3.5 h-3.5 shrink-0 text-gray-400" />
                                {{ $cancelarReservaDados['participantes'] }} participante(s)
                            </div>
                        </div>
                    </div>

                    <div class="flex items-start gap-2.5 p-3.5 bg-red-50 border border-red-100 rounded-xl">
                        <x-lucide-triangle-alert class="w-4 h-4 text-red-500 shrink-0 mt-0.5" />
                        <p class="text-xs text-red-600 leading-relaxed">
                            Ao confirmar, a reserva será cancelada e a sala ficará disponível para outros agendamentos
                            neste horário.
                        </p>
                    </div>

                </div>

                {{-- Footer --}}
                <div
                    class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <button wire:click="fecharModalCancelar"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-100 transition bg-white cursor-pointer">
                        Voltar
                    </button>
                    <button wire:click="cancelarReserva" wire:loading.attr="disabled"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-red-500 hover:bg-red-600 rounded-xl transition flex items-center gap-2 disabled:opacity-60 cursor-pointer">
                        <span wire:loading.remove wire:target="cancelarReserva">Confirmar Cancelamento</span>
                        <span wire:loading wire:target="cancelarReserva">
                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    {{-- Modal Manutenção --}}
    @if ($modalManutencao)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="fecharModalManutencao" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col z-10">

                {{-- Header --}}
                <div class="flex items-start justify-between p-6 pb-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Definir Manutenção</h2>
                        <p class="text-sm text-gray-400 mt-0.5">Informe o período em que a sala ficará indisponível</p>
                    </div>
                    <button wire:click="fecharModalManutencao"
                        class="text-gray-400 hover:text-gray-600 transition cursor-pointer mt-0.5">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-6 flex flex-col gap-5">

                    {{-- Aviso --}}
                    <div class="flex items-start gap-3 p-4 bg-yellow-50 border border-yellow-200 rounded-xl">
                        <x-lucide-triangle-alert class="w-4 h-4 text-yellow-600 shrink-0 mt-0.5" />
                        <p class="text-xs text-yellow-700 leading-relaxed">
                            Durante este período a sala ficará <strong>indisponível para reservas</strong>.
                            Certifique-se de que não há agendamentos futuros antes de confirmar.
                        </p>
                    </div>

                    {{-- Início --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Início da Manutenção
                        </label>
                        <input type="datetime-local" wire:model="manutencaoInicio"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent
                                   @error('manutencaoInicio') border-red-400 @enderror" />
                        @error('manutencaoInicio')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Fim --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            Término da Manutenção
                        </label>
                        <input type="datetime-local" wire:model="manutencaoFim"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                   focus:outline-none focus:ring-2 focus:ring-yellow-400 focus:border-transparent
                                   @error('manutencaoFim') border-red-400 @enderror" />
                        @error('manutencaoFim')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                </div>

                {{-- Footer --}}
                <div
                    class="flex items-center justify-end gap-3 px-6 py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <button wire:click="fecharModalManutencao"
                        class="px-5 py-2.5 text-sm font-medium text-gray-600 border border-gray-200 rounded-xl hover:bg-gray-50 transition bg-white cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="definirManutencao" wire:loading.attr="disabled"
                        class="px-5 py-2.5 text-sm font-medium text-white bg-yellow-500 hover:bg-yellow-600 rounded-xl transition flex items-center gap-2 disabled:opacity-60 cursor-pointer">
                        <span wire:loading.remove wire:target="definirManutencao">Confirmar Manutenção</span>
                        <span wire:loading wire:target="definirManutencao">
                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        </span>
                    </button>
                </div>

            </div>
        </div>
    @endif

    @livewire('components.ui.modal.room-create-modal')
    @livewire('components.ui.modal.alert-modal')
    @livewire('components.ui.modal.delete-room-modal')

</div>
                                                
