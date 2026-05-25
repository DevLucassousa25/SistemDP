<?php

namespace App\Livewire\Pages\Humor;

use App\Livewire\SecureComponent;
use App\Models\MoodCheckin;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class HistoricoPessoal extends SecureComponent
{
    // ── Filtro de período ─────────────────────────────────────────────

    #[Url(as: 'periodo')]
    public string $periodo = '30'; // '7' | '30' | '90' | 'custom'

    #[Url(as: 'de')]
    public string $dataInicio = '';

    #[Url(as: 'ate')]
    public string $dataFim = '';

    // ── Lifecycle ─────────────────────────────────────────────────────

    public function mount(): void
    {
        $this->requireAuth();

        // Bloqueia admins — esta página é só para funcionários verem o próprio histórico
        if (Auth::user()->isAdmin()) {
            abort(403);
        }

        $this->dataFim   = today()->toDateString();
        $this->dataInicio = today()->subDays(29)->toDateString();
    }

    // ── Navegação de período ──────────────────────────────────────────

    public function setPeriodo(string $periodo): void
    {
        $this->periodo    = $periodo;
        $this->dataFim    = today()->toDateString();
        $this->dataInicio = match ($periodo) {
            '7'      => today()->subDays(6)->toDateString(),
            '30'     => today()->subDays(29)->toDateString(),
            '90'     => today()->subDays(89)->toDateString(),
            default  => $this->dataInicio,
        };
        unset($this->checkins, $this->stats, $this->chartData);
    }

    public function aplicarCustom(): void
    {
        $this->validate([
            'dataInicio' => 'required|date|before_or_equal:dataFim',
            'dataFim'    => 'required|date|after_or_equal:dataInicio',
        ], [
            'dataInicio.required'          => 'Informe a data inicial.',
            'dataInicio.before_or_equal'   => 'A data inicial deve ser anterior à final.',
            'dataFim.required'             => 'Informe a data final.',
        ]);
        $this->periodo = 'custom';
        unset($this->checkins, $this->stats, $this->chartData);
    }

    // ── Período ativo ─────────────────────────────────────────────────

    private function inicio(): Carbon
    {
        return Carbon::parse($this->dataInicio)->startOfDay();
    }

    private function fim(): Carbon
    {
        return Carbon::parse($this->dataFim)->endOfDay();
    }

    // ── Dados ─────────────────────────────────────────────────────────

    #[Computed]
    public function checkins()
    {
        return MoodCheckin::where('user_id', Auth::id())
            ->whereBetween('checkin_date', [$this->inicio(), $this->fim()])
            ->orderBy('checkin_date', 'desc')
            ->get();
    }

    #[Computed]
    public function stats(): array
    {
        $all = $this->checkins;

        $total    = $all->count();
        $dist     = $all->groupBy('mood')->map->count();
        $maisFreq = $dist->sortDesc()->keys()->first();

        // Score de bem-estar médio (0–100)
        $score = $total > 0
            ? round((
                ($dist->get('otimo',   0) * 100) +
                ($dist->get('bem',     0) * 66)  +
                ($dist->get('normal',  0) * 33)  +
                ($dist->get('pessimo', 0) * 0)
            ) / $total)
            : null;

        // Streak atual (dias consecutivos respondidos até hoje)
        $streak  = 0;
        $dia     = today();
        while (true) {
            $existe = MoodCheckin::where('user_id', Auth::id())
                ->whereDate('checkin_date', $dia)
                ->exists();
            if (! $existe) break;
            $streak++;
            $dia = $dia->subDay();
        }

        // Melhor streak histórico
        $todos = MoodCheckin::where('user_id', Auth::id())
            ->orderBy('checkin_date')
            ->pluck('checkin_date')
            ->map(fn ($d) => Carbon::parse($d)->toDateString())
            ->unique()
            ->values();

        $melhorStreak = 0;
        $corrente     = 0;
        foreach ($todos as $i => $data) {
            if ($i === 0) {
                $corrente = 1;
            } else {
                $anterior = Carbon::parse($todos[$i - 1]);
                $atual    = Carbon::parse($data);
                $corrente = $anterior->diffInDays($atual) === 1 ? $corrente + 1 : 1;
            }
            $melhorStreak = max($melhorStreak, $corrente);
        }

        return [
            'total'         => $total,
            'score'         => $score,
            'mais_frequente'=> $maisFreq,
            'streak'        => $streak,
            'melhor_streak' => $melhorStreak,
            'distribuicao'  => [
                'otimo'   => $dist->get('otimo',   0),
                'bem'     => $dist->get('bem',     0),
                'normal'  => $dist->get('normal',  0),
                'pessimo' => $dist->get('pessimo', 0),
            ],
        ];
    }

    #[Computed]
    public function chartData(): array
    {
        // Gera todos os dias do período
        $inicio = $this->inicio();
        $fim    = $this->fim();

        $moodScore = ['otimo' => 4, 'bem' => 3, 'normal' => 2, 'pessimo' => 1];

        // Indexa checkins por data
        $porData = $this->checkins
            ->keyBy(fn ($c) => $c->checkin_date->toDateString());

        $labels  = [];
        $scores  = [];
        $moods   = [];
        $colors  = [];

        $moodColors = [
            'otimo'   => '#10b981',
            'bem'     => '#6366f1',
            'normal'  => '#f59e0b',
            'pessimo' => '#f43f5e',
        ];

        $current = $inicio->copy();
        while ($current->lte($fim)) {
            $dateStr = $current->toDateString();
            $labels[] = $current->format('d/m');

            if (isset($porData[$dateStr])) {
                $mood     = $porData[$dateStr]->mood;
                $scores[] = $moodScore[$mood];
                $moods[]  = $mood;
                $colors[] = $moodColors[$mood];
            } else {
                $scores[] = null;
                $moods[]  = null;
                $colors[] = 'transparent';
            }

            $current->addDay();
        }

        return [
            'labels' => $labels,
            'scores' => $scores,
            'moods'  => $moods,
            'colors' => $colors,
        ];
    }

    public function render()
    {
        return view('livewire.pages.humor.historico-pessoal')
            ->title('Meu Humor · PeopleHub');
    }
}
