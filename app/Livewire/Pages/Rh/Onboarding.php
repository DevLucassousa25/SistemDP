<?php

namespace App\Livewire\Pages\Rh;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Livewire\SecureComponent;
use App\Models\RhOnboarding;
use App\Models\RhOnboardingTarefa;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Title;

#[Title('Onboarding')]
class Onboarding extends SecureComponent
{
    use EnviaNotificacoes;

    // ── Filtros / busca ───────────────────────────────────────────────
    public string $statusFiltro = 'em_andamento'; // em_andamento | concluido | cancelado | todos
    public string $busca        = '';

    // ── Drawer de detalhe ─────────────────────────────────────────────
    public bool   $drawerOpen = false;
    #[Locked]
    public ?int   $drawerOnbId = null;

    // ── Nova tarefa avulsa ────────────────────────────────────────────
    public string $novaTarefaTitulo     = '';
    public string $novaTarefaResp       = 'rh';

    // ── Modal confirmar conclusão do onboarding ───────────────────────
    public bool   $concluirModal   = false;
    #[Locked]
    public ?int   $concluirOnbId   = null;

    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();
    }

    // ── Computeds ─────────────────────────────────────────────────────

    #[Computed]
    public function onboardings(): \Illuminate\Database\Eloquent\Collection
    {
        return RhOnboarding::with([
                'user:id,name,position,avatar',
                'department:id,name',
                'tarefas',
                'criadoPor:id,name',
                'candidatura.vaga:id,titulo',
            ])
            ->when($this->statusFiltro !== 'todos', fn ($q) => $q->where('status', $this->statusFiltro))
            ->when(trim($this->busca), fn ($q) =>
                $q->whereHas('user', fn ($s) =>
                    $s->where('name', 'ilike', '%'.trim($this->busca).'%')
                )
            )
            ->orderByDesc('created_at')
            ->get();
    }

    #[Computed]
    public function drawerOnboarding(): ?RhOnboarding
    {
        if (! $this->drawerOnbId) return null;
        return RhOnboarding::with([
            'user:id,name,position,email,avatar',
            'department:id,name',
            'tarefas.concluidoPor:id,name',
            'criadoPor:id,name',
            'candidatura.vaga:id,titulo,cargo',
            'candidatura.curriculo:id,nome,email,telefone',
        ])->find($this->drawerOnbId);
    }

    #[Computed]
    public function stats(): array
    {
        return [
            'em_andamento' => RhOnboarding::where('status', 'em_andamento')->count(),
            'concluido'    => RhOnboarding::where('status', 'concluido')->count(),
            'cancelado'    => RhOnboarding::where('status', 'cancelado')->count(),
            'total'        => RhOnboarding::count(),
        ];
    }

    // ── Drawer ────────────────────────────────────────────────────────

    public function openDrawer(int $onbId): void
    {
        $this->drawerOnbId = $onbId;
        $this->drawerOpen  = true;
        unset($this->drawerOnboarding);
    }

    public function closeDrawer(): void
    {
        $this->drawerOpen  = false;
        $this->drawerOnbId = null;
    }

    // ── Tarefas ───────────────────────────────────────────────────────

    public function toggleTarefa(int $tarefaId): void
    {
        $this->requireRhOrAdmin();
        $tarefa = RhOnboardingTarefa::findOrFail($tarefaId);

        if ($tarefa->status === 'pendente') {
            $tarefa->update([
                'status'       => 'concluido',
                'concluido_by' => Auth::id(),
                'concluido_at' => now(),
            ]);
        } else {
            $tarefa->update([
                'status'       => 'pendente',
                'concluido_by' => null,
                'concluido_at' => null,
            ]);
        }

        unset($this->drawerOnboarding, $this->onboardings);
    }

    public function adicionarTarefa(): void
    {
        $this->requireRhOrAdmin();
        $this->validate([
            'novaTarefaTitulo' => 'required|string|max:255',
            'novaTarefaResp'   => 'required|in:rh,ti,gestao,financeiro',
        ], [
            'novaTarefaTitulo.required' => 'Informe o título da tarefa.',
        ]);

        $maxOrdem = RhOnboardingTarefa::where('onboarding_id', $this->drawerOnbId)->max('ordem') ?? 0;

        RhOnboardingTarefa::create([
            'onboarding_id' => $this->drawerOnbId,
            'titulo'        => trim($this->novaTarefaTitulo),
            'responsavel'   => $this->novaTarefaResp,
            'ordem'         => $maxOrdem + 1,
            'status'        => 'pendente',
        ]);

        $this->novaTarefaTitulo = '';
        $this->novaTarefaResp   = 'rh';
        unset($this->drawerOnboarding, $this->onboardings);
    }

    public function removerTarefa(int $tarefaId): void
    {
        $this->requireRhOrAdmin();
        RhOnboardingTarefa::findOrFail($tarefaId)->delete();
        unset($this->drawerOnboarding, $this->onboardings);
    }

    // ── Concluir onboarding ───────────────────────────────────────────

    public function abrirConcluir(int $onbId): void
    {
        $this->concluirOnbId = $onbId;
        $this->concluirModal = true;
    }

    public function concluirOnboarding(): void
    {
        $this->requireRhOrAdmin();
        $onb = RhOnboarding::findOrFail($this->concluirOnbId);

        $onb->update([
            'status'       => 'concluido',
            'concluido_at' => now(),
        ]);

        // Marcar todas as tarefas pendentes como concluídas também
        RhOnboardingTarefa::where('onboarding_id', $onb->id)
            ->where('status', 'pendente')
            ->update([
                'status'       => 'concluido',
                'concluido_by' => Auth::id(),
                'concluido_at' => now(),
            ]);

        $this->concluirModal = false;
        $this->concluirOnbId = null;
        $this->drawerOpen    = false;

        unset($this->drawerOnboarding, $this->onboardings, $this->stats);

        $this->alertSuccess('Onboarding concluído!', 'O processo de integração foi finalizado.');
    }

    public function cancelarOnboarding(int $onbId): void
    {
        $this->requireRhOrAdmin();
        RhOnboarding::findOrFail($onbId)->update(['status' => 'cancelado']);
        unset($this->onboardings, $this->stats, $this->drawerOnboarding);
        $this->alertSuccess('Onboarding cancelado.');
    }

    public function render()
    {
        return view('livewire.pages.rh.onboarding')
            ->layout('components.layouts.app', ['title' => 'Onboarding']);
    }
}
