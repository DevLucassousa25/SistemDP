@php
    $me     = Auth::user();
    $meParts = explode(' ', trim($me->name ?? '?'));
    $meInit  = strtoupper(substr($meParts[0],0,1).(isset($meParts[1])?substr($meParts[1],0,1):''));
    $pal     = ['bg-indigo-500','bg-indigo-600','bg-pink-500','bg-teal-500','bg-amber-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
    $meBg    = $pal[abs(crc32($me->name ?? '')) % count($pal)];

    if (!function_exists('commBg')) {
        function commBg(string $name): string {
            $p = ['bg-indigo-500','bg-indigo-600','bg-pink-500','bg-teal-500','bg-amber-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
            return $p[abs(crc32($name)) % count($p)];
        }
    }
    if (!function_exists('commInit')) {
        function commInit(string $name): string {
            $parts = explode(' ', trim($name));
            return strtoupper(substr($parts[0],0,1).(isset($parts[1])?substr($parts[1],0,1):''));
        }
    }

    $community = $this->community;
    $isAdmin   = $this->isAdmin;
@endphp

<div class="min-h-screen bg-slate-50 dark:bg-slate-900
            [&_button:not([disabled])]:cursor-pointer [&_a]:cursor-pointer [&_label]:cursor-pointer">

    {{-- ── Banner da comunidade ────────────────────────────────────── --}}
    <div class="relative">
        {{-- Imagem do banner --}}
        <div class="h-44 sm:h-56 w-full overflow-hidden bg-gradient-to-r from-indigo-600 via-violet-600 to-indigo-600 relative">
            @if ($community->cover_image)
                <img src="{{ Storage::url($community->cover_image) }}"
                     class="w-full h-full object-cover object-top" />
            @endif
            {{-- Gradiente de legibilidade --}}
            <div class="absolute inset-0 bg-gradient-to-t from-black/75 via-black/20 to-transparent"></div>

            {{-- Nome + info sobrepostos ao banner --}}
            <div class="absolute bottom-0 left-0 right-0 max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 pb-3 sm:pb-4">
                <div class="flex items-end gap-3">
                    {{-- Avatar --}}
                    <span class="w-14 h-14 sm:w-20 sm:h-20 rounded-xl sm:rounded-2xl {{ $community->avatar_color }} text-white
                                 text-lg sm:text-2xl lato-bold flex items-center justify-center
                                 ring-2 sm:ring-4 ring-white/20 shrink-0 shadow-2xl">
                        {{ $community->initials }}
                    </span>
                    <div class="flex-1 min-w-0 mb-0.5">
                        <h1 class="text-base sm:text-xl lato-bold text-white flex items-center gap-1.5 drop-shadow leading-tight">
                            {{ $community->name }}
                            @if ($community->is_private)
                                <x-lucide-lock class="w-3.5 h-3.5 sm:w-4 sm:h-4 text-white/70 shrink-0" />
                            @endif
                        </h1>
                        <div class="flex items-center gap-1.5 flex-wrap mt-1">
                            @if ($community->department)
                                <span class="text-[10px] px-2 py-0.5 rounded-full bg-white/20 backdrop-blur-sm text-white lato-bold">
                                    {{ $community->department->name }}
                                </span>
                            @endif
                            <span class="text-[10px] text-white/80 lato-regular flex items-center gap-1">
                                <x-lucide-users class="w-2.5 h-2.5" />
                                {{ $community->accepted_members_count }} {{ $community->accepted_members_count === 1 ? 'membro' : 'membros' }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Barra de ações — scrollável no mobile --}}
        <div class="bg-white dark:bg-slate-800">
            <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8">
                <div class="flex items-center gap-1.5 py-2.5 overflow-x-auto scrollbar-hide">

                    {{-- Descrição inline — só desktop --}}
                    @if ($community->description)
                        <div class="hidden sm:block flex-1 min-w-0 pl-2">
                            <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular truncate">
                                {{ $community->description }}
                            </p>
                        </div>
                    @endif

                    {{-- Botões de admin --}}
                    @if ($isAdmin)
                        <button wire:click="$set('requestsModal', true)" type="button"
                                class="relative flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold whitespace-nowrap shrink-0
                                       bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700
                                       text-amber-600 dark:text-amber-400 hover:bg-amber-100 dark:hover:bg-amber-900/40 transition">
                            <x-lucide-bell class="w-3.5 h-3.5 shrink-0" />
                            Solicitações
                            @if ($this->pendingCount > 0)
                                <span class="absolute -top-1 -right-1 min-w-[16px] h-4 px-0.5 rounded-full bg-red-500 text-white text-[9px] lato-bold flex items-center justify-center">
                                    {{ $this->pendingCount > 9 ? '9+' : $this->pendingCount }}
                                </span>
                            @endif
                        </button>

                        <button wire:click="$set('statsModal', true)" type="button"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold whitespace-nowrap shrink-0
                                       border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300
                                       hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            <x-lucide-bar-chart-2 class="w-3.5 h-3.5 shrink-0" /> Estatísticas
                        </button>

                        <button wire:click="openEditCommunity" type="button"
                                class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold whitespace-nowrap shrink-0
                                       border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300
                                       hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                            <x-lucide-settings class="w-3.5 h-3.5 shrink-0" /> Configurar
                        </button>
                    @endif

                    <button wire:click="$set('membersModal', true)" type="button"
                            class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold whitespace-nowrap shrink-0
                                   border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300
                                   hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-users class="w-3.5 h-3.5 shrink-0" /> Membros
                    </button>

                    <a href="{{ route('communities') }}" wire:navigate
                       class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold whitespace-nowrap shrink-0
                              border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400
                              hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-arrow-left class="w-3.5 h-3.5 shrink-0" /> Voltar
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-5xl mx-auto px-3 sm:px-6 lg:px-8 pb-10 mt-3 sm:mt-4">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

            {{-- ══════════════════════════════════════════════════════
                 COLUNA PRINCIPAL
            ══════════════════════════════════════════════════════ --}}
            <div class="lg:col-span-2 space-y-4">

                {{-- Criar post --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4"
                     x-data="{
                         expanded: @entangle('postBoxExpanded'),
                         showPoll: @entangle('showPollCreator'),
                         previewUrls: [],
                         addFiles(e) {
                             Array.from(e.target.files).forEach(f => {
                                 const r = new FileReader();
                                 r.onload = ev => this.previewUrls.push(ev.target.result);
                                 r.readAsDataURL(f);
                             });
                             e.target.value = '';
                         },
                         clearImages() {
                             this.previewUrls = [];
                             const dt = new DataTransfer();
                             if (this.$refs.fileInput) this.$refs.fileInput.files = dt.files;
                         }
                     }"
                     @post-created.window="clearImages(); expanded = false; showPoll = false;">
                    <div class="flex items-start gap-3">
                        <span class="w-10 h-10 rounded-full {{ $meBg }} text-white text-sm lato-bold flex items-center justify-center shrink-0 mt-0.5">{{ $meInit }}</span>
                        <div class="flex-1">
                            <textarea wire:model="newPostContent" @focus="expanded = true"
                                      placeholder="Compartilhe algo com a comunidade…"
                                      x-bind:rows="expanded ? 4 : 1"
                                      maxlength="2000"
                                      class="w-full px-4 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                             bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                             focus:outline-none focus:ring-2 focus:ring-indigo-400/30 placeholder-slate-400 resize-none transition-all"></textarea>

                            {{-- Preview imagens --}}
                            <template x-if="expanded && previewUrls.length > 0">
                                <div class="mt-2 grid gap-1" x-bind:class="previewUrls.length === 1 ? 'grid-cols-1' : 'grid-cols-2'">
                                    <template x-for="(url, i) in previewUrls" :key="i">
                                        <div class="rounded-xl overflow-hidden bg-slate-100 dark:bg-slate-900 aspect-video">
                                            <img :src="url" class="w-full h-full object-cover" />
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Enquete creator --}}
                            <div x-show="showPoll" x-transition class="mt-3 p-3 rounded-xl bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-200 dark:border-indigo-700 space-y-2">
                                <p class="text-xs lato-bold text-indigo-700 dark:text-indigo-300 flex items-center gap-1.5">
                                    <x-lucide-bar-chart-2 class="w-3.5 h-3.5" /> Enquete
                                </p>
                                <input wire:model="pollQuestion" type="text" placeholder="Qual é a pergunta?" maxlength="200"
                                       class="w-full px-3 py-2 text-sm border border-indigo-200 dark:border-indigo-700 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-indigo-400/30" />
                                @foreach ($pollOptions as $i => $opt)
                                    <div class="flex items-center gap-2">
                                        <input wire:model="pollOptions.{{ $i }}" type="text" placeholder="Opção {{ $i + 1 }}" maxlength="100"
                                               class="flex-1 px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none" />
                                        @if (count($pollOptions) > 2)
                                            <button wire:click="removePollOption({{ $i }})" type="button" class="p-1 text-slate-400 hover:text-red-500 transition">
                                                <x-lucide-x class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                @if (count($pollOptions) < 6)
                                    <button wire:click="addPollOption" type="button" class="text-xs text-indigo-600 dark:text-indigo-400 hover:underline">+ Adicionar opção</button>
                                @endif
                            </div>

                            {{-- Seletor de tipo de post --}}
                            <div x-show="expanded" x-transition class="flex items-center gap-1 mt-2 flex-wrap">
                                @php
                                    $typeOpts = [
                                        'post'      => ['label' => 'Post',      'icon' => 'message-circle', 'active' => 'text-slate-600 dark:text-slate-300 bg-slate-100 dark:bg-slate-700'],
                                        'aviso'     => ['label' => 'Aviso',     'icon' => 'megaphone',      'active' => 'text-orange-600 bg-orange-50 dark:bg-orange-900/20'],
                                        'evento'    => ['label' => 'Evento',    'icon' => 'calendar',       'active' => 'text-purple-600 bg-purple-50 dark:bg-purple-900/20'],
                                        'discussao' => ['label' => 'Discussão', 'icon' => 'message-square', 'active' => 'text-blue-600 bg-blue-50 dark:bg-blue-900/20'],
                                    ];
                                @endphp
                                <span class="text-[10px] text-slate-400 lato-regular shrink-0">Tipo:</span>
                                @foreach ($typeOpts as $tVal => $tCfg)
                                    <button type="button" wire:click="$set('newPostType', '{{ $tVal }}')"
                                            class="flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] lato-bold transition shrink-0
                                                   {{ $newPostType === $tVal ? $tCfg['active'] . ' ring-1 ring-current/20' : 'text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                                        <x-dynamic-component :component="'lucide-' . $tCfg['icon']" class="w-3 h-3" />
                                        {{ $tCfg['label'] }}
                                    </button>
                                @endforeach
                            </div>

                            {{-- Campos de evento (só quando tipo = evento) --}}
                            @if ($newPostType === 'evento')
                                <div class="mt-2 space-y-2 p-3 rounded-xl bg-purple-50 dark:bg-purple-900/10 border border-purple-200 dark:border-purple-800/40">
                                    <p class="text-[10px] lato-bold text-purple-600 dark:text-purple-400 flex items-center gap-1.5">
                                        <x-lucide-calendar class="w-3 h-3" /> Detalhes do evento
                                    </p>
                                    <input wire:model="eventLocation" type="text" placeholder="Local do evento (opcional)" maxlength="200"
                                           class="w-full px-3 py-2 text-sm border border-purple-200 dark:border-purple-700 rounded-xl
                                                  bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                  focus:outline-none focus:ring-2 focus:ring-purple-400/30 placeholder-slate-400 lato-regular" />
                                    <div class="grid grid-cols-2 gap-2">
                                        <div>
                                            <label class="block text-[10px] lato-bold text-purple-500 mb-1">Início *</label>
                                            <input wire:model="eventDate" type="datetime-local"
                                                   class="w-full px-3 py-2 text-sm border border-purple-200 dark:border-purple-700 rounded-xl
                                                          bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                          focus:outline-none focus:ring-2 focus:ring-purple-400/30 lato-regular" />
                                        </div>
                                        <div>
                                            <label class="block text-[10px] lato-bold text-purple-500 mb-1">Término</label>
                                            <input wire:model="eventEndsAt" type="datetime-local"
                                                   class="w-full px-3 py-2 text-sm border border-purple-200 dark:border-purple-700 rounded-xl
                                                          bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                                          focus:outline-none focus:ring-2 focus:ring-purple-400/30 lato-regular" />
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <div x-show="expanded" x-transition class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-3 mt-3">
                        <div class="flex items-center gap-1">
                            <label class="p-2 rounded-xl text-slate-400 hover:text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition cursor-pointer" title="Imagens">
                                <x-lucide-image class="w-4 h-4" />
                                <input type="file" class="hidden" x-ref="fileInput" wire:model="newPostImages" accept="image/*" multiple @change="addFiles($event)" />
                            </label>
                            <button type="button" @click="showPoll = !showPoll" class="p-2 rounded-xl text-slate-400 hover:text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition" title="Enquete">
                                <x-lucide-bar-chart-2 class="w-4 h-4" />
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 lato-regular">{{ strlen($newPostContent) }}/2000</span>
                            <button wire:click="createPost" @click="clearImages(); expanded = false; showPoll = false;" type="button" wire:loading.attr="disabled"
                                    class="px-5 py-2 text-xs lato-bold rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 text-white
                                           hover:from-indigo-600 hover:to-violet-700 disabled:opacity-50 transition shadow-sm">
                                <span wire:loading.remove wire:target="createPost">Publicar</span>
                                <span wire:loading wire:target="createPost">Publicando…</span>
                            </button>
                        </div>
                    </div>
                    @error('newPostContent') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Posts fixados --}}
                @foreach ($this->pinnedPosts as $post)
                    @php
                        $myRx  = $post->reactions->where('user_id', Auth::id())->first()?->type;
                        $autName = $post->user->name ?? '?';
                    @endphp
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border-2 border-indigo-200 dark:border-indigo-700/50 overflow-hidden">
                        <div class="px-4 pt-3 pb-0 flex items-center gap-2">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] lato-bold bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400">
                                <x-lucide-pin class="w-2.5 h-2.5" /> Fixado
                            </span>
                        </div>
                        <div class="p-4">
                            @include('livewire.pages.communities._post_body', ['post' => $post, 'myRx' => $myRx, 'authorName' => $autName, 'pinned' => true])
                        </div>
                    </div>
                @endforeach

                {{-- Feed de posts --}}
                <div wire:loading wire:target="loadMore" class="w-full space-y-3" style="display:none">
                    @for ($sk = 0; $sk < 2; $sk++)
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4 animate-pulse space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-slate-200 dark:bg-slate-700 shrink-0"></div>
                                <div class="flex-1 space-y-1.5">
                                    <div class="h-4 bg-slate-200 dark:bg-slate-700 rounded-md w-1/3"></div>
                                    <div class="h-3 bg-slate-200 dark:bg-slate-700 rounded-md w-1/4"></div>
                                </div>
                            </div>
                            <div class="h-5 bg-slate-200 dark:bg-slate-700 rounded-md w-full"></div>
                            <div class="h-5 bg-slate-200 dark:bg-slate-700 rounded-md w-5/6"></div>
                        </div>
                    @endfor
                </div>

                <div class="space-y-3">
                    @forelse ($this->feedItems as $post)
                        @php
                            $myRx     = $post->reactions->first()?->type;
                            $autName  = $post->user->name ?? '?';
                        @endphp
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
                            <div class="p-4">
                                @include('livewire.pages.communities._post_body', ['post' => $post, 'myRx' => $myRx, 'authorName' => $autName, 'pinned' => false])
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-14 text-slate-400 dark:text-slate-500">
                            <div class="w-16 h-16 rounded-2xl bg-indigo-50 dark:bg-indigo-900/20 flex items-center justify-center mx-auto mb-4">
                                <x-lucide-message-square class="w-8 h-8 text-indigo-300 dark:text-indigo-600" />
                            </div>
                            <p class="text-sm lato-bold text-slate-500 dark:text-slate-400 mb-1">Nenhum post ainda</p>
                            <p class="text-xs lato-regular text-slate-400 dark:text-slate-500">Seja o primeiro a compartilhar algo com a comunidade!</p>
                        </div>
                    @endforelse
                </div>

                {{-- Sentinel --}}
                @php $feedCount = $this->feedItems->count(); @endphp
                @if ($feedCount > 0 && $feedCount >= $loadedCount)
                    <div wire:loading.remove wire:target="loadMore"
                         x-data
                         x-init="const obs = new IntersectionObserver(e => { if(e[0].isIntersecting) $wire.loadMore(); }, { rootMargin: '200px' }); obs.observe($el);"
                         class="h-4"></div>
                @elseif ($feedCount > 0)
                    <p class="text-center text-xs text-slate-400 dark:text-slate-600 py-4 lato-regular">— Você chegou ao fim —</p>
                @endif

            </div>{{-- /col principal --}}

            {{-- ══════════════════════════════════════════════════════
                 SIDEBAR
            ══════════════════════════════════════════════════════ --}}
            <div class="space-y-4 sticky top-4">

                {{-- Info comunidade --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <h3 class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Sobre a comunidade</h3>
                    <p class="text-xs text-slate-600 dark:text-slate-300 lato-regular leading-relaxed mb-3">
                        {{ $community->description ?: 'Sem descrição.' }}
                    </p>
                    <div class="space-y-2 text-xs text-slate-500 dark:text-slate-400 lato-regular">
                        <div class="flex items-center gap-2">
                            <x-lucide-users class="w-3.5 h-3.5 text-indigo-400" />
                            <span>{{ $community->accepted_members_count }} membros</span>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($community->is_private)
                                <x-lucide-lock class="w-3.5 h-3.5 text-amber-400" />
                                <span>Comunidade privada · requer aprovação</span>
                            @else
                                <x-lucide-globe class="w-3.5 h-3.5 text-green-400" />
                                <span>Comunidade aberta</span>
                            @endif
                        </div>
                        <div class="flex items-center gap-2">
                            <x-lucide-user class="w-3.5 h-3.5 text-slate-400" />
                            <span>Criada por {{ $community->creator->name ?? '?' }}</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <x-lucide-calendar class="w-3.5 h-3.5 text-slate-400" />
                            <span>{{ $community->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @if (! $isAdmin)
                        <button wire:click="leaveCommunity" type="button"
                                wire:confirm="Tem certeza que deseja sair desta comunidade?"
                                class="mt-4 w-full py-2 text-xs lato-bold rounded-xl border border-red-200 dark:border-red-800/50 text-red-500 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition">
                            Sair da comunidade
                        </button>
                    @endif
                </div>

                {{-- Membros recentes --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="flex items-center justify-between mb-3">
                        <h3 class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">
                            Membros
                        </h3>
                        <button wire:click="$set('membersModal', true)" type="button"
                                class="text-xs text-indigo-500 dark:text-indigo-400 hover:underline lato-regular">ver todos</button>
                    </div>
                    <div class="space-y-3">
                        @foreach ($this->acceptedMembers->take(6) as $member)
                            @php $mName = $member->user->name ?? '?'; @endphp
                            <div class="flex items-center gap-2.5">
                                <span class="w-8 h-8 rounded-full {{ commBg($mName) }} text-white text-xs lato-bold flex items-center justify-center shrink-0">
                                    {{ commInit($mName) }}
                                </span>
                                <div class="flex-1 min-w-0">
                                    <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $mName }}</p>
                                    <p class="text-[10px] lato-regular {{ $member->role === 'admin' ? 'text-indigo-500 dark:text-indigo-400' : 'text-slate-400' }}">
                                        @if ($member->role === 'admin')
                                            <x-lucide-shield-check class="w-3 h-3 inline -mt-0.5 mr-0.5" />Admin
                                        @else
                                            Membro
                                        @endif
                                    </p>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                {{-- Regras da comunidade --}}
                @if ($community->rules || $isAdmin)
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                        <h3 class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide flex items-center gap-1.5 mb-3">
                            <x-lucide-scroll-text class="w-3.5 h-3.5 text-indigo-400" /> Regras
                        </h3>
                        @if ($community->rules)
                            <div class="text-xs text-slate-600 dark:text-slate-300 lato-regular leading-relaxed whitespace-pre-line">
                                {{ $community->rules }}
                            </div>
                        @elseif ($isAdmin)
                            <p class="text-xs text-slate-400 italic">Sem regras definidas. <button wire:click="openEditCommunity" type="button" class="text-indigo-400 hover:underline not-italic">Adicionar →</button></p>
                        @endif
                    </div>
                @endif

            </div>

        </div>
    </div>

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Membros
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($membersModal)
        <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
             x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.set('membersModal', false), 300); } }"
             x-init="requestAnimationFrame(() => open = true)"
             @click.self="close()">
            <div @click.stop
                 class="bg-white dark:bg-slate-800 shadow-2xl w-full rounded-t-3xl sm:rounded-2xl sm:max-w-md sm:mx-4
                        flex flex-col transition-transform duration-300 ease-out will-change-transform"
                 :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
                 style="max-height: 88dvh;">

                {{-- Drag handle --}}
                <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                    <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                </div>

                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <x-lucide-users class="w-4 h-4 text-indigo-500" />
                        Membros · {{ $this->acceptedMembers->count() }}
                    </h3>
                    <button @click="close()" type="button"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-5 space-y-3 pb-[max(1.25rem,env(safe-area-inset-bottom))]">
                    @foreach ($this->acceptedMembers as $member)
                        @php $mName = $member->user->name ?? '?'; @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-full {{ commBg($mName) }} text-white text-xs lato-bold flex items-center justify-center shrink-0">
                                {{ commInit($mName) }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $mName }}</p>
                                <p class="text-[10px] text-slate-400">
                                    @if ($member->role === 'admin') <x-lucide-shield-check class="w-3 h-3 inline -mt-0.5 mr-0.5" />Admin @else Membro @endif
                                    @if ($member->joined_at) · desde {{ $member->joined_at->format('d/m/Y') }} @endif
                                </p>
                            </div>
                            @if ($isAdmin && $member->role !== 'admin')
                                <div class="relative flex items-center gap-1" x-data="{ open: false }" @click.away="open=false">
                                    <button @click="open=!open" type="button"
                                            class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                        <x-lucide-more-horizontal class="w-4 h-4" />
                                    </button>
                                    <div x-show="open" x-transition
                                         class="absolute right-0 top-8 w-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg py-1 z-20">
                                        <button wire:click="promoteToAdmin({{ $member->id }})" @click="open=false" type="button"
                                                class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                                            <x-lucide-star class="w-3.5 h-3.5 text-amber-500" /> Tornar admin
                                        </button>
                                        <button wire:click="removeMember({{ $member->id }})" @click="open=false" type="button"
                                                wire:confirm="Remover este membro da comunidade?"
                                                class="w-full text-left px-3 py-2 text-xs text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2">
                                            <x-lucide-user-x class="w-3.5 h-3.5" /> Remover
                                        </button>
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Solicitações pendentes (admin)
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($requestsModal && $isAdmin)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="$wire.set('requestsModal', false)" style="cursor:pointer">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md max-h-[80vh] flex flex-col" @click.stop>
                <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <x-lucide-bell class="w-4 h-4 text-amber-500" />
                        Solicitações pendentes · {{ $this->pendingMembers->count() }}
                    </h3>
                    <button wire:click="$set('requestsModal', false)" type="button"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <div class="flex-1 overflow-y-auto p-5 space-y-3">
                    @forelse ($this->pendingMembers as $req)
                        @php $rName = $req->user->name ?? '?'; @endphp
                        <div class="flex items-center gap-3">
                            <span class="w-9 h-9 rounded-full {{ commBg($rName) }} text-white text-xs lato-bold flex items-center justify-center shrink-0">
                                {{ commInit($rName) }}
                            </span>
                            <div class="flex-1 min-w-0">
                                <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $rName }}</p>
                                <p class="text-[10px] text-slate-400">Solicitou {{ $req->created_at->diffForHumans() }}</p>
                            </div>
                            <div class="flex items-center gap-1.5 shrink-0">
                                <button wire:click="approveMember({{ $req->id }})" type="button"
                                        class="p-2 rounded-xl bg-green-50 dark:bg-green-900/20 text-green-600 dark:text-green-400 hover:bg-green-100 dark:hover:bg-green-900/40 transition">
                                    <x-lucide-check class="w-4 h-4" />
                                </button>
                                <button wire:click="rejectMember({{ $req->id }})" type="button"
                                        class="p-2 rounded-xl bg-red-50 dark:bg-red-900/20 text-red-500 hover:bg-red-100 dark:hover:bg-red-900/40 transition">
                                    <x-lucide-x class="w-4 h-4" />
                                </button>
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-8 text-slate-400">
                            <x-lucide-check-circle class="w-10 h-10 mx-auto mb-2 opacity-40" />
                            <p class="text-sm lato-regular">Nenhuma solicitação pendente.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Editar post
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($editPostModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="$wire.set('editPostModal', false)" style="cursor:pointer">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg p-5" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">Editar post</h3>
                    <button wire:click="$set('editPostModal', false)" type="button"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <textarea wire:model="editContent" rows="5" maxlength="2000"
                          class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 resize-none lato-regular"></textarea>
                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="$set('editPostModal', false)" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="saveEditPost" type="button"
                            class="px-5 py-2 text-xs lato-bold rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 text-white hover:from-indigo-600 hover:to-violet-700 transition shadow-sm disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveEditPost" class="flex items-center gap-1.5">
                            Salvar
                        </span>
                        <span wire:loading wire:target="saveEditPost" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Excluir post
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($deletePostModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div class="bg-white dark:bg-slate-800 rounded-2xl p-6 shadow-2xl max-w-sm w-full">
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-trash-2 class="w-5 h-5 text-red-500" />
                    </div>
                    <div>
                        <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">Excluir post?</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Essa ação não pode ser desfeita.</p>
                    </div>
                </div>
                <div class="flex gap-2 justify-end">
                    <button wire:click="$set('deletePostModal', false)" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="deletePost" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl bg-red-500 text-white hover:bg-red-600 transition disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deletePost" class="flex items-center gap-1.5">
                            Excluir
                        </span>
                        <span wire:loading wire:target="deletePost" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Editar comunidade (admin)
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($editCommunityModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="$wire.set('editCommunityModal', false)" style="cursor:pointer">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-5" @click.stop
                 x-data="{ coverPreview: null }">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <x-lucide-settings class="w-4 h-4 text-indigo-500" /> Configurar comunidade
                    </h3>
                    <button wire:click="$set('editCommunityModal', false)" type="button"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <div class="space-y-4">
                    {{-- Capa --}}
                    <label class="block group cursor-pointer">
                        <div class="h-24 rounded-xl overflow-hidden relative flex items-center justify-center">
                            @if ($community->cover_image)
                                <img src="{{ Storage::url($community->cover_image) }}" class="w-full h-full object-cover object-top absolute inset-0" />
                            @else
                                <div class="w-full h-full bg-gradient-to-br from-indigo-400 to-violet-600 absolute inset-0"></div>
                            @endif
                            <template x-if="coverPreview">
                                <img :src="coverPreview" class="w-full h-full object-cover absolute inset-0" />
                            </template>
                            <div class="relative z-10 bg-black/40 rounded-lg px-3 py-1.5 flex items-center gap-1.5 text-white text-xs lato-bold">
                                <x-lucide-image class="w-3.5 h-3.5" /> Alterar capa
                            </div>
                        </div>
                        <input type="file" class="hidden" wire:model="editCoverImage" accept="image/*"
                               @change="const f=event.target.files[0]; if(f){ const r=new FileReader(); r.onload=e=>coverPreview=e.target.result; r.readAsDataURL(f); }" />
                    </label>

                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Nome</label>
                        <input wire:model="editName" type="text" maxlength="80"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular" />
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Descrição</label>
                        <textarea wire:model="editDescription" rows="3" maxlength="500"
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                         bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                         focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular resize-none"></textarea>
                    </div>
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">
                            Regras da comunidade <span class="font-normal text-slate-400">(opcional)</span>
                        </label>
                        <textarea wire:model="editRules" rows="4" maxlength="1000"
                                  placeholder="Liste as regras, uma por linha…"
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                         bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                         focus:outline-none focus:ring-2 focus:ring-indigo-400/30 lato-regular resize-none"></textarea>
                    </div>
                </div>
                <div class="flex justify-end gap-2 mt-5">
                    <button wire:click="$set('editCommunityModal', false)" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="saveEditCommunity" type="button"
                            class="px-5 py-2 text-xs lato-bold rounded-xl bg-gradient-to-r from-indigo-500 to-violet-600 text-white hover:from-indigo-600 hover:to-violet-700 transition shadow-sm disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveEditCommunity" class="flex items-center gap-1.5">
                            Salvar
                        </span>
                        <span wire:loading wire:target="saveEditCommunity" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Estatísticas (admin)
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($statsModal && $isAdmin)
        @php $stats = $this->communityStats; @endphp
        <div class="fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-end sm:items-center justify-center"
             x-data="{ open: false, close() { this.open = false; setTimeout(() => $wire.set('statsModal', false), 300); } }"
             x-init="requestAnimationFrame(() => open = true)"
             @click.self="close()">
            <div @click.stop
                 class="bg-white dark:bg-slate-800 shadow-2xl w-full rounded-t-3xl sm:rounded-2xl sm:max-w-md sm:mx-4
                        flex flex-col transition-transform duration-300 ease-out will-change-transform"
                 :class="open ? 'translate-y-0' : 'translate-y-full sm:translate-y-4'"
                 style="max-height: 88dvh;">

                {{-- Drag handle --}}
                <div class="flex justify-center pt-3 pb-1 sm:hidden shrink-0">
                    <div class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></div>
                </div>

                <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <x-lucide-bar-chart-2 class="w-4 h-4 text-indigo-500" /> Estatísticas da comunidade
                    </h3>
                    <button @click="close()" type="button"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <div class="flex-1 overflow-y-auto p-5 space-y-5 pb-[max(1.25rem,env(safe-area-inset-bottom))]">

                    {{-- Números principais --}}
                    <div class="grid grid-cols-3 gap-3">
                        <div class="bg-indigo-50 dark:bg-indigo-900/20 rounded-xl p-3 text-center">
                            <p class="text-xl lato-bold text-indigo-600 dark:text-indigo-400">{{ $stats['total_posts'] }}</p>
                            <p class="text-[10px] text-slate-500 lato-regular mt-0.5">Total de posts</p>
                        </div>
                        <div class="bg-violet-50 dark:bg-violet-900/20 rounded-xl p-3 text-center">
                            <p class="text-xl lato-bold text-indigo-600 dark:text-indigo-400">{{ $stats['posts_week'] }}</p>
                            <p class="text-[10px] text-slate-500 lato-regular mt-0.5">Esta semana</p>
                        </div>
                        <div class="bg-teal-50 dark:bg-teal-900/20 rounded-xl p-3 text-center">
                            <p class="text-xl lato-bold text-teal-600 dark:text-teal-400">{{ $stats['active_members'] }}</p>
                            <p class="text-[10px] text-slate-500 lato-regular mt-0.5">Ativos (30d)</p>
                        </div>
                    </div>

                    {{-- Breakdown por tipo --}}
                    @if (! empty($stats['types']))
                        <div>
                            <p class="text-[10px] lato-bold text-slate-500 uppercase tracking-wide mb-2">Posts por tipo</p>
                            <div class="space-y-1.5">
                                @php
                                    $typeLabels = ['post' => 'Geral', 'aviso' => 'Aviso', 'evento' => 'Evento', 'discussao' => 'Discussão'];
                                    $typeColors = ['post' => 'bg-slate-400', 'aviso' => 'bg-orange-400', 'evento' => 'bg-purple-400', 'discussao' => 'bg-blue-400'];
                                    $totalTyped = array_sum($stats['types']);
                                @endphp
                                @foreach ($stats['types'] as $typeKey => $typeCount)
                                    @php $pct = $totalTyped > 0 ? round($typeCount / $totalTyped * 100) : 0; @endphp
                                    <div class="flex items-center gap-2">
                                        <div class="flex-1 h-2 bg-slate-100 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="h-full rounded-full bg-indigo-500" style="width: {{ min(100, $typeCount) }}%"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif
