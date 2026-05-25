<div
    wire:poll.15000ms="verificarNovas"
    x-data="{ open: false }"
    x-on:click.outside="open = false"
    class="relative"
>
    {{-- ── Botão Sino ─────────────────────────────────────────────── --}}
    <button
        x-on:click="open = !open"
        type="button"
        class="relative flex items-center justify-center w-9 h-9 rounded-lg text-slate-500 dark:text-slate-400
               hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
        aria-label="Notificações"
    >
        <x-lucide-bell class="w-5 h-5" />

        @if($this->naoLidas > 0)
            <span class="absolute -top-1 -right-1 flex items-center justify-center
                         min-w-[18px] h-[18px] px-1 text-[10px] font-semibold
                         text-white bg-red-500 rounded-full leading-none">
                {{ $this->naoLidas > 99 ? '99+' : $this->naoLidas }}
            </span>
        @endif
    </button>

    {{-- ── Dropdown ────────────────────────────────────────────────── --}}
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-150"
        x-transition:enter-start="opacity-0 scale-95 translate-y-1"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-100"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 translate-y-1"
        class="absolute right-0 mt-2 w-80 sm:w-96 origin-top-right z-50
               bg-white dark:bg-slate-800 rounded-2xl shadow-xl
               ring-1 ring-slate-200 dark:ring-slate-700 overflow-hidden"
        style="display: none;"
    >
        {{-- Header do dropdown --}}
        <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-center gap-2">
                <span class="w-6 h-6 rounded-md bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center shrink-0">
                    <x-lucide-bell class="w-3 h-3 text-white" />
                </span>
                <span class="text-sm lato-bold text-slate-800 dark:text-slate-100">Notificações</span>
                @if($this->naoLidas > 0)
                    <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[10px] font-semibold bg-red-100 dark:bg-red-900/30 text-red-600 dark:text-red-400">
                        {{ $this->naoLidas }}
                    </span>
                @endif
            </div>

            <div class="flex items-center gap-1">
                @if($this->naoLidas > 0)
                    <button
                        wire:click="marcarTodasLidas"
                        type="button"
                        title="Marcar todas como lidas"
                        class="p-1.5 rounded-lg text-slate-400 hover:text-violet-600 dark:hover:text-violet-400
                               hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
                    >
                        <x-lucide-check-check class="w-3.5 h-3.5" />
                    </button>
                @endif

                <button
                    wire:click="limparLidas"
                    type="button"
                    title="Limpar lidas"
                    class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 dark:hover:text-red-400
                           hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
                >
                    <x-lucide-trash-2 class="w-3.5 h-3.5" />
                </button>
            </div>
        </div>

        {{-- Lista de notificações --}}
        <div class="max-h-[420px] overflow-y-auto divide-y divide-slate-100 dark:divide-slate-700/60">

            @forelse($this->notificacoes as $notif)
                @php
                    $d = $notif->data;
                    $lida = !is_null($notif->read_at);

                    $colorMap = [
                        'blue'   => 'from-blue-500 to-blue-600',
                        'green'  => 'from-green-500 to-emerald-600',
                        'red'    => 'from-red-500 to-rose-600',
                        'indigo' => 'from-indigo-500 to-indigo-600',
                        'violet' => 'from-violet-500 to-purple-600',
                        'teal'   => 'from-teal-500 to-cyan-600',
                        'amber'  => 'from-amber-500 to-orange-500',
                        'rose'   => 'from-rose-500 to-pink-600',
                    ];
                    $gradient = $colorMap[$d['color'] ?? 'blue'] ?? 'from-blue-500 to-blue-600';
                @endphp

                <div class="flex items-start gap-3 px-4 py-3 {{ $lida ? 'opacity-60' : 'bg-violet-50/30 dark:bg-violet-900/10' }}
                            hover:bg-slate-50 dark:hover:bg-slate-700/40 transition group">

                    {{-- Ícone da notificação --}}
                    <div class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $gradient }} flex items-center justify-center shrink-0 mt-0.5">
                        <span class="w-4 h-4 text-white flex items-center justify-center">
                            @switch($d['icon'] ?? 'bell')
                                @case('file-text')       <x-lucide-file-text class="w-3.5 h-3.5" /> @break
                                @case('clipboard-check') <x-lucide-clipboard-check class="w-3.5 h-3.5" /> @break
                                @case('megaphone')       <x-lucide-megaphone class="w-3.5 h-3.5" /> @break
                                @case('calendar')        <x-lucide-calendar class="w-3.5 h-3.5" /> @break
                                @case('message-circle')  <x-lucide-message-circle class="w-3.5 h-3.5" /> @break
                                @case('bar-chart-2')     <x-lucide-bar-chart-2 class="w-3.5 h-3.5" /> @break
                                @case('trending-up')     <x-lucide-trending-up class="w-3.5 h-3.5" /> @break
                                @case('check-square')    <x-lucide-check-square class="w-3.5 h-3.5" /> @break
                                @case('target')          <x-lucide-target class="w-3.5 h-3.5" /> @break
                                @case('book-open')       <x-lucide-book-open class="w-3.5 h-3.5" /> @break
                                @case('heart')           <x-lucide-heart class="w-3.5 h-3.5" /> @break
                                @default                 <x-lucide-bell class="w-3.5 h-3.5" /> @break
                            @endswitch
                        </span>
                    </div>

                    {{-- Conteúdo --}}
                    <div class="flex-1 min-w-0">
                        @if(!empty($d['url']))
                            <a
                                href="{{ $d['url'] }}"
                                wire:click="marcarLida('{{ $notif->id }}')"
                                class="block"
                            >
                        @else
                            <div wire:click="marcarLida('{{ $notif->id }}')" class="cursor-pointer">
                        @endif

                            <p class="text-xs lato-bold text-slate-800 dark:text-slate-100 truncate leading-snug">
                                {{ $d['title'] ?? 'Notificação' }}
                            </p>
                            <p class="text-xs lato-regular text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2 leading-snug">
                                {{ $d['message'] ?? '' }}
                            </p>
                            <p class="text-[10px] text-slate-400 dark:text-slate-500 mt-1">
                                {{ $notif->created_at->diffForHumans() }}
                            </p>

                        @if(!empty($d['url']))
                            </a>
                        @else
                            </div>
                        @endif
                    </div>

                    {{-- Ações --}}
                    <div class="flex flex-col items-center gap-1 shrink-0 opacity-0 group-hover:opacity-100 transition">
                        @if(!$lida)
                            <button
                                wire:click="marcarLida('{{ $notif->id }}')"
                                type="button"
                                title="Marcar como lida"
                                class="p-1 rounded-lg text-slate-400 hover:text-violet-600 dark:hover:text-violet-400
                                       hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
                            >
                                <x-lucide-check class="w-3 h-3" />
                            </button>
                        @endif

                        <button
                            wire:click="excluir('{{ $notif->id }}')"
                            type="button"
                            title="Excluir"
                            class="p-1 rounded-lg text-slate-400 hover:text-red-500 dark:hover:text-red-400
                                   hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer"
                        >
                            <x-lucide-x class="w-3 h-3" />
                        </button>
                    </div>

                    {{-- Indicador de não lida --}}
                    @if(!$lida)
                        <div class="w-1.5 h-1.5 rounded-full bg-violet-500 shrink-0 mt-2"></div>
                    @endif
                </div>

            @empty
                <div class="flex flex-col items-center justify-center py-10 text-center px-4">
                    <div class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mb-3">
                        <x-lucide-bell-off class="w-5 h-5 text-slate-400 dark:text-slate-500" />
                    </div>
                    <p class="text-sm lato-bold text-slate-600 dark:text-slate-400">Nenhuma notificação</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Você está em dia!</p>
                </div>
            @endforelse

        </div>

        {{-- Footer do dropdown --}}
        @if($this->notificacoes->isNotEmpty())
            <div class="px-4 py-2.5 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/30">
                <p class="text-[10px] text-center text-slate-400 dark:text-slate-500">
                    Mostrando as últimas {{ $this->notificacoes->count() }} notificações
                </p>
            </div>
        @endif

    </div>
</div>
