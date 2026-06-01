    <!-- Modal de Nova Reserva -->
    <div x-data
         x-effect="document.body.style.overflow = $wire.modalReserva ? 'hidden' : ''">
        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4 transition-all duration-300
                    {{ $modalReserva ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none' }}">
            {{-- Backdrop --}}
            <div wire:click="fecharModalReserva"
                 class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            {{-- Modal Box --}}
            <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg max-h-[95vh] sm:max-h-[92vh]
                        flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden
                        transform transition-all duration-300
                        {{ $modalReserva ? 'translate-y-0 opacity-100 scale-100' : 'translate-y-4 opacity-0 scale-95' }}">

                {{-- Alça de arraste (mobile) --}}
                <div class="flex justify-center pt-2.5 pb-1 sm:hidden flex-shrink-0">
                    <div class="w-10 h-1 bg-slate-200 dark:bg-slate-600 rounded-full"></div>
                </div>

                {{-- Header --}}
                <div class="flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-3 sm:gap-4 px-4 sm:px-6 py-4 sm:py-5">
                        <div class="shrink-0 w-10 h-10 sm:w-11 sm:h-11 rounded-xl
                                    bg-gradient-to-br from-indigo-500 to-violet-600
                                    flex items-center justify-center shadow-sm shadow-indigo-200/50">
                            <x-lucide-calendar-plus class="w-5 h-5 text-white" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h2 class="text-base sm:text-lg lato-black font-bold text-slate-800 dark:text-white leading-tight">
                                Nova Reserva
                            </h2>
                            <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular mt-0.5">
                                {{ $sala->name }}
                            </p>
                        </div>
                        <button wire:click="fecharModalReserva"
                            class="shrink-0 w-8 h-8 flex items-center justify-center rounded-xl
                                   text-slate-400 hover:text-slate-600 dark:hover:text-slate-200
                                   hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="px-4 sm:px-6 py-4 sm:py-5 space-y-4 overflow-y-auto flex-1 bg-slate-50/60 dark:bg-slate-800/60">

                    {{-- Seção 1: Dados da Reserva --}}
                    <section class="bg-white dark:bg-slate-700/40 rounded-2xl border border-slate-100 dark:border-slate-700 p-4 sm:p-5 space-y-4">
                        <div class="flex items-center gap-2.5">
                            <div class="w-7 h-7 rounded-lg bg-gradient-to-br from-indigo-50 to-violet-50 dark:bg-indigo-900/30 flex items-center justify-center">
                                <x-lucide-calendar class="w-3.5 h-3.5 text-indigo-500 dark:text-indigo-400" />
                            </div>
                            <h3 class="text-xs lato-bold font-semibold text-slate-600 dark:text-slate-300 uppercase tracking-wider">
                                Dados da Reserva
                            </h3>
                        </div>

                        {{-- Título --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1 text-xs lato-bold text-slate-600 dark:text-slate-300">
                                Título <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-type class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="text" wire:model="titulo" placeholder="Ex: Reunião de planejamento"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-800
                                           text-slate-800 dark:text-white placeholder-slate-400
                                           focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition
                                           @error('titulo') border border-red-400 bg-red-50/30 @else border border-slate-200 dark:border-slate-600 hover:border-slate-300 @enderror" />
                            </div>
                            @error('titulo')
                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                    <x-lucide-alert-circle class="w-3 h-3" /> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Data --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1 text-xs lato-bold text-slate-600 dark:text-slate-300">
                                Data <span class="text-red-500">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-calendar class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="date" wire:model.live="dataReserva"
                                    class="w-full pl-9 pr-3 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-800
                                           text-slate-800 dark:text-white
                                           focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition
                                           @error('dataReserva') border border-red-400 @else border border-slate-200 dark:border-slate-600 hover:border-slate-300 @enderror" />
                            </div>
                            @error('dataReserva')
                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                    <x-lucide-alert-circle class="w-3 h-3" /> {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Início + Término --}}
                        <div class="grid grid-cols-2 gap-3">
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1 text-xs lato-bold text-slate-600 dark:text-slate-300">
                                    Início <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <x-lucide-clock class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                    <input type="time" wire:model="horaInicio"
                                        class="w-full pl-9 pr-3 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-800
                                               text-slate-800 dark:text-white
                                               focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition
                                               @error('horaInicio') border border-red-400 @else border border-slate-200 dark:border-slate-600 hover:border-slate-300 @enderror" />
                                </div>
                                @error('horaInicio')
                                    <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                        <x-lucide-alert-circle class="w-3 h-3" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                            <div class="space-y-1.5">
                                <label class="flex items-center gap-1 text-xs lato-bold text-slate-600 dark:text-slate-300">
                                    Término <span class="text-red-500">*</span>
                                </label>
                                <div class="relative">
                                    <x-lucide-clock class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                    <input type="time" wire:model="horaFim"
                                        class="w-full pl-9 pr-3 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-800
                                               text-slate-800 dark:text-white
                                               focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 transition
                                               @error('horaFim') border border-red-400 @else border border-slate-200 dark:border-slate-600 hover:border-slate-300 @enderror" />
                                </div>
                                @error('horaFim')
                                    <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                        <x-lucide-alert-circle class="w-3 h-3" /> {{ $message }}
                                    </p>
                                @enderror
                            </div>
                        </div>

                        {{-- Aviso de feriados/eventos --}}
                        @if($dataReserva)
                            <x-calendario-aviso :data="$dataReserva" />
                        @endif

                        {{-- Descrição --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1.5 text-xs lato-bold text-slate-600 dark:text-slate-300">
                                Descrição
                                <span class="font-normal text-slate-400 lato-regular">(opcional)</span>
                            </label>
                            <div class="relative">
                                <x-lucide-file-text class="absolute left-3 top-3 w-4 h-4 text-slate-400 pointer-events-none" />
                                <textarea wire:model="descricao" rows="2" placeholder="Detalhes da reunião..."
                                    class="w-full pl-9 pr-3 py-2.5 text-sm lato-regular rounded-xl bg-white dark:bg-slate-800
                                           text-slate-800 dark:text-white placeholder-slate-400
                                           border border-slate-200 dark:border-slate-600 hover:border-slate-300
                                           focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400
                                           transition resize-none"></textarea>
                            </div>
                        </div>
                    </section>

                </div>

                {{-- Footer --}}
                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                    <div class="flex flex-col gap-3">
                        <p class="flex items-center gap-1.5 text-xs text-slate-400 lato-regular">
                            <x-lucide-info class="w-3.5 h-3.5 shrink-0" />
                            Campos com <span class="text-red-500 font-semibold mx-0.5">*</span> são obrigatórios
                        </p>
                        <div class="flex gap-2 sm:gap-3">
                            <button wire:click="fecharModalReserva"
                                class="flex-1 sm:flex-none px-4 sm:px-5 py-2.5 text-sm lato-bold rounded-xl
                                       text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700
                                       border border-slate-200 dark:border-slate-600
                                       hover:bg-slate-50 dark:hover:bg-slate-600 transition cursor-pointer">
                                Cancelar
                            </button>
                            <button wire:click="salvarReserva" wire:loading.attr="disabled"
                                class="flex-1 sm:flex-none px-4 sm:px-6 py-2.5 text-sm lato-bold rounded-xl
                                       bg-gradient-to-r from-indigo-500 to-violet-600 hover:from-indigo-600 hover:to-violet-700
                                       shadow-sm shadow-indigo-500/25 text-white flex items-center justify-center gap-2
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
