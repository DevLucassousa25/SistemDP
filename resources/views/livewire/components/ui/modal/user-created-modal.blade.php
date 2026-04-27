<div>
    @if($open)
        <div class="fixed inset-0 z-50 flex items-center justify-center transition-all duration-300
        {{ $open ? 'opacity-100 backdrop-blur-sm bg-black/40' : 'opacity-0 pointer-events-none' }}">

            <div class="absolute inset-0 bg-black/40"></div>

            <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md p-6 text-center">

                <!-- Close -->
                <button wire:click="close"
                    class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 cursor-pointer">
                    <x-lucide-x class="w-5 h-5" />
                </button>

                <!-- Icon -->
                <div class="mx-auto mb-4 flex items-center justify-center w-16 h-16 rounded-2xl
                    {{ $type === 'create' ? 'bg-emerald-100' : 'bg-blue-100' }}">

                    <x-lucide-circle-check
                        class="w-8 h-8 {{ $type === 'create' ? 'text-emerald-600' : 'text-blue-600' }}" />
                </div>

                <!-- Title -->
                <h2 class="text-lg font-semibold text-gray-800 mb-2 lato-bold">
                    {{ $type === 'create'
                        ? 'Cadastro realizado com sucesso!'
                        : 'Usuário atualizado com sucesso!' }}
                </h2>

                <!-- Description -->
                <p class="text-gray-500 text-sm mb-6 lato-normal">
                    {{ $type === 'create'
                        ? 'As informações foram salvas com sucesso na plataforma.'
                        : 'As alterações foram atualizadas com sucesso.' }}
                </p>

                <!-- Button -->
                <button wire:click="close"
                    class="w-full text-sm text-white font-medium py-2.5 rounded-lg transition cursor-pointer
                    {{ $type === 'create'
                        ? 'bg-emerald-500 hover:bg-emerald-600'
                        : 'bg-blue-500 hover:bg-blue-600' }}">
                    Continuar
                </button>

            </div>
        </div>
    @endif
</div>
