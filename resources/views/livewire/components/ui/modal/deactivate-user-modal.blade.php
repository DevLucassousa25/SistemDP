<div>
    @if ($show)

        <div class="fixed inset-0 z-50 flex items-center justify-center">

            <!-- Backdrop -->
            <div class="absolute inset-0 bg-black/40 backdrop-blur-sm" wire:click="cancel"></div>

            <!-- Card -->
            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md mx-4 p-6 z-10">

                <!-- Ícone -->
                <div class="flex items-center justify-center w-14 h-14 rounded-full mx-auto mb-4
                    {{ $status === 'deactivate' ? 'bg-red-50' : 'bg-blue-50' }}">

                    @if ($status === 'deactivate')
                        <x-lucide-alert-triangle class="w-7 h-7 text-red-500"/>
                    @else
                        <x-lucide-user-check class="w-7 h-7 text-blue-500"/>
                    @endif

                </div>

                <!-- Título -->
                <h3 class="text-center text-gray-900 font-semibold text-lg mb-1">
                    {{ $status === 'deactivate' ? 'Desativar usuário' : 'Ativar usuário' }}
                </h3>

                <!-- Mensagem -->
                <p class="text-center text-gray-500 text-sm mb-6">
                    Tem certeza que deseja
                    <span class="font-medium">
                        {{ $status === 'deactivate' ? 'desativar' : 'ativar' }}
                    </span>

                    <span class="font-medium text-gray-700">
                        {{ $userName }}
                    </span>?

                    <br>

                    {{ $status === 'deactivate'
                        ? 'O usuário perderá o acesso ao sistema.'
                        : 'O usuário terá acesso ao sistema novamente.' }}
                </p>

                <!-- Botões -->
                <div class="flex gap-3">

                    <button wire:click="cancel"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-gray-700 bg-white border border-gray-200
                        rounded-xl hover:bg-gray-50 transition cursor-pointer">
                        Cancelar
                    </button>

                    <button wire:click="confirm" wire:loading.attr="disabled"
                        class="flex-1 px-4 py-2.5 text-sm font-medium text-white rounded-xl transition cursor-pointer
                        {{ $status === 'deactivate'
                            ? 'bg-red-500 hover:bg-red-600'
                            : 'bg-blue-500 hover:bg-blue-600' }}">

                        <span wire:loading.remove wire:target="confirm">
                            {{ $status === 'deactivate' ? 'Sim, desativar' : 'Sim, ativar' }}
                        </span>

                        <span wire:loading wire:target="confirm">
                            {{ $status === 'deactivate' ? 'Desativando...' : 'Ativando...' }}
                        </span>

                    </button>

                </div>

            </div>
        </div>
    @endif
</div>
