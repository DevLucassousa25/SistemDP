{{-- Partial: card de tarefa individual --}}
{{-- Variáveis esperadas: $task (Task), $showAssignee (bool) --}}
<div class="group bg-white dark:bg-slate-800 rounded-xl border
            {{ $task->is_overdue
                ? 'border-red-200 dark:border-red-800/40'
                : 'border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600' }}
            hover:shadow-sm transition-all duration-200 p-4"
     x-data="{ expanded: false }">

    {{-- Linha principal: checkbox + conteúdo --}}
    <div class="flex items-start gap-3">

        {{-- Checkbox --}}
        <button type="button"
                wire:click="atualizarStatus({{ $task->id }}, '{{ $task->status === 'concluida' ? 'pendente' : 'concluida' }}')"
                class="mt-0.5 w-[18px] h-[18px] shrink-0 rounded-full flex items-center justify-center cursor-pointer transition-all duration-200
                       {{ $task->status === 'concluida'
                           ? 'bg-emerald-500 hover:bg-emerald-600'
                           : 'border-2 border-slate-300 dark:border-slate-600 hover:border-emerald-400 dark:hover:border-emerald-500' }}">
            @if ($task->status === 'concluida')
                <x-lucide-check class="w-2.5 h-2.5 text-white" />
            @endif
        </button>

        {{-- Conteúdo --}}
        <div class="flex-1 min-w-0">

            {{-- Título + ações --}}
            <div class="flex items-start justify-between gap-2">
                <p class="text-sm lato-bold leading-snug
                           {{ in_array($task->status, ['concluida', 'cancelada'])
                               ? 'line-through text-slate-400 dark:text-slate-500'
                               : 'text-slate-700 dark:text-slate-200' }}">
                    {{ $task->title }}
                </p>
                <div class="flex items-center gap-0.5 opacity-0 group-hover:opacity-100 transition-opacity shrink-0 -mr-1">
                    <button type="button" wire:click="editar({{ $task->id }})"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-blue-500 hover:bg-blue-50 dark:hover:bg-blue-900/20 transition cursor-pointer">
                        <x-lucide-pencil class="w-3.5 h-3.5" />
                    </button>
                    <button type="button" wire:click="confirmarExclusao({{ $task->id }})"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-900/20 transition cursor-pointer disabled:opacity-60"
                        wire:loading.attr="disabled">
                        <span wire:loading.remove wire:target="confirmarExclusao" class="flex items-center gap-1.5">
                            <x-lucide-trash-2 class="w-3.5 h-3.5" />
                        </span>
                        <span wire:loading wire:target="confirmarExclusao" class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                        </span>
                    </button>
                </div>
            </div>

            {{-- Descrição --}}
            @if ($task->description)
                <p class="text-xs text-slate-400 dark:text-slate-500 lato-regular mt-1 leading-relaxed line-clamp-2">
                    {{ $task->description }}
                </p>
            @endif

            {{-- Tags --}}
            @if ($task->tags->isNotEmpty())
                <div class="flex flex-wrap gap-1 mt-2">
                    @foreach ($task->tags as $tag)
                        <span class="text-[10px] lato-bold px-2 py-0.5 rounded-full text-white"
                              style="background: {{ $tag->color }}">{{ $tag->name }}</span>
                    @endforeach
                </div>
            @endif

            {{-- Meta row --}}
            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 mt-2.5">

                {{-- Prioridade como ponto colorido + label --}}
                <span class="flex items-center gap-1 text-[11px] lato-bold
                             {{ $task->priority === 'alta'  ? 'text-red-500'
                             : ($task->priority === 'media' ? 'text-amber-500'
                             : 'text-slate-400 dark:text-slate-500') }}">
                    <span class="w-1.5 h-1.5 rounded-full
                                 {{ $task->priority === 'alta'  ? 'bg-red-400'
                                 : ($task->priority === 'media' ? 'bg-amber-400'
                                 : 'bg-slate-300 dark:bg-slate-600') }}">
                    </span>
                    {{ $task->priority_label }}
                </span>

                {{-- Status (apenas se não for pendente) --}}
                @if ($task->status !== 'pendente')
                    <span class="text-[11px] lato-bold px-2 py-0.5 rounded-full {{ $task->status_color }}">
                        {{ $task->status_label }}
                    </span>
                @endif

                {{-- Prazo --}}
                @if ($task->due_date)
                    <span class="flex items-center gap-1 text-[11px] lato-regular
                                 {{ $task->is_overdue
                                     ? 'text-red-500 dark:text-red-400 font-semibold'
                                     : 'text-slate-400 dark:text-slate-500' }}">
                        <x-lucide-calendar class="w-3 h-3 shrink-0" />
                        {{ $task->due_date->format('d/m/Y') }}
                        @if ($task->is_overdue)
                            <span class="text-[10px] bg-red-100 dark:bg-red-900/30 text-red-500 dark:text-red-400 lato-bold px-1.5 py-0.5 rounded-full">vencida</span>
                        @endif
                    </span>
                @endif

                {{-- Tempo estimado --}}
                @if ($task->estimated_time_formatted)
                    <span class="flex items-center gap-1 text-[11px] lato-regular text-slate-400 dark:text-slate-500">
                        <x-lucide-timer class="w-3 h-3 shrink-0" />
                        {{ $task->estimated_time_formatted }}
                    </span>
                @endif

                {{-- Recorrência --}}
                @if ($task->recurrence)
                    <span class="flex items-center gap-1 text-[11px] lato-regular text-indigo-400 dark:text-indigo-500">
                        <x-lucide-repeat class="w-3 h-3 shrink-0" />
                        {{ $task->recurrence_label }}
                    </span>
                @endif

                {{-- Atribuída por --}}
                @if ($task->assignedBy && $task->assigned_by !== $task->assigned_to)
                    <span class="flex items-center gap-1 text-[11px] lato-regular text-slate-400 dark:text-slate-500">
                        <x-lucide-user-check class="w-3 h-3 shrink-0" />
                        {{ Str::before($task->assignedBy->name, ' ') }}
                    </span>
                @endif

                {{-- Responsável (aba departamento) --}}
                @if (!empty($showAssignee) && $task->assignedTo)
                    @php
                        $parts       = explode(' ', trim($task->assignedTo->name));
                        $initials    = strtoupper(substr($parts[0], 0, 1) . (isset($parts[1]) ? substr($parts[1], 0, 1) : ''));
                        $avatarPalette = ['bg-indigo-400','bg-violet-400','bg-pink-400','bg-teal-400','bg-amber-500','bg-blue-400'];
                        $avatarColor   = $avatarPalette[abs(crc32($task->assignedTo->name)) % count($avatarPalette)];
                    @endphp
                    <span class="flex items-center gap-1.5 text-[11px] lato-regular text-slate-500 dark:text-slate-400">
                        <span class="w-4 h-4 rounded-full {{ $avatarColor }} text-white text-[8px] lato-bold flex items-center justify-center shrink-0">
                            {{ $initials }}
                        </span>
                        {{ Str::before($task->assignedTo->name, ' ') }}
                    </span>
                @endif

                {{-- Concluída em --}}
                @if ($task->completed_at && $task->status === 'concluida')
                    <span class="flex items-center gap-1 text-[11px] lato-regular text-emerald-500">
                        <x-lucide-circle-check class="w-3 h-3 shrink-0" />
                        {{ $task->completed_at->format('d/m/Y') }}
                    </span>
                @endif

                {{-- Comentários --}}
                <button type="button" wire:click="abrirComentarios({{ $task->id }})"
                        class="flex items-center gap-1 text-[11px] lato-regular transition cursor-pointer ml-auto
                               {{ $task->comments_count > 0
                                   ? 'text-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-300'
                                   : 'text-slate-300 dark:text-slate-600 hover:text-slate-500 dark:hover:text-slate-400' }}">
                    <x-lucide-message-circle class="w-3.5 h-3.5" />
                    @if ($task->comments_count > 0)
                        <span class="lato-bold">{{ $task->comments_count }}</span>
                    @endif
                </button>

                {{-- Subtarefas toggle --}}
                @if ($task->subtasks_total > 0)
                    <button type="button" @click="expanded = !expanded"
                            class="flex items-center gap-1 text-[11px] lato-bold transition cursor-pointer
                                   {{ $task->subtasks_done === $task->subtasks_total
                                       ? 'text-emerald-500 dark:text-emerald-400'
                                       : 'text-slate-400 dark:text-slate-500 hover:text-slate-600 dark:hover:text-slate-300' }}">
                        <x-lucide-list-checks class="w-3.5 h-3.5" />
                        {{ $task->subtasks_done }}/{{ $task->subtasks_total }}
                        <x-lucide-chevron-down class="w-3 h-3 transition-transform duration-150"
                                               x-bind:class="expanded ? 'rotate-180' : ''" />
                    </button>
                @endif
            </div>

            {{-- Barra de progresso das subtarefas --}}
            @if ($task->subtasks_total > 0)
                <div class="mt-2 w-full bg-slate-100 dark:bg-slate-700 rounded-full h-1 overflow-hidden">
                    <div class="h-1 rounded-full transition-all duration-500
                                {{ $task->subtasks_progress === 100 ? 'bg-emerald-400' : 'bg-blue-400' }}"
                         style="width: {{ $task->subtasks_progress }}%">
                    </div>
                </div>
            @endif
        </div>
    </div>

    {{-- Subtarefas expandidas --}}
    @if ($task->subtasks_total > 0)
        <div x-show="expanded"
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-1"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-100"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-1"
             class="mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/70">
            <ul class="space-y-2">
                @foreach ($task->subtasks as $sub)
                    <li class="flex items-center gap-2 group/sub">
                        <button type="button"
                                wire:click="toggleSubtarefa({{ $sub->id }})"
                                class="w-4 h-4 rounded-full shrink-0 flex items-center justify-center transition cursor-pointer
                                       {{ $sub->completed
                                           ? 'bg-emerald-500 hover:bg-emerald-600'
                                           : 'border-2 border-slate-300 dark:border-slate-600 hover:border-emerald-400' }}">
                            @if ($sub->completed)
                                <x-lucide-check class="w-2.5 h-2.5 text-white" />
                            @endif
                        </button>
                        <span class="text-xs lato-regular flex-1 leading-snug
                                     {{ $sub->completed
                                         ? 'line-through text-slate-400 dark:text-slate-500'
                                         : 'text-slate-600 dark:text-slate-300' }}">
                            {{ $sub->title }}
                        </span>
                        @if ($sub->completed && $sub->completed_at)
                            <span class="text-[10px] text-slate-400 lato-regular shrink-0 opacity-0 group-hover/sub:opacity-100 transition-opacity">
                                {{ $sub->completed_at->format('d/m') }}
                            </span>
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- Troca rápida de status (hover) --}}
    @if (! in_array($task->status, ['concluida', 'cancelada']))
        <div class="flex items-center gap-1.5 mt-3 pt-3 border-t border-slate-100 dark:border-slate-700/70
                    opacity-0 group-hover:opacity-100 transition-opacity duration-150">
            @foreach ([
                'pendente'     => 'Pendente',
                'em_andamento' => 'Em andamento',
                'concluida'    => 'Concluída',
            ] as $val => $lbl)
                <button type="button"
                        wire:click="atualizarStatus({{ $task->id }}, '{{ $val }}')"
                        class="text-[10px] lato-bold px-2.5 py-1 rounded-lg border transition cursor-pointer
                               {{ $task->status === $val
                                   ? 'bg-slate-700 dark:bg-slate-200 text-white dark:text-slate-800 border-transparent'
                                   : 'border-slate-200 dark:border-slate-600 text-slate-400 dark:text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-700/60 hover:text-slate-600 dark:hover:text-slate-300' }}">
                    {{ $lbl }}
                </button>
            @endforeach
        </div>
    @endif
</div>
