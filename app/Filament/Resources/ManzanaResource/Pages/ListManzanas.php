<?php

namespace App\Filament\Resources\ManzanaResource\Pages;

use App\Filament\Resources\ManzanaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListManzanas extends ListRecords
{
    protected static string $resource = ManzanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
