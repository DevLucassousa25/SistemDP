<?php

namespace App\Http\Controllers\Surveys;

use App\Http\Controllers\Controller;
use App\Models\Survey;
use App\Models\SurveyAnswer;
use App\Models\SurveyResponse;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    // ──────────────────────────────────────────────────────────────────
    // Autorização
    // ──────────────────────────────────────────────────────────────────

    private function authorizedSurvey(int $id): Survey
    {
        $user   = Auth::user();
        $survey = Survey::with(['questions', 'creator:id,name'])->findOrFail($id);

        if (! $user->podeGerenciarPesquisas()) {
            abort(403, 'Sem permissão para exportar os resultados.');
        }

        if ($user->isGerente() && $survey->created_by !== $user->id) {
            abort(403, 'Você não tem permissão para exportar os resultados desta pesquisa.');
        }

        return $survey;
    }

    // ──────────────────────────────────────────────────────────────────
    // Construção dos dados (mesma lógica do componente Results.php)
    // ──────────────────────────────────────────────────────────────────

    private function buildData(Survey $survey): array
    {
        // Stats
        $totalResponses = SurveyResponse::where('survey_id', $survey->id)
            ->whereNotNull('completed_at')
            ->count();

        if ($survey->target_audience === 'todos') {
            $totalInvited = User::count();
        } else {
            $deptIds      = array_map('intval', $survey->target_department_ids ?? []);
            $totalInvited = User::whereIn('department_id', $deptIds)->count();
        }

        $stats = [
            'total_responses' => $totalResponses,
            'total_invited'   => $totalInvited,
            'response_rate'   => $totalInvited > 0 ? round(($totalResponses / $totalInvited) * 100, 1) : 0,
            'total_questions' => $survey->questions->count(),
        ];

        // Respostas concluídas
        $completedIds = SurveyResponse::where('survey_id', $survey->id)
            ->whereNotNull('completed_at')
            ->pluck('id');

        // Resultados por pergunta
        $questionResults = [];

        foreach ($survey->questions as $question) {
            $answers = SurveyAnswer::where('question_id', $question->id)
                ->whereIn('response_id', $completedIds)
                ->get();

            $result = [
                'id'       => $question->id,
                'question' => $question->question,
                'type'     => $question->type,
                'count'    => $answers->count(),
            ];

            if ($question->type === 'escala') {
                $distribution = array_fill(0, 11, 0);
                foreach ($answers as $a) {
                    if ($a->value_scale !== null) {
                        $distribution[(int) $a->value_scale]++;
                    }
                }
                $total      = array_sum($distribution);
                $promoters  = $distribution[9] + $distribution[10];
                $passives   = $distribution[7] + $distribution[8];
                $detractors = array_sum(array_slice($distribution, 0, 7));
                $sum        = 0;
                foreach ($answers as $a) {
                    if ($a->value_scale !== null) {
                        $sum += (int) $a->value_scale;
                    }
                }
                $result += [
                    'distribution' => array_values($distribution),
                    'total'        => $total,
                    'promoters'    => $promoters,
                    'passives'     => $passives,
                    'detractors'   => $detractors,
                    'nps'          => $total > 0 ? round((($promoters / $total) - ($detractors / $total)) * 100) : null,
                    'average'      => $total > 0 ? round($sum / $total, 1) : null,
                ];

            } elseif ($question->type === 'multipla_escolha') {
                $options = $question->options ?? [];
                $counts  = array_fill_keys($options, 0);
                foreach ($answers as $a) {
                    if ($a->value_option !== null && array_key_exists($a->value_option, $counts)) {
                        $counts[$a->value_option]++;
                    }
                }
                $total       = array_sum($counts);
                $percentages = [];
                foreach ($counts as $opt => $cnt) {
                    $percentages[$opt] = $total > 0 ? round(($cnt / $total) * 100, 1) : 0;
                }
                arsort($counts);
                $result += [
                    'options'     => $options,
                    'counts'      => $counts,
                    'percentages' => $percentages,
                    'total'       => $total,
                ];

            } elseif ($question->type === 'texto_livre') {
                $texts   = $answers->pluck('value_text')->filter()->values()->toArray();
                $result += [
                    'texts' => $texts,
                    'total' => count($texts),
                ];
            }

            $questionResults[] = $result;
        }

        // Evolução diária
        $dailyResponses = SurveyResponse::where('survey_id', $survey->id)
            ->whereNotNull('completed_at')
            ->selectRaw('DATE(completed_at) as date, COUNT(*) as total')
            ->groupBy('date')
            ->orderBy('date')
            ->pluck('total', 'date')
            ->toArray();

        return compact('survey', 'stats', 'questionResults', 'dailyResponses');
    }

    // ──────────────────────────────────────────────────────────────────
    // PDF
    // ──────────────────────────────────────────────────────────────────

    public function pdf(int $id)
    {
        $survey = $this->authorizedSurvey($id);
        $data   = $this->buildData($survey);

        $pdf = Pdf::loadView('surveys.exports.pdf', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 96,
            ]);

        return $pdf->download('pesquisa-' . $survey->id . '-resultados.pdf');
    }

    // ──────────────────────────────────────────────────────────────────
    // Excel
    // ──────────────────────────────────────────────────────────────────

    public function excel(int $id)
    {
        $survey = $this->authorizedSurvey($id);
        $data   = $this->buildData($survey);

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setTitle('Resultados — ' . $survey->title)
            ->setCreator('SistemDP')
            ->setDescription('Exportação automática do dashboard de resultados.');

        $this->buildResumoSheet($spreadsheet, $data);
        $this->buildAnaliseSheet($spreadsheet, $data);

        if (! empty($data['dailyResponses'])) {
            $this->buildEvolucaoSheet($spreadsheet, $data);
        }

        $filename = 'pesquisa-' . $survey->id . '-resultados.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'        => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control'       => 'max-age=0',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // Sheet 1: Resumo geral
    // ──────────────────────────────────────────────────────────────────

    private function buildResumoSheet(Spreadsheet $spreadsheet, array $data): void
    {
        $survey = $data['survey'];
        $stats  = $data['stats'];

        $sheet = $spreadsheet->getActiveSheet()->setTitle('Resumo');

        // ── Cabeçalho principal ──────────────────────────────────────
        $this->mergeAndStyle($sheet, 'A1:D1', 'RESULTADOS DA PESQUISA DE SATISFAÇÃO', [
            'font'      => ['bold' => true, 'size' => 14, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(32);

        // ── Informações da pesquisa ──────────────────────────────────
        $this->sectionHeader($sheet, 'A3:D3', 'INFORMAÇÕES GERAIS', '334155');
        $row = 4;

        $period = match (true) {
            (bool) $survey->start_date && (bool) $survey->end_date =>
                $survey->start_date->format('d/m/Y') . ' a ' . $survey->end_date->format('d/m/Y'),
            (bool) $survey->start_date => 'A partir de ' . $survey->start_date->format('d/m/Y'),
            (bool) $survey->end_date   => 'Até ' . $survey->end_date->format('d/m/Y'),
            default                    => 'Sem período definido',
        };

        $infoRows = [
            ['Título',              $survey->title],
            ['Status',              $survey->status_label],
            ['Criada por',          $survey->creator?->name ?? '—'],
            ['Público-alvo',        $survey->audience_label],
            ['Período',             $period],
            ['Respostas anônimas',  $survey->is_anonymous ? 'Sim' : 'Não'],
            ['Exportado em',        now()->format('d/m/Y H:i')],
        ];

        foreach ($infoRows as [$label, $value]) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $value);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true)->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('334155'));
            $sheet->getStyle("B{$row}")->getFont()->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('475569'));
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:D{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
            }
            $row++;
        }

        // ── Estatísticas ─────────────────────────────────────────────
        $row++;
        $this->sectionHeader($sheet, "A{$row}:D{$row}", 'ESTATÍSTICAS GERAIS', '334155');
        $row++;

        $statRows = [
            ['Total de respostas',  $stats['total_responses'], '059669'],
            ['Total de convidados', $stats['total_invited'],   '334155'],
            ['Taxa de resposta',    $stats['response_rate'] . '%', '0891b2'],
            ['Total de perguntas',  $stats['total_questions'], '7c3aed'],
        ];

        foreach ($statRows as [$label, $value, $color]) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $value);
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(13)
                ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB($color));
            $row++;
        }

        // ── Larguras ─────────────────────────────────────────────────
        $sheet->getColumnDimension('A')->setWidth(28);
        $sheet->getColumnDimension('B')->setWidth(45);
        $sheet->getColumnDimension('C')->setWidth(18);
        $sheet->getColumnDimension('D')->setWidth(18);

        // Bordas na área de info
        $this->applyBorderArea($sheet, 'A4:B' . ($row - 1));
    }

    // ──────────────────────────────────────────────────────────────────
    // Sheet 2: Análise por pergunta
    // ──────────────────────────────────────────────────────────────────

    private function buildAnaliseSheet(Spreadsheet $spreadsheet, array $data): void
    {
        $sheet = $spreadsheet->createSheet()->setTitle('Análise por Pergunta');

        $headerColors = ['059669', '0891b2', '7c3aed', 'd97706', 'dc2626', '4f46e5'];
        $row          = 1;
        $ci           = 0;

        foreach ($data['questionResults'] as $i => $qr) {
            $color = $headerColors[$ci % count($headerColors)];
            $ci++;

            // ── Cabeçalho da pergunta ────────────────────────────────
            $this->mergeAndStyle($sheet, "A{$row}:E{$row}",
                ($i + 1) . '. ' . $qr['question'], [
                    'font'      => ['bold' => true, 'size' => 11, 'color' => ['rgb' => 'FFFFFF']],
                    'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => $color]],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                ]);
            $sheet->getRowDimension($row)->setRowHeight(28);
            $row++;

            // Tipo + contagem
            $typeLabel = match ($qr['type']) {
                'escala'           => 'Escala 0–10',
                'multipla_escolha' => 'Múltipla Escolha',
                'texto_livre'      => 'Texto Livre',
                default            => $qr['type'],
            };
            $this->mergeAndStyle($sheet, "A{$row}:E{$row}",
                "Tipo: {$typeLabel}   |   Respostas recebidas: {$qr['count']}", [
                    'font' => ['italic' => true, 'color' => ['rgb' => '64748b']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'f8fafc']],
                ]);
            $row++;

            // ── Escala ───────────────────────────────────────────────
            if ($qr['type'] === 'escala' && $qr['count'] > 0) {
                // Métricas rápidas
                $sheet->setCellValue("A{$row}", 'Média');
                $sheet->setCellValue("B{$row}", $qr['average']);
                $sheet->setCellValue("C{$row}", 'NPS');
                $sheet->setCellValue("D{$row}", $qr['nps'] !== null ? ($qr['nps'] > 0 ? '+' : '') . $qr['nps'] : '—');
                $sheet->getStyle("A{$row}")->getFont()->setBold(true);
                $sheet->getStyle("C{$row}")->getFont()->setBold(true);
                $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(12)
                    ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('059669'));
                $sheet->getStyle("D{$row}")->getFont()->setBold(true)->setSize(12)
                    ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('0891b2'));
                $row++;

                // NPS segmentado
                if ($qr['nps'] !== null) {
                    $total = $qr['total'];
                    $sheet->setCellValue("A{$row}", 'Promotores (9–10)');
                    $sheet->setCellValue("B{$row}", $qr['promoters']);
                    $sheet->setCellValue("C{$row}", $total > 0 ? round(($qr['promoters'] / $total) * 100, 1) . '%' : '—');
                    $sheet->getStyle("B{$row}")->getFont()->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('059669'));
                    $row++;
                    $sheet->setCellValue("A{$row}", 'Neutros (7–8)');
                    $sheet->setCellValue("B{$row}", $qr['passives']);
                    $sheet->setCellValue("C{$row}", $total > 0 ? round(($qr['passives'] / $total) * 100, 1) . '%' : '—');
                    $sheet->getStyle("B{$row}")->getFont()->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('d97706'));
                    $row++;
                    $sheet->setCellValue("A{$row}", 'Detratores (0–6)');
                    $sheet->setCellValue("B{$row}", $qr['detractors']);
                    $sheet->setCellValue("C{$row}", $total > 0 ? round(($qr['detractors'] / $total) * 100, 1) . '%' : '—');
                    $sheet->getStyle("B{$row}")->getFont()->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('dc2626'));
                    $row++;
                }

                // Distribuição 0–10
                $this->tableHeader($sheet, $row, ['A' => 'Nota', 'B' => 'Respostas', 'C' => 'Percentual']);
                $row++;
                $total = $qr['total'];
                foreach ($qr['distribution'] as $note => $cnt) {
                    $pct = $total > 0 ? round(($cnt / $total) * 100, 1) : 0;
                    $barColor = match (true) {
                        $note >= 9  => 'dcfce7',
                        $note >= 7  => 'fef9c3',
                        $note >= 5  => 'ffedd5',
                        default     => 'fee2e2',
                    };
                    $sheet->setCellValue("A{$row}", $note);
                    $sheet->setCellValue("B{$row}", $cnt);
                    $sheet->setCellValue("C{$row}", $pct . '%');
                    if ($cnt > 0) {
                        $sheet->getStyle("A{$row}:C{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($barColor);
                    }
                    $row++;
                }

            // ── Múltipla escolha ─────────────────────────────────────
            } elseif ($qr['type'] === 'multipla_escolha' && $qr['count'] > 0) {
                $this->tableHeader($sheet, $row, ['A' => 'Opção', 'B' => 'Respostas', 'C' => 'Percentual']);
                $row++;
                foreach ($qr['counts'] as $option => $cnt) {
                    $pct = $qr['percentages'][$option] ?? 0;
                    $sheet->setCellValue("A{$row}", $option);
                    $sheet->setCellValue("B{$row}", $cnt);
                    $sheet->setCellValue("C{$row}", $pct . '%');
                    $sheet->getStyle("B{$row}")->getFont()->setBold(true);
                    $row++;
                }

            // ── Texto livre ───────────────────────────────────────────
            } elseif ($qr['type'] === 'texto_livre' && $qr['count'] > 0) {
                $this->tableHeader($sheet, $row, ['A' => 'Nº', 'B' => 'Resposta']);
                $row++;
                foreach ($qr['texts'] as $ti => $text) {
                    $sheet->setCellValue("A{$row}", $ti + 1);
                    $sheet->setCellValue("B{$row}", $text);
                    $sheet->getStyle("B{$row}")->getAlignment()->setWrapText(true);
                    $sheet->getRowDimension($row)->setRowHeight(-1); // auto
                    if ($ti % 2 === 0) {
                        $sheet->getStyle("A{$row}:B{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
                    }
                    $row++;
                }
            } else {
                $sheet->setCellValue("A{$row}", 'Sem respostas registradas para esta pergunta.');
                $sheet->getStyle("A{$row}")->getFont()->setItalic(true)
                    ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('94a3b8'));
                $row++;
            }

            $row += 2; // Espaço entre perguntas
        }

        $sheet->getColumnDimension('A')->setWidth(32);
        $sheet->getColumnDimension('B')->setWidth(55);
        $sheet->getColumnDimension('C')->setWidth(15);
        $sheet->getColumnDimension('D')->setWidth(15);
        $sheet->getColumnDimension('E')->setWidth(15);
    }

    // ──────────────────────────────────────────────────────────────────
    // Sheet 3: Evolução diária
    // ──────────────────────────────────────────────────────────────────

    private function buildEvolucaoSheet(Spreadsheet $spreadsheet, array $data): void
    {
        $sheet = $spreadsheet->createSheet()->setTitle('Evolução Diária');

        $this->tableHeader($sheet, 1, ['A' => 'Data', 'B' => 'Respostas no dia', 'C' => 'Acumulado']);

        $row        = 2;
        $acumulado  = 0;
        foreach ($data['dailyResponses'] as $date => $cnt) {
            $parts = explode('-', $date);
            $sheet->setCellValue("A{$row}", $parts[2] . '/' . $parts[1] . '/' . $parts[0]);
            $sheet->setCellValue("B{$row}", $cnt);
            $acumulado += $cnt;
            $sheet->setCellValue("C{$row}", $acumulado);
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:C{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
            }
            $row++;
        }

        // Linha de total
        $lastData = $row - 1;
        $sheet->setCellValue("A{$row}", 'TOTAL');
        $sheet->setCellValue("B{$row}", "=SUM(B2:B{$lastData})");
        $sheet->getStyle("A{$row}:C{$row}")->applyFromArray([
            'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '059669']],
        ]);

        $sheet->getColumnDimension('A')->setWidth(16);
        $sheet->getColumnDimension('B')->setWidth(22);
        $sheet->getColumnDimension('C')->setWidth(16);
    }

    // ──────────────────────────────────────────────────────────────────
    // Helpers de estilo
    // ──────────────────────────────────────────────────────────────────

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
        [$start, $end] = explode(':', $range);
        preg_match('/\d+/', $start, $m);
        $sheet->getRowDimension($m[0])->setRowHeight(20);
    }

    private function tableHeader($sheet, int $row, array $columns): void
    {
        foreach ($columns as $col => $label) {
            $sheet->setCellValue("{$col}{$row}", $label);
        }
        $cols   = array_keys($columns);
        $range  = $cols[0] . $row . ':' . end($cols) . $row;
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
}
