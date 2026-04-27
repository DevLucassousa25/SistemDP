<?php

namespace App\Livewire\Pages\Tasks\Modals;

use App\Livewire\SecureComponent;
use App\Models\Task;
use App\Models\TaskComment;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class CommentDrawer extends SecureComponent
{
    public bool   $open       = false;
    public ?int   $taskId     = null;
    public string $newComment = '';

    #[On('open-comment-drawer')]
    public function openDrawer(int $taskId): void
    {
        $this->taskId     = $taskId;
        $this->newComment = '';
        $this->open       = true;
        unset($this->task);
    }

    public function close(): void
    {
        $this->open       = false;
        $this->taskId     = null;
        $this->newComment = '';
        unset($this->task);
    }

    #[Computed]
    public function task(): ?Task
    {
        if (! $this->taskId) return null;
        return Task::with(['comments.user', 'assignedTo', 'assignedBy', 'tags', 'subtasks'])
            ->find($this->taskId);
    }

    public function addComment(): void
    {
        $body = trim($this->newComment);
        if ($body === '' || ! $this->taskId) return;

        TaskComment::create([
            'task_id' => $this->taskId,
            'user_id' => Auth::id(),
            'body'    => $this->sanitize($body),
        ]);

        $this->newComment = '';
        unset($this->task);
    }

    public function deleteComment(int $commentId): void
    {
        $comment = TaskComment::findOrFail($commentId);
        if ($comment->user_id !== Auth::id()) return;

        $comment->delete();
        unset($this->task);
    }

    public function render()
    {
        return view('livewire.pages.tasks.modals.comment-drawer');
    }
}
