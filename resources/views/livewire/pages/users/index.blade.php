<div class="p-4 sm:p-6 lg:p-8">

    {{-- ── HEADER ──────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="text-center sm:text-left">
            <h1 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 dark:text-white font-display tracking-tight">
                Usuários
            </h1>
            <p class="text-sm text-slate-400 mt-1.5 font-normal">
                Gerencie usuários e configurações da empresa
            </p>
        </div>

        @if ($aba === 'usuarios')
        <div class="w-full sm:w-auto">
            <button type="button" wire:click="openModal"
                class="w-full sm:w-auto flex items-center justify-center gap-2
                       bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                       text-white text-sm font-medium px-4 py-2.5
                       rounded-lg shadow-md shadow-blue-500/20 transition cursor-pointer lato-bold">
                <x-lucide-user-plus class="w-4 h-4" />
                Novo usuário
            </button>
        </div>
        @endif
    </div>

    {{-- ── TABS ─────────────────────────────────────────────────────── --}}
    <div class="mt-6 flex items-center gap-1 border-b border-slate-200 dark:border-slate-700">
        <button wire:click="$set('aba','usuarios')" type="button"
                class="cursor-pointer flex items-center gap-2 px-4 py-2.5 text-sm lato-bold rounded-t-lg transition border-b-2
                       {{ $aba === 'usuarios'
                          ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                          : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            <x-lucide-users class="w-4 h-4" />
            Usuários
        </button>
        <button wire:click="$set('aba','configuracoes')" type="button"
                class="cursor-pointer flex items-center gap-2 px-4 py-2.5 text-sm lato-bold rounded-t-lg transition border-b-2
                       {{ $aba === 'configuracoes'
                          ? 'border-blue-500 text-blue-600 dark:text-blue-400'
                          : 'border-transparent text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
            <x-lucide-settings class="w-4 h-4" />
            Configurações
            @if (!$cfgEmailDominio)
                <span class="w-2 h-2 rounded-full bg-amber-400 shrink-0" title="Domínio de e-mail não configurado"></span>
            @endif
        </button>
    </div>

    {{-- ══════════════════════════════════════════════════════════════
         ABA: USUÁRIOS
    ══════════════════════════════════════════════════════════════ --}}
    @if ($aba === 'usuarios')

    <div class="mt-6">
        <livewire:components.user.user-stats />
    </div>

    <div class="mt-6">
        <livewire:components.ui.table.users-table />
    </div>

    <livewire:components.ui.modal.modal-create>
        <livewire:components.ui.modal.user-created-modal />
        <livewire:components.ui.modal.deactivate-user-modal />
        <livewire:components.ui.modal.alert-modal />
    </livewire:components.ui.modal.modal-create>

    {{-- ══════════════════════════════════════════════════════════════
         ABA: CONFIGURAÇÕES
    ══════════════════════════════════════════════════════════════ --}}
    @elseif ($aba === 'configuracoes')

    <div class="mt-8 max-w-2xl space-y-6">

        {{-- Flash de sucesso --}}
        @if (session('cfg_sucesso'))
        <div class="flex items-center gap-3 px-4 py-3 bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-700/50 rounded-xl text-sm text-emerald-700 dark:text-emerald-300">
            <x-lucide-check-circle class="w-4 h-4 shrink-0" />
            {{ session('cfg_sucesso') }}
        </div>
        @endif

        {{-- Card: E-mail corporativo --}}
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 space-y-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-blue-500 to-indigo-600 flex items-center justify-center shrink-0">
                    <x-lucide-at-sign class="w-4.5 h-4.5 text-white" />
                </div>
                <div>
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-white">E-mail Corporativo</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Usado para gerar e-mails automáticos ao contratar candidatos</p>
                </div>
            </div>

            {{-- Preview --}}
            @if ($cfgEmailDominio)
            <div class="flex items-center gap-2 px-3 py-2.5 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700/50 rounded-xl">
                <x-lucide-mail class="w-4 h-4 text-blue-500 shrink-0" />
                <span class="text-xs text-blue-700 dark:text-blue-300">
                    Exemplo de e-mail gerado:
                    <span class="lato-bold">joao.silva@{{ $cfgEmailDominio }}</span>
                </span>
            </div>
            @endif

            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                    Domínio do e-mail <span class="text-blue-500">*</span>
                </label>
                <div class="flex items-center gap-2">
                    <span class="text-sm text-slate-400 lato-regular shrink-0">@</span>
                    <input wire:model="cfgEmailDominio"
                           type="text"
                           placeholder="empresa.com.br"
                           class="flex-1 px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600
                                  bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-300 dark:placeholder-slate-500
                                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent lato-regular" />
                </div>
                @error('cfgEmailDominio')
                <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                @enderror
                <p class="text-xs text-slate-400 mt-1.5">Digite apenas o domínio, sem <code class="bg-slate-100 dark:bg-slate-700 px-1 rounded">@</code> ou <code class="bg-slate-100 dark:bg-slate-700 px-1 rounded">https://</code></p>
            </div>
        </div>

        {{-- Card: Dados da empresa --}}
        <div class="bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-6 space-y-5">
            <div class="flex items-center gap-3">
                <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-slate-500 to-slate-700 flex items-center justify-center shrink-0">
                    <x-lucide-building-2 class="w-4.5 h-4.5 text-white" />
                </div>
                <div>
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-white">Dados da Empresa</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Informações gerais (opcional)</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div class="sm:col-span-2">
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nome da Empresa</label>
                    <input wire:model="cfgNomeEmpresa" type="text" placeholder="TechVision Ltda."
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600
                                  bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-300 dark:placeholder-slate-500
                                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">CNPJ</label>
                    <input wire:model="cfgCnpj" type="text" placeholder="00.000.000/0001-00"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600
                                  bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-300 dark:placeholder-slate-500
                                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent lato-regular" />
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Telefone</label>
                    <input wire:model="cfgTelefone" type="text" placeholder="(11) 99999-9999"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600
                                  bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-300 dark:placeholder-slate-500
                                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent lato-regular" />
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Site</label>
                    <input wire:model="cfgSite" type="text" placeholder="https://empresa.com.br"
                           class="w-full px-3.5 py-2.5 text-sm rounded-xl border border-slate-200 dark:border-slate-600
                                  bg-white dark:bg-slate-700 text-slate-800 dark:text-white
                                  placeholder-slate-300 dark:placeholder-slate-500
                                  focus:outline-none focus:ring-2 focus:ring-blue-400 focus:border-transparent lato-regular" />
                    @error('cfgSite')
                    <p class="text-xs text-red-500 mt-1.5">{{ $message }}</p>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Botão salvar --}}
        <div class="flex justify-end">
            <button wire:click="salvarConfiguracoes" type="button"
                    wire:loading.attr="disabled" wire:target="salvarConfiguracoes"
                    class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 rounded-xl
                           bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700
                           text-white text-sm lato-bold shadow-md shadow-blue-500/20 transition disabled:opacity-60">
                <span wire:loading.remove wire:target="salvarConfiguracoes" class="flex items-center gap-2">
                    <x-lucide-save class="w-4 h-4" />
                    Salvar Configurações
                </span>
                <span wire:loading wire:target="salvarConfiguracoes" class="flex items-center gap-2">
                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                    Salvando...
                </span>
            </button>
        </div>

    </div>
    @endif

</div>
