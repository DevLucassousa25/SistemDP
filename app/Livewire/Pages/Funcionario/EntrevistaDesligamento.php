<?php

namespace App\Livewire\Pages\Funcionario;

use App\Models\RhEntrevistaDesligamento;
use Livewire\Component;

class EntrevistaDesligamento extends Component
{
    public string $token;

    // Estado: formulario | concluido | invalido | ja_respondido
    public string $tela = 'formulario';

    public ?RhEntrevistaDesligamento $entrevista = null;

    public ?int   $satisfacaoGestao      = null;
    public ?int   $satisfacaoCultura     = null;
    public ?int   $satisfacaoRemuneracao = null;
    public ?int   $satisfacaoCrescimento = null;
    public ?int   $satisfacaoEquilibrio  = null;
    public ?bool  $recomendariaEmpresa   = null;
    public string $motivoPrincipal       = '';
    public string $pontosPositivos       = '';
    public string $pontosMelhoria        = '';
    public string $outrosComentarios     = '';

    public function mount(string $token): void
    {
        $this->token = $token;

        $e = RhEntrevistaDesligamento::where('token', $token)
            ->with('desligamento.funcionario')
            ->first();

        if (!$e) { $this->tela = 'invalido'; return; }

        $this->entrevista = $e;

        if ($e->respondido_at) { $this->tela = 'ja_respondido'; return; }
    }

    public function responder(): void
    {
        if (!$this->entrevista || $this->tela !== 'formulario') return;

        $this->validate([
            'motivoPrincipal' => 'required|string',
        ], ['motivoPrincipal.required' => 'Selecione o motivo principal.']);

        $this->entrevista->update([
            'respondido_at'          => now(),
            'satisfacao_gestao'      => $this->satisfacaoGestao,
            'satisfacao_cultura'     => $this->satisfacaoCultura,
            'satisfacao_remuneracao' => $this->satisfacaoRemuneracao,
            'satisfacao_crescimento' => $this->satisfacaoCrescimento,
            'satisfacao_equilibrio'  => $this->satisfacaoEquilibrio,
            'recomendaria_empresa'   => $this->recomendariaEmpresa,
            'motivo_principal'       => $this->motivoPrincipal,
            'pontos_positivos'       => $this->pontosPositivos ?: null,
            'pontos_melhoria'        => $this->pontosMelhoria ?: null,
            'outros_comentarios'     => $this->outrosComentarios ?: null,
        ]);

        $this->tela = 'concluido';
    }

    public function render()
    {
        return view('livewire.pages.funcionario.entrevista-desligamento')
            ->layout('components.layouts.guest');
    }
}
