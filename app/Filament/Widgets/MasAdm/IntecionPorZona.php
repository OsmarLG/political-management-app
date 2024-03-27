<?php

namespace App\Filament\Widgets\MasAdm;

use App\Models\User;
use App\Models\Zona;
use Filament\Widgets\ChartWidget;

class IntecionPorZona extends ChartWidget
{
    protected static ?string $heading = 'Intención de voto por Zona';
    protected static ?int $sort = 4;
    protected function getData(): array
    {
        $zonas = Zona::all(); // Obtiene todos los zonas

        $labels = $zonas->pluck('nombre')->all(); // Extrae los usernames como etiquetas

        $data = $zonas->map(function ($zona) {
        return $zona->IntencionVoto;
        })->all();
        $colors = ['#36A2EB', '#FF6384', '#FFCE56', '#4BC0C0', '#9966FF'];

        return [
            'datasets' => [
                [
                    'label' => 'Ejercicios por Usuario',
                    'data' => $data,
                    'backgroundColor' => $colors,
                    'borderColor' => '#9BD0F5',
                ],
            ],
            'labels' => $labels,
        ];
    }

    protected function getType(): string
    {
        return 'pie';
    }
    public static function canView(): bool
    {
        return auth()->user()->hasAnyRole(['MASTER', 'ADMIN']);
    }
}
