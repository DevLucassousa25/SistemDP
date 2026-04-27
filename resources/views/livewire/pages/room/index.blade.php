<div class="p-4 sm:p-6 lg:p-8">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-8">
        <div class="text-center sm:text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 font-display tracking-tight">Cadastro de Salas</h1>
            <p class="text-sm text-slate-400 mt-1.5 font-normal">Gerencie as salas de reunião da empresa</p>
        </div>

        @if (auth()->user()?->isRhOuDp())
            <button wire:click="$dispatch('abrir-modal-sala')"
                class="w-full sm:w-auto flex items-center justify-center gap-2
                    bg-emerald-500 hover:bg-emerald-600
                    text-white text-sm font-medium
                    px-4 py-2.5
                    rounded-lg shadow-sm
                    transition cursor-pointer lato-bold">
                <x-lucide-plus class="w-4 h-4" />
                Nova Sala
            </button>
        @endif
    </div>

    <div class="mt-5">
        @livewire('components.room.stats-cards')
    </div>

    <div>
        @livewire('components.ui.table.room-table')
    </div>

    @livewire('components.ui.modal.room-create-modal')
    @livewire('components.ui.modal.alert-modal')
    @livewire('components.ui.modal.delete-room-modal')
</div>
