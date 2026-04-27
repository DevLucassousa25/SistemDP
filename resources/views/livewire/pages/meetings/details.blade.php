<div class="p-4 sm:p-6 lg:p-8 min-h-full">

    @php
        $statusLabel = match ($meeting->status) {
            'confirmada' => 'Confirmada',
            'cancelada'  => 'Cancelada',
            default      => 'Pendente',
        };

        $statusPill = match ($meeting->status) {
            'confirmada' => ['bg' => 'bg-white/90',            'text' => 'text-emerald-700', 'dot' => 'bg-emerald-500'],
            'cancelada'  => ['bg' => 'bg-red-100',             'text' => 'text-red-700',     'dot' => 'bg-red-500'],
            default      => ['bg' => 'bg-amber-100',           'text' => 'text-amber-700',   'dot' => 'bg-amber-500'],
        };
    @endphp

    {{-- ══════════════════ CABEÇALHO ══════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-6">
        {{-- Esquerda: voltar + título --}}
        <div class="flex items-start gap-3">
            <a href="{{ route('reunioes') }}" wire:navigate
               class="p-2 -ml-2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 rounded-lg transition">
                <x-lucide-arrow-left class="w-5 h-5" />
            </a>
            <div>
                <h1 class="text-xl sm:text-2xl font-bold text-slate-800 dark:text-white tracking-tight">
                    Detalhes da Reunião
                </h1>
                <p class="text-sm text-slate-400 dark:text-slate-500 mt-0.5">
                    Visualize e gerencie a reunião
                </p>
            </div>
        </div>

        {{-- Direita: notificações + Editar + Cancelar --}}
        <div class="flex flex-wrap items-center gap-2">
            <button type="button" title="Notificações"
                    class="p-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800
                           text-slate-500 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                <x-lucide-bell class="w-4 h-4" />
            </button>

            @if ($isOrganizer && $meeting->status !== 'cancelada')
                <button wire:click="goToEdit"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold
                               text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800
                               border border-slate-200 dark:border-slate-700 rounded-xl
                               hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                    <x-lucide-pencil class="w-4 h-4" />
                    Editar
                </button>

                <button wire:click="openCancelModal"
                        class="inline-flex items-center gap-2 px-4 py-2.5 text-sm font-semibold
                               text-red-600 dark:text-red-400 bg-white dark:bg-slate-800
                               border border-red-200 dark:border-red-900/50 rounded-xl
                               hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                    <x-lucide-x class="w-4 h-4" />
                    Cancelar
                </button>
            @endif
        </div>
    </div>

    {{-- ══════════════════ HERO BANNER ══════════════════ --}}
    <div class="relative rounded-2xl overflow-hidden mb-4
                bg-gradient-to-br from-emerald-600 via-emerald-600 to-teal-600 shadow-sm">

        {{-- Decoração sutil --}}
        <div class="absolute -top-20 -right-20 w-72 h-72 bg-white/10 rounded-full blur-3xl pointer-events-none"></div>

        <div class="relative p-6 sm:p-8 text-white">

            {{-- Linha superior: tipo (esq) + status (dir) --}}
            <div class="flex items-start justify-between gap-3 mb-5">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold
                             bg-white/15 text-white ring-1 ring-inset ring-white/20">
                    {{ $meeting->typeLabel() }}
                </span>

                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold {{ $statusPill['bg'] }} {{ $statusPill['text'] }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $statusPill['dot'] }}"></span>
                    {{ $statusLabel }}
                </span>
            </div>

            {{-- Título --}}
            <h2 class="text-3xl sm:text-4xl font-bold tracking-tight leading-tight text-white/90">
                {{ $meeting->title }}
            </h2>

            {{-- Linha de infos --}}
            <div class="mt-5 flex flex-wrap items-center gap-x-6 gap-y-2 text-sm text-white/90">
                <span class="inline-flex items-center gap-2">
                    <x-lucide-calendar-days class="w-4 h-4 opacity-80" />
                    <span class="capitalize">{{ $meeting->start_time->translatedFormat('l, d \\d\\e F \\d\\e Y') }}</span>
                </span>

                <span class="inline-flex items-center gap-2">
                    <x-lucide-clock class="w-4 h-4 opacity-80" />
                    {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }} · {{ $meeting->durationLabel() }}
                </span>

                <span class="inline-flex items-center gap-2">
                    <x-lucide-map-pin class="w-4 h-4 opacity-80" />
                    {{ $meeting->locationLabel() }}
                </span>

                <span class="inline-flex items-center gap-2">
                    <x-lucide-users class="w-4 h-4 opacity-80" />
                    {{ $rsvpCounts['confirmados'] }}/{{ $rsvpCounts['total'] }} confirmados
                </span>
            </div>
        </div>
    </div>

    {{-- ══════════════════ ORGANIZADOR + ENTRAR ══════════════════ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6 px-2">
        <div class="flex items-center gap-3 min-w-0">
            <span class="text-sm text-slate-500 dark:text-slate-400 shrink-0">Organizado por</span>
            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-600
                        flex items-center justify-center text-white text-[11px] font-bold shrink-0 ring-2 ring-white dark:ring-slate-800 shadow-sm">
                {{ strtoupper(substr($meeting->organizer?->name ?? '?', 0, 2)) }}
            </div>
            <div class="min-w-0 flex items-center gap-1.5 flex-wrap">
                <span class="text-sm font-semibold text-slate-800 dark:text-white">
                    {{ $meeting->organizer?->name ?? '—' }}
                </span>
                @if ($meeting->organizer?->position)
                    <span class="text-sm text-slate-400 dark:text-slate-500">· {{ $meeting->organizer->position }}</span>
                @endif
            </div>
        </div>

        @if ($meeting->status !== 'cancelada')
            @if ($meeting->location_type === 'online' && $meeting->online_link)
                <a href="{{ $meeting->online_link }}" target="_blank" rel="noopener noreferrer"
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                          bg-emerald-600 hover:bg-emerald-700 text-white transition shrink-0 cursor-pointer shadow-sm">
                    <x-lucide-link class="w-4 h-4" />
                    Entrar na Reunião
                </a>
            @elseif ($meeting->location_type === 'presencial' && $meeting->room)
                <a href="{{ route('rooms.details', ['id' => $meeting->room->id]) }}" wire:navigate
                   class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl text-sm font-semibold
                          bg-emerald-600 hover:bg-emerald-700 text-white transition shrink-0 cursor-pointer shadow-sm">
                    <x-lucide-map-pin class="w-4 h-4" />
                    Ver Sala
                </a>
            @endif
        @endif
    </div>

    {{-- ══════════════════ RSVP (para convidados) ══════════════════ --}}
    @if ($isParticipant && ! $isOrganizer && $meeting->status !== 'cancelada')
        @php $rsvp = $myRsvp ?? 'pendente'; @endphp
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 mb-4">
            @if ($rsvp === 'pendente')
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-900/30">
                            <x-lucide-mail class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">Você foi convidado</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Responda ao convite para confirmar sua presença.</p>
                        </div>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <button wire:click="respondInvite('recusado')"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold
                                       text-red-600 dark:text-red-400 bg-white dark:bg-slate-800
                                       border border-red-200 dark:border-red-900/50 rounded-lg
                                       hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer">
                            <x-lucide-x class="w-3.5 h-3.5" />
                            Recusar
                        </button>
                        <button wire:click="respondInvite('aceito')"
                                class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white
                                       bg-emerald-600 hover:bg-emerald-700 rounded-lg transition cursor-pointer">
                            <x-lucide-check class="w-3.5 h-3.5" />
                            Aceitar
                        </button>
                    </div>
                </div>
            @elseif ($rsvp === 'aceito')
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-lg bg-emerald-50 dark:bg-emerald-900/30">
                            <x-lucide-circle-check-big class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">Você aceitou o convite</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Sua presença está confirmada.</p>
                        </div>
                    </div>
                    <button wire:click="respondInvite('recusado')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold
                                   text-slate-500 dark:text-slate-400 hover:text-red-600 dark:hover:text-red-400 cursor-pointer">
                        Alterar para recusado
                    </button>
                </div>
            @elseif ($rsvp === 'recusado')
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                    <div class="flex items-center gap-2.5">
                        <div class="p-2 rounded-lg bg-red-50 dark:bg-red-900/30">
                            <x-lucide-circle-x class="w-4 h-4 text-red-600 dark:text-red-400" />
                        </div>
                        <div>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white">Você recusou o convite</p>
                            <p class="text-xs text-slate-400 dark:text-slate-500">Pode alterar sua resposta a qualquer momento.</p>
                        </div>
                    </div>
                    <button wire:click="respondInvite('aceito')"
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 text-xs font-semibold text-white
                                   bg-emerald-600 hover:bg-emerald-700 rounded-lg transition cursor-pointer">
                        <x-lucide-check class="w-3.5 h-3.5" />
                        Aceitar agora
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════ TABS (pill style) ══════════════════ --}}
    <div class="mb-4">
        <div class="inline-flex items-center gap-1 p-1 bg-slate-100 dark:bg-slate-800 rounded-xl">
            @foreach ([
                ['key' => 'detalhes',      'label' => 'Detalhes'],
                ['key' => 'pauta',         'label' => 'Pauta'],
                ['key' => 'participantes', 'label' => 'Participantes'],
                ['key' => 'notas',         'label' => 'Notas'],
            ] as $t)
                @php $isActive = $activeTab === $t['key']; @endphp
                <button wire:click="setTab('{{ $t['key'] }}')"
                        @class([
                            'px-4 py-2 text-sm font-semibold rounded-lg transition cursor-pointer',
                            'bg-white dark:bg-slate-700 text-slate-800 dark:text-white shadow-sm' => $isActive,
                            'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' => ! $isActive,
                        ])>
                    {{ $t['label'] }}
                </button>
            @endforeach
        </div>
    </div>

    {{-- ══════════════════ CONTEÚDO DAS ABAS ══════════════════ --}}

    {{-- ──────────── TAB: DETALHES ──────────── --}}
    @if ($activeTab === 'detalhes')
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
            {{-- Descrição e Objetivos --}}
            <div class="lg:col-span-2 bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4">
                    Descrição e Objetivos
                </h3>

                @if ($meeting->description)
                    <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                        {{ $meeting->description }}
                    </p>
                @else
                    <p class="text-sm text-slate-400 dark:text-slate-500 italic">
                        Nenhuma descrição foi informada para esta reunião.
                    </p>
                @endif
            </div>

            {{-- Informações --}}
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
                <h3 class="text-base font-bold text-slate-800 dark:text-white mb-4">
                    Informações
                </h3>

                <dl class="space-y-3 text-sm">
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-400 dark:text-slate-500">Tipo</dt>
                        <dd class="font-semibold text-slate-700 dark:text-slate-200 text-right">{{ $meeting->typeLabel() }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-400 dark:text-slate-500">Data</dt>
                        <dd class="font-semibold text-slate-700 dark:text-slate-200 text-right">
                            {{ $meeting->start_time->format('d/m/Y') }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-400 dark:text-slate-500">Horário</dt>
                        <dd class="font-semibold text-slate-700 dark:text-slate-200 text-right">
                            {{ $meeting->start_time->format('H:i') }} - {{ $meeting->end_time->format('H:i') }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-400 dark:text-slate-500">Duração</dt>
                        <dd class="font-semibold text-slate-700 dark:text-slate-200 text-right">{{ $meeting->durationLabel() }}</dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-400 dark:text-slate-500">Local</dt>
                        <dd class="font-semibold text-slate-700 dark:text-slate-200 text-right truncate max-w-[60%]">
                            {{ $meeting->locationLabel() }}
                        </dd>
                    </div>
                    <div class="flex items-center justify-between gap-3">
                        <dt class="text-slate-400 dark:text-slate-500">Participantes</dt>
                        <dd class="font-semibold text-slate-700 dark:text-slate-200 text-right">
                            {{ $rsvpCounts['total'] }} {{ $rsvpCounts['total'] === 1 ? 'pessoa' : 'pessoas' }}
                        </dd>
                    </div>
                </dl>

                @if ($meeting->location_type === 'online' && $meeting->online_link)
                    <button type="button" wire:click="copyAccessLink"
                            x-data
                            @click="$dispatch('copy-link', {{ json_encode($meeting->online_link) }})"
                            class="mt-5 w-full inline-flex items-center justify-center gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold
                                   bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200
                                   border border-slate-200 dark:border-slate-600
                                   hover:bg-slate-50 dark:hover:bg-slate-600 transition cursor-pointer">
                        <x-lucide-copy class="w-3.5 h-3.5" />
                        Copiar link de acesso
                    </button>
                @endif
            </div>
        </div>
    @endif

    {{-- ──────────── TAB: PAUTA ──────────── --}}
    @if ($activeTab === 'pauta')
        @php
            $totalItems = $meeting->agendaItems->count();
            $doneItems  = $meeting->agendaItems->where('is_completed', true)->count();
        @endphp

        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
            {{-- Cabeçalho --}}
            <div class="flex items-center justify-between gap-3 mb-5">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">
                    Pauta da Reunião
                </h3>
                @if ($totalItems > 0)
                    <span class="text-sm text-slate-400 dark:text-slate-500">
                        {{ $doneItems }}/{{ $totalItems }} {{ $totalItems === 1 ? 'item concluído' : 'itens concluídos' }}
                    </span>
                @endif
            </div>

            {{-- Lista --}}
            @if ($totalItems === 0)
                <div class="flex flex-col items-center justify-center gap-3 py-12 text-center">
                    <div class="p-3 rounded-full bg-slate-100 dark:bg-slate-700">
                        <x-lucide-clipboard-list class="w-6 h-6 text-slate-400 dark:text-slate-500" />
                    </div>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Nenhum item na pauta</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">
                        @if ($isOrganizer)
                            Adicione tópicos que serão discutidos durante a reunião.
                        @else
                            O organizador ainda não adicionou itens à pauta.
                        @endif
                    </p>
                </div>
            @else
                <div class="space-y-2">
                    @foreach ($meeting->agendaItems as $item)
                        @php
                            $canToggle = $isOrganizer
                                         || ((int) ($item->responsible_user_id ?? 0) === (int) auth()->id());
                            $done = (bool) $item->is_completed;
                        @endphp
                        <div @class([
                                'group flex items-center gap-4 px-4 py-3.5 rounded-xl border transition',
                                'bg-emerald-50/70 dark:bg-emerald-900/20 border-emerald-100 dark:border-emerald-800/40' => $done,
                                'bg-slate-50/70 dark:bg-slate-700/30 border-slate-200/60 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600' => ! $done,
                            ])>
                            {{-- Checkbox circular --}}
                            <button type="button"
                                    @class([
                                        'w-6 h-6 rounded-full flex items-center justify-center transition shrink-0',
                                        'bg-emerald-500 text-white' => $done,
                                        'bg-white dark:bg-slate-800 border-2 border-slate-300 dark:border-slate-500 hover:border-emerald-500' => ! $done,
                                        'cursor-pointer' => $canToggle,
                                        'cursor-not-allowed opacity-60' => ! $canToggle,
                                    ])
                                    @if ($canToggle) wire:click="toggleAgendaItem({{ $item->id }})" @endif
                                    @if (! $canToggle) disabled @endif>
                                @if ($done)
                                    <x-lucide-check class="w-4 h-4" />
                                @endif
                            </button>

                            {{-- Título + responsável --}}
                            <div class="flex-1 min-w-0">
                                <p @class([
                                        'text-sm font-semibold',
                                        'line-through text-slate-400 dark:text-slate-500' => $done,
                                        'text-slate-800 dark:text-white' => ! $done,
                                    ])>
                                    {{ $item->title }}
                                </p>
                                @if ($item->responsible)
                                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                        {{ $item->responsible->name }}
                                    </p>
                                @endif
                            </div>

                            {{-- Duração --}}
                            <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-medium
                                         bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-600
                                         text-slate-600 dark:text-slate-300 shrink-0">
                                {{ $item->duration_minutes }} min
                            </span>

                            {{-- Lixeira (organizador) --}}
                            @if ($isOrganizer)
                                <button type="button"
                                        wire:click="deleteAgendaItem({{ $item->id }})"
                                        wire:confirm="Remover este item da pauta?"
                                        class="p-1.5 rounded-md text-slate-300 dark:text-slate-600 opacity-0 group-hover:opacity-100
                                               hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition cursor-pointer shrink-0">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Form: adicionar item --}}
            @if ($isOrganizer && $meeting->status !== 'cancelada')
                <div class="mt-5 pt-5 border-t border-slate-100 dark:border-slate-700">
                    <h4 class="text-xs uppercase tracking-wide font-semibold text-slate-400 dark:text-slate-500 mb-3">
                        Adicionar item
                    </h4>

                    <div class="grid grid-cols-1 sm:grid-cols-12 gap-3">
                        <div class="sm:col-span-6">
                            <input type="text" wire:model="newAgendaTitle"
                                   placeholder="Tópico a ser discutido…"
                                   class="w-full px-3 py-2 text-sm rounded-lg border
                                          bg-white dark:bg-slate-800
                                          border-slate-200 dark:border-slate-600
                                          text-slate-800 dark:text-white
                                          placeholder-slate-400 dark:placeholder-slate-500
                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                            @error('newAgendaTitle')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div class="sm:col-span-4">
                            <select wire:model="newAgendaResponsibleId"
                                    class="w-full px-3 py-2 text-sm rounded-lg border
                                           bg-white dark:bg-slate-800
                                           border-slate-200 dark:border-slate-600
                                           text-slate-800 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent">
                                <option value="">Responsável (opcional)</option>
                                <option value="{{ $meeting->organizer?->id }}">{{ $meeting->organizer?->name }} (organizador)</option>
                                @foreach ($meeting->participants as $p)
                                    <option value="{{ $p->id }}">{{ $p->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <input type="number" min="1" max="600" wire:model="newAgendaDuration"
                                   placeholder="min"
                                   class="w-full px-3 py-2 text-sm rounded-lg border
                                          bg-white dark:bg-slate-800
                                          border-slate-200 dark:border-slate-600
                                          text-slate-800 dark:text-white
                                          focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:border-transparent" />
                            @error('newAgendaDuration')
                                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <button type="button" wire:click="addAgendaItem"
                            class="mt-3 inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold
                                   bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer">
                        <x-lucide-plus class="w-4 h-4" />
                        Adicionar à pauta
                    </button>
                </div>
            @endif
        </div>
    @endif

    {{-- ──────────── TAB: PARTICIPANTES ──────────── --}}
    @if ($activeTab === 'participantes')
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
            {{-- Cabeçalho --}}
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-5">
                <h3 class="text-base font-bold text-slate-800 dark:text-white">
                    Participantes ({{ $rsvpCounts['total'] + 1 }})
                </h3>
                <div class="flex flex-wrap items-center gap-3 text-xs">
                    <span class="inline-flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                        {{ $rsvpCounts['confirmados'] }} confirmados
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-amber-600 dark:text-amber-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                        {{ $rsvpCounts['pendentes'] }} pendentes
                    </span>
                    <span class="inline-flex items-center gap-1.5 text-red-600 dark:text-red-400">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span>
                        {{ $rsvpCounts['recusou'] }} recusou
                    </span>
                </div>
            </div>

            {{-- Lista (organizador em destaque + convidados) --}}
            <div class="space-y-2">
                {{-- Organizador --}}
                <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 dark:bg-slate-700/30 border border-slate-200/60 dark:border-slate-700">
                    <div class="w-10 h-10 rounded-full bg-gradient-to-br from-emerald-500 to-teal-500
                                flex items-center justify-center text-white text-sm font-bold shrink-0">
                        {{ strtoupper(substr($meeting->organizer?->name ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">
                            {{ $meeting->organizer?->name ?? '—' }}
                        </p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                            {{ $meeting->organizer?->position ?? '—' }}
                            @if ($meeting->organizer?->department?->name)
                                · {{ $meeting->organizer->department->name }}
                            @endif
                        </p>
                    </div>
                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold
                                 bg-emerald-100 text-emerald-700 dark:bg-emerald-900/40 dark:text-emerald-300 shrink-0">
                        <x-lucide-crown class="w-3 h-3" />
                        Organizador
                    </span>
                </div>

                {{-- Convidados --}}
                @foreach ($meeting->participants as $p)
                    @php
                        $rsvpVal = $p->pivot->rsvp ?? 'pendente';
                        $rsvpBadge = match ($rsvpVal) {
                            'aceito'   => ['Confirmado', 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300', 'bg-emerald-500'],
                            'recusado' => ['Recusou',    'bg-red-50 text-red-700 dark:bg-red-900/30 dark:text-red-300',                 'bg-red-500'],
                            default    => ['Pendente',   'bg-amber-50 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300',         'bg-amber-500'],
                        };
                    @endphp
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-50/70 dark:bg-slate-700/30 border border-slate-200/60 dark:border-slate-700">
                        <div class="w-10 h-10 rounded-full bg-gradient-to-br from-slate-400 to-slate-600
                                    flex items-center justify-center text-white text-sm font-bold shrink-0">
                            {{ strtoupper(substr($p->name, 0, 2)) }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $p->name }}</p>
                            <p class="text-xs text-slate-500 dark:text-slate-400 truncate">
                                {{ $p->position ?? '—' }}
                                @if ($p->department?->name)
                                    · {{ $p->department->name }}
                                @endif
                            </p>
                        </div>
                        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[11px] font-semibold shrink-0 {{ $rsvpBadge[1] }}">
                            <span class="w-1.5 h-1.5 rounded-full {{ $rsvpBadge[2] }}"></span>
                            {{ $rsvpBadge[0] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    {{-- ──────────── TAB: NOTAS ──────────── --}}
    @if ($activeTab === 'notas')
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
            <h3 class="text-base font-bold text-slate-800 dark:text-white mb-5">
                Notas e Observações
            </h3>

            {{-- Lista --}}
            @if ($meeting->meetingNotes->isEmpty())
                <div class="flex flex-col items-center justify-center gap-3 py-10 text-center">
                    <div class="p-3 rounded-full bg-slate-100 dark:bg-slate-700">
                        <x-lucide-notebook-pen class="w-6 h-6 text-slate-400 dark:text-slate-500" />
                    </div>
                    <p class="text-sm font-semibold text-slate-600 dark:text-slate-300">Nenhuma nota ainda</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 max-w-xs">
                        Registre decisões, observações ou ações combinadas durante a reunião.
                    </p>
                </div>
            @else
                <div class="space-y-3 mb-5">
                    @foreach ($meeting->meetingNotes as $note)
                        @php $canDelete = ((int) $note->user_id === (int) auth()->id()) || $isOrganizer; @endphp
                        <div class="group flex items-start gap-3 p-4 rounded-xl bg-slate-50/70 dark:bg-slate-700/30 border border-slate-200/60 dark:border-slate-700">
                            <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-400 to-slate-600
                                        flex items-center justify-center text-white text-xs font-bold shrink-0">
                                {{ strtoupper(substr($note->user?->name ?? '?', 0, 2)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <span class="text-sm font-semibold text-slate-800 dark:text-white">
                                        {{ $note->user?->name ?? '—' }}
                                    </span>
                                    <span class="text-xs text-slate-400 dark:text-slate-500"
                                          title="{{ $note->created_at->format('d/m/Y H:i') }}">
                                        {{ $note->created_at->format('d/m/Y') }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-slate-600 dark:text-slate-300 leading-relaxed whitespace-pre-line">
                                    {{ $note->content }}
                                </p>
                            </div>
                            @if ($canDelete)
                                <button type="button"
                                        wire:click="deleteNote({{ $note->id }})"
                                        wire:confirm="Remover esta nota?"
                                        class="p-1.5 rounded-md text-slate-300 dark:text-slate-600 opacity-0 group-hover:opacity-100
                                               hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition cursor-pointer shrink-0">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </button>
                            @endif
                        </div>
                    @endforeach
                </div>
            @endif

            {{-- Form: adicionar nota --}}
            @if (($isOrganizer || $isParticipant) && $meeting->status !== 'cancelada')
                <div class="flex items-start gap-3 p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <div class="w-9 h-9 rounded-full bg-gradient-to-br from-slate-400 to-slate-600
                                flex items-center justify-center text-white text-xs font-bold shrink-0 mt-0.5">
                        {{ strtoupper(substr(auth()->user()->name ?? '?', 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <textarea wire:model="newNote" rows="2"
                                  placeholder="Adicione uma nota ou observação…"
                                  class="w-full px-0 py-1 text-sm bg-transparent
                                         text-slate-800 dark:text-white
                                         placeholder-slate-400 dark:placeholder-slate-500
                                         border-0 focus:outline-none focus:ring-0 resize-none"></textarea>
                        @error('newNote')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                        @enderror

                        <div class="flex items-center justify-between gap-2 mt-2">
                            <button type="button"
                                    class="p-1.5 rounded-md text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-not-allowed"
                                    disabled title="Em breve">
                                <x-lucide-paperclip class="w-4 h-4" />
                            </button>
                            <button type="button" wire:click="addNote"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 rounded-lg text-sm font-semibold
                                           bg-emerald-600 hover:bg-emerald-700 text-white transition cursor-pointer">
                                <x-lucide-send class="w-3.5 h-3.5" />
                                Adicionar Nota
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    @endif

    {{-- ══════════════════ MODAL: CANCELAR REUNIÃO ══════════════════ --}}
    @if ($showCancelModal)
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4 bg-black/50 backdrop-blur-sm"
             wire:click.self="closeCancelModal">
            <div class="w-full sm:max-w-md bg-white dark:bg-slate-800 rounded-t-2xl sm:rounded-2xl overflow-hidden"
                 wire:click.stop>
                <div class="p-5 sm:p-6">
                    <div class="flex items-start gap-3">
                        <div class="p-2.5 rounded-full bg-red-50 dark:bg-red-900/30 shrink-0">
                            <x-lucide-triangle-alert class="w-5 h-5 text-red-600 dark:text-red-400" />
                        </div>
                        <div class="min-w-0">
                            <h3 class="text-base font-bold text-slate-800 dark:text-white">Cancelar reunião?</h3>
                            <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">
                                A reunião <span class="font-semibold text-slate-700 dark:text-slate-200">"{{ $meeting->title }}"</span>
                                será marcada como cancelada. Essa ação pode ser revertida editando a reunião.
                            </p>
                        </div>
                    </div>

                    <div class="mt-5 flex flex-col-reverse sm:flex-row sm:items-center sm:justify-end gap-2">
                        <button type="button" wire:click="closeCancelModal"
                                class="px-4 py-2.5 text-sm font-semibold rounded-lg
                                       text-slate-700 dark:text-slate-200 bg-white dark:bg-slate-800
                                       border border-slate-200 dark:border-slate-700
                                       hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                            Voltar
                        </button>
                        <button type="button" wire:click="cancelMeeting"
                                class="px-4 py-2.5 text-sm font-semibold rounded-lg text-white
                                       bg-red-600 hover:bg-red-700 transition cursor-pointer">
                            Sim, cancelar reunião
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════ CLIPBOARD (copy link) ══════════════════ --}}
    <div x-data
         @copy-link.window="
            const text = $event.detail;
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(text);
            } else {
                const ta = document.createElement('textarea');
                ta.value = text;
                ta.style.position = 'fixed';
                ta.style.opacity = '0';
                document.body.appendChild(ta);
                ta.select();
                try { document.execCommand('copy'); } catch (e) {}
                document.body.removeChild(ta);
            }
         "></div>
</div>
