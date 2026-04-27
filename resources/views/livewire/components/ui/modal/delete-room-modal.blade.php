{{-- resources/views/livewire/components/ui/delete-room-modal.blade.php --}}
<div>
    @if ($open)
        {{-- Backdrop --}}
        <div class="fixed inset-0 z-50 flex items-center justify-center px-4" x-data x-init="document.body.classList.add('overflow-hidden')"
            x-destroy="document.body.classList.remove('overflow-hidden')">
            {{-- Overlay --}}
            <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" wire:click="fechar"></div>

            {{-- Modal --}}
            <div class="relative z-10 w-full max-w-md bg-white rounded-2xl shadow-xl" x-data x-init="$el.style.opacity = 0;
            $el.style.transform = 'scale(0.95) translateY(8px)';
            requestAnimationFrame(() => {
                $el.style.transition = 'opacity 200ms ease, transform 200ms ease';
                $el.style.opacity = 1;
                $el.style.transform = 'scale(1) translateY(0)';
            });">
                {{-- Header --}}
                <div class="flex items-start justify-between p-5 border-b border-slate-100">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-red-50 flex items-center justify-center shrink-0">
                            <x-lucide-trash-2 class="w-5 h-5 text-red-500" />
                        </div>
                        <div>
                            <h2 class="text-base font-semibold text-slate-800">Excluir sala</h2>
                            <p class="text-xs text-slate-400 mt-0.5">Esta ação não pode ser desfeita</p>
                        </div>
                    </div>

                    <button wire:click="fechar"
                        class="text-slate-400 hover:text-slate-600 hover:bg-slate-100 rounded-lg p-1.5 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                {{-- Body --}}
                <div class="p-5 space-y-4">

                    {{-- Alerta --}}
                    <div class="flex gap-3 bg-red-50 border border-red-100 rounded-xl p-4">
                        <x-lucide-alert-triangle class="w-5 h-5 text-red-500 shrink-0 mt-0.5" />
                        <div class="text-sm text-red-700 leading-relaxed">
                            Tem certeza que deseja excluir a sala
                            <span class="font-semibold">"{{ $roomName }}"</span>?
                            Todas as reservas ativas serão <span class="font-semibold">canceladas
                                automaticamente</span>.
                        </div>
                    </div>

                    {{-- Confirmação visual --}}
                    <div class="bg-slate-50 rounded-xl px-4 py-3 flex items-center gap-3 border border-slate-100">
                        <x-lucide-building class="w-4 h-4 text-slate-400 shrink-0" />
                        <span class="text-sm text-slate-600 truncate">{{ $roomName }}</span>
                        <span class="ml-auto text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded-full font-medium">
                            será excluída
                        </span>
                    </div>

                </div>

                {{-- Footer --}}
                <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-slate-100">

                    <button wire:click="fechar"
                        class="px-4 py-2 text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-xl transition font-medium cursor-pointer">
                        Cancelar
                    </button>

                    <button wire:click="excluir" wire:loading.attr="disabled"
                        wire:loading.class="opacity-60 cursor-not-allowed"
                        class="flex items-center gap-2 px-4 py-2 text-sm text-white bg-red-500 hover:bg-red-600 active:scale-[0.98] rounded-xl transition font-medium cursor-pointer">
                        {{-- Normal --}}
                        <x-lucide-trash-2 class="w-4 h-4" wire:loading.remove wire:target="excluir" />
                        <span wire:loading.remove wire:target="excluir">Excluir sala</span>

                        {{-- Loading --}}
                        <svg wire:loading wire:target="excluir" class="w-4 h-4 animate-spin" fill="none"
                            viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                stroke-width="4" />
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                        </svg>
                        <span wire:loading wire:target="excluir">Excluindo...</span>
                    </button>

                </div>
            </div>
        </div>
    @endif
</div>
