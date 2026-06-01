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
                            <p class="text-xs text-gray-400 mt-0.5">Esta a
o poder
 ser desfeita</p>
                        </div>
                    </div>
                    <button wire:click="fecharModalCancelar"
                        class="text-gray-400 hover:text-gray-600 transition cursor-pointer mt-0.5">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>
                {{-- Detalhes da reserva --}}
                <div class="p-6 flex flex-col gap-4">
                    <p class="text-sm text-gray-600">Voc
 est
 prestes a cancelar a seguinte reserva:</p>
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
                            Ao confirmar, a reserva ser
 cancelada e a sala ficar
 dispon
vel para outros agendamentos
                            neste hor
rio.
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
