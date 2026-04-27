<?php

namespace App\Livewire\Components\Ui\Modal;

use App\Livewire\SecureComponent;
use App\Models\Survey;
use App\Models\SurveyResponse;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class SurveyRespondentsModal extends SecureComponent
{
    // ──────────────────────────────────────────────────────────────────
    // Estado do modal
    // ──────────────────────────────────────────────────────────────────

    public bool $modalOpen = false;

    #[Locked]
    public ?int $surveyId = null;

    public string $surveyTitle = '';

    public bool $isAnonymous = false;

    /** ID da resposta cujas perguntas estão expandidas (null = nenhuma) */
    public ?int $expandedResponseId = null;

    // ──────────────────────────────────────────────────────────────────
    // Abertura via evento
    // ──────────────────────────────────────────────────────────────────

    #[On('abrir-modal-respondentes')]
    public function abrirModal(int $surveyId): void
    {
        $survey = Survey::with(['creator.accessProfile:id,slug'])->findOrFail($surveyId);

        $this->autorizarSobre($survey);

        $this->surveyId          = $survey->id;
        $this->surveyTitle       = $survey->title;
        $this->isAnonymous       = (bool) $survey->is_anonymous;
        $this->expandedResponseId = null;
        $this->modalOpen         = true;

        unset($this->responses);
    }

    public function fecharModal(): void
    {
        $this->modalOpen          = false;
        $this->surveyId           = null;
        $this->surveyTitle        = '';
        $this->isAnonymous        = false;
        $this->expandedResponseId = null;

        unset($this->responses);
    }

    // ──────────────────────────────────────────────────────────────────
    // Expandir / recolher respostas individuais
    // ──────────────────────────────────────────────────────────────────

    public function toggleExpand(int $responseId): void
    {
        if ($this->isAnonymous) {
            return;
        }

        $this->expandedResponseId = ($this->expandedResponseId === $responseId)
            ? null
            : $responseId;
    }

    // ──────────────────────────────────────────────────────────────────
    // Autorização
    // ──────────────────────────────────────────────────────────────────

    /**
     * Mesma lógica de autorização do SurveyQuestionsModal:
     *  - Pesquisa criada por Gerente → somente o próprio criador.
     *  - Pesquisa criada por RH/Admin → qualquer RH/Admin.
     */
    private function autorizarSobre(Survey $survey): void
    {
        $user    = Auth::user();
        $creator = $survey->relationLoaded('creator')
            ? $survey->creator
            : \App\Models\User::with('accessProfile:id,slug')->find($survey->created_by);

        if ($creator && $creator->isGerente()) {
            if ($user->id !== $survey->created_by) {
                abort(403, 'Apenas o gerente que criou esta pesquisa pode ver os respondentes.');
            }
            return;
        }

        if (! $user->isRhOuDp()) {
            abort(403, 'Sem permissão para visualizar respondentes.');
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Dados computados
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function responses()
    {
        if (! $this->surveyId) {
            return collect();
        }

        $query = SurveyResponse::where('survey_id', $this->surveyId)
            ->whereNotNull('completed_at')
            ->orderByDesc('completed_at');

        // Pesquisa não-anônima: carrega usuário e respostas com perguntas
        if (! $this->isAnonymous) {
            $query->with([
                'user:id,name,email',
                'answers.question:id,question,type,order',
            ]);
        }

        return $query->get();
    }

    // ──────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.components.ui.modal.survey-respondents-modal');
    }
}
