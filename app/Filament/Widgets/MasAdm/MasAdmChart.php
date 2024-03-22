<?php

namespace App\Filament\Widgets\MasAdm;

use App\Models\User;
use Filament\Widgets\ChartWidget;

class MasAdmChart extends ChartWidget
{
    protected static ?string $heading = 'Relación Usuarios/Ejercicios';
    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $usuarios = User::all(); // Obtiene todos los usuarios

        $labels = $usuarios->pluck('username')->all(); // Extrae los usernames como etiquetas

        $data = $usuarios->map(function ($user) {
            return $user->ejercicios->count();
        })->all();

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
