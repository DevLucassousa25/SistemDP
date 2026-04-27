<div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">

    {{-- Total --}}
    <div class="bg-[#F1F5F9] rounded-2xl p-5 flex items-center justify-between">
        <div>
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">Total de Salas</p>
            <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 lato-black">{{ $total }}</p>
        </div>
        <div class="text-slate-400">
            <x-lucide-building-2 class="w-10 h-10" />
        </div>
    </div>

    {{-- Disponíveis --}}
    <div class="bg-[#D1FAE5] rounded-2xl p-5 flex items-center justify-between">
        <div>
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">Disponíveis</p>
            <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 lato-black">{{ $disponiveis }}</p>
        </div>
        <div class="text-emerald-500">
            <x-lucide-check-circle-2 class="w-10 h-10" />
        </div>
    </div>

    {{-- Ocupadas --}}
    <div class="bg-[#FEE2E2] rounded-2xl p-5 flex items-center justify-between">
        <div>
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">Ocupadas</p>
            <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 lato-black">{{ $ocupadas }}</p>
        </div>
        <div class="text-red-500">
            <x-lucide-user-check class="w-10 h-10" />
        </div>
    </div>

    {{-- Reservadas --}}
    <div class="bg-[#DBEAFE] rounded-2xl p-5 flex items-center justify-between">
        <div>
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">Reservadas</p>
            <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 lato-black">{{ $reservadas }}</p>
        </div>
        <div class="text-blue-500">
            <x-lucide-calendar-clock class="w-10 h-10" />
        </div>
    </div>

    {{-- Manutenção --}}
    <div class="bg-[#FEF3C7] rounded-2xl p-5 flex items-center justify-between">
        <div>
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">Manutenção</p>
            <p class="text-xl sm:text-2xl lg:text-3xl font-bold text-gray-900 mt-1 sm:mt-2 lato-black">{{ $manutencao }}</p>
        </div>
        <div class="text-amber-500">
            <x-lucide-wrench class="w-10 h-10" />
        </div>
    </div>

</div>
