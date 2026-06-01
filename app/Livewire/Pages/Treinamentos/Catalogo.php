<?php

namespace App\Livewire\Pages\Treinamentos;

use App\Livewire\SecureComponent;
use App\Models\Treinamento;
use App\Models\TreinamentoCertificado;
use App\Models\TreinamentoInscricao;
use App\Models\TreinamentoObrigatorio;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;
use Livewire\WithPagination;

class Catalogo extends SecureComponent
{
    use WithPagination;

    // ── Filtros ────────────────────────────────────────────────────────────
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(except: 'todos')]
    public string $tipoFiltro = 'todos';

    #[Url(except: 'todos')]
    public string $nivelFiltro = 'todos';

    #[Url(except: 'todos')]
    public string $categoriaFiltro = 'todos';

    // ── Aba ativa ──────────────────────────────────────────────────────────
    #[Url(except: 'catalogo')]
    public string $aba = 'catalogo';  // catalogo | meus_cursos | certificados

    // ─────────────────────────────────────────────────────────────────────
    public function mount(): void
    {
        $this->requireAuth();
    }

    public function updatingSearch(): void   { $this->resetPage(); }
    public function updatingTipoFiltro(): void { $this->resetPage(); }
    public function updatingNivelFiltro(): void { $this->resetPage(); }
    public function updatingCategoriaFiltro(): void { $this->resetPage(); }

    // ── Catálogo (cursos ativos) ───────────────────────────────────────────
    #[Computed]
    public function cursos()
    {
        return Treinamento::query()
            ->where('status', 'ativo')
            ->when($this->search, fn($q) =>
                $q->where(fn($q2) =>
                    $q2->where('titulo', 'like', "%{$this->search}%")
                       ->orWhere('categoria', 'like', "%{$this->search}%")
                       ->orWhere('instrutor', 'like', "%{$this->search}%")
                ))
            ->when($this->tipoFiltro !== 'todos', fn($q) => $q->where('tipo', $this->tipoFiltro))
            ->when($this->nivelFiltro !== 'todos', fn($q) => $q->where('nivel', $this->nivelFiltro))
            ->when($this->categoriaFiltro !== 'todos', fn($q) => $q->where('categoria', $this->categoriaFiltro))
            ->withCount('inscricoes')
            ->latest()
            ->paginate(12);
    }

    // ── Meus cursos (inscrições do usuário) ───────────────────────────────
    #[Computed]
    public function meusСursos()
    {
        return TreinamentoInscricao::with(['treinamento'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();
    }

    // ── Certificados do usuário ────────────────────────────────────────────
    #[Computed]
    public function meusCertificados()
    {
        return TreinamentoCertificado::with(['inscricao.treinamento'])
            ->whereHas('inscricao', fn($q) => $q->where('user_id', Auth::id()))
            ->latest('emitido_em')
            ->get();
    }

    // ── Categorias disponíveis ─────────────────────────────────────────────
    #[Computed]
    public function categorias()
    {
        return Treinamento::where('status', 'ativo')
            ->whereNotNull('categoria')
            ->distinct()
            ->pluck('categoria')
            ->sort()
            ->values();
    }

    // ── Stats da aba "Meus Cursos" ─────────────────────────────────────────
    #[Computed]
    public function statsAluno(): array
    {
        $inscricoes = TreinamentoInscricao::where('user_id', Auth::id())->get();
        return [
            'total'       => $inscricoes->count(),
            'concluidos'  => $inscricoes->where('status', 'concluido')->count(),
            'em_andamento'=> $inscricoes->where('status', 'em_andamento')->count(),
            'certificados'=> TreinamentoCertificado::whereHas('inscricao', fn($q) => $q->where('user_id', Auth::id()))->count(),
        ];
    }

    // ── IDs de cursos obrigatórios para o usuário ─────────────────────────
    #[Computed]
    public function cursosObrigatoriosIds(): array
    {
        $user = Auth::user();
        return TreinamentoObrigatorio::where(function ($q) use ($user) {
                $q->where('user_id', $user->id)
                  ->orWhere('department_id', $user->department_id);
            })
            ->pluck('treinamento_id')
            ->unique()
            ->toArray();
    }

    // ── Inscrever-se em um curso ───────────────────────────────────────────
    public function inscrever(int $treinamentoId): void
    {
        $this->requireAuth();

        $treinamento = Treinamento::findOrFail($treinamentoId);

        if (!$treinamento->inscricao_aberta) {
            $this->alertError('Inscrições fechadas', 'Este curso não está aceitando novas inscrições.');
            return;
        }

        $jaInscrito = TreinamentoInscricao::where([
            'treinamento_id' => $treinamentoId,
            'user_id'        => Auth::id(),
        ])->exists();

        if ($jaInscrito) {
            $this->alertInfo('Já inscrito', 'Você já está inscrito neste curso.');
            return;
        }

        TreinamentoInscricao::create([
            'treinamento_id' => $treinamentoId,
            'user_id'        => Auth::id(),
            'status'         => 'inscrito',
            'progresso'      => 0,
        ]);

        $this->alertSuccess('Inscrição realizada!', "Você foi inscrito em \"{$treinamento->titulo}\".");
        unset($this->meusСursos, $this->statsAluno);
    }

    // ── Cancelar inscrição ─────────────────────────────────────────────────
    public function cancelarInscricao(int $inscricaoId): void
    {
        $this->requireAuth();

        $inscricao = TreinamentoInscricao::where('id', $inscricaoId)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        if ($inscricao->status === 'concluido') {
            $this->alertError('Não permitido', 'Não é possível cancelar um curso já concluído.');
            return;
        }

        $inscricao->delete();
        $this->alertSuccess('Inscrição cancelada.');
        unset($this->meusСursos, $this->statsAluno);
    }

    public function acessarCurso(int $treinamentoId)
    {
        return $this->redirect(route('treinamentos.curso', $treinamentoId), navigate: true);
    }

    public function render()
    {
        return view('livewire.pages.treinamentos.catalogo')
            ->layout('components.layouts.app', ['title' => 'Treinamentos']);
    }
}
