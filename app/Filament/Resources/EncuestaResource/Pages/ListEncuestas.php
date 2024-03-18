<?php

namespace App\Filament\Resources\EncuestaResource\Pages;

use App\Filament\Resources\EncuestaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListEncuestas extends ListRecords
{
    protected static string $resource = EncuestaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
