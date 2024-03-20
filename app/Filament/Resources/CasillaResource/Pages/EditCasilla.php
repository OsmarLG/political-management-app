<?php

namespace App\Filament\Resources\CasillaResource\Pages;

use Filament\Actions;
use App\Models\AsignacionGeografica;
use Illuminate\Database\Eloquent\Model;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\CasillaResource;
use App\Models\Seccion;
use App\Models\Zona;

class EditCasilla extends EditRecord
{
    protected static string $resource = CasillaResource::class;

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
        $casilla = $this->record;

        $seccion = Seccion::find($casilla->seccion_id);
        $zona_id = $seccion->zona->id;
    
        // Aquí no necesitas buscar el modelo nuevamente, ya que $this->record ya es tu modelo.
        $this->form->fill([
            'numero' => $casilla->numero,
            'tipo' => $casilla->tipo,
            'status' => $casilla->status,
            'zona_id' => $zona_id ?? null,
            'seccion_id' => $casilla->seccion ? $casilla->seccion->id : null,
            'Latitud' => $casilla->asignacionGeografica->latitud,
            'Longitud' => $casilla->asignacionGeografica->longitud,
        ]);
    }

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $record->update($data);
        $asignacion = AsignacionGeografica::where('modelo', 'Casilla')->where('id_modelo', $record->id)->get()->first();
        $asignacion->latitud = $data['Latitud'];
        $asignacion->longitud = $data['Longitud'];
        $asignacion->save();
    
        return $record;
    }
}
