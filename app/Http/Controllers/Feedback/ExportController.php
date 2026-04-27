<?php

namespace App\Http\Controllers\Feedback;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
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
    // Autorização + query base
    // ──────────────────────────────────────────────────────────────────

    private function authorizedQuery(): \Illuminate\Database\Eloquent\Builder
    {
        $user = Auth::user();

        if (! $user->podeGerenciarPesquisas()) {
            abort(403, 'Sem permissão para exportar feedbacks.');
        }

        $query = Feedback::with(['employee:id,name', 'evaluator:id,name', 'actionPlanResponsible:id,name'])
            ->orderBy('created_at', 'desc');

        if ($user->isGerente()) {
            $deptId       = $user->department_id;
            $employeeIds  = User::where('department_id', $deptId)->pluck('id');
            $query->whereIn('employee_id', $employeeIds);
        }

        return $query;
    }

    // ──────────────────────────────────────────────────────────────────
    // PDF
    // ──────────────────────────────────────────────────────────────────

    public function pdf()
    {
        $feedbacks = $this->authorizedQuery()->get();
        $user      = Auth::user();

        $pdf = Pdf::loadView('feedback.exports.pdf', compact('feedbacks', 'user'))
            ->setPaper('a4', 'landscape')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 96,
            ]);

        return $pdf->download('feedbacks-' . now()->format('Y-m-d') . '.pdf');
    }

    // ──────────────────────────────────────────────────────────────────
    // Excel
    // ──────────────────────────────────────────────────────────────────

    public function excel()
    {
        $feedbacks   = $this->authorizedQuery()->get();
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setTitle('Feedbacks — SistemDP')
            ->setCreator('SistemDP')
            ->setDescription('Exportação automática de feedbacks.');

        $this->buildFeedbackSheet($spreadsheet, $feedbacks);
        $this->buildResumoSheet($spreadsheet, $feedbacks);

        $filename = 'feedbacks-' . now()->format('Y-m-d') . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // ──────────────────────────────────────────────────────────────────
    // Sheet 1: Lista de feedbacks
    // ──────────────────────────────────────────────────────────────────

    private function buildFeedbackSheet(Spreadsheet $spreadsheet, $feedbacks): void
    {
        $sheet = $spreadsheet->getActiveSheet()->setTitle('Feedbacks');

        // Cabeçalho principal
        $this->mergeAndStyle($sheet, 'A1:L1', 'EXPORTAÇÃO DE FEEDBACKS — ' . now()->format('d/m/Y H:i'), [
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7c3aed']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Cabeçalhos das colunas
        $headers = ['ID', 'Colaborador', 'Avaliador', 'Tipo', 'Categoria', 'Gravidade', 'Nota', 'Data Ocorrido', 'Status', 'Plano de Ação', 'Prazo Plano', 'Anônimo'];
        $cols    = range('A', 'L');

        foreach ($headers as $i => $h) {
            $col = $cols[$i];
            $sheet->setCellValue("{$col}3", $h);
        }

        $sheet->getStyle('A3:L3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '334155']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        // Dados
        $row = 4;
        foreach ($feedbacks as $fb) {
            $isEven = ($row % 2 === 0);

            $sheet->setCellValue("A{$row}", $fb->id);
            $sheet->setCellValue("B{$row}", $fb->employee?->name ?? '—');
            $sheet->setCellValue("C{$row}", $fb->evaluator_name);
            $sheet->setCellValue("D{$row}", $fb->type_label);
            $sheet->setCellValue("E{$row}", $fb->category_label);
            $sheet->setCellValue("F{$row}", $fb->severity_label ?? '—');
            $sheet->setCellValue("G{$row}", $fb->rating ?? '—');
            $sheet->setCellValue("H{$row}", $fb->occurred_at?->format('d/m/Y') ?? '—');
            $sheet->setCellValue("I{$row}", $fb->status_label);
            $sheet->setCellValue("J{$row}", $fb->action_plan ?? '—');
            $sheet->setCellValue("K{$row}", $fb->action_plan_deadline?->format('d/m/Y') ?? '—');
            $sheet->setCellValue("L{$row}", $fb->is_anonymous ? 'Sim' : 'Não');

            // Cor do tipo
            $typeColor = match ($fb->type) {
                'reconhecimento' => '10b981',
                'sugestao'       => '3b82f6',
                'alerta'         => 'ef4444',
                default          => '64748b',
            };
            $sheet->getStyle("D{$row}")->getFont()
                ->setBold(true)
                ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB($typeColor));

            if ($isEven) {
                $sheet->getStyle("A{$row}:L{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
            }

            $row++;
        }

        // Borda na tabela
        if ($row > 4) {
            $this->applyBorderArea($sheet, "A3:L" . ($row - 1));
        }

        // Larguras
        $widths = [6, 28, 22, 16, 20, 14, 8, 14, 20, 45, 14, 10];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimension($cols[$i])->setWidth($w);
        }
    }

    // ──────────────────────────────────────────────────────────────────
    // Sheet 2: Resumo / Totais
    // ──────────────────────────────────────────────────────────────────

    private function buildResumoSheet(Spreadsheet $spreadsheet, $feedbacks): void
    {
        $sheet = $spreadsheet->createSheet()->setTitle('Resumo');

        $total          = $feedbacks->count();
        $reconhecimento = $feedbacks->where('type', 'reconhecimento')->count();
        $sugestao       = $feedbacks->where('type', 'sugestao')->count();
        $alerta         = $feedbacks->where('type', 'alerta')->count();
        $criticos       = $feedbacks->where('severity', 'critico')->count();
        $abertos        = $feedbacks->whereIn('status', ['aberto', 'em_analise', 'aguardando_plano', 'plano_em_andamento'])->count();
        $resolvidos     = $feedbacks->where('status', 'resolvido')->count();
        $arquivados     = $feedbacks->where('status', 'arquivado')->count();

        // Título
        $this->mergeAndStyle($sheet, 'A1:D1', 'RESUMO DE FEEDBACKS', [
            'font'      => ['bold' => true, 'size' => 13, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '7c3aed']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Por tipo
        $this->sectionHeader($sheet, 'A3:D3', 'POR TIPO', '334155');

        $tipoRows = [
            ['Total de feedbacks',   $total,          '7c3aed'],
            ['Reconhecimentos',      $reconhecimento, '10b981'],
            ['Sugestões',            $sugestao,       '3b82f6'],
            ['Alertas',              $alerta,         'ef4444'],
            ['Alertas críticos',     $criticos,       'dc2626'],
        ];

        $row = 4;
        foreach ($tipoRows as [$label, $value, $color]) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $value);
            $sheet->setCellValue("C{$row}", $total > 0 ? round(($value / $total) * 100, 1) . '%' : '0%');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(12)
                ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB($color));
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:D{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
            }
            $row++;
        }

        // Por status
        $row++;
        $this->sectionHeader($sheet, "A{$row}:D{$row}", 'POR STATUS', '334155');
        $row++;

        $statusRows = [
            ['Em aberto (pendentes)', $abertos,    '0891b2'],
            ['Resolvidos',           $resolvidos, '059669'],
            ['Arquivados',           $arquivados, '64748b'],
        ];

        foreach ($statusRows as [$label, $value, $color]) {
            $sheet->setCellValue("A{$row}", $label);
            $sheet->setCellValue("B{$row}", $value);
            $sheet->setCellValue("C{$row}", $total > 0 ? round(($value / $total) * 100, 1) . '%' : '0%');
            $sheet->getStyle("A{$row}")->getFont()->setBold(true);
            $sheet->getStyle("B{$row}")->getFont()->setBold(true)->setSize(12)
                ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB($color));
            if ($row % 2 === 0) {
                $sheet->getStyle("A{$row}:D{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f8fafc');
            }
            $row++;
        }

        // Top colaboradores com mais alertas
        $topAlerts = $feedbacks->where('type', 'alerta')
            ->groupBy('employee_id')
            ->map(fn($g) => ['name' => $g->first()->employee?->name ?? '—', 'count' => $g->count()])
            ->sortByDesc('count')
            ->take(10);

        if ($topAlerts->isNotEmpty()) {
            $row++;
            $this->sectionHeader($sheet, "A{$row}:D{$row}", 'TOP COLABORADORES COM ALERTAS', '7f1d1d');
            $row++;

            $this->tableHeader($sheet, $row, ['A' => 'Colaborador', 'B' => 'Qtd. Alertas']);
            $row++;

            foreach ($topAlerts as $item) {
                $sheet->setCellValue("A{$row}", $item['name']);
                $sheet->setCellValue("B{$row}", $item['count']);
                $sheet->getStyle("B{$row}")->getFont()->setBold(true)
                    ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('ef4444'));
                if ($row % 2 === 0) {
                    $sheet->getStyle("A{$row}:B{$row}")->getFill()
                        ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('fff1f2');
                }
                $row++;
            }
        }

        // Widths
        $sheet->getColumnDimension('A')->setWidth(30);
        $sheet->getColumnDimension('B')->setWidth(18);
        $sheet->getColumnDimension('C')->setWidth(14);
        $sheet->getColumnDimension('D')->setWidth(14);
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
        [$start] = explode(':', $range);
        preg_match('/\d+/', $start, $m);
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
}
