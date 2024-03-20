<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Zona;
use Filament\Tables;
use App\Models\Casilla;
use App\Models\Seccion;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Arr;
use Filament\Resources\Resource;
use Filament\Forms\Components\View;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Illuminate\Database\Eloquent\Model;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\BelongsToSelect;
use App\Filament\Resources\CasillaResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\CasillaResource\RelationManagers;

class CasillaResource extends Resource
{
    protected static ?string $model = Casilla::class;
    protected static ?string $label = 'Casilla';
    protected static ?string $pluralLabel = 'Casillas';
    protected static ?string $navigationLabel = 'Casillas';
    protected static ?string $navigationGroup = 'System Management';
    protected static ?string $navigationIcon = 'heroicon-o-cube';    
    protected static ?int    $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Section::make('Detalles de la Casilla')
                            ->schema([
                                TextInput::make('numero')->placeholder('Número de Casilla')->required()->numeric()->maxLength(255),
                                
                                Select::make('tipo')
                                    ->placeholder('Tipo de Casilla')
                                    ->label('Tipo de Casilla')
                                    ->options(['BASICA' => 'BASICA', 'CONTINUA' => 'CONTINUA'])
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->live()
                                    ->default('BASICA')
                                    ->required(),
                                
                                Select::make('status')
                                    ->placeholder('Estatus de la Casilla')
                                    ->label('Estatus de la Casilla')
                                    ->options(['ACTIVO' => 'ACTIVO', 'INACTIVO' => 'INACTIVO'])
                                    ->searchable()
                                    ->preload()
                                    ->reactive()
                                    ->live()
                                    ->default('ACTIVO')
                                    ->required(),
                                
                            // Select para zona que actualiza las secciones disponibles.
                            Select::make('zona_id')
                                ->options(function(){
                                    return Zona::all()->pluck('nombre', 'id');
                                })
                                ->searchable()
                                ->reactive()
                                ->live()
                                ->preload()
                                ->label('Zona')
                                ->placeholder('Selecciona una Zona')
                                ->afterStateUpdated(fn (callable $set) => $set('seccion_id', null)),
                            
                            // Select para sección que muestra las secciones basadas en la zona seleccionada.
                            Select::make('seccion_id')
                                ->label('Sección')
                                ->options(function (callable $get) {
                                    $zonaId = $get('zona_id');
                                    // Si no se ha seleccionado una zona, devuelve todas las secciones.
                                    // De lo contrario, filtra las secciones por la zona seleccionada.
                                    return Seccion::when($zonaId, function ($query) use ($zonaId) {
                                        return $query->where('zona_id', $zonaId);
                                    })->pluck('nombre', 'id');
                                })
                                ->searchable()
                                ->preload()
                                ->reactive() // Importante para asegurar que se actualiza cuando cambia zona_id
                                ->required()
                                ->label('Sección')
                                ->placeholder('Selecciona una sección'),

                                Section::make('Coordenadas de Ubicación Geográfica')
                                    ->schema([
                                        TextInput::make('Latitud')
                                        ->required()
                                        ->maxLength(255)
                                        ->label('Latitud')
                                        ->placeholder('Latitud')
                                        ->helperText('Coordenadas para el ejercicio.')
                                        ->readOnly(),

                                        TextInput::make('Longitud')
                                        ->required()
                                        ->maxLength(255)
                                        ->label('Longitud')
                                        ->placeholder('Longitud')
                                        ->helperText('Coordenadas para el ejercicio.')
                                        ->readOnly(),
                                    ]),
                            ])
                            ->columns(2),
                    ]),
                    View::make('casillas.map')->columnSpan([
                        'sm' => 2,
                    ]),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('numero')->searchable(),
                Tables\Columns\TextColumn::make('tipo'),
                Tables\Columns\TextColumn::make('seccion.nombre')->searchable(),
            ])
            ->filters([
                //
            ])
            ->actions([
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
            'index' => Pages\ListCasillas::route('/'),
            'create' => Pages\CreateCasilla::route('/create'),
            'edit' => Pages\EditCasilla::route('/{record}/edit'),
        ];
    }
}
