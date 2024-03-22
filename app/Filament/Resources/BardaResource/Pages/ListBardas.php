<?php

namespace App\Filament\Resources\BardaResource\Pages;

use App\Filament\Resources\BardaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListBardas extends ListRecords
{
    protected static string $resource = BardaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
