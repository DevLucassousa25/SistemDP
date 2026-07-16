    {{-- ════════════════════════════════════════════════════════════════
         MODAL — GERENCIAR TAGS  (Bottom Sheet no mobile, modal no desktop)
    ════════════════════════════════════════════════════════════════ --}}
    @if ($tagModal)
        <style>
            @keyframes slideUp {
                from { transform: translateY(100%); opacity: 0; }
                to   { transform: translateY(0);    opacity: 1; }
            }
            @keyframes fadeIn {
                from { opacity: 0; }
                to   { opacity: 1; }
            }
            .bottom-sheet-overlay  { animation: fadeIn  .2s ease forwards; }
            .bottom-sheet-panel    { animation: slideUp .28s cubic-bezier(.32,.72,0,1) forwards; }
            .modal-panel           { animation: fadeIn  .2s ease forwards; }
        </style>

        {{-- ── MOBILE: Bottom Sheet ── --}}
        <div class="sm:hidden fixed inset-0 z-50 flex flex-col justify-end bottom-sheet-overlay"
             style="background:rgba(0,0,0,.45);backdrop-filter:blur(4px)">

            {{-- tap-outside to close --}}
            <div class="absolute inset-0" wire:click="closeTagManager"></div>

            <div class="relative bottom-sheet-panel bg-white dark:bg-slate-800 w-full
                        rounded-t-3xl shadow-2xl flex flex-col
                        max-h-[90dvh] overflow-hidden">

                {{-- Drag handle --}}
                <div class="flex justify-center pt-3 pb-1 shrink-0">
                    <span class="w-10 h-1 rounded-full bg-slate-300 dark:bg-slate-600"></span>
                </div>

                {{-- Header --}}
                <div class="flex items-center justify-between px-5 py-3 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <h3 class="text-sm lato-black text-slate-800 dark:text-white flex items-center gap-2">
                        <x-lucide-tags class="w-4 h-4 text-indigo-500" /> Gerenciar tags
                    </h3>
                    <button wire:click="closeTagManager" type="button"
                            class="p-1.5 rounded-full hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                {{-- Criar nova tag --}}
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-700 space-y-3 shrink-0">
                    <p class="text-xs lato-bold text-slate-600 dark:text-slate-400">Nova tag</p>
                    <div class="flex gap-2">
                        <input wire:model="newTagName" type="text" placeholder="Nome da tag..."
                               class="flex-1 px-3 py-2.5 text-sm lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition"
                               wire:keydown.enter="saveTag" />
                        <button wire:click="saveTag" type="button"
                                class="px-4 py-2.5 rounded-xl text-xs lato-bold bg-indigo-600 hover:bg-indigo-700 active:bg-indigo-800 text-white transition cursor-pointer shrink-0 disabled:opacity-60"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveTag" class="flex items-center gap-1.5">
                                <x-lucide-plus class="w-4 h-4" />
                            </span>
                            <span wire:loading wire:target="saveTag" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                    </div>
                    @error('newTagName') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                    {{-- Seletor de cor --}}
                    <div>
                        <p class="text-[11px] text-slate-400 lato-regular mb-2">Cor</p>
                        <div class="flex gap-2.5 flex-wrap">
                            @foreach (['indigo','violet','blue','emerald','teal','amber','orange','red','pink','slate'] as $c)
                                <button type="button" wire:click="$set('newTagColor', '{{ $c }}')"
                                        class="w-7 h-7 rounded-full transition-all cursor-pointer border-2 bg-{{ $c }}-400
                                        {{ $newTagColor === $c ? 'border-slate-600 dark:border-white scale-110 ring-2 ring-offset-1 ring-'.$c.'-400' : 'border-transparent' }}">
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Lista de tags --}}
                <div class="flex-1 overflow-y-auto px-5 py-3 space-y-2">
                    @forelse ($this->availableTags as $tag)
                        <div class="flex items-center justify-between p-3 rounded-2xl border border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/30 active:bg-slate-100 transition">
                            <div class="flex items-center gap-2.5">
                                <span class="w-3.5 h-3.5 rounded-full {{ $tag->dot_class }} shrink-0"></span>
                                <span class="text-sm lato-bold text-slate-700 dark:text-slate-200">{{ $tag->name }}</span>
                                <span class="text-[11px] text-slate-400 lato-regular">({{ $tag->posts->count() }} posts)</span>
                            </div>
                            <button wire:click="deleteTag({{ $tag->id }})"
                                    wire:confirm="Excluir a tag '{{ $tag->name }}'? Ela será removida de todos os posts."
                                    type="button"
                                    class="p-2 rounded-xl hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-300 hover:text-red-500 transition cursor-pointer disabled:opacity-60"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="deleteTag" class="flex items-center gap-1.5">
                                    <x-lucide-trash-2 class="w-4 h-4" />
                                </span>
                                <span wire:loading wire:target="deleteTag" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </span>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-10">
                            <x-lucide-tags class="w-9 h-9 text-slate-200 dark:text-slate-600 mx-auto mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhuma tag criada ainda.</p>
                        </div>
                    @endforelse
                </div>

                {{-- Footer com safe-area para home indicator --}}
                <div class="shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-700"
                     style="padding-bottom: max(1rem, env(safe-area-inset-bottom))">
                    <button wire:click="closeTagManager" type="button"
                            class="w-full py-3 rounded-2xl text-sm lato-bold bg-slate-100 dark:bg-slate-700
                                   text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600
                                   active:scale-95 transition cursor-pointer">
                        Fechar
                    </button>
                </div>
            </div>
        </div>

        {{-- ── DESKTOP: Modal centralizado (comportamento original) ── --}}
        <div class="hidden sm:flex fixed inset-0 bg-black/50 backdrop-blur-sm z-50 items-center justify-center p-4 modal-panel">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-md flex flex-col max-h-[80vh] overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <h3 class="text-sm lato-black text-slate-800 dark:text-white flex items-center gap-2">
                        <x-lucide-tags class="w-4 h-4 text-indigo-500" /> Gerenciar tags
                    </h3>
                    <button wire:click="closeTagManager" type="button" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                {{-- Criar nova tag --}}
                <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-700 space-y-3 shrink-0">
                    <p class="text-xs lato-bold text-slate-600 dark:text-slate-400">Nova tag</p>
                    <div class="flex gap-2">
                        <input wire:model="newTagName" type="text" placeholder="Nome da tag..."
                               class="flex-1 px-3 py-2 text-xs lato-regular border border-slate-200 dark:border-slate-700 rounded-xl
                                      bg-white dark:bg-slate-900 text-slate-700 dark:text-slate-200
                                      focus:outline-none focus:ring-2 focus:ring-indigo-400/40 focus:border-indigo-400 transition"
                               wire:keydown.enter="saveTag" />
                        <button wire:click="saveTag" type="button"
                                class="px-3 py-2 rounded-xl text-xs lato-bold bg-indigo-600 hover:bg-indigo-700 text-white transition cursor-pointer shrink-0 disabled:opacity-60"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="saveTag" class="flex items-center gap-1.5">
                                <x-lucide-plus class="w-3.5 h-3.5" />
                            </span>
                            <span wire:loading wire:target="saveTag" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                    </div>
                    @error('newTagName') <p class="text-xs text-red-500">{{ $message }}</p> @enderror

                    {{-- Seletor de cor --}}
                    <div>
                        <p class="text-[10px] text-slate-400 lato-regular mb-2">Cor</p>
                        <div class="flex gap-2 flex-wrap">
                            @foreach (['indigo','violet','blue','emerald','teal','amber','orange','red','pink','slate'] as $c)
                                <button type="button" wire:click="$set('newTagColor', '{{ $c }}')"
                                        class="w-6 h-6 rounded-full transition cursor-pointer border-2 bg-{{ $c }}-400
                                        {{ $newTagColor === $c ? 'border-slate-600 dark:border-white scale-110' : 'border-transparent' }}">
                                </button>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Lista de tags existentes --}}
                <div class="flex-1 overflow-y-auto px-6 py-4 space-y-2">
                    @forelse ($this->availableTags as $tag)
                        <div class="flex items-center justify-between p-2.5 rounded-xl border border-slate-100 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-700/30 transition">
                            <div class="flex items-center gap-2.5">
                                <span class="w-3 h-3 rounded-full {{ $tag->dot_class }} shrink-0"></span>
                                <span class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $tag->name }}</span>
                                <span class="text-[10px] text-slate-400 lato-regular">({{ $tag->posts->count() }} posts)</span>
                            </div>
                            <button wire:click="deleteTag({{ $tag->id }})"
                                    wire:confirm="Excluir a tag '{{ $tag->name }}'? Ela será removida de todos os posts."
                                    type="button"
                                    class="p-1 rounded-lg hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-300 hover:text-red-500 transition cursor-pointer disabled:opacity-60"
                                wire:loading.attr="disabled">
                                <span wire:loading.remove wire:target="deleteTag" class="flex items-center gap-1.5">
                                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                                </span>
                                <span wire:loading wire:target="deleteTag" class="flex items-center gap-1.5">
                                    <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                </span>
                            </button>
                        </div>
                    @empty
                        <div class="text-center py-8">
                            <x-lucide-tags class="w-8 h-8 text-slate-200 dark:text-slate-600 mx-auto mb-2" />
                            <p class="text-xs text-slate-400 lato-regular">Nenhuma tag criada ainda.</p>
                        </div>
                    @endforelse
                </div>

                <div class="shrink-0 px-6 py-3 border-t border-slate-100 dark:border-slate-700 flex justify-end">
                    <button wire:click="closeTagManager" type="button"
                            class="px-4 py-2 rounded-xl text-sm lato-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif
