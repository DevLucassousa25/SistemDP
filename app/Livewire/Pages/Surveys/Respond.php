<?php

namespace App\Livewire\Pages\Surveys;

use App\Livewire\SecureComponent;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class Respond extends SecureComponent
{
    // ──────────────────────────────────────────────────────────────────
    // Estado da página
    // ──────────────────────────────────────────────────────────────────

    #[Locked]
    public int $surveyId;

    /**
     * Respostas indexadas por question_id.
     * Escala   → inteiro (0-10) ou null
     * Múltipla → string (opção escolhida) ou null
     * Texto    → string ou null
     *
     * @var array<int, int|string|null>
     */
    public array $answers = [];

    /** Pesquisa enviada com sucesso */
    public bool $submitted = false;

    /** Usuário não tem acesso a esta pesquisa */
    public bool $semAcesso = false;
    public string $semAcessoMotivo = '';

    /** Usuário já respondeu */
    public bool $jaRespondeu = false;

    // ──────────────────────────────────────────────────────────────────
    // Mount
    // ──────────────────────────────────────────────────────────────────

    public function mount(int $id): void
    {
        $this->requireAuth();

        $survey = Survey::with([
            'questions' => fn ($q) => $q->orderBy('order')->orderBy('id'),
        ])->find($id);

        // Pesquisa não existe
        if (! $survey) {
            $this->semAcesso       = true;
            $this->semAcessoMotivo = 'Pesquisa não encontrada.';
            return;
        }

        $this->surveyId = $survey->id;
        $user = Auth::user();

        // Verifica acesso ao público-alvo
        if (! $survey->podeResponder($user)) {
            $this->semAcesso       = true;
            $this->semAcessoMotivo = match (true) {
                $survey->status === 'rascunho'   => 'Esta pesquisa ainda não foi publicada.',
                $survey->status === 'encerrada'  => 'Esta pesquisa foi encerrada.',
                ! $survey->isAtivaHoje()         => 'Esta pesquisa não está disponível no momento.',
                default                          => 'Você não faz parte do público-alvo desta pesquisa.',
            };
            return;
        }

        // Já respondeu?
        if ($survey->jaRespondeu($user->id)) {
            $this->jaRespondeu = true;
            return;
        }

        // Inicializa o array de respostas com null para cada pergunta
        foreach ($survey->questions as $question) {
            $this->answers[$question->id] = null;
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Dados computados
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function survey(): Survey
    {
        return Survey::with([
            'questions' => fn ($q) => $q->orderBy('order')->orderBy('id'),
            'creator:id,name',
        ])->findOrFail($this->surveyId);
    }

    #[Computed]
    public function progresso(): array
    {
        $perguntas    = $this->survey->questions;
        $total        = $perguntas->count();
        $respondidas  = collect($this->answers)
            ->filter(fn ($v) => $v !== null && $v !== '')
            ->count();

        return [
            'total'       => $total,
            'respondidas' => $respondidas,
            'percent'     => $total > 0 ? (int) round(($respondidas / $total) * 100) : 0,
        ];
    }

    #[Computed]
    public function podeEnviar(): bool
    {
        foreach ($this->survey->questions as $question) {
            if (! $question->required) {
                continue;
            }

            $resposta = $this->answers[$question->id] ?? null;

            if ($resposta === null || $resposta === '') {
                return false;
            }
        }

        return true;
    }

    // ──────────────────────────────────────────────────────────────────
    // Interação com escala
    // ──────────────────────────────────────────────────────────────────

    public function selecionarEscala(int $questionId, int $valor): void
    {
        if ($valor < 0 || $valor > 10) {
            return;
        }

        $this->answers[$questionId] = $valor;
        unset($this->progresso, $this->podeEnviar);
    }

    public function selecionarOpcao(int $questionId, string $valor): void
    {
        $this->answers[$questionId] = $valor;
        unset($this->progresso, $this->podeEnviar);
    }

    // ──────────────────────────────────────────────────────────────────
    // Enviar resposta
    // ──────────────────────────────────────────────────────────────────

    public function enviar(): void
    {
        $this->requireAuth();

        $user   = Auth::user();
        $survey = $this->survey;

        // Double-check de acesso
        if (! $survey->podeResponder($user)) {
            $this->semAcesso       = true;
            $this->semAcessoMotivo = 'Esta pesquisa não está mais disponível.';
            return;
        }

        // Double-check de dupla resposta
        if ($survey->jaRespondeu($user->id)) {
            $this->jaRespondeu = true;
            return;
        }

        // Valida perguntas obrigatórias
        $erros = [];
        foreach ($survey->questions as $question) {
            if (! $question->required) {
                continue;
            }

            $resposta = $this->answers[$question->id] ?? null;

            if ($resposta === null || $resposta === '') {
                $erros[] = $question->id;
            }
        }

        if (! empty($erros)) {
            $this->dispatch('toast', type: 'warning', message: 'Responda todas as perguntas obrigatórias antes de enviar.');
            return;
        }

        // Persiste em transação
        DB::transaction(function () use ($survey, $user) {
            $response = SurveyResponse::create([
                'survey_id'    => $survey->id,
                'user_id'      => $user->id,
                'completed_at' => now(),
            ]);

            foreach ($survey->questions as $question) {
                $valor = $this->answers[$question->id] ?? null;

                if ($valor === null || $valor === '') {
                    continue; // pergunta não obrigatória sem resposta
                }

                $answerData = [
                    'response_id' => $response->id,
                    'question_id' => $question->id,
                ];

                match ($question->type) {
                    'escala'           => $answerData['value_scale']  = (int) $valor,
                    'multipla_escolha' => $answerData['value_option'] = (string) $valor,
                    'texto_livre'      => $answerData['value_text']   = $this->sanitize((string) $valor),
                };

                SurveyAnswer::create($answerData);
            }
        });

        $this->submitted = true;
        unset($this->progresso, $this->podeEnviar);
    }

    // ──────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.surveys.respond')
            ->title($this->semAcesso || $this->jaRespondeu
                ? 'Pesquisa'
                : ($this->submitted ? 'Resposta enviada' : $this->survey->title));
    }
}
