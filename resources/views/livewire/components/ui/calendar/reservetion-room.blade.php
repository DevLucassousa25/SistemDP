<div x-data="calendarComponent(@js($reservas), {{ $mes }}, {{ $ano }})" class="bg-white border border-gray-100 rounded-xl overflow-hidden">

    {{-- Stats --}}
    <div class="grid grid-cols-3 divide-x divide-gray-100 border-b border-gray-100">
        <div class="px-4 py-3 text-center">
            <p class="text-lg font-medium text-gray-900">{{ $stats['total'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">no mês</p>
        </div>
        <div class="px-4 py-3 text-center">
            <p class="text-lg font-medium text-green-700">{{ $stats['confirmadas'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">confirmadas</p>
        </div>
        <div class="px-4 py-3 text-center">
            <p class="text-lg font-medium text-red-600">{{ $stats['canceladas'] }}</p>
            <p class="text-xs text-gray-500 mt-0.5">canceladas</p>
        </div>
    </div>

    {{-- Header com navegação --}}
    <div class="flex items-center justify-between px-4 py-3 border-b border-gray-100">
        <div class="flex items-center gap-2">
            <button wire:click="mesAnterior"
                class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer">
                <x-lucide-chevron-left class="w-4 h-4 text-gray-600" />
            </button>
            <span class="text-sm font-medium text-gray-900 capitalize min-w-[130px] text-center"
                x-text="monthName + ' ' + year"></span>
            <button wire:click="proximoMes"
                class="w-8 h-8 flex items-center justify-center rounded-lg border border-gray-200 hover:bg-gray-50 transition-colors cursor-pointer">
                <x-lucide-chevron-right class="w-4 h-4 text-gray-600" />
            </button>
        </div>

        {{-- Legenda desktop --}}
        <div class="hidden sm:flex items-center gap-4 text-xs text-gray-500">
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-green-600 inline-block"></span>
                Confirmada</span>
            <span class="flex items-center gap-1.5"><span
                    class="w-2 h-2 rounded-full bg-yellow-500 inline-block"></span> Pendente</span>
            <span class="flex items-center gap-1.5"><span class="w-2 h-2 rounded-full bg-red-500 inline-block"></span>
                Cancelada</span>
        </div>
    </div>

    {{-- ============================================================ --}}
    {{-- LISTVIEW — mobile only                                       --}}
    {{-- ============================================================ --}}
    <div class="block sm:hidden divide-y divide-gray-100">
        <template x-for="group in listGroups" :key="group.date">
            <div>
                {{-- Cabeçalho do dia --}}
                <div class="flex items-center gap-3 px-4 pt-3 pb-2">
                    <div class="w-10 h-10 rounded-full flex flex-col items-center justify-center flex-shrink-0 border"
                        :class="group.isToday ?
                            'bg-green-500 border-green-500' :
                            'bg-gray-50 border-gray-200'">
                        <span class="text-sm font-medium leading-none"
                            :class="group.isToday ? 'text-white' : 'text-gray-800'" x-text="group.day"></span>
                        <span class="text-[9px] leading-none mt-0.5"
                            :class="group.isToday ? 'text-green-100' : 'text-gray-400'"
                            x-text="group.weekdayShort"></span>
                    </div>
                    <span class="text-xs text-gray-500 capitalize" x-text="group.weekdayLong"></span>
                </div>

                {{-- Eventos do dia --}}
                <div class="px-4 pb-3 pl-[4.5rem] flex flex-col gap-2">
                    <template x-for="ev in group.events" :key="ev.title + ev.start">
                        <div class="flex items-center gap-2 border border-gray-100 rounded-lg p-2.5 bg-white">
                            {{-- Barra colorida --}}
                            <div class="w-1 h-8 rounded-full flex-shrink-0"
                                :class="{
                                    'bg-green-600': ev.status === 'confirmada',
                                    'bg-red-500': ev.status === 'cancelada',
                                    'bg-yellow-500': ev.status === 'pendente',
                                }">
                            </div>
                            {{-- Info --}}
                            <div class="flex-1 min-w-0">
                                <p class="text-sm font-medium text-gray-900 truncate" x-text="ev.title"></p>
                                <p class="text-xs text-gray-500 mt-0.5" x-text="ev.start + ' — ' + ev.end"></p>
                            </div>
                            {{-- Badge status --}}
                            <span class="text-[10px] px-2 py-0.5 rounded-full flex-shrink-0 capitalize"
                                :class="{
                                    'bg-green-100 text-green-800': ev.status === 'confirmada',
                                    'bg-red-100 text-red-700': ev.status === 'cancelada',
                                    'bg-yellow-100 text-yellow-800': ev.status === 'pendente',
                                }"
                                x-text="ev.status">
                            </span>
                        </div>
                    </template>
                </div>
            </div>
        </template>

        {{-- Vazio --}}
        <template x-if="listGroups.length === 0">
            <div class="py-10 text-center text-sm text-gray-400">Nenhuma reserva neste mês</div>
        </template>
    </div>

    {{-- ============================================================ --}}
    {{-- CALENDAR GRID — desktop only                                 --}}
    {{-- ============================================================ --}}
    {{-- CALENDAR GRID — desktop only --}}
    <div class="hidden sm:block">
        <div class="grid grid-cols-7 text-center px-4 py-2">
            <template x-for="d in weekDays">
                <div class="text-[10px] font-medium text-gray-400 uppercase tracking-wide" x-text="d"></div>
            </template>
        </div>

        <div class="grid grid-cols-7 gap-1 px-4 pb-4">
            <template x-for="_ in blankDays">
                <div></div>
            </template>

            <template x-for="(day, idx) in daysInMonth" :key="day">
                <div class="relative border rounded-lg min-h-[90px] p-1.5 flex flex-col gap-1 transition-colors group"
                    :class="[
                        isToday(day) ? 'border-blue-400' : 'border-gray-100',
                        getReservations(day).length ? 'cursor-pointer hover:border-gray-300 hover:bg-gray-50' : ''
                    ]">

                    {{-- Número do dia --}}
                    <span
                        class="text-xs font-medium w-6 h-6 flex items-center justify-center rounded-full flex-shrink-0"
                        :class="isToday(day) ? 'bg-blue-500 text-white' : 'text-gray-600'" x-text="day"></span>

                    {{-- Chips --}}
                    <template x-for="ev in getReservations(day).slice(0, 2)">
                        <div class="text-[10px] px-1 py-0.5 rounded flex gap-1 items-center overflow-hidden"
                            :class="statusColor(ev.status)">
                            <span class="font-medium flex-shrink-0" x-text="ev.start"></span>
                            <span class="truncate" x-text="ev.title"></span>
                        </div>
                    </template>
                    <template x-if="getReservations(day).length > 2">
                        <span class="text-[9px] text-gray-400 px-1">
                            +<span x-text="getReservations(day).length - 2"></span> mais
                        </span>
                    </template>

                    {{-- TOOLTIP --}}
                    <template x-if="getReservations(day).length > 0">
                        <div class="absolute z-50 w-56 max-h-64 overflow-y-auto bg-white border border-gray-200 rounded-xl shadow-sm p-3
    invisible opacity-0 group-hover:visible group-hover:opacity-100
    transition-opacity duration-150 pointer-events-auto"
                            :class="tooltipPosition(idx)">

                            {{-- Setinha --}}
                            <div class="absolute w-2 h-2 bg-white border-l border-t border-gray-200 rotate-45"
                                :class="tooltipArrow(idx)"></div>

                            {{-- Cabeçalho --}}
                            <p class="text-xs font-medium text-gray-500 mb-2 pb-2 border-b border-gray-100 capitalize"
                                x-text="tooltipDateLabel(day)"></p>

                            {{-- Eventos --}}
                            <div class="flex flex-col divide-y divide-gray-100">
                                <template x-for="ev in getReservations(day)">
                                    <div class="flex gap-2 items-start py-2 first:pt-0 last:pb-0">
                                        {{-- Barra colorida --}}
                                        <div class="w-0.5 self-stretch rounded-full flex-shrink-0 mt-0.5"
                                            :class="{
                                                'bg-green-600': ev.status === 'confirmada',
                                                'bg-red-500': ev.status === 'cancelada',
                                                'bg-yellow-500': ev.status === 'pendente',
                                            }">
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs font-medium text-gray-900 truncate" x-text="ev.title"></p>
                                            <p class="text-[11px] text-gray-500 mt-0.5"
                                                x-text="ev.start + ' — ' + ev.end"></p>
                                            <div class="flex items-center gap-1.5 mt-1 flex-wrap">
                                                <span class="text-[10px] px-1.5 py-0.5 rounded-full capitalize"
                                                    :class="{
                                                        'bg-green-100 text-green-800': ev.status === 'confirmada',
                                                        'bg-red-100 text-red-700': ev.status === 'cancelada',
                                                        'bg-yellow-100 text-yellow-800': ev.status === 'pendente',
                                                    }"
                                                    x-text="ev.status"></span>
                                                <span class="text-[10px] text-gray-400"
                                                    x-text="ev.attendees + ' participante' + (ev.attendees !== 1 ? 's' : '')"></span>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
            </template>
        </div>
    </div>
</div>

<script>
    function calendarComponent(reservas, mesInicial, anoInicial) {
        return {
            reservas,
            month: mesInicial - 1,
            year: anoInicial,
            today: new Date(),
            weekDays: ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'],
            weekDaysLong: ['domingo', 'segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado'],

            get monthName() {
                return new Date(this.year, this.month).toLocaleString('pt-BR', {
                    month: 'long'
                });
            },
            get daysInMonth() {
                return new Date(this.year, this.month + 1, 0).getDate();
            },
            get blankDays() {
                return new Date(this.year, this.month, 1).getDay();
            },

            // Grupos para a listview (apenas dias com reservas)
            get listGroups() {
                const days = new Date(this.year, this.month + 1, 0).getDate();
                const groups = [];
                for (let d = 1; d <= days; d++) {
                    const evs = this.getReservations(d);
                    if (!evs.length) continue;
                    const dow = new Date(this.year, this.month, d).getDay();
                    groups.push({
                        date: this.formatDate(d),
                        day: d,
                        isToday: this.isToday(d),
                        weekdayShort: this.weekDays[dow],
                        weekdayLong: this.weekDaysLong[dow],
                        events: evs,
                    });
                }
                return groups;
            },

            formatDate(day) {
                return `${this.year}-${String(this.month + 1).padStart(2,'0')}-${String(day).padStart(2,'0')}`;
            },
            getReservations(day) {
                return this.reservas[this.formatDate(day)] ?? [];
            },
            isToday(day) {
                return day === this.today.getDate() &&
                    this.month === this.today.getMonth() &&
                    this.year === this.today.getFullYear();
            },
            statusColor(status) {
                return {
                    'confirmada': 'bg-green-100 text-green-800',
                    'pendente': 'bg-yellow-100 text-yellow-800',
                    'cancelada': 'bg-red-100 text-red-700',
                } [status] ?? 'bg-gray-100 text-gray-600';
            },

            tooltipPosition(idx) {
                const col = (this.blankDays + idx) % 7;
                const totalRows = Math.ceil((this.blankDays + this.daysInMonth) / 7);
                const row = Math.floor((this.blankDays + idx) / 7);
                const isLastRows = row >= totalRows - 2;
                const base = isLastRows ?
                    'bottom-full mb-2' // abre para cima
                    :
                    'top-full mt-2'; // abre para baixo
                const align = col <= 1 ?
                    'left-0' // alinha à esquerda
                    :
                    col >= 5 ?
                    'right-0' // alinha à direita
                    :
                    'left-1/2 -translate-x-1/2'; // centralizado
                return `${base} ${align}`;
            },

            tooltipArrow(idx) {
                const col = (this.blankDays + idx) % 7;
                const totalRows = Math.ceil((this.blankDays + this.daysInMonth) / 7);
                const row = Math.floor((this.blankDays + idx) / 7);
                const isLastRows = row >= totalRows - 2;
                const v = isLastRows ? 'bottom-[-5px] border-b border-r border-t-0 border-l-0' : 'top-[-5px]';
                const h = col <= 1 ? 'left-4' : col >= 5 ? 'right-4' : 'left-1/2 -translate-x-1/2';
                return `${v} ${h}`;
            },

            tooltipDateLabel(day) {
                const dow = new Date(this.year, this.month, day).getDay();
                const wdays = ['domingo', 'segunda', 'terça', 'quarta', 'quinta', 'sexta', 'sábado'];
                const months = ['janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
                    'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
                ];
                return `${wdays[dow]}, ${day} de ${months[this.month]}`;
            },
        }
    }
</script>
