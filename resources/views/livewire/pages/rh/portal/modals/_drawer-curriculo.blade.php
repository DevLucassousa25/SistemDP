@if ($currDrawer && $this->curriculoDrawer)
@php
    $dc = $this->curriculoDrawer;
    $dcIn = collect(explode(' ', $dc->nome))->map(fn($w) => strtoupper($w[0] ?? ''))->take(2)->join('');
    $dcColors = ['bg-indigo-500','bg-indigo-600','bg-blue-500','bg-teal-500','bg-emerald-500','bg-pink-500','bg-rose-500','bg-amber-500'];
    $dcBg = $dcColors[abs(crc32($dc->nome)) % count($dcColors)];
    [$dcSCls, $dcSLbl] = $statusCurrMap[$dc->status] ?? $statusCurrMap['inativo'];
@endphp
<div class="fixed inset-0 z-40 bg-black/40 backdrop-blur-sm" wire:click="$set('currDrawer', false)"></div>
<div class="fixed inset-y-0 right-0 z-50 w-full sm:w-[480px] lg:w-[560px] bg-white dark:bg-slate-800 shadow-2xl overflow-y-auto">
    <div class="sticky top-0 z-10 bg-white dark:bg-slate-800 border-b border-slate-200 dark:border-slate-700 px-6 py-4">
        <div class="flex items-start gap-4">
            <div class="w-12 h-12 rounded-xl {{ $dcBg }} flex items-center justify-center text-white lato-black text-base shrink-0">{{ $dcIn }}</div>
            <div class="flex-1 min-w-0">
                <div class="flex items-center gap-2 flex-wrap">
                    <h2 class="text-base lato-black text-slate-800 dark:text-white">{{ $dc->nome }}</h2>
                    <span class="px-2 py-0.5 rounded-full text-[11px] lato-bold {{ $dcSCls }}">{{ $dcSLbl }}</span>
                </div>
                <div class="flex flex-wrap gap-3 mt-1 text-xs text-slate-500 dark:text-slate-400 lato-regular">
                    @if ($dc->email) <span><x-lucide-mail class="w-3 h-3 inline" /> {{ $dc->email }}</span> @endif
                    @if ($dc->telefone) <span><x-lucide-phone class="w-3 h-3 inline" /> {{ $dc->telefone }}</span> @endif
                    @if ($dc->cidade || $dc->estado) <span><x-lucide-map-pin class="w-3 h-3 inline" /> {{ trim(($dc->cidade ?? '').', '.($dc->estado ?? ''), ', ') }}</span> @endif
                </div>
            </div>
            <button wire:click="$set('currDrawer', false)" type="button"
                    class="cursor-pointer p-2 rounded-xl text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-700 transition shrink-0">
                <x-lucide-x class="w-5 h-5" />
            </button>
        </div>
        <div class="flex items-center gap-2 mt-3">
            <button wire:click="openCurrEdit({{ $dc->id }})" type="button"
                    class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300 text-xs lato-bold hover:bg-slate-200 dark:hover:bg-slate-600 transition">
                <x-lucide-pencil class="w-3.5 h-3.5" /> Editar
            </button>
            <button wire:click="toggleFavorito({{ $dc->id }})" type="button"
                    class="cursor-pointer flex items-center gap-1.5 px-3 py-1.5 rounded-xl {{ $dc->status === 'favorito' ? 'bg-amber-100 text-amber-600 dark:bg-amber-900/30' : 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300' }} text-xs lato-bold transition">
                <x-lucide-star class="w-3.5 h-3.5" /> {{ $dc->status === 'favorito' ? 'Favoritado' : 'Favoritar' }}
            </button>
            @if ($dc->arquivo_path)
            <a href="{{ Storage::url($dc->arquivo_path) }}" target="_blank"
               class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-blue-50 dark:bg-blue-900/30 text-blue-600 dark:text-blue-400 text-xs lato-bold hover:bg-blue-100 dark:hover:bg-blue-900/50 transition">
                <x-lucide-download class="w-3.5 h-3.5" /> Baixar arquivo
            </a>
            @endif
        </div>
    </div>
    <div class="px-6 py-4 space-y-6">
        @if ($dc->resumo_profissional)
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Resumo Profissional</h3>
            <p class="text-sm text-slate-700 dark:text-slate-300 lato-regular leading-relaxed">{{ $dc->resumo_profissional }}</p>
        </div>
        @endif
        @if ($dc->experiencias && $dc->experiencias->count())
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Experiências</h3>
            <div class="relative pl-5 border-l-2 border-slate-200 dark:border-slate-700 space-y-4">
                @foreach ($dc->experiencias->sortByDesc('data_inicio') as $exp)
                <div class="relative">
                    <div class="absolute -left-[22px] top-1 w-3.5 h-3.5 rounded-full bg-blue-500 border-2 border-white dark:border-slate-800"></div>
                    <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $exp->cargo }}</p>
                    <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $exp->empresa }}</p>
                    <p class="text-xs text-slate-400 lato-regular mt-0.5">
                        {{ \Carbon\Carbon::parse($exp->data_inicio)->format('M/Y') }}
                        — {{ $exp->data_fim ? \Carbon\Carbon::parse($exp->data_fim)->format('M/Y') : 'Atual' }}
                    </p>
                    @if ($exp->descricao) <p class="text-xs text-slate-600 dark:text-slate-400 lato-regular mt-1 leading-relaxed">{{ $exp->descricao }}</p> @endif
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if ($dc->formacoes && $dc->formacoes->count())
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Formação Acadêmica</h3>
            <div class="space-y-3">
                @foreach ($dc->formacoes->sortByDesc('data_conclusao') as $form)
                <div class="flex items-start gap-3">
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 dark:bg-indigo-900/30 flex items-center justify-center shrink-0">
                        <x-lucide-graduation-cap class="w-4 h-4 text-indigo-500" />
                    </div>
                    <div>
                        <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $form->curso }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $form->instituicao }}</p>
                        <p class="text-xs text-slate-400 lato-regular">{{ $escLabels[$form->nivel] ?? ucfirst($form->nivel ?? '') }}@if ($form->data_conclusao) · {{ \Carbon\Carbon::parse($form->data_conclusao)->format('Y') }}@endif</p>
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        @if ($dc->habilidades && $dc->habilidades->count())
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Habilidades</h3>
            <div class="flex flex-wrap gap-2">
                @foreach ($dc->habilidades as $hab)
                <span class="px-2.5 py-1 rounded-full text-xs lato-bold bg-indigo-50 dark:bg-indigo-900/30 text-indigo-700 dark:text-indigo-300">
                    {{ $hab->nome }}@if ($hab->nivel) <span class="opacity-60 font-normal">({{ ucfirst($hab->nivel) }})</span>@endif
                </span>
                @endforeach
            </div>
        </div>
        @endif
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Tags</h3>
            <div class="flex flex-wrap gap-2 mb-3">
                @forelse ($dc->tags ?? [] as $tag)
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-indigo-50 dark:bg-indigo-900/30 text-indigo-600 dark:text-indigo-300 text-xs lato-bold">
                    {{ $tag->tag }}
                    <button wire:click="removeTag({{ $tag->id }})" type="button" class="cursor-pointer ml-0.5 text-indigo-400 hover:text-red-500 transition">
                        <x-lucide-x class="w-3 h-3" />
                    </button>
                </span>
                @empty
                <p class="text-xs text-slate-400 lato-regular">Nenhuma tag adicionada.</p>
                @endforelse
            </div>
            <div class="flex gap-2">
                <input type="text" wire:model="newTag" placeholder="Nova tag..."
                       wire:keydown.enter="addTag({{ $dc->id }})"
                       class="flex-1 px-3 py-2 text-sm border border-slate-200 dark:border-slate-700 rounded-xl bg-slate-50 dark:bg-slate-900 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-blue-400/30 lato-regular" />
                <button wire:click="addTag({{ $dc->id }})" type="button"
                        class="cursor-pointer px-3 py-2 rounded-xl bg-indigo-500 text-white text-sm lato-bold hover:bg-indigo-600 transition">
                    <x-lucide-plus class="w-4 h-4" />
                </button>
            </div>
        </div>
        @if ($dc->candidaturas && $dc->candidaturas->count())
        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-3">Candidaturas</h3>
            <div class="space-y-2">
                @foreach ($dc->candidaturas as $cand)
                <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-700">
                    <div>
                        <p class="text-sm lato-bold text-slate-800 dark:text-white">{{ $cand->vaga?->titulo ?? '—' }}</p>
                        <p class="text-xs text-slate-500 dark:text-slate-400 lato-regular">{{ $cand->etapa?->nome ?? '—' }}</p>
                    </div>
                    <span class="text-xs text-slate-400 lato-regular">{{ $cand->created_at?->format('d/m/Y') }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
        {{-- Conteúdo extraído do PDF (OCR) --}}
        @if ($dc->texto_ocr || $dc->arquivo_path)
        <div x-data="{ expandido: false }">
            <div class="flex items-center justify-between mb-2">
                <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <x-lucide-scan-text class="w-3.5 h-3.5" />
                    Conteúdo extraído do PDF
                </h3>
                <div class="flex items-center gap-2">
                    @if ($dc->ocr_processado_at)
                        <span class="text-[10px] text-slate-400 lato-regular">
                            Processado {{ $dc->ocr_processado_at->format('d/m/Y H:i') }}
                        </span>
                    @endif
                    <button wire:click="reprocessarOcr({{ $dc->id }})" type="button"
                            title="Reprocessar OCR"
                            class="cursor-pointer p-1 rounded-lg text-slate-400 hover:text-indigo-500 hover:bg-violet-50 dark:hover:bg-violet-900/20 transition">
                        <x-lucide-refresh-cw class="w-3.5 h-3.5" />
                    </button>
                </div>
            </div>

            @if ($dc->texto_ocr)
                {{-- Preview compacto com expandir --}}
                <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-900 overflow-hidden">
                    <div class="relative">
                        <pre :class="expandido ? 'max-h-96 overflow-y-auto' : 'max-h-40 overflow-hidden'"
                             class="text-xs text-slate-600 dark:text-slate-400 lato-regular leading-relaxed p-4 whitespace-pre-wrap transition-all duration-300">{{ $dc->texto_ocr }}</pre>
                        {{-- Fade overlay quando compacto --}}
                        <div x-show="!expandido"
                             class="absolute bottom-0 left-0 right-0 h-10 bg-gradient-to-t from-slate-50 dark:from-slate-900 to-transparent pointer-events-none"></div>
                    </div>
                    <button @click="expandido = !expandido" type="button"
                            class="cursor-pointer w-full flex items-center justify-center gap-1.5 py-2 border-t border-slate-200 dark:border-slate-700 text-xs text-indigo-600 dark:text-indigo-400 lato-bold hover:bg-violet-50 dark:hover:bg-violet-900/20 transition">
                        <span class="transition-transform duration-200" :class="expandido ? 'rotate-180' : ''"><x-lucide-chevron-down class="w-3.5 h-3.5" /></span>
                        <span x-text="expandido ? 'Mostrar menos' : 'Ver texto completo'"></span>
                    </button>
                </div>

                {{-- Palavras-chave detectadas automaticamente --}}
                @php
                    $techs = ['Laravel','PHP','Python','JavaScript','TypeScript','React','Vue','Angular','Node','Java','C#','C\+\+','Docker','AWS','Azure','GCP','SQL','PostgreSQL','MySQL','MongoDB','Redis','Git','Linux','Scrum','Agile','inglês','espanhol','inglês fluente','bilíngue'];
                    $encontradas = array_filter($techs, fn($t) => preg_match('/\b'.$t.'\b/i', $dc->texto_ocr));
                @endphp
                @if ($encontradas)
                <div class="mt-3">
                    <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wider mb-1.5">Detectado no currículo</p>
                    <div class="flex flex-wrap gap-1.5">
                        @foreach ($encontradas as $tech)
                        <span class="px-2 py-0.5 rounded-full bg-violet-50 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300 text-[11px] lato-bold">
                            {{ $tech }}
                        </span>
                        @endforeach
                    </div>
                </div>
                @endif

            @else
                <div class="flex items-center gap-2 px-4 py-3 rounded-xl border border-dashed border-slate-300 dark:border-slate-600 text-slate-400 text-sm">
                    <x-lucide-clock class="w-4 h-4 shrink-0" />
                    <span class="lato-regular text-xs">
                        @if ($dc->arquivo_path)
                            Texto ainda sendo extraído. Clique em <x-lucide-refresh-cw class="w-3 h-3 inline" /> para reprocessar.
                        @else
                            Nenhum arquivo anexado a este currículo.
                        @endif
                    </span>
                </div>
            @endif
        </div>
        @endif

        <div>
            <h3 class="text-xs lato-bold text-slate-400 uppercase tracking-wider mb-2">Notas Internas</h3>
            <p class="text-sm text-slate-600 dark:text-slate-400 lato-regular leading-relaxed whitespace-pre-line">{{ $dc->notas_internas ?? 'Nenhuma nota registrada.' }}</p>
        </div>
    </div>
</div>
@endif
