<?php

namespace App\Livewire\Pages\Rh;

use App\Livewire\SecureComponent;
use App\Models\RhTeste;
use App\Models\RhQuestao;
use App\Models\RhOpcao;
use App\Models\RhCandidatoTeste;
use App\Models\RhCurriculo;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithPagination;

class Testes extends SecureComponent
{
    use WithPagination;

    public string $activeTab  = 'testes';  // testes | resultados

    // ── Filtros ───────────────────────────────────────────────────────
    public string $search     = '';

    // ── Modal criar/editar teste ──────────────────────────────────────
    public bool   $testeModal = false;
    #[Locked]
    public ?int   $testeId    = null;
    public string $testeTitulo      = '';
    public string $testeDescricao   = '';
    public string $testeInstrucoes  = '';
    public int    $testeTempo       = 60;
    public bool   $testeRandomQ     = false;
    public bool   $testeRandomO     = false;
    public int    $testeNota        = 60;
    public bool   $testeAtivo       = true;
    public array  $questoes         = [];

    // ── Drawer teste ──────────────────────────────────────────────────
    public bool   $drawerOpen   = false;
    #[Locked]
    public ?int   $drawerTesteId= null;

    // ── Enviar teste ──────────────────────────────────────────────────
    public bool   $enviarModal    = false;
    public string $enviarSearch   = '';
    #[Locked]
    public ?int   $enviarTesteId  = null;

    // ── Excluir ───────────────────────────────────────────────────────
    public bool   $deleteModal  = false;
    #[Locked]
    public ?int   $deleteId     = null;

    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();
    }

    public function updatedSearch(): void { $this->resetPage(); }

    // ── Computeds ─────────────────────────────────────────────────────

    #[Computed]
    public function testes()
    {
        return RhTeste::withCount('questoes')
            ->withCount('tentativas')
            ->when($this->search, fn($q) => $q->where('titulo', 'ilike', "%{$this->search}%"))
            ->latest()
            ->paginate(12);
    }

    #[Computed]
    public function drawerTeste(): ?RhTeste
    {
        if (!$this->drawerTesteId) return null;
        return RhTeste::with(['questoes.opcoes', 'creator'])->find($this->drawerTesteId);
    }

    #[Computed]
    public function resultados()
    {
        return RhCandidatoTeste::with(['curriculo', 'teste', 'vaga'])
            ->where('status', 'concluido')
            ->orderByDesc('concluido_at')
            ->paginate(15);
    }

    #[Computed]
    public function curriculosBusca()
    {
        if (!$this->enviarSearch || strlen($this->enviarSearch) < 2) return collect();
        return RhCurriculo::where('nome', 'ilike', "%{$this->enviarSearch}%")
            ->orWhere('email', 'ilike', "%{$this->enviarSearch}%")
            ->limit(10)->get();
    }

    // ── Criar / Editar ────────────────────────────────────────────────

    public function openCreate(): void
    {
        $this->resetTesteForm();
        $this->testeId    = null;
        $this->testeModal = true;
    }

    public function openEdit(int $id): void
    {
        $this->requireRhOrAdmin();
        $t = RhTeste::with('questoes.opcoes')->findOrFail($id);
        $this->testeId         = $id;
        $this->testeTitulo     = $t->titulo;
        $this->testeDescricao  = $t->descricao ?? '';
        $this->testeInstrucoes = $t->instrucoes ?? '';
        $this->testeTempo      = $t->tempo_limite_minutos;
        $this->testeRandomQ    = $t->randomizar_questoes;
        $this->testeRandomO    = $t->randomizar_opcoes;
        $this->testeNota       = $t->nota_aprovacao;
        $this->testeAtivo      = $t->ativo;
        $this->questoes        = $t->questoes->map(fn($q) => [
            'id'       => $q->id,
            'enunciado'=> $q->enunciado,
            'tipo'     => $q->tipo,
            'peso'     => $q->peso,
            'opcoes'   => $q->opcoes->map(fn($o) => [
                'id'     => $o->id,
                'texto'  => $o->texto,
                'correta'=> $o->correta,
            ])->toArray(),
        ])->toArray();
        $this->testeModal = true;
    }

    public function saveTeste(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'testeTitulo' => 'required|string|max:200',
            'testeTempo'  => 'required|integer|min:1|max:480',
            'testeNota'   => 'required|integer|min:0|max:100',
        ]);

        $data = [
            'created_by'           => Auth::id(),
            'titulo'               => $this->sanitize($this->testeTitulo),
            'descricao'            => $this->sanitize($this->testeDescricao),
            'instrucoes'           => $this->sanitize($this->testeInstrucoes),
            'tempo_limite_minutos' => $this->testeTempo,
            'randomizar_questoes'  => $this->testeRandomQ,
            'randomizar_opcoes'    => $this->testeRandomO,
            'nota_aprovacao'       => $this->testeNota,
            'ativo'                => $this->testeAtivo,
        ];

        if ($this->testeId) {
            $teste = RhTeste::findOrFail($this->testeId);
            $teste->update($data);
            $teste->questoes()->each(fn($q) => $q->opcoes()->delete());
            $teste->questoes()->delete();
        } else {
            $teste = RhTeste::create($data);
        }

        foreach ($this->questoes as $ordem => $qData) {
            $questao = $teste->questoes()->create([
                'enunciado' => $this->sanitize($qData['enunciado']),
                'tipo'      => $qData['tipo'],
                'peso'      => $qData['peso'] ?? 1,
                'ordem'     => $ordem,
            ]);
            if ($qData['tipo'] === 'objetiva') {
                foreach ($qData['opcoes'] ?? [] as $oIdx => $oData) {
                    $questao->opcoes()->create([
                        'texto'  => $this->sanitize($oData['texto']),
                        'correta'=> (bool) ($oData['correta'] ?? false),
                        'ordem'  => $oIdx,
                    ]);
                }
            }
        }

        $this->testeModal = false;
        unset($this->testes, $this->drawerTeste);
        $this->alertSuccess($this->testeId ? 'Teste atualizado!' : 'Teste criado!');
    }

    // ── Questões helpers ──────────────────────────────────────────────

    public function addQuestao(string $tipo = 'objetiva'): void
    {
        $this->questoes[] = [
            'enunciado' => '',
            'tipo'      => $tipo,
            'peso'      => 1,
            'opcoes'    => $tipo === 'objetiva' ? [
                ['texto' => '', 'correta' => false],
                ['texto' => '', 'correta' => false],
                ['texto' => '', 'correta' => false],
                ['texto' => '', 'correta' => false],
            ] : [],
        ];
    }

    public function removeQuestao(int $idx): void
    {
        unset($this->questoes[$idx]);
        $this->questoes = array_values($this->questoes);
    }

    public function addOpcao(int $qIdx): void
    {
        $this->questoes[$qIdx]['opcoes'][] = ['texto' => '', 'correta' => false];
    }

    public function removeOpcao(int $qIdx, int $oIdx): void
    {
        unset($this->questoes[$qIdx]['opcoes'][$oIdx]);
        $this->questoes[$qIdx]['opcoes'] = array_values($this->questoes[$qIdx]['opcoes']);
    }

    public function setCorreta(int $qIdx, int $oIdx): void
    {
        foreach ($this->questoes[$qIdx]['opcoes'] as $i => $_) {
            $this->questoes[$qIdx]['opcoes'][$i]['correta'] = ($i === $oIdx);
        }
    }

    private function resetTesteForm(): void
    {
        $this->testeTitulo = $this->testeDescricao = $this->testeInstrucoes = '';
        $this->testeTempo  = 60;
        $this->testeNota   = 60;
        $this->testeRandomQ = $this->testeRandomO = false;
        $this->testeAtivo  = true;
        $this->questoes    = [];
    }

    // ── Enviar teste a candidato ───────────────────────────────────────

    public function openEnviar(int $testeId): void
    {
        $this->enviarTesteId = $testeId;
        $this->enviarSearch  = '';
        $this->enviarModal   = true;
    }

    public function enviarTesteCandidato(int $curriculoId): void
    {
        $this->requireRhOrAdmin();
        $token = RhCandidatoTeste::create([
            'curriculo_id' => $curriculoId,
            'teste_id'     => $this->enviarTesteId,
            'status'       => 'pendente',
            'expira_at'    => now()->addDays(7),
        ]);
        $this->enviarModal = false;
        $this->alertSuccess('Teste enviado!', 'Link: /candidato/teste/' . $token->token);
    }

    // ── Correção automática ───────────────────────────────────────────

    public function corrigirTeste(int $tentativaId): void
    {
        $this->requireRhOrAdmin();
        $tentativa = RhCandidatoTeste::with(['respostas.questao', 'respostas.opcao', 'teste.questoes'])->findOrFail($tentativaId);

        $totalPeso = $tentativa->teste->questoes->sum('peso');
        $acertos   = 0;

        foreach ($tentativa->respostas as $resp) {
            if ($resp->questao->tipo === 'objetiva' && $resp->opcao?->correta) {
                $resp->update(['correta' => true, 'nota_obtida' => $resp->questao->peso]);
                $acertos += $resp->questao->peso;
            } elseif ($resp->questao->tipo === 'objetiva') {
                $resp->update(['correta' => false, 'nota_obtida' => 0]);
            }
        }

        $nota     = $totalPeso > 0 ? round($acertos / $totalPeso * 100, 2) : 0;
        $aprovado = $nota >= $tentativa->teste->nota_aprovacao;
        $tentativa->update(['nota' => $nota, 'aprovado' => $aprovado]);

        unset($this->resultados);
        $this->alertSuccess('Teste corrigido!', "Nota: {$nota} — " . ($aprovado ? 'Aprovado' : 'Reprovado'));
    }

    // ── Drawer ────────────────────────────────────────────────────────

    public function openDrawer(int $id): void
    {
        $this->drawerTesteId = $id;
        $this->drawerOpen    = true;
        unset($this->drawerTeste);
    }

    // ── Excluir ───────────────────────────────────────────────────────

    public function confirmDelete(int $id): void
    {
        $this->deleteId    = $id;
        $this->deleteModal = true;
    }

    public function deleteTeste(): void
    {
        $this->requireRhOrAdmin();
        RhTeste::findOrFail($this->deleteId)->delete();
        $this->deleteModal = false;
        unset($this->testes);
        $this->alertSuccess('Teste excluído.');
    }

    public function render()
    {
        return view('livewire.pages.rh.testes')
            ->layout('components.layouts.app', ['title' => 'Testes Online']);
    }
}
