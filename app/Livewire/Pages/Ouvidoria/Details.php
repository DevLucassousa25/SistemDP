<?php

namespace App\Livewire\Pages\Ouvidoria;

use App\Livewire\SecureComponent;
use App\Models\AnexoManifestacao;
use App\Models\ConfiguracaoOuvidoria;
use App\Models\Manifestacao;
use App\Models\RespostaManifestacao;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;

class Details extends SecureComponent
{
    use WithFileUploads;

    #[Locked]
    public string $manifestacaoId;

    public ?Manifestacao $manifestacao = null;

    public string $novaResposta = '';

    /** Arquivos temporários selecionados pelo usuário antes do envio. */
    public array $anexosUpload = [];

    // ── Campos de auto-encerramento por manifestação ──────────────────────────
    public bool   $autoEncerramentoDesativado  = false;
    public string $prazoPersonalizadoInput     = '';   // string para o input (vazio = usa global)
    public bool   $editandoAutoEncerramento    = false; // controla modo view ↔ edit

    // ── Modal de sucesso ─────────────────────────────────────────────────────
    public bool   $modalSucesso         = false;
    public string $modalSucessoMensagem = '';
    public string $modalSucessoTipo     = 'save'; // 'save' | 'disable'

    // ──────────────────────────────────────────────────────────────────────────
    // LIFECYCLE
    // ──────────────────────────────────────────────────────────────────────────

    public function mount(string $id): void
    {
        $user = Auth::user();

        $query = Manifestacao::with([
            'user',
            'respostas.respondente',
            'respostas.anexos',
            'anexos',
        ])->where('id', $id);

        // IDOR: usuário comum só acessa as próprias manifestações
        if (! $user->isRhOuDp()) {
            $query->where('user_id', $user->id);
        }

        $this->manifestacao   = $query->firstOrFail();
        $this->manifestacaoId = $id;

        // Sincroniza estado de auto-encerramento local com o modelo
        $this->autoEncerramentoDesativado = (bool) $this->manifestacao->auto_encerramento_desativado;
        $this->prazoPersonalizadoInput    = $this->manifestacao->prazo_personalizado_horas !== null
            ? (string) $this->manifestacao->prazo_personalizado_horas
            : '';
    }

    // ──────────────────────────────────────────────────────────────────────────
    // ACTIONS
    // ──────────────────────────────────────────────────────────────────────────

    public function responder(): void
    {
        $this->requireRhOrAdmin();

        $this->validate([
            'novaResposta'    => 'required|string|min:5|max:3000',
            'anexosUpload'    => 'nullable|array|max:5',
            'anexosUpload.*'  => [
                'nullable',
                'file',
                'max:10240', // 10 MB por arquivo
                'mimes:jpeg,jpg,png,gif,webp,pdf,doc,docx,xls,xlsx',
            ],
        ], [
            'novaResposta.required' => 'Escreva uma resposta antes de enviar.',
            'novaResposta.min'      => 'A resposta deve ter pelo menos 5 caracteres.',
            'novaResposta.max'      => 'A resposta não pode ultrapassar 3000 caracteres.',
            'anexosUpload.max'      => 'Você pode enviar no máximo 5 arquivos por resposta.',
            'anexosUpload.*.max'    => 'Cada arquivo não pode ultrapassar 10 MB.',
            'anexosUpload.*.mimes'  => 'Formatos permitidos: imagens, PDF, Word e Excel.',
        ]);

        // Cria a resposta
        $resposta = RespostaManifestacao::create([
            'manifestacao_id' => $this->manifestacaoId,
            'respondente_id'  => Auth::id(),
            'conteudo'        => $this->sanitize($this->novaResposta),
            'is_interno'      => false,
        ]);

        // Salva os arquivos vinculados à resposta
        foreach ($this->anexosUpload as $file) {
            $caminho = $file->store(
                'ouvidoria-respostas/' . $this->manifestacaoId,
                'public'
            );

            AnexoManifestacao::create([
                'manifestacao_id'          => $this->manifestacaoId,
                'resposta_manifestacao_id' => $resposta->id,
                'uploaded_by'              => Auth::id(),
                'nome_arquivo'             => $file->getClientOriginalName(),
                'caminho'                  => $caminho,
                'mime_type'                => $file->getMimeType(),
                'tamanho_bytes'            => $file->getSize(),
            ]);
        }

        // Avança automaticamente para "respondido" se ainda estiver em análise ou andamento
        if (in_array($this->manifestacao->status, [Manifestacao::STATUS_EM_ANALISE, Manifestacao::STATUS_EM_ANDAMENTO])) {
            $this->manifestacao->update([
                'status'        => Manifestacao::STATUS_RESPONDIDO,
                'respondido_em' => $this->manifestacao->respondido_em ?? now(),
            ]);
        }

        $this->novaResposta  = '';
        $this->anexosUpload  = [];

        // Re-busca do DB para garantir que o Livewire exibe o estado mais recente
        $this->recarregarManifestacao();

        $this->dispatch('manifestacaoAtualizada');
        $this->alertSuccess('Resposta enviada com sucesso!');
    }

    /**
     * Remove um arquivo da lista de uploads temporários antes do envio.
     */
    public function removerAnexo(int $index): void
    {
        array_splice($this->anexosUpload, $index, 1);
    }

    /**
     * Atualiza o status da manifestação.
     * Apenas RH/Admin pode executar. Registra os timestamps de marco.
     */
    public function atualizarStatus(string $novoStatus): void
    {
        $this->requireRhOrAdmin();

        $statusValidos = [
            Manifestacao::STATUS_EM_ANALISE,
            Manifestacao::STATUS_EM_ANDAMENTO,
            Manifestacao::STATUS_RESPONDIDO,
            Manifestacao::STATUS_CONCLUIDO,
        ];

        if (! in_array($novoStatus, $statusValidos, true)) {
            $this->alertError('Status inválido.');
            return;
        }

        if ($novoStatus === $this->manifestacao->status) {
            return; // nada a fazer
        }

        // Impede marcar como "respondido" ou "concluído" sem ao menos uma resposta real
        $statusQueRequerResposta = [Manifestacao::STATUS_RESPONDIDO, Manifestacao::STATUS_CONCLUIDO];

        if (in_array($novoStatus, $statusQueRequerResposta, true)) {
            $temResposta = $this->manifestacao->respostas()
                ->where('is_automatica', false)
                ->exists();

            if (! $temResposta) {
                $this->alertError('Envie uma resposta antes de alterar o status para "' . ($novoStatus === Manifestacao::STATUS_RESPONDIDO ? 'Respondido' : 'Concluído') . '".');
                return;
            }
        }

        $updates = ['status' => $novoStatus];

        // Registra timestamps de marco apenas na primeira vez
        if ($novoStatus === Manifestacao::STATUS_RESPONDIDO && ! $this->manifestacao->respondido_em) {
            $updates['respondido_em'] = now();
        }

        if ($novoStatus === Manifestacao::STATUS_CONCLUIDO) {
            if (! $this->manifestacao->respondido_em) {
                $updates['respondido_em'] = now();
            }
            if (! $this->manifestacao->concluido_em) {
                $updates['concluido_em'] = now();
            }
        }

        $this->manifestacao->update($updates);

        $this->recarregarManifestacao();

        $this->dispatch('manifestacaoAtualizada');

        $labels = [
            Manifestacao::STATUS_EM_ANALISE   => 'Em Análise',
            Manifestacao::STATUS_EM_ANDAMENTO => 'Em Andamento',
            Manifestacao::STATUS_RESPONDIDO   => 'Respondido',
            Manifestacao::STATUS_CONCLUIDO    => 'Concluído',
        ];

        $this->alertSuccess('Status atualizado para "' . ($labels[$novoStatus] ?? $novoStatus) . '".');
    }

    // ──────────────────────────────────────────────────────────────────────────
    // AUTO-ENCERRAMENTO POR MANIFESTAÇÃO
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Abre/fecha o formulário de edição de auto-encerramento.
     * Ao cancelar, restaura os valores do modelo sem gravar nada.
     */
    public function toggleEditarAutoEncerramento(): void
    {
        $this->requireRhOrAdmin();

        if ($this->editandoAutoEncerramento) {
            // Cancelar: descarta alterações pendentes
            $this->autoEncerramentoDesativado = (bool) $this->manifestacao->auto_encerramento_desativado;
            $this->prazoPersonalizadoInput    = $this->manifestacao->prazo_personalizado_horas !== null
                ? (string) $this->manifestacao->prazo_personalizado_horas
                : '';
            $this->resetErrorBag(['autoEncerramentoDesativado', 'prazoPersonalizadoInput']);
        }

        $this->editandoAutoEncerramento = ! $this->editandoAutoEncerramento;
    }

    /**
     * Salva as configurações de auto-encerramento específicas desta manifestação.
     */
    public function salvarAutoEncerramento(): void
    {
        $this->requireRhOrAdmin();

        $this->validate([
            'autoEncerramentoDesativado' => 'boolean',
            'prazoPersonalizadoInput'    => 'nullable|integer|min:1|max:8760',
        ], [
            'prazoPersonalizadoInput.min' => 'O prazo mínimo é 1 hora.',
            'prazoPersonalizadoInput.max' => 'O prazo máximo é 8760 horas (1 ano).',
        ]);

        $this->manifestacao->update([
            'auto_encerramento_desativado' => $this->autoEncerramentoDesativado,
            'prazo_personalizado_horas'    => $this->prazoPersonalizadoInput !== ''
                ? (int) $this->prazoPersonalizadoInput
                : null,
        ]);

        $this->recarregarManifestacao();
        $this->editandoAutoEncerramento = false; // volta ao modo visualização

        if ($this->autoEncerramentoDesativado) {
            $this->modalSucessoTipo     = 'disable';
            $this->modalSucessoMensagem = 'O auto-encerramento foi desativado para esta ouvidoria. Ela não será encerrada automaticamente.';
        } else {
            $prazo = $this->prazoPersonalizadoInput !== ''
                ? $this->prazoPersonalizadoInput . 'h (personalizado)'
                : ConfiguracaoOuvidoria::instancia()->prazo_horas . 'h (global)';
            $this->modalSucessoTipo     = 'save';
            $this->modalSucessoMensagem = 'Configuração salva! Esta ouvidoria será monitorada com prazo de ' . $prazo . '.';
        }

        $this->modalSucesso = true;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS PRIVADOS
    // ──────────────────────────────────────────────────────────────────────────

    private function recarregarManifestacao(): void
    {
        $this->manifestacao = Manifestacao::with([
            'user',
            'respostas.respondente',
            'respostas.anexos',
            'anexos',
        ])->find($this->manifestacaoId);
    }

    // ──────────────────────────────────────────────────────────────────────────

    public function render()
    {
        // Verifica se há ao menos uma resposta humana (não automática)
        $temResposta = $this->manifestacao?->respostas
            ->where('is_automatica', false)
            ->isNotEmpty() ?? false;

        return view('livewire.pages.ouvidoria.details', [
            'configGlobal' => ConfiguracaoOuvidoria::instancia(),
            'temResposta'  => $temResposta,
        ]);
    }
}
