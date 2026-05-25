<?php

namespace App\Console\Commands;

use App\Models\CalendarioEvento;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportarFeriadosNacionais extends Command
{
    protected $signature = 'calendario:importar-feriados
                            {anos?* : Anos a importar (padrão: ano atual e próximo)}
                            {--force : Atualiza registros já existentes}';

    protected $description = 'Importa feriados nacionais brasileiros via BrasilAPI (gratuito, sem chave)';

    // URL base da BrasilAPI
    private const API_URL = 'https://brasilapi.com.br/api/feriados/v1/';

    public function handle(): int
    {
        $anos = $this->argument('anos');

        if (empty($anos)) {
            $anos = [now()->year, now()->addYear()->year];
        }

        $this->info('🗓  Importando feriados nacionais via BrasilAPI...');
        $this->newLine();

        $totalCriados    = 0;
        $totalAtualizados = 0;
        $totalIgnorados  = 0;

        foreach ($anos as $ano) {
            $this->line("  → Buscando feriados de <comment>{$ano}</comment>...");

            try {
                $response = Http::timeout(10)->get(self::API_URL . $ano);

                if ($response->failed()) {
                    $this->error("    Falha ao buscar {$ano}: HTTP {$response->status()}");
                    continue;
                }

                $feriados = $response->json();

                if (empty($feriados)) {
                    $this->warn("    Nenhum feriado retornado para {$ano}.");
                    continue;
                }

                foreach ($feriados as $f) {
                    $data   = $f['date']   ?? null;
                    $titulo = $f['name']   ?? null;
                    $tipo   = $f['type']   ?? 'national'; // 'national' | 'optional'

                    if (!$data || !$titulo) continue;

                    // BrasilAPI retorna 'national' para feriados obrigatórios
                    $tipoInterno = $tipo === 'national' ? 'feriado_nacional' : 'data_comemorativa';

                    $existing = CalendarioEvento::where('data', $data)
                        ->where('titulo', $titulo)
                        ->first();

                    if ($existing) {
                        if ($this->option('force')) {
                            $existing->update([
                                'tipo' => $tipoInterno,
                                'cor'  => '#EF4444',
                            ]);
                            $totalAtualizados++;
                        } else {
                            $totalIgnorados++;
                        }
                        continue;
                    }

                    CalendarioEvento::create([
                        'data'             => $data,
                        'titulo'           => $titulo,
                        'tipo'             => $tipoInterno,
                        'cor'              => '#EF4444',
                        'recorrente_anual' => false, // BrasilAPI já retorna por ano
                        'descricao'        => null,
                        'created_by'       => null,
                    ]);

                    $totalCriados++;
                }

                $this->line("    <info>✓</info> {$ano}: " . count($feriados) . " feriados processados.");

            } catch (\Exception $e) {
                $this->error("    Erro ao processar {$ano}: " . $e->getMessage());
            }
        }

        $this->newLine();
        $this->table(
            ['Criados', 'Atualizados', 'Ignorados (já existem)'],
            [[$totalCriados, $totalAtualizados, $totalIgnorados]]
        );

        if ($totalIgnorados > 0 && !$this->option('force')) {
            $this->line('<comment>Dica:</comment> use <info>--force</info> para sobrescrever registros existentes.');
        }

        $this->newLine();
        $this->info('Concluído!');

        return self::SUCCESS;
    }
}
