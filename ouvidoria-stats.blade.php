{{--
    Componente: OuvidoriaStats
    Uso       : <livewire:ouvidoria.ouvidoria-stats />
    Listeners : manifestacaoCriada | manifestacaoAtualizada | manifestacaoExcluida | refresh
--}}

<div class="grid grid-cols-2 gap-4 sm:grid-cols-2 lg:grid-cols-4">

    {{-- ── TOTAL ─────────────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-between rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
        <div>
            <p class="text-sm text-gray-500">Total</p>
            <p class="mt-1 text-4xl font-bold text-gray-900 tabular-nums transition-all duration-300">
                {{ $total }}
            </p>
        </div>
        <div class="mt-1 text-gray-300">
            {{-- ícone exclamação --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"/>
                <line x1="12" y1="8" x2="12" y2="12"/>
                <line x1="12" y1="16" x2="12.01" y2="16"/>
            </svg>
        </div>
    </div>

    {{-- ── EM ANÁLISE ───────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-between rounded-xl border border-yellow-200 bg-yellow-50 p-5 shadow-sm">
        <div>
            <p class="text-sm text-yellow-700">Em Análise</p>
            <p class="mt-1 text-4xl font-bold text-yellow-800 tabular-nums transition-all duration-300">
                {{ $emAnalise }}
            </p>
        </div>
        <div class="mt-1 text-yellow-300">
            {{-- ícone relógio --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <circle cx="12" cy="12" r="10"/>
                <polyline points="12 6 12 12 16 14"/>
            </svg>
        </div>
    </div>

    {{-- ── EM ANDAMENTO ─────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-between rounded-xl border border-teal-200 bg-teal-50 p-5 shadow-sm">
        <div>
            <p class="text-sm text-teal-700">Em Andamento</p>
            <p class="mt-1 text-4xl font-bold text-teal-800 tabular-nums transition-all duration-300">
                {{ $emAndamento }}
            </p>
        </div>
        <div class="mt-1 text-teal-300">
            {{-- ícone balão de chat --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/>
            </svg>
        </div>
    </div>

    {{-- ── CONCLUÍDOS ───────────────────────────────────────────────────── --}}
    <div class="flex items-start justify-between rounded-xl border border-green-200 bg-green-50 p-5 shadow-sm">
        <div>
            <p class="text-sm text-green-700">Concluídos</p>
            <p class="mt-1 text-4xl font-bold text-green-800 tabular-nums transition-all duration-300">
                {{ $concluidos }}
            </p>
        </div>
        <div class="mt-1 text-green-300">
            {{-- ícone check-circle --}}
            <svg xmlns="http://www.w3.org/2000/svg" class="h-7 w-7" fill="none"
                 viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5">
                <path stroke-linecap="round" stroke-linejoin="round"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
        </div>
    </div>

</div>
