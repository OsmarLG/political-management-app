<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use App\Models\AsignacionGeografica;
use Illuminate\Contracts\View\View;

class MapaGeneral extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-map';
    protected static ?string $navigationGroup = 'Mapa';
    protected static string $view = 'filament.pages.mapa.mapa-general';

    public $asignacionesGeograficas;
}
