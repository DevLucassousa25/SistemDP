<?php

namespace App\Livewire\Pages\Dashboard;

use App\Livewire\SecureComponent;
use App\Models\Feedback;
use App\Models\Manifestacao;
use App\Models\Meeting;
use App\Models\OkrObjective;
use App\Models\RhVaga;
use App\Models\Survey;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class Index extends SecureComponent
{
    // ── Funcionário ──────────────────────────────────────────────────────────

    public function getMinhasTarefasProperty()
    {
        return Task::where('assigned_to', Auth::id())
            ->whereIn('status', ['pendente', 'em_progresso'])
            ->orderByRaw("CASE priority WHEN 'urgente' THEN 1 WHEN 'alta' THEN 2 WHEN 'media' THEN 3 WHEN 'baixa' THEN 4 ELSE 5 END")
            ->orderBy('due_date')
            ->limit(5)
            ->get();
    }

    public function getMinhasReunioesProperty()
    {
        $uid = Auth::id();
        return Meeting::where(function ($q) use ($uid) {
                $q->where('organizer_id', $uid)
                  ->orWhereHas('participants', fn ($p) => $p->where('user_id', $uid));
            })
            ->where('start_time', '>=', now())
            ->where('status', '!=', 'cancelada')
            ->orderBy('start_time')
            ->limit(3)
            ->get();
    }

    public function getMeusOkrsProperty()
    {
        return OkrObjective::where('owner_id', Auth::id())
            ->whereHas('cycle', fn ($q) => $q->where('status', 'ativo'))
            ->with('keyResults')
            ->limit(3)
            ->get();
    }

    public function getPesquisasPendentesProperty()
    {
        return Survey::where('status', 'ativo')
            ->whereDoesntHave('responses', fn ($q) => $q->where('user_id', Auth::id()))
            ->limit(3)
            ->get();
    }

    public function getStatsEmployeeProperty(): array
    {
        $uid = Auth::id();
        return [
            'tarefas_pendentes'    => Task::where('assigned_to', $uid)->where('status', 'pendente')->count(),
            'tarefas_em_progresso' => Task::where('assigned_to', $uid)->where('status', 'em_progresso')->count(),
            'tarefas_concluidas'   => Task::where('assigned_to', $uid)->where('status', 'concluida')->count(),
            'reunioes_hoje'        => Meeting::where(function ($q) use ($uid) {
                                            $q->where('organizer_id', $uid)
                                              ->orWhereHas('participants', fn ($p) => $p->where('user_id', $uid));
                                        })
                                        ->whereDate('start_time', today())
                                        ->where('status', '!=', 'cancelada')
                                        ->count(),
        ];
    }

    // ── Gerente ───────────────────────────────────────────────────────────────

    public function getStatsGerenteProperty(): array
    {
        $user         = Auth::user();
        $departmentId = $user->department_id;
        $teamIds      = User::where('department_id', $departmentId)->where('id', '!=', $user->id)->pluck('id');

        return [
            'funcionarios'           => $teamIds->count(),
            'tarefas_time_total'     => Task::whereIn('assigned_to', $teamIds)->count(),
            'tarefas_time_atrasadas' => Task::whereIn('assigned_to', $teamIds)
                                            ->where('status', '!=', 'concluida')
                                            ->whereNotNull('due_date')
                                            ->whereDate('due_date', '<', today())
                                            ->count(),
            'feedbacks_abertos'      => Feedback::where('evaluator_id', $user->id)->where('status', 'aberto')->count(),
            'reunioes_semana'        => Meeting::where(function ($q) use ($user) {
                                                $q->where('organizer_id', $user->id)
                                                  ->orWhereHas('participants', fn ($p) => $p->where('user_id', $user->id));
                                            })
                                            ->whereBetween('start_time', [now()->startOfWeek(), now()->endOfWeek()])
                                            ->where('status', '!=', 'cancelada')
                                            ->count(),
        ];
    }

    public function getTarefasTimeProperty()
    {
        $departmentId = Auth::user()->department_id;
        $teamIds      = User::where('department_id', $departmentId)->where('id', '!=', Auth::id())->pluck('id');

        return Task::whereIn('assigned_to', $teamIds)
            ->whereIn('status', ['pendente', 'em_progresso'])
            ->with('assignedTo')
            ->orderByRaw("CASE status WHEN 'em_progresso' THEN 1 WHEN 'pendente' THEN 2 ELSE 3 END")
            ->orderBy('due_date')
            ->limit(6)
            ->get();
    }

    public function getFeedbacksPendentesGerenteProperty()
    {
        return Feedback::where('evaluator_id', Auth::id())
            ->where('status', 'aberto')
            ->with('employee')
            ->latest()
            ->limit(4)
            ->get();
    }

    public function getOkrsDepartamentoProperty()
    {
        return OkrObjective::where('department_id', Auth::user()->department_id)
            ->whereHas('cycle', fn ($q) => $q->where('status', 'ativo'))
            ->with('keyResults')
            ->limit(4)
            ->get();
    }

    // ── DP / RH ───────────────────────────────────────────────────────────────

    public function getStatsRhProperty(): array
    {
        return [
            'total_funcionarios'    => User::whereHas('accessProfile', fn ($q) => $q->where('slug', 'employee'))->where('is_active', true)->count(),
            'total_gerentes'        => User::whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))->where('is_active', true)->count(),
            'vagas_abertas'         => RhVaga::where('status', 'aberta')->count(),
            'pesquisas_ativas'      => Survey::where('status', 'ativo')->count(),
            'manifestacoes_abertas' => Manifestacao::whereIn('status', [Manifestacao::STATUS_EM_ANALISE, Manifestacao::STATUS_EM_ANDAMENTO])->count(),
            'reunioes_hoje'         => Meeting::whereDate('start_time', today())->where('status', '!=', 'cancelada')->count(),
        ];
    }

    public function getUltimosUsuariosProperty()
    {
        return User::with('accessProfile', 'department')->latest()->limit(5)->get();
    }

    public function getManifestacoesPendentesProperty()
    {
        return Manifestacao::whereIn('status', [Manifestacao::STATUS_EM_ANALISE, Manifestacao::STATUS_EM_ANDAMENTO])
            ->latest()
            ->limit(4)
            ->get();
    }

    public function getVagasAbertasRhProperty()
    {
        return RhVaga::where('status', 'aberta')->withCount('candidaturas')->latest()->limit(4)->get();
    }

    public function getPesquisasAtivasRhProperty()
    {
        return Survey::where('status', 'ativo')->withCount('responses')->latest()->limit(4)->get();
    }

    // ── Render ────────────────────────────────────────────────────────────────

    public function render()
    {
        return view('livewire.pages.dashboard.index');
    }
}
