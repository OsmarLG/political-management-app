<?php

namespace App\Livewire;

use Livewire\Component;
use Filament\Forms\Form;
use App\Models\Ejercicio;
use App\Models\Encuesta;
use App\Models\EncuestaOpcion;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaRespuesta;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Radio;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class EjercicioComponent extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];

    public function mount(){
        $this->form->fill();

    }

    public function form(Form $form): Form
    {
        $preguntas = EncuestaPregunta::all();
 

    // Define un array para almacenar los campos del formulario
    $campos = [];

    // Itera sobre las preguntas y agrega los campos correspondientes al array
    foreach ($preguntas as $pregunta) {
        //agregamos las opciones
        $opciones = EncuestaOpcion::where('pregunta_id',$pregunta->id)->get();
        if($pregunta->opciones){
            foreach ($opciones as $opcion) {
                // Agrega la opción al array de opciones del campo de Radio
                $opcionesCampo[$opcion->texto_opcion] = $opcion->texto_opcion;
            }
            $campo = Radio::make($pregunta->texto_pregunta)->options($opcionesCampo);
            $campos[] = $campo;
        }
        $opcionesCampo = array();
    }


    return $form
        ->schema($campos)
        ->statePath('data')
        ->model(Ejercicio::class);
    }
       
    public function create(): void
    {
        //dd($this->form->getState());
        $datas = $this->form->getState();
          
        $ejercicio = new Ejercicio();
        $ejercicio->asignacion_geografica_id = null;
        $ejercicio->encuesta_id = Encuesta::first()->id;
        $ejercicio->user_id = auth()->user()->id;
        $ejercicio->folio = '123';
        $ejercicio->save();

        foreach($datas as $pregunta => $res){
            
            $respuesta = new EncuestaRespuesta();
            $respuesta->ejercicio_id = $ejercicio->id;
            $respuesta->pregunta_id = EncuestaPregunta::where('texto_pregunta',$pregunta)->first()->id;
            $respuesta->respuesta = $res;
            $respuesta->save();
        }
    }

    public function render()
    {
        return view('livewire.ejercicio-component');
    }
}
