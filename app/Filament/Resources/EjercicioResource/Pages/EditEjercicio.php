<?php

namespace App\Filament\Resources\EjercicioResource\Pages;

use App\Filament\Resources\EjercicioResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEjercicio extends EditRecord
{
    protected static string $resource = EjercicioResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
