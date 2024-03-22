<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Ejercicio;

class EjercicioPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

        /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['MASTER', 'ADMIN','MANZANAL','C DISTRITAL','C ENLACE DE MANZANA']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Ejercicio $ejercicio): bool
    {
        return $user->hasRole(['MASTER', 'ADMIN','MANZANAL','C DISTRITAL','C ENLACE MANZANA']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return false;
        //return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Ejercicio $ejercicio): bool
    {
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Ejercicio $ejercicio): bool
    {
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Ejercicio $ejercicio): bool
    {
        return $user->hasRole(['MASTER']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Ejercicio $ejercicio): bool
    {
        return $user->hasRole(['MASTER']);
    }
}
