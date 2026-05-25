<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="utf-8">
<title>Avaliação de Desempenho — {{ $cycle->name }}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; }
  body { font-family: 'DejaVu Sans', sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

  .page-header {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
    color: white; padding: 18px 24px; margin-bottom: 20px; border-radius: 4px;
  }
  .page-header h1 { font-size: 16px; font-weight: bold; margin-bottom: 4px; }
  .page-header p  { font-size: 10px; opacity: 0.85; }

  .section { margin-bottom: 18px; }
  .section-title {
    background: #334155; color: white; padding: 6px 10px;
    font-size: 10px; font-weight: bold; letter-spacing: 0.5px;
    text-transform: uppercase; border-radius: 3px 3px 0 0; margin-bottom: 0;
  }

  .stats-grid { display: table; width: 100%; border-collapse: collapse; margin-bottom: 18px; }
  .stat-cell {
    display: table-cell; width: 25%; padding: 10px 14px;
    border: 1px solid #e2e8f0; text-align: center; background: #f8fafc;
  }
  .stat-value { font-size: 22px; font-weight: bold; }
  .stat-label { font-size: 9px; color: #64748b; margin-top: 2px; }
  .stat-indigo .stat-value { color: #6366f1; }
  .stat-green  .stat-value { color: #059669; }
  .stat-blue   .stat-value { color: #0891b2; }
  .stat-amber  .stat-value { color: #d97706; }

  table { width: 100%; border-collapse: collapse; font-size: 10px; }
  thead tr { background: #6366f1; color: white; }
  thead th { padding: 7px 8px; text-align: left; font-weight: bold; }
  tbody tr:nth-child(even) { background: #f8fafc; }
  tbody td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; }
  tbody tr:hover { background: #ede9fe; }

  .badge {
    display: inline-block; padding: 2px 7px; border-radius: 10px;
    font-size: 9px; font-weight: bold; letter-spacing: 0.3px;
  }
  .badge-green  { background: #dcfce7; color: #15803d; }
  .badge-yellow { background: #fef9c3; color: #854d0e; }
  .badge-orange { background: #ffedd5; color: #9a3412; }
  .badge-red    { background: #fee2e2; color: #991b1b; }
  .badge-gray   { background: #f1f5f9; color: #475569; }
  .badge-indigo { background: #ede9fe; color: #4338ca; }

  .score-bar { background: #e2e8f0; border-radius: 3px; height: 8px; width: 100%; }
  .score-fill { height: 8px; border-radius: 3px; }

  .criteria-section table thead tr { background: #64748b; }

  .comparison-table table thead tr { background: #0891b2; }

  .footer {
    margin-top: 24px; padding-top: 10px; border-top: 1px solid #e2e8f0;
    font-size: 9px; color: #94a3b8; text-align: center;
  }

  .calibrated-mark { color: #d97706; font-size: 9px; font-style: italic; }

  h2.section-heading {
    font-size: 12px; font-weight: bold; color: #4f46e5;
    border-bottom: 2px solid #c7d2fe; padding-bottom: 4px; margin-bottom: 10px; margin-top: 16px;
  }
</style>
</head>
<body>

{{-- ── Cabeçalho ─────────────────────────────────────────────── --}}
<div class="page-header">
  <h1>Avaliação de Desempenho — {{ $cycle->name }}</h1>
  <p>Período: {{ $cycle->periodLabel }} &nbsp;|&nbsp; Status: {{ $cycle->statusLabel }} &nbsp;|&nbsp; Gerado em: {{ now()->format('d/m/Y H:i') }}</p>
</div>

{{-- ── Estatísticas Gerais ────────────────────────────────────── --}}
<div class="section">
  <div class="section-title">Estatísticas Gerais</div>
  <div class="stats-grid">
    <div class="stat-cell stat-indigo">
      <div class="stat-value">{{ $stats['total_managers'] }}</div>
      <div class="stat-label">Gerentes</div>
    </div>
    <div class="stat-cell stat-green">
      <div class="stat-value">{{ $stats['total_evaluated'] }}</div>
      <div class="stat-label">Colaboradores Avaliados</div>
    </div>
    <div class="stat-cell stat-blue">
      <div class="stat-value">{{ $stats['avg_score'] !== null ? number_format($stats['avg_score'], 2, ',', '') : '—' }}</div>
      <div class="stat-label">Nota Média Geral</div>
    </div>
    <div class="stat-cell stat-amber">
      <div class="stat-value">{{ $stats['avg_self_score'] !== null ? number_format($stats['avg_self_score'], 2, ',', '') : '—' }}</div>
      <div class="stat-label">Média Autoavaliação</div>
    </div>
  </div>
</div>

{{-- ── Notas por Critério ─────────────────────────────────────── --}}
<div class="section criteria-section">
  <div class="section-title">Desempenho por Critério</div>
  <table>
    <thead>
      <tr>
        <th style="width:40%">Critério</th>
        <th style="width:18%">Tipo de Resposta</th>
        <th style="width:10%">Peso</th>
        <th style="width:15%">Média</th>
        <th style="width:10%">Avaliações</th>
        <th style="width:7%">Gráfico</th>
      </tr>
    </thead>
    <tbody>
      @foreach($criteriaStats as $cs)
        @php
          $avg = $cs['avg'];
          $pct = $avg !== null ? round(($avg / 5) * 100) : 0;
          $badgeClass = match(true) {
            $avg === null => 'badge-gray',
            $avg >= 4.5   => 'badge-green',
            $avg >= 3.5   => 'badge-yellow',
            $avg >= 2.5   => 'badge-orange',
            default       => 'badge-red',
          };
          $barColor = match(true) {
            $avg === null => '#94a3b8',
            $avg >= 4.5   => '#059669',
            $avg >= 3.5   => '#d97706',
            $avg >= 2.5   => '#f97316',
            default       => '#dc2626',
          };
        @endphp
        <tr>
          <td><strong>{{ $cs['criterion']->name }}</strong>
            @if($cs['criterion']->description)
              <br><span style="color:#94a3b8;font-size:9px">{{ $cs['criterion']->description }}</span>
            @endif
          </td>
          <td>{{ $cs['criterion']->responseTypeLabel }}</td>
          <td style="text-align:center">×{{ number_format($cs['criterion']->weight ?? 1.0, 1) }}</td>
          <td>
            @if($avg !== null)
              <span class="badge {{ $badgeClass }}">{{ number_format($avg, 2, ',', '') }}</span>
            @else
              <span style="color:#94a3b8">—</span>
            @endif
          </td>
          <td style="text-align:center">{{ $cs['count'] }}</td>
          <td>
            <div class="score-bar">
              <div class="score-fill" style="width:{{ $pct }}%;background:{{ $barColor }}"></div>
            </div>
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- ── Detalhes por Colaborador ───────────────────────────────── --}}
<h2 class="section-heading">Detalhes por Colaborador</h2>
<div class="section">
  <table>
    <thead>
      <tr>
        <th style="width:28%">Colaborador</th>
        <th style="width:18%">Gerente</th>
        <th style="width:14%">Nota Gerente</th>
        <th style="width:14%">Autoavaliação</th>
        <th style="width:14%">Diferença</th>
        <th style="width:12%">Calibrado</th>
      </tr>
    </thead>
    <tbody>
      @foreach($employeeScores as $es)
        @php
          $finalScore = $es['final_score'];
          $selfScore  = $es['self_score'];
          $diff       = ($finalScore !== null && $selfScore !== null) ? round($finalScore - $selfScore, 2) : null;

          $badgeClass = match(true) {
            $finalScore === null => 'badge-gray',
            $finalScore >= 4.5   => 'badge-green',
            $finalScore >= 3.5   => 'badge-yellow',
            $finalScore >= 2.5   => 'badge-orange',
            default              => 'badge-red',
          };
          $diffClass = match(true) {
            $diff === null   => 'badge-gray',
            $diff > 0.5      => 'badge-yellow',
            $diff < -0.5     => 'badge-red',
            default          => 'badge-green',
          };
        @endphp
        <tr>
          <td>
            <strong>{{ $es['employee']->name }}</strong>
            <br><span style="color:#94a3b8;font-size:9px">{{ $es['employee']->department?->name ?? '—' }}</span>
          </td>
          <td>{{ $es['manager']->name }}</td>
          <td style="text-align:center">
            @if($finalScore !== null)
              <span class="badge {{ $badgeClass }}">{{ number_format($finalScore, 2, ',', '') }}</span>
            @else
              <span style="color:#94a3b8">—</span>
            @endif
          </td>
          <td style="text-align:center">
            @if($selfScore !== null)
              <span class="badge badge-indigo">{{ number_format($selfScore, 2, ',', '') }}</span>
            @else
              <span style="color:#94a3b8">—</span>
            @endif
          </td>
          <td style="text-align:center">
            @if($diff !== null)
              <span class="badge {{ $diffClass }}">{{ ($diff > 0 ? '+' : '') . number_format($diff, 2, ',', '') }}</span>
            @else
              <span style="color:#94a3b8">—</span>
            @endif
          </td>
          <td style="text-align:center">
            @if($es['calibrated'])
              <span class="badge badge-yellow">Sim</span>
            @else
              <span style="color:#94a3b8">Não</span>
            @endif
          </td>
        </tr>
      @endforeach
    </tbody>
  </table>
</div>

{{-- ── Rodapé ──────────────────────────────────────────────────── --}}
<div class="footer">
  SistemDP &bull; Avaliação de Desempenho &bull; Ciclo: {{ $cycle->name }} &bull; {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
