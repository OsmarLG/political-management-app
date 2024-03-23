<?php

namespace App\Filament\Widgets\Manzanal;

use App\Models\User;
use App\Models\Ejercicio;
use App\Models\Configuracion;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class Ejercicios extends BaseWidget
{
    protected function getStats(): array
    {
        $usuario = User::find(auth()->user()->id);
        $ejercicios = Ejercicio::where('user_id',$usuario->id)->get();
        $ejercicios_favor = Ejercicio::where('user_id',$usuario->id)
        ->where('a_favor','A FAVOR')
        ->get();

        return [
            Stat::make('Ejercicios Realizados', count($ejercicios))->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Inteción de voto', count($ejercicios_favor))->description('Ejercicios donde Apoyan a ' . Configuracion::getCandidato())->chart([1, 5, 10, 3, 15, 4, 17])->color('success'),
        ];
    }
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['MANZANAL']);
    }
}
