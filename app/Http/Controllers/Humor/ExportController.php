<?php

namespace App\Http\Controllers\Humor;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\MoodCheckin;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class ExportController extends Controller
{
    // ── Autorização ───────────────────────────────────────────────────

    private function authorize(): void
    {
        $user = Auth::user();
        if (! $user || (! $user->isRhOuDp() && ! $user->isAdmin())) {
            abort(403, 'Sem permissão para exportar relatório de humor.');
        }
    }

    // ── PDF diário ────────────────────────────────────────────────────

    public function pdfHoje()
    {
        $this->authorize();

        $data = $this->dadosHoje();

        $pdf = Pdf::loadView('humor.exports.pdf-hoje', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 96,
            ]);

        return $pdf->download('humor-equipes-' . today()->format('Y-m-d') . '.pdf');
    }

    // ── PDF mensal ────────────────────────────────────────────────────

    public function pdfMensal(Request $request)
    {
        $this->authorize();

        $mes = $request->query('mes', now()->format('Y-m'));

        // valida formato YYYY-MM
        if (! preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $mes = now()->format('Y-m');
        }

        $data = $this->dadosMensal($mes);

        $pdf = Pdf::loadView('humor.exports.pdf-mensal', $data)
            ->setPaper('a4', 'portrait')
            ->setOptions([
                'defaultFont'          => 'DejaVu Sans',
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled'      => false,
                'dpi'                  => 96,
            ]);

        $filename = 'humor-equipes-mensal-' . $mes . '.pdf';

        return $pdf->download($filename);
    }

    // ── Excel mensal ──────────────────────────────────────────────────

    public function excelMensal(Request $request)
    {
        $this->authorize();

        $mes = $request->query('mes', now()->format('Y-m'));
        if (! preg_match('/^\d{4}-\d{2}$/', $mes)) {
            $mes = now()->format('Y-m');
        }

        $data        = $this->dadosMensal($mes);
        $spreadsheet = new Spreadsheet();

        $spreadsheet->getProperties()
            ->setTitle('Humor das Equipes — ' . $data['labelMes'])
            ->setCreator('SistemDP')
            ->setDescription('Relatório mensal de humor das equipes.');

        $this->buildSheetCheckins($spreadsheet, $data);
        $this->buildSheetResumo($spreadsheet, $data);
        $this->buildSheetPessimos($spreadsheet, $data);

        $filename = 'humor-equipes-' . $mes . '.xlsx';

        return response()->streamDownload(function () use ($spreadsheet) {
            $writer = new Xlsx($spreadsheet);
            $writer->save('php://output');
        }, $filename, [
            'Content-Type'  => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
            'Cache-Control' => 'max-age=0',
        ]);
    }

    // ══════════════════════════════════════════════════════════════════
    // Montagem dos dados
    // ══════════════════════════════════════════════════════════════════

    private function dadosHoje(): array
    {
        $totalFuncionarios = User::whereHas('accessProfile', fn ($q) => $q->where('slug', '!=', 'administrator'))
            ->count();

        $checkins = MoodCheckin::with(['user.department'])
            ->where('checkin_date', today())
            ->orderByRaw("CASE mood WHEN 'pessimo' THEN 0 WHEN 'normal' THEN 1 WHEN 'bem' THEN 2 WHEN 'otimo' THEN 3 END")
            ->orderBy('created_at', 'desc')
            ->get();

        $total     = $checkins->count();
        $dist      = $checkins->groupBy('mood')->map->count();
        $pessimos  = $dist->get('pessimo', 0);
        $participacao = $totalFuncionarios > 0 ? round($total / $totalFuncionarios * 100) : 0;

        // Pessimos com info de gerente
        $pessimosDetalhados = $checkins
            ->where('mood', 'pessimo')
            ->map(function ($checkin) {
                $user  = $checkin->user;
                $dept  = $user->department;
                $gerente = null;
                if ($dept) {
                    $gerente = User::where('department_id', $dept->id)
                        ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
                        ->first(['id', 'name', 'email']);
                }
                return [
                    'user_nome'     => $user->name,
                    'departamento'  => $dept?->name ?? 'Sem departamento',
                    'nota'          => $checkin->note,
                    'horario'       => $checkin->created_at->format('H:i'),
                    'gerente_nome'  => $gerente?->name  ?? 'Sem gestor',
                    'gerente_email' => $gerente?->email ?? null,
                ];
            })->values();

        // Por departamento
        $porDept = $checkins
            ->groupBy(fn ($c) => $c->user->department?->name ?? 'Sem departamento')
            ->map(function ($items, $dept) {
                $total = $items->count();
                $d     = $items->groupBy('mood')->map->count();
                return [
                    'departamento' => $dept,
                    'total'        => $total,
                    'otimo'        => $d->get('otimo', 0),
                    'bem'          => $d->get('bem', 0),
                    'normal'       => $d->get('normal', 0),
                    'pessimo'      => $d->get('pessimo', 0),
                    'score'        => $total > 0
                        ? round((($d->get('otimo', 0) * 3) + ($d->get('bem', 0) * 2) + ($d->get('normal', 0) * 1)) / ($total * 3) * 100)
                        : null,
                ];
            })->sortByDesc('score')->values()->toArray();

        return compact(
            'totalFuncionarios', 'total', 'dist', 'pessimos',
            'participacao', 'pessimosDetalhados', 'porDept', 'checkins'
        );
    }

    private function dadosMensal(string $mes): array
    {
        [$ano, $numMes] = explode('-', $mes);
        $inicio  = Carbon::createFromDate($ano, $numMes, 1)->startOfDay();
        $fim     = $inicio->copy()->endOfMonth();
        $labelMes = $inicio->translatedFormat('F \d\e Y');

        $checkins = MoodCheckin::with(['user.department'])
            ->whereYear('checkin_date', $ano)
            ->whereMonth('checkin_date', $numMes)
            ->orderBy('checkin_date')
            ->orderBy('created_at')
            ->get();

        $total    = $checkins->count();
        $dist     = $checkins->groupBy('mood')->map->count();
        $pessimos = $dist->get('pessimo', 0);

        $score = $total > 0
            ? round((
                ($dist->get('otimo', 0) * 100) +
                ($dist->get('bem', 0) * 66) +
                ($dist->get('normal', 0) * 33) +
                ($dist->get('pessimo', 0) * 0)
            ) / $total)
            : null;

        // Ranking departamentos
        $ranking = $checkins
            ->groupBy(fn ($c) => $c->user->department?->name ?? 'Sem departamento')
            ->map(function ($items, $dept) {
                $total = $items->count();
                $d     = $items->groupBy('mood')->map->count();
                $sc    = $total > 0
                    ? round((($d->get('otimo', 0) * 100) + ($d->get('bem', 0) * 66) + ($d->get('normal', 0) * 33)) / $total)
                    : 0;
                return [
                    'departamento' => $dept,
                    'total'        => $total,
                    'score'        => $sc,
                    'otimo'        => $d->get('otimo', 0),
                    'bem'          => $d->get('bem', 0),
                    'normal'       => $d->get('normal', 0),
                    'pessimo'      => $d->get('pessimo', 0),
                ];
            })->sortByDesc('score')->values()->toArray();

        // Pessimos detalhados com gerente
        $pessimosDetalhados = $checkins
            ->where('mood', 'pessimo')
            ->map(function ($checkin) {
                $user  = $checkin->user;
                $dept  = $user->department;
                $gerente = null;
                if ($dept) {
                    $gerente = User::where('department_id', $dept->id)
                        ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
                        ->first(['id', 'name', 'email']);
                }
                return [
                    'data'          => $checkin->checkin_date->format('d/m/Y'),
                    'user_nome'     => $user->name,
                    'departamento'  => $dept?->name ?? 'Sem departamento',
                    'nota'          => $checkin->note,
                    'gerente_nome'  => $gerente?->name  ?? 'Sem gestor',
                    'gerente_email' => $gerente?->email ?? null,
                ];
            })->values();

        // Tendência por dia
        $tendencia = $checkins
            ->groupBy(fn ($c) => $c->checkin_date->format('d/m'))
            ->map(function ($items, $dia) {
                $d = $items->groupBy('mood')->map->count();
                return [
                    'dia'     => $dia,
                    'otimo'   => $d->get('otimo', 0),
                    'bem'     => $d->get('bem', 0),
                    'normal'  => $d->get('normal', 0),
                    'pessimo' => $d->get('pessimo', 0),
                    'total'   => $items->count(),
                ];
            })->values()->toArray();

        return compact(
            'mes', 'labelMes', 'inicio', 'fim',
            'total', 'dist', 'pessimos', 'score',
            'ranking', 'pessimosDetalhados', 'tendencia', 'checkins'
        );
    }

    // ══════════════════════════════════════════════════════════════════
    // Sheets Excel
    // ══════════════════════════════════════════════════════════════════

    private function buildSheetCheckins(Spreadsheet $spreadsheet, array $data): void
    {
        $sheet    = $spreadsheet->getActiveSheet()->setTitle('Check-ins');
        $checkins = $data['checkins'];

        // Cabeçalho principal
        $this->mergeAndStyle($sheet, 'A1:G1',
            'HUMOR DAS EQUIPES — ' . strtoupper($data['labelMes']) . ' — Gerado em ' . now()->format('d/m/Y H:i'),
            [
                'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
                'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0f766e']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ]
        );
        $sheet->getRowDimension(1)->setRowHeight(30);

        // Cabeçalhos das colunas
        $headers = ['Data', 'Colaborador', 'E-mail', 'Departamento', 'Humor', 'Nota', 'Horário Registro'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}3", $h);
        }
        $sheet->getStyle('A3:G3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '134e4a']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);
        $sheet->getRowDimension(3)->setRowHeight(20);

        $moodLabel = ['otimo' => 'Ótimo', 'bem' => 'Bem', 'normal' => 'Normal', 'pessimo' => 'Péssimo'];
        $moodColor = ['otimo' => '059669', 'bem' => '2563eb', 'normal' => 'd97706', 'pessimo' => 'e11d48'];

        $row = 4;
        foreach ($checkins as $c) {
            $isEven = ($row % 2 === 0);
            $sheet->setCellValue("A{$row}", $c->checkin_date->format('d/m/Y'));
            $sheet->setCellValue("B{$row}", $c->user->name);
            $sheet->setCellValue("C{$row}", $c->user->email);
            $sheet->setCellValue("D{$row}", $c->user->department?->name ?? 'Sem departamento');
            $sheet->setCellValue("E{$row}", $moodLabel[$c->mood] ?? $c->mood);
            $sheet->setCellValue("F{$row}", $c->note ?? '');
            $sheet->setCellValue("G{$row}", $c->created_at->format('H:i'));

            $color = $moodColor[$c->mood] ?? '64748b';
            $sheet->getStyle("E{$row}")->getFont()->setBold(true)
                ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB($color));

            if ($isEven) {
                $sheet->getStyle("A{$row}:G{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f0fdf4');
            }
            $row++;
        }

        if ($row > 4) {
            $this->applyBorderArea($sheet, "A3:G" . ($row - 1));
        }

        $widths = [12, 30, 30, 24, 12, 50, 14];
        foreach ($widths as $i => $w) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($w);
        }
    }

    private function buildSheetResumo(Spreadsheet $spreadsheet, array $data): void
    {
        $sheet   = $spreadsheet->createSheet()->setTitle('Resumo por Depto');
        $ranking = $data['ranking'];

        $this->mergeAndStyle($sheet, 'A1:G1', 'RESUMO POR DEPARTAMENTO — ' . strtoupper($data['labelMes']), [
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '0f766e']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        $headers = ['#', 'Departamento', 'Total', '😄 Ótimo', '🙂 Bem', '😐 Normal', '😔 Péssimo', 'Score'];
        foreach ($headers as $i => $h) {
            $col = chr(65 + $i);
            $sheet->setCellValue("{$col}3", $h);
        }
        $sheet->getStyle('A3:H3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '134e4a']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 4;
        foreach ($ranking as $i => $dept) {
            $isEven = ($row % 2 === 0);
            $sheet->setCellValue("A{$row}", $i + 1);
            $sheet->setCellValue("B{$row}", $dept['departamento']);
            $sheet->setCellValue("C{$row}", $dept['total']);
            $sheet->setCellValue("D{$row}", $dept['otimo']);
            $sheet->setCellValue("E{$row}", $dept['bem']);
            $sheet->setCellValue("F{$row}", $dept['normal']);
            $sheet->setCellValue("G{$row}", $dept['pessimo']);
            $sheet->setCellValue("H{$row}", $dept['score']);

            $scoreColor = $dept['score'] >= 70 ? '059669' : ($dept['score'] >= 40 ? 'd97706' : 'e11d48');
            $sheet->getStyle("H{$row}")->getFont()->setBold(true)
                ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB($scoreColor));

            if ($dept['pessimo'] > 0) {
                $sheet->getStyle("G{$row}")->getFont()->setBold(true)
                    ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('e11d48'));
            }

            if ($isEven) {
                $sheet->getStyle("A{$row}:H{$row}")->getFill()
                    ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB('f0fdf4');
            }
            $row++;
        }

        if ($row > 4) {
            $this->applyBorderArea($sheet, "A3:H" . ($row - 1));
        }

        foreach ([6, 30, 10, 10, 10, 10, 12, 10] as $i => $w) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($w);
        }

        foreach (range('A', 'H') as $col) {
            $sheet->getStyle("{$col}3:{$col}{$row}")->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_CENTER);
        }
        $sheet->getStyle("B3:B{$row}")->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_LEFT);
    }

    private function buildSheetPessimos(Spreadsheet $spreadsheet, array $data): void
    {
        $sheet    = $spreadsheet->createSheet()->setTitle('Registros Péssimo');
        $pessimos = $data['pessimosDetalhados'];

        $this->mergeAndStyle($sheet, 'A1:F1', 'REGISTROS PÉSSIMO — ' . strtoupper($data['labelMes']), [
            'font'      => ['bold' => true, 'size' => 12, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'be123c']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
        ]);
        $sheet->getRowDimension(1)->setRowHeight(30);

        foreach (['A' => 'Data', 'B' => 'Colaborador', 'C' => 'Departamento', 'D' => 'Observação', 'E' => 'Gestor', 'F' => 'E-mail Gestor'] as $col => $h) {
            $sheet->setCellValue("{$col}3", $h);
        }
        $sheet->getStyle('A3:F3')->applyFromArray([
            'font'      => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
            'fill'      => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '9f1239']],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
        ]);

        $row = 4;
        foreach ($pessimos as $p) {
            $sheet->setCellValue("A{$row}", $p['data']);
            $sheet->setCellValue("B{$row}", $p['user_nome']);
            $sheet->setCellValue("C{$row}", $p['departamento']);
            $sheet->setCellValue("D{$row}", $p['nota'] ?? '—');
            $sheet->setCellValue("E{$row}", $p['gerente_nome']);
            $sheet->setCellValue("F{$row}", $p['gerente_email'] ?? '—');

            $sheet->getStyle("A{$row}:F{$row}")->getFill()
                ->setFillType(Fill::FILL_SOLID)->getStartColor()
                ->setRGB($row % 2 === 0 ? 'fff1f2' : 'FFFFFF');

            $row++;
        }

        if ($row > 4) {
            $this->applyBorderArea($sheet, "A3:F" . ($row - 1));
        }

        foreach ([12, 30, 24, 50, 28, 32] as $i => $w) {
            $sheet->getColumnDimension(chr(65 + $i))->setWidth($w);
        }

        if ($pessimos->isEmpty()) {
            $sheet->setCellValue('A4', '✅ Nenhum registro péssimo no período.');
            $sheet->getStyle('A4')->getFont()->setItalic(true)
                ->setColor((new \PhpOffice\PhpSpreadsheet\Style\Color())->setRGB('059669'));
        }
    }

    // ══════════════════════════════════════════════════════════════════
    // Helpers
    // ══════════════════════════════════════════════════════════════════

    private function mergeAndStyle($sheet, string $range, string $text, array $style): void
    {
        [$start] = explode(':', $range);
        $sheet->setCellValue($start, $text);
        $sheet->mergeCells($range);
        $sheet->getStyle($range)->applyFromArray($style);
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
