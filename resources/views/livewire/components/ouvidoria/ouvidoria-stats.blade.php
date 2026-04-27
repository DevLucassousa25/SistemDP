<div class="grid grid-cols-2 gap-3 sm:gap-4 lg:grid-cols-4">

    {{-- ── TOTAL ── --}}
    <div class="flex items-start justify-between rounded-xl bg-[#F1F5F9] p-4 sm:p-5">
        <div>
            <p class="text-xs sm:text-sm text-gray-500 lato-regular">Total</p>
            <p class="mt-1 text-3xl sm:text-4xl font-bold text-gray-900 tabular-nums transition-all duration-300 lato-black">
                {{ $total }}
            </p>
        </div>
        <div class="mt-1 text-gray-400">
            <x-lucide-info class="h-5 w-5 sm:h-7 sm:w-7"/>
        </div>
    </div>

    {{-- ── EM ANÁLISE ── --}}
    <div class="flex items-start justify-between rounded-xl bg-[#FEF3C7] p-4 sm:p-5">
        <div>
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">Em Análise</p>
            <p class="mt-1 text-3xl sm:text-4xl font-bold text-gray-800 tabular-nums transition-all duration-300 lato-black">
                {{ $emAnalise }}
            </p>
        </div>
        <div class="mt-1 text-gray-400">
            <x-lucide-clock class="h-5 w-5 sm:h-7 sm:w-7"/>
        </div>
    </div>

    {{-- ── EM ANDAMENTO ── --}}
    <div class="flex items-start justify-between rounded-xl bg-[#D0F5E4] p-4 sm:p-5">
        <div>
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">Em Andamento</p>
            <p class="mt-1 text-3xl sm:text-4xl font-bold text-gray-800 tabular-nums transition-all duration-300 lato-black">
                {{ $emAndamento }}
            </p>
        </div>
        <div class="mt-1 text-gray-400">
            <x-lucide-message-square class="h-5 w-5 sm:h-7 sm:w-7"/>
        </div>
    </div>

    {{-- ── CONCLUÍDOS ── --}}
    <div class="flex items-start justify-between rounded-xl bg-[#D1FAE5] p-4 sm:p-5">
        <div>
            <p class="text-xs sm:text-sm text-gray-700 lato-regular">Concluídos</p>
            <p class="mt-1 text-3xl sm:text-4xl font-bold text-gray-800 tabular-nums transition-all duration-300 lato-black">
                {{ $concluidos }}
            </p>
        </div>
        <div class="mt-1 text-gray-400">
            <x-lucide-check-circle class="h-5 w-5 sm:h-7 sm:w-7"/>
        </div>
    </div>

</div>
