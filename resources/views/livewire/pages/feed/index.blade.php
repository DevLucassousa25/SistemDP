@php
    use App\Models\FeedPost;
    $me     = Auth::user();
    $meParts = explode(' ', trim($me->name ?? '?'));
    $meInit  = strtoupper(substr($meParts[0], 0, 1) . (isset($meParts[1]) ? substr($meParts[1], 0, 1) : ''));
    $pal     = ['bg-indigo-500','bg-indigo-600','bg-pink-500','bg-teal-500','bg-amber-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
    $meBg    = $pal[abs(crc32($me->name ?? '')) % count($pal)];

    if (!function_exists('feedInitials')) {
        function feedInitials(string $name): string {
            $p = explode(' ', trim($name));
            return strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));
        }
    }
    if (!function_exists('feedBg')) {
        function feedBg(string $name): string {
            $pal2 = ['bg-indigo-500','bg-indigo-600','bg-pink-500','bg-teal-500','bg-amber-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
            return $pal2[abs(crc32($name)) % count($pal2)];
        }
    }
    if (!function_exists('renderFeedContent')) {
        function renderFeedContent(string $content): string {
            $safe = e($content);
            // Hashtags → clickable spans (event delegation via data-hashtag)
            $safe = preg_replace_callback(
                '/(?<![&\w])#([\w\p{L}]+)/u',
                fn ($m) => '<span class="text-blue-500 lato-bold cursor-pointer hover:underline" data-hashtag="' . e($m[1]) . '">#' . e($m[1]) . '</span>',
                $safe
            );
            // @mentions → highlighted spans
            $safe = preg_replace_callback(
                '/(?<![&\w])@([\w\p{L}]+)/u',
                fn ($m) => '<span class="text-indigo-500 lato-bold">@' . e($m[1]) . '</span>',
                $safe
            );
            return nl2br($safe);
        }
    }
@endphp

<div id="feed-root" class="min-h-screen bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 lg:p-8 [&_button:not([disabled])]:cursor-pointer [&_a]:cursor-pointer [&_select]:cursor-pointer [&_label]:cursor-pointer"
     x-data="{
         lightbox: { open: false, src: '', alt: '' },
         openLightbox(src, alt) { this.lightbox = { open: true, src, alt: alt||'' }; document.body.style.overflow='hidden'; },
         closeLightbox() { this.lightbox.open = false; document.body.style.overflow=''; }
     }"
     @open-lightbox.window="openLightbox($event.detail.src, $event.detail.alt)"
     @keydown.escape.window="if(lightbox.open) closeLightbox()"
     @click="if($event.target.dataset.hashtag) $wire.setHashtag($event.target.dataset.hashtag)"
     @post-created.window="
         const targetId = $event.detail.postId;
         const tryScroll = (attempts) => {
             const el = document.querySelector('[data-feed-post-id=\'' + targetId + '\']');
             if (el) {
                 el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                 el.classList.add('feed-post-flash');
                 setTimeout(() => el.classList.remove('feed-post-flash'), 2500);
             } else if (attempts > 0) {
                 setTimeout(() => tryScroll(attempts - 1), 200);
             } else {
                 window.scrollTo({ top: 0, behavior: 'smooth' });
             }
         };
         setTimeout(() => tryScroll(8), 300);
     "
     @new-posts-available.window="if (window.scrollY < 300) $wire.refreshFeed()">

    <div class="max-w-6xl mx-auto">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-5 items-start">

            {{-- ══════════════════════════════════════════════════════════
                 COLUNA PRINCIPAL
            ══════════════════════════════════════════════════════════ --}}
            <div class="lg:col-span-2 space-y-3" wire:poll.30s="checkNewPosts">

                {{-- Banner novos posts --}}
                @if ($newPostsBanner > 0)
                    <button wire:click="refreshFeed" type="button"
                            class="w-full flex items-center justify-center gap-2 py-2.5 rounded-2xl text-xs lato-bold
                                   bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-md
                                   hover:from-blue-600 hover:to-indigo-700 transition cursor-pointer"
                            x-transition:enter="transition ease-out duration-300"
                            x-transition:enter-start="opacity-0 -translate-y-2"
                            x-transition:enter-end="opacity-100 translate-y-0">
                        <x-lucide-arrow-up class="w-3.5 h-3.5" />
                        {{ $newPostsBanner }} {{ $newPostsBanner === 1 ? 'nova publicação' : 'novas publicações' }} — clique para atualizar
                    </button>
                @endif

                {{-- ── Criar post ──────────────────────────────────── --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4"
                     @post-created.window="clearImages()"
                     x-data="{
                         expanded: @entangle('postBoxExpanded'),
                         showPoll: @entangle('showPollCreator'),
                         previewUrls: [],
                         imageFiles: [],
                         carouselIdx: 0,
                         addFiles(e) {
                             const newFiles = Array.from(e.target.files);
                             newFiles.forEach(f => {
                                 this.imageFiles.push(f);
                                 const r = new FileReader();
                                 r.onload = ev => this.previewUrls.push(ev.target.result);
                                 r.readAsDataURL(f);
                             });
                             e.target.value = '';
                         },
                         removeImage(i) {
                             this.previewUrls.splice(i, 1);
                             this.imageFiles.splice(i, 1);
                             if (this.carouselIdx >= this.previewUrls.length) {
                                 this.carouselIdx = Math.max(0, this.previewUrls.length - 1);
                             }
                             const dt = new DataTransfer();
                             this.imageFiles.forEach(f => dt.items.add(f));
                             this.$refs.fileInput.files = dt.files;
                             this.$refs.fileInput.dispatchEvent(new Event('change', { bubbles: true }));
                         },
                         clearImages() {
                             this.previewUrls = [];
                             this.imageFiles = [];
                             this.carouselIdx = 0;
                             const dt = new DataTransfer();
                             this.$refs.fileInput.files = dt.files;
                         },
                         prevSlide() { this.carouselIdx = (this.carouselIdx - 1 + this.previewUrls.length) % this.previewUrls.length; },
                         nextSlide() { this.carouselIdx = (this.carouselIdx + 1) % this.previewUrls.length; },
                         mentionOpen: false,
                         mentionUsers: [],
                         mentionStart: -1,
                         async handleInput(e) {
                             const val = e.target.value;
                             const pos = e.target.selectionStart;
                             const before = val.substring(0, pos);
                             const atIdx = before.lastIndexOf('@');
                             if (atIdx !== -1 && (atIdx === 0 || /[\s,]/.test(before[atIdx - 1]))) {
                                 const query = before.substring(atIdx + 1);
                                 if (!query.includes(' ') && query.length >= 1 && query.length <= 20) {
                                     this.mentionStart = atIdx;
                                     this.mentionUsers = await $wire.searchMentions(query);
                                     this.mentionOpen = this.mentionUsers.length > 0;
                                     return;
                                 }
                             }
                             this.mentionOpen = false;
                         },
                         insertMention(user, textarea) {
                             const val = textarea.value;
                             const before = val.substring(0, this.mentionStart);
                             const after = val.substring(textarea.selectionStart);
                             const newVal = before + '@' + user.first + ' ' + after;
                             textarea.value = newVal;
                             $wire.set('newPostContent', newVal);
                             this.mentionOpen = false;
                             this.mentionUsers = [];
                             textarea.focus();
                         }
                     }">

                    <div class="flex items-start gap-3">
                        <span class="w-10 h-10 rounded-full {{ $meBg }} text-white text-sm lato-bold flex items-center justify-center shrink-0 mt-0.5">{{ $meInit }}</span>
                        <div class="flex-1 relative">
                            <textarea
                                x-ref="postTextarea"
                                wire:model="newPostContent"
                                @focus="expanded = true"
                                @input="handleInput($event)"
                                @keydown.escape="mentionOpen = false"
                                placeholder="Compartilhe algo com a equipe... use @nome para mencionar"
                                x-bind:rows="expanded ? 4 : 1"
                                class="w-full px-4 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                       bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                       focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400
                                       placeholder-slate-400 resize-none transition-all duration-200"
                                maxlength="2000"></textarea>

                            {{-- Dropdown de @menção --}}
                            <div x-show="mentionOpen" x-transition @click.away="mentionOpen = false"
                                 class="absolute left-0 top-full mt-1 w-56 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-xl z-30 py-1 overflow-hidden">
                                <template x-for="user in mentionUsers" :key="user.id">
                                    <button type="button" @click="insertMention(user, $refs.postTextarea)"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition text-left">
                                        <span class="w-7 h-7 rounded-full bg-indigo-500 text-white text-[10px] lato-bold flex items-center justify-center shrink-0"
                                              x-text="user.initials"></span>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 truncate" x-text="user.name"></p>
                                        </div>
                                    </button>
                                </template>
                            </div>

                            {{-- Preview de imagens --}}
                            <template x-if="expanded && previewUrls.length > 0">
                                <div class="mt-3">
                                    {{-- Carrossel --}}
                                    <div class="relative rounded-2xl overflow-hidden bg-slate-100 dark:bg-slate-900 aspect-video group">
                                        {{-- Slides --}}
                                        <template x-for="(url, i) in previewUrls" :key="i">
                                            <div x-show="carouselIdx === i"
                                                 x-transition:enter="transition ease-out duration-200"
                                                 x-transition:enter-start="opacity-0 scale-[0.98]"
                                                 x-transition:enter-end="opacity-100 scale-100"
                                                 class="absolute inset-0">
                                                <img :src="url" class="w-full h-full object-cover" />
                                                {{-- Botão remover --}}
                                                <button @click.stop="removeImage(i)" type="button"
                                                        class="cursor-pointer absolute top-2 right-2 w-7 h-7 rounded-full bg-black/60 hover:bg-red-600 text-white flex items-center justify-center transition-colors shadow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                                                </button>
                                                {{-- Contador --}}
                                                <div class="absolute top-2 left-2 px-2 py-0.5 rounded-full bg-black/50 text-white text-[11px] lato-bold"
                                                     x-show="previewUrls.length > 1"
                                                     x-text="(i + 1) + ' / ' + previewUrls.length"></div>
                                            </div>
                                        </template>

                                        {{-- Setas de navegação (só com mais de 1) --}}
                                        <template x-if="previewUrls.length > 1">
                                            <div>
                                                <button @click="prevSlide()" type="button"
                                                        class="cursor-pointer absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                                                </button>
                                                <button @click="nextSlide()" type="button"
                                                        class="cursor-pointer absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 rounded-full bg-black/50 hover:bg-black/70 text-white flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity shadow">
                                                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                                                </button>
                                            </div>
                                        </template>
                                    </div>

                                    {{-- Dots indicadores --}}
                                    <template x-if="previewUrls.length > 1">
                                        <div class="flex items-center justify-center gap-1.5 mt-2">
                                            <template x-for="(url, i) in previewUrls" :key="i">
                                                <button @click="carouselIdx = i" type="button"
                                                        :class="carouselIdx === i ? 'w-4 bg-blue-500' : 'w-2 bg-slate-300 dark:bg-slate-600 hover:bg-slate-400'"
                                                        class="cursor-pointer h-2 rounded-full transition-all duration-200"></button>
                                            </template>
                                        </div>
                                    </template>
                                </div>
                            </template>

                            {{-- Criador de enquete --}}
                            <div x-show="showPoll" x-transition class="mt-3 p-3 rounded-xl bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-700 space-y-2">
                                <p class="text-xs lato-bold text-blue-700 dark:text-blue-300 flex items-center gap-1.5">
                                    <x-lucide-bar-chart-2 class="w-3.5 h-3.5" /> Enquete
                                </p>
                                <input wire:model="pollQuestion" type="text" placeholder="Qual é a pergunta?" maxlength="200"
                                       class="w-full px-3 py-2 text-sm border border-blue-300 dark:border-blue-600 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30" />
                                @foreach ($pollOptions as $i => $opt)
                                    <div class="flex items-center gap-2">
                                        <input wire:model="pollOptions.{{ $i }}" type="text" placeholder="Opção {{ $i + 1 }}" maxlength="100"
                                               class="flex-1 px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-lg bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30" />
                                        @if (count($pollOptions) > 2)
                                            <button wire:click="removePollOption({{ $i }})" type="button" class="p-1 text-slate-400 hover:text-red-500 transition">
                                                <x-lucide-x class="w-4 h-4" />
                                            </button>
                                        @endif
                                    </div>
                                @endforeach
                                @if (count($pollOptions) < 6)
                                    <button wire:click="addPollOption" type="button" class="text-xs text-blue-600 dark:text-blue-400 hover:underline">+ Adicionar opção</button>
                                @endif
                                <div class="flex items-center gap-2 text-xs text-slate-500">
                                    <span>Duração:</span>
                                    <select wire:model="pollDuration" class="px-2 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200 text-xs">
                                        <option value="1">1 dia</option>
                                        <option value="3">3 dias</option>
                                        <option value="7" selected>7 dias</option>
                                        <option value="14">14 dias</option>
                                        <option value="30">30 dias</option>
                                        <option value="0">Sem expiração</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Toolbar --}}
                    <div x-show="expanded" x-transition class="flex items-center justify-between border-t border-slate-100 dark:border-slate-700 pt-3 mt-3">
                        <div class="flex items-center gap-1">
                            <label class="p-2 rounded-xl text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition cursor-pointer" title="Adicionar fotos">
                                <x-lucide-image class="w-4 h-4" />
                                <input type="file" class="hidden" x-ref="fileInput" wire:model="newPostImages" accept="image/*" multiple
                                       @change="addFiles($event)" />
                            </label>
                            <button type="button" @click="showPoll = !showPoll"
                                    class="p-2 rounded-xl text-slate-400 hover:text-green-500 hover:bg-green-50 dark:hover:bg-green-900/20 transition" title="Enquete">
                                <x-lucide-bar-chart-2 class="w-4 h-4" />
                            </button>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 lato-regular">{{ strlen($newPostContent) }}/2000</span>
                            <button wire:click="createPost" @click="clearImages(); expanded = false; showPoll = false;" type="button" wire:loading.attr="disabled"
                                    class="px-5 py-2 text-xs lato-bold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                                           hover:from-blue-600 hover:to-indigo-700 disabled:opacity-50 transition shadow-sm">
                                <span wire:loading wire:target="createPost">
                                    <x-lucide-loader-2 class="w-4 h-4 animate-spin" />
                                </span>

                                 <span wire:loading.remove wire:target="createPost" class="flex items-center gap-1.5">
                                        <x-lucide-circle-check class="w-4 h-4" />
                                        Confirmar
                                </span>
                            </button>
                        </div>
                    </div>
                    @error('newPostContent') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    @error('newPostImages.*') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- ── Filtros + Busca + Sort ──────────────────────── --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-3 space-y-2.5">
                    {{-- Tabs de filtro --}}
                    <div class="flex items-center gap-1 overflow-x-auto pb-0.5 scrollbar-hide">
                        @php
                            $tabs = [
                                ['all',         'Tudo',        'layout-list'],
                                ['official',    'Oficiais',    'megaphone'],
                                ['users',       'Colegas',     'users'],
                                ['following',   'Seguindo',    'user-check'],
                                ['communities', 'Comunidades', 'users-round'],
                                ['bookmarks',   'Salvos',      'bookmark'],
                            ];
                            $unread = $this->unreadOfficialCount;
                        @endphp
                        @foreach ($tabs as [$val, $label, $icon])
                            <button wire:click="setFilter('{{ $val }}')" type="button"
                                    class="relative flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs lato-bold whitespace-nowrap transition
                                           {{ $activeFilter === $val
                                               ? 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white shadow-sm'
                                               : 'text-slate-500 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700' }}">
                                <x-dynamic-component :component="'lucide-'.$icon" class="w-3.5 h-3.5" />
                                {{ $label }}
                                {{-- Badge de não lidas na tab Oficiais --}}
                                @if ($val === 'official' && $unread > 0)
                                    <span class="ml-0.5 inline-flex items-center justify-center min-w-[16px] h-4 px-1 rounded-full text-[10px] lato-bold leading-none
                                                 {{ $activeFilter === 'official' ? 'bg-white/30 text-white' : 'bg-red-500 text-white' }}">
                                        {{ $unread > 99 ? '99+' : $unread }}
                                    </span>
                                @endif
                            </button>
                        @endforeach
                    </div>
                    {{-- Busca + Sort --}}
                    <div class="flex items-center gap-2">
                        <div class="relative flex-1">
                            <x-lucide-search class="absolute left-3 top-1/2 -translate-y-1/2 w-3.5 h-3.5 text-slate-400 pointer-events-none" />
                            <input wire:model.live.debounce.400ms="searchQuery" type="search"
                                   placeholder="Buscar no feed…"
                                   class="w-full pl-9 pr-4 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400 placeholder-slate-400" />
                        </div>
                        <select wire:model.live="sortBy"
                                class="px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-xl bg-white dark:bg-slate-800
                                       text-slate-600 dark:text-slate-300 focus:outline-none focus:ring-2 focus:ring-blue-400/30">
                            <option value="recent">Recentes</option>
                            <option value="popular">Populares</option>
                        </select>
                    </div>
                </div>

                {{-- ── Skeleton loading ────────────────────────────── --}}
                <div wire:loading wire:target="setFilter,setSortBy,updatedSearchQuery,mount" class="w-full space-y-3" style="display:none">
                    @for ($sk = 0; $sk < 3; $sk++)
                        @include('livewire.pages.feed._skeleton')
                    @endfor
                </div>

                {{-- ── Feed ───────────────────────────────────────────── --}}
                <div wire:loading.remove wire:target="setFilter,setSortBy,updatedSearchQuery,mount" class="space-y-3">
                @forelse ($this->feedItems as $item)
                    @php
                        $key  = $item['key'];
                        $type = $item['type'];
                        $post = $item['post'];
                    @endphp

                    {{-- ╔═══════════════════════════════════════════════╗
                         ║  POST OFICIAL                                 ║
                         ╚═══════════════════════════════════════════════╝ --}}
                    @if ($type === 'official')
                        @php
                            $authorName = $post->author->name ?? 'Redação';
                            $myRx       = $post->reactions->first()?->type;
                            $hasRead    = $post->reads->where('user_id', Auth::id())->isNotEmpty();
                        @endphp
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden group"
                             data-feed-post-id="{{ $post->id }}"
                             x-data="{
                                 rx: '{{ $myRx }}',
                                 lk: {{ $post->like_count }}, ht: {{ $post->heart_count }}, cl: {{ $post->clap_count }},
                                 bump(type) {
                                     const old = this.rx;
                                     if (old === type) {
                                         this.rx = null;
                                         if(type==='like') this.lk--; else if(type==='heart') this.ht--; else this.cl--;
                                     } else {
                                         if(old==='like') this.lk--; else if(old==='heart') this.ht--; else if(old==='clap') this.cl--;
                                         this.rx = type;
                                         if(type==='like') this.lk++; else if(type==='heart') this.ht++; else this.cl++;
                                     }
                                     $wire.toggleReaction('{{ $key }}', type);
                                 }
                             }">

                            {{-- Tag oficial --}}
                            <div class="px-4 pt-3 pb-0 flex items-center gap-2">
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] lato-bold
                                             bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300">
                                    <x-lucide-megaphone class="w-2.5 h-2.5" /> Publicação Oficial
                                </span>
                                @if ($post->mandatory)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] lato-bold
                                                 bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                                        <x-lucide-alert-circle class="w-2.5 h-2.5" /> Leitura obrigatória
                                    </span>
                                @endif
                            </div>

                            <div class="p-4">
                                {{-- Header --}}
                                <div class="flex items-start gap-3 mb-3">
                                    <span class="w-10 h-10 rounded-full {{ feedBg($authorName) }} text-white text-sm lato-bold flex items-center justify-center shrink-0">
                                        {{ feedInitials($authorName) }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $authorName }}</p>
                                        <p class="text-xs text-slate-400 lato-regular">
                                            {{ $post->published_at?->diffForHumans() ?? '' }}
                                            @if ($post->tags->isNotEmpty())
                                                · @foreach($post->tags as $tag) <span class="text-blue-500">#{{ $tag->name }}</span> @endforeach
                                            @endif
                                        </p>
                                    </div>
                                    @if ($hasRead)
                                        <span class="shrink-0 text-[10px] flex items-center gap-1 text-green-600 dark:text-green-400">
                                            <x-lucide-check-circle class="w-3 h-3" /> Lido
                                        </span>
                                    @endif
                                </div>

                                {{-- Conteúdo --}}
                                <button wire:click="openPreview({{ $post->id }})" type="button" class="w-full text-left group/title mb-3">
                                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 group-hover/title:text-blue-600 dark:group-hover/title:text-blue-400 transition mb-1 leading-snug">
                                        {{ $post->title }}
                                    </h3>
                                    <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular line-clamp-3 leading-relaxed">
                                        {!! nl2br(e(Str::limit(strip_tags($post->content), 280))) !!}
                                    </p>
                                </button>

                                {{-- Imagem se houver --}}
                                @if ($post->cover_image)
                                    <div class="rounded-xl overflow-hidden mb-3 bg-slate-100 dark:bg-slate-900 cursor-zoom-in"
                                         @click="$dispatch('open-lightbox', { src: '{{ Storage::url($post->cover_image) }}', alt: '{{ addslashes($post->title) }}' })">
                                        <img src="{{ Storage::url($post->cover_image) }}" alt="{{ $post->title }}"
                                             class="w-full max-h-72 object-cover object-top pointer-events-none" />
                                    </div>
                                @endif

                                {{-- Reação + Comentário bar --}}
                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-1">
                                        {{-- Like --}}
                                        <button type="button" @click="bump('like')"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition group/rx"
                                                x-bind:class="rx==='like' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                 x-bind:fill="rx==='like' ? 'currentColor' : 'none'">
                                                <path d="M7 10v12"/><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/>
                                            </svg>
                                            <span x-text="lk > 0 ? lk : ''"></span>
                                        </button>
                                        {{-- Heart --}}
                                        <button type="button" @click="bump('heart')"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition"
                                                x-bind:class="rx==='heart' ? 'bg-red-100 dark:bg-red-900/30 text-red-500' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                 x-bind:fill="rx==='heart' ? 'currentColor' : 'none'" x-bind:stroke="rx==='heart' ? 'none' : 'currentColor'">
                                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                                            </svg>
                                            <span x-text="ht > 0 ? ht : ''"></span>
                                        </button>
                                        {{-- Clap --}}
                                        <button type="button" @click="bump('clap')"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition"
                                                x-bind:class="rx==='clap' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                 x-bind:fill="rx==='clap' ? 'currentColor' : 'none'">
                                                <path d="M18 8.5a4.5 4.5 0 0 0-4.5-4.5 4.5 4.5 0 0 0-4.5 4.5v4.5a4.5 4.5 0 0 0 9 0V8.5z"/><path d="M12 4V2M8 5l-1-2M16 5l1-2"/>
                                            </svg>
                                            <span x-text="cl > 0 ? cl : ''"></span>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        <button wire:click="toggleComments('{{ $key }}')" type="button"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition lato-regular">
                                            <x-lucide-message-circle class="w-4 h-4" />
                                            {{ $post->comments_count > 0 ? $post->comments_count : '' }}
                                        </button>
                                        <button wire:click="openPreview({{ $post->id }})" type="button"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition lato-bold">
                                            <x-lucide-eye class="w-4 h-4" /> Ler
                                        </button>
                                    </div>
                                </div>

                                {{-- Comentários secção --}}
                                @if (in_array($key, $openComments))
                                    @include('livewire.pages.feed._comments', ['key' => $key, 'comments' => $post->comments, 'postType' => 'official'])
                                @endif
                            </div>
                        </div>

                    {{-- ╔═══════════════════════════════════════════════╗
                         ║  POST DE COMUNIDADE                           ║
                         ╚═══════════════════════════════════════════════╝ --}}
                    @elseif ($type === 'community')
                        @php
                            $author     = $post->user;
                            $authorName = $author->name ?? '?';
                            $community  = $post->community;
                            $myRx       = $post->reactions->first()?->type;
                        @endphp
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden" data-feed-post-id="{{ $post->id }}"
                             x-data="{
                                 rx: '{{ $myRx }}',
                                 lk: {{ $post->like_count }}, ht: {{ $post->heart_count }}, cl: {{ $post->clap_count }},
                                 bump(type) {
                                     const old = this.rx;
                                     if (old === type) {
                                         this.rx = null;
                                         if(type==='like') this.lk--; else if(type==='heart') this.ht--; else this.cl--;
                                     } else {
                                         if(old==='like') this.lk--; else if(old==='heart') this.ht--; else if(old==='clap') this.cl--;
                                         this.rx = type;
                                         if(type==='like') this.lk++; else if(type==='heart') this.ht++; else this.cl++;
                                     }
                                     $wire.toggleReaction('{{ $key }}', type);
                                 }
                             }">

                            {{-- Badge comunidade --}}
                            <div class="px-4 pt-3 pb-0 flex items-center gap-2">
                                <a href="{{ route('communities.show', $community->slug) }}"
                                   class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full text-[10px] lato-bold
                                          bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-400
                                          hover:bg-indigo-200 dark:hover:bg-indigo-900/50 transition">
                                    <span class="w-3.5 h-3.5 rounded-sm {{ $community->avatar_color ?? 'bg-indigo-500' }} text-white text-[8px] flex items-center justify-center font-bold leading-none">
                                        {{ substr($community->initials ?? '?', 0, 1) }}
                                    </span>
                                    {{ $community->name }}
                                </a>
                                @if ($post->is_pinned)
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] lato-bold bg-amber-100 dark:bg-amber-900/30 text-amber-600 dark:text-amber-400">
                                        <x-lucide-pin class="w-2.5 h-2.5" /> Fixado
                                    </span>
                                @endif
                            </div>

                            <div class="p-4">
                                {{-- Header --}}
                                <div class="flex items-start gap-3 mb-3">
                                    <span class="w-10 h-10 rounded-full {{ feedBg($authorName) }} text-white text-sm lato-bold flex items-center justify-center shrink-0">
                                        {{ feedInitials($authorName) }}
                                    </span>
                                    <div class="flex-1 min-w-0">
                                        <p class="text-sm lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $authorName }}</p>
                                        <p class="text-xs text-slate-400 lato-regular">
                                            {{ $post->created_at->diffForHumans() }}
                                            @if ($post->edited_at) · <span class="italic">editado</span> @endif
                                        </p>
                                    </div>
                                    <a href="{{ route('communities.show', $community->slug) }}"
                                       class="shrink-0 flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-[10px] lato-bold
                                              text-indigo-500 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition">
                                        <x-lucide-arrow-right class="w-3 h-3" /> Ver na comunidade
                                    </a>
                                </div>

                                {{-- Conteúdo --}}
                                @if ($post->content)
                                    <div class="text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed mb-3">
                                        {!! renderFeedContent(\Illuminate\Support\Str::limit($post->content, 300)) !!}
                                    </div>
                                @endif

                                {{-- Imagens (carrossel) --}}
                                @if ($post->images->isNotEmpty())
                                    @include('livewire.pages.feed._carousel', ['imgs' => $post->images, 'lightbox' => false])
                                @endif

                                {{-- Enquete --}}
                                @if ($post->poll)
                                    @php
                                        $poll       = $post->poll;
                                        $totalVotes = $poll->options->sum(fn($o) => $o->votes->count());
                                        $myVoteIds  = $poll->options->filter(fn($o) => $o->votes->contains('user_id', Auth::id()))->pluck('id')->toArray();
                                        $pollClosed = $poll->is_expired;
                                    @endphp
                                    <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-3 mb-3 space-y-2">
                                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $poll->question }}</p>
                                        @foreach ($poll->options as $opt)
                                            @php $votes = $opt->votes->count(); $pct = $totalVotes > 0 ? round($votes / $totalVotes * 100) : 0; $voted = in_array($opt->id, $myVoteIds); @endphp
                                            <div class="relative">
                                                <div class="h-8 rounded-lg {{ $voted ? 'bg-indigo-100 dark:bg-indigo-900/30 border border-indigo-300 dark:border-indigo-600' : 'bg-slate-100 dark:bg-slate-700' }} overflow-hidden">
                                                    <div class="h-full bg-indigo-200 dark:bg-indigo-800/60 transition-all" style="width: {{ $pct }}%"></div>
                                                </div>
                                                <div class="absolute inset-0 flex items-center justify-between px-3">
                                                    <span class="text-xs {{ $voted ? 'lato-bold text-indigo-700 dark:text-indigo-300' : 'lato-regular text-slate-600 dark:text-slate-300' }}">{{ $opt->label }}</span>
                                                    <span class="text-xs text-slate-500 lato-bold">{{ $pct }}%</span>
                                                </div>
                                            </div>
                                        @endforeach
                                        <p class="text-xs text-slate-400 lato-regular">{{ $totalVotes }} {{ $totalVotes === 1 ? 'voto' : 'votos' }}</p>
                                    </div>
                                @endif

                                {{-- Action bar --}}
                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-1">
                                        <button type="button" @click="bump('like')"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition"
                                                x-bind:class="rx==='like' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x-bind:fill="rx==='like' ? 'currentColor' : 'none'">
                                                <path d="M7 10v12"/><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/>
                                            </svg>
                                            <span x-text="lk > 0 ? lk : ''"></span>
                                        </button>
                                        <button type="button" @click="bump('heart')"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition"
                                                x-bind:class="rx==='heart' ? 'bg-red-100 dark:bg-red-900/30 text-red-500' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" x-bind:fill="rx==='heart' ? 'currentColor' : 'none'" x-bind:stroke="rx==='heart' ? 'none' : 'currentColor'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                                            </svg>
                                            <span x-text="ht > 0 ? ht : ''"></span>
                                        </button>
                                        <button type="button" @click="bump('clap')"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition"
                                                x-bind:class="rx==='clap' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" x-bind:fill="rx==='clap' ? 'currentColor' : 'none'">
                                                <path d="M18 8.5a4.5 4.5 0 0 0-4.5-4.5 4.5 4.5 0 0 0-4.5 4.5v4.5a4.5 4.5 0 0 0 9 0V8.5z"/><path d="M12 4V2M8 5l-1-2M16 5l1-2"/>
                                            </svg>
                                            <span x-text="cl > 0 ? cl : ''"></span>
                                        </button>
                                    </div>
                                    <button wire:click="toggleComments('{{ $key }}')" type="button"
                                            class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                        <x-lucide-message-circle class="w-4 h-4" />
                                        {{ $post->comments_count > 0 ? $post->comments_count : '' }}
                                    </button>
                                </div>

                                {{-- Comentários --}}
                                @if (in_array($key, $openComments))
                                    @include('livewire.pages.feed._comments', ['key' => $key, 'comments' => $post->comments, 'postType' => 'community'])
                                @endif
                            </div>
                        </div>

                    {{-- ╔═══════════════════════════════════════════════╗
                         ║  POST SOCIAL DO UTILIZADOR                    ║
                         ╚═══════════════════════════════════════════════╝ --}}
                    @else
                        @php
                            $author     = $post->user;
                            $authorName = $author->name ?? '?';
                            $myRx       = $post->reactions->first()?->type;
                            $isBookmarked = $post->bookmarks->isNotEmpty();
                            $isMyPost   = $author->id === Auth::id();
                            $isRepost   = $post->is_repost;
                            $original   = $post->repostedFrom;
                        @endphp
                        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden" data-feed-post-id="{{ $post->id }}"
                             x-data="{
                                 rx: '{{ $myRx }}',
                                 lk: {{ $post->like_count }}, ht: {{ $post->heart_count }}, cl: {{ $post->clap_count }},
                                 bm: {{ $isBookmarked ? 'true' : 'false' }},
                                 bump(type) {
                                     const old = this.rx;
                                     if (old === type) {
                                         this.rx = null;
                                         if(type==='like') this.lk--; else if(type==='heart') this.ht--; else this.cl--;
                                     } else {
                                         if(old==='like') this.lk--; else if(old==='heart') this.ht--; else if(old==='clap') this.cl--;
                                         this.rx = type;
                                         if(type==='like') this.lk++; else if(type==='heart') this.ht++; else this.cl++;
                                     }
                                     $wire.toggleReaction('{{ $key }}', type);
                                 },
                                 toggleBm() { this.bm = !this.bm; $wire.toggleBookmark({{ $post->id }}); }
                             }">

                            {{-- Indicador de repost --}}
                            @if ($isRepost && $original)
                                <div class="px-4 pt-3 pb-0 flex items-center gap-1.5 text-xs text-slate-500 dark:text-slate-400">
                                    <x-lucide-repeat-2 class="w-3.5 h-3.5 text-green-500" />
                                    <span class="lato-bold">{{ $authorName }}</span> repostou
                                </div>
                            @endif

                            <div class="p-4">
                                {{-- Header --}}
                                <div class="flex items-start gap-3 mb-3">
                                    <a href="{{ route('feed.profile', $author->id) }}" class="shrink-0">
                                        <span class="w-10 h-10 rounded-full {{ feedBg($authorName) }} text-white text-sm lato-bold flex items-center justify-center hover:ring-2 hover:ring-blue-400 transition">
                                            {{ feedInitials($authorName) }}
                                        </span>
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <div class="flex items-center gap-2 flex-wrap">
                                            <a href="{{ route('feed.profile', $author->id) }}" class="text-sm lato-bold text-slate-800 dark:text-slate-100 hover:text-blue-600 dark:hover:text-blue-400 transition truncate">
                                                {{ $authorName }}
                                            </a>
                                            @if ($author->accessProfile)
                                                <span class="text-[10px] px-1.5 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 lato-regular">
                                                    {{ $author->accessProfile->name }}
                                                </span>
                                            @endif
                                        </div>
                                        <p class="text-xs text-slate-400 lato-regular flex items-center gap-1.5">
                                            {{ $post->created_at->diffForHumans() }}
                                            @if ($post->edited_at)
                                                · <span class="italic">editado</span>
                                            @endif
                                            · <x-lucide-eye class="w-3 h-3 inline" /> {{ $post->views_count ?? 0 }}
                                        </p>
                                    </div>
                                    {{-- Menu 3 pontos --}}
                                    <div class="relative shrink-0" x-data="{ open: false }" @click.away="open=false">
                                        <button @click="open=!open" type="button"
                                                class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                            <x-lucide-more-horizontal class="w-4 h-4" />
                                        </button>
                                        <div x-show="open" x-transition class="absolute right-0 mt-1 w-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg py-1 z-20">
                                            <button @click="toggleBm(); open=false" type="button"
                                                    class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                                                <x-lucide-bookmark class="w-3.5 h-3.5" />
                                                <span x-text="bm ? 'Remover dos salvos' : 'Salvar post'"></span>
                                            </button>
                                            @if (! $isMyPost)
                                                <button wire:click="openRepostModal({{ $post->id }})" @click="open=false" type="button"
                                                        class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                                                    <x-lucide-repeat-2 class="w-3.5 h-3.5" /> Repostar
                                                </button>
                                                <button wire:click="toggleFollow({{ $author->id }})" @click="open=false" type="button"
                                                        class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                                                    <x-lucide-user-plus class="w-3.5 h-3.5" />
                                                    {{ in_array($author->id, $this->myFollowingIds) ? 'Deixar de seguir' : 'Seguir' }}
                                                </button>
                                            @else
                                                <button wire:click="openEditModal({{ $post->id }})" @click="open=false" type="button"
                                                        class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2">
                                                    <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                                                </button>
                                                <button wire:click="confirmDeleteFeedPost({{ $post->id }})" @click="open=false" type="button"
                                                        class="w-full text-left px-3 py-2 text-xs text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 disabled:opacity-60"
                                                    wire:loading.attr="disabled">
                                                    <span wire:loading.remove wire:target="confirmDeleteFeedPost" class="flex items-center gap-1.5">
                                                        <x-lucide-trash-2 class="w-3.5 h-3.5" /> Excluir
                                                    </span>
                                                    <span wire:loading wire:target="confirmDeleteFeedPost" class="flex items-center gap-1.5">
                                                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                                    </span>
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                </div>

                                {{-- Conteúdo do post --}}
                                @if ($post->content)
                                    <div class="text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed mb-3">
                                        {!! renderFeedContent($post->content) !!}
                                    </div>
                                @endif

                                {{-- Imagens (carrossel) --}}
                                @if ($post->images->isNotEmpty())
                                    @include('livewire.pages.feed._carousel', ['imgs' => $post->images, 'lightbox' => true])
                                @elseif ($post->image)
                                    {{-- Imagem legacy (campo único) --}}
                                    <div class="rounded-2xl overflow-hidden mb-3 bg-slate-100 dark:bg-slate-900 cursor-zoom-in"
                                         @click="$dispatch('open-lightbox', { src: '{{ Storage::url($post->image) }}', alt: '{{ addslashes($authorName) }}' })">
                                        <img src="{{ Storage::url($post->image) }}"
                                             class="w-full max-h-80 object-contain pointer-events-none" />
                                    </div>
                                @endif

                                {{-- Repost: card do original --}}
                                @if ($isRepost && $original)
                                    <div class="border border-slate-200 dark:border-slate-600 rounded-xl overflow-hidden mb-3 bg-slate-50 dark:bg-slate-900/50">
                                        <div class="p-3">
                                            @php $origName = $original->user->name ?? '?'; @endphp
                                            <div class="flex items-center gap-2 mb-1.5">
                                                <span class="w-6 h-6 rounded-full {{ feedBg($origName) }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0">
                                                    {{ feedInitials($origName) }}
                                                </span>
                                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $origName }}</span>
                                                <span class="text-xs text-slate-400">· {{ $original->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if ($original->content)
                                                <p class="text-xs text-slate-600 dark:text-slate-300 line-clamp-3 leading-relaxed">
                                                    {{ Str::limit($original->content, 200) }}
                                                </p>
                                            @endif
                                        </div>
                                        {{-- Imagens do post original --}}
                                        @if ($original->images?->isNotEmpty())
                                            @php $origImgs = $original->images; $origCount = $origImgs->count(); @endphp
                                            <div class="{{ $origCount > 1 ? 'grid grid-cols-2 gap-0.5' : '' }}">
                                                @foreach ($origImgs->take(4) as $oi => $origImg)
                                                    <div class="relative bg-slate-100 dark:bg-slate-800 {{ $origCount === 1 ? '' : 'aspect-square' }} cursor-zoom-in"
                                                         @click="$dispatch('open-lightbox', { src: '{{ Storage::url($origImg->path) }}', alt: '{{ addslashes($origName) }}' })">
                                                        <img src="{{ Storage::url($origImg->path) }}"
                                                             class="w-full h-full {{ $origCount === 1 ? 'max-h-60 object-cover' : 'object-cover' }} pointer-events-none" />
                                                        @if ($oi === 3 && $origCount > 4)
                                                            <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-lg lato-bold">
                                                                +{{ $origCount - 4 }}
                                                            </div>
                                                        @endif
                                                    </div>
                                                @endforeach
                                            </div>
                                        @elseif ($original->image)
                                            <div class="cursor-zoom-in"
                                                 @click="$dispatch('open-lightbox', { src: '{{ Storage::url($original->image) }}', alt: '{{ addslashes($origName) }}' })">
                                                <img src="{{ Storage::url($original->image) }}"
                                                     class="w-full max-h-60 object-cover pointer-events-none" />
                                            </div>
                                        @endif
                                    </div>
                                @endif

                                {{-- Enquete --}}
                                @if ($post->poll)
                                    @php
                                        $poll = $post->poll;
                                        $totalVotes = $poll->options->sum(fn($o) => $o->votes->count());
                                        $myVoteIds  = $poll->options->filter(fn($o) => $o->votes->contains('user_id', Auth::id()))->pluck('id')->toArray();
                                        $pollClosed = $poll->is_expired;
                                    @endphp
                                    <div class="border border-slate-200 dark:border-slate-700 rounded-xl p-3 mb-3 space-y-2">
                                        <p class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $poll->question }}</p>
                                        @if ($pollClosed || $totalVotes > 0 && count($myVoteIds) > 0)
                                            {{-- Mostrar resultados --}}
                                            @foreach ($poll->options as $opt)
                                                @php
                                                    $votes = $opt->votes->count();
                                                    $pct   = $totalVotes > 0 ? round($votes / $totalVotes * 100) : 0;
                                                    $voted = in_array($opt->id, $myVoteIds);
                                                @endphp
                                                <div class="relative">
                                                    <div class="h-8 rounded-lg {{ $voted ? 'bg-blue-100 dark:bg-blue-900/30 border border-blue-300 dark:border-blue-600' : 'bg-slate-100 dark:bg-slate-700' }} overflow-hidden">
                                                        <div class="h-full bg-blue-200 dark:bg-blue-800/60 transition-all duration-500"
                                                             style="width: {{ $pct }}%"></div>
                                                    </div>
                                                    <div class="absolute inset-0 flex items-center justify-between px-3">
                                                        <span class="text-xs {{ $voted ? 'lato-bold text-blue-700 dark:text-blue-300' : 'lato-regular text-slate-600 dark:text-slate-300' }}">
                                                            {{ $opt->label }}
                                                            @if ($voted) <x-lucide-check class="w-3 h-3 inline" /> @endif
                                                        </span>
                                                        <span class="text-xs text-slate-500 lato-bold">{{ $pct }}%</span>
                                                    </div>
                                                </div>
                                            @endforeach
                                        @else
                                            {{-- Votar --}}
                                            @foreach ($poll->options as $opt)
                                                <button wire:click="votePoll({{ $opt->id }})" type="button"
                                                        class="w-full text-left px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-700
                                                               hover:border-blue-400 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition lato-regular
                                                               text-slate-700 dark:text-slate-200">
                                                    {{ $opt->label }}
                                                </button>
                                            @endforeach
                                        @endif
                                        <p class="text-xs text-slate-400 lato-regular">
                                            {{ $totalVotes }} {{ $totalVotes === 1 ? 'voto' : 'votos' }}
                                            @if ($poll->ends_at)
                                                · {{ $pollClosed ? 'Enquete encerrada' : 'Encerra '.$poll->ends_at->diffForHumans() }}
                                            @endif
                                        </p>
                                    </div>
                                @endif

                                {{-- Reação + Comentário + Repost bar --}}
                                <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center gap-1">
                                        {{-- Like --}}
                                        <button type="button" @click="bump('like')"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition"
                                                x-bind:class="rx==='like' ? 'bg-blue-100 dark:bg-blue-900/30 text-blue-600' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                 x-bind:fill="rx==='like' ? 'currentColor' : 'none'">
                                                <path d="M7 10v12"/><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/>
                                            </svg>
                                            <span x-text="lk > 0 ? lk : ''"></span>
                                        </button>
                                        {{-- Heart --}}
                                        <button type="button" @click="bump('heart')"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition"
                                                x-bind:class="rx==='heart' ? 'bg-red-100 dark:bg-red-900/30 text-red-500' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                                                 x-bind:fill="rx==='heart' ? 'currentColor' : 'none'" x-bind:stroke="rx==='heart' ? 'none' : 'currentColor'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                                <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                                            </svg>
                                            <span x-text="ht > 0 ? ht : ''"></span>
                                        </button>
                                        {{-- Clap --}}
                                        <button type="button" @click="bump('clap')"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition"
                                                x-bind:class="rx==='clap' ? 'bg-yellow-100 dark:bg-yellow-900/30 text-yellow-600' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                 x-bind:fill="rx==='clap' ? 'currentColor' : 'none'">
                                                <path d="M18 8.5a4.5 4.5 0 0 0-4.5-4.5 4.5 4.5 0 0 0-4.5 4.5v4.5a4.5 4.5 0 0 0 9 0V8.5z"/><path d="M12 4V2M8 5l-1-2M16 5l1-2"/>
                                            </svg>
                                            <span x-text="cl > 0 ? cl : ''"></span>
                                        </button>
                                    </div>
                                    <div class="flex items-center gap-1">
                                        {{-- Comentar --}}
                                        <button wire:click="toggleComments('{{ $key }}')" type="button"
                                                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                                            <x-lucide-message-circle class="w-4 h-4" />
                                            {{ $post->comments_count > 0 ? $post->comments_count : '' }}
                                        </button>
                                        {{-- Repost --}}
                                        @if (! $isMyPost)
                                            <button wire:click="openRepostModal({{ $post->id }})" type="button"
                                                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs text-slate-500 hover:bg-green-50 dark:hover:bg-green-900/20 hover:text-green-600 transition">
                                                <x-lucide-repeat-2 class="w-4 h-4" />
                                                {{ $post->reposts_count > 0 ? $post->reposts_count : '' }}
                                            </button>
                                        @endif
                                        {{-- Bookmark --}}
                                        <button @click="toggleBm()" type="button"
                                                class="px-2.5 py-1.5 rounded-xl text-xs transition"
                                                x-bind:class="bm ? 'text-blue-600 dark:text-blue-400' : 'text-slate-400 hover:text-slate-600'">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                                                 x-bind:fill="bm ? 'currentColor' : 'none'">
                                                <path d="m19 21-7-4-7 4V5a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v16z"/>
                                            </svg>
                                        </button>
                                    </div>
                                </div>

                                {{-- Comentários --}}
                                @if (in_array($key, $openComments))
                                    @include('livewire.pages.feed._comments', ['key' => $key, 'comments' => $post->comments, 'postType' => 'user'])
                                @endif
                            </div>
                        </div>
                    @endif
                @empty
                    <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                        <x-lucide-inbox class="w-12 h-12 mx-auto mb-3 opacity-50" />
                        <p class="text-sm lato-regular">Nenhuma publicação encontrada.</p>
                    </div>
                @endforelse
                </div>

                {{-- Skeleton load-more (só quando há itens na tela) --}}
                @if ($this->feedItems->count() > 0)
                <div wire:loading wire:target="loadMore" class="w-full space-y-3" style="display:none">
                    @for ($sk = 0; $sk < 2; $sk++)
                        @include('livewire.pages.feed._skeleton')
                    @endfor
                </div>
                @endif

                {{-- Sentinel IntersectionObserver --}}
                {{-- Só mostra o sentinel se a página veio cheia (possível ter mais) --}}
                @php $feedCount = $this->feedItems->count(); @endphp
                @if ($feedCount > 0 && $feedCount >= $loadedCount)
                    <div wire:loading.remove wire:target="loadMore"
                         x-data
                         x-init="
                             const obs = new IntersectionObserver(entries => {
                                 if(entries[0].isIntersecting) $wire.loadMore();
                             }, { rootMargin: '200px' });
                             obs.observe($el);
                         "
                         class="h-4"></div>
                @elseif ($feedCount > 0)
                    <p class="text-center text-xs text-slate-400 dark:text-slate-600 py-4 lato-regular">
                        — Você chegou ao fim do feed —
                    </p>
                @endif

            </div>{{-- /coluna principal --}}

            {{-- ══════════════════════════════════════════════════════════
                 SIDEBAR
            ══════════════════════════════════════════════════════════ --}}
            <div class="space-y-4 sticky top-4">

                {{-- Card do usuário logado --}}
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="flex items-center gap-3 mb-3">
                        <span class="w-12 h-12 rounded-full {{ $meBg }} text-white text-base lato-bold flex items-center justify-center shrink-0">{{ $meInit }}</span>
                        <div class="min-w-0">
                            <p class="text-sm lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $me->name }}</p>
                            @if ($me->accessProfile)
                                <p class="text-xs text-slate-400 lato-regular">{{ $me->accessProfile->name }}</p>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-center border-t border-slate-100 dark:border-slate-700 pt-3">
                        <div>
                            <p class="text-base lato-bold text-slate-800 dark:text-slate-100">{{ $me->feedPosts()->count() }}</p>
                            <p class="text-[10px] text-slate-400 lato-regular">Posts</p>
                        </div>
                        <div>
                            <p class="text-base lato-bold text-slate-800 dark:text-slate-100">{{ $me->followers()->count() }}</p>
                            <p class="text-[10px] text-slate-400 lato-regular">Seguidores</p>
                        </div>
                    </div>
                    <a href="{{ route('feed.profile', $me->id) }}"
                       class="mt-3 block text-center text-xs lato-bold text-blue-600 dark:text-blue-400 hover:underline">
                        Ver meu perfil →
                    </a>
                </div>

                {{-- Sugeridos para seguir --}}
                @if ($this->suggestedUsers->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                        <h3 class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3">Sugeridos para você</h3>
                        <div class="space-y-3">
                            @foreach ($this->suggestedUsers as $sug)
                                @php $sugName = $sug->name; $sugBg = feedBg($sugName); @endphp
                                <div class="flex items-center gap-2.5">
                                    <a href="{{ route('feed.profile', $sug->id) }}" class="shrink-0">
                                        <span class="w-8 h-8 rounded-full {{ $sugBg }} text-white text-xs lato-bold flex items-center justify-center">
                                            {{ feedInitials($sugName) }}
                                        </span>
                                    </a>
                                    <div class="flex-1 min-w-0">
                                        <a href="{{ route('feed.profile', $sug->id) }}" class="text-xs lato-bold text-slate-700 dark:text-slate-200 hover:text-blue-600 dark:hover:text-blue-400 truncate block">
                                            {{ $sugName }}
                                        </a>
                                        <p class="text-[10px] text-slate-400">{{ $sug->feed_posts_count }} posts</p>
                                    </div>
                                    <button wire:click="toggleFollow({{ $sug->id }})" type="button"
                                            class="shrink-0 px-2.5 py-1 text-[10px] lato-bold rounded-full border transition
                                                   {{ in_array($sug->id, $this->myFollowingIds)
                                                       ? 'border-slate-300 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-red-300 hover:text-red-500'
                                                       : 'border-blue-500 text-blue-600 dark:text-blue-400 hover:bg-blue-500 hover:text-white' }}">
                                        {{ in_array($sug->id, $this->myFollowingIds) ? 'Seguindo' : 'Seguir' }}
                                    </button>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Publicações em destaque --}}
                @if ($this->pinnedPosts->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                        <h3 class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                            <x-lucide-pin class="w-3 h-3" /> Em destaque
                        </h3>
                        <div class="space-y-3">
                            @foreach ($this->pinnedPosts as $pp)
                                <button wire:click="openPreview({{ $pp->id }})" type="button" class="w-full text-left group flex items-center gap-3">
                                    {{-- Thumbnail --}}
                                    <div class="shrink-0 w-11 h-11 rounded-xl overflow-hidden bg-gradient-to-br from-blue-100 to-indigo-200 dark:from-blue-900/40 dark:to-indigo-900/40 flex items-center justify-center">
                                        @if ($pp->cover_image)
                                            <img src="{{ Storage::url($pp->cover_image) }}" alt="{{ $pp->title }}"
                                                 class="w-full h-full object-cover object-top pointer-events-none" />
                                        @else
                                            <x-lucide-megaphone class="w-4 h-4 text-blue-400 dark:text-blue-500" />
                                        @endif
                                    </div>
                                    {{-- Texto --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition line-clamp-2 leading-snug">
                                            {{ $pp->title }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $pp->published_at?->format('d/m/Y') }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- Publicações recentes --}}
                @if ($this->recentOfficial->isNotEmpty())
                    <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                        <h3 class="text-xs lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide mb-3 flex items-center gap-1.5">
                            <x-lucide-newspaper class="w-3 h-3" /> Publicações recentes
                        </h3>
                        <div class="space-y-3">
                            @foreach ($this->recentOfficial as $rp)
                                <button wire:click="openPreview({{ $rp->id }})" type="button" class="w-full text-left group flex items-center gap-3">
                                    {{-- Thumbnail --}}
                                    <div class="shrink-0 w-11 h-11 rounded-xl overflow-hidden bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-600 flex items-center justify-center">
                                        @if ($rp->cover_image)
                                            <img src="{{ Storage::url($rp->cover_image) }}" alt="{{ $rp->title }}"
                                                 class="w-full h-full object-cover object-top pointer-events-none" />
                                        @else
                                            <x-lucide-file-text class="w-4 h-4 text-slate-400 dark:text-slate-500" />
                                        @endif
                                    </div>
                                    {{-- Texto --}}
                                    <div class="flex-1 min-w-0">
                                        <p class="text-xs lato-bold text-slate-700 dark:text-slate-200 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition line-clamp-2 leading-snug">
                                            {{ $rp->title }}
                                        </p>
                                        <p class="text-[10px] text-slate-400 mt-0.5">{{ $rp->published_at?->diffForHumans() }}</p>
                                    </div>
                                </button>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>{{-- /sidebar --}}
        </div>{{-- /grid --}}
    </div>{{-- /max-w --}}

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Preview post oficial
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($previewOpen && $this->previewPost)
        @php $pp = $this->previewPost; $ppKey = 'official_'.$pp->id; @endphp
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="$wire.closePreview()" style="cursor:pointer">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto"
                 @click.stop>
                <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 sticky top-0 bg-white dark:bg-slate-800 z-10">
                    <div>
                        <h2 class="text-base lato-bold text-slate-800 dark:text-slate-100">{{ $pp->title }}</h2>
                        <p class="text-xs text-slate-400 mt-0.5">{{ $pp->published_at?->format('d \d\e F \d\e Y') }}</p>
                    </div>
                    <button wire:click="closePreview" type="button"
                            class="p-2 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <div class="p-5 space-y-4">
                    @if ($pp->cover_image)
                        <div class="overflow-hidden bg-slate-100 dark:bg-slate-900 cursor-zoom-in"
                             style="aspect-ratio:16/7"
                             @click="$dispatch('open-lightbox', { src: '{{ Storage::url($pp->cover_image) }}', alt: '{{ addslashes($pp->title) }}' })">
                            <img src="{{ Storage::url($pp->cover_image) }}" alt="{{ $pp->title }}"
                                 class="w-full h-full object-cover object-top pointer-events-none" />
                        </div>
                    @endif
                    <div class="prose prose-sm dark:prose-invert max-w-none text-slate-700 dark:text-slate-200 lato-regular leading-relaxed">
                        {!! nl2br(e($pp->content)) !!}
                    </div>
                    @if ($pp->tags->isNotEmpty())
                        <div class="flex flex-wrap gap-1.5">
                            @foreach ($pp->tags as $t)
                                <span class="px-2 py-0.5 text-xs rounded-full bg-blue-50 dark:bg-blue-900/20 text-blue-600 dark:text-blue-400 border border-blue-200 dark:border-blue-700">
                                    #{{ $t->name }}
                                </span>
                            @endforeach
                        </div>
                    @endif
                    <div class="flex items-center justify-between pt-3 border-t border-slate-100 dark:border-slate-700">
                        <div class="text-xs text-slate-400 lato-regular">
                            {{ $pp->reads->count() }} leituras ·
                            {{ $pp->reads->where('confirmed_at', '!=', null)->count() }} confirmadas
                        </div>
                        @if (! $pp->reads->where('user_id', Auth::id())->where('confirmed_at', '!=', null)->first())
                            <button wire:click="confirmRead({{ $pp->id }})" type="button"
                                    class="px-4 py-2 text-xs lato-bold rounded-xl bg-gradient-to-r from-green-500 to-emerald-600
                                           text-white hover:from-green-600 hover:to-emerald-700 transition shadow-sm disabled:opacity-60"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="confirmRead" class="flex items-center gap-1.5">
                                    ✓ Confirmar leitura
                                </span>
                                <span wire:loading wire:target="confirmRead" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </span>
                            </button>
                        @else
                            <span class="px-3 py-1.5 text-xs lato-bold rounded-xl bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-400">
                                ✓ Leitura confirmada
                            </span>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Confirmar exclusão
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($deleteModal)
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
                    <button wire:click="$set('deleteModal', false)" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="deleteFeedPost" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl bg-red-500 text-white hover:bg-red-600 transition disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="deleteFeedPost" class="flex items-center gap-1.5">
                            Excluir
                        </span>
                        <span wire:loading wire:target="deleteFeedPost" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Editar post
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($editModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="$wire.set('editModal', false)" style="cursor:pointer">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg p-5" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">Editar post</h3>
                    <button wire:click="$set('editModal', false)" type="button"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <textarea wire:model="editContent" rows="5" maxlength="2000"
                          class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                 focus:outline-none focus:ring-2 focus:ring-blue-400/30 resize-none lato-regular"></textarea>
                @error('editContent') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="$set('editModal', false)" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="saveEdit" type="button"
                            class="px-5 py-2 text-xs lato-bold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="saveEdit" class="flex items-center gap-1.5">
                            Salvar
                        </span>
                        <span wire:loading wire:target="saveEdit" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         MODAL: Repost
    ══════════════════════════════════════════════════════════════════ --}}
    @if ($repostModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="$wire.set('repostModal', false)" style="cursor:pointer">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg p-5" @click.stop>
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100">Repostar</h3>
                    <button wire:click="$set('repostModal', false)" type="button"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>
                <textarea wire:model="repostComment" rows="3" maxlength="1000"
                          placeholder="Adicione um comentário (opcional)…"
                          class="w-full px-4 py-3 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                 bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                 focus:outline-none focus:ring-2 focus:ring-blue-400/30 resize-none lato-regular placeholder-slate-400"></textarea>
                @error('repostComment') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                <div class="flex justify-end gap-2 mt-4">
                    <button wire:click="$set('repostModal', false)" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition">
                        Cancelar
                    </button>
                    <button wire:click="repost" type="button"
                            class="px-5 py-2 text-xs lato-bold rounded-xl bg-gradient-to-r from-green-500 to-emerald-600 text-white hover:from-green-600 hover:to-emerald-700 transition shadow-sm">
                        <x-lucide-repeat-2 class="w-3.5 h-3.5 inline mr-1" /> Repostar
                    </button>
                </div>
            </div>
        </div>
    @endif

    {{-- ══════════════════════════════════════════════════════════════════
         LIGHTBOX
    ══════════════════════════════════════════════════════════════════ --}}
    <div x-show="lightbox.open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/90 backdrop-blur-sm p-4"
         @click="closeLightbox()" style="display:none">
        <div class="relative max-w-5xl max-h-full flex flex-col items-center"
             @click.stop>
            <button @click="closeLightbox()" type="button"
                    class="absolute -top-10 right-0 text-white/70 hover:text-white transition p-1">
                <x-lucide-x class="w-6 h-6" />
            </button>
            <img :src="lightbox.src" :alt="lightbox.alt"
                 class="max-w-full max-h-[85vh] object-contain rounded-xl shadow-2xl"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="scale-95 opacity-0"
                 x-transition:enter-end="scale-100 opacity-100" />
            <p x-show="lightbox.alt" x-text="lightbox.alt"
               class="mt-3 text-white/70 text-sm text-center lato-regular"></p>
        </div>
    </div>

    <style>
    @keyframes feedFlash {
        0%   { box-shadow: 0 0 0 0   rgb(99 102 241 / 0);   background-color: transparent; }
        15%  { box-shadow: 0 0 0 4px rgb(99 102 241 / 0.5); background-color: rgb(238 242 255 / 0.6); }
        80%  { box-shadow: 0 0 0 2px rgb(99 102 241 / 0.2); background-color: rgb(238 242 255 / 0.2); }
        100% { box-shadow: 0 0 0 0   transparent;            background-color: transparent; }
    }
    .dark .feed-post-flash {
        animation: feedFlashDark 2.5s ease-out forwards;
    }
    .feed-post-flash {
        animation: feedFlash 2.5s ease-out forwards;
        border-radius: 1rem;
    }
    @keyframes feedFlashDark {
        0%   { box-shadow: 0 0 0 0   rgb(99 102 241 / 0);   background-color: transparent; }
        15%  { box-shadow: 0 0 0 4px rgb(99 102 241 / 0.5); background-color: rgb(49 46 129 / 0.3); }
        80%  { box-shadow: 0 0 0 2px rgb(99 102 241 / 0.2); background-color: rgb(49 46 129 / 0.1); }
        100% { box-shadow: 0 0 0 0   transparent;            background-color: transparent; }
    }
    </style>

    @script
    <script>
    (function () {
        const seen = new Set();

        function markRead(postId) {
            if (seen.has(postId)) return;
            seen.add(postId);
            $wire.markFeedRead(postId);
        }

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                const el  = entry.target;
                const id  = parseInt(el.dataset.feedPostId);
                if (!id) return;
                if (entry.isIntersecting) {
                    el._readTimer = setTimeout(() => markRead(id), 1500);
                } else {
                    clearTimeout(el._readTimer);
                }
            });
        }, { threshold: 0.5 });

        function observeCards() {
            document.querySelectorAll('[data-feed-post-id]').forEach(el => {
                if (!el._observed) {
                    observer.observe(el);
                    el._observed = true;
                }
            });
        }

        observeCards();

        Livewire.hook('commit', ({ succeed }) => {
            succeed(() => setTimeout(observeCards, 100));
        });

        window.addEventListener('open-lightbox', (e) => {
            const postId = e.detail?.postId;
            if (postId) markRead(postId);
        });
    })();
    </script>
    @endscript

</div>
