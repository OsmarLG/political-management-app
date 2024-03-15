<?php

namespace App\Filament\Resources\UserResource\Pages;

use Filament\Infolists\Infolist;
use Filament\Resources\Forms\Form;
use App\Filament\Resources\UserResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\TextEntry;

class ViewUser extends ViewRecord
{
    protected static string $resource = UserResource::class;

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                // ...
            ]);
    }
}
