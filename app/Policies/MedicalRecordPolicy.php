<?php

namespace App\Policies;

use App\Models\MedicalRecord;
use App\Models\User;

/**
 * Isolamento multi-tenant dos prontuários.
 */
class MedicalRecordPolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->id === $medicalRecord->user_id;
    }

    public function create(User $user): bool
    {
        return true;
    }

    public function update(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->id === $medicalRecord->user_id;
    }

    public function delete(User $user, MedicalRecord $medicalRecord): bool
    {
        return $user->id === $medicalRecord->user_id;
    }
}
