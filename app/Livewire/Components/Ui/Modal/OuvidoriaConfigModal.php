<?php

namespace App\Livewire\Components\Ui\Modal;

use App\Livewire\SecureComponent;
use App\Models\ConfiguracaoOuvidoria;
use Livewire\Attributes\On;

class OuvidoriaConfigModal extends SecureComponent
{
    public bool $modalAberto = false;

    // ── Campos do formulário ──────────────────────────────────────────────────
    public bool   $autoEncerramentoAtivo    = false;
    public int    $prazoValor               = 3;       // valor digitado pelo usuário
    public string $prazoUnidade             = 'dias';  // 'horas' ou 'dias'
    public string $mensagemAutoEncerramento = '';

    // ──────────────────────────────────────────────────────────────────────────

    #[On('abrir-config-ouvidoria')]
    public function abrir(): void
    {
        $this->requireRhOrAdmin();

        $config = ConfiguracaoOuvidoria::instancia();

        $this->autoEncerramentoAtivo    = $config->auto_encerramento_ativo;
        $this->mensagemAutoEncerramento = $config->mensagem_auto_encerramento;

        // Converte horas para a unidade mais legível
        if ($config->prazo_horas % 24 === 0) {
            $this->prazoUnidade = 'dias';
            $this->prazoValor   = $config->prazo_horas / 24;
        } else {
            $this->prazoUnidade = 'horas';
            $this->prazoValor   = $config->prazo_horas;
        }

        $this->modalAberto = true;
    }

    public function fechar(): void
    {
        $this->modalAberto = false;
    }

    public function salvar(): void
    {
        $this->requireRhOrAdmin();

        $this->validate([
            'autoEncerramentoAtivo'    => 'boolean',
            'prazoValor'               => 'required|integer|min:1|max:8760', // máx 1 ano
            'prazoUnidade'             => 'required|in:horas,dias',
            'mensagemAutoEncerramento' => 'required|string|min:10|max:2000',
        ], [
            'prazoValor.required' => 'Informe o prazo.',
            'prazoValor.min'      => 'O prazo mínimo é 1.',
            'prazoValor.max'      => 'O prazo máximo é 8760 horas (1 ano).',
            'mensagemAutoEncerramento.required' => 'A mensagem não pode estar vazia.',
            'mensagemAutoEncerramento.min'      => 'A mensagem deve ter pelo menos 10 caracteres.',
        ]);

        // Converte para horas antes de salvar
        $prazoHoras = $this->prazoUnidade === 'dias'
            ? $this->prazoValor * 24
            : $this->prazoValor;

        $config = ConfiguracaoOuvidoria::instancia();
        $config->update([
            'auto_encerramento_ativo'    => $this->autoEncerramentoAtivo,
            'prazo_horas'                => $prazoHoras,
            'mensagem_auto_encerramento' => $this->sanitize($this->mensagemAutoEncerramento),
        ]);

        $this->modalAberto = false;
        $this->alertSuccess('Configurações salvas!', 'Auto-encerramento atualizado com sucesso.');
    }

    public function render()
    {
        return view('livewire.components.ui.modal.ouvidoria-config-modal');
    }
}
