@if ($paginator->hasPages())
<div class="flex flex-col sm:flex-row items-center justify-between gap-3 px-1 py-3">

    {{-- Contagem --}}
    <p class="text-xs text-gray-400 dark:text-gray-500 lato-regular order-2 sm:order-1">
        Mostrando
        <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $paginator->firstItem() }}</span>
        –
        <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $paginator->lastItem() }}</span>
        de
        <span class="font-semibold text-gray-600 dark:text-gray-300">{{ $paginator->total() }}</span>
    </p>

    {{-- Controles --}}
    <div class="flex items-center gap-1 order-1 sm:order-2">

        {{-- ← Voltar --}}
        <button
            wire:click.prevent="previousPage('{{ $paginator->getPageName() }}')"
            wire:loading.attr="disabled"
            @if ($paginator->onFirstPage()) disabled @endif
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold lato-bold
                   border transition
                   {{ $paginator->onFirstPage()
                       ? 'border-gray-100 dark:border-gray-700 text-gray-300 dark:text-gray-600 cursor-not-allowed'
                       : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer' }}">
            <x-lucide-chevron-left class="w-3.5 h-3.5" />
            Voltar
        </button>

        {{-- Números de página --}}
        @php
            $current  = $paginator->currentPage();
            $last     = $paginator->lastPage();

            // Gera a sequência de páginas com reticências
            $pages = [];
            for ($p = 1; $p <= $last; $p++) {
                if (
                    $p === 1 || $p === $last          // sempre exibe primeira e última
                    || abs($p - $current) <= 1        // exibe ±1 em torno da atual
                ) {
                    $pages[] = $p;
                }
            }

            // Insere '...' onde há saltos
            $result = [];
            $prev   = null;
            foreach ($pages as $p) {
                if ($prev !== null && $p - $prev > 1) {
                    $result[] = '...';
                }
                $result[] = $p;
                $prev = $p;
            }
        @endphp

        @foreach ($result as $item)
            @if ($item === '...')
                <span class="px-1.5 py-1.5 text-xs text-gray-400 dark:text-gray-500 select-none">…</span>
            @else
                <button
                    wire:click.prevent="gotoPage({{ $item }}, '{{ $paginator->getPageName() }}')"
                    wire:loading.attr="disabled"
                    class="min-w-[32px] px-2 py-1.5 rounded-lg text-xs font-semibold lato-bold transition
                           {{ $item == $current
                               ? 'bg-emerald-500 dark:bg-emerald-600 text-white border border-emerald-500 dark:border-emerald-600 cursor-default'
                               : 'border border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer' }}">
                    {{ $item }}
                </button>
            @endif
        @endforeach

        {{-- Próximo → --}}
        <button
            wire:click.prevent="nextPage('{{ $paginator->getPageName() }}')"
            wire:loading.attr="disabled"
            @if (! $paginator->hasMorePages()) disabled @endif
            class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold lato-bold
                   border transition
                   {{ ! $paginator->hasMorePages()
                       ? 'border-gray-100 dark:border-gray-700 text-gray-300 dark:text-gray-600 cursor-not-allowed'
                       : 'border-gray-200 dark:border-gray-600 text-gray-600 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700/50 cursor-pointer' }}">
            Próximo
            <x-lucide-chevron-right class="w-3.5 h-3.5" />
        </button>

    </div>
</div>
@endif
