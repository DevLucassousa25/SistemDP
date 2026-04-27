<?php

namespace App\Policies;

use App\Models\room as Room;
use App\Models\User;

class RoomPolicy
{
    /**
     * Qualquer usuário autenticado pode listar/ver salas.
     */
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Room $room): bool
    {
        return true;
    }

    /**
     * Apenas RH/Admin pode criar, editar ou excluir salas.
     */
    public function create(User $user): bool
    {
        return $user->isRhOuDp();
    }

    public function update(User $user, Room $room): bool
    {
        return $user->isRhOuDp();
    }

    public function delete(User $user, Room $room): bool
    {
        return $user->isRhOuDp();
    }

    /**
     * Alterar status (disponível/manutenção) exige RH/Admin.
     */
    public function changeStatus(User $user, Room $room): bool
    {
        return $user->isRhOuDp();
    }
}
