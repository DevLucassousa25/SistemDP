<?php

namespace App\Livewire\Pages\Rh;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Models\ConfiguracaoEmpresa;
use App\Livewire\SecureComponent;
use App\Models\AccessProfile;
use App\Models\Department;
use App\Models\RhCandidatura;
use App\Models\RhCandidaturaComentario;
use App\Models\RhCandidaturaHistorico;
use App\Models\RhCurriculo;
use App\Models\RhOnboarding;
use App\Models\RhOnboardingTarefa;
use App\Models\RhVaga;
use App\Models\RhVagaEtapa;
use App\Models\User;
use App\Notifications\CandidatoContratadoNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\WithPagination;

class Pipeline extends SecureComponent
{
    use EnviaNotificacoes;

    #[Locked]
    public ?int $vagaId    = null;
    public string $search  = '';

    // ── Candidatura drawer ────────────────────────────────────────────
    public bool   $drawerOpen        = false;
    #[Locked]
    public ?int   $drawerCandidId    = null;
    public string $novoComentario    = '';

    // ── Vincular currículo ────────────────────────────────────────────
    public bool   $vincularModal     = false;
    public string $searchCurriculo   = '';
    #[Locked]
    public ?int   $vincularEtapaId   = null;

    // ── Nota rápida ───────────────────────────────────────────────────
    public string $notaRapida        = '';

    // ── Modal confirmação de contratação ──────────────────────────────
    public bool   $contratarModal   = false;
    #[Locked]
    public ?int   $contratarCandId  = null;
    #[Locked]
    public ?int   $contratarEtapaId = null;
    public string $onbNome          = '';
    public string $onbEmail         = '';
    public string $onbCargo         = '';
    public string $onbDepartamento  = '';
    public string $onbPerfil        = '';
    public string $onbDataInicio    = '';
    public string $onbObservacoes   = '';
    #[Locked]
    public ?int   $notaCandidId      = null;

    public function mount(?int $vaga = null): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();
        $this->vagaId = $vaga;
    }

    // ── Computeds ─────────────────────────────────────────────────────

    #[Computed]
    public function vagas(): \Illuminate\Support\Collection
    {
        return RhVaga::whereIn('status', ['publicada','pausada'])
            ->withCount('candidaturas')
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function vaga(): ?RhVaga
    {
        if (!$this->vagaId) return null;
        return RhVaga::with('etapas')->find($this->vagaId);
    }

    #[Computed]
    public function kanban(): array
    {
        if (!$this->vagaId) return [];

        $etapas = RhVagaEtapa::where('vaga_id', $this->vagaId)
            ->orderBy('ordem')
            ->get();

        $candidaturas = RhCandidatura::with(['curriculo.tags', 'curriculo.habilidades'])
            ->where('vaga_id', $this->vagaId)
            ->when($this->search, fn($q) =>
                $q->whereHas('curriculo', fn($s) =>
                    $s->where('nome', 'ilike', "%{$this->search}%")
                )
            )
            ->get()
            ->groupBy('etapa_id');

        return $etapas->map(fn($etapa) => [
            'etapa'        => $etapa,
            'candidaturas' => $candidaturas->get($etapa->id, collect()),
            'count'        => $candidaturas->get($etapa->id, collect())->count(),
        ])->toArray();
    }

    #[Computed]
    public function drawerCandidatura(): ?RhCandidatura
    {
        if (!$this->drawerCandidId) return null;
        return RhCandidatura::with([
            'curriculo.experiencias',
            'curriculo.formacoes',
            'curriculo.habilidades',
            'curriculo.tags',
            'curriculo.testes.teste',
            'vaga.etapas',
            'etapa',
            'historicos.user',
            'comentarios.user',
        ])->find($this->drawerCandidId);
    }

    #[Computed]
    public function curriculosDisponiveis()
    {
        if (!$this->vagaId) return collect();
        // Excluir quem já candidatou
        $jaIds = RhCandidatura::where('vaga_id', $this->vagaId)->pluck('curriculo_id');
        return RhCurriculo::whereNotIn('id', $jaIds)
            ->when($this->searchCurriculo, fn($q) =>
                $q->where('nome', 'ilike', "%{$this->searchCurriculo}%")
                  ->orWhere('email', 'ilike', "%{$this->searchCurriculo}%")
            )
            ->limit(20)
            ->get();
    }

    // ── Mover candidato (drag & drop / dropdown) ──────────────────────

    public function moverCandidato(int $candidaturaId, int $etapaId): void
    {
        $this->requireRhOrAdmin();
        $candidatura = RhCandidatura::with(['curriculo', 'vaga'])->findOrFail($candidaturaId);
        $novaEtapa   = RhVagaEtapa::findOrFail($etapaId);

        // Se a etapa destino é de aprovação → abre modal de contratação
        // Considera is_aprovado OU nome que contenha "aprovad" (fallback para etapas não configuradas)
        $ehAprovacao = $novaEtapa->is_aprovado
            || str_contains(mb_strtolower($novaEtapa->nome), 'aprovad');

        if ($ehAprovacao) {
            // Marca a flag no banco se ainda não estava marcada
            if (!$novaEtapa->is_aprovado) {
                $novaEtapa->update(['is_aprovado' => true]);
            }
            $curriculo = $candidatura->curriculo;

            if ($curriculo->user_id) {
                $this->alertError(
                    'Já contratado',
                    'Um usuário já foi criado para este candidato anteriormente.'
                );
                return;
            }

            $this->contratarCandId  = $candidaturaId;
            $this->contratarEtapaId = $etapaId;
            $this->onbNome          = $curriculo->nome ?? '';
            $emailSugerido = ConfiguracaoEmpresa::gerarEmail($curriculo->nome ?? '');
            $this->onbEmail = $emailSugerido ?: ($curriculo->email ?? '');
            $this->onbCargo         = $candidatura->vaga?->cargo ?? '';
            $this->onbDepartamento  = '';
            $this->onbPerfil        = (string) (AccessProfile::where('slug','employee')->value('id') ?? '');
            $this->onbDataInicio    = now()->addDays(7)->toDateString();
            $this->onbObservacoes   = '';
            $this->contratarModal   = true;
            return;
        }

        // Etapas normais — move direto
        $this->_executarMover($candidatura, $novaEtapa, $etapaId);
    }

    public function confirmarContratacao(): void
    {
        $this->requireRhOrAdmin();

        $this->validate([
            'onbNome'         => 'required|string|max:255',
            'onbEmail'        => 'required|email|unique:users,email',
            'onbCargo'        => 'required|string|max:150',
            'onbDepartamento' => 'nullable|exists:departments,id',
            'onbPerfil'       => 'nullable|exists:access_profiles,id',
            'onbDataInicio'   => 'nullable|date',
        ], [
            'onbNome.required'  => 'O nome é obrigatório.',
            'onbEmail.required' => 'O e-mail é obrigatório.',
            'onbEmail.email'    => 'Informe um e-mail válido.',
            'onbEmail.unique'   => 'Este e-mail já está cadastrado no sistema.',
            'onbCargo.required' => 'O cargo é obrigatório.',
        ]);

        $cand = RhCandidatura::with(['curriculo', 'vaga'])->findOrFail($this->contratarCandId);
        $nova = RhVagaEtapa::findOrFail($this->contratarEtapaId);

        // 1. Criar usuário
        $user = User::create([
            'name'              => trim($this->onbNome),
            'email'             => trim($this->onbEmail),
            'password'          => Hash::make('lu753951'),
            'position'          => trim($this->onbCargo),
            'department_id'     => $this->onbDepartamento ?: null,
            'access_profile_id' => $this->onbPerfil ?: null,
            'is_active'         => true,
        ]);

        // 2. Vincular ao currículo
        $cand->curriculo->update(['user_id' => $user->id]);

        // 3. Mover candidatura
        $this->_executarMover($cand, $nova, $this->contratarEtapaId);

        // 4. Onboarding + tarefas padrão
        $onboarding = RhOnboarding::create([
            'candidatura_id' => $this->contratarCandId,
            'user_id'        => $user->id,
            'criado_by'      => Auth::id(),
            'department_id'  => $this->onbDepartamento ?: null,
            'cargo'          => trim($this->onbCargo),
            'data_inicio'    => $this->onbDataInicio ?: null,
            'status'         => 'em_andamento',
            'observacoes'    => $this->onbObservacoes ?: null,
        ]);

        foreach (RhOnboarding::tarefasPadrao() as $tarefa) {
            RhOnboardingTarefa::create([
                'onboarding_id' => $onboarding->id,
                'titulo'        => $tarefa['titulo'],
                'responsavel'   => $tarefa['responsavel'],
                'ordem'         => $tarefa['ordem'],
                'status'        => 'pendente',
            ]);
        }

        // 5. Notificar RH
        $this->notificarRhAdmin(new CandidatoContratadoNotification($onboarding->load('user')));

        $this->contratarModal   = false;
        $this->contratarCandId  = null;
        $this->contratarEtapaId = null;

        $this->toastNotif(
            'Contratação confirmada!',
            "Usuário criado para {$this->onbNome}. Onboarding iniciado com 8 tarefas.",
            'user-check', 'green',
            route('rh.onboarding')
        );

        unset($this->kanban, $this->drawerCandidatura);
    }

    #[Computed]
    public function departamentosOnb(): \Illuminate\Database\Eloquent\Collection
    {
        return Department::orderBy('name')->get(['id', 'name']);
    }

    #[Computed]
    public function perfisOnb(): \Illuminate\Database\Eloquent\Collection
    {
        return AccessProfile::orderBy('name')->get(['id', 'name', 'slug']);
    }

    private function _executarMover(RhCandidatura $candidatura, RhVagaEtapa $novaEtapa, int $etapaId): void
    {
        $etapaAnterior = $candidatura->etapa?->nome ?? 'Sem etapa';
        $novoStatus    = 'ativo';
        if ($novaEtapa->is_aprovado)            $novoStatus = 'aprovado';
        elseif ($novaEtapa->is_reprovado)       $novoStatus = 'reprovado';
        elseif ($novaEtapa->is_banco_talentos)  $novoStatus = 'banco_talentos';

        $candidatura->update([
            'etapa_id'    => $etapaId,
            'status'      => $novoStatus,
            'aprovado_at' => $novoStatus === 'aprovado' ? now() : null,
        ]);

        RhCandidaturaHistorico::create([
            'candidatura_id' => $candidatura->id,
            'user_id'        => Auth::id(),
            'etapa_anterior' => $etapaAnterior,
            'etapa_nova'     => $novaEtapa->nome,
            'acao'           => 'mover_etapa',
            'descricao'      => "Movido de '{$etapaAnterior}' para '{$novaEtapa->nome}'",
            'created_at'     => now(),
        ]);

        unset($this->kanban, $this->drawerCandidatura);
    }

    // ── Comentário ────────────────────────────────────────────────────

    public function addComentario(): void
    {
        $this->requireRhOrAdmin();
        $txt = trim($this->novoComentario);
        if (!$txt) return;
        $this->validate(['novoComentario' => 'required|string|max:2000']);

        RhCandidaturaComentario::create([
            'candidatura_id' => $this->drawerCandidId,
            'user_id'        => Auth::id(),
            'comentario'     => $this->sanitize($txt),
        ]);
        RhCandidaturaHistorico::create([
            'candidatura_id' => $this->drawerCandidId,
            'user_id'        => Auth::id(),
            'acao'           => 'comentario',
            'descricao'      => 'Comentário adicionado.',
            'created_at'     => now(),
        ]);

        $this->novoComentario = '';
        unset($this->drawerCandidatura);
    }

    // ── Nota final ────────────────────────────────────────────────────

    public function salvarNota(int $candidaturaId): void
    {
        $this->requireRhOrAdmin();
        $nota = (float) $this->notaRapida;
        if ($nota < 0 || $nota > 100) {
            $this->alertError('Nota inválida.', 'Deve estar entre 0 e 100.');
            return;
        }
        RhCandidatura::findOrFail($candidaturaId)->update(['nota_final' => $nota]);
        $this->recalcularRanking();
        $this->notaCandidId  = null;
        $this->notaRapida    = '';
        unset($this->kanban, $this->drawerCandidatura);
        $this->alertSuccess('Nota salva!');
    }

    private function recalcularRanking(): void
    {
        if (!$this->vagaId) return;
        $cands = RhCandidatura::where('vaga_id', $this->vagaId)
            ->whereNotNull('nota_final')
            ->orderByDesc('nota_final')
            ->get();
        foreach ($cands as $pos => $c) {
            $c->update(['ranking_posicao' => $pos + 1]);
        }
    }

    // ── Vincular candidato ────────────────────────────────────────────

    public function openVincular(): void
    {
        $this->vincularModal = true;
        $this->searchCurriculo = '';
        unset($this->curriculosDisponiveis);
    }

    public function vincularCandidato(int $curriculoId): void
    {
        $this->requireRhOrAdmin();
        if (!$this->vagaId) return;

        $primeiraEtapa = RhVagaEtapa::where('vaga_id', $this->vagaId)
            ->orderBy('ordem')->first();

        $candidatura = RhCandidatura::create([
            'curriculo_id' => $curriculoId,
            'vaga_id'      => $this->vagaId,
            'etapa_id'     => $primeiraEtapa?->id,
            'status'       => 'ativo',
        ]);

        RhCandidaturaHistorico::create([
            'candidatura_id' => $candidatura->id,
            'user_id'        => Auth::id(),
            'acao'           => 'vinculado',
            'descricao'      => 'Candidato vinculado à vaga.',
            'created_at'     => now(),
        ]);

        $this->vincularModal = false;
        unset($this->kanban, $this->curriculosDisponiveis);
        $this->alertSuccess('Candidato vinculado!');
    }

    // ── Drawer ────────────────────────────────────────────────────────

    public function openDrawer(int $candidaturaId): void
    {
        $this->drawerCandidId = $candidaturaId;
        $this->drawerOpen     = true;
        unset($this->drawerCandidatura);
    }

    public function render()
    {
        return view('livewire.pages.rh.pipeline')
            ->layout('components.layouts.app', ['title' => 'Pipeline R&S']);
    }
}
