<div>
    @if ($modalAberto)
        <div x-data x-init="document.body.style.overflow = 'hidden'" x-destroy="document.body.style.overflow = ''"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4">

            {{-- Backdrop --}}
            <div wire:click="fecharModal" class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

            {{-- Modal Box — bottom-sheet em mobile, centralizado no desktop --}}
            <div class="relative bg-white w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh]
                        rounded-t-2xl sm:rounded-2xl shadow-2xl flex flex-col z-10">

                {{-- Alça de arraste (mobile) --}}
                <div class="flex justify-center pt-2.5 pb-1 sm:hidden flex-shrink-0">
                    <div class="w-10 h-1 bg-gray-200 rounded-full"></div>
                </div>

                {{-- Header --}}
                <div class="flex items-start justify-between px-4 pt-3 pb-3 sm:p-6 sm:pb-4 border-b border-gray-100 flex-shrink-0">
                    <div>
                        <h2 class="text-base sm:text-lg font-semibold text-gray-900 lato-bold">
                            {{ $salaId ? 'Editar Sala' : 'Nova Sala' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-gray-400 mt-0.5 lato-regular hidden sm:block">
                            {{ $salaId ? 'Atualize as informações e fotos da sala' : 'Cadastre uma nova sala de reunião' }}
                        </p>
                    </div>
                    <button wire:click="fecharModal"
                        class="text-gray-400 hover:text-gray-600 transition mt-0.5 cursor-pointer flex-shrink-0 ml-2">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-4 py-4 sm:p-6 flex flex-col gap-4 sm:gap-6 overflow-y-auto flex-1">

                    {{-- ── Fotos ── --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-0.5 lato-bold">Fotos da Sala</h3>
                        <p class="text-xs text-gray-400 mb-3 lato-regular">Adicione até 4 fotos para ilustrar a sala</p>

                        @php
                            $totalExistentes = count($imagensExistentes);
                            $slotsNovos      = max(0, 4 - $totalExistentes);
                        @endphp

                        <div class="grid grid-cols-2 gap-2 sm:gap-3">

                            {{-- Imagens já salvas (modo edição) --}}
                            @foreach ($imagensExistentes as $img)
                                <div class="relative group">
                                    <div class="relative rounded-xl overflow-hidden aspect-video bg-gray-100">
                                        <img src="{{ asset('storage/' . $img['path']) }}" class="w-full h-full object-cover" />
                                        <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                            <button wire:click="removerImagemExistente({{ $img['id'] }})"
                                                class="p-1.5 bg-red-500 rounded-lg text-white hover:bg-red-600 transition cursor-pointer">
                                                <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endforeach

                            {{-- Slots de novos uploads --}}
                            @for ($i = 0; $i < $slotsNovos; $i++)
                                <div x-data="{ dragging: false }" @dragover.prevent="dragging = true"
                                    @dragleave="dragging = false" @drop.prevent="dragging = false"
                                    class="relative group">

                                    @if (isset($fotos[$i]) && $fotos[$i])
                                        <div class="relative rounded-xl overflow-hidden aspect-video bg-gray-100">
                                            <img src="{{ $fotos[$i]->temporaryUrl() }}" class="w-full h-full object-cover" />
                                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition flex items-center justify-center gap-2">
                                                <button wire:click="removerFoto({{ $i }})"
                                                    class="p-1.5 bg-red-500 rounded-lg text-white hover:bg-red-600 transition cursor-pointer">
                                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                                </button>
                                            </div>
                                        </div>
                                    @else
                                        <label
                                            class="relative flex flex-col items-center justify-center gap-1 sm:gap-1.5 aspect-video rounded-xl border-2 border-dashed cursor-pointer transition"
                                            :class="dragging ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200 bg-gray-50 hover:border-emerald-300 hover:bg-emerald-50/50'">

                                            <div wire:loading.remove wire:target="fotos.{{ $i }}"
                                                class="flex flex-col items-center gap-1 sm:gap-1.5">
                                                <x-lucide-image class="w-5 h-5 sm:w-6 sm:h-6 text-gray-300 group-hover:text-emerald-400 transition" />
                                                <span class="text-[10px] sm:text-xs text-gray-400 font-medium lato-bold">
                                                    Foto {{ $totalExistentes + $i + 1 }}
                                                </span>
                                                <span class="text-[10px] sm:text-xs text-emerald-500 lato-regular hidden sm:block">
                                                    clique para enviar
                                                </span>
                                            </div>

                                            <div wire:loading wire:target="fotos.{{ $i }}"
                                                class="inset-0 flex items-center justify-center rounded-xl bg-white/80">
                                                <x-lucide-loader-2 class="w-5 h-5 text-emerald-500 animate-spin" />
                                            </div>

                                            <input type="file" wire:model="fotos.{{ $i }}" accept="image/*" class="hidden" />
                                        </label>
                                    @endif

                                </div>
                            @endfor

                        </div>

                        @error('fotos.*')
                            <p class="text-xs text-red-500 mt-2 lato-regular">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ── Nome ── --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 lato-bold">Nome da Sala</label>
                        <input type="text" wire:model="nome" placeholder="Ex: Sala Inovação"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                            focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent
                            placeholder-gray-300 lato-regular @error('nome') border-red-400 @enderror" />
                        @error('nome')
                            <p class="text-xs text-red-500 mt-1 lato-regular">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- ── Capacidade + Andar ── --}}
                    <div class="grid grid-cols-2 gap-2 sm:gap-3">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 lato-bold">Capacidade</label>
                            <input type="number" wire:model="capacidade" placeholder="Ex: 10" min="1"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent
                                placeholder-gray-300 lato-regular @error('capacidade') border-red-400 @enderror" />
                            @error('capacidade')
                                <p class="text-xs text-red-500 mt-1 lato-regular">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5 lato-bold">Andar</label>
                            <input type="text" wire:model="andar" placeholder="Ex: 3º andar"
                                class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent
                                placeholder-gray-300 lato-regular @error('andar') border-red-400 @enderror" />
                            @error('andar')
                                <p class="text-xs text-red-500 mt-1 lato-regular">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- ── Recursos ── --}}
                    <div>
                        <h3 class="text-sm font-semibold text-gray-700 mb-2 sm:mb-3 lato-bold">Recursos Disponíveis</h3>
                        <div class="grid grid-cols-2 gap-2">
                            @foreach ([
                                ['value' => 'tv',               'label' => 'TV',               'icon' => 'tv-2'],
                                ['value' => 'wifi',             'label' => 'Wi-Fi',            'icon' => 'wifi'],
                                ['value' => 'videoconferencia', 'label' => 'Videoconferência', 'icon' => 'monitor'],
                                ['value' => 'projetor',         'label' => 'Projetor',         'icon' => 'projector'],
                                ['value' => 'cafe',             'label' => 'Café',             'icon' => 'coffee'],
                                ['value' => 'quadro',           'label' => 'Quadro Branco',    'icon' => 'presentation'],
                            ] as $recurso)
                                <label
                                    class="flex items-center gap-2 sm:gap-3 px-3 sm:px-3.5 py-2.5 sm:py-3 border rounded-xl cursor-pointer transition select-none
                                    {{ in_array($recurso['value'], $recursos) ? 'border-emerald-400 bg-emerald-50' : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                                    <input type="checkbox" wire:model="recursos" value="{{ $recurso['value'] }}"
                                        class="w-4 h-4 rounded text-emerald-500 border-gray-300 focus:ring-emerald-400 flex-shrink-0" />
                                    <x-dynamic-component :component="'lucide-' . $recurso['icon']"
                                        class="w-4 h-4 flex-shrink-0 {{ in_array($recurso['value'], $recursos) ? 'text-emerald-500' : 'text-gray-400' }}" />
                                    <span class="text-xs sm:text-sm truncate lato-regular
                                        {{ in_array($recurso['value'], $recursos) ? 'text-emerald-700 font-medium' : 'text-gray-700' }}">
                                        {{ $recurso['label'] }}
                                    </span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl">
                    <div class="flex gap-2 sm:gap-3 sm:justify-end">
                        <button wire:click="fecharModal"
                            class="flex-1 sm:flex-none px-4 sm:px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800
                                border border-gray-200 hover:border-gray-300 rounded-xl transition bg-white cursor-pointer lato-bold">
                            Cancelar
                        </button>
                        <button wire:click="salvarSala" wire:loading.attr="disabled"
                            class="flex-1 sm:flex-none px-4 sm:px-5 py-2.5 text-sm font-medium text-white cursor-pointer
                                bg-emerald-500 hover:bg-emerald-600 rounded-xl transition
                                flex items-center justify-center gap-2 disabled:opacity-60 lato-bold">
                            <span wire:loading.remove wire:target="salvarSala">
                                {{ $salaId ? 'Salvar Alterações' : 'Salvar Sala' }}
                            </span>
                            <span wire:loading wire:target="salvarSala" class="flex items-center gap-2">
                                <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                Salvando...
                            </span>
                        </button>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
