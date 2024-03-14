<?php

namespace App\Filament\Resources\SeccionResource\Pages;

use App\Filament\Resources\SeccionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListSeccions extends ListRecords
{
    protected static string $resource = SeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
