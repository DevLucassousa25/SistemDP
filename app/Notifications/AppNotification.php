<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

/**
 * Notificação base do sistema.
 *
 * Todas as notificações persistem no banco via canal `database`.
 * O campo `data` segue o formato:
 *   {
 *     "title":   "Título curto",
 *     "message": "Mensagem descritiva",
 *     "icon":    "nome-do-lucide-icon",
 *     "color":   "blue|green|red|amber|violet|rose|indigo|teal",
 *     "url":     "/rota-do-modulo",
 *     "module":  "slug-do-modulo"
 *   }
 */
abstract class AppNotification extends Notification
{
    use Queueable;

    /** Retorna os canais de entrega. */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /** Cada subclasse implementa os dados da notificação. */
    abstract public function toArray(object $notifiable): array;
}
