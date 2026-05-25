<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Humor das Equipes — {{ $labelMes }}</title>
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
        }
        .header h1 { font-size: 18px; font-weight: bold; letter-spacing: -0.5px; }
        .header .sub { font-size: 10px; opacity: 0.8; margin-top: 3px; }
        .header .badge {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            border-radius: 6px;
            padding: 4px 12px;
            font-size: 10px;
            font-weight: bold;
            text-align: right;
        }

        /* ── Cards de stat ── */
        .stat-card {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 12px 14px;
        }
        .stat-card.alert { background: #fff1f2; border-color: #fda4af; }
        .stat-card .label {
            font-size: 8px; font-weight: bold; color: #64748b;
            text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 5px;
        }
        .stat-card .value { font-size: 28px; font-weight: bold; line-height: 1; }
        .stat-card .sub-value { font-size: 8.5px; color: #94a3b8; margin-top: 4px; }

        /* ── Seção ── */
        .section { margin-bottom: 18px; }
        .section-title {
            font-size: 11px; font-weight: bold; color: #134e4a;
            border-bottom: 2px solid #0f766e;
            padding-bottom: 4px; margin-bottom: 10px;
        }

        /* ── Tabela ── */
        table { width: 100%; border-collapse: collapse; font-size: 9px; }
        table thead tr { background: #134e4a; color: white; }
        table thead th { padding: 7px 8px; text-align: left; font-weight: bold; }
        table thead th.center { text-align: center; }
        table tbody tr:nth-child(even) { background: #f0fdf4; }
        table tbody tr td { padding: 6px 8px; border-bottom: 1px solid #e2e8f0; color: #334155; }
        table tbody tr td.center { text-align: center; }

        .badge-score {
            display: inline-block; padding: 2px 8px; border-radius: 20px;
            font-weight: bold; font-size: 9px;
        }
        .score-green { background: #d1fae5; color: #065f46; }
        .score-amber { background: #fef3c7; color: #92400e; }
        .score-red   { background: #ffe4e6; color: #9f1239; }

        .mood-otimo   { color: #059669; font-weight: bold; }
        .mood-bem     { color: #2563eb; font-weight: bold; }
        .mood-normal  { color: #d97706; font-weight: bold; }
        .mood-pessimo { color: #e11d48; font-weight: bold; }

        /* ── Progress bar ── */
        .bar-wrap { background: #e2e8f0; border-radius: 4px; height: 6px; margin-top: 2px; }
        .bar-fill  { height: 6px; border-radius: 4px; }
        .bar-green { background: #10b981; }
        .bar-amber { background: #f59e0b; }
        .bar-red   { background: #f43f5e; }

        /* ── Distribuição ── */
        .dist-row {
            display: flex; align-items: center; gap: 8px;
            margin-bottom: 6px;
        }
        .dist-label { width: 55px; font-size: 9px; color: #475569; }
        .dist-bar-wrap { flex: 1; background: #e2e8f0; border-radius: 4px; height: 10px; }
        .dist-bar { height: 10px; border-radius: 4px; }
        .dist-count { width: 30px; text-align: right; font-weight: bold; font-size: 9px; }

        /* ── Alerta ── */
        .alert-box { background: #fff1f2; border: 1px solid #fda4af; border-radius: 8px; overflow: hidden; margin-bottom: 14px; }
        .alert-header { background: #e11d48; color: white; padding: 7px 14px; font-weight: bold; font-size: 10px; }
        .alert-row { padding: 8px 14px; border-bottom: 1px solid #ffe4e6; }
        .alert-row:last-child { border-bottom: none; }
        .alert-name { font-weight: bold; font-size: 10px; }
        .alert-nota { font-style: italic; color: #9f1239; font-size: 9px; margin-top: 2px; padding-left: 6px; border-left: 2px solid #fda4af; }

        /* ── Tendência ── */
        .trend-table { font-size: 8.5px; }
        .trend-table th, .trend-table td { padding: 5px 6px; }

        /* ── Footer ── */
        .footer {
            margin-top: 20px; padding-top: 10px;
            border-top: 1px solid #e2e8f0;
            font-size: 8px; color: #94a3b8;
            display: flex; justify-content: space-between;
        }

        .page-break { page-break-before: always; }
    </style>
</head>
<body>

    {{-- ═══ CABEÇALHO ═══ --}}
    <div class="header">
        <div class="header-top">
            <div>
                <h1>🫀 Relatório Mensal de Humor das Equipes</h1>
                <div class="sub">Departamento Pessoal / Recursos Humanos</div>
            </div>
            <div class="badge">
                📊 {{ ucfirst($labelMes) }}<br>
                Gerado em {{ now()->format('d/m/Y \à\s H:i') }}
            </div>
        </div>
    </div>

    <div style="padding: 0 12px;">

        {{-- ═══ CARDS DE STATS ═══ --}}
        <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 16px;">
            <tr>
                <td style="width: 25%; vertical-align: top;">
                    <div class="stat-card">
                        <div class="label">📋 Total Check-ins</div>
                        <div class="value" style="color: #2563eb;">{{ $total }}</div>
                        <div class="sub-value">registros no mês</div>
                    </div>
                </td>
                <td style="width: 25%; vertical-align: top;">
                    <div class="stat-card">
                        <div class="label">💚 Score Médio</div>
                        @php
                            $scoreCor = $score !== null
                                ? ($score >= 70 ? '#059669' : ($score >= 40 ? '#d97706' : '#e11d48'))
                                : '#94a3b8';
                        @endphp
                        <div class="value" style="color: {{ $scoreCor }};">{{ $score ?? '—' }}</div>
                        <div class="sub-value">bem-estar 0–100</div>
                    </div>
                </td>
                <td style="width: 25%; vertical-align: top;">
                    <div class="stat-card">
                        <div class="label">😄 Positivos</div>
                        <div class="value" style="color: #059669;">{{ $dist->get('otimo', 0) + $dist->get('bem', 0) }}</div>
                        <div class="sub-value">Ótimo + Bem</div>
                    </div>
                </td>
                <td style="width: 25%; vertical-align: top;">
                    <div class="stat-card {{ $pessimos > 0 ? 'alert' : '' }}">
                        <div class="label">😔 Péssimos</div>
                        <div class="value" style="color: {{ $pessimos > 0 ? '#e11d48' : '#059669' }};">{{ $pessimos }}</div>
                        <div class="sub-value">{{ $pessimos > 0 ? 'requer atenção' : 'ótimo!' }}</div>
                    </div>
                </td>
            </tr>
        </table>

        {{-- ═══ DISTRIBUIÇÃO + RANKING LADO A LADO ═══ --}}
        <table style="width: 100%; border-collapse: separate; border-spacing: 8px 0; margin-bottom: 16px;">
            <tr>
                {{-- Distribuição geral --}}
                <td style="width: 40%; vertical-align: top;">
                    <div class="section-title">📊 Distribuição Geral</div>
                    @php
                        $moods = [
                            ['key' => 'otimo',   'label' => '😄 Ótimo',   'color' => '#059669'],
                            ['key' => 'bem',     'label' => '🙂 Bem',     'color' => '#2563eb'],
                            ['key' => 'normal',  'label' => '😐 Normal',  'color' => '#d97706'],
                            ['key' => 'pessimo', 'label' => '😔 Péssimo', 'color' => '#e11d48'],
                        ];
                    @endphp
                    @foreach($moods as $m)
                    @php
                        $cnt     = $dist->get($m['key'], 0);
                        $pct     = $total > 0 ? round($cnt / $total * 100) : 0;
                    @endphp
                    <div style="margin-bottom: 8px;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2px;">
                            <span style="font-size: 9px; color: #475569;">{{ $m['label'] }}</span>
                            <span style="font-size: 9px; font-weight: bold; color: {{ $m['color'] }};">{{ $cnt }} ({{ $pct }}%)</span>
                        </div>
                        <div class="bar-wrap">
                            <div class="bar-fill" style="width: {{ $pct }}%; background: {{ $m['color'] }};"></div>
                        </div>
                    </div>
                    @endforeach

                    @if($score !== null)
                    <div style="margin-top: 10px; padding: 8px 10px; background: #f0fdf4; border: 1px solid #86efac; border-radius: 6px;">
                        <div style="font-size: 8px; font-weight: bold; color: #065f46; text-transform: uppercase;">Score de bem-estar geral</div>
                        <div style="font-size: 20px; font-weight: bold; color: {{ $scoreCor }}; margin-top: 2px;">{{ $score }}/100</div>
                        <div style="font-size: 8px; color: #64748b; margin-top: 1px;">
                            @if($score >= 70) Equipe em bom estado geral
                            @elseif($score >= 40) Atenção: alguns colaboradores precisam de apoio
                            @else Situação crítica — ação imediata necessária
                            @endif
                        </div>
                    </div>
                    @endif
                </td>

                {{-- Ranking departamentos --}}
                <td style="width: 60%; vertical-align: top;">
                    <div class="section-title">🏆 Ranking de Departamentos</div>
                    @if(empty($ranking))
                        <p style="color: #94a3b8; font-style: italic;">Sem dados disponíveis.</p>
                    @else
                    <table>
                        <thead>
                            <tr>
                                <th style="width: 24px;">#</th>
                                <th>Departamento</th>
                                <th class="center">Total</th>
                                <th class="center">😔</th>
                                <th class="center">Score</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($ranking as $i => $r)
                            <tr>
                                <td class="center" style="color: {{ $i === 0 ? '#d97706' : '#94a3b8' }}; font-weight: bold;">{{ $i + 1 }}</td>
                                <td><strong>{{ $r['departamento'] }}</strong></td>
                                <td class="center">{{ $r['total'] }}</td>
                                <td class="center mood-pessimo">{{ $r['pessimo'] ?: '—' }}</td>
                                <td class="center">
                                    @php $cls = $r['score'] >= 70 ? 'score-green' : ($r['score'] >= 40 ? 'score-amber' : 'score-red'); @endphp
                                    <span class="badge-score {{ $cls }}">{{ $r['score'] }}</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @endif
                </td>
            </tr>
        </table>

        {{-- ═══ TENDÊNCIA DIÁRIA ═══ --}}
        @if(!empty($tendencia))
        <div class="section">
            <div class="section-title">📈 Tendência Diária</div>
            <table class="trend-table">
                <thead>
                    <tr>
                        <th>Dia</th>
                        <th class="center">Total</th>
                        <th class="center mood-otimo">Ótimo</th>
                        <th class="center mood-bem">Bem</th>
                        <th class="center mood-normal">Normal</th>
                        <th class="center mood-pessimo">Péssimo</th>
                        <th class="center">Score dia</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($tendencia as $dia)
                    @php
                        $dScore = $dia['total'] > 0
                            ? round((($dia['otimo'] * 100) + ($dia['bem'] * 66) + ($dia['normal'] * 33)) / $dia['total'])
                            : null;
                        $dCls   = $dScore !== null ? ($dScore >= 70 ? 'score-green' : ($dScore >= 40 ? 'score-amber' : 'score-red')) : '';
                    @endphp
                    <tr>
                        <td><strong>{{ $dia['dia'] }}</strong></td>
                        <td class="center">{{ $dia['total'] }}</td>
                        <td class="center mood-otimo">{{ $dia['otimo'] ?: '—' }}</td>
                        <td class="center mood-bem">{{ $dia['bem'] ?: '—' }}</td>
                        <td class="center mood-normal">{{ $dia['normal'] ?: '—' }}</td>
                        <td class="center mood-pessimo">{{ $dia['pessimo'] ?: '—' }}</td>
                        <td class="center">
                            @if($dScore !== null)
                                <span class="badge-score {{ $dCls }}">{{ $dScore }}</span>
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

        {{-- ═══ REGISTROS PÉSSIMOS NO MÊS ═══ --}}
        @if($pessimosDetalhados->isNotEmpty())
        <div class="page-break"></div>
        <div style="padding: 0;">
            <div style="background: #be123c; color: white; padding: 14px 24px 10px; margin-bottom: 14px;">
                <h2 style="font-size: 14px; font-weight: bold;">⚠️ Registros Péssimo — {{ ucfirst($labelMes) }}</h2>
                <p style="font-size: 9px; opacity: 0.85; margin-top: 2px;">Entre em contato com o gestor de cada setor para acompanhamento</p>
            </div>
            <div style="padding: 0 12px;">
                <div class="alert-box">
                    <div class="alert-header">
                        {{ $pessimosDetalhados->count() }} registro(s) — Ação recomendada: contato com gestor
                    </div>
                    @foreach($pessimosDetalhados as $p)
                    <div class="alert-row">
                        <table style="width: 100%; border: none;">
                            <tr>
                                <td style="width: 40%; vertical-align: top;">
                                    <div class="alert-name">{{ $p['user_nome'] }}</div>
                                    <div style="font-size: 9px; color: #64748b;">{{ $p['departamento'] }} · {{ $p['data'] }}</div>
                                    @if($p['nota'])
                                    <div class="alert-nota">"{{ $p['nota'] }}"</div>
                                    @endif
                                </td>
                                <td style="width: 60%; vertical-align: top; text-align: right; padding-left: 10px;">
                                    <div style="font-size: 9px; color: #475569;">
                                        <strong style="color: #0f766e;">Gestor:</strong> {{ $p['gerente_nome'] }}
                                    </div>
                                    @if($p['gerente_email'])
                                    <div style="font-size: 9px; color: #64748b;">{{ $p['gerente_email'] }}</div>
                                    @endif
                                </td>
                            </tr>
                        </table>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @else
        <div style="background: #f0fdf4; border: 1px solid #86efac; border-radius: 8px; padding: 14px; text-align: center; color: #065f46; font-weight: bold;">
            🎉 Nenhum registro péssimo no mês de {{ $labelMes }}!
        </div>
        @endif

        {{-- ═══ FOOTER ═══ --}}
        <div class="footer">
            <span>SistemDP · Relatório Mensal de Humor das Equipes — {{ ucfirst($labelMes) }}</span>
            <span>Gerado em {{ now()->format('d/m/Y \à\s H:i') }} · Uso interno — DP/RH</span>
        </div>

    </div>
</body>
</html>
