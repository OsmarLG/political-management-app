<?php

namespace App\Filament\Widgets\CEN;

use App\Models\Ejercicio;
use App\Models\User;
use App\Models\Manzana;
use App\Models\Seccion;
use App\Models\UsuarioAsignacion;
use Filament\Widgets\ChartWidget;

class EjercicioPorUsuario extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 2;

    protected function getData(): array
    {/*
            $user =  User::find(auth()->user()->id);
            $asignacion = $user->Asignacion;
            if($asignacion){
            $seccion = Seccion::find($asignacion->id_modelo);
            $usuarios = User::join('usuario_asignaciones', 'users.id', '=', 'usuario_asignaciones.user_id')
            ->join('manzanas', 'usuario_asignaciones.id_modelo', '=', 'manzanas.id')
            ->where('usuario_asignaciones.modelo', 'Manzana')
            ->where('manzanas.seccion_id', $seccion->id);
            dd($usuarios->first()->ejercicios);

            $data = $usuarios->get()->map(function ($user) {
                return $user->ejercicios->count();
            })->all();
           
        }
    */

        $seccion = Seccion::find($seccion_id = auth()->user()->Asignacion->id_modelo);
        $users_id = array();
        $users_id[] = auth()->user()->id;
        $manzanas = Manzana::where('seccion_id',$seccion->id)->get();
        $users_id_manzanas = UsuarioAsignacion::where('modelo','Manzana')
        ->whereIn('id_modelo',$manzanas->pluck('id'))->get()->pluck('user_id');
        foreach($users_id_manzanas as $user){
            $users_id[] = $user;
        }

        $usuarios = User::whereIn('id',$users_id)->get();
        $labels = $usuarios->pluck('username')->all();

        $data = $usuarios->map(function($usuario){
            return $usuario->ejercicios->count();
        })->all();

        return [
            'datasets' => [
                [
                    'label' => 'Ejercicios',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
        
    }

    protected function getType(): string
    {
        return 'bar';
    }

    
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['C ENLACE DE MANZANA']);
    }
}
