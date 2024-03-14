<?php

namespace App\Filament\Resources\SeccionResource\Pages;

use App\Filament\Resources\SeccionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditSeccion extends EditRecord
{
    protected static string $resource = SeccionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
