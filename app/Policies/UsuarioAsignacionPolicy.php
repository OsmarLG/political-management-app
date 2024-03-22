<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Manzana;
use App\Models\Seccion;
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
        // Los usuarios con roles 'MASTER' y 'ADMIN' siempre pueden ver asignaciones
        if ($user->hasRole(['MASTER', 'ADMIN'])) {
            return true;
        }
    
        // Los usuarios con roles 'C DISTRITAL' o 'C ENLACE DE MANZANA' deben tener una asignación para ver cualquier asignación
        if ($user->hasRole(['C DISTRITAL', 'C ENLACE DE MANZANA'])) {
            // Verifica si el usuario tiene una asignación asociada
            $hasAsignacion = $user->Asignacion()->exists();
    
            return $hasAsignacion;
        }
    
        // Por defecto, los usuarios no pueden ver asignaciones
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, UsuarioAsignacion $usuarioAsignacion): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL', 'C ENLACE DE MANZANA']);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
        return $user->hasRole(['MASTER', 'ADMIN', 'C DISTRITAL', 'C ENLACE DE MANZANA']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, UsuarioAsignacion $usuarioAsignacion): bool
    {
       if($user->hasRole('ADMIN') || $user->hasRole('MASTER')){
            return true;
        }

        if($user->hasRole('C ENLACE DE MANZANA')){

            $seccionalAsignacion = $user->Asignacion()->where('modelo', 'Seccion')->first();
            $seccion_id = $seccionalAsignacion ? $seccionalAsignacion->id_modelo : null;
            
            if ($usuarioAsignacion->user->hasRole('MANZANAL')) {
                $manzanalAsignacion = $usuarioAsignacion->user->Asignacion()->where('modelo', 'Manzana')->first();
                $manzanalAssignedSectionId = $manzanalAsignacion ? $manzanalAsignacion->asignable->seccion_id : null;
                if($manzanalAssignedSectionId == $seccion_id){
                    return true;
                }
            }
        }

        // Los roles 'MASTER' y 'ADMIN' pueden eliminar cualquier asignación
        if ($user->hasRole(['MASTER', 'ADMIN'])) {
            return true;
        }

        // C DISTRITAL no puede eliminar su propia asignación ni la de otros C DISTRITALES
        if ($user->hasRole('C DISTRITAL')) {
            if ($usuarioAsignacion->user_id == $user->id) {
                return false; // No puede eliminar su propia asignación
            }

            $assignedZonal = $usuarioAsignacion->user; // Usuario asignado en la asignación
            // Comprobar si la asignación pertenece a otro C DISTRITAL
            if ($assignedZonal->hasRole('C DISTRITAL')) {
                return false; // No puede eliminar la asignación de otro C DISTRITAL
            }

            // Para C ENLACE DE MANZANA y MANZANAL, verificar que la sección/manzana esté dentro de su zona
            $asignacion = UsuarioAsignacion::where('user_id', $user->id)->get()->first();

            if ($usuarioAsignacion->modelo == 'Seccion') {
                $seccionId = $usuarioAsignacion->id_modelo;
                $seccion = Seccion::find($seccionId);
                if ($seccion){
                    if ($seccion->zona_id != $asignacion->id_modelo){
                        return false;
                    } else {
                        return true;
                    }
                }
            }

            if ($usuarioAsignacion->modelo == 'Manzana') {
                $manzanaId = $usuarioAsignacion->id_modelo;
                $manzana = Manzana::find($manzanaId);
                $seccion = $manzana->seccion ?? null;
                if (!$seccion || $seccion->zona_id != $asignacion->id_modelo) {
                    return false; // La manzana no está en ninguna sección de la zona del C DISTRITAL
                } else return true;
            }
        }

        // C ENLACE DE MANZANA y MANZANAL no pueden eliminar asignaciones por defecto, pero puedes agregar reglas específicas si es necesario

        return false; 
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, UsuarioAsignacion $usuarioAsignacion): bool
    {
        if($user->hasRole('ADMIN') || $user->hasRole('MASTER')){
            return true;
        }

        if($user->hasRole('C ENLACE DE MANZANA')){
            $seccionalAsignacion = $user->Asignacion()->where('modelo', 'Seccion')->first();
            $seccion_id = $seccionalAsignacion ? $seccionalAsignacion->id_modelo : null;

            if ($usuarioAsignacion->user->hasRole('MANZANAL')) {
                $manzanalAsignacion = $usuarioAsignacion->user->Asignacion()->where('modelo', 'Manzana')->first();
                $manzanalAssignedSectionId = $manzanalAsignacion ? $manzanalAsignacion->asignable->seccion_id : null;
                if($manzanalAssignedSectionId == $seccion_id){
                    return true;
                }
            }
        }



        // Los roles 'MASTER' y 'ADMIN' pueden eliminar cualquier asignación
        if ($user->hasRole(['MASTER', 'ADMIN'])) {
            return true;
        }

        // C DISTRITAL no puede eliminar su propia asignación ni la de otros C DISTRITALES
        if ($user->hasRole('C DISTRITAL')) {
            if ($usuarioAsignacion->user_id == $user->id) {
                return false; // No puede eliminar su propia asignación
            }

            $assignedZonal = $usuarioAsignacion->user; // Usuario asignado en la asignación
            // Comprobar si la asignación pertenece a otro C DISTRITAL
            if ($assignedZonal->hasRole('C DISTRITAL')) {
                return false; // No puede eliminar la asignación de otro C DISTRITAL
            }

            // Para C ENLACE DE MANZANA y MANZANAL, verificar que la sección/manzana esté dentro de su zona
            $asignacion = UsuarioAsignacion::where('user_id', $user->id)->get()->first();

            if ($usuarioAsignacion->modelo == 'Seccion') {
                $seccionId = $usuarioAsignacion->id_modelo;
                $seccion = Seccion::find($seccionId);
                if ($seccion){
                    if ($seccion->zona_id != $asignacion->id_modelo){
                        return false;
                    } else {
                        return true;
                    }
                }
            }

            if ($usuarioAsignacion->modelo == 'Manzana') {
                $manzanaId = $usuarioAsignacion->id_modelo;
                $manzana = Manzana::find($manzanaId);
                $seccion = $manzana->seccion ?? null;
                if (!$seccion || $seccion->zona_id != $asignacion->id_modelo) {
                    return false; // La manzana no está en ninguna sección de la zona del C DISTRITAL
                } else return true;
            }
        }

        // C ENLACE DE MANZANA y MANZANAL no pueden eliminar asignaciones por defecto, pero puedes agregar reglas específicas si es necesario

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
