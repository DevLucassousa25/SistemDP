<?php

namespace App\Livewire\Pages\Ouvidoria;

use App\Livewire\SecureComponent;
use App\Models\ConfiguracaoOuvidoria;

class Index extends SecureComponent
{
    // ── Config global de auto-encerramento ───────────────────────────────────
    public bool   $configAtiva              = false;
    public string $prazoValor               = '72';
    public string $prazoUnidade             = 'horas';
    public string $mensagemAutoEncerramento = '';
    public bool   $editandoConfig           = false;

    // ── Modal de sucesso ─────────────────────────────────────────────────────
    public bool   $modalSucesso         = false;
    public string $modalSucessoMensagem = '';
    public bool   $modalSucessoAtivou   = false; // true = ativou, false = só salvou

    // ──────────────────────────────────────────────────────────────────────────
    // LIFECYCLE
    // ──────────────────────────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->carregarConfig();
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ACTIONS
    // ──────────────────────────────────────────────────────────────────────────

    public function abrirModalOuvidoria(): void
    {
        $this->dispatch('abrir-modal-ouvidoria');
    }

    /**
     * Abre ou cancela a edição da configuração global.
     * Ao cancelar, restaura os valores do banco sem gravar nada.
     */
    public function toggleEditarConfig(): void
    {
        $this->requireRhOrAdmin();

        if ($this->editandoConfig) {
            $this->carregarConfig();
            $this->resetErrorBag();
        }

        $this->editandoConfig = ! $this->editandoConfig;
    }

    /**
     * Salva as configurações globais de auto-encerramento.
     */
    public function salvarConfig(): void
    {
        $this->requireRhOrAdmin();

        $this->validate([
            'prazoValor'               => 'required|integer|min:1|max:999',
            'prazoUnidade'             => 'required|in:horas,dias',
            'mensagemAutoEncerramento' => 'required|string|min:10|max:2000',
        ], [
            'prazoValor.required'               => 'Informe o prazo.',
            'prazoValor.min'                    => 'O prazo mínimo é 1.',
            'prazoValor.max'                    => 'O prazo máximo é 999.',
            'mensagemAutoEncerramento.required' => 'A mensagem de encerramento é obrigatória.',
            'mensagemAutoEncerramento.min'      => 'A mensagem deve ter ao menos 10 caracteres.',
        ]);

        $prazoHoras = $this->prazoUnidade === 'dias'
            ? (int) $this->prazoValor * 24
            : (int) $this->prazoValor;

        ConfiguracaoOuvidoria::instancia()->update([
            'auto_encerramento_ativo'    => $this->configAtiva,
            'prazo_horas'                => $prazoHoras,
            'mensagem_auto_encerramento' => $this->mensagemAutoEncerramento,
        ]);

        $this->editandoConfig = false;
        $this->carregarConfig(); // re-sincroniza para exibir valores formatados

        $this->modalSucessoAtivou   = $this->configAtiva;
        $this->modalSucessoMensagem = $this->configAtiva
            ? 'Auto-encerramento ativado com sucesso! Todas as ouvidorias abertas serão monitoradas automaticamente.'
            : 'Configuração salva. O auto-encerramento está desativado.';
        $this->modalSucesso = true;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ──────────────────────────────────────────────────────────────────────────

    private function carregarConfig(): void
    {
        $config = ConfiguracaoOuvidoria::instancia();

        $this->configAtiva              = $config->auto_encerramento_ativo;
        $this->mensagemAutoEncerramento = $config->mensagem_auto_encerramento ?? '';

        // Converte horas → dias se for múltiplo exato de 24, para melhor UX
        if ($config->prazo_horas % 24 === 0) {
            $this->prazoValor   = (string) ($config->prazo_horas / 24);
            $this->prazoUnidade = 'dias';
        } else {
            $this->prazoValor   = (string) $config->prazo_horas;
            $this->prazoUnidade = 'horas';
        }
    }

    // ──────────────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.ouvidoria.index', [
            'configGlobal' => ConfiguracaoOuvidoria::instancia(),
        ]);
    }
}
