<?php

namespace App\Livewire\Pages\Tasks\Modals;

use App\Livewire\SecureComponent;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ConfirmDelete extends SecureComponent
{
    public bool $open    = false;
    public ?int $taskId  = null;

    #[On('confirm-delete-task')]
    public function openModal(int $taskId): void
    {
        $this->taskId = $taskId;
        $this->open   = true;
    }

    public function confirm(): void
    {
        if (! $this->taskId) return;

        $task = Task::findOrFail($this->taskId);
        $this->authorizeTaskAccess($task);
        $task->delete();

        $this->open   = false;
        $this->taskId = null;

        $this->alertSuccess('Tarefa excluída.');
        $this->dispatch('task-deleted');
    }

    public function cancel(): void
    {
        $this->open   = false;
        $this->taskId = null;
    }

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

    public function render()
    {
        return view('livewire.pages.tasks.modals.confirm-delete');
    }
}
