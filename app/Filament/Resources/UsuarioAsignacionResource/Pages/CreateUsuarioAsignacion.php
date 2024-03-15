<?php

namespace App\Filament\Resources\UsuarioAsignacionResource\Pages;

use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\UsuarioAsignacionResource;

class CreateUsuarioAsignacion extends CreateRecord
{
    protected static string $resource = UsuarioAsignacionResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getCreatedNotification(): ?Notification
    {
        return Notification::make()
        ->title('Asignacion de usuario creada')
        ->body('La nueva asignacion de Usuario ha Sido Creada Satisfactoriamente.')
        ->color('success')
        ->duration(2500)
        ->send();
    }
}
