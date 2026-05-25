@php
    $user  = auth()->user();
    $color = $user->avatarColor();
@endphp

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 lg:p-8">
    <div class="max-w-3xl mx-auto space-y-6">

        {{-- ── Header ─────────────────────────────────────────────────── --}}
        <div>
            <h1 class="text-2xl lato-bold text-slate-800 dark:text-slate-100">Meu Perfil</h1>
            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-0.5">Gerencie suas informações pessoais e foto de perfil.</p>
        </div>

        {{-- ── Card Avatar ─────────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6">
            <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-5 flex items-center gap-2">
                <x-lucide-camera class="w-4 h-4 text-blue-500" /> Foto de perfil
            </h2>

            <div class="flex flex-col sm:flex-row items-center gap-6">

                {{-- Avatar atual ou iniciais --}}
                <div class="shrink-0">
                    @if($user->avatar)
                        <div class="relative group">
                            <img src="{{ $user->avatarUrl() }}"
                                 alt="{{ $user->name }}"
                                 class="w-24 h-24 rounded-2xl object-cover shadow-md" />
                            <button wire:click="removerAvatar"
                                    class="absolute inset-0 rounded-2xl bg-black/50 opacity-0 group-hover:opacity-100 transition flex items-center justify-center cursor-pointer">
                                <x-lucide-trash-2 class="w-5 h-5 text-white" />
                            </button>
                        </div>
                    @else
                        <div class="w-24 h-24 rounded-2xl bg-gradient-to-br {{ $color }} flex items-center justify-center shadow-md">
                            <span class="text-2xl lato-bold text-white select-none">{{ $user->initials() }}</span>
                        </div>
                    @endif
                </div>

                {{-- Upload --}}
                <div class="flex-1 w-full">
                    <div x-data="{ dragging: false }"
                         @dragover.prevent="dragging = true"
                         @dragleave.prevent="dragging = false"
                         @drop.prevent="dragging = false; $refs.fileInput.files = $event.dataTransfer.files; $refs.fileInput.dispatchEvent(new Event('change'))"
                         class="relative border-2 border-dashed rounded-xl p-6 text-center transition cursor-pointer"
                         :class="dragging ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20' : 'border-slate-200 dark:border-slate-700 hover:border-blue-300 dark:hover:border-blue-600'"
                         @click="$refs.fileInput.click()">

                        <input x-ref="fileInput"
                               type="file"
                               accept="image/jpeg,image/png,image/webp"
                               class="hidden"
                               wire:model="avatarFile" />

                        @if($avatarFile)
                            {{-- Preview do novo arquivo --}}
                            <div class="flex items-center justify-center gap-3">
                                <img src="{{ $avatarFile->temporaryUrl() }}"
                                     class="w-14 h-14 rounded-xl object-cover shadow" />
                                <div class="text-left">
                                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $avatarFile->getClientOriginalName() }}</p>
                                    <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ round($avatarFile->getSize() / 1024) }} KB</p>
                                </div>
                            </div>
                        @else
                            <x-lucide-upload-cloud class="w-8 h-8 text-slate-400 dark:text-slate-500 mx-auto mb-2" />
                            <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular">
                                <span class="text-blue-500 lato-bold">Clique para escolher</span> ou arraste a imagem aqui
                            </p>
                            <p class="text-xs text-slate-400 lato-regular mt-1">JPG, PNG ou WebP · máx. 2 MB</p>
                        @endif

                        <div wire:loading wire:target="avatarFile" class="absolute inset-0 rounded-xl bg-white/70 dark:bg-slate-800/70 flex items-center justify-center">
                            <svg class="w-6 h-6 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                        </div>
                    </div>

                    @error('avatarFile')
                        <p class="text-xs text-red-500 lato-regular mt-1.5">{{ $message }}</p>
                    @enderror

                    @if($avatarFile)
                        <div class="flex gap-2 mt-3">
                            <button wire:click="salvarAvatar"
                                    class="flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm lato-bold rounded-xl shadow-sm transition cursor-pointer">
                                <x-lucide-check class="w-4 h-4" />
                                Salvar foto
                            </button>
                            <button wire:click="$set('avatarFile', null)"
                                    class="px-4 py-2 text-sm lato-regular text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                                Cancelar
                            </button>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- ── Tabs ────────────────────────────────────────────────────── --}}
        <div class="flex gap-1 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-2xl p-1.5">
            <button wire:click="$set('activeTab', 'info')"
                    class="flex-1 py-2 text-sm lato-bold rounded-xl transition cursor-pointer
                           {{ $activeTab === 'info' ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                Informações pessoais
            </button>
            <button wire:click="$set('activeTab', 'senha')"
                    class="flex-1 py-2 text-sm lato-bold rounded-xl transition cursor-pointer
                           {{ $activeTab === 'senha' ? 'bg-slate-800 dark:bg-slate-600 text-white shadow-sm' : 'text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200' }}">
                Alterar senha
            </button>
        </div>

        {{-- ── Aba: Informações ─────────────────────────────────────────── --}}
        @if($activeTab === 'info')
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">
            <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                <x-lucide-user class="w-4 h-4 text-blue-500" /> Informações pessoais
            </h2>

            {{-- Nome --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nome completo</label>
                <input wire:model="name" type="text" maxlength="100"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 lato-regular focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                @error('name') <p class="text-xs text-red-500 lato-regular mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- E-mail (somente leitura) --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">E-mail</label>
                <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900/50">
                    <x-lucide-mail class="w-4 h-4 text-slate-400 shrink-0" />
                    <span class="text-sm text-slate-500 dark:text-slate-400 lato-regular">{{ $email }}</span>
                </div>
                <p class="text-[11px] text-slate-400 lato-regular mt-1">O e-mail não pode ser alterado por aqui.</p>
            </div>

            {{-- Cargo --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Cargo / Função</label>
                <input wire:model="position" type="text" maxlength="100"
                       placeholder="Ex: Analista de RH"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 lato-regular focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                @error('position') <p class="text-xs text-red-500 lato-regular mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Bio --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Bio</label>
                <textarea wire:model="bio" rows="3" maxlength="300"
                          placeholder="Conte um pouco sobre você..."
                          class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 lato-regular focus:outline-none focus:ring-2 focus:ring-blue-500 transition resize-none"></textarea>
                <div class="flex justify-between mt-1">
                    @error('bio')
                        <p class="text-xs text-red-500 lato-regular">{{ $message }}</p>
                    @else
                        <span></span>
                    @enderror
                    <p class="text-[11px] text-slate-400 lato-regular">{{ mb_strlen($bio) }}/300</p>
                </div>
            </div>

            {{-- Departamento e Perfil (somente leitura) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Departamento</label>
                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900/50">
                        <x-lucide-building-2 class="w-4 h-4 text-slate-400 shrink-0" />
                        <span class="text-sm text-slate-500 dark:text-slate-400 lato-regular truncate">{{ $user->department?->name ?? '—' }}</span>
                    </div>
                </div>
                <div>
                    <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Perfil de acesso</label>
                    <div class="flex items-center gap-2 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-900/50">
                        <x-lucide-shield class="w-4 h-4 text-slate-400 shrink-0" />
                        <span class="text-sm text-slate-500 dark:text-slate-400 lato-regular truncate">{{ $user->accessProfile?->name ?? '—' }}</span>
                    </div>
                </div>
            </div>

            <div class="pt-2">
                <button wire:click="salvarInfo"
                        class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm lato-bold rounded-xl shadow-sm shadow-blue-500/20 transition cursor-pointer">
                    <span wire:loading.remove wire:target="salvarInfo">
                        <x-lucide-save class="w-4 h-4 inline -mt-0.5 mr-1" /> Salvar alterações
                    </span>
                    <span wire:loading wire:target="salvarInfo" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Salvando...
                    </span>
                </button>
            </div>
        </div>
        @endif

        {{-- ── Aba: Senha ───────────────────────────────────────────────── --}}
        @if($activeTab === 'senha')
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-6 space-y-5">
            <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                <x-lucide-lock class="w-4 h-4 text-blue-500" /> Alterar senha
            </h2>

            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Senha atual</label>
                <input wire:model="currentPassword" type="password"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 lato-regular focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                @error('currentPassword') <p class="text-xs text-red-500 lato-regular mt-1">{{ $message }}</p> @enderror
            </div>

            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nova senha</label>
                <input wire:model="newPassword" type="password"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 lato-regular focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                @error('newPassword') <p class="text-xs text-red-500 lato-regular mt-1">{{ $message }}</p> @enderror
                <p class="text-[11px] text-slate-400 lato-regular mt-1">Mínimo 8 caracteres, letras maiúsculas, minúsculas e números.</p>
            </div>

            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Confirmar nova senha</label>
                <input wire:model="confirmPassword" type="password"
                       class="w-full px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 text-sm text-slate-800 dark:text-slate-100 lato-regular focus:outline-none focus:ring-2 focus:ring-blue-500 transition" />
                @error('confirmPassword') <p class="text-xs text-red-500 lato-regular mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="pt-2">
                <button wire:click="salvarSenha"
                        class="flex items-center gap-2 px-5 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white text-sm lato-bold rounded-xl shadow-sm shadow-blue-500/20 transition cursor-pointer">
                    <span wire:loading.remove wire:target="salvarSenha">
                        <x-lucide-lock class="w-4 h-4 inline -mt-0.5 mr-1" /> Alterar senha
                    </span>
                    <span wire:loading wire:target="salvarSenha" class="flex items-center gap-2">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                        </svg>
                        Salvando...
                    </span>
                </button>
            </div>
        </div>
        @endif

    </div>
</div>
