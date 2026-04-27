<?php

namespace App\Livewire\Pages\Surveys;

use App\Livewire\SecureComponent;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;

class Compare extends SecureComponent
{
    /** IDs das pesquisas selecionadas para comparação (2–4). */
    public array $selectedIds = [];

    /** Mensagem de erro de similaridade, se houver. */
    public ?string $similarityError = null;

    // ─────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();

        if (! Auth::user()->podeGerenciarPesquisas()) {
            abort(403, 'Sem permissão para acessar o comparativo de pesquisas.');
        }
    }

    // ─── Pesquisas que o usuário pode ver (com ≥1 resposta) ──────────
    #[Computed]
    public function accessibleSurveys(): Collection
    {
        $user = Auth::user();

        return Survey::with('questions')
            ->when(
                ! $user->isAdmin(),
                fn ($q) => $q->where('created_by', $user->id)
            )
            ->whereIn('status', ['ativa', 'encerrada'])
            ->whereHas('responses', fn ($q) => $q->whereNotNull('completed_at'))
            ->orderBy('start_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    // ─── Reativo: valida similaridade ao mudar seleção ───────────────
    public function updatedSelectedIds(): void
    {
        $this->similarityError = null;

        if (count($this->selectedIds) >= 2) {
            $this->validateSimilarity();
        }
    }

    // ─── Valida todos os pares em relação à 1ª pesquisa selecionada ──
    private function validateSimilarity(): void
    {
        $surveys = $this->accessibleSurveys
            ->whereIn('id', $this->selectedIds)
            ->values();

        if ($surveys->count() < 2) {
            return;
        }

        // Usa a primeira pesquisa selecionada como base
        $base = $surveys->firstWhere('id', $this->selectedIds[0]) ?? $surveys->first();

        foreach ($surveys as $other) {
            if ($other->id === $base->id) {
                continue;
            }

            if ($this->computeSimilarity($base, $other) < 0.5) {
                $this->similarityError = sprintf(
                    '"%s" e "%s" não são similares o suficiente — menos de 50%% das perguntas em comum. '
                    . 'Selecione edições da mesma pesquisa ou pesquisas com as mesmas perguntas.',
                    $base->title,
                    $other->title
                );

                return;
            }
        }
    }

    // ─── % de perguntas em comum entre dois surveys (0.0–1.0) ────────
    private function computeSimilarity(Survey $a, Survey $b): float
    {
        $qA = $a->questions->map(fn ($q) => mb_strtolower(trim($q->question)));
        $qB = $b->questions->map(fn ($q) => mb_strtolower(trim($q->question)));

        if ($qA->isEmpty() || $qB->isEmpty()) {
            return 0.0;
        }

        $matched = 0;

        foreach ($qA as $textA) {
            foreach ($qB as $textB) {
                similar_text($textA, $textB, $pct);
                if ($pct >= 80) {
                    $matched++;
                    break;
                }
            }
        }

        $smaller = min($qA->count(), $qB->count());

        return $smaller > 0 ? ($matched / $smaller) : 0.0;
    }

    // ─── Dados completos do comparativo ──────────────────────────────
    #[Computed]
    public function comparison(): ?array
    {
        if (count($this->selectedIds) < 2 || $this->similarityError) {
            return null;
        }

        // Ordena cronologicamente pela data de início
        $surveys = $this->accessibleSurveys
            ->whereIn('id', $this->selectedIds)
            ->sortBy('start_date')
            ->values();

        if ($surveys->count() < 2) {
            return null;
        }

        // ── 1. Perguntas compartilhadas (presentes em TODOS os surveys) ──
        $base            = $surveys->first();
        $sharedQuestions = [];

        foreach ($base->questions as $baseQ) {
            $normBase = mb_strtolower(trim($baseQ->question));
            $matchMap = [$base->id => $baseQ->id]; // survey_id => question_id

            foreach ($surveys->slice(1) as $survey) {
                $best    = null;
                $bestPct = 0;

                foreach ($survey->questions as $q) {
                    similar_text($normBase, mb_strtolower(trim($q->question)), $pct);
                    if ($pct > $bestPct && $pct >= 80) {
                        $bestPct = $pct;
                        $best    = $q;
                    }
                }

                if ($best) {
                    $matchMap[$survey->id] = $best->id;
                }
            }

            if (count($matchMap) === $surveys->count()) {
                $sharedQuestions[] = [
                    'text'    => $baseQ->question,
                    'type'    => $baseQ->type,
                    'options' => $baseQ->options ?? [],
                    'map'     => $matchMap,
                ];
            }
        }

        // ── 2. Stats por pesquisa ─────────────────────────────────────
        $surveyStats  = [];
        $completedMap = []; // survey_id => response_ids[]

        foreach ($surveys as $survey) {
            $completedIds = SurveyResponse::where('survey_id', $survey->id)
                ->whereNotNull('completed_at')
                ->pluck('id')
                ->toArray();

            $completedMap[$survey->id] = $completedIds;
            $totalResp                 = count($completedIds);

            if ($survey->target_audience === 'todos') {
                $totalInvited = User::count();
            } else {
                $deptIds      = array_map('intval', $survey->target_department_ids ?? []);
                $totalInvited = User::whereIn('department_id', $deptIds)->count();
            }

            $period = '';
            if ($survey->start_date && $survey->end_date) {
                $period = $survey->start_date->format('d/m/Y') . ' – ' . $survey->end_date->format('d/m/Y');
            } elseif ($survey->start_date) {
                $period = 'A partir de ' . $survey->start_date->format('d/m/Y');
            } elseif ($survey->end_date) {
                $period = 'Até ' . $survey->end_date->format('d/m/Y');
            }

            // Label curto para eixos dos gráficos
            $label = $survey->start_date
                ? ucfirst($survey->start_date->translatedFormat('M/y'))
                : mb_strimwidth($survey->title, 0, 18, '…');

            $surveyStats[$survey->id] = [
                'id'              => $survey->id,
                'title'           => $survey->title,
                'period'          => $period,
                'label'           => $label,
                'total_responses' => $totalResp,
                'total_invited'   => $totalInvited,
                'response_rate'   => $totalInvited > 0
                    ? round($totalResp / $totalInvited * 100, 1)
                    : 0,
            ];
        }

        // ── 3. Analytics por pergunta compartilhada ───────────────────
        $questionAnalytics = [];

        foreach ($sharedQuestions as $sq) {
            $perSurvey = [];

            foreach ($surveys as $survey) {
                $qId     = $sq['map'][$survey->id];
                $respIds = $completedMap[$survey->id];

                $answers = SurveyAnswer::where('question_id', $qId)
                    ->whereIn('response_id', $respIds)
                    ->get();

                if ($sq['type'] === 'escala') {
                    $dist = array_fill(0, 11, 0);

                    foreach ($answers as $a) {
                        if ($a->value_scale !== null) {
                            $dist[(int) $a->value_scale]++;
                        }
                    }

                    $total      = array_sum($dist);
                    $promoters  = $dist[9] + $dist[10];
                    $passives   = $dist[7] + $dist[8];
                    $detractors = array_sum(array_slice($dist, 0, 7));
                    $nps        = $total > 0
                        ? round((($promoters - $detractors) / $total) * 100)
                        : null;

                    $sum = 0;
                    foreach ($answers as $a) {
                        if ($a->value_scale !== null) {
                            $sum += (int) $a->value_scale;
                        }
                    }

                    $perSurvey[$survey->id] = [
                        'dist'        => array_values($dist),
                        'total'       => $total,
                        'promoters'   => $total > 0 ? round($promoters  / $total * 100, 1) : 0,
                        'passives'    => $total > 0 ? round($passives   / $total * 100, 1) : 0,
                        'detractors'  => $total > 0 ? round($detractors / $total * 100, 1) : 0,
                        'nps'         => $nps,
                        'avg'         => $total > 0 ? round($sum / $total, 1) : null,
                    ];

                } elseif ($sq['type'] === 'multipla_escolha') {
                    $counts = array_fill_keys($sq['options'], 0);

                    foreach ($answers as $a) {
                        if ($a->value_option && isset($counts[$a->value_option])) {
                            $counts[$a->value_option]++;
                        }
                    }

                    $total = array_sum($counts);
                    $pcts  = [];

                    foreach ($counts as $opt => $cnt) {
                        $pcts[$opt] = $total > 0 ? round($cnt / $total * 100, 1) : 0;
                    }

                    $perSurvey[$survey->id] = [
                        'counts'      => $counts,
                        'percentages' => $pcts,
                        'total'       => $total,
                    ];

                } else {
                    // texto_livre — apenas contagem
                    $perSurvey[$survey->id] = ['total' => $answers->count()];
                }
            }

            $questionAnalytics[] = [
                'text'    => $sq['text'],
                'type'    => $sq['type'],
                'options' => $sq['options'],
                'data'    => $perSurvey,
            ];
        }

        return [
            'ordered_ids'        => $surveys->pluck('id')->toArray(),
            'stats'              => $surveyStats,
            'question_analytics' => $questionAnalytics,
            'shared_count'       => count($sharedQuestions),
        ];
    }

    // ─────────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.surveys.compare');
    }
}
