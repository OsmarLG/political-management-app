<?php

namespace App\Filament\Resources\EncuestaResource\Pages;

use App\Filament\Resources\EncuestaResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEncuesta extends EditRecord
{
    protected static string $resource = EncuestaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
