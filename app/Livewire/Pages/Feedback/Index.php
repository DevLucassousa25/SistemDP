<?php

namespace App\Livewire\Pages\Feedback;

use App\Livewire\SecureComponent;
use App\Models\Feedback;
use App\Models\FeedbackActionTask;
use App\Models\FeedbackComment;
use App\Models\FeedbackStatusHistory;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\WithFileUploads;

class Index extends SecureComponent
{
    use WithFileUploads;

    // ── Estado da UI ──────────────────────────────────────────────────
    public bool   $modalOpen     = false;
    public bool   $wizardMode    = false;     // true = wizard de criação
    public bool   $viewModal     = false;
    public bool   $confirmDelete = false;
    public string $activeTab     = 'lista';   // 'lista' | 'analytics'
    public ?int   $editingId     = null;
    public ?int   $viewingId     = null;
    public ?int   $deletingId    = null;

    // ── Filtros ───────────────────────────────────────────────────────
    public string $search           = '';
    public string $typeFilter       = '';
    public string $statusFilter     = '';
    public string $categoryFilter   = '';
    public string $severityFilter   = '';                 // baixo|medio|alto|critico
    public string $periodFilter     = '';                 // este_mes|ultimo_mes|ultimos_3_meses|este_ano
    public string $actionPlanFilter = '';                 // com_plano|sem_plano|plano_vencido

    // ── Campos do formulário ──────────────────────────────────────────
    public ?int    $employee_id                = null;
    public string  $type                      = 'alerta';
    public string  $category                  = 'desempenho';
    public string  $severity                  = 'baixo';
    public ?int    $rating                    = null;
    public string  $occurred_at               = '';
    public string  $message                   = '';
    public string  $template                  = 'livre';
    public array   $templateData              = [];
    public string  $privateNote               = '';
    public string  $action_plan               = '';
    public string  $action_plan_deadline      = '';
    public ?int    $action_plan_responsible_id = null;
    public string  $status                    = 'aberto';
    public bool    $is_anonymous              = false;
    public $attachment                        = null;

    // ── Tarefas do plano de ação (buffer para o formulário) ───────────
    public array  $actionTasks          = [];  // [{description, responsible_id, due_date, phase, resources}]
    public string $newTaskDesc          = '';
    public string $newTaskDue           = '';
    public string $newTaskPhase         = '';   // ''|30_dias|60_dias|90_dias
    public string $newTaskResources     = '';
    public ?int   $newTaskResponsibleId = null;

    // ── Comentários ───────────────────────────────────────────────────
    public string $newComment = '';

    // ─────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();

        if (! Auth::user()->podeGerenciarPesquisas()) {
            abort(403, 'Sem permissão para acessar feedbacks.');
        }

        $this->occurred_at = now()->format('Y-m-d');
    }

    // ── Query base com controle de acesso ─────────────────────────────
    private function baseQuery()
    {
        $user = Auth::user();

        return Feedback::when(
            $user->isGerente(),
            fn ($q) => $q->whereHas('employee', fn ($e) => $e->where('department_id', $user->department_id))
        );
    }

    // ── Feedbacks filtrados ───────────────────────────────────────────
    #[Computed]
    public function feedbacks()
    {
        return $this->baseQuery()
            ->with(['employee.department', 'evaluator'])
            ->when($this->search, function ($q) {
                $term = "%{$this->search}%";
                $q->where(function ($inner) use ($term) {
                    $inner->whereHas('employee', fn ($e) => $e->where('name', 'like', $term))
                          ->orWhereHas('evaluator', fn ($e) => $e->where('name', 'like', $term));
                });
            })
            ->when($this->typeFilter,       fn ($q) => $q->where('type',        $this->typeFilter))
            ->when($this->statusFilter,     fn ($q) => $q->where('status',      $this->statusFilter))
            ->when($this->categoryFilter,   fn ($q) => $q->where('category',    $this->categoryFilter))
            ->when($this->severityFilter,   fn ($q) => $q->where('severity',    $this->severityFilter))
            ->when($this->periodFilter, function ($q) {
                match ($this->periodFilter) {
                    'este_mes'        => $q->where('occurred_at', '>=', now()->startOfMonth()),
                    'ultimo_mes'      => $q->whereBetween('occurred_at', [now()->subMonth()->startOfMonth(), now()->subMonth()->endOfMonth()]),
                    'ultimos_3_meses' => $q->where('occurred_at', '>=', now()->subMonths(3)->startOfMonth()),
                    'este_ano'        => $q->where('occurred_at', '>=', now()->startOfYear()),
                    default           => null,
                };
            })
            ->when($this->actionPlanFilter, function ($q) {
                if ($this->actionPlanFilter === 'com_plano') {
                    $q->whereNotNull('action_plan_deadline');
                } elseif ($this->actionPlanFilter === 'sem_plano') {
                    $q->whereNull('action_plan_deadline');
                } elseif ($this->actionPlanFilter === 'plano_vencido') {
                    $q->whereNotNull('action_plan_deadline')
                      ->where('action_plan_deadline', '<', now()->toDateString())
                      ->whereNotIn('status', ['resolvido', 'arquivado']);
                }
            })
            ->orderByRaw("CASE severity WHEN 'critico' THEN 1 WHEN 'alto' THEN 2 WHEN 'medio' THEN 3 WHEN 'baixo' THEN 4 ELSE 5 END")
            ->orderBy('occurred_at', 'desc')
            ->get();
    }

    // ── Stats gerais ──────────────────────────────────────────────────
    #[Computed]
    public function stats(): array
    {
        $base = $this->baseQuery();

        return [
            'total'           => (clone $base)->count(),
            'reconhecimentos' => (clone $base)->where('type', 'reconhecimento')->count(),
            'alertas'         => (clone $base)->where('type', 'alerta')->count(),
            'criticos'        => (clone $base)->where('type', 'alerta')->where('severity', 'critico')->count(),
            'pendentes'       => (clone $base)->whereIn('status', ['aberto', 'em_analise', 'aguardando_plano'])->count(),
        ];
    }

    // ── Dados para o painel Analytics ────────────────────────────────
    #[Computed]
    public function analyticsData(): array
    {
        $base = $this->baseQuery();

        $byType     = (clone $base)->selectRaw("type, COUNT(*) as cnt")->groupBy('type')->pluck('cnt', 'type');
        $byCategory = (clone $base)->selectRaw("category, COUNT(*) as cnt")->groupBy('category')->orderByDesc('cnt')->pluck('cnt', 'category');
        $bySeverity = (clone $base)->where('type', 'alerta')->selectRaw("severity, COUNT(*) as cnt")->groupBy('severity')->pluck('cnt', 'severity');
        $byStatus   = (clone $base)->selectRaw("status, COUNT(*) as cnt")->groupBy('status')->pluck('cnt', 'status');

        // ── Tendência: mês atual vs mês anterior ─────────────────────
        $curStart  = now()->startOfMonth();
        $prevStart = now()->subMonth()->startOfMonth();
        $prevEnd   = now()->subMonth()->endOfMonth();

        $trend = [];
        foreach (['total' => null, 'alertas' => 'alerta', 'reconhecimentos' => 'reconhecimento', 'resolvidos' => null] as $key => $type) {
            $curQ  = clone $base;
            $prevQ = clone $base;
            if ($type) { $curQ->where('type', $type); $prevQ->where('type', $type); }
            if ($key === 'resolvidos') { $curQ->where('status', 'resolvido'); $prevQ->where('status', 'resolvido'); }
            $cur  = $curQ->where('created_at', '>=', $curStart)->count();
            $prev = $prevQ->whereBetween('created_at', [$prevStart, $prevEnd])->count();
            $diff = $prev > 0 ? round((($cur - $prev) / $prev) * 100) : ($cur > 0 ? 100 : 0);
            $trend[$key] = ['current' => $cur, 'previous' => $prev, 'diff' => $diff];
        }

        // ── Evolução mensal (últimos 12 meses) ───────────────────────
        $monthlyRaw = (clone $base)
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as month, COUNT(*) as total")
            ->where('created_at', '>=', now()->subMonths(11)->startOfMonth())
            ->groupBy('month')
            ->orderBy('month')
            ->pluck('total', 'month');

        $monthLabels = [];
        $monthData   = [];
        for ($i = 11; $i >= 0; $i--) {
            $key           = now()->subMonths($i)->format('Y-m');
            $monthLabels[] = ucfirst(now()->subMonths($i)->translatedFormat('M/y'));
            $monthData[]   = (int) ($monthlyRaw[$key] ?? 0);
        }

        // ── Heatmap por departamento ──────────────────────────────────
        $deptNames  = \App\Models\Department::pluck('name', 'id');
        $heatmapRaw = (clone $base)
            ->join('users', 'feedbacks.employee_id', '=', 'users.id')
            ->selectRaw("users.department_id, feedbacks.type, COUNT(*) as cnt")
            ->whereNotNull('users.department_id')
            ->groupBy('users.department_id', 'feedbacks.type')
            ->get();

        $heatmap = [];
        foreach ($heatmapRaw as $row) {
            $dept = $deptNames[$row->department_id] ?? 'Outros';
            $heatmap[$dept][$row->type] = (int) $row->cnt;
        }

        // ── Proporção reconhecimento/alerta por departamento ─────────
        $deptRatio = [];
        foreach ($heatmap as $dept => $types) {
            $deptRatio[] = [
                'dept'           => $dept,
                'reconhecimento' => $types['reconhecimento'] ?? 0,
                'sugestao'       => $types['sugestao']       ?? 0,
                'alerta'         => $types['alerta']         ?? 0,
            ];
        }
        usort($deptRatio, fn ($a, $b) => ($b['alerta'] + $b['sugestao'] + $b['reconhecimento']) <=> ($a['alerta'] + $a['sugestao'] + $a['reconhecimento']));

        // ── Radar de risco por funcionário ───────────────────────────
        $riskEmployees = (clone $base)
            ->where('type', 'alerta')
            ->selectRaw("employee_id, COUNT(*) as total,
                SUM(CASE severity WHEN 'critico' THEN 4 WHEN 'alto' THEN 3 WHEN 'medio' THEN 2 ELSE 1 END) as risk_score")
            ->groupBy('employee_id')
            ->orderByDesc('risk_score')
            ->limit(8)
            ->with('employee:id,name')
            ->get()
            ->map(fn ($r) => [
                'name'       => $r->employee?->name ?? '—',
                'total'      => (int) $r->total,
                'risk_score' => (int) $r->risk_score,
            ])
            ->toArray();

        // ── Funil de resolução ───────────────────────────────────────
        $funnel = [
            ['label' => 'Aberto',           'value' => (int) ($byStatus['aberto']             ?? 0), 'color' => '#3b82f6'],
            ['label' => 'Em análise',        'value' => (int) ($byStatus['em_analise']         ?? 0), 'color' => '#f59e0b'],
            ['label' => 'Aguard. plano',     'value' => (int) ($byStatus['aguardando_plano']   ?? 0), 'color' => '#f97316'],
            ['label' => 'Plano andamento',   'value' => (int) ($byStatus['plano_em_andamento'] ?? 0), 'color' => '#8b5cf6'],
            ['label' => 'Resolvido',         'value' => (int) ($byStatus['resolvido']          ?? 0), 'color' => '#10b981'],
        ];

        // ── Planos de ação ───────────────────────────────────────────
        $withPlan    = (clone $base)->whereNotNull('action_plan_deadline')->count();
        $overdue     = (clone $base)
            ->whereNotNull('action_plan_deadline')
            ->where('action_plan_deadline', '<', now()->toDateString())
            ->whereNotIn('status', ['resolvido', 'arquivado'])
            ->count();

        // ── Uso de templates ─────────────────────────────────────────
        $templateLabels = [
            'livre'       => 'Livre',
            'padrao'      => 'Padrão',
            'sci'         => 'SCI',
            'pcc'         => 'Para/Começa/Continua',
            'star'        => 'STAR',
            'feedforward' => 'Feedforward',
        ];
        $templateRaw = (clone $base)->selectRaw("template, COUNT(*) as cnt")->groupBy('template')->pluck('cnt', 'template');
        $templateUsage = [
            'labels' => collect($templateRaw)->keys()->map(fn ($k) => $templateLabels[$k] ?? ucfirst($k))->toArray(),
            'data'   => collect($templateRaw)->values()->map(fn ($v) => (int) $v)->toArray(),
        ];

        // ── Top 5 funcionários com mais alertas ──────────────────────
        $topEmployees = (clone $base)
            ->where('type', 'alerta')
            ->selectRaw("employee_id, COUNT(*) as total")
            ->groupBy('employee_id')
            ->orderByDesc('total')
            ->limit(5)
            ->with('employee:id,name')
            ->get()
            ->map(fn ($r) => ['name' => $r->employee?->name ?? '—', 'total' => $r->total])
            ->toArray();

        $catLabels = [
            'comportamento'      => 'Comportamento',
            'desempenho'         => 'Desempenho',
            'pontualidade'       => 'Pontualidade',
            'trabalho_em_equipe' => 'Trabalho em equipe',
            'comunicacao'        => 'Comunicação',
            'lideranca'          => 'Liderança',
            'outros'             => 'Outros',
        ];

        return [
            'by_type' => [
                'labels' => ['Reconhecimento', 'Sugestão', 'Alerta'],
                'data'   => [
                    (int) ($byType['reconhecimento'] ?? 0),
                    (int) ($byType['sugestao'] ?? 0),
                    (int) ($byType['alerta'] ?? 0),
                ],
            ],
            'by_category' => [
                'labels' => collect($byCategory)->keys()->map(fn ($k) => $catLabels[$k] ?? ucfirst($k))->toArray(),
                'data'   => collect($byCategory)->values()->map(fn ($v) => (int) $v)->toArray(),
            ],
            'by_severity' => [
                'labels' => ['Baixo', 'Médio', 'Alto', 'Crítico'],
                'data'   => [
                    (int) ($bySeverity['baixo']   ?? 0),
                    (int) ($bySeverity['medio']   ?? 0),
                    (int) ($bySeverity['alto']    ?? 0),
                    (int) ($bySeverity['critico'] ?? 0),
                ],
            ],
            'by_status' => [
                'labels' => ['Aberto', 'Em análise', 'Aguard. plano', 'Plano andamento', 'Resolvido', 'Arquivado'],
                'data'   => [
                    (int) ($byStatus['aberto']             ?? 0),
                    (int) ($byStatus['em_analise']         ?? 0),
                    (int) ($byStatus['aguardando_plano']   ?? 0),
                    (int) ($byStatus['plano_em_andamento'] ?? 0),
                    (int) ($byStatus['resolvido']          ?? 0),
                    (int) ($byStatus['arquivado']          ?? 0),
                ],
            ],
            'monthly'        => ['labels' => $monthLabels, 'data' => $monthData],
            'trend'          => $trend,
            'heatmap'        => $heatmap,
            'dept_ratio'     => $deptRatio,
            'risk_employees' => $riskEmployees,
            'funnel'         => $funnel,
            'action_plans'   => ['total' => $withPlan, 'overdue' => $overdue, 'on_time' => max(0, $withPlan - $overdue)],
            'template_usage' => $templateUsage,
            'top_employees'  => $topEmployees,
        ];
    }

    // ── Alertas recorrentes (mesmo funcionário + categoria, 3+ em 90 dias) ──
    #[Computed]
    public function recurrentAlerts(): array
    {
        return $this->baseQuery()
            ->where('type', 'alerta')
            ->where('occurred_at', '>=', now()->subDays(90))
            ->selectRaw("employee_id, category, COUNT(*) as cnt")
            ->groupBy('employee_id', 'category')
            ->havingRaw('COUNT(*) >= 3')
            ->get()
            ->map(fn ($r) => $r->employee_id . '-' . $r->category)
            ->toArray();
    }

    // ── Funcionários avaliáveis ───────────────────────────────────────
    #[Computed]
    public function employees()
    {
        $user = Auth::user();

        return User::with('department')
            ->when($user->isGerente(), fn ($q) => $q->where('department_id', $user->department_id))
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    // ── Mapa departamento → gerente ───────────────────────────────────
    #[Computed]
    public function departmentManagers(): array
    {
        return User::whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
            ->where('is_active', true)
            ->get(['id', 'name', 'department_id'])
            ->keyBy('department_id')
            ->map(fn ($m) => ['id' => $m->id, 'name' => $m->name])
            ->toArray();
    }

    // ── Feedback em visualização ──────────────────────────────────────
    #[Computed]
    public function viewing(): ?Feedback
    {
        if (! $this->viewingId) {
            return null;
        }

        return Feedback::with([
            'employee.department',
            'evaluator',
            'actionPlanResponsible',
            'statusHistory.changedBy',
            'comments.user',
            'actionTasks.responsible',
            'actionTasks.completedBy',
        ])->find($this->viewingId);
    }

    // ─────────────────────────────────────────────────────────────────
    // Ações CRUD
    // ─────────────────────────────────────────────────────────────────

    public function abrirModal(): void
    {
        $this->resetForm();
        $this->editingId = null;
        $this->modalOpen = true;
    }

    public function ver(int $id): void
    {
        $fb = Feedback::findOrFail($id);
        $this->authorizeAccess($fb);
        $this->viewingId  = $id;
        $this->newComment = '';
        $this->viewModal  = true;
    }

    public function fecharView(): void
    {
        $this->viewModal  = false;
        $this->viewingId  = null;
        $this->newComment = '';
        unset($this->viewing);
    }

    public function editar(int $id): void
    {
        $fb = Feedback::findOrFail($id);
        $this->authorizeAccess($fb);

        $this->editingId                  = $id;
        $this->employee_id                = $fb->employee_id;
        $this->type                       = $fb->type;
        $this->category                   = $fb->category;
        $this->severity                   = $fb->severity ?? 'baixo';
        $this->rating                     = $fb->rating;
        $this->occurred_at                = $fb->occurred_at->format('Y-m-d');
        $this->message                    = $fb->message;
        $this->template                   = $fb->template ?? 'livre';
        $this->templateData               = $fb->template_data ?? [];
        $this->privateNote                = $fb->private_note ?? '';
        $this->action_plan                = $fb->action_plan ?? '';
        $this->action_plan_deadline       = $fb->action_plan_deadline?->format('Y-m-d') ?? '';
        $this->action_plan_responsible_id = $fb->action_plan_responsible_id;
        $this->status                     = $fb->status;
        $this->is_anonymous               = $fb->is_anonymous;
        $this->attachment                 = null;

        // Carrega tarefas existentes no buffer do formulário
        $this->actionTasks = $fb->actionTasks()
            ->with('responsible')
            ->get()
            ->map(fn ($t) => [
                'id'               => $t->id,
                'description'      => $t->description,
                'responsible_id'   => $t->responsible_id,
                'responsible_name' => $t->responsible?->name ?? '',
                'due_date'         => $t->due_date?->format('Y-m-d') ?? '',
                'phase'            => $t->phase ?? '',
                'resources'        => $t->resources ?? '',
                'completed'        => $t->completed,
            ])
            ->toArray();

        $this->modalOpen = true;
    }

    // ── Definições dos templates ──────────────────────────────────────
    public static function templateDefinitions(): array
    {
        return [
            'livre' => [
                'label'       => 'Livre',
                'description' => 'Texto livre sem estrutura',
                'icon'        => 'edit-3',
                'color'       => 'slate',
                'fields'      => [
                    'feedback' => ['label' => 'Feedback', 'placeholder' => 'Escreva seu feedback livremente...', 'required' => true],
                ],
            ],
            'padrao' => [
                'label'       => 'Padrão',
                'description' => 'Positivos, melhorias e comentários',
                'icon'        => 'layout-template',
                'color'       => 'violet',
                'fields'      => [
                    'pontos_positivos'   => ['label' => 'Pontos positivos',  'placeholder' => 'O que o colaborador faz muito bem...', 'required' => false],
                    'pontos_melhorar'    => ['label' => 'Pontos a melhorar', 'placeholder' => 'Onde há espaço para crescimento...', 'required' => false],
                    'comentarios_gerais' => ['label' => 'Comentários gerais','placeholder' => 'Observações e contexto adicional...', 'required' => false],
                ],
            ],
            'sci' => [
                'label'       => 'SCI',
                'description' => 'Situação, Comportamento e Impacto',
                'icon'        => 'git-branch',
                'color'       => 'blue',
                'fields'      => [
                    'situacao'      => ['label' => 'Situação',      'placeholder' => 'Em qual situação/contexto ocorreu...', 'required' => true],
                    'comportamento' => ['label' => 'Comportamento', 'placeholder' => 'Qual comportamento foi observado...', 'required' => true],
                    'impacto'       => ['label' => 'Impacto',       'placeholder' => 'Qual foi o impacto no time/resultado...', 'required' => true],
                ],
            ],
            'pcc' => [
                'label'       => 'Para/Começa/Continua',
                'description' => 'Parar, começar e continuar',
                'icon'        => 'repeat',
                'color'       => 'emerald',
                'fields'      => [
                    'parar'     => ['label' => 'Parar de fazer',    'placeholder' => 'Comportamentos que devem ser encerrados...', 'required' => false],
                    'comecar'   => ['label' => 'Começar a fazer',   'placeholder' => 'Novas práticas que devem ser adotadas...', 'required' => false],
                    'continuar' => ['label' => 'Continuar fazendo', 'placeholder' => 'O que está funcionando e deve seguir...', 'required' => false],
                ],
            ],
            'star' => [
                'label'       => 'STAR',
                'description' => 'Situação, Tarefa, Ação e Resultado',
                'icon'        => 'star',
                'color'       => 'amber',
                'fields'      => [
                    'situacao'  => ['label' => 'Situação',  'placeholder' => 'Qual era o contexto ou problema...', 'required' => true],
                    'tarefa'    => ['label' => 'Tarefa',    'placeholder' => 'Qual era o objetivo ou desafio...', 'required' => true],
                    'acao'      => ['label' => 'Ação',      'placeholder' => 'O que foi feito especificamente...', 'required' => true],
                    'resultado' => ['label' => 'Resultado', 'placeholder' => 'Qual foi o resultado alcançado...', 'required' => true],
                ],
            ],
            'feedforward' => [
                'label'       => 'Feedforward',
                'description' => 'Foco no desenvolvimento futuro',
                'icon'        => 'trending-up',
                'color'       => 'rose',
                'fields'      => [
                    'contexto'        => ['label' => 'Contexto atual',         'placeholder' => 'Como está a situação hoje...', 'required' => false],
                    'sugestao_futura' => ['label' => 'Sugestão para o futuro', 'placeholder' => 'O que pode fazer diferente ou melhor daqui pra frente...', 'required' => true],
                ],
            ],
        ];
    }

    // Compila o campo `message` a partir dos dados do template (para preview nos cards)
    private function compileMessage(): string
    {
        if ($this->template === 'livre') {
            return $this->templateData['feedback'] ?? $this->message;
        }

        $defs  = self::templateDefinitions();
        $tpl   = $defs[$this->template] ?? null;
        $parts = [];

        if ($tpl) {
            foreach ($tpl['fields'] as $key => $field) {
                $val = trim($this->templateData[$key] ?? '');
                if ($val !== '') {
                    $parts[] = "**{$field['label']}**: {$val}";
                }
            }
        }

        return implode("\n\n", $parts) ?: $this->message;
    }

    public function salvar(): void
    {
        $defs = self::templateDefinitions();
        $tpl  = $defs[$this->template] ?? $defs['livre'];

        $rules = [
            'employee_id'                => 'required|exists:users,id',
            'type'                       => 'required|in:reconhecimento,sugestao,alerta',
            'category'                   => 'required|in:comportamento,desempenho,pontualidade,trabalho_em_equipe,comunicacao,lideranca,outros',
            'occurred_at'                => 'required|date',
            'action_plan'                => 'nullable|max:5000',
            'action_plan_deadline'       => 'nullable|date',
            'action_plan_responsible_id' => 'nullable|exists:users,id',
            'status'                     => 'required|in:aberto,em_analise,aguardando_plano,plano_em_andamento,resolvido,arquivado',
            'rating'                     => 'nullable|integer|min:1|max:5',
            'is_anonymous'               => 'boolean',
            'attachment'                 => 'nullable|file|max:10240',
            'privateNote'                => 'nullable|max:5000',
        ];

        // Validação dos campos do template
        foreach ($tpl['fields'] as $key => $field) {
            $rules["templateData.{$key}"] = $field['required']
                ? 'required|min:5|max:5000'
                : 'nullable|max:5000';
        }

        $this->validate($rules, [
            'templateData.feedback.required'        => 'O campo Feedback é obrigatório.',
            'templateData.feedback.min'             => 'O feedback deve ter pelo menos 5 caracteres.',
            'templateData.situacao.required'        => 'O campo Situação é obrigatório.',
            'templateData.situacao.min'             => 'A Situação deve ter pelo menos 5 caracteres.',
            'templateData.comportamento.required'   => 'O campo Comportamento é obrigatório.',
            'templateData.impacto.required'         => 'O campo Impacto é obrigatório.',
            'templateData.tarefa.required'          => 'O campo Tarefa é obrigatório.',
            'templateData.acao.required'            => 'O campo Ação é obrigatório.',
            'templateData.resultado.required'       => 'O campo Resultado é obrigatório.',
            'templateData.sugestao_futura.required' => 'O campo Sugestão para o futuro é obrigatório.',
            'templateData.sugestao_futura.min'      => 'A Sugestão deve ter pelo menos 5 caracteres.',
        ]);

        $attachments = [];

        if ($this->attachment) {
            $path        = $this->attachment->store('feedbacks/attachments', 'public');
            $attachments = [[
                'name' => $this->attachment->getClientOriginalName(),
                'path' => $path,
                'size' => $this->attachment->getSize(),
            ]];
        }

        // Salva template_data limpo (sem campos vazios)
        $cleanTemplateData = [];
        foreach ($tpl['fields'] as $key => $field) {
            $val = trim($this->templateData[$key] ?? '');
            if ($val !== '') {
                $cleanTemplateData[$key] = $this->sanitize($val);
            }
        }

        $data = [
            'employee_id'                => $this->employee_id,
            'evaluator_id'               => $this->is_anonymous ? null : Auth::id(),
            'type'                       => $this->type,
            'category'                   => $this->category,
            'severity'                   => $this->type === 'alerta' ? $this->severity : null,
            'rating'                     => $this->rating ?: null,
            'occurred_at'                => $this->occurred_at,
            'message'                    => $this->sanitize($this->compileMessage()),
            'template'                   => $this->template,
            'template_data'              => $cleanTemplateData ?: null,
            'private_note'               => $this->sanitize($this->privateNote) ?: null,
            'action_plan'                => $this->sanitize($this->action_plan) ?: null,
            'action_plan_deadline'       => $this->action_plan_deadline ?: null,
            'action_plan_responsible_id' => $this->action_plan_responsible_id ?: null,
            'status'                     => $this->status,
            'is_anonymous'               => $this->is_anonymous,
        ];

        if ($this->editingId) {
            $fb = Feedback::findOrFail($this->editingId);
            $this->authorizeAccess($fb);

            if (! empty($attachments)) {
                $data['attachments'] = array_merge($fb->attachments ?? [], $attachments);
            }

            // Registra histórico se status mudou
            if ($fb->status !== $data['status']) {
                FeedbackStatusHistory::create([
                    'feedback_id' => $fb->id,
                    'changed_by'  => Auth::id(),
                    'old_status'  => $fb->status,
                    'new_status'  => $data['status'],
                ]);
            }

            $fb->update($data);
            $this->syncActionTasks($fb);
            $this->alertSuccess('Feedback atualizado com sucesso.');
        } else {
            $data['attachments'] = $attachments ?: null;
            $fb = Feedback::create($data);

            // Registra criação no histórico
            FeedbackStatusHistory::create([
                'feedback_id' => $fb->id,
                'changed_by'  => $this->is_anonymous ? null : Auth::id(),
                'old_status'  => null,
                'new_status'  => $data['status'],
            ]);

            $this->syncActionTasks($fb);
            $this->alertSuccess('Feedback registrado com sucesso.');
        }

        $this->fecharModal();
        unset($this->feedbacks, $this->stats, $this->recurrentAlerts);
    }

    // ── Atualizar status com histórico automático ─────────────────────
    public function atualizarStatus(int $id, string $status): void
    {
        $allowed = ['aberto', 'em_analise', 'aguardando_plano', 'plano_em_andamento', 'resolvido', 'arquivado'];

        if (! in_array($status, $allowed, true)) {
            return;
        }

        $fb = Feedback::findOrFail($id);
        $this->authorizeAccess($fb);

        if ($fb->status === $status) {
            return;
        }

        FeedbackStatusHistory::create([
            'feedback_id' => $fb->id,
            'changed_by'  => Auth::id(),
            'old_status'  => $fb->status,
            'new_status'  => $status,
        ]);

        $fb->update(['status' => $status]);
        unset($this->feedbacks, $this->stats, $this->viewing);

        $this->alertSuccess('Status atualizado.');
    }

    // ── Comentários ───────────────────────────────────────────────────
    public function addComment(int $feedbackId): void
    {
        $this->validate(['newComment' => 'required|min:2|max:1000']);

        $fb = Feedback::findOrFail($feedbackId);
        $this->authorizeAccess($fb);

        FeedbackComment::create([
            'feedback_id' => $feedbackId,
            'user_id'     => Auth::id(),
            'comment'     => $this->sanitize($this->newComment),
        ]);

        $this->newComment = '';
        unset($this->viewing);
    }

    public function deleteComment(int $commentId): void
    {
        $comment = FeedbackComment::findOrFail($commentId);

        // Só o autor pode excluir seu próprio comentário
        if ($comment->user_id !== Auth::id() && ! Auth::user()->isRhOuDp()) {
            abort(403);
        }

        $comment->delete();
        unset($this->viewing);
    }

    // ── Tarefas do plano de ação ──────────────────────────────────────

    public function adicionarTarefa(): void
    {
        $this->validate([
            'newTaskDesc' => 'required|min:3|max:500',
            'newTaskDue'  => 'nullable|date',
        ], [
            'newTaskDesc.required' => 'Descreva a tarefa antes de adicionar.',
            'newTaskDesc.min'      => 'A descrição deve ter pelo menos 3 caracteres.',
        ]);

        $responsibleName = '';
        if ($this->newTaskResponsibleId) {
            $u = $this->employees->firstWhere('id', $this->newTaskResponsibleId);
            $responsibleName = $u?->name ?? '';
        }

        $this->actionTasks[] = [
            'id'               => null,
            'description'      => $this->sanitize($this->newTaskDesc),
            'responsible_id'   => $this->newTaskResponsibleId,
            'responsible_name' => $responsibleName,
            'due_date'         => $this->newTaskDue,
            'phase'            => $this->newTaskPhase,
            'resources'        => $this->sanitize($this->newTaskResources),
            'completed'        => false,
        ];

        $this->newTaskDesc          = '';
        $this->newTaskDue           = '';
        $this->newTaskPhase         = '';
        $this->newTaskResources     = '';
        $this->newTaskResponsibleId = null;
        $this->resetErrorBag(['newTaskDesc', 'newTaskDue']);
    }

    public function removerTarefa(int $index): void
    {
        array_splice($this->actionTasks, $index, 1);
    }

    public function toggleTask(int $taskId): void
    {
        $task = FeedbackActionTask::findOrFail($taskId);
        $this->authorizeAccess($task->feedback);

        $completed = ! $task->completed;

        $task->update([
            'completed'    => $completed,
            'completed_at' => $completed ? now() : null,
            'completed_by' => $completed ? Auth::id() : null,
        ]);

        unset($this->viewing);

        // Se todas as tarefas estiverem concluídas, sugere atualizar status
        $fb = Feedback::find($task->feedback_id);
        if ($fb && $fb->actionTasks()->where('completed', false)->count() === 0 && $fb->actionTasks()->count() > 0) {
            $this->dispatch('all-tasks-done', feedbackId: $fb->id);
        }
    }

    // Sincroniza o buffer $actionTasks com o banco para um dado feedback
    private function syncActionTasks(Feedback $fb): void
    {
        $existingIds = collect($this->actionTasks)
            ->filter(fn ($t) => ! empty($t['id']))
            ->pluck('id')
            ->toArray();

        // Remove tarefas que foram deletadas no formulário
        $fb->actionTasks()->whereNotIn('id', $existingIds)->delete();

        foreach ($this->actionTasks as $task) {
            if (! empty($task['id'])) {
                // Atualiza existente (só campos editáveis — não toca em completed)
                FeedbackActionTask::where('id', $task['id'])->update([
                    'description'    => $task['description'],
                    'responsible_id' => $task['responsible_id'] ?: null,
                    'due_date'       => $task['due_date'] ?: null,
                    'phase'          => $task['phase'] ?: null,
                    'resources'      => $task['resources'] ?: null,
                ]);
            } else {
                // Cria nova tarefa
                FeedbackActionTask::create([
                    'feedback_id'    => $fb->id,
                    'description'    => $task['description'],
                    'responsible_id' => $task['responsible_id'] ?: null,
                    'due_date'       => $task['due_date'] ?: null,
                    'phase'          => $task['phase'] ?: null,
                    'resources'      => $task['resources'] ?: null,
                    'completed'      => false,
                    'created_by'     => Auth::id(),
                ]);
            }
        }
    }

    // ── Exclusão ──────────────────────────────────────────────────────
    public function confirmarExclusao(int $id): void
    {
        $this->deletingId    = $id;
        $this->confirmDelete = true;
    }

    public function excluir(): void
    {
        if (! $this->deletingId) {
            return;
        }

        $fb = Feedback::findOrFail($this->deletingId);
        $this->authorizeAccess($fb);
        $fb->delete();

        $this->confirmDelete = false;
        $this->deletingId    = null;
        unset($this->feedbacks, $this->stats, $this->recurrentAlerts);
        $this->alertSuccess('Feedback excluído.');
    }

    public function cancelarExclusao(): void
    {
        $this->confirmDelete = false;
        $this->deletingId    = null;
    }

    public function limparFiltros(): void
    {
        $this->search           = '';
        $this->typeFilter       = '';
        $this->statusFilter     = '';
        $this->categoryFilter   = '';
        $this->severityFilter   = '';
        $this->periodFilter     = '';
        $this->actionPlanFilter = '';
    }

    public function fecharModal(): void
    {
        $this->modalOpen = false;
        $this->editingId = null;
        $this->resetForm();
    }

    // ─────────────────────────────────────────────────────────────────
    // Helpers
    // ─────────────────────────────────────────────────────────────────

    private function authorizeAccess(Feedback $fb): void
    {
        $user = Auth::user();

        if ($user->isGerente()) {
            $employee = $fb->employee ?? User::find($fb->employee_id);
            if ($employee?->department_id !== $user->department_id) {
                abort(403, 'Sem permissão para acessar este feedback.');
            }
        }
    }

    private function resetForm(): void
    {
        $this->employee_id                = null;
        $this->type                       = 'alerta';
        $this->category                   = 'desempenho';
        $this->severity                   = 'baixo';
        $this->rating                     = null;
        $this->occurred_at                = now()->format('Y-m-d');
        $this->message                    = '';
        $this->template                   = 'livre';
        $this->templateData               = [];
        $this->privateNote                = '';
        $this->action_plan                = '';
        $this->action_plan_deadline       = '';
        $this->action_plan_responsible_id = null;
        $this->status                     = 'aberto';
        $this->is_anonymous               = false;
        $this->attachment                 = null;
        $this->actionTasks                = [];
        $this->newTaskDesc                = '';
        $this->newTaskDue                 = '';
        $this->newTaskPhase               = '';
        $this->newTaskResources           = '';
        $this->newTaskResponsibleId       = null;
        $this->resetErrorBag();
    }

    // ─────────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.feedback.index', [
            'templateDefs'       => self::templateDefinitions(),
            'departmentManagers' => $this->departmentManagers,
        ]);
    }
}
