<?php

namespace App\Filament\Resources\ManzanaResource\Pages;

use App\Filament\Resources\ManzanaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditManzana extends EditRecord
{
    protected static string $resource = ManzanaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
