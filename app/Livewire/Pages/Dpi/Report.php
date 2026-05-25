<?php

namespace App\Livewire\Pages\Dpi;

use App\Livewire\SecureComponent;
use App\Models\Department;
use App\Models\DpiPlan;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class Report extends SecureComponent
{
    #[Url]
    public int $selectedYear;

    #[Url]
    public string $filterStatus = '';

    #[Url]
    public string $filterDept = '';

    #[Url]
    public string $search = '';

    public function mount(): void
    {
        $this->requireAuth();

        $user = Auth::user();
        if (! ($user->isRhOuDp() || $user->isAdmin())) {
            abort(403, 'Acesso restrito ao RH/DP.');
        }

        $this->selectedYear = now()->year;
    }

    public function getYearsProperty(): array
    {
        $current = now()->year;
        return [$current + 1, $current, $current - 1];
    }

    // ── KPIs globais ──────────────────────────────────────────────────

    #[Computed]
    public function stats(): array
    {
        // Todos os usuários ativos não-admin
        $totalUsers = User::notAdmin()->where('is_active', true)->count();

        // Planos do ano selecionado
        $plans = DpiPlan::with(['goals.actions', 'user.accessProfile'])
            ->where('year', $this->selectedYear)
            ->get();

        $byStatus = $plans->groupBy('status');

        $totalActions    = $plans->sum(fn ($p) => $p->actions->count());
        $doneActions     = $plans->sum(fn ($p) => $p->actions->where('status', 'concluido')->count());
        $avgProgress     = $plans->isNotEmpty()
            ? (int) round($plans->avg(fn ($p) => $p->progress_percent))
            : 0;

        return [
            'total_users'    => $totalUsers,
            'with_plan'      => $plans->count(),
            'without_plan'   => max(0, $totalUsers - $plans->count()),
            'rascunho'       => $byStatus->get('rascunho', collect())->count(),
            'enviado'        => $byStatus->get('enviado',  collect())->count(),
            'aprovado'       => $byStatus->get('aprovado', collect())->count(),
            'reprovado'      => $byStatus->get('reprovado',collect())->count(),
            'concluido'      => $byStatus->get('concluido',collect())->count(),
            'total_actions'  => $totalActions,
            'done_actions'   => $doneActions,
            'avg_progress'   => $avgProgress,
        ];
    }

    // ── Breakdown por departamento ─────────────────────────────────────

    #[Computed]
    public function byDepartment(): \Illuminate\Support\Collection
    {
        $depts = Department::withCount([
            'users as total_users' => fn ($q) => $q->where('is_active', true)->whereDoesntHave('accessProfile', fn ($q2) => $q2->where('slug', 'administrator')),
        ])->get();

        return $depts->map(function ($dept) {
            $plans = DpiPlan::with(['goals.actions'])
                ->where('year', $this->selectedYear)
                ->whereHas('user', fn ($q) => $q->where('department_id', $dept->id)->where('is_active', true))
                ->get();

            $done    = $plans->where('status', 'concluido')->count();
            $active  = $plans->where('status', 'aprovado')->count();
            $waiting = $plans->whereIn('status', ['enviado'])->count();
            $draft   = $plans->whereIn('status', ['rascunho', 'reprovado'])->count();

            return [
                'name'        => $dept->name,
                'total_users' => $dept->total_users,
                'with_plan'   => $plans->count(),
                'no_plan'     => max(0, $dept->total_users - $plans->count()),
                'rascunho'    => $draft,
                'enviado'     => $waiting,
                'aprovado'    => $active,
                'concluido'   => $done,
                'avg_progress'=> $plans->isNotEmpty()
                    ? (int) round($plans->avg(fn ($p) => $p->progress_percent))
                    : 0,
            ];
        })->sortByDesc('with_plan')->values();
    }

    // ── Listagem filtrável de planos ───────────────────────────────────

    #[Computed]
    public function plans(): \Illuminate\Support\Collection
    {
        return DpiPlan::with(['goals.actions', 'user.department', 'user.accessProfile'])
            ->where('year', $this->selectedYear)
            ->when($this->filterStatus, fn ($q) => $q->where('status', $this->filterStatus))
            ->when($this->filterDept, fn ($q) => $q->whereHas(
                'user', fn ($q2) => $q2->where('department_id', $this->filterDept)
            ))
            ->when($this->search, fn ($q) => $q->whereHas(
                'user', fn ($q2) => $q2->where('name', 'ilike', '%' . $this->search . '%')
            ))
            ->get()
            ->sortBy(fn ($p) => $p->user?->name);
    }

    // ── Dados para gráfico de status (ApexCharts donut) ───────────────

    #[Computed]
    public function chartStatusData(): array
    {
        $s = $this->stats;
        return [
            'series' => [
                $s['rascunho'], $s['enviado'], $s['aprovado'], $s['reprovado'], $s['concluido'],
            ],
            'labels' => [
                'Em preparação', 'Aguardando', 'Ativos', 'Devolvidos', 'Concluídos',
            ],
            'colors' => ['#94a3b8', '#f59e0b', '#10b981', '#ef4444', '#14b8a6'],
        ];
    }

    // ── Departamentos para filtro ──────────────────────────────────────

    #[Computed]
    public function departments(): \Illuminate\Support\Collection
    {
        return Department::orderBy('name')->get();
    }

    public function render()
    {
        return view('livewire.pages.dpi.report')
            ->layout('components.layouts.app', ['title' => 'Relatório DPI']);
    }
}
