    {{-- ═══════════════════════════════════════════════════════════════
         MODAL: CONFIRMAÇÃO GENÉRICA
    ═══════════════════════════════════════════════════════════════════ --}}
    @if($confirmModal)
    <div class="fixed inset-0 z-50 flex items-center justify-center p-4">
        <div class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm" wire:click="$set('confirmModal', false)"></div>
        <div class="relative z-10 bg-white dark:bg-slate-800 rounded-2xl shadow-xl w-full max-w-sm p-6">

            @php
                $confirmConfig = match($confirmType) {
                    'activate_cycle'      => ['Ativar ciclo', 'Ao ativar, qualquer ciclo ativo atual será encerrado automaticamente. Os gerentes poderão iniciar suas avaliações.', 'Ativar', 'emerald'],
                    'close_cycle'         => ['Encerrar ciclo', 'Ao encerrar, os gerentes não poderão mais realizar ou editar avaliações neste ciclo.', 'Encerrar', 'rose'],
                    'delete_cycle'        => ['Excluir ciclo', 'Esta ação é irreversível. Todas as avaliações vinculadas a este ciclo serão excluídas.', 'Excluir', 'red'],
                    'complete_evaluation' => ['Finalizar avaliação', 'Após finalizar, os dados não poderão ser editados. Tem certeza que deseja continuar?', 'Finalizar', 'emerald'],
                    default               => ['Confirmar', 'Deseja confirmar esta ação?', 'Confirmar', 'indigo'],
                };
            @endphp

            <div class="flex items-center gap-3 mb-4">
                <div class="w-10 h-10 rounded-full bg-{{ $confirmConfig[3] }}-100 dark:bg-{{ $confirmConfig[3] }}-900/30 flex items-center justify-center shrink-0">
                    @if(in_array($confirmType, ['delete_cycle', 'close_cycle']))
                        <x-lucide-alert-triangle class="w-5 h-5 text-{{ $confirmConfig[3] }}-600" />
                    @else
                        <x-lucide-check-circle class="w-5 h-5 text-{{ $confirmConfig[3] }}-600" />
                    @endif
                </div>
                <h3 class="text-sm font-bold text-slate-800 dark:text-white lato-black">{{ $confirmConfig[0] }}</h3>
            </div>

            <p class="text-sm text-slate-500 dark:text-slate-400 lato-regular leading-relaxed mb-6">
                {{ $confirmConfig[1] }}
            </p>

            <div class="flex gap-3 justify-end">
                <button type="button" wire:click="$set('confirmModal', false)"
                        class="px-4 py-2 text-sm lato-bold text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 rounded-xl transition cursor-pointer">
                    Cancelar
                </button>
                <button type="button" wire:click="confirmAction"
                        wire:loading.attr="disabled" wire:target="confirmAction"
                        class="px-5 py-2 text-sm lato-bold text-white rounded-xl shadow-sm transition cursor-pointer disabled:opacity-60
                               bg-{{ $confirmConfig[3] }}-600 hover:bg-{{ $confirmConfig[3] }}-700">
                    {{ $confirmConfig[2] }}
                </button>
            </div>
        </div>
    </div>
    @endif
