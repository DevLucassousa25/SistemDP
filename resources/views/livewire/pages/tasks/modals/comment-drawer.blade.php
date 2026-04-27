<div x-data="{ open: @entangle('open') }" x-effect="document.body.style.overflow = open ? 'hidden' : ''">
    @if ($open && $this->task)
        {{-- Overlay --}}
        <div class="fixed inset-0 z-40 bg-slate-900/30 backdrop-blur-sm"
             wire:click="close"></div>

        {{-- Painel --}}
        <div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[420px] bg-white dark:bg-slate-800
                    shadow-2xl flex flex-col"
             x-data x-init="$el.scrollTop = 0">

            {{-- Header --}}
            <div class="flex items-start justify-between px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-br from-indigo-500 to-indigo-700 flex items-center justify-center shrink-0">
                        <x-lucide-message-circle class="w-4 h-4 text-white" />
                    </div>
                    <div>
                        <h2 class="text-sm font-bold text-slate-800 dark:text-white lato-black leading-tight line-clamp-1">
                            {{ $this->task->title }}
                        </h2>
                        <p class="text-[11px] text-slate-400 lato-regular mt-0.5">
                            {{ $this->task->comments->count() }} comentário(s)
                        </p>
                    </div>
                </div>
                <button type="button" wire:click="close"
                        class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                               hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                               transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>

            {{-- Info resumida da tarefa --}}
            <div class="px-5 py-3 bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-700 shrink-0">
                <div class="flex flex-wrap gap-2">
                    <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full {{ $this->task->priority_color }}">
                        {{ $this->task->priority_label }}
                    </span>
                    <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full {{ $this->task->status_color }}">
                        {{ $this->task->status_label }}
                    </span>
                    @if ($this->task->due_date)
                        <span class="flex items-center gap-1 text-[10px] lato-regular
                                     {{ $this->task->is_overdue ? 'text-red-500' : 'text-slate-400' }}">
                            <x-lucide-calendar class="w-3 h-3" />
                            {{ $this->task->due_date->format('d/m/Y') }}
                        </span>
                    @endif
                    @if ($this->task->tags->isNotEmpty())
                        @foreach ($this->task->tags as $tag)
                            <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full text-white"
                                  style="background: {{ $tag->color }}">{{ $tag->name }}</span>
                        @endforeach
                    @endif
                </div>
            </div>

            {{-- Thread de comentários --}}
            <div class="flex-1 overflow-y-auto px-5 py-4 space-y-4">
                @if ($this->task->comments->isEmpty())
                    <div class="flex flex-col items-center justify-center py-12 text-center">
                        <div class="w-12 h-12 rounded-full bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                            <x-lucide-message-circle class="w-5 h-5 text-slate-400" />
                        </div>
                        <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular">Nenhum comentário ainda.</p>
                        <p class="text-xs text-slate-400 lato-regular mt-1">Seja o primeiro a comentar.</p>
                    </div>
                @else
                    @foreach ($this->task->comments as $comment)
                        <div class="flex gap-3 group/comment">
                            <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-600
                                        flex items-center justify-center text-white text-xs lato-black shrink-0 mt-0.5">
                                {{ mb_strtoupper(mb_substr($comment->user->name, 0, 1)) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="text-xs lato-bold text-slate-800 dark:text-white">{{ $comment->user->name }}</span>
                                    <span class="text-[10px] text-slate-400 lato-regular">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if ($comment->user_id === auth()->id())
                                        <button type="button" wire:click="deleteComment({{ $comment->id }})"
                                                class="ml-auto opacity-0 group-hover/comment:opacity-100 transition text-slate-300
                                                       hover:text-red-500 cursor-pointer p-0.5 rounded">
                                            <x-lucide-trash-2 class="w-3 h-3" />
                                        </button>
                                    @endif
                                </div>
                                <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl rounded-tl-none px-3 py-2.5">
                                    <p class="text-sm lato-regular text-slate-700 dark:text-slate-200 leading-relaxed whitespace-pre-line">{{ $comment->body }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            {{-- Input de novo comentário --}}
            <div class="shrink-0 px-5 py-4 border-t border-slate-100 dark:border-slate-700 bg-white dark:bg-slate-800">
                <div class="flex gap-2">
                    <div class="w-8 h-8 rounded-full bg-gradient-to-br from-emerald-400 to-emerald-600
                                flex items-center justify-center text-white text-xs lato-black shrink-0 mt-1">
                        {{ mb_strtoupper(mb_substr(auth()->user()->name, 0, 1)) }}
                    </div>
                    <div class="flex-1">
                        <textarea wire:model="newComment" rows="2"
                                  placeholder="Escreva um comentário..."
                                  @keydown.ctrl.enter="$wire.addComment()"
                                  class="w-full px-3 py-2 text-sm lato-regular rounded-xl bg-slate-50 dark:bg-slate-700
                                         border border-slate-200 dark:border-slate-600 text-slate-800 dark:text-white
                                         placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/40 transition resize-none"></textarea>
                        <div class="flex items-center justify-between mt-2">
                            <span class="text-[10px] text-slate-400 lato-regular">Ctrl+Enter para enviar</span>
                            <button type="button" wire:click="addComment"
                                    wire:loading.attr="disabled" wire:target="addComment"
                                    class="flex items-center gap-1.5 px-4 py-1.5 text-xs lato-bold bg-indigo-500 hover:bg-indigo-600
                                           disabled:opacity-60 text-white rounded-lg shadow-sm transition cursor-pointer">
                                <x-lucide-send class="w-3 h-3" />
                                Enviar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>
