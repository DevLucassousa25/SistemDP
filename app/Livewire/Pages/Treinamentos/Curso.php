<?php

namespace App\Livewire\Pages\Treinamentos;

use App\Livewire\SecureComponent;
use App\Models\Treinamento;
use App\Models\TreinamentoAula;
use App\Models\TreinamentoAulaProgresso;
use App\Models\TreinamentoAvaliacaoReacao;
use App\Models\TreinamentoCertificado;
use App\Models\TreinamentoDuvida;
use App\Models\TreinamentoInscricao;
use App\Models\TreinamentoQuestaoOpcao;
use App\Models\TreinamentoResposta;
use App\Models\TreinamentoTentativa;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Locked;
use Livewire\Attributes\Url;
use Livewire\Attributes\Validate;

class Curso extends SecureComponent
{
    #[Locked]
    public int $treinamentoId;

    // ── Navegação interna ─────────────────────────────────────────────────
    public string $aba = 'aulas'; // aulas | teste | duvidas

    #[Url(as: 'aula')]
    public ?int $aulaAtualId = null;

    // ── Teste ─────────────────────────────────────────────────────────────
    public bool $testeAtivo       = false;
    public bool $testeEnviado     = false;
    public array $respostas       = [];   // [questao_id => opcao_id]
    public ?array $resultadoTeste = null;

    #[Locked]
    public ?int $tentativaAtualId = null;

    // ── Dúvidas ───────────────────────────────────────────────────────────
    #[Validate('required|string|min:10|max:1000')]
    public string $novaDuvida = '';

    public ?int $aulaFiltroId = null;

    // ── Avaliação de reação ───────────────────────────────────────────────
    public bool   $modalAvaliacao  = false;
    public int    $avaliacaoNota   = 0;   // 1–5
    public string $avaliacaoComentario = '';

    // ─────────────────────────────────────────────────────────────────────
    public function mount(int $id): void
    {
        $this->requireAuth();
        $this->treinamentoId = $id;

        $treinamento = Treinamento::findOrFail($id);

        // Garante inscrição
        $inscricao = TreinamentoInscricao::firstOrCreate(
            ['treinamento_id' => $id, 'user_id' => Auth::id()],
            ['status' => 'inscrito', 'progresso' => 0]
        );

        // Atualiza status para em_andamento se necessário
        if ($inscricao->status === 'inscrito') {
            $inscricao->update(['status' => 'em_andamento']);
        }

        // Define aula inicial (respeita ?aula= da URL se já vier preenchido)
        if (!$this->aulaAtualId) {
            $primeira = $treinamento->aulas()->first();
            $this->aulaAtualId = $primeira?->id;
        }
    }

    // ── Computed: curso ───────────────────────────────────────────────────
    #[Computed]
    public function treinamento(): Treinamento
    {
        return Treinamento::with(['aulas', 'teste.questoes.opcoes'])->findOrFail($this->treinamentoId);
    }

    // ── Computed: inscrição ───────────────────────────────────────────────
    #[Computed]
    public function inscricao(): TreinamentoInscricao
    {
        return TreinamentoInscricao::with(['progressoAulas', 'certificado', 'tentativas'])
            ->where('treinamento_id', $this->treinamentoId)
            ->where('user_id', Auth::id())
            ->firstOrFail();
    }

    // ── Computed: aula atual ──────────────────────────────────────────────
    #[Computed]
    public function aulaAtual(): ?TreinamentoAula
    {
        if (!$this->aulaAtualId) return null;
        return TreinamentoAula::find($this->aulaAtualId);
    }

    // ── Computed: progresso por aula ──────────────────────────────────────
    #[Computed]
    public function progressoMap(): array
    {
        return $this->inscricao->progressoAulas
            ->keyBy('aula_id')
            ->toArray();
    }

    // ── Computed: dúvidas ─────────────────────────────────────────────────
    #[Computed]
    public function duvidas()
    {
        return TreinamentoDuvida::with(['usuario', 'respostas.usuario', 'aula'])
            ->where('treinamento_id', $this->treinamentoId)
            ->where('user_id', Auth::id())
            ->when($this->aulaFiltroId, fn($q) => $q->where('aula_id', $this->aulaFiltroId))
            ->latest()
            ->get();
    }

    // ── Selecionar aula ───────────────────────────────────────────────────
    public function selecionarAula(int $aulaId): void
    {
        $this->aulaAtualId = $aulaId;
        $this->aba = 'aulas';
        unset($this->aulaAtual);
    }

    // ── Registrar segundos assistidos (chamado pelo JS) ──────────────────
    public function registrarProgressoVideo(int $aulaId, int $segundosAssistidos): void
    {
        $this->requireAuth();

        TreinamentoAulaProgresso::updateOrCreate(
            ['inscricao_id' => $this->inscricao->id, 'aula_id' => $aulaId],
            ['segundos_assistidos' => $segundosAssistidos]
        );

        unset($this->progressoMap, $this->inscricao);
    }

    // ── Marcar aula como concluída ────────────────────────────────────────
    // Aceita segundosAssistidos opcional para evitar race condition quando o
    // Alpine salva progresso e o usuário clica "Marcar concluída" ao mesmo tempo.
    public function marcarAulaConcluida(int $aulaId, int $segundosAssistidos = 0): void
    {
        $this->requireAuth();

        $aula = TreinamentoAula::findOrFail($aulaId);

        // Salva progresso recebido do frontend antes de validar (resolve race condition)
        if ($segundosAssistidos > 0) {
            TreinamentoAulaProgresso::updateOrCreate(
                ['inscricao_id' => $this->inscricao->id, 'aula_id' => $aulaId],
                ['segundos_assistidos' => $segundosAssistidos]
            );
        }

        // Validar vídeo — só se a flag estiver ativa
        if (($aula->video_url || $aula->video_arquivo) && $aula->exigir_video) {
            $duracaoSegundos = $aula->duracaoSegundos;
            $minimo          = $duracaoSegundos; // 100%

            $prog      = TreinamentoAulaProgresso::where('inscricao_id', $this->inscricao->id)
                             ->where('aula_id', $aulaId)->first();
            $assistido = $prog?->segundos_assistidos ?? 0;

            if ($duracaoSegundos > 0 && $assistido < $minimo) {
                $pct = round($assistido / $duracaoSegundos * 100);
                $this->alertError(
                    'Vídeo não assistido o suficiente',
                    "Você assistiu {$pct}% do vídeo. É necessário assistir o vídeo completo para concluir."
                );
                return;
            }
        }

        TreinamentoAulaProgresso::updateOrCreate(
            ['inscricao_id' => $this->inscricao->id, 'aula_id' => $aulaId],
            ['concluida' => true, 'concluida_em' => now()]
        );

        $this->inscricao->recalcularProgresso();
        $this->tentarEmitirCertificado();

        unset($this->inscricao, $this->progressoMap);
        $this->alertSuccess('Aula concluída!', 'Seu progresso foi salvo.');
    }

    // ── Avançar para próxima aula ─────────────────────────────────────────
    public function proximaAula(): void
    {
        $aulas = $this->treinamento->aulas;
        $idx   = $aulas->search(fn($a) => $a->id === $this->aulaAtualId);

        if ($idx !== false && isset($aulas[$idx + 1])) {
            $this->aulaAtualId = $aulas[$idx + 1]->id;
            unset($this->aulaAtual);
        }
    }

    // ══════════════════════════════════════════════════════════════════════
    // TESTE
    // ══════════════════════════════════════════════════════════════════════

    public function iniciarTeste(): void
    {
        $this->requireAuth();
        $teste = $this->treinamento->teste;
        if (!$teste) return;

        $qtdTentativas = $this->inscricao->tentativas()->where('teste_id', $teste->id)->count();
        if ($qtdTentativas >= $teste->tentativas_maximas) {
            $this->alertError('Limite atingido', "Você atingiu o limite de {$teste->tentativas_maximas} tentativa(s).");
            return;
        }

        $numero = $qtdTentativas + 1;
        $tentativa = TreinamentoTentativa::create([
            'inscricao_id' => $this->inscricao->id,
            'teste_id'     => $teste->id,
            'numero'       => $numero,
            'iniciada_em'  => now(),
        ]);

        $this->tentativaAtualId = $tentativa->id;
        $this->respostas        = [];
        $this->testeAtivo       = true;
        $this->testeEnviado     = false;
        $this->resultadoTeste   = null;
        $this->aba              = 'teste';
        unset($this->inscricao);
    }

    public function responder(int $questaoId, int $opcaoId): void
    {
        $this->respostas[$questaoId] = $opcaoId;
    }

    public function enviarTeste(): void
    {
        $this->requireAuth();

        $teste    = $this->treinamento->teste;
        $tentativa = TreinamentoTentativa::findOrFail($this->tentativaAtualId);

        $totalPontos  = 0;
        $acertos      = 0;
        $totalQuestoes = $teste->questoes->count();

        foreach ($teste->questoes as $questao) {
            $opcaoId  = $this->respostas[$questao->id] ?? null;
            $opcao    = $opcaoId ? TreinamentoQuestaoOpcao::find($opcaoId) : null;
            $correta  = $opcao?->correta ?? false;
            $acertos += $correta ? 1 : 0;
            $totalPontos += $questao->pontos;

            TreinamentoResposta::create([
                'tentativa_id' => $tentativa->id,
                'questao_id'   => $questao->id,
                'opcao_id'     => $opcaoId,
                'correta'      => $correta,
            ]);
        }

        $nota    = $totalQuestoes > 0 ? round(($acertos / $totalQuestoes) * 100, 2) : 0;
        $aprovado = $nota >= $teste->nota_minima;

        $tentativa->update([
            'nota'          => $nota,
            'aprovado'      => $aprovado,
            'finalizada_em' => now(),
        ]);

        if ($aprovado && $this->inscricao->status !== 'concluido') {
            $this->inscricao->update(['status' => 'concluido', 'concluido_em' => now()]);
        } elseif (!$aprovado) {
            $this->inscricao->update(['status' => 'reprovado']);
        }

        $this->tentarEmitirCertificado();

        $this->testeEnviado   = true;
        $this->testeAtivo     = false;
        $this->resultadoTeste = [
            'nota'          => $nota,
            'aprovado'      => $aprovado,
            'acertos'       => $acertos,
            'total'         => $totalQuestoes,
            'nota_minima'   => $teste->nota_minima,
        ];

        unset($this->inscricao);
    }

    // ══════════════════════════════════════════════════════════════════════
    // DÚVIDAS
    // ══════════════════════════════════════════════════════════════════════

    public function enviarDuvida(): void
    {
        $this->requireAuth();
        $this->validate(['novaDuvida' => 'required|string|min:10|max:1000']);

        if (!$this->rateLimit('treinamento-duvida', 5, 300)) return;

        TreinamentoDuvida::create([
            'treinamento_id' => $this->treinamentoId,
            'aula_id'        => $this->aulaAtualId,
            'user_id'        => Auth::id(),
            'pergunta'       => $this->sanitize($this->novaDuvida),
            'status'         => 'aberta',
        ]);

        $this->novaDuvida = '';
        $this->alertSuccess('Dúvida enviada!', 'O instrutor responderá em breve.');
        unset($this->duvidas);
    }

    // ══════════════════════════════════════════════════════════════════════
    // AVALIAÇÃO DE REAÇÃO
    // ══════════════════════════════════════════════════════════════════════

    public function abrirModalAvaliacao(): void
    {
        $inscricao = $this->inscricao;
        if ($inscricao->status !== 'concluido') return;
        if ($inscricao->avaliacaoReacao) { $this->alertInfo('Você já avaliou este curso.'); return; }
        $this->avaliacaoNota       = 0;
        $this->avaliacaoComentario = '';
        $this->modalAvaliacao      = true;
    }

    public function setNota(int $nota): void
    {
        $this->avaliacaoNota = $nota;
    }

    public function salvarAvaliacao(): void
    {
        $this->requireAuth();
        if ($this->avaliacaoNota < 1 || $this->avaliacaoNota > 5) {
            $this->alertError('Selecione uma nota de 1 a 5 estrelas.');
            return;
        }
        $inscricao = $this->inscricao;
        if ($inscricao->avaliacaoReacao) { $this->modalAvaliacao = false; return; }

        TreinamentoAvaliacaoReacao::create([
            'inscricao_id' => $inscricao->id,
            'nota'         => $this->avaliacaoNota,
            'comentario'   => $this->sanitize($this->avaliacaoComentario),
        ]);

        $this->modalAvaliacao = false;
        unset($this->inscricao);
        $this->alertSuccess('Obrigado pela avaliação!');
    }

    // ── Emitir certificado (se elegível) ──────────────────────────────────
    private function tentarEmitirCertificado(): void
    {
        $inscricao = TreinamentoInscricao::with(['certificado', 'tentativas'])
            ->find($this->inscricao->id ?? null);

        if (!$inscricao) return;

        $treinamento = $this->treinamento;
        if (!$treinamento->certificado_habilitado) return;

        // Precisa de certificado e ainda não tem
        if ($inscricao->certificado) return;

        // Verificar progresso de aulas
        $todasConcluidas = $inscricao->progresso >= 100;
        if (!$todasConcluidas) return;

        // Se tem teste, precisa ter aprovado
        if ($treinamento->teste) {
            $aprovado = $inscricao->tentativas()->where('aprovado', true)->exists();
            if (!$aprovado) return;
        }

        // Emitir
        TreinamentoCertificado::create([
            'inscricao_id' => $inscricao->id,
            'codigo'       => strtoupper(Str::random(12)),
            'emitido_em'   => now(),
        ]);
    }

    // ─────────────────────────────────────────────────────────────────────
    public function render()
    {
        return view('livewire.pages.treinamentos.curso')
            ->layout('components.layouts.app', ['title' => 'Curso']);
    }
}
