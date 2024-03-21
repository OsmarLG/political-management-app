<?php

namespace App\Filament\Resources\EjercicioResource\Pages;

use App\Filament\Resources\EjercicioResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;

class ViewEjercicio extends ViewRecord
{
    protected static string $resource = EjercicioResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // ...
            ]);
    }
}
