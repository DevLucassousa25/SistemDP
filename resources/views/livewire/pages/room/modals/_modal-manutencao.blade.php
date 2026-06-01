    {{-- Modal Manuten
o --}}
    @if ($modalManutencao)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div wire:click="fecharModalManutencao" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>
            <div class="relative bg-white rounded-2xl shadow-2xl w-full max-w-md flex flex-col z-10">
                {{-- Header --}}
                <div class="flex items-start justify-between p-6 pb-4 border-b border-gray-100">
                    <div>
                        <h2 class="text-lg font-semibold text-gray-900">Definir Manuten
o</h2>
                        <p class="text-sm text-gray-400 mt-0.5">Informe o per
odo em que a sala ficar
 indispon
vel</p>
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
                            Durante este per
odo a sala ficar
 <strong>indispon
vel para reservas</strong>.
                            Certifique-se de que n
 agendamentos futuros antes de confirmar.
                        </p>
                    </div>
                    {{-- In
cio --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
                            In
cio da Manuten
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
                            T
rmino da Manuten
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
                        <span wire:loading.remove wire:target="definirManutencao">Confirmar Manuten
o</span>
                        <span wire:loading wire:target="definirManutencao">
                            <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif
