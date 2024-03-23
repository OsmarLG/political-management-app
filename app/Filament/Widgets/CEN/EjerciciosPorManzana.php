<?php

namespace App\Filament\Widgets\CEN;

use App\Models\User;
use App\Models\Manzana;
use App\Models\Seccion;
use App\Models\UsuarioAsignacion;
use Filament\Widgets\ChartWidget;

class EjerciciosPorManzana extends ChartWidget
{
    protected static ?string $heading = 'Chart';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $seccion = Seccion::find($seccion_id = auth()->user()->Asignacion->id_modelo);
        $users_id = array();
        $users_id[] = auth()->user()->id;
        $manzanas = Manzana::where('seccion_id',$seccion->id)->get();
        
        $labels = $manzanas->pluck('nombre')->all();

        $data = $manzanas->map(function($manzana){
            return $manzana->ejercicios->count();
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
