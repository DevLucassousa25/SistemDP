<div class="p-4 sm:p-6 lg:p-8 min-h-full">
    {{--
 Cabe
alho
 --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
        <div class="text-center sm:text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white font-display tracking-tight">
                Agendamento de Reunião
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5">
                Organize e visualize as reuniões da equipe
            </p>
        </div>
        <button
            wire:click="openCreateModal"
            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 dark:from-blue-500 dark:to-indigo-600 dark:hover:from-blue-600 dark:hover:to-indigo-700
                   text-white text-sm font-semibold lato-bold px-4 py-2.5 rounded-lg shadow-md shadow-blue-500/20 transition cursor-pointer">
            <x-lucide-plus class="w-4 h-4" />
            Nova Reunião
        </button>
    </div>
    {{--
 Layout principal: calend
rio + painel lateral
 --}}
    <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">
        {{--
 CALEND
RIO
 --}}
        <div class="w-full lg:flex-1 xl:w-[1100px] xl:flex-none xl:shrink-0 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">
            {{-- Header: m
 esquerda, nav
 direita --}}
            <div class="flex items-center justify-between px-4 py-4 sm:px-6 sm:py-5">
                <h2 class="text-base sm:text-lg font-bold lato-bold text-slate-800 dark:text-white capitalize truncate pr-2">
                    {{ \Carbon\Carbon::create($calYear, $calMonth, 1)->translatedFormat('F Y') }}
                </h2>
                <div class="flex items-center gap-1 shrink-0">
                    <button wire:click="previousMonth"
                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600
                                   text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-chevron-left class="w-4 h-4" />
                    </button>
                    <button wire:click="goToToday"
                            class="h-8 px-2.5 sm:px-3 text-xs sm:text-sm font-semibold lato-bold rounded-lg border border-slate-200 dark:border-slate-600
                                   text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        Hoje
                    </button>
                    <button wire:click="nextMonth"
                            class="w-8 h-8 flex items-center justify-center rounded-lg border border-slate-200 dark:border-slate-600
                                   text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-chevron-right class="w-4 h-4" />
                    </button>
                </div>
            </div>
            {{-- Cabe
alho dos dias da semana (Dom
b) --}}
            <div class="grid grid-cols-7 px-1.5 sm:px-2 mb-1">
                @foreach (['Dom','Seg','Ter','Qua','Qui','Sex','S
b'] as $wd)
                    <div class="py-1.5 sm:py-2 text-center text-[10px] sm:text-xs font-semibold lato-bold text-slate-400 dark:text-slate-500 tracking-wide">
                        {{ $wd }}
                    </div>
                @endforeach
            </div>
            {{-- Grid de dias --}}
            <div class="grid grid-cols-7 gap-y-1 px-1.5 sm:px-2 pb-3 sm:pb-4">
                @php
                    $today      = \Carbon\Carbon::today()->toDateString();
                    $colorsByDay = $this->eventColorsByDay;
                @endphp
                @foreach ($this->calendarWeeks as $week)
                    @foreach ($week as $day)
                        @if ($day === null)
                            <div class="h-12 sm:h-16 lg:h-20"></div>
                        @else
                            @php
                                $dateStr = $day->toDateString();
                                $isToday = $dateStr === $today;
                                $isSel   = $dateStr === $selectedDay;
                                $dots    = $colorsByDay[$dateStr] ?? [];
                            @endphp
                            <button
                                wire:click="selectDay('{{ $dateStr }}')"
                                class="relative flex flex-col items-center justify-start pt-1.5 sm:pt-2 pb-1.5 sm:pb-2 h-12 sm:h-16 lg:h-20 rounded-lg sm:rounded-xl transition cursor-pointer group
                                       {{ $isSel
                                           ? 'bg-emerald-500 dark:bg-emerald-600'
                                           : 'hover:bg-slate-50 dark:hover:bg-slate-700/50' }}">
                                {{-- N
mero do dia --}}
                                <span class="text-xs sm:text-sm lato-bold leading-none transition
                                    {{ $isSel
                                        ? 'text-white'
                                        : ($isToday
                                            ? 'text-emerald-600 dark:text-emerald-400 font-extrabold'
                                            : 'text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white') }}">
                                    {{ $day->day }}
                                </span>
                                {{-- Pontos coloridos (no rodap
 da c
lula) --}}
                                @if (! empty($dots))
                                    <div class="absolute bottom-1 sm:bottom-2 flex items-center justify-center gap-0.5">
                                        @foreach (array_slice($dots, 0, 3) as $dot)
                                            <span class="w-1 h-1 sm:w-1.5 sm:h-1.5 rounded-full {{ $isSel ? 'bg-white/70' : $dot }}"></span>
                                        @endforeach
                                    </div>
                                @endif
                            </button>
                        @endif
                    @endforeach
                @endforeach
            </div>
        </div>
        {{--
 PAINEL LATERAL
 --}}
        <div class="flex-1 min-w-0 flex flex-col">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 flex flex-col">
                {{-- T
tulo --}}
                <div class="px-4 sm:px-5 pt-4 sm:pt-5 pb-3">
                    <h3 class="text-base sm:text-lg font-bold lato-bold text-slate-800 dark:text-white">
                        {{ \Carbon\Carbon::parse($selectedDay)->isToday()
                            ? 'Reuniões de Hoje'
                            : 'Reuniões de ' . \Carbon\Carbon::parse($selectedDay)->translatedFormat('d \de M') }}
                    </h3>
                </div>
                {{-- Lista --}}
                @php $meetings = $this->meetingsOnSelectedDay; @endphp
                @if ($meetings->isEmpty())
                    <div class="flex flex-col items-center justify-center py-10 sm:py-12 px-6 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                            <x-lucide-calendar-x class="w-6 h-6 text-slate-400 dark:text-slate-500" />
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">Nenhuma reunião neste dia.</p>
                        <button wire:click="openCreateModal"
                                class="mt-3 text-xs text-emerald-600 dark:text-emerald-400 hover:underline cursor-pointer lato-regular">
                            + Agendar reunião
                        </button>
                    </div>
                @else
                    <div class="px-3 sm:px-4 pb-4 sm:pb-5 space-y-3">
                        @foreach ($meetings as $event)
                            @php
                                $isReservation  = $event->source === 'reservation';
                                $avatarGradient = match($event->color_key) {
                                    'zoom'       => 'from-indigo-400 to-indigo-600',
                                    'teams'      => 'from-purple-400 to-indigo-600',
                                    'presencial' => 'from-teal-400 to-emerald-600',
                                    default      => 'from-teal-400 to-cyan-600',
                                };
                            @endphp
                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 group relative">
                                {{-- A
es: s
 quem criou pode editar / excluir --}}
                                @if (! $isReservation && ! empty($event->is_organizer))
                                    <div class="absolute top-3 right-3 flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition">
                                        <button wire:click="openEditModal({{ $event->id }})"
                                                class="w-6 h-6 flex items-center justify-center rounded-md text-slate-400
                                                       hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition cursor-pointer">
                                            <x-lucide-pencil class="w-3.5 h-3.5" />
                                        </button>
                                        <button wire:click="confirmDelete({{ $event->id }})"
                                                class="w-6 h-6 flex items-center justify-center rounded-md text-slate-400
                                                       hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition cursor-pointer disabled:opacity-60"
                                            wire:loading.attr="disabled">
                                            <span wire:loading.remove wire:target="confirmDelete" class="flex items-center gap-1.5">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </span>
                                            <span wire:loading wire:target="confirmDelete" class="flex items-center gap-1.5">
                                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                            </span>
                                        </button>
                                    </div>
                                @endif
                                {{-- T
tulo --}}
                                <p class="text-sm font-bold lato-bold text-slate-800 dark:text-white leading-snug pr-14">
                                    {{ $event->title }}
                                </p>
                                {{-- Tag reserva --}}
                                @if ($isReservation)
                                    <span class="inline-flex items-center gap-1 mt-1 text-[10px] font-semibold lato-bold px-1.5 py-0.5 rounded
                                                 bg-slate-200 dark:bg-slate-600 text-slate-500 dark:text-slate-400">
                                        <x-lucide-building class="w-2.5 h-2.5" />
                                        Reserva de Sala
                                    </span>
                                @endif
                                {{-- Hor
rio --}}
                                <div class="flex items-center gap-1.5 mt-2 text-xs text-slate-500 dark:text-slate-400 lato-regular">
                                    <x-lucide-clock class="w-3.5 h-3.5 shrink-0" />
                                    <span>{{ $event->start_time->format('H:i') }} - {{ $event->end_time->format('H:i') }}</span>
                                </div>
                                {{-- Local --}}
                                <div class="flex items-center gap-1.5 mt-1.5 text-xs text-slate-500 dark:text-slate-400 lato-regular">
                                    @if ($event->location_type === 'presencial')
                                        <x-lucide-map-pin class="w-3.5 h-3.5 shrink-0" />
                                    @else
                                        <x-lucide-video class="w-3.5 h-3.5 shrink-0" />
                                    @endif
                                    <span>{{ $event->location_label }}</span>
                                </div>
                                {{-- Participantes (meetings) --}}
                                @if (! $isReservation && $event->participants->isNotEmpty())
                                    @php
                                        $shown    = $event->participants->take(3);
                                        $overflow = max(0, $event->participants->count() - 3);
                                    @endphp
                                    <div class="flex items-center gap-2 mt-3">
                                        <div class="flex -space-x-2">
                                            @foreach ($shown as $p)
                                                <div title="{{ $p->name }}"
                                                     class="w-7 h-7 rounded-full bg-gradient-to-br {{ $avatarGradient }}
                                                            flex items-center justify-center text-white text-[11px] font-bold lato-bold
                                                            border-2 border-white dark:border-slate-800 shrink-0">
                                                    {{ strtoupper(substr($p->name, 0, 2)) }}
                                                </div>
                                            @endforeach
                                            @if ($overflow > 0)
                                                <div class="w-7 h-7 rounded-full bg-slate-200 dark:bg-slate-600
                                                            flex items-center justify-center text-[10px] font-bold lato-bold
                                                            text-slate-600 dark:text-slate-300
                                                            border-2 border-white dark:border-slate-800 shrink-0">
                                                    +{{ $overflow }}
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @elseif ($isReservation && !empty($event->organizer_name))
                                    <div class="flex items-center gap-2 mt-3">
                                        <div class="w-7 h-7 rounded-full bg-gradient-to-br {{ $avatarGradient }}
                                                    flex items-center justify-center text-white text-[11px] font-bold lato-bold
                                                    border-2 border-white dark:border-slate-800 shrink-0">
                                            {{ strtoupper(substr($event->organizer_name, 0, 2)) }}
                                        </div>
                                        <span class="text-xs text-slate-400 dark:text-slate-500 lato-regular truncate">
                                            {{ $event->organizer_name }}
                                        </span>
                                    </div>
                                @endif
                                {{-- RSVP: s
 aparece para participantes convidados (n
o-organizadores) --}}
                                @if (! $isReservation && ! empty($event->is_participant) && empty($event->is_organizer))
                                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-600/60">
                                        @php $rsvp = $event->my_rsvp ?? 'pendente'; @endphp
                                        @if ($rsvp === 'pendente')
                                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                                <span class="text-[11px] font-semibold lato-bold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                                                    <x-lucide-mail class="w-3.5 h-3.5" />
                                                    Voc
 foi convidado
                                                </span>
                                                <div class="flex items-center gap-2">
                                                    <button wire:click="respondInvite({{ $event->id }}, 'recusado')"
                                                            class="flex items-center gap-1 px-2.5 py-1 text-xs lato-bold
                                                                   text-red-600 dark:text-red-400
                                                                   border border-red-200 dark:border-red-700
                                                                   hover:bg-red-50 dark:hover:bg-red-900/30
                                                                   rounded-md transition cursor-pointer">
                                                        <x-lucide-x class="w-3 h-3" />
                                                        Recusar
                                                    </button>
                                                    <button wire:click="respondInvite({{ $event->id }}, 'aceito')"
                                                            class="flex items-center gap-1 px-2.5 py-1 text-xs lato-bold
                                                                   text-white bg-emerald-600 hover:bg-emerald-700
                                                                   rounded-md transition cursor-pointer">
                                                        <x-lucide-check class="w-3 h-3" />
                                                        Aceitar
                                                    </button>
                                                </div>
                                            </div>
                                        @elseif ($rsvp === 'aceito')
                                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold lato-bold
                                                             text-emerald-600 dark:text-emerald-400">
                                                    <x-lucide-circle-check-big class="w-3.5 h-3.5" />
                                                    Voc
 aceitou o convite
                                                </span>
                                                <button wire:click="respondInvite({{ $event->id }}, 'recusado')"
                                                        class="text-[11px] lato-regular text-slate-400 hover:text-red-500 dark:hover:text-red-400 hover:underline cursor-pointer">
                                                    Recusar
                                                </button>
                                            </div>
                                        @elseif ($rsvp === 'recusado')
                                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                                <span class="inline-flex items-center gap-1.5 text-[11px] font-semibold lato-bold
                                                             text-red-600 dark:text-red-400">
                                                    <x-lucide-circle-x class="w-3.5 h-3.5" />
                                                    Voc
 recusou o convite
                                                </span>
                                                <button wire:click="respondInvite({{ $event->id }}, 'aceito')"
                                                        class="text-[11px] lato-regular text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:underline cursor-pointer">
                                                    Aceitar
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif
                                {{-- Ver detalhes: dispon
vel para organizador e participantes --}}
                                @if (! $isReservation)
                                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-600/60">
                                        <a href="{{ route('reunioes.details', ['id' => $event->id]) }}"
                                           wire:navigate
                                           class="inline-flex items-center gap-1.5 text-xs lato-bold
                                                  text-emerald-600 dark:text-emerald-400
                                                  hover:text-emerald-700 dark:hover:text-emerald-300
                                                  hover:underline cursor-pointer">
                                            <x-lucide-eye class="w-3.5 h-3.5" />
                                            Ver detalhes
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
    {{--
 MODAL NOVA/EDITAR REUNI
 --}}
    @include('livewire.pages.meetings.modals._modal-reuniao')
    @include('livewire.pages.meetings.modals._modal-excluir')
</div>
