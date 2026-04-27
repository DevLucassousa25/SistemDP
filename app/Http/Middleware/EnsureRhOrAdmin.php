<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que apenas usuários com perfil Administrador ou RH/DP
 * possam acessar as rotas protegidas.
 *
 * Uso nas rotas:
 *   Route::middleware('rh_or_admin')->group(function () { ... });
 */
class EnsureRhOrAdmin
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->isRhOuDp()) {
            abort(403, 'Acesso restrito a Administradores e RH/DP.');
        }

        return $next($request);
    }
}
