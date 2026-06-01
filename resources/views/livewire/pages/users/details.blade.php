<div class="min-h-screen bg-slate-50 dark:bg-slate-950">

    @php
        $slug   = $user->accessProfile?->slug ?? '';
        $role   = $user->accessProfile?->name ?? 'Sem perfil';

        // Cores por perfil
        $bannerFrom = match ($slug) {
            'administrator' => '#f43f5e',
            'hr'            => '#8b5cf6',
            'manager'       => '#10b981',
            'employee'      => '#3b82f6',
            default         => '#64748b',
        };
        $bannerTo = match ($slug) {
            'administrator' => '#e11d48',
            'hr'            => '#7c3aed',
            'manager'       => '#0d9488',
            'employee'      => '#4f46e5',
            default         => '#475569',
        };
        $roleGradient = match ($slug) {
            'administrator' => 'from-rose-500 to-rose-700',
            'hr'            => 'from-blue-500 to-violet-700',
            'manager'       => 'from-emerald-500 to-teal-700',
            'employee'      => 'from-blue-500 to-indigo-700',
            default         => 'from-slate-400 to-slate-600',
        };
        $roleText = match ($slug) {
            'administrator' => 'text-rose-600 bg-rose-50 ring-rose-200 dark:bg-rose-900/30 dark:ring-rose-700 dark:text-rose-300',
            'hr'            => 'text-indigo-600 bg-violet-50 ring-violet-200 dark:bg-violet-900/30 dark:ring-violet-700 dark:text-violet-300',
            'manager'       => 'text-emerald-700 bg-emerald-50 ring-emerald-200 dark:bg-emerald-900/30 dark:ring-emerald-700 dark:text-emerald-300',
            'employee'      => 'text-blue-700 bg-blue-50 ring-blue-200 dark:bg-blue-900/30 dark:ring-blue-700 dark:text-blue-300',
            default         => 'text-slate-600 bg-slate-50 ring-slate-200 dark:bg-slate-800 dark:ring-slate-600 dark:text-slate-300',
        };
        $roleIcon = match ($slug) {
            'administrator' => 'shield',
            'hr'            => 'users',
            'manager'       => 'briefcase',
            'employee'      => 'user',
            default         => 'user',
        };

        $parts    = explode(' ', trim($user->name));
        $initials = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
    @endphp

    <div class="max-w-5xl mx-auto px-4 sm:px-6 py-6 space-y-4">

        {{-- ── Botão Voltar ─────────────────────────────────────────── --}}
        <button onclick="history.back()"
            class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400
                   hover:text-slate-800 dark:hover:text-white transition-colors cursor-pointer group">
            <x-lucide-arrow-left class="w-4 h-4 transition-transform group-hover:-translate-x-0.5" />
            Voltar
        </button>

        {{-- ── Card de Perfil ───────────────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-900 rounded-2xl overflow-hidden ring-1 ring-slate-200 dark:ring-slate-700 shadow-sm">

            {{-- ── Banner com avatar integrado ────────────────────────── --}}
            <div class="relative h-36 sm:h-44"
                 style="background: linear-gradient(135deg, {{ $bannerFrom }} 0%, {{ $bannerTo }} 100%);">

                {{-- Ornamentos geométricos --}}
                <div class="absolute inset-0 overflow-hidden">
                    {{-- Círculo grande direita --}}
                    <div class="absolute -right-10 -top-10 w-52 h-52 rounded-full"
                         style="background: rgba(255,255,255,0.08)"></div>
                    {{-- Círculo médio direita-baixo --}}
                    <div class="absolute right-24 -bottom-8 w-32 h-32 rounded-full"
                         style="background: rgba(255,255,255,0.06)"></div>
                    {{-- Círculo pequeno esquerda --}}
                    <div class="absolute -left-6 top-6 w-24 h-24 rounded-full"
                         style="background: rgba(255,255,255,0.05)"></div>
                    {{-- Linha diagonal decorativa --}}
                    <div class="absolute inset-0"
                         style="background-image: repeating-linear-gradient(45deg, rgba(255,255,255,0.03) 0px, rgba(255,255,255,0.03) 1px, transparent 1px, transparent 12px);"></div>
                </div>

                {{-- Conteúdo dentro do banner: ações no topo-direito --}}
                <div class="absolute top-4 right-4 sm:right-5 flex items-center gap-2">
                    <button
                        wire:click="$dispatch('editUser', { id: {{ $user->id }} })"
                        class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium
                               text-white/90 bg-white/15 hover:bg-white/25
                               backdrop-blur-sm border border-white/20
                               px-3 py-1.5 rounded-xl transition cursor-pointer">
                        <x-lucide-pencil class="w-3.5 h-3.5" />
                        Editar
                    </button>

                    @if ($user->is_active)
                        <button
                            wire:click="$dispatch('deactivateUser', { id: {{ $user->id }}, status: 'deactivate' })"
                            class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium
                                   text-white/90 bg-white/15 hover:bg-red-500/50
                                   backdrop-blur-sm border border-white/20
                                   px-3 py-1.5 rounded-xl transition cursor-pointer">
                            <x-lucide-user-x class="w-3.5 h-3.5" />
                            Desativar
                        </button>
                    @else
                        <button
                            wire:click="$dispatch('deactivateUser', { id: {{ $user->id }}, status: 'activate' })"
                            class="inline-flex items-center gap-1.5 text-xs sm:text-sm font-medium
                                   text-white/90 bg-white/15 hover:bg-emerald-500/50
                                   backdrop-blur-sm border border-white/20
                                   px-3 py-1.5 rounded-xl transition cursor-pointer">
                            <x-lucide-user-check class="w-3.5 h-3.5" />
                            Ativar
                        </button>
                    @endif
                </div>

                {{-- Avatar posicionado na parte inferior do banner --}}
                <div class="absolute bottom-0 left-5 sm:left-8 translate-y-1/2">
                    <div class="relative">
                        {{-- Círculo avatar --}}
                        <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl
                                    ring-4 ring-white dark:ring-slate-900
                                    bg-gradient-to-br {{ $roleGradient }}
                                    flex items-center justify-center shadow-xl">
                            <span class="text-white font-black text-2xl sm:text-3xl tracking-tight select-none">
                                {{ $initials }}
                            </span>
                        </div>
                        {{-- Badge de status --}}
                        <div class="absolute -bottom-1 -right-1
                                    {{ $user->is_active ? 'bg-emerald-500' : 'bg-slate-400' }}
                                    w-5 h-5 rounded-full ring-2 ring-white dark:ring-slate-900
                                    flex items-center justify-center">
                            @if ($user->is_active)
                                <span class="w-2 h-2 rounded-full bg-white animate-ping absolute"></span>
                                <span class="w-2 h-2 rounded-full bg-white"></span>
                            @else
                                <span class="w-2 h-2 rounded-full bg-white/70"></span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            {{-- ── Info abaixo do banner ────────────────────────────────── --}}
            <div class="px-5 sm:px-8 pt-14 sm:pt-16 pb-6">

                {{-- Nome + badges --}}
                <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-3">
                    <div>
                        <div class="flex flex-wrap items-center gap-2 mb-1">
                            <h1 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white leading-tight">
                                {{ $user->name }}
                            </h1>
                            {{-- Badge status --}}
                            @if ($user->is_active)
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full
                                             bg-emerald-50 text-emerald-700 ring-1 ring-emerald-200
                                             dark:bg-emerald-900/30 dark:text-emerald-300 dark:ring-emerald-700">
                                    Ativo
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-xs font-semibold px-2.5 py-0.5 rounded-full
                                             bg-slate-100 text-slate-500 ring-1 ring-slate-200
                                             dark:bg-slate-800 dark:text-slate-400 dark:ring-slate-600">
                                    Inativo
                                </span>
                            @endif
                        </div>

                        {{-- Cargo + Departamento --}}
                        <p class="text-sm text-slate-500 dark:text-slate-400">
                            {{ $user->position ?? 'Cargo não informado' }}
                            @if ($user->department)
                                <span class="mx-1.5 text-slate-300 dark:text-slate-600">·</span>
                                <span class="text-slate-600 dark:text-slate-300 font-medium">{{ $user->department->name }}</span>
                            @endif
                        </p>

                        {{-- Meta row --}}
                        <div class="flex flex-wrap items-center gap-x-4 gap-y-1.5 mt-3">
                            <span class="inline-flex items-center gap-1.5 text-sm text-slate-500 dark:text-slate-400">
                                <x-lucide-mail class="w-3.5 h-3.5 shrink-0 text-slate-400" />
                                {{ $user->email }}
                            </span>
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold px-2.5 py-1 rounded-lg ring-1 {{ $roleText }}">
                                <x-dynamic-component :component="'lucide-' . $roleIcon" class="w-3 h-3" />
                                {{ $role }}
                            </span>
                            @if ($user->created_at)
                                <span class="inline-flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-500">
                                    <x-lucide-calendar class="w-3.5 h-3.5" />
                                    Desde {{ $user->created_at->format('d/m/Y') }}
                                </span>
                            @endif
                        </div>

                        @if ($user->bio)
                            <p class="mt-3 text-sm text-slate-500 dark:text-slate-400 leading-relaxed max-w-xl">
                                {{ $user->bio }}
                            </p>
                        @endif
                    </div>
                </div>

                {{-- ── Stats ────────────────────────────────────────────── --}}
                <div class="grid grid-cols-3 sm:grid-cols-6 gap-2.5 mt-6 pt-6 border-t border-slate-100 dark:border-slate-800">
                    @php
                        $statItems = [
                            ['label' => 'Tarefas',   'value' => $this->stats['tarefas'],   'icon' => 'check-square',   'color' => 'text-blue-500',   'bg' => 'bg-blue-50 dark:bg-blue-900/20'],
                            ['label' => 'Reuniões',  'value' => $this->stats['reunioes'],  'icon' => 'calendar',       'color' => 'text-indigo-500', 'bg' => 'bg-indigo-50 dark:bg-indigo-900/20'],
                            ['label' => 'Feed',      'value' => $this->stats['feed'],      'icon' => 'message-circle', 'color' => 'text-indigo-500', 'bg' => 'bg-violet-50 dark:bg-violet-900/20'],
                            ['label' => 'Leituras',  'value' => $this->stats['leituras'],  'icon' => 'book-open',      'color' => 'text-teal-500',   'bg' => 'bg-teal-50 dark:bg-teal-900/20'],
                            ['label' => 'Feedbacks', 'value' => $this->stats['feedbacks'], 'icon' => 'trending-up',    'color' => 'text-rose-500',   'bg' => 'bg-rose-50 dark:bg-rose-900/20'],
                            ['label' => 'Pesquisas', 'value' => $this->stats['pesquisas'], 'icon' => 'bar-chart-2',    'color' => 'text-amber-500',  'bg' => 'bg-amber-50 dark:bg-amber-900/20'],
                        ];
                    @endphp

                    @foreach ($statItems as $stat)
                        <div class="flex flex-col items-center gap-2 py-3 px-2 rounded-xl
                                    bg-slate-50 dark:bg-slate-800/50
                                    ring-1 ring-slate-100 dark:ring-slate-700/50">
                            <div class="w-8 h-8 rounded-lg flex items-center justify-center {{ $stat['bg'] }}">
                                <x-dynamic-component :component="'lucide-' . $stat['icon']"
                                    class="w-4 h-4 {{ $stat['color'] }}" />
                            </div>
                            <div class="text-center">
                                <p class="text-lg font-bold text-slate-800 dark:text-white leading-none">
                                    {{ $stat['value'] }}
                                </p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                                    {{ $stat['label'] }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>

            </div>
        </div>

        {{-- ── Tabs ─────────────────────────────────────────────────── --}}
        <div>
            <livewire:components.ui.tab.profile-tabs :user="$user" />
        </div>

    </div>

    {{-- Modais --}}
    <livewire:components.ui.modal.modal-create />
    <livewire:components.ui.modal.user-created-modal />
    <livewire:components.ui.modal.deactivate-user-modal />
    <livewire:components.ui.modal.alert-modal />

</div>
