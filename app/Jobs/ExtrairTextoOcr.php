<?php

namespace App\Jobs;

use App\Models\RhCurriculo;
use App\Services\OcrService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ExtrairTextoOcr implements ShouldQueue
{
    use Queueable;

    public int $tries   = 2;
    public int $timeout = 120;

    public function __construct(public int $curriculoId) {}

    public function handle(OcrService $ocr): void
    {
        $curriculo = RhCurriculo::find($this->curriculoId);

        if (!$curriculo || !$curriculo->arquivo_path) return;

        $texto = $ocr->extrair($curriculo->arquivo_path, $curriculo->arquivo_mime);

        $curriculo->updateQuietly([
            'texto_ocr'          => $texto,
            'ocr_processado_at'  => now(),
        ]);
    }
}
