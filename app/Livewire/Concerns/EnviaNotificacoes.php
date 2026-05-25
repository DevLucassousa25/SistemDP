<?php

namespace App\Livewire\Concerns;

use App\Models\User;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;

/**
 * Trait para Livewire components que precisam enviar notificações.
 *
 * Usa o sistema de database notifications do Laravel para persistir
 * no banco, e $this->dispatch() do Livewire para toast imediato no
 * browser do usuário que executou a ação.
 *
 * REGRA: o usuário que executou a ação NUNCA recebe a notificação
 * destinada a outros — ele já sabe o que fez.
 */
trait EnviaNotificacoes
{
    // ── Helpers de destinatários ──────────────────────────────────────

    /** Todos os usuários ativos com perfil RH ou Admin, exceto o atual. */
    protected function rhEAdmins(): Collection
    {
        return User::whereHas('accessProfile', fn ($q) =>
            $q->whereIn('slug', ['administrator', 'hr'])
        )
        ->where('is_active', true)
        ->where('id', '!=', Auth::id() ?? 0)   // ← nunca notifica quem disparou
        ->get();
    }

    // ── Salvar no banco ───────────────────────────────────────────────

    /**
     * Notifica um único usuário.
     * Ignora silenciosamente se for o próprio usuário autenticado.
     */
    protected function notificarUsuario(User $usuario, Notification $notificacao): void
    {
        if ($usuario->id === Auth::id()) return;
        $usuario->notify($notificacao);
    }

    /** Notifica todos os usuários RH/Admin (exceto o atual). */
    protected function notificarRhAdmin(Notification $notificacao): void
    {
        $this->rhEAdmins()->each(fn ($u) => $u->notify($notificacao));
    }

    /**
     * Notifica uma coleção de usuários (exceto o atual).
     * Retorna o número de usuários efetivamente notificados.
     */
    protected function notificarLista(Collection $usuarios, Notification $notificacao): int
    {
        $destinatarios = $usuarios->reject(fn ($u) => $u->id === Auth::id());
        $destinatarios->each(fn ($u) => $u->notify($notificacao));
        return $destinatarios->count();
    }

    /**
     * Notifica todos os usuários ativos (exceto o atual).
     * Retorna o número de usuários efetivamente notificados.
     */
    protected function notificarTodosAtivos(Notification $notificacao): int
    {
        $usuarios = User::where('is_active', true)
            ->where('id', '!=', Auth::id() ?? 0)
            ->get();
        $usuarios->each(fn ($u) => $u->notify($notificacao));
        return $usuarios->count();
    }

    // ── Toast imediato via Livewire ───────────────────────────────────

    /**
     * Dispara um toast imediato no browser do usuário atual.
     * Capturado pelo Alpine.js em app.blade.php via x-on:nova-notificacao.window.
     */
    protected function toastNotif(
        string $title,
        string $message,
        string $icon  = 'bell',
        string $color = 'blue',
        string $url   = ''
    ): void {
        $this->dispatch('nova-notificacao',
            title:   $title,
            message: $message,
            icon:    $icon,
            color:   $color,
            url:     $url,
        );
    }
}
