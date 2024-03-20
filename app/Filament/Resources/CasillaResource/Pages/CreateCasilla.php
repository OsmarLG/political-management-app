<?php

namespace App\Filament\Resources\CasillaResource\Pages;

use Filament\Actions;
use App\Models\AsignacionGeografica;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\CasillaResource;
use App\Models\Casilla;

class CreateCasilla extends CreateRecord
{
    protected static string $resource = CasillaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $casilla =  static::getModel()::create($data);

        AsignacionGeografica::create([
            // Asumiendo que tienes campos como 'modelo', 'id_modelo', 'latitud', 'longitud'
            'modelo' => 'Casilla',
            'id_modelo' => $casilla->id,
            'latitud' => $data['Latitud'],
            'longitud' => $data['Longitud'],
        ]);


        return $casilla;
    }
}
