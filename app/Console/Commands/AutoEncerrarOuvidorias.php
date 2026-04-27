<?php

namespace App\Console\Commands;

use App\Actions\AutoEncerrarOuvidoriasAction;
use App\Models\ConfiguracaoOuvidoria;
use App\Models\Manifestacao;
use Illuminate\Console\Command;

class AutoEncerrarOuvidorias extends Command
{
    /**
     * Verifica manifestações ativas e encerra automaticamente as que
     * ultrapassaram o prazo de inatividade configurado.
     */
    protected $signature = 'ouvidoria:auto-encerrar
                            {--dry-run : Simula o encerramento sem gravar no banco}';

    protected $description = 'Encerra automaticamente ouvidorias inativas que ultrapassaram o prazo configurado.';

    public function handle(): int
    {
        $config = ConfiguracaoOuvidoria::instancia();

        if (! $config->auto_encerramento_ativo) {
            $this->info('Auto-encerramento está desativado. Nenhuma ação realizada.');
            return self::SUCCESS;
        }

        if ($this->option('dry-run')) {
            $this->warn('[DRY-RUN] Simulando — nenhuma alteração será gravada.');

            $candidatas = Manifestacao::whereIn('status', [
                Manifestacao::STATUS_EM_ANALISE,
                Manifestacao::STATUS_EM_ANDAMENTO,
            ])
            ->where('auto_encerramento_desativado', false)
            ->get()
            ->filter(fn ($m) => $m->deveAutoEncerrar());

            foreach ($candidatas as $m) {
                $this->line(sprintf(
                    '  → Seria encerrada: %s (inativo há %dh, prazo: %dh)',
                    $m->protocolo,
                    now()->diffInHours($m->ultimaAtividadeEm()),
                    $m->prazoEfetivoHoras()
                ));
            }

            $this->info("Concluído (dry-run). Candidatas: {$candidatas->count()}");
            return self::SUCCESS;
        }

        $this->info('Iniciando verificação de ouvidorias inativas...');

        $encerradas = (new AutoEncerrarOuvidoriasAction())->execute();

        $this->info("Concluído. Encerradas: {$encerradas}");

        return self::SUCCESS;
    }
}
