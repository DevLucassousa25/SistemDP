<!DOCTYPE html>
<html lang="pt-BR">
<head>
<meta charset="UTF-8">
<style>
  @page { margin: 0; size: A4 landscape; }
  * { margin: 0; padding: 0; box-sizing: border-box; }
  body {
    font-family: 'DejaVu Sans', sans-serif;
    background: #fff;
    width: 297mm;
    height: 210mm;
    overflow: hidden;
    position: relative;
  }

  .header-band {
    position: absolute;
    top: 0; left: 0; right: 0;
    height: 28mm;
    background: #5b21b6;
  }
  .footer-band {
    position: absolute;
    bottom: 0; left: 0; right: 0;
    height: 14mm;
    background: #6d28d9;
  }
  .border-outer {
    position: absolute;
    inset: 8mm;
    border: 3px solid #6d28d9;
    border-radius: 4px;
  }
  .border-inner {
    position: absolute;
    inset: 11mm;
    border: 1px solid #a78bfa;
    border-radius: 3px;
  }
  .corner { position: absolute; width: 18mm; height: 18mm; }
  .corner-tl { top: 8mm; left: 8mm; border-top: 4px solid #a78bfa; border-left: 4px solid #a78bfa; }
  .corner-tr { top: 8mm; right: 8mm; border-top: 4px solid #a78bfa; border-right: 4px solid #a78bfa; }
  .corner-bl { bottom: 8mm; left: 8mm; border-bottom: 4px solid #a78bfa; border-left: 4px solid #a78bfa; }
  .corner-br { bottom: 8mm; right: 8mm; border-bottom: 4px solid #a78bfa; border-right: 4px solid #a78bfa; }

  .empresa-nome {
    position: absolute;
    top: 0mm; left: 0; right: 0;
    text-align: center;
    line-height: 20mm;
    font-size: 11pt;
    color: #fff;
    letter-spacing: 2px;
    text-transform: uppercase;
  }

  .medalha-circle {
    position: absolute;
    top: 34mm; right: 18mm;
    width: 22mm; height: 22mm;
    background: #6d28d9;
    border-radius: 50%;
    border: 3px solid #a78bfa;
  }
  .medalha-star {
    position: absolute;
    top: 26mm; right: 18.5mm;
    width: 22mm; height: 22mm;
    text-align: center;
    line-height: 22mm;
    color: #fff;
    font-size: 18pt;
  }

  /* Área central */
  .content-wrap {
    position: absolute;
    top: 60mm; left: 16mm; right: 16mm; bottom: 18mm;
    text-align: center;
  }

  .cert-label {
    font-size: 8pt;
    color: #7c3aed;
    letter-spacing: 4px;
    text-transform: uppercase;
    margin-bottom: 3mm;
    display: block;
  }
  .cert-intro {
    font-size: 10pt;
    color: #6b7280;
    margin-bottom: 2mm;
    display: block;
  }
  .colaborador {
    font-size: 22pt;
    font-weight: bold;
    color: #6d28d9;
    display: block;
    margin-bottom: 1mm;
  }
  .colaborador-line {
    width: 80mm;
    height: 2px;
    background: #a78bfa;
    margin: 0 auto 4mm auto;
  }
  .concluiu {
    font-size: 10pt;
    color: #6b7280;
    margin-bottom: 1.5mm;
    display: block;
  }
  .curso-label {
    font-size: 7pt;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 2px;
    display: block;
    margin-bottom: 1.5mm;
  }
  .curso-nome {
    font-size: 14pt;
    font-weight: bold;
    color: #1f2937;
    margin-bottom: 3mm;
    display: block;
    line-height: 1.3;
  }
  .nivel-badge {
    display: inline-block;
    background: #ede9fe;
    color: #7c3aed;
    font-size: 8pt;
    font-weight: bold;
    padding: 1mm 5mm;
    border-radius: 3px;
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 5mm;
  }

  /* Meta em tabela centralizada */
  table.meta {
    margin: 0 auto 4mm auto;
    border-collapse: collapse;
  }
  table.meta td {
    text-align: center;
    padding: 0 8mm;
    border-right: 1px solid #e5e7eb;
  }
  table.meta td:last-child { border-right: none; }
  .meta-label {
    font-size: 6.5pt;
    color: #9ca3af;
    text-transform: uppercase;
    letter-spacing: 1px;
    display: block;
    margin-bottom: 1mm;
  }
  .meta-value {
    font-size: 10pt;
    font-weight: bold;
    color: #374151;
    display: block;
  }

  /* Assinaturas em tabela centralizada */
  .assinaturas-wrap {
    position: absolute;
    bottom: 17mm; left: 0; right: 0;
  }
  table.assinaturas {
    margin: 0 auto;
    border-collapse: collapse;
  }
  table.assinaturas td {
    text-align: center;
    padding: 0 20mm;
  }
  .assinatura-linha {
    width: 55mm;
    height: 1px;
    background: #d1d5db;
    margin: 0 auto 2mm auto;
  }
  .assinatura-nome {
    font-size: 8pt;
    font-weight: bold;
    color: #374151;
    display: block;
  }
  .assinatura-cargo {
    font-size: 7pt;
    color: #9ca3af;
    display: block;
  }

  .codigo {
    position: absolute;
    bottom: 4mm; left: 0; right: 0;
    text-align: center;
    font-size: 7pt;
    color: #fff;
    letter-spacing: 1px;
  }
</style>
</head>
<body>

  <div class="header-band"></div>
  <div class="footer-band"></div>
  <div class="border-outer"></div>
  <div class="border-inner"></div>
  <div class="corner corner-tl"></div>
  <div class="corner corner-tr"></div>
  <div class="corner corner-bl"></div>
  <div class="corner corner-br"></div>

  <div class="empresa-nome">{{ $empresa }}</div>

  <div class="medalha-circle"></div>
  <div class="medalha-star">
    &#9733;
  </div>

  <div class="content-wrap">
    <span class="cert-label">Certificado de Conclusão</span>
    <span class="cert-intro">Certificamos que</span>
    <span class="colaborador">{{ $colaborador }}</span>
    <div class="colaborador-line"></div>
    <span class="concluiu">concluiu com êxito o curso</span>
    <span class="curso-label">curso</span>
    <span class="curso-nome">{{ $curso }}</span>
    <span class="nivel-badge">{{ $nivel }}</span>

    <table class="meta" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <span class="meta-label">Carga horária</span>
          <span class="meta-value">{{ $carga_horaria }}</span>
        </td>
        @if($instrutor)
        <td>
          <span class="meta-label">Instrutor</span>
          <span class="meta-value">{{ $instrutor }}</span>
        </td>
        @endif
        <td>
          <span class="meta-label">Concluído em</span>
          <span class="meta-value">{{ $concluido_em }}</span>
        </td>
        <td>
          <span class="meta-label">Emitido em</span>
          <span class="meta-value">{{ $emitido_em }}</span>
        </td>
      </tr>
    </table>
  </div>

  <div class="assinaturas-wrap">
    <table class="assinaturas" cellspacing="0" cellpadding="0">
      <tr>
        <td>
          <div class="assinatura-linha"></div>
          <span class="assinatura-nome">{{ $instrutor ?: $empresa }}</span>
          <span class="assinatura-cargo">Instrutor(a)</span>
        </td>
        <td>
          <div class="assinatura-linha"></div>
          <span class="assinatura-nome">{{ $empresa }}</span>
          <span class="assinatura-cargo">Departamento de RH</span>
        </td>
      </tr>
    </table>
  </div>

  <div class="codigo">Código de validação: {{ $codigo }} &nbsp;·&nbsp; {{ $empresa }}</div>

</body>
</html>
