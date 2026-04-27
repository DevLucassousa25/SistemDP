<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Administradores e RH podem listar todos os usuários.
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->isRhOuDp();
    }

    /**
     * RH/Admin vê qualquer perfil; usuário comum só vê o próprio.
     */
    public function view(User $authUser, User $target): bool
    {
        return $authUser->id === $target->id || $authUser->isRhOuDp();
    }

    /**
     * Apenas RH/Admin pode criar usuários.
     */
    public function create(User $authUser): bool
    {
        return $authUser->isRhOuDp();
    }

    /**
     * RH/Admin edita qualquer usuário; usuário comum não pode editar outros.
     */
    public function update(User $authUser, User $target): bool
    {
        return $authUser->isRhOuDp();
    }

    /**
     * Apenas admin pode excluir. Não pode excluir a si mesmo.
     */
    public function delete(User $authUser, User $target): bool
    {
        return $authUser->isAdmin() && $authUser->id !== $target->id;
    }

    /**
     * Ativar/desativar conta. Não pode agir sobre si mesmo.
     */
    public function toggleActive(User $authUser, User $target): bool
    {
        if ($authUser->id === $target->id) {
            return false;
        }

        return $authUser->isRhOuDp();
    }
}
