<?php

namespace App\Livewire\Pages\Tasks\Modals;

use App\Livewire\SecureComponent;
use App\Models\FeedbackActionTask;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class ValidationModal extends SecureComponent
{
    public bool   $open           = false;
    public ?int   $taskId         = null;
    public string $validationNote = '';

    #[On('open-validation-modal')]
    public function openModal(int $taskId): void
    {
        $this->taskId         = $taskId;
        $this->validationNote = '';
        $this->resetErrorBag();
        $this->open = true;
    }

    public function confirm(): void
    {
        if (! $this->taskId) return;

        $this->validate([
            'validationNote' => 'required|min:5|max:1000',
        ], [
            'validationNote.required' => 'Descreva a evidência ou observação para validar a conclusão.',
            'validationNote.min'      => 'A nota deve ter pelo menos 5 caracteres.',
        ]);

        $task = FeedbackActionTask::with('feedback.employee')->findOrFail($this->taskId);

        $user     = Auth::user();
        $employee = $task->feedback?->employee;

        $isGestorDireto = $user->isGerente()
            && $employee
            && $employee->department_id === $user->department_id;

        if (! $isGestorDireto && ! $user->isRhOuDp()) {
            abort(403);
        }

        $task->update([
            'completed'       => true,
            'completed_at'    => now(),
            'completed_by'    => Auth::id(),
            'validation_note' => $this->validationNote,
        ]);

        $this->open           = false;
        $this->taskId         = null;
        $this->validationNote = '';

        $this->dispatch('task-validated');

        // Verifica se todas as tarefas do plano foram concluídas
        $fb = $task->feedback;
        if ($fb && $fb->actionTasks()->where('completed', false)->count() === 0) {
            $this->alertSuccess('Todas as tarefas foram validadas! O plano de ação foi concluído.');
        } else {
            $this->alertSuccess('Tarefa validada com sucesso.');
        }
    }

    public function cancel(): void
    {
        $this->open           = false;
        $this->taskId         = null;
        $this->validationNote = '';
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.pages.tasks.modals.validation-modal');
    }
}
