    {{-- ═══════════════════════════════════════════════════════════════
         MODAL — Detalhes (com histórico + comentários)
    ═══════════════════════════════════════════════════════════════════ --}}
    @if ($viewModal && $fb = $this->viewing)
        @php
            $typeStyle2 = match($fb->type) {
                'reconhecimento' => 'background:#ecfdf5;color:#065f46;border-color:#6ee7b7',
                'sugestao'       => 'background:#eff6ff;color:#1e40af;border-color:#93c5fd',
                default          => 'background:#fef2f2;color:#991b1b;border-color:#fca5a5',
            };
            $stStyle2 = match($fb->status) {
                'aberto'             => 'background:#eff6ff;color:#1e40af',
                'em_analise'         => 'background:#fefce8;color:#854d0e',
                'aguardando_plano'   => 'background:#fff7ed;color:#9a3412',
                'plano_em_andamento' => 'background:#f5f3ff;color:#5b21b6',
                'resolvido'          => 'background:#ecfdf5;color:#166534',
                'arquivado'          => 'background:#f1f5f9;color:#475569',
                default              => 'background:#f1f5f9;color:#475569',
            };
        @endphp

        <div class="fixed inset-0 z-50 flex items-end sm:items-center justify-center sm:p-4">
            <div class="absolute inset-0 bg-slate-900/50 backdrop-blur-sm" wire:click="fecharView"></div>

            <div class="relative bg-white dark:bg-slate-800 w-full sm:max-w-2xl max-h-[95vh] sm:max-h-[90vh]
                        flex flex-col rounded-t-2xl sm:rounded-2xl shadow-2xl z-10 overflow-hidden">

                {{-- Header --}}
                <div class="flex-shrink-0 border-b border-slate-100 dark:border-slate-700">
                    <div class="flex items-center gap-3 px-4 sm:px-6 py-3.5 sm:py-5">
                        <div class="flex flex-wrap items-center gap-1.5 flex-1">
                            <span class="text-xs font-semibold lato-bold px-2.5 py-1 rounded-full border"
                                  style="{{ $typeStyle2 }}">
                                {{ $fb->type_label }}
                            </span>
                            @if ($fb->type === 'alerta' && $fb->severity)
                                <span class="text-xs font-bold lato-bold px-2.5 py-1 rounded-full"
                                      style="{{ match($fb->severity) {
                                          'baixo'  => 'background:#dcfce7;color:#166534',
                                          'medio'  => 'background:#fefce8;color:#854d0e',
                                          'alto'   => 'background:#ffedd5;color:#9a3412',
                                          default  => 'background:#fee2e2;color:#991b1b',
                                      } }}">
                                    {{ strtoupper($fb->severity_label) }}
                                </span>
                            @endif
                            <span class="text-xs font-semibold lato-bold px-2.5 py-1 rounded-full"
                                  style="{{ $stStyle2 }}">
                                {{ $fb->status_label }}
                            </span>
                        </div>
                        <button type="button" wire:click="fecharView"
                                class="shrink-0 w-8 h-8 flex items-center justify-center rounded-lg text-slate-400
                                       hover:text-slate-700 hover:bg-slate-100 dark:hover:bg-slate-700 transition cursor-pointer">
                            <x-lucide-x class="w-4 h-4" />
                        </button>
                    </div>
                </div>

                {{-- Body --}}
                <div class="flex-1 overflow-y-auto px-4 sm:px-6 py-4 sm:py-5 space-y-5">

                    {{-- Funcionário + Avaliador --}}
                    <div class="grid grid-cols-2 gap-3">
                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Funcionário avaliado</p>
                            <div class="flex items-center gap-2">
                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-slate-400 to-slate-600
                                            flex items-center justify-center text-white text-xs font-bold lato-black shrink-0">
                                    {{ mb_strtoupper(mb_substr($fb->employee?->name ?? '?', 0, 1)) }}
                                </div>
                                <div>
                                    <p class="text-sm font-semibold text-slate-800 dark:text-white lato-bold leading-tight">{{ $fb->employee?->name ?? '—' }}</p>
                                    <p class="text-[11px] text-slate-400 lato-regular">{{ $fb->employee?->department?->name ?? '—' }}</p>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4">
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Avaliador</p>
                            <p class="text-sm font-semibold text-slate-800 dark:text-white lato-bold">{{ $fb->evaluator_name }}</p>
                            @if ($fb->is_anonymous)
                                <p class="text-[11px] text-slate-400 lato-regular">Identidade ocultada</p>
                            @endif
                        </div>
                    </div>

                    {{-- Metadados --}}
                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Categoria</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold mt-0.5">{{ $fb->category_label }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Data</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold mt-0.5">{{ $fb->occurred_at->format('d/m/Y') }}</p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Nota</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold mt-0.5">
                                @if ($fb->rating)
                                    @for ($i = 1; $i <= 5; $i++)<span style="color: {{ $i <= $fb->rating ? '#f59e0b' : '#e2e8f0' }}">★</span>@endfor
                                @else —
                                @endif
                            </p>
                        </div>
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Criado em</p>
                            <p class="text-sm font-semibold text-slate-700 dark:text-slate-200 lato-bold mt-0.5">{{ $fb->created_at->format('d/m/Y') }}</p>
                        </div>
                    </div>

                    {{-- Conteúdo do feedback (estruturado por template) --}}
                    @php
                        $fbTemplate  = $fb->template ?? 'livre';
                        $fbTplDef    = \App\Livewire\Pages\Feedback\Index::templateDefinitions()[$fbTemplate] ?? null;
                        $fbTplData   = $fb->template_data ?? [];
                        $colorBorder = match($fbTplDef['color'] ?? 'slate') {
                            'violet'  => 'border-violet-200 dark:border-violet-700/50',
                            'blue'    => 'border-blue-200 dark:border-blue-700/50',
                            'emerald' => 'border-emerald-200 dark:border-emerald-700/50',
                            'amber'   => 'border-amber-200 dark:border-amber-700/50',
                            'rose'    => 'border-rose-200 dark:border-rose-700/50',
                            default   => 'border-slate-200 dark:border-slate-700/50',
                        };
                        $colorDotView = match($fbTplDef['color'] ?? 'slate') {
                            'violet'  => 'bg-indigo-600',
                            'blue'    => 'bg-blue-500',
                            'emerald' => 'bg-emerald-500',
                            'amber'   => 'bg-amber-500',
                            'rose'    => 'bg-rose-500',
                            default   => 'bg-slate-400',
                        };
                    @endphp

                    <div class="space-y-2">
                        {{-- Badge do template --}}
                        @if ($fbTplDef)
                        <div class="flex items-center gap-2 mb-1">
                            <span class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Formato</span>
                            <span class="text-[10px] font-semibold lato-bold px-2 py-0.5 rounded-full
                                         {{ match($fbTplDef['color'] ?? 'slate') {
                                             'violet'  => 'bg-violet-100 dark:bg-violet-900/30 text-violet-700 dark:text-violet-300',
                                             'blue'    => 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
                                             'emerald' => 'bg-emerald-100 dark:bg-emerald-900/30 text-emerald-700 dark:text-emerald-300',
                                             'amber'   => 'bg-amber-100 dark:bg-amber-900/30 text-amber-700 dark:text-amber-300',
                                             'rose'    => 'bg-rose-100 dark:bg-rose-900/30 text-rose-700 dark:text-rose-300',
                                             default   => 'bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-300',
                                         } }}">
                                {{ $fbTplDef['label'] }}
                            </span>
                        </div>
                        @endif

                        {{-- Campos do template --}}
                        @if ($fbTplDef && !empty($fbTplData))
                            @foreach ($fbTplDef['fields'] as $fieldKey => $fieldDef)
                                @if (!empty($fbTplData[$fieldKey]))
                                <div class="rounded-xl border {{ $colorBorder }} bg-white dark:bg-slate-800/50 overflow-hidden">
                                    <div class="flex items-center gap-1.5 px-3 py-1.5 bg-slate-50 dark:bg-slate-700/50 border-b {{ $colorBorder }}">
                                        <span class="w-1.5 h-1.5 rounded-full {{ $colorDotView }} shrink-0"></span>
                                        <span class="text-[10px] font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">{{ $fieldDef['label'] }}</span>
                                    </div>
                                    <p class="px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed whitespace-pre-wrap">{{ $fbTplData[$fieldKey] }}</p>
                                </div>
                                @endif
                            @endforeach
                        @else
                            {{-- Fallback para feedbacks sem template_data (registros antigos) --}}
                            <div class="bg-slate-50 dark:bg-slate-700/50 rounded-xl p-4 text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed whitespace-pre-wrap">
                                {{ $fb->message }}
                            </div>
                        @endif
                    </div>

                    {{-- Anotação privada (só para o avaliador ou RH/DP) --}}
                    @if ($fb->private_note && (auth()->id() === $fb->evaluator_id || auth()->user()->isRhOuDp()))
                    <div class="rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-700/50 overflow-hidden">
                        <div class="flex items-center gap-1.5 px-3 py-1.5 border-b border-slate-200 dark:border-slate-700">
                            <x-lucide-lock class="w-3 h-3 text-slate-400 shrink-0" />
                            <span class="text-[10px] font-semibold lato-bold text-slate-500 dark:text-slate-400 uppercase tracking-wide">Anotação privada</span>
                            <span class="text-[9px] text-slate-400 lato-regular ml-1">— visível só para você</span>
                        </div>
                        <p class="px-3 py-2.5 text-sm text-slate-700 dark:text-slate-200 lato-regular leading-relaxed whitespace-pre-wrap">{{ $fb->private_note }}</p>
                    </div>
                    @endif

                    {{-- Plano de ação --}}
                    @if ($fb->action_plan)
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Plano de ação</p>
                            <div class="bg-indigo-50 dark:bg-indigo-900/20 border border-indigo-100 dark:border-indigo-800 rounded-xl p-4">
                                <p class="text-sm text-indigo-900 dark:text-indigo-200 lato-regular whitespace-pre-wrap leading-relaxed">{{ $fb->action_plan }}</p>
                                @if ($fb->action_plan_deadline || $fb->actionPlanResponsible)
                                    <div class="flex flex-wrap gap-3 mt-3 pt-3 border-t border-indigo-100 dark:border-indigo-800">
                                        @if ($fb->action_plan_deadline)
                                            <span class="flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-300 lato-regular">
                                                <x-lucide-calendar-clock class="w-3.5 h-3.5" />
                                                Prazo: {{ $fb->action_plan_deadline->format('d/m/Y') }}
                                                @if ($fb->action_plan_deadline->isPast() && $fb->status !== 'resolvido')
                                                    ss="text-red-500 font-semibold">(vencido)</span>
                                                @endif
                                            </span>
                                        @endif
                                        @if ($fb->actionPlanResponsible)
                                            <span class="flex items-center gap-1 text-[11px] text-indigo-600 dark:text-indigo-300 lato-regular">
                                                <x-lucide-user-check class="w-3.5 h-3.5" />
                                                Responsável: {{ $fb->actionPlanResponsible->name }}
                                            </span>
                                        @endif
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    {{-- Tarefas do plano de ação (checklist — view) --}}
                    @if ($fb->actionTasks->isNotEmpty())
                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide">Tarefas do plano</p>
                                @php
                                    $doneCount  = $fb->actionTasks->where('completed', true)->count();
                                    $totalCount = $fb->actionTasks->count();
                                    $pct        = $totalCount > 0 ? round(($doneCount / $totalCount) * 100) : 0;
                                @endphp
                                <span class="text-[10px] lato-regular text-slate-400">{{ $doneCount }}/{{ $totalCount }} concluídas</span>
                            </div>

                            {{-- Barra de progresso --}}
                            <div class="w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1.5 mb-3">
                                <div class="h-1.5 rounded-full transition-all duration-500
                                            {{ $pct === 100 ? 'bg-emerald-500' : 'bg-indigo-400' }}"
                                     style="width: {{ $pct }}%"></div>
                            </div>

                            <div class="space-y-1.5">
                                @foreach ($fb->actionTasks as $task)
                                    <div class="flex items-start gap-2.5 px-3 py-2.5 rounded-xl border
                                                {{ $task->completed
                                                    ? 'bg-emerald-50 dark:bg-emerald-900/10 border-emerald-100 dark:border-emerald-800/40'
                                                    : ($task->is_overdue
                                                        ? 'bg-red-50 dark:bg-red-900/10 border-red-100 dark:border-red-800/40'
                                                        : 'bg-white dark:bg-slate-700/40 border-slate-200 dark:border-slate-600') }}">

                                        {{-- Checkbox --}}
                                        <button type="button"
                                                wire:click="toggleTask({{ $task->id }})"
                                                class="mt-0.5 w-4 h-4 rounded-full shrink-0 flex items-center justify-center
                                                       transition cursor-pointer
                                                       {{ $task->completed
                                                           ? 'bg-emerald-500 hover:bg-emerald-600'
                                                           : 'border-2 border-slate-300 dark:border-slate-500 hover:border-emerald-400' }}">
                                            @if ($task->completed)
                                                <x-lucide-check class="w-2.5 h-2.5 text-white" />
                                            @endif
                                        </button>

                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm lato-regular leading-snug
                                                       {{ $task->completed
                                                           ? 'line-through text-slate-400 dark:text-slate-500'
                                                           : 'text-slate-700 dark:text-slate-200' }}">
                                                {{ $task->description }}
                                            </p>
                                            <div class="flex flex-wrap gap-2 mt-1">
                                                @if ($task->responsible)
                                                    <span class="flex items-center gap-1 text-[10px] text-slate-400 lato-regular">
                                                        <x-lucide-user class="w-3 h-3" />
                                                        {{ $task->responsible->name }}
                                                    </span>
                                                @endif
                                                @if ($task->due_date)
                                                    <span class="flex items-center gap-1 text-[10px] lato-regular
                                                                 {{ $task->is_overdue ? 'text-red-500 font-semibold' : 'text-slate-400' }}">
                                                        <x-lucide-calendar class="w-3 h-3" />
                                                        {{ $task->due_date->format('d/m/Y') }}
                                                        @if ($task->is_overdue)
                                                            (vencida)
                                                        @endif
                                                    </span>
                                                @endif
                                                @if ($task->completed && $task->completedBy)
                                                    <span class="flex items-center gap-1 text-[10px] text-emerald-500 lato-regular">
                                                        <x-lucide-check-circle class="w-3 h-3" />
                                                        {{ $task->completedBy->name }} · {{ $task->completed_at?->format('d/m/Y') }}
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Anexos --}}
                    @if (!empty($fb->attachments))
                        <div>
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Anexos</p>
                            <div class="space-y-2">
                                @foreach ($fb->attachments as $att)
                                    <a href="{{ asset('storage/' . $att['path']) }}" target="_blank"
                                       class="flex items-center gap-2 p-3 bg-slate-50 dark:bg-slate-700/50 rounded-xl
                                              hover:bg-slate-100 dark:hover:bg-slate-700 transition text-sm
                                              text-slate-700 dark:text-slate-200 lato-regular">
                                        <x-lucide-paperclip class="w-4 h-4 text-slate-400 shrink-0" />
                                        {{ $att['name'] }}
                                        <span class="ml-auto text-[11px] text-slate-400">{{ number_format($att['size'] / 1024, 1) }} KB</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Atualizar status --}}
                    <div class="pt-2">
                        <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-2">Atualizar status</p>
                        <div class="flex flex-wrap gap-2">
                            @foreach ([
                                'aberto'             => ['Aberto',            '#eff6ff', '#1e40af'],
                                'em_analise'         => ['Em análise',         '#fefce8', '#854d0e'],
                                'aguardando_plano'   => ['Aguard. plano',      '#fff7ed', '#9a3412'],
                                'plano_em_andamento' => ['Plano em andamento', '#f5f3ff', '#5b21b6'],
                                'resolvido'          => ['Resolvido',          '#ecfdf5', '#166534'],
                                'arquivado'          => ['Arquivado',          '#f1f5f9', '#475569'],
                            ] as $val => [$label, $bg, $col])
                                <button type="button"
                                        wire:click="atualizarStatus({{ $fb->id }}, '{{ $val }}')"
                                        class="text-xs px-3 py-1.5 rounded-lg font-semibold lato-bold transition cursor-pointer border"
                                        style="background:{{ $bg }};color:{{ $col }};border-color:{{ $col }}44;
                                               {{ $fb->status === $val ? 'box-shadow:0 0 0 2px ' . $col . '66' : '' }}">
                                    {{ $label }} @if ($fb->status === $val) ✓ @endif
                                </button>
                            @endforeach
                        </div>
                    </div>

                    {{-- Histórico de status --}}
                    @if ($fb->statusHistory->isNotEmpty())
                        <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                            <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-3">Histórico</p>
                            <div class="space-y-0">
                                @foreach ($fb->statusHistory->sortByDesc('created_at') as $h)
                                    <div class="flex gap-3">
                                        <div class="flex flex-col items-center shrink-0">
                                            <div class="w-2.5 h-2.5 rounded-full mt-1.5 shrink-0
                                                        {{ $h->old_status === null ? 'bg-indigo-600' : 'bg-emerald-500' }}"></div>
                                            @if (! $loop->last)
                                                <div class="w-px flex-1 bg-slate-200 dark:bg-slate-700 mt-1 mb-1" style="min-height: 20px"></div>
                                            @endif
                                        </div>
                                        <div class="pb-3 min-w-0">
                                            <p class="text-xs text-slate-700 dark:text-slate-200 lato-regular leading-snug">
                                                <span class="font-semibold lato-bold">{{ $h->changedBy?->name ?? 'Sistema' }}</span>
                                                @if ($h->old_status === null)
                                                    registrou como <span class="font-semibold">{{ $h->new_status_label }}</span>
                                                @else
                                                    alterou de <span class="text-slate-500">{{ $h->old_status_label }}</span>
                                                    para <span class="font-semibold">{{ $h->new_status_label }}</span>
                                                @endif
                                            </p>
                                            <p class="text-[11px] text-slate-400 lato-regular mt-0.5">
                                                {{ $h->created_at->format('d/m/Y H:i') }} · {{ $h->created_at->diffForHumans() }}
                                            </p>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    {{-- Notas internas / comentários --}}
                    <div class="pt-2 border-t border-slate-100 dark:border-slate-700">
                        <p class="text-[10px] text-slate-400 lato-bold uppercase tracking-wide mb-3">
                            Notas internas
                            @if ($fb->comments->isNotEmpty())
                                <span class="ml-1 text-slate-300">({{ $fb->comments->count() }})</span>
                            @endif
                        </p>

                        @forelse ($fb->comments->sortByDesc('created_at') as $comment)
                            <div class="flex items-start gap-2.5 mb-3">
                                <div class="w-7 h-7 rounded-full bg-gradient-to-br from-indigo-400 to-indigo-600
                                            flex items-center justify-center text-white text-[10px] font-bold lato-black shrink-0">
                                    {{ mb_strtoupper(mb_substr($comment->user?->name ?? '?', 0, 1)) }}
                                </div>
                                <div class="flex-1 bg-slate-50 dark:bg-slate-700/50 rounded-xl px-3 py-2.5">
                                    <div class="flex items-center justify-between gap-2 mb-1">
                                        <p class="text-xs font-semibold text-slate-700 dark:text-slate-200 lato-bold">
                                            {{ $comment->user?->name ?? '—' }}
                                        </p>
                                        <div class="flex items-center gap-1">
                                            <p class="text-[11px] text-slate-400 lato-regular">{{ $comment->created_at->diffForHumans() }}</p>
                                            @if ($comment->user_id === auth()->id() || auth()->user()?->isRhOuDp())
                                                <button type="button" wire:click="deleteComment({{ $comment->id }})"
                                                        class="p-0.5 text-slate-300 hover:text-red-500 transition cursor-pointer rounded">
                                                    <x-lucide-x class="w-3 h-3" />
                                                </button>
                                            @endif
                                        </div>
                                    </div>
                                    <p class="text-sm text-slate-600 dark:text-slate-300 lato-regular leading-relaxed">{{ $comment->comment }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-xs text-slate-400 lato-regular italic mb-3">Nenhuma nota interna ainda.</p>
                        @endforelse

                        <div class="flex gap-2 mt-2">
                            <input type="text" wire:model="newComment"
                                   wire:keydown.enter="addComment({{ $fb->id }})"
                                   placeholder="Adicionar nota interna..."
                                   class="flex-1 px-3 py-2 text-sm lato-regular rounded-xl bg-white dark:bg-slate-700
                                          border border-slate-200 dark:border-slate-600 text-slate-700 dark:text-slate-200
                                          placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-500/40">
                            <button type="button" wire:click="addComment({{ $fb->id }})"
                                    class="px-4 py-2 text-xs lato-bold bg-gradient-to-r from-blue-500 to-indigo-600 hover:from-blue-600 hover:to-indigo-700 text-white
                                           shadow-sm shadow-blue-500/20 rounded-xl transition cursor-pointer shrink-0">
                                Enviar
                            </button>
                        </div>
                        @error('newComment')
                            <p class="flex items-center gap-1 text-xs text-red-500 lato-regular mt-1">
                                <x-lucide-alert-circle class="w-3 h-3" />{{ $message }}
                            </p>
                        @enderror
                    </div>

                </div>

                <div class="flex-shrink-0 px-4 py-3 sm:px-6 sm:py-4 border-t border-slate-100 dark:border-slate-700
                            bg-white dark:bg-slate-800 flex justify-between items-center">
                    <button type="button" wire:click="editar({{ $fb->id }})" @click="$wire.fecharView()"
                            class="flex items-center gap-2 px-4 py-2 text-sm lato-bold text-blue-600 border border-blue-200
                                   rounded-xl hover:bg-blue-50 dark:hover:bg-blue-900/20 transition cursor-pointer">
                        <x-lucide-pencil class="w-4 h-4" />
                        Editar
                    </button>
                    <button type="button" wire:click="fecharView"
                            class="px-4 py-2 text-sm lato-bold text-slate-600 dark:text-slate-300 border border-slate-200
                                   dark:border-slate-700 rounded-xl hover:bg-slate-50 dark:hover:bg-slate-800 transition cursor-pointer">
                        Fechar
                    </button>
                </div>
            </div>
        </div>
    @endif
