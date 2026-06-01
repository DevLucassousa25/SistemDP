<?php

namespace App\Livewire\Pages\Treinamentos;

use App\Livewire\SecureComponent;
use App\Models\Department;
use App\Models\Treinamento;
use App\Models\TreinamentoAvaliacaoReacao;
use App\Models\TreinamentoCertificado;
use App\Models\TreinamentoInscricao;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class Relatorios extends SecureComponent
{
    // ── Filtros ────────────────────────────────────────────────────────────
    #[Url(except: 'geral')]
    public string $aba = 'geral'; // geral | cursos | colaboradores | avaliacoes

    #[Url(except: '')]
    public string $periodoInicio = '';

    #[Url(except: '')]
    public string $periodoFim = '';

    #[Url(except: '')]
    public string $departamentoFiltro = '';

    // ─────────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();

        if (!$this->periodoInicio) {
            $this->periodoInicio = now()->startOfYear()->format('Y-m-d');
        }
        if (!$this->periodoFim) {
            $this->periodoFim = now()->format('Y-m-d');
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // COMPUTEDS — GERAL
    // ══════════════════════════════════════════════════════════════════════

    #[Computed]
    public function statsGerais(): array
    {
        $query = TreinamentoInscricao::query()
            ->when($this->periodoInicio, fn($q) => $q->where('created_at', '>=', $this->periodoInicio))
            ->when($this->periodoFim,    fn($q) => $q->where('created_at', '<=', $this->periodoFim . ' 23:59:59'))
            ->when($this->departamentoFiltro, fn($q) => $q->whereHas('usuario', fn($u) => $u->where('department_id', $this->departamentoFiltro)));

        $total          = $query->count();
        $concluidos     = (clone $query)->where('status', 'concluido')->count();
        $emAndamento    = (clone $query)->where('status', 'em_andamento')->count();
        $taxaConclusao  = $total > 0 ? round(($concluidos / $total) * 100, 1) : 0;
        $certificados   = TreinamentoCertificado::query()
            ->whereHas('inscricao', function ($q) {
                $q->when($this->periodoInicio, fn($q2) => $q2->where('created_at', '>=', $this->periodoInicio))
                  ->when($this->periodoFim,    fn($q2) => $q2->where('created_at', '<=', $this->periodoFim . ' 23:59:59'));
            })
            ->count();

        $mediaNota = TreinamentoInscricao::query()
            ->join('treinamento_tentativas', 'treinamento_tentativas.inscricao_id', '=', 'treinamento_inscricoes.id')
            ->where('treinamento_tentativas.aprovado', true)
            ->when($this->periodoInicio, fn($q) => $q->where('treinamento_inscricoes.created_at', '>=', $this->periodoInicio))
            ->when($this->periodoFim,    fn($q) => $q->where('treinamento_inscricoes.created_at', '<=', $this->periodoFim . ' 23:59:59'))
            ->avg('treinamento_tentativas.nota');

        return [
            'total'          => $total,
            'concluidos'     => $concluidos,
            'em_andamento'   => $emAndamento,
            'taxa_conclusao' => $taxaConclusao,
            'certificados'   => $certificados,
            'media_nota'     => $mediaNota ? round($mediaNota, 1) : null,
        ];
    }

    // ── Top cursos por inscrições ─────────────────────────────────────────
    #[Computed]
    public function topCursos()
    {
        return Treinamento::query()
            ->withCount('inscricoes as total_inscritos')
            ->orderByDesc('total_inscritos')
            ->get()
            ->filter(fn($c) => $c->total_inscritos > 0)
            ->each(function ($c) {
                $c->total_concluidos = $c->inscricoes()->where('status', 'concluido')->count();
            })
            ->take(10)
            ->values();
    }

    // ── Inscrições por mês ────────────────────────────────────────────────
    #[Computed]
    public function inscricoesPorMes(): array
    {
        return TreinamentoInscricao::query()
            ->selectRaw("TO_CHAR(created_at, 'YYYY-MM') as mes, COUNT(*) as total, SUM(CASE WHEN status = 'concluido' THEN 1 ELSE 0 END) as concluidos")
            ->when($this->periodoInicio, fn($q) => $q->where('created_at', '>=', $this->periodoInicio))
            ->when($this->periodoFim,    fn($q) => $q->where('created_at', '<=', $this->periodoFim . ' 23:59:59'))
            ->groupByRaw("TO_CHAR(created_at, 'YYYY-MM')")
            ->orderByRaw("TO_CHAR(created_at, 'YYYY-MM') ASC")
            ->get()
            ->map(fn($r) => [
                'mes'        => \Carbon\Carbon::parse($r->mes . '-01')->translatedFormat('M/y'),
                'total'      => $r->total,
                'concluidos' => $r->concluidos,
            ])
            ->toArray();
    }

    // ── Ranking por departamento ──────────────────────────────────────────
    #[Computed]
    public function rankingDepartamentos()
    {
        return Department::query()
            ->withCount([
                'users as total_colaboradores',
            ])
            ->get()
            ->map(function ($dept) {
                $inscritos  = TreinamentoInscricao::whereHas('usuario', fn($q) => $q->where('department_id', $dept->id))->count();
                $concluidos = TreinamentoInscricao::whereHas('usuario', fn($q) => $q->where('department_id', $dept->id))
                                ->where('status', 'concluido')->count();
                $taxa       = $inscritos > 0 ? round(($concluidos / $inscritos) * 100, 1) : 0;
                return [
                    'departamento'        => $dept->name,
                    'total_colaboradores' => $dept->total_colaboradores,
                    'inscritos'           => $inscritos,
                    'concluidos'          => $concluidos,
                    'taxa_conclusao'      => $taxa,
                ];
            })
            ->filter(fn($d) => $d['inscritos'] > 0)
            ->sortByDesc('taxa_conclusao')
            ->values();
    }

    // ── Lista detalhada de cursos ─────────────────────────────────────────
    #[Computed]
    public function detalhesCursos()
    {
        return Treinamento::query()
            ->withCount('inscricoes as total_inscritos')
            ->orderBy('titulo')
            ->get()
            ->map(function ($c) {
                $concluidos = $c->inscricoes()->where('status', 'concluido')->count();
                $reprovados = $c->inscricoes()->where('status', 'reprovado')->count();
                $taxa = $c->total_inscritos > 0 ? round(($concluidos / $c->total_inscritos) * 100, 1) : 0;
                $media = TreinamentoInscricao::where('treinamento_id', $c->id)
                    ->join('treinamento_tentativas', 'treinamento_tentativas.inscricao_id', '=', 'treinamento_inscricoes.id')
                    ->where('treinamento_tentativas.aprovado', true)
                    ->avg('treinamento_tentativas.nota');
                return [
                    'id'             => $c->id,
                    'titulo'         => $c->titulo,
                    'nivel'          => ucfirst($c->nivel),
                    'carga_horaria'  => $c->carga_horaria_formatada,
                    'inscritos'      => $c->total_inscritos,
                    'concluidos'     => $concluidos,
                    'reprovados'     => $reprovados,
                    'taxa_conclusao' => $taxa,
                    'media_nota'     => $media ? round($media, 1) : '—',
                ];
            });
    }

    // ── Avaliações de reação ──────────────────────────────────────────────
    #[Computed]
    public function avaliacoes()
    {
        return TreinamentoAvaliacaoReacao::query()
            ->with(['inscricao.treinamento', 'inscricao.usuario'])
            ->when($this->periodoInicio, fn($q) => $q->where('created_at', '>=', $this->periodoInicio))
            ->when($this->periodoFim,    fn($q) => $q->where('created_at', '<=', $this->periodoFim . ' 23:59:59'))
            ->latest()
            ->get();
    }

    #[Computed]
    public function mediaAvaliacoes(): array
    {
        return Treinamento::query()
            ->with(['inscricoes.avaliacaoReacao'])
            ->get()
            ->map(function ($c) {
                $notas = $c->inscricoes->pluck('avaliacaoReacao')->filter()->pluck('nota');
                return [
                    'titulo' => $c->titulo,
                    'media'  => $notas->isNotEmpty() ? round($notas->avg(), 2) : null,
                    'total'  => $notas->count(),
                ];
            })
            ->filter(fn($r) => $r['total'] > 0)
            ->sortByDesc('media')
            ->values()
            ->toArray();
    }

    #[Computed]
    public function departamentos()
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }

    // ══════════════════════════════════════════════════════════════════════
    // EXPORT EXCEL
    // ══════════════════════════════════════════════════════════════════════

    public function exportarExcel(): mixed
    {
        $this->requireRhOrAdmin();

        $spreadsheet = new Spreadsheet();

        // ── Aba 1: Resumo geral ───────────────────────────────────────────
        $sheet1 = $spreadsheet->getActiveSheet();
        $sheet1->setTitle('Resumo');
        $sheet1->fromArray([['Indicador', 'Valor']], null, 'A1');
        $stats = $this->statsGerais;
        $sheet1->fromArray([
            ['Total de inscrições',    $stats['total']],
            ['Concluídos',             $stats['concluidos']],
            ['Em andamento',           $stats['em_andamento']],
            ['Taxa de conclusão (%)',  $stats['taxa_conclusao']],
            ['Certificados emitidos',  $stats['certificados']],
            ['Média das notas',        $stats['media_nota'] ?? '—'],
        ], null, 'A2');

        // ── Aba 2: Cursos ─────────────────────────────────────────────────
        $sheet2 = $spreadsheet->createSheet();
        $sheet2->setTitle('Cursos');
        $sheet2->fromArray([['Curso', 'Nível', 'Carga Horária', 'Inscritos', 'Concluídos', 'Taxa (%)', 'Média Nota']], null, 'A1');
        $row = 2;
        foreach ($this->detalhesCursos as $c) {
            $sheet2->fromArray([[$c['titulo'], $c['nivel'], $c['carga_horaria'], $c['inscritos'], $c['concluidos'], $c['taxa_conclusao'], $c['media_nota']]], null, "A{$row}");
            $row++;
        }

        // ── Aba 3: Departamentos ──────────────────────────────────────────
        $sheet3 = $spreadsheet->createSheet();
        $sheet3->setTitle('Departamentos');
        $sheet3->fromArray([['Departamento', 'Colaboradores', 'Inscritos', 'Concluídos', 'Taxa (%)']], null, 'A1');
        $row = 2;
        foreach ($this->rankingDepartamentos as $d) {
            $sheet3->fromArray([[$d['departamento'], $d['total_colaboradores'], $d['inscritos'], $d['concluidos'], $d['taxa_conclusao']]], null, "A{$row}");
            $row++;
        }

        // Auto-size colunas
        foreach ([$sheet1, $sheet2, $sheet3] as $s) {
            foreach (range('A', 'G') as $col) {
                $s->getColumnDimension($col)->setAutoSize(true);
            }
        }

        $filename = 'relatorio-treinamentos-' . now()->format('Y-m-d') . '.xlsx';
        $path     = storage_path('app/temp/' . $filename);

        if (!is_dir(storage_path('app/temp'))) {
            mkdir(storage_path('app/temp'), 0755, true);
        }

        $writer = new Xlsx($spreadsheet);
        $writer->save($path);

        return response()->download($path, $filename)->deleteFileAfterSend(true);
    }

    // ─────────────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.treinamentos.relatorios')
            ->layout('components.layouts.app', ['title' => 'Relatórios de Treinamentos']);
    }
}
