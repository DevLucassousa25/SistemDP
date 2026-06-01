<div class="min-h-screen bg-gradient-to-br from-slate-50 to-slate-100 dark:from-slate-900 dark:to-slate-800 flex items-center justify-center p-4">

    {{-- ── Token Inválido ── --}}
    @if ($tela === 'invalido')
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-10 max-w-md w-full text-center">
        <div class="w-16 h-16 rounded-full bg-red-100 dark:bg-red-900/30 flex items-center justify-center mx-auto mb-4">
            <x-lucide-alert-circle class="w-8 h-8 text-red-500" />
        </div>
        <h1 class="text-xl lato-black text-slate-800 dark:text-white mb-2">Link inválido</h1>
        <p class="text-sm text-slate-400 lato-regular">Este link de entrevista não é válido ou expirou. Entre em contato com o RH.</p>
    </div>

    {{-- ── Já Respondido ── --}}
    @elseif ($tela === 'ja_respondido')
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-10 max-w-md w-full text-center">
        <div class="w-16 h-16 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center mx-auto mb-4">
            <x-lucide-check-circle class="w-8 h-8 text-blue-500" />
        </div>
        <h1 class="text-xl lato-black text-slate-800 dark:text-white mb-2">Já respondido</h1>
        <p class="text-sm text-slate-400 lato-regular">Você já respondeu esta entrevista. Obrigado pelo seu feedback!</p>
    </div>

    {{-- ── Concluído ── --}}
    @elseif ($tela === 'concluido')
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl p-10 max-w-md w-full text-center">
        <div class="w-20 h-20 rounded-full bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center mx-auto mb-5 shadow-lg shadow-emerald-200 dark:shadow-emerald-900/30">
            <x-lucide-heart class="w-10 h-10 text-white" />
        </div>
        <h1 class="text-2xl lato-black text-slate-800 dark:text-white mb-3">Obrigado pelo feedback!</h1>
        <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular leading-relaxed">
            Suas respostas foram registradas com sucesso. Suas opiniões são muito importantes para o nosso crescimento.
            Desejamos sucesso na sua próxima jornada!
        </p>
        <div class="mt-6 p-4 bg-slate-50 dark:bg-slate-700 rounded-xl">
            <p class="text-xs text-slate-400 lato-regular">Respondido em {{ now()->format('d/m/Y \à\s H:i') }}</p>
        </div>
    </div>

    {{-- ── Formulário ── --}}
    @else
    <div class="bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden">

        {{-- Header --}}
        <div class="bg-gradient-to-r from-slate-800 to-slate-700 dark:from-slate-900 dark:to-slate-800 p-6 text-white">
            <div class="flex items-center gap-3 mb-3">
                <div class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                    <x-lucide-message-square class="w-5 h-5 text-white" />
                </div>
                <div>
                    <p class="text-xs text-white/60 lato-regular">{{ config('app.name') }}</p>
                    <h1 class="text-lg lato-black">Entrevista de Desligamento</h1>
                </div>
            </div>
            @if ($entrevista?->desligamento?->funcionario)
            <p class="text-sm text-white/70 lato-regular">
                Olá, <span class="text-white lato-bold">{{ $entrevista->desligamento->funcionario->name }}</span>!
                Suas respostas são confidenciais e nos ajudam a melhorar continuamente.
            </p>
            @endif
        </div>

        <div class="p-6 space-y-6 max-h-[75vh] overflow-y-auto">

            {{-- Avaliações de satisfação (escala 1-5) --}}
            <div>
                <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 mb-4 flex items-center gap-2">
                    <x-lucide-star class="w-4 h-4 text-amber-400" />
                    Avaliação de Satisfação <span class="text-xs text-slate-400 lato-regular font-normal">(1 = muito insatisfeito · 5 = muito satisfeito)</span>
                </h2>
                <div class="space-y-3">
                    @php
                    $avaliacoes = [
                        ['prop' => 'satisfacaoGestao',      'label' => 'Liderança / Gestão'],
                        ['prop' => 'satisfacaoCultura',     'label' => 'Cultura e clima organizacional'],
                        ['prop' => 'satisfacaoRemuneracao', 'label' => 'Remuneração e benefícios'],
                        ['prop' => 'satisfacaoCrescimento', 'label' => 'Crescimento e plano de carreira'],
                        ['prop' => 'satisfacaoEquilibrio',  'label' => 'Equilíbrio trabalho-vida pessoal'],
                    ];
                    @endphp
                    @foreach ($avaliacoes as $av)
                    <div class="flex items-center justify-between gap-4">
                        <span class="text-sm text-slate-600 dark:text-slate-300 lato-regular flex-1">{{ $av['label'] }}</span>
                        <div class="flex items-center gap-1.5 shrink-0">
                            @for ($i = 1; $i <= 5; $i++)
                            <button wire:click="$set('{{ $av['prop'] }}', {{ $i }})" type="button"
                                    class="cursor-pointer w-9 h-9 rounded-xl text-sm lato-bold transition
                                           {{ $this->{$av['prop']} === $i
                                               ? 'bg-amber-400 text-white shadow-sm shadow-amber-200'
                                               : 'bg-slate-100 dark:bg-slate-700 text-slate-400 dark:text-slate-500 hover:bg-amber-100 hover:text-amber-600' }}">
                                {{ $i }}
                            </button>
                            @endfor
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700"></div>

            {{-- Motivo principal --}}
            <div>
                <label class="block text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-2">
                    <x-lucide-help-circle class="w-4 h-4 text-blue-400" />
                    Qual foi o principal motivo do desligamento? *
                </label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                    @foreach (\App\Models\RhEntrevistaDesligamento::$motivosPrincipais as $k => $v)
                    <button wire:click="$set('motivoPrincipal', '{{ $k }}')" type="button"
                            class="cursor-pointer text-left px-4 py-3 rounded-xl border text-sm lato-regular transition
                                   {{ $motivoPrincipal === $k
                                       ? 'border-blue-400 bg-blue-50 dark:bg-blue-900/20 text-blue-700 dark:text-blue-300 lato-bold'
                                       : 'border-slate-200 dark:border-slate-600 text-slate-600 dark:text-slate-300 hover:border-blue-300 hover:bg-blue-50 dark:hover:bg-blue-900/10' }}">
                        {{ $v }}
                    </button>
                    @endforeach
                </div>
                @error('motivoPrincipal') <p class="text-xs text-red-500 mt-2">{{ $message }}</p> @enderror
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700"></div>

            {{-- Recomendaria --}}
            <div>
                <label class="block text-sm lato-bold text-slate-700 dark:text-slate-200 mb-3 flex items-center gap-2">
                    <x-lucide-thumbs-up class="w-4 h-4 text-emerald-400" />
                    Você recomendaria a empresa como local de trabalho?
                </label>
                <div class="flex gap-3">
                    <button wire:click="$set('recomendariaEmpresa', true)" type="button"
                            class="cursor-pointer flex-1 py-2.5 rounded-xl border text-sm lato-bold transition
                                   {{ $recomendariaEmpresa === true
                                       ? 'border-emerald-400 bg-emerald-50 dark:bg-emerald-900/20 text-emerald-700 dark:text-emerald-300'
                                       : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-emerald-300' }}">
                        👍 Sim
                    </button>
                    <button wire:click="$set('recomendariaEmpresa', false)" type="button"
                            class="cursor-pointer flex-1 py-2.5 rounded-xl border text-sm lato-bold transition
                                   {{ $recomendariaEmpresa === false
                                       ? 'border-red-400 bg-red-50 dark:bg-red-900/20 text-red-700 dark:text-red-300'
                                       : 'border-slate-200 dark:border-slate-600 text-slate-500 dark:text-slate-400 hover:border-red-300' }}">
                        👎 Não
                    </button>
                </div>
            </div>

            <div class="border-t border-slate-100 dark:border-slate-700"></div>

            {{-- Questões abertas --}}
            <div class="space-y-4">
                <h2 class="text-sm lato-bold text-slate-700 dark:text-slate-200 flex items-center gap-2">
                    <x-lucide-message-circle class="w-4 h-4 text-indigo-400" />
                    Feedback aberto <span class="text-xs text-slate-400 lato-regular font-normal">(opcional)</span>
                </h2>

                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">O que você mais valorizou na empresa?</label>
                    <textarea wire:model="pontosPositivos" rows="3" placeholder="Pontos positivos, boas experiências..."
                              class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">O que poderia melhorar?</label>
                    <textarea wire:model="pontosMelhoria" rows="3" placeholder="Sugestões de melhoria..."
                              class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 resize-none"></textarea>
                </div>

                <div>
                    <label class="block text-xs lato-bold text-slate-500 dark:text-slate-400 mb-1.5">Algum comentário adicional?</label>
                    <textarea wire:model="outrosComentarios" rows="2" placeholder="Qualquer outra consideração..."
                              class="w-full px-3 py-2.5 text-sm border border-slate-200 dark:border-slate-600 rounded-xl bg-white dark:bg-slate-700 text-slate-700 dark:text-slate-200 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-indigo-400/30 resize-none"></textarea>
                </div>
            </div>
        </div>

        {{-- Footer --}}
        <div class="px-6 py-4 border-t border-slate-100 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50">
            <button wire:click="responder" type="button" wire:loading.attr="disabled" wire:target="responder"
                    class="cursor-pointer w-full py-3 rounded-xl bg-gradient-to-r from-slate-700 to-slate-800 dark:from-slate-600 dark:to-slate-700 text-white text-sm lato-bold hover:from-slate-800 hover:to-slate-900 transition shadow-sm disabled:opacity-60">
                <span wire:loading.remove wire:target="responder">Enviar Respostas</span>
                <span wire:loading wire:target="responder">Enviando...</span>
            </button>
            <p class="text-xs text-slate-400 text-center mt-2 lato-regular">Suas respostas são confidenciais e tratadas pelo RH</p>
        </div>
    </div>
    @endif

</div>
