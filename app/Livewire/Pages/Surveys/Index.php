<?php

namespace App\Livewire\Pages\Surveys;

use App\Livewire\SecureComponent;
use App\Models\Department;
use App\Models\EvaluationCycle;
use App\Models\Survey;
use App\Models\SurveyQuestion;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;

class Index extends SecureComponent
{
    // ──────────────────────────────────────────────────────────────────
    // Filtros
    // ──────────────────────────────────────────────────────────────────
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'todas')]
    public string $statusFilter = 'todas';

    // ──────────────────────────────────────────────────────────────────
    // Modal de criação/edição
    // ──────────────────────────────────────────────────────────────────
    public bool $modalOpen = false;

    #[Locked]
    public ?int $surveyId = null;

    public string $modalMode = 'create'; // create | edit

    #[Validate('required|string|min:3|max:150')]
    public string $title = '';

    #[Validate('nullable|string|max:500')]
    public ?string $description = '';

    #[Validate('required|in:rascunho,ativa,encerrada')]
    public string $status = 'rascunho';

    #[Validate('required|in:todos,departamentos')]
    public string $targetAudience = 'todos';

    /** @var array<int> */
    #[Validate('array')]
    public array $targetDepartmentIds = [];

    public bool $isAnonymous = true;

    #[Validate('nullable|date')]
    public ?string $startDate = null;

    #[Validate('nullable|date|after_or_equal:startDate')]
    public ?string $endDate = null;

    /** Ciclo de avaliação de desempenho vinculado (opcional, apenas RH/Admin) */
    #[Validate('nullable|integer|exists:evaluation_cycles,id')]
    public ?int $evaluationCycleId = null;

    // ──────────────────────────────────────────────────────────────────
    // Templates — perguntas pendentes para criação após salvar
    // ──────────────────────────────────────────────────────────────────

    /** Perguntas vindas de um template, aplicadas ao próximo salvar() */
    public ?array $pendingTemplateQuestions = null;

    // ──────────────────────────────────────────────────────────────────
    // Confirmação de exclusão
    // ──────────────────────────────────────────────────────────────────
    public bool $confirmDelete = false;

    #[Locked]
    public ?int $deleteTargetId = null;

    // ──────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();
    }

    // ──────────────────────────────────────────────────────────────────
    // Helpers de autorização
    // ──────────────────────────────────────────────────────────────────

    /**
     * Exige que o usuário seja RH/Admin ou Gerente de departamento.
     */
    private function requireCriador(): void
    {
        if (! Auth::user()?->podeGerenciarPesquisas()) {
            abort(403, 'Sem permissão para gerenciar pesquisas.');
        }
    }

    /**
     * Verifica se o usuário logado pode editar/excluir/ativar uma pesquisa específica.
     *
     * Regra:
     *  - Pesquisa criada por Gerente → somente o próprio gerente criador pode gerir.
     *  - Pesquisa criada por RH/Admin → qualquer RH/Admin pode gerir.
     */
    private function podeGerir(Survey $survey): bool
    {
        $user    = Auth::user();
        $creator = $survey->relationLoaded('creator')
            ? $survey->creator
            : \App\Models\User::with('accessProfile:id,slug')->find($survey->created_by);

        // Pesquisa criada por gerente: só o criador pode gerir
        if ($creator && $creator->isGerente()) {
            return $user->id === $survey->created_by;
        }

        // Pesquisa criada por RH/Admin: qualquer RH/Admin pode gerir
        return $user->isRhOuDp();
    }

    // ──────────────────────────────────────────────────────────────────
    protected function messages(): array
    {
        return [
            'title.required'             => 'Informe um título para a pesquisa.',
            'title.min'                  => 'O título deve ter ao menos 3 caracteres.',
            'title.max'                  => 'O título pode ter no máximo 150 caracteres.',
            'description.max'            => 'A descrição pode ter no máximo 500 caracteres.',
            'status.in'                  => 'Status inválido.',
            'targetAudience.in'          => 'Público-alvo inválido.',
            'endDate.after_or_equal'     => 'A data final não pode ser anterior à inicial.',
        ];
    }

    // ──────────────────────────────────────────────────────────────────
    // Filtros
    // ──────────────────────────────────────────────────────────────────
    public function limparFiltros(): void
    {
        $this->search       = '';
        $this->statusFilter = 'todas';
    }

    // ──────────────────────────────────────────────────────────────────
    // Modal
    // ──────────────────────────────────────────────────────────────────
    public function abrirModal(): void
    {
        $this->requireCriador();
        $this->resetForm();
        $this->modalMode = 'create';

        // Gerente: força público-alvo para o seu departamento
        $user = Auth::user();
        if ($user->isGerente()) {
            $this->targetAudience      = 'departamentos';
            $this->targetDepartmentIds = $user->department_id
                ? [(int) $user->department_id]
                : [];
        }

        $this->modalOpen = true;
    }

    public function editar(int $id): void
    {
        $this->requireCriador();

        $survey = Survey::findOrFail($id);

        if (! $this->podeGerir($survey)) {
            abort(403, 'Você não tem permissão para editar esta pesquisa.');
        }

        $this->surveyId            = $survey->id;
        $this->title               = $survey->title;
        $this->description         = $survey->description ?? '';
        $this->status              = $survey->status;
        $this->targetAudience      = $survey->target_audience;
        $this->targetDepartmentIds = $survey->target_department_ids ?? [];
        $this->isAnonymous         = (bool) $survey->is_anonymous;
        $this->startDate           = $survey->start_date?->format('Y-m-d');
        $this->endDate             = $survey->end_date?->format('Y-m-d');
        $this->evaluationCycleId   = $survey->evaluation_cycle_id;

        $this->modalMode = 'edit';
        $this->modalOpen = true;
        $this->resetErrorBag();
    }

    public function fecharModal(): void
    {
        $this->modalOpen = false;
        $this->resetForm();
    }

    // ── Ciclos de avaliação disponíveis (para vincular surveys) ───────
    #[Computed]
    public function availableEvaluationCycles(): Collection
    {
        try {
            return EvaluationCycle::whereIn('status', ['draft', 'active'])
                ->orderByDesc('created_at')
                ->get(['id', 'name', 'status', 'start_date', 'end_date']);
        } catch (\Throwable) {
            return collect();
        }
    }

    private function resetForm(): void
    {
        $this->surveyId                 = null;
        $this->title                    = '';
        $this->description              = '';
        $this->status                   = 'rascunho';
        $this->targetAudience           = 'todos';
        $this->targetDepartmentIds      = [];
        $this->isAnonymous              = true;
        $this->startDate                = null;
        $this->endDate                  = null;
        $this->evaluationCycleId        = null;
        $this->pendingTemplateQuestions = null;
        $this->resetErrorBag();
    }

    // ──────────────────────────────────────────────────────────────────
    // Template: recebe o template selecionado e pré-preenche o form
    // ──────────────────────────────────────────────────────────────────

    #[On('template-selecionado')]
    public function aplicarTemplate(string $title, array $questions): void
    {
        $this->requireCriador();

        // Preenche o título se estiver vazio; senão mantém o que o usuário digitou
        if (trim($this->title) === '') {
            $this->title = $title;
        }

        $this->pendingTemplateQuestions = $questions;

        // Abre o modal de criação se ainda não estiver aberto
        if (! $this->modalOpen) {
            $this->modalMode = 'create';

            $user = Auth::user();
            if ($user->isGerente()) {
                $this->targetAudience      = 'departamentos';
                $this->targetDepartmentIds = $user->department_id
                    ? [(int) $user->department_id]
                    : [];
            }

            $this->modalOpen = true;
        }
    }

    public function salvar(): void
    {
        $this->requireCriador();
        $this->validate();

        $user = Auth::user();

        // Gerente: força sempre o seu próprio departamento como público-alvo
        if ($user->isGerente()) {
            $targetAudience      = 'departamentos';
            $targetDepartmentIds = $user->department_id
                ? [(int) $user->department_id]
                : [];
        } else {
            $targetAudience      = $this->targetAudience;
            $targetDepartmentIds = $targetAudience === 'departamentos'
                ? array_values(array_unique(array_map('intval', $this->targetDepartmentIds)))
                : null;
        }

        // Apenas RH/Admin podem vincular surveys a ciclos de avaliação
        $evalCycleId = $user->isRhOuDp() ? ($this->evaluationCycleId ?: null) : null;

        $payload = [
            'title'                 => trim($this->title),
            'description'           => $this->description ? trim($this->description) : null,
            'status'                => $this->status,
            'target_audience'       => $targetAudience,
            'target_department_ids' => $targetDepartmentIds,
            'is_anonymous'          => $this->isAnonymous,
            'start_date'            => $this->startDate ?: null,
            'end_date'              => $this->endDate ?: null,
            'evaluation_cycle_id'   => $evalCycleId,
        ];

        if ($this->modalMode === 'edit' && $this->surveyId) {
            $survey = Survey::findOrFail($this->surveyId);

            if (! $this->podeGerir($survey)) {
                abort(403);
            }

            $survey->update($payload);
            $this->dispatch('toast', type: 'success', message: 'Pesquisa atualizada com sucesso.');
        } else {
            $payload['created_by'] = $user->id;
            $survey = Survey::create($payload);

            // Se veio de um template, cria as perguntas automaticamente
            if (! empty($this->pendingTemplateQuestions)) {
                foreach ($this->pendingTemplateQuestions as $index => $q) {
                    SurveyQuestion::create([
                        'survey_id' => $survey->id,
                        'question'  => $q['question'],
                        'type'      => $q['type'],
                        'options'   => $q['options'] ?? null,
                        'required'  => $q['required'] ?? true,
                        'order'     => $q['order'] ?? ($index + 1),
                    ]);
                }
                $this->dispatch('toast', type: 'success', message: 'Pesquisa criada com ' . count($this->pendingTemplateQuestions) . ' perguntas do template.');
            } else {
                $this->dispatch('toast', type: 'success', message: 'Pesquisa criada com sucesso.');
            }
        }

        $this->fecharModal();
        unset($this->surveys, $this->stats);
    }

    // ──────────────────────────────────────────────────────────────────
    // Ações rápidas
    // ──────────────────────────────────────────────────────────────────
    public function ativar(int $id): void
    {
        $this->requireCriador();

        $survey = Survey::findOrFail($id);

        if (! $this->podeGerir($survey)) {
            abort(403);
        }

        $survey->update(['status' => 'ativa']);
        $this->dispatch('toast', type: 'success', message: 'Pesquisa ativada.');
        unset($this->surveys, $this->stats);
    }

    public function encerrar(int $id): void
    {
        $this->requireCriador();

        $survey = Survey::findOrFail($id);

        if (! $this->podeGerir($survey)) {
            abort(403);
        }

        $survey->update(['status' => 'encerrada']);
        $this->dispatch('toast', type: 'success', message: 'Pesquisa encerrada.');
        unset($this->surveys, $this->stats);
    }

    public function confirmarExclusao(int $id): void
    {
        $this->requireCriador();

        $survey = Survey::findOrFail($id);

        if (! $this->podeGerir($survey)) {
            abort(403);
        }

        $this->deleteTargetId = $id;
        $this->confirmDelete  = true;
    }

    public function cancelarExclusao(): void
    {
        $this->confirmDelete  = false;
        $this->deleteTargetId = null;
    }

    public function excluir(): void
    {
        $this->requireCriador();

        if (! $this->deleteTargetId) {
            return;
        }

        $survey = Survey::findOrFail($this->deleteTargetId);

        if (! $this->podeGerir($survey)) {
            abort(403);
        }

        $survey->delete();

        $this->confirmDelete  = false;
        $this->deleteTargetId = null;
        $this->dispatch('toast', type: 'success', message: 'Pesquisa excluída.');
        unset($this->surveys, $this->stats);
    }

    /**
     * Abre o modal de salvar como template (dispatch server-side garante propagação).
     */
    public function abrirSalvarTemplate(int $id): void
    {
        $this->requireCriador();

        $survey = Survey::with(['creator.accessProfile:id,slug'])->findOrFail($id);

        if (! $this->podeGerir($survey)) {
            abort(403);
        }

        $this->dispatch('abrir-salvar-template', surveyId: $id);
    }

    public function duplicar(int $id): void
    {
        $this->requireCriador();

        $original = Survey::findOrFail($id);

        if (! $this->podeGerir($original)) {
            abort(403);
        }

        $user = Auth::user();

        // Gerente: força o próprio departamento na cópia
        if ($user->isGerente()) {
            $targetAudience      = 'departamentos';
            $targetDepartmentIds = $user->department_id
                ? [(int) $user->department_id]
                : ($original->target_department_ids ?? []);
        } else {
            $targetAudience      = $original->target_audience;
            $targetDepartmentIds = $original->target_department_ids;
        }

        Survey::create([
            'title'                 => $original->title . ' (cópia)',
            'description'           => $original->description,
            'status'                => 'rascunho',
            'target_audience'       => $targetAudience,
            'target_department_ids' => $targetDepartmentIds,
            'is_anonymous'          => $original->is_anonymous,
            'start_date'            => null,
            'end_date'              => null,
            'created_by'            => $user->id,
        ]);

        $this->dispatch('toast', type: 'success', message: 'Pesquisa duplicada como rascunho.');
        unset($this->surveys, $this->stats);
    }

    // ──────────────────────────────────────────────────────────────────
    // Dados para view
    // ──────────────────────────────────────────────────────────────────
    #[Computed]
    public function surveys()
    {
        $user  = Auth::user();
        $query = Survey::with([
            'creator' => fn ($q) => $q->select('id', 'name', 'access_profile_id'),
            'creator.accessProfile' => fn ($q) => $q->select('id', 'slug'),
        ]);

        if ($user->isRhOuDp()) {
            // RH/Admin vê tudo
        } elseif ($user->isGerente()) {
            // Gerente vê:
            // 1. Todas as pesquisas que ele mesmo criou (qualquer status)
            // 2. Pesquisas ativas criadas por outros e direcionadas ao seu departamento
            $query->where(function ($q) use ($user) {
                // Pesquisas próprias
                $q->where('created_by', $user->id);

                // Pesquisas ativas de outros dirigidas ao seu dept.
                $q->orWhere(function ($q2) use ($user) {
                    $q2->where('status', 'ativa')
                       ->where('created_by', '!=', $user->id)
                       ->where(function ($q3) use ($user) {
                           $q3->where('target_audience', 'todos');

                           if ($user->department_id) {
                               $q3->orWhere(function ($q4) use ($user) {
                                   $q4->where('target_audience', 'departamentos')
                                      ->whereJsonContains(
                                          'target_department_ids',
                                          (int) $user->department_id
                                      );
                               });
                           }
                       });
                });
            });
        } else {
            // Colaborador comum: só pesquisas ativas direcionadas a ele
            $query->where('status', 'ativa')
                  ->where(function ($q) use ($user) {
                      $q->where('target_audience', 'todos');

                      if ($user->department_id) {
                          $q->orWhere(function ($q2) use ($user) {
                              $q2->where('target_audience', 'departamentos')
                                 ->whereJsonContains(
                                     'target_department_ids',
                                     (int) $user->department_id
                                 );
                          });
                      }
                  });
        }

        if ($this->statusFilter !== 'todas') {
            $query->where('status', $this->statusFilter);
        }

        if (trim($this->search) !== '') {
            $termo = '%' . trim($this->search) . '%';
            $query->where(function ($q) use ($termo) {
                $q->where('title', 'ilike', $termo)
                  ->orWhere('description', 'ilike', $termo);
            });
        }

        return $query->orderByRaw("
                CASE status
                    WHEN 'ativa'     THEN 1
                    WHEN 'rascunho'  THEN 2
                    WHEN 'encerrada' THEN 3
                    ELSE 4
                END
            ")
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function stats(): array
    {
        $user = Auth::user();

        if ($user->isRhOuDp()) {
            return [
                'total'      => Survey::count(),
                'ativas'     => Survey::where('status', 'ativa')->count(),
                'rascunho'   => Survey::where('status', 'rascunho')->count(),
                'encerradas' => Survey::where('status', 'encerrada')->count(),
            ];
        }

        if ($user->isGerente()) {
            $proprias = Survey::where('created_by', $user->id);
            return [
                'total'      => $proprias->count(),
                'ativas'     => (clone $proprias)->where('status', 'ativa')->count(),
                'rascunho'   => (clone $proprias)->where('status', 'rascunho')->count(),
                'encerradas' => (clone $proprias)->where('status', 'encerrada')->count(),
            ];
        }

        // Colaborador: conta apenas o que está disponível pra ele
        $total = $this->surveys->count();
        return [
            'total'      => $total,
            'ativas'     => $total,
            'rascunho'   => 0,
            'encerradas' => 0,
        ];
    }

    #[Computed]
    public function departamentos()
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }

    public function render()
    {
        return view('livewire.pages.surveys.index');
    }
}
