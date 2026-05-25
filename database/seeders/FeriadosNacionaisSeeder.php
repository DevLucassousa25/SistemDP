<?php

namespace Database\Seeders;

use App\Models\CalendarioEvento;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;

class FeriadosNacionaisSeeder extends Seeder
{
    private const API_URL = 'https://brasilapi.com.br/api/feriados/v1/';

    public function run(): void
    {
        $anos = [now()->year, now()->addYear()->year];

        $this->command->info('Importando feriados via BrasilAPI...');

        $importadosDaApi = false;

        foreach ($anos as $ano) {
            try {
                $response = Http::timeout(8)->get(self::API_URL . $ano);

                if ($response->successful() && !empty($response->json())) {
                    foreach ($response->json() as $f) {
                        $data   = $f['date'] ?? null;
                        $titulo = $f['name'] ?? null;
                        $tipo   = ($f['type'] ?? 'national') === 'national'
                            ? 'feriado_nacional'
                            : 'data_comemorativa';

                        if (!$data || !$titulo) continue;

                        CalendarioEvento::firstOrCreate(
                            ['data' => $data, 'titulo' => $titulo],
                            [
                                'tipo'             => $tipo,
                                'cor'              => '#EF4444',
                                'recorrente_anual' => false,
                                'descricao'        => null,
                                'created_by'       => null,
                            ]
                        );
                    }

                    $this->command->info("  ✓ {$ano}: " . count($response->json()) . " feriados importados da BrasilAPI.");
                    $importadosDaApi = true;
                    continue;
                }
            } catch (\Exception $e) {
                // sem internet ou API fora do ar — usa fallback abaixo
            }

            // ── Fallback: dados fixos caso a API não esteja acessível ─────
            $this->command->warn("  ⚠ API indisponível para {$ano}. Usando dados fixos.");
            $this->seedFallback($ano);
        }

        if (!$importadosDaApi) {
            $this->command->line('');
            $this->command->line('Dica: para importar feriados atualizados sempre que quiser, rode:');
            $this->command->line('  php artisan calendario:importar-feriados');
        }
    }

    // ── Fallback com feriados fixos ────────────────────────────────────────

    private function seedFallback(int $ano): void
    {
        $fixos = $this->feriadosFixos();

        // Feriados móveis por ano
        $moveis = $this->feriadosMoveis($ano);

        $todos = array_merge(
            array_map(fn($f) => array_merge($f, ['data' => "{$ano}-{$f['mes_dia']}"]), $fixos),
            $moveis
        );

        foreach ($todos as $f) {
            CalendarioEvento::firstOrCreate(
                ['data' => $f['data'], 'titulo' => $f['titulo']],
                [
                    'tipo'             => 'feriado_nacional',
                    'cor'              => '#EF4444',
                    'recorrente_anual' => false,
                    'descricao'        => null,
                    'created_by'       => null,
                ]
            );
        }
    }

    /** Feriados de data fixa (mês-dia). */
    private function feriadosFixos(): array
    {
        return [
            ['mes_dia' => '01-01', 'titulo' => 'Confraternização Universal'],
            ['mes_dia' => '04-21', 'titulo' => 'Tiradentes'],
            ['mes_dia' => '05-01', 'titulo' => 'Dia do Trabalho'],
            ['mes_dia' => '09-07', 'titulo' => 'Independência do Brasil'],
            ['mes_dia' => '10-12', 'titulo' => 'Nossa Senhora Aparecida'],
            ['mes_dia' => '11-02', 'titulo' => 'Finados'],
            ['mes_dia' => '11-15', 'titulo' => 'Proclamação da República'],
            ['mes_dia' => '11-20', 'titulo' => 'Dia da Consciência Negra'],
            ['mes_dia' => '12-25', 'titulo' => 'Natal'],
        ];
    }

    /** Feriados móveis calculados para um ano específico. */
    private function feriadosMoveis(int $ano): array
    {
        // Algoritmo de Gauss para calcular a Páscoa
        $pascoa = $this->calcularPascoa($ano);

        return [
            // Carnaval = Páscoa − 47 dias
            ['data' => $pascoa->copy()->subDays(48)->toDateString(), 'titulo' => 'Carnaval (segunda-feira)'],
            ['data' => $pascoa->copy()->subDays(47)->toDateString(), 'titulo' => 'Carnaval (terça-feira)'],
            // Sexta Santa = Páscoa − 2
            ['data' => $pascoa->copy()->subDays(2)->toDateString(),  'titulo' => 'Sexta-feira Santa'],
            // Páscoa
            ['data' => $pascoa->toDateString(),                       'titulo' => 'Páscoa'],
            // Corpus Christi = Páscoa + 60
            ['data' => $pascoa->copy()->addDays(60)->toDateString(),  'titulo' => 'Corpus Christi'],
        ];
    }

    private function calcularPascoa(int $ano): \Carbon\Carbon
    {
        $a = $ano % 19;
        $b = intdiv($ano, 100);
        $c = $ano % 100;
        $d = intdiv($b, 4);
        $e = $b % 4;
        $f = intdiv($b + 8, 25);
        $g = intdiv($b - $f + 1, 3);
        $h = (19 * $a + $b - $d - $g + 15) % 30;
        $i = intdiv($c, 4);
        $k = $c % 4;
        $l = (32 + 2 * $e + 2 * $i - $h - $k) % 7;
        $m = intdiv($a + 11 * $h + 22 * $l, 451);
        $mes = intdiv($h + $l - 7 * $m + 114, 31);
        $dia = (($h + $l - 7 * $m + 114) % 31) + 1;

        return \Carbon\Carbon::create($ano, $mes, $dia);
    }
}
