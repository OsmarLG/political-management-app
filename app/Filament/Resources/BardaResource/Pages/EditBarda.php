<?php

namespace App\Filament\Resources\BardaResource\Pages;

use Filament\Actions;
use App\Models\Seccion;
use App\Models\AsignacionGeografica;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\BardaResource;

class EditBarda extends EditRecord
{
    protected static string $resource = BardaResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    public function mount($record): void
    {
        // Llama al método mount del padre con el $record proporcionado.
        parent::mount($record);
    
        // Puedes acceder a $this->record, ya que ahora se establecerá correctamente después de llamar a parent::mount($record).
        $barda = $this->record;

        $seccion = Seccion::find($barda->seccion_id);
        $zona_id = $seccion->zona->id;
    
        // Aquí no necesitas buscar el modelo nuevamente, ya que $this->record ya es tu modelo.
        $this->form->fill([
            'identificador' => $barda->identificador,
            'seccion_id' => $barda->seccion ? $barda->seccion->id : null,
            'zona_id' => $zona_id ?? null,
            'Latitud' => $barda->asignacionGeografica->latitud,
            'Longitud' => $barda->asignacionGeografica->longitud,
        ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $asignacion = AsignacionGeografica::where('modelo', 'Barda')->where('id_modelo', $record->id)->get()->first();
        $asignacion->latitud = $data['Latitud'];
        $asignacion->longitud = $data['Longitud'];
        $asignacion->save();
    
        return $record;
    }
}
