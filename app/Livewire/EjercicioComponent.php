<?php

namespace App\Livewire;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Zona;
use App\Models\Manzana;
use App\Models\Seccion;
use Filament\Forms\Get;
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
use App\Models\AsignacionGeografica;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\View as ViewF;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Concerns\InteractsWithForms;


class EjercicioComponent extends Component implements HasForms
{
    use InteractsWithForms;
    public ?array $data = [];
    public $manzana;
    public $folio;

    public function mount(){
        $this->form->fill();

    }

    public function form(Form $form): Form
    {

        $preguntas = EncuestaPregunta::all();
 

    // Define un array para almacenar los campos del formulario
    $campos = [];
    $manzana_id = null;

    // Itera sobre las preguntas y agrega los campos correspondientes al array
    foreach ($preguntas as $pregunta) {
        //agregamos las opciones
        $opciones = EncuestaOpcion::where('pregunta_id',$pregunta->id)->get();
        if($pregunta->opciones){
            foreach ($opciones as $opcion) {
                // Agrega la opción al array de opciones del campo de Radio
                $opcionesCampo[$opcion->texto_opcion] = $opcion->texto_opcion;
            }
            $campo = Radio::make($pregunta->id)->options($opcionesCampo)->label($pregunta->texto_pregunta)->required();
            $campos[] = $campo;
        }
        $opcionesCampo = array();
    }

    $usuario = User::find(auth()->user()->id);

    if($usuario->hasRole('MANZANAL')){

        $usuario = User::find(auth()->user()->id);
        $manzana_id = UsuarioAsignacion::where('user_id',$usuario->id)->where('modelo','Manzana')->first()->id_modelo;
        $manzana = Manzana::where('id',$manzana_id)->first();
 
        $campos[] =  TextInput::make('manzana_id')
        ->required()
        ->maxLength(255)
        ->label('Manzana')
        ->helperText('El folio del ejercicio.')
        ->default($manzana->nombre)
        ->readOnly()    ;
        $this->manzana = Manzana::find($manzana_id);
        $this->generar_folio();

        $campos[] =  TextInput::make('Folio')
        ->required()
        ->maxLength(255)
        ->label('Folio')
        ->helperText('El folio del ejercicio.')
        ->readOnly() 
        ->default($this->folio)
        ;
    }

    if($usuario->hasRole('C ENLACE DE MANZANA')){
        $campos[] =        
        Select::make('manzana_id')
        ->label('Manzana')
        ->options(function (callable $get) {
            $assignedIds = UsuarioAsignacion::where('modelo', 'Manzana')->pluck('id_modelo')->toArray();

            $usuario = User::find(auth()->user()->id);
            $entidad_id = UsuarioAsignacion::where('user_id',$usuario->id)->where('modelo','Seccion')->first()->id_modelo ?? '';
            $entidad = Manzana::where('seccion_id',$entidad_id)
            ->whereIn('id', $assignedIds)
            ->pluck('nombre','id') ?? '';


            return $entidad;
        })
        ->searchable()
        ->preload()
        ->reactive() // Importante para asegurar que se actualiza cuando cambia zona_id
        ->required()
        ->afterStateUpdated(function (?string $state, ?string $old) {
            $this->manzana = Manzana::find($state);
            $this->generar_folio();
        })
        ->afterStateUpdated(fn (callable $set) => $set('Folio', $this->folio))
        ->placeholder('Selecciona una sección'); 

        $campos[] =  TextInput::make('Folio')
        ->required()
        ->maxLength(255)
        ->label('Folio')
        ->helperText('El folio del ejercicio.')
        ->readOnly() 
        ;
    }
    if($usuario->hasRole('C DISTRITAL')){
        $campos[] =        
        Select::make('seccion_id')
        ->label('Sección')
        ->options(function (callable $get) {
            $assignedIds = UsuarioAsignacion::where('modelo', 'Seccion')->pluck('id_modelo')->toArray();

            $usuario = User::find(auth()->user()->id);
            $entidad_id = UsuarioAsignacion::where('user_id',$usuario->id)->where('modelo','Zona')->first()->id_modelo ?? '';
            $entidad = Seccion::where('zona_id',$entidad_id)
            ->whereIn('id', $assignedIds)
            ->pluck('nombre','id') ?? '';
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
                //$assignedIds = UsuarioAsignacion::where('modelo', 'Manzana')->pluck('id_modelo')->toArray();
                return Manzana::when($seccion_id, function ($query) use ($seccion_id) {
                    return $query->where('seccion_id', $seccion_id) 
                    ->whereIn('id', UsuarioAsignacion::where('modelo', 'Manzana')->pluck('id_modelo')->toArray());
                })->pluck('nombre', 'id');
            }

        })
        ->searchable()
        ->preload()
        ->reactive() // Importante para asegurar que se actualiza cuando cambia zona_id
        ->afterStateUpdated(function (?string $state, ?string $old) {
            $this->manzana = Manzana::find($state);
            $this->generar_folio();
        })
        ->afterStateUpdated(fn (callable $set) => $set('Folio', $this->folio))
        ->label('Manzana')
        ->placeholder('Selecciona una Manzana');

        $campos[] =  TextInput::make('Folio')
        ->required()
        ->maxLength(255)
        ->label('Folio')
        ->helperText('El folio del ejercicio.')
        ->readOnly() 
        ;
    }

        if($usuario->hasRole('MASTER') || $usuario->hasRole('ADMIN')){
        $campos[] =        
        Select::make('zona_id')
        ->label('Zona')
        ->options(function (callable $get) {
            return Zona::all()
            ->whereIn('id', UsuarioAsignacion::where('modelo', 'Zona')->pluck('id_modelo')->toArray())
            ->pluck('nombre','id');
        })
        ->searchable()
        ->preload()
        ->reactive() 
        ->required()
        ->placeholder('Selecciona una zona'); 

        $campos[] =        
        Select::make('seccion_id')
        ->label('Sección')
        ->options(function (callable $get) {
            $zona_id = $get('zona_id');
            if($zona_id){
                return Seccion::when($zona_id, function ($query) use ($zona_id) {
                    return $query->where('zona_id', $zona_id)
                    ->whereIn('id', UsuarioAsignacion::where('modelo', 'Seccion')->pluck('id_modelo')->toArray())
                    ;
                })->pluck('nombre', 'id');
            }

        })
        ->searchable()
        ->preload()
        ->reactive() 
        ->required()
        ->placeholder('Selecciona una Seccion');

        $campos[] =        
        Select::make('manzana_id')
        ->label('Manzana')
        ->options(function (callable $get) {
            $seccion_id = $get('seccion_id');

            if($seccion_id){

                return Manzana::when($seccion_id, function ($query) use ($seccion_id) {
                    return $query->where('seccion_id', $seccion_id)
                    ->whereIn('id', UsuarioAsignacion::where('modelo', 'Manzana')->pluck('id_modelo')->toArray())
                    ;
                })->pluck('nombre', 'id');
            }

        })
        ->searchable()
        ->afterStateUpdated(function (?string $state, ?string $old) {
            $this->manzana = Manzana::find($state);
            $this->generar_folio();
        })
        ->afterStateUpdated(fn (callable $set) => $set('Folio', $this->folio))
        ->preload()
        ->reactive() 
        ->required()
        ->placeholder('Selecciona una Manzana');

        $campos[] =  TextInput::make('Folio')
        ->required()
        ->maxLength(255)
        ->label('Folio')
        ->helperText('El folio del ejercicio.')
        ->readOnly() 
        ;
    }




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
    ->readOnly();

    $campos[] = ViewF::make('ejercicios.map');

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
        $respuestas = array_filter($datas, 'is_numeric', ARRAY_FILTER_USE_KEY);
        $ejercicio = new Ejercicio();
        $ejercicio->encuesta_id = Encuesta::first()->id ? Encuesta::first()->id : "";
        $ejercicio->user_id = auth()->user()->id;
        $ejercicio->folio = $this->folio;
        $ejercicio->manzana_id = $this->manzana->id;
        if($respuestas['2'] == "SI"){
            
            $ejercicio->a_favor = "A FAVOR";
        }
        $ejercicio->save();

        $asignacion_geografica = new AsignacionGeografica();
        $asignacion_geografica->modelo = 'Ejercicio';
        $asignacion_geografica->id_modelo = $ejercicio->id;
        $asignacion_geografica->latitud = $datas['Latitud'];
        $asignacion_geografica->longitud = $datas['Longitud'];
        $asignacion_geografica->status = 'ACTIVO';
        $asignacion_geografica->save();
    
        foreach($respuestas as $key => $res){         
            $respuesta = new EncuestaRespuesta();
            $respuesta->ejercicio_id = $ejercicio->id;
            $respuesta->pregunta_id = EncuestaPregunta::where('id',$key)->first()->id;
            $respuesta->respuesta = $res;
            $respuesta->save();
        }
        Notification::make()
        ->title('Ejercicio Guardado!')
        ->success()
        ->send();
        $this->folio = "";
        $this->form->fill();
    }

    public function render()
    {
        return view('livewire.ejercicio-component');
    }

    public function generar_folio(){
        /*
            D(numero de zona)-(Iniciales de nombre del encargado de zona o coordinador distrital)-S(numero de seccion)-(Iniciales de Nombre del coordinador de enlace de manzana)-M(numero de mananza)-(Iniciales de nombre del encargado de manzana)-(DateTime formato ->(dd-mm-YYYY-ss-mm-hh))-Random(1,999)
        */
        if($this->manzana){

            $consulta_c_distrital =  UsuarioAsignacion::where('modelo','Zona')->where('id_modelo',$this->manzana->seccion->zona->id);
            $consulta_c_enlace_manzana =  UsuarioAsignacion::where('modelo','Seccion')->where('id_modelo',$this->manzana->seccion->id);
            $consulta_manzanal = UsuarioAsignacion::where('modelo','Manzana')->where('id_modelo',$this->manzana->id);

            $C_DISTRITAL_INICIALES = $consulta_c_distrital->first() ? User::find($consulta_c_distrital->first()->user_id)->iniciales() : "" ;
            $C_ENLACE_MANZANA_INICIALES =  $consulta_c_enlace_manzana->first() ? User::find($consulta_c_enlace_manzana->first()->user_id)->iniciales() : '';
            $MANZANAL_INICIALES = $consulta_manzanal->first() ? User::find( $consulta_manzanal->first()->user_id)->iniciales() : '';
            $fecha = Carbon::Now()->format('d-m-Y-s-i-h');
            $random = rand(1, 999);
            $this->folio = "D{$this->manzana->seccion->zona->id}-{$C_DISTRITAL_INICIALES}-S{$this->manzana->seccion->id}-{$C_ENLACE_MANZANA_INICIALES}-M{$this->manzana->id}-{$MANZANAL_INICIALES}-{$fecha}-{$random}";

            /*
                        $C_DISTRITAL = User::find( UsuarioAsignacion::where('modelo','Zona')->where('id_modelo',$this->manzana->seccion->zona->id)->first()->user_id) ?? '';
            $C_ENLACE_MANZANA =  User::find( UsuarioAsignacion::where('modelo','Seccion')->where('id_modelo',$this->manzana->seccion->id)->first()->user_id) ?? '';
            $MANZANAL = User::find( UsuarioAsignacion::where('modelo','Manzana')->where('id_modelo',$this->manzana->id)->first()->user_id) ?? '';
            $fecha = Carbon::Now()->format('d-m-Y-s-i-h');
            $random = rand(1, 999);
            $this->folio = "D{$this->manzana->seccion->zona->id}-{$C_DISTRITAL->iniciales()}-S{$this->manzana->seccion->id}-{$C_ENLACE_MANZANA->iniciales()}-M{$this->manzana->id}-{$MANZANAL->iniciales()}-{$fecha}-{$random}";
            */
        }
    }
}
