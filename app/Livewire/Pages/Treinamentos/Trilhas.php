<?php

namespace App\Livewire\Pages\Treinamentos;

use App\Livewire\SecureComponent;
use App\Models\Treinamento;
use App\Models\Trilha;
use App\Models\TrilhaCurso;
use App\Models\TrilhaInscricao;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;

class Trilhas extends SecureComponent
{
    // ── Aba e seleção ──────────────────────────────────────────────────────
    #[Url(except: 'catalogo')]
    public string $aba = 'catalogo'; // catalogo | minhas_trilhas

    #[Locked]
    public ?int $trilhaSelecionadaId = null;

    #[Url(as: 'q', except: '')]
    public string $search = '';

    // ── Modal criar/editar trilha (só RH/Admin) ───────────────────────────
    public bool   $modalTrilha    = false;
    public string $modalTrilhaModo = 'criar';

    #[Locked]
    public ?int $trilhaEditandoId = null;

    #[Validate('required|string|min:3|max:200')]
    public string $trilhaTitulo = '';

    #[Validate('nullable|string|max:2000')]
    public ?string $trilhaDescricao = '';

    #[Validate('nullable|string|max:100')]
    public ?string $trilhaCategoria = '';

    #[Validate('required|in:rascunho,ativa,arquivada')]
    public string $trilhaStatus = 'ativa';

    // ── Modal adicionar curso à trilha ────────────────────────────────────
    public bool $modalAddCurso    = false;
    public ?int $addCursoId       = null;
    public int  $addCursoOrdem    = 0;
    public bool $addCursoObrig    = true;

    // ─────────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();
    }

    // ══════════════════════════════════════════════════════════════════════
    // COMPUTEDS
    // ══════════════════════════════════════════════════════════════════════

    #[Computed]
    public function trilhas()
    {
        return Trilha::query()
            ->where('status', 'ativa')
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('titulo', 'like', "%{$this->search}%")
                       ->orWhere('categoria', 'like', "%{$this->search}%")
                ))
            ->withCount('trilhaCursos')
            ->latest()
            ->get();
    }

    #[Computed]
    public function todasTrilhas()
    {
        // Para RH/Admin: inclui rascunhos e arquivados
        return Trilha::query()
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('titulo', 'like', "%{$this->search}%")
                       ->orWhere('categoria', 'like', "%{$this->search}%")
                ))
            ->withCount('trilhaCursos')
            ->with('inscricoes')
            ->latest()
            ->get();
    }

    #[Computed]
    public function minhasTrilhas()
    {
        return TrilhaInscricao::with(['trilha.trilhaCursos.treinamento'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    #[Computed]
    public function trilhaSelecionada(): ?Trilha
    {
        if (!$this->trilhaSelecionadaId) return null;
        return Trilha::with(['trilhaCursos.treinamento', 'inscricoes'])->find($this->trilhaSelecionadaId);
    }

    #[Computed]
    public function cursosDisponiveis()
    {
        // Cursos já na trilha
        $jaAdicionados = $this->trilhaSelecionada?->trilhaCursos->pluck('treinamento_id')->toArray() ?? [];
        return Treinamento::where('status', 'ativo')
            ->whereNotIn('id', $jaAdicionados)
            ->orderBy('titulo')
            ->get(['id', 'titulo', 'carga_horaria', 'nivel']);
    }

    #[Computed]
    public function inscricaoDoUsuario(): ?TrilhaInscricao
    {
        if (!$this->trilhaSelecionadaId) return null;
        return TrilhaInscricao::where('trilha_id', $this->trilhaSelecionadaId)
            ->where('user_id', Auth::id())
            ->first();
    }

    // ══════════════════════════════════════════════════════════════════════
    // NAVEGAÇÃO
    // ══════════════════════════════════════════════════════════════════════

    public function selecionarTrilha(int $id): void
    {
        $this->trilhaSelecionadaId = $id;
        unset($this->trilhaSelecionada, $this->inscricaoDoUsuario, $this->cursosDisponiveis);
    }

    public function voltarLista(): void
    {
        $this->trilhaSelecionadaId = null;
        unset($this->trilhaSelecionada, $this->cursosDisponiveis);
    }

    // ══════════════════════════════════════════════════════════════════════
    // INSCRIÇÃO DO COLABORADOR
    // ══════════════════════════════════════════════════════════════════════

    public function inscreverTrilha(int $trilhaId): void
    {
        $this->requireAuth();

        $existe = TrilhaInscricao::where('trilha_id', $trilhaId)
            ->where('user_id', Auth::id())->exists();

        if ($existe) {
            $this->alertInfo('Você já está inscrito nesta trilha.');
            return;
        }

        TrilhaInscricao::create([
            'trilha_id' => $trilhaId,
            'user_id'   => Auth::id(),
            'status'    => 'em_andamento',
            'progresso' => 0,
        ]);

        $this->alertSuccess('Inscrito na trilha!', 'Acesse os cursos para começar.');
        unset($this->trilhas, $this->minhasTrilhas);
    }

    // ══════════════════════════════════════════════════════════════════════
    // CRUD TRILHA (RH/Admin)
    // ══════════════════════════════════════════════════════════════════════

    public function abrirModalTrilha(string $modo = 'criar', ?int $id = null): void
    {
        $this->requireRhOrAdmin();
        $this->resetTrilhaForm();
        $this->modalTrilhaModo  = $modo;
        $this->trilhaEditandoId = $id;

        if ($modo === 'editar' && $id) {
            $t = Trilha::findOrFail($id);
            $this->trilhaTitulo    = $t->titulo;
            $this->trilhaDescricao = $t->descricao;
            $this->trilhaCategoria = $t->categoria;
            $this->trilhaStatus    = $t->status;
        }

        $this->modalTrilha = true;
    }

    public function salvarTrilha(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'trilhaTitulo'  => 'required|string|min:3|max:200',
            'trilhaStatus'  => 'required|in:rascunho,ativa,arquivada',
        ]);

        $dados = [
            'titulo'     => $this->sanitize($this->trilhaTitulo),
            'descricao'  => $this->sanitize($this->trilhaDescricao ?? ''),
            'categoria'  => $this->sanitize($this->trilhaCategoria ?? ''),
            'status'     => $this->trilhaStatus,
            'criado_por' => Auth::id(),
        ];

        if ($this->modalTrilhaModo === 'editar' && $this->trilhaEditandoId) {
            unset($dados['criado_por']);
            Trilha::findOrFail($this->trilhaEditandoId)->update($dados);
            $this->alertSuccess('Trilha atualizada!');
        } else {
            $nova = Trilha::create($dados);
            $this->trilhaSelecionadaId = $nova->id;
            $this->alertSuccess('Trilha criada!', 'Adicione cursos à trilha.');
        }

        $this->modalTrilha = false;
        $this->resetTrilhaForm();
        unset($this->trilhas, $this->todasTrilhas, $this->trilhaSelecionada);
    }

    private function resetTrilhaForm(): void
    {
        $this->trilhaTitulo    = $this->trilhaDescricao = $this->trilhaCategoria = '';
        $this->trilhaStatus    = 'ativa';
        $this->trilhaEditandoId = null;
    }

    public function excluirTrilha(int $id): void
    {
        $this->requireRhOrAdmin();
        Trilha::findOrFail($id)->delete();
        if ($this->trilhaSelecionadaId === $id) $this->voltarLista();
        unset($this->trilhas, $this->todasTrilhas);
        $this->alertSuccess('Trilha excluída.');
    }

    // ══════════════════════════════════════════════════════════════════════
    // GERENCIAR CURSOS DA TRILHA (RH/Admin)
    // ══════════════════════════════════════════════════════════════════════

    public function abrirModalAddCurso(): void
    {
        $this->requireRhOrAdmin();
        $this->addCursoId    = null;
        $this->addCursoObrig = true;
        $this->addCursoOrdem = ($this->trilhaSelecionada?->trilhaCursos->max('ordem') ?? -1) + 1;
        $this->modalAddCurso = true;
        unset($this->cursosDisponiveis);
    }

    public function salvarCursoNaTrilha(): void
    {
        $this->requireRhOrAdmin();
        if (!$this->addCursoId) {
            $this->alertError('Selecione um curso.');
            return;
        }

        TrilhaCurso::create([
            'trilha_id'      => $this->trilhaSelecionadaId,
            'treinamento_id' => $this->addCursoId,
            'ordem'          => $this->addCursoOrdem,
            'obrigatorio'    => $this->addCursoObrig,
        ]);

        $this->modalAddCurso = false;
        unset($this->trilhaSelecionada, $this->cursosDisponiveis);
        $this->alertSuccess('Curso adicionado à trilha!');
    }

    public function removerCursoDaTrilha(int $trilhaCursoId): void
    {
        $this->requireRhOrAdmin();
        TrilhaCurso::findOrFail($trilhaCursoId)->delete();
        unset($this->trilhaSelecionada, $this->cursosDisponiveis);
        $this->alertSuccess('Curso removido da trilha.');
    }

    // ─────────────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.treinamentos.trilhas')
            ->layout('components.layouts.app', ['title' => 'Trilhas de Aprendizado']);
    }
}
