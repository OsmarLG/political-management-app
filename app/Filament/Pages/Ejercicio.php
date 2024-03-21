<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;

class Ejercicio extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.ejercicio';

    protected static ?string $navigationGroup = 'Ejercicios';
}
