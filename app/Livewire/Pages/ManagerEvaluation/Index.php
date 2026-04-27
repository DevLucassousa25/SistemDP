<?php

namespace App\Livewire\Pages\ManagerEvaluation;

use App\Livewire\SecureComponent;
use App\Models\EvaluationCriterion;
use App\Models\EvaluationCycle;
use App\Models\ManagerEvaluation;
use App\Models\ManagerEvaluationEntry;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class Index extends SecureComponent
{
    // ── UI State ────────────────────────────────────────────────────────
    public string $activeTab = 'dashboard';

    // ── Gerente: Avaliação ──────────────────────────────────────────────
    public ?int   $selectedEmployeeId = null;
    public array  $scores             = [];        // [criterionId => score]
    public array  $entryComments      = [];        // [criterionId => comment]
    public string $employeeSearch     = '';

    // ── Gerente: Histórico ──────────────────────────────────────────────
    public ?int $historyYearFilter  = null;
    public ?int $historyCycleFilter = null;
    public ?int $expandedHistoryId  = null;

    // ── Admin: Ciclo form ───────────────────────────────────────────────
    public bool   $cycleModal       = false;
    #[Locked] public ?int $editingCycleId = null;
    public string $cycleName        = '';
    public string $cycleDescription = '';
    public string $cycleStart       = '';
    public string $cycleEnd         = '';

    // ── Admin: Critério form ────────────────────────────────────────────
    public bool   $criterionModal        = false;
    #[Locked] public ?int $editingCriterionId = null;
    public string $criterionName         = '';
    public string $criterionDescription  = '';

    // ── Confirmação ─────────────────────────────────────────────────────
    public bool   $confirmModal  = false;
    public string $confirmType   = '';   // activate_cycle|close_cycle|delete_cycle|complete_evaluation
    #[Locked] public ?int $confirmId = null;

    // ───────────────────────────────────────────────────────────────────
    // MOUNT
    // ───────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->requireAuth();

        if (! auth()->user()->podeGerenciarPesquisas()) {
            abort(403, 'Acesso restrito a Gerentes, RH e Administradores.');
        }

        $this->activeTab  = auth()->user()->isGerente() ? 'dashboard' : 'ciclos';
        $this->cycleStart = now()->format('Y-m-d');
        $this->cycleEnd   = now()->addMonth()->format('Y-m-d');
    }

    // ───────────────────────────────────────────────────────────────────
    // COMPUTED — COMPARTILHADO
    // ───────────────────────────────────────────────────────────────────

    #[Computed]
    public function activeCycle(): ?EvaluationCycle
    {
        return EvaluationCycle::where('status', 'active')->first();
    }

    #[Computed]
    public function criteria(): Collection
    {
        return EvaluationCriterion::where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function allCriteria(): Collection
    {
        return EvaluationCriterion::orderBy('order')->orderBy('name')->get();
    }

    // ───────────────────────────────────────────────────────────────────
    // COMPUTED — GERENTE
    // ───────────────────────────────────────────────────────────────────

    #[Computed]
    public function myEvaluation(): ?ManagerEvaluation
    {
        if (! auth()->user()->isGerente() || ! $this->activeCycle) {
            return null;
        }

        return ManagerEvaluation::firstOrCreate(
            [
                'evaluation_cycle_id' => $this->activeCycle->id,
                'manager_id'          => auth()->id(),
            ],
            ['status' => 'not_started']
        );
    }

    #[Computed]
    public function allDeptEmployees(): Collection
    {
        if (! auth()->user()->isGerente()) {
            return collect();
        }

        return User::where('department_id', auth()->user()->department_id)
            ->where('id', '!=', auth()->id())
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function employees(): Collection
    {
        if (! auth()->user()->isGerente()) {
            return collect();
        }

        return User::where('department_id', auth()->user()->department_id)
            ->where('id', '!=', auth()->id())
            ->where('is_active', true)
            ->when($this->employeeSearch, fn ($q) => $q->where('name', 'like', '%' . $this->employeeSearch . '%'))
            ->orderBy('name')
            ->get();
    }

    #[Computed]
    public function completedEmployeeIds(): array
    {
        if (! $this->myEvaluation) {
            return [];
        }

        $criteriaCount = $this->criteria->count();
        if ($criteriaCount === 0) {
            return [];
        }

        return ManagerEvaluationEntry::where('manager_evaluation_id', $this->myEvaluation->id)
            ->selectRaw('employee_id, COUNT(*) as cnt')
            ->groupBy('employee_id')
            ->having('cnt', '>=', $criteriaCount)
            ->pluck('employee_id')
            ->toArray();
    }

    #[Computed]
    public function evaluationProgress(): array
    {
        $total     = $this->allDeptEmployees->count();
        $evaluated = count($this->completedEmployeeIds);
        $pct       = $total > 0 ? (int) round(($evaluated / $total) * 100) : 0;

        return compact('total', 'evaluated', 'pct');
    }

    #[Computed]
    public function satisfactionScore(): ?float
    {
        $user = auth()->user();
        if (! $user->department_id) {
            return null;
        }

        try {
            $avg = DB::table('survey_answers')
                ->join('survey_questions', 'survey_questions.id', '=', 'survey_answers.question_id')
                ->join('surveys', 'surveys.id', '=', 'survey_questions.survey_id')
                ->join('survey_responses', 'survey_responses.id', '=', 'survey_answers.response_id')
                ->whereNotNull('survey_responses.completed_at')
                ->where('survey_questions.type', 'escala')
                ->whereNotNull('survey_answers.value_scale')
                ->whereJsonContains('surveys.target_department_ids', $user->department_id)
                ->avg('survey_answers.value_scale');

            return $avg ? round((float) $avg, 1) : null;
        } catch (\Throwable) {
            return null;
        }
    }

    /**
     * Resultados das perguntas de avaliação do gestor (is_manager_evaluation = true)
     * em pesquisas voltadas ao departamento do gerente autenticado.
     *
     * Retorna uma Collection de objetos com:
     *   question_id, question, type, avg_scale, response_count, comments
     */
    #[Computed]
    public function managerSurveyResults(): Collection
    {
        $user = auth()->user();
        if (! $user->isGerente() || ! $user->department_id) {
            return collect();
        }

        try {
            $rows = DB::table('survey_answers as sa')
                ->join('survey_questions as sq', 'sq.id', '=', 'sa.question_id')
                ->join('surveys as s', 's.id', '=', 'sq.survey_id')
                ->join('survey_responses as sr', 'sr.id', '=', 'sa.response_id')
                ->where('sq.is_manager_evaluation', true)
                ->whereNotNull('sr.completed_at')
                ->where(function ($q) use ($user) {
                    $q->where('s.target_audience', 'todos')
                      ->orWhereJsonContains('s.target_department_ids', $user->department_id);
                })
                ->select([
                    'sq.id as question_id',
                    'sq.question',
                    'sq.type',
                    DB::raw('ROUND(AVG(sa.value_scale)::numeric, 1) as avg_scale'),
                    DB::raw('COUNT(sa.id) as response_count'),
                ])
                ->groupBy('sq.id', 'sq.question', 'sq.type')
                ->orderBy('sq.id')
                ->get();

            return $rows->map(function ($row) {
                $result = (object) [
                    'question_id'    => $row->question_id,
                    'question'       => $row->question,
                    'type'           => $row->type,
                    'avg_scale'      => $row->avg_scale !== null ? (float) $row->avg_scale : null,
                    'response_count' => (int) $row->response_count,
                    'comments'       => collect(),
                ];

                if (in_array($row->type, ['texto_livre', 'multipla_escolha'])) {
                    $result->comments = DB::table('survey_answers as sa')
                        ->join('survey_responses as sr', 'sr.id', '=', 'sa.response_id')
                        ->where('sa.question_id', $row->question_id)
                        ->whereNotNull('sr.completed_at')
                        ->whereNotNull('sa.value_text')
                        ->where('sa.value_text', '!=', '')
                        ->orderByDesc('sr.completed_at')
                        ->limit(20)
                        ->pluck('sa.value_text');
                }

                return $result;
            });
        } catch (\Throwable) {
            return collect();
        }
    }

    /**
     * Estatísticas consolidadas dos resultados de avaliação do gestor via pesquisas:
     * média geral (escala), quantidade de respostas (escala), e resumo por pergunta.
     */
    #[Computed]
    public function managerSurveyStats(): array
    {
        $results = $this->managerSurveyResults;

        $scaleResults = $results->where('type', 'escala')->filter(fn ($r) => $r->avg_scale !== null);

        if ($scaleResults->isEmpty()) {
            return ['count' => 0, 'avg' => null, 'by_question' => []];
        }

        $totalResponses = $scaleResults->sum('response_count');
        $weightedSum    = $scaleResults->sum(fn ($r) => $r->avg_scale * $r->response_count);
        $avgGeneral     = $totalResponses > 0 ? round($weightedSum / $totalResponses, 1) : null;

        $byQuestion = $scaleResults->mapWithKeys(fn ($r) => [
            $r->question_id => $r->avg_scale,
        ])->all();

        return [
            'count'       => (int) $totalResponses,
            'avg'         => $avgGeneral,
            'by_question' => $byQuestion,
        ];
    }

    // ── Histórico ──────────────────────────────────────────────────────

    #[Computed]
    public function historyEvaluations(): Collection
    {
        if (! auth()->user()->isGerente()) {
            return collect();
        }

        return ManagerEvaluation::with(['cycle', 'entries.employee', 'entries.criterion'])
            ->where('manager_id', auth()->id())
            ->where('status', 'completed')
            ->when($this->historyYearFilter, fn ($q) => $q->whereHas('cycle',
                fn ($q2) => $q2->whereYear('start_date', $this->historyYearFilter)
            ))
            ->when($this->historyCycleFilter, fn ($q) => $q->where('evaluation_cycle_id', $this->historyCycleFilter))
            ->orderByDesc('completed_at')
            ->get();
    }

    #[Computed]
    public function availableYears(): array
    {
        return EvaluationCycle::selectRaw('EXTRACT(YEAR FROM start_date)::int as year')
            ->distinct()
            ->orderByDesc('year')
            ->pluck('year')
            ->toArray();
    }

    #[Computed]
    public function allCycles(): Collection
    {
        return EvaluationCycle::orderByDesc('start_date')->get();
    }

    // ───────────────────────────────────────────────────────────────────
    // COMPUTED — ADMIN / RH
    // ───────────────────────────────────────────────────────────────────

    #[Computed]
    public function cycles(): Collection
    {
        return EvaluationCycle::with('creator')
            ->withCount('managerEvaluations')
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function ranking(): Collection
    {
        $managers = User::with('department')
            ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
            ->where('is_active', true)
            ->get();

        // IDs do ciclo ativo para filtrar avaliações de funcionários
        $activeCycleId = $this->activeCycle?->id;

        return $managers->map(function (User $manager) use ($activeCycleId) {
            // Média das avaliações realizadas (avaliações do gerente aos colaboradores)
            $avgScore = DB::table('manager_evaluation_entries')
                ->join('manager_evaluations', 'manager_evaluations.id', '=', 'manager_evaluation_entries.manager_evaluation_id')
                ->where('manager_evaluations.manager_id', $manager->id)
                ->where('manager_evaluations.status', 'completed')
                ->avg('score');

            // Nota de satisfação vinda das pesquisas do departamento
            $survScore = null;
            if ($manager->department_id) {
                try {
                    $raw = DB::table('survey_answers')
                        ->join('survey_questions', 'survey_questions.id', '=', 'survey_answers.question_id')
                        ->join('surveys', 'surveys.id', '=', 'survey_questions.survey_id')
                        ->join('survey_responses', 'survey_responses.id', '=', 'survey_answers.response_id')
                        ->whereNotNull('survey_responses.completed_at')
                        ->where('survey_questions.type', 'escala')
                        ->whereNotNull('survey_answers.value_scale')
                        ->whereJsonContains('surveys.target_department_ids', $manager->department_id)
                        ->avg('survey_answers.value_scale');

                    $survScore = $raw ? round((float) $raw, 1) : null;
                } catch (\Throwable) {
                    $survScore = null;
                }
            }

            // Nota do gestor via perguntas is_manager_evaluation nas pesquisas (escala 0–10)
            $managerEvalScore = null;
            $managerEvalCount = 0;
            if ($manager->department_id) {
                try {
                    $meQuery = DB::table('survey_answers as sa')
                        ->join('survey_questions as sq', 'sq.id', '=', 'sa.question_id')
                        ->join('surveys as s', 's.id', '=', 'sq.survey_id')
                        ->join('survey_responses as sr', 'sr.id', '=', 'sa.response_id')
                        ->where('sq.is_manager_evaluation', true)
                        ->where('sq.type', 'escala')
                        ->whereNotNull('sa.value_scale')
                        ->whereNotNull('sr.completed_at')
                        ->where(function ($q) use ($manager) {
                            $q->where('s.target_audience', 'todos')
                              ->orWhereJsonContains('s.target_department_ids', $manager->department_id);
                        });

                    $managerEvalCount = $meQuery->count();
                    $meAvg            = $meQuery->avg('sa.value_scale');
                    $managerEvalScore = $meAvg ? round((float) $meAvg, 1) : null;
                } catch (\Throwable) {
                    // ignore
                }
            }

            $cyclesCompleted = ManagerEvaluation::where('manager_id', $manager->id)
                ->where('status', 'completed')
                ->count();

            return (object) [
                'manager'           => $manager,
                'avg_score'         => $avgScore ? round((float) $avgScore, 1) : null,
                'survey_score'      => $survScore,
                'manager_eval_score' => $managerEvalScore,
                'manager_eval_count' => $managerEvalCount,
                'cycles_completed'  => $cyclesCompleted,
            ];
        })->sortByDesc(fn ($m) => $m->manager_eval_score ?? $m->survey_score ?? 0)->values();
    }

    #[Computed]
    public function evaluationsOverview(): Collection
    {
        $cycle = $this->activeCycle;
        if (! $cycle) {
            return collect();
        }

        return User::with('department')
            ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
            ->where('is_active', true)
            ->get()
            ->map(function (User $manager) use ($cycle) {
                $eval = ManagerEvaluation::where('manager_id', $manager->id)
                    ->where('evaluation_cycle_id', $cycle->id)
                    ->withCount('entries')
                    ->first();

                return (object) [
                    'manager'      => $manager,
                    'status'       => $eval?->status ?? 'not_started',
                    'completed_at' => $eval?->completed_at,
                    'entries'      => $eval?->entries_count ?? 0,
                ];
            });
    }

    // ───────────────────────────────────────────────────────────────────
    // AÇÕES — GERENTE: AVALIAR COLABORADORES
    // ───────────────────────────────────────────────────────────────────

    public function openEmployee(int $employeeId): void
    {
        $this->requireAuth();
        if (! auth()->user()->isGerente()) {
            abort(403);
        }

        // Toggle: fechar se já aberto
        if ($this->selectedEmployeeId === $employeeId) {
            $this->selectedEmployeeId = null;
            $this->scores             = [];
            $this->entryComments      = [];
            return;
        }

        $this->selectedEmployeeId = $employeeId;
        $this->scores             = [];
        $this->entryComments      = [];

        if (! $this->myEvaluation) {
            return;
        }

        // Carregar notas existentes
        ManagerEvaluationEntry::where('manager_evaluation_id', $this->myEvaluation->id)
            ->where('employee_id', $employeeId)
            ->each(function (ManagerEvaluationEntry $entry) {
                $this->scores[$entry->criterion_id]        = $entry->score;
                $this->entryComments[$entry->criterion_id] = $entry->comment ?? '';
            });
    }

    public function saveScore(int $criterionId, int $score): void
    {
        $this->requireAuth();
        if (! auth()->user()->isGerente()) {
            abort(403);
        }
        if ($score < 1 || $score > 5) {
            return;
        }
        if (! $this->selectedEmployeeId || ! $this->myEvaluation) {
            return;
        }
        if ($this->myEvaluation->status === 'completed') {
            return;
        }

        $this->scores[$criterionId] = $score;

        // Primeiro lançamento: marcar como em andamento
        if ($this->myEvaluation->status === 'not_started') {
            $this->myEvaluation->update(['status' => 'in_progress']);
        }

        ManagerEvaluationEntry::updateOrCreate(
            [
                'manager_evaluation_id' => $this->myEvaluation->id,
                'employee_id'           => $this->selectedEmployeeId,
                'criterion_id'          => $criterionId,
            ],
            [
                'score'   => $score,
                'comment' => $this->entryComments[$criterionId] ?? null,
            ]
        );

        // Invalidar caches
        unset($this->myEvaluation, $this->completedEmployeeIds, $this->evaluationProgress);
    }

    public function saveComment(int $criterionId): void
    {
        $this->requireAuth();
        if (! auth()->user()->isGerente()) {
            abort(403);
        }
        if (! $this->selectedEmployeeId || ! $this->myEvaluation) {
            return;
        }
        if ($this->myEvaluation->status === 'completed') {
            return;
        }

        $comment = $this->sanitize($this->entryComments[$criterionId] ?? '');
        $score   = $this->scores[$criterionId] ?? null;

        if (! $score) {
            return; // só salva comentário se já houver nota
        }

        ManagerEvaluationEntry::updateOrCreate(
            [
                'manager_evaluation_id' => $this->myEvaluation->id,
                'employee_id'           => $this->selectedEmployeeId,
                'criterion_id'          => $criterionId,
            ],
            ['score' => $score, 'comment' => $comment ?: null]
        );
    }

    public function requestComplete(): void
    {
        $this->requireAuth();
        if (! auth()->user()->isGerente()) {
            abort(403);
        }

        $progress = $this->evaluationProgress;
        if ($progress['evaluated'] === 0) {
            $this->alertError('Nenhum colaborador avaliado ainda.');
            return;
        }

        $this->confirmType  = 'complete_evaluation';
        $this->confirmId    = null;
        $this->confirmModal = true;
    }

    public function completeEvaluation(): void
    {
        $this->requireAuth();
        if (! auth()->user()->isGerente()) {
            abort(403);
        }

        $eval = $this->myEvaluation;
        if (! $eval || $eval->status === 'completed') {
            $this->confirmModal = false;
            return;
        }

        $eval->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        unset($this->myEvaluation, $this->completedEmployeeIds, $this->evaluationProgress, $this->historyEvaluations);

        $this->confirmModal       = false;
        $this->selectedEmployeeId = null;
        $this->scores             = [];
        $this->entryComments      = [];
        $this->activeTab          = 'dashboard';

        $this->alertSuccess('Avaliação finalizada!', 'Sua avaliação foi registrada com sucesso.');
    }

    // ───────────────────────────────────────────────────────────────────
    // AÇÕES — ADMIN: CICLOS
    // ───────────────────────────────────────────────────────────────────

    public function abrirCicloModal(?int $id = null): void
    {
        $this->requireRhOrAdmin();
        $this->resetCycleForm();

        if ($id) {
            $cycle = EvaluationCycle::findOrFail($id);
            $this->editingCycleId   = $id;
            $this->cycleName        = $cycle->name;
            $this->cycleDescription = $cycle->description ?? '';
            $this->cycleStart       = $cycle->start_date->format('Y-m-d');
            $this->cycleEnd         = $cycle->end_date->format('Y-m-d');
        }

        $this->cycleModal = true;
    }

    public function saveCycle(): void
    {
        $this->requireRhOrAdmin();

        $this->validate(
            [
                'cycleName'  => 'required|min:3|max:100',
                'cycleStart' => 'required|date',
                'cycleEnd'   => 'required|date|after:cycleStart',
            ],
            [
                'cycleName.required'  => 'O nome do ciclo é obrigatório.',
                'cycleStart.required' => 'A data de início é obrigatória.',
                'cycleEnd.required'   => 'A data de fim é obrigatória.',
                'cycleEnd.after'      => 'A data de fim deve ser posterior ao início.',
            ]
        );

        $data = [
            'name'        => $this->sanitize($this->cycleName),
            'description' => $this->sanitize($this->cycleDescription),
            'start_date'  => $this->cycleStart,
            'end_date'    => $this->cycleEnd,
        ];

        if ($this->editingCycleId) {
            $cycle = EvaluationCycle::findOrFail($this->editingCycleId);
            if ($cycle->status === 'active') {
                $this->alertError('Não é possível editar um ciclo ativo.');
                return;
            }
            $cycle->update($data);
            $this->alertSuccess('Ciclo atualizado com sucesso!');
        } else {
            EvaluationCycle::create(array_merge($data, [
                'status'     => 'draft',
                'created_by' => auth()->id(),
            ]));
            $this->alertSuccess('Ciclo criado com sucesso!');
        }

        unset($this->cycles, $this->activeCycle);
        $this->cycleModal = false;
        $this->resetCycleForm();
    }

    public function requestActivateCycle(int $id): void
    {
        $this->requireRhOrAdmin();
        $this->confirmType  = 'activate_cycle';
        $this->confirmId    = $id;
        $this->confirmModal = true;
    }

    public function activateCycle(): void
    {
        $this->requireRhOrAdmin();

        // Fechar qualquer ciclo ativo anterior
        EvaluationCycle::where('status', 'active')->update(['status' => 'closed']);
        EvaluationCycle::findOrFail($this->confirmId)->update(['status' => 'active']);

        unset($this->cycles, $this->activeCycle, $this->evaluationsOverview);
        $this->confirmModal = false;
        $this->alertSuccess('Ciclo ativado!', 'Os gerentes já podem iniciar suas avaliações.');
    }

    public function requestCloseCycle(int $id): void
    {
        $this->requireRhOrAdmin();
        $this->confirmType  = 'close_cycle';
        $this->confirmId    = $id;
        $this->confirmModal = true;
    }

    public function closeCycle(): void
    {
        $this->requireRhOrAdmin();
        EvaluationCycle::findOrFail($this->confirmId)->update(['status' => 'closed']);
        unset($this->cycles, $this->activeCycle, $this->evaluationsOverview);
        $this->confirmModal = false;
        $this->alertSuccess('Ciclo encerrado!');
    }

    public function requestDeleteCycle(int $id): void
    {
        $this->requireRhOrAdmin();
        $this->confirmType  = 'delete_cycle';
        $this->confirmId    = $id;
        $this->confirmModal = true;
    }

    public function deleteCycle(): void
    {
        $this->requireRhOrAdmin();

        $cycle = EvaluationCycle::findOrFail($this->confirmId);
        if ($cycle->status === 'active') {
            $this->alertError('Não é possível excluir um ciclo ativo.', 'Encerre o ciclo antes de excluir.');
            $this->confirmModal = false;
            return;
        }

        $cycle->delete();
        unset($this->cycles, $this->activeCycle);
        $this->confirmModal = false;
        $this->alertSuccess('Ciclo excluído!');
    }

    // ───────────────────────────────────────────────────────────────────
    // AÇÕES — ADMIN: CRITÉRIOS
    // ───────────────────────────────────────────────────────────────────

    public function abrirCriterioModal(?int $id = null): void
    {
        $this->requireRhOrAdmin();
        $this->editingCriterionId   = null;
        $this->criterionName        = '';
        $this->criterionDescription = '';

        if ($id) {
            $crit = EvaluationCriterion::findOrFail($id);
            $this->editingCriterionId   = $id;
            $this->criterionName        = $crit->name;
            $this->criterionDescription = $crit->description ?? '';
        }

        $this->criterionModal = true;
    }

    public function saveCriterion(): void
    {
        $this->requireRhOrAdmin();

        $this->validate(
            ['criterionName' => 'required|min:2|max:100'],
            ['criterionName.required' => 'O nome do critério é obrigatório.']
        );

        $data = [
            'name'        => $this->sanitize($this->criterionName),
            'description' => $this->sanitize($this->criterionDescription),
        ];

        if ($this->editingCriterionId) {
            EvaluationCriterion::findOrFail($this->editingCriterionId)->update($data);
            $this->alertSuccess('Critério atualizado!');
        } else {
            EvaluationCriterion::create(array_merge($data, ['is_active' => true]));
            $this->alertSuccess('Critério criado!');
        }

        unset($this->allCriteria, $this->criteria);
        $this->criterionModal        = false;
        $this->editingCriterionId    = null;
        $this->criterionName         = '';
        $this->criterionDescription  = '';
    }

    public function toggleCriterion(int $id): void
    {
        $this->requireRhOrAdmin();
        $crit = EvaluationCriterion::findOrFail($id);
        $crit->update(['is_active' => ! $crit->is_active]);
        unset($this->allCriteria, $this->criteria);
    }

    public function deleteCriterion(int $id): void
    {
        $this->requireRhOrAdmin();
        $crit = EvaluationCriterion::findOrFail($id);

        if ($crit->is_default) {
            $this->alertError('Critérios padrão não podem ser excluídos.', 'Você pode desativá-lo, mas não excluir.');
            return;
        }
        if ($crit->entries()->exists()) {
            $this->alertError('Este critério já possui avaliações registradas e não pode ser excluído.');
            return;
        }

        $crit->delete();
        unset($this->allCriteria, $this->criteria);
        $this->alertSuccess('Critério excluído!');
    }

    // ───────────────────────────────────────────────────────────────────
    // CONFIRMAÇÃO GENÉRICA
    // ───────────────────────────────────────────────────────────────────

    public function confirmAction(): void
    {
        match ($this->confirmType) {
            'activate_cycle'      => $this->activateCycle(),
            'close_cycle'         => $this->closeCycle(),
            'delete_cycle'        => $this->deleteCycle(),
            'complete_evaluation' => $this->completeEvaluation(),
            default               => ($this->confirmModal = false),
        };
    }

    // ───────────────────────────────────────────────────────────────────
    // HELPERS
    // ───────────────────────────────────────────────────────────────────

    protected function resetCycleForm(): void
    {
        $this->editingCycleId   = null;
        $this->cycleName        = '';
        $this->cycleDescription = '';
        $this->cycleStart       = now()->format('Y-m-d');
        $this->cycleEnd         = now()->addMonth()->format('Y-m-d');
        $this->resetValidation();
    }

    public function render(): \Illuminate\View\View
    {
        return view('livewire.pages.manager-evaluation.index');
    }
}
