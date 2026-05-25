<?php

namespace App\Livewire\Pages\Rh;

use App\Livewire\SecureComponent;
use App\Models\RhCurriculo;
use App\Models\RhVaga;
use App\Models\RhCandidatura;
use App\Models\RhCandidatoTeste;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Livewire\Attributes\Computed;

class Dashboard extends SecureComponent
{
    public function mount(): void
    {
        $this->requireAuth();
        $this->requireRhOrAdmin();
    }

    #[Computed]
    public function stats(): array
    {
        $totalCandidatos     = RhCurriculo::count();
        $vagasAbertas        = RhVaga::where('status', 'publicada')->count();
        $processosAtivos     = RhCandidatura::where('status', 'ativo')->distinct('vaga_id')->count('vaga_id');
        $testesRealizados    = RhCandidatoTeste::where('status', 'concluido')->count();
        $totalTestes         = RhCandidatoTeste::whereIn('status', ['concluido', 'expirado'])->count();
        $taxaAprovacao       = $totalTestes > 0
            ? round(RhCandidatoTeste::where('status','concluido')->where('aprovado', true)->count() / $totalTestes * 100)
            : 0;

        // Tempo médio de contratação (dias entre criação da candidatura e aprovado_at)
        $tempoMedio = RhCandidatura::where('status','aprovado')
            ->whereNotNull('aprovado_at')
            ->selectRaw('AVG(EXTRACT(DAY FROM (aprovado_at - created_at))) as media')
            ->value('media');

        return [
            'total_candidatos'   => $totalCandidatos,
            'vagas_abertas'      => $vagasAbertas,
            'processos_ativos'   => $processosAtivos,
            'testes_realizados'  => $testesRealizados,
            'taxa_aprovacao'     => $taxaAprovacao,
            'tempo_medio_dias'   => round($tempoMedio ?? 0),
        ];
    }

    #[Computed]
    public function candidatosPorVaga(): array
    {
        return RhVaga::where('status', 'publicada')
            ->withCount('candidaturas')
            ->orderByDesc('candidaturas_count')
            ->limit(8)
            ->get()
            ->map(fn($v) => ['label' => $v->titulo, 'value' => $v->candidaturas_count])
            ->toArray();
    }

    #[Computed]
    public function contratacoesPorMes(): array
    {
        return RhCandidatura::where('status', 'aprovado')
            ->whereNotNull('aprovado_at')
            ->where('aprovado_at', '>=', now()->subMonths(6))
            ->selectRaw("TO_CHAR(aprovado_at, 'Mon/YY') as mes, COUNT(*) as total")
            ->groupByRaw("TO_CHAR(aprovado_at, 'Mon/YY'), DATE_TRUNC('month', aprovado_at)")
            ->orderByRaw("DATE_TRUNC('month', aprovado_at)")
            ->get()
            ->map(fn($r) => ['label' => $r->mes, 'value' => $r->total])
            ->toArray();
    }

    #[Computed]
    public function desempenhoTestes(): array
    {
        return RhCandidatoTeste::where('status', 'concluido')
            ->selectRaw('
                CASE WHEN nota >= 90 THEN "90-100"
                     WHEN nota >= 70 THEN "70-89"
                     WHEN nota >= 50 THEN "50-69"
                     ELSE "<50" END as faixa,
                COUNT(*) as total
            ')
            ->groupBy('faixa')
            ->get()
            ->mapWithKeys(fn($r) => [$r->faixa => $r->total])
            ->toArray();
    }

    #[Computed]
    public function vagasRecentes(): \Illuminate\Support\Collection
    {
        return RhVaga::withCount('candidaturas')
            ->latest()
            ->limit(5)
            ->get();
    }

    #[Computed]
    public function candidatosRecentes(): \Illuminate\Support\Collection
    {
        return RhCurriculo::latest()->limit(5)->get();
    }

    #[Computed]
    public function rankingRecrutadores(): array
    {
        return DB::table('rh_candidaturas as c')
            ->join('rh_vagas as v', 'v.id', '=', 'c.vaga_id')
            ->join('users as u', 'u.id', '=', 'v.created_by')
            ->where('c.status', 'aprovado')
            ->selectRaw('u.name, COUNT(*) as total')
            ->groupBy('u.name', 'u.id')
            ->orderByDesc('total')
            ->limit(5)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.pages.rh.dashboard')
            ->layout('components.layouts.app', ['title' => 'R&S — Dashboard']);
    }
}
