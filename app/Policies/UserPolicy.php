<?php

namespace App\Policies;

use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
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
    public function view(User $user, User $model): bool
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

                // $user->hasPermissionTo('Create User');

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        // Un usuario no puede editar a otro usuario con rol 'MASTER' a menos que también tenga ese rol
        if ($model->hasRole('MASTER') && !$user->hasRole('MASTER')) {
            return false;
        }
    
        // Un administrador solo puede editar su propio perfil, a menos que sea un 'MASTER'
        if ($user->hasRole('ADMIN') && !$user->is($model) && !$user->hasRole('MASTER')) {
            return false;
        }
    
        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        if ($model->hasRole('MASTER') && !$user->hasRole('MASTER')) {
            return false;
        }

        // Un administrador solo puede editar su propio perfil, a menos que sea un 'MASTER'
        if ($user->hasRole('ADMIN') && !$user->is($model) && !$user->hasRole('MASTER')) {
            return false;
        }
    
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
        return $user->hasRole(['MASTER']);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
        return $user->hasRole(['MASTER']);
    }
}
