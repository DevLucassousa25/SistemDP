<?php

namespace App\Livewire\Pages\Dpi;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Notifications\DpiPlanoAtualizacaoNotification;
use App\Models\Department;
use App\Models\DpiAction;
use App\Models\DpiGoal;
use App\Models\DpiPlan;
use App\Models\DpiTemplate;
use App\Models\DpiTemplateGoal;
use App\Models\DpiTemplateAction;
use App\Models\ManagerEvaluationEntry;
use App\Models\Meeting;
use App\Models\Reservations;
use App\Models\room;
use App\Models\SelfEvaluation;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;

class Index extends SecureComponent
{
    use EnviaNotificacoes;
    use WithFileUploads;

    // ── Tabs / UI ─────────────────────────────────────────────────────
    public string $activeTab   = 'meu_plano';
    public int    $selectedYear;
    public ?int   $teamUserId       = null;
    public ?int   $editingForUserId = null;

    // ── Modal combinado: Competência + Ações ──────────────────────────
    public bool   $goalModal        = false;
    public ?int   $editGoalId       = null;

    // Campos da competência
    public string $compNome         = '';
    public string $compPrioridade   = 'media';
    public int    $compNivelAtual   = 1;
    public int    $compNivelMeta    = 3;

    // Formulário inline de ação (dentro do modal)
    public bool   $showInlineForm              = false;
    public string $inlineActionTitle           = '';
    public string $inlineActionType            = 'curso';
    public string $inlineActionDate            = '';
    public string $inlineActionDesc            = '';
    public ?int   $inlineActionTreinamentoId   = null;
    public ?int   $editingInlineActionId       = null;
    public ?int   $editingInlineActionIndex    = null;

    // Ações pendentes (apenas para nova competência, antes de salvar)
    public array $pendingActions = [];

    // ── Modal Feedback (gerente aprova/reprova plano do RH / RH aprova plano do Gestor) ─
    public bool   $feedbackModal       = false;
    public ?int   $reviewPlanId        = null;
    public string $reviewAction        = 'aprovado';
    public string $reviewFeedback      = '';
    public bool   $reviewIsGerentePlan = false; // RH revisando DPI de um Gestor

    // ── Relatório (tab exclusiva RH/DP) ──────────────────────────────
    public string $reportSearch       = '';
    public string $reportFilterStatus = '';
    public string $reportFilterDept   = '';

    // ── Relatório de setor (tab Gerente) ─────────────────────────────
    public string $gerenteReportSearch  = '';
    public string $gerenteReportStatus  = '';

    // ── Modal Confirmar exclusão ──────────────────────────────────────
    public bool   $deleteModal = false;
    public string $deleteType  = '';
    public ?int   $deleteId    = null;

    // ── Modal Upload de evidência ─────────────────────────────────────
    public bool  $uploadModal      = false;
    public ?int  $uploadActionId   = null;
    public $evidenceFile           = null; // TemporaryUploadedFile

    // ── Modal Reunião 1:1 de Acompanhamento DPI ───────────────────────
    public bool   $meetingModal       = false;
    public ?int   $meetingForUserId   = null;
    public string $meetingTitle       = '';
    public string $meetingDate        = '';
    public string $meetingStartTime   = '09:00';
    public string $meetingDuration    = '60';
    public string $meetingPlatform      = 'google_meet';
    public string $meetingLink          = '';
    public string $meetingLocationType  = 'online';
    public ?int   $meetingRoomId        = null;

    // ── Visualizações alternativas (Gantt + Radar) ────────────────────
    public string $vizView   = 'gantt';
    public ?int   $vizUserId = null;

    // ── Templates ─────────────────────────────────────────────────────
    public bool   $templateModal        = false;   // modal aplicar template
    public bool   $manageTemplateModal  = false;   // modal gerenciar templates
    public string $templateTab          = 'list';  // list | create | edit

    // Form novo template
    public string $tplName        = '';
    public string $tplDescription = '';
    public array  $tplGoals       = [];   // [{name, priority, nivel_atual, nivel_meta, actions:[{title,description,type}]}]

    // Edição
    public ?int $editTemplateId = null;

    #[Locked] public ?int $planId = null;

    // ── Boot ──────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();
        $this->selectedYear = now()->year;
    }

    // ── Polling em tempo real ──────────────────────────────────────────
    /**
     * Chamado pelo wire:poll a cada 15 segundos.
     * Se algum modal estiver aberto, ignora o re-render para não
     * perder texto que o usuário possa estar digitando nos formulários.
     */
    public function refreshIfIdle(): void
    {
        if ($this->goalModal || $this->feedbackModal || $this->deleteModal || $this->uploadModal || $this->meetingModal) {
            $this->skipRender();
            return;
        }

        // Invalida cache dos computeds para buscar dados frescos
        unset($this->myPlan, $this->teamPlan, $this->teamMembers, $this->pendingGerentePlans,
              $this->reportStats, $this->reportByDepartment, $this->reportPlans,
              $this->reportActionsByType, $this->reportUsersWithoutPlan,
              $this->reportOverdueActions, $this->reportProgressRanking,
              $this->reportSetorStats, $this->reportSetorPlans, $this->reportSetorWithoutPlan,
              $this->reportSetorOverdue, $this->reportSetorRanking, $this->reportSetorActionStatus,
              $this->teamMemberEvaluationData, $this->teamMemberMeetings,
              $this->availableRoomsForMeeting,
              $this->analyticsPersonal, $this->analyticsTeam,
              $this->vizPlan, $this->ganttData, $this->radarCompetenciasData);
    }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function myPlan(): ?DpiPlan
    {
        return DpiPlan::with(['goals.actions.treinamento', 'creator'])
            ->where('user_id', Auth::id())
            ->where('year', $this->selectedYear)
            ->first();
    }

    #[Computed]
    public function teamPlan(): ?DpiPlan
    {
        if (! $this->teamUserId) return null;

        return DpiPlan::with(['goals.actions.treinamento', 'user', 'creator'])
            ->where('user_id', $this->teamUserId)
            ->where('year', $this->selectedYear)
            ->first();
    }

    #[Computed]
    public function teamMembers()
    {
        $user = Auth::user();

        if ($user->isRhOuDp()) {
            return User::notAdmin()
                ->with(['department', 'dpiPlans' => fn ($q) => $q->where('year', $this->selectedYear)])
                ->where('is_active', true)
                ->where('id', '!=', Auth::id())
                ->orderBy('name')
                ->get();
        }

        if ($user->isGerente()) {
            return User::notAdmin()
                ->with(['department', 'dpiPlans' => fn ($q) => $q->where('year', $this->selectedYear)])
                ->where('department_id', $user->department_id)
                ->where('id', '!=', Auth::id())
                ->where('is_active', true)
                ->orderBy('name')
                ->get();
        }

        return collect();
    }

    /**
     * Planos de Gestores aguardando aprovação do RH (status = enviado).
     * Usado exclusivamente na aba "Aprovações" — visível apenas para RH/DP.
     */
    #[Computed]
    public function pendingGerentePlans()
    {
        if (! Auth::user()->isRhOuDp()) return collect();

        return DpiPlan::with(['goals.actions', 'user.department'])
            ->where('year', $this->selectedYear)
            ->where('status', 'enviado')
            ->whereHas('user', fn ($q) => $q->whereHas(
                'accessProfile', fn ($q2) => $q2->where('slug', 'manager')
            ))
            ->orderBy('updated_at', 'desc')
            ->get();
    }

    // ── Computeds do Relatório ────────────────────────────────────────

    #[Computed]
    public function reportStats(): array
    {
        if (! Auth::user()->isRhOuDp()) return [];

        $totalUsers = User::notAdmin()->where('is_active', true)->count();
        $plans = DpiPlan::with(['goals.actions'])
            ->where('year', $this->selectedYear)
            ->get();

        $byStatus    = $plans->groupBy('status');
        $totalActions = $plans->sum(fn ($p) => $p->actions->count());
        $doneActions  = $plans->sum(fn ($p) => $p->actions->where('status', 'concluido')->count());
        $avgProgress  = $plans->isNotEmpty()
            ? (int) round($plans->avg(fn ($p) => $p->progress_percent))
            : 0;

        return [
            'total_users'   => $totalUsers,
            'with_plan'     => $plans->count(),
            'without_plan'  => max(0, $totalUsers - $plans->count()),
            'rascunho'      => $byStatus->get('rascunho',  collect())->count(),
            'enviado'       => $byStatus->get('enviado',   collect())->count(),
            'aprovado'      => $byStatus->get('aprovado',  collect())->count(),
            'reprovado'     => $byStatus->get('reprovado', collect())->count(),
            'concluido'     => $byStatus->get('concluido', collect())->count(),
            'total_actions' => $totalActions,
            'done_actions'  => $doneActions,
            'avg_progress'  => $avgProgress,
        ];
    }

    #[Computed]
    public function reportByDepartment(): \Illuminate\Support\Collection
    {
        if (! Auth::user()->isRhOuDp()) return collect();

        return Department::orderBy('name')->get()->map(function ($dept) {
            $plans = DpiPlan::with(['goals.actions'])
                ->where('year', $this->selectedYear)
                ->whereHas('user', fn ($q) => $q->where('department_id', $dept->id)->where('is_active', true))
                ->get();

            $totalUsers = User::notAdmin()
                ->where('is_active', true)
                ->where('department_id', $dept->id)
                ->count();

            return [
                'name'         => $dept->name,
                'total_users'  => $totalUsers,
                'with_plan'    => $plans->count(),
                'no_plan'      => max(0, $totalUsers - $plans->count()),
                'rascunho'     => $plans->whereIn('status', ['rascunho', 'reprovado'])->count(),
                'enviado'      => $plans->where('status', 'enviado')->count(),
                'aprovado'     => $plans->where('status', 'aprovado')->count(),
                'concluido'    => $plans->where('status', 'concluido')->count(),
                'avg_progress' => $plans->isNotEmpty()
                    ? (int) round($plans->avg(fn ($p) => $p->progress_percent))
                    : 0,
            ];
        })->filter(fn ($d) => $d['total_users'] > 0)->values();
    }

    #[Computed]
    public function reportPlans(): \Illuminate\Support\Collection
    {
        if (! Auth::user()->isRhOuDp()) return collect();

        return DpiPlan::with(['goals.actions', 'user.department', 'user.accessProfile'])
            ->where('year', $this->selectedYear)
            ->when($this->reportFilterStatus, fn ($q) => $q->where('status', $this->reportFilterStatus))
            ->when($this->reportFilterDept,   fn ($q) => $q->whereHas(
                'user', fn ($q2) => $q2->where('department_id', $this->reportFilterDept)
            ))
            ->when($this->reportSearch, fn ($q) => $q->whereHas(
                'user', fn ($q2) => $q2->where('name', 'ilike', '%' . $this->reportSearch . '%')
            ))
            ->get()
            ->sortBy(fn ($p) => $p->user?->name)
            ->values();
    }

    #[Computed]
    public function reportDepartments(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->get();
    }

    /** Contagem de ações agrupadas por tipo — para gráfico de barras */
    #[Computed]
    public function reportActionsByType(): array
    {
        if (! Auth::user()->isRhOuDp()) return [];

        $types = ['curso','certificacao','leitura','mentoria','projeto','workshop','outro'];
        $labels = ['Curso','Certificação','Leitura','Mentoria','Projeto','Workshop','Outro'];
        $colors = ['#6366f1','#f59e0b','#10b981','#8b5cf6','#f97316','#ec4899','#94a3b8'];

        $counts = DpiAction::whereHas('goal.plan', fn ($q) => $q->where('year', $this->selectedYear))
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $doneCounts = DpiAction::whereHas('goal.plan', fn ($q) => $q->where('year', $this->selectedYear))
            ->where('status', 'concluido')
            ->selectRaw('type, count(*) as total')
            ->groupBy('type')
            ->pluck('total', 'type');

        $series     = [];
        $doneSeries = [];
        $filtLabels = [];
        $filtColors = [];
        foreach ($types as $i => $t) {
            $total = (int) ($counts[$t] ?? 0);
            if ($total === 0) continue;          // omite tipos sem nenhuma ação
            $series[]     = $total;
            $doneSeries[] = (int) ($doneCounts[$t] ?? 0);
            $filtLabels[] = $labels[$i];
            $filtColors[] = $colors[$i];
        }

        // fallback: se não há dados retorna arrays vazios (o blade trata)
        $labels = $filtLabels;
        $colors = $filtColors;

        return compact('labels', 'colors', 'series', 'doneSeries');
    }

    /** Colaboradores ativos sem plano no ano selecionado */
    #[Computed]
    public function reportUsersWithoutPlan(): \Illuminate\Support\Collection
    {
        if (! Auth::user()->isRhOuDp()) return collect();

        $withPlan = DpiPlan::where('year', $this->selectedYear)->pluck('user_id');

        return User::notAdmin()
            ->with(['department', 'accessProfile'])
            ->where('is_active', true)
            ->whereNotIn('id', $withPlan)
            ->orderBy('name')
            ->get();
    }

    /** Ações com prazo vencido e ainda não concluídas */
    #[Computed]
    public function reportOverdueActions(): \Illuminate\Support\Collection
    {
        if (! Auth::user()->isRhOuDp()) return collect();

        return DpiAction::with(['goal.plan.user.department'])
            ->whereHas('goal.plan', fn ($q) => $q->where('year', $this->selectedYear))
            ->where('status', '!=', 'concluido')
            ->whereNotNull('target_date')
            ->where('target_date', '<', now()->toDateString())
            ->orderBy('target_date')
            ->get();
    }

    /** Ranking de planos por progresso — top e bottom */
    #[Computed]
    public function reportProgressRanking(): array
    {
        if (! Auth::user()->isRhOuDp()) return ['top' => collect(), 'bottom' => collect()];

        $plans = DpiPlan::with(['goals.actions', 'user.department'])
            ->where('year', $this->selectedYear)
            ->whereIn('status', ['aprovado', 'concluido', 'enviado'])
            ->get()
            ->map(fn ($p) => ['plan' => $p, 'progress' => $p->progress_percent])
            ->sortByDesc('progress')
            ->values();

        return [
            'top'    => $plans->take(5),
            'bottom' => $plans->reverse()->take(5)->values(),
        ];
    }

    public function clearReportFilters(): void
    {
        $this->reportSearch       = '';
        $this->reportFilterStatus = '';
        $this->reportFilterDept   = '';
        unset($this->reportPlans);
    }

    public function clearGerenteReportFilters(): void
    {
        $this->gerenteReportSearch = '';
        $this->gerenteReportStatus = '';
        unset($this->reportSetorPlans);
    }

    // ── Computeds do Relatório do Setor (Gerente) ─────────────────────

    /**
     * KPIs gerais do setor para o Gerente.
     */
    #[Computed]
    public function reportSetorStats(): array
    {
        $user = Auth::user();
        if (! $user->isGerente()) return [];

        $members = User::notAdmin()
            ->where('is_active', true)
            ->where('department_id', $user->department_id)
            ->where('id', '!=', $user->id)
            ->get();

        $memberIds = $members->pluck('id');

        $plans = DpiPlan::with(['goals.actions'])
            ->where('year', $this->selectedYear)
            ->whereIn('user_id', $memberIds)
            ->get();

        $byStatus    = $plans->groupBy('status');
        $totalActions = $plans->sum(fn ($p) => $p->actions->count());
        $doneActions  = $plans->sum(fn ($p) => $p->actions->where('status', 'concluido')->count());
        $inProgress   = $plans->sum(fn ($p) => $p->actions->where('status', 'em_andamento')->count());
        $avgProgress  = $plans->isNotEmpty()
            ? (int) round($plans->avg(fn ($p) => $p->progress_percent))
            : 0;

        return [
            'total_members' => $members->count(),
            'with_plan'     => $plans->count(),
            'without_plan'  => max(0, $members->count() - $plans->count()),
            'rascunho'      => $byStatus->get('rascunho',  collect())->count(),
            'enviado'       => $byStatus->get('enviado',   collect())->count(),
            'aprovado'      => $byStatus->get('aprovado',  collect())->count(),
            'reprovado'     => $byStatus->get('reprovado', collect())->count(),
            'concluido'     => $byStatus->get('concluido', collect())->count(),
            'total_actions' => $totalActions,
            'done_actions'  => $doneActions,
            'in_progress'   => $inProgress,
            'avg_progress'  => $avgProgress,
        ];
    }

    /**
     * Planos individuais dos membros do setor para o Gerente.
     */
    #[Computed]
    public function reportSetorPlans(): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        if (! $user->isGerente()) return collect();

        return DpiPlan::with(['goals.actions', 'user.accessProfile'])
            ->where('year', $this->selectedYear)
            ->whereHas('user', fn ($q) => $q
                ->where('department_id', $user->department_id)
                ->where('id', '!=', $user->id)
                ->where('is_active', true)
                ->when($this->gerenteReportSearch, fn ($q2) =>
                    $q2->where('name', 'ilike', '%' . $this->gerenteReportSearch . '%')
                )
            )
            ->when($this->gerenteReportStatus, fn ($q) => $q->where('status', $this->gerenteReportStatus))
            ->get()
            ->sortBy(fn ($p) => $p->user?->name)
            ->values();
    }

    /**
     * Membros do setor sem plano criado.
     */
    #[Computed]
    public function reportSetorWithoutPlan(): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        if (! $user->isGerente()) return collect();

        $withPlan = DpiPlan::where('year', $this->selectedYear)->pluck('user_id');

        return User::notAdmin()
            ->with(['accessProfile'])
            ->where('is_active', true)
            ->where('department_id', $user->department_id)
            ->where('id', '!=', $user->id)
            ->whereNotIn('id', $withPlan)
            ->orderBy('name')
            ->get();
    }

    /**
     * Ações com prazo vencido nos planos do setor.
     */
    #[Computed]
    public function reportSetorOverdue(): \Illuminate\Support\Collection
    {
        $user = Auth::user();
        if (! $user->isGerente()) return collect();

        return DpiAction::with(['goal.plan.user'])
            ->whereHas('goal.plan', fn ($q) => $q
                ->where('year', $this->selectedYear)
                ->whereHas('user', fn ($q2) => $q2
                    ->where('department_id', $user->department_id)
                    ->where('id', '!=', $user->id)
                )
            )
            ->where('status', '!=', 'concluido')
            ->whereNotNull('target_date')
            ->where('target_date', '<', now()->toDateString())
            ->orderBy('target_date')
            ->get();
    }

    /**
     * Ranking de progresso dos membros do setor (top 5 + bottom 5).
     */
    #[Computed]
    public function reportSetorRanking(): array
    {
        $user = Auth::user();
        if (! $user->isGerente()) return ['top' => collect(), 'bottom' => collect()];

        $plans = DpiPlan::with(['goals.actions', 'user'])
            ->where('year', $this->selectedYear)
            ->whereHas('user', fn ($q) => $q
                ->where('department_id', $user->department_id)
                ->where('id', '!=', $user->id)
                ->where('is_active', true)
            )
            ->get()
            ->map(fn ($p) => ['plan' => $p, 'progress' => $p->progress_percent])
            ->sortByDesc('progress')
            ->values();

        return [
            'top'    => $plans->take(5),
            'bottom' => $plans->reverse()->take(5)->values(),
        ];
    }

    /**
     * Distribuição de ações por status agregado do setor.
     */
    #[Computed]
    public function reportSetorActionStatus(): array
    {
        $user = Auth::user();
        if (! $user->isGerente()) return [];

        $actions = DpiAction::whereHas('goal.plan', fn ($q) => $q
            ->where('year', $this->selectedYear)
            ->whereHas('user', fn ($q2) => $q2
                ->where('department_id', $user->department_id)
                ->where('id', '!=', $user->id)
            )
        )->get();

        return [
            'pendente'     => $actions->where('status', 'pendente')->count(),
            'em_andamento' => $actions->where('status', 'em_andamento')->count(),
            'concluido'    => $actions->where('status', 'concluido')->count(),
            'total'        => $actions->count(),
        ];
    }

    /** Cursos ativos disponíveis para vincular a uma ação DPI */
    #[Computed]
    public function treinamentosDisponiveis()
    {
        return \App\Models\Treinamento::where('status', 'ativo')
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'nivel', 'carga_horaria']);
    }

    /** Ações da competência em edição (apenas quando editGoalId está definido) */
    #[Computed]
    public function editingGoalActions()
    {
        if (! $this->editGoalId) return collect();

        return DpiGoal::with(['actions' => fn ($q) => $q->with('treinamento')->orderBy('order')->orderBy('id')])
            ->findOrFail($this->editGoalId)
            ->actions;
    }

    public function getYearsProperty(): array
    {
        $current = now()->year;
        return [$current + 1, $current];
    }

    // ═════════════════════════════════════════════════════════════════
    // PLANO — CRIAÇÃO
    // ═════════════════════════════════════════════════════════════════

    /** Gerente ou RH criam o próprio plano de desenvolvimento */
    public function createPlan(): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $current = now()->year;

        if ($this->selectedYear < $current || $this->selectedYear > $current + 1) {
            $this->alertError('Ano inválido', 'O DPI pode ser criado para o ano atual ou próximo.');
            return;
        }

        if (DpiPlan::where('user_id', Auth::id())->where('year', $this->selectedYear)->exists()) return;

        DpiPlan::create([
            'user_id'    => Auth::id(),
            'created_by' => Auth::id(),
            'year'       => $this->selectedYear,
            'status'     => 'rascunho',
        ]);
        unset($this->myPlan);
        $this->alertSuccess('Plano criado!', "Plano DPI {$this->selectedYear} iniciado.");
    }

    /** Gerente ou RH criam plano para um colaborador da equipe */
    public function createPlanForEmployee(int $userId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $current = now()->year;

        if ($this->selectedYear < $current || $this->selectedYear > $current + 1) {
            $this->alertError('Ano inválido', 'O DPI pode ser criado para o ano atual ou próximo.');
            return;
        }

        if (DpiPlan::where('user_id', $userId)->where('year', $this->selectedYear)->exists()) return;

        DpiPlan::create([
            'user_id'    => $userId,
            'created_by' => Auth::id(),
            'year'       => $this->selectedYear,
            'status'     => 'rascunho',
        ]);
        $this->teamUserId = $userId;
        unset($this->teamPlan, $this->teamMembers);
        $this->alertSuccess('Plano criado!', "Plano DPI {$this->selectedYear} iniciado para o colaborador.");
    }

    // ═════════════════════════════════════════════════════════════════
    // PLANO — FLUXO POR QUEM CRIOU
    // ═════════════════════════════════════════════════════════════════

    /**
     * Gerente publica diretamente o plano de um colaborador.
     * Não precisa de aprovação externa — o gerente É o aprovador.
     *
     * RH/DP NÃO pode usar este método para publicar diretamente para Funcionário:
     * deve passar por submitPlanToManager → aprovação do Gerente → publicação.
     * Exceção: RH publicando plano de um Gestor (fluxo DPI do Gestor) é permitido.
     */
    public function publishPlanForEmployee(int $planId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $plan   = DpiPlan::with('user')->findOrFail($planId);
        $caller = Auth::user();

        // Impede RH de publicar diretamente para Funcionário (não-Gestor)
        if ($caller->isRhOuDp() && ! $plan->isPlanForGerente()) {
            $this->alertError(
                'Aprovação obrigatória',
                'Planos do RH para Funcionários precisam ser aprovados pelo Gerente antes de serem publicados. Use "Enviar para Gerente".'
            );
            return;
        }

        // Gerente não pode publicar plano devolvido (reprovado): cabe ao RH corrigir e re-enviar
        if ($caller->isGerente() && $plan->status === 'reprovado') {
            $this->alertError(
                'Ação não permitida',
                'O plano foi devolvido ao RH para correção. Aguarde o RH corrigir e reenviar para sua aprovação.'
            );
            return;
        }

        if ($plan->goals()->count() === 0) {
            $this->alertError('Plano vazio', 'Adicione pelo menos uma competência antes de publicar.');
            return;
        }

        $plan->update([
            'status'      => 'aprovado',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);

        // Notifica o colaborador que seu plano foi aprovado/publicado
        $plan->load('user');
        $this->notificarUsuario($plan->user, new DpiPlanoAtualizacaoNotification($plan, 'aprovado'));
        $this->toastNotif(
            'Plano publicado!',
            "{$plan->user->name} foi notificado(a) sobre o plano aprovado.",
            'target', 'green',
            route('dpi')
        );

        unset($this->teamPlan, $this->teamMembers);
        $this->alertSuccess('Plano publicado!', 'O colaborador já pode visualizar o plano.');
    }

    /**
     * RH/DP envia plano de colaborador para aprovação do gerente.
     */
    public function submitPlanToManager(int $planId): void
    {
        $this->requireRole(['administrator', 'hr']);
        $plan = DpiPlan::findOrFail($planId);

        if ($plan->goals()->count() === 0) {
            $this->alertError('Plano vazio', 'Adicione pelo menos uma competência antes de enviar.');
            return;
        }

        $plan->update(['status' => 'enviado', 'manager_feedback' => null]);

        // Notifica o colaborador que o plano foi enviado para revisão
        $plan->load('user');
        $this->notificarUsuario($plan->user, new DpiPlanoAtualizacaoNotification($plan, 'enviado'));
        $this->toastNotif(
            'Plano enviado para revisão!',
            "O gerente foi notificado para revisar o plano de {$plan->user->name}.",
            'target', 'amber',
            route('dpi')
        );

        unset($this->teamPlan, $this->teamMembers);
        $this->alertSuccess('Enviado!', 'O gerente foi notificado para revisar o plano.');
    }

    /**
     * RH retira plano do estado "enviado" para edição,
     * ou Gerente/RH reabre plano "aprovado"/"concluido" para ajustes.
     *
     * Para planos de Gestor em "enviado" (Gestor submeteu ao RH),
     * reverter retorna ao "aprovado" (Gestor continua executando),
     * não ao "rascunho".
     */
    public function retractFromManager(int $planId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $plan = DpiPlan::with('user')->findOrFail($planId);

        // Plano de Gestor em 'enviado': volta para 'aprovado' (continua em execução)
        $newStatus = ($plan->isPlanForGerente() && $plan->status === 'enviado')
            ? 'aprovado'
            : 'rascunho';

        $plan->update(['status' => $newStatus, 'reviewed_by' => null, 'reviewed_at' => null]);
        unset($this->teamPlan, $this->teamMembers);
        $this->alertInfo('Plano reaberto', 'Edite e publique novamente quando estiver pronto.');
    }

    /**
     * Gestor finaliza a execução do seu próprio DPI (criado pelo RH)
     * e envia ao RH para aprovação e conclusão.
     */
    public function submitGerentePlanToRh(): void
    {
        $this->requireRole(['administrator', 'manager']);
        $plan = DpiPlan::with('user')
            ->where('user_id', Auth::id())
            ->where('year', $this->selectedYear)
            ->firstOrFail();

        if (! $plan->isPlanForGerente()) {
            $this->alertError('Operação inválida.', 'Este fluxo é exclusivo para planos de Gestores.');
            return;
        }

        if (! in_array($plan->status, ['aprovado', 'reprovado'])) {
            $this->alertError('Status inválido.', 'O plano precisa estar ativo para ser enviado ao RH.');
            return;
        }

        if ($plan->goals()->count() === 0) {
            $this->alertError('Plano vazio.', 'Nenhuma competência encontrada no plano.');
            return;
        }

        $plan->update(['status' => 'enviado', 'manager_feedback' => null]);
        unset($this->myPlan);
        $this->alertSuccess('Plano enviado ao RH!', 'Aguarde a revisão e aprovação do RH/DP.');
    }

    /**
     * Gerente/RH publicam o próprio plano de desenvolvimento.
     */
    public function submitPlan(): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $plan = DpiPlan::where('user_id', Auth::id())->where('year', $this->selectedYear)->firstOrFail();

        if ($plan->goals()->count() === 0) {
            $this->alertError('Plano vazio', 'Adicione pelo menos uma competência antes de publicar.');
            return;
        }

        $plan->update([
            'status'      => 'aprovado',
            'reviewed_by' => Auth::id(),
            'reviewed_at' => now(),
        ]);
        unset($this->myPlan);
        $this->alertSuccess('Plano publicado!', 'Seu plano DPI está ativo.');
    }

    public function retractPlan(): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $plan = DpiPlan::where('user_id', Auth::id())->where('year', $this->selectedYear)->firstOrFail();
        $plan->update(['status' => 'rascunho']);
        unset($this->myPlan);
        $this->alertInfo('Plano reaberto', 'Você pode editar e republicar quando quiser.');
    }

    // ── Revisão pelo Gerente (plano criado pelo RH) ───────────────────

    public function openFeedbackModal(int $planId, string $action): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $plan = DpiPlan::with('user')->findOrFail($planId);
        $this->reviewPlanId        = $planId;
        $this->reviewAction        = $action;
        $this->reviewFeedback      = '';
        $this->reviewIsGerentePlan = $plan->isPlanForGerente();
        $this->feedbackModal       = true;
    }

    public function submitReview(): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);

        $this->validate([
            'reviewFeedback' => $this->reviewAction === 'reprovado'
                ? 'required|string|max:1000'
                : 'nullable|string|max:1000',
        ], ['reviewFeedback.required' => 'O feedback é obrigatório ao devolver.']);

        $plan = DpiPlan::with('user')->findOrFail($this->reviewPlanId);

        // RH aprovando DPI de Gestor → status final é 'concluido'
        $targetStatus = $this->reviewAction;
        if ($targetStatus === 'aprovado' && $plan->isPlanForGerente()) {
            $targetStatus = 'concluido';
        }

        $plan->update([
            'status'           => $targetStatus,
            'manager_feedback' => $this->sanitize($this->reviewFeedback) ?: null,
            'reviewed_by'      => Auth::id(),
            'reviewed_at'      => now(),
        ]);

        $this->feedbackModal = false;
        unset($this->myPlan, $this->teamPlan, $this->teamMembers);

        if ($plan->isPlanForGerente()) {
            $label = $targetStatus === 'concluido' ? 'concluído e aprovado' : 'devolvido ao gestor para correção';
            $this->alertSuccess("Plano {$label}!", 'O gestor será notificado.');
        } else {
            $label = $targetStatus === 'aprovado' ? 'aprovado e publicado' : 'devolvido para revisão';
            $this->alertSuccess("Plano {$label}!", 'O colaborador será notificado.');
        }
    }

    // ═════════════════════════════════════════════════════════════════
    // COMPETÊNCIAS
    // ═════════════════════════════════════════════════════════════════

    public function openGoalModal(?int $goalId = null): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $this->editingForUserId = null;
        $this->resetGoalForm();
        $this->editGoalId = $goalId;

        if ($goalId) {
            $goal = DpiGoal::findOrFail($goalId);
            $this->compNome       = $goal->name;
            $this->compPrioridade = $goal->priority;
            $this->compNivelAtual = $goal->nivel_atual;
            $this->compNivelMeta  = $goal->nivel_meta;
        }

        $this->goalModal = true;
    }

    public function openGoalModalForEmployee(?int $goalId = null): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $this->editingForUserId = $this->teamUserId;
        $this->resetGoalForm();
        $this->editGoalId = $goalId;

        if ($goalId) {
            $goal = DpiGoal::findOrFail($goalId);
            $this->requireCanEditPlan($goal->plan->user_id);
            $this->compNome       = $goal->name;
            $this->compPrioridade = $goal->priority;
            $this->compNivelAtual = $goal->nivel_atual;
            $this->compNivelMeta  = $goal->nivel_meta;
        }

        $this->goalModal = true;
    }

    public function saveGoal(): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);

        $this->validate([
            'compNome'       => 'required|string|max:255',
            'compPrioridade' => 'required|in:alta,media,baixa',
            'compNivelAtual' => 'required|integer|min:1|max:5',
            'compNivelMeta'  => 'required|integer|min:1|max:5',
        ]);

        $plan = $this->getEditablePlan();
        if (! $plan) return;

        $data = [
            'dpi_plan_id' => $plan->id,
            'name'        => $this->sanitize($this->compNome),
            'priority'    => $this->compPrioridade,
            'nivel_atual' => $this->compNivelAtual,
            'nivel_meta'  => $this->compNivelMeta,
        ];

        if ($this->editGoalId) {
            DpiGoal::findOrFail($this->editGoalId)->update($data);
            $msg = 'Competência atualizada!';
        } else {
            $data['order'] = $plan->goals()->max('order') + 1;
            $goal = DpiGoal::create($data);

            foreach ($this->pendingActions as $i => $pa) {
                DpiAction::create([
                    'dpi_goal_id'    => $goal->id,
                    'title'          => $pa['title'],
                    'type'           => $pa['type'],
                    'target_date'    => $pa['date'] ?: null,
                    'description'    => $pa['desc'] ?: null,
                    'treinamento_id' => $pa['treinamento_id'] ?? null,
                    'status'         => 'pendente',
                    'order'          => $i + 1,
                ]);
            }

            $msg = 'Competência adicionada!';
        }

        $this->goalModal = false;
        unset($this->myPlan, $this->teamPlan, $this->editingGoalActions);
        $this->alertSuccess($msg);
    }

    public function confirmDeleteGoal(int $goalId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $this->deleteType  = 'goal';
        $this->deleteId    = $goalId;
        $this->deleteModal = true;
    }

    // ═════════════════════════════════════════════════════════════════
    // AÇÕES DE DESENVOLVIMENTO (inline no modal de competência)
    // ═════════════════════════════════════════════════════════════════

    public function editInlineAction(int $actionId): void
    {
        $action = DpiAction::findOrFail($actionId);
        $this->editingInlineActionId      = $actionId;
        $this->editingInlineActionIndex   = null;
        $this->inlineActionTitle          = $action->title;
        $this->inlineActionType           = $action->type;
        $this->inlineActionDate           = $action->target_date?->format('Y-m-d') ?? '';
        $this->inlineActionDesc           = $action->description ?? '';
        $this->inlineActionTreinamentoId  = $action->treinamento_id;
        $this->showInlineForm             = true;
    }

    public function editPendingAction(int $index): void
    {
        $pa = $this->pendingActions[$index] ?? null;
        if (! $pa) return;
        $this->editingInlineActionIndex  = $index;
        $this->editingInlineActionId     = null;
        $this->inlineActionTitle         = $pa['title'];
        $this->inlineActionType          = $pa['type'];
        $this->inlineActionDate          = $pa['date'];
        $this->inlineActionDesc          = $pa['desc'];
        $this->inlineActionTreinamentoId = $pa['treinamento_id'] ?? null;
        $this->showInlineForm            = true;
    }

    public function saveInlineAction(): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);

        $this->validate([
            'inlineActionTitle' => 'required|string|max:255',
            'inlineActionType'  => 'required|in:curso,certificacao,leitura,mentoria,projeto,workshop,outro',
            'inlineActionDate'  => 'nullable|date',
            'inlineActionDesc'  => 'nullable|string|max:1000',
        ], [], [
            'inlineActionTitle' => 'título',
            'inlineActionType'  => 'tipo',
        ]);

        $treinamentoId = ($this->inlineActionType === 'curso') ? $this->inlineActionTreinamentoId : null;

        $payload = [
            'title'          => $this->sanitize($this->inlineActionTitle),
            'type'           => $this->inlineActionType,
            'date'           => $this->inlineActionDate,
            'desc'           => $this->sanitize($this->inlineActionDesc),
            'treinamento_id' => $treinamentoId,
        ];

        if ($this->editGoalId) {
            if ($this->editingInlineActionId) {
                DpiAction::findOrFail($this->editingInlineActionId)->update([
                    'title'          => $payload['title'],
                    'type'           => $payload['type'],
                    'target_date'    => $payload['date'] ?: null,
                    'description'    => $payload['desc'] ?: null,
                    'treinamento_id' => $payload['treinamento_id'],
                ]);
            } else {
                $goal = DpiGoal::findOrFail($this->editGoalId);
                DpiAction::create([
                    'dpi_goal_id'    => $goal->id,
                    'title'          => $payload['title'],
                    'type'           => $payload['type'],
                    'target_date'    => $payload['date'] ?: null,
                    'description'    => $payload['desc'] ?: null,
                    'treinamento_id' => $payload['treinamento_id'],
                    'status'         => 'pendente',
                    'order'          => $goal->actions()->max('order') + 1,
                ]);
            }
            unset($this->editingGoalActions);
        } else {
            if ($this->editingInlineActionIndex !== null) {
                $this->pendingActions[$this->editingInlineActionIndex] = $payload;
            } else {
                $this->pendingActions[] = $payload;
            }
        }

        $this->resetInlineActionForm();
    }

    public function removePendingAction(int $index): void
    {
        array_splice($this->pendingActions, $index, 1);
        $this->pendingActions = array_values($this->pendingActions);
    }

    public function deleteInlineAction(int $actionId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $action = DpiAction::findOrFail($actionId);
        $this->requireCanEditPlan($action->goal->plan->user_id);

        // Remove arquivo se existir
        if ($action->attachment_path) {
            Storage::disk('local')->delete($action->attachment_path);
        }

        // Remove a tarefa vinculada se existir
        if ($action->task_id) {
            Task::find($action->task_id)?->delete();
        }

        $action->delete();
        unset($this->editingGoalActions, $this->myPlan, $this->teamPlan);
    }

    public function confirmDeleteAction(int $actionId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $this->deleteType  = 'action';
        $this->deleteId    = $actionId;
        $this->deleteModal = true;
    }

    // ── Exclusão confirmada ───────────────────────────────────────────

    public function confirmDelete(): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);

        if ($this->deleteType === 'goal') {
            $goal = DpiGoal::findOrFail($this->deleteId);
            $this->requireCanEditPlan($goal->plan->user_id);
            // Remove arquivos e tarefas vinculadas das ações filhas
            foreach ($goal->actions as $a) {
                if ($a->attachment_path) {
                    Storage::disk('local')->delete($a->attachment_path);
                }
                if ($a->task_id) {
                    Task::find($a->task_id)?->delete();
                }
            }
            $goal->delete();
            $this->alertSuccess('Competência removida.');
        } elseif ($this->deleteType === 'action') {
            $action = DpiAction::findOrFail($this->deleteId);
            $this->requireCanEditPlan($action->goal->plan->user_id);
            if ($action->attachment_path) {
                Storage::disk('local')->delete($action->attachment_path);
            }
            // Remove a tarefa vinculada se existir
            if ($action->task_id) {
                Task::find($action->task_id)?->delete();
            }
            $action->delete();
            $this->alertSuccess('Ação removida.');
        }

        $this->deleteModal = false;
        unset($this->myPlan, $this->teamPlan, $this->editingGoalActions);
    }

    // ═════════════════════════════════════════════════════════════════
    // VALIDAÇÃO DE AÇÕES (somente Gerente / RH)
    // ═════════════════════════════════════════════════════════════════

    /**
     * Apenas Gerente e RH/DP podem avançar o status de uma ação.
     * O funcionário NÃO pode marcar suas próprias ações como concluídas.
     */
    public function toggleActionStatus(int $actionId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $action = DpiAction::findOrFail($actionId);
        $this->requireCanEditPlan($action->goal->plan->user_id);

        $next = match ($action->status) {
            'pendente'     => 'em_andamento',
            'em_andamento' => 'concluido',
            'concluido'    => 'pendente',
            default        => 'pendente',
        };

        $updateData = ['status' => $next];

        if ($next === 'concluido') {
            $updateData['validated_by'] = Auth::id();
            $updateData['validated_at'] = now();
        } elseif ($next === 'pendente') {
            $updateData['validated_by'] = null;
            $updateData['validated_at'] = null;
        }

        $action->update($updateData);
        unset($this->myPlan, $this->teamPlan);
    }

    // ═════════════════════════════════════════════════════════════════
    // UPLOAD DE EVIDÊNCIA (diploma, certificado, etc.)
    // ═════════════════════════════════════════════════════════════════

    public function openUploadModal(int $actionId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $action = DpiAction::findOrFail($actionId);
        $plan   = $action->goal->plan;

        // Gestor pode fazer upload no seu próprio plano (criado pelo RH)
        // Demais casos: usa verificação padrão de permissão
        if (! (Auth::id() === $plan->user_id && $plan->isPlanForGerente())) {
            $this->requireCanEditPlan($plan->user_id);
        }

        $this->uploadActionId = $actionId;
        $this->evidenceFile   = null;
        $this->uploadModal    = true;
    }

    public function saveEvidenceUpload(): void
    {
        // Gestor pode salvar evidência no seu próprio plano;
        // demais roles já cobertos pelo requireRole padrão.
        $user = Auth::user();
        if (! ($user->isGerente() || $user->isRhOuDp() || $user->isAdmin())) {
            abort(403);
        }

        $this->validate([
            'evidenceFile' => 'required|file|max:10240|mimes:pdf,jpg,jpeg,png,doc,docx,xls,xlsx',
        ], [
            'evidenceFile.required' => 'Selecione um arquivo para enviar.',
            'evidenceFile.max'      => 'O arquivo deve ter no máximo 10 MB.',
            'evidenceFile.mimes'    => 'Formatos aceitos: PDF, imagens, Word ou Excel.',
        ]);

        $action = DpiAction::findOrFail($this->uploadActionId);

        // Remove arquivo anterior se existir
        if ($action->attachment_path) {
            Storage::disk('local')->delete($action->attachment_path);
        }

        $originalName = $this->evidenceFile->getClientOriginalName();
        $path = $this->evidenceFile->store('dpi-attachments', 'local');

        $action->update([
            'attachment_path' => $path,
            'attachment_name' => $originalName,
        ]);

        $this->uploadModal  = false;
        $this->evidenceFile = null;
        unset($this->myPlan, $this->teamPlan);
        $this->alertSuccess('Evidência salva!', "Arquivo \"{$originalName}\" vinculado à ação.");
    }

    public function deleteEvidence(int $actionId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $action = DpiAction::findOrFail($actionId);
        $this->requireCanEditPlan($action->goal->plan->user_id);

        if ($action->attachment_path) {
            Storage::disk('local')->delete($action->attachment_path);
        }

        $action->update(['attachment_path' => null, 'attachment_name' => null]);
        unset($this->myPlan, $this->teamPlan);
        $this->alertSuccess('Evidência removida.');
    }

    // ── Tab equipe ────────────────────────────────────────────────────

    public function selectTeamUser(int $userId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $this->teamUserId = $userId;
        unset($this->teamPlan);
    }

    /** Seleciona colaborador E navega para a aba Equipe em um único request. */
    public function goToTeamUser(int $userId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $this->teamUserId = $userId;
        $this->activeTab  = 'equipe';
        unset($this->teamPlan, $this->teamMemberEvaluationData, $this->teamMemberMeetings);
    }

    public function updatedSelectedYear(): void
    {
        unset($this->myPlan, $this->teamPlan, $this->teamMembers,
              $this->analyticsPersonal, $this->analyticsTeam,
              $this->vizPlan, $this->ganttData, $this->radarCompetenciasData);
        $this->teamUserId = null;
        $this->vizUserId  = null;
    }

    // ── Helpers ───────────────────────────────────────────────────────

    private function getEditablePlan(): ?DpiPlan
    {
        $userId = $this->editingForUserId ?? Auth::id();

        if ($this->editingForUserId) {
            $this->requireCanEditPlan($this->editingForUserId);
        }

        $plan = DpiPlan::where('user_id', $userId)
            ->where('year', $this->selectedYear)
            ->first();

        if (! $plan) {
            $this->alertError('Plano não encontrado.', 'Crie o plano antes de adicionar competências.');
            return null;
        }

        $user = Auth::user();

        // Gerente pode editar plano "enviado" (criado pelo RH) antes de aprovar
        if ($plan->status === 'enviado' && $user->isGerente()) {
            // Permite edição — Gerente ajusta antes de publicar
            $plan->update(['status' => 'rascunho']);
            return $plan;
        }

        if (in_array($plan->status, ['aprovado'])) {
            $this->alertError('Plano ativo.', 'O plano já foi publicado. Não é possível editar.');
            return null;
        }

        return $plan;
    }

    private function requireCanEditPlan(int $userId): void
    {
        $user = Auth::user();
        if ($user->isRhOuDp()) return;
        if ($user->id === $userId) return;

        if ($user->isGerente()) {
            $employee = User::find($userId);
            if ($employee && $employee->department_id === $user->department_id) return;
        }

        abort(403, 'Sem permissão para editar este plano.');
    }

    private function resetGoalForm(): void
    {
        $this->editGoalId     = null;
        $this->compNome       = '';
        $this->compPrioridade = 'media';
        $this->compNivelAtual = 1;
        $this->compNivelMeta  = 3;
        $this->pendingActions = [];
        $this->resetInlineActionForm();
    }

    public function resetInlineActionForm(): void
    {
        $this->inlineActionTitle           = '';
        $this->inlineActionType            = 'curso';
        $this->inlineActionDate            = '';
        $this->inlineActionDesc            = '';
        $this->inlineActionTreinamentoId   = null;
        $this->editingInlineActionId       = null;
        $this->editingInlineActionIndex    = null;
        $this->showInlineForm              = false;
    }

    // ═════════════════════════════════════════════════════════════════
    // INTEGRAÇÃO: TAREFAS
    // ═════════════════════════════════════════════════════════════════

    /**
     * Cria uma Tarefa vinculada à ação DPI e salva o task_id na ação.
     */
    public function createTaskForAction(int $actionId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $action = DpiAction::with(['goal.plan.user'])->findOrFail($actionId);
        $plan   = $action->goal->plan;

        // Se já há task_id, verifica se a tarefa ainda existe
        if ($action->task_id) {
            $existingTask = Task::find($action->task_id);

            if ($existingTask) {
                // Verifica se título e responsável batem com os dados do DPI
                $expectedTitle = '[DPI] ' . $action->title;
                if ($existingTask->title === $expectedTitle && $existingTask->assigned_to === $action->goal->plan->user_id) {
                    $this->alertInfo('Tarefa já existe', 'Esta ação já possui uma tarefa vinculada com as mesmas informações.');
                    return;
                }

                // Título mudou — atualiza a tarefa existente em vez de criar outra
                $existingTask->update([
                    'title'    => '[DPI] ' . $action->title,
                    'due_date' => $action->target_date,
                ]);
                unset($this->myPlan, $this->teamPlan);
                $this->alertInfo('Tarefa atualizada', 'A tarefa vinculada foi atualizada com as informações atuais.');
                return;
            }

            // task_id órfão (tarefa foi excluída externamente) — limpa e recria
            $action->update(['task_id' => null]);
        }

        $priority = match($action->goal->priority) {
            'alta'  => 'alta',
            'baixa' => 'baixa',
            default => 'media',
        };

        $task = Task::create([
            'title'       => '[DPI] ' . $action->title,
            'description' => implode("\n", array_filter([
                "Ação de desenvolvimento do DPI {$plan->year}.",
                $action->description ? "Descrição: {$action->description}" : null,
                "Tipo: {$action->type_label}",
                "Competência: {$action->goal->name}",
            ])),
            'assigned_to' => $plan->user_id,
            'assigned_by' => Auth::id(),
            'due_date'    => $action->target_date,
            'priority'    => $priority,
            'status'      => match($action->status) {
                'em_andamento' => 'em_andamento',
                'concluido'    => 'concluida',
                default        => 'pendente',
            },
        ]);

        $action->update(['task_id' => $task->id]);
        unset($this->myPlan, $this->teamPlan);
        $this->alertSuccess('Tarefa criada!', 'A tarefa foi criada e vinculada ao plano.');
    }

    /**
     * Remove o vínculo entre a ação DPI e exclui a tarefa vinculada.
     */
    public function detachTaskFromAction(int $actionId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $action = DpiAction::findOrFail($actionId);

        if ($action->task_id) {
            Task::find($action->task_id)?->delete();
        }

        $action->update(['task_id' => null]);
        unset($this->myPlan, $this->teamPlan);
        $this->alertSuccess('Tarefa excluída.');
    }

    // ═════════════════════════════════════════════════════════════════
    // INTEGRAÇÃO: AVALIAÇÕES
    // ═════════════════════════════════════════════════════════════════

    /**
     * Dados da avaliação mais recente do membro selecionado na equipe.
     * Retorna [score, label, cycle_name] ou null se não houver avaliação.
     */
    #[Computed]
    public function teamMemberEvaluationData(): ?array
    {
        if (! $this->teamUserId) return null;
        if (! Auth::user()->isRhOuDp() && ! Auth::user()->isGerente()) return null;

        // Tenta auto-avaliação mais recente concluída
        $self = SelfEvaluation::with(['cycle', 'entries.criterion'])
            ->where('employee_id', $this->teamUserId)
            ->where('status', 'completed')
            ->whereHas('cycle', fn ($q) => $q->where('status', 'closed'))
            ->latest()
            ->first();

        if ($self && $self->weighted_score !== null) {
            return [
                'score'      => $self->weighted_score,
                'label'      => $self->weighted_score >= 4 ? 'Alto desempenho' : ($self->weighted_score >= 3 ? 'Bom desempenho' : 'Em desenvolvimento'),
                'color'      => $self->weighted_score >= 4 ? 'emerald' : ($self->weighted_score >= 3 ? 'blue' : 'amber'),
                'cycle_name' => $self->cycle?->name ?? 'Ciclo anterior',
                'type'       => 'Auto-avaliação',
            ];
        }

        // Fallback: avaliação pelo gerente
        $managerEval = ManagerEvaluationEntry::with(['evaluation.cycle', 'criterion'])
            ->where('employee_id', $this->teamUserId)
            ->whereHas('evaluation', fn ($q) => $q->where('status', 'completed'))
            ->whereHas('evaluation.cycle', fn ($q) => $q->where('status', 'closed'))
            ->latest()
            ->first();

        if ($managerEval) {
            $avg = ManagerEvaluationEntry::where('employee_id', $this->teamUserId)
                ->whereHas('evaluation', fn ($q) => $q->where('id', $managerEval->manager_evaluation_id))
                ->avg('score');

            if ($avg !== null) {
                $avg = round((float) $avg, 1);
                return [
                    'score'      => $avg,
                    'label'      => $avg >= 4 ? 'Alto desempenho' : ($avg >= 3 ? 'Bom desempenho' : 'Em desenvolvimento'),
                    'color'      => $avg >= 4 ? 'emerald' : ($avg >= 3 ? 'blue' : 'amber'),
                    'cycle_name' => $managerEval->evaluation?->cycle?->name ?? 'Ciclo anterior',
                    'type'       => 'Avaliação do Gerente',
                ];
            }
        }

        return null;
    }

    // ═════════════════════════════════════════════════════════════════
    // INTEGRAÇÃO: REUNIÕES
    // ═════════════════════════════════════════════════════════════════

    // ── Lifecycle hooks para reatividade das salas ────────────────────

    public function updatedMeetingDate(): void
    {
        $this->meetingRoomId = null;
        unset($this->availableRoomsForMeeting);
    }

    public function updatedMeetingStartTime(): void
    {
        $this->meetingRoomId = null;
        unset($this->availableRoomsForMeeting);
    }

    public function updatedMeetingDuration(): void
    {
        $this->meetingRoomId = null;
        unset($this->availableRoomsForMeeting);
    }

    public function updatedMeetingLocationType(): void
    {
        $this->meetingRoomId = null;
        unset($this->availableRoomsForMeeting);
    }

    public function updatedVizUserId(): void
    {
        unset($this->vizPlan, $this->ganttData, $this->radarCompetenciasData);
    }

    public function openMeetingModal(int $userId): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);
        $employee = User::find($userId);
        if (! $employee) return;

        $this->meetingForUserId     = $userId;
        $this->meetingTitle         = 'Acompanhamento DPI — ' . explode(' ', $employee->name)[0];
        $this->meetingDate          = now()->addDay()->format('Y-m-d');
        $this->meetingStartTime     = '09:00';
        $this->meetingDuration      = '60';
        $this->meetingPlatform      = 'google_meet';
        $this->meetingLink          = '';
        $this->meetingLocationType  = 'online';
        $this->meetingRoomId        = null;
        unset($this->availableRoomsForMeeting);
        $this->meetingModal         = true;
    }

    public function saveMeeting(): void
    {
        $this->requireRole(['administrator', 'hr', 'manager']);

        $rules = [
            'meetingTitle'     => 'required|string|max:255',
            'meetingDate'      => 'required|date|after_or_equal:today',
            'meetingStartTime' => 'required',
            'meetingDuration'  => 'required|integer|min:15|max:480',
        ];

        if ($this->meetingLocationType === 'online') {
            $rules['meetingLink'] = 'nullable|url';
        } else {
            $rules['meetingRoomId'] = 'required|integer|exists:rooms,id';
        }

        $this->validate($rules, [
            'meetingTitle.required'      => 'Informe o título da reunião.',
            'meetingDate.required'       => 'Informe a data.',
            'meetingDate.after_or_equal' => 'A data deve ser hoje ou no futuro.',
            'meetingStartTime.required'  => 'Informe o horário.',
            'meetingLink.url'            => 'O link deve ser uma URL válida.',
            'meetingRoomId.required'     => 'Selecione uma sala para a reunião presencial.',
            'meetingRoomId.exists'       => 'Sala inválida.',
        ]);

        $startDt = \Carbon\Carbon::parse($this->meetingDate . ' ' . $this->meetingStartTime);
        $endDt   = $startDt->copy()->addMinutes((int) $this->meetingDuration);

        // Verifica disponibilidade da sala antes de criar
        if ($this->meetingLocationType === 'presencial' && $this->meetingRoomId) {
            if (Reservations::temConflito($this->meetingRoomId, $startDt, $endDt)) {
                $this->alertError('Sala indisponível', 'Esta sala já possui uma reserva neste horário. Escolha outro horário ou outra sala.');
                return;
            }
            $meetingConflict = Meeting::where('room_id', $this->meetingRoomId)
                ->where('status', '!=', 'cancelada')
                ->where('start_time', '<', $endDt)
                ->where('end_time', '>', $startDt)
                ->exists();
            if ($meetingConflict) {
                $this->alertError('Sala indisponível', 'Esta sala já possui uma reunião agendada neste horário.');
                return;
            }
        }

        $meeting = Meeting::create([
            'title'           => $this->sanitize($this->meetingTitle),
            'description'     => 'Reunião de acompanhamento do DPI — ' . $this->selectedYear,
            'start_time'      => $startDt,
            'end_time'        => $endDt,
            'location_type'   => $this->meetingLocationType,
            'online_platform' => $this->meetingLocationType === 'online' ? $this->meetingPlatform : null,
            'online_link'     => $this->meetingLocationType === 'online' ? ($this->meetingLink ?: null) : null,
            'room_id'         => $this->meetingLocationType === 'presencial' ? $this->meetingRoomId : null,
            'organizer_id'    => Auth::id(),
            'status'          => 'pendente',
        ]);

        // Adiciona participantes: organizador + colaborador
        $meeting->participants()->syncWithoutDetaching([
            Auth::id()              => ['rsvp' => 'aceito'],
            $this->meetingForUserId => ['rsvp' => 'pendente'],
        ]);

        // Adiciona item de pauta sobre o DPI
        $employee = User::find($this->meetingForUserId);
        $meeting->agendaItems()->create([
            'title'               => 'Revisão do Plano DPI ' . $this->selectedYear . ' — ' . ($employee?->name ?? ''),
            'duration_minutes'    => (int) $this->meetingDuration,
            'responsible_user_id' => Auth::id(),
            'sort_order'          => 1,
        ]);

        // Cria reserva da sala — aparece no módulo Salas exatamente igual a uma reunião normal
        if ($this->meetingLocationType === 'presencial' && $this->meetingRoomId) {
            Reservations::updateOrCreate(
                ['meeting_id' => $meeting->id],
                [
                    'room_id'         => $this->meetingRoomId,
                    'user_id'         => Auth::id(),
                    'title'           => $meeting->title,
                    'description'     => $meeting->description,
                    'start_time'      => $startDt,
                    'end_time'        => $endDt,
                    'attendees_count' => 2,
                    'status'          => 'confirmada',
                    'is_maintenance'  => false,
                ]
            );
        }

        $this->meetingModal = false;
        $successMsg = $this->meetingLocationType === 'presencial'
            ? 'Reunião presencial agendada e sala reservada com sucesso!'
            : 'Reunião online agendada com sucesso!';
        $this->alertSuccess('Reunião agendada!', $successMsg);
    }

    /**
     * Lista de salas com flag is_available para o horário selecionado no modal.
     * Recomputed sempre que date/time/duration/locationType mudam.
     */
    #[Computed]
    public function availableRoomsForMeeting(): \Illuminate\Support\Collection
    {
        if ($this->meetingLocationType !== 'presencial' || ! $this->meetingDate || ! $this->meetingStartTime) {
            return collect();
        }

        $startDt = \Carbon\Carbon::parse($this->meetingDate . ' ' . $this->meetingStartTime);
        $endDt   = $startDt->copy()->addMinutes((int) $this->meetingDuration);

        return room::orderBy('name')->get()->map(function ($r) use ($startDt, $endDt) {
            $reservationConflict = Reservations::temConflito($r->id, $startDt, $endDt);
            $meetingConflict     = Meeting::where('room_id', $r->id)
                ->where('status', '!=', 'cancelada')
                ->where('start_time', '<', $endDt)
                ->where('end_time', '>', $startDt)
                ->exists();

            $r->setAttribute('is_available', ! $reservationConflict && ! $meetingConflict);
            return $r;
        });
    }

    /**
     * Reuniões de acompanhamento DPI recentes com o membro selecionado.
     */
    #[Computed]
    public function teamMemberMeetings(): \Illuminate\Support\Collection
    {
        if (! $this->teamUserId) return collect();
        if (! Auth::user()->isRhOuDp() && ! Auth::user()->isGerente()) return collect();

        return Meeting::where(function ($q) {
                $q->where('organizer_id', Auth::id())
                  ->orWhere('organizer_id', $this->teamUserId);
            })
            ->whereHas('participants', fn ($q) => $q->whereIn('users.id', [Auth::id(), $this->teamUserId]))
            ->where('title', 'like', '%DPI%')
            ->orderBy('start_time', 'desc')
            ->limit(3)
            ->get();
    }

    // ═════════════════════════════════════════════════════════════════
    // ANÁLISES AVANÇADAS
    // ═════════════════════════════════════════════════════════════════

    /**
     * Análise detalhada do plano pessoal do usuário logado.
     * KPIs, radar de competências, evolução mensal, ações em risco e projeção.
     */
    #[Computed]
    public function analyticsPersonal(): array
    {
        $plan = DpiPlan::with(['goals.actions'])
            ->where('user_id', Auth::id())
            ->where('year', $this->selectedYear)
            ->first();

        if (! $plan) return [];

        $goals      = $plan->goals;
        $allActions = $goals->flatMap(fn ($g) => $g->actions);

        $totalActions = $allActions->count();
        $doneActions  = $allActions->where('status', 'concluido')->count();
        $inProgress   = $allActions->where('status', 'em_andamento')->count();
        $pending      = $allActions->where('status', 'pendente')->count();
        $overdue      = $allActions
            ->where('status', '!=', 'concluido')
            ->filter(fn ($a) => $a->target_date && $a->target_date->isPast())
            ->count();
        $progress = $totalActions > 0 ? (int) round($doneActions / $totalActions * 100) : 0;

        // Distribuição por tipo
        $types      = ['curso', 'certificacao', 'leitura', 'mentoria', 'projeto', 'workshop', 'outro'];
        $typeLabels = ['Curso', 'Certificação', 'Leitura', 'Mentoria', 'Projeto', 'Workshop', 'Outro'];
        $typeSeries = array_values(array_map(fn ($t) => $allActions->where('type', $t)->count(), $types));
        $typeDone   = array_values(array_map(fn ($t) => $allActions->where('type', $t)->where('status', 'concluido')->count(), $types));
        // Remove tipos sem nenhuma ação
        $typeData = [];
        foreach ($types as $i => $t) {
            if ($typeSeries[$i] > 0) {
                $typeData[] = ['label' => $typeLabels[$i], 'total' => $typeSeries[$i], 'done' => $typeDone[$i]];
            }
        }

        // Radar de competências: nível atual vs meta
        $radarLabels  = $goals->map(fn ($g) => mb_strlen($g->name) > 20
            ? mb_substr($g->name, 0, 20) . '…'
            : $g->name)->values()->toArray();
        $radarCurrent = $goals->map(fn ($g) => $g->nivel_atual)->values()->toArray();
        $radarTarget  = $goals->map(fn ($g) => $g->nivel_meta)->values()->toArray();

        // Ações em risco: atrasadas ou com prazo nos próximos 14 dias
        $atRisk = $allActions
            ->where('status', '!=', 'concluido')
            ->filter(fn ($a) => $a->target_date)
            ->filter(fn ($a) => $a->target_date->lte(now()->addDays(14)))
            ->sortBy('target_date')
            ->take(6)
            ->values();

        // Evolução mensal: ações concluídas por mês (últimos 6 meses)
        $monthLabels = [];
        $monthCounts = [];
        for ($i = 5; $i >= 0; $i--) {
            $m             = now()->subMonths($i);
            $monthLabels[] = $m->format('M/y');
            $monthCounts[] = $allActions
                ->where('status', 'concluido')
                ->filter(fn ($a) => $a->validated_at
                    && (int) $a->validated_at->format('m') === (int) $m->format('m')
                    && (int) $a->validated_at->format('Y') === (int) $m->format('Y'))
                ->count();
        }

        // Velocidade e projeção
        $recentDone = $allActions
            ->where('status', 'concluido')
            ->filter(fn ($a) => $a->validated_at && $a->validated_at->gte(now()->subDays(30)))
            ->count();
        $remaining      = max(0, $totalActions - $doneActions);
        $projectionDays = ($recentDone > 0 && $remaining > 0)
            ? (int) ceil($remaining / ($recentDone / 30))
            : null;

        // Prioridade de competências
        $goalsByPriority = [
            'alta'  => $goals->where('priority', 'alta')->count(),
            'media' => $goals->where('priority', 'media')->count(),
            'baixa' => $goals->where('priority', 'baixa')->count(),
        ];

        return compact(
            'totalActions', 'doneActions', 'inProgress', 'pending', 'overdue', 'progress',
            'typeData', 'typeLabels', 'typeSeries', 'typeDone',
            'radarLabels', 'radarCurrent', 'radarTarget',
            'atRisk', 'monthLabels', 'monthCounts',
            'recentDone', 'projectionDays', 'goalsByPriority'
        );
    }

    /**
     * Análise consolidada da equipe para Gerente / RH.
     */
    #[Computed]
    public function analyticsTeam(): array
    {
        $authUser = Auth::user();
        if (! $authUser->isRhOuDp() && ! $authUser->isGerente()) return [];

        $memberIds = $this->teamMembers->pluck('id');
        if ($memberIds->isEmpty()) return [];

        $plans = DpiPlan::with(['goals.actions', 'user'])
            ->where('year', $this->selectedYear)
            ->whereIn('user_id', $memberIds)
            ->get();

        $rows = $plans->map(function ($p) {
            $actions = $p->goals->flatMap(fn ($g) => $g->actions);
            $total   = $actions->count();
            $done    = $actions->where('status', 'concluido')->count();
            $inProg  = $actions->where('status', 'em_andamento')->count();
            $overdue = $actions
                ->where('status', '!=', 'concluido')
                ->filter(fn ($a) => $a->target_date && $a->target_date->isPast())
                ->count();
            $health = $total > 0 ? max(0, 100 - (int) round($overdue / $total * 100)) : 100;

            return [
                'name'     => $p->user->name,
                'first'    => explode(' ', trim($p->user->name))[0],
                'user_id'  => $p->user_id,
                'progress' => $p->progress_percent,
                'total'    => $total,
                'done'     => $done,
                'in_prog'  => $inProg,
                'overdue'  => $overdue,
                'health'   => $health,
                'status'   => $p->status,
            ];
        })->sortByDesc('progress')->values();

        $totalMembers = $this->teamMembers->count();
        $withPlan     = $plans->count();
        $avgProgress  = $plans->isNotEmpty()
            ? (int) round($plans->avg(fn ($p) => $p->progress_percent))
            : 0;
        $atRiskCount  = $rows->filter(fn ($r) => $r['overdue'] > 0)->count();
        $totalOverdue = $rows->sum('overdue');
        $healthScore  = $rows->isNotEmpty() ? (int) round($rows->avg('health')) : 100;

        // Dados para o gráfico de barras horizontal (progresso por pessoa)
        $chartNames    = $rows->pluck('first')->toArray();
        $chartProgress = $rows->pluck('progress')->toArray();

        // Distribuição por status de plano
        $byStatus = $plans->groupBy('status');

        return compact(
            'rows', 'totalMembers', 'withPlan', 'avgProgress',
            'atRiskCount', 'totalOverdue', 'healthScore',
            'chartNames', 'chartProgress', 'byStatus'
        );
    }

    // ═════════════════════════════════════════════════════════════════
    // VISUALIZAÇÕES ALTERNATIVAS — Gantt & Radar
    // ═════════════════════════════════════════════════════════════════

    /**
     * Plano usado para as visualizações alternativas.
     * RH/Gerente pode escolher qualquer membro; colaborador vê o próprio.
     */
    #[Computed]
    public function vizPlan(): ?DpiPlan
    {
        $authUser = Auth::user();
        $userId   = $this->vizUserId ?? Auth::id();

        // Segurança: Gerente só vê membros do próprio departamento
        if ($this->vizUserId && $authUser->isGerente()) {
            $member = User::where('id', $this->vizUserId)
                ->where('department_id', $authUser->department_id)
                ->first();
            if (! $member) return null;
        }

        return DpiPlan::with([
            'goals'         => fn ($q) => $q->orderBy('order'),
            'goals.actions' => fn ($q) => $q->orderBy('target_date')->orderBy('order'),
        ])
            ->where('user_id', $userId)
            ->where('year', $this->selectedYear)
            ->first();
    }

    /**
     * Dados para o gráfico Gantt (rangeBar) do plano.
     * Retorna array de itens com start/end em ms para ApexCharts.
     */
    #[Computed]
    public function ganttData(): array
    {
        $plan = $this->vizPlan;
        if (! $plan) return ['series' => [], 'no_date_count' => 0];

        $year      = $this->selectedYear;
        $yearStart = \Carbon\Carbon::createFromDate($year, 1, 1)->startOfDay();
        $yearEnd   = \Carbon\Carbon::createFromDate($year, 12, 31)->endOfDay();
        $today     = now()->toDateString();

        $series      = [];
        $noDateCount = 0;

        foreach ($plan->goals as $goal) {
            $datedActions = $goal->actions->filter(fn ($a) => ! empty($a->target_date));

            if ($datedActions->isEmpty()) {
                $noDateCount += $goal->actions->count();
                continue;
            }

            $noDateCount += $goal->actions->count() - $datedActions->count();

            // ── Linha pai: objetivo ───────────────────────────────────
            $goalId = 'g-' . $goal->id;

            $goalStart = $datedActions->map(function ($a) use ($yearStart) {
                $dt = $a->status === 'concluido' && $a->validated_at
                    ? \Carbon\Carbon::parse($a->validated_at)
                    : \Carbon\Carbon::parse($a->created_at);
                return $dt->lt($yearStart) ? $yearStart->copy() : $dt;
            })->min();

            $goalEnd = $datedActions->map(function ($a) use ($yearEnd) {
                $dt = \Carbon\Carbon::parse($a->target_date);
                return $dt->gt($yearEnd) ? $yearEnd->copy() : $dt;
            })->max();

            $done     = $datedActions->where('status', 'concluido')->count();
            $total    = $datedActions->count();
            $progress = $total > 0 ? (int) round($done / $total * 100) : 0;

            $series[] = [
                'id'               => $goalId,
                'name'             => $goal->name,
                'startTime'        => $goalStart->format('Y-m-d'),
                'endTime'          => $goalEnd->format('Y-m-d'),
                'progress'         => $progress,
                'collapsed'        => false,
                'barBackgroundColor' => '#6366f1',
            ];

            // ── Linhas filhas: ações ──────────────────────────────────
            foreach ($datedActions as $action) {
                $startDt = $action->status === 'concluido' && $action->validated_at
                    ? \Carbon\Carbon::parse($action->validated_at)
                    : \Carbon\Carbon::parse($action->created_at);

                if ($startDt->lt($yearStart)) $startDt = $yearStart->copy();

                $endDt = \Carbon\Carbon::parse($action->target_date);
                if ($endDt->gt($yearEnd)) $endDt = $yearEnd->copy();

                if ($startDt->gte($endDt)) {
                    $startDt = $endDt->copy()->subDays(1);
                    if ($startDt->lt($yearStart)) $startDt = $yearStart->copy();
                }

                $isOverdue = $action->status !== 'concluido'
                    && $action->target_date < $today;

                $color = match (true) {
                    $action->status === 'concluido' => '#10b981',
                    $isOverdue                      => '#ef4444',
                    $action->status === 'em_andamento' => '#3b82f6',
                    default                         => '#94a3b8',
                };

                $series[] = [
                    'id'               => 'a-' . $action->id,
                    'name'             => mb_strlen($action->title) > 45
                        ? mb_substr($action->title, 0, 43) . '…'
                        : $action->title,
                    'startTime'        => $startDt->format('Y-m-d'),
                    'endTime'          => $endDt->format('Y-m-d'),
                    'progress'         => match ($action->status) {
                        'concluido'    => 100,
                        'em_andamento' => 50,
                        default        => 0,
                    },
                    'parentId'         => $goalId,
                    'barBackgroundColor' => $color,
                ];
            }
        }

        return ['series' => $series, 'no_date_count' => $noDateCount];
    }

    /**
     * Dados do Radar de Competências por objetivo para visualização avançada.
     */
    #[Computed]
    public function radarCompetenciasData(): array
    {
        $plan = $this->vizPlan;
        if (! $plan) return [];

        $today = now()->toDateString();

        return $plan->goals->map(function ($goal) use ($today) {
            $actions = $goal->actions;
            $total   = $actions->count();
            $done    = $actions->where('status', 'concluido')->count();
            $inProg  = $actions->where('status', 'em_andamento')->count();
            $pending = $actions->where('status', 'pendente')->count();
            $overdue = $actions->filter(fn ($a) =>
                $a->status !== 'concluido' && $a->target_date && $a->target_date < $today
            )->count();
            $progress = $total > 0 ? (int) round($done / $total * 100) : 0;
            $gap      = max(0, ($goal->nivel_meta ?? 0) - ($goal->nivel_atual ?? 0));

            $short = mb_strlen($goal->name) > 18
                ? mb_substr($goal->name, 0, 18) . '…'
                : $goal->name;

            return [
                'id'          => $goal->id,
                'name'        => $goal->name,
                'short'       => $short,
                'priority'    => $goal->priority ?? 'baixa',
                'nivel_atual' => (float) ($goal->nivel_atual ?? 0),
                'nivel_meta'  => (float) ($goal->nivel_meta  ?? 0),
                'gap'         => $gap,
                'total'       => $total,
                'done'        => $done,
                'in_prog'     => $inProg,
                'pending'     => $pending,
                'overdue'     => $overdue,
                'progress'    => $progress,
            ];
        })->values()->toArray();
    }

    // ══════════════════════════════════════════════════════════════════
    //  TEMPLATES
    // ══════════════════════════════════════════════════════════════════

    #[Computed]
    public function availableTemplates(): \Illuminate\Database\Eloquent\Collection
    {
        return DpiTemplate::active()
            ->with(['goals.actions', 'creator'])
            ->orderBy('name')
            ->get();
    }

    // ID do plano alvo ao aplicar template (null = próprio plano)
    public ?int $templateTargetPlanId = null;

    /**
     * Abre o modal de aplicar template.
     * @param int|null $planId  Se informado, aplica ao plano desse funcionário (Equipe).
     */
    public function openTemplateModal(?int $planId = null): void
    {
        $this->templateTargetPlanId = $planId;
        $this->templateModal        = true;
    }

    /** Fecha o modal de aplicar template. */
    public function closeTemplateModal(): void
    {
        $this->templateModal        = false;
        $this->templateTargetPlanId = null;
    }

    /**
     * Aplica um template ao plano alvo.
     * Se $templateTargetPlanId estiver definido, aplica ao plano do funcionário (Equipe).
     * Caso contrário, aplica ao próprio plano do usuário autenticado.
     */
    public function applyTemplate(int $templateId): void
    {
        if ($this->templateTargetPlanId) {
            // Aplica ao plano de um funcionário (Gerente/RH na aba Equipe)
            $plan = DpiPlan::with('goals')->find($this->templateTargetPlanId);

            if (! $plan) {
                $this->dispatch('notify', type: 'error', message: 'Plano não encontrado.');
                return;
            }

            // Segurança: apenas RH ou Gerente do mesmo dept pode aplicar
            $auth = Auth::user();
            if (! $auth->isRhOuDp() && ! ($auth->isGerente() && $plan->user->department_id === $auth->department_id)) {
                $this->dispatch('notify', type: 'error', message: 'Sem permissão para editar este plano.');
                return;
            }
        } else {
            $plan = $this->myPlan;

            if (! $plan) {
                $this->dispatch('notify', type: 'error', message: 'Você não possui um plano DPI para este ano.');
                return;
            }
        }

        $template = DpiTemplate::with('goals.actions')->find($templateId);

        if (! $template) {
            $this->dispatch('notify', type: 'error', message: 'Template não encontrado.');
            return;
        }

        $nextGoalOrder = $plan->goals()->max('order') + 1;

        foreach ($template->goals as $tGoal) {
            $goal = DpiGoal::create([
                'dpi_plan_id' => $plan->id,
                'name'        => $tGoal->name,
                'priority'    => $tGoal->priority,
                'nivel_atual' => $tGoal->nivel_atual,
                'nivel_meta'  => $tGoal->nivel_meta,
                'order'       => $nextGoalOrder++,
            ]);

            foreach ($tGoal->actions as $order => $tAction) {
                DpiAction::create([
                    'dpi_goal_id' => $goal->id,
                    'title'       => $tAction->title,
                    'description' => $tAction->description,
                    'type'        => $tAction->type,
                    'status'      => 'pendente',
                    'order'       => $order,
                ]);
            }
        }

        $this->templateModal        = false;
        $this->templateTargetPlanId = null;
        unset($this->myPlan, $this->teamPlan);

        $this->dispatch('notify', type: 'success',
            message: "Template \"{$template->name}\" aplicado com sucesso!");
    }

    // ── Gerenciar Templates ───────────────────────────────────────────

    public function openManageTemplates(): void
    {
        $this->resetTemplateForm();
        $this->manageTemplateModal = true;
        $this->templateTab         = 'list';
    }

    public function closeManageTemplates(): void
    {
        $this->manageTemplateModal = false;
        $this->resetTemplateForm();
    }

    public function startCreateTemplate(): void
    {
        $this->resetTemplateForm();
        $this->templateTab = 'create';
        $this->addTemplateGoalRow();
    }

    public function editTemplate(int $id): void
    {
        $template = DpiTemplate::with('goals.actions')->find($id);
        if (! $template) return;

        $this->editTemplateId = $id;
        $this->tplName        = $template->name;
        $this->tplDescription = $template->description ?? '';
        $this->tplGoals       = $template->goals->map(fn ($g) => [
            'name'        => $g->name,
            'priority'    => $g->priority,
            'nivel_atual' => $g->nivel_atual,
            'nivel_meta'  => $g->nivel_meta,
            'actions'     => $g->actions->map(fn ($a) => [
                'title'       => $a->title,
                'description' => $a->description ?? '',
                'type'        => $a->type,
            ])->toArray(),
        ])->toArray();

        $this->templateTab = 'create';
    }

    public function deleteTemplate(int $id): void
    {
        if (! Auth::user()->isRhOuDp() && ! Auth::user()->isGerente()) return;

        DpiTemplate::find($id)?->delete();
        unset($this->availableTemplates);
        $this->dispatch('notify', type: 'success', message: 'Template removido.');
    }

    public function toggleTemplateActive(int $id): void
    {
        $t = DpiTemplate::find($id);
        if (! $t) return;
        $t->update(['is_active' => ! $t->is_active]);
        unset($this->availableTemplates);
    }

    public function saveTemplate(): void
    {
        if (! Auth::user()->isRhOuDp() && ! Auth::user()->isGerente()) return;

        $this->validate([
            'tplName'          => 'required|string|max:120',
            'tplDescription'   => 'nullable|string|max:500',
            'tplGoals'         => 'required|array|min:1',
            'tplGoals.*.name'  => 'required|string|max:120',
        ], [
            'tplName.required'         => 'O nome do template é obrigatório.',
            'tplGoals.required'        => 'Adicione ao menos uma meta ao template.',
            'tplGoals.*.name.required' => 'Toda meta precisa de um nome.',
        ]);

        if ($this->editTemplateId) {
            // Atualiza template existente
            $template = DpiTemplate::find($this->editTemplateId);
            $template->update([
                'name'        => $this->tplName,
                'description' => $this->tplDescription ?: null,
            ]);

            // Recria metas e ações
            $template->goals()->delete();
        } else {
            $template = DpiTemplate::create([
                'name'        => $this->tplName,
                'description' => $this->tplDescription ?: null,
                'is_active'   => true,
                'created_by'  => Auth::id(),
            ]);
        }

        foreach ($this->tplGoals as $gOrder => $gData) {
            $goal = DpiTemplateGoal::create([
                'dpi_template_id' => $template->id,
                'name'            => $gData['name'],
                'priority'        => $gData['priority'] ?? 'media',
                'nivel_atual'     => $gData['nivel_atual'] ?? 1,
                'nivel_meta'      => $gData['nivel_meta'] ?? 3,
                'order'           => $gOrder,
            ]);

            foreach (($gData['actions'] ?? []) as $aOrder => $aData) {
                if (empty(trim($aData['title'] ?? ''))) continue;
                DpiTemplateAction::create([
                    'dpi_template_goal_id' => $goal->id,
                    'title'                => $aData['title'],
                    'description'          => $aData['description'] ?? null,
                    'type'                 => $aData['type'] ?? 'curso',
                    'order'                => $aOrder,
                ]);
            }
        }

        unset($this->availableTemplates);
        $this->resetTemplateForm();
        $this->templateTab = 'list';

        $verb = $this->editTemplateId ? 'atualizado' : 'criado';
        $this->dispatch('notify', type: 'success', message: "Template {$verb} com sucesso!");
    }

    // ── Helpers de form ───────────────────────────────────────────────

    public function addTemplateGoalRow(): void
    {
        $this->tplGoals[] = [
            'name'        => '',
            'priority'    => 'media',
            'nivel_atual' => 1,
            'nivel_meta'  => 3,
            'actions'     => [],
        ];
    }

    public function removeTemplateGoalRow(int $index): void
    {
        array_splice($this->tplGoals, $index, 1);
    }

    public function addTemplateActionRow(int $goalIndex): void
    {
        $this->tplGoals[$goalIndex]['actions'][] = [
            'title'       => '',
            'description' => '',
            'type'        => 'curso',
        ];
    }

    public function removeTemplateActionRow(int $goalIndex, int $actionIndex): void
    {
        array_splice($this->tplGoals[$goalIndex]['actions'], $actionIndex, 1);
    }

    private function resetTemplateForm(): void
    {
        $this->tplName        = '';
        $this->tplDescription = '';
        $this->tplGoals       = [];
        $this->editTemplateId = null;
        $this->resetErrorBag(['tplName', 'tplDescription', 'tplGoals']);
    }

    public function render(): \Illuminate\Contracts\View\View
    {
        return view('livewire.pages.dpi.index');
    }
}
