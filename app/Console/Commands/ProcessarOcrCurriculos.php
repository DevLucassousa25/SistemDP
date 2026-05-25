<?php

namespace App\Console\Commands;

use App\Jobs\ExtrairTextoOcr;
use App\Models\RhCurriculo;
use Illuminate\Console\Command;

class ProcessarOcrCurriculos extends Command
{
    protected $signature   = 'rh:ocr {--force : Reprocessar mesmo os já processados}';
    protected $description = 'Extrai texto (OCR) dos currículos que possuem arquivo PDF/imagem';

    public function handle(): int
    {
        $query = RhCurriculo::whereNotNull('arquivo_path');

        if (!$this->option('force')) {
            $query->whereNull('ocr_processado_at');
        }

        $total = $query->count();

        if ($total === 0) {
            $this->info('Nenhum currículo para processar.');
            return self::SUCCESS;
        }

        $this->info("Processando {$total} currículo(s)...");
        $bar = $this->output->createProgressBar($total);
        $bar->start();

        $query->each(function (RhCurriculo $c) use ($bar) {
            ExtrairTextoOcr::dispatch($c->id);
            $bar->advance();
        });

        $bar->finish();
        $this->newLine();
        $this->info('Jobs despachados! O texto será extraído em background.');

        return self::SUCCESS;
    }
}
