@php
    if (!function_exists('cmtInitials')) {
        function cmtInitials(string $name): string {
            $p = explode(' ', trim($name));
            return strtoupper(substr($p[0],0,1).(isset($p[1])?substr($p[1],0,1):''));
        }
    }
    if (!function_exists('cmtBg')) {
        function cmtBg(string $name): string {
            $pal = ['bg-indigo-500','bg-violet-500','bg-pink-500','bg-teal-500','bg-amber-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
            return $pal[abs(crc32($name)) % count($pal)];
        }
    }
    $me      = Auth::user();
    $meParts = explode(' ', trim($me->name ?? '?'));
    $meInit  = strtoupper(substr($meParts[0],0,1).(isset($meParts[1])?substr($meParts[1],0,1):''));
    $pal2    = ['bg-indigo-500','bg-violet-500','bg-pink-500','bg-teal-500','bg-amber-500','bg-orange-500','bg-cyan-500','bg-rose-500'];
    $meBg    = $pal2[abs(crc32($me->name ?? '')) % count($pal2)];
@endphp

<div class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700 space-y-3"
     x-data="{
         text: '',
         pending: [],
         async send() {
             if (!this.text.trim()) return;
             const t = this.text;
             this.pending.push({ text: t, ts: Date.now() });
             this.text = '';
             await $wire.submitComment('{{ $key }}', t);
             this.pending = [];
         }
     }"
     @comment-saved.window="if($event.detail.key === '{{ $key }}') pending = []">

    {{-- Comentários existentes --}}
    @forelse ($comments as $cmt)
        @php $cmtAuthor = $cmt->user->name ?? '?'; @endphp
        <div class="flex gap-2.5" x-data="{ showReply: false, replyText: '' }">
            <span class="w-7 h-7 rounded-full {{ cmtBg($cmtAuthor) }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0 mt-0.5">
                {{ cmtInitials($cmtAuthor) }}
            </span>
            <div class="flex-1">
                <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl px-3 py-2">
                    <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $cmtAuthor }}</p>
                    <p class="text-xs text-slate-600 dark:text-slate-300 lato-regular leading-relaxed mt-0.5">{{ $cmt->content }}</p>
                </div>
                <div class="flex items-center gap-3 mt-1 ml-1">
                    <span class="text-[10px] text-slate-400">{{ $cmt->created_at->diffForHumans() }}</span>
                    @if ($postType === 'user')
                        <button type="button" @click="showReply = !showReply"
                                class="text-[10px] text-slate-400 hover:text-blue-500 transition lato-bold">
                            Responder
                        </button>
                    @endif
                    @if ($cmt->user_id === Auth::id())
                        <button wire:click="deleteComment('{{ $key }}', {{ $cmt->id }})" type="button"
                                class="text-[10px] text-slate-400 hover:text-red-500 transition">
                            Excluir
                        </button>
                    @endif
                </div>

                {{-- Respostas aninhadas --}}
                @if ($cmt->replies?->isNotEmpty())
                    <div class="mt-2 space-y-2 pl-4 border-l-2 border-slate-100 dark:border-slate-700">
                        @foreach ($cmt->replies as $reply)
                            @php $replyAuthor = $reply->user->name ?? '?'; @endphp
                            <div class="flex gap-2">
                                <span class="w-6 h-6 rounded-full {{ cmtBg($replyAuthor) }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0">
                                    {{ cmtInitials($replyAuthor) }}
                                </span>
                                <div class="flex-1">
                                    <div class="bg-slate-50 dark:bg-slate-900/50 rounded-xl px-2.5 py-1.5">
                                        <p class="text-[10px] lato-bold text-slate-700 dark:text-slate-200">{{ $replyAuthor }}</p>
                                        <p class="text-[10px] text-slate-600 dark:text-slate-300 lato-regular leading-relaxed">{{ $reply->content }}</p>
                                    </div>
                                    <span class="text-[9px] text-slate-400 ml-1">{{ $reply->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                {{-- Campo de resposta --}}
                @if ($postType === 'user')
                    <div x-show="showReply" x-transition class="mt-2 flex gap-2 items-start">
                        <span class="w-6 h-6 rounded-full {{ $meBg ?? 'bg-indigo-500' }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0">
                            {{ $meInit ?? '?' }}
                        </span>
                        <div class="flex-1 flex gap-1.5">
                            <input x-model="replyText" type="text"
                                   placeholder="Responder…"
                                   maxlength="500"
                                   @keydown.enter.prevent="if(replyText.trim()) { $wire.submitComment('{{ $key }}', replyText, {{ $cmt->id }}); replyText=''; showReply=false; }"
                                   class="flex-1 px-3 py-1.5 text-xs border border-slate-200 dark:border-slate-700 rounded-xl
                                          bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                                          focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular placeholder-slate-400" />
                            <button type="button"
                                    @click="if(replyText.trim()) { $wire.submitComment('{{ $key }}', replyText, {{ $cmt->id }}); replyText=''; showReply=false; }"
                                    class="px-3 py-1.5 text-[10px] lato-bold rounded-xl bg-blue-500 text-white hover:bg-blue-600 transition shrink-0">
                                Enviar
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    @empty
        <p class="text-xs text-slate-400 text-center py-1 lato-regular">Seja o primeiro a comentar.</p>
    @endforelse

    {{-- Comentários optimistas (pending) --}}
    <template x-for="p in pending" :key="p.ts">
        <div class="flex gap-2.5 opacity-60">
            <span class="w-7 h-7 rounded-full {{ $meBg ?? 'bg-indigo-500' }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0">
                {{ $meInit ?? '?' }}
            </span>
            <div class="flex-1 bg-slate-50 dark:bg-slate-900/50 rounded-xl px-3 py-2">
                <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-600 dark:text-slate-300 lato-regular mt-0.5" x-text="p.text"></p>
                <p class="text-[10px] text-slate-400 mt-1 flex items-center gap-1">
                    <svg class="w-3 h-3 animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path>
                    </svg>
                    enviando…
                </p>
            </div>
        </div>
    </template>

    {{-- Campo de novo comentário --}}
    <div class="flex gap-2.5 items-start pt-1">
        <span class="w-7 h-7 rounded-full {{ $meBg ?? 'bg-indigo-500' }} text-white text-[10px] lato-bold flex items-center justify-center shrink-0 mt-0.5">
            {{ $meInit ?? '?' }}
        </span>
        <div class="flex-1 flex gap-2 items-start">
            <input x-model="text" type="text"
                   placeholder="Escreva um comentário…"
                   maxlength="1000"
                   @keydown.enter.prevent="send()"
                   class="flex-1 px-3 py-2 text-xs border border-slate-200 dark:border-slate-700 rounded-xl
                          bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-200
                          focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular placeholder-slate-400" />
            <button type="button" @click="send()"
                    class="px-3 py-2 text-[10px] lato-bold rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white
                           hover:from-blue-600 hover:to-indigo-700 transition shrink-0">
                Enviar
            </button>
        </div>
    </div>
</div>
