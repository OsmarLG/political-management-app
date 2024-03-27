<?php

namespace App\Filament\Resources\EjercicioResource\Pages;

use Filament\Actions;
use App\Models\Encuesta;
use App\Models\AsignacionGeografica;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\EncuestaResource;
use App\Filament\Resources\EjercicioResource;
use App\Models\Ejercicio;
use App\Models\EncuestaRespuesta;
use App\Models\Manzana;
use Illuminate\Database\Eloquent\Model;

class EditEjercicio extends EditRecord
{
    protected static string $resource = EjercicioResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
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


    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        //dd($data);
        $respuestas_form = array_filter($data, 'is_numeric', ARRAY_FILTER_USE_KEY);

        if($respuestas_form['2'] == "SI"){
            
            $record->a_favor = "A FAVOR";
        }
        if($respuestas_form['2'] == "INDECISO"){
            
            $record->a_favor = "INDECISO";
        }
        if($respuestas_form['2'] == "NO"){
            
            $record->a_favor = "EN DESACUERDO";
        }

        $record->update($data);

        $asignacion = AsignacionGeografica::where('modelo', 'Ejercicio')->where('id_modelo', $record->id)->get()->first();
        $asignacion->latitud = $data['latitud'];
        $asignacion->longitud = $data['longitud'];
        $asignacion->save();

        $respuestas = EncuestaRespuesta::where('ejercicio_id',$record->id)->get();

        foreach($respuestas as $respuesta){
            $respuesta->respuesta = $respuestas_form[$respuesta->pregunta_id];
            $respuesta->save();
        }

        return $record;
    }
}
