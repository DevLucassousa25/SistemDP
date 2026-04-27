<?php

namespace App\Livewire\Components\Ui\Modal;
use App\Livewire\SecureComponent;

use App\Models\Manifestacao;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;

class OuvidoriaCreateModal extends SecureComponent
{
    public bool   $modalAberto = false;

    public string $categoria   = '';
    public string $assunto     = '';
    public string $descricao   = '';
    public bool   $isAnonimo   = false;

    // ── Abrir modal ─────────────────────────────────────────────

    #[On('abrir-modal-ouvidoria')]
    public function abrirModal(): void
    {
        $this->resetForm();
        $this->modalAberto = true;
    }

    // ── Fechar ──────────────────────────────────────────────────

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    // ── Validação ────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'categoria' => 'required|in:sugestao,reclamacao,denuncia,elogio',
            'assunto'   => 'required|string|min:5|max:150',
            'descricao' => 'required|string|min:20|max:3000',
            'isAnonimo' => 'boolean',
        ];
    }

    protected array $messages = [
        'categoria.required' => 'Selecione uma categoria para a manifestação.',
        'categoria.in'       => 'Categoria inválida.',
        'assunto.required'   => 'O assunto é obrigatório.',
        'assunto.min'        => 'O assunto deve ter pelo menos 5 caracteres.',
        'assunto.max'        => 'O assunto pode ter no máximo 150 caracteres.',
        'descricao.required' => 'A descrição é obrigatória.',
        'descricao.min'      => 'A descrição deve ter pelo menos 20 caracteres.',
        'descricao.max'      => 'A descrição pode ter no máximo 3000 caracteres.',
    ];

    // ── Salvar ──────────────────────────────────────────────────

    public function salvar(): void
    {
        $this->validate();

        Manifestacao::create([
            'categoria'   => $this->categoria,
            'assunto'     => $this->assunto,
            'descricao'   => $this->descricao,
            'is_anonimo'  => $this->isAnonimo,
            'user_id'     => $this->isAnonimo ? null : Auth::id(),
            'status'      => Manifestacao::STATUS_EM_ANALISE,
        ]);

        $this->fecharModal();

        $this->dispatch('manifestacaoCriada');
        $this->dispatch('openAlert',
            title: 'Manifestação enviada com sucesso!',
            description: 'Sua manifestação foi registrada e será analisada em breve. Guarde seu protocolo para acompanhamento.',
            type: 'success'
        );
    }

    // ── Reset ────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->categoria = '';
        $this->assunto   = '';
        $this->descricao = '';
        $this->isAnonimo = false;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.components.ui.modal.ouvidoria-create-modal');
    }
}
