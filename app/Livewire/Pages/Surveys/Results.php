<?php

namespace App\Livewire\Pages\Surveys;

use App\Livewire\SecureComponent;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class Results extends SecureComponent
{
    #[Locked]
    public int $surveyId;

    // ──────────────────────────────────────────────────────────────────
    public function mount(int $id): void
    {
        $this->requireAuth();

        $survey = Survey::findOrFail($id);
        $user   = Auth::user();

        if (! $user->podeGerenciarPesquisas()) {
            abort(403, 'Sem permissão para ver os resultados.');
        }

        // Gerente só vê resultados das próprias pesquisas
        if ($user->isGerente() && $survey->created_by !== $user->id) {
            abort(403, 'Você não tem permissão para ver os resultados desta pesquisa.');
        }

        $this->surveyId = $id;

        $this->dispatch('breadcrumb-set', items: [
            ['label' => 'Pesquisas', 'icon' => 'clipboard-list', 'url' => route('pesquisas')],
            ['label' => $survey->title, 'url' => null],
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    #[Computed]
    public function survey(): Survey
    {
        return Survey::with([
            'questions',
            'creator:id,name',
        ])->findOrFail($this->surveyId);
    }

    // ──────────────────────────────────────────────────────────────────
    #[Computed]
    public function stats(): array
    {
        $survey = $this->survey;

        $totalResponses = SurveyResponse::where('survey_id', $survey->id)
            ->whereNotNull('completed_at')
            ->count();

        if ($survey->target_audience === 'todos') {
            $totalInvited = User::count();
        } else {
            $deptIds      = array_map('intval', $survey->target_department_ids ?? []);
            $totalInvited = User::whereIn('department_id', $deptIds)->count();
        }

        $responseRate = $totalInvited > 0
            ? round(($totalResponses / $totalInvited) * 100, 1)
            : 0;

        return [
            'total_responses' => $totalResponses,
            'total_invited'   => $totalInvited,
            'response_rate'   => $responseRate,
            'total_questions' => $survey->questions->count(),
        ];
    }

    // ──────────────────────────────────────────────────────────────────
    #[Computed]
    public function questionResults(): array
    {
        $survey  = $this->survey;
        $results = [];

        // IDs de respostas completas desta pesquisa (para JOIN eficiente)
        $completedResponseIds = SurveyResponse::where('survey_id', $survey->id)
            ->whereNotNull('completed_at')
            ->pluck('id');

        foreach ($survey->questions as $question) {
            $answers = SurveyAnswer::where('question_id', $question->id)
                ->whereIn('response_id', $completedResponseIds)
                ->get();

            $result = [
                'id'       => $question->id,
                'question' => $question->question,
                'type'     => $question->type,
                'count'    => $answers->count(),
            ];

            // ── Escala 0–10 ──────────────────────────────────────────
            if ($question->type === 'escala') {
                $distribution = array_fill(0, 11, 0);

                foreach ($answers as $answer) {
                    if ($answer->value_scale !== null) {
                        $distribution[(int) $answer->value_scale]++;
                    }
                }

                $total = array_sum($distribution);

                $promoters  = $distribution[9] + $distribution[10];          // 9-10
                $passives   = $distribution[7] + $distribution[8];           // 7-8
                $detractors = array_sum(array_slice($distribution, 0, 7));   // 0-6

                $nps = $total > 0
                    ? round((($promoters / $total) - ($detractors / $total)) * 100)
                    : null;

                $sum = 0;
                foreach ($answers as $a) {
                    if ($a->value_scale !== null) {
                        $sum += (int) $a->value_scale;
                    }
                }

                $result += [
                    'distribution' => array_values($distribution),
                    'nps'          => $nps,
                    'promoters'    => $promoters,
                    'passives'     => $passives,
                    'detractors'   => $detractors,
                    'total'        => $total,
                    'average'      => $total > 0 ? round($sum / $total, 1) : null,
                ];

            // ── Múltipla escolha ─────────────────────────────────────
            } elseif ($question->type === 'multipla_escolha') {
                $options = $question->options ?? [];
                $counts  = array_fill_keys($options, 0);

                foreach ($answers as $answer) {
                    if ($answer->value_option !== null && array_key_exists($answer->value_option, $counts)) {
                        $counts[$answer->value_option]++;
                    }
                }

                $total       = array_sum($counts);
                $percentages = [];
                foreach ($counts as $opt => $count) {
                    $percentages[$opt] = $total > 0 ? round(($count / $total) * 100, 1) : 0;
                }

                // Ordena por count desc para visualização
                arsort($counts);

                $result += [
                    'options'     => $options,
                    'counts'      => $counts,
                    'percentages' => $percentages,
                    'total'       => $total,
                ];

            // ── Texto livre ───────────────────────────────────────────
            } elseif ($question->type === 'texto_livre') {
                $texts = $answers->pluck('value_text')->filter()->values()->toArray();

                $wordFreq  = [];
                $stopWords = [
                    'de', 'a', 'o', 'e', 'do', 'da', 'em', 'um', 'para', 'com',
                    'que', 'os', 'as', 'dos', 'das', 'no', 'na', 'por', 'se',
                    'ao', 'mais', 'mas', 'não', 'foi', 'ser', 'tem', 'uma', 'isso',
                    'este', 'esta', 'são', 'está', 'como', 'muito', 'também', 'ele',
                    'ela', 'eu', 'você', 'nós', 'eles', 'elas', 'seu', 'sua', 'seus',
                    'suas', 'meu', 'minha', 'ter', 'tudo', 'já', 'até', 'pelo', 'pela',
                    'entre', 'quando', 'depois', 'antes', 'ainda', 'cada', 'todo',
                    'toda', 'todos', 'todas', 'aqui', 'aí', 'lá', 'aquele', 'aquela',
                    'esse', 'essa', 'esses', 'essas', 'num', 'numa', 'pelos', 'pelas',
                    'nos', 'nas', 'me', 'te', 'lhe', 'nos', 'vos', 'lhes',
                ];

                foreach ($texts as $text) {
                    $words = preg_split('/[\s,\.!?;:()\[\]"\']+/u', mb_strtolower($text));
                    foreach ((array) $words as $word) {
                        $word = preg_replace('/[^a-záéíóúàâêôãõç]/u', '', (string) $word);
                        if (mb_strlen($word) > 3 && ! in_array($word, $stopWords, true)) {
                            $wordFreq[$word] = ($wordFreq[$word] ?? 0) + 1;
                        }
                    }
                }

                arsort($wordFreq);
                $wordFreq = array_slice($wordFreq, 0, 40, true);

                $result += [
                    'texts'     => $texts,
                    'word_freq' => $wordFreq,
                    'total'     => count($texts),
                ];
            }

            $results[] = $result;
        }

        return $results;
    }

    // ──────────────────────────────────────────────────────────────────
    #[Computed]
    public function dailyResponses(): array
    {
        return SurveyResponse::where('survey_id', $this->surveyId)
            ->whereNotNull('completed_at')
            ->selectRaw("DATE(completed_at) as date, COUNT(*) as total")
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();
    }

    // ──────────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.surveys.results');
    }
}
