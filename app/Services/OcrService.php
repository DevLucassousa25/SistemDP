<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class OcrService
{
    /**
     * Extrai texto de um arquivo de currículo (PDF, DOC ou DOCX).
     * Usa bibliotecas PHP puras — não depende de pdftotext nem tesseract.
     */
    public function extrair(string $storagePath, ?string $mime = null): ?string
    {
        $absolutePath = Storage::disk('public')->path($storagePath);

        if (!file_exists($absolutePath)) {
            Log::warning("OcrService: arquivo não encontrado: {$absolutePath}");
            return null;
        }

        $mime = $mime ?? mime_content_type($absolutePath);
        $ext  = strtolower(pathinfo($absolutePath, PATHINFO_EXTENSION));

        Log::info("OcrService: processando (mime: {$mime}, ext: {$ext})");

        try {
            // PDF → smalot/pdfparser
            if ($mime === 'application/pdf' || $ext === 'pdf') {
                return $this->extrairPdf($absolutePath);
            }

            // DOCX → phpoffice/phpword (ZipArchive + XML)
            if (in_array($ext, ['docx']) || $mime === 'application/vnd.openxmlformats-officedocument.wordprocessingml.document') {
                return $this->extrairDocx($absolutePath);
            }

            // DOC → leitura bruta (melhor esforço)
            if ($ext === 'doc' || $mime === 'application/msword') {
                return $this->extrairDoc($absolutePath);
            }

            Log::warning("OcrService: formato não suportado: {$mime} / .{$ext}");
        } catch (\Throwable $e) {
            Log::error('OcrService: ' . $e->getMessage());
        }

        return null;
    }

    // ── PDF ──────────────────────────────────────────────────────────────

    private function extrairPdf(string $path): ?string
    {
        if (!class_exists(\Smalot\PdfParser\Parser::class)) {
            Log::error('OcrService: smalot/pdfparser não instalado. Execute: composer require smalot/pdfparser');
            return null;
        }

        $config = new \Smalot\PdfParser\Config();
        $config->setRetainImageContent(false); // evita consumo excessivo de memória

        $parser = new \Smalot\PdfParser\Parser([], $config);
        $pdf    = $parser->parseFile($path);
        $texto  = $pdf->getText();

        if (empty(trim($texto))) {
            Log::info('OcrService: PDF sem texto selecionável (provavelmente escaneado). OCR de imagem não disponível sem tesseract.');
            return null;
        }

        Log::info('OcrService: PDF extraído — ' . strlen($texto) . ' chars');
        return $this->limpar($texto);
    }

    // ── DOCX ─────────────────────────────────────────────────────────────

    private function extrairDocx(string $path): ?string
    {
        if (!class_exists('ZipArchive')) {
            Log::error('OcrService: extensão ZipArchive não disponível no PHP.');
            return null;
        }

        $zip = new \ZipArchive();
        if ($zip->open($path) !== true) {
            Log::error('OcrService: não foi possível abrir o DOCX como ZIP.');
            return null;
        }

        $xml = $zip->getFromName('word/document.xml');
        $zip->close();

        if ($xml === false) {
            return null;
        }

        // Remove tags XML, mantém apenas o texto
        $texto = strip_tags(str_replace(
            ['</w:p>', '</w:tr>'],
            ["\n", "\n"],
            $xml
        ));

        Log::info('OcrService: DOCX extraído — ' . strlen($texto) . ' chars');
        return $this->limpar($texto);
    }

    // ── DOC (legado) ──────────────────────────────────────────────────────

    private function extrairDoc(string $path): ?string
    {
        // Leitura bruta: extrai sequências de caracteres legíveis do binário .doc
        $content = file_get_contents($path);
        if ($content === false) return null;

        // Converte de UTF-16LE (comum em .doc) se necessário
        if (substr($content, 0, 2) === "\xFF\xFE") {
            $content = mb_convert_encoding($content, 'UTF-8', 'UTF-16LE');
        }

        // Extrai apenas caracteres imprimíveis em sequências longas (>3 chars)
        preg_match_all('/[\x20-\x7E\xC0-\xFF]{4,}/', $content, $m);
        $texto = implode(' ', $m[0]);

        if (empty(trim($texto))) return null;

        Log::info('OcrService: DOC (bruto) extraído — ' . strlen($texto) . ' chars');
        return $this->limpar($texto);
    }

    // ── Utilitários ───────────────────────────────────────────────────────

    private function limpar(string $texto): string
    {
        // Garante UTF-8 válido
        $texto = mb_convert_encoding($texto, 'UTF-8', 'UTF-8');

        // Remove espaços múltiplos e linhas em branco excessivas
        $texto = preg_replace('/[ \t]+/', ' ', $texto) ?? $texto;
        $texto = preg_replace('/\n{3,}/', "\n\n", $texto) ?? $texto;

        return trim($texto);
    }
}
