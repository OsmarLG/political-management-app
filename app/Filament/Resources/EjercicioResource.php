<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use App\Models\User;
use App\Models\Zona;
use Filament\Tables;
use App\Models\Manzana;
use App\Models\Seccion;
use Filament\Forms\Get;
use App\Models\Encuesta;
use Filament\Forms\Form;
use App\Models\Ejercicio;
use Filament\Tables\Table;
use App\Models\EncuestaOpcion;
use App\Models\EncuestaPregunta;
use Filament\Resources\Resource;
use App\Models\EncuestaRespuesta;
use App\Models\UsuarioAsignacion;
use Filament\Forms\Components\View;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\View as ViewF;
use App\Filament\Resources\EjercicioResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EjercicioResource\RelationManagers;
use App\Models\AsignacionGeografica;

class EjercicioResource extends Resource
{
    protected static ?string $model = Ejercicio::class;

    protected static ?string $label = 'Mis Ejercicios';
    protected static ?string $navigationLabel = 'Mis Ejercicios';
    protected static ?string $navigationGroup = 'Ejercicios';
    protected static ?string $navigationIcon = 'heroicon-c-folder-open';    
    protected static ?int    $navigationSort = 4;
    public $manzana;

    public $folio;

    public static function canCreate(): bool
    {
        return false;
    }

    public static function form(Form $form): Form
    {
        
        $preguntas = EncuestaPregunta::all();
        // Define un array para almacenar los campos del formulario
        $campos = [];
        $manzana_id = null;
    
        // Itera sobre las preguntas y agrega los campos correspondientes al array
        foreach ($preguntas as $pregunta) {

            if($pregunta->opciones){
                //agregamos las opciones
                $opciones = EncuestaOpcion::where('pregunta_id',$pregunta->id)->get();
                foreach ($opciones as $opcion) {
                    // Agrega la opción al array de opciones del campo de Radio
                    $opcionesCampo[$opcion->texto_opcion] = $opcion->texto_opcion;
                }

                $campo = Radio::make($pregunta->id)->options($opcionesCampo)
                ->formatStateUsing(function ($state, $record) use ($pregunta) {
                    return EncuestaRespuesta::where('ejercicio_id',$record->id)->where('pregunta_id',$pregunta->id)->first()->respuesta;
                }) 
                 ->label($pregunta->texto_pregunta)->required();
                $campos[] = $campo;
            }
            $opcionesCampo = array();
        }

        $usuario = User::find(auth()->user()->id);
        if($usuario->hasRole('MASTER') || $usuario->hasRole('ADMIN')){
            $campos[] =        
            Select::make('zona_id')
            ->label('Zona')
            ->options(function (callable $get) {
                return Zona::all()
                ->whereIn('id', UsuarioAsignacion::where('modelo', 'Zona')->pluck('id_modelo')->toArray())
                ->pluck('nombre','id');
            })
         
            ->afterStateUpdated(fn (callable $set) => $set('seccion_id', null))
            ->afterStateUpdated(fn (callable $set) => $set('manzana_id', null))
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
        
            ->afterStateUpdated(fn (callable $set) => $set('manzana_id', null))
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
            ->afterStateUpdated(function (?string $state, ?string $old, callable $set) {
                $manzana = Manzana::find($state);
                $consulta_c_distrital =  UsuarioAsignacion::where('modelo','Zona')->where('id_modelo',$manzana->seccion->zona->id);
                $consulta_c_enlace_manzana =  UsuarioAsignacion::where('modelo','Seccion')->where('id_modelo',$manzana->seccion->id);
                $consulta_manzanal = UsuarioAsignacion::where('modelo','Manzana')->where('id_modelo',$manzana->id);
    
                $C_DISTRITAL_INICIALES = $consulta_c_distrital->first() ? User::find($consulta_c_distrital->first()->user_id)->iniciales() : "" ;
                $C_ENLACE_MANZANA_INICIALES =  $consulta_c_enlace_manzana->first() ? User::find($consulta_c_enlace_manzana->first()->user_id)->iniciales() : '';
                $MANZANAL_INICIALES = $consulta_manzanal->first() ? User::find( $consulta_manzanal->first()->user_id)->iniciales() : '';
                $fecha = Carbon::Now()->format('d-m-Y-s-i-h');
                $random = rand(1, 999);
                $folio = "D{$manzana->seccion->zona->id}-{$C_DISTRITAL_INICIALES}-S{$manzana->seccion->id}-{$C_ENLACE_MANZANA_INICIALES}-M{$manzana->id}-{$MANZANAL_INICIALES}-{$fecha}-{$random}";
                $set('folio', $folio );
            })
            ->preload()
            ->reactive() 
            ->required()
            ->placeholder('Selecciona una Manzana');
        }


            $campos[] = 
            TextInput::make('folio')
            ->label('Folio')
            ->placeholder('folio')
            ->required()
            ->maxLength(255);



            $campos[] = 
                TextInput::make('latitud')
                ->label('Latitud')
                ->placeholder('latitud')
                ->formatStateUsing(function ($state, $record) {
                    return $record->asignacionGeografica->latitud ?? 'Sin latitud';
                }) 
                ->required()
                ->maxLength(255);

                $campos[] = 
                                TextInput::make('longitud')
                ->label('Longitud')
                ->placeholder('Longitud')
                ->formatStateUsing(function ($state, $record) {
                    return $record->asignacionGeografica->longitud ?? 'Sin latitud';
                }) 
                ->required()
                ->maxLength(255);          

                $campos[] = Section::make('Ubicación Geográfica')
                ->schema([                                        
                    View::make('ejercicio.map'),
                ]) ;

        return $form
        ->schema([
            Section::make('Encuesta')
            ->schema($campos)

        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('folio')
                ->label('folio')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('manzana.nombre')
                ->label('manzana')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('manzana.seccion.nombre')
                ->label('seccion')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('manzana.seccion.zona.nombre')
                ->label('zona')
                ->sortable()
                ->searchable(),
                Tables\Columns\TextColumn::make('a_favor')
                ->label('A FAVOR')
                ->sortable()
                ->searchable(),
                
            ])
            ->filters([
                //
            ])
            ->actions([
                Action::make('view')
                ->label('Ver')
                ->url(fn ($record) => EjercicioResource::getUrl('view', ['record' => $record]))
                ->icon('heroicon-o-eye'),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListEjercicios::route('/'),
            'create' => Pages\CreateEjercicio::route('/create'),
            'edit' => Pages\EditEjercicio::route('/{record}/edit'),
            'view' => Pages\ViewEjercicio::route('/{record}/view'),
        ];
    }
}
