{{-- resources/views/livewire/components/ui/user-dropdown.blade.php --}}
<div class="relative" x-data="{ open: @entangle('open') }" @click.outside="open = false">

    {{-- Botão trigger --}}
    <div
        wire:click="toggle"
        class="flex items-center gap-2 sm:gap-3 cursor-pointer hover:bg-slate-50 dark:hover:bg-slate-700 px-2 sm:px-3 py-2 rounded-lg transition select-none"
    >
        <img
            src="https://i.pravatar.cc/100"
            class="w-8 h-8 sm:w-9 sm:h-9 rounded-full object-cover"
        />

        <div class="leading-tight hidden md:block">
            @php
                $nomes = explode(' ', auth()->user()->name);
                $primeiroSegundo = $nomes[0] . (isset($nomes[1]) ? ' ' . $nomes[1] : '');
            @endphp

            <p class="text-sm font-semibold text-slate-700 lato-bold">
                {{ $primeiroSegundo }}
            </p>
            <p class="text-xs text-slate-500 lato-regular">
                {{ auth()->user()->department->name }}
            </p>
        </div>

        <x-lucide-chevron-down
            class="w-4 h-4 text-slate-400 hidden sm:block transition-transform duration-200"
            ::class="{ 'rotate-180': open }"
        />
    </div>

    {{-- Dropdown --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-1"
        class="absolute right-0 mt-2 w-52 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg z-50 py-1 origin-top-right"
        style="display: none;"
    >
        {{-- Cabeçalho --}}
        <div class="px-4 py-3 border-b border-slate-100 dark:border-slate-700">
            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 truncate">{{ auth()->user()->name }}</p>
            <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ auth()->user()->email }}</p>
        </div>

        {{-- Itens --}}
        <div class="py-1">
            <a
                href="#"
                class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition"
            >
                <x-lucide-user class="w-4 h-4 text-slate-400 dark:text-slate-500" />
                Meu perfil
            </a>

            <a
                href="#"
                class="flex items-center gap-2.5 px-4 py-2 text-sm text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition"
            >
                <x-lucide-settings class="w-4 h-4 text-slate-400 dark:text-slate-500" />
                Configurações
            </a>
        </div>

        {{-- Sair --}}
        <div class="border-t border-slate-100 dark:border-slate-700 py-1">
           <button
    wire:click="logout"
    class="flex items-center gap-2.5 w-full px-4 py-2 text-sm text-red-500 hover:bg-red-50 transition cursor-pointer"
>
    {{-- Ícone normal --}}
    <x-lucide-log-out class="w-4 h-4" wire:loading.remove wire:target="logout" />

    {{-- Spinner --}}
    <svg wire:loading wire:target="logout"
        class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
    </svg>

    <span wire:loading.remove wire:target="logout">Sair</span>
    <span wire:loading wire:target="logout">Saindo...</span>
</button>
        </div>
    </div>
</div>
