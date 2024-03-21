<?php

namespace App\Policies;

use App\Models\Barda;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class BardaPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Barda $barda): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Barda $barda): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Barda $barda): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Barda $barda): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Barda $barda): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN']);
    }
}
