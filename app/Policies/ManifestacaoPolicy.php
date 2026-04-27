<?php

namespace App\Policies;

use App\Models\Manifestacao;
use App\Models\User;

class ManifestacaoPolicy
{
    /**
     * RH/Admin vê todas; usuário comum só vê as próprias.
     */
    public function view(User $user, Manifestacao $manifestacao): bool
    {
        if ($user->isRhOuDp()) {
            return true;
        }

        // Manifestação anônima: só RH/Admin pode ver o autor real
        // O próprio autor pode ver a manifestação pelo user_id, mesmo anônima
        return $manifestacao->user_id === $user->id;
    }

    /**
     * Qualquer usuário autenticado pode criar manifestações.
     */
    public function create(User $user): bool
    {
        return true;
    }

    /**
     * Apenas RH/Admin pode responder ou atualizar status.
     */
    public function update(User $user, Manifestacao $manifestacao): bool
    {
        return $user->isRhOuDp();
    }

    /**
     * Apenas admin pode excluir definitivamente.
     */
    public function delete(User $user, Manifestacao $manifestacao): bool
    {
        return $user->isAdmin();
    }
}
