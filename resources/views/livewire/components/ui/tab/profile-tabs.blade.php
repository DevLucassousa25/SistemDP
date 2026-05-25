<div class="bg-white dark:bg-slate-900 rounded-2xl ring-1 ring-slate-200 dark:ring-slate-700 shadow-sm overflow-hidden">

    @php
        $tabs = [
            ['key' => 'informacoes', 'label' => 'Informações',     'icon' => 'user'],
            ['key' => 'timeline',    'label' => 'Histórico',        'icon' => 'clock'],
        ];

        // Config de cores por tipo de evento
        $typeConfig = [
            'account'  => ['bg' => 'bg-slate-100 dark:bg-slate-700',   'text' => 'text-slate-500 dark:text-slate-400',   'ring' => 'ring-slate-200 dark:ring-slate-600'],
            'task'     => ['bg' => 'bg-blue-50 dark:bg-blue-900/30',    'text' => 'text-blue-600 dark:text-blue-400',     'ring' => 'ring-blue-100 dark:ring-blue-800'],
            'meeting'  => ['bg' => 'bg-indigo-50 dark:bg-indigo-900/30','text' => 'text-indigo-600 dark:text-indigo-400', 'ring' => 'ring-indigo-100 dark:ring-indigo-800'],
            'feed'     => ['bg' => 'bg-violet-50 dark:bg-violet-900/30','text' => 'text-violet-600 dark:text-violet-400', 'ring' => 'ring-violet-100 dark:ring-violet-800'],
            'read'     => ['bg' => 'bg-teal-50 dark:bg-teal-900/30',   'text' => 'text-teal-600 dark:text-teal-400',     'ring' => 'ring-teal-100 dark:ring-teal-800'],
            'feedback' => ['bg' => 'bg-rose-50 dark:bg-rose-900/30',   'text' => 'text-rose-600 dark:text-rose-400',     'ring' => 'ring-rose-100 dark:ring-rose-800'],
            'survey'   => ['bg' => 'bg-amber-50 dark:bg-amber-900/30', 'text' => 'text-amber-600 dark:text-amber-400',   'ring' => 'ring-amber-100 dark:ring-amber-800'],
            'dpi'      => ['bg' => 'bg-cyan-50 dark:bg-cyan-900/30',   'text' => 'text-cyan-600 dark:text-cyan-400',     'ring' => 'ring-cyan-100 dark:ring-cyan-800'],
            'ouvidoria'=> ['bg' => 'bg-orange-50 dark:bg-orange-900/30','text' => 'text-orange-600 dark:text-orange-400','ring' => 'ring-orange-100 dark:ring-orange-800'],
        ];
    @endphp

    {{-- ── Tab Nav ──────────────────────────────────────────────────── --}}
    <div class="flex border-b border-slate-100 dark:border-slate-800 px-5 sm:px-8 overflow-x-auto">
        @foreach ($tabs as $tab)
            <button
                wire:click="setTab('{{ $tab['key'] }}')"
                class="flex items-center gap-2 py-4 px-1 mr-6 text-sm font-medium transition-all duration-150 cursor-pointer whitespace-nowrap border-b-2
                       {{ $activeTab === $tab['key']
                            ? 'text-slate-800 dark:text-white border-indigo-500'
                            : 'text-slate-400 dark:text-slate-500 border-transparent hover:text-slate-600 dark:hover:text-slate-300' }}">
                <x-dynamic-component :component="'lucide-' . $tab['icon']" class="w-3.5 h-3.5" />
                {{ $tab['label'] }}
            </button>
        @endforeach
    </div>

    {{-- ── Tab: Informações ─────────────────────────────────────────── --}}
    @if ($activeTab === 'informacoes')
        <div class="p-5 sm:p-8">
            <h2 class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-5">
                Dados do Cadastro
            </h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">

                {{-- Nome --}}
                <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl ring-1 ring-slate-100 dark:ring-slate-700/50">
                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-600 flex items-center justify-center shrink-0">
                        <x-lucide-user class="w-4 h-4 text-slate-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-1">Nome Completo</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $user->name ?? '—' }}</p>
                    </div>
                </div>

                {{-- E-mail --}}
                <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl ring-1 ring-slate-100 dark:ring-slate-700/50">
                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-600 flex items-center justify-center shrink-0">
                        <x-lucide-mail class="w-4 h-4 text-slate-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-1">E-mail</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $user->email ?? '—' }}</p>
                    </div>
                </div>

                {{-- Departamento --}}
                <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl ring-1 ring-slate-100 dark:ring-slate-700/50">
                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-600 flex items-center justify-center shrink-0">
                        <x-lucide-building-2 class="w-4 h-4 text-slate-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-1">Departamento</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $user->department?->name ?? '—' }}</p>
                    </div>
                </div>

                {{-- Cargo --}}
                <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl ring-1 ring-slate-100 dark:ring-slate-700/50">
                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-600 flex items-center justify-center shrink-0">
                        <x-lucide-briefcase class="w-4 h-4 text-slate-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-1">Cargo</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $user->position ?? '—' }}</p>
                    </div>
                </div>

                {{-- Perfil de Acesso --}}
                <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl ring-1 ring-slate-100 dark:ring-slate-700/50">
                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-600 flex items-center justify-center shrink-0">
                        <x-lucide-shield class="w-4 h-4 text-slate-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-1">Perfil de Acesso</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $user->accessProfile?->name ?? '—' }}</p>
                    </div>
                </div>

                {{-- Cadastrado em --}}
                <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl ring-1 ring-slate-100 dark:ring-slate-700/50">
                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-600 flex items-center justify-center shrink-0">
                        <x-lucide-calendar class="w-4 h-4 text-slate-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-1">Cadastrado em</p>
                        <p class="text-sm font-semibold text-slate-800 dark:text-white">
                            {{ $user->created_at?->format('d/m/Y \à\s H:i') ?? '—' }}
                        </p>
                    </div>
                </div>

                {{-- Gestor(es) --}}
                <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl ring-1 ring-slate-100 dark:ring-slate-700/50
                            {{ $managers->count() > 1 ? 'sm:col-span-2' : '' }}">
                    <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-600 flex items-center justify-center shrink-0 mt-0.5">
                        <x-lucide-user-check class="w-4 h-4 text-slate-400" />
                    </div>
                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-2">
                            {{ $managers->count() > 1 ? 'Gestores Diretos' : 'Gestor Direto' }}
                        </p>

                        @if ($managers->isEmpty())
                            <p class="text-sm font-semibold text-slate-400 dark:text-slate-500">Não informado</p>
                        @elseif ($managers->count() === 1)
                            @php $manager = $managers->first() @endphp
                            <div class="flex items-center gap-2.5">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center shrink-0">
                                    <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                        {{ strtoupper(substr($manager->name, 0, 1)) }}
                                    </span>
                                </div>
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $manager->name }}</p>
                                    <p class="text-xs text-slate-400 truncate">{{ $manager->email }}</p>
                                </div>
                            </div>
                        @else
                            <div class="flex flex-wrap gap-2">
                                @foreach ($managers as $manager)
                                    <div class="flex items-center gap-2 bg-white dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-600 rounded-lg px-3 py-2">
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 dark:bg-indigo-900/40 flex items-center justify-center shrink-0">
                                            <span class="text-xs font-bold text-indigo-600 dark:text-indigo-400">
                                                {{ strtoupper(substr($manager->name, 0, 1)) }}
                                            </span>
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-slate-800 dark:text-white truncate">{{ $manager->name }}</p>
                                            <p class="text-xs text-slate-400 truncate">{{ $manager->email }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                {{-- Bio --}}
                @if ($user->bio)
                    <div class="flex items-start gap-3 p-4 bg-slate-50 dark:bg-slate-800/60 rounded-xl ring-1 ring-slate-100 dark:ring-slate-700/50 sm:col-span-2">
                        <div class="w-8 h-8 rounded-lg bg-white dark:bg-slate-700 ring-1 ring-slate-200 dark:ring-slate-600 flex items-center justify-center shrink-0 mt-0.5">
                            <x-lucide-file-text class="w-4 h-4 text-slate-400" />
                        </div>
                        <div class="min-w-0">
                            <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500 mb-1">Bio</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300 leading-relaxed">{{ $user->bio }}</p>
                        </div>
                    </div>
                @endif

            </div>
        </div>
    @endif

    {{-- ── Tab: Timeline / Histórico ───────────────────────────────── --}}
    @if ($activeTab === 'timeline')
        <div class="p-5 sm:p-8">

            <div class="flex items-center justify-between mb-6">
                <div>
                    <h2 class="text-xs font-semibold tracking-widest uppercase text-slate-400 dark:text-slate-500">
                        Histórico de Atividades
                    </h2>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">
                        {{ count($this->timeline) }} evento(s) registrado(s)
                    </p>
                </div>

                {{-- Legenda --}}
                <div class="hidden sm:flex items-center gap-3 flex-wrap justify-end">
                    @foreach ([
                        ['label' => 'Tarefas',    'class' => 'bg-blue-500'],
                        ['label' => 'Reuniões',   'class' => 'bg-indigo-500'],
                        ['label' => 'Feed',       'class' => 'bg-violet-500'],
                        ['label' => 'Leituras',   'class' => 'bg-teal-500'],
                        ['label' => 'Feedback',   'class' => 'bg-rose-500'],
                        ['label' => 'Pesquisa',   'class' => 'bg-amber-500'],
                        ['label' => 'DPI',        'class' => 'bg-cyan-500'],
                        ['label' => 'Ouvidoria',  'class' => 'bg-orange-500'],
                    ] as $leg)
                        <span class="flex items-center gap-1 text-xs text-slate-500 dark:text-slate-400">
                            <span class="w-2 h-2 rounded-full {{ $leg['class'] }}"></span>
                            {{ $leg['label'] }}
                        </span>
                    @endforeach
                </div>
            </div>

            @if (count($this->timeline) <= 1)
                <div class="flex flex-col items-center justify-center py-16 text-center">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center mb-3">
                        <x-lucide-clock class="w-7 h-7 text-slate-300 dark:text-slate-600" />
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Sem histórico de atividades</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Este usuário ainda não realizou ações no sistema.</p>
                </div>
            @else
                {{-- Agrupar por mês --}}
                @php
                    $grouped = collect($this->timeline)->groupBy(fn ($e) => $e['date']->format('Y-m'));
                @endphp

                <div class="space-y-8">
                    @foreach ($grouped as $monthKey => $events)
                        @php
                            $monthLabel = \Carbon\Carbon::createFromFormat('Y-m', $monthKey)->translatedFormat('F \d\e Y');
                        @endphp

                        <div>
                            {{-- Cabeçalho do mês --}}
                            <div class="flex items-center gap-3 mb-4">
                                <span class="text-xs font-bold tracking-widest uppercase text-slate-400 dark:text-slate-500">
                                    {{ $monthLabel }}
                                </span>
                                <div class="flex-1 h-px bg-slate-100 dark:bg-slate-800"></div>
                                <span class="text-xs text-slate-400 dark:text-slate-500">
                                    {{ count($events) }}
                                </span>
                            </div>

                            {{-- Eventos do mês --}}
                            <div class="relative">
                                {{-- Linha vertical --}}
                                <div class="absolute left-4 top-0 bottom-0 w-px bg-slate-100 dark:bg-slate-800"></div>

                                <div class="space-y-3">
                                    @foreach ($events as $event)
                                        @php
                                            $cfg = $typeConfig[$event['type']] ?? $typeConfig['account'];
                                        @endphp

                                        <div class="flex items-start gap-4 pl-10 relative">
                                            {{-- Dot + ícone --}}
                                            <div class="absolute left-0 w-8 h-8 rounded-full ring-2 ring-white dark:ring-slate-900
                                                        flex items-center justify-center shrink-0
                                                        {{ $cfg['bg'] }} {{ $cfg['ring'] }} ring-1">
                                                @switch($event['icon'])
                                                    @case('user-plus')
                                                        <x-lucide-user-plus class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                        @break
                                                    @case('check-square')
                                                        <x-lucide-check-square class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                        @break
                                                    @case('calendar')
                                                        <x-lucide-calendar class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                        @break
                                                    @case('message-circle')
                                                        <x-lucide-message-circle class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                        @break
                                                    @case('book-open')
                                                        <x-lucide-book-open class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                        @break
                                                    @case('trending-up')
                                                        <x-lucide-trending-up class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                        @break
                                                    @case('bar-chart-2')
                                                        <x-lucide-bar-chart-2 class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                        @break
                                                    @case('target')
                                                        <x-lucide-target class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                        @break
                                                    @case('megaphone')
                                                        <x-lucide-megaphone class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                        @break
                                                    @default
                                                        <x-lucide-clock class="w-3.5 h-3.5 {{ $cfg['text'] }}" />
                                                @endswitch
                                            </div>

                                            {{-- Conteúdo --}}
                                            <div class="flex-1 min-w-0 pb-3 border-b border-slate-50 dark:border-slate-800/60 last:border-0 last:pb-0">
                                                <div class="flex items-start justify-between gap-2">
                                                    <div class="min-w-0">
                                                        <p class="text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-0.5">
                                                            {{ $event['label'] }}
                                                        </p>
                                                        <p class="text-sm text-slate-800 dark:text-slate-200 leading-snug">
                                                            {{ $event['desc'] }}
                                                        </p>
                                                    </div>
                                                    <time class="text-xs text-slate-400 dark:text-slate-500 shrink-0 mt-0.5 whitespace-nowrap">
                                                        {{ $event['date']->format('d/m H:i') }}
                                                    </time>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @endif
        </div>
    @endif

</div>
