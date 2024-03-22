<?php

namespace App\Filament\Widgets\MasAdm;

use App\Models\Configuracion;
use App\Models\Ejercicio;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;

class MasAdmOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '10s';
    protected static ?int $sort = 1;


    protected function getStats(): array
    {
        // Asegúrate de que las consultas sean eficientes. User::role('...')->count() es más óptimo que User::all()->filter(...)
        $totalUsers = User::role(['C DISTRITAL', 'C ENLACE DE MANZANA', 'MANZANAL'])->count();
        $masterUsersCount = User::role('MASTER')->count(); // Asume que 'MASTER' es el nombre de tu rol
        $adminUsersCount = User::role('ADMIN')->count(); // Asume que 'ADMIN' es el nombre de tu rol
        $cDistritalUsersCount = User::role('C DISTRITAL')->count(); // Asume que 'ADMIN' es el nombre de tu rol
        $cEnlaceManzanaUsersCount = User::role('C ENLACE DE MANZANA')->count(); // Asume que 'ADMIN' es el nombre de tu rol
        $cManzanalUsersCount = User::role('MANZANAL')->count(); // Asume que 'ADMIN' es el nombre de tu rol

        $intencion_voto = Ejercicio::where('a_favor', 'A FAVOR')->get()->count();

        $ejerciciosTotales = Ejercicio::all()->count();

        return [
            Stat::make('Total Usuarios', $totalUsers)->chart([7, 2, 10, 3, 15, 4, 17]),
            Stat::make('Total Ejercicios', $ejerciciosTotales)->chart([1, 5, 10, 3, 15, 4, 17])->color(''),
            Stat::make('Intención de Voto', $intencion_voto)->description('Ejercicios donde Apoyan a ' . Configuracion::getCandidato())->chart([1, 5, 10, 3, 15, 4, 17])->color('success'),
        ];
    }

    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['MASTER', 'ADMIN']);
    }
}
