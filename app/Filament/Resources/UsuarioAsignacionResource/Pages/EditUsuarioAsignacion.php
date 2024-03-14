<?php

namespace App\Filament\Resources\UsuarioAsignacionResource\Pages;

use App\Filament\Resources\UsuarioAsignacionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditUsuarioAsignacion extends EditRecord
{
    protected static string $resource = UsuarioAsignacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
