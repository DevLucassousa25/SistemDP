<?php

namespace App\Actions;

use App\Models\ConfiguracaoOuvidoria;
use App\Models\Manifestacao;
use App\Models\RespostaManifestacao;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Encerra automaticamente ouvidorias que ultrapassaram o prazo de inatividade.
 *
 * Pode ser invocada de duas formas:
 *  - execute()      → roda imediatamente (usado pelo artisan command)
 *  - executeIfDue() → roda apenas se o intervalo mínimo entre verificações
 *                     já passou (usado pela verificação lazy no Livewire)
 */
class AutoEncerrarOuvidoriasAction
{
    /**
     * Intervalo mínimo entre verificações lazy, em minutos.
     * Evita rodar a cada request sem sobrecarregar o banco.
     */
    private const INTERVALO_MINUTOS = 60;

    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Executa o auto-encerramento imediatamente.
     * Retorna o número de ouvidorias encerradas.
     */
    public function execute(): int
    {
        $config = ConfiguracaoOuvidoria::instancia();

        if (! $config->auto_encerramento_ativo) {
            return 0;
        }

        // Marca o momento da verificação antes de processar,
        // para que outro request concorrente não dispare ao mesmo tempo.
        $config->update(['ultima_verificacao_em' => now()]);

        $encerradas = 0;

        Manifestacao::whereIn('status', [
            Manifestacao::STATUS_EM_ANALISE,
            Manifestacao::STATUS_EM_ANDAMENTO,
        ])
        ->where('auto_encerramento_desativado', false)
        ->chunk(100, function ($manifestacoes) use ($config, &$encerradas) {
            foreach ($manifestacoes as $manifestacao) {

                if (! $manifestacao->deveAutoEncerrar()) {
                    continue;
                }

                try {
                    DB::transaction(function () use ($manifestacao, $config) {
                        RespostaManifestacao::create([
                            'manifestacao_id' => $manifestacao->id,
                            'respondente_id'  => null,
                            'conteudo'        => $config->mensagem_auto_encerramento,
                            'is_interno'      => false,
                            'is_automatica'   => true,
                        ]);

                        $manifestacao->update([
                            'status'        => Manifestacao::STATUS_CONCLUIDO,
                            'respondido_em' => $manifestacao->respondido_em ?? now(),
                            'concluido_em'  => now(),
                        ]);
                    });

                    $encerradas++;

                } catch (\Throwable $e) {
                    Log::error('Erro ao auto-encerrar ouvidoria', [
                        'manifestacao_id' => $manifestacao->id,
                        'protocolo'       => $manifestacao->protocolo,
                        'erro'            => $e->getMessage(),
                    ]);
                }
            }
        });

        return $encerradas;
    }

    /**
     * Executa apenas se já passou o intervalo mínimo desde a última verificação.
     * Ideal para chamadas lazy (no mount do Livewire), sem precisar de cron.
     *
     * Retorna o número de ouvidorias encerradas, ou 0 se ainda não era a hora.
     */
    public function executeIfDue(): int
    {
        $config = ConfiguracaoOuvidoria::instancia();

        if (! $config->auto_encerramento_ativo) {
            return 0;
        }

        // Ainda não passou o intervalo mínimo → pula
        if (
            $config->ultima_verificacao_em !== null &&
            now()->diffInMinutes($config->ultima_verificacao_em) < self::INTERVALO_MINUTOS
        ) {
            return 0;
        }

        return $this->execute();
    }
}
