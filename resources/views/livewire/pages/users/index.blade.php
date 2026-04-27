<div class="p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">

        <!-- Texto -->
        <div class="text-center sm:text-left">

            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 font-display tracking-tight">
                Cadastro de Usuários
            </h1>

            <p class="text-sm text-slate-400 mt-1.5 font-normal">
                Gerencie os usuários da plataforma
            </p>

        </div>

        <!-- Botão -->
        <div class="w-full sm:w-auto">

            <button type="button" wire:click="openModal"
                class="w-full sm:w-auto flex items-center justify-center gap-2
                   bg-emerald-500 hover:bg-emerald-600
                   text-white text-sm font-medium
                   px-4 py-2.5
                   rounded-lg shadow-sm
                   transition cursor-pointer lato-bold">

                <x-lucide-user-plus class="w-4 h-4" />

                <span>Novo usuário</span>

            </button>

        </div>

    </div>

    <div class="mt-10">
        <livewire:components.user.user-stats />
    </div>

    <div class="mt-6">
        <livewire:components.ui.table.users-table />
    </div>

    <livewire:components.ui.modal.modal-create>
        <livewire:components.ui.modal.user-created-modal />
        <livewire:components.ui.modal.deactivate-user-modal />
        <livewire:components.ui.modal.alert-modal />
</div>
