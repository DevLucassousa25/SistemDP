<header class="w-full sticky top-0 z-30 border-b border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 shrink-0 transition-colors duration-200">

    <div class="flex items-center justify-between h-20 px-3 sm:px-4 lg:px-6">

        <!-- Lado esquerdo -->
        <div class="flex items-center gap-3">

            <!-- Botão hamburguer -->
            <button wire:click="abrirMenu"
                class="lg:hidden flex items-center justify-center p-2 rounded-lg text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition">

                <x-lucide-menu class="w-6 h-6" />

            </button>

        </div>

        <!-- Lado direito -->
        <div class="flex items-center gap-3 sm:gap-4">

            <!-- Toggle Dark Mode -->
            <button
                id="theme-toggle"
                onclick="toggleTheme()"
                title="Alternar tema"
                class="flex items-center cursor-pointer justify-center w-9 h-9 rounded-lg text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition">

                {{-- Ícone Sol (visível no modo escuro, para alternar para claro) --}}
                <x-lucide-sun  id="icon-sun"  class="w-5 h-5 hidden" />
                {{-- Ícone Lua (visível no modo claro, para alternar para escuro) --}}
                <x-lucide-moon id="icon-moon" class="w-5 h-5" />

            </button>

            <!-- Notificações -->
            @livewire('components.notification-bell')

            <!-- Usuário -->
            @livewire('components.ui.dropdown.user-dropdown')
        </div>

    </div>

</header>

<script>
    // Sincroniza os ícones com o tema atual ao carregar
    (function () {
        function syncIcons() {
            var isDark = document.documentElement.classList.contains('dark');
            var sun  = document.getElementById('icon-sun');
            var moon = document.getElementById('icon-moon');
            if (!sun || !moon) return;
            if (isDark) {
                sun.classList.remove('hidden');
                moon.classList.add('hidden');
            } else {
                sun.classList.add('hidden');
                moon.classList.remove('hidden');
            }
        }
        // Executa após o DOM estar pronto
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', syncIcons);
        } else {
            syncIcons();
        }
    })();

    function toggleTheme() {
        var html  = document.documentElement;
        var isDark = html.classList.toggle('dark');
        try {
            localStorage.setItem('theme', isDark ? 'dark' : 'light');
        } catch (_) {}
        // Atualiza os ícones
        var sun  = document.getElementById('icon-sun');
        var moon = document.getElementById('icon-moon');
        if (sun && moon) {
            if (isDark) {
                sun.classList.remove('hidden');
                moon.classList.add('hidden');
            } else {
                sun.classList.add('hidden');
                moon.classList.remove('hidden');
            }
        }
    }
</script>
