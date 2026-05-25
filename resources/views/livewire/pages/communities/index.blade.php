<div class="min-h-screen bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 lg:p-8
            [&_button:not([disabled])]:cursor-pointer [&_a]:cursor-pointer [&_label]:cursor-pointer">

    <div class="max-w-5xl mx-auto space-y-5">

        {{-- ── Cabeçalho ───────────────────────────────────────────── --}}
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-xl lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <x-lucide-users class="w-5 h-5 text-indigo-500" /> Comunidades
                </h1>
                <p class="text-sm text-slate-400 lato-regular mt-0.5">Espaços por setor para trocar ideias e experiências</p>
            </div>
            @if ($this->canCreate)
                <button wire:click="$set('createModal', true)" type="button"
                        class="flex items-center gap-2 px-4 py-2.5 rounded-xl text-xs lato-bold
                               bg-gradient-to-r from-indigo-500 to-violet-600 text-white
                               hover:from-indigo-600 hover:to-violet-700 transition shadow-sm">
                    <x-lucide-plus class="w-4 h-4" /> Nova comunidade
                </button>
            @endif
        </div>

        {{-- ── Busca + Tabs ────────────────────────────────────────── --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-3 flex items-center gap-3 flex-wrap">
            {{-- Tabs --}}
            <div class="flex items-center gap-1">
                @foreach ([['discover','Descobrir','compass'], ['mine','Minhas','users'], ['pending','Solicitações','clock']] as [$tab, $label, $icon])
                    <button wire:click="$set('activeTab', '{{ $tab }}')" type="button"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold transition relative
                                   {{ $activeTab === $tab
                                       ? 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white shadow-sm'
                                       : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                        <x-dynamic-component :component="'lucide-'.$icon" class="w-3.5 h-3.5" />
                        {{ $label }}
                        @if ($tab === 'pending' && $this->pendingRequests->count() > 0)
                            <span class="ml-0.5 inline-flex items-center justify-center w-4 h-4 rounded-full text-[10px] lato-bold
                                         {{ $activeTab === 'pending' ? 'bg-white/30 text-white' : 'bg-red-500 text-white' }}">
                                {{ $this->pendingRequests->count() }}
                            </span>
                        @endif
                    </button>
                @endforeach
            </div>
            {{-- Busca --}}
            <div class="relative flex-1 min-w-[160px]">
                <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                <input wire:model.live.debounce.300ms="searchQuery" type="search"
                       placeholder="Buscar comunidades…"
                       class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                              bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                              focus:outline-none focus:ring-2 focus:ring-indigo-400/30 placeholder-slate-400" />
            </div>
        </div>

        {{-- ── Tab: Descobrir ─────────────────────────────────────── --}}
        @if ($activeTab === 'discover')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($this->discoverCommunities as $community)
                    @php $membership = $myMemberships[$community->id] ?? null; @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                        {{-- Capa --}}
                        <div class="h-24 relative overflow-hidden">
                            @if ($community->cover_image)
                                <img src="{{ Storage::url($community->cover_image) }}"
                                     class="w-full h-full object-cover object-top" />
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-400 via-violet-500 to-purple-600"></div>
                            @endif
                            {{-- Avatar --}}
                            <div class="absolute -bottom-5 left-4">
                                <span class="w-11 h-11 rounded-xl {{ $community->avatar_color }} text-white text-sm lato-bold
                                             flex items-center justify-center ring-4 ring-white dark:ring-slate-800 shrink-0">
                                    {{ $community->initials }}
                                </span>
                            </div>
                        </div>

                        <div class="pt-7 px-4 pb-4 flex-1 flex flex-col">
                            <div class="flex items-start justify-between mb-1">
                                <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 leading-snug">{{ $community->name }}</h3>
                                @if ($community->is_private)
                                    <x-lucide-lock class="w-3.5 h-3.5 text-slate-400 shrink-0 mt-0.5" />
                                @endif
                            </div>
                            @if ($community->department)
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 lato-bold self-start mb-1">
                                    {{ $community->department->name }}
                                </span>
                            @endif
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular leading-relaxed line-clamp-2 flex-1 mb-3">
                                {{ $community->description ?: 'Sem descrição.' }}
                            </p>
                            <div class="flex items-center gap-3 text-[10px] text-slate-400 mb-3">
                                <span class="flex items-center gap-1"><x-lucide-users class="w-3 h-3" /> {{ $community->accepted_members_count }} membros</span>
                                <span class="flex items-center gap-1"><x-lucide-file-text class="w-3 h-3" /> {{ $community->posts_count }} posts</span>
                            </div>

                            @if ($membership === 'pending')
                                <button wire:click="cancelRequest({{ $community->id }})" type="button"
                                        class="w-full py-2 text-xs lato-bold rounded-xl border border-amber-300 dark:border-amber-700 text-amber-600 dark:text-amber-400 hover:bg-amber-50 dark:hover:bg-amber-900/20 transition flex items-center justify-center gap-1.5">
                                    <x-lucide-clock class="w-3.5 h-3.5" /> Pendente · Cancelar
                                </button>
                            @elseif ($membership === 'rejected')
                                <span class="w-full py-2 text-xs lato-bold rounded-xl bg-red-50 dark:bg-red-900/20 text-red-500 text-center block">
                                    Solicitação rejeitada
                                </span>
                            @else
                                <button wire:click="requestJoin({{ $community->id }})" type="button"
                                        class="w-full py-2 text-xs lato-bold rounded-xl transition flex items-center justify-center gap-1.5
                                               {{ $community->is_private
                                                   ? 'bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-700 hover:bg-indigo-100 dark:hover:bg-indigo-900/40'
                                                   : 'bg-gradient-to-r from-indigo-500 to-violet-600 text-white hover:from-indigo-600 hover:to-violet-700 shadow-sm' }}">
                                    @if ($community->is_private)
                                        <x-lucide-send class="w-3.5 h-3.5" /> Solicitar entrada
                                    @else
                                        <x-lucide-log-in class="w-3.5 h-3.5" /> Entrar
                                    @endif
                                </button>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="col-span-3 text-center py-16 text-slate-400 dark:text-slate-500">
                        <x-lucide-users class="w-12 h-12 mx-auto mb-3 opacity-40" />
                        <p class="text-sm lato-regular">Nenhuma comunidade encontrada.</p>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- ── Tab: Minhas ─────────────────────────────────────────── --}}
        @if ($activeTab === 'mine')
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                @forelse ($this->myCommunities as $community)
                    <a href="{{ route('communities.show', $community->slug) }}"
                       class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden flex flex-col hover:shadow-md hover:border-indigo-300 dark:hover:border-indigo-700 transition-all group">
                        <div class="h-24 relative overflow-hidden">
                            @if ($community->cover_image)
                                <img src="{{ Storage::url($community->cover_image) }}" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-300" />
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-400 via-violet-500 to-purple-600 group-hover:scale-105 transition-transform duration-300"></div>
                            @endif
                            <div class="absolute -bottom-5 left-4">
                                <span class="w-11 h-11 rounded-xl {{ $community->avatar_color }} text-white text-sm lato-bold
                                             flex items-center justify-center ring-4 ring-white dark:ring-slate-800">
                                    {{ $community->initials }}
                                </span>
                            </div>
                        </div>
                        <div class="pt-7 px-4 pb-4 flex-1 flex flex-col">
                            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 mb-0.5 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition">{{ $community->name }}</h3>
                            @if ($community->department)
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 lato-bold self-start mb-2">{{ $community->department->name }}</span>
                            @endif
                            <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular leading-relaxed line-clamp-2 flex-1 mb-3">
                                {{ $community->description ?: 'Sem descrição.' }}
                            </p>
                            <div class="flex items-center gap-3 text-[10px] text-slate-400">
                                <span class="flex items-center gap-1"><x-lucide-users class="w-3 h-3" /> {{ $community->accepted_members_count }} membros</span>
                                <span class="flex items-center gap-1"><x-lucide-file-text class="w-3 h-3" /> {{ $community->posts_count }} posts</span>
                            </div>
                        </div>
                    </a>
                @empty
                    <div class="col-span-3 text-center py-16 text-slate-400 dark:text-slate-500">
                        <x-lucide-users class="w-12 h-12 mx-auto mb-3 opacity-40" />
                        <p class="text-sm lato-regular">Você ainda não é membro de nenhuma comunidade.</p>
                        <button wire:click="$set('activeTab', 'discover')" type="button"
                                class="mt-3 text-xs text-indigo-500 hover:underline lato-bold">
                            Descobrir comunidades →
                        </button>
                    </div>
                @endforelse
            </div>
        @endif

        {{-- ── Tab: Solicitações pendentes ────────────────────────── --}}
        @if ($activeTab === 'pending')
            <div class="space-y-3">
                @forelse ($this->pendingRequests as $req)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl {{ $req->community->avatar_color ?? 'bg-indigo-500' }} text-white text-sm lato-bold flex items-center justify-center shrink-0">
                            {{ $req->community->initials ?? '?' }}
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $req->community->name }}</p>
                            <p class="text-xs text-slate-400 lato-regular">Solicitação enviada {{ $req->created_at->diffForHumans() }}</p>
                        </div>
                        <span class="flex items-center gap-1.5 text-xs text-amber-600 dark:text-amber-400 lato-bold px-3 py-1.5 bg-amber-50 dark:bg-amber-900/20 rounded-xl">
                            <x-lucide-clock class="w-3.5 h-3.5" /> Aguardando aprovação
                        </span>
                        <button wire:click="cancelRequest({{ $req->community_id }})" type="button"
                                class="p-2 rounded-xl text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                @empty
                    <div class="text-center py-16 text-slate-400 dark:text-slate-500">
                        <x-lucide-check-circle class="w-12 h-12 mx-auto mb-3 opacity-40" />
                        <p class="text-sm lato-regular">Nenhuma solicitação pendente.</p>
                    </div>
                @endforelse
            </div>
        @endif

    </div>

    {{-- ══════════════════════════════════════════════════════════════
         MODAL: Criar comunidade
    ══════════════════════════════════════════════════════════════ --}}
    @if ($createModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="$wire.set('createModal', false)" style="cursor:pointer">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg" @click.stop
                 x-data="{ coverPreview: null }">

                <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <x-lucide-users class="w-4 h-4 text-indigo-500" /> Nova comunidade
                    </h3>
                    <button wire:click="$set('createModal', false)" type="button"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <div class="p-5 space-y-4">
                    {{-- Capa --}}
                    <label class="block group cursor-pointer">
                        <div class="h-28 rounded-xl overflow-hidden bg-gradient-to-br from-indigo-400 to-violet-600 relative flex items-center justify-center">
                            <template x-if="coverPreview">
                                <img :src="coverPreview" class="w-full h-full object-cover absolute inset-0" />
                            </template>
                            <div class="relative z-10 flex flex-col items-center gap-1 text-white/80 group-hover:text-white transition">
                                <x-lucide-image class="w-6 h-6" />
                                <span class="text-xs lato-bold">Adicionar capa</span>
                            </div>
                        </div>
                        <input type="file" class="hidden" wire:model="newCoverImage" accept="image/*"
                               @change="const f=event.target.files[0]; if(f){ const r=new FileReader(); r.onload=e=>coverPreview=e.target.result; r.readAsDataURL(f); }" />
                    </label>

                    {{-- Nome --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nome da comunidade *</label>
                        <input wire:model="newName" type="text" maxlength="80" placeholder="Ex: Tecnologia & Inovação"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400" />
                        @error('newName') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Descrição --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição</label>
                        <textarea wire:model="newDescription" rows="3" maxlength="500" placeholder="Sobre o que é esta comunidade?"
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                         bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                         focus:outline-none focus:ring-2 focus:ring-indigo-400/30 focus:border-indigo-400 lato-regular placeholder-slate-400 resize-none"></textarea>
                    </div>

                    {{-- Setor --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Setor (opcional)</label>
                        <select wire:model="newDepartmentId"
                                class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                       bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                       focus:outline-none focus:ring-2 focus:ring-indigo-400/30">
                            <option value="">Nenhum</option>
                            @foreach ($this->departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Privacidade --}}
                    <label class="flex items-center gap-3 cursor-pointer">
                        <div class="relative">
                            <input wire:model="newIsPrivate" type="checkbox" class="sr-only peer" />
                            <div class="w-10 h-6 bg-slate-200 dark:bg-slate-700 rounded-full peer-checked:bg-indigo-500 transition-colors"></div>
                            <div class="absolute top-0.5 left-0.5 w-5 h-5 bg-white rounded-full shadow transition-transform peer-checked:translate-x-4"></div>
                        </div>
                        <div>
                            <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">Comunidade privada</p>
                            <p class="text-xs text-slate-400 lato-regular">Requer aprovação para entrar</p>
                        </div>
                    </label>
                </div>

                <div class="flex justify-end gap-2 px-5 pb-5">
                    <button wire:click="$set('createModal', false)" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                                   text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="createCommunity" type="button" wire:loading.attr="disabled"
                            class="px-5 py-2 text-xs lato-bold rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600
                                   text-white hover:from-indigo-600 hover:to-violet-700 disabled:opacity-50 transition shadow-sm">
                        <span wire:loading.remove wire:target="createCommunity">Criar comunidade</span>
                        <span wire:loading wire:target="createCommunity">Criando…</span>
                    </button>
                </div>
            </div>
        </div>
    @endif

</div>
