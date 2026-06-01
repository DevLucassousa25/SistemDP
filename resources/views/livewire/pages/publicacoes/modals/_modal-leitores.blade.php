    {{-- ════════════════════════════════════════════════════════════════
         MODAL — QUEM LEU
    ════════════════════════════════════════════════════════════════ --}}
    @if ($readersModal && $this->readersPost)
        @php
            $rp = $this->readersPost;
            $readers = $rp->reads->sortByDesc('read_at');
            $nonReaders = $this->nonReaders;
        @endphp
        <div class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center p-4">
            <div class="bg-white dark:bg-slate-800 rounded-2xl border border-slate-200 dark:border-slate-700 shadow-2xl w-full max-w-2xl max-h-[85vh] flex flex-col overflow-hidden">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <div>
                        <h3 class="text-sm lato-black text-slate-800 dark:text-white flex items-center gap-2">
                            <x-lucide-users class="w-4 h-4 text-indigo-500" />
                            Relatório de leituras
                        </h3>
                        <p class="text-xs text-slate-400 lato-regular mt-0.5">{{ Str::limit($rp->title, 60) }}</p>
                    </div>
                    <button wire:click="closeReadersReport" type="button" class="p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-400 hover:text-slate-600 transition cursor-pointer">
                        <x-lucide-x class="w-5 h-5" />
                    </button>
                </div>

                {{-- Métricas rápidas --}}
                @php
                    $avgHours = $readers->filter(fn($r) => $rp->published_at && $r->read_at->greaterThanOrEqualTo($rp->published_at))
                        ->map(fn($r) => $r->read_at->diffInHours($rp->published_at))
                        ->average();
                    $avgDisplay = $avgHours !== null
                        ? ($avgHours < 1 ? '< 1h' : ($avgHours < 24 ? round($avgHours) . 'h' : round($avgHours / 24) . 'd'))
                        : '—';
                @endphp
                <div class="grid grid-cols-4 gap-3 px-6 py-4 border-b border-slate-100 dark:border-slate-700 shrink-0">
                    <div class="text-center">
                        <p class="text-2xl lato-black text-indigo-600 dark:text-indigo-400">{{ $readers->count() }}</p>
                        <p class="text-[10px] text-slate-400 lato-regular mt-0.5">Leitores únicos</p>
                    </div>
                    @if($rp->is_mandatory_read)
                        <div class="text-center">
                            <p class="text-2xl lato-black text-emerald-600 dark:text-emerald-400">{{ $readers->whereNotNull('confirmed_at')->count() }}</p>
                            <p class="text-[10px] text-slate-400 lato-regular mt-0.5">Confirmaram</p>
                        </div>
                    @else
                        <div class="text-center">
                            <p class="text-2xl lato-black text-slate-800 dark:text-white">{{ $rp->views_count }}</p>
                            <p class="text-[10px] text-slate-400 lato-regular mt-0.5">Visualizações</p>
                        </div>
                    @endif
                    <div class="text-center">
                        <p class="text-2xl lato-black text-amber-500 dark:text-amber-400">{{ $nonReaders->count() }}</p>
                        <p class="text-[10px] text-slate-400 lato-regular mt-0.5">Não leram</p>
                    </div>
                    <div class="text-center">
                        <p class="text-2xl lato-black text-slate-600 dark:text-slate-300">{{ $avgDisplay }}</p>
                        <p class="text-[10px] text-slate-400 lato-regular mt-0.5">Tempo médio</p>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto">
                    <div x-data="{ tab: 'readers' }" class="h-full flex flex-col">

                        {{-- Sub-tabs --}}
                        <div class="flex border-b border-slate-100 dark:border-slate-700 px-6 shrink-0">
                            <button @click="tab='readers'" type="button"
                                    x-bind:class="tab==='readers' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-400 hover:text-slate-600'"
                                    class="px-4 py-3 text-xs lato-bold border-b-2 transition cursor-pointer">
                                Leram ({{ $readers->count() }})
                            </button>
                            <button @click="tab='nonreaders'" type="button"
                                    x-bind:class="tab==='nonreaders' ? 'border-indigo-500 text-indigo-600 dark:text-indigo-400' : 'border-transparent text-slate-400 hover:text-slate-600'"
                                    class="px-4 py-3 text-xs lato-bold border-b-2 transition cursor-pointer">
                                Não leram ({{ $nonReaders->count() }})
                            </button>
                        </div>

                        {{-- Leram --}}
                        <div x-show="tab==='readers'" class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-700/50">
                            @forelse ($readers as $read)
                                <div class="flex items-center justify-between px-6 py-3">
                                    <div class="flex items-center gap-3">
                                        @php
                                            $rParts = explode(' ', trim($read->user?->name ?? '?'));
                                            $rInit = strtoupper(substr($rParts[0],0,1).(isset($rParts[1])?substr($rParts[1],0,1):''));
                                            $rPal = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-500','bg-amber-500'];
                                            $rBg = $rPal[abs(crc32($read->user?->name ?? '')) % count($rPal)];
                                        @endphp
                                        <span class="w-8 h-8 rounded-full {{ $rBg }} text-white text-xs lato-bold flex items-center justify-center shrink-0">{{ $rInit }}</span>
                                        <div>
                                            <p class="text-xs lato-bold text-slate-700 dark:text-slate-200">{{ $read->user?->name ?? '—' }}</p>
                                            <p class="text-[10px] text-slate-400 lato-regular">{{ $read->user?->department?->name ?? '—' }}</p>
                                        </div>
                                    </div>
                                    <div class="text-right">
                                        <p class="text-[10px] text-slate-400 lato-regular">{{ $read->read_at->format('d/m/Y H:i') }}</p>
                                        @if($rp->is_mandatory_read)
                                            @if($read->confirmed_at)
                                                <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded-full text-[9px] lato-bold bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-400">
                                                    <x-lucide-check class="w-2 h-2" /> Confirmado
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-1.5 py-0.5 rounded-full text-[9px] lato-bold bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-400">
                                                    Aguardando confirmação
                                                </span>
                                            @endif
                                        @endif
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <x-lucide-eye-off class="w-8 h-8 text-slate-200 dark:text-slate-600 mb-2" />
                                    <p class="text-xs text-slate-400 lato-regular">Ninguém leu ainda.</p>
                                </div>
                            @endforelse
                        </div>

                        {{-- Não leram --}}
                        <div x-show="tab==='nonreaders'" class="flex-1 overflow-y-auto divide-y divide-slate-50 dark:divide-slate-700/50">
                            @forelse ($nonReaders as $u)
                                <div class="flex items-center gap-3 px-6 py-3">
                                    @php
                                        $nParts = explode(' ', trim($u->name));
                                        $nInit = strtoupper(substr($nParts[0],0,1).(isset($nParts[1])?substr($nParts[1],0,1):''));
                                        $nPal = ['bg-slate-300','bg-slate-400'];
                                        $nBg = $nPal[abs(crc32($u->name)) % count($nPal)];
                                    @endphp
                                    <span class="w-8 h-8 rounded-full {{ $nBg }} text-white text-xs lato-bold flex items-center justify-center shrink-0">{{ $nInit }}</span>
                                    <div>
                                        <p class="text-xs lato-bold text-slate-600 dark:text-slate-400">{{ $u->name }}</p>
                                        <p class="text-[10px] text-slate-400 lato-regular">{{ $u->department?->name ?? '—' }} · {{ $u->accessProfile?->name ?? '—' }}</p>
                                    </div>
                                </div>
                            @empty
                                <div class="flex flex-col items-center justify-center py-12 text-center">
                                    <x-lucide-check-circle class="w-8 h-8 text-emerald-300 dark:text-emerald-600 mb-2" />
                                    <p class="text-xs text-emerald-600 dark:text-emerald-400 lato-bold">Todos leram esta publicação!</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="shrink-0 px-6 py-3 border-t border-slate-100 dark:border-slate-700 flex items-center justify-between gap-2">
                    <button wire:click="exportReadersReport({{ $rp->id }})" type="button"
                            class="flex items-center gap-2 px-4 py-2 rounded-xl text-sm lato-bold bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/40 transition cursor-pointer">
                        <x-lucide-download class="w-3.5 h-3.5" /> Exportar CSV
                    </button>
                    <button wire:click="closeReadersReport" type="button"
                            class="px-4 py-2 rounded-xl text-sm lato-bold bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-600 transition cursor-pointer">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif
