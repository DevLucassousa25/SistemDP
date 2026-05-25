<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Bloqueia o acesso de usuários com perfil Administrador
 * a rotas operacionais (Tarefas, Avaliações, Meu Time, Feedback).
 *
 * Uso nas rotas:
 *   Route::middleware('not_admin')->group(function () { ... });
 */
class EnsureNotAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if ($user && $user->isAdmin()) {
            abort(403, 'Esta área não está disponível para o perfil Administrador.');
        }

        return $next($request);
    }
}
