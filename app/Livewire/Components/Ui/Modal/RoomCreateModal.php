<?php

namespace App\Livewire\Components\Ui\Modal;
use App\Livewire\SecureComponent;

use App\Models\room;
use App\Models\room_images;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\On;
use Livewire\WithFileUploads;

class RoomCreateModal extends SecureComponent
{
    use WithFileUploads;

    public bool   $modalAberto = false;
    public ?int   $salaId      = null; // null = criação · int = edição

    public string $nome        = '';
    public ?int   $capacidade  = null;
    public string $andar       = '';
    public array  $recursos    = [];
    public array  $fotos       = [null, null, null, null]; // novos uploads

    // Apenas no modo edição
    public array $imagensExistentes  = []; // [['id', 'url', 'path'], ...]
    public array $imagensParaRemover = []; // IDs a excluir ao salvar

    // ── Abrir modal — CRIAÇÃO ───────────────────────────────────

    #[On('abrir-modal-sala')]
    public function abrirModal(): void
    {
        $this->resetForm();
        $this->modalAberto = true;
    }

    // ── Abrir modal — EDIÇÃO ────────────────────────────────────

    #[On('abrir-modal-edicao')]
    public function abrirModalEdicao(int $salaId): void
    {
        $this->resetForm();

        $sala = room::with('images')->findOrFail($salaId);

        $this->salaId     = $salaId;
        $this->nome       = $sala->name;
        $this->capacidade = $sala->capacity;
        $this->andar      = $sala->location ?? '';
        $this->recursos   = array_values(array_filter([
            $sala->has_tv               ? 'tv'               : null,
            $sala->has_wifi             ? 'wifi'             : null,
            $sala->has_video_conference ? 'videoconferencia' : null,
            $sala->has_projector        ? 'projetor'         : null,
            $sala->has_coffee           ? 'cafe'             : null,
            $sala->has_whiteboard       ? 'quadro'           : null,
        ]));

        $this->imagensExistentes = $sala->images
            ->map(fn($img) => [
                'id'   => $img->id,
                'path' => $img->path,
            ])
            ->values()
            ->toArray();

        $this->modalAberto = true;
    }

    // ── Fechar ──────────────────────────────────────────────────

    public function fecharModal(): void
    {
        $this->modalAberto = false;
        $this->resetForm();
    }

    // ── Gerenciamento de imagens ────────────────────────────────

    /** Remove um novo upload (ainda não salvo) */
    public function removerFoto(int $index): void
    {
        $this->fotos[$index] = null;
    }

    /** Remove uma imagem já existente no banco (marcada para excluir ao salvar) */
    public function removerImagemExistente(int $id): void
    {
        $this->imagensParaRemover[] = $id;

        $this->imagensExistentes = array_values(
            array_filter($this->imagensExistentes, fn($img) => $img['id'] !== $id)
        );
    }

    // ── Validação ────────────────────────────────────────────────

    protected function rules(): array
    {
        return [
            'nome'       => 'required|string|min:2|max:100',
            'capacidade' => 'required|integer|min:1',
            'andar'      => 'required|string|max:50',
            'recursos'   => 'array',
            'fotos.*'    => 'nullable|image|max:20048',
        ];
    }

    protected array $messages = [
        'nome.required'       => 'O nome da sala é obrigatório.',
        'capacidade.required' => 'Informe a capacidade da sala.',
        'capacidade.min'      => 'A capacidade deve ser pelo menos 1.',
        'andar.required'      => 'Informe o andar da sala.',
        'fotos.*.image'       => 'O arquivo deve ser uma imagem.',
        'fotos.*.max'         => 'Cada foto pode ter no máximo 20MB.',
    ];

    // ── Salvar ──────────────────────────────────────────────────

    public function salvarSala(): void
    {
        $this->validate();

        $this->salaId ? $this->atualizarSala() : $this->criarSala();
    }

    private function criarSala(): void
    {
        $sala = room::create([
            'name'                 => $this->nome,
            'capacity'             => $this->capacidade,
            'location'             => $this->andar,
            'has_tv'               => in_array('tv', $this->recursos),
            'has_wifi'             => in_array('wifi', $this->recursos),
            'has_video_conference' => in_array('videoconferencia', $this->recursos),
            'has_projector'        => in_array('projetor', $this->recursos),
            'has_coffee'           => in_array('cafe', $this->recursos),
            'has_whiteboard'       => in_array('quadro', $this->recursos),
        ]);

        $this->salvarNovasFotos($sala->id, startOrder: 0);

        $this->fecharModal();
        $this->dispatch('room-created');
        $this->dispatch('openAlert',
            title: 'Sala de reunião cadastrada',
            description: 'O cadastro da sala de reunião foi realizado com sucesso.',
            type: 'success'
        );
    }

    private function atualizarSala(): void
    {
        $sala = room::findOrFail($this->salaId);

        $sala->update([
            'name'                 => $this->nome,
            'capacity'             => $this->capacidade,
            'location'             => $this->andar,
            'has_tv'               => in_array('tv', $this->recursos),
            'has_wifi'             => in_array('wifi', $this->recursos),
            'has_video_conference' => in_array('videoconferencia', $this->recursos),
            'has_projector'        => in_array('projetor', $this->recursos),
            'has_coffee'           => in_array('cafe', $this->recursos),
            'has_whiteboard'       => in_array('quadro', $this->recursos),
        ]);

        // Exclui imagens marcadas para remoção
        foreach ($this->imagensParaRemover as $imageId) {
            $img = room_images::find($imageId);
            if ($img) {
                Storage::disk($img->disk)->delete($img->path);
                $img->delete();
            }
        }

        // Novas fotos entram após as existentes (que não foram removidas)
        $startOrder = count($this->imagensExistentes);
        $this->salvarNovasFotos($sala->id, $startOrder);

        $this->fecharModal();
        $this->dispatch('room-updated', salaId: $sala->id);
        $this->dispatch('openAlert',
            title: 'Sala atualizada',
            description: 'As informações da sala foram atualizadas com sucesso.',
            type: 'success'
        );
    }

    private function salvarNovasFotos(int $salaId, int $startOrder): void
    {
        $ordem = $startOrder;
        foreach ($this->fotos as $foto) {
            if (! $foto) continue;

            $path = $foto->store('salas/fotos', 'public');

            room_images::create([
                'room_id'       => $salaId,
                'disk'          => 'public',
                'path'          => $path,
                'original_name' => $foto->getClientOriginalName(),
                'mime_type'     => $foto->getMimeType(),
                'size'          => $foto->getSize(),
                'order'         => $ordem++,
            ]);
        }
    }

    // ── Reset ────────────────────────────────────────────────────

    private function resetForm(): void
    {
        $this->salaId             = null;
        $this->nome               = '';
        $this->capacidade         = null;
        $this->andar              = '';
        $this->recursos           = [];
        $this->fotos              = [null, null, null, null];
        $this->imagensExistentes  = [];
        $this->imagensParaRemover = [];
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.components.ui.modal.room-create-modal');
    }
}
