@php
    $isMyPost = $post->user_id === Auth::id();
    $imgCount = $post->images->count();
    $postType = $post->type ?? 'post';

    // Config de tipos
    $typeConfig = [
        'aviso'    => ['label' => 'Aviso',    'icon' => 'megaphone',      'cls' => 'bg-orange-100 dark:bg-orange-900/30 text-orange-600 dark:text-orange-400'],
        'evento'   => ['label' => 'Evento',   'icon' => 'calendar',       'cls' => 'bg-purple-100 dark:bg-purple-900/30 text-purple-600 dark:text-purple-400'],
        'discussao'=> ['label' => 'Discussão','icon' => 'message-circle',  'cls' => 'bg-blue-100 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400'],
    ];

    // RSVP do usuário atual
    $myRsvp     = null;
    $rsvpCounts = ['going' => 0, 'not_going' => 0, 'maybe' => 0];
    if ($postType === 'evento' && $post->rsvps) {
        foreach ($post->rsvps as $r) {
            if ($r->user_id === Auth::id()) $myRsvp = $r->status;
            if (isset($rsvpCounts[$r->status])) $rsvpCounts[$r->status]++;
        }
    }
@endphp

<div x-data="{
    rx: '{{ $myRx ?? '' }}',
    lk: {{ $post->like_count ?? 0 }}, ht: {{ $post->heart_count ?? 0 }}, cl: {{ $post->clap_count ?? 0 }},
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
        $wire.toggleReaction({{ $post->id }}, type);
    }
}">
    {{-- Badge de tipo --}}
    @if (isset($typeConfig[$postType]))
        @php $tc = $typeConfig[$postType]; @endphp
        <div class="mb-3">
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] lato-bold {{ $tc['cls'] }}">
                <x-dynamic-component :component="'lucide-'.$tc['icon']" class="w-3 h-3" />
                {{ $tc['label'] }}
            </span>
        </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start gap-3 mb-3">
        <span class="w-9 h-9 rounded-full {{ commBg($authorName) }} text-white text-xs lato-bold flex items-center justify-center shrink-0 mt-0.5">
            {{ commInit($authorName) }}
        </span>
        <div class="flex-1 min-w-0">
            <p class="text-sm lato-bold text-slate-800 dark:text-slate-100 truncate">{{ $authorName }}</p>
            <p class="text-xs text-slate-400 lato-regular">
                {{ $post->created_at->diffForHumans() }}
                @if ($post->edited_at) · <span class="italic">editado</span> @endif
            </p>
        </div>
        {{-- Menu 3 pontos --}}
        @if ($isMyPost || $isAdmin)
            <div class="relative shrink-0" x-data="{ open: false }" @click.away="open=false">
                <button @click="open=!open" type="button"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                    <x-lucide-more-horizontal class="w-4 h-4" />
                </button>
                <div x-show="open" x-transition
                     class="absolute right-0 mt-1 w-40 bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 rounded-xl shadow-lg py-1 z-20">
                    @if ($isAdmin)
                        <button wire:click="togglePin({{ $post->id }})" @click="open=false" type="button"
                                class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                            <x-lucide-pin class="w-3.5 h-3.5 {{ $pinned ? 'text-indigo-500' : '' }}" />
                            {{ $pinned ? 'Desafixar' : 'Fixar post' }}
                        </button>
                    @endif
                    @if ($isMyPost)
                        <button wire:click="openEditPost({{ $post->id }})" @click="open=false" type="button"
                                class="w-full text-left px-3 py-2 text-xs text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-700 flex items-center gap-2 cursor-pointer">
                            <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
                        </button>
                    @endif
                    @if ($isMyPost || $isAdmin)
                        <button wire:click="confirmDeletePost({{ $post->id }})" @click="open=false" type="button"
                                class="w-full text-left px-3 py-2 text-xs text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 flex items-center gap-2 cursor-pointer disabled:opacity-60"
                            wire:loading.attr="disabled">
                            <span wire:loading.remove wire:target="confirmDeletePost" class="flex items-center gap-1.5">
                                <x-lucide-trash-2 class="w-3.5 h-3.5" /> Excluir
                            </span>
                            <span wire:loading wire:target="confirmDeletePost" class="flex items-center gap-1.5">
                                <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                            </span>
                        </button>
                    @endif
                </div>
            </div>
        @endif
    </div>

    {{-- Card de evento --}}
    @if ($postType === 'evento' && $post->event_date)
        <div class="mb-3 rounded-xl border border-purple-200 dark:border-purple-700/50 bg-purple-50 dark:bg-purple-900/10 p-3">
            <div class="flex items-start gap-3">
                <div class="shrink-0 w-12 h-12 rounded-xl bg-purple-100 dark:bg-purple-900/30 flex flex-col items-center justify-center text-purple-600 dark:text-purple-400">
                    <span class="text-[10px] lato-bold uppercase">{{ $post->event_date->translatedFormat('M') }}</span>
                    <span class="text-lg lato-bold leading-tight">{{ $post->event_date->format('d') }}</span>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs lato-bold text-purple-700 dark:text-purple-300">
                        {{ $post->event_date->translatedFormat('l, d \d\e F') }} · {{ $post->event_date->format('H:i') }}
                        @if ($post->event_ends_at)
                            – {{ $post->event_ends_at->format('H:i') }}
                        @endif
                    </p>
                    @if ($post->event_location)
                        <p class="text-[11px] text-purple-500 dark:text-purple-400 mt-0.5 flex items-center gap-1">
                            <x-lucide-map-pin class="w-3 h-3 shrink-0" /> {{ $post->event_location }}
                        </p>
                    @endif
                    {{-- Contagem RSVP --}}
                    <div class="flex items-center gap-3 mt-2 text-[10px] text-slate-500 dark:text-slate-400">
                        @if ($rsvpCounts['going'] > 0)
                            <span class="flex items-center gap-1 text-green-600 dark:text-green-400">
                                <x-lucide-check-circle class="w-3 h-3" /> {{ $rsvpCounts['going'] }} {{ $rsvpCounts['going'] === 1 ? 'vai' : 'vão' }}
                            </span>
                        @endif
                        @if ($rsvpCounts['maybe'] > 0)
                            <span class="flex items-center gap-1 text-amber-600 dark:text-amber-400">
                                <x-lucide-help-circle class="w-3 h-3" /> {{ $rsvpCounts['maybe'] }} talvez
                            </span>
                        @endif
                        @if ($rsvpCounts['not_going'] > 0)
                            <span class="flex items-center gap-1 text-slate-400">
                                <x-lucide-x-circle class="w-3 h-3" /> {{ $rsvpCounts['not_going'] }} não vai
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Botões RSVP --}}
            <div class="flex items-center gap-2 mt-3 pt-3 border-t border-purple-200 dark:border-purple-700/50">
                <span class="text-[10px] text-purple-500 dark:text-purple-400 lato-bold mr-1">Você vai?</span>
                @php $rsvpBtns = [['going','Vou','check','green'],['maybe','Talvez','help-circle','amber'],['not_going','Não vou','x','slate']]; @endphp
                @foreach ($rsvpBtns as [$val, $lbl, $ico, $col])
                    <button wire:click="rsvp({{ $post->id }}, '{{ $val }}')" type="button"
                            class="flex items-center gap-1 px-2.5 py-1 rounded-full text-[10px] lato-bold transition cursor-pointer
                                   {{ $myRsvp === $val
                                       ? "bg-{$col}-100 dark:bg-{$col}-900/30 text-{$col}-600 dark:text-{$col}-400 ring-1 ring-{$col}-300 dark:ring-{$col}-700"
                                       : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-500 hover:border-purple-300' }}">
                        <x-dynamic-component :component="'lucide-'.$ico" class="w-3 h-3" />
                        {{ $lbl }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Conteúdo --}}
    @if ($post->content)
        <div class="text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed mb-3 whitespace-pre-line">
            {!! nl2br(e($post->content)) !!}
        </div>
    @endif

    {{-- Imagens --}}
    @if ($imgCount > 0)
        <div class="rounded-xl overflow-hidden mb-3 {{ $imgCount > 1 ? 'grid grid-cols-2 gap-1' : '' }}">
            @foreach ($post->images->take(4) as $idx => $img)
                <div class="relative bg-slate-100 dark:bg-slate-900 {{ $imgCount === 1 ? '' : 'aspect-square' }}">
                    <img src="{{ Storage::url($img->path) }}"
                         class="w-full h-full {{ $imgCount === 1 ? 'max-h-80 object-contain' : 'object-cover' }}" />
                    @if ($idx === 3 && $imgCount > 4)
                        <div class="absolute inset-0 bg-black/60 flex items-center justify-center text-white text-xl lato-bold">
                            +{{ $imgCount - 4 }}
                        </div>
                    @endif
                </div>
            @endforeach
        </div>
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
            @if ($pollClosed || (count($myVoteIds) > 0))
                @foreach ($poll->options as $opt)
                    @php
                        $votes  = $opt->votes->count();
                        $pct    = $totalVotes > 0 ? round($votes / $totalVotes * 100) : 0;
                        $voted  = in_array($opt->id, $myVoteIds);
                    @endphp
                    <div class="relative">
                        <div class="h-8 rounded-lg {{ $voted ? 'bg-indigo-100 dark:bg-indigo-900/30 border border-indigo-300 dark:border-indigo-600' : 'bg-slate-100 dark:bg-slate-700' }} overflow-hidden">
                            <div class="h-full bg-indigo-200 dark:bg-indigo-800/60 transition-all duration-500" style="width: {{ $pct }}%"></div>
                        </div>
                        <div class="absolute inset-0 flex items-center justify-between px-3">
                            <span class="text-xs {{ $voted ? 'lato-bold text-indigo-700 dark:text-indigo-300' : 'lato-regular text-slate-600 dark:text-slate-300' }}">
                                {{ $opt->label }}
                                @if ($voted) <x-lucide-check class="w-3 h-3 inline" /> @endif
                            </span>
                            <span class="text-xs text-slate-500 lato-bold">{{ $pct }}%</span>
                        </div>
                    </div>
                @endforeach
            @else
                @foreach ($poll->options as $opt)
                    <button wire:click="votePoll({{ $opt->id }})" type="button"
                            class="w-full text-left px-3 py-2 text-sm rounded-lg border border-slate-200 dark:border-slate-700
                                   hover:border-indigo-400 hover:bg-indigo-50 dark:hover:bg-indigo-900/20 transition lato-regular
                                   text-slate-700 dark:text-slate-200 cursor-pointer">
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

    {{-- Action bar --}}
    <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-700">
        <div class="flex items-center gap-1">
            {{-- Like --}}
            <button type="button" @click="bump('like')"
                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition cursor-pointer"
                    x-bind:class="rx==='like' ? 'bg-indigo-100 dark:bg-indigo-900/30 text-indigo-600' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     x-bind:fill="rx==='like' ? 'currentColor' : 'none'">
                    <path d="M7 10v12"/><path d="M15 5.88 14 10h5.83a2 2 0 0 1 1.92 2.56l-2.33 8A2 2 0 0 1 17.5 22H4a2 2 0 0 1-2-2v-8a2 2 0 0 1 2-2h2.76a2 2 0 0 0 1.79-1.11L12 2a3.13 3.13 0 0 1 3 3.88Z"/>
                </svg>
                <span x-text="lk > 0 ? lk : ''"></span>
            </button>
            {{-- Heart --}}
            <button type="button" @click="bump('heart')"
                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition cursor-pointer"
                    x-bind:class="rx==='heart' ? 'bg-red-100 dark:bg-red-900/30 text-red-500' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24"
                     x-bind:fill="rx==='heart' ? 'currentColor' : 'none'" x-bind:stroke="rx==='heart' ? 'none' : 'currentColor'" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M19 14c1.49-1.46 3-3.21 3-5.5A5.5 5.5 0 0 0 16.5 3c-1.76 0-3 .5-4.5 2-1.5-1.5-2.74-2-4.5-2A5.5 5.5 0 0 0 2 8.5c0 2.3 1.5 4.05 3 5.5l7 7Z"/>
                </svg>
                <span x-text="ht > 0 ? ht : ''"></span>
            </button>
            {{-- Clap --}}
            <button type="button" @click="bump('clap')"
                    class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs lato-bold transition cursor-pointer"
                    x-bind:class="rx==='clap' ? 'bg-amber-100 dark:bg-amber-900/30 text-amber-600' : 'text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700'">
                <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"
                     x-bind:fill="rx==='clap' ? 'currentColor' : 'none'">
                    <path d="M18 8.5a4.5 4.5 0 0 0-4.5-4.5 4.5 4.5 0 0 0-4.5 4.5v4.5a4.5 4.5 0 0 0 9 0V8.5z"/><path d="M12 4V2M8 5l-1-2M16 5l1-2"/>
                </svg>
                <span x-text="cl > 0 ? cl : ''"></span>
            </button>
        </div>
        <button wire:click="toggleComments({{ $post->id }})" type="button"
                class="flex items-center gap-1.5 px-2.5 py-1.5 rounded-xl text-xs text-slate-500 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
            <x-lucide-message-circle class="w-4 h-4" />
            {{ $post->comments_count > 0 ? $post->comments_count : '' }}
        </button>
    </div>

    {{-- Comentários --}}
    @if (in_array($post->id, $openComments))
        @include('livewire.pages.communities._comments', [
            'postId'   => $post->id,
            'comments' => $post->comments,
        ])
    @endif
</div>
