<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Termo de Responsabilidade — {{ $atribuicao->equipamento->nome }}</title>
    <style>
        /* ── Reset & Base ──────────────────────────────────────────── */
        *, *::before, *::after { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            font-size: 13px;
            color: #1a202c;
            background: #edf2f7;
            padding: 32px 16px;
            line-height: 1.55;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }

        /* ── Folha A4 ──────────────────────────────────────────────── */
        .sheet {
            background: #ffffff;
            max-width: 820px;
            margin: 0 auto;
            border-radius: 4px;
            box-shadow: 0 2px 32px rgba(0,0,0,.12);
            overflow: hidden;
        }

        /* ── Faixa de topo (accent) ────────────────────────────────── */
        .accent-bar {
            height: 6px;
            background: linear-gradient(90deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
        }

        /* ── Corpo com padding ─────────────────────────────────────── */
        .body-wrap { padding: 40px 48px 44px; }

        /* ── Cabeçalho ─────────────────────────────────────────────── */
        .doc-header {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 20px;
            padding-bottom: 24px;
            margin-bottom: 24px;
            border-bottom: 2px solid #e2e8f0;
        }
        .logo-block { flex-shrink: 0; }
        .logo-block img { max-height: 64px; max-width: 180px; object-fit: contain; display: block; }
        .logo-placeholder {
            width: 64px; height: 64px; border-radius: 12px;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            display: flex; align-items: center; justify-content: center;
        }
        .logo-placeholder span { color: #fff; font-size: 22px; font-weight: 900; letter-spacing: -1px; }

        /* cabeçalho centro */
        .header-center { flex: 1; text-align: center; }
        .header-center .company-name {
            font-size: 16px; font-weight: 800; color: #1e3a8a; letter-spacing: -.3px;
        }
        .header-center .company-sub { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        /* cabeçalho esquerda */
        .header-left .company-name { font-size: 16px; font-weight: 800; color: #1e3a8a; }
        .header-left .company-sub  { font-size: 11px; color: #94a3b8; margin-top: 2px; }

        /* meta-badge (Nº e data) */
        .doc-meta {
            flex-shrink: 0;
            text-align: right;
            min-width: 130px;
        }
        .doc-meta .meta-label { font-size: 9px; color: #94a3b8; text-transform: uppercase; letter-spacing: 1px; }
        .doc-meta .meta-value { font-size: 13px; font-weight: 700; color: #1e293b; margin-top: 1px; }
        .doc-meta .meta-date  { font-size: 10px; color: #64748b; margin-top: 4px; }

        /* ── Título do documento ───────────────────────────────────── */
        .doc-title {
            text-align: center;
            margin-bottom: 24px;
            padding: 14px 20px;
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
        }
        .doc-title h1 {
            font-size: 15px;
            font-weight: 800;
            color: #1e293b;
            text-transform: uppercase;
            letter-spacing: 1.2px;
        }
        .doc-title p { font-size: 11px; color: #94a3b8; margin-top: 4px; }

        /* ── Intro ─────────────────────────────────────────────────── */
        .intro-box {
            background: #eff6ff;
            border-left: 4px solid #3b82f6;
            border-radius: 0 6px 6px 0;
            padding: 12px 16px;
            margin-bottom: 22px;
            font-size: 12px;
            color: #1e40af;
            line-height: 1.6;
        }

        /* ── Seções de dados ───────────────────────────────────────── */
        .data-section { margin-bottom: 22px; }
        .section-header {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
            padding-bottom: 6px;
            border-bottom: 1.5px solid #e2e8f0;
        }
        .section-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #3b82f6; flex-shrink: 0;
        }
        .section-dot.green  { background: #10b981; }
        .section-dot.purple { background: #8b5cf6; }
        .section-title {
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: #475569;
        }

        /* Card de equipamento em destaque */
        .eq-banner {
            display: flex;
            align-items: center;
            gap: 16px;
            padding: 16px 20px;
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 1px solid #bfdbfe;
            border-radius: 8px;
            margin-bottom: 22px;
        }
        .eq-icon-wrap {
            width: 52px; height: 52px;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            border-radius: 10px;
            display: flex; align-items: center; justify-content: center;
            flex-shrink: 0;
            font-size: 24px;
        }
        .eq-banner h2 { font-size: 17px; font-weight: 800; color: #1e3a8a; line-height: 1.2; }
        .eq-banner p  { font-size: 11px; color: #3b82f6; margin-top: 3px; }

        /* Grade de campos */
        .field-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px 20px;
        }
        .field-grid.cols-2 { grid-template-columns: repeat(2, 1fr); }
        .field-item {}
        .field-label {
            font-size: 9px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: .8px;
            color: #94a3b8;
            margin-bottom: 2px;
        }
        .field-value {
            font-size: 12.5px;
            font-weight: 600;
            color: #1e293b;
        }
        .field-value.mono { font-family: 'Courier New', monospace; font-size: 11.5px; }
        .field-value.badge-new    { display:inline-block; background:#d1fae5; color:#065f46; padding:1px 8px; border-radius:4px; font-size:11px; }
        .field-value.badge-good   { display:inline-block; background:#dbeafe; color:#1e40af; padding:1px 8px; border-radius:4px; font-size:11px; }
        .field-value.badge-reg    { display:inline-block; background:#fef3c7; color:#92400e; padding:1px 8px; border-radius:4px; font-size:11px; }
        .field-value.badge-dmg    { display:inline-block; background:#fee2e2; color:#991b1b; padding:1px 8px; border-radius:4px; font-size:11px; }

        /* Observações */
        .obs-box {
            background: #fffbeb;
            border: 1px solid #fde68a;
            border-radius: 6px;
            padding: 10px 14px;
            font-size: 12px;
            color: #78350f;
            line-height: 1.55;
        }

        /* ── Cláusulas ─────────────────────────────────────────────── */
        .clauses-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 18px 20px;
            margin-bottom: 28px;
        }
        .clauses-box .clauses-title {
            font-size: 9px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: 1.4px;
            color: #64748b;
            margin-bottom: 12px;
        }
        .clauses-list { list-style: none; counter-reset: clause; }
        .clauses-list li {
            counter-increment: clause;
            display: flex;
            gap: 10px;
            margin-bottom: 8px;
            font-size: 11.5px;
            color: #374151;
            line-height: 1.55;
        }
        .clauses-list li::before {
            content: counter(clause) ".";
            font-weight: 700;
            color: #3b82f6;
            min-width: 18px;
            flex-shrink: 0;
            margin-top: 0;
        }

        /* ── Assinaturas ───────────────────────────────────────────── */
        .sig-section { margin-top: 32px; }
        .sig-grid {
            display: grid;
            gap: 32px;
        }
        .sig-grid.cols-1 { grid-template-columns: 1fr; }
        .sig-grid.cols-2 { grid-template-columns: 1fr 1fr; }
        .sig-grid.cols-3 { grid-template-columns: 1fr 1fr 1fr; }
        .sig-grid.cols-4 { grid-template-columns: 1fr 1fr 1fr 1fr; }

        .sig-card { text-align: center; }
        .sig-space { height: 52px; }
        .sig-line  { border-top: 1.5px solid #334155; padding-top: 8px; }
        .sig-name  { font-size: 12px; font-weight: 700; color: #1e293b; }
        .sig-role  { font-size: 10.5px; color: #94a3b8; margin-top: 2px; }
        .sig-cpf   { font-size: 9.5px; color: #cbd5e1; margin-top: 3px; font-family: 'Courier New', monospace; }

        /* ── Rodapé ────────────────────────────────────────────────── */
        .doc-footer {
            margin-top: 28px;
            padding-top: 14px;
            border-top: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 12px;
        }
        .doc-footer .footer-left  { font-size: 10px; color: #94a3b8; }
        .doc-footer .footer-right { font-size: 10px; color: #94a3b8; text-align: right; }
        .doc-footer .footer-id    { font-size: 11px; font-weight: 700; color: #64748b; font-family: 'Courier New', monospace; }

        /* Faixa de fundo do rodapé */
        .sheet-footer-bar {
            height: 4px;
            background: linear-gradient(90deg, #1e40af 0%, #3b82f6 50%, #60a5fa 100%);
        }

        /* ── Botões de ação (não imprimem) ─────────────────────────── */
        .no-print {
            max-width: 820px;
            margin: 0 auto 20px;
            display: flex;
            gap: 8px;
            justify-content: flex-end;
            flex-wrap: wrap;
        }
        .btn-back {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 16px;
            background: #fff; border: 1px solid #e2e8f0;
            border-radius: 8px; font-size: 13px; color: #64748b;
            text-decoration: none; cursor: pointer;
            font-family: inherit;
            transition: background .15s;
        }
        .btn-back:hover { background: #f8fafc; }
        .btn-print {
            display: inline-flex; align-items: center; gap: 6px;
            padding: 9px 20px;
            background: linear-gradient(135deg, #1e40af, #3b82f6);
            color: #fff; border: none; border-radius: 8px;
            font-size: 13px; font-weight: 700; cursor: pointer;
            font-family: inherit;
            box-shadow: 0 2px 8px rgba(59,130,246,.35);
            transition: opacity .15s;
        }
        .btn-print:hover { opacity: .9; }

        /* ── Print ─────────────────────────────────────────────────── */
        @media print {
            body { background: white; padding: 0; }
            .sheet { box-shadow: none; border-radius: 0; max-width: 100%; }
            .body-wrap { padding: 28px 36px 32px; }
            .no-print { display: none !important; }
            @page { margin: 10mm 12mm; }
        }
    </style>
</head>
<body>

{{-- Barra de ações (oculta na impressão) --}}
<div class="no-print">
    <a href="{{ route('equipamentos') }}" class="btn-back">← Voltar</a>
    <button onclick="window.print()" class="btn-print">🖨️ Imprimir / Salvar PDF</button>
</div>

@php
    // $cfg é passado pelo controller (template selecionado ou padrão)
    $template = $cfg ?? \App\Models\ConfiguracaoTermo::instancia();

    // Ícones e labels
    $catIcons = [
        'notebook'=>'💻','desktop'=>'🖥️','monitor'=>'🖥️','teclado'=>'⌨️',
        'mouse'=>'🖱️','headset'=>'🎧','cracha'=>'🪪','epi'=>'⛑️',
        'celular'=>'📱','cadeira'=>'🪑','outros'=>'📦',
    ];
    $categoriaLabels = [
        'notebook'=>'Notebook','desktop'=>'Desktop','monitor'=>'Monitor',
        'teclado'=>'Teclado','mouse'=>'Mouse','headset'=>'Headset',
        'cracha'=>'Crachá','epi'=>'EPI','celular'=>'Celular',
        'cadeira'=>'Cadeira','outros'=>'Outros',
    ];
    $condicaoLabel = ['novo'=>'Novo','bom'=>'Bom','regular'=>'Regular','danificado'=>'Danificado'];
    $condicaoBadge = ['novo'=>'badge-new','bom'=>'badge-good','regular'=>'badge-reg','danificado'=>'badge-dmg'];
    $eq = $atribuicao->equipamento;

    // Campos visíveis
    $camposVisiveis = $template->campos_visiveis ?? \App\Models\ConfiguracaoTermo::camposPadraoVisiveis();
    $mostra = fn(string $c) => in_array($c, $camposVisiveis);

    // Substituição de variáveis
    $vars = [
        '{empresa.nome}'           => $empresa?->nome_empresa ?? '',
        '{empresa.cnpj}'           => $empresa?->cnpj ?? '',
        '{funcionario.nome}'       => $atribuicao->funcionario->name,
        '{funcionario.cargo}'      => $atribuicao->funcionario->position ?? '',
        '{funcionario.depto}'      => $atribuicao->funcionario->department?->name ?? '',
        '{funcionario.email}'      => $atribuicao->funcionario->email ?? '',
        '{equipamento.nome}'       => $eq->nome,
        '{equipamento.categoria}'  => $categoriaLabels[$eq->categoria] ?? $eq->categoria,
        '{equipamento.marca}'      => $eq->marca ?? '',
        '{equipamento.modelo}'     => $eq->modelo ?? '',
        '{equipamento.serie}'      => $eq->numero_serie ?? '',
        '{equipamento.patrimonio}' => $eq->codigo_patrimonio ?? '',
        '{equipamento.condicao}'   => $condicaoLabel[$atribuicao->condicao_entrega] ?? $atribuicao->condicao_entrega,
        '{data.entrega}'           => $atribuicao->data_entrega?->format('d/m/Y') ?? '',
        '{data.geracao}'           => now()->format('d/m/Y'),
        '{termo.numero}'           => str_pad($atribuicao->id, 6, '0', STR_PAD_LEFT),
        '{responsavel.nome}'       => $atribuicao->responsavel?->name ?? '',
    ];

    $renderText = fn(string $t) => htmlspecialchars(str_replace(array_keys($vars), array_values($vars), $t));
    $renderBold = fn(string $t) => preg_replace(
        '/\*\*(.+?)\*\*/',
        '<strong>$1</strong>',
        str_replace(array_keys($vars), array_values($vars), htmlspecialchars($t))
    );

    // Cláusulas, assinaturas, textos
    $clausulas  = array_filter(
        $template->clausulas ?? \App\Models\ConfiguracaoTermo::clausulasPadrao(),
        fn($c) => !empty(trim($c['texto'] ?? ''))
    );
    $assinaturas   = $template->assinaturas ?? \App\Models\ConfiguracaoTermo::assinaturasPadrao();
    $numAss        = count($assinaturas);
    $sigClass      = match(true) { $numAss >= 4 => 'cols-4', $numAss === 3 => 'cols-3', $numAss === 1 => 'cols-1', default => 'cols-2' };
    $titulo        = $template->titulo       ?: 'Termo de Entrega e Responsabilidade de Equipamento';
    $subtitulo     = $template->subtitulo    ?: '';
    $intro         = $template->intro_texto  ?: '';
    $rodape        = $template->rodape_texto ?: 'Documento de controle interno';
    $termoNum      = str_pad($atribuicao->id, 6, '0', STR_PAD_LEFT);

    // Logo
    $logoPath    = $template->logo ?? null;
    $logoPosicao = $template->logo_posicao ?? 'esquerda';
    $logoUrl     = $logoPath ? asset('storage/' . $logoPath) : null;
    $nomeEmpresa = $empresa?->nome_empresa ?? 'Empresa';
    $iniciais    = mb_strtoupper(mb_substr(preg_replace('/[^A-Za-z\x{00C0}-\x{00FF}]/u', '', $nomeEmpresa), 0, 2));
@endphp

<div class="sheet">
    <div class="accent-bar"></div>

    <div class="body-wrap">

        {{-- ── Cabeçalho ─────────────────────────────────────── --}}
        <div class="doc-header">

            @if($logoPosicao === 'esquerda')
                {{-- Logo à esquerda | Nome ao centro | Meta à direita --}}
                <div class="logo-block">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo {{ $nomeEmpresa }}">
                    @else
                        <div class="logo-placeholder"><span>{{ $iniciais }}</span></div>
                    @endif
                </div>
                <div class="header-center" style="flex:1;text-align:center;">
                    <div class="company-name">{{ $nomeEmpresa }}</div>
                    <div class="company-sub">Departamento de TI / Recursos Humanos</div>
                </div>
                <div class="doc-meta">
                    <div class="meta-label">Nº do Documento</div>
                    <div class="meta-value">#{{ $termoNum }}</div>
                    <div class="meta-date">{{ now()->format('d/m/Y') }}</div>
                </div>

            @elseif($logoPosicao === 'centro')
                {{-- Nome à esquerda | Logo ao centro | Meta à direita --}}
                <div class="header-left" style="flex:1;">
                    <div class="company-name">{{ $nomeEmpresa }}</div>
                    <div class="company-sub">Departamento de TI / Recursos Humanos</div>
                </div>
                <div class="logo-block" style="text-align:center;">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo {{ $nomeEmpresa }}" style="margin:0 auto;">
                    @else
                        <div class="logo-placeholder"><span>{{ $iniciais }}</span></div>
                    @endif
                </div>
                <div class="doc-meta" style="flex:1;text-align:right;">
                    <div class="meta-label">Nº do Documento</div>
                    <div class="meta-value">#{{ $termoNum }}</div>
                    <div class="meta-date">{{ now()->format('d/m/Y') }}</div>
                </div>

            @elseif($logoPosicao === 'direita')
                {{-- Nome à esquerda | Meta ao centro | Logo à direita --}}
                <div class="header-left" style="flex:1;">
                    <div class="company-name">{{ $nomeEmpresa }}</div>
                    <div class="company-sub">Departamento de TI / Recursos Humanos</div>
                </div>
                <div class="doc-meta" style="text-align:center;">
                    <div class="meta-label">Nº do Documento</div>
                    <div class="meta-value">#{{ $termoNum }}</div>
                    <div class="meta-date">{{ now()->format('d/m/Y') }}</div>
                </div>
                <div class="logo-block" style="text-align:right;">
                    @if($logoUrl)
                        <img src="{{ $logoUrl }}" alt="Logo {{ $nomeEmpresa }}" style="margin-left:auto;">
                    @else
                        <div class="logo-placeholder" style="margin-left:auto;"><span>{{ $iniciais }}</span></div>
                    @endif
                </div>

            @else
                {{-- Sem logo — nome à esquerda, meta à direita --}}
                <div class="header-left">
                    <div class="company-name">{{ $nomeEmpresa }}</div>
                    <div class="company-sub">Departamento de TI / Recursos Humanos</div>
                </div>
                <div class="doc-meta">
                    <div class="meta-label">Nº do Documento</div>
                    <div class="meta-value">#{{ $termoNum }}</div>
                    <div class="meta-date">{{ now()->format('d/m/Y') }}</div>
                </div>
            @endif

        </div>

        {{-- ── Título ─────────────────────────────────────────── --}}
        <div class="doc-title">
            <h1>{{ $titulo }}</h1>
            @if($subtitulo)
                <p>{!! $renderText($subtitulo) !!}</p>
            @else
                <p>Gerado em {{ now()->isoFormat('D \d\e MMMM \d\e YYYY') }} &nbsp;·&nbsp; Documento Nº {{ $termoNum }}</p>
            @endif
        </div>

        {{-- ── Intro ──────────────────────────────────────────── --}}
        @if($intro)
            <div class="intro-box">{!! $renderBold($intro) !!}</div>
        @endif

        {{-- ── Dados do Equipamento ───────────────────────────── --}}
        @php
            $camposEq = [];
            if($mostra('numero_serie') && $eq->numero_serie)      $camposEq[] = ['label'=>'Nº de Série',         'value'=>$eq->numero_serie, 'mono'=>true];
            if($mostra('codigo_patrimonio') && $eq->codigo_patrimonio) $camposEq[] = ['label'=>'Cód. Patrimônio','value'=>$eq->codigo_patrimonio,'mono'=>true];
            if($mostra('marca') && $eq->marca)                    $camposEq[] = ['label'=>'Marca',               'value'=>$eq->marca];
            if($mostra('modelo') && $eq->modelo)                  $camposEq[] = ['label'=>'Modelo',              'value'=>$eq->modelo];
            if($mostra('condicao'))                               $camposEq[] = ['label'=>'Condição na Entrega',  'value'=>$condicaoLabel[$atribuicao->condicao_entrega] ?? $atribuicao->condicao_entrega, 'badge'=>$condicaoBadge[$atribuicao->condicao_entrega] ?? ''];
            if($mostra('data_entrega'))                           $camposEq[] = ['label'=>'Data de Entrega',     'value'=>$atribuicao->data_entrega?->format('d/m/Y')];
            if($mostra('garantia') && $eq->data_garantia)         $camposEq[] = ['label'=>'Garantia até',        'value'=>$eq->data_garantia->format('d/m/Y')];
            if($mostra('local') && $eq->local)                    $camposEq[] = ['label'=>'Local / Setor',       'value'=>$eq->local];
            if($mostra('valor') && $eq->valor)                    $camposEq[] = ['label'=>'Valor do Bem',        'value'=>'R$ ' . number_format((float)$eq->valor, 2, ',', '.')];
        @endphp
        @if(count($camposEq))
        <div class="data-section">
            <div class="section-header">
                <div class="section-dot"></div>
                <div class="section-title">Dados do Equipamento</div>
            </div>
            <div class="field-grid">
                @foreach($camposEq as $cf)
                    <div class="field-item">
                        <div class="field-label">{{ $cf['label'] }}</div>
                        <div class="field-value {{ ($cf['mono'] ?? false) ? 'mono' : '' }} {{ $cf['badge'] ?? '' }}">{{ $cf['value'] }}</div>
                    </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- ── Dados do Funcionário ───────────────────────────── --}}
        <div class="data-section">
            <div class="section-header">
                <div class="section-dot green"></div>
                <div class="section-title">Dados do Funcionário / Responsável</div>
            </div>
            <div class="field-grid cols-2">
                <div class="field-item">
                    <div class="field-label">Nome Completo</div>
                    <div class="field-value">{{ $atribuicao->funcionario->name }}</div>
                </div>
                @if($mostra('departamento'))
                <div class="field-item">
                    <div class="field-label">Departamento</div>
                    <div class="field-value">{{ $atribuicao->funcionario->department?->name ?? '—' }}</div>
                </div>
                @endif
                @if($mostra('cargo') && $atribuicao->funcionario->position)
                <div class="field-item">
                    <div class="field-label">Cargo</div>
                    <div class="field-value">{{ $atribuicao->funcionario->position }}</div>
                </div>
                @endif
                @if($mostra('email') && $atribuicao->funcionario->email)
                <div class="field-item">
                    <div class="field-label">E-mail</div>
                    <div class="field-value">{{ $atribuicao->funcionario->email }}</div>
                </div>
                @endif
                {{-- Campos extras preenchidos no modal --}}
                @foreach($camposExtras as $campo)
                    @php $val = trim($extrasValores[$campo['id']] ?? ''); @endphp
                    @if($val !== '')
                    <div class="field-item">
                        <div class="field-label">{{ $campo['label'] }}</div>
                        <div class="field-value">{{ $val }}</div>
                    </div>
                    @endif
                @endforeach
            </div>
        </div>

        {{-- ── Observações ────────────────────────────────────── --}}
        @if($mostra('observacoes') && $atribuicao->observacoes)
        <div class="data-section">
            <div class="section-header">
                <div class="section-dot purple"></div>
                <div class="section-title">Observações</div>
            </div>
            <div class="obs-box">{{ $atribuicao->observacoes }}</div>
        </div>
        @endif

        {{-- ── Cláusulas ──────────────────────────────────────── --}}
        @if(count($clausulas) > 0)
        <div class="clauses-box">
            <div class="clauses-title">Termos e Condições de Uso</div>
            <ol class="clauses-list">
                @foreach($clausulas as $c)
                    <li>{!! $renderBold($c['texto']) !!}</li>
                @endforeach
            </ol>
        </div>
        @endif

        {{-- ── Assinaturas ────────────────────────────────────── --}}
        <div class="sig-section">
            <div class="sig-grid {{ $sigClass }}">
                @foreach($assinaturas as $idx => $ass)
                    @php
                        if ($idx === 0) {
                            $sigNome  = $atribuicao->funcionario->name;
                            $sigPapel = !empty($ass['papel']) ? $ass['papel'] : ($atribuicao->funcionario->department?->name ?? '');
                            $sigCpf   = $extrasValores['cpf'] ?? '';
                        } elseif ($idx === 1) {
                            $sigNome  = $atribuicao->responsavel?->name ?? ($ass['label'] ?? 'Responsável RH/TI');
                            $sigPapel = !empty($ass['papel'])
                                ? $ass['papel'] . ' · ' . ($atribuicao->data_entrega?->format('d/m/Y') ?? '')
                                : 'Entregue por · ' . ($atribuicao->data_entrega?->format('d/m/Y') ?? '');
                            $sigCpf   = '';
                        } else {
                            $sigNome  = $ass['label'] ?? 'Assinatura';
                            $sigPapel = $ass['papel'] ?? '';
                            $sigCpf   = '';
                        }
                    @endphp
                    <div class="sig-card">
                        <div class="sig-space"></div>
                        <div class="sig-line">
                            <div class="sig-name">{{ $sigNome }}</div>
                            @if($sigPapel) <div class="sig-role">{{ $sigPapel }}</div> @endif
                            @if($sigCpf)   <div class="sig-cpf">CPF: {{ $sigCpf }}</div> @endif
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- ── Rodapé ─────────────────────────────────────────── --}}
        <div class="doc-footer">
            <div class="footer-left">{{ str_replace(array_keys($vars), array_values($vars), $rodape) }}</div>
            <div class="footer-right">
                <div class="footer-id">TERMO-{{ $termoNum }}</div>
                <div>Gerado em {{ now()->format('d/m/Y \à\s H:i') }}</div>
            </div>
        </div>

    </div>{{-- /body-wrap --}}
    <div class="sheet-footer-bar"></div>
</div>{{-- /sheet --}}

</body>
</html>
