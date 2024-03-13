<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class RolePolicy
{
    public function before(User $user, $ability)
    {
        // Si el usuario tiene el rol 'MASTER', puede realizar cualquier acción
        if ($user->hasRole('MASTER')) {
            return true;
        }
    }
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Role $role): bool
    {
        //
        if ($role->name === 'ADMIN' || $role->name === 'MASTER') {
            return false;
        }
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        //
        if ($role->name === 'ADMIN' || $role->name === 'MASTER') {
            return false;
        }
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        //
        return $user->hasRole(['MASTER']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        //
        return $user->hasRole(['MASTER']);
    }
}
