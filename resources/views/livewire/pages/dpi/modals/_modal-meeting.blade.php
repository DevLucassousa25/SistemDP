    {{-- ════════════════════════════════════════════════════════════════
         MODAL: Agendar Reunião 1:1  (Bottom Sheet mobile / Dialog desktop)
    ═════════════════════════════════════════════════════════════════ --}}
    @if ($meetingModal)
        <div class="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-end sm:items-center justify-center"
             x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.set('meetingModal', false), 300); } }"
             x-init="requestAnimationFrame(() => open = true)"
             @click.self="close()">

            <div class="w-full sm:max-w-lg sm:mx-4 bg-white dark:bg-slate-800
                        rounded-t-3xl sm:rounded-2xl
                        border-0 sm:border sm:border-slate-200 sm:dark:border-slate-700
                        shadow-2xl flex flex-col
                        transition-transform duration-300 ease-out will-change-transform"
                 :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
                 style="max-height: 92dvh;"
                 @click.stop>

                {{-- Drag handle --}}
                <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                    <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                </div>

                {{-- Cabeçalho --}}
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <div class="flex items-center gap-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-100 dark:bg-indigo-900/30 flex items-center justify-center">
                            <x-lucide-calendar-plus class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
                        </div>
                        <h3 class="text-base lato-bold text-slate-800 dark:text-white">Agendar reunião 1:1</h3>
                    </div>
                    <button @click="close()" type="button"
                            class="w-7 h-7 flex items-center justify-center rounded-lg
                                   text-slate-400 hover:text-slate-700 hover:bg-slate-100
                                   dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Corpo scrollável --}}
                <div class="flex-1 overflow-y-auto px-6 py-5 space-y-4">

                    {{-- Título --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                            Título <span class="text-red-400">*</span>
                        </label>
                        <input wire:model="meetingTitle" type="text"
                               class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                      bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      border-slate-200 dark:border-slate-600 focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                        @error('meetingTitle') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                    </div>

                    {{-- Data + Hora --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                Data <span class="text-red-400">*</span>
                            </label>
                            <input wire:model="meetingDate" type="date"
                                   x-on:change="$wire.set('meetingDate', $event.target.value)"
                                   class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          border-slate-200 dark:border-slate-700
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                            @error('meetingDate') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                                Horário <span class="text-red-400">*</span>
                            </label>
                            <input wire:model="meetingStartTime" type="time"
                                   x-on:change="$wire.set('meetingStartTime', $event.target.value)"
                                   class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          border-slate-200 dark:border-slate-700
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                            @error('meetingStartTime') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    </div>

                    {{-- Duração --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Duração</label>
                        <select wire:model="meetingDuration"
                                x-on:change="$wire.set('meetingDuration', $event.target.value)"
                                class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                       bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                       border-slate-200 dark:border-slate-700
                                       focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition">
                            <option value="30">30 minutos</option>
                            <option value="45">45 minutos</option>
                            <option value="60">1 hora</option>
                            <option value="90">1h 30min</option>
                            <option value="120">2 horas</option>
                        </select>
                    </div>

                    {{-- Tipo de local: Online / Presencial --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">
                            Tipo de reunião
                        </label>
                        <div class="inline-flex rounded-xl border border-slate-200 dark:border-slate-700 overflow-hidden w-full">
                            <button wire:click="$set('meetingLocationType','online')" type="button"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-sm transition cursor-pointer
                                           {{ $meetingLocationType === 'online'
                                               ? 'bg-indigo-600 text-white lato-bold'
                                               : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <x-lucide-video class="w-3.5 h-3.5" /> Online
                            </button>
                            <button wire:click="$set('meetingLocationType','presencial')" type="button"
                                    class="flex-1 flex items-center justify-center gap-1.5 py-2.5 text-sm transition cursor-pointer
                                           {{ $meetingLocationType === 'presencial'
                                               ? 'bg-indigo-600 text-white lato-bold'
                                               : 'bg-white dark:bg-slate-800 text-slate-500 dark:text-slate-400 hover:bg-slate-50 dark:hover:bg-slate-700' }}">
                                <x-lucide-map-pin class="w-3.5 h-3.5" /> Presencial
                            </button>
                        </div>
                    </div>

                    {{-- Online: plataforma + link --}}
                    @if ($meetingLocationType === 'online')
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Plataforma</label>
                            <select wire:model="meetingPlatform"
                                    class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                           bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                           border-slate-200 dark:border-slate-700
                                           focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition">
                                <option value="google_meet">Google Meet</option>
                                <option value="teams">Microsoft Teams</option>
                                <option value="zoom">Zoom</option>
                                <option value="outro">Outro</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-1.5">Link da reunião</label>
                            <input wire:model="meetingLink" type="url"
                                   placeholder="https://meet.google.com/..."
                                   class="w-full px-3.5 py-2.5 text-sm lato-regular rounded-xl border
                                          bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          border-slate-200 dark:border-slate-700
                                          focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition" />
                            @error('meetingLink') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                        </div>
                    @endif

                    {{-- Presencial: seleção de sala --}}
                    @if ($meetingLocationType === 'presencial')
                        <div>
                            <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">
                                Sala <span class="text-red-400">*</span>
                            </label>

                            @if (! $meetingDate || ! $meetingStartTime)
                                <div class="text-xs text-amber-600 dark:text-amber-400 bg-amber-50 dark:bg-amber-900/20
                                            border border-amber-200 dark:border-amber-700 rounded-xl px-3 py-2">
                                    Informe a data e horário para ver as salas disponíveis.
                                </div>
                            @else
                                <div wire:loading wire:target="meetingDate,meetingStartTime,meetingDuration,meetingLocationType"
                                     class="flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400 py-2">
                                    <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" class="opacity-25"></circle>
                                        <path fill="currentColor" class="opacity-75" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                                    </svg>
                                    Verificando disponibilidade…
                                </div>
                                <div wire:loading.remove wire:target="meetingDate,meetingStartTime,meetingDuration,meetingLocationType"
                                     class="grid grid-cols-1 gap-2">
                                    @foreach ($this->availableRoomsForMeeting as $room)
                                        @php $isAvail = $room->getAttribute('is_available'); @endphp
                                        <button
                                            @if ($isAvail) wire:click="$set('meetingRoomId', {{ $room->id }})" @endif
                                            type="button"
                                            class="flex items-center gap-3 p-3 rounded-xl border-2 text-left transition
                                                   {{ ! $isAvail
                                                       ? 'opacity-50 cursor-not-allowed border-slate-100 dark:border-slate-700'
                                                       : ($meetingRoomId === $room->id
                                                           ? 'border-indigo-500 bg-indigo-50 dark:bg-indigo-900/20 cursor-pointer'
                                                           : 'border-slate-200 dark:border-slate-600 bg-white dark:bg-slate-800 hover:border-indigo-300 cursor-pointer') }}">
                                            <div class="w-5 h-5 rounded-full border-2 shrink-0 flex items-center justify-center
                                                        {{ $meetingRoomId === $room->id
                                                            ? 'border-indigo-500 bg-indigo-500'
                                                            : 'border-slate-300 dark:border-slate-600' }}">
                                                @if ($meetingRoomId === $room->id)
                                                    <x-lucide-check class="w-3 h-3 text-white" />
                                                @endif
                                            </div>
                                            <div class="flex-1 min-w-0">
                                                <div class="flex items-center gap-2 flex-wrap">
                                                    <p class="text-sm lato-bold text-slate-800 dark:text-slate-100">{{ $room->name }}</p>
                                                    <span class="text-xs px-1.5 py-0.5 rounded-full
                                                        {{ $isAvail
                                                            ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                                            : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-400' }}">
                                                        {{ $isAvail ? 'Disponível' : 'Ocupada' }}
                                                    </span>
                                                </div>
                                                <div class="flex items-center gap-3 mt-0.5 text-xs text-slate-400 flex-wrap">
                                                    <span><x-lucide-users class="w-3 h-3 inline" /> {{ $room->capacity }}</span>
                                                    @if ($room->has_video_conference) <span><x-lucide-video class="w-3 h-3 inline" /> Vídeo</span> @endif
                                                    @if ($room->has_projector) <span><x-lucide-projector class="w-3 h-3 inline" /> Projetor</span> @endif
                                                    @if ($room->has_whiteboard) <span><x-lucide-pencil-ruler class="w-3 h-3 inline" /> Quadro</span> @endif
                                                    @if ($room->has_wifi) <span><x-lucide-wifi class="w-3 h-3 inline" /> Wi-Fi</span> @endif
                                                </div>
                                            </div>
                                        </button>
                                    @endforeach
                                    @if ($this->availableRoomsForMeeting->isEmpty())
                                        <p class="text-xs text-slate-400 text-center py-3">Nenhuma sala cadastrada.</p>
                                    @endif
                                </div>
                            @error('meetingRoomId') <p class="mt-1 text-xs text-red-400 lato-regular">{{ $message }}</p> @enderror
                        @endif
                        {{-- /if meetingDate --}}
                    @endif
                    {{-- /presencial --}}

                </div>
                {{-- /body scrollável --}}

                {{-- Rodapé --}}
                <div class="shrink-0 px-6 py-4 border-t border-slate-100 dark:border-slate-700
                            bg-white dark:bg-slate-800 flex gap-3">
                    <button wire:click="$set('meetingModal', false)" type="button"
                            class="flex-1 py-2.5 text-sm lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                                   text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="saveMeeting" type="button"
                            class="flex-1 py-2.5 text-sm lato-bold rounded-xl
                                   bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                                   text-white shadow-md shadow-blue-500/20 transition cursor-pointer">
                        {{ isset($editMeetingId) && $editMeetingId ? 'Salvar alterações' : 'Criar reunião' }}
                    </button>
                </div>

            </div>
        </div>
    @endif
