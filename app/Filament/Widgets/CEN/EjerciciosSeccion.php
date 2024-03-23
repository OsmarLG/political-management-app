<?php

namespace App\Filament\Widgets\CEN;

use App\Models\User;
use App\Models\Manzana;
use App\Models\Seccion;
use App\Models\Ejercicio;
use App\Models\Configuracion;
use App\Models\UsuarioAsignacion;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class EjerciciosSeccion extends BaseWidget
{
    protected function getStats(): array
    {

        $seccion = Seccion::find($seccion_id = auth()->user()->Asignacion->id_modelo);
        $users_id = array();
        $users_id[] = auth()->user()->id;
        $manzanas = Manzana::where('seccion_id', $seccion->id)->get();
        $users_id_manzanas = UsuarioAsignacion::where('modelo', 'Manzana')
            ->whereIn('id_modelo', $manzanas->pluck('id'))->get()->pluck('user_id');
        foreach ($users_id_manzanas as $user) {
            $users_id[] = $user;
        }

        $usuarios = User::whereIn('id', $users_id)->get();



        $ejercicios = null;
        $ejercicios_favor = null;

        $user = User::find(auth()->user()->id);
        $asignacion = $user->Asignacion()->first();
        if ($asignacion) {
            $seccion = Seccion::where('id', $asignacion->id_modelo)->first();
            $ejercicios = Ejercicio::whereHas('manzana', function ($query) use ($seccion) {
                $query->wherehas('seccion', function ($query) use ($seccion) {
                    $query->where('id', $seccion->id);
                });
            })->get();

            $ejercicios_favor = Ejercicio::whereHas('manzana', function ($query) use ($seccion) {
                $query->wherehas('seccion', function ($query) use ($seccion) {
                    $query->where('id', $seccion->id)->where('a_favor','A FAVOR');
                });
            })->get();
        }



        return [
            Stat::make('Ejercicios de la seccion', count($ejercicios))->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Usuarios de la sección', count($usuarios))->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Inteción de voto', count($ejercicios_favor))->description('Ejercicios donde Apoyan a ' . Configuracion::getCandidato())->chart([1, 5, 10, 3, 15, 4, 17])->color('success'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['C ENLACE DE MANZANA']);
    }
}
