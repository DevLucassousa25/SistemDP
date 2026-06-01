<?php

namespace App\Livewire\Pages\Tasks;

use App\Livewire\SecureComponent;
use App\Models\FeedbackActionTask;
use App\Models\Task;
use App\Models\TaskSubtask;
use App\Models\TaskTag;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class Index extends SecureComponent
{
    // ── UI state ──────────────────────────────────────────────────────
    public string $activeTab = 'minhas';   // minhas|departamento|plano_acao|relatorio
    public string $viewMode  = 'list';     // list|kanban


    // ── Filtros ───────────────────────────────────────────────────────
    public string $search           = '';
    public string $statusFilter     = '';
    public string $priorityFilter   = '';
    public array  $tagFilter        = [];
    public string $groupBy          = 'none';   // none|priority|due_date
    public string $dueDateFilter    = '';       // ''|hoje|esta_semana|proximos_7|vencidas|sem_prazo
    public string $recurrenceFilter = '';       // ''|sim|nao
    public ?int   $assigneeFilter   = null;

    // ─────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();
        $this->activeTab = 'minhas';
    }

    // ── Query base ────────────────────────────────────────────────────
    private function applyFilters($query)
    {
        return $query
            ->when($this->search, function ($q) {
                $q->where(function ($q2) {
                    $q2->where('title', 'like', "%{$this->search}%")
                       ->orWhere('description', 'like', "%{$this->search}%");
                });
            })
            ->when($this->statusFilter,   fn ($q) => $q->where('status',   $this->statusFilter))
            ->when($this->priorityFilter, fn ($q) => $q->where('priority', $this->priorityFilter))
            ->when($this->tagFilter,      fn ($q) => $q->whereHas('tags',  fn ($tq) => $tq->whereIn('task_tags.id', $this->tagFilter)))
            ->when($this->dueDateFilter, function ($q) {
                $today = now()->toDateString();
                if ($this->dueDateFilter === 'hoje') {
                    $q->whereDate('due_date', $today);
                } elseif ($this->dueDateFilter === 'esta_semana') {
                    $q->whereBetween('due_date', [now()->startOfWeek()->toDateString(), now()->endOfWeek()->toDateString()]);
                } elseif ($this->dueDateFilter === 'proximos_7') {
                    $q->whereBetween('due_date', [$today, now()->addDays(7)->toDateString()]);
                } elseif ($this->dueDateFilter === 'vencidas') {
                    $q->whereNotNull('due_date')
                      ->where('due_date', '<', $today)
                      ->whereNotIn('status', ['concluida', 'cancelada']);
                } elseif ($this->dueDateFilter === 'sem_prazo') {
                    $q->whereNull('due_date');
                }
            })
            ->when($this->recurrenceFilter, function ($q) {
                if ($this->recurrenceFilter === 'sim') $q->whereNotNull('recurrence');
                if ($this->recurrenceFilter === 'nao') $q->whereNull('recurrence');
            })
            ->when($this->assigneeFilter, fn ($q) => $q->where('assigned_to', $this->assigneeFilter));
    }

    private function baseOrder($query)
    {
        return $query
            ->orderByRaw("CASE status WHEN 'em_andamento' THEN 1 WHEN 'pendente' THEN 2 WHEN 'concluida' THEN 3 ELSE 4 END")
            ->orderByRaw("CASE priority WHEN 'alta' THEN 1 WHEN 'media' THEN 2 ELSE 3 END")
            ->orderBy('due_date');
    }

    // ── Minhas tarefas ────────────────────────────────────────────────
    #[Computed]
    public function myTasks()
    {
        return $this->baseOrder(
            $this->applyFilters(
                Task::with(['assignedBy', 'subtasks', 'tags', 'comments'])
                    ->where('assigned_to', Auth::id())
            )
        )->get();
    }

    // ── Tarefas do departamento ───────────────────────────────────────
    #[Computed]
    public function deptTasks()
    {
        $user = Auth::user();
        if (! $user->isGerente()) return collect();

        return $this->baseOrder(
            $this->applyFilters(
                Task::with(['assignedTo.department', 'assignedBy', 'subtasks', 'tags', 'comments'])
                    ->whereHas('assignedTo', fn ($q) => $q->where('department_id', $user->department_id))
                    ->where('assigned_to', '!=', Auth::id())
            )
        )->get();
    }

    // ── Kanban: tarefas agrupadas por status ──────────────────────────
    #[Computed]
    public function kanbanTasks()
    {
        $user = Auth::user();

        $query = $this->applyFilters(
            Task::with(['assignedBy', 'subtasks', 'tags', 'comments', 'assignedTo'])
        );

        if ($user->isGerente()) {
            $query->whereHas('assignedTo', fn ($q) => $q->where('department_id', $user->department_id));
        } else {
            $query->where('assigned_to', Auth::id());
        }

        return $query->orderByRaw("CASE priority WHEN 'alta' THEN 1 WHEN 'media' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->get()
            ->groupBy('status');
    }

    // ── Plano de ação ─────────────────────────────────────────────────
    #[Computed]
    public function actionPlanTasks()
    {
        $user  = Auth::user();
        $query = FeedbackActionTask::with(['feedback.employee', 'feedback.evaluator', 'responsible'])
            ->whereNotNull('responsible_id');

        if ($user->isGerente()) {
            $query->whereHas('feedback.employee', fn ($q) => $q->where('department_id', $user->department_id));
        } else {
            $query->where('responsible_id', Auth::id());
        }

        return $query->orderBy('completed')->orderBy('due_date')->get();
    }

    // ── Usuários atribuíveis (filtro) ─────────────────────────────────
    #[Computed]
    public function assignableUsers()
    {
        $user = Auth::user();
        if (! $user->isGerente()) return collect();

        return User::notAdmin()
            ->where('department_id', $user->department_id)
            ->where('id', '!=', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // ── Tags disponíveis (filtro) ─────────────────────────────────────
    #[Computed]
    public function allTags()
    {
        return TaskTag::where('created_by', auth()->id())->orderBy('name')->get();
    }

    // ── Stats ─────────────────────────────────────────────────────────
    #[Computed]
    public function stats(): array
    {
        $mine = Task::where('assigned_to', Auth::id());

        return [
            'total'        => (clone $mine)->count(),
            'pendentes'    => (clone $mine)->where('status', 'pendente')->count(),
            'em_andamento' => (clone $mine)->where('status', 'em_andamento')->count(),
            'concluidas'   => (clone $mine)->where('status', 'concluida')->count(),
            'vencidas'     => (clone $mine)
                ->whereNotIn('status', ['concluida', 'cancelada'])
                ->whereNotNull('due_date')
                ->where('due_date', '<', now()->toDateString())
                ->count(),
        ];
    }

    // ── Pendências do plano de ação ───────────────────────────────────
    #[Computed]
    public function pendingActionCount(): int
    {
        $user  = Auth::user();
        $query = FeedbackActionTask::whereNotNull('responsible_id')->where('completed', false);

        if ($user->isGerente()) {
            $query->whereHas('feedback.employee', fn ($q) => $q->where('department_id', $user->department_id));
        } else {
            $query->where('responsible_id', Auth::id());
        }

        return $query->count();
    }

    // ── Relatório de produtividade ────────────────────────────────────
    #[Computed]
    public function reportData(): array
    {
        $user       = Auth::user();
        $isGerente  = $user->isGerente();
        $today      = now()->toDateString();
        $mine       = Task::where('assigned_to', Auth::id());

        // ── KPIs individuais ──────────────────────────────────────────
        $total        = (clone $mine)->count();
        $concluidas   = (clone $mine)->where('status', 'concluida')->count();
        $emAndamento  = (clone $mine)->where('status', 'em_andamento')->count();
        $pendentes    = (clone $mine)->where('status', 'pendente')->count();
        $canceladas   = (clone $mine)->where('status', 'cancelada')->count();
        $vencidas     = (clone $mine)
                            ->whereNotIn('status', ['concluida', 'cancelada'])
                            ->whereNotNull('due_date')
                            ->where('due_date', '<', $today)->count();
        $recorrentes  = (clone $mine)->whereNotNull('recurrence')->count();

        // Taxa de conclusão no prazo
        $comPrazo = (clone $mine)->where('status', 'concluida')
                        ->whereNotNull('due_date')->whereNotNull('completed_at')->count();
        $noPrazo  = (clone $mine)->where('status', 'concluida')
                        ->whereNotNull('due_date')->whereNotNull('completed_at')
                        ->whereColumn('completed_at', '<=', 'due_date')->count();
        $taxaPrazo = $comPrazo > 0 ? round($noPrazo / $comPrazo * 100) : null;

        // Tempo médio geral (dias)
        $avgDays = (clone $mine)->where('status', 'concluida')
            ->whereNotNull('completed_at')
            ->selectRaw('AVG(EXTRACT(EPOCH FROM (completed_at - created_at)) / 86400) as avg_days')
            ->value('avg_days');

        // Tempo médio por prioridade
        $avgByPriority = [];
        foreach (['alta', 'media', 'baixa'] as $p) {
            $avg = (clone $mine)->where('status', 'concluida')->where('priority', $p)
                    ->whereNotNull('completed_at')
                    ->selectRaw('AVG(EXTRACT(EPOCH FROM (completed_at - created_at)) / 86400) as avg_days')
                    ->value('avg_days');
            $avgByPriority[$p] = $avg ? round((float) $avg, 1) : null;
        }

        // Últimos 7 dias
        $days7 = collect();
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $days7->push([
                'date'       => now()->subDays($i)->format('d/m'),
                'criadas'    => (clone $mine)->whereDate('created_at', $date)->count(),
                'concluidas' => (clone $mine)->whereDate('completed_at', $date)->count(),
            ]);
        }

        // Heatmap últimos 30 dias
        $days30 = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = now()->subDays($i)->toDateString();
            $days30->push([
                'date'       => $date,
                'label'      => now()->subDays($i)->format('d/m'),
                'diaSemana'  => now()->subDays($i)->locale('pt')->dayName,
                'concluidas' => (clone $mine)->whereDate('completed_at', $date)->count(),
            ]);
        }

        // Por prioridade / status
        $byPriority = [
            'alta'  => (clone $mine)->where('priority', 'alta')->count(),
            'media' => (clone $mine)->where('priority', 'media')->count(),
            'baixa' => (clone $mine)->where('priority', 'baixa')->count(),
        ];
        $byStatus = [
            'pendente'     => $pendentes,
            'em_andamento' => $emAndamento,
            'concluida'    => $concluidas,
            'cancelada'    => $canceladas,
        ];

        // Top 5 tags
        $topTags = DB::table('task_tag')
            ->join('task_tags', 'task_tag.task_tag_id', '=', 'task_tags.id')
            ->join('tasks',     'task_tag.task_id',     '=', 'tasks.id')
            ->where('tasks.assigned_to', Auth::id())
            ->select('task_tags.name', 'task_tags.color', DB::raw('count(*) as total'))
            ->groupBy('task_tags.id', 'task_tags.name', 'task_tags.color')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $result = [
            'total'          => $total,
            'concluidas'     => $concluidas,
            'emAndamento'    => $emAndamento,
            'pendentes'      => $pendentes,
            'canceladas'     => $canceladas,
            'vencidas'       => $vencidas,
            'recorrentes'    => $recorrentes,
            'taxaPrazo'      => $taxaPrazo,
            'avgDays'        => round((float) $avgDays, 1),
            'avgByPriority'  => $avgByPriority,
            'days'           => $days7,
            'days30'         => $days30,
            'byPriority'     => $byPriority,
            'byStatus'       => $byStatus,
            'topTags'        => $topTags,
            'dept'           => null,
        ];

        // ── Relatório do departamento (apenas gestores) ───────────────
        if ($isGerente) {
            $dBase = Task::whereHas('assignedTo', fn ($q) => $q->where('department_id', $user->department_id));

            $dTotal       = (clone $dBase)->count();
            $dConcluidas  = (clone $dBase)->where('status', 'concluida')->count();
            $dVencidas    = (clone $dBase)->whereNotIn('status', ['concluida','cancelada'])
                                ->whereNotNull('due_date')->where('due_date', '<', $today)->count();
            $dEmAndamento = (clone $dBase)->where('status', 'em_andamento')->count();
            $dPendentes   = (clone $dBase)->where('status', 'pendente')->count();
            $dCanceladas  = (clone $dBase)->where('status', 'cancelada')->count();

            $dAvgDays = (clone $dBase)->where('status', 'concluida')
                ->whereNotNull('completed_at')
                ->selectRaw('AVG(EXTRACT(EPOCH FROM (completed_at - created_at)) / 86400) as avg_days')
                ->value('avg_days');

            $dComPrazo = (clone $dBase)->where('status', 'concluida')
                            ->whereNotNull('due_date')->whereNotNull('completed_at')->count();
            $dNoPrazo  = (clone $dBase)->where('status', 'concluida')
                            ->whereNotNull('due_date')->whereNotNull('completed_at')
                            ->whereColumn('completed_at', '<=', 'due_date')->count();
            $dTaxaPrazo = $dComPrazo > 0 ? round($dNoPrazo / $dComPrazo * 100) : null;

            // Últimos 7 dias do departamento
            $dDays7 = collect();
            for ($i = 6; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $dDays7->push([
                    'date'       => now()->subDays($i)->format('d/m'),
                    'criadas'    => (clone $dBase)->whereDate('created_at', $date)->count(),
                    'concluidas' => (clone $dBase)->whereDate('completed_at', $date)->count(),
                ]);
            }

            // Últimos 30 dias do departamento
            $dDays30 = collect();
            for ($i = 29; $i >= 0; $i--) {
                $date = now()->subDays($i)->toDateString();
                $dDays30->push([
                    'date'       => $date,
                    'label'      => now()->subDays($i)->format('d/m'),
                    'concluidas' => (clone $dBase)->whereDate('completed_at', $date)->count(),
                    'criadas'    => (clone $dBase)->whereDate('created_at', $date)->count(),
                ]);
            }

            // Por prioridade do departamento
            $dByPriority = [
                'alta'  => (clone $dBase)->where('priority', 'alta')->count(),
                'media' => (clone $dBase)->where('priority', 'media')->count(),
                'baixa' => (clone $dBase)->where('priority', 'baixa')->count(),
            ];

            // Por colaborador (expandido)
            $byUser = User::notAdmin()
                ->where('department_id', $user->department_id)
                ->where('is_active', true)
                ->withCount([
                    'tasks as total_tasks',
                    'tasks as done_tasks'      => fn ($q) => $q->where('status', 'concluida'),
                    'tasks as in_progress'     => fn ($q) => $q->where('status', 'em_andamento'),
                    'tasks as pending_tasks'   => fn ($q) => $q->where('status', 'pendente'),
                    'tasks as cancelled_tasks' => fn ($q) => $q->where('status', 'cancelada'),
                    'tasks as overdue_tasks'   => fn ($q) => $q
                        ->whereNotIn('status', ['concluida','cancelada'])
                        ->whereNotNull('due_date')->where('due_date', '<', $today),
                    'tasks as on_time_tasks'   => fn ($q) => $q
                        ->where('status', 'concluida')
                        ->whereNotNull('due_date')->whereNotNull('completed_at')
                        ->whereColumn('completed_at', '<=', 'due_date'),
                    'tasks as with_due_done'   => fn ($q) => $q
                        ->where('status', 'concluida')
                        ->whereNotNull('due_date')->whereNotNull('completed_at'),
                ])
                ->orderByDesc('done_tasks')
                ->get()
                ->filter(fn ($u) => $u->total_tasks > 0)
                ->values()
                ->map(function ($u) {
                    $avg = Task::where('assigned_to', $u->id)
                        ->where('status', 'concluida')
                        ->whereNotNull('completed_at')
                        ->selectRaw('AVG(EXTRACT(EPOCH FROM (completed_at - created_at)) / 86400) as avg_days')
                        ->value('avg_days');
                    $u->avg_days      = $avg ? round((float) $avg, 1) : null;
                    $u->taxa_conclusao = $u->total_tasks > 0 ? round($u->done_tasks / $u->total_tasks * 100) : 0;
                    $u->taxa_prazo    = $u->with_due_done > 0 ? round($u->on_time_tasks / $u->with_due_done * 100) : null;
                    return $u;
                });

            // Top tags do departamento
            $dTopTags = DB::table('task_tag')
                ->join('task_tags', 'task_tag.task_tag_id', '=', 'task_tags.id')
                ->join('tasks',     'task_tag.task_id',     '=', 'tasks.id')
                ->join('users',     'tasks.assigned_to',    '=', 'users.id')
                ->where('users.department_id', $user->department_id)
                ->select('task_tags.name', 'task_tags.color', DB::raw('count(*) as total'))
                ->groupBy('task_tags.id', 'task_tags.name', 'task_tags.color')
                ->orderByDesc('total')
                ->limit(6)
                ->get();

            $result['dept'] = [
                'total'       => $dTotal,
                'concluidas'  => $dConcluidas,
                'vencidas'    => $dVencidas,
                'emAndamento' => $dEmAndamento,
                'pendentes'   => $dPendentes,
                'canceladas'  => $dCanceladas,
                'avgDays'     => round((float) $dAvgDays, 1),
                'taxaPrazo'   => $dTaxaPrazo,
                'days'        => $dDays7,
                'days30'      => $dDays30,
                'byUser'      => $byUser,
                'byPriority'  => $dByPriority,
                'topTags'     => $dTopTags,
                'totalUsers'  => $byUser->count(),
                'mvp'         => $byUser->sortByDesc('done_tasks')->first(),
                'maisVencidas'=> $byUser->sortByDesc('overdue_tasks')->first(),
            ];
        }

        return $result;
    }

    // ─────────────────────────────────────────────────────────────────
    // Abrir modais — dispara eventos para os componentes filhos
    // ─────────────────────────────────────────────────────────────────

    public function abrirModal(): void
    {
        $this->dispatch('open-task-form');
    }

    public function confirmarExclusao(int $id): void
    {
        $this->dispatch('confirm-delete-task', taskId: $id);
    }

    public function editar(int $id): void
    {
        $this->dispatch('edit-task', taskId: $id);
    }

    public function abrirComentarios(int $taskId): void
    {
        $this->dispatch('open-comment-drawer', taskId: $taskId);
    }

    public function abrirValidacao(int $taskId): void
    {
        $task     = FeedbackActionTask::with('feedback.employee')->findOrFail($taskId);
        $user     = Auth::user();
        $employee = $task->feedback?->employee;

        $isGestorDireto = $user->isGerente()
            && $employee
            && $employee->department_id === $user->department_id;

        if (! $isGestorDireto && ! $user->isRhOuDp()) {
            $this->alertError('Apenas o gestor direto ou o RH pode validar tarefas do plano de ação.');
            return;
        }

        $this->dispatch('open-validation-modal', taskId: $taskId);
    }

    // ─────────────────────────────────────────────────────────────────
    // Listeners de eventos dos componentes filhos
    // ─────────────────────────────────────────────────────────────────

    #[On('task-saved')]
    public function onTaskSaved(): void
    {
        $this->clearComputed();
    }

    #[On('task-deleted')]
    public function onTaskDeleted(): void
    {
        $this->clearComputed();
    }

    #[On('task-validated')]
    public function onTaskValidated(): void
    {
        unset($this->actionPlanTasks, $this->pendingActionCount);
    }

    #[On('tags-updated')]
    public function onTagsUpdated(): void
    {
        unset($this->allTags);
    }

    // ─────────────────────────────────────────────────────────────────
    // Ações inline (não precisam de modal)
    // ─────────────────────────────────────────────────────────────────

    public function atualizarStatus(int $id, string $status): void
    {
        $allowed = ['pendente', 'em_andamento', 'concluida', 'cancelada'];
        if (! in_array($status, $allowed, true)) return;

        $task = Task::findOrFail($id);
        $this->authorizeTaskAccess($task);

        $wasNotDone = $task->status !== 'concluida';
        $data = ['status' => $status];

        if ($status === 'concluida' && $wasNotDone) {
            $data['completed_at'] = now();
            $this->criarTarefaRecorrente($task, $data);
        } elseif ($status !== 'concluida') {
            $data['completed_at'] = null;
        }

        $task->update($data);
        $this->clearComputed();
        $this->alertSuccess('Status atualizado.');
    }

    public function moverKanban(int $taskId, string $newStatus): void
    {
        $allowed = ['pendente', 'em_andamento', 'concluida', 'cancelada'];
        if (! in_array($newStatus, $allowed, true)) return;

        $task = Task::findOrFail($taskId);
        $this->authorizeTaskAccess($task);

        $wasNotDone = $task->status !== 'concluida';
        $data = ['status' => $newStatus];

        if ($newStatus === 'concluida' && $wasNotDone) {
            $data['completed_at'] = now();
            $this->criarTarefaRecorrente($task, $data);
        } elseif ($newStatus !== 'concluida') {
            $data['completed_at'] = null;
        }

        $task->update($data);
        $this->clearComputed();
        $this->dispatch('kanban-atualizado');
    }

    public function toggleSubtarefa(int $subtaskId): void
    {
        $sub       = TaskSubtask::findOrFail($subtaskId);
        $completed = ! $sub->completed;

        $sub->update([
            'completed'    => $completed,
            'completed_at' => $completed ? now() : null,
            'completed_by' => $completed ? Auth::id() : null,
        ]);

        $this->clearComputed();
    }

    // ── Plano de ação: desfazer validação (RH apenas) ────────────────
    public function desfazerValidacao(int $taskId): void
    {
        if (! Auth::user()->isRhOuDp()) {
            $this->alertError('Apenas o RH pode desfazer uma validação.');
            return;
        }

        $task = FeedbackActionTask::findOrFail($taskId);
        $task->update([
            'completed'       => false,
            'completed_at'    => null,
            'completed_by'    => null,
            'validation_note' => null,
        ]);

        unset($this->actionPlanTasks, $this->pendingActionCount);
        $this->alertSuccess('Validação desfeita.');
    }

    // ── Filtros / UI ──────────────────────────────────────────────────
    public function toggleTagFilter(int $tagId): void
    {
        if (in_array($tagId, $this->tagFilter)) {
            $this->tagFilter = array_values(array_filter($this->tagFilter, fn ($id) => $id !== $tagId));
        } else {
            $this->tagFilter[] = $tagId;
        }
        $this->clearComputed();
    }

    public function limparFiltros(): void
    {
        $this->search           = '';
        $this->statusFilter     = '';
        $this->priorityFilter   = '';
        $this->tagFilter        = [];
        $this->groupBy          = 'none';
        $this->dueDateFilter    = '';
        $this->recurrenceFilter = '';
        $this->assigneeFilter   = null;
        $this->clearComputed();
    }

    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['list', 'kanban']) ? $mode : 'list';
    }

    // ─────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────

    private function authorizeTaskAccess(Task $task): void
    {
        $user = Auth::user();

        if ($user->isGerente()) {
            $employee = User::find($task->assigned_to);
            if ($employee?->department_id === $user->department_id) return;
        }

        if ($task->assigned_to === Auth::id() || $task->assigned_by === Auth::id()) return;

        abort(403, 'Sem permissão para acessar esta tarefa.');
    }

    private function clearComputed(): void
    {
        unset($this->myTasks, $this->deptTasks, $this->kanbanTasks, $this->stats, $this->pendingActionCount, $this->reportData);
    }

    private function criarTarefaRecorrente(Task $task, array $data): void
    {
        if (! $task->recurrence || ! $task->due_date) return;

        $endDate = $task->recurrence_ends_at;
        $nextDue = match ($task->recurrence) {
            'daily'   => $task->due_date->addDay(),
            'weekly'  => $task->due_date->addWeek(),
            'monthly' => $task->due_date->addMonth(),
            'yearly'  => $task->due_date->addYear(),
            default   => null,
        };

        if (! $nextDue) return;
        if ($endDate && $nextDue->gt($endDate)) return;

        Task::create([
            'title'              => $task->title,
            'description'        => $task->description,
            'assigned_to'        => $task->assigned_to,
            'assigned_by'        => $task->assigned_by,
            'due_date'           => $nextDue,
            'priority'           => $task->priority,
            'status'             => 'pendente',
            'recurrence'         => $task->recurrence,
            'recurrence_ends_at' => $task->recurrence_ends_at,
            'estimated_minutes'  => $task->estimated_minutes,
        ]);
    }

    // ─────────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.tasks.index');
    }
}
