<?php

namespace App\Filament\Resources\EjercicioResource\Pages;

use Filament\Actions;
use App\Models\Manzana;
use App\Models\Ejercicio;
use Filament\Infolists\Infolist;
use Filament\Resources\Pages\ViewRecord;
use App\Filament\Resources\EjercicioResource;

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

    public function mount($record): void
    {
        parent::mount($record);

        $ejercicio = Ejercicio::find($record);
        $manzana = Manzana::find($ejercicio->manzana_id);
       
        $this->form->fill([
                'zona_id' => $manzana->seccion->zona->id,
                'seccion_id' => $manzana->seccion->id,
                'manzana_id' => $manzana->id,
                'folio' => $ejercicio->folio,
            ]);

    }
}
