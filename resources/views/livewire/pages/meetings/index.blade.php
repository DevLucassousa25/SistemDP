<div class="p-4 sm:p-6 lg:p-8 min-h-full">

    {{-- ── Cabeçalho ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6 sm:mb-8">
        <div class="text-center sm:text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white font-display tracking-tight">
                Agendamento de Reuniões
            </h1>
            <p class="text-sm text-slate-400 dark:text-slate-500 mt-1.5">
                Organize e visualize as reuniões da equipe
            </p>
        </div>
        <button
            wire:click="openCreateModal"
            class="w-full sm:w-auto flex items-center justify-center gap-2 bg-green-600 hover:bg-green-700 dark:bg-green-500 dark:hover:bg-green-600
                   text-white text-sm font-semibold lato-bold px-4 py-2.5 rounded-lg transition cursor-pointer">
            <x-lucide-plus class="w-4 h-4" />
            Nova Reunião
        </button>
    </div>

    {{-- ── Layout principal: calendário + painel lateral ──────────────────────── --}}
    <div class="flex flex-col lg:flex-row gap-4 sm:gap-6">

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ CALENDÁRIO ━━━━━━━━━━━━━━━━━━━━━ --}}
        <div class="w-full lg:flex-1 xl:w-[1100px] xl:flex-none xl:shrink-0 bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 overflow-hidden">

            {{-- Header: mês à esquerda, nav à direita --}}
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

            {{-- Cabeçalho dos dias da semana (Dom → Sáb) --}}
            <div class="grid grid-cols-7 px-1.5 sm:px-2 mb-1">
                @foreach (['Dom','Seg','Ter','Qua','Qui','Sex','Sáb'] as $wd)
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

                                {{-- Número do dia --}}
                                <span class="text-xs sm:text-sm lato-bold leading-none transition
                                    {{ $isSel
                                        ? 'text-white'
                                        : ($isToday
                                            ? 'text-emerald-600 dark:text-emerald-400 font-extrabold'
                                            : 'text-slate-700 dark:text-slate-300 group-hover:text-slate-900 dark:group-hover:text-white') }}">
                                    {{ $day->day }}
                                </span>

                                {{-- Pontos coloridos (no rodapé da célula) --}}
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

        {{-- ━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━━ PAINEL LATERAL ━━━━━━━━━━━━━━━━━━━ --}}
        <div class="flex-1 min-w-0 flex flex-col">

            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-100 dark:border-slate-700 flex flex-col">

                {{-- Título --}}
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
                                    'teams'      => 'from-purple-400 to-purple-600',
                                    'presencial' => 'from-teal-400 to-emerald-600',
                                    default      => 'from-teal-400 to-cyan-600',
                                };
                            @endphp

                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 group relative">

                                {{-- Ações: só quem criou pode editar / excluir --}}
                                @if (! $isReservation && ! empty($event->is_organizer))
                                    <div class="absolute top-3 right-3 flex items-center gap-1 opacity-100 sm:opacity-0 sm:group-hover:opacity-100 transition">
                                        <button wire:click="openEditModal({{ $event->id }})"
                                                class="w-6 h-6 flex items-center justify-center rounded-md text-slate-400
                                                       hover:text-emerald-500 hover:bg-emerald-50 dark:hover:bg-emerald-900/30 transition cursor-pointer">
                                            <x-lucide-pencil class="w-3.5 h-3.5" />
                                        </button>
                                        <button wire:click="confirmDelete({{ $event->id }})"
                                                class="w-6 h-6 flex items-center justify-center rounded-md text-slate-400
                                                       hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition cursor-pointer">
                                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                        </button>
                                    </div>
                                @endif

                                {{-- Título --}}
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

                                {{-- Horário --}}
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
                                @elseif ($isReservation && $event->organizer_name !== '—')
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

                                {{-- RSVP: só aparece para participantes convidados (não-organizadores) --}}
                                @if (! $isReservation && ! empty($event->is_participant) && empty($event->is_organizer))
                                    <div class="mt-3 pt-3 border-t border-slate-200 dark:border-slate-600/60">
                                        @php $rsvp = $event->my_rsvp ?? 'pendente'; @endphp

                                        @if ($rsvp === 'pendente')
                                            <div class="flex items-center justify-between gap-2 flex-wrap">
                                                <span class="text-[11px] font-semibold lato-bold text-amber-600 dark:text-amber-400 flex items-center gap-1.5">
                                                    <x-lucide-mail class="w-3.5 h-3.5" />
                                                    Você foi convidado
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
                                                    Você aceitou o convite
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
                                                    Você recusou o convite
                                                </span>
                                                <button wire:click="respondInvite({{ $event->id }}, 'aceito')"
                                                        class="text-[11px] lato-regular text-slate-400 hover:text-emerald-600 dark:hover:text-emerald-400 hover:underline cursor-pointer">
                                                    Aceitar
                                                </button>
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Ver detalhes: disponível para organizador e participantes --}}
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


    {{-- ════════════════════════════════ MODAL NOVA/EDITAR REUNIÃO ═══════════════ --}}
    @if ($showModal)
    <div
        class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4 bg-black/50 backdrop-blur-sm"
        wire:click.self="closeModal">

        <div class="w-full sm:max-w-2xl bg-white dark:bg-slate-800 rounded-t-2xl sm:rounded-2xl overflow-hidden max-h-[95vh] sm:max-h-[90vh] flex flex-col"
             wire:click.stop>

            {{-- Alça de arraste (mobile) --}}
            <div class="flex justify-center pt-2.5 pb-1 sm:hidden flex-shrink-0">
                <div class="w-10 h-1 bg-slate-200 dark:bg-slate-600 rounded-full"></div>
            </div>

            {{-- Header --}}
            <div class="flex items-center justify-between px-4 sm:px-6 py-3 sm:py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="flex items-center gap-2.5 min-w-0">
                    <div class="w-8 h-8 rounded-lg bg-emerald-100 dark:bg-emerald-900/50 flex items-center justify-center shrink-0">
                        <x-lucide-calendar-plus class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    </div>
                    <h2 class="text-base font-bold lato-bold text-slate-800 dark:text-white truncate">
                        {{ $isEditing ? 'Editar Reunião' : 'Nova Reunião' }}
                    </h2>
                </div>
                <button wire:click="closeModal"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 shrink-0 ml-2
                               hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Body --}}
            <div class="overflow-y-auto flex-1 px-4 sm:px-6 py-4 sm:py-5 space-y-4 sm:space-y-5">

                {{-- Título --}}
                <div>
                    <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Título <span class="text-red-400">*</span>
                    </label>
                    <input wire:model="title" type="text" placeholder="Ex: Reunião de alinhamento Q2"
                           class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                  bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-400 dark:placeholder-slate-500
                                  focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                  transition lato-regular" />
                    @error('title') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                </div>

                {{-- Descrição --}}
                <div>
                    <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Descrição
                    </label>
                    <textarea wire:model="description" rows="2" placeholder="Pauta ou objetivo da reunião..."
                              class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                     bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                     placeholder-slate-400 dark:placeholder-slate-500
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                     transition lato-regular resize-none"></textarea>
                </div>

                {{-- Data e Hora --}}
                @php
                    $nowDate     = \Carbon\Carbon::now()->format('Y-m-d');
                    $nowTime     = \Carbon\Carbon::now()->format('H:i');
                    $startIsPast = $startDate && $startTime
                        && \Carbon\Carbon::parse("{$startDate} {$startTime}")->lt(\Carbon\Carbon::now());
                    $endBeforeStart = $startDate && $startTime && $endDate && $endTime
                        && \Carbon\Carbon::parse("{$endDate} {$endTime}")->lte(\Carbon\Carbon::parse("{$startDate} {$startTime}"));
                @endphp

                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                            Início <span class="text-red-400">*</span>
                        </label>
                        <input wire:model.live="startDate" type="date"
                               min="{{ $nowDate }}"
                               class="w-full px-3 py-2 text-sm rounded-lg border transition lato-regular mb-2
                                      {{ $startIsPast ? 'border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700' }}
                                      text-slate-800 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400" />
                        <input wire:model.live="startTime" type="time"
                               class="w-full px-3 py-2 text-sm rounded-lg border transition lato-regular
                                      {{ $startIsPast ? 'border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700' }}
                                      text-slate-800 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400" />
                        @if ($startIsPast)
                            <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                                Data/horário já passou.
                            </p>
                        @endif
                        @error('startDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @error('startTime') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                            Término <span class="text-red-400">*</span>
                        </label>
                        <input wire:model.live="endDate" type="date"
                               min="{{ $startDate ?: $nowDate }}"
                               class="w-full px-3 py-2 text-sm rounded-lg border transition lato-regular mb-2
                                      {{ $endBeforeStart ? 'border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700' }}
                                      text-slate-800 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400" />
                        <input wire:model.live="endTime" type="time"
                               class="w-full px-3 py-2 text-sm rounded-lg border transition lato-regular
                                      {{ $endBeforeStart ? 'border-red-400 dark:border-red-500 bg-red-50 dark:bg-red-900/20' : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700' }}
                                      text-slate-800 dark:text-white
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400" />
                        @if ($endBeforeStart)
                            <p class="mt-1 text-xs text-red-500 flex items-center gap-1">
                                <x-lucide-alert-circle class="w-3 h-3 shrink-0" />
                                Término deve ser após o início.
                            </p>
                        @endif
                        @error('endDate') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                        @error('endTime') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>

                {{-- Tipo de local --}}
                <div>
                    <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-2 uppercase tracking-wide">
                        Tipo de Local <span class="text-red-400">*</span>
                    </label>
                    <div class="flex gap-3">
                        <label class="flex-1 flex items-center gap-2.5 p-3 rounded-lg border cursor-pointer transition
                                      {{ $locationType === 'online'
                                          ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 dark:border-emerald-500'
                                          : 'border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/40' }}">
                            <input type="radio" wire:model.live="locationType" value="online" class="hidden" />
                            <x-lucide-video class="w-4 h-4 {{ $locationType === 'online' ? 'text-emerald-500' : 'text-slate-400' }}" />
                            <span class="text-sm lato-regular {{ $locationType === 'online' ? 'text-emerald-700 dark:text-emerald-300 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">Online</span>
                        </label>
                        <label class="flex-1 flex items-center gap-2.5 p-3 rounded-lg border cursor-pointer transition
                                      {{ $locationType === 'presencial'
                                          ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/30 dark:border-emerald-500'
                                          : 'border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/40' }}">
                            <input type="radio" wire:model.live="locationType" value="presencial" class="hidden" />
                            <x-lucide-map-pin class="w-4 h-4 {{ $locationType === 'presencial' ? 'text-emerald-500' : 'text-slate-400' }}" />
                            <span class="text-sm lato-regular {{ $locationType === 'presencial' ? 'text-emerald-700 dark:text-emerald-300 font-semibold' : 'text-slate-600 dark:text-slate-300' }}">Presencial</span>
                        </label>
                    </div>
                </div>

                {{-- Plataforma online --}}
                @if ($locationType === 'online')
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                            Plataforma <span class="text-red-400">*</span>
                        </label>
                        <div class="grid grid-cols-3 gap-2">
                            @foreach ([
                                ['google_meet', 'Google Meet', 'text-blue-600 dark:text-blue-400',     'border-blue-400 bg-blue-50 dark:bg-blue-900/30 dark:border-blue-500'],
                                ['zoom',        'Zoom',        'text-indigo-600 dark:text-indigo-400', 'border-indigo-400 bg-indigo-50 dark:bg-indigo-900/30 dark:border-indigo-500'],
                                ['teams',       'Teams',       'text-purple-600 dark:text-purple-400', 'border-purple-400 bg-purple-50 dark:bg-purple-900/30 dark:border-purple-500'],
                            ] as [$val, $lbl, $tc, $ac])
                            <label class="flex flex-col items-center gap-1 p-2.5 rounded-lg border cursor-pointer transition text-center
                                          {{ $onlinePlatform === $val ? $ac : 'border-slate-200 dark:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-700/40' }}">
                                <input type="radio" wire:model.live="onlinePlatform" value="{{ $val }}" class="hidden" />
                                @if ($val === 'google_meet')
                                    <x-lucide-video class="w-4 h-4 {{ $onlinePlatform === $val ? $tc : 'text-slate-400' }}" />
                                @elseif ($val === 'zoom')
                                    <x-lucide-video class="w-4 h-4 {{ $onlinePlatform === $val ? $tc : 'text-slate-400' }}" />
                                @else
                                    <x-lucide-monitor class="w-4 h-4 {{ $onlinePlatform === $val ? $tc : 'text-slate-400' }}" />
                                @endif
                                <span class="text-xs lato-regular {{ $onlinePlatform === $val ? $tc . ' font-semibold' : 'text-slate-500 dark:text-slate-400' }}">{{ $lbl }}</span>
                            </label>
                            @endforeach
                        </div>
                        @error('onlinePlatform') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                            Link da reunião
                        </label>
                        @php
                            $linkPlaceholder = match($onlinePlatform) {
                                'zoom'  => 'https://zoom.us/j/...',
                                'teams' => 'https://teams.microsoft.com/l/meetup-join/...',
                                default => 'https://meet.google.com/...',
                            };
                        @endphp
                        <input wire:model="onlineLink" type="url" placeholder="{{ $linkPlaceholder }}"
                               class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                      bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                      placeholder-slate-400 dark:placeholder-slate-500
                                      focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400 transition lato-regular" />
                        @error('onlineLink') <p class="mt-1 text-xs text-red-500">{{ $message }}</p> @enderror
                    </div>
                </div>
                @endif

                {{-- Sala presencial --}}
                @if ($locationType === 'presencial')
                <div>
                    <label class="flex items-center gap-1.5 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Sala
                        <span class="text-red-500 normal-case tracking-normal" title="Obrigatório para reuniões presenciais">*</span>
                    </label>

                    @php
                        $busyRoomIds   = [];
                        $busyRoomTimes = [];
                        if ($startDate && $startTime && $endDate && $endTime) {
                            try {
                                $fStart    = \Carbon\Carbon::parse("{$startDate} {$startTime}");
                                $fEnd      = \Carbon\Carbon::parse("{$endDate} {$endTime}");
                                $excludeId = $isEditing ? $editingId : null;

                                if ($fEnd->gt($fStart)) {
                                    // Conflitos em meetings
                                    $fromMeetings = \App\Models\Meeting::whereNotNull('room_id')
                                        ->where('status', '!=', 'cancelada')
                                        ->where('start_time', '<', $fEnd)
                                        ->where('end_time',   '>', $fStart)
                                        ->when($excludeId, fn ($q) => $q->where('id', '!=', $excludeId))
                                        ->get(['room_id', 'start_time', 'end_time']);

                                    // Conflitos em reservations (tela de salas)
                                    $fromReservations = \App\Models\Reservations::whereNotNull('room_id')
                                        ->where('status', '!=', 'cancelada')
                                        ->where('start_time', '<', $fEnd)
                                        ->where('end_time',   '>', $fStart)
                                        ->get(['room_id', 'start_time', 'end_time']);

                                    foreach ($fromMeetings->concat($fromReservations) as $c) {
                                        $rid = (string) $c->room_id;
                                        if (! isset($busyRoomTimes[$rid])) {
                                            $busyRoomIds[] = $rid;
                                            $busyRoomTimes[$rid] =
                                                $c->start_time->format('H:i') . '–' . $c->end_time->format('H:i');
                                        }
                                    }
                                }
                            } catch (\Exception) {}
                        }
                    @endphp

                    <div class="max-h-40 overflow-y-auto rounded-lg border {{ $errors->has('roomId') ? 'border-red-400 dark:border-red-500' : 'border-slate-200 dark:border-slate-600' }} bg-white dark:bg-slate-700 divide-y divide-slate-50 dark:divide-slate-600">

                        @forelse ($this->rooms as $room)
                            @php
                                $rid   = (string) $room->id;
                                $busy  = in_array($rid, $busyRoomIds);
                                $bTime = $busyRoomTimes[$rid] ?? null;
                                $isSel = (string)($roomId ?? '') === $rid;
                            @endphp
                            <label class="flex items-center gap-3 px-3 py-2.5 transition
                                          {{ $busy
                                              ? 'bg-red-50/60 dark:bg-red-900/20 cursor-not-allowed'
                                              : ($isSel ? 'bg-emerald-50 dark:bg-emerald-900/20 cursor-pointer' : 'hover:bg-slate-50 dark:hover:bg-slate-600/50 cursor-pointer') }}">
                                <input type="radio" wire:model.live="roomId" value="{{ $room->id }}"
                                       {{ $busy ? 'disabled' : '' }}
                                       class="w-4 h-4 border-slate-300 dark:border-slate-500 text-emerald-600 focus:ring-emerald-500/40
                                              {{ $busy ? 'cursor-not-allowed opacity-40' : 'cursor-pointer' }}" />

                                <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0
                                            {{ $busy ? 'bg-red-100 dark:bg-red-900/40' : ($isSel ? 'bg-emerald-100 dark:bg-emerald-900/40' : 'bg-emerald-50 dark:bg-slate-600') }}">
                                    <x-lucide-door-open class="w-3.5 h-3.5 {{ $busy ? 'text-red-400' : ($isSel ? 'text-emerald-500 dark:text-emerald-400' : 'text-emerald-500 dark:text-slate-400') }}" />
                                </div>

                                <div class="flex-1 min-w-0">
                                    <p class="text-sm lato-regular truncate
                                              {{ $busy ? 'text-red-500 dark:text-red-400' : ($isSel ? 'text-emerald-700 dark:text-emerald-300 font-semibold' : 'text-slate-700 dark:text-slate-200') }}">
                                        {{ $room->name }}
                                    </p>
                                    @if ($busy && $bTime)
                                        <p class="text-[11px] text-red-400 dark:text-red-500 lato-regular">Ocupada {{ $bTime }}</p>
                                    @endif
                                </div>

                                @if ($busy)
                                    <span class="text-[10px] font-semibold lato-bold px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 shrink-0 whitespace-nowrap">
                                        Ocupada
                                    </span>
                                @elseif ($isSel)
                                    <span class="text-[10px] font-semibold lato-bold px-1.5 py-0.5 rounded bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400 shrink-0">
                                        Selecionada
                                    </span>
                                @endif
                            </label>
                        @empty
                            <div class="flex flex-col items-center justify-center py-6 text-center px-4">
                                <x-lucide-door-closed class="w-6 h-6 text-slate-300 dark:text-slate-600 mb-1.5" />
                                <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular">
                                    Nenhuma sala cadastrada.
                                </p>
                            </div>
                        @endforelse
                    </div>

                    <p class="mt-1.5 text-[11px] text-slate-400 dark:text-slate-500 lato-regular">
                        Para reuniões presenciais é obrigatório escolher uma sala disponível.
                    </p>

                    @error('roomId')
                        <div class="mt-2 flex items-start gap-2 p-2.5 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
                            <x-lucide-alert-triangle class="w-4 h-4 text-red-500 dark:text-red-400 shrink-0 mt-0.5" />
                            <p class="text-xs text-red-600 dark:text-red-400 lato-regular leading-snug">{{ $message }}</p>
                        </div>
                    @enderror
                </div>
                @endif

                {{-- Participantes --}}
                <div>
                    <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Participantes
                    </label>

                    @error('participantIds')
                        <div class="mb-2 flex items-start gap-2 p-2.5 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
                            <x-lucide-user-x class="w-4 h-4 text-red-500 dark:text-red-400 shrink-0 mt-0.5" />
                            <p class="text-xs text-red-600 dark:text-red-400 lato-regular leading-snug">{{ $message }}</p>
                        </div>
                    @enderror

                    {{-- Campo de busca --}}
                    <div class="relative mb-1.5">
                        <x-lucide-search class="absolute left-2.5 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 dark:text-slate-500 pointer-events-none" />
                        <input
                            wire:model.live.debounce.250ms="participantSearch"
                            type="text"
                            placeholder="Buscar por nome ou cargo..."
                            class="w-full pl-8 pr-8 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                   bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                   placeholder-slate-400 dark:placeholder-slate-500
                                   focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                   transition lato-regular" />
                        @if (trim($participantSearch) !== '')
                            <button
                                wire:click="$set('participantSearch', '')"
                                class="absolute right-2.5 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition cursor-pointer">
                                <x-lucide-x class="w-3.5 h-3.5" />
                            </button>
                        @endif
                    </div>

                    @php
                        $busyInForm = [];
                        if ($startDate && $startTime && $endDate && $endTime) {
                            try {
                                $fStart = \Carbon\Carbon::parse("{$startDate} {$startTime}");
                                $fEnd   = \Carbon\Carbon::parse("{$endDate} {$endTime}");
                                if ($fEnd->gt($fStart) && count($participantIds)) {
                                    $excludeId = $isEditing ? $editingId : null;
                                    $busyInForm = \Illuminate\Support\Facades\DB::table('meeting_participants as mp')
                                        ->join('meetings as m', 'm.id', '=', 'mp.meeting_id')
                                        ->whereIn('mp.user_id', $participantIds)
                                        ->where('m.status', '!=', 'cancelada')
                                        ->where('m.start_time', '<', $fEnd)
                                        ->where('m.end_time',   '>', $fStart)
                                        ->when($excludeId, fn ($q) => $q->where('m.id', '!=', $excludeId))
                                        ->pluck('mp.user_id')
                                        ->unique()
                                        ->values()
                                        ->map(fn ($v) => (string) $v)
                                        ->toArray();
                                }
                            } catch (\Exception) {}
                        }
                    @endphp

                    <div class="max-h-36 overflow-y-auto rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 divide-y divide-slate-50 dark:divide-slate-600">
                        @forelse ($this->allUsers as $user)
                            @php $isBusy = in_array((string) $user->id, $busyInForm); @endphp
                            <label class="flex items-center gap-3 px-3 py-2 cursor-pointer transition
                                          {{ $isBusy ? 'bg-red-50/60 dark:bg-red-900/20' : 'hover:bg-slate-50 dark:hover:bg-slate-600/50' }}">
                                <input type="checkbox" wire:model="participantIds" value="{{ $user->id }}"
                                       class="w-4 h-4 rounded border-slate-300 dark:border-slate-500 text-emerald-600 focus:ring-emerald-500/40 cursor-pointer" />
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br flex items-center justify-center text-white text-[10px] font-bold shrink-0
                                            {{ $isBusy ? 'from-red-400 to-red-500' : 'from-emerald-400 to-teal-500' }}">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm lato-regular truncate
                                              {{ $isBusy ? 'text-red-600 dark:text-red-400' : 'text-slate-700 dark:text-slate-200' }}">
                                        {{ $user->name }}
                                    </p>
                                    @if ($user->position)
                                        <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ $user->position }}</p>
                                    @endif
                                </div>
                                @if ($isBusy)
                                    <span class="text-[10px] font-semibold lato-bold px-1.5 py-0.5 rounded bg-red-100 dark:bg-red-900/50 text-red-600 dark:text-red-400 shrink-0 whitespace-nowrap">
                                        Ocupado
                                    </span>
                                @endif
                            </label>
                        @empty
                            <div class="flex flex-col items-center justify-center py-6 text-center px-4">
                                <x-lucide-user-search class="w-6 h-6 text-slate-300 dark:text-slate-600 mb-1.5" />
                                <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular">
                                    Nenhum usuário encontrado para
                                    "<span class="font-semibold">{{ $participantSearch }}</span>"
                                </p>
                            </div>
                        @endforelse
                    </div>

                    {{-- Contador --}}
                    @if (count($participantIds) > 0)
                        <p class="mt-1.5 text-xs text-slate-400 dark:text-slate-500 lato-regular text-right">
                            {{ count($participantIds) }} participante{{ count($participantIds) !== 1 ? 's' : '' }} selecionado{{ count($participantIds) !== 1 ? 's' : '' }}
                        </p>
                    @endif
                </div>

                {{-- Pauta (Agenda Items) --}}
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">
                            Pauta da Reunião
                        </label>
                        @if (count($agendaItems) > 0)
                            <span class="text-[10px] font-semibold lato-bold px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400">
                                {{ count($agendaItems) }} {{ count($agendaItems) === 1 ? 'item' : 'itens' }}
                            </span>
                        @endif
                    </div>

                    {{-- Lista de itens já adicionados --}}
                    @if (count($agendaItems) > 0)
                        <div class="mb-2 rounded-lg border border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-700 divide-y divide-slate-100 dark:divide-slate-600 overflow-hidden">
                            @foreach ($agendaItems as $idx => $item)
                                @php
                                    $responsibleName = null;
                                    if (!empty($item['responsible_user_id'])) {
                                        $rid = (int) $item['responsible_user_id'];
                                        if ($rid === (int) auth()->id()) {
                                            $responsibleName = auth()->user()->name . ' (você)';
                                        } else {
                                            $u = $this->allUsers->firstWhere('id', $rid);
                                            $responsibleName = $u?->name;
                                        }
                                    }
                                @endphp
                                <div class="flex items-center gap-3 px-3 py-2.5">
                                    <div class="w-6 h-6 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center text-white text-[10px] font-bold shrink-0">
                                        {{ $idx + 1 }}
                                    </div>
                                    <div class="min-w-0 flex-1">
                                        <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate">
                                            {{ $item['title'] }}
                                        </p>
                                        <div class="flex items-center gap-2 mt-0.5">
                                            <span class="inline-flex items-center gap-1 text-[11px] text-slate-400 dark:text-slate-500 lato-regular">
                                                <x-lucide-clock class="w-3 h-3" />
                                                {{ (int) $item['duration_minutes'] }} min
                                            </span>
                                            @if ($responsibleName)
                                                <span class="inline-flex items-center gap-1 text-[11px] text-slate-400 dark:text-slate-500 lato-regular truncate">
                                                    <x-lucide-user class="w-3 h-3 shrink-0" />
                                                    <span class="truncate">{{ $responsibleName }}</span>
                                                </span>
                                            @endif
                                        </div>
                                    </div>
                                    <button type="button"
                                            wire:click="removeAgendaItemFromForm({{ $idx }})"
                                            class="shrink-0 w-7 h-7 flex items-center justify-center rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/30 transition cursor-pointer"
                                            title="Remover item">
                                        <x-lucide-trash-2 class="w-4 h-4" />
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="mb-2 flex items-center gap-2 px-3 py-2.5 rounded-lg border border-dashed border-slate-200 dark:border-slate-600 bg-slate-50/50 dark:bg-slate-700/40">
                            <x-lucide-list class="w-4 h-4 text-slate-400 dark:text-slate-500 shrink-0" />
                            <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular">
                                Nenhum item adicionado. Use os campos abaixo para montar a pauta.
                            </p>
                        </div>
                    @endif

                    {{-- Formulário para adicionar novo item --}}
                    <div class="rounded-lg border border-slate-200 dark:border-slate-600 bg-slate-50/70 dark:bg-slate-700/40 p-3 space-y-2">
                        <div class="grid grid-cols-12 gap-2">
                            {{-- Título --}}
                            <div class="col-span-12 sm:col-span-6">
                                <input
                                    wire:model="newAgendaItemTitle"
                                    wire:keydown.enter.prevent="addAgendaItemToForm"
                                    type="text"
                                    placeholder="Título do item da pauta"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                           bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                           placeholder-slate-400 dark:placeholder-slate-500
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                           transition lato-regular" />
                            </div>

                            {{-- Responsável --}}
                            <div class="col-span-8 sm:col-span-4">
                                <select
                                    wire:model="newAgendaItemResponsibleId"
                                    class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                           bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                           transition lato-regular cursor-pointer">
                                    <option value="">Responsável (opcional)</option>
                                    <option value="{{ auth()->id() }}">{{ auth()->user()->name }} (você)</option>
                                    @foreach ($this->allUsers as $user)
                                        @if (in_array((string) $user->id, $participantIds))
                                            <option value="{{ $user->id }}">{{ $user->name }}</option>
                                        @endif
                                    @endforeach
                                </select>
                            </div>

                            {{-- Duração --}}
                            <div class="col-span-4 sm:col-span-2">
                                <div class="relative">
                                    <input
                                        wire:model="newAgendaItemDuration"
                                        type="number"
                                        min="1"
                                        max="600"
                                        placeholder="15"
                                        class="w-full pl-3 pr-9 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                               bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                               placeholder-slate-400 dark:placeholder-slate-500
                                               focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                               transition lato-regular" />
                                    <span class="absolute right-2.5 top-1/2 -translate-y-1/2 text-[10px] font-semibold lato-bold text-slate-400 dark:text-slate-500 pointer-events-none">
                                        min
                                    </span>
                                </div>
                            </div>
                        </div>

                        {{-- Erros de validação --}}
                        @error('newAgendaItemTitle')
                            <div class="flex items-start gap-2 p-2 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
                                <x-lucide-alert-triangle class="w-3.5 h-3.5 text-red-500 dark:text-red-400 shrink-0 mt-0.5" />
                                <p class="text-xs text-red-600 dark:text-red-400 lato-regular leading-snug">{{ $message }}</p>
                            </div>
                        @enderror
                        @error('newAgendaItemDuration')
                            <div class="flex items-start gap-2 p-2 rounded-lg bg-red-50 dark:bg-red-900/30 border border-red-200 dark:border-red-800">
                                <x-lucide-alert-triangle class="w-3.5 h-3.5 text-red-500 dark:text-red-400 shrink-0 mt-0.5" />
                                <p class="text-xs text-red-600 dark:text-red-400 lato-regular leading-snug">{{ $message }}</p>
                            </div>
                        @enderror

                        <button type="button"
                                wire:click="addAgendaItemToForm"
                                wire:loading.attr="disabled"
                                wire:target="addAgendaItemToForm"
                                class="w-full flex items-center justify-center gap-1.5 px-3 py-2 text-xs lato-bold rounded-lg
                                       text-emerald-700 dark:text-emerald-300 bg-white dark:bg-slate-700
                                       border border-emerald-200 dark:border-emerald-800
                                       hover:bg-emerald-50 dark:hover:bg-emerald-900/40
                                       transition cursor-pointer disabled:opacity-60">
                            <x-lucide-plus class="w-3.5 h-3.5" />
                            Adicionar item à pauta
                        </button>
                    </div>
                </div>

                {{-- Anotações --}}
                <div>
                    <label class="block text-xs font-semibold lato-bold text-slate-600 dark:text-slate-400 mb-1.5 uppercase tracking-wide">
                        Anotações
                    </label>
                    <textarea wire:model="notes" rows="3" placeholder="Observações, links, documentos relevantes..."
                              class="w-full px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-600
                                     bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                     placeholder-slate-400 dark:placeholder-slate-500
                                     focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                     transition lato-regular resize-none"></textarea>
                </div>

            </div>{{-- fim body --}}

            {{-- Footer --}}
            <div class="flex items-center justify-end gap-2 sm:gap-3 px-4 sm:px-6 py-3 sm:py-4 border-t border-slate-100 dark:border-slate-700 shrink-0 bg-slate-50/50 dark:bg-slate-800/80">
                <button wire:click="closeModal"
                        class="flex-1 sm:flex-none px-4 py-2.5 sm:py-2 text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-600 sm:border-0
                               hover:bg-slate-100 dark:hover:bg-slate-700 rounded-lg transition cursor-pointer">
                    Cancelar
                </button>
                <button wire:click="saveReuniao"
                        wire:loading.attr="disabled"
                        class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 sm:px-5 py-2.5 sm:py-2 text-sm lato-bold text-white
                               bg-emerald-600 hover:bg-emerald-700 dark:bg-emerald-500 dark:hover:bg-emerald-600
                               rounded-lg transition cursor-pointer disabled:opacity-60">
                    <span wire:loading.remove wire:target="saveReuniao" class="flex items-center">
                        <x-lucide-check class="w-4 h-4 inline-block mr-1" />
                        {{ $isEditing ? 'Salvar Alterações' : 'Agendar Reunião' }}
                    </span>
                    <span wire:loading wire:target="saveReuniao" class="flex items-center gap-1.5">
                        <svg class="w-4 h-4 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                        </svg>
                        Salvando...
                    </span>
                </button>
            </div>

        </div>
    </div>
    @endif


    {{-- ════════════════════════════════ MODAL DE EXCLUSÃO ════════════════════════ --}}
    @if ($showDeleteModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm">
        <div class="w-full max-w-sm bg-white dark:bg-slate-800 rounded-2xl overflow-hidden">
            <div class="p-6">
                <div class="w-12 h-12 rounded-full bg-red-100 dark:bg-red-900/40 flex items-center justify-center mx-auto mb-4">
                    <x-lucide-trash-2 class="w-5 h-5 text-red-500" />
                </div>
                <h3 class="text-base font-bold lato-bold text-slate-800 dark:text-white text-center mb-2">
                    Excluir Reunião?
                </h3>
                <p class="text-sm text-slate-500 dark:text-slate-400 text-center lato-regular">
                    Esta ação é irreversível. A reunião será removida do calendário e todos os participantes perderão o acesso.
                </p>
            </div>
            <div class="flex gap-3 px-6 pb-6">
                <button wire:click="cancelDelete"
                        class="flex-1 px-4 py-2 text-sm lato-bold text-slate-600 dark:text-slate-300
                               border border-slate-200 dark:border-slate-600
                               hover:bg-slate-50 dark:hover:bg-slate-700 rounded-lg transition cursor-pointer">
                    Cancelar
                </button>
                <button wire:click="deleteReuniao"
                        class="flex-1 px-4 py-2 text-sm lato-bold text-white
                               bg-red-500 hover:bg-red-600 rounded-lg transition cursor-pointer">
                    Sim, excluir
                </button>
            </div>
        </div>
    </div>
    @endif

</div>
                                                                        
    
