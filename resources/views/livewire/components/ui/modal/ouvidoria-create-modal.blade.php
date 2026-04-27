<div>
    @if ($modalAberto)
        <div x-data x-init="document.body.style.overflow = 'hidden'"
            x-destroy="document.body.style.overflow = ''"
            class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4">

            {{-- Backdrop --}}
            <div wire:click="fecharModal"
                class="absolute inset-0 bg-black/40 backdrop-blur-sm"></div>

            {{-- Modal Box --}}
            {{-- Mobile: sobe da base (bottom-sheet). Desktop: centralizado --}}
            <div class="relative bg-white w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh]
                        rounded-t-2xl sm:rounded-2xl shadow-2xl flex flex-col z-10">

                {{-- Header --}}
                <div class="flex items-start justify-between px-4 pt-4 pb-3 sm:p-6 sm:pb-4 border-b border-gray-100 flex-shrink-0">
                    {{-- Alça de arraste (mobile) --}}
                    <div class="absolute top-2 left-1/2 -translate-x-1/2 w-10 h-1 bg-gray-200 rounded-full sm:hidden"></div>

                    <div class="flex items-center gap-3">
                        <div class="flex items-center justify-center w-9 h-9 sm:w-10 sm:h-10 rounded-xl bg-emerald-100 flex-shrink-0">
                            <x-lucide-megaphone class="w-4 h-4 sm:w-5 sm:h-5 text-emerald-600" />
                        </div>
                        <div>
                            <h2 class="text-base sm:text-lg font-semibold text-gray-900 lato-bold">Nova Manifestação</h2>
                            <p class="text-xs sm:text-sm text-gray-400 mt-0.5 lato-regular hidden sm:block">
                                Preencha os campos com clareza e objetividade
                            </p>
                        </div>
                    </div>
                    <button wire:click="fecharModal"
                        class="text-gray-400 hover:text-gray-600 transition cursor-pointer mt-0.5 ml-2 flex-shrink-0">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="px-4 py-4 sm:p-6 flex flex-col gap-4 sm:gap-6 overflow-y-auto flex-1">

                    {{-- ── Categoria ── --}}
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2 sm:mb-3 lato-bold">
                            Tipo de Manifestação
                            <span class="text-red-400 ml-0.5">*</span>
                        </label>

                        <div class="grid grid-cols-2 gap-2 sm:gap-3">
                            @foreach ([
                                ['value' => 'sugestao',   'label' => 'Sugestão',   'desc' => 'Proposta de melhoria',      'icon' => 'lightbulb',   'color' => 'blue'],
                                ['value' => 'reclamacao', 'label' => 'Reclamação', 'desc' => 'Insatisfação com serviço',   'icon' => 'alert-circle','color' => 'orange'],
                                ['value' => 'denuncia',   'label' => 'Denúncia',   'desc' => 'Irregularidade ou infração', 'icon' => 'shield-alert','color' => 'red'],
                                ['value' => 'elogio',     'label' => 'Elogio',     'desc' => 'Reconhecimento positivo',    'icon' => 'thumbs-up',   'color' => 'emerald'],
                            ] as $cat)
                                @php
                                    $selected = $categoria === $cat['value'];
                                    $colorMap = [
                                        'blue'    => ['border' => 'border-blue-400 bg-blue-50',      'icon' => 'text-blue-500',    'label' => 'text-blue-700',    'desc' => 'text-blue-500'],
                                        'orange'  => ['border' => 'border-orange-400 bg-orange-50',  'icon' => 'text-orange-500',  'label' => 'text-orange-700',  'desc' => 'text-orange-500'],
                                        'red'     => ['border' => 'border-red-400 bg-red-50',        'icon' => 'text-red-500',     'label' => 'text-red-700',     'desc' => 'text-red-500'],
                                        'emerald' => ['border' => 'border-emerald-400 bg-emerald-50','icon' => 'text-emerald-500', 'label' => 'text-emerald-700', 'desc' => 'text-emerald-500'],
                                    ];
                                    $colors = $colorMap[$cat['color']];
                                @endphp

                                <button type="button" wire:click="$set('categoria', '{{ $cat['value'] }}')"
                                    class="flex items-center gap-2 sm:gap-3 px-3 sm:px-4 py-3 border-2 rounded-xl text-left cursor-pointer transition select-none
                                        {{ $selected ? $colors['border'] : 'border-gray-200 hover:border-gray-300 hover:bg-gray-50' }}">
                                    <x-dynamic-component :component="'lucide-' . $cat['icon']"
                                        class="w-4 h-4 sm:w-5 sm:h-5 flex-shrink-0 {{ $selected ? $colors['icon'] : 'text-gray-400' }}" />
                                    <div class="min-w-0">
                                        <p class="text-xs sm:text-sm font-semibold {{ $selected ? $colors['label'] : 'text-gray-700' }} lato-bold leading-tight">
                                            {{ $cat['label'] }}
                                        </p>
                                        <p class="text-[10px] sm:text-xs {{ $selected ? $colors['desc'] : 'text-gray-400' }} lato-regular leading-tight mt-0.5 hidden sm:block">
                                            {{ $cat['desc'] }}
                                        </p>
                                    </div>
                                </button>
                            @endforeach
                        </div>

                        @error('categoria')
                            <p class="text-xs text-red-500 mt-2 flex items-center gap-1">
                                <x-lucide-alert-circle class="w-3.5 h-3.5" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- ── Assunto ── --}}
                    <div x-data="{ count: {{ strlen($assunto) }} }">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 lato-bold">
                            Assunto <span class="text-red-400 ml-0.5">*</span>
                        </label>
                        <input
                            type="text"
                            wire:model="assunto"
                            @input="count = $event.target.value.length"
                            placeholder="Descreva brevemente o assunto"
                            maxlength="150"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent
                                placeholder-gray-300 transition lato-regular
                                @error('assunto') border-red-400 bg-red-50 @enderror" />
                        <div class="flex items-center justify-between mt-1">
                            @error('assunto')
                                <p class="text-xs text-red-500 flex items-center gap-1">
                                    <x-lucide-alert-circle class="w-3.5 h-3.5" /> {{ $message }}
                                </p>
                            @else
                                <span></span>
                            @enderror
                            <span class="text-xs lato-regular ml-auto transition-colors"
                                :class="count >= 140 ? 'text-red-400 font-semibold' : 'text-gray-400'"
                                x-text="count + '/150'"></span>
                        </div>
                    </div>

                    {{-- ── Descrição ── --}}
                    <div x-data="{ count: {{ strlen($descricao) }} }">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5 lato-bold">
                            Descrição <span class="text-red-400 ml-0.5">*</span>
                        </label>
                        <textarea
                            wire:model="descricao"
                            @input="count = $event.target.value.length"
                            rows="4"
                            placeholder="Descreva detalhadamente sua manifestação..."
                            maxlength="3000"
                            class="w-full px-3.5 py-2.5 text-sm border border-gray-200 rounded-xl
                                focus:outline-none focus:ring-2 focus:ring-emerald-400 focus:border-transparent
                                placeholder-gray-300 transition resize-none lato-regular
                                @error('descricao') border-red-400 bg-red-50 @enderror">
                        </textarea>
                        <div class="flex items-center justify-between mt-1">
                            @error('descricao')
                                <p class="text-xs text-red-500 flex items-center gap-1">
                                    <x-lucide-alert-circle class="w-3.5 h-3.5" /> {{ $message }}
                                </p>
                            @else
                                <span></span>
                            @enderror
                            <span class="text-xs lato-regular ml-auto transition-colors"
                                :class="count >= 2800 ? 'text-red-400 font-semibold' : 'text-gray-400'"
                                x-text="count + '/3000'"></span>
                        </div>
                    </div>

                    {{-- ── Anonimato ── --}}
                    <div class="flex items-start gap-3 p-3.5 sm:p-4 rounded-xl border border-gray-200 bg-gray-50/50 cursor-pointer"
                        wire:click="$toggle('isAnonimo')">
                        <div class="flex-shrink-0 mt-0.5">
                            <div class="w-5 h-5 rounded border-2 flex items-center justify-center transition
                                {{ $isAnonimo ? 'bg-emerald-500 border-emerald-500' : 'border-gray-300 bg-white' }}">
                                @if ($isAnonimo)
                                    <x-lucide-check class="w-3 h-3 text-white" />
                                @endif
                            </div>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="flex items-center gap-2 mb-0.5">
                                <x-lucide-eye-off class="w-4 h-4 text-gray-500 flex-shrink-0" />
                                <p class="text-sm font-semibold text-gray-700 lato-bold">Enviar de forma anônima</p>
                            </div>
                            <p class="text-xs text-gray-500 lato-regular leading-relaxed">
                                Sua identidade não será vinculada. Você não poderá acompanhar o protocolo após fechar.
                            </p>
                        </div>
                    </div>

                    {{-- Aviso sigilo --}}
                    <div class="flex items-start gap-3 p-3 sm:p-3.5 rounded-xl border border-emerald-200 bg-emerald-50/70">
                        <x-lucide-lock class="w-4 h-4 text-emerald-600 flex-shrink-0 mt-0.5" />
                        <p class="text-xs text-emerald-800 lato-regular leading-relaxed">
                            <span class="font-semibold lato-bold">Confidencialidade garantida.</span>
                            Todas as manifestações são tratadas com sigilo absoluto. Nenhuma retaliação será tolerada.
                        </p>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-gray-100 bg-gray-50/50 rounded-b-2xl space-y-2 sm:space-y-0">

                    {{-- Indicador de identidade --}}
                    <div class="flex items-center gap-1.5 pb-2 sm:hidden border-b border-gray-100">
                        @if ($isAnonimo)
                            <x-lucide-eye-off class="w-3.5 h-3.5 text-gray-400" />
                            <span class="text-xs text-gray-400 lato-regular">Envio anônimo</span>
                        @else
                            <x-lucide-user class="w-3.5 h-3.5 text-gray-400" />
                            <span class="text-xs text-gray-400 lato-regular truncate">
                                Identificado como <strong class="font-semibold">{{ Auth::user()?->name ?? 'você' }}</strong>
                            </span>
                        @endif
                    </div>

                    <div class="flex flex-col-reverse sm:flex-row sm:items-center sm:justify-between gap-2">

                        {{-- Identidade (desktop) --}}
                        <div class="hidden sm:flex items-center gap-1.5">
                            @if ($isAnonimo)
                                <x-lucide-eye-off class="w-3.5 h-3.5 text-gray-400" />
                                <span class="text-xs text-gray-400 lato-regular">Envio anônimo</span>
                            @else
                                <x-lucide-user class="w-3.5 h-3.5 text-gray-400" />
                                <span class="text-xs text-gray-400 lato-regular">
                                    Identificado como {{ Auth::user()?->name ?? 'você' }}
                                </span>
                            @endif
                        </div>

                        {{-- Botões --}}
                        <div class="flex gap-2 sm:gap-3">
                            <button wire:click="fecharModal"
                                class="flex-1 sm:flex-none px-4 sm:px-5 py-2.5 text-sm font-medium text-gray-600 hover:text-gray-800
                                    border border-gray-200 hover:border-gray-300 rounded-xl transition bg-white cursor-pointer lato-bold">
                                Cancelar
                            </button>

                            <button wire:click="salvar" wire:loading.attr="disabled"
                                class="flex-1 sm:flex-none px-4 sm:px-5 py-2.5 text-sm font-medium text-white cursor-pointer
                                    bg-emerald-500 hover:bg-emerald-600 rounded-xl transition
                                    flex items-center justify-center gap-2 disabled:opacity-60 lato-bold">
                                <span wire:loading.remove wire:target="salvar" class="flex items-center gap-2">
                                    <x-lucide-send class="w-4 h-4" />
                                    Enviar
                                </span>
                                <span wire:loading wire:target="salvar" class="flex items-center gap-2">
                                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                    Enviando...
                                </span>
                            </button>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    @endif
</div>
