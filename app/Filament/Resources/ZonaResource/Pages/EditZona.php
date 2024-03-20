<?php

namespace App\Filament\Resources\ZonaResource\Pages;

use Filament\Actions;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\ZonaResource;
use App\Models\AsignacionGeografica;
use Filament\Resources\Pages\EditRecord;

class EditZona extends EditRecord
{
    protected static string $resource = ZonaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
