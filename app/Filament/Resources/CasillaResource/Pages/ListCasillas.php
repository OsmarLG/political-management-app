<?php

namespace App\Filament\Resources\CasillaResource\Pages;

use App\Filament\Resources\CasillaResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListCasillas extends ListRecords
{
    protected static string $resource = CasillaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
