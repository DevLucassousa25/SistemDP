<?php

namespace App\Livewire\Pages\Candidato;

use App\Livewire\Concerns\EnviaNotificacoes;
use App\Models\RhCandidatoResposta;
use App\Models\RhCandidatoTeste;
use App\Notifications\TesteOnlineConcluidoNotification;
use Livewire\Component;

class Teste extends Component
{
    use EnviaNotificacoes;
    public string $token;

    // Estado da tela: 'boas_vindas' | 'em_andamento' | 'concluido' | 'expirado' | 'invalido'
    public string $tela = 'boas_vindas';

    // Respostas selecionadas: [questao_id => opcao_id | texto_discursivo]
    public array $respostas = [];

    // Dados do teste carregados
    public ?RhCandidatoTeste $candidatoTeste = null;

    public function mount(string $token): void
    {
        $this->token = $token;

        $ct = RhCandidatoTeste::where('token', $token)
            ->with(['teste.questoes.opcoes', 'curriculo'])
            ->first();

        if (!$ct) {
            $this->tela = 'invalido';
            return;
        }

        $this->candidatoTeste = $ct;

        if ($ct->status === 'concluido') {
            $this->tela = 'concluido';
            return;
        }

        if ($ct->status === 'expirado' || ($ct->expira_at && $ct->expira_at->isPast())) {
            if ($ct->status !== 'expirado') {
                $ct->update(['status' => 'expirado']);
                $this->candidatoTeste = $ct->fresh(['teste.questoes.opcoes', 'curriculo']);
            }
            $this->tela = 'expirado';
            return;
        }

        if ($ct->status === 'em_andamento') {
            $this->tela = 'em_andamento';
            // Pré-carregar respostas já dadas
            foreach ($ct->respostas as $r) {
                $this->respostas[$r->questao_id] = $r->opcao_id ?? $r->resposta_discursiva;
            }
        }
    }

    public function iniciar(): void
    {
        if (!$this->candidatoTeste || $this->tela !== 'boas_vindas') return;

        $this->candidatoTeste->update([
            'status'      => 'em_andamento',
            'iniciado_at' => now(),
        ]);

        $this->candidatoTeste = $this->candidatoTeste->fresh(['teste.questoes.opcoes', 'curriculo']);
        $this->tela = 'em_andamento';
    }

    public function concluir(): void
    {
        if (!$this->candidatoTeste || $this->tela !== 'em_andamento') return;

        $teste     = $this->candidatoTeste->teste;
        $questoes  = $teste->questoes;
        $totalPeso = $questoes->sum('peso') ?: 1;
        $notaTotal = 0;

        foreach ($questoes as $questao) {
            $resposta = $this->respostas[$questao->id] ?? null;

            if ($questao->tipo === 'objetiva') {
                $opcaoId = is_numeric($resposta) ? (int) $resposta : null;
                $opcao   = $opcaoId ? $questao->opcoes->find($opcaoId) : null;
                $correta = $opcao?->correta ?? false;
                $nota    = $correta ? $questao->peso : 0;
                $notaTotal += $nota;

                // Só salva se ainda não respondeu
                RhCandidatoResposta::updateOrCreate(
                    ['candidato_teste_id' => $this->candidatoTeste->id, 'questao_id' => $questao->id],
                    ['opcao_id' => $opcaoId, 'correta' => $correta, 'nota_obtida' => $nota]
                );
            } elseif ($questao->tipo === 'discursiva') {
                $texto = is_string($resposta) ? trim($resposta) : null;

                RhCandidatoResposta::updateOrCreate(
                    ['candidato_teste_id' => $this->candidatoTeste->id, 'questao_id' => $questao->id],
                    ['resposta_discursiva' => $texto, 'correta' => null, 'nota_obtida' => null]
                );
            }
        }

        // Nota final 0–100 (percentual de acertos ponderados por peso)
        $notaFinal = $totalPeso > 0 ? round(($notaTotal / $totalPeso) * 100, 2) : 0;
        $aprovado  = $notaFinal >= ($teste->nota_aprovacao ?? 60);

        // Questões discursivas? Deixa aprovado como null para revisão manual
        $temDiscursiva = $questoes->where('tipo', 'discursiva')->count() > 0;

        $this->candidatoTeste->update([
            'status'       => 'concluido',
            'concluido_at' => now(),
            'nota'         => $notaFinal,
            'aprovado'     => $temDiscursiva ? null : $aprovado,
        ]);

        $this->candidatoTeste = $this->candidatoTeste->fresh(['teste.questoes.opcoes', 'curriculo']);

        // Notifica RH/Admin sobre teste concluído (sem toast — página pública)
        $this->notificarRhAdmin(new TesteOnlineConcluidoNotification($this->candidatoTeste));

        $this->tela = 'concluido';
    }

    public function render()
    {
        return view('livewire.pages.candidato.teste')
            ->layout('components.layouts.guest');
    }
}
