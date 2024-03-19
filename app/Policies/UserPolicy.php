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
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL','C ENLACE DE MANZANA']);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL','C ENLACE DE MANZANA']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL','C ENLACE DE MANZANA']);

                // $user->hasPermissionTo('Create User');

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {

        if ($user->id === $model->id) {
            return true;
        }
        
        // Un usuario no puede editar a otro usuario con rol 'MASTER' a menos que también tenga ese rol
        if ($model->hasRole('MASTER') && !$user->hasRole('MASTER')) {
            return false;
        }
        
        // Un administrador puede editar su propio perfil o si tiene rol 'MASTER'
        if ($user->hasRole('ADMIN') && !$user->hasRole('MASTER')) {
            // Permitir la edición si es su propio perfil o si el otro usuario no es 'MASTER' o 'ADMIN'
            return $user->is($model) || (!$model->hasRole('MASTER') && !$model->hasRole('ADMIN'));
        }
    
        // Para usuarios con rol 'C DISTRITAL' asegurarse de que solo puedan editar usuarios de niveles más bajos.
        if ($user->hasRole('C DISTRITAL')) {
            // Verificar que el modelo no tenga roles de igual o mayor jerarquía.
            if ($model->hasRole('MASTER') || $model->hasRole('ADMIN') || $model->hasRole('C DISTRITAL')) {
                return false;
            }

            // Obtener la zona asignada al usuario 'C DISTRITAL'.
            $zonalAsignacion = $user->Asignacion()->where('modelo', 'Zona')->first();
            $zonalAssignedZoneId = $zonalAsignacion ? $zonalAsignacion->id_modelo : null;

            // Para 'C ENLACE DE MANZANA', verificar que la sección asignada esté en la misma zona que el 'C DISTRITAL'.
            if ($model->hasRole('C ENLACE DE MANZANA')) {
                $seccionalAsignacion = $model->Asignacion()->where('modelo', 'Seccion')->first();
                $seccionalAssignedZoneId = $seccionalAsignacion ? $seccionalAsignacion->asignable->zona_id : null;

                if ($zonalAssignedZoneId !== $seccionalAssignedZoneId) {
                    return false;
                }
            }

            // Para 'MANZANAL', verificar que la manzana asignada esté en una sección de la misma zona que el 'C DISTRITAL'.
            if ($model->hasRole('MANZANAL')) {
                $manzanalAsignacion = $model->Asignacion()->where('modelo', 'Manzana')->first();
                $manzanalAssignedSectionId = $manzanalAsignacion ? $manzanalAsignacion->asignable->seccion_id : null;
                $manzanalAssignedZoneId = $manzanalAsignacion ? $manzanalAsignacion->asignable->seccion->zona_id : null;

                if ($zonalAssignedZoneId !== $manzanalAssignedZoneId) {
                    return false;
                }
            }

            //
        }

        if($user->hasRole('C ENLACE DE MANZANA')){
               // Verificar que el modelo no tenga roles de igual o mayor jerarquía.
            if ($model->hasRole('MASTER') || $model->hasRole('ADMIN') || $model->hasRole('C DISTRITAL')|| $model->hasRole('C ENLACE DE MANZANA')) {
                return false;
            }

            // Obtener la zona asignada al usuario 'C ENLACE DE MANZANA'.
            $seccionalAsignacion = $user->Asignacion()->where('modelo', 'Seccion')->first();
            $seccionalAssignedZoneId = $seccionalAsignacion ? $seccionalAsignacion->id_modelo : null;
       

            // Para 'MANZANAL', verificar que la manzana asignada esté en una sección de la misma zona que el 'C ENLACE DE MANZANA'.
            if ($model->hasRole('MANZANAL')) {
            $manzanalAsignacion = $model->Asignacion()->where('modelo', 'Manzana')->first();
            $manzanalAssignedSectionId = $manzanalAsignacion ? $manzanalAsignacion->asignable->seccion_id : null;
            $manzanalAssignedZoneId = $manzanalAsignacion ? $manzanalAsignacion->asignable->seccion->zona_id : null;

            if ($seccionalAssignedZoneId !== $manzanalAssignedSectionId) {
                return false;
            }
        }

        }

        return true;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        return $user->hasRole(['MASTER']);
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
