<?php

namespace App\Livewire\Pages\ManagerEvaluation;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Notifications\AvaliacaoSubmetidaNotification;
use App\Models\EvaluationCriterion;
use App\Models\EvaluationCycle;
use App\Models\ManagerEvaluation;
use App\Models\ManagerEvaluationEntry;
use App\Models\SelfEvaluation;
use App\Models\SelfEvaluationEntry;
use App\Models\SurveyManagerScore;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class Index extends SecureComponent
{
    use EnviaNotificacoes;
    // ── UI State ────────────────────────────────────────────────────────
    public string $activeTab = 'dashboard';

    // ── Gerente: Avaliação ──────────────────────────────────────────────
    public ?int   $selectedEmployeeId = null;
    public array  $scores             = [];
    public array  $entryComments      = [];
    public string $employeeSearch     = '';

    // ── Gerente: Histórico ──────────────────────────────────────────────
    public ?int $historyYearFilter  = null;
    public ?int $historyCycleFilter = null;
    public ?int $expandedHistoryId  = null;

    // ── Gerente: Histórico de evolução do colaborador ───────────────────
    public ?int $evolutionEmployeeId = null;

    // ── Colaborador: Autoavaliação ──────────────────────────────────────
    public array  $selfScores    = [];
    public array  $selfComments  = [];

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
    public float  $criterionWeight       = 1.0;
    public string $criterionResponseType = 'scale';
    public string $criterionOptions      = '';

    // ── Admin: Calibração ───────────────────────────────────────────────
    public ?int   $calibrationCycleId    = null;
    public ?int   $calibrationManagerId  = null;
    public ?int   $calibrationEmployeeId = null;
    public array  $calibrationScores     = [];
    public array  $calibrationNotes      = [];

    // ── Confirmação ─────────────────────────────────────────────────────
    public bool   $confirmModal  = false;
    public string $confirmType   = '';
    #[Locked] public ?int $confirmId = null;

    // ───────────────────────────────────────────────────────────────────
    // MOUNT
    // ───────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->requireAuth();

        $user = auth()->user();

        if (! $user->podeGerenciarPesquisas() && ! $user->isEmployee()) {
            abort(403, 'Acesso restrito.');
        }

        if ($user->isEmployee()) {
            $this->activeTab = 'autoavaliacao';
        } elseif ($user->isGerente()) {
            $this->activeTab = 'dashboard';
        } else {
            $this->activeTab = 'ciclos';
        }

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
    // COMPUTED — COLABORADOR: AUTOAVALIAÇÃO
    // ───────────────────────────────────────────────────────────────────

    #[Computed]
    public function mySelfEvaluation(): ?SelfEvaluation
    {
        if (! $this->activeCycle) {
            return null;
        }

        return SelfEvaluation::firstOrCreate(
            [
                'evaluation_cycle_id' => $this->activeCycle->id,
                'employee_id'         => auth()->id(),
            ],
            ['status' => 'pending']
        );
    }

    #[Computed]
    public function selfEvaluationCompleted(): bool
    {
        return $this->mySelfEvaluation?->status === 'completed';
    }

    /**
     * Resultado da avaliação do gerente visível ao colaborador (após publicação).
     */
    #[Computed]
    public function myManagerEvaluationResult(): ?object
    {
        $cycle = $this->activeCycle
            ?? EvaluationCycle::whereNotNull('results_published_at')
                               ->orderByDesc('results_published_at')
                               ->first();

        if (! $cycle || ! $cycle->results_published_at) {
            return null;
        }

        $managerEval = ManagerEvaluation::where('evaluation_cycle_id', $cycle->id)
            ->where('status', 'completed')
            ->whereHas('entries', fn ($q) => $q->where('employee_id', auth()->id()))
            ->with([
                'manager',
                'entries' => fn ($q) => $q->where('employee_id', auth()->id())->with('criterion'),
            ])
            ->first();

        if (! $managerEval) {
            return null;
        }

        $selfEntries = SelfEvaluationEntry::whereHas(
            'selfEvaluation',
            fn ($q) => $q->where('evaluation_cycle_id', $cycle->id)
                         ->where('employee_id', auth()->id())
        )->with('criterion')->get()->keyBy('criterion_id');

        $comparison = $managerEval->entries->map(function ($entry) use ($selfEntries) {
            $selfEntry = $selfEntries->get($entry->criterion_id);
            return (object) [
                'criterion'        => $entry->criterion,
                'manager_score'    => $entry->effectiveScore,
                'self_score'       => $selfEntry?->score,
                'manager_display'  => $entry->effectiveDisplayValue,
                'is_calibrated'    => $entry->calibrated_score !== null,
                'original_display' => $entry->displayValue,
                'self_display'     => $selfEntry?->displayValue ?? '—',
                'comment'          => $entry->calibration_note ?? $entry->comment,
            ];
        });

        return (object) [
            'cycle'        => $cycle,
            'manager'      => $managerEval->manager,
            'final_score'  => $managerEval->final_score ?? $managerEval->averageScore,
            'completed_at' => $managerEval->completed_at,
            'comparison'   => $comparison,
        ];
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

        return User::notAdmin()
            ->where('department_id', auth()->user()->department_id)
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

        return User::notAdmin()
            ->where('department_id', auth()->user()->department_id)
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
            ->havingRaw('COUNT(*) >= ?', [$criteriaCount])
            ->pluck('employee_id')
            ->toArray();
    }

    #[Computed]
    public function evaluationProgress(): array
    {
        $total     = $this->allDeptEmployees->count();
        $evaluated = count($this->completedEmployeeIds);
        $pct       = $total > 0 ? min(100, (int) round(($evaluated / $total) * 100)) : 0;

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
                    'sq.options',
                    DB::raw('COUNT(sa.id) as response_count'),
                    DB::raw('ROUND(AVG(sa.value_scale)::numeric, 2) as avg_scale'),
                ])
                ->groupBy('sq.id', 'sq.question', 'sq.type', 'sq.options')
                ->orderBy('sq.id')
                ->get();

            return $rows->map(function ($row) use ($user) {
                $options      = json_decode($row->options ?? '[]', true) ?? [];
                $n            = count($options);
                $avgScore     = null;
                $distribution = [];

                if ($row->type === 'escala' && $row->avg_scale !== null) {
                    $avgScore = (float) $row->avg_scale;
                } elseif ($row->type === 'multipla_escolha' && $n > 0) {
                    $respostas = DB::table('survey_answers as sa')
                        ->join('survey_responses as sr', 'sr.id', '=', 'sa.response_id')
                        ->join('survey_questions as sq', 'sq.id', '=', 'sa.question_id')
                        ->join('surveys as s', 's.id', '=', 'sq.survey_id')
                        ->where('sa.question_id', $row->question_id)
                        ->whereNotNull('sr.completed_at')
                        ->whereNotNull('sa.value_option')
                        ->where(function ($q) use ($user) {
                            $q->where('s.target_audience', 'todos')
                              ->orWhereJsonContains('s.target_department_ids', $user->department_id);
                        })
                        ->pluck('sa.value_option');

                    $notasOpcoes = $respostas->map(function ($opcao) use ($options, $n) {
                        $index = array_search($opcao, $options, strict: true);
                        if ($index === false) return null;
                        return $n === 1 ? 10.0 : round(($n - 1 - $index) / ($n - 1) * 10, 4);
                    })->filter(fn ($v) => $v !== null);

                    $avgScore = $notasOpcoes->isNotEmpty() ? round($notasOpcoes->avg(), 2) : null;

                    foreach ($options as $opcao) {
                        $distribution[$opcao] = $respostas->filter(fn ($r) => $r === $opcao)->count();
                    }
                }

                $comments = collect();
                if ($row->type === 'texto_livre') {
                    $comments = DB::table('survey_answers as sa')
                        ->join('survey_responses as sr', 'sr.id', '=', 'sa.response_id')
                        ->where('sa.question_id', $row->question_id)
                        ->whereNotNull('sr.completed_at')
                        ->whereNotNull('sa.value_text')
                        ->where('sa.value_text', '!=', '')
                        ->orderByDesc('sr.completed_at')
                        ->limit(20)
                        ->pluck('sa.value_text');
                }

                return (object) [
                    'question_id'    => $row->question_id,
                    'question'       => $row->question,
                    'type'           => $row->type,
                    'options'        => $options,
                    'avg_score'      => $avgScore,
                    'response_count' => (int) $row->response_count,
                    'distribution'   => $distribution,
                    'comments'       => $comments,
                ];
            });
        } catch (\Throwable) {
            return collect();
        }
    }

    #[Computed]
    public function managerSurveyStats(): array
    {
        $results   = $this->managerSurveyResults;
        $numericos = $results
            ->whereIn('type', ['escala', 'multipla_escolha'])
            ->filter(fn ($r) => $r->avg_score !== null);

        if ($numericos->isEmpty()) {
            return ['count' => 0, 'avg' => null, 'by_question' => []];
        }

        $totalResponses = $numericos->sum('response_count');
        $weightedSum    = $numericos->sum(fn ($r) => $r->avg_score * $r->response_count);
        $avgGeneral     = $totalResponses > 0 ? round($weightedSum / $totalResponses, 1) : null;

        return [
            'count'       => (int) $totalResponses,
            'avg'         => $avgGeneral,
            'by_question' => $numericos->mapWithKeys(fn ($r) => [$r->question_id => $r->avg_score])->all(),
        ];
    }

    // ── Histórico de avaliações realizadas pelo gerente ────────────────

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

    // ── Histórico de evolução de um colaborador específico ─────────────

    #[Computed]
    public function employeeEvolutionData(): array
    {
        if (! $this->evolutionEmployeeId) {
            return [];
        }

        $entries = ManagerEvaluationEntry::with(['evaluation.cycle', 'criterion'])
            ->where('employee_id', $this->evolutionEmployeeId)
            ->whereHas('evaluation', fn ($q) => $q->where('status', 'completed'))
            ->get();

        if ($entries->isEmpty()) {
            return [];
        }

        $byCycle = $entries->groupBy('evaluation.evaluation_cycle_id');
        $points  = [];

        foreach ($byCycle as $cycleId => $cycleEntries) {
            $cycle       = $cycleEntries->first()->evaluation->cycle;
            $totalWeight = 0;
            $weightedSum = 0;

            foreach ($cycleEntries as $entry) {
                $weight = (float) ($entry->criterion->weight ?? 1.0);
                $score  = $entry->normalizedScore;
                if ($score !== null) {
                    $weightedSum += $score * $weight;
                    $totalWeight += $weight;
                }
            }

            if ($totalWeight > 0) {
                $points[] = [
                    'cycle_id'   => $cycle->id,
                    'cycle_name' => $cycle->name,
                    'period'     => $cycle->periodLabel,
                    'score'      => round($weightedSum / $totalWeight, 2),
                    'start_date' => $cycle->start_date->format('Y-m-d'),
                    'self_score' => null,
                ];
            }
        }

        usort($points, fn ($a, $b) => strcmp($a['start_date'], $b['start_date']));

        // Enriquecer com autoavaliação
        $selfPoints = SelfEvaluationEntry::with(['selfEvaluation.cycle', 'criterion'])
            ->whereHas('selfEvaluation', fn ($q) => $q
                ->where('employee_id', $this->evolutionEmployeeId)
                ->where('status', 'completed'))
            ->get()
            ->groupBy('selfEvaluation.evaluation_cycle_id')
            ->map(function ($selfEntries) {
                $totalWeight = 0;
                $weightedSum = 0;
                foreach ($selfEntries as $entry) {
                    $weight = (float) ($entry->criterion->weight ?? 1.0);
                    $score  = $entry->normalizedScore;
                    if ($score !== null) {
                        $weightedSum += $score * $weight;
                        $totalWeight += $weight;
                    }
                }
                return $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : null;
            })->filter();

        foreach ($points as &$point) {
            $point['self_score'] = $selfPoints->get($point['cycle_id']);
        }

        return $points;
    }

    #[Computed]
    public function evolutionEmployee(): ?User
    {
        if (! $this->evolutionEmployeeId) {
            return null;
        }
        return User::find($this->evolutionEmployeeId);
    }

    // ───────────────────────────────────────────────────────────────────
    // COMPUTED — ADMIN / RH
    // ───────────────────────────────────────────────────────────────────

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

        return $managers->map(function (User $manager) {
            // Usa nota calibrada quando disponível (COALESCE garante fallback para original)
            $avgScore = DB::table('manager_evaluation_entries')
                ->join('manager_evaluations', 'manager_evaluations.id', '=', 'manager_evaluation_entries.manager_evaluation_id')
                ->where('manager_evaluations.manager_id', $manager->id)
                ->where('manager_evaluations.status', 'completed')
                ->avg(DB::raw('COALESCE(manager_evaluation_entries.calibrated_score, manager_evaluation_entries.score)'));

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

            $managerEvalScore = null;
            $managerEvalCount = 0;
            try {
                $scores = SurveyManagerScore::where('manager_id', $manager->id)
                    ->whereNotNull('average_score')
                    ->get(['average_score', 'total_responses']);
                if ($scores->isNotEmpty()) {
                    $managerEvalCount = (int) $scores->sum('total_responses');
                    $weightedSum      = $scores->sum(fn ($s) => $s->average_score * $s->total_responses);
                    $managerEvalScore = $managerEvalCount > 0
                        ? round($weightedSum / $managerEvalCount, 1)
                        : null;
                }
            } catch (\Throwable) {
            }

            $cyclesCompleted = ManagerEvaluation::where('manager_id', $manager->id)
                ->where('status', 'completed')
                ->count();

            return (object) [
                'manager'            => $manager,
                'avg_score'          => $avgScore ? round((float) $avgScore, 1) : null,
                'survey_score'       => $survScore,
                'manager_eval_score' => $managerEvalScore,
                'manager_eval_count' => $managerEvalCount,
                'cycles_completed'   => $cyclesCompleted,
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

    // ── Calibração ─────────────────────────────────────────────────────

    #[Computed]
    public function calibrationEvaluations(): Collection
    {
        if (! $this->calibrationCycleId) {
            return collect();
        }

        return ManagerEvaluation::with(['manager', 'manager.department'])
            ->where('evaluation_cycle_id', $this->calibrationCycleId)
            ->where('status', 'completed')
            ->when($this->calibrationManagerId, fn ($q) => $q->where('manager_id', $this->calibrationManagerId))
            ->orderBy('completed_at')
            ->get();
    }

    #[Computed]
    public function calibrationEntries(): Collection
    {
        if (! $this->calibrationManagerId || ! $this->calibrationEmployeeId || ! $this->calibrationCycleId) {
            return collect();
        }

        $eval = ManagerEvaluation::where('evaluation_cycle_id', $this->calibrationCycleId)
            ->where('manager_id', $this->calibrationManagerId)
            ->first();

        if (! $eval) {
            return collect();
        }

        return ManagerEvaluationEntry::with('criterion')
            ->where('manager_evaluation_id', $eval->id)
            ->where('employee_id', $this->calibrationEmployeeId)
            ->orderBy('criterion_id')
            ->get();
    }

    #[Computed]
    public function calibrationEmployees(): Collection
    {
        if (! $this->calibrationManagerId || ! $this->calibrationCycleId) {
            return collect();
        }

        $eval = ManagerEvaluation::where('evaluation_cycle_id', $this->calibrationCycleId)
            ->where('manager_id', $this->calibrationManagerId)
            ->first();

        if (! $eval) {
            return collect();
        }

        $employeeIds = ManagerEvaluationEntry::where('manager_evaluation_id', $eval->id)
            ->distinct()
            ->pluck('employee_id');

        return User::notAdmin()->whereIn('id', $employeeIds)->orderBy('name')->get();
    }

    #[Computed]
    public function calibrationManagers(): Collection
    {
        if (! $this->calibrationCycleId) {
            return collect();
        }

        return User::notAdmin()
            ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
            ->whereHas('managerEvaluations', fn ($q) => $q
                ->where('evaluation_cycle_id', $this->calibrationCycleId)
                ->where('status', 'completed'))
            ->with('department')
            ->orderBy('name')
            ->get();
    }

    // ───────────────────────────────────────────────────────────────────
    // AÇÕES — COLABORADOR: AUTOAVALIAÇÃO
    // ───────────────────────────────────────────────────────────────────

    public function saveSelfScore(int $criterionId, int $score): void
    {
        $this->requireAuth();

        $selfEval = $this->mySelfEvaluation;
        if (! $selfEval || $selfEval->status === 'completed') {
            return;
        }

        $criterion = EvaluationCriterion::find($criterionId);
        if (! $criterion) {
            return;
        }

        $valid = match ($criterion->response_type ?? 'scale') {
            'scale'           => $score >= 1 && $score <= 5,
            'boolean'         => in_array($score, [0, 1]),
            'multiple_choice' => $score >= 0 && $score < count($criterion->options ?? []),
            default           => false,
        };

        if (! $valid) {
            return;
        }

        $this->selfScores[$criterionId] = $score;

        if ($selfEval->status === 'pending') {
            $selfEval->update(['status' => 'in_progress']);
        }

        SelfEvaluationEntry::updateOrCreate(
            [
                'self_evaluation_id' => $selfEval->id,
                'criterion_id'       => $criterionId,
            ],
            [
                'score'   => $score,
                'comment' => $this->selfComments[$criterionId] ?? null,
            ]
        );

        unset($this->mySelfEvaluation);
    }

    public function saveSelfComment(int $criterionId): void
    {
        $this->requireAuth();

        $selfEval = $this->mySelfEvaluation;
        if (! $selfEval || $selfEval->status === 'completed') {
            return;
        }

        $comment = $this->sanitize($this->selfComments[$criterionId] ?? '');
        $score   = $this->selfScores[$criterionId] ?? null;

        if ($score === null) {
            return;
        }

        SelfEvaluationEntry::updateOrCreate(
            [
                'self_evaluation_id' => $selfEval->id,
                'criterion_id'       => $criterionId,
            ],
            ['score' => $score, 'comment' => $comment ?: null]
        );
    }

    public function loadSelfEvaluationData(): void
    {
        $selfEval = $this->mySelfEvaluation;
        if (! $selfEval) {
            return;
        }

        $this->selfScores   = [];
        $this->selfComments = [];

        SelfEvaluationEntry::where('self_evaluation_id', $selfEval->id)
            ->each(function (SelfEvaluationEntry $entry) {
                $this->selfScores[$entry->criterion_id]   = $entry->score;
                $this->selfComments[$entry->criterion_id] = $entry->comment ?? '';
            });
    }

    public function submitSelfEvaluation(): void
    {
        $this->requireAuth();

        $selfEval      = $this->mySelfEvaluation;
        $criteriaCount = $this->criteria->count();

        if (! $selfEval || $selfEval->status === 'completed') {
            return;
        }

        $answeredCount = SelfEvaluationEntry::where('self_evaluation_id', $selfEval->id)->count();

        if ($answeredCount < $criteriaCount) {
            $faltam = $criteriaCount - $answeredCount;
            $this->alertError('Autoavaliação incompleta', "Ainda faltam {$faltam} critério(s) sem resposta.");
            return;
        }

        $selfEval->update([
            'status'       => 'completed',
            'completed_at' => now(),
        ]);

        unset($this->mySelfEvaluation, $this->selfEvaluationCompleted);

        // Notifica RH/Admin sobre autoavaliação submetida
        $ciclo = $this->activeCycle;
        $this->notificarRhAdmin(new AvaliacaoSubmetidaNotification(Auth::user()->name, $ciclo?->title ?? 'Ciclo atual'));

        $this->alertSuccess('Autoavaliação enviada!', 'Sua autoavaliação foi registrada com sucesso.');
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

        $criterion = EvaluationCriterion::find($criterionId);
        if (! $criterion) {
            return;
        }

        $valid = match ($criterion->response_type ?? 'scale') {
            'scale'           => $score >= 1 && $score <= 5,
            'boolean'         => in_array($score, [0, 1]),
            'multiple_choice' => $score >= 0 && $score < count($criterion->options ?? []),
            default           => false,
        };

        if (! $valid || ! $this->selectedEmployeeId || ! $this->myEvaluation) {
            return;
        }
        if ($this->myEvaluation->status === 'completed') {
            return;
        }

        $this->scores[$criterionId] = $score;

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

        if ($score === null) {
            return;
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

        if ($progress['evaluated'] < $progress['total']) {
            $restantes = $progress['total'] - $progress['evaluated'];
            $this->alertError(
                'Avaliação incompleta',
                "Ainda faltam {$restantes} colaborador(es) sem avaliação. Conclua todos antes de finalizar."
            );
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

        // Calcular e persistir o final_score com média ponderada
        $eval->calculateFinalScore();

        unset($this->myEvaluation, $this->completedEmployeeIds, $this->evaluationProgress, $this->historyEvaluations);

        $this->confirmModal       = false;
        $this->selectedEmployeeId = null;
        $this->scores             = [];
        $this->entryComments      = [];
        $this->activeTab          = 'dashboard';

        // Notifica RH/Admin sobre avaliação de gestor finalizada
        $ciclo = $this->activeCycle;
        $this->notificarRhAdmin(new AvaliacaoSubmetidaNotification(Auth::user()->name, $ciclo?->title ?? 'Ciclo atual'));

        $this->alertSuccess('Avaliação finalizada!', 'Sua avaliação foi registrada com sucesso.');
    }

    // ── Histórico: visualizar evolução ─────────────────────────────────

    public function openEvolution(int $employeeId): void
    {
        $this->requireAuth();
        $this->evolutionEmployeeId = ($this->evolutionEmployeeId === $employeeId) ? null : $employeeId;
        unset($this->employeeEvolutionData, $this->evolutionEmployee);
    }

    // ───────────────────────────────────────────────────────────────────
    // AÇÕES — ADMIN/RH: CALIBRAÇÃO
    // ───────────────────────────────────────────────────────────────────

    public function selectCalibrationManager(int $managerId): void
    {
        $this->requireRhOrAdmin();
        $this->calibrationManagerId  = $managerId;
        $this->calibrationEmployeeId = null;
        $this->calibrationScores     = [];
        $this->calibrationNotes      = [];
        unset($this->calibrationEntries, $this->calibrationEmployees, $this->calibrationEvaluations);
    }

    public function loadCalibrationEntries(int $employeeId): void
    {
        $this->requireRhOrAdmin();

        $this->calibrationEmployeeId = $employeeId;
        $this->calibrationScores     = [];
        $this->calibrationNotes      = [];

        unset($this->calibrationEntries);

        foreach ($this->calibrationEntries as $entry) {
            $this->calibrationScores[$entry->id] = $entry->calibrated_score ?? $entry->score;
            $this->calibrationNotes[$entry->id]  = $entry->calibration_note ?? '';
        }
    }

    public function saveCalibration(): void
    {
        $this->requireRhOrAdmin();

        $entries = $this->calibrationEntries;
        $saved   = 0;

        foreach ($entries as $entry) {
            $newScore = isset($this->calibrationScores[$entry->id])
                ? (int) $this->calibrationScores[$entry->id]
                : null;
            $note = $this->sanitize($this->calibrationNotes[$entry->id] ?? '');

            if ($newScore === null) {
                continue;
            }

            $criterion = $entry->criterion;
            $valid     = match ($criterion->response_type ?? 'scale') {
                'scale'           => $newScore >= 1 && $newScore <= 5,
                'boolean'         => in_array($newScore, [0, 1]),
                'multiple_choice' => $newScore >= 0 && $newScore < count($criterion->options ?? []),
                default           => false,
            };

            if (! $valid) {
                continue;
            }

            $entry->update([
                'calibrated_score' => $newScore,
                'calibration_note' => $note ?: null,
                'calibrated_by'    => auth()->id(),
                'calibrated_at'    => now(),
            ]);

            $saved++;
        }

        if ($saved === 0) {
            $this->alertError(
                'Nenhuma nota calibrada.',
                'Selecione ao menos uma nota válida para cada critério antes de salvar.'
            );
            return;
        }

        // Recalcular final_score da avaliação após calibração
        if ($this->calibrationManagerId && $this->calibrationCycleId) {
            $eval = ManagerEvaluation::where('evaluation_cycle_id', $this->calibrationCycleId)
                ->where('manager_id', $this->calibrationManagerId)
                ->first();
            $eval?->calculateFinalScore();
        }

        unset($this->calibrationEntries);
        $this->alertSuccess(
            'Calibração salva!',
            "{$saved} " . ($saved === 1 ? 'nota calibrada' : 'notas calibradas') . ' com sucesso.'
        );
    }

    public function publishResults(int $cycleId): void
    {
        $this->requireRhOrAdmin();

        $cycle = EvaluationCycle::findOrFail($cycleId);
        $cycle->update(['results_published_at' => now()]);

        unset($this->cycles, $this->activeCycle);
        $this->alertSuccess('Resultados publicados!', 'Os colaboradores já podem visualizar suas avaliações.');
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
        $this->editingCriterionId    = null;
        $this->criterionName         = '';
        $this->criterionDescription  = '';
        $this->criterionWeight       = 1.0;
        $this->criterionResponseType = 'scale';
        $this->criterionOptions      = '';

        if ($id) {
            $crit = EvaluationCriterion::findOrFail($id);
            $this->editingCriterionId    = $id;
            $this->criterionName         = $crit->name;
            $this->criterionDescription  = $crit->description ?? '';
            $this->criterionWeight       = (float) ($crit->weight ?? 1.0);
            $this->criterionResponseType = $crit->response_type ?? 'scale';
            $this->criterionOptions      = implode("\n", $crit->options ?? []);
        }

        $this->criterionModal = true;
    }

    public function saveCriterion(): void
    {
        $this->requireRhOrAdmin();

        $this->validate(
            [
                'criterionName'   => 'required|min:2|max:100',
                'criterionWeight' => 'required|numeric|min:0.1|max:10',
            ],
            [
                'criterionName.required'   => 'O nome do critério é obrigatório.',
                'criterionWeight.required' => 'O peso é obrigatório.',
                'criterionWeight.min'      => 'O peso mínimo é 0,1.',
                'criterionWeight.max'      => 'O peso máximo é 10.',
            ]
        );

        $options = null;
        if ($this->criterionResponseType === 'multiple_choice') {
            $lines   = array_filter(
                array_map('trim', explode("\n", $this->criterionOptions)),
                fn ($l) => $l !== ''
            );
            $options = array_values($lines);

            if (count($options) < 2) {
                $this->addError('criterionOptions', 'Informe pelo menos 2 opções (uma por linha).');
                return;
            }
        }

        $data = [
            'name'          => $this->sanitize($this->criterionName),
            'description'   => $this->sanitize($this->criterionDescription),
            'weight'        => round($this->criterionWeight, 2),
            'response_type' => $this->criterionResponseType,
            'options'       => $options,
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
        $this->criterionWeight       = 1.0;
        $this->criterionResponseType = 'scale';
        $this->criterionOptions      = '';
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
