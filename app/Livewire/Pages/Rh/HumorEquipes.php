<?php

namespace App\Livewire\Pages\Rh;

use App\Livewire\SecureComponent;
use App\Models\Department;
use App\Models\MoodCheckin;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class HumorEquipes extends SecureComponent
{
    // ── Filtros ───────────────────────────────────────────────────────

    #[Url(as: 'aba')]
    public string $aba = 'hoje'; // 'hoje' | 'mensal'

    #[Url(as: 'dept')]
    public string $filtroDepartamento = '';

    #[Url(as: 'humor')]
    public string $filtroHumor = '';

    #[Url(as: 'busca')]
    public string $filtroBusca = '';

    #[Url(as: 'mes')]
    public string $mes = ''; // YYYY-MM

    // ── Lifecycle ─────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();

        if (empty($this->mes)) {
            $this->mes = now()->format('Y-m');
        }
    }

    // ── Navegação ─────────────────────────────────────────────────────

    public function setAba(string $aba): void
    {
        $this->aba = $aba;
    }

    public function mesAnterior(): void
    {
        $this->mes = Carbon::createFromFormat('Y-m', $this->mes)
            ->subMonth()->format('Y-m');
    }

    public function mesSeguinte(): void
    {
        $carbon = Carbon::createFromFormat('Y-m', $this->mes)->addMonth();
        if ($carbon->lessThanOrEqualTo(now()->startOfMonth())) {
            $this->mes = $carbon->format('Y-m');
        }
    }

    public function limparFiltros(): void
    {
        $this->filtroDepartamento = '';
        $this->filtroHumor        = '';
        $this->filtroBusca        = '';
    }

    // ── Dados auxiliares ──────────────────────────────────────────────

    #[Computed]
    public function departamentos()
    {
        return Department::orderBy('name')->get();
    }

    // ── ABA: HOJE ─────────────────────────────────────────────────────

    #[Computed]
    public function statsHoje(): array
    {
        $totalFuncionarios = User::whereHas('accessProfile', fn ($q) => $q->where('slug', '!=', 'administrator'))
            ->count();

        $checkins = MoodCheckin::where('checkin_date', today())->count();

        $pessimos = MoodCheckin::where('checkin_date', today())
            ->where('mood', 'pessimo')->count();

        $participacao = $totalFuncionarios > 0
            ? round($checkins / $totalFuncionarios * 100)
            : 0;

        $distribuicao = MoodCheckin::where('checkin_date', today())
            ->select('mood', DB::raw('COUNT(*) as total'))
            ->groupBy('mood')
            ->pluck('total', 'mood')
            ->toArray();

        return [
            'total_funcionarios' => $totalFuncionarios,
            'checkins'           => $checkins,
            'pessimos'           => $pessimos,
            'participacao'       => $participacao,
            'distribuicao'       => $distribuicao,
        ];
    }

    #[Computed]
    public function pessimosHoje()
    {
        return MoodCheckin::with(['user.department', 'user.accessProfile'])
            ->where('checkin_date', today())
            ->where('mood', 'pessimo')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($checkin) {
                $user     = $checkin->user;
                $dept     = $user->department;

                // Busca gerente do departamento
                $gerente = null;
                if ($dept) {
                    $gerente = User::where('department_id', $dept->id)
                        ->whereHas('accessProfile', fn ($q) => $q->where('slug', 'manager'))
                        ->first(['id', 'name', 'email']);
                }

                return [
                    'checkin_id'     => $checkin->id,
                    'user_id'        => $user->id,
                    'user_nome'      => $user->name,
                    'user_email'     => $user->email,
                    'departamento'   => $dept?->name ?? 'Sem departamento',
                    'nota'           => $checkin->note,
                    'horario'        => $checkin->created_at->format('H:i'),
                    'gerente_nome'   => $gerente?->name,
                    'gerente_email'  => $gerente?->email,
                ];
            });
    }

    #[Computed]
    public function checkinsHoje()
    {
        return MoodCheckin::with(['user.department'])
            ->where('checkin_date', today())
            ->when($this->filtroDepartamento, fn ($q) => $q->whereHas('user', fn ($q2) =>
                $q2->where('department_id', $this->filtroDepartamento)
            ))
            ->when($this->filtroHumor, fn ($q) => $q->where('mood', $this->filtroHumor))
            ->when($this->filtroBusca, fn ($q) => $q->whereHas('user', fn ($q2) =>
                $q2->where('name', 'like', '%' . $this->filtroBusca . '%')
            ))
            ->orderByRaw("CASE mood WHEN 'pessimo' THEN 0 WHEN 'normal' THEN 1 WHEN 'bem' THEN 2 WHEN 'otimo' THEN 3 END")
            ->orderBy('created_at', 'desc')
            ->get();
    }

    #[Computed]
    public function porDepartamentoHoje(): array
    {
        $moods = ['otimo', 'bem', 'normal', 'pessimo'];

        $rows = MoodCheckin::with('user.department')
            ->where('checkin_date', today())
            ->get()
            ->groupBy(fn ($c) => $c->user?->department?->name ?? 'Sem departamento');

        return $rows->map(function ($checkins, $dept) use ($moods) {
            $total = $checkins->count();
            $dist  = $checkins->groupBy('mood')->map->count();

            return [
                'departamento' => $dept,
                'total'        => $total,
                'otimo'        => $dist->get('otimo', 0),
                'bem'          => $dist->get('bem', 0),
                'normal'       => $dist->get('normal', 0),
                'pessimo'      => $dist->get('pessimo', 0),
                'score'        => $total > 0
                    ? round((
                        ($dist->get('otimo', 0) * 3) +
                        ($dist->get('bem', 0) * 2) +
                        ($dist->get('normal', 0) * 1) +
                        ($dist->get('pessimo', 0) * 0)
                    ) / ($total * 3) * 100)
                    : null,
            ];
        })->sortByDesc('score')->values()->toArray();
    }

    // ── ABA: MENSAL ───────────────────────────────────────────────────

    #[Computed]
    public function periodoMensal(): array
    {
        [$ano, $mes] = explode('-', $this->mes);
        $inicio = Carbon::createFromDate($ano, $mes, 1)->startOfDay();
        $fim    = $inicio->copy()->endOfMonth();

        $mesesPt = [
            1 => 'Janeiro', 2 => 'Fevereiro', 3 => 'Março',    4 => 'Abril',
            5 => 'Maio',    6 => 'Junho',      7 => 'Julho',    8 => 'Agosto',
            9 => 'Setembro',10 => 'Outubro',  11 => 'Novembro',12 => 'Dezembro',
        ];

        return [
            'inicio'       => $inicio,
            'fim'          => $fim,
            'label'        => $mesesPt[$inicio->month] . ' de ' . $inicio->year,
            'pode_avancar' => $inicio->copy()->addMonth()->lessThanOrEqualTo(now()->startOfMonth()),
        ];
    }

    #[Computed]
    public function statsMensal(): array
    {
        [$ano, $mes] = explode('-', $this->mes);

        $checkins = MoodCheckin::whereYear('checkin_date', $ano)
            ->whereMonth('checkin_date', $mes)
            ->when($this->filtroDepartamento, fn ($q) => $q->whereHas('user', fn ($q2) =>
                $q2->where('department_id', $this->filtroDepartamento)
            ))
            ->get();

        $total    = $checkins->count();
        $pessimos = $checkins->where('mood', 'pessimo')->count();
        $dist     = $checkins->groupBy('mood')->map->count();

        // Score médio de bem-estar (0–100)
        $score = $total > 0
            ? round((
                ($dist->get('otimo', 0) * 100) +
                ($dist->get('bem', 0) * 66) +
                ($dist->get('normal', 0) * 33) +
                ($dist->get('pessimo', 0) * 0)
            ) / $total)
            : null;

        return [
            'total'          => $total,
            'pessimos'       => $pessimos,
            'score'          => $score,
            'distribuicao'   => [
                'otimo'   => $dist->get('otimo', 0),
                'bem'     => $dist->get('bem', 0),
                'normal'  => $dist->get('normal', 0),
                'pessimo' => $dist->get('pessimo', 0),
            ],
        ];
    }

    #[Computed]
    public function tendenciaMensal(): array
    {
        [$ano, $mes] = explode('-', $this->mes);

        $rows = MoodCheckin::whereYear('checkin_date', $ano)
            ->whereMonth('checkin_date', $mes)
            ->when($this->filtroDepartamento, fn ($q) => $q->whereHas('user', fn ($q2) =>
                $q2->where('department_id', $this->filtroDepartamento)
            ))
            ->select('checkin_date', 'mood', DB::raw('COUNT(*) as total'))
            ->groupBy('checkin_date', 'mood')
            ->orderBy('checkin_date')
            ->get()
            ->groupBy(fn ($r) => $r->checkin_date->format('d/m'));

        $labels = $rows->keys()->values()->toArray();

        $series = [
            ['name' => 'Ótimo',   'color' => '#10b981', 'data' => []],
            ['name' => 'Bem',     'color' => '#6366f1', 'data' => []],
            ['name' => 'Normal',  'color' => '#f59e0b', 'data' => []],
            ['name' => 'Péssimo', 'color' => '#f43f5e', 'data' => []],
        ];

        $map = ['Ótimo' => 'otimo', 'Bem' => 'bem', 'Normal' => 'normal', 'Péssimo' => 'pessimo'];

        foreach ($labels as $label) {
            $dia = $rows[$label];
            foreach ($series as &$s) {
                $mood   = $map[$s['name']];
                $entry  = $dia->firstWhere('mood', $mood);
                $s['data'][] = $entry ? (int) $entry->total : 0;
            }
        }

        return ['labels' => $labels, 'series' => $series];
    }

    #[Computed]
    public function pessimosNoMes()
    {
        [$ano, $mes] = explode('-', $this->mes);

        return MoodCheckin::with(['user.department'])
            ->whereYear('checkin_date', $ano)
            ->whereMonth('checkin_date', $mes)
            ->where('mood', 'pessimo')
            ->when($this->filtroDepartamento, fn ($q) => $q->whereHas('user', fn ($q2) =>
                $q2->where('department_id', $this->filtroDepartamento)
            ))
            ->orderBy('checkin_date', 'desc')
            ->get();
    }

    #[Computed]
    public function rankingDepartamentosMensal(): array
    {
        [$ano, $mes] = explode('-', $this->mes);

        $checkins = MoodCheckin::with('user.department')
            ->whereYear('checkin_date', $ano)
            ->whereMonth('checkin_date', $mes)
            ->get()
            ->groupBy(fn ($c) => $c->user?->department?->name ?? 'Sem departamento');

        return $checkins->map(function ($items, $dept) {
            $total = $items->count();
            $dist  = $items->groupBy('mood')->map->count();
            $score = $total > 0
                ? round((
                    ($dist->get('otimo', 0) * 100) +
                    ($dist->get('bem', 0) * 66) +
                    ($dist->get('normal', 0) * 33) +
                    ($dist->get('pessimo', 0) * 0)
                ) / $total)
                : 0;

            return [
                'departamento' => $dept,
                'total'        => $total,
                'score'        => $score,
                'pessimos'     => $dist->get('pessimo', 0),
                'otimos'       => $dist->get('otimo', 0),
            ];
        })->sortByDesc('score')->values()->toArray();
    }

    public function render()
    {
        return view('livewire.pages.rh.humor-equipes')
            ->title('Humor das Equipes · RH');
    }
}
