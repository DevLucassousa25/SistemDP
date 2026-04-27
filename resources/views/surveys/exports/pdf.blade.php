<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 10px;
    color: #1e293b;
    line-height: 1.5;
    background: #ffffff;
}

/* ── Cabeçalho ──────────────────────────────────────────────── */
.page-header {
    background-color: #059669;
    color: #ffffff;
    padding: 14px 20px;
    margin-bottom: 18px;
}
.page-header h1 {
    font-size: 15px;
    font-weight: bold;
    margin-bottom: 3px;
}
.page-header p {
    font-size: 9px;
    opacity: 0.85;
}
.badge {
    display: inline-block;
    padding: 2px 8px;
    border-radius: 20px;
    font-size: 9px;
    font-weight: bold;
    margin-left: 6px;
    vertical-align: middle;
}
.badge-ativa     { background: #d1fae5; color: #065f46; }
.badge-encerrada { background: #e2e8f0; color: #475569; }
.badge-rascunho  { background: #fef3c7; color: #92400e; }

/* ── Cards de info ───────────────────────────────────────────── */
.info-grid {
    width: 100%;
    margin-bottom: 18px;
    border-collapse: separate;
    border-spacing: 6px;
}
.info-grid td {
    width: 50%;
    vertical-align: top;
    padding: 0;
}
.info-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 10px 12px;
}
.info-box .label {
    font-size: 8.5px;
    color: #64748b;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-bottom: 2px;
}
.info-box .value {
    font-size: 10.5px;
    font-weight: bold;
    color: #1e293b;
}

/* ── Stats ───────────────────────────────────────────────────── */
.stats-row {
    width: 100%;
    margin-bottom: 20px;
    border-collapse: separate;
    border-spacing: 6px;
}
.stats-row td { width: 25%; }
.stat-card {
    border-radius: 8px;
    padding: 10px 8px;
    text-align: center;
}
.stat-card .num {
    font-size: 20px;
    font-weight: bold;
    line-height: 1.1;
}
.stat-card .lbl {
    font-size: 8.5px;
    margin-top: 2px;
}
.stat-card .sub {
    font-size: 8px;
    margin-top: 1px;
}
.s-gray  { background: #f1f5f9; color: #1e293b; }
.s-green { background: #d1fae5; color: #065f46; }
.s-blue  { background: #e0f2fe; color: #075985; }
.s-amber { background: #fef3c7; color: #92400e; }

/* ── Seção ───────────────────────────────────────────────────── */
.section-title {
    font-size: 10px;
    font-weight: bold;
    color: #334155;
    border-bottom: 2px solid #e2e8f0;
    padding-bottom: 4px;
    margin-bottom: 12px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ── Card por pergunta ───────────────────────────────────────── */
.q-card {
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    overflow: hidden;
    margin-bottom: 14px;
    page-break-inside: avoid;
}
.q-header {
    padding: 9px 12px;
    border-bottom: 1px solid #e2e8f0;
}
.q-num {
    font-size: 9px;
    color: #64748b;
    font-weight: bold;
    margin-bottom: 2px;
}
.q-text {
    font-size: 10.5px;
    font-weight: bold;
    color: #1e293b;
}
.q-badge {
    display: inline-block;
    font-size: 8px;
    font-weight: bold;
    padding: 1px 6px;
    border-radius: 4px;
    margin-top: 3px;
}
.q-badge-scale { background: #e0e7ff; color: #3730a3; }
.q-badge-mc    { background: #ede9fe; color: #6d28d9; }
.q-badge-text  { background: #ccfbf1; color: #0f766e; }
.q-body { padding: 12px; }

/* ── Métricas rápidas de escala ──────────────────────────────── */
.metrics-row {
    width: 100%;
    border-collapse: separate;
    border-spacing: 5px;
    margin-bottom: 12px;
}
.metrics-row td { width: 25%; }
.metric-box {
    border-radius: 6px;
    padding: 8px;
    text-align: center;
}
.metric-box .mval { font-size: 16px; font-weight: bold; }
.metric-box .mlbl { font-size: 8px; margin-bottom: 2px; }
.metric-box .msub { font-size: 7.5px; }
.m-avg   { background: #f1f5f9; }
.m-prom  { background: #d1fae5; color: #065f46; }
.m-neut  { background: #fef3c7; color: #92400e; }
.m-detr  { background: #fee2e2; color: #991b1b; }

/* ── Tabelas de dados ────────────────────────────────────────── */
.data-table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 8px;
    font-size: 9.5px;
}
.data-table th {
    background: #64748b;
    color: #ffffff;
    padding: 5px 8px;
    text-align: left;
    font-size: 8.5px;
    text-transform: uppercase;
    letter-spacing: 0.4px;
}
.data-table td {
    padding: 5px 8px;
    border-bottom: 1px solid #f1f5f9;
    vertical-align: top;
}
.data-table tr:nth-child(even) td { background: #f8fafc; }
.data-table .num-col { text-align: right; width: 70px; }
.data-table .pct-col { text-align: right; width: 70px; }

/* ── Barras CSS ──────────────────────────────────────────────── */
.bar-wrap {
    background: #f1f5f9;
    border-radius: 3px;
    height: 10px;
    width: 100%;
    overflow: hidden;
}
.bar-fill {
    height: 10px;
    border-radius: 3px;
}

/* ── NPS ─────────────────────────────────────────────────────── */
.nps-box {
    border-radius: 6px;
    padding: 10px 12px;
    margin-top: 10px;
    border: 1px solid;
}
.nps-box.positive { background: #d1fae5; border-color: #a7f3d0; }
.nps-box.neutral  { background: #fef3c7; border-color: #fde68a; }
.nps-box.negative { background: #fee2e2; border-color: #fca5a5; }
.nps-score {
    font-size: 28px;
    font-weight: bold;
    float: right;
    line-height: 1;
    margin-top: -2px;
}
.nps-label { font-size: 8px; text-transform: uppercase; letter-spacing: 0.5px; font-weight: bold; }
.nps-sublabel { font-size: 9px; margin-top: 2px; }
.nps-bar-wrap {
    margin-top: 8px;
    height: 10px;
    background: #e2e8f0;
    border-radius: 5px;
    overflow: hidden;
    display: table;
    width: 100%;
}
.nps-seg { display: table-cell; height: 10px; }
.nps-legend { font-size: 8px; margin-top: 5px; }
.nps-legend span { margin-right: 12px; }
.dot { display: inline-block; width: 7px; height: 7px; border-radius: 50%; margin-right: 3px; vertical-align: middle; }
.dot-red   { background: #f87171; }
.dot-amber { background: #fbbf24; }
.dot-green { background: #34d399; }

/* ── Texto livre ─────────────────────────────────────────────── */
.text-response {
    padding: 6px 10px;
    border-left: 3px solid #059669;
    background: #f8fafc;
    border-radius: 0 4px 4px 0;
    margin-bottom: 5px;
    font-size: 9.5px;
    color: #334155;
}
.text-num {
    font-size: 8px;
    font-weight: bold;
    color: #94a3b8;
    margin-bottom: 2px;
}

/* ── Rodapé ──────────────────────────────────────────────────── */
.page-footer {
    margin-top: 24px;
    padding-top: 8px;
    border-top: 1px solid #e2e8f0;
    font-size: 8.5px;
    color: #94a3b8;
    text-align: center;
}
</style>
</head>
<body>

{{-- ── Cabeçalho ──────────────────────────────────────────────── --}}
<div class="page-header">
    <h1>
        {{ $survey->title }}
        <span class="badge badge-{{ $survey->status }}">{{ $survey->status_label }}</span>
    </h1>
    <p>
        Dashboard de Resultados &nbsp;·&nbsp;
        Criada por {{ $survey->creator?->name ?? 'N/A' }} &nbsp;·&nbsp;
        {{ $survey->is_anonymous ? 'Respostas anônimas' : 'Respostas identificadas' }} &nbsp;·&nbsp;
        Exportado em {{ now()->format('d/m/Y \à\s H:i') }}
    </p>
</div>

{{-- ── Informações ─────────────────────────────────────────────── --}}
<table class="info-grid">
    <tr>
        <td>
            <div class="info-box">
                <div class="label">Período</div>
                <div class="value">
                    @if ($survey->start_date && $survey->end_date)
                        {{ $survey->start_date->format('d/m/Y') }} a {{ $survey->end_date->format('d/m/Y') }}
                    @elseif ($survey->start_date)
                        A partir de {{ $survey->start_date->format('d/m/Y') }}
                    @elseif ($survey->end_date)
                        Até {{ $survey->end_date->format('d/m/Y') }}
                    @else
                        Sem período definido
                    @endif
                </div>
            </div>
        </td>
        <td>
            <div class="info-box">
                <div class="label">Público-alvo</div>
                <div class="value">{{ $survey->audience_label }}</div>
            </div>
        </td>
    </tr>
</table>

{{-- ── Stats ───────────────────────────────────────────────────── --}}
<table class="stats-row">
    <tr>
        <td>
            <div class="stat-card s-gray">
                <div class="num" style="color:#1e293b;">{{ $stats['total_responses'] }}</div>
                <div class="lbl">Respostas</div>
                <div class="sub">de {{ $stats['total_invited'] }} convidados</div>
            </div>
        </td>
        <td>
            <div class="stat-card s-green">
                <div class="num">{{ $stats['response_rate'] }}%</div>
                <div class="lbl">Taxa de resposta</div>
                <div class="sub">&nbsp;</div>
            </div>
        </td>
        <td>
            <div class="stat-card s-blue">
                <div class="num">{{ $stats['total_questions'] }}</div>
                <div class="lbl">Perguntas</div>
                <div class="sub">no questionário</div>
            </div>
        </td>
        <td>
            <div class="stat-card s-amber">
                <div class="num">{{ $survey->target_audience === 'todos' ? 'Todos' : count($survey->target_department_ids ?? []) }}</div>
                <div class="lbl">{{ $survey->target_audience === 'todos' ? 'Colaboradores' : 'Departamentos' }}</div>
                <div class="sub">&nbsp;</div>
            </div>
        </td>
    </tr>
</table>

{{-- ── Análise por pergunta ────────────────────────────────────── --}}
<div class="section-title">Análise por Pergunta</div>

@foreach ($questionResults as $i => $qr)
    @php
        $headerColors = ['#059669','#0891b2','#7c3aed','#d97706','#dc2626','#4f46e5'];
        $hc = $headerColors[$i % count($headerColors)];
    @endphp
    <div class="q-card">
        <div class="q-header" style="background: {{ $hc }}18; border-left: 4px solid {{ $hc }};">
            <div class="q-num">Pergunta {{ $i + 1 }}</div>
            <div class="q-text">{{ $qr['question'] }}</div>
            <div>
                @if ($qr['type'] === 'escala')
                    <span class="q-badge q-badge-scale">Escala 0–10</span>
                @elseif ($qr['type'] === 'multipla_escolha')
                    <span class="q-badge q-badge-mc">Múltipla escolha</span>
                @else
                    <span class="q-badge q-badge-text">Texto livre</span>
                @endif
                <span style="font-size:8.5px; color:#94a3b8; margin-left:6px;">
                    {{ $qr['count'] }} {{ $qr['count'] === 1 ? 'resposta' : 'respostas' }}
                </span>
            </div>
        </div>

        <div class="q-body">

            {{-- Escala --}}
            @if ($qr['type'] === 'escala' && $qr['count'] > 0)
                <table class="metrics-row">
                    <tr>
                        <td>
                            <div class="metric-box m-avg">
                                <div class="mlbl" style="color:#64748b;">Média</div>
                                <div class="mval" style="color:{{ $qr['average'] >= 7 ? '#059669' : ($qr['average'] >= 5 ? '#d97706' : '#dc2626') }};">
                                    {{ $qr['average'] }}
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="metric-box m-prom">
                                <div class="mlbl">Promotores</div>
                                <div class="mval">{{ $qr['promoters'] }}</div>
                                <div class="msub">nota 9–10</div>
                            </div>
                        </td>
                        <td>
                            <div class="metric-box m-neut">
                                <div class="mlbl">Neutros</div>
                                <div class="mval">{{ $qr['passives'] }}</div>
                                <div class="msub">nota 7–8</div>
                            </div>
                        </td>
                        <td>
                            <div class="metric-box m-detr">
                                <div class="mlbl">Detratores</div>
                                <div class="mval">{{ $qr['detractors'] }}</div>
                                <div class="msub">nota 0–6</div>
                            </div>
                        </td>
                    </tr>
                </table>

                {{-- Distribuição 0–10 --}}
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Nota</th>
                            <th>Respostas</th>
                            <th>%</th>
                            <th style="width:45%;">Distribuição</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($qr['distribution'] as $note => $cnt)
                            @php
                                $pct = $qr['total'] > 0 ? round(($cnt / $qr['total']) * 100, 1) : 0;
                                $barColor = match(true) {
                                    $note >= 9  => '#22c55e',
                                    $note >= 7  => '#a3e635',
                                    $note >= 5  => '#eab308',
                                    $note >= 3  => '#f97316',
                                    default     => '#ef4444',
                                };
                            @endphp
                            <tr>
                                <td style="font-weight:bold; text-align:center;">{{ $note }}</td>
                                <td class="num-col">{{ $cnt }}</td>
                                <td class="pct-col">{{ $pct }}%</td>
                                <td>
                                    <div class="bar-wrap">
                                        <div class="bar-fill" style="width:{{ $pct }}%; background-color:{{ $barColor }};"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

                {{-- NPS --}}
                @if ($qr['nps'] !== null && $qr['total'] > 0)
                    @php
                        $pctP = $qr['total'] > 0 ? round(($qr['promoters']  / $qr['total']) * 100, 1) : 0;
                        $pctN = $qr['total'] > 0 ? round(($qr['passives']   / $qr['total']) * 100, 1) : 0;
                        $pctD = $qr['total'] > 0 ? round(($qr['detractors'] / $qr['total']) * 100, 1) : 0;
                        $npsClass = $qr['nps'] >= 50 ? 'positive' : ($qr['nps'] >= 0 ? 'neutral' : 'negative');
                        $npsColor = $qr['nps'] >= 50 ? '#059669' : ($qr['nps'] >= 0 ? '#d97706' : '#dc2626');
                        $npsLbl   = $qr['nps'] >= 75 ? 'Excelente' : ($qr['nps'] >= 50 ? 'Muito bom' : ($qr['nps'] >= 0 ? 'Bom' : ($qr['nps'] >= -25 ? 'Atenção' : 'Crítico')));
                    @endphp
                    <div class="nps-box {{ $npsClass }}">
                        <div class="nps-score" style="color:{{ $npsColor }};">{{ $qr['nps'] > 0 ? '+' : '' }}{{ $qr['nps'] }}</div>
                        <div class="nps-label" style="color:{{ $npsColor }};">NPS — Net Promoter Score</div>
                        <div class="nps-sublabel" style="color:{{ $npsColor }};">{{ $npsLbl }}</div>
                        <div style="clear:both; margin-top:6px;"></div>
                        <table class="nps-bar-wrap">
                            <tr>
                                @if ($pctD > 0)
                                    <td class="nps-seg" style="width:{{ $pctD }}%; background:#f87171;"></td>
                                @endif
                                @if ($pctN > 0)
                                    <td class="nps-seg" style="width:{{ $pctN }}%; background:#fbbf24;"></td>
                                @endif
                                @if ($pctP > 0)
                                    <td class="nps-seg" style="width:{{ $pctP }}%; background:#34d399;"></td>
                                @endif
                            </tr>
                        </table>
                        <div class="nps-legend">
                            <span><span class="dot dot-red"></span>Detratores {{ $pctD }}%</span>
                            <span><span class="dot dot-amber"></span>Neutros {{ $pctN }}%</span>
                            <span><span class="dot dot-green"></span>Promotores {{ $pctP }}%</span>
                        </div>
                    </div>
                @endif

            {{-- Múltipla escolha --}}
            @elseif ($qr['type'] === 'multipla_escolha' && $qr['count'] > 0)
                @php
                    $mcColors = ['#10b981','#06b6d4','#8b5cf6','#f59e0b','#ef4444','#6366f1','#ec4899','#84cc16'];
                    $ci = 0;
                @endphp
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Opção</th>
                            <th class="num-col">Respostas</th>
                            <th class="pct-col">%</th>
                            <th style="width:40%;">Distribuição</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($qr['counts'] as $option => $cnt)
                            @php
                                $pct   = $qr['percentages'][$option] ?? 0;
                                $color = $mcColors[$ci % count($mcColors)];
                                $ci++;
                            @endphp
                            <tr>
                                <td>{{ $option }}</td>
                                <td class="num-col" style="font-weight:bold;">{{ $cnt }}</td>
                                <td class="pct-col" style="font-weight:bold;">{{ $pct }}%</td>
                                <td>
                                    <div class="bar-wrap">
                                        <div class="bar-fill" style="width:{{ $pct }}%; background-color:{{ $color }};"></div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>

            {{-- Texto livre --}}
            @elseif ($qr['type'] === 'texto_livre' && $qr['count'] > 0)
                @foreach ($qr['texts'] as $ti => $text)
                    <div class="text-response">
                        <div class="text-num">#{{ $ti + 1 }}</div>
                        {{ $text }}
                    </div>
                @endforeach

            @else
                <p style="color:#94a3b8; font-style:italic; font-size:9px;">Nenhuma resposta registrada para esta pergunta.</p>
            @endif

        </div>
    </div>
@endforeach

{{-- ── Evolução diária ─────────────────────────────────────────── --}}
@if (! empty($dailyResponses))
    <div class="section-title" style="margin-top:20px;">Evolução Diária das Respostas</div>
    <table class="data-table">
        <thead>
            <tr>
                <th>Data</th>
                <th class="num-col">Respostas no dia</th>
                <th class="num-col">Acumulado</th>
            </tr>
        </thead>
        <tbody>
            @php $acum = 0; @endphp
            @foreach ($dailyResponses as $date => $cnt)
                @php
                    $parts = explode('-', $date);
                    $acum += $cnt;
                @endphp
                <tr>
                    <td>{{ $parts[2] }}/{{ $parts[1] }}/{{ $parts[0] }}</td>
                    <td class="num-col">{{ $cnt }}</td>
                    <td class="num-col">{{ $acum }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@endif

{{-- ── Rodapé ───────────────────────────────────────────────────── --}}
<div class="page-footer">
    Relatório gerado automaticamente pelo SistemDP &nbsp;·&nbsp; {{ now()->format('d/m/Y H:i') }}
</div>

</body>
</html>
