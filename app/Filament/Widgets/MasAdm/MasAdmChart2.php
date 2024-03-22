<?php

namespace App\Filament\Widgets\MasAdm;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class MasAdmChart2 extends ChartWidget
{
    protected static ?string $heading = 'Top 3 Usuarios con Más Ejercicios';

    protected static ?int $sort = 3;


    protected function getData(): array
    {
        $usuarios = User::withCount('ejercicios')
                        ->orderByDesc('ejercicios_count')
                        ->take(3)
                        ->get();

        $labels = $usuarios->pluck('username')->all(); // Extrae los usernames como etiquetas

        $data = $usuarios->pluck('ejercicios_count')->all(); // Extrae el conteo de ejercicios

        return [
            'datasets' => [
                [
                    'label' => 'Ejercicios por Usuario',
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
        return auth()->user()->hasAnyRole(['MASTER', 'ADMIN']);
    }
}
