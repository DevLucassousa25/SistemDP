{{-- ══════════════════════════════════════════════════════════════════════════
     MODAL: ATENDER SOLICITAÇÃO (RH)
══════════════════════════════════════════════════════════════════════════ --}}
<div x-data x-show="$wire.solModal" x-cloak style="display:none"
     class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm"
     wire:click.self="$set('solModal', false)">
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-2xl w-full max-w-lg flex flex-col" @click.stop>

        {{-- Header --}}
        <div class="flex items-center justify-between p-5 border-b border-slate-100 dark:border-slate-700 shrink-0">
            <h3 class="text-sm lato-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                <span class="w-7 h-7 rounded-lg bg-gradient-to-br from-amber-500 to-orange-500 flex items-center justify-center">
                    <x-lucide-inbox class="w-3.5 h-3.5 text-white" />
                </span>
                Atender Solicitação
            </h3>
            <button wire:click="$set('solModal', false)" type="button"
                    class="cursor-pointer p-1.5 rounded-lg text-slate-400 hover:text-slate-600 hover:bg-slate-100 dark:hover:bg-slate-700 transition">
                <x-lucide-x class="w-4 h-4" />
            </button>
        </div>

        {{-- Body --}}
        <div class="p-5 space-y-4 overflow-y-auto">
            @if ($solEditId)
            @php $solRec = \App\Models\RhSolicitacao::with('user.department')->find($solEditId); @endphp
            @if ($solRec)
            {{-- Info do solicitante --}}
            <div class="flex items-center gap-3 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl">
                <div class="w-9 h-9 rounded-full bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center text-white text-sm lato-bold shrink-0">
                    {{ strtoupper(substr($solRec->user?->name ?? '?', 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm lato-bold text-slate-700 dark:text-slate-200 truncate">{{ $solRec->user?->name ?? '—' }}</p>
                    <p class="text-xs text-slate-400">{{ $solRec->user?->department?->name ?? '' }} · {{ \App\Models\RhSolicitacao::$tipoLabels[$solRec->tipo] ?? $solRec->tipo }}</p>
                </div>
                <span class="text-xs text-slate-400">{{ $solRec->created_at->format('d/m/Y') }}</span>
            </div>
            @if ($solRec->descricao)
            <div class="p-3 bg-amber-50 dark:bg-amber-900/20 border border-amber-200 dark:border-amber-700/40 rounded-xl">
                <p class="text-xs lato-bold text-amber-600 dark:text-amber-400 mb-1">Descrição do funcionário</p>
                <p class="text-sm text-slate-700 dark:text-slate-300">{{ $solRec->descricao }}</p>
            </div>
            @endif
            @endif
            @endif

            {{-- Status --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Status</label>
                <div class="grid grid-cols-2 gap-2">
                    @foreach (\App\Models\RhSolicitacao::$statusLabels as $k => $v)
                    <button wire:click="$set('solStatusEdit', '{{ $k }}')" type="button"
                            class="cursor-pointer flex items-center gap-2 px-3 py-2.5 rounded-xl border text-sm lato-bold transition
                                   {{ $solStatusEdit === $k
                                       ? 'border-amber-400 bg-amber-50 dark:bg-amber-900/20 text-amber-700 dark:text-amber-300'
                                       : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-amber-300' }}">
                        <span class="w-2 h-2 rounded-full
                            {{ $k === 'pendente' ? 'bg-amber-400' : ($k === 'em_andamento' ? 'bg-blue-400' : ($k === 'concluida' ? 'bg-emerald-400' : 'bg-slate-400')) }}"></span>
                        {{ $v }}
                    </button>
                    @endforeach
                </div>
            </div>

            {{-- Prazo --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Prazo de Entrega</label>
                <input wire:model="solPrazo" type="date"
                       class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400" />
            </div>

            {{-- Observação do RH --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Observação / Resposta ao Funcionário</label>
                <textarea wire:model="solObsRh" rows="4" placeholder="Ex: Documento gerado e disponível para retirada na recepção…"
                          class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 focus:outline-none focus:ring-2 focus:ring-amber-400 resize-none"></textarea>
            </div>

            {{-- Upload de arquivo --}}
            <div>
                <label class="block text-xs lato-bold text-slate-600 dark:text-slate-400 mb-2">Anexar Documento (opcional)</label>
                <label class="cursor-pointer flex items-center gap-3 px-4 py-3 rounded-xl border-2 border-dashed border-slate-200 dark:border-slate-600 hover:border-amber-400 transition bg-slate-50 dark:bg-slate-700/50">
                    <x-lucide-paperclip class="w-4 h-4 text-slate-400 shrink-0" />
                    <span class="text-sm text-slate-500 dark:text-slate-400">
                        @if ($solArquivo)
                            {{ $solArquivo->getClientOriginalName() }}
                        @else
                            Clique para selecionar arquivo (PDF, DOC, DOCX — máx. 10 MB)
                        @endif
                    </span>
                    <input type="file" wire:model="solArquivo" class="hidden" accept=".pdf,.doc,.docx,.png,.jpg" />
                </label>
                @error('solArquivo') <p class="text-xs text-rose-500 mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Footer --}}
        <div class="flex items-center justify-end gap-3 px-5 py-4 border-t border-slate-100 dark:border-slate-700 shrink-0">
            <button wire:click="$set('solModal', false)" type="button"
                    class="cursor-pointer px-4 py-2.5 rounded-xl border border-slate-200 dark:border-slate-600 text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-white dark:hover:bg-slate-700 transition">
                Cancelar
            </button>
            <button wire:click="saveSol" type="button" wire:loading.attr="disabled" wire:target="saveSol"
                    class="cursor-pointer inline-flex items-center gap-2 px-6 py-2.5 rounded-xl bg-gradient-to-r from-blue-500 to-indigo-600 text-white text-sm lato-bold hover:from-blue-600 hover:to-indigo-700 transition shadow-sm disabled:opacity-60">
                <span wire:loading.remove wire:target="saveSol" class="flex items-center gap-2">
                    <x-lucide-check class="w-4 h-4" />
                    Salvar Atendimento
                </span>
                <span wire:loading wire:target="saveSol" class="flex items-center gap-2">
                    <x-lucide-loader-circle class="w-4 h-4 animate-spin" /> Salvando...
                </span>
            </button>
        </div>
    </div>
</div>

                                                                