<?php

namespace App\Livewire\Pages\Surveys;

use App\Livewire\SecureComponent;
use App\Models\Survey;
use App\Models\SurveyManagerScore;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

/**
 * Ranking de Gerentes baseado nas respostas da Pesquisa de Clima.
 *
 * Acesso exclusivo para DP/RH (RN-RG03).
 * A pontuação é calculada a partir das perguntas com is_manager_evaluation = true (RN-RG01).
 */
class ManagerRanking extends SecureComponent
{
    // ──────────────────────────────────────────────────────────────────
    // Filtros
    // ──────────────────────────────────────────────────────────────────

    #[Url(except: '')]
    public string $search = '';

    /** ID da pesquisa selecionada para filtrar o ranking */
    #[Url(as: 'pesquisa', except: '')]
    public string $surveyFilter = '';

    /** Coluna de ordenação: 'score' | 'responses' | 'name' */
    #[Url(as: 'ordem', except: 'score')]
    public string $sort = 'score';

    /** Direção de ordenação: 'asc' | 'desc' */
    #[Url(as: 'dir', except: 'desc')]
    public string $sortDir = 'desc';

    // ──────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();

        // RN-RG03: somente DP/RH acessa o ranking
        if (! Auth::user()->isRhOuDp()) {
            abort(403, 'Acesso restrito ao DP/RH.');
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Ordenação
    // ──────────────────────────────────────────────────────────────────

    /**
     * Alterna coluna/direção de ordenação.
     * Clicar na mesma coluna inverte a direção; clicar em outra começa em 'desc'.
     */
    public function sortBy(string $column): void
    {
        if ($this->sort === $column) {
            $this->sortDir = $this->sortDir === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sort    = $column;
            $this->sortDir = 'desc';
        }

        unset($this->ranking);
    }

    // ──────────────────────────────────────────────────────────────────
    // Pesquisas disponíveis para filtro (apenas as que têm perguntas de gestor)
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function pesquisasDisponiveis()
    {
        return Survey::whereHas('questions', fn ($q) => $q->where('is_manager_evaluation', true))
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'status']);
    }

    // ──────────────────────────────────────────────────────────────────
    // Ranking
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function ranking()
    {
        $query = SurveyManagerScore::with([
            'manager:id,name,department_id',
            'manager.department:id,name',
            'survey:id,title,status',
        ]);

        // Filtro por pesquisa
        if ($this->surveyFilter !== '') {
            $query->where('survey_id', (int) $this->surveyFilter);
        }

        // Filtro por nome do gerente
        if (trim($this->search) !== '') {
            $termo = '%' . trim($this->search) . '%';
            $query->whereHas('manager', fn ($q) => $q->where('name', 'ilike', $termo));
        }

        // Ordenação por score ou respostas (feita no banco)
        $dir = $this->sortDir === 'asc' ? 'asc' : 'desc';

        match ($this->sort) {
            'responses' => $query->orderBy('total_responses', $dir)
                                 ->orderByDesc('average_score'),
            // 'name' é ordenado em memória abaixo (campo de relacionamento)
            default     => $query->orderBy('average_score', $dir)
                                 ->orderByDesc('total_responses'),
        };

        $results = $query->get();

        // Ordenação por nome em memória (campo do relacionamento)
        if ($this->sort === 'name') {
            $results = $dir === 'asc'
                ? $results->sortBy(fn ($s) => mb_strtolower($s->manager?->name ?? ''))
                : $results->sortByDesc(fn ($s) => mb_strtolower($s->manager?->name ?? ''));

            $results = $results->values();
        }

        return $results;
    }

    // ──────────────────────────────────────────────────────────────────
    // Estatísticas resumidas
    // ──────────────────────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        $base = SurveyManagerScore::query();

        if ($this->surveyFilter !== '') {
            $base->where('survey_id', (int) $this->surveyFilter);
        }

        // Aplica filtro de nome nas estatísticas também
        if (trim($this->search) !== '') {
            $termo = '%' . trim($this->search) . '%';
            $base->whereHas('manager', fn ($q) => $q->where('name', 'ilike', $termo));
        }

        $all     = $base->get(['average_score', 'total_responses']);
        $comNota = $all->whereNotNull('average_score');

        $mediaGeral = $comNota->isNotEmpty()
            ? round($comNota->avg('average_score'), 2)
            : null;

        return [
            'total_gerentes'  => $all->count(),
            'com_nota'        => $comNota->count(),
            'sem_nota'        => $all->count() - $comNota->count(),
            'media_geral'     => $mediaGeral,
            'total_respostas' => $all->sum('total_responses'),
            // Distribuição por nível
            'excelente'       => $comNota->filter(fn ($s) => $s->average_score >= 8)->count(),
            'regular'         => $comNota->filter(fn ($s) => $s->average_score >= 6 && $s->average_score < 8)->count(),
            'atencao'         => $comNota->filter(fn ($s) => $s->average_score < 6)->count(),
        ];
    }

    // ──────────────────────────────────────────────────────────────────

    public function limparFiltros(): void
    {
        $this->search       = '';
        $this->surveyFilter = '';
        $this->sort         = 'score';
        $this->sortDir      = 'desc';
        unset($this->ranking, $this->stats);
    }

    public function render()
    {
        return view('livewire.pages.surveys.manager-ranking')
            ->title('Ranking de Gerentes');
    }
}
