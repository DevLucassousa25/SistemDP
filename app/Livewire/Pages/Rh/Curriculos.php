<?php

namespace App\Livewire\Pages\Rh;

use App\Livewire\SecureComponent;
use App\Models\RhCurriculo;
use App\Models\RhCurriculoExperiencia;
use App\Models\RhCurriculoFormacao;
use App\Models\RhCurriculoHabilidade;
use App\Models\RhCurriculoTag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Curriculos extends SecureComponent
{
    use WithFileUploads, WithPagination;

    // ── Filtros ───────────────────────────────────────────────────────
    public string $search        = '';
    public string $filterStatus  = '';
    public string $filterEstado  = '';
    public string $filterEscolaridade = '';
    public string $filterArea    = '';
    public string $filterSalMin  = '';
    public string $filterSalMax  = '';

    // ── Upload ────────────────────────────────────────────────────────
    public bool   $uploadModal   = false;
    public $arquivo              = null;

    // ── Drawer candidato ──────────────────────────────────────────────
    public bool   $drawerOpen    = false;
    #[Locked]
    public ?int   $drawerCurrId  = null;

    // ── Edição ────────────────────────────────────────────────────────
    public bool   $editModal     = false;
    #[Locked]
    public ?int   $editId        = null;
    public string $editNome         = '';
    public string $editEmail        = '';
    public string $editTelefone     = '';
    public string $editCidade       = '';
    public string $editEstado       = '';
    public string $editArea         = '';
    public string $editEscolaridade = '';
    public string $editResumo       = '';
    public string $editPretensao    = '';
    public string $editNotas        = '';
    public string $editStatus       = 'ativo';

    // ── Exclusão ──────────────────────────────────────────────────────
    public bool   $deleteModal   = false;
    #[Locked]
    public ?int   $deleteId      = null;

    // ── Tag ───────────────────────────────────────────────────────────
    public string $newTag        = '';
    #[Locked]
    public ?int   $tagCurrId     = null;

    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();
    }

    public function updatedSearch(): void   { $this->resetPage(); }
    public function updatedFilterStatus(): void  { $this->resetPage(); }
    public function updatedFilterEstado(): void  { $this->resetPage(); }
    public function updatedFilterEscolaridade(): void { $this->resetPage(); }

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function curriculos()
    {
        return RhCurriculo::with(['tags', 'experiencias', 'formacoes', 'habilidades'])
            ->when($this->search, fn($q) =>
                $q->where(fn($s) =>
                    $s->where('nome', 'ilike', "%{$this->search}%")
                      ->orWhere('email', 'ilike', "%{$this->search}%")
                      ->orWhere('area_interesse', 'ilike', "%{$this->search}%")
                      ->orWhere('cidade', 'ilike', "%{$this->search}%")
                )
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterEstado, fn($q) => $q->where('estado', $this->filterEstado))
            ->when($this->filterEscolaridade, fn($q) => $q->where('escolaridade', $this->filterEscolaridade))
            ->when($this->filterArea, fn($q) => $q->where('area_interesse', 'ilike', "%{$this->filterArea}%"))
            ->when($this->filterSalMin, fn($q) => $q->where('pretensao_salarial', '>=', $this->filterSalMin))
            ->when($this->filterSalMax, fn($q) => $q->where('pretensao_salarial', '<=', $this->filterSalMax))
            ->latest()
            ->paginate(20);
    }

    #[Computed]
    public function drawerCurriculo(): ?RhCurriculo
    {
        if (!$this->drawerCurrId) return null;
        return RhCurriculo::with([
            'experiencias', 'formacoes', 'habilidades', 'tags',
            'candidaturas.vaga', 'candidaturas.etapa', 'testes.teste'
        ])->find($this->drawerCurrId);
    }

    // ── Upload & Parse ────────────────────────────────────────────────

    public function uploadCurriculo(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'arquivo' => 'required|file|mimes:pdf,doc,docx|max:5120',
        ]);

        if (!$this->rateLimit('upload-curriculo', 20, 60)) return;

        $original = $this->arquivo->getClientOriginalName();
        $mime     = $this->arquivo->getMimeType();
        $path     = $this->arquivo->store('rh/curriculos', 'public');

        // Parser básico de texto do PDF
        $parsedData = $this->parseCurriculo($path, $mime);

        RhCurriculo::create(array_merge($parsedData, [
            'arquivo_path'     => $path,
            'arquivo_original' => $original,
            'arquivo_mime'     => $mime,
            'status'           => 'ativo',
        ]));

        $this->arquivo    = null;
        $this->uploadModal = false;
        unset($this->curriculos);
        $this->alertSuccess('Currículo enviado!', 'Dados extraídos automaticamente.');
    }

    private function parseCurriculo(string $path, string $mime): array
    {
        $text = '';
        $fullPath = Storage::disk('public')->path($path);

        // Tenta extrair texto do PDF usando pdftotext (se disponível)
        if (str_contains($mime, 'pdf')) {
            $out = shell_exec("pdftotext " . escapeshellarg($fullPath) . " - 2>/dev/null");
            $text = $out ?? '';
        }

        // Regex parsers básicos
        $data = ['nome' => pathinfo($path, PATHINFO_FILENAME)];

        if (preg_match('/[\w.+-]+@[\w-]+\.\w{2,}/i', $text, $m)) {
            $data['email'] = $m[0];
        }
        if (preg_match('/(?:\+55\s?)?(?:\(?\d{2}\)?\s?)?(?:9\s?)?\d{4}[-\s]?\d{4}/', $text, $m)) {
            $data['telefone'] = preg_replace('/\D/', '', $m[0]);
        }
        if (preg_match('/\d{3}\.?\d{3}\.?\d{3}-?\d{2}/', $text, $m)) {
            $data['cpf'] = $m[0];
        }
        // Salário pretendido
        if (preg_match('/pretens[ãa]o[:\s]+R?\$?\s*([\d.,]+)/i', $text, $m)) {
            $data['pretensao_salarial'] = (float) str_replace(['.', ','], ['', '.'], $m[1]);
        }

        return $data;
    }

    // ── Drawer ────────────────────────────────────────────────────────

    public function openDrawer(int $id): void
    {
        $this->requireRhOrAdmin();
        $this->drawerCurrId = $id;
        $this->drawerOpen   = true;
        unset($this->drawerCurriculo);
    }

    public function closeDrawer(): void
    {
        $this->drawerOpen   = false;
        $this->drawerCurrId = null;
    }

    // ── Edição ────────────────────────────────────────────────────────

    public function openEdit(int $id): void
    {
        $this->requireRhOrAdmin();
        $c = RhCurriculo::findOrFail($id);
        $this->editId          = $id;
        $this->editNome         = $c->nome;
        $this->editEmail        = $c->email ?? '';
        $this->editTelefone     = $c->telefone ?? '';
        $this->editCidade       = $c->cidade ?? '';
        $this->editEstado       = $c->estado ?? '';
        $this->editArea         = $c->area_interesse ?? '';
        $this->editEscolaridade = $c->escolaridade ?? '';
        $this->editResumo       = $c->resumo_profissional ?? '';
        $this->editPretensao    = $c->pretensao_salarial ?? '';
        $this->editNotas        = $c->notas_internas ?? '';
        $this->editStatus       = $c->status;
        $this->editModal        = true;
    }

    public function saveEdit(): void
    {
        $this->requireRhOrAdmin();
        $c = RhCurriculo::findOrFail($this->editId);
        $this->validate([
            'editNome'  => 'required|string|max:200',
            'editEmail' => 'nullable|email|max:200',
            'editStatus'=> 'required|in:ativo,inativo,banco_talentos,favorito',
        ]);
        $c->update([
            'nome'                => $this->sanitize($this->editNome),
            'email'               => $this->sanitize($this->editEmail),
            'telefone'            => $this->sanitize($this->editTelefone),
            'cidade'              => $this->sanitize($this->editCidade),
            'estado'              => $this->sanitize($this->editEstado),
            'area_interesse'      => $this->sanitize($this->editArea),
            'escolaridade'        => $this->editEscolaridade ?: null,
            'resumo_profissional' => $this->sanitize($this->editResumo),
            'pretensao_salarial'  => $this->editPretensao ? (float) $this->editPretensao : null,
            'notas_internas'      => $this->sanitize($this->editNotas),
            'status'              => $this->editStatus,
        ]);
        $this->editModal = false;
        unset($this->curriculos, $this->drawerCurriculo);
        $this->alertSuccess('Currículo atualizado!');
    }

    // ── Tag ───────────────────────────────────────────────────────────

    public function addTag(int $currId): void
    {
        $this->requireRhOrAdmin();
        $tag = trim($this->newTag);
        if (!$tag) return;
        $this->tagCurrId = $currId;
        RhCurriculoTag::create(['curriculo_id' => $currId, 'tag' => $this->sanitize($tag)]);
        $this->newTag = '';
        unset($this->drawerCurriculo);
    }

    public function removeTag(int $tagId): void
    {
        $this->requireRhOrAdmin();
        RhCurriculoTag::findOrFail($tagId)->delete();
        unset($this->drawerCurriculo);
    }

    // ── Status rápido ─────────────────────────────────────────────────

    public function toggleFavorito(int $id): void
    {
        $this->requireRhOrAdmin();
        $c = RhCurriculo::findOrFail($id);
        $c->update(['status' => $c->status === 'favorito' ? 'ativo' : 'favorito']);
        unset($this->curriculos, $this->drawerCurriculo);
    }

    public function moverBancoTalentos(int $id): void
    {
        $this->requireRhOrAdmin();
        RhCurriculo::findOrFail($id)->update(['status' => 'banco_talentos']);
        unset($this->curriculos, $this->drawerCurriculo);
        $this->alertSuccess('Movido para banco de talentos!');
    }

    // ── Exclusão ──────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->deleteId    = $id;
        $this->deleteModal = true;
    }

    public function deleteCurriculo(): void
    {
        $this->requireRhOrAdmin();
        $c = RhCurriculo::findOrFail($this->deleteId);
        if ($c->arquivo_path) Storage::disk('public')->delete($c->arquivo_path);
        $c->delete();
        $this->deleteModal = false;
        $this->deleteId    = null;
        $this->drawerOpen  = false;
        unset($this->curriculos);
        $this->alertSuccess('Currículo excluído.');
    }

    public function render()
    {
        return view('livewire.pages.rh.curriculos')
            ->layout('components.layouts.app', ['title' => 'Currículos']);
    }
}
