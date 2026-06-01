<?php

namespace App\Livewire\Pages\Surveys;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Notifications\PesquisaRespondidaNotification;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyManagerScore;
use App\Models\SurveyResponse;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;

class Respond extends SecureComponent
{
    use EnviaNotificacoes;
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

        $this->dispatch('breadcrumb-set', items: [
            ['label' => 'Pesquisas', 'icon' => 'clipboard-list', 'url' => route('pesquisas')],
            ['label' => $survey->title, 'url' => null],
        ]);
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
    // Localiza o gerente do colaborador pelo departamento
    // ──────────────────────────────────────────────────────────────────

    /**
     * Retorna o ID do gerente ativo do departamento do usuário.
     * Retorna null se o usuário não tiver departamento ou se não houver gerente.
     *
     * Regra: gerente é o User com accessProfile.slug = 'manager'
     *        e mesmo department_id, que esteja ativo.
     */
    private function resolverManagerId(User $user): ?int
    {
        if (! $user->department_id) {
            return null;
        }

        return User::whereHas(
            'accessProfile',
            fn ($q) => $q->where('slug', 'manager')
        )
            ->where('department_id', $user->department_id)
            ->where('is_active', true)
            ->value('id');
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

        // Resolve gerente do respondente (antes da transação, para não bloquear)
        $managerId = $this->resolverManagerId($user);

        // Coleta quais question_ids contribuem para o score do gestor:
        // apenas perguntas com is_manager_evaluation = true E tipo numérico (escala ou múltipla escolha).
        // Texto livre é exibido na view mas não entra no cálculo de pontuação.
        $managerEvalQuestionIds = $survey->questions
            ->where('is_manager_evaluation', true)
            ->whereIn('type', ['escala', 'multipla_escolha'])
            ->pluck('id')
            ->flip()   // transforma em mapa para O(1) lookup
            ->all();

        // Persiste em transação
        DB::transaction(function () use ($survey, $user, $managerId, $managerEvalQuestionIds) {
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

                // RN-PC02 / RN-PC03: se a pergunta avalia o gestor, vincula o manager_id
                if (isset($managerEvalQuestionIds[$question->id]) && $managerId) {
                    $answerData['manager_id'] = $managerId;
                }

                SurveyAnswer::create($answerData);
            }
        });

        // Recalcula score do gerente fora da transação (não precisa bloquear o insert)
        // RN-RG04: recalcular a cada nova submissão
        if ($managerId && ! empty($managerEvalQuestionIds)) {
            SurveyManagerScore::recalcular($survey->id, $managerId);
        }

        // Notifica RH/Admin sobre nova resposta à pesquisa
        $this->notificarRhAdmin(new PesquisaRespondidaNotification($survey));

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
