<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UsuarioAsignacion;
use Illuminate\Auth\Access\Response;

class UsuarioAsignacionPolicy
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
        return $user->hasRole(['MASTER', 'ADMIN', 'ZONAL', 'SECCIONAL']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UsuarioAsignacion $usuarioAsignacion): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'ZONAL', 'SECCIONAL']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'ZONAL', 'SECCIONAL']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UsuarioAsignacion $usuarioAsignacion): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UsuarioAsignacion $usuarioAsignacion): bool
    {
        if($user->hasRole('ADMIN') || $user->hasRole('MASTER')){
            return true;
        }

        if($user->hasRole('SECCIONAL')){
            $seccionalAsignacion = $user->Asignacion()->where('modelo', 'Seccion')->first();
            $seccion_id = $seccionalAsignacion ? $seccionalAsignacion->asignable->zona_id : null;

            if ($usuarioAsignacion->user->hasRole('MANZANAL')) {
                $manzanalAsignacion = $usuarioAsignacion->user->Asignacion()->where('modelo', 'Manzana')->first();
                $manzanalAssignedSectionId = $manzanalAsignacion ? $manzanalAsignacion->asignable->seccion_id : null;
                if($manzanalAssignedSectionId == $seccion_id){
                    return true;
                }
            }
        }


        return false;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, UsuarioAsignacion $usuarioAsignacion): bool
    {
        //
        return $user->hasRole(['MASTER']);
    }
    
    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, UsuarioAsignacion $usuarioAsignacion): bool
    {
        //
        return $user->hasRole(['MASTER']);
    }
}
