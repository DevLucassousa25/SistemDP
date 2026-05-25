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
    <div x-data
         x-effect="document.body.style.overflow = $wire.modalReserva ? 'hidden' : ''">

        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4 transition-all duration-300
                    {{ $modalReserva ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none' }}">

            {{-- Backdrop --}}
            <div wire:click="fecharModalReserva"
                 class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm"></div>

            {{-- Modal Box — bottom-sheet em mobile, centralizado no desktop --}}
            <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg max-h-[95vh] sm:max-h-[92vh]
                        flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden
                        transform transition-all duration-300
                        {{ $modalReserva ? 'translate-y-0 opacity-100 scale-100' : 'translate-y-4 opacity-0 scale-95' }}">

                {{-- Alça de arraste (mobile) --}}
                <div class="flex justify-center pt-2.5 pb-1 sm:hidden flex-shrink-0">
                    <div class="w-10 h-1 bg-slate-200 dark:bg-slate-600 rounded-full"></div>
                </div>

                {{-- Header --}}
                <div class="relative flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 sm:py-5">

                        {{-- Ícone --}}
                        <div class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl
                                    bg-gradient-to-br from-emerald-500 to-teal-500
                                    flex items-center justify-center shadow-sm shadow-emerald-200/50">
                            <x-lucide-calendar-plus class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        </div>

                        <div class="flex-1 min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white lato-black leading-tight">
                                Nova Reserva
                            </h2>
                            <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5 leading-snug">
                                {{ $sala->name }}
                            </p>
                        </div>

                        <button wire:click="fecharModalReserva"
                            class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg
                                   text-slate-400 hover:text-slate-700 dark:hover:text-slate-200
                                   hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-4 sm:px-6 py-4 sm:py-5 space-y-4 overflow-y-auto flex-1 bg-slate-50/40 dark:bg-slate-800">

                    {{-- ── Seção 1: Dados da Reunião ──────────────────────────── --}}
                    <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4 sm:p-5 space-y-4">

                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                                <x-lucide-calendar class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                            </div>
                            <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                                Dados da Reunião
                            </h3>
                        </div>

                        {{-- Título --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                Título <span class="text-red-500 normal-case tracking-normal">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-type class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="text" wire:model="titulo" placeholder="Ex: Reunião de planejamento"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                           text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition duration-150
                                           @error('titulo') border border-red-400 dark:border-red-500 bg-red-50/40 @else border border-slate-200 dark:border-slate-600 hover:border-slate-300 @enderror" />
                            </div>
                            @error('titulo')
                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Data --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                Data <span class="text-red-500 normal-case tracking-normal">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="date" wire:model.live="dataReserva"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                           text-slate-800 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition duration-150
                                           @error('dataReserva') border border-red-400 dark:border-red-500 @else border border-slate-200 dark:border-slate-600 hover:border-slate-300 @enderror" />
                            </div>
                            @error('dataReserva')
                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- Início + Término --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                    Início <span class="text-red-500 normal-case tracking-normal">*</span>
                                </label>
                                <div class="relative">
                                    <x-lucide-clock class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                    <input type="time" wire:model="horaInicio"
                                        class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                               text-slate-800 dark:text-white
                                               focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition duration-150
                                               @error('horaInicio') border border-red-400 dark:border-red-500 @else border border-slate-200 dark:border-slate-600 hover:border-slate-300 @enderror" />
                                </div>
                                @error('horaInicio')
                                    <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                    Término <span class="text-red-500 normal-case tracking-normal">*</span>
                                </label>
                                <div class="relative">
                                    <x-lucide-clock class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                    <input type="time" wire:model="horaFim"
                                        class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                               text-slate-800 dark:text-white
                                               focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition duration-150
                                               @error('horaFim') border border-red-400 dark:border-red-500 @else border border-slate-200 dark:border-slate-600 hover:border-slate-300 @enderror" />
                                </div>
                                @error('horaFim')
                                    <p class="flex items-center gap-1 text-xs text-red-500 lato-regular"><x-lucide-alert-circle class="w-3 h-3" />{{ $message }}</p>
                                @enderror
                            </div>
                        </div>

                        {{-- Aviso de feriados/eventos --}}
                        @if($dataReserva)
                            <x-calendario-aviso :data="$dataReserva" />
                        @endif

                        {{-- Descrição --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                Descrição <span class="normal-case tracking-normal font-normal text-slate-400 lato-regular">(opcional)</span>
                            </label>
                            <div class="relative">
                                <x-lucide-file-text class="absolute left-3 top-3 w-4 h-4 text-slate-400 pointer-events-none" />
                                <textarea wire:model="descricao" rows="2" placeholder="Detalhes da reunião..."
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                           text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500
                                           border border-slate-200 dark:border-slate-600 hover:border-slate-300
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                           transition duration-150 resize-none"></textarea>
                            </div>
                        </div>
                    </section>

                    {{-- ── Seção 2: Participantes (picker Alpine.js) ────────────── --}}
                    <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4 sm:p-5"
                        x-data="{
                            selectedIds: $wire.entangle('participantesSelecionados'),
                            deptFiltro: '',
                            busca: '',
                            usuarios: @js($usuariosDisponiveis),
                            departamentos: @js($departamentos),
                            capacidade: {{ $sala->capacity }},

                            get filteredUsers() {
                                return this.usuarios.filter(u => {
                                    const okDept = !this.deptFiltro || String(u.department_id) === String(this.deptFiltro);
                                    const q = this.busca.toLowerCase();
                                    const okBusca = !q || u.name.toLowerCase().includes(q) || (u.position && u.position.toLowerCase().includes(q));
                                    return okDept && okBusca;
                                });
                            },
                            isSelected(id) { return this.selectedIds.includes(id); },
                            toggleUser(id) {
                                if (this.isSelected(id)) {
                                    this.selectedIds = this.selectedIds.filter(i => i !== id);
                                } else if (this.selectedIds.length < this.capacidade) {
                                    this.selectedIds = [...this.selectedIds, id];
                                }
                            },
                            todosDeptoSelecionados(deptId) {
                                const ids = this.usuarios.filter(u => String(u.department_id) === String(deptId)).map(u => u.id);
                                return ids.length > 0 && ids.every(id => this.isSelected(id));
                            },
                            toggleDepto(deptId) {
                                const ids = this.usuarios.filter(u => String(u.department_id) === String(deptId)).map(u => u.id);
                                if (this.todosDeptoSelecionados(deptId)) {
                                    this.selectedIds = this.selectedIds.filter(id => !ids.includes(id));
                                } else {
                                    const novos = ids.filter(id => !this.isSelected(id));
                                    const vagas = this.capacidade - this.selectedIds.length;
                                    this.selectedIds = [...this.selectedIds, ...novos.slice(0, vagas)];
                                }
                            },
                            get selectedCount() { return this.selectedIds.length; },
                            get capacidadeAtingida() { return this.selectedIds.length >= this.capacidade; }
                        }">

                        {{-- Header da seção --}}
                        <div class="flex items-center justify-between gap-2 mb-4">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                                    <x-lucide-users class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                                </div>
                                <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                                    Participantes
                                </h3>
                            </div>
                            <div class="flex items-center gap-2">
                                {{-- Badge de contagem --}}
                                <span class="flex items-center gap-1 text-xs lato-bold px-2 py-0.5 rounded-full"
                                    :class="capacidadeAtingida
                                        ? 'bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400'
                                        : 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400'">
                                    <span x-text="selectedCount"></span>/<span x-text="capacidade"></span>
                                </span>
                                {{-- Limpar seleção --}}
                                <button type="button" x-show="selectedCount > 0" @click="selectedIds = []"
                                    class="text-xs text-slate-400 hover:text-red-500 lato-regular cursor-pointer transition">
                                    Limpar
                                </button>
                            </div>
                        </div>

                        {{-- Filtro por departamento --}}
                        <div class="flex gap-1.5 flex-wrap mb-3">
                            <button type="button" @click="deptFiltro = ''"
                                :class="deptFiltro === ''
                                    ? 'bg-slate-800 dark:bg-white text-white dark:text-slate-800'
                                    : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600'"
                                class="px-2.5 py-1 text-xs lato-bold rounded-lg transition cursor-pointer">
                                Todos
                            </button>
                            <template x-for="dept in departamentos" :key="dept.id">
                                <button type="button" @click="deptFiltro = deptFiltro === String(dept.id) ? '' : String(dept.id)"
                                    :class="String(deptFiltro) === String(dept.id)
                                        ? 'bg-emerald-600 text-white'
                                        : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600'"
                                    class="flex items-center gap-1.5 px-2.5 py-1 text-xs lato-bold rounded-lg transition cursor-pointer">
                                    <span x-text="dept.name"></span>
                                    {{-- checkmark quando todos do dept estão selecionados --}}
                                    <span x-show="todosDeptoSelecionados(dept.id)"
                                        class="w-3.5 h-3.5 rounded-full bg-white/30 flex items-center justify-center">
                                        <svg class="w-2 h-2" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </span>
                                </button>
                            </template>
                        </div>

                        {{-- Botão "Selecionar / Desmarcar todo departamento" --}}
                        <template x-if="deptFiltro !== ''">
                            <button type="button" @click="toggleDepto(deptFiltro)"
                                class="w-full mb-3 flex items-center justify-center gap-2 px-3 py-2 text-xs lato-bold rounded-lg border transition cursor-pointer"
                                :class="todosDeptoSelecionados(deptFiltro)
                                    ? 'border-red-200 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 hover:bg-red-100 dark:hover:bg-red-900/30'
                                    : 'border-emerald-200 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/30'">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/>
                                </svg>
                                <span x-text="todosDeptoSelecionados(deptFiltro) ? 'Desmarcar todo o departamento' : 'Selecionar todo o departamento'"></span>
                            </button>
                        </template>

                        {{-- Campo de busca --}}
                        <div class="relative mb-2">
                            <svg class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <circle cx="11" cy="11" r="8"/><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-4.35-4.35"/>
                            </svg>
                            <input type="text" x-model="busca" placeholder="Buscar colaborador..."
                                class="w-full pl-8 pr-3 py-1.5 text-xs lato-regular rounded-lg
                                       bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                                       text-slate-700 dark:text-white placeholder-slate-400
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition" />
                        </div>

                        {{-- Lista de usuários --}}
                        <div class="max-h-52 overflow-y-auto space-y-1 pr-0.5">
                            <template x-for="user in filteredUsers" :key="user.id">
                                <button type="button"
                                    @click="toggleUser(user.id)"
                                    :disabled="capacidadeAtingida && !isSelected(user.id)"
                                    class="w-full flex items-center gap-2.5 px-3 py-2 rounded-lg text-left transition"
                                    :class="isSelected(user.id)
                                        ? 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800 cursor-pointer'
                                        : capacidadeAtingida
                                            ? 'bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700 opacity-40 cursor-not-allowed'
                                            : 'bg-slate-50 dark:bg-slate-800/40 border border-slate-100 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 cursor-pointer'">

                                    {{-- Avatar --}}
                                    <template x-if="user.avatarUrl">
                                        <img :src="user.avatarUrl" :alt="user.name" class="w-8 h-8 rounded-lg object-cover flex-shrink-0" />
                                    </template>
                                    <template x-if="!user.avatarUrl">
                                        <div class="w-8 h-8 rounded-lg flex items-center justify-center flex-shrink-0 text-white text-xs font-bold lato-bold"
                                            :style="`background-color:${user.cor}`"
                                            x-text="user.initials">
                                        </div>
                                    </template>

                                    {{-- Info --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs font-semibold lato-bold text-slate-800 dark:text-white truncate" x-text="user.name"></p>
                                        <p class="text-[10px] text-slate-400 dark:text-slate-500 lato-regular truncate" x-text="user.position"></p>
                                    </div>

                                    {{-- Dept (desktop) --}}
                                    <span class="hidden sm:block text-[10px] text-slate-400 dark:text-slate-500 lato-regular flex-shrink-0 max-w-[80px] truncate"
                                          x-text="user.department_name"></span>

                                    {{-- Checkbox visual --}}
                                    <div class="w-4 h-4 rounded-full flex-shrink-0 flex items-center justify-center transition"
                                        :class="isSelected(user.id)
                                            ? 'bg-emerald-500'
                                            : 'border-2 border-slate-300 dark:border-slate-600'">
                                        <svg x-show="isSelected(user.id)" class="w-2.5 h-2.5 text-white" fill="none" stroke="currentColor" stroke-width="3.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7"/>
                                        </svg>
                                    </div>
                                </button>
                            </template>

                            <div x-show="filteredUsers.length === 0"
                                class="py-8 text-center text-xs text-slate-400 lato-regular">
                                Nenhum colaborador encontrado
                            </div>
                        </div>

                        {{-- Erro de validação --}}
                        @error('participantesSelecionados')
                            <p class="flex items-center gap-1 text-xs text-red-500 lato-regular mt-2">
                                <x-lucide-alert-circle class="w-3 h-3" />{{ $message }}
                            </p>
                        @enderror

                    </section>
                </div>

                {{-- Footer --}}
                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <div class="flex items-center justify-between gap-3">
                        <p class="hidden sm:flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-500 lato-regular">
                            <x-lucide-info class="w-3.5 h-3.5" />
                            Campos com <span class="text-red-500 font-semibold">*</span> são obrigatórios
                        </p>

                        <div class="flex gap-2 sm:gap-3 flex-1 sm:flex-initial">
                            <button wire:click="fecharModalReserva"
                                class="flex-1 sm:flex-none px-4 sm:px-5 py-2 text-sm lato-bold rounded-lg
                                       text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700
                                       border border-slate-200 dark:border-slate-600
                                       hover:bg-slate-50 dark:hover:bg-slate-600 transition cursor-pointer">
                                Cancelar
                            </button>

                            <button wire:click="salvarReserva" wire:loading.attr="disabled"
                                class="flex-1 sm:flex-none px-4 sm:px-6 py-2 text-sm lato-bold rounded-lg
                                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                                       shadow-md shadow-blue-500/20 text-white flex items-center justify-center gap-2
                                       cursor-pointer transition disabled:opacity-60">
                                <span wire:loading wire:target="salvarReserva">
                                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                </span>
                                <span wire:loading.remove wire:target="salvarReserva" class="flex items-center gap-1.5">
                                    <x-lucide-circle-check class="w-4 h-4" />
                                    Confirmar Reserva
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

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
