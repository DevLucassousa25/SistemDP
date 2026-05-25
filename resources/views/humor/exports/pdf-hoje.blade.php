<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Humor das Equipes — {{ today()->format('d/m/Y') }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10px;
            color: #1e293b;
            background: #ffffff;
        }

        /* ── Cabeçalho ── */
        .header {
            background: linear-gradient(135deg, #0f766e 0%, #134e4a 100%);
            color: #ffffff;
            padding: 20px 24px 16px;
            margin-bottom: 18px;
        }
        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 6px;
        }
        .header h1 {
            font-size: 18px;
            font-weight: bold;
            letter-spacing: -0.5px;
        }
        .header .sub {
            font-size: 10px;
            opacity: 0.8;
            margin-top: 3px;
        }
        .header .badge {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            padding: 4px 10px;
            font-size: 10px;
            font-weight: bold;
            text-align: right;
        }

        /* ── Cards de stat ── */
        .stats-grid {
            display: table;
            width: 100%;
            margin-bottom: 16px;
            border-collapse: separate;
            border-spacing: 8px 0;
        }
        .stat-card {
            display: table-cell;
            width: 25%;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
            vertical-align: top;
        }
        .stat-card.alert {
            background: #fff1f2;
            border-color: #fda4af;
        }
        .stat-card .label {
            font-size: 8px;
            font-weight: bold;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 5px;
        }
        .stat-card .value {
            font-size: 26px;
            font-weight: bold;
            color: #0f766e;
            line-height: 1;
        }
        .stat-card.alert .value { color: #e11d48; }
        .stat-card .sub-value {
            font-size: 8.5px;
            color: #94a3b8;
            margin-top: 4px;
        }

        /* ── Seção ── */
        .section {
            margin-bottom: 16px;
        }
        .section-title {
            font-size: 11px;
            font-weight: bold;
            color: #134e4a;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 4px;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        .section-title .icon { font-size: 13px; }

        /* ── Alerta péssimo ── */
        .alert-box {
            background: #fff1f2;
            border: 1px solid #fda4af;
            border-radius: 8px;
            margin-bottom: 16px;
            overflow: hidden;
        }
        .alert-header {
            background: #e11d48;
            color: white;
            padding: 8px 14px;
            font-weight: bold;
            font-size: 10px;
        }
        .alert-row {
            padding: 9px 14px;
            border-bottom: 1px solid #ffe4e6;
        }
        .alert-row:last-child { border-bottom: none; }
        .alert-name {
            font-weight: bold;
            font-size: 10px;
            color: #1e293b;
        }
        .alert-dept {
            font-size: 9px;
            color: #64748b;
        }
        .alert-nota {
            font-style: italic;
            color: #9f1239;
            font-size: 9px;
            margin-top: 3px;
            padding-left: 8px;
            border-left: 2px solid #fda4af;
        }
        .alert-gestor {
            font-size: 9px;
            color: #475569;
            margin-top: 4px;
        }
        .alert-gestor strong { color: #0f766e; }

        /* ── Tabela ── */
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 9px;
        }
        table thead tr {
            background: #134e4a;
            color: white;
        }
        table thead th {
            padding: 7px 8px;
            text-align: left;
            font-weight: bold;
        }
        table thead th.center { text-align: center; }
        table tbody tr:nth-child(even) { background: #f0fdf4; }
        table tbody tr td {
            padding: 6px 8px;
            border-bottom: 1px solid #e2e8f0;
            color: #334155;
        }
        table tbody tr td.center { text-align: center; }
        .badge-score {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 20px;
            font-weight: bold;
            font-size: 9px;
        }
        .score-green { background: #d1fae5; color: #065f46; }
        .score-amber { background: #fef3c7; color: #92400e; }
        .score-red   { background: #ffe4e6; color: #9f1239; }

        .mood-otimo   { color: #059669; font-weight: bold; }
        .mood-bem     { color: #2563eb; font-weight: bold; }
        .mood-normal  { color: #d97706; font-weight: bold; }
        .mood-pessimo { color: #e11d48; font-weight: bold; }

        /* ── Footer ── */
        .footer {
            margin-top: 20px;
            padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px;
            color: #94a3b8;
            display: flex;
            justify-content: space-between;
        }

        /* ── Page break ── */
        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- ═══ CABEÇALHO ═══ --}}
    <div class="header">
        <div class="header-top">
            <div>
                <h1>🫀 Relatório de Humor das Equipes</h1>
                <div class="sub">Departamento Pessoal / Recursos Humanos</div>
            </div>
            <div class="badge">
                📅 {{ today()->translatedFormat('d \d\e F \d\e Y') }}<br>
                Gerado às {{ now()->format('H:i') }}
            </div>
        </div>
    </div>

    {{-- ═══ CARDS DE STATS ═══ --}}
    <div style="padding: 0 12px;">
        <table class="stats-grid" style="margin-bottom: 16px;">
            <tr>
                <td class="stat-card">
                    <div class="label">👥 Participação</div>
                    <div class="value" style="color: #2563eb;">{{ $participacao }}%</div>
                    <div class="sub-value">{{ $total }} de {{ $totalFuncionarios }} responderam</div>
                </td>
                <td class="stat-card">
                    <div class="label">😄 Positivos</div>
                    <div class="value" style="color: #059669;">{{ ($dist->get('otimo', 0) + $dist->get('bem', 0)) }}</div>
                    <div class="sub-value">Ótimo ({{ $dist->get('otimo', 0) }}) + Bem ({{ $dist->get('bem', 0) }})</div>
                </td>
                <td class="stat-card">
                    <div class="label">😐 Neutros</div>
                    <div class="value" style="color: #d97706;">{{ $dist->get('normal', 0) }}</div>
                    <div class="sub-value">Normal</div>
                </td>
                <td class="stat-card {{ $pessimos > 0 ? 'alert' : '' }}">
                    <div class="label">😔 Péssimos</div>
                    <div class="value">{{ $pessimos }}</div>
                    <div class="sub-value">{{ $pessimos > 0 ? 'Requer atenção imediata' : 'Nenhum hoje' }}</div>
                </td>
            </tr>
        </table>

        {{-- ═══ ALERTAS PÉSSIMOS ═══ --}}
        @if($pessimosDetalhados->isNotEmpty())
        <div class="alert-box">
            <div class="alert-header">
                ⚠️ {{ $pessimosDetalhados->count() }} colaborador(es) com humor péssimo hoje — Entre em contato com o gestor do setor
            </div>
            @foreach($pessimosDetalhados as $p)
            <div class="alert-row">
                <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                    <div>
                        <div class="alert-name">{{ $p['user_nome'] }}</div>
                        <div class="alert-dept">{{ $p['departamento'] }} · {{ $p['horario'] }}</div>
                        @if($p['nota'])
                        <div class="alert-nota">"{{ $p['nota'] }}"</div>
                        @endif
                    </div>
                    <div style="text-align: right; min-width: 180px;">
                        <div class="alert-gestor">
                            <strong>Gestor: </strong>{{ $p['gerente_nome'] }}
                        </div>
                        @if($p['gerente_email'])
                        <div class="alert-gestor">{{ $p['gerente_email'] }}</div>
                        @endif
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @else
        <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 14px; margin-bottom: 16px; text-align: center; color: #065f46; font-weight: bold;">
            🎉 Nenhum colaborador com humor péssimo hoje!
        </div>
        @endif

        {{-- ═══ TABELA POR DEPARTAMENTO ═══ --}}
        @if(!empty($porDept))
        <div class="section">
            <div class="section-title">
                <span class="icon">🏢</span> Resumo por Departamento
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Departamento</th>
                        <th class="center">Total</th>
                        <th class="center">😄 Ótimo</th>
                        <th class="center">🙂 Bem</th>
                        <th class="center">😐 Normal</th>
                        <th class="center">😔 Péssimo</th>
                        <th class="center">Score</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($porDept as $row)
                    <tr>
                        <td><strong>{{ $row['departamento'] }}</strong></td>
                        <td class="center">{{ $row['total'] }}</td>
                        <td class="center mood-otimo">{{ $row['otimo'] ?: '—' }}</td>
                        <td class="center mood-bem">{{ $row['bem'] ?: '—' }}</td>
                        <td class="center mood-normal">{{ $row['normal'] ?: '—' }}</td>
                        <td class="center mood-pessimo">{{ $row['pessimo'] ?: '—' }}</td>
                        <td class="center">
                            @if($row['score'] !== null)
                                @php
                                    $cls = $row['score'] >= 70 ? 'score-green' : ($row['score'] >= 40 ? 'score-amber' : 'score-red');
                                @endphp
                                <span class="badge-score {{ $cls }}">{{ $row['score'] }}</span>
                            @else
                                —
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- ═══ LISTA COMPLETA DE CHECK-INS ═══ --}}
        @if($checkins->isNotEmpty())
        <div class="section" style="margin-top: 10px;">
            <div class="section-title">
                <span class="icon">📋</span> Todos os Check-ins do Dia ({{ $checkins->count() }})
            </div>
            <table>
                <thead>
                    <tr>
                        <th>Colaborador</th>
                        <th>Departamento</th>
                        <th class="center">Humor</th>
                        <th>Observação</th>
                        <th class="center">Horário</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($checkins as $c)
                    @php
                        $moodLabels = ['otimo' => 'Ótimo', 'bem' => 'Bem', 'normal' => 'Normal', 'pessimo' => 'Péssimo'];
                    @endphp
                    <tr>
                        <td><strong>{{ $c->user->name }}</strong></td>
                        <td>{{ $c->user->department?->name ?? 'Sem depto.' }}</td>
                        <td class="center mood-{{ $c->mood }}">{{ $moodLabels[$c->mood] ?? $c->mood }}</td>
                        <td style="font-style: italic; color: #475569;">{{ $c->note ?? '—' }}</td>
                        <td class="center" style="color: #64748b;">{{ $c->created_at->format('H:i') }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif

        {{-- ═══ FOOTER ═══ --}}
        <div class="footer">
            <span>SistemDP · Relatório de Humor das Equipes</span>
            <span>Gerado em {{ now()->format('d/m/Y \à\s H:i') }} · Uso interno — DP/RH</span>
        </div>
    </div>

</body>
</html>
