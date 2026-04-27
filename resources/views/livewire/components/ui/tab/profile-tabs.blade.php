<div class="mt-5">
    <div class="max-w-7xl mx-auto bg-white rounded-2xl border border-gray-100 overflow-hidden"
        x-data="{ activeTab: 'informacoes' }">

        <!-- Tabs nav -->
        <div class="flex border-b border-gray-100 px-4 md:px-6 overflow-x-auto">

            <button @click="activeTab = 'informacoes'"
                :class="activeTab === 'informacoes'
                    ? 'text-gray-800 font-semibold border-b-2 border-gray-800'
                    : 'text-gray-400 font-medium border-b-2 border-transparent hover:text-gray-600'"
                class="py-3 px-1 mr-6 text-sm transition-all duration-150 cursor-pointer whitespace-nowrap">
                Informações
            </button>

            <button @click="activeTab = 'atividade'"
                :class="activeTab === 'atividade'
                    ? 'text-gray-800 font-semibold border-b-2 border-gray-800'
                    : 'text-gray-400 font-medium border-b-2 border-transparent hover:text-gray-600'"
                class="py-3 px-1 text-sm transition-all duration-150 cursor-pointer whitespace-nowrap">
                Atividade Recente
            </button>

        </div>

        <!-- Tab Panel: Informações -->
        <div x-show="activeTab === 'informacoes'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="p-4 md:p-8">

            <h2 class="text-sm font-semibold text-gray-700 mb-4 md:mb-5">Dados do Cadastro</h2>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 md:gap-4">

                <!-- Nome Completo -->
                <div class="flex items-start gap-3 p-3 md:p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                        <x-lucide-user class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 mb-1">Nome Completo</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->name ?? 'Não identificado' }}</p>
                    </div>
                </div>

                <!-- E-mail -->
                <div class="flex items-start gap-3 p-3 md:p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                        <x-lucide-mail class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 mb-1">E-mail</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->email ?? 'Não informado' }}</p>
                    </div>
                </div>

                <!-- Departamento -->
                <div class="flex items-start gap-3 p-3 md:p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                        <x-lucide-building-2 class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 mb-1">Departamento</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->department->name ?? 'Não informado' }}</p>
                    </div>
                </div>

                <!-- Cargo -->
                <div class="flex items-start gap-3 p-3 md:p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                        <x-lucide-briefcase class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 mb-1">Cargo</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->position ?? 'Não informado' }}</p>
                    </div>
                </div>

                <!-- Perfil de Acesso -->
                <div class="flex items-start gap-3 p-3 md:p-4 bg-gray-50 rounded-xl border border-gray-100">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0">
                        <x-lucide-shield class="w-4 h-4 text-gray-400" />
                    </div>
                    <div class="min-w-0">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 mb-1">Perfil de Acesso</p>
                        <p class="text-sm font-semibold text-gray-800 truncate">{{ $user->accessProfile->name ?? 'Não informado' }}</p>
                    </div>
                </div>

                <!-- Gestor(es) Direto(s) -->
                <div class="flex items-start gap-3 p-3 md:p-4 bg-gray-50 rounded-xl border border-gray-100
                    {{ $managers->count() > 1 ? 'sm:col-span-2' : '' }}">
                    <div class="w-8 h-8 rounded-lg bg-white border border-gray-200 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <x-lucide-user-check class="w-4 h-4 text-gray-400" />
                    </div>

                    <div class="min-w-0 flex-1">
                        <p class="text-xs font-semibold tracking-widest uppercase text-slate-400 mb-2">
                            {{ $managers->count() > 1 ? 'Gestores Diretos' : 'Gestor Direto' }}
                        </p>

                        @if($managers->isEmpty())
                            <p class="text-sm font-semibold text-gray-400">Não informado</p>

                        @elseif($managers->count() === 1)
                            @php $manager = $managers->first() @endphp
                            <div class="flex items-center gap-2">
                                <img
                                    src="https://ui-avatars.com/api/?name={{ urlencode($manager->name) }}&size=32&background=e0e7ff&color=4f46e5"
                                    class="w-7 h-7 rounded-full border border-gray-200 shrink-0"
                                    alt="{{ $manager->name }}"
                                >
                                <div class="min-w-0">
                                    <p class="text-sm font-semibold text-gray-800 truncate leading-tight">
                                        {{ $manager->name }}
                                    </p>
                                    <p class="text-xs text-gray-400 truncate">{{ $manager->email }}</p>
                                </div>
                            </div>

                        @else
                            {{-- Múltiplos gestores --}}
                            <div class="flex flex-wrap gap-3">
                                @foreach($managers as $manager)
                                    <div class="flex items-center gap-2 bg-white border border-gray-200 rounded-lg px-3 py-2 min-w-0">
                                        <img
                                            src="https://ui-avatars.com/api/?name={{ urlencode($manager->name) }}&size=32&background=e0e7ff&color=4f46e5"
                                            class="w-7 h-7 rounded-full border border-gray-100 shrink-0"
                                            alt="{{ $manager->name }}"
                                        >
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-gray-800 truncate leading-tight">
                                                {{ $manager->name }}
                                            </p>
                                            <p class="text-xs text-gray-400 truncate">{{ $manager->email }}</p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>

        <!-- Tab Panel: Atividade Recente -->
        <div x-show="activeTab === 'atividade'"
            x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 translate-y-1"
            x-transition:enter-end="opacity-100 translate-y-0"
            class="p-4 md:p-8">

            <h2 class="text-sm font-semibold text-gray-700 mb-4 md:mb-5">Atividade Recente</h2>

            <div class="flex flex-col gap-3">

                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-50 border border-emerald-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                        <x-lucide-log-in class="w-3.5 h-3.5 text-emerald-500" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-gray-800">Login realizado</p>
                        <p class="text-xs text-gray-400 mt-0.5">Hoje às 09:14</p>
                    </div>
                </div>

                <div class="ml-4 border-l border-gray-100 pl-7">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-blue-50 border border-blue-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <x-lucide-pencil class="w-3.5 h-3.5 text-blue-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">Perfil atualizado</p>
                            <p class="text-xs text-gray-400 mt-0.5">Ontem às 16:32</p>
                        </div>
                    </div>
                </div>

                <div class="ml-4 border-l border-gray-100 pl-7">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-amber-50 border border-amber-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <x-lucide-shield-check class="w-3.5 h-3.5 text-amber-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800 leading-snug">
                                Permissões alteradas para
                                <span class="font-semibold">Administrador</span>
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">12/03/2025 às 11:05</p>
                        </div>
                    </div>
                </div>

                <div class="ml-4 border-l border-gray-100 pl-7 pb-1">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <x-lucide-user-plus class="w-3.5 h-3.5 text-gray-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">Usuário criado</p>
                            <p class="text-xs text-gray-400 mt-0.5">01/01/2025 às 08:00</p>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
