{{-- ── Modal: Histórico de Check-ins ─────────────────────────────────── --}}
@if ($historyModal && $this->historyKr)
<div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4"
     x-data x-effect="document.body.style.overflow = 'hidden'">
    {{-- Overlay --}}
    <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="$set('historyModal', false)"></div>

    {{-- Card --}}
    <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-lg
                flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10
                max-h-[85vh] sm:max-h-[80vh] overflow-hidden">

        {{-- Header --}}
        <div class="flex-shrink-0 px-5 pt-5 pb-4 border-b border-slate-100 dark:border-slate-700">
            <div class="flex items-start justify-between gap-3">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 shadow-sm
                                bg-gradient-to-br from-slate-500 to-slate-700">
                        <x-lucide-clock class="w-4 h-4 text-white" />
                    </div>
                    <div class="min-w-0">
                        <h3 class="text-base font-bold text-slate-800 dark:text-white leading-tight">Histórico de Check-ins</h3>
                        <p class="text-[11px] text-slate-400 mt-0.5 truncate max-w-[220px] sm:max-w-xs">{{ $this->historyKr->title }}</p>
                    </div>
                </div>
                <button wire:click="$set('historyModal', false)"
                    class="w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                           hover:text-slate-700 dark:hover:text-slate-200 hover:bg-slate-100 dark:hover:bg-slate-700
                           transition cursor-pointer shrink-0">
                    <x-lucide-x class="w-4 h-4" />
                </button>
            </div>
        </div>

        {{-- Body (scrollável) --}}
        <div class="flex-1 overflow-y-auto px-5 py-4">
            @if ($this->historyKr->checkins->isEmpty())
                <div class="text-center py-12">
                    <div class="w-14 h-14 rounded-2xl bg-slate-100 dark:bg-slate-700 flex items-center justify-center mx-auto mb-3">
                        <x-lucide-clock class="w-6 h-6 text-slate-400 dark:text-slate-500" />
                    </div>
                    <p class="text-sm font-semibold text-slate-500 dark:text-slate-400">Nenhum check-in registrado</p>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">Os check-ins aparecerão aqui conforme forem registrados.</p>
                </div>
            @else
                {{-- Timeline --}}
                <div class="relative">
                    {{-- Linha vertical --}}
                    <div class="absolute left-[19px] top-0 bottom-0 w-px bg-slate-100 dark:bg-slate-700"></div>

                    <div class="space-y-4">
                        @foreach ($this->historyKr->checkins->sortByDesc('created_at') as $ci)
                            <div class="flex items-start gap-4">
                                {{-- Avatar com inicial --}}
                                <div class="w-10 h-10 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600
                                            flex items-center justify-center shrink-0 z-10 shadow-sm ring-2 ring-white dark:ring-slate-800">
                                    <span class="text-xs font-bold text-white">
                                        {{ strtoupper(substr($ci->user?->name ?? '?', 0, 1)) }}
                                    </span>
                                </div>

                                {{-- Card do check-in --}}
                                <div class="flex-1 min-w-0 bg-slate-50 dark:bg-slate-700/50 rounded-xl p-3.5 border border-slate-100 dark:border-slate-700">
                                    <div class="flex items-center justify-between gap-2 mb-2">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 truncate">
                                            {{ $ci->user?->name ?? '—' }}
                                        </p>
                                        <time class="text-[10px] text-slate-400 shrink-0">{{ $ci->created_at->format('d/m/y H:i') }}</time>
                                    </div>

                                    <div class="flex items-center gap-2 flex-wrap">
                                        <span class="text-base font-bold text-slate-800 dark:text-white">
                                            {{ number_format($ci->new_value, 2, ',', '.') }}
                                        </span>
                                        <span class="inline-flex items-center gap-0.5 text-xs font-semibold px-2 py-0.5 rounded-lg
                                            {{ $ci->delta >= 0
                                                ? 'bg-emerald-50 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400'
                                                : 'bg-rose-50 text-rose-700 dark:bg-rose-900/30 dark:text-rose-400' }}">
                                            {{ $ci->delta >= 0 ? '↑' : '↓' }} {{ $ci->delta_label }}
                                        </span>
                                        @if ($ci->confidence)
                                            <span class="text-[10px] text-slate-400 font-medium">
                                                confiança {{ $ci->confidence }}/10
                                            </span>
                                        @endif
                                    </div>

                                    @if ($ci->comment)
                                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-2 leading-relaxed border-t border-slate-200 dark:border-slate-600 pt-2">
                                            {{ $ci->comment }}
                                        </p>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

        {{-- Footer --}}
        <div class="flex-shrink-0 flex justify-end px-5 py-4
                    border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/80">
            <button wire:click="$set('historyModal', false)"
                class="px-5 py-2.5 text-sm font-semibold text-slate-600 dark:text-slate-300
                       border border-slate-200 dark:border-slate-600 rounded-xl
                       hover:bg-white dark:hover:bg-slate-700 transition cursor-pointer">
                Fechar
            </button>
        </div>
    </div>
</div>
@endif
