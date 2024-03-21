<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use App\Models\Ejercicio;
use Filament\Tables\Table;
use App\Models\EncuestaOpcion;
use App\Models\EncuestaPregunta;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Forms\Components\Radio;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\EjercicioResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\EjercicioResource\RelationManagers;
use App\Models\EncuestaRespuesta;

class EjercicioResource extends Resource
{
    protected static ?string $model = Ejercicio::class;

    protected static ?string $label = 'Mis Ejercicios';
    protected static ?string $navigationLabel = 'Mis Ejercicios';
    protected static ?string $navigationGroup = 'Ejercicios';
    protected static ?string $navigationIcon = 'heroicon-o-cube';    
    protected static ?int    $navigationSort = 4;

    public static function form(Form $form): Form
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
                $campo = Radio::make($pregunta->id)->options($opcionesCampo)
                
                ->label($pregunta->texto_pregunta)->required();
                $campos[] = $campo;
            }
            $opcionesCampo = array();
        }

            $campos[] = 
            TextInput::make('folio')
            ->label('Folio')
            ->placeholder('folio')
            ->required()
            ->maxLength(255);

            $campos[] = 
                TextInput::make('manzana.nombre')
            ->label('Manzana')
            ->placeholder('Manzana')
            ->required()
            ->formatStateUsing(function ($state, $record) {
                return $record->manzana->nombre ?? 'Sin manzana';
            }) 
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
