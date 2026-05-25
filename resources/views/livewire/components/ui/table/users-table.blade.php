<div class="p-0 md:p-6">

    <!-- Header -->
 <div class="bg-white p-4 rounded-xl border border-gray-100 flex flex-col gap-3 mb-5">

    <!-- Linha superior: Busca -->
    <div class="flex flex-col gap-3">

    <!-- Busca -->
    <div class="relative w-full">
        <x-lucide-search class="w-4 h-4 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" />
        <input
            type="text"
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar usuário..."
            class="w-full pl-10 pr-9 py-2.5 text-sm border border-gray-200 rounded-xl
                focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent
                placeholder-gray-400"
        />
        @if ($search)
            <button wire:click="$set('search', '')"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600">
                <x-lucide-x class="w-3.5 h-3.5" />
            </button>
        @endif
    </div>

    <!-- Filtros -->
    <div class="grid grid-cols-3 gap-2">

        {{-- Filtro Departamento --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white transition
                {{ $department ? 'border-green-400 bg-green-50 text-green-700' : 'border-gray-200 text-gray-500' }}">
                <div class="flex items-center gap-1.5 truncate">
                    <x-lucide-building-2 class="w-3.5 h-3.5 flex-shrink-0" />
                    <span class="truncate text-xs font-medium">
                        {{ $department ?: 'Departamento' }}
                    </span>
                </div>
                <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                    x-bind:class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                class="absolute mt-1 w-56 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-1">
                <button @click="open=false; $wire.set('department', '')"
                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2">
                    <x-lucide-building-2 class="w-3.5 h-3.5" /> Todos departamentos
                </button>
                <div class="border-t border-gray-100 my-1"></div>
                <div class="overflow-y-auto max-h-48">
                    @foreach (\App\Models\Department::pluck('name') as $dept)
                        <button @click="open=false; $wire.set('department', '{{ $dept }}')"
                            class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700 flex items-center justify-between
                            {{ $department === $dept ? 'bg-green-50 text-green-700 font-medium' : '' }}">
                            {{ $dept }}
                            @if ($department === $dept)
                                <x-lucide-check class="w-3.5 h-3.5 text-green-500" />
                            @endif
                        </button>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- Filtro Status --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white transition
                {{ $status !== '' ? 'border-green-400 bg-green-50 text-green-700' : 'border-gray-200 text-gray-500' }}">
                <div class="flex items-center gap-1.5 truncate">
                    @if ($status === 'true')
                        <span class="w-2 h-2 rounded-full bg-emerald-400 flex-shrink-0"></span>
                    @elseif ($status === 'false')
                        <span class="w-2 h-2 rounded-full bg-red-400 flex-shrink-0"></span>
                    @else
                        <x-lucide-circle-check class="w-3.5 h-3.5 flex-shrink-0" />
                    @endif
                    <span class="truncate text-xs font-medium">
                        {{ match($status) {
                            'true'  => 'Ativo',
                            'false' => 'Inativo',
                            default => 'Status',
                        } }}
                    </span>
                </div>
                <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                    x-bind:class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                class="absolute mt-1 w-40 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-1">
                <button @click="open=false; $wire.set('status', '')"
                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2">
                    <x-lucide-circle-check class="w-3.5 h-3.5" /> Todos
                </button>
                <div class="border-t border-gray-100 my-1"></div>
                <button @click="open=false; $wire.set('status', 'true')"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm hover:bg-emerald-50
                    {{ $status === 'true' ? 'bg-emerald-50 font-medium text-emerald-700' : 'text-gray-700' }}">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span> Ativo
                    @if ($status === 'true')
                        <x-lucide-check class="w-3.5 h-3.5 ml-auto text-emerald-500" />
                    @endif
                </button>
                <button @click="open=false; $wire.set('status', 'false')"
                    class="w-full flex items-center gap-2 px-3 py-2 text-sm hover:bg-red-50
                    {{ $status === 'false' ? 'bg-red-50 font-medium text-red-600' : 'text-gray-700' }}">
                    <span class="w-2 h-2 rounded-full bg-red-400"></span> Inativo
                    @if ($status === 'false')
                        <x-lucide-check class="w-3.5 h-3.5 ml-auto text-red-500" />
                    @endif
                </button>
            </div>
        </div>

        {{-- Filtro Perfil de Acesso --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                class="w-full flex items-center justify-between text-sm border rounded-xl px-3 py-2.5 bg-white transition
                {{ $accessProfile ? 'border-green-400 bg-green-50 text-green-700' : 'border-gray-200 text-gray-500' }}">
                <div class="flex items-center gap-1.5 truncate">
                    <x-lucide-user-cog class="w-3.5 h-3.5 flex-shrink-0" />
                    <span class="truncate text-xs font-medium">
                        {{ $accessProfile ? \App\Models\AccessProfile::find($accessProfile)?->name : 'Acesso' }}
                    </span>
                </div>
                <x-lucide-chevron-down class="w-3.5 h-3.5 flex-shrink-0 transition-transform"
                    x-bind:class="open ? 'rotate-180' : ''" />
            </button>
            <div x-show="open" @click.outside="open = false" x-transition
                class="absolute right-0 mt-1 w-52 bg-white border border-gray-200 rounded-lg shadow-lg z-20 py-1">
                <button @click="open=false; $wire.set('accessProfile', '')"
                    class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-500 flex items-center gap-2">
                    <x-lucide-user-cog class="w-3.5 h-3.5" /> Todos acessos
                </button>
                <div class="border-t border-gray-100 my-1"></div>
                @foreach (\App\Models\AccessProfile::pluck('name', 'id') as $id => $profile)
                    <button @click="open=false; $wire.set('accessProfile', '{{ $id }}')"
                        class="w-full text-left px-3 py-2 text-sm hover:bg-gray-50 text-gray-700 flex items-center justify-between
                        {{ $accessProfile == $id ? 'bg-green-50 text-green-700 font-medium' : '' }}">
                        {{ $profile }}
                        @if ($accessProfile == $id)
                            <x-lucide-check class="w-3.5 h-3.5 text-green-500" />
                        @endif
                    </button>
                @endforeach
            </div>
        </div>

    </div>

</div>

    {{-- Chips de filtros ativos --}}
    @php
        $temFiltros = $search || $department || $status !== '' || $accessProfile;
    @endphp

    @if ($temFiltros)
        <div class="flex items-center gap-2 flex-wrap">
            <span class="text-xs text-gray-400">Filtros:</span>

            @if ($search)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-slate-100 text-slate-600 text-xs font-medium">
                    <x-lucide-search class="w-3 h-3" />
                    "{{ Str::limit($search, 20) }}"
                    <button wire:click="$set('search', '')" class="ml-0.5 hover:text-slate-900 transition">
                        <x-lucide-x class="w-3 h-3" />
                    </button>
                </span>
            @endif

            @if ($department)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-medium">
                    <x-lucide-building-2 class="w-3 h-3" />
                    {{ $department }}
                    <button wire:click="$set('department', '')" class="ml-0.5 hover:text-indigo-900 transition">
                        <x-lucide-x class="w-3 h-3" />
                    </button>
                </span>
            @endif

            @if ($status !== '')
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-medium
                    {{ $status === 'true' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-600' }}">
                    <span class="w-1.5 h-1.5 rounded-full {{ $status === 'true' ? 'bg-emerald-400' : 'bg-red-400' }}"></span>
                    {{ $status === 'true' ? 'Ativo' : 'Inativo' }}
                    <button wire:click="$set('status', '')" class="ml-0.5 transition">
                        <x-lucide-x class="w-3 h-3" />
                    </button>
                </span>
            @endif

            @if ($accessProfile)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-100 text-indigo-700 text-xs font-medium">
                    <x-lucide-user-cog class="w-3 h-3" />
                    {{ \App\Models\AccessProfile::find($accessProfile)?->name }}
                    <button wire:click="$set('accessProfile', '')" class="ml-0.5 hover:text-indigo-900 transition">
                        <x-lucide-x class="w-3 h-3" />
                    </button>
                </span>
            @endif

            <button wire:click="limparFiltros"
                class="ml-auto text-xs text-gray-400 hover:text-red-500 transition flex items-center gap-1">
                <x-lucide-filter-x class="w-3.5 h-3.5" />
                Limpar tudo
            </button>
        </div>
    @endif

</div>

    {{-- ═══════════════════════════════════════════════
         SKELETON — exibido enquanto o Livewire carrega
    ═══════════════════════════════════════════════ --}}
    <div wire:loading class="w-full">

        {{-- Skeleton Mobile --}}
        <div class="md:hidden flex flex-col gap-3">
            @for ($i = 0; $i < 5; $i++)
                <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-start justify-between gap-3 shadow-sm animate-pulse">
                    <div class="flex items-center gap-3 min-w-0 flex-1">
                        <div class="w-11 h-11 rounded-full bg-gray-200 shrink-0"></div>
                        <div class="min-w-0 flex-1 space-y-2">
                            <div class="h-3.5 bg-gray-200 rounded-full w-2/3"></div>
                            <div class="h-3 bg-gray-200 rounded-full w-4/5"></div>
                            <div class="h-3 bg-gray-200 rounded-full w-1/2"></div>
                            <div class="flex gap-2 mt-2">
                                <div class="h-5 w-20 bg-gray-200 rounded-full"></div>
                                <div class="h-5 w-14 bg-gray-200 rounded-full"></div>
                            </div>
                        </div>
                    </div>
                    <div class="w-8 h-8 bg-gray-200 rounded-lg shrink-0 mt-0.5"></div>
                </div>
            @endfor
        </div>

        {{-- Skeleton Desktop --}}
        <div class="hidden md:block w-full overflow-hidden border border-gray-100 rounded-xl">
            <table class="w-full text-sm">
                <thead class="bg-gray-50 border-b border-gray-200">
                    <tr>
                        <th class="px-4 py-3 w-10">
                            <div class="w-4 h-4 bg-gray-200 rounded animate-pulse"></div>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <div class="h-3 bg-gray-200 rounded-full w-16 animate-pulse"></div>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <div class="h-3 bg-gray-200 rounded-full w-24 animate-pulse"></div>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <div class="h-3 bg-gray-200 rounded-full w-12 animate-pulse"></div>
                        </th>
                        <th class="px-4 py-3 text-left">
                            <div class="h-3 bg-gray-200 rounded-full w-14 animate-pulse"></div>
                        </th>
                        <th class="px-4 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @for ($i = 0; $i < 8; $i++)
                        <tr>
                            <td class="px-4 py-3.5">
                                <div class="w-4 h-4 bg-gray-200 rounded animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-gray-200 shrink-0 animate-pulse"></div>
                                    <div class="space-y-2">
                                        <div class="h-3 bg-gray-200 rounded-full w-32 animate-pulse"></div>
                                        <div class="h-2.5 bg-gray-200 rounded-full w-44 animate-pulse"></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="h-3 bg-gray-200 rounded-full w-24 animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="h-6 bg-gray-200 rounded-full w-20 animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3.5">
                                <div class="h-6 bg-gray-200 rounded-full w-16 animate-pulse"></div>
                            </td>
                            <td class="px-4 py-3.5 text-right">
                                <div class="w-8 h-8 bg-gray-200 rounded-lg ml-auto animate-pulse"></div>
                            </td>
                        </tr>
                    @endfor
                </tbody>
            </table>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════
         CONTEÚDO REAL — oculto durante o carregamento
    ═══════════════════════════════════════════════ --}}
    <div wire:loading.remove>

        {{-- ───────────────────────────────────────────
             MOBILE — List view (visível apenas em telas < md)
        ─────────────────────────────────────────────── --}}
        <div class="md:hidden flex flex-col gap-3">

            @forelse($users as $user)
                @php
                    $active = $user->is_active === true;
                    $role   = $user->accessProfile->name ?? '—';
                    $colors = [
                        'CEO'           => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 ring-amber-300 dark:ring-amber-700',
                        'Administrador' => 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 ring-red-200 dark:ring-red-800',
                        'Gerente de RH' => 'bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 ring-teal-200 dark:ring-teal-700',
                        'RH'            => 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 ring-purple-200 dark:ring-purple-800',
                        'Gerente'       => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 ring-blue-200 dark:ring-blue-700',
                        'Funcionário'   => 'bg-gray-50 dark:bg-slate-700 text-gray-600 dark:text-slate-300 ring-gray-200 dark:ring-slate-600',
                    ];
                @endphp

                <div class="bg-white border border-gray-100 rounded-xl p-4 flex items-start justify-between gap-3 shadow-sm">
                    <div class="flex items-center gap-3 min-w-0">
                        @if($user->avatar)
                            <img src="{{ $user->avatarUrl() }}"
                                 class="w-11 h-11 rounded-full object-cover border border-gray-200 dark:border-slate-600 shrink-0" />
                        @else
                            <div class="w-11 h-11 rounded-full bg-gradient-to-br {{ $user->avatarColor() }} flex items-center justify-center shrink-0">
                                <span class="text-sm lato-bold text-white select-none">{{ $user->initials() }}</span>
                            </div>
                        @endif
                        <div class="min-w-0">
                            <p class="font-semibold text-gray-900 text-sm leading-tight truncate">{{ $user->name }}</p>
                            <p class="text-xs text-gray-400 truncate">{{ $user->email }}</p>
                            <p class="text-xs text-gray-500 mt-1 flex items-center gap-1">
                                <x-lucide-building-2 class="w-3 h-3 shrink-0" />
                                {{ $user->department->name ?? '—' }}
                            </p>
                            <div class="flex flex-wrap items-center gap-1.5 mt-2">
                                <span
                                    class="inline-flex items-center px-2 py-0.5 text-xs font-medium rounded-full ring-1
                                    {{ $colors[$role] ?? 'bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-slate-300 ring-gray-200 dark:ring-slate-600' }}">
                                    {{ $role }}
                                </span>
                                <span
                                    class="inline-flex items-center gap-1.5 px-2 py-0.5 text-xs font-medium rounded-full
                                    {{ $active ? 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 ring-1 ring-green-200 dark:ring-green-800' : 'bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-slate-400 ring-1 ring-gray-200 dark:ring-slate-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-green-500 dark:bg-green-400' : 'bg-gray-400 dark:bg-slate-500' }}"></span>
                                    {{ $active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </div>
                        </div>
                    </div>
                    <button
                        onclick="toggleDropdown(event, {{ $user->id }}, {{ $user->is_active ? 'true' : 'false' }})"
                        class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition shrink-0 mt-0.5">
                        <x-lucide-more-horizontal class="w-5 h-5" />
                    </button>
                </div>

            @empty
                <div class="text-center py-10 text-gray-400 text-sm">
                    Nenhum usuário encontrado
                </div>
            @endforelse

            {{ $users->links('partials.pagination') }}

        </div>

        {{-- ───────────────────────────────────────────
             DESKTOP — Table (visível apenas em telas >= md)
        ─────────────────────────────────────────────── --}}
        <div class="hidden md:block overflow-hidden border border-gray-100 rounded-xl">
            <table class="w-full text-sm overflow-visible">

                <thead class="bg-gray-50 border-b border-gray-200 text-gray-500 text-xs uppercase tracking-wide">
                    <tr>
                        <th class="px-4 py-3">
                            <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        </th>
                        <th class="px-4 py-3 text-left">Usuário</th>
                        <th class="px-4 py-3 text-left">Departamento</th>
                        <th class="px-4 py-3 text-left">Perfil</th>
                        <th class="px-4 py-3 text-left">Status</th>
                        <th class="px-4 py-3 text-right"></th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($users as $user)
                        @php
                            $active = $user->is_active === true;
                            $role   = $user->accessProfile->name ?? '—';
                            $colors = [
                                'CEO'           => 'bg-amber-50 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400 ring-amber-300 dark:ring-amber-700',
                                'Administrador' => 'bg-red-50 dark:bg-red-900/30 text-red-600 dark:text-red-400 ring-red-200 dark:ring-red-800',
                                'Gerente de RH' => 'bg-teal-50 dark:bg-teal-900/30 text-teal-700 dark:text-teal-400 ring-teal-200 dark:ring-teal-700',
                                'RH'            => 'bg-purple-50 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400 ring-purple-200 dark:ring-purple-800',
                                'Gerente'       => 'bg-blue-50 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400 ring-blue-200 dark:ring-blue-700',
                                'Funcionário'   => 'bg-gray-50 dark:bg-slate-700 text-gray-600 dark:text-slate-300 ring-gray-200 dark:ring-slate-600',
                            ];
                        @endphp

                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-4 py-3">
                                <input type="checkbox" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                            </td>
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    @if($user->avatar)
                                        <img src="{{ $user->avatarUrl() }}"
                                             class="w-10 h-10 rounded-full object-cover border border-gray-200 dark:border-slate-600 shrink-0" />
                                    @else
                                        <div class="w-10 h-10 rounded-full bg-gradient-to-br {{ $user->avatarColor() }} flex items-center justify-center shrink-0">
                                            <span class="text-sm lato-bold text-white select-none">{{ $user->initials() }}</span>
                                        </div>
                                    @endif
                                    <div>
                                        <p class="font-medium text-gray-900 leading-tight">{{ $user->name }}</p>
                                        <p class="text-gray-500 text-xs">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-gray-600">{{ $user->department->name ?? '—' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center px-2.5 py-1 text-xs font-medium rounded-full ring-1
                                    {{ $colors[$role] ?? 'bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-slate-300 ring-gray-200 dark:ring-slate-600' }}">
                                    {{ $role }}
                                </span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="inline-flex items-center gap-2 px-2.5 py-1 text-xs font-medium rounded-full
                                    {{ $active ? 'bg-green-50 dark:bg-green-900/30 text-green-600 dark:text-green-400 ring-1 ring-green-200 dark:ring-green-800' : 'bg-gray-50 dark:bg-slate-700 text-gray-500 dark:text-slate-400 ring-1 ring-gray-200 dark:ring-slate-600' }}">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $active ? 'bg-green-500 dark:bg-green-400' : 'bg-gray-400 dark:bg-slate-500' }}"></span>
                                    {{ $active ? 'Ativo' : 'Inativo' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right">
                                <button
                                    onclick="toggleDropdown(event, {{ $user->id }}, {{ $user->is_active ? 'true' : 'false' }})"
                                    class="p-2 rounded-lg hover:bg-gray-100 text-gray-400 hover:text-gray-600 transition cursor-pointer">
                                    <x-lucide-more-horizontal class="w-5 h-5" />
                                </button>
                            </td>
                        </tr>

                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-10 text-gray-400">Nenhum usuário encontrado</td>
                        </tr>
                    @endforelse
                </tbody>

            </table>

            <div class="bg-white border-t border-gray-100 px-4">
                {{ $users->links('partials.pagination') }}
            </div>
        </div>

    </div>{{-- fim wire:loading.remove --}}


    <!-- Dropdown único fixo fora da tabela -->
    <div id="dropdown" class="hidden fixed w-44 bg-white border border-gray-200 rounded-lg shadow-lg z-50">

        <a href="#" id="dropdown-profile-btn" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100">
            <x-lucide-eye class="w-4 h-4" /> Ver perfil
        </a>

        <a href="#" id="dropdown-edit-btn" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100">
            <x-lucide-pencil class="w-4 h-4" /> Editar
        </a>

        <a href="#" id="dropdown-deactivate-btn" class="flex items-center gap-2 px-4 py-2 text-sm hover:bg-gray-100">
            <span id="dropdown-deactivate-icon"></span>
            <span id="dropdown-deactivate-text"></span>
        </a>

    </div>

    <script>
        function toggleDropdown(event, userId, is_active) {

            const dropdown = document.getElementById('dropdown');

            const editBtn = document.getElementById('dropdown-edit-btn');
            editBtn.onclick = function(e) {
                e.preventDefault();
                window.Livewire.dispatch('editUser', { id: userId });
                dropdown.classList.add('hidden');
            };

            const actionBtn  = document.getElementById('dropdown-deactivate-btn');
            const actionText = document.getElementById('dropdown-deactivate-text');
            const actionIcon = document.getElementById('dropdown-deactivate-icon');

            if (is_active) {
                actionBtn.classList.remove('text-blue-600');
                actionBtn.classList.add('text-red-600');
                actionText.innerText = 'Desativar';
                actionIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-red-400"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 0 1-2 2H8a2 2 0 0 1-2-2L5 6"/><path d="M10 11v6"/><path d="M14 11v6"/><path d="M9 6V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"/></svg>`;
                actionBtn.onclick = function(e) {
                    e.preventDefault();
                    window.Livewire.dispatch('deactivateUser', { id: userId, status: 'deactivate' });
                    dropdown.classList.add('hidden');
                };
            } else {
                actionBtn.classList.remove('text-red-600');
                actionBtn.classList.add('text-blue-600');
                actionText.innerText = 'Ativar';
                actionIcon.innerHTML = `<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="text-blue-400"><path d="M18.36 6.64a9 9 0 1 1-12.73 0"/><line x1="12" y1="2" x2="12" y2="12"/></svg>`;
                actionBtn.onclick = function(e) {
                    e.preventDefault();
                    window.Livewire.dispatch('deactivateUser', { id: userId, status: 'activate' });
                    dropdown.classList.add('hidden');
                };
            }

            document.getElementById('dropdown-profile-btn').onclick = function(e) {
                e.preventDefault();
                window.location.href = `/users/${userId}`;
                dropdown.classList.add('hidden');
            };

            const rect = event.currentTarget.getBoundingClientRect();
            dropdown.style.top  = (rect.bottom + 8) + 'px';
            dropdown.style.left = (rect.right - 176) + 'px';

            dropdown.classList.toggle('hidden');
        }

        document.addEventListener('click', function(e) {
            if (!e.target.closest('button')) {
                document.getElementById('dropdown').classList.add('hidden');
            }
        });
    </script>

</div>
