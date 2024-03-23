<?php

namespace App\Filament\Widgets\CEN;

use App\Models\Ejercicio;
use App\Models\User;
use App\Models\Seccion;
use App\Models\UsuarioAsignacion;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class EjerciciosSeccion extends BaseWidget
{
    protected function getStats(): array
    {
         $ejercicios = null;
         $user =  User::find(auth()->user()->id);
         $asignacion = $user->Asignacion()->first();
         if($asignacion){
            $seccion = Seccion::where('id',$asignacion->id_modelo)->first();
            $ejercicios = Ejercicio::whereHas('manzana', function($query) use ($seccion){
                $query->wherehas('seccion',function($query) use ($seccion){
                    $query->where('id',$seccion->id);
                });
            })->get();
         }

        return [
            Stat::make('Ejercicios de la seccion', count($ejercicios))->chart([7, 2, 10, 3, 15, 4, 17]),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['C ENLACE DE MANZANA']);
    }
}
