<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    {{-- ─── Anti-flash: aplica o tema ANTES da renderização ──────── --}}
    <script>
        (function () {
            try {
                var stored = localStorage.getItem('theme');
                if (stored === 'dark' || (!stored && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
                    document.documentElement.classList.add('dark');
                } else {
                    document.documentElement.classList.remove('dark');
                }
            } catch (_) {}
        })();
    </script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,300..700;1,14..32,300..700&family=Plus+Jakarta+Sans:ital,wght@0,400;0,500;0,600;0,700;0,800;1,400;1,500;1,600;1,700;1,800&display=swap" rel="stylesheet">

    <title>{{ $title ?? config('app.name') }}</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @livewireStyles
</head>

<body class="bg-slate-100 dark:bg-slate-900 h-screen overflow-hidden transition-colors duration-200">

    <div class="flex h-screen overflow-hidden">

        <!-- Sidebar -->
        <livewire:layout.sidebar />

        <!-- Área da direita -->
        <div class="flex flex-col flex-1 min-w-0 overflow-hidden">

            <!-- Header -->
            <livewire:layout.header />

            <!-- Conteúdo da página -->
            <main class="flex-1 min-h-0 overflow-y-auto p-0 lg:p-8 bg-[#F8FAFC] dark:bg-slate-900 transition-colors duration-200">
                {{ $slot }}
            </main>

        </div>

    </div>

    @livewireScripts
</body>

</html>
