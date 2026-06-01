<?php

namespace App\Livewire\Pages\Users;

use App\Livewire\SecureComponent;
use App\Models\DpiPlan;
use App\Models\FeedPost;
use App\Models\Feedback;
use App\Models\Meeting;
use App\Models\Manifestacao;
use App\Models\PostRead;
use App\Models\SurveyResponse;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class Details extends SecureComponent
{
    #[Locked]
    public int $userId;

    public ?User $user = null;

    protected $listeners = [
        'userUpdated'     => 'refreshUser',
        'userDeactivated' => 'refreshUser',
        'activateUser'    => 'refreshUser',
    ];

    public function mount(int $id): void
    {
        $this->requireRhOrAdmin();
        $this->userId = $id;
        $this->loadUser();
        $this->dispatch('breadcrumb-set', items: [
            ['label' => 'Usuários', 'icon' => 'users', 'url' => route('users')],
            ['label' => $this->user->name ?? 'Perfil', 'url' => null],
        ]);
    }

    public function loadUser(): void
    {
        $this->user = User::with(['department', 'accessProfile'])
            ->select([
                'id', 'name', 'email', 'department_id',
                'access_profile_id', 'position', 'is_active',
                'bio', 'created_at',
            ])
            ->findOrFail($this->userId);
    }

    public function refreshUser(): void
    {
        $this->loadUser();
    }

    // ── Stats ─────────────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        $uid = $this->userId;

        return [
            'tarefas'   => Task::where('assigned_to', $uid)->count(),
            'reunioes'  => Meeting::whereHas('participants', fn ($q) => $q->where('user_id', $uid))->count(),
            'feed'      => FeedPost::where('user_id', $uid)->count(),
            'leituras'  => PostRead::where('user_id', $uid)->count(),
            'feedbacks' => Feedback::where('employee_id', $uid)->count(),
            'pesquisas' => SurveyResponse::where('user_id', $uid)->whereNotNull('completed_at')->count(),
        ];
    }

    public function render()
    {
        return view('livewire.pages.users.details')
            ->layout('components.layouts.app', ['title' => 'Perfil do Usuário']);
    }
}
