<div>

    @php
        $role = $user->accessProfile->name ?? '';

        $colors = match ($role) {
            'Administrador' => 'text-rose-400 bg-rose-50 border-rose-100',
            'Colaborador' => 'text-gray-500 bg-gray-100 border-gray-200',
            'RH' => 'text-purple-500 bg-purple-50 border-purple-100',
            'Gestor' => 'text-emerald-600 bg-emerald-50 border-emerald-100',
            default => 'text-gray-400 bg-gray-50 border-gray-100',
        };
    @endphp

    <!-- Voltar -->
    <div class="max-w-7xl mx-auto">
        <button onclick="history.back()"
            class="flex items-center gap-1.5 text-sm text-gray-500 hover:text-gray-800 transition-colors cursor-pointer">
            <x-lucide-arrow-left class="w-4 h-4" />
            Voltar
        </button>
    </div>

    <!-- Card usuário -->
    <div class="max-w-7xl mx-auto bg-white rounded-2xl overflow-hidden border border-gray-100 mt-10">

        <!-- Banner -->
        <div class="h-20 sm:h-24 bg-gradient-to-r from-emerald-400 to-emerald-500"></div>

        <div class="px-4 sm:px-6 pb-4">

            <!-- Header -->
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between -mt-10 sm:-mt-8 gap-4">

                <!-- Avatar + Info -->
                <div class="flex items-center sm:items-end gap-4">

                    <div
                        class="w-16 h-16 sm:w-20 sm:h-20 rounded-xl overflow-hidden ring-4 ring-white bg-gray-200 flex-shrink-0">
                        <img src="https://randomuser.me/api/portraits/men/75.jpg"
                            class="w-full h-full object-cover" />
                    </div>

                    <div class="pb-0 sm:pb-1">
                        <h1 class="text-base sm:text-lg font-bold text-gray-900 leading-tight">
                            {{ $user->name ?? 'Não informado' }}
                        </h1>

                        <p class="text-xs sm:text-sm text-gray-500">
                            {{ $user->position ?? 'Não informado' }}
                            ·
                            {{ $user->department?->name ?? 'Não informado' }}
                        </p>
                    </div>

                </div>

                <!-- Ações -->
                <div class="flex flex-wrap items-center gap-2 sm:mt-10">

                    <!-- Status -->
                    @if ($user->is_active)
                        <span
                            class="flex items-center gap-1.5 text-xs font-semibold text-emerald-600 bg-white border border-emerald-300 px-3 py-1.5 rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                            Ativo
                        </span>
                    @else
                        <span
                            class="flex items-center gap-1.5 text-xs font-semibold text-gray-500 bg-gray-50 border border-gray-200 px-3 py-1.5 rounded-md">
                            <span class="w-1.5 h-1.5 rounded-full bg-gray-400"></span>
                            Inativo
                        </span>
                    @endif


                    <!-- Editar -->
                    <button
                        wire:click="$dispatch('editUser', { id: {{ $user->id }} })"
                        class="flex items-center gap-1.5 text-xs sm:text-sm font-medium text-gray-700 border border-gray-200 hover:bg-gray-50 px-3 py-1.5 rounded-md bg-white transition-colors cursor-pointer">

                        <x-lucide-pencil class="w-3.5 h-3.5 text-gray-500" />
                        Editar
                    </button>


                    <!-- Ativar / Desativar -->
                    @if ($user->is_active)

                        <button
                            wire:click="$dispatch('deactivateUser', { id: {{ $user->id }}, status: 'deactivate' })"
                            class="flex items-center gap-1.5 text-xs sm:text-sm font-medium text-red-500 border border-gray-200 hover:bg-red-50 px-3 py-1.5 rounded-md bg-white transition-colors cursor-pointer">

                            <x-lucide-trash-2 class="w-3.5 h-3.5 text-red-400" />
                            Desativar
                        </button>

                    @else

                        <button
                            wire:click="$dispatch('deactivateUser', { id: {{ $user->id }}, status: 'activate' })"
                            class="flex items-center gap-1.5 text-xs sm:text-sm font-medium text-emerald-600 border border-gray-200 hover:bg-emerald-50 px-3 py-1.5 rounded-md bg-white transition-colors cursor-pointer">

                            <x-lucide-user-check class="w-3.5 h-3.5 text-emerald-500" />
                            Ativar
                        </button>

                    @endif

                </div>
            </div>


            <!-- Info extra -->
            <div class="flex flex-col sm:flex-row sm:items-center gap-2 sm:gap-3 mt-4">

                <!-- Email -->
                <span class="flex items-center gap-1.5 text-xs sm:text-sm text-gray-500 break-all">
                    <x-lucide-mail class="w-3.5 h-3.5 text-gray-400" />
                    {{ $user->email ?? 'Não informado' }}
                </span>


                <!-- Badge acesso -->
                <span
                    class="flex items-center gap-1 text-xs font-semibold px-2.5 py-1 rounded-md w-fit border {{ $colors }}">

                    <x-lucide-shield class="w-3 h-3" />

                    {{ $user->accessProfile?->name ?? 'Não informado' }}

                </span>

            </div>

        </div>

    </div>


    <!-- Tabs -->
    <div>
        <livewire:components.ui.tab.profile-tabs :user="$user" />
    </div>


    <!-- Modais -->
    <livewire:components.ui.modal.modal-create />

    <livewire:components.ui.modal.user-created-modal />

    <livewire:components.ui.modal.deactivate-user-modal />

    <livewire:components.ui.modal.alert-modal />

</div>
