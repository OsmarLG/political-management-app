<?php

namespace App\Filament\Resources\EjercicioResource\Pages;

use App\Filament\Resources\EjercicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEjercicios extends ListRecords
{
    protected static string $resource = EjercicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
