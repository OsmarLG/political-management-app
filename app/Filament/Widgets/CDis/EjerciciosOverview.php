<?php

namespace App\Filament\Widgets\CDis;

use App\Models\User;
use App\Models\Zona;
use App\Models\Seccion;
use App\Models\Ejercicio;
use App\Models\Configuracion;
use App\Models\UsuarioAsignacion;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class EjerciciosOverview extends BaseWidget
{
    protected function getStats(): array
    {

        $zona = Zona::find($zonaId = auth()->user()->Asignacion->id_modelo);
        $manzanas = $zona->manzanas;
        $idManzanas = $manzanas->pluck('id');
        $usersIds[] = auth()->user()->id;

        $secciones = Seccion::where('zona_id', $zona->id)->get();
        $usersIdsSecciones = UsuarioAsignacion::where('modelo', 'Seccion')->whereIn('id_modelo', $secciones->pluck('id'))->get()->pluck('user_id');
        
        foreach ($usersIdsSecciones as $userIdSeccion) {
            $usersIds[] = $userIdSeccion;
        }

        $usersIdsManzanas = UsuarioAsignacion::where('modelo', 'Manzana')->whereIn('id_modelo', $manzanas->pluck('id'))->get()->pluck('user_id');

        foreach ($usersIdsManzanas as $userIdManzana) {
            $usersIds[] = $userIdManzana;
        }

        $usuarios = User::whereIn('id', $usersIds)->get()->count();

        $ejercicios = Ejercicio::whereIn('manzana_id', $idManzanas)->get()->count();
        $intencion_voto = Ejercicio::whereIn('manzana_id', $idManzanas)->where('a_favor', 'A FAVOR')->get()->count();

        return [
            //
            Stat::make('Total Usuarios', $usuarios)->chart([1, 5, 10, 3, 15, 4, 17]),
            Stat::make('Total Ejercicios', $ejercicios)->chart([1, 5, 10, 3, 15, 4, 17])->color(''),
            Stat::make('Intención de Voto', $intencion_voto)->description('Ejercicios donde Apoyan a ' . Configuracion::getCandidato())->chart([1, 5, 10, 3, 15, 4, 17])->color('success'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['C DISTRITAL']);
    }
}
