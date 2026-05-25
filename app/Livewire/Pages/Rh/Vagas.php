<?php

namespace App\Livewire\Pages\Rh;

use App\Livewire\SecureComponent;
use App\Models\RhVaga;
use App\Models\RhVagaEtapa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithPagination;

class Vagas extends SecureComponent
{
    use WithPagination;

    public string $search       = '';
    public string $filterStatus = '';
    public string $filterModal  = '';
    public string $activeTab    = 'lista';   // lista | pipeline

    // ── Modal criar/editar ────────────────────────────────────────────
    public bool   $vagaModal    = false;
    #[Locked]
    public ?int   $vagaId       = null;

    // Campos da vaga
    public string $titulo        = '';
    public string $cargo         = '';
    public string $descricao     = '';
    public string $requisitos    = '';
    public string $competencias  = '';
    public string $beneficios    = '';
    public string $salarioMin    = '';
    public string $salarioMax    = '';
    public string $modalidade    = 'presencial';
    public string $cidade        = '';
    public string $estado        = '';
    public string $status        = 'rascunho';
    public int    $notaMinima    = 60;
    public string $slaDias       = '';
    public int    $vagasDisp     = 1;
    public string $dataEnc       = '';

    // Etapas do processo
    public array  $etapas        = [];

    // ── Drawer vaga ───────────────────────────────────────────────────
    public bool   $drawerOpen    = false;
    #[Locked]
    public ?int   $drawerVagaId  = null;

    // ── Confirmação ───────────────────────────────────────────────────
    public bool   $deleteModal   = false;
    #[Locked]
    public ?int   $deleteId      = null;

    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();
        $this->resetEtapasPadrao();
    }

    public function updatedSearch(): void { $this->resetPage(); }

    // ── Computeds ─────────────────────────────────────────────────────

    #[Computed]
    public function vagas()
    {
        return RhVaga::withCount('candidaturas')
            ->when($this->search, fn($q) =>
                $q->where('titulo', 'ilike', "%{$this->search}%")
                  ->orWhere('cargo', 'ilike', "%{$this->search}%")
            )
            ->when($this->filterStatus, fn($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterModal, fn($q) => $q->where('modalidade', $this->filterModal))
            ->latest()
            ->paginate(15);
    }

    #[Computed]
    public function drawerVaga(): ?RhVaga
    {
        if (!$this->drawerVagaId) return null;
        return RhVaga::with(['etapas', 'creator', 'candidaturas.curriculo', 'candidaturas.etapa'])
            ->withCount('candidaturas')
            ->find($this->drawerVagaId);
    }

    // ── Criar / Editar ────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetVagaForm();
        $this->vagaId    = null;
        $this->vagaModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->requireRhOrAdmin();
        $v = RhVaga::with('etapas')->findOrFail($id);
        $this->vagaId       = $id;
        $this->titulo       = $v->titulo;
        $this->cargo        = $v->cargo;
        $this->descricao    = $v->descricao ?? '';
        $this->requisitos   = $v->requisitos ?? '';
        $this->competencias = $v->competencias ?? '';
        $this->beneficios   = $v->beneficios ?? '';
        $this->salarioMin   = $v->salario_min ?? '';
        $this->salarioMax   = $v->salario_max ?? '';
        $this->modalidade   = $v->modalidade;
        $this->cidade       = $v->cidade ?? '';
        $this->estado       = $v->estado ?? '';
        $this->status       = $v->status;
        $this->notaMinima   = $v->nota_minima_aprovacao;
        $this->slaDias      = $v->sla_dias ?? '';
        $this->vagasDisp    = $v->vagas_disponiveis;
        $this->dataEnc      = $v->data_encerramento?->format('Y-m-d') ?? '';
        $this->etapas       = $v->etapas->map(fn($e) => [
            'id'    => $e->id,
            'nome'  => $e->nome,
            'cor'   => $e->cor,
            'is_aprovado'       => $e->is_aprovado,
            'is_reprovado'      => $e->is_reprovado,
            'is_banco_talentos' => $e->is_banco_talentos,
        ])->toArray();
        $this->vagaModal = true;
    }

    public function saveVaga(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'titulo'    => 'required|string|max:200',
            'cargo'     => 'required|string|max:200',
            'modalidade'=> 'required|in:presencial,remoto,hibrido',
            'status'    => 'required|in:rascunho,publicada,pausada,encerrada',
            'notaMinima'=> 'integer|min:0|max:100',
            'vagasDisp' => 'integer|min:1',
        ]);

        $data = [
            'created_by'             => Auth::id(),
            'titulo'                 => $this->sanitize($this->titulo),
            'cargo'                  => $this->sanitize($this->cargo),
            'descricao'              => $this->sanitize($this->descricao),
            'requisitos'             => $this->sanitize($this->requisitos),
            'competencias'           => $this->sanitize($this->competencias),
            'beneficios'             => $this->sanitize($this->beneficios),
            'salario_min'            => $this->salarioMin ? (float) $this->salarioMin : null,
            'salario_max'            => $this->salarioMax ? (float) $this->salarioMax : null,
            'modalidade'             => $this->modalidade,
            'cidade'                 => $this->sanitize($this->cidade),
            'estado'                 => $this->sanitize($this->estado),
            'status'                 => $this->status,
            'nota_minima_aprovacao'  => $this->notaMinima,
            'sla_dias'               => $this->slaDias ?: null,
            'vagas_disponiveis'      => $this->vagasDisp,
            'data_encerramento'      => $this->dataEnc ?: null,
        ];

        if ($this->vagaId) {
            $vaga = RhVaga::findOrFail($this->vagaId);
            $vaga->update($data);
            // Sincronizar etapas
            $vaga->etapas()->delete();
        } else {
            $vaga = RhVaga::create($data);
        }

        foreach ($this->etapas as $ordem => $etapa) {
            $vaga->etapas()->create([
                'nome'              => $this->sanitize($etapa['nome']),
                'cor'               => $etapa['cor'],
                'ordem'             => $ordem,
                'is_aprovado'       => $etapa['is_aprovado'] ?? false,
                'is_reprovado'      => $etapa['is_reprovado'] ?? false,
                'is_banco_talentos' => $etapa['is_banco_talentos'] ?? false,
            ]);
        }

        $this->vagaModal = false;
        unset($this->vagas, $this->drawerVaga);
        $this->alertSuccess($this->vagaId ? 'Vaga atualizada!' : 'Vaga criada!');
    }

    public function duplicarVaga(int $id): void
    {
        $this->requireRhOrAdmin();
        $orig  = RhVaga::with('etapas')->findOrFail($id);
        $nova  = $orig->replicate(['created_at','updated_at']);
        $nova->titulo  = $orig->titulo . ' (cópia)';
        $nova->status  = 'rascunho';
        $nova->created_by = Auth::id();
        $nova->save();
        foreach ($orig->etapas as $e) {
            $nova->etapas()->create($e->only(['nome','cor','ordem','is_aprovado','is_reprovado','is_banco_talentos']));
        }
        unset($this->vagas);
        $this->alertSuccess('Vaga duplicada!');
    }

    public function encerrarVaga(int $id): void
    {
        $this->requireRhOrAdmin();
        RhVaga::findOrFail($id)->update(['status' => 'encerrada']);
        unset($this->vagas, $this->drawerVaga);
        $this->alertSuccess('Vaga encerrada.');
    }

    public function publicarVaga(int $id): void
    {
        $this->requireRhOrAdmin();
        RhVaga::findOrFail($id)->update(['status' => 'publicada']);
        unset($this->vagas, $this->drawerVaga);
        $this->alertSuccess('Vaga publicada!');
    }

    // ── Etapas helpers ────────────────────────────────────────────────

    private function resetEtapasPadrao(): void
    {
        $this->etapas = [
            ['nome' => 'Recebido',        'cor' => 'bg-slate-400',  'is_aprovado' => false, 'is_reprovado' => false, 'is_banco_talentos' => false],
            ['nome' => 'Triagem',          'cor' => 'bg-blue-400',   'is_aprovado' => false, 'is_reprovado' => false, 'is_banco_talentos' => false],
            ['nome' => 'Em análise',       'cor' => 'bg-indigo-400', 'is_aprovado' => false, 'is_reprovado' => false, 'is_banco_talentos' => false],
            ['nome' => 'Teste',            'cor' => 'bg-violet-400', 'is_aprovado' => false, 'is_reprovado' => false, 'is_banco_talentos' => false],
            ['nome' => 'Entrevista',       'cor' => 'bg-purple-400', 'is_aprovado' => false, 'is_reprovado' => false, 'is_banco_talentos' => false],
            ['nome' => 'Aprovado',         'cor' => 'bg-green-400',  'is_aprovado' => true,  'is_reprovado' => false, 'is_banco_talentos' => false],
            ['nome' => 'Reprovado',        'cor' => 'bg-red-400',    'is_aprovado' => false, 'is_reprovado' => true,  'is_banco_talentos' => false],
            ['nome' => 'Banco de talentos','cor' => 'bg-amber-400',  'is_aprovado' => false, 'is_reprovado' => false, 'is_banco_talentos' => true],
        ];
    }

    private function resetVagaForm(): void
    {
        $this->titulo = $this->cargo = $this->descricao = $this->requisitos =
        $this->competencias = $this->beneficios = $this->salarioMin = $this->salarioMax =
        $this->cidade = $this->estado = $this->slaDias = $this->dataEnc = '';
        $this->modalidade  = 'presencial';
        $this->status      = 'rascunho';
        $this->notaMinima  = 60;
        $this->vagasDisp   = 1;
        $this->resetEtapasPadrao();
    }

    public function addEtapa(): void
    {
        $this->etapas[] = ['nome' => 'Nova etapa', 'cor' => 'bg-slate-400', 'is_aprovado' => false, 'is_reprovado' => false, 'is_banco_talentos' => false];
    }

    public function removeEtapa(int $idx): void
    {
        unset($this->etapas[$idx]);
        $this->etapas = array_values($this->etapas);
    }

    // ── Drawer ────────────────────────────────────────────────────────

    public function openDrawer(int $id): void
    {
        $this->drawerVagaId = $id;
        $this->drawerOpen   = true;
        unset($this->drawerVaga);
    }

    // ── Excluir ───────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->deleteId    = $id;
        $this->deleteModal = true;
    }

    public function deleteVaga(): void
    {
        $this->requireRhOrAdmin();
        RhVaga::findOrFail($this->deleteId)->delete();
        $this->deleteModal = false;
        unset($this->vagas);
        $this->alertSuccess('Vaga excluída.');
    }

    public function render()
    {
        return view('livewire.pages.rh.vagas')
            ->layout('components.layouts.app', ['title' => 'Vagas']);
    }
}
