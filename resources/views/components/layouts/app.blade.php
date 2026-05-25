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
            <main class="flex-1 min-h-0 overflow-y-auto bg-[#F8FAFC] dark:bg-slate-900 transition-colors duration-200">
                {{ $slot }}
            </main>

        </div>

    </div>

    {{-- Modal de alertas global (sucesso, erro, info, aviso) --}}
    <livewire:components.ui.modal.alert-modal />

    {{-- Modal de humor diário --}}
    @auth
        @if(!auth()->user()->isAdmin())
            <livewire:components.mood-checkin />
        @endif
    @endauth

    {{-- ── Toast de Notificações ──────────────────────────────────────
         Escuta o evento Livewire "nova-notificacao" e exibe toasts
         empilhados no canto inferior-direito com auto-dismiss de 6 s.
    ────────────────────────────────────────────────────────────────── --}}
    <div
        id="toast-container"
        x-data="{
            toasts: [],
            colorGradient: {
                blue:   'from-blue-500 to-blue-600',
                green:  'from-green-500 to-emerald-600',
                red:    'from-red-500 to-rose-600',
                indigo: 'from-indigo-500 to-indigo-600',
                violet: 'from-violet-500 to-purple-600',
                teal:   'from-teal-500 to-cyan-600',
                amber:  'from-amber-500 to-orange-500',
                rose:   'from-rose-500 to-pink-600',
            },
            playSound() {
                try {
                    const ctx = new (window.AudioContext || window.webkitAudioContext)();
                    // Nota 1: tom mais alto (880 Hz)
                    const o1 = ctx.createOscillator();
                    const g1 = ctx.createGain();
                    o1.connect(g1); g1.connect(ctx.destination);
                    o1.type = 'sine'; o1.frequency.setValueAtTime(880, ctx.currentTime);
                    g1.gain.setValueAtTime(0.15, ctx.currentTime);
                    g1.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.25);
                    o1.start(ctx.currentTime); o1.stop(ctx.currentTime + 0.25);
                    // Nota 2: tom mais baixo (660 Hz) com leve delay
                    const o2 = ctx.createOscillator();
                    const g2 = ctx.createGain();
                    o2.connect(g2); g2.connect(ctx.destination);
                    o2.type = 'sine'; o2.frequency.setValueAtTime(1100, ctx.currentTime + 0.12);
                    g2.gain.setValueAtTime(0.0001, ctx.currentTime + 0.12);
                    g2.gain.linearRampToValueAtTime(0.12, ctx.currentTime + 0.18);
                    g2.gain.exponentialRampToValueAtTime(0.0001, ctx.currentTime + 0.45);
                    o2.start(ctx.currentTime + 0.12); o2.stop(ctx.currentTime + 0.45);
                } catch(e) {}
            },
            add(data) {
                const id = Date.now() + Math.random();
                this.toasts.push({ id, ...data, visible: false });
                this.$nextTick(() => {
                    const t = this.toasts.find(t => t.id === id);
                    if (t) t.visible = true;
                });
                this.playSound();
                setTimeout(() => this.remove(id), 6000);
            },
            remove(id) {
                const t = this.toasts.find(t => t.id === id);
                if (t) {
                    t.visible = false;
                    setTimeout(() => {
                        this.toasts = this.toasts.filter(t => t.id !== id);
                    }, 300);
                }
            }
        }"
        x-on:nova-notificacao.window="add($event.detail)"
        class="fixed bottom-5 right-5 z-[9999] flex flex-col gap-2.5 items-end pointer-events-none"
        style="max-width: 360px; width: calc(100vw - 2.5rem);"
    >
        <template x-for="toast in toasts" :key="toast.id">
            <div
                x-show="toast.visible"
                x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-x-8 scale-95"
                x-transition:enter-end="opacity-100 translate-x-0 scale-100"
                x-transition:leave="transition ease-in duration-200"
                x-transition:leave-start="opacity-100 translate-x-0 scale-100"
                x-transition:leave-end="opacity-0 translate-x-8 scale-95"
                class="pointer-events-auto w-full bg-white dark:bg-slate-800 rounded-2xl shadow-xl
                       ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden flex items-start gap-3 p-3.5"
            >
                {{-- Ícone --}}
                <div
                    class="w-9 h-9 rounded-xl flex items-center justify-center shrink-0 bg-gradient-to-br"
                    :class="colorGradient[toast.color] ?? colorGradient.blue"
                >
                    {{-- file-text --}}
                    <template x-if="toast.icon === 'file-text'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/>
                            <polyline stroke-linecap="round" stroke-linejoin="round" points="14 2 14 8 20 8"/>
                            <line stroke-linecap="round" stroke-linejoin="round" x1="16" y1="13" x2="8" y2="13"/>
                            <line stroke-linecap="round" stroke-linejoin="round" x1="16" y1="17" x2="8" y2="17"/>
                            <polyline stroke-linecap="round" stroke-linejoin="round" points="10 9 9 9 8 9"/>
                        </svg>
                    </template>
                    {{-- clipboard-check --}}
                    <template x-if="toast.icon === 'clipboard-check'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 0 0-2 2v12a2 2 0 0 0 2 2h10a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-2M9 5a2 2 0 0 0 2 2h2a2 2 0 0 0 2-2M9 5a2 2 0 0 0 2-2h2a2 2 0 0 0 2 2"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 14 2 2 4-4"/>
                        </svg>
                    </template>
                    {{-- megaphone --}}
                    <template x-if="toast.icon === 'megaphone'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M11 5.882V19.24a1.76 1.76 0 0 1-3.417.592l-2.147-6.15M18 13a3 3 0 1 0 0-6M5.436 13.683A4.001 4.001 0 0 1 7 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 0 1-1.564-.317z"/>
                        </svg>
                    </template>
                    {{-- calendar --}}
                    <template x-if="toast.icon === 'calendar'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <rect stroke-linecap="round" stroke-linejoin="round" x="3" y="4" width="18" height="18" rx="2" ry="2"/>
                            <line stroke-linecap="round" stroke-linejoin="round" x1="16" y1="2" x2="16" y2="6"/>
                            <line stroke-linecap="round" stroke-linejoin="round" x1="8" y1="2" x2="8" y2="6"/>
                            <line stroke-linecap="round" stroke-linejoin="round" x1="3" y1="10" x2="21" y2="10"/>
                        </svg>
                    </template>
                    {{-- message-circle --}}
                    <template x-if="toast.icon === 'message-circle'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                        </svg>
                    </template>
                    {{-- bar-chart-2 --}}
                    <template x-if="toast.icon === 'bar-chart-2'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <line stroke-linecap="round" stroke-linejoin="round" x1="18" y1="20" x2="18" y2="10"/>
                            <line stroke-linecap="round" stroke-linejoin="round" x1="12" y1="20" x2="12" y2="4"/>
                            <line stroke-linecap="round" stroke-linejoin="round" x1="6" y1="20" x2="6" y2="14"/>
                        </svg>
                    </template>
                    {{-- trending-up --}}
                    <template x-if="toast.icon === 'trending-up'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <polyline stroke-linecap="round" stroke-linejoin="round" points="23 6 13.5 15.5 8.5 10.5 1 18"/>
                            <polyline stroke-linecap="round" stroke-linejoin="round" points="17 6 23 6 23 12"/>
                        </svg>
                    </template>
                    {{-- check-square --}}
                    <template x-if="toast.icon === 'check-square'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m9 11 3 3L22 4"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"/>
                        </svg>
                    </template>
                    {{-- target --}}
                    <template x-if="toast.icon === 'target'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <circle cx="12" cy="12" r="10"/>
                            <circle cx="12" cy="12" r="6"/>
                            <circle cx="12" cy="12" r="2"/>
                        </svg>
                    </template>
                    {{-- book-open --}}
                    <template x-if="toast.icon === 'book-open'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>
                        </svg>
                    </template>
                    {{-- heart --}}
                    <template x-if="toast.icon === 'heart'">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78l1.06 1.06L12 21.23l7.78-7.78 1.06-1.06a5.5 5.5 0 0 0 0-7.78z"/>
                        </svg>
                    </template>
                    {{-- bell (fallback) --}}
                    <template x-if="!['file-text','clipboard-check','megaphone','calendar','message-circle','bar-chart-2','trending-up','check-square','target','book-open','heart'].includes(toast.icon)">
                        <svg class="w-4 h-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0 1 18 14.158V11a6.002 6.002 0 0 0-4-5.659V5a2 2 0 1 0-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 1 1-6 0v-1m6 0H9"/>
                        </svg>
                    </template>
                </div>

                {{-- Texto --}}
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-semibold text-slate-800 dark:text-slate-100 leading-snug truncate" x-text="toast.title"></p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5 leading-snug line-clamp-2" x-text="toast.message"></p>
                </div>

                {{-- Fechar --}}
                <button
                    type="button"
                    x-on:click="remove(toast.id)"
                    class="shrink-0 p-1 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-slate-200
                           hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
                >
                    <svg class="w-3.5 h-3.5" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
        </template>
    </div>

    @stack('scripts')
    @livewireScripts
</body>

</html>
