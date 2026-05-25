<?php

namespace App\Livewire\Pages\Funcionario;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Models\RhSolicitacao;
use App\Notifications\RhSolicitacaoNovaNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\On;
use Livewire\Attributes\Url;

class Solicitacoes extends SecureComponent
{
    use EnviaNotificacoes;

    // ── Navegação interna ──────────────────────────────────────────────
    #[Url]
    public string $aba = 'minhas'; // minhas | nova

    // ── Nova solicitação ──────────────────────────────────────────────
    public string $tipo      = '';
    public string $descricao = '';

    // ── Filtro de status na lista ─────────────────────────────────────
    public string $filtroStatus = '';

    // ── Detalhe (drawer) ─────────────────────────────────────────────
    public bool  $drawer    = false;
    public ?int  $drawerId  = null;

    public function mount(): void
    {
        $this->requireAuth();
    }

    // ── Refresh via evento do RH ──────────────────────────────────────
    #[On('sol-status-atualizado')]
    public function refreshLista(): void
    {
        unset($this->solicitacoes, $this->stats, $this->detalhe);
    }

    // ── Computed: lista de solicitações do funcionário logado ─────────
    #[Computed]
    public function solicitacoes()
    {
        $q = RhSolicitacao::where('user_id', Auth::id())
            ->orderByRaw("CASE status WHEN 'pendente' THEN 0 WHEN 'em_andamento' THEN 1 WHEN 'concluida' THEN 2 ELSE 3 END")
            ->orderBy('created_at', 'desc');

        if ($this->filtroStatus) {
            $q->where('status', $this->filtroStatus);
        }

        return $q->get();
    }

    #[Computed]
    public function stats(): array
    {
        $base = RhSolicitacao::where('user_id', Auth::id());
        return [
            'total'       => (clone $base)->count(),
            'abertas'     => (clone $base)->whereIn('status', ['pendente', 'em_andamento'])->count(),
            'concluidas'  => (clone $base)->where('status', 'concluida')->count(),
        ];
    }

    #[Computed]
    public function detalhe(): ?RhSolicitacao
    {
        if (!$this->drawerId) return null;
        return RhSolicitacao::where('user_id', Auth::id())
            ->where('id', $this->drawerId)
            ->first();
    }

    // ── Ações ─────────────────────────────────────────────────────────
    public function novaSolicitacao(): void
    {
        $this->validate([
            'tipo'      => 'required|in:' . implode(',', array_keys(RhSolicitacao::$tipoLabels)),
            'descricao' => 'nullable|string|max:2000',
        ]);

        $sol = RhSolicitacao::create([
            'user_id'   => Auth::id(),
            'tipo'      => $this->tipo,
            'descricao' => $this->descricao ?: null,
            'status'    => 'pendente',
        ]);

        // Notifica todos os usuários RH/Admin
        $this->notificarRhAdmin(
            new RhSolicitacaoNovaNotification($sol, Auth::user()->name)
        );

        $this->reset('tipo', 'descricao');
        $this->aba = 'minhas';
        unset($this->solicitacoes, $this->stats);
        $this->alertSuccess('Solicitação enviada!', 'O RH foi notificado e atenderá em breve.');
    }

    public function openDrawer(int $id): void
    {
        $this->drawerId = $id;
        $this->drawer   = true;
        unset($this->detalhe);
    }

    public function cancelarSolicitacao(int $id): void
    {
        RhSolicitacao::where('id', $id)
            ->where('user_id', Auth::id())
            ->where('status', 'pendente')
            ->update(['status' => 'cancelada']);

        $this->drawer = false;
        unset($this->solicitacoes, $this->stats, $this->detalhe);
        $this->alertSuccess('Cancelada', 'Sua solicitação foi cancelada.');
    }
}
