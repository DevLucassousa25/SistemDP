<div
    x-data
    x-effect="document.body.style.overflow = $wire.open ? 'hidden' : ''">

    <!-- Overlay -->
    <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4 transition-all duration-300
        {{ $open ? 'opacity-100 pointer-events-auto' : 'opacity-0 pointer-events-none' }}">

        <!-- Backdrop -->
        <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="close"></div>

        <!-- Modal Box — bottom-sheet em mobile, centralizado no desktop -->
        <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[92vh] flex flex-col
                    rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden
                    transform transition-all duration-300
                    {{ $open ? 'translate-y-0 opacity-100 scale-100' : 'translate-y-4 opacity-0 scale-95' }}">

            <!-- Alça de arraste (mobile) -->
            <div class="flex justify-center pt-2.5 pb-1 sm:hidden flex-shrink-0">
                <div class="w-10 h-1 bg-slate-200 rounded-full"></div>
            </div>

            {{-- ───── Header com gradient accent ────────────────────────────── --}}
            <div class="relative flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                {{-- Faixa decorativa emerald --}}

                <div class="flex items-start gap-3 sm:gap-4 px-4 sm:px-6 py-3.5 sm:py-5">
                    {{-- Ícone avatar --}}
                    <div class="shrink-0 w-10 h-10 sm:w-12 sm:h-12 rounded-xl bg-gradient-to-br from-emerald-500 to-teal-500 flex items-center justify-center shadow-sm shadow-emerald-200/50">
                        @if ($mode === 'create')
                            <x-lucide-user-plus class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        @else
                            <x-lucide-user-cog class="w-5 h-5 sm:w-6 sm:h-6 text-white" />
                        @endif
                    </div>

                    <div class="flex-1 min-w-0">
                        <h2 class="text-base sm:text-lg font-bold text-slate-800 dark:text-white lato-black leading-tight">
                            {{ $mode === 'create' ? 'Novo Usuário' : 'Editar Usuário' }}
                        </h2>
                        <p class="text-xs sm:text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5 leading-snug">
                            {{ $mode === 'create'
                                ? 'Cadastre um novo colaborador na plataforma'
                                : 'Atualize as informações do colaborador' }}
                        </p>
                    </div>

                    <button wire:click="close"
                        class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
            </div>

            {{-- ───── Body ─────────────────────────────────────────────────── --}}
            <div class="px-4 sm:px-6 py-4 sm:py-5 space-y-5 sm:space-y-6 overflow-y-auto flex-1 bg-slate-50/40 dark:bg-slate-800">

                {{-- Alerta: conflito de gerente --}}
                @if($managerWarning)
                    <div class="flex items-start gap-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-800 rounded-xl p-3 sm:p-4">
                        <div class="w-8 h-8 rounded-lg bg-amber-100 dark:bg-amber-900/40 flex items-center justify-center shrink-0">
                            <x-lucide-triangle-alert class="w-4 h-4 text-amber-500 dark:text-amber-400" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-amber-800 dark:text-amber-300 lato-bold">Não foi possível salvar</p>
                            <p class="text-xs sm:text-sm text-amber-700 dark:text-amber-400 lato-regular mt-0.5 leading-snug">{{ $managerWarning }}</p>
                        </div>
                        <button wire:click="$set('managerWarning', null)"
                            class="text-amber-400 hover:text-amber-600 dark:hover:text-amber-300 shrink-0 cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                @endif

                {{-- ─── Seção: Dados pessoais ──────────────────────────────── --}}
                <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4 sm:p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <x-lucide-user class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                            Dados Pessoais
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        {{-- Nome --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                Nome Completo
                                <span class="text-red-500 normal-case tracking-normal">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-user class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input wire:model.blur="name" type="text" placeholder="Nome do colaborador"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                           text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                           transition duration-150
                                           {{ $errors->has('name') ? 'border border-red-400 dark:border-red-500 bg-red-50/40 dark:bg-red-900/10' : 'border border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500' }}">
                            </div>
                            @error('name')
                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                    <x-lucide-alert-circle class="w-3 h-3" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Email --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                E-mail
                                <span class="text-red-500 normal-case tracking-normal">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-mail class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input wire:model.blur="email" type="email" placeholder="email@empresa.com"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                           text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                           transition duration-150
                                           {{ $errors->has('email') ? 'border border-red-400 dark:border-red-500 bg-red-50/40 dark:bg-red-900/10' : 'border border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500' }}">
                            </div>
                            @error('email')
                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                    <x-lucide-alert-circle class="w-3 h-3" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- ─── Seção: Cargo e Departamento ────────────────────────── --}}
                <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4 sm:p-5">
                    <div class="flex items-center gap-2 mb-4">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <x-lucide-briefcase class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                            Cargo e Departamento
                        </h3>
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        {{-- Departamento --}}
                        <div wire:key="dept-dropdown-{{ $userId ?? 'new' }}" x-data="{
                            open: false,
                            search: '',
                            selected: @entangle('department'),
                            departments: @js($departments),
                            blockedIds: @entangle('departmentsWithManager'),
                            init() {
                                this.syncSearch();
                                this.$watch('selected', () => this.syncSearch());
                                this.$watch('blockedIds', () => this.syncSearch());
                            },
                            syncSearch() {
                                const found = this.departments.find(d => String(d.id) === String(this.selected));
                                this.search = found ? found.name : '';
                            },
                            isBlocked(id) {
                                return this.blockedIds.includes(parseInt(id));
                            },
                            clear() {
                                this.selected = null;
                                this.search = '';
                            }
                        }" class="relative space-y-1.5">
                            <label class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                Departamento
                            </label>
                            <div class="relative">
                                <x-lucide-building-2 class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input type="text" x-model="search" @click="open = true" @click.away="open = false"
                                    placeholder="Selecione um departamento"
                                    class="w-full pl-9 pr-9 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                           text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                           transition duration-150 cursor-pointer
                                           {{ $errors->has('department') ? 'border border-red-400 dark:border-red-500' : 'border border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500' }}" />
                                <button type="button" x-show="selected" @click.stop="clear(); $wire.call('checkDepartment')"
                                        class="absolute right-8 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                    <x-lucide-x class="w-3.5 h-3.5" />
                                </button>
                                <x-lucide-chevron-down class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 transition-transform pointer-events-none"
                                                       x-bind:class="open ? 'rotate-180' : ''" />
                            </div>

                            <div x-show="open" x-transition.opacity.duration.150ms
                                class="absolute z-30 w-full mt-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg shadow-xl max-h-56 overflow-y-auto">
                                <template
                                    x-for="dept in departments.filter(d => d.name.toLowerCase().includes(search.toLowerCase()))"
                                    :key="dept.id">
                                    <div
                                        @click="if (!isBlocked(dept.id)) { selected = dept.id; search = dept.name; open = false; $wire.call('checkDepartment'); }"
                                        :class="isBlocked(dept.id)
                                            ? 'px-3 py-2.5 flex items-center justify-between cursor-not-allowed opacity-50 bg-slate-50 dark:bg-slate-800/50'
                                            : 'px-3 py-2.5 flex items-center justify-between cursor-pointer hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition'">
                                        <div class="flex items-center gap-2 min-w-0">
                                            <x-lucide-building class="w-3.5 h-3.5 text-slate-400 shrink-0" />
                                            <span x-text="dept.name" class="text-sm lato-regular text-slate-700 dark:text-slate-200 truncate"></span>
                                        </div>
                                        <span x-show="isBlocked(dept.id)"
                                            class="shrink-0 text-[10px] font-semibold lato-bold text-amber-700 bg-amber-50 border border-amber-200 rounded-full px-2 py-0.5 flex items-center gap-1 whitespace-nowrap">
                                            <x-lucide-triangle-alert class="w-3 h-3" />
                                            Já tem gerente
                                        </span>
                                    </div>
                                </template>
                                <div x-show="departments.filter(d => d.name.toLowerCase().includes(search.toLowerCase())).length === 0"
                                    class="flex flex-col items-center justify-center px-4 py-6 text-center">
                                    <x-lucide-building-2 class="w-5 h-5 text-slate-300 mb-1" />
                                    <p class="text-xs text-slate-400 lato-regular">Nenhum resultado encontrado</p>
                                </div>
                            </div>

                            @error('department')
                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                    <x-lucide-alert-circle class="w-3 h-3" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        {{-- Cargo --}}
                        <div class="space-y-1.5">
                            <label class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                Cargo
                                <span class="text-red-500 normal-case tracking-normal">*</span>
                            </label>
                            <div class="relative">
                                <x-lucide-id-card class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                                <input wire:model.blur="cargo" type="text" placeholder="Ex: Desenvolvedor Sênior"
                                    class="w-full pl-9 pr-3 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                           text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500
                                           focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                           transition duration-150
                                           {{ $errors->has('cargo') ? 'border border-red-400 dark:border-red-500 bg-red-50/40 dark:bg-red-900/10' : 'border border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500' }}">
                            </div>
                            @error('cargo')
                                <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                    <x-lucide-alert-circle class="w-3 h-3" />
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>
                </section>

                {{-- ─── Seção: Acesso ao sistema ───────────────────────────── --}}
                <section class="bg-white dark:bg-slate-700/40 rounded-xl border border-slate-100 dark:border-slate-700 p-4 sm:p-5 space-y-4">
                    <div class="flex items-center gap-2">
                        <div class="w-7 h-7 rounded-lg bg-emerald-50 dark:bg-emerald-900/30 flex items-center justify-center">
                            <x-lucide-key-round class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" />
                        </div>
                        <h3 class="text-xs font-semibold lato-bold text-slate-700 dark:text-slate-200 uppercase tracking-wide">
                            Acesso ao Sistema
                        </h3>
                    </div>

                    {{-- Senha --}}
                    <div x-data="{
                            show: false,
                            senhaPadrao: 'Mudar@123',
                            usarPadrao() {
                                $wire.set('password', this.senhaPadrao);
                                this.show = true;
                            }
                        }"
                         class="space-y-2">
                        <div class="flex items-center justify-between gap-2 flex-wrap">
                            <label class="flex items-center gap-1 text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                                Senha de primeiro acesso
                                @if ($mode === 'create')
                                    <span class="text-red-500 normal-case tracking-normal">*</span>
                                @else
                                    <span class="normal-case tracking-normal text-slate-400 lato-regular">(deixe em branco para manter)</span>
                                @endif
                            </label>
                            @if ($mode === 'create' || !$errors->has('password'))
                                <button type="button" @click="usarPadrao()"
                                        class="flex items-center gap-1 text-[11px] lato-bold text-emerald-600 hover:text-emerald-700 dark:text-emerald-400 dark:hover:text-emerald-300 cursor-pointer transition">
                                    <x-lucide-key-round class="w-3 h-3" />
                                    Usar senha padrão
                                </button>
                            @endif
                        </div>

                        <div class="relative">
                            <x-lucide-lock class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input :type="show ? 'text' : 'password'"
                                   wire:model="password"
                                   placeholder="{{ $mode === 'create' ? 'Ex: Mudar@123' : 'Nova senha (opcional)' }}"
                                @if ($mode === 'create') required @endif
                                class="w-full pl-9 pr-10 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                       text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                       transition duration-150
                                       {{ $errors->has('password') ? 'border border-red-400 dark:border-red-500 bg-red-50/40 dark:bg-red-900/10' : 'border border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500' }}">
                            <button type="button" @click="show = !show"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 cursor-pointer transition">
                                <x-lucide-eye x-show="!show" class="w-4 h-4" />
                                <x-lucide-eye-off x-show="show" class="w-4 h-4" />
                            </button>
                        </div>

                        {{-- Dica de primeiro acesso --}}
                        @if ($mode === 'create')
                            <div class="flex items-start gap-2 bg-emerald-50/60 dark:bg-emerald-900/10 border border-emerald-100 dark:border-emerald-900/40 rounded-lg px-3 py-2">
                                <x-lucide-info class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 shrink-0 mt-0.5" />
                                <p class="text-[11px] sm:text-xs text-emerald-700 dark:text-emerald-300 lato-regular leading-snug">
                                    Use uma senha simples de primeiro acesso (ex.: <span class="font-semibold lato-bold">Mudar@123</span>). O usuário poderá alterá-la após fazer login.
                                </p>
                            </div>
                        @endif

                        @error('password')
                            <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                <x-lucide-alert-circle class="w-3 h-3" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Perfil de Acesso --}}
                    <div wire:key="perfil-dropdown-{{ $userId ?? 'new' }}" x-data="{
                        open: false,
                        search: '',
                        selected: @entangle('perfil'),
                        profiles: @js($accessProfiles),
                        init() {
                            this.syncSearch();
                            this.$watch('selected', () => this.syncSearch());
                        },
                        syncSearch() {
                            const found = this.profiles.find(p => String(p.id) === String(this.selected));
                            this.search = found ? found.name : '';
                        },
                        selectedProfile() {
                            return this.profiles.find(p => String(p.id) === String(this.selected));
                        },
                        iconFor(slug) {
                            return {
                                administrator: 'shield-check',
                                hr: 'users',
                                manager: 'briefcase'
                            }[slug] ?? 'user';
                        },
                        clear() {
                            this.selected = null;
                            this.search = '';
                        }
                    }" class="relative space-y-1.5">
                        <label class="text-xs font-semibold lato-bold text-slate-600 dark:text-slate-300 uppercase tracking-wide">
                            Perfil de Acesso
                        </label>
                        <div class="relative">
                            <x-lucide-shield class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 pointer-events-none" />
                            <input type="text" x-model="search" @click="open = true" @click.away="open = false"
                                placeholder="Selecione um perfil"
                                class="w-full pl-9 pr-9 py-2 text-sm lato-regular rounded-lg bg-white dark:bg-slate-700
                                       text-slate-800 dark:text-white placeholder-slate-400 dark:placeholder-slate-500
                                       focus:outline-none focus:ring-2 focus:ring-emerald-500/40 focus:border-emerald-400
                                       transition duration-150 cursor-pointer
                                       {{ $errors->has('perfil') ? 'border border-red-400 dark:border-red-500' : 'border border-slate-200 dark:border-slate-600 hover:border-slate-300 dark:hover:border-slate-500' }}" />
                            <button type="button" x-show="selected" @click.stop="clear()"
                                    class="absolute right-8 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer">
                                <x-lucide-x class="w-3.5 h-3.5" />
                            </button>
                            <x-lucide-chevron-down class="absolute right-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-400 transition-transform pointer-events-none"
                                                   x-bind:class="open ? 'rotate-180' : ''" />
                        </div>

                        <div x-show="open" x-transition.opacity.duration.150ms
                            class="absolute z-30 w-full mt-1 bg-white dark:bg-slate-700 border border-slate-200 dark:border-slate-600 rounded-lg shadow-xl max-h-56 overflow-y-auto">
                            <template
                                x-for="profile in profiles.filter(p => p.name.toLowerCase().includes(search.toLowerCase()))"
                                :key="profile.id">
                                <div @click="selected = profile.id; search = profile.name; open = false;"
                                    class="flex items-center gap-2.5 px-3 py-2.5 cursor-pointer hover:bg-emerald-50 dark:hover:bg-emerald-900/20 transition">
                                    <div class="w-7 h-7 rounded-lg flex items-center justify-center shrink-0"
                                         :class="{
                                            'bg-red-50 text-red-500':        profile.slug === 'administrator',
                                            'bg-blue-50 text-blue-500':      profile.slug === 'hr',
                                            'bg-emerald-50 text-emerald-500': profile.slug === 'manager',
                                            'bg-slate-100 text-slate-500':   !['administrator','hr','manager'].includes(profile.slug)
                                         }">
                                        <template x-if="profile.slug === 'administrator'">
                                            <x-lucide-shield-check class="w-4 h-4" />
                                        </template>
                                        <template x-if="profile.slug === 'hr'">
                                            <x-lucide-users class="w-4 h-4" />
                                        </template>
                                        <template x-if="profile.slug === 'manager'">
                                            <x-lucide-briefcase class="w-4 h-4" />
                                        </template>
                                        <template x-if="!['administrator','hr','manager'].includes(profile.slug)">
                                            <x-lucide-user class="w-4 h-4" />
                                        </template>
                                    </div>
                                    <span class="text-sm lato-regular text-slate-700 dark:text-slate-200" x-text="profile.name"></span>
                                </div>
                            </template>
                            <div x-show="profiles.filter(p => p.name.toLowerCase().includes(search.toLowerCase())).length === 0"
                                class="flex flex-col items-center justify-center px-4 py-6 text-center">
                                <x-lucide-shield-off class="w-5 h-5 text-slate-300 mb-1" />
                                <p class="text-xs text-slate-400 lato-regular">Nenhum perfil encontrado</p>
                            </div>
                        </div>

                        @error('perfil')
                            <p class="flex items-center gap-1 text-xs text-red-500 lato-regular">
                                <x-lucide-alert-circle class="w-3 h-3" />
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    {{-- Toggle Ativar usuário --}}
                    <div class="flex items-center justify-between gap-3 p-3 rounded-xl
                                {{ $ativo ? 'bg-emerald-50 dark:bg-emerald-900/20 border border-emerald-200 dark:border-emerald-800' : 'bg-slate-50 dark:bg-slate-700/60 border border-slate-200 dark:border-slate-600' }}
                                transition-colors">
                        <div class="flex items-center gap-3 min-w-0">
                            <div class="w-9 h-9 rounded-lg flex items-center justify-center shrink-0
                                        {{ $ativo ? 'bg-emerald-100 dark:bg-emerald-900/50 text-emerald-600 dark:text-emerald-400' : 'bg-slate-200 dark:bg-slate-600 text-slate-500 dark:text-slate-400' }}">
                                @if ($ativo)
                                    <x-lucide-user-check class="w-4 h-4" />
                                @else
                                    <x-lucide-user-x class="w-4 h-4" />
                                @endif
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="text-sm font-semibold lato-bold text-slate-800 dark:text-white leading-tight">
                                    {{ $ativo ? 'Usuário ativo' : 'Usuário inativo' }}
                                </p>
                                <p class="text-[11px] sm:text-xs text-slate-500 dark:text-slate-400 lato-regular mt-0.5 leading-snug">
                                    {{ $ativo
                                        ? 'Este usuário pode acessar o sistema.'
                                        : 'Este usuário não conseguirá fazer login.' }}
                                </p>
                            </div>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer shrink-0">
                            <input type="checkbox" wire:model.live="ativo" class="sr-only peer">
                            <div class="w-10 h-5.5 bg-slate-300 dark:bg-slate-600 rounded-full peer peer-checked:bg-emerald-500
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-white after:border after:border-slate-200 after:rounded-full after:h-4.5 after:w-4.5
                                        after:transition-all peer-checked:after:translate-x-full peer-checked:after:border-emerald-500
                                        transition-colors"
                                 style="width:2.5rem; height:1.375rem;">
                            </div>
                        </label>
                    </div>
                </section>

            </div>

            {{-- ───── Footer ────────────────────────────────────────────────── --}}
            <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                <div class="flex items-center justify-between gap-3">
                    <p class="hidden sm:flex items-center gap-1.5 text-xs text-slate-400 dark:text-slate-500 lato-regular">
                        <x-lucide-info class="w-3.5 h-3.5" />
                        Campos com <span class="text-red-500 font-semibold">*</span> são obrigatórios
                    </p>

                    <div class="flex gap-2 sm:gap-3 flex-1 sm:flex-initial">
                        <button wire:click="close"
                            class="flex-1 sm:flex-none px-4 sm:px-5 py-2 text-sm lato-bold rounded-lg
                                   text-slate-600 dark:text-slate-300 bg-white dark:bg-slate-700
                                   border border-slate-200 dark:border-slate-600
                                   hover:bg-slate-50 dark:hover:bg-slate-600 transition cursor-pointer">
                            Cancelar
                        </button>

                        <button wire:click="save"
                            wire:loading.attr="disabled"
                            wire:target="save"
                            :disabled="$wire.managerWarning !== null && $wire.managerWarning !== ''"
                            :class="($wire.managerWarning !== null && $wire.managerWarning !== '')
                                ? 'flex-1 sm:flex-none px-4 sm:px-6 py-2 text-sm lato-bold rounded-lg bg-slate-300 text-white flex items-center justify-center gap-2 cursor-not-allowed opacity-60'
                                : 'flex-1 sm:flex-none px-4 sm:px-6 py-2 text-sm lato-bold rounded-lg bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 shadow-md shadow-blue-500/20 text-white flex items-center justify-center gap-2 cursor-pointer transition'">

                            <span wire:loading wire:target="save">
                               <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                            </span>


                            <span wire:loading.remove wire:target="save" class="flex items-center gap-1.5">
                                @if ($mode === 'create')
                                    <x-lucide-circle-check class="w-4 h-4" />
                                    Confirmar
                                @else
                                    <x-lucide-square-pen class="w-4 h-4" />
                                    Salvar Alterações
                                @endif
                            </span>
                        </button>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
