<?php

namespace App\Filament\Widgets\CDis;

use App\Models\Seccion;
use App\Models\Zona;
use App\Models\User;
use App\Models\UsuarioAsignacion;
use Filament\Widgets\ChartWidget;

class CDisManzanas extends ChartWidget
{
    protected static ?string $heading = 'Relación Usuarios/Ejercicios en Manzanas';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        $zona = Zona::find($zonaId = auth()->user()->Asignacion->id_modelo);
        $usersIds = array();

        $manzanas = $zona->manzanas;
        $usersIdsManzanas = UsuarioAsignacion::where('modelo', 'Manzana')->whereIn('id_modelo', $manzanas->pluck('id'))->get()->pluck('user_id');

        foreach ($usersIdsManzanas as $userIdManzana) {
            $usersIds[] = $userIdManzana;
        }

        $usuarios = User::whereIn('id', $usersIds)->get();
        
        // Etiquetas para el gráfico: Nombres de usuario
        $labels = $usuarios->pluck('username')->all();

        // Datos para el gráfico: Cantidad de ejercicios por usuario
        $data = $usuarios->map(function ($user) {
            return $user->ejercicios->count();
        })->all();

        return [
            'datasets' => [
                [
                    'label' => "Ejercicios por Usuario en Zona {$zona->nombre}",
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
        return auth()->user()->hasRole('C DISTRITAL');
    }
}
