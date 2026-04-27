<?php

namespace App\Livewire\Components\Ui\Modal;

use App\Livewire\SecureComponent;
use App\Models\Survey;
use App\Models\SurveyTemplate;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;

class SurveyTemplatesModal extends SecureComponent
{
    // ──────────────────────────────────────────────────────────────────
    // Modo:
    //   'select' → biblioteca (escolher template para nova pesquisa)
    //   'save'   → salvar pesquisa existente como template
    //   'form'   → criar / editar template do zero
    // ──────────────────────────────────────────────────────────────────

    public bool $modalOpen = false;

    public string $mode = 'select';

    // ── Modo select ───────────────────────────────────────────────────
    public string $search         = '';
    public string $categoryFilter = 'todos';

    // ── Modo save (salvar a partir de uma pesquisa existente) ─────────
    public ?int $saveSurveyId = null;

    public string $templateName        = '';
    public string $templateDescription = '';
    public string $templateCategory    = 'custom';

    // ── Modo form (criar / editar template do zero) ───────────────────
    public ?int $editingTemplateId = null;   // null = criando novo

    public string $formName        = '';
    public string $formDescription = '';
    public string $formCategory    = 'custom';

    /** Perguntas do template em edição: array de objetos */
    public array $formQuestions = [];

    // Builder de pergunta inline
    public bool   $showQuestionForm    = false;
    public ?int   $editingQuestionIndex = null;
    public string $qText               = '';
    public string $qType               = 'escala'; // escala | multipla_escolha | texto_livre
    public bool   $qRequired           = true;
    /** @var array<string> */
    public array  $qOptions            = ['', ''];

    // ──────────────────────────────────────────────────────────────────
    // Eventos de abertura
    // ──────────────────────────────────────────────────────────────────

    #[On('abrir-modal-templates')]
    public function abrirParaSelecionar(): void
    {
        $this->requireCriador();
        $this->resetTudo();
        $this->mode      = 'select';
        $this->modalOpen = true;
        unset($this->templates);
    }

    #[On('abrir-salvar-template')]
    public function abrirParaSalvar(int $surveyId): void
    {
        $survey = Survey::with(['creator.accessProfile:id,slug'])->findOrFail($surveyId);
        $this->autorizarSobre($survey);

        $this->resetTudo();
        $this->mode                = 'save';
        $this->saveSurveyId        = $surveyId;
        $this->templateName        = $survey->title;
        $this->templateDescription = $survey->description ?? '';
        $this->templateCategory    = 'custom';
        $this->modalOpen           = true;

        $this->resetErrorBag();
    }

    // ──────────────────────────────────────────────────────────────────
    // Modo form: criar / editar template
    // ──────────────────────────────────────────────────────────────────

    public function abrirCriarTemplate(): void
    {
        $this->requireCriador();
        $this->resetForm();
        $this->editingTemplateId = null;
        $this->mode              = 'form';
    }

    public function editarTemplate(int $templateId): void
    {
        $this->requireCriador();

        $template = SurveyTemplate::findOrFail($templateId);

        if ($template->is_system) {
            $this->dispatch('toast', type: 'error', message: 'Templates do sistema não podem ser editados.');
            return;
        }

        $user = Auth::user();
        if ($template->created_by !== $user->id && ! $user->isRhOuDp()) {
            abort(403);
        }

        $this->editingTemplateId = $templateId;
        $this->formName          = $template->name;
        $this->formDescription   = $template->description ?? '';
        $this->formCategory      = $template->category;

        // Garante que cada pergunta tem o campo 'options' como array (nunca null)
        $this->formQuestions = collect($template->questions ?? [])
            ->map(fn ($q) => array_merge(['options' => []], $q))
            ->values()
            ->toArray();

        $this->showQuestionForm    = false;
        $this->editingQuestionIndex = null;
        $this->mode                = 'form';

        $this->resetErrorBag();
        unset($this->templates);
    }

    public function voltarParaBiblioteca(): void
    {
        $this->resetForm();
        $this->mode = 'select';
        unset($this->templates);
    }

    // ──────────────────────────────────────────────────────────────────
    // Builder de perguntas (modo form)
    // ──────────────────────────────────────────────────────────────────

    public function abrirFormularioPergunta(): void
    {
        $this->resetQForm();
        $this->showQuestionForm = true;
    }

    public function cancelarFormPergunta(): void
    {
        $this->showQuestionForm     = false;
        $this->editingQuestionIndex = null;
        $this->resetQForm();
    }

    public function editarPerguntaTemplate(int $index): void
    {
        $q = $this->formQuestions[$index] ?? null;
        if (! $q) return;

        $this->editingQuestionIndex = $index;
        $this->qText                = $q['question'];
        $this->qType                = $q['type'];
        $this->qRequired            = (bool) $q['required'];

        $opts          = $q['options'] ?? [];
        $this->qOptions = (is_array($opts) && count($opts) >= 2) ? $opts : ['', ''];

        $this->showQuestionForm = true;
        $this->resetErrorBag();
    }

    public function salvarPerguntaTemplate(): void
    {
        $rules = [
            'qText' => 'required|string|min:3|max:500',
            'qType' => 'required|in:escala,multipla_escolha,texto_livre',
        ];
        $messages = [
            'qText.required' => 'A pergunta é obrigatória.',
            'qText.min'      => 'A pergunta deve ter ao menos 3 caracteres.',
            'qType.in'       => 'Tipo inválido.',
        ];

        if ($this->qType === 'multipla_escolha') {
            $rules['qOptions']   = 'required|array|min:2';
            $rules['qOptions.*'] = 'required|string|min:1';
            $messages['qOptions.min']        = 'Adicione ao menos 2 opções.';
            $messages['qOptions.*.required'] = 'Preencha todas as opções.';
        }

        $this->validate($rules, $messages);

        $pergunta = [
            'question' => trim($this->qText),
            'type'     => $this->qType,
            'options'  => $this->qType === 'multipla_escolha'
                ? array_values(array_filter(array_map('trim', $this->qOptions)))
                : [],
            'required' => $this->qRequired,
            'order'    => 0, // recalculado abaixo
        ];

        if ($this->editingQuestionIndex !== null) {
            $this->formQuestions[$this->editingQuestionIndex] = $pergunta;
        } else {
            $this->formQuestions[] = $pergunta;
        }

        // Recalcula orders
        $this->recalcularOrdens();

        $this->showQuestionForm     = false;
        $this->editingQuestionIndex = null;
        $this->resetQForm();
    }

    public function removerPerguntaTemplate(int $index): void
    {
        array_splice($this->formQuestions, $index, 1);
        $this->formQuestions = array_values($this->formQuestions);
        $this->recalcularOrdens();
    }

    public function moverPerguntaCima(int $index): void
    {
        if ($index <= 0) return;
        [$this->formQuestions[$index - 1], $this->formQuestions[$index]]
            = [$this->formQuestions[$index], $this->formQuestions[$index - 1]];
        $this->recalcularOrdens();
    }

    public function moverPerguntaBaixo(int $index): void
    {
        $last = count($this->formQuestions) - 1;
        if ($index >= $last) return;
        [$this->formQuestions[$index], $this->formQuestions[$index + 1]]
            = [$this->formQuestions[$index + 1], $this->formQuestions[$index]];
        $this->recalcularOrdens();
    }

    public function adicionarOpcao(): void
    {
        $this->qOptions[] = '';
    }

    public function removerOpcao(int $i): void
    {
        if (count($this->qOptions) > 2) {
            array_splice($this->qOptions, $i, 1);
            $this->qOptions = array_values($this->qOptions);
        }
    }

    private function recalcularOrdens(): void
    {
        foreach ($this->formQuestions as $i => &$q) {
            $q['order'] = $i + 1;
        }
        unset($q);
    }

    // ──────────────────────────────────────────────────────────────────
    // Salvar template (criar ou editar)
    // ──────────────────────────────────────────────────────────────────

    public function salvarTemplate(): void
    {
        $this->requireCriador();

        $this->validate([
            'formName'        => 'required|string|min:3|max:150',
            'formDescription' => 'nullable|string|max:500',
            'formCategory'    => 'required|in:nps,clima,feedback,rh,custom',
        ], [
            'formName.required' => 'Informe um nome para o template.',
            'formName.min'      => 'O nome deve ter ao menos 3 caracteres.',
            'formCategory.in'   => 'Categoria inválida.',
        ]);

        if (empty($this->formQuestions)) {
            $this->addError('formQuestions', 'Adicione ao menos uma pergunta ao template.');
            return;
        }

        $payload = [
            'name'        => trim($this->formName),
            'description' => trim($this->formDescription) ?: null,
            'category'    => $this->formCategory,
            'questions'   => $this->formQuestions,
            'is_system'   => false,
        ];

        if ($this->editingTemplateId) {
            $template = SurveyTemplate::findOrFail($this->editingTemplateId);

            $user = Auth::user();
            if ($template->created_by !== $user->id && ! $user->isRhOuDp()) {
                abort(403);
            }

            $template->update($payload);
            $this->dispatch('toast', type: 'success', message: 'Template atualizado com sucesso!');
        } else {
            $payload['created_by'] = Auth::id();
            SurveyTemplate::create($payload);
            $this->dispatch('toast', type: 'success', message: 'Template criado com sucesso!');
        }

        $this->resetForm();
        $this->mode = 'select';
        unset($this->templates);
    }

    // ──────────────────────────────────────────────────────────────────
    // Ação: usar template selecionado
    // ──────────────────────────────────────────────────────────────────

    public function usarTemplate(int $templateId): void
    {
        $this->requireCriador();

        $template = SurveyTemplate::findOrFail($templateId);

        if (! $template->is_system && $template->created_by !== Auth::id()) {
            if (! Auth::user()->isRhOuDp()) {
                abort(403);
            }
        }

        $this->dispatch('template-selecionado',
            title:     $template->name,
            questions: $template->questions,
        );

        $this->fecharModal();
    }

    // ──────────────────────────────────────────────────────────────────
    // Ação: salvar pesquisa como template (modo save)
    // ──────────────────────────────────────────────────────────────────

    public function salvarComoTemplate(): void
    {
        $this->requireCriador();

        if (! $this->saveSurveyId) {
            $this->dispatch('toast', type: 'error', message: 'Pesquisa não encontrada. Tente novamente.');
            return;
        }

        $this->validate([
            'templateName'        => 'required|string|min:3|max:150',
            'templateDescription' => 'nullable|string|max:500',
            'templateCategory'    => 'required|in:nps,clima,feedback,rh,custom',
        ], [
            'templateName.required' => 'Informe um nome para o template.',
            'templateName.min'      => 'O nome deve ter ao menos 3 caracteres.',
            'templateCategory.in'   => 'Categoria inválida.',
        ]);

        $survey = Survey::with('questions')->findOrFail($this->saveSurveyId);

        $questions = $survey->questions->map(fn ($q) => [
            'question' => $q->question,
            'type'     => $q->type,
            'options'  => $q->options ?? [],
            'required' => $q->required,
            'order'    => $q->order,
        ])->values()->toArray();

        SurveyTemplate::create([
            'name'        => trim($this->templateName),
            'description' => trim($this->templateDescription) ?: null,
            'category'    => $this->templateCategory,
            'questions'   => $questions,
            'created_by'  => Auth::id(),
            'is_system'   => false,
        ]);

        $this->dispatch('toast', type: 'success', message: 'Template salvo com sucesso!');
        $this->fecharModal();
    }

    // ──────────────────────────────────────────────────────────────────
    // Ação: excluir template personalizado
    // ──────────────────────────────────────────────────────────────────

    public function excluirTemplate(int $templateId): void
    {
        $template = SurveyTemplate::findOrFail($templateId);

        if ($template->is_system) {
            abort(403, 'Templates do sistema não podem ser excluídos.');
        }

        $user = Auth::user();
        if ($template->created_by !== $user->id && ! $user->isRhOuDp()) {
            abort(403);
        }

        $template->delete();
        $this->dispatch('toast', type: 'success', message: 'Template excluído.');
        unset($this->templates);
    }

    // ──────────────────────────────────────────────────────────────────
    // Fechar modal
    // ──────────────────────────────────────────────────────────────────

    public function fecharModal(): void
    {
        $this->resetTudo();
        $this->modalOpen = false;
        unset($this->templates);
    }

    // ──────────────────────────────────────────────────────────────────
    // Autorização
    // ──────────────────────────────────────────────────────────────────

    private function requireCriador(): void
    {
        if (! Auth::user()?->podeGerenciarPesquisas()) {
            abort(403, 'Sem permissão para gerenciar pesquisas.');
        }
    }

    private function autorizarSobre(Survey $survey): void
    {
        $user    = Auth::user();
        $creator = $survey->relationLoaded('creator')
            ? $survey->creator
            : \App\Models\User::with('accessProfile:id,slug')->find($survey->created_by);

        if ($creator && $creator->isGerente()) {
            if ($user->id !== $survey->created_by) {
                abort(403, 'Apenas o gerente criador pode usar esta ação.');
            }
            return;
        }

        if (! $user->isRhOuDp()) {
            abort(403, 'Sem permissão.');
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Dados computados
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function templates()
    {
        $user  = Auth::user();
        $query = SurveyTemplate::with('creator:id,name');

        $query->where(function ($q) use ($user) {
            $q->where('is_system', true);

            if ($user->isRhOuDp()) {
                $q->orWhere(function ($q2) {
                    $q2->where('is_system', false)
                       ->whereHas('creator', fn ($q3) => $q3->whereHas(
                           'accessProfile', fn ($q4) => $q4->whereIn('slug', ['rh', 'dp', 'admin'])
                       ));
                });
            } else {
                $q->orWhere(function ($q2) use ($user) {
                    $q2->where('is_system', false)
                       ->where('created_by', $user->id);
                });
            }
        });

        if ($this->categoryFilter !== 'todos') {
            $query->where('category', $this->categoryFilter);
        }

        if (trim($this->search) !== '') {
            $termo = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($termo) {
                $q->where('name', 'ilike', $termo)
                  ->orWhere('description', 'ilike', $termo);
            });
        }

        return $query->orderByDesc('is_system')->orderBy('name')->get();
    }

    // ──────────────────────────────────────────────────────────────────
    // Resets
    // ──────────────────────────────────────────────────────────────────

    private function resetTudo(): void
    {
        $this->mode                = 'select';
        $this->search              = '';
        $this->categoryFilter      = 'todos';
        $this->saveSurveyId        = null;
        $this->templateName        = '';
        $this->templateDescription = '';
        $this->templateCategory    = 'custom';
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->editingTemplateId    = null;
        $this->formName             = '';
        $this->formDescription      = '';
        $this->formCategory         = 'custom';
        $this->formQuestions        = [];
        $this->showQuestionForm     = false;
        $this->editingQuestionIndex = null;
        $this->resetQForm();
        $this->resetErrorBag();
    }

    private function resetQForm(): void
    {
        $this->qText     = '';
        $this->qType     = 'escala';
        $this->qRequired = true;
        $this->qOptions  = ['', ''];
        $this->resetErrorBag();
    }

    // ──────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.components.ui.modal.survey-templates-modal');
    }
}
