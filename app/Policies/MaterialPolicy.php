<?php

namespace App\Policies;

use App\Models\Material;
use App\Models\User;

class MaterialPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return null;
    }

    /**
     * Determine whether the user can view any materials.
     */
    public function viewAny(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can view the material.
     */
    public function view(User $user, Material $material): bool
    {
        if ($user->isTeacher()) {
            return true;
        }

        if ($user->isStudent()) {
            if (! $material->is_published) {
                return false;
            }

            if ($material->available_from !== null && now()->lt($material->available_from)) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * Determine whether the user can create materials.
     */
    public function create(User $user): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can update the material.
     */
    public function update(User $user, Material $material): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can delete the material.
     */
    public function delete(User $user, Material $material): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can restore the material.
     */
    public function restore(User $user, Material $material): bool
    {
        return $user->isTeacher();
    }

    /**
     * Determine whether the user can permanently delete the material.
     */
    public function forceDelete(User $user, Material $material): bool
    {
        return $user->isTeacher();
    }
}
