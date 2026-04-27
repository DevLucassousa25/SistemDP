<div>
    @if ($show)
        <div class="fixed inset-0 z-50 flex items-center justify-center">

            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="cancel"></div>

            <!-- Card -->
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6 z-10">

                <!-- Ícone -->
                <div class="flex items-center justify-center w-14 h-14 rounded-full bg-blue-50 mx-auto mb-4">
                    <x-lucide-user-check class="w-7 h-7 text-blue-500" />
                </div>

                <!-- Título -->
                <h3 class="text-center text-gray-900 font-semibold text-lg mb-1">
                    Ativar usuário
                </h3>

                <!-- Mensagem -->
                <p class="text-center text-gray-500 text-sm mb-6">
                    Tem certeza que deseja ativar
                    <span class="font-medium text-gray-700">{{ $userName }}</span>?
                    <br>
                    O usuário terá acesso ao sistema novamente.
                </p>

                <!-- Botões -->
                <div class="flex gap-3">
                    <button wire:click="cancel"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200
                        rounded-xl hover:bg-gray-50 transition cursor-pointer">
                        Cancelar
                    </button>

                    <button wire:click="confirm" wire:loading.attr="disabled"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white bg-blue-500
                        rounded-xl hover:bg-blue-600 disabled:opacity-60 transition cursor-pointer">

                        <span wire:loading.remove wire:target="confirm">
                            Sim, ativar
                        </span>

                        <span wire:loading wire:target="confirm">
                            Ativando...
                        </span>

                    </button>
                </div>

            </div>
        </div>
    @endif
</div>
