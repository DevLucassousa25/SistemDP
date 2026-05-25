<?php

namespace App\Livewire\Components\Ui\Tab;

use App\Livewire\SecureComponent;
use App\Models\DpiPlan;
use App\Models\FeedPost;
use App\Models\Feedback;
use App\Models\Manifestacao;
use App\Models\Meeting;
use App\Models\PostRead;
use App\Models\SurveyResponse;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Collection;
use Livewire\Attributes\Computed;

class ProfileTabs extends SecureComponent
{
    public string $activeTab = 'informacoes';

    public ?User $user = null;

    /** @var Collection */
    public $managers;

    public function mount($user): void
    {
        $this->user = $user;
        $this->loadManagers();
    }

    public function setTab(string $tab): void
    {
        $this->activeTab = $tab;
    }

    private const MANAGER_PROFILE_ID = 3;

    private function loadManagers(): void
    {
        if (! $this->user?->department_id) {
            $this->managers = collect();
            return;
        }

        $this->managers = User::query()
            ->where('access_profile_id', self::MANAGER_PROFILE_ID)
            ->where('department_id', $this->user->department_id)
            ->where('id', '!=', $this->user->id)
            ->where('is_active', true)
            ->get(['id', 'name', 'email', 'department_id', 'access_profile_id']);
    }

    // ── Timeline ──────────────────────────────────────────────────────

    #[Computed]
    public function timeline(): Collection
    {
        $uid    = $this->user->id;
        $events = collect();

        // ── Conta criada ──────────────────────────────────────────────
        $events->push([
            'type'  => 'account',
            'icon'  => 'user-plus',
            'color' => 'slate',
            'label' => 'Conta criada',
            'desc'  => 'Usuário cadastrado no sistema.',
            'date'  => $this->user->created_at,
            'url'   => null,
        ]);

        // ── Tarefas atribuídas ────────────────────────────────────────
        Task::where('assigned_to', $uid)
            ->select(['id', 'title', 'status', 'priority', 'created_at'])
            ->latest()
            ->limit(30)
            ->get()
            ->each(fn ($t) => $events->push([
                'type'  => 'task',
                'icon'  => 'check-square',
                'color' => match ($t->status) {
                    'completed' => 'emerald',
                    'in_progress' => 'blue',
                    default => 'amber',
                },
                'label' => 'Tarefa atribuída',
                'desc'  => $t->title,
                'date'  => $t->created_at,
                'url'   => null,
            ]));

        // ── Reuniões ──────────────────────────────────────────────────
        Meeting::whereHas('participants', fn ($q) => $q->where('user_id', $uid))
            ->select(['id', 'title', 'start_time'])
            ->latest('start_time')
            ->limit(20)
            ->get()
            ->each(fn ($m) => $events->push([
                'type'  => 'meeting',
                'icon'  => 'calendar',
                'color' => 'indigo',
                'label' => 'Participou de reunião',
                'desc'  => $m->title,
                'date'  => $m->start_time,
                'url'   => null,
            ]));

        // ── Posts no feed ─────────────────────────────────────────────
        FeedPost::where('user_id', $uid)
            ->select(['id', 'content', 'created_at'])
            ->latest()
            ->limit(20)
            ->get()
            ->each(fn ($p) => $events->push([
                'type'  => 'feed',
                'icon'  => 'message-circle',
                'color' => 'violet',
                'label' => 'Publicação no feed',
                'desc'  => \Illuminate\Support\Str::limit(strip_tags($p->content), 80),
                'date'  => $p->created_at,
                'url'   => null,
            ]));

        // ── Publicações lidas ─────────────────────────────────────────
        PostRead::where('user_id', $uid)
            ->with('post:id,title')
            ->latest('read_at')
            ->limit(20)
            ->get()
            ->each(fn ($r) => $events->push([
                'type'  => 'read',
                'icon'  => 'book-open',
                'color' => 'teal',
                'label' => 'Publicação lida',
                'desc'  => $r->post?->title ?? '—',
                'date'  => $r->read_at ?? $r->created_at,
                'url'   => null,
            ]));

        // ── Feedbacks recebidos ───────────────────────────────────────
        Feedback::where('employee_id', $uid)
            ->select(['id', 'type', 'category', 'status', 'created_at'])
            ->latest()
            ->limit(15)
            ->get()
            ->each(fn ($f) => $events->push([
                'type'  => 'feedback',
                'icon'  => 'trending-up',
                'color' => 'rose',
                'label' => 'Feedback recebido',
                'desc'  => ucfirst($f->type) . ' · ' . ucfirst($f->category ?? $f->status),
                'date'  => $f->created_at,
                'url'   => null,
            ]));

        // ── Pesquisas respondidas ─────────────────────────────────────
        SurveyResponse::where('user_id', $uid)
            ->whereNotNull('completed_at')
            ->with('survey:id,title')
            ->latest('completed_at')
            ->limit(15)
            ->get()
            ->each(fn ($r) => $events->push([
                'type'  => 'survey',
                'icon'  => 'bar-chart-2',
                'color' => 'amber',
                'label' => 'Pesquisa respondida',
                'desc'  => $r->survey?->title ?? '—',
                'date'  => $r->completed_at,
                'url'   => null,
            ]));

        // ── DPI Plans ─────────────────────────────────────────────────
        DpiPlan::where('user_id', $uid)
            ->select(['id', 'year', 'status', 'created_at', 'updated_at'])
            ->latest()
            ->limit(10)
            ->get()
            ->each(fn ($p) => $events->push([
                'type'  => 'dpi',
                'icon'  => 'target',
                'color' => 'cyan',
                'label' => "DPI {$p->year}",
                'desc'  => 'Status: ' . ucfirst($p->status),
                'date'  => $p->updated_at ?? $p->created_at,
                'url'   => null,
            ]));

        // ── Manifestações (Ouvidoria) ─────────────────────────────────
        Manifestacao::where('user_id', $uid)
            ->select(['id', 'assunto', 'categoria', 'status', 'created_at'])
            ->latest()
            ->limit(10)
            ->get()
            ->each(fn ($m) => $events->push([
                'type'  => 'ouvidoria',
                'icon'  => 'megaphone',
                'color' => 'orange',
                'label' => 'Manifestação enviada',
                'desc'  => $m->assunto . ' · ' . ucfirst($m->categoria),
                'date'  => $m->created_at,
                'url'   => null,
            ]));

        return $events
            ->filter(fn ($e) => ! empty($e['date']))
            ->sortByDesc(fn ($e) => $e['date']->timestamp)
            ->values();
    }

    public function render()
    {
        return view('livewire.components.ui.tab.profile-tabs', [
            'managers' => $this->managers,
        ]);
    }
}
