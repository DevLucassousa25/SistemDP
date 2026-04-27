<?php

namespace App\Livewire\Pages\Tasks\Modals;

use App\Livewire\SecureComponent;
use App\Models\Task;
use App\Models\TaskSubtask;
use App\Models\TaskTag;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class TaskForm extends SecureComponent
{
    // ── Estado ────────────────────────────────────────────────────────
    public bool   $open       = false;
    public ?int   $editingId  = null;

    // ── Campos do formulário ─────────────────────────────────────────
    public string  $title              = '';
    public string  $description        = '';
    public string  $due_date           = '';
    public string  $priority           = 'media';
    public string  $status             = 'pendente';
    public ?int    $assigned_to        = null;
    public string  $recurrence         = '';
    public string  $recurrence_ends_at = '';
    public string  $estimated_minutes  = '';
    public array   $selectedTagIds     = [];

    // ── Subtarefas (buffer) ──────────────────────────────────────────
    public array  $subtasks        = [];
    public string $newSubtaskTitle = '';

    // ── Nova tag rápida ──────────────────────────────────────────────
    public string $newTagName  = '';
    public string $newTagColor = '#10b981';

    // ─────────────────────────────────────────────────────────────────

    #[On('open-task-form')]
    public function openNew(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->open      = true;
    }

    #[On('edit-task')]
    public function openEdit(int $taskId): void
    {
        $task = Task::with(['subtasks', 'tags'])->findOrFail($taskId);
        $this->authorizeTaskAccess($task);

        $this->editingId          = $taskId;
        $this->title              = $task->title;
        $this->description        = $task->description ?? '';
        $this->due_date           = $task->due_date?->format('Y-m-d') ?? '';
        $this->priority           = $task->priority;
        $this->status             = $task->status;
        $this->assigned_to        = $task->assigned_to;
        $this->recurrence         = $task->recurrence ?? '';
        $this->recurrence_ends_at = $task->recurrence_ends_at?->format('Y-m-d') ?? '';
        $this->estimated_minutes  = $task->estimated_minutes ? (string) $task->estimated_minutes : '';
        $this->selectedTagIds     = $task->tags->pluck('id')->toArray();
        $this->subtasks           = $task->subtasks->map(fn ($s) => [
            'id'        => $s->id,
            'title'     => $s->title,
            'completed' => $s->completed,
        ])->toArray();

        $this->resetErrorBag();
        $this->open = true;
    }

    public function close(): void
    {
        $this->open      = false;
        $this->editingId = null;
        $this->resetForm();
    }

    public function save(): void
    {
        $user = Auth::user();

        $assignedTo = ($user->isGerente() && $this->assigned_to)
            ? $this->assigned_to
            : Auth::id();

        $rules = [
            'title'              => 'required|min:3|max:255',
            'description'        => 'nullable|max:2000',
            'due_date'           => 'nullable|date',
            'priority'           => 'required|in:baixa,media,alta',
            'status'             => 'required|in:pendente,em_andamento,concluida,cancelada',
            'recurrence'         => 'nullable|in:daily,weekly,monthly,yearly',
            'recurrence_ends_at' => 'nullable|date|after:due_date',
            'estimated_minutes'  => 'nullable|integer|min:1|max:9999',
        ];

        if ($user->isGerente()) {
            $rules['assigned_to'] = 'nullable|exists:users,id';
        }

        $this->validate($rules, [
            'title.required' => 'O título da tarefa é obrigatório.',
            'title.min'      => 'O título deve ter pelo menos 3 caracteres.',
        ]);

        $data = [
            'title'              => $this->sanitize($this->title),
            'description'        => $this->sanitize($this->description) ?: null,
            'due_date'           => $this->due_date ?: null,
            'priority'           => $this->priority,
            'status'             => $this->status,
            'assigned_to'        => $assignedTo,
            'recurrence'         => $this->recurrence ?: null,
            'recurrence_ends_at' => $this->recurrence_ends_at ?: null,
            'estimated_minutes'  => $this->estimated_minutes !== '' ? (int) $this->estimated_minutes : null,
        ];

        if ($this->editingId) {
            $task = Task::findOrFail($this->editingId);
            $this->authorizeTaskAccess($task);

            if ($this->status === 'concluida' && $task->status !== 'concluida') {
                $data['completed_at'] = now();
                $this->criarTarefaRecorrente($task, $data);
            } elseif ($this->status !== 'concluida') {
                $data['completed_at'] = null;
            }

            $task->update($data);
            $this->syncSubtasks($task);
            $task->tags()->sync($this->selectedTagIds);
            $this->alertSuccess('Tarefa atualizada com sucesso.');
        } else {
            $data['assigned_by']  = Auth::id();
            $data['completed_at'] = $this->status === 'concluida' ? now() : null;
            $task = Task::create($data);
            $this->syncSubtasks($task);
            $task->tags()->sync($this->selectedTagIds);
            $this->alertSuccess('Tarefa criada com sucesso.');
        }

        $this->close();
        $this->dispatch('task-saved');
    }

    public function requestDelete(): void
    {
        if (! $this->editingId) return;
        $this->dispatch('confirm-delete-task', taskId: $this->editingId);
        $this->close();
    }

    // ── Subtarefas ───────────────────────────────────────────────────

    public function addSubtask(): void
    {
        $title = trim($this->newSubtaskTitle);
        if ($title === '') return;

        $this->subtasks[]      = ['id' => null, 'title' => $title, 'completed' => false];
        $this->newSubtaskTitle = '';
    }

    public function removeSubtask(int $index): void
    {
        $sub = $this->subtasks[$index] ?? null;
        if ($sub && ! empty($sub['id'])) {
            TaskSubtask::find($sub['id'])?->delete();
        }
        array_splice($this->subtasks, $index, 1);
    }

    public function toggleSubtaskBuffer(int $index): void
    {
        if (isset($this->subtasks[$index])) {
            $this->subtasks[$index]['completed'] = ! $this->subtasks[$index]['completed'];
        }
    }

    private function syncSubtasks(Task $task): void
    {
        $existingIds = collect($this->subtasks)->pluck('id')->filter()->values();
        $task->subtasks()->whereNotIn('id', $existingIds)->delete();

        foreach ($this->subtasks as $order => $sub) {
            if (! empty($sub['id'])) {
                TaskSubtask::where('id', $sub['id'])->update(['title' => $sub['title'], 'order' => $order]);
            } else {
                TaskSubtask::create(['task_id' => $task->id, 'title' => $sub['title'], 'completed' => false, 'order' => $order]);
            }
        }
    }

    // ── Tags ─────────────────────────────────────────────────────────

    public function toggleSelectedTag(int $tagId): void
    {
        if (in_array($tagId, $this->selectedTagIds)) {
            $this->selectedTagIds = array_values(array_filter($this->selectedTagIds, fn ($id) => $id !== $tagId));
        } else {
            $this->selectedTagIds[] = $tagId;
        }
    }

    public function createTag(): void
    {
        $name = trim($this->newTagName);
        if ($name === '') return;

        TaskTag::create([
            'name'       => $name,
            'color'      => $this->newTagColor,
            'created_by' => Auth::id(),
        ]);

        $this->newTagName  = '';
        $this->newTagColor = '#10b981';
        unset($this->allTags);

        // Notify parent to refresh its tag filter list
        $this->dispatch('tags-updated');
    }

    public function deleteTag(int $tagId): void
    {
        TaskTag::find($tagId)?->delete();
        $this->selectedTagIds = array_values(array_filter($this->selectedTagIds, fn ($id) => $id !== $tagId));
        unset($this->allTags);
        $this->dispatch('tags-updated');
    }

    // ── Computed ─────────────────────────────────────────────────────

    #[Computed]
    public function allTags()
    {
        return TaskTag::orderBy('name')->get();
    }

    #[Computed]
    public function assignableUsers()
    {
        $user = Auth::user();
        if (! $user->isGerente()) return collect();

        return User::where('department_id', $user->department_id)
            ->where('id', '!=', Auth::id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // ── Helpers ──────────────────────────────────────────────────────

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

    private function resetForm(): void
    {
        $this->title              = '';
        $this->description        = '';
        $this->due_date           = '';
        $this->priority           = 'media';
        $this->status             = 'pendente';
        $this->assigned_to        = null;
        $this->recurrence         = '';
        $this->recurrence_ends_at = '';
        $this->estimated_minutes  = '';
        $this->selectedTagIds     = [];
        $this->subtasks           = [];
        $this->newSubtaskTitle    = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.pages.tasks.modals.task-form');
    }
}
