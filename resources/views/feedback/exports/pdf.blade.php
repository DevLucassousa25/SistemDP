<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body {
    font-family: DejaVu Sans, Arial, sans-serif;
    font-size: 9px;
    color: #1e293b;
    line-height: 1.5;
    background: #ffffff;
}

/* ── Cabeçalho ───────────────────────────────────────────────── */
.page-header {
    background-color: #7c3aed;
    color: #ffffff;
    padding: 14px 20px;
    margin-bottom: 16px;
}
.page-header h1 { font-size: 15px; font-weight: bold; margin-bottom: 3px; }
.page-header p  { font-size: 9px; opacity: 0.85; }

/* ── Cards de resumo ─────────────────────────────────────────── */
.summary-grid {
    width: 100%;
    margin-bottom: 16px;
    border-collapse: separate;
    border-spacing: 5px;
}
.summary-grid td { width: 20%; vertical-align: top; padding: 0; }
.summary-card {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 6px;
    padding: 10px 12px;
    text-align: center;
}
.summary-card .val {
    font-size: 22px;
    font-weight: bold;
    line-height: 1.1;
    margin-bottom: 3px;
}
.summary-card .lbl { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; }
.color-purple { color: #7c3aed; }
.color-green  { color: #10b981; }
.color-blue   { color: #3b82f6; }
.color-red    { color: #ef4444; }
.color-orange { color: #f97316; }

/* ── Seção ───────────────────────────────────────────────────── */
.section-title {
    background: #334155;
    color: #fff;
    font-size: 9px;
    font-weight: bold;
    padding: 4px 10px;
    margin-bottom: 8px;
    border-radius: 3px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* ── Tabela de feedbacks ─────────────────────────────────────── */
.feedback-table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 20px;
    font-size: 8.5px;
}
.feedback-table th {
    background: #334155;
    color: #ffffff;
    font-weight: bold;
    padding: 5px 6px;
    text-align: left;
    border: 1px solid #475569;
}
.feedback-table td {
    padding: 5px 6px;
    border: 1px solid #e2e8f0;
    vertical-align: top;
}
.feedback-table tr:nth-child(even) td { background: #f8fafc; }

/* ── Badges de tipo ──────────────────────────────────────────── */
.badge {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 8px;
    font-weight: bold;
}
.badge-reconhecimento { background: #d1fae5; color: #065f46; }
.badge-sugestao       { background: #dbeafe; color: #1e40af; }
.badge-alerta         { background: #fee2e2; color: #991b1b; }

/* ── Status ──────────────────────────────────────────────────── */
.badge-status {
    display: inline-block;
    padding: 1px 6px;
    border-radius: 10px;
    font-size: 8px;
}
.badge-aberto             { background: #f1f5f9; color: #475569; }
.badge-em_analise         { background: #dbeafe; color: #1e40af; }
.badge-aguardando_plano   { background: #fef3c7; color: #92400e; }
.badge-plano_em_andamento { background: #ffedd5; color: #9a3412; }
.badge-resolvido          { background: #d1fae5; color: #065f46; }
.badge-arquivado          { background: #f1f5f9; color: #94a3b8; }

/* ── Gravidade ───────────────────────────────────────────────── */
.badge-sev-baixo  { background: #d1fae5; color: #065f46; font-size: 8px; padding: 1px 5px; border-radius: 8px; }
.badge-sev-medio  { background: #fef3c7; color: #92400e; font-size: 8px; padding: 1px 5px; border-radius: 8px; }
.badge-sev-alto   { background: #ffedd5; color: #9a3412; font-size: 8px; padding: 1px 5px; border-radius: 8px; }
.badge-sev-critico{ background: #fee2e2; color: #991b1b; font-size: 8px; padding: 1px 5px; border-radius: 8px; font-weight: bold; }

/* ── Rodapé ──────────────────────────────────────────────────── */
.page-footer {
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
    background: #f8fafc;
    border-top: 1px solid #e2e8f0;
    padding: 5px 20px;
    font-size: 8px;
    color: #94a3b8;
}
.page-footer table { width: 100%; }

/* ── Nota de mensagem ────────────────────────────────────────── */
.msg-cell { max-width: 160px; word-wrap: break-word; }
</style>
</head>
<body>

{{-- Rodapé fixo --}}
<div class="page-footer">
    <table>
        <tr>
            <td>SistemDP &mdash; Exportação de Feedbacks</td>
            <td style="text-align:right">Gerado em {{ now()->format('d/m/Y \à\s H:i') }} por {{ $user->name }}</td>
        </tr>
    </table>
</div>

{{-- Cabeçalho --}}
<div class="page-header">
    <h1>Feedbacks</h1>
    <p>Exportado em {{ now()->format('d/m/Y') }} &mdash; Total: {{ $feedbacks->count() }} registros</p>
</div>

{{-- Cards de resumo --}}
@php
    $total          = $feedbacks->count();
    $reconhecimento = $feedbacks->where('type', 'reconhecimento')->count();
    $sugestao       = $feedbacks->where('type', 'sugestao')->count();
    $alerta         = $feedbacks->where('type', 'alerta')->count();
    $pendentes      = $feedbacks->whereIn('status', ['aberto', 'em_analise', 'aguardando_plano', 'plano_em_andamento'])->count();
@endphp

<table class="summary-grid">
    <tr>
        <td>
            <div class="summary-card">
                <div class="val color-purple">{{ $total }}</div>
                <div class="lbl">Total</div>
            </div>
        </td>
        <td>
            <div class="summary-card">
                <div class="val color-green">{{ $reconhecimento }}</div>
                <div class="lbl">Reconhecimentos</div>
            </div>
        </td>
        <td>
            <div class="summary-card">
                <div class="val color-blue">{{ $sugestao }}</div>
                <div class="lbl">Sugestões</div>
            </div>
        </td>
        <td>
            <div class="summary-card">
                <div class="val color-red">{{ $alerta }}</div>
                <div class="lbl">Alertas</div>
            </div>
        </td>
        <td>
            <div class="summary-card">
                <div class="val color-orange">{{ $pendentes }}</div>
                <div class="lbl">Pendentes</div>
            </div>
        </td>
    </tr>
</table>

{{-- Tabela principal --}}
<div class="section-title">Lista de Feedbacks</div>

<table class="feedback-table">
    <thead>
        <tr>
            <th style="width:4%">#</th>
            <th style="width:14%">Colaborador</th>
            <th style="width:12%">Avaliador</th>
            <th style="width:10%">Tipo</th>
            <th style="width:12%">Categoria</th>
            <th style="width:8%">Gravidade</th>
            <th style="width:6%">Nota</th>
            <th style="width:8%">Ocorrido</th>
            <th style="width:10%">Status</th>
            <th style="width:16%">Mensagem</th>
        </tr>
    </thead>
    <tbody>
        @forelse($feedbacks as $fb)
        <tr>
            <td style="text-align:center; color:#94a3b8">{{ $fb->id }}</td>
            <td><strong>{{ $fb->employee?->name ?? '—' }}</strong></td>
            <td>{{ $fb->evaluator_name }}</td>
            <td>
                <span class="badge badge-{{ $fb->type }}">{{ $fb->type_label }}</span>
            </td>
            <td>{{ $fb->category_label }}</td>
            <td>
                @if($fb->severity)
                    <span class="badge-sev-{{ $fb->severity }}">{{ $fb->severity_label }}</span>
                @else
                    <span style="color:#94a3b8">—</span>
                @endif
            </td>
            <td style="text-align:center">
                @if($fb->rating)
                    {{ $fb->rating }}/5
                @else
                    <span style="color:#94a3b8">—</span>
                @endif
            </td>
            <td>{{ $fb->occurred_at?->format('d/m/Y') ?? '—' }}</td>
            <td>
                <span class="badge-status badge-{{ $fb->status }}">{{ $fb->status_label }}</span>
            </td>
            <td class="msg-cell">
                {{ \Illuminate\Support\Str::limit($fb->message, 80) }}
            </td>
        </tr>
        @empty
        <tr>
            <td colspan="10" style="text-align:center; padding:20px; color:#94a3b8; font-style:italic">
                Nenhum feedback encontrado.
            </td>
        </tr>
        @endforelse
    </tbody>
</table>

{{-- Resumo por categoria (alertas) --}}
@php
    $alertasPorCategoria = $feedbacks->where('type', 'alerta')
        ->groupBy('category')
        ->map(fn($g) => $g->count())
        ->sortDesc();
@endphp

@if($alertasPorCategoria->isNotEmpty())
<div class="section-title" style="margin-top: 4px;">Alertas por Categoria</div>
<table class="feedback-table">
    <thead>
        <tr>
            <th style="width:50%">Categoria</th>
            <th style="width:25%; text-align:center">Qtd. Alertas</th>
            <th style="width:25%; text-align:center">% do Total de Alertas</th>
        </tr>
    </thead>
    <tbody>
        @foreach($alertasPorCategoria as $cat => $cnt)
        @php
            $catLabel = match($cat) {
                'comportamento'      => 'Comportamento',
                'desempenho'         => 'Desempenho',
                'pontualidade'       => 'Pontualidade',
                'trabalho_em_equipe' => 'Trabalho em equipe',
                'comunicacao'        => 'Comunicação',
                'lideranca'          => 'Liderança',
                'outros'             => 'Outros',
                default              => ucfirst($cat),
            };
            $pct = $alerta > 0 ? round(($cnt / $alerta) * 100, 1) : 0;
        @endphp
        <tr>
            <td>{{ $catLabel }}</td>
            <td style="text-align:center; font-weight:bold; color:#ef4444">{{ $cnt }}</td>
            <td style="text-align:center; color:#64748b">{{ $pct }}%</td>
        </tr>
        @endforeach
    </tbody>
</table>
@endif

</body>
</html>
