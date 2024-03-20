<?php

namespace App\Livewire;

use App\Models\User;
use App\Models\Manzana;
use App\Models\Seccion;
use Livewire\Component;
use App\Models\Encuesta;
use Filament\Forms\Form;
use App\Models\Ejercicio;
use App\Models\Configuracion;
use App\Models\EncuestaOpcion;
use App\Models\EncuestaPregunta;
use App\Models\EncuestaRespuesta;
use App\Models\UsuarioAsignacion;
use Illuminate\Contracts\View\View;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
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
        /*
            D(numero de zona)-(Iniciales de nombre del encargado de zona o coordinador distrital)-S(numero de seccion)-(Iniciales de Nombre del coordinador de enlace de manzana)-M(numero de mananza)-(Iniciales de nombre del encargado de manzana)-(DateTime formato ->(dd-mm-YYYY-ss-mm-hh))-Random(1,999)
        */
       

        $folio = 'D';
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

    

    $usuario = User::find(auth()->user()->id);

    if($usuario->hasRole('MANZANAL')){

        $usuario = User::find(auth()->user()->id);
        $manzana_id = UsuarioAsignacion::where('user_id',$usuario->id)->where('modelo','Manzana')->first()->id_modelo;
        $manzana = Manzana::where('id',$manzana_id)->first();
/*
        $campos[] =     
        Select::make('manzana_id')
        ->label('Manzana')
        ->options(function (callable $get) {
            $usuario = User::find(auth()->user()->id);
            $manzana_id = UsuarioAsignacion::where('user_id',$usuario->id)->where('modelo','Manzana')->first()->id_modelo;
            return $manzana = Manzana::where('id',$manzana_id)->pluck('nombre','id');
        })
        ->searchable()
        ->preload()
        ->reactive() // Importante para asegurar que se actualiza cuando cambia zona_id
        ->required()
        ->placeholder('Selecciona una sección');
*/


        $campos[] =  TextInput::make('manzana_id')
        ->required()
        ->maxLength(255)
        ->label('Manzana')
        ->helperText('El folio del ejercicio.')
        ->default($manzana->nombre)
        ->readOnly()    ;
    

    }

    if($usuario->hasRole('C ENLACE DE MANZANA')){
        $campos[] =        
        Select::make('manzana_id')
        ->label('Manzana')
        ->options(function (callable $get) {
            $usuario = User::find(auth()->user()->id);
            $entidad_id = UsuarioAsignacion::where('user_id',$usuario->id)->where('modelo','Seccion')->first()->id_modelo ?? '';
            $entidad = Manzana::where('seccion_id',$entidad_id)->pluck('nombre','id') ?? '';
            return $entidad;
        })
        ->searchable()
        ->preload()
        ->reactive() // Importante para asegurar que se actualiza cuando cambia zona_id
        ->required()
        ->placeholder('Selecciona una sección'); 
    }
    if($usuario->hasRole('C DISTRITAL')){
        $campos[] =        
        Select::make('seccion_id')
        ->label('Sección')
        ->options(function (callable $get) {
            $usuario = User::find(auth()->user()->id);
            $entidad_id = UsuarioAsignacion::where('user_id',$usuario->id)->where('modelo','Zona')->first()->id_modelo ?? '';
            $entidad = Seccion::where('zona_id',$entidad_id)->pluck('nombre','id') ?? '';
            return $entidad;
        })
        ->searchable()
        ->preload()
        ->reactive() // Importante para asegurar que se actualiza cuando cambia zona_id
        ->required()
        ->label('Sección')
        ->placeholder('Selecciona una sección'); 

        $campos[] =        
        Select::make('manzana_id')
        ->label('Sección')
        ->options(function (callable $get) {
            $seccion_id = $get('seccion_id');

            if($seccion_id){
                return Manzana::when($seccion_id, function ($query) use ($seccion_id) {
                    return $query->where('seccion_id', $seccion_id);
                })->pluck('nombre', 'id');
            }

        })
        ->searchable()
        ->preload()
        ->reactive() // Importante para asegurar que se actualiza cuando cambia zona_id
        ->required()
        ->label('Manzana')
        ->placeholder('Selecciona una Manzana');
    }


    $campos[] =  TextInput::make('Folio')
    ->required()
    ->maxLength(255)
    ->label('Folio')
    ->helperText('El folio del ejercicio.')
    ->readOnly()    ;


    $campos[] =  TextInput::make('Latitud')
    ->required()
    ->maxLength(255)
    ->label('Latitud')
    ->placeholder('Latitud')
    ->helperText('Coordenadas para el ejercicio.')
    ->readOnly()    ;

    $campos[] =  TextInput::make('Longitud')
    ->required()
    ->maxLength(255)
    ->label('Logintud')
    ->placeholder('Longitud')
    ->helperText('Coordenadas para el ejercicio.')
    ->readOnly()    ;


    return $form
    ->schema([
        Section::make('Responde las siguientes preguntas')
        ->schema($campos)
    ])
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
