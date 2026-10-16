<?php

namespace App\Policies;

use App\Models\Patient;
use App\Models\User;

/**
 * Isolamento multi-tenant: o profissional só enxerga os próprios pacientes.
 *
 * Camada 3 da defesa em profundidade — o Global Scope já filtra, mas aqui a
 * autorização barra mesmo que alguém consiga montar a query sem o escopo.
 */
class PatientPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Patient $patient): bool
    {
        return $user->id === $patient->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, Patient $patient): bool
    {
        return $user->id === $patient->user_id;
    }

    public function delete(User $user, Patient $patient): bool
    {
        return $user->id === $patient->user_id;
    }
}
