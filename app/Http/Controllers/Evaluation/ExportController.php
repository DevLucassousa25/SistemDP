<?php

namespace App\Http\Controllers\Evaluation;

use App\Http\Controllers\Controller;
use App\Models\EvaluationCriterion;
use App\Models\EvaluationCycle;
use App\Models\ManagerEvaluation;
use App\Models\ManagerEvaluationEntry;
use App\Models\SelfEvaluation;
use App\Models\SelfEvaluationEntry;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    // ───────────────────────────────────────────────────────────────────
    // Autorização
    // ───────────────────────────────────────────────────────────────────

    private function authorize(): void
    {
        $user = Auth::user();
        if (! $user || ! $user->podeGerenciarPesquisas()) {
            abort(403, 'Sem permissão para exportar avaliações.');
        }
    }

    // ───────────────────────────────────────────────────────────────────
    // Construção dos dados consolidados
    // ───────────────────────────────────────────────────────────────────

    private function buildData(EvaluationCycle $cycle): array
    {
        $criteria = EvaluationCriterion::where('is_active', true)
            ->orderBy('order')
            ->orderBy('name')
            ->get();

        $evaluations = ManagerEvaluation::with([
            'manager',
            'manager.department',
            'entries.criterion',
            'entries.employee',
        ])
            ->where('evaluation_cycle_id', $cycle->id)
            ->where('status', 'completed')
            ->orderBy('completed_at')
            ->get();

        // Calcula score por colaborador (média ponderada com calibração)
        $employeeScores = [];
        foreach ($evaluations as $eval) {
            foreach ($eval->entries->groupBy('employee_id') as $employeeId => $entries) {
                $totalWeight = 0;
                $weightedSum = 0;
                $calibrated  = false;

                foreach ($entries as $entry) {
                    $weight = (float) ($entry->criterion->weight ?? 1.0);
                    $score  = $entry->normalizedScore;
                    if ($score !== null) {
                        $weightedSum += $score * $weight;
                        $totalWeight += $weight;
                    }
                    if ($entry->calibrated_score !== null) {
                        $calibrated = true;
                    }
                }

                $finalScore = $totalWeight > 0 ? round($weightedSum / $totalWeight, 2) : null;

                $selfEntry = SelfEvaluation::where('evaluation_cycle_id', $cycle->id)
                    ->where('employee_id', $employeeId)
                    ->where('status', 'completed')
                    ->first();

                $selfScore = null;
                if ($selfEntry) {
                    $selfEntries  = SelfEvaluationEntry::with('criterion')
                        ->where('self_evaluation_id', $selfEntry->id)
                        ->get();
                    $sw = 0; $ss = 0;
                    foreach ($selfEntries as $se) {
                        $w = (float) ($se->criterion->weight ?? 1.0);
                        $s = $se->normalizedScore;
                        if ($s !== null) { $ss += $s * $w; $sw += $w; }
                    }
                    $selfScore = $sw > 0 ? round($ss / $sw, 2) : null;
                }

                $employeeScores[$employeeId] = [
                    'manager'      => $eval->manager,
                    'employee'     => $entries->first()->employee,
                    'final_score'  => $finalScore,
                    'self_score'   => $selfScore,
                    'calibrated'   => $calibrated,
                    'entries'      => $entries,
                    'completed_at' => $eval->completed_at,
                ];
            }
        }

        // Estatísticas gerais
        $scores      = collect($employeeScores)->pluck('final_score')->filter();
        $selfScores  = collect($employeeScores)->pluck('self_score')->filter();

        $stats = [
            'total_managers'       => $evaluations->count(),
            'total_evaluated'      => count($employeeScores),
            'avg_score'            => $scores->isNotEmpty() ? round($scores->avg(), 2) : null,
            'avg_self_score'       => $selfScores->isNotEmpty() ? round($selfScores->avg(), 2) : null,
            'max_score'            => $scores->isNotEmpty() ? $scores->max() : null,
            'min_score'            => $scores->isNotEmpty() ? $scores->min() : null,
            'calibrated_count'     => collect($employeeScores)->where('calibrated', true)->count(),
        ];

        // Médias por critério
        $criteriaStats = $criteria->map(function ($criterion) use ($employeeScores) {
            $allEntries = collect($employeeScores)
                ->flatMap(fn ($es) => $es['entries'])
                ->filter(fn ($e) => $e->criterion_id === $criterion->id);

            $scores = $allEntries->map(fn ($e) => $e->normalizedScore)->filter();
            return [
                'criterion' => $criterion,
                'avg'       => $scores->isNotEmpty() ? round($scores->avg(), 2) : null,
                'count'     => $scores->count(),
            ];
        });

        return compact('cycle', 'criteria', 'evaluations', 'employeeScores', 'stats', 'criteriaStats');
    }

    // ───────────────────────────────────────────────────────────────────
    // PDF
    // ───────────────────────────────────────────────────────────────────

    public function pdf(int $cycleId)
    {
        $this->authorize();

        $cycle = EvaluationCycle::findOrFail($cycleId);
        $data  = $this->buildData($cycle);

        $pdf = Pdf::loadView('evaluation.exports.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 96,
            ]);

        $filename = 'avaliacao-' . $cycle->id . '-' . now()->format('Y-m-d') . '.pdf';
        return $pdf->download($filename);
    }

    // ───────────────────────────────────────────────────────────────────
    // Excel
    // ───────────────────────────────────────────────────────────────────

    public function excel(int $cycleId)
    {
        $this->authorize();

        $cycle       = EvaluationCycle::findOrFail($cycleId);
        $data        = $this->buildData($cycle);
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setTitle('Avaliação de Desempenho — ' . $cycle->name)
            ->setCreator('SistemDP')
            ->setDescription('Exportação automática das avaliações de desempenho.');

        $this->buildResumoSheet($spreadsheet, $data);
        $this->buildDetalhesSheet($spreadsheet, $data);
        $this->buildCriteriosSheet($spreadsheet, $data);

        if (collect($data['employeeScores'])->filter(fn ($e) => $e['self_score'] !== null)->isNotEmpty()) {
            $this->buildComparativoSheet($spreadsheet, $data);
        }

        $filename = 'avaliacao-desempenho-' . $cycle->id . '-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // ───────────────────────────────────────────────────────────────────
    // Sheet 1: Resumo do ciclo
    // ───────────────────────────────────────────────────────────────────

    private function buildResumoSheet(Spreadsheet $spreadsheet, array $data): void
    {
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Resumo');
        $cycle = $data['cycle'];
        $stats = $data['stats'];

        // Título
        $this->mergeAndStyle($sheet, 'A1:E1', 'AVALIAÇÃO DE DESEMPENHO — ' . strtoupper($cycle->name), [
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366f1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // Informações do ciclo
        $this->sectionHeader($sheet, 'A3:E3', 'INFORMAÇÕES DO CICLO', '334155');
        $row = 4;
        foreach ([
            ['Ciclo',          $cycle->name],
            ['Período',        $cycle->periodLabel],
            ['Status',         $cycle->statusLabel],
            ['Exportado em',   now()->format('d/m/Y H:i')],
        ] as [$label, $value]) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $value);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:E{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
            }
            $row++;
        }

        // Estatísticas
        $row++;
        $this->sectionHeader($sheet, "A{$row}:E{$row}", 'ESTATÍSTICAS GERAIS', '334155');
        $row++;

        $statRows = [
            ['Gerentes que avaliaram', $stats['total_managers'],   '6366f1'],
            ['Total de colaboradores avaliados', $stats['total_evaluated'], '334155'],
            ['Nota média geral', $stats['avg_score'] !== null ? number_format($stats['avg_score'], 2, ',', '') : '—', '059669'],
            ['Nota média autoavaliação', $stats['avg_self_score'] !== null ? number_format($stats['avg_self_score'], 2, ',', '') : '—', '0891b2'],
            ['Maior nota', $stats['max_score'] !== null ? number_format($stats['max_score'], 2, ',', '') : '—', '059669'],
            ['Menor nota', $stats['min_score'] !== null ? number_format($stats['min_score'], 2, ',', '') : '—', 'dc2626'],
            ['Notas calibradas pelo RH', $stats['calibrated_count'], 'd97706'],
        ];

        foreach ($statRows as [$label, $value, $color]) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $value);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(12)
                ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB($color));
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(36);
        $sheet->getColumnDimension('B')->setWidth(30);
        $sheet->getColumnDimension('C')->setWidth(16);
        $sheet->getColumnDimension('D')->setWidth(16);
        $sheet->getColumnDimension('E')->setWidth(16);
    }

    // ───────────────────────────────────────────────────────────────────
    // Sheet 2: Detalhes por colaborador
    // ───────────────────────────────────────────────────────────────────

    private function buildDetalhesSheet(Spreadsheet $spreadsheet, array $data): void
    {
        $sheet    = $spreadsheet->createSheet()->setTitle('Detalhes por Colaborador');
        $criteria = $data['criteria'];

        // Header da tabela
        $headers = ['Colaborador', 'Departamento', 'Gerente', 'Nota Final (Ponderada)', 'Autoavaliação', 'Calibrado'];
        foreach ($criteria as $i => $criterion) {
            $headers[] = $criterion->name . ' (×' . number_format($criterion->weight, 1) . ')';
        }

        foreach ($headers as $i => $header) {
            $col = $this->colLetter($i);
            $sheet->setCellValue("{$col}1", $header);
        }
        $lastCol = $this->colLetter(count($headers) - 1);
        $sheet->getStyle("A1:{$lastCol}1")->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '6366f1']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'wrapText' => true],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(36);

        $row = 2;
        foreach ($data['employeeScores'] as $employeeId => $es) {
            $col = 0;
            $sheet->setCellValue($this->colLetter($col++) . $row, $es['employee']->name);
            $sheet->setCellValue($this->colLetter($col++) . $row, $es['employee']->department?->name ?? '—');
            $sheet->setCellValue($this->colLetter($col++) . $row, $es['manager']->name);
            $sheet->setCellValue($this->colLetter($col++) . $row,
                $es['final_score'] !== null ? number_format($es['final_score'], 2, ',', '') : '—');
            $sheet->setCellValue($this->colLetter($col++) . $row,
                $es['self_score'] !== null ? number_format($es['self_score'], 2, ',', '') : '—');
            $sheet->setCellValue($this->colLetter($col++) . $row, $es['calibrated'] ? 'Sim' : 'Não');

            // Notas por critério
            $entriesByCriterion = $es['entries']->keyBy('criterion_id');
            foreach ($criteria as $criterion) {
                $entry = $entriesByCriterion->get($criterion->id);
                $score = $entry?->effectiveScore;
                $sheet->setCellValue($this->colLetter($col++) . $row,
                    $score !== null ? $entry->displayValue : '—');
            }

            // Destaque de nota
            $finalScore = $es['final_score'];
            $fillColor  = match (true) {
                $finalScore === null       => null,
                $finalScore >= 4.5        => 'dcfce7',
                $finalScore >= 3.5        => 'fef9c3',
                $finalScore >= 2.5        => 'ffedd5',
                default                   => 'fee2e2',
            };
            if ($fillColor) {
                $noteCol = $this->colLetter(3);
                $sheet->getStyle("{$noteCol}{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
            }

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:{$lastCol}{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
            }

            $row++;
        }

        $this->applyBorderArea($sheet, "A1:{$lastCol}" . ($row - 1));

        // Larguras
        $sheet->getColumnDimension('A')->setWidth(28);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(22);
        $sheet->getColumnDimension('D')->setWidth(22);
        $sheet->getColumnDimension('E')->setWidth(18);
        $sheet->getColumnDimension('F')->setWidth(12);
        for ($i = 6; $i < count($headers); $i++) {
            $sheet->getColumnDimension($this->colLetter($i))->setWidth(20);
        }
    }

    // ───────────────────────────────────────────────────────────────────
    // Sheet 3: Médias por critério
    // ───────────────────────────────────────────────────────────────────

    private function buildCriteriosSheet(Spreadsheet $spreadsheet, array $data): void
    {
        $sheet = $spreadsheet->createSheet()->setTitle('Médias por Critério');

        $this->sectionHeader($sheet, 'A1:E1', 'DESEMPENHO POR CRITÉRIO — ' . strtoupper($data['cycle']->name), '6366f1');
        $sheet->getRowDimension(1)->setRowHeight(24);

        $this->tableHeader($sheet, 2, [
            'A' => 'Critério',
            'B' => 'Tipo de Resposta',
            'C' => 'Peso',
            'D' => 'Média',
            'E' => 'Avaliações',
        ]);

        $row = 3;
        foreach ($data['criteriaStats'] as $cs) {
            $criterion = $cs['criterion'];
            $avg       = $cs['avg'];

            $fillColor = match (true) {
                $avg === null    => null,
                $avg >= 4.5      => 'dcfce7',
                $avg >= 3.5      => 'fef9c3',
                $avg >= 2.5      => 'ffedd5',
                default          => 'fee2e2',
            };

            $sheet->setCellValue("A{$row}", $criterion->name);
            $sheet->setCellValue("B{$row}", $criterion->responseTypeLabel);
            $sheet->setCellValue("C{$row}", '×' . number_format($criterion->weight ?? 1.0, 1));
            $sheet->setCellValue("D{$row}", $avg !== null ? number_format($avg, 2, ',', '') : '—');
            $sheet->setCellValue("E{$row}", $cs['count']);

            if ($fillColor) {
                $sheet->getStyle("D{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($fillColor);
                $sheet->getStyle("D{$row}")->getFont()->setBold(true);
            }

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:E{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
            }

            $row++;
        }

        $this->applyBorderArea($sheet, 'A2:E' . ($row - 1));

        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(10);
        $sheet->getColumnDimension('D')->setWidth(14);
        $sheet->getColumnDimension('E')->setWidth(14);
    }

    // ───────────────────────────────────────────────────────────────────
    // Sheet 4: Comparativo Gerente vs Autoavaliação
    // ───────────────────────────────────────────────────────────────────

    private function buildComparativoSheet(Spreadsheet $spreadsheet, array $data): void
    {
        $sheet = $spreadsheet->createSheet()->setTitle('Comparativo Auto vs Gerente');

        $this->sectionHeader($sheet, 'A1:D1', 'COMPARATIVO: AUTOAVALIAÇÃO vs AVALIAÇÃO DO GERENTE', '6366f1');
        $sheet->getRowDimension(1)->setRowHeight(24);

        $this->tableHeader($sheet, 2, [
            'A' => 'Colaborador',
            'B' => 'Nota do Gerente',
            'C' => 'Autoavaliação',
            'D' => 'Diferença',
        ]);

        $row = 3;
        foreach ($data['employeeScores'] as $es) {
            if ($es['self_score'] === null && $es['final_score'] === null) {
                continue;
            }

            $diff    = null;
            $diffStr = '—';
            if ($es['final_score'] !== null && $es['self_score'] !== null) {
                $diff    = round($es['final_score'] - $es['self_score'], 2);
                $diffStr = ($diff > 0 ? '+' : '') . number_format($diff, 2, ',', '');
            }

            $sheet->setCellValue("A{$row}", $es['employee']->name);
            $sheet->setCellValue("B{$row}", $es['final_score'] !== null ? number_format($es['final_score'], 2, ',', '') : '—');
            $sheet->setCellValue("C{$row}", $es['self_score'] !== null ? number_format($es['self_score'], 2, ',', '') : '—');
            $sheet->setCellValue("D{$row}", $diffStr);

            // Cor da diferença
            if ($diff !== null) {
                $diffColor = match (true) {
                    $diff > 0.5  => 'fef9c3',  // gerente > autoavaliação significativo
                    $diff < -0.5 => 'fee2e2',  // autoavaliação > gerente (otimismo excessivo)
                    default      => 'dcfce7',  // alinhados
                };
                $sheet->getStyle("D{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($diffColor);
            }

            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:C{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
            }

            $row++;
        }

        $this->applyBorderArea($sheet, 'A2:D' . ($row - 1));

        // Legenda
        $row++;
        $sheet->setCellValue("A{$row}", 'Legenda da coluna "Diferença" (Gerente − Autoavaliação):');
        $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setItalic(true);
        $row++;
        $legendas = [
            ['dcfce7', 'Alinhado (diferença ≤ ±0,5): percepção consistente'],
            ['fef9c3', 'Positivo (> +0,5): gerente avalia acima da autoavaliação'],
            ['fee2e2', 'Negativo (< −0,5): colaborador se avalia acima do gerente'],
        ];
        foreach ($legendas as [$color, $text]) {
            $sheet->setCellValue("A{$row}", '');
            $sheet->setCellValue("B{$row}", $text);
            $sheet->getStyle("A{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
            $row++;
        }

        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(20);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);
    }

    // ───────────────────────────────────────────────────────────────────
    // Helpers de estilo (replicados do Surveys/ExportController)
    // ───────────────────────────────────────────────────────────────────

    private function mergeAndStyle($sheet, string $range, string $text, array $style): void
    {
        [$start] = explode(':', $range);
        $sheet->setCellValue($start, $text);
        $sheet->mergeCells($range);
        $sheet->getStyle($range)->applyFromArray($style);
    }

    private function sectionHeader($sheet, string $range, string $text, string $rgb): void
    {
        $this->mergeAndStyle($sheet, $range, $text, [
            'font'      => ['bold' => true, 'size' => 10, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $rgb]],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_LEFT, 'indent' => 1],
        ]);
        preg_match('/\d+/', $range, $m);
        $sheet->getRowDimension($m[0])->setRowHeight(20);
    }

    private function tableHeader($sheet, int $row, array $columns): void
    {
        foreach ($columns as $col => $label) {
            $sheet->setCellValue("{$col}{$row}", $label);
        }
        $cols  = array_keys($columns);
        $range = $cols[0] . $row . ':' . end($cols) . $row;
        $sheet->getStyle($range)->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '64748b']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
    }

    private function applyBorderArea($sheet, string $range): void
    {
        $sheet->getStyle($range)->applyFromArray([
            'borders' => [
                'allBorders' => [
                    'borderStyle' => Border::BORDER_THIN,
                    'color'       => ['rgb' => 'e2e8f0'],
                ],
            ],
        ]);
    }

    private function colLetter(int $index): string
    {
        $letters = '';
        $index++;
        while ($index > 0) {
            $mod      = ($index - 1) % 26;
            $letters  = chr(65 + $mod) . $letters;
            $index    = (int) (($index - $mod) / 26);
        }
        return $letters;
    }
}
