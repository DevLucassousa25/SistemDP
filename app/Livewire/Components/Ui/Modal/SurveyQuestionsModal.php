<?php

namespace App\Livewire\Components\Ui\Modal;

use App\Livewire\SecureComponent;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;

class SurveyQuestionsModal extends SecureComponent
{
    // ──────────────────────────────────────────────────────────────────
    // Estado do modal
    // ──────────────────────────────────────────────────────────────────

    public bool $modalOpen = false;

    #[Locked]
    public ?int $surveyId = null;

    public string $surveyTitle = '';

    // ──────────────────────────────────────────────────────────────────
    // Estado do formulário inline de pergunta
    // ──────────────────────────────────────────────────────────────────

    public bool $showForm = false;

    #[Locked]
    public ?int $editingId = null;

    public string $questionText = '';

    public string $questionType = 'escala'; // escala | multipla_escolha | texto_livre

    public bool $questionRequired = true;

    /** Marca a pergunta como sendo de avaliação do gestor do setor */
    public bool $questionIsManagerEval = false;

    /** @var array<string> */
    public array $options = ['', ''];

    // ──────────────────────────────────────────────────────────────────
    // Confirmação de exclusão
    // ──────────────────────────────────────────────────────────────────

    public bool $confirmDelete = false;

    #[Locked]
    public ?int $deleteTargetId = null;

    // ──────────────────────────────────────────────────────────────────
    // Abertura / fechamento
    // ──────────────────────────────────────────────────────────────────

    #[On('abrir-modal-perguntas')]
    public function abrirModal(int $surveyId): void
    {
        $survey = Survey::with(['creator.accessProfile:id,slug'])->findOrFail($surveyId);

        $this->autorizarSobre($survey);

        $this->surveyId    = $survey->id;
        $this->surveyTitle = $survey->title;
        $this->modalOpen   = true;
        $this->showForm    = false;

        $this->resetForm();
    }

    public function fecharModal(): void
    {
        $this->modalOpen    = false;
        $this->surveyId     = null;
        $this->surveyTitle  = '';
        $this->showForm     = false;
        $this->confirmDelete = false;
        $this->deleteTargetId = null;

        $this->resetForm();
        unset($this->perguntas);
    }

    // ──────────────────────────────────────────────────────────────────
    // Formulário de pergunta
    // ──────────────────────────────────────────────────────────────────

    /**
     * Autoriza o usuário logado a gerir as perguntas desta pesquisa.
     *
     * Regra:
     *  - Pesquisa criada por Gerente → somente o próprio gerente criador.
     *  - Pesquisa criada por RH/Admin → qualquer RH/Admin.
     */
    private function autorizarSobre(Survey $survey): void
    {
        $user    = Auth::user();
        $creator = $survey->relationLoaded('creator')
            ? $survey->creator
            : \App\Models\User::with('accessProfile:id,slug')->find($survey->created_by);

        if ($creator && $creator->isGerente()) {
            // Pesquisa do gerente: só o próprio criador
            if ($user->id !== $survey->created_by) {
                abort(403, 'Apenas o gerente que criou esta pesquisa pode gerenciar suas perguntas.');
            }
            return;
        }

        // Pesquisa de RH/Admin: exige RH/Admin
        if (! $user->isRhOuDp()) {
            abort(403, 'Sem permissão para gerenciar perguntas.');
        }
    }

    /**
     * Recarrega a pesquisa e revalida as permissões (usado nas ações CRUD).
     */
    private function verificarPermissao(): void
    {
        $survey = Survey::with(['creator.accessProfile:id,slug'])->findOrFail($this->surveyId);
        $this->autorizarSobre($survey);
    }

    public function abrirFormulario(): void
    {
        $this->verificarPermissao();
        $this->resetForm();
        $this->showForm = true;
    }

    public function cancelarForm(): void
    {
        $this->showForm = false;
        $this->resetForm();
    }

    public function editarPergunta(int $id): void
    {
        $this->verificarPermissao();

        $question = SurveyQuestion::findOrFail($id);

        $this->editingId              = $id;
        $this->questionText           = $question->question;
        $this->questionType           = $question->type;
        $this->questionRequired       = $question->required;
        $this->questionIsManagerEval  = (bool) $question->is_manager_evaluation;

        $opts = $question->options ?? [];
        $this->options = (count($opts) >= 2) ? $opts : ['', ''];

        $this->showForm = true;
        $this->resetErrorBag();
    }

    // ──────────────────────────────────────────────────────────────────
    // Opções (múltipla escolha)
    // ──────────────────────────────────────────────────────────────────

    public function adicionarOpcao(): void
    {
        $this->options[] = '';
    }

    public function removerOpcao(int $index): void
    {
        if (count($this->options) > 2) {
            array_splice($this->options, $index, 1);
            $this->options = array_values($this->options);
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Salvar pergunta
    // ──────────────────────────────────────────────────────────────────

    public function salvarPergunta(): void
    {
        $this->verificarPermissao();

        $rules = [
            'questionText' => 'required|string|min:3|max:500',
            'questionType' => 'required|in:escala,multipla_escolha,texto_livre',
        ];

        $messages = [
            'questionText.required' => 'A pergunta é obrigatória.',
            'questionText.min'      => 'A pergunta deve ter ao menos 3 caracteres.',
            'questionText.max'      => 'A pergunta pode ter no máximo 500 caracteres.',
            'questionType.required' => 'Selecione um tipo de pergunta.',
            'questionType.in'       => 'Tipo de pergunta inválido.',
        ];

        if ($this->questionType === 'multipla_escolha') {
            $rules['options']   = 'required|array|min:2';
            $rules['options.*'] = 'required|string|min:1|max:200';

            $messages['options.required']   = 'Adicione ao menos 2 opções.';
            $messages['options.min']        = 'Adicione ao menos 2 opções.';
            $messages['options.*.required'] = 'Preencha todas as opções.';
            $messages['options.*.min']      = 'Cada opção deve ter ao menos 1 caractere.';
        }

        $this->validate($rules, $messages);

        $payload = [
            'survey_id'              => $this->surveyId,
            'question'               => $this->sanitize($this->questionText),
            'type'                   => $this->questionType,
            'options'                => $this->questionType === 'multipla_escolha'
                ? array_values(array_filter(array_map([$this, 'sanitize'], $this->options)))
                : null,
            'required'               => $this->questionRequired,
            'is_manager_evaluation'  => $this->questionIsManagerEval,
        ];

        if ($this->editingId) {
            SurveyQuestion::findOrFail($this->editingId)->update($payload);
            $this->dispatch('toast', type: 'success', message: 'Pergunta atualizada.');
        } else {
            $maxOrder       = SurveyQuestion::where('survey_id', $this->surveyId)->max('order') ?? 0;
            $payload['order'] = $maxOrder + 1;
            SurveyQuestion::create($payload);
            $this->dispatch('toast', type: 'success', message: 'Pergunta adicionada.');
        }

        $this->showForm = false;
        $this->resetForm();
        unset($this->perguntas);
    }

    // ──────────────────────────────────────────────────────────────────
    // Exclusão
    // ──────────────────────────────────────────────────────────────────

    public function confirmarExclusao(int $id): void
    {
        $this->verificarPermissao();
        $this->deleteTargetId = $id;
        $this->confirmDelete  = true;
    }

    public function cancelarExclusao(): void
    {
        $this->confirmDelete  = false;
        $this->deleteTargetId = null;
    }

    public function excluirPergunta(): void
    {
        $this->verificarPermissao();

        if (! $this->deleteTargetId) {
            return;
        }

        SurveyQuestion::findOrFail($this->deleteTargetId)->delete();

        $this->confirmDelete  = false;
        $this->deleteTargetId = null;

        $this->dispatch('toast', type: 'success', message: 'Pergunta removida.');
        unset($this->perguntas);
    }

    // ──────────────────────────────────────────────────────────────────
    // Reordenação
    // ──────────────────────────────────────────────────────────────────

    public function moverCima(int $id): void
    {
        $this->reordenar($id, 'up');
    }

    public function moverBaixo(int $id): void
    {
        $this->reordenar($id, 'down');
    }

    private function reordenar(int $id, string $direction): void
    {
        $questions = SurveyQuestion::where('survey_id', $this->surveyId)
            ->orderBy('order')
            ->orderBy('id')
            ->get();

        $index = $questions->search(fn ($q) => $q->id === $id);

        if ($direction === 'up' && $index > 0) {
            $vizinho = $questions[$index - 1];
        } elseif ($direction === 'down' && $index < $questions->count() - 1) {
            $vizinho = $questions[$index + 1];
        } else {
            return;
        }

        $atual = $questions[$index];

        $tempOrder = $atual->order;
        $atual->update(['order' => $vizinho->order]);
        $vizinho->update(['order' => $tempOrder]);

        // garante que orders distintos após swap com valores iguais
        if ($atual->order === $vizinho->order) {
            $questions->each(function ($q, $i) {
                $q->update(['order' => $i + 1]);
            });
        }

        unset($this->perguntas);
    }

    // ──────────────────────────────────────────────────────────────────
    // Dados computados
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function perguntas()
    {
        if (! $this->surveyId) {
            return collect();
        }

        return SurveyQuestion::where('survey_id', $this->surveyId)
            ->orderBy('order')
            ->orderBy('id')
            ->get();
    }

    // ──────────────────────────────────────────────────────────────────
    // Reset
    // ──────────────────────────────────────────────────────────────────

    public function toggleManagerEval(int $id): void
    {
        $this->verificarPermissao();
        $q = SurveyQuestion::findOrFail($id);
        $q->update(['is_manager_evaluation' => ! $q->is_manager_evaluation]);
        unset($this->perguntas);
    }

    private function resetForm(): void
    {
        $this->editingId             = null;
        $this->questionText          = '';
        $this->questionType          = 'escala';
        $this->questionRequired      = true;
        $this->questionIsManagerEval = false;
        $this->options               = ['', ''];
        $this->resetErrorBag();
    }

    public function render()
    {
        return view('livewire.components.ui.modal.survey-questions-modal');
    }
}
