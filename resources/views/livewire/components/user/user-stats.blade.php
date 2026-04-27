<div class="relative">

    <!-- Skeleton Loader -->
    <div wire:loading
        class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6 animate-pulse">

        <!-- Card -->
        <div class="bg-[#F1F5F9] rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6">
            <div class="h-3 sm:h-4 w-24 sm:w-32 bg-gray-300 rounded mb-3"></div>
            <div class="h-6 sm:h-8 w-16 bg-gray-300 rounded"></div>
        </div>

        <div class="bg-[#FEE2E2] rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6">
            <div class="h-3 sm:h-4 w-24 sm:w-32 bg-gray-300 rounded mb-3"></div>
            <div class="h-6 sm:h-8 w-16 bg-gray-300 rounded"></div>
        </div>

        <div class="bg-[#D0F5E4] rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6">
            <div class="h-3 sm:h-4 w-24 sm:w-32 bg-gray-300 rounded mb-3"></div>
            <div class="h-6 sm:h-8 w-16 bg-gray-300 rounded"></div>
        </div>

        <div class="bg-[#D1FAE5] rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6">
            <div class="h-3 sm:h-4 w-24 sm:w-32 bg-gray-300 rounded mb-3"></div>
            <div class="h-6 sm:h-8 w-16 bg-gray-300 rounded"></div>
        </div>

    </div>


    <!-- Conteúdo real -->
    <div wire:loading.remove
        class="grid grid-cols-2 md:grid-cols-4 gap-3 sm:gap-4 lg:gap-6 mb-6">

        <!-- Total -->
        <div class="bg-[#F1F5F9] rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6">
            <p class="text-xs sm:text-sm text-gray-600 lato-regular">
                Total de Usuários
            </p>

            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 lato-black">
                {{ $totalUsuarios }}
            </h2>
        </div>

        <!-- Administradores -->
        <div class="bg-[#FEE2E2] rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6">
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">
                Administradores
            </p>

            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 lato-black">
                {{ $administradores }}
            </h2>
        </div>

        <!-- Gestores -->
        <div class="bg-[#D0F5E4] rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6">
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">
                Gestores
            </p>

            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 lato-black">
                {{ $gestores }}
            </h2>
        </div>

        <!-- Colaboradores -->
        <div class="bg-[#D1FAE5] rounded-xl sm:rounded-2xl p-4 sm:p-5 lg:p-6">
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">
                Colaboradores
            </p>

            <h2 class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 lato-black">
                {{ $colaboradores }}
            </h2>
        </div>

    </div>

</div>
