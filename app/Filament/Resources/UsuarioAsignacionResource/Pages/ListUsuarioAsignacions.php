<?php

namespace App\Filament\Resources\UsuarioAsignacionResource\Pages;

use App\Filament\Resources\UsuarioAsignacionResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListUsuarioAsignacions extends ListRecords
{
    protected static string $resource = UsuarioAsignacionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
