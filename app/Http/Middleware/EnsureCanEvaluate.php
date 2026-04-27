<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Garante que apenas Gerentes, RH e Administradores
 * possam acessar o módulo de Avaliação de Desempenho.
 *
 * Uso nas rotas:
 *   Route::middleware('can_evaluate')->group(function () { ... });
 */
class EnsureCanEvaluate
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = Auth::user();

        if (! $user || ! $user->podeGerenciarPesquisas()) {
            abort(403, 'Acesso restrito a Gerentes, RH e Administradores.');
        }

        return $next($request);
    }
}
