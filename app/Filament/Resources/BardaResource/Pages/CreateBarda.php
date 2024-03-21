<?php

namespace App\Filament\Resources\BardaResource\Pages;

use Filament\Actions;
use App\Models\AsignacionGeografica;
use Illuminate\Database\Eloquent\Model;
use App\Filament\Resources\BardaResource;
use Filament\Resources\Pages\CreateRecord;

class CreateBarda extends CreateRecord
{
    protected static string $resource = BardaResource::class;

    protected function handleRecordCreation(array $data): Model
    {
        $barda =  static::getModel()::create($data);

        AsignacionGeografica::create([
            // Asumiendo que tienes campos como 'modelo', 'id_modelo', 'latitud', 'longitud'
            'modelo' => 'Barda',
            'id_modelo' => $barda->id,
            'latitud' => $data['Latitud'],
            'longitud' => $data['Longitud'],
        ]);


        return $barda;
    }
}
