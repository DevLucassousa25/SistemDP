<?php

namespace App\Listeners;

use App\Events\AvaliacaoSubmetidaEvent;
use App\Events\DpiPlanoAtualizacaoEvent;
use App\Events\FeedbackRecebidoEvent;
use App\Events\FeedInteracaoEvent;
use App\Events\NovoCurriculoEvent;
use App\Events\OuvidoriaNovaMensagemEvent;
use App\Events\PesquisaRespondidaEvent;
use App\Events\PublicacaoObrigatoriaEvent;
use App\Events\ReuniaoAgendadaEvent;
use App\Events\TarefaAtribuidaEvent;
use App\Events\TesteOnlineConcluidoEvent;
use App\Models\User;
use App\Notifications\AvaliacaoSubmetidaNotification;
use App\Notifications\DpiPlanoAtualizacaoNotification;
use App\Notifications\FeedbackRecebidoNotification;
use App\Notifications\FeedInteracaoNotification;
use App\Notifications\NovoCurriculoNotification;
use App\Notifications\OuvidoriaNovaMensagemNotification;
use App\Notifications\PesquisaRespondidaNotification;
use App\Notifications\PublicacaoObrigatoriaNotification;
use App\Notifications\ReuniaoAgendadaNotification;
use App\Notifications\TarefaAtribuidaNotification;
use App\Notifications\TesteOnlineConcluidoNotification;
use Illuminate\Contracts\Queue\ShouldQueue;

/**
 * Listener central de notificações.
 * Cada evento do sistema entra aqui e é roteado para a notificação correta.
 * Implementa ShouldQueue para não bloquear a request do usuário.
 */
class EnviarNotificacaoListener implements ShouldQueue
{
    public string $queue = 'notifications';

    // ── Helpers ──────────────────────────────────────────────────────

    /** Retorna todos os usuários com perfil RH ou Admin. */
    private function rhEAdmins(): \Illuminate\Database\Eloquent\Collection
    {
        return User::whereHas('accessProfile', fn ($q) =>
            $q->whereIn('slug', ['administrator', 'hr'])
        )->where('is_active', true)->get();
    }

    // ── Handlers ─────────────────────────────────────────────────────

    public function handleNovoCurriculo(NovoCurriculoEvent $event): void
    {
        $notif = new NovoCurriculoNotification($event->curriculo);
        $this->rhEAdmins()->each(fn ($u) => $u->notify($notif));
    }

    public function handleTesteOnlineConcluido(TesteOnlineConcluidoEvent $event): void
    {
        $notif = new TesteOnlineConcluidoNotification($event->tentativa);
        $this->rhEAdmins()->each(fn ($u) => $u->notify($notif));
    }

    public function handleOuvidoriaNovaMensagem(OuvidoriaNovaMensagemEvent $event): void
    {
        $notif = new OuvidoriaNovaMensagemNotification($event->manifestacao);
        $this->rhEAdmins()->each(fn ($u) => $u->notify($notif));
    }

    public function handleReuniaoAgendada(ReuniaoAgendadaEvent $event): void
    {
        $notif = new ReuniaoAgendadaNotification($event->reuniao);
        $event->participantes->each(fn ($u) => $u->notify($notif));
    }

    public function handleFeedbackRecebido(FeedbackRecebidoEvent $event): void
    {
        $event->destinatario->notify(
            new FeedbackRecebidoNotification($event->remetente, $event->tipo)
        );
    }

    public function handlePesquisaRespondida(PesquisaRespondidaEvent $event): void
    {
        $notif = new PesquisaRespondidaNotification($event->pesquisa);
        // Notifica o criador da pesquisa e os admins/RH
        $this->rhEAdmins()->each(fn ($u) => $u->notify($notif));
    }

    public function handleAvaliacaoSubmetida(AvaliacaoSubmetidaEvent $event): void
    {
        $notif = new AvaliacaoSubmetidaNotification($event->avaliado, $event->ciclo);
        $this->rhEAdmins()->each(fn ($u) => $u->notify($notif));
    }

    public function handleTarefaAtribuida(TarefaAtribuidaEvent $event): void
    {
        $event->destinatario->notify(
            new TarefaAtribuidaNotification($event->tarefa, $event->atribuidaPor)
        );
    }

    public function handleDpiPlanoAtualizacao(DpiPlanoAtualizacaoEvent $event): void
    {
        $event->destinatario->notify(
            new DpiPlanoAtualizacaoNotification($event->plano, $event->acao)
        );
    }

    public function handlePublicacaoObrigatoria(PublicacaoObrigatoriaEvent $event): void
    {
        $notif = new PublicacaoObrigatoriaNotification($event->post);
        // Notifica todos os usuários ativos
        User::where('is_active', true)->get()->each(fn ($u) => $u->notify($notif));
    }

    public function handleFeedInteracao(FeedInteracaoEvent $event): void
    {
        // Não notifica o próprio autor da interação
        if ($event->destinatario->id !== auth()->id()) {
            $event->destinatario->notify(
                new FeedInteracaoNotification($event->post, $event->atorNome, $event->tipo)
            );
        }
    }
}
