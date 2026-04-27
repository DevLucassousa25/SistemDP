<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\RateLimiter;
use Livewire\Component;

/**
 * Classe base para todos os componentes Livewire do sistema.
 *
 * Fornece helpers prontos de segurança. Todo novo componente DEVE estender
 * esta classe em vez de Component diretamente.
 *
 * Checklist obrigatório para cada novo método público:
 *
 *  [1] AUTENTICAÇÃO  — o usuário está autenticado?
 *  [2] AUTORIZAÇÃO   — ele tem permissão para ESTE recurso específico?
 *  [3] IDOR          — o recurso pertence ao usuário (ou ele é RH/Admin)?
 *  [4] LOCKED        — propriedades server-side têm #[Locked]?
 *  [5] VALIDAÇÃO     — todos os inputs passam por $this->validate()?
 *  [6] SANITIZAÇÃO   — campos de texto livre passam por $this->sanitize()?
 *  [7] RATE LIMIT    — operações sensíveis têm $this->rateLimit()?
 *  [8] DUPLA CHECAGEM — autorização verificada na ABERTURA e na EXECUÇÃO?
 */
abstract class SecureComponent extends Component
{
    // ──────────────────────────────────────────────────────────────────────────
    // AUTENTICAÇÃO
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Garante que existe um usuário autenticado.
     * Aborta com 401 se não houver sessão ativa.
     */
    protected function requireAuth(): void
    {
        if (! Auth::check()) {
            abort(401, 'Autenticação necessária.');
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // AUTORIZAÇÃO POR PERFIL
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Exige que o usuário seja Administrador.
     */
    protected function requireAdmin(): void
    {
        if (! Auth::user()?->isAdmin()) {
            abort(403, 'Apenas administradores podem realizar esta ação.');
        }
    }

    /**
     * Exige que o usuário seja RH ou Administrador.
     */
    protected function requireRhOrAdmin(): void
    {
        if (! Auth::user()?->isRhOuDp()) {
            abort(403, 'Apenas RH/Admin podem realizar esta ação.');
        }
    }

    /**
     * Exige que o usuário possua um dos slugs de perfil informados.
     *
     * Exemplo: $this->requireRole(['administrator', 'hr', 'manager'])
     */
    protected function requireRole(string|array $roles): void
    {
        $slug = Auth::user()?->accessProfile?->slug;

        if (! in_array($slug, (array) $roles, strict: true)) {
            abort(403, 'Perfil de acesso insuficiente para esta ação.');
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // IDOR — PROTEÇÃO DE RECURSOS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Garante que o recurso pertence ao usuário autenticado,
     * ou que ele é RH/Admin (que vê tudo).
     *
     * @param  int|string  $ownerId  ID do dono do recurso
     */
    protected function requireOwnerOrRhAdmin(int|string $ownerId): void
    {
        $user = Auth::user();

        if ($user->isRhOuDp()) {
            return; // RH/Admin acessa qualquer recurso
        }

        if ((string) $user->id !== (string) $ownerId) {
            abort(403, 'Você não tem permissão para acessar este recurso.');
        }
    }

    /**
     * Verifica uma policy do Laravel.
     *
     * Exemplo: $this->authorizePolicy('update', $model)
     */
    protected function authorizePolicy(string $ability, mixed $model): void
    {
        if (Gate::denies($ability, $model)) {
            abort(403, 'Ação não autorizada.');
        }
    }

    // ──────────────────────────────────────────────────────────────────────────
    // RATE LIMITING
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Aplica rate limiting a uma operação sensível.
     * Retorna false e adiciona erro se o limite foi atingido.
     *
     * @param  string  $action       Identificador da ação (ex: 'login', 'criar-manifestacao')
     * @param  int     $maxAttempts  Máximo de tentativas
     * @param  int     $decaySeconds Janela de tempo em segundos
     * @return bool    true = pode prosseguir | false = bloqueado
     */
    protected function rateLimit(
        string $action,
        int $maxAttempts = 10,
        int $decaySeconds = 60
    ): bool {
        $key = $action . '|' . (Auth::id() ?? request()->ip());

        if (RateLimiter::tooManyAttempts($key, $maxAttempts)) {
            $seconds = RateLimiter::availableIn($key);
            $this->dispatch('openAlert',
                title: 'Muitas tentativas',
                description: "Aguarde {$seconds} segundo(s) antes de tentar novamente.",
                type: 'error'
            );
            return false;
        }

        RateLimiter::hit($key, $decaySeconds);
        return true;
    }

    /**
     * Limpa o rate limiter após uma operação bem-sucedida.
     */
    protected function clearRateLimit(string $action): void
    {
        $key = $action . '|' . (Auth::id() ?? request()->ip());
        RateLimiter::clear($key);
    }

    // ──────────────────────────────────────────────────────────────────────────
    // SANITIZAÇÃO — XSS
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Remove tags HTML de uma string (anti-XSS para campos de texto livre).
     */
    protected function sanitize(?string $value): string
    {
        return strip_tags(trim((string) $value));
    }

    /**
     * Sanitiza múltiplos campos de um array associativo.
     *
     * Exemplo: $data = $this->sanitizeFields($data, ['name', 'position'])
     */
    protected function sanitizeFields(array $data, array $fields): array
    {
        foreach ($fields as $field) {
            if (array_key_exists($field, $data)) {
                $data[$field] = $this->sanitize($data[$field]);
            }
        }
        return $data;
    }

    // ──────────────────────────────────────────────────────────────────────────
    // HELPERS DE RESPOSTA
    // ──────────────────────────────────────────────────────────────────────────

    /**
     * Dispara alerta de sucesso padronizado.
     */
    protected function alertSuccess(string $title, string $description = ''): void
    {
        $this->dispatch('openAlert', title: $title, description: $description, type: 'success');
    }

    /**
     * Dispara alerta de erro padronizado.
     */
    protected function alertError(string $title, string $description = ''): void
    {
        $this->dispatch('openAlert', title: $title, description: $description, type: 'error');
    }

    /**
     * Dispara alerta informativo padronizado.
     */
    protected function alertInfo(string $title, string $description = ''): void
    {
        $this->dispatch('openAlert', title: $title, description: $description, type: 'info');
    }
}
