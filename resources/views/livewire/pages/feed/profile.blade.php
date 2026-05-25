@php
    if (!function_exists('profBg')) {
        function profBg(string $name): string {
            $pal2 = ['bg-indigo-500','bg-violet-500','bg-pink-500','bg-teal-500','bg-amber-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
            return $pal2[abs(crc32($name)) % count($pal2)];
        }
    }
    if (!function_exists('profInit')) {
        function profInit(string $name): string {
            $p = explode(' ', trim($name));
            return strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));
        }
    }
    $user  = $this->profileUser;
    $uName = $user->name ?? '?';
    $uBg   = profBg($uName);
    $uInit = profInit($uName);
@endphp

<div class="min-h-screen bg-slate-50 dark:bg-slate-900 p-4 sm:p-6 lg:p-8">
    <div class="max-w-4xl mx-auto space-y-5">

        {{-- Voltar --}}
        <a href="{{ route('feed') }}" class="inline-flex items-center gap-1.5 text-xs text-slate-500 hover:text-blue-600 transition lato-regular">
            <x-lucide-arrow-left class="w-3.5 h-3.5" /> Voltar ao feed
        </a>

        {{-- Banner + Avatar --}}
        <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 overflow-hidden">
            {{-- Banner --}}
            <div class="h-28 bg-gradient-to-r from-blue-500 via-indigo-500 to-violet-600"></div>

            {{-- Info --}}
            <div class="px-5 pb-5">
                <div class="flex items-end justify-between -mt-8 mb-4">
                    <span class="w-20 h-20 rounded-2xl {{ $uBg }} text-white text-2xl lato-bold flex items-center justify-center ring-4 ring-white dark:ring-slate-800 shrink-0">
                        {{ $uInit }}
                    </span>
                    <div class="flex items-center gap-2 mb-1">
                        @if (! $this->isOwnProfile)
                            <button wire:click="toggleFollow" type="button"
                                    class="px-4 py-2 text-xs lato-bold rounded-xl transition shadow-sm
                                           {{ $this->isFollowing
                                               ? 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-red-50 dark:hover:bg-red-900/20 hover:text-red-500 border border-slate-200 dark:border-slate-600'
                                               : 'bg-gradient-to-r from-blue-500 to-indigo-600 text-white hover:from-blue-600 hover:to-indigo-700' }}">
                                {{ $this->isFollowing ? 'Seguindo' : '+ Seguir' }}
                            </button>
                        @else
                            <button wire:click="openEditModal" type="button"
                                    class="px-4 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                                <x-lucide-pencil class="w-3.5 h-3.5 inline mr-1" /> Editar perfil
                            </button>
                        @endif
                    </div>
                </div>

                <h1 class="text-lg lato-bold text-slate-800 dark:text-slate-100">{{ $uName }}</h1>
                <div class="flex items-center gap-2 flex-wrap mt-0.5">
                    @if ($user->position)
                        <span class="text-sm text-slate-500 lato-regular">{{ $user->position }}</span>
                    @endif
                    @if ($user->accessProfile)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-slate-100 dark:bg-slate-700 text-slate-500 dark:text-slate-400 lato-bold">{{ $user->accessProfile->name }}</span>
                    @endif
                    @if ($user->department?->name)
                        <span class="text-[10px] px-2 py-0.5 rounded-full bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 lato-regular">{{ $user->department->name }}</span>
                    @endif
                </div>
                @if ($user->bio)
                    <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular mt-2 leading-relaxed">{{ $user->bio }}</p>
                @endif

                {{-- Stats --}}
                <div class="flex items-center gap-6 mt-4 pt-4 border-t border-slate-100 dark:border-slate-700">
                    <div class="text-center">
                        <p class="text-lg lato-bold text-slate-800 dark:text-slate-100">{{ $user->feed_posts_count }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Posts</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg lato-bold text-slate-800 dark:text-slate-100">{{ $user->followers_count }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Seguidores</p>
                    </div>
                    <div class="text-center">
                        <p class="text-lg lato-bold text-slate-800 dark:text-slate-100">{{ $user->following_count }}</p>
                        <p class="text-xs text-slate-400 lato-regular">Seguindo</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Posts do usuário --}}
        <div class="space-y-3">
            <h2 class="text-sm lato-bold text-slate-600 dark:text-slate-400 uppercase tracking-wide">Publicações</h2>

            @forelse ($this->posts as $post)
                @php
                    $imgCount = $post->images->count();
                    $myRx = $post->reactions->where('user_id', Auth::id())->first()?->type;
                @endphp
                <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 p-4">
                    <div class="flex items-center gap-2 mb-2">
                        <span class="w-8 h-8 rounded-full {{ $uBg }} text-white text-xs lato-bold flex items-center justify-center shrink-0">{{ $uInit }}</span>
                        <div>
                            <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $uName }}</p>
                            <p class="text-[10px] text-slate-400">{{ $post->created_at->diffForHumans() }}
                                @if($post->edited_at) · <span class="italic">editado</span> @endif
                            </p>
                        </div>
                    </div>

                    @if ($post->content)
                        <p class="text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed mb-3 whitespace-pre-line">
                            {{ Str::limit($post->content, 300) }}
                        </p>
                    @endif

                    @if ($imgCount > 0)
                        <div class="rounded-xl overflow-hidden mb-3 {{ $imgCount > 1 ? 'grid grid-cols-2 gap-1' : '' }}">
                            @foreach ($post->images->take(4) as $idx => $img)
                                <div class="bg-slate-100 dark:bg-slate-900 {{ $imgCount === 1 ? '' : 'aspect-square' }}">
                                    <img src="{{ Storage::url($img->path) }}"
                                         class="w-full h-full {{ $imgCount === 1 ? 'max-h-64 object-contain' : 'object-cover' }}" />
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <div class="flex items-center gap-3 text-xs text-slate-400 lato-regular pt-2 border-t border-slate-100 dark:border-slate-700">
                        <span class="flex items-center gap-1">
                            <x-lucide-thumbs-up class="w-3.5 h-3.5" /> {{ $post->like_count }}
                        </span>
                        <span class="flex items-center gap-1">
                            <x-lucide-message-circle class="w-3.5 h-3.5" /> {{ $post->comments_count }}
                        </span>
                        <span class="flex items-center gap-1">
                            <x-lucide-eye class="w-3.5 h-3.5" /> {{ $post->views_count ?? 0 }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-slate-400 dark:text-slate-500">
                    <x-lucide-file-text class="w-10 h-10 mx-auto mb-3 opacity-50" />
                    <p class="text-sm lato-regular">Nenhuma publicação ainda.</p>
                </div>
            @endforelse

            {{-- Paginação --}}
            {{ $this->posts->links() }}
        </div>

    </div>

    {{-- ── Modal: Editar perfil ─────────────────────────────────────── --}}
    @if ($editModal)
        <div class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
             @click.self="$wire.set('editModal', false)" style="cursor:pointer">
            <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-md p-6" @click.stop>

                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <x-lucide-user-cog class="w-4 h-4 text-blue-500" /> Editar perfil
                    </h3>
                    <button wire:click="$set('editModal', false)" type="button"
                            class="p-1.5 rounded-xl text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                        <x-lucide-x class="w-4 h-4" />
                    </button>
                </div>

                <div class="space-y-4">
                    {{-- Cargo --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Cargo / Função</label>
                        <input wire:model="editPosition" type="text" maxlength="100"
                               placeholder="Ex: Analista de TI, Gerente Comercial…"
                               class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400
                                      lato-regular placeholder-slate-400" />
                        @error('editPosition') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>

                    {{-- Bio --}}
                    <div>
                        <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-1.5">Sobre mim</label>
                        <textarea wire:model="editBio" rows="4" maxlength="300"
                                  placeholder="Uma breve descrição sobre você…"
                                  class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-700 rounded-xl
                                         bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                         focus:outline-none focus:ring-2 focus:ring-blue-400/30 focus:border-blue-400
                                         lato-regular placeholder-slate-400 resize-none"></textarea>
                        <p class="text-[10px] text-slate-400 mt-1 text-right">{{ strlen($editBio) }}/300</p>
                        @error('editBio') <p class="text-xs text-red-500 mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="flex justify-end gap-2 mt-5">
                    <button wire:click="$set('editModal', false)" type="button"
                            class="px-4 py-2 text-xs lato-bold rounded-xl border border-slate-200 dark:border-slate-700
                                   text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 transition cursor-pointer">
                        Cancelar
                    </button>
                    <button wire:click="saveProfile" type="button"
                            class="px-5 py-2 text-xs lato-bold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600
                                   text-white hover:from-blue-600 hover:to-indigo-700 transition shadow-sm cursor-pointer">
                        <span wire:loading.remove wire:target="saveProfile">Salvar</span>
                        <span wire:loading wire:target="saveProfile">Salvando…</span>
                    </button>
                </div>

            </div>
        </div>
    @endif

</div>
