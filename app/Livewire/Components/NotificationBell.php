<?php

namespace App\Livewire\Components;

use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Computed;
use Livewire\Component;

class NotificationBell extends Component
{
    /** IDs das notificações já exibidas como toast nesta sessão (evita duplicatas). */
    public array $toastados = [];

    // ── Computed ──────────────────────────────────────────────────────

    #[Computed]
    public function naoLidas(): int
    {
        return Auth::user()?->unreadNotifications()->count() ?? 0;
    }

    #[Computed]
    public function notificacoes()
    {
        return Auth::user()?->notifications()->latest()->limit(25)->get() ?? collect();
    }

    // ── Polling ───────────────────────────────────────────────────────

    /**
     * Chamado automaticamente a cada 15 s pelo wire:poll.
     * Verifica se existem notificações novas e dispara toast para cada uma.
     */
    public function verificarNovas(): void
    {
        if (!Auth::check()) return;

        $novas = Auth::user()
            ->unreadNotifications()
            ->whereNotIn('id', $this->toastados)
            ->latest()
            ->limit(5)
            ->get();

        foreach ($novas as $n) {
            $this->toastados[] = $n->id;
            $this->dispatch('nova-notificacao', ...$n->data);
        }

        // Limita o array para não crescer indefinidamente
        if (count($this->toastados) > 50) {
            $this->toastados = array_slice($this->toastados, -50);
        }

        unset($this->naoLidas, $this->notificacoes);
    }

    // ── Ações ─────────────────────────────────────────────────────────

    /** Marca uma notificação como lida. */
    public function marcarLida(string $id): void
    {
        Auth::user()?->notifications()->where('id', $id)->first()?->markAsRead();
        unset($this->naoLidas, $this->notificacoes);
    }

    /** Marca todas as notificações como lidas. */
    public function marcarTodasLidas(): void
    {
        Auth::user()?->unreadNotifications()->update(['read_at' => now()]);
        unset($this->naoLidas, $this->notificacoes);
    }

    /** Exclui uma notificação. */
    public function excluir(string $id): void
    {
        Auth::user()?->notifications()->where('id', $id)->delete();
        unset($this->naoLidas, $this->notificacoes);
    }

    /** Exclui todas as notificações lidas. */
    public function limparLidas(): void
    {
        Auth::user()?->notifications()->whereNotNull('read_at')->delete();
        unset($this->naoLidas, $this->notificacoes);
    }

    public function render()
    {
        return view('livewire.components.notification-bell');
    }
}
